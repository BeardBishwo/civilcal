<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\GamificationService;
use App\Services\NonceService;
use App\Services\SecurityMonitor;
use App\Services\Security;
use App\Services\Quiz\DailyQuizService;
use App\Services\Quiz\StreakService;
use App\Services\Quiz\ShuffleService;
use App\Services\Quiz\ScoringService;

class ExamEngineController extends Controller
{
    protected $db;
    private $gamificationService;
    private $dailyQuizService;
    private $streakService;
    private $scoringService;
    private NonceService $nonceService;
    private $storagePath;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->db = \App\Core\Database::getInstance();
        $this->gamificationService = new GamificationService();
        $this->dailyQuizService = new DailyQuizService();
        $this->streakService = new StreakService();
        $this->scoringService = new ScoringService();
        $this->nonceService = new NonceService();
        $this->storagePath = __DIR__ . '/../../../storage/app/exams/';

        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    }

    /**
     * Start or Resume an Exam (JSON Cached)
     */
    public function start($slug)
    {
        // 1. Get Exam ID
        $stmt = $this->db->getPdo()->prepare("SELECT id, title, duration_minutes, is_premium, shuffle_questions, mode, negative_marking_rate, negative_marking_unit, negative_marking_basis FROM quiz_exams WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $exam = $stmt->fetch();

        if (!$exam) {
            $this->redirect('/quiz');
        }

        // 2. Check for existing active attempt (DB)
        $stmtAttempt = $this->db->getPdo()->prepare("
            SELECT id FROM quiz_attempts 
            WHERE user_id = :uid AND exam_id = :eid AND status = 'ongoing'
        ");
        $stmtAttempt->execute(['uid' => $_SESSION['user_id'], 'eid' => $exam['id']]);
        $existing = $stmtAttempt->fetch();

        if ($existing) {
            // Check if JSON exists, if not regenerate it
            if (!file_exists($this->storagePath . $existing['id'] . '.json')) {
                $this->regenerateCache($existing['id'], $exam);
            }
            $this->redirect('/quiz/room/' . $existing['id']);
        } else {
            // 3. Create New Attempt (DB)
            $sql = "INSERT INTO quiz_attempts (user_id, exam_id, status, started_at) VALUES (:uid, :eid, 'ongoing', NOW())";
            $stmtInsert = $this->db->getPdo()->prepare($sql);
            $stmtInsert->execute(['uid' => $_SESSION['user_id'], 'eid' => $exam['id']]);
            $attemptId = $this->db->getPdo()->lastInsertId();

            // 4. Initialize JSON Cache
            $this->initializeCache($attemptId, $exam);

            $this->redirect('/quiz/room/' . $attemptId);
        }
    }

    /**
     * Start Daily Quest
     */
    public function startDaily()
    {
        $date = date('Y-m-d');
        $userId = $_SESSION['user_id'];

        // 1. Get User Profile for targeting
        $profileService = new \App\Services\ProfileService();
        $profile = $profileService->getUserProfile($userId);

        $userStreamId = $profile['user']['stream_id'] ?? null;
        $eduLevelName = $profile['career_interests']['education_level'] ?? null;
        $userEduLevelId = $profileService->resolveEduLevelId($eduLevelName);

        // 2. Get Quiz info from Schedule
        $daily = $this->dailyQuizService->getQuizForUser($date, $userStreamId, $userEduLevelId);

        if (!$daily) {
            // If no targeted quiz found, fallback to Mixed (getQuizForUser already handles hierarchy)
            $_SESSION['flash_error'] = "No Daily Quest available for today yet. Please try again later.";
            $this->redirect('/quiz/dashboard');
            return;
        }

        // 3. Check if already attempted
        if ($this->dailyQuizService->checkAttempt($userId, $date)) {
            $_SESSION['flash_info'] = "You have already completed today's quest!";
            $this->redirect('/quiz/dashboard');
            return;
        }

        // 4. Handle Start Action (POST)
        if (isset($_POST['action']) && $_POST['action'] === 'start') {
            // Get Placeholder Exam
            $stmt = $this->db->getPdo()->prepare("SELECT * FROM quiz_exams WHERE slug = 'daily-quest'");
            $stmt->execute();
            $exam = $stmt->fetch();

            if (!$exam) {
                die("System Error: Daily Quest configuration missing.");
            }

            // Create Attempt 
            $sql = "INSERT INTO quiz_attempts (user_id, exam_id, status, started_at) VALUES (:uid, :eid, 'ongoing', NOW())";
            $stmtInsert = $this->db->getPdo()->prepare($sql);
            $stmtInsert->execute(['uid' => $userId, 'eid' => $exam['id']]);
            $attemptId = $this->db->getPdo()->lastInsertId();

            // Initialize Cache with Questions
            $questionIds = json_decode($daily['questions'], true);
            $this->initializeCache($attemptId, $exam, $questionIds, $daily['id']);

            // Safety check: ensure questions were actually loaded
            $attemptJson = file_get_contents($this->storagePath . $attemptId . '.json');
            $attemptData = json_decode($attemptJson, true);
            if (empty($attemptData['questions'])) {
                header('Location: ' . app_base_url('/quiz?error=No+questions+available+for+today+quest'));
                exit;
            }

            $this->redirect('/quiz/room/' . $attemptId);
            return;
        }

        // 5. Show Lobby (GET)
        $user = $this->db->findOne('users', ['id' => $userId]);

        // Determine focus area title
        $focusArea = 'Mixed Engineering';
        if (!empty($daily['target_stream_id'])) {
            $node = $this->db->findOne('syllabus_nodes', ['id' => $daily['target_stream_id']]);
            if ($node) $focusArea = $node['title'];
        }

        $this->view->render('quiz/daily_lobby', [
            'daily' => $daily,
            'user' => $user,
            'focus_area' => $focusArea
        ]);
    }

    /**
     * Start Practice Quiz from Syllabus Topic
     */
    public function startPractice($id)
    {
        // 1. Get Node Info
        $node = $this->db->findOne('syllabus_nodes', ['id' => $id]);

        if (!$node) {
            $this->redirect('/quiz/zone');
        }

        // 2. Determine column to search in quiz_questions
        $column = 'topic_id';
        switch ($node['type']) {
            case 'course':
                $column = 'course_id';
                break;
            case 'category':
                $column = 'category_id';
                break;
            case 'sub_category':
                $column = 'sub_category_id';
                break;
        }

        // 3. Fetch Questions
        $sql = "SELECT id FROM quiz_questions WHERE {$column} = :id AND status = 'approved' ORDER BY RAND() LIMIT 20";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $questionIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($questionIds)) {
            // Fallback: search by title
            $sql = "SELECT id FROM quiz_questions WHERE content LIKE :title AND status = 'approved' ORDER BY RAND() LIMIT 20";
            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute(['title' => '%' . $node['title'] . '%']);
            $questionIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }

        if (empty($questionIds)) {
            $this->redirect('/quiz/zone?error=No+questions+available+for+this+section');
        }

        // 4. Create Attempt
        $sql = "INSERT INTO quiz_attempts (user_id, exam_id, status, started_at) VALUES (?, NULL, 'ongoing', NOW())";
        $this->db->query($sql, [$_SESSION['user_id']]);
        $attemptId = $this->db->getPdo()->lastInsertId();

        // 5. Initialize Cache
        $this->initializeCache($attemptId, [
            'id' => null,
            'title' => 'Practice: ' . $node['title'],
            'duration_minutes' => 20,
            'mode' => 'practice',
            'shuffle_questions' => 1
        ], $questionIds);

        $this->redirect('/quiz/room/' . $attemptId);
    }

    /**
     * Initialize the JSON Cache for a new attempt
     */
    private function initializeCache($attemptId, $exam, $questionIds = null, $dailyQuizId = null)
    {
        $questions = [];

        if ($questionIds && is_array($questionIds)) {
            // Fetch Specific Questions for Daily Quest
            if (empty($questionIds)) {
                $questions = [];
            } else {
                $placeholders = str_repeat('?,', count($questionIds) - 1) . '?';
                $sqlQ = "SELECT id, type, content, options, correct_answer_json, default_marks, default_negative_marks, difficulty_level, answer_explanation as explanation FROM quiz_questions WHERE id IN ($placeholders)";
                $stmtQ = $this->db->getPdo()->prepare($sqlQ);
                $stmtQ->execute($questionIds);
                $questions = $stmtQ->fetchAll(\PDO::FETCH_ASSOC);

                // Restore randomness/ladder order if needed, but SQL might mess it up.
                // Re-sort based on input array order?
                $qMap = [];
                foreach ($questions as $q) $qMap[$q['id']] = $q;

                $orderedQuestions = [];
                foreach ($questionIds as $qid) {
                    if (isset($qMap[$qid])) $orderedQuestions[] = $qMap[$qid];
                }
                $questions = $orderedQuestions;
            }
        } else {
            // Standard Exam Fetch
            // Fetch Questions
            $sqlQ = "
                SELECT q.id, q.type, q.content, q.options, q.correct_answer_json, q.default_marks, q.default_negative_marks, q.difficulty_level, q.answer_explanation as explanation
                FROM quiz_exam_questions eq
                JOIN quiz_questions q ON eq.question_id = q.id
                WHERE eq.exam_id = :eid
                ORDER BY eq.`order` ASC, q.id ASC
            ";
            $stmtQ = $this->db->getPdo()->prepare($sqlQ);
            $stmtQ->execute(['eid' => $exam['id']]);
            $questions = $stmtQ->fetchAll(\PDO::FETCH_ASSOC);
        }

        // 2-Level Shuffle (Using Chaos Engine)
        if (!empty($exam['shuffle_questions'])) {
            $shuffler = new ShuffleService();
            $questions = $shuffler->randomize($questions, null); // True Random
        }

        // Decode JSON question/options for storage
        foreach ($questions as &$q) {
            // Store question as 'content' key for consistency in JSON cache
            $q['content'] = is_string($q['content']) ? json_decode($q['content'], true) : $q['content'];
            $q['options'] = is_string($q['options']) ? json_decode($q['options'], true) : $q['options'];
        }

        $data = [
            'attempt_id' => $attemptId,
            'user_id' => $_SESSION['user_id'],
            'exam' => $exam,
            'questions' => $questions,
            'answers' => [], // Key: question_id, Value: selected_option
            'start_time' => time(),
            'daily_quiz_id' => $dailyQuizId
        ];

        file_put_contents($this->storagePath . $attemptId . '.json', json_encode($data));
    }

    /**
     * Regenerate Cache from DB (Fallback)
     */
    private function regenerateCache($attemptId, $exam)
    {
        // Same as initialize but fetch existing answers if needed?
        // For 'ongoing' attempt without cache, we assume lost cache = empty answers or fetch from DB if partial save existed?
        // Since we only save to DB on submit, 'ongoing' implies we rely on JSON.
        // If JSON is gone, answers are gone. This is a trade-off. 
        // But user is on shared hosting, file persistence is usually reliable.
        // We'll just re-init.
        $this->initializeCache($attemptId, $exam);
    }

    /**
     * The Main Exam Room Interface (Reads JSON)
     */
    public function room($attemptId)
    {
        $file = $this->storagePath . $attemptId . '.json';

        if (!file_exists($file)) {
            // Fallback: Check DB if valid attempt, then regen
            $stmt = $this->db->getPdo()->prepare("SELECT * FROM quiz_attempts WHERE id = :id AND user_id = :uid");
            $stmt->execute(['id' => $attemptId, 'uid' => $_SESSION['user_id']]);
            $attempt = $stmt->fetch();

            if (!$attempt || $attempt['status'] !== 'ongoing') {
                $this->redirect('/quiz/result/' . $attemptId);
            }

            // Get Exam to regen
            $stmtEx = $this->db->getPdo()->prepare("SELECT * FROM quiz_exams WHERE id = :id");
            $stmtEx->execute(['id' => $attempt['exam_id']]);
            $exam = $stmtEx->fetch();

            $this->regenerateCache($attemptId, $exam);
        }

        $data = json_decode(file_get_contents($file), true);

        // Security check
        if ($data['user_id'] != $_SESSION['user_id']) {
            die("Unauthorized Access");
        }

        // Prepare View Data (Hide sensitive + Sanitize HTML)
        $viewQuestions = $data['questions'];
        foreach ($viewQuestions as &$q) {
            // XSS Prevention: Sanitize question content
            if (isset($q['content']['text'])) {
                $q['content']['text'] = htmlspecialchars($q['content']['text'], ENT_QUOTES, 'UTF-8');
            }

            // Sanitize options
            if (is_array($q['options'])) {
                foreach ($q['options'] as $key => &$opt) {
                    if (is_string($opt)) {
                        $q['options'][$key] = htmlspecialchars($opt, ENT_QUOTES, 'UTF-8');
                    }
                    // This part was originally conditional on exam mode, but the instruction places it here.
                    // It ensures 'is_correct' is never sent to the client if it exists within an option array.
                    if (is_array($opt) && isset($opt['is_correct'])) {
                        unset($opt['is_correct']);
                    }
                }
            }

            if ($data['exam']['mode'] == 'exam') {
                unset($q['correct_answer_json']);
                unset($q['explanation']);
            }
        }

        // Nonce
        $nonce = $this->nonceService->generate($_SESSION['user_id'], 'quiz');
        $csrfToken = Security::generateCsrfToken();

        $this->view('quiz/arena/room', [
            'attempt' => ['id' => $attemptId, 'title' => $data['exam']['title'], 'duration_minutes' => $data['exam']['duration_minutes']],
            'questions' => $viewQuestions,
            'savedAnswers' => $data['answers'],
            'quizNonce' => $nonce['nonce'] ?? null,
            'csrfToken' => $csrfToken,
            'title' => $data['exam']['title'],
            'server_start_time' => $data['start_time'], // For client-side timer validation
            'server_current_time' => time() // Current server time
        ], 'layouts/quiz_focus'); // Use Distraction-Free Layout
    }

    /**
     * AJAX: Save Answer (Writes to JSON)
     */
    public function saveAnswer()
    {
        $attemptId = $_POST['attempt_id'];
        $questionId = $_POST['question_id'];
        $selectedOptions = $_POST['selected_options'];
        $trap = $_POST['trap_answer'] ?? '';

        if (!empty($trap)) {
            SecurityMonitor::log($_SESSION['user_id'] ?? null, 'honeypot_trigger', $_SERVER['REQUEST_URI'] ?? '', ['attempt_id' => $attemptId], 'critical');
            http_response_code(400);
            echo json_encode(['error' => 'Invalid']);
            exit;
        }

        $file = $this->storagePath . $attemptId . '.json';
        if (!file_exists($file)) {
            http_response_code(404);
            echo json_encode(['error' => 'Session expired']);
            exit;
        }

        // ATOMIC FILE OPERATION: Use flock for entire read-modify-write cycle
        $fp = fopen($file, 'r+');
        if (!$fp) {
            http_response_code(500);
            echo json_encode(['error' => 'File access error']);
            exit;
        }

        // Acquire exclusive lock
        if (flock($fp, LOCK_EX)) {
            // Read current data
            $fileSize = filesize($file);
            $json = $fileSize > 0 ? fread($fp, $fileSize) : '{}';
            $data = json_decode($json, true);

            // Validate ownership
            if ($data['user_id'] != $_SESSION['user_id']) {
                flock($fp, LOCK_UN);
                fclose($fp);
                http_response_code(403);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            // Update answer
            $data['answers'][$questionId] = $selectedOptions;

            // Write back atomically
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($data));
            fflush($fp);

            // Release lock
            flock($fp, LOCK_UN);
        } else {
            fclose($fp);
            http_response_code(500);
            echo json_encode(['error' => 'Could not acquire lock']);
            exit;
        }

        fclose($fp);

        echo json_encode(['success' => true]);
    }

    /**
     * Submit Exam (Reads JSON -> Writes DB)
     */
    public function submit()
    {
        $attemptId = $_POST['attempt_id'];
        $nonce = $_POST['nonce'] ?? '';
        $trap = $_POST['trap_answer'] ?? '';

        if (!empty($trap)) die("Invalid request");
        if (!$this->nonceService->validateAndConsume($nonce, $_SESSION['user_id'], 'quiz')) die("Token expired");

        $file = $this->storagePath . $attemptId . '.json';
        if (!file_exists($file)) die("Session not found");

        $data = json_decode(file_get_contents($file), true);

        // Grading Logic
        $totalScore = 0;
        $correctCount = 0;
        $correctAnswersList = [];

        // Prepare Batch Insert SQL
        $sqlValues = [];
        $params = [];

        // We use the questions from JSON which are the source of truth for THIS attempt
        foreach ($data['questions'] as $q) {
            $qId = $q['id'];
            $userAns = $data['answers'][$qId] ?? null;

            // Delegate to Scoring Engine
            $result = $this->scoringService->gradeQuestion($q, $userAns, $data['exam']);

            $isCorrect = $result['isCorrect'];
            $marks = $result['marks'];

            $totalScore += $marks;

            if ($isCorrect) {
                $correctCount++;
                $correctAnswersList[] = ['question_id' => $qId, 'difficulty' => $q['difficulty_level'] ?? 3];
            }

            // Prepare Insert
            $sqlValues[] = "(?, ?, ?, ?, ?)";
            $params[] = $attemptId;
            $params[] = $qId;
            $params[] = json_encode($userAns);
            $params[] = $isCorrect ? 1 : 0;
            $params[] = $marks;
        }

        // SECURITY: Time Limit Check (Server Side)
        // Allow 2 minute buffer for network lag
        $allowedDuration = ($data['exam']['duration_minutes'] * 60) + 120;
        $elapsed = time() - $data['start_time'];

        if ($elapsed > $allowedDuration) {
            // Reject Submission
            unlink($file); // Destroy session
            http_response_code(408); // Request Timeout
            die(json_encode(['error' => 'Time limit exceeded. Submission rejected.']));
        }

        // SECURITY: Prevent Reward Farming (Check if previously completed)
        $stmtCheck = $this->db->getPdo()->prepare("SELECT id FROM quiz_attempts WHERE user_id = :uid AND exam_id = :eid AND status = 'completed'");
        $stmtCheck->execute(['uid' => $_SESSION['user_id'], 'eid' => $data['exam']['id']]);
        $alreadyCompleted = $stmtCheck->fetch();

        // Bulk Insert Answers
        if (!empty($sqlValues)) {
            $sql = "INSERT INTO quiz_attempt_answers (attempt_id, question_id, selected_options, is_correct, marks_earned) VALUES " . implode(',', $sqlValues);
            $this->db->getPdo()->prepare($sql)->execute($params);
        }

        // Update Attempt
        $this->db->getPdo()->prepare("UPDATE quiz_attempts SET status = 'completed', completed_at = NOW(), score = :score WHERE id = :id")
            ->execute(['score' => $totalScore, 'id' => $attemptId]);

        // Gamification & Leaderboard
        // Only reward if not previously completed
        if (!$alreadyCompleted) {
            $this->gamificationService->processExamRewards($_SESSION['user_id'], $correctAnswersList, $attemptId);
        } else {
            // Optional: Give reduced XP or just log it?
            // For now, strict blocked as per prompt.
        }

        try {
            require_once __DIR__ . '/../../Services/LeaderboardService.php';
            $lbService = new \App\Services\LeaderboardService();
            $lbService->updateUserRank($_SESSION['user_id'], $totalScore, count($data['questions']), $correctCount);
        } catch (\Exception $e) {
        }

        // --- DAILY QUEST LOGIC ---
        if (isset($data['daily_quiz_id']) && $data['daily_quiz_id']) {
            try {
                // Determine rewards - e.g., Base coin per question or fixed reward?
                // The schedule table has `reward_coins` (usually 50).
                // Let's fetch the schedule to get the max reward.
                // For now, let's just award what's in the schedule OR a function of score.

                // Let's use the StreakService directly, passing 50 as base. 
                // Ideally, we fetch `reward_coins` from daily_quiz_schedule.

                $streakRes = $this->streakService->processVictory($_SESSION['user_id'], 50);

                // Record the Daily Attempt specifically
                $this->dailyQuizService->recordAttempt($_SESSION['user_id'], $data['daily_quiz_id'], $totalScore, $streakRes['coins']);

                // Store streak info in session to show on result page
                $_SESSION['latest_streak_info'] = $streakRes;
            } catch (\Exception $e) {
                // Log error but don't fail the submit
                error_log("Daily Quest Error: " . $e->getMessage());
            }
        }
        // -------------------------

        // Delete Cache
        unlink($file);

        $this->redirect('/quiz/result/' . $attemptId);
    }

    public function result($attemptId)
    {
        // 1. Fetch Attempt Summary
        $stmt = $this->db->getPdo()->prepare("
            SELECT a.*, e.title, e.total_marks, e.slug
            FROM quiz_attempts a
            JOIN quiz_exams e ON a.exam_id = e.id
            WHERE a.id = :aid AND a.user_id = :uid
        ");
        $stmt->execute(['aid' => $attemptId, 'uid' => $_SESSION['user_id']]);
        $attempt = $stmt->fetch();

        if (!$attempt) {
            $this->redirect('/quiz');
        }

        // 2. Fetch Incorrect Answers for "Smart Failure" Tool Linking
        $stmtIncorrect = $this->db->getPdo()->prepare("
            SELECT aa.*, q.content, q.answer_explanation as explanation, q.options, q.type, q.correct_answer_json
            FROM quiz_attempt_answers aa
            JOIN quiz_questions q ON aa.question_id = q.id
            WHERE aa.attempt_id = :aid AND aa.is_correct = 0
            LIMIT 10
        ");
        $stmtIncorrect->execute(['aid' => $attemptId]);
        $incorrectAnswers = $stmtIncorrect->fetchAll();

        // Decode JSON fields for view
        foreach ($incorrectAnswers as &$inc) {
            try {
                // Rename 'question' to 'content' for view consistency
                $inc['content'] = is_string($inc['content']) ? json_decode($inc['content'], true) : $inc['content'];
                $inc['options'] = is_string($inc['options']) ? json_decode($inc['options'], true) : $inc['options'];
                $inc['selected_options'] = is_string($inc['selected_options']) ? json_decode($inc['selected_options'], true) : $inc['selected_options'];
                $inc['correct_answer_json'] = is_string($inc['correct_answer_json']) ? json_decode($inc['correct_answer_json'], true) : $inc['correct_answer_json'];
            } catch (\Exception $e) {
                // Keep as is or handle error
            }
        }

        $this->view('quiz/analysis/report', [
            'attempt' => $attempt,
            'incorrect_answers' => $incorrectAnswers
        ]);
    }
}
