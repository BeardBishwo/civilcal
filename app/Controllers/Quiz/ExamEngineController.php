<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\GamificationService;

class ExamEngineController extends Controller
{
    private $db;
    private $gamificationService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->db = \App\Core\Database::getInstance();
        $this->gamificationService = new GamificationService();
    }

    /**
     * Start or Resume an Exam
     */
    public function start($slug)
    {
        // 1. Get Exam ID
        $stmt = $this->db->getPdo()->prepare("SELECT id, title, duration_minutes, is_premium, price FROM quiz_exams WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $exam = $stmt->fetch();

        if (!$exam) {
            $this->redirect('/quiz');
        }

        // 2. Check for existing active attempt
        $stmtAttempt = $this->db->getPdo()->prepare("
            SELECT id FROM quiz_attempts 
            WHERE user_id = :uid AND exam_id = :eid AND status = 'in_progress'
        ");
        $stmtAttempt->execute(['uid' => $_SESSION['user_id'], 'eid' => $exam['id']]);
        $existing = $stmtAttempt->fetch();

        if ($existing) {
            // Resume
            $this->redirect('/quiz/room/' . $existing['id']);
        } else {
            // Create New Attempt
            $sql = "INSERT INTO quiz_attempts (user_id, exam_id, status, started_at) VALUES (:uid, :eid, 'in_progress', NOW())";
            $stmtInsert = $this->db->getPdo()->prepare($sql);
            $stmtInsert->execute(['uid' => $_SESSION['user_id'], 'eid' => $exam['id']]);
            $attemptId = $this->db->getPdo()->lastInsertId();

            $this->redirect('/quiz/room/' . $attemptId);
        }
    }

    /**
     * The Main Exam Room Interface
     */
    public function room($attemptId)
    {
        // 1. Fetch Attempt & Exam
        $sql = "
            SELECT a.*, e.title, e.duration_minutes, e.mode, e.shuffle_questions
            FROM quiz_attempts a
            JOIN quiz_exams e ON a.exam_id = e.id
            WHERE a.id = :aid AND a.user_id = :uid
        ";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(['aid' => $attemptId, 'uid' => $_SESSION['user_id']]);
        $attempt = $stmt->fetch();

        if (!$attempt || $attempt['status'] == 'completed') {
            $this->redirect('/quiz/result/' . $attemptId);
        }

        // 2. Fetch Questions (Ordered)
        // We need to fetch questions associated with this exam via pivot
        $sqlQ = "
            SELECT q.id, q.type, q.content, q.options, q.default_marks
            FROM quiz_exam_questions eq
            JOIN quiz_questions q ON eq.question_id = q.id
            WHERE eq.exam_id = :eid
            ORDER BY eq.order ASC, q.id ASC
        ";
        $stmtQ = $this->db->getPdo()->prepare($sqlQ);
        $stmtQ->execute(['eid' => $attempt['exam_id']]);
        $questions = $stmtQ->fetchAll(\PDO::FETCH_ASSOC);

        // Shuffle if enabled
        if (!empty($attempt['shuffle_questions'])) {
            mt_srand($attempt['id']); // Seed with attempt ID for deterministic shuffle
            shuffle($questions);
            mt_srand(); // Reset seed
        }

        // Filter sensitive data from JSON before sending to view (e.g., is_correct flag)
        foreach ($questions as &$q) {
            $opts = json_decode($q['options'], true);
            if (is_array($opts)) {
                foreach ($opts as &$opt) {
                    if ($attempt['mode'] == 'exam') {
                        unset($opt['is_correct']); // Hide answer in exam mode
                    }
                }
            }
            $q['options'] = $opts; // Keep as array for view
            $q['content'] = json_decode($q['content'], true);
        }

        // 3. fetch saved answers if any (to restore state)
        $sqlAns = "SELECT question_id, selected_options FROM quiz_attempt_answers WHERE attempt_id = :aid";
        $stmtAns = $this->db->getPdo()->prepare($sqlAns);
        $stmtAns->execute(['aid' => $attemptId]);
        $savedAnswers = $stmtAns->fetchAll(\PDO::FETCH_KEY_PAIR); // [qid => json_val]

        $this->view('quiz/arena/room', [
            'attempt' => $attempt,
            'questions' => $questions,
            'savedAnswers' => $savedAnswers,
            'title' => 'Exam Room'
        ]);
    }

    /**
     * AJAX: Save Answer (Heartbeat)
     */
    public function saveAnswer()
    {
        $attemptId = $_POST['attempt_id'];
        $questionId = $_POST['question_id'];
        $selectedOptions = $_POST['selected_options']; // Array or Value

        // Validate ownership
        $stmt = $this->db->getPdo()->prepare("SELECT id FROM quiz_attempts WHERE id = :id AND user_id = :uid");
        $stmt->execute(['id' => $attemptId, 'uid' => $_SESSION['user_id']]);
        if (!$stmt->fetch()) {
             http_response_code(403);
             echo json_encode(['success' => false, 'error' => 'Unauthorized']);
             exit;
        }

        // Upsert Answer
        $json = json_encode($selectedOptions);
        
        $sql = "
            INSERT INTO quiz_attempt_answers (attempt_id, question_id, selected_options)
            VALUES (:aid, :qid, :val)
            ON DUPLICATE KEY UPDATE selected_options = :val2
        ";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([
            'aid' => $attemptId,
            'qid' => $questionId,
            'val' => $json,
            'val2' => $json
        ]);

        echo json_encode(['success' => true]);
    }

    /**
     * Submit Exam
     */
    public function submit()
    {
        $attemptId = $_POST['attempt_id'];
        
        // 1. Validate
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM quiz_attempts WHERE id = :id AND user_id = :uid");
        $stmt->execute(['id' => $attemptId, 'uid' => $_SESSION['user_id']]);
        $attempt = $stmt->fetch();

        if (!$attempt) die("Invalid attempt");

        // 2. Load Exam settings for marking scheme
        $stmtExam = $this->db->getPdo()->prepare("SELECT * FROM quiz_exams WHERE id = :id");
        $stmtExam->execute(['id' => $attempt['exam_id']]);
        $exam = $stmtExam->fetch();

        // 3. Calculate Score
        $totalScore = 0;
        
        // Fetch all questions and user answers
        $sqlParams = "
            SELECT q.id, q.type, q.options, q.default_marks, q.default_negative_marks,
                   a.selected_options
            FROM quiz_questions q
            JOIN quiz_attempt_answers a ON q.id = a.question_id
            WHERE a.attempt_id = :aid
        ";
        $stmtCalc = $this->db->getPdo()->prepare($sqlParams);
        $stmtCalc->execute(['aid' => $attemptId]);
        $results = $stmtCalc->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as $res) {
            $isCorrect = false;
            $userAns = json_decode($res['selected_options'], true);
            $correctOpts = array_filter(json_decode($res['options'], true), function($o) { return !empty($o['is_correct']); });
            
            // Logic based on type
            if ($res['type'] == 'mcq_single' || $res['type'] == 'true_false') {
                 // userAns should be a single ID (or string value)
                 // Check if valid
                 foreach($correctOpts as $co) {
                     // Assuming options have generated IDs or simply checking text/index equality
                     // Since we saved options as JSON without explicit IDs in form (we might have dependent on index), 
                     // let's rely on matching Text or Index if we implemented IDs.
                     // The form builder didn't assign UUIDs to options, so we rely on array index match or text match.
                     // Let's assume for now userAns is the INDEX of the option.
                     // IMPORTANT: The frontend room needs to send index or text.
                     // Let's assume standard is Option Index (0, 1, 2, 3).
                     // But wait, the form builder allows deleting options, so indexes shift? 
                     // Actually, database stores options as a blob. 
                     // Safe way: Match the 'text' or assume the array order is preserved.
                     // Revision: The Exam Room will send the index of the option in the array.
                     
                     // If userAns matches the index of a correct option
                     if (isset($userAns) && isset($correctOpts[$userAns])) {
                         $isCorrect = true; 
                     }
                     // Or strict value match needed?
                 }
            }
                     // Or strict value match needed?
                 }
            } elseif ($res['type'] == 'numerical') {
                 // Numerical Logic
                 $userVal = is_numeric($userAns) ? (float)$userAns : null;
                 if ($userVal !== null && count($correctOpts) > 0) {
                     // Get first correct option (assuming only 1 for numerical)
                     $firstCorrect = reset($correctOpts);
                     $targetVal = (float)$firstCorrect['text'];
                     $tolerance = isset($firstCorrect['tolerance']) ? (float)$firstCorrect['tolerance'] : 0;
                     
                     if ($userVal >= ($targetVal - $tolerance) && $userVal <= ($targetVal + $tolerance)) {
                         $isCorrect = true;
                     }
                 }
                 
                 // If exact match needed without tolerance check (fallback)
                 if (!$isCorrect && count($correctOpts) > 0) {
                     // Check exact string match just in case
                     $firstCorrect = reset($correctOpts);
                     if ((string)$userAns === (string)$firstCorrect['text']) {
                         $isCorrect = true;
                     }
                 }
            }
            // For MVP strictness: simple index match.  
            // In a real robust system, I'd give UUIDs to options. 
            // For this implementation, I will treat userAns as the INDEX (0,1,2,3).
            
            // Checking logic:
            // Get correct indices
            $correctIndices = array_keys($correctOpts);
            
            if ($res['type'] == 'mcq_single' || $res['type'] == 'true_false') {
                if (in_array((int)$userAns, $correctIndices)) {
                    $isCorrect = true;
                }
            }
            
            // Update the answer row with result
            $marks = $isCorrect ? $res['default_marks'] : (-1 * abs($res['default_negative_marks']));
            if($userAns === null) $marks = 0; // Unanswered

            $totalScore += $marks;

            $this->db->getPdo()->prepare("UPDATE quiz_attempt_answers SET is_correct = :ic, marks_earned = :me WHERE attempt_id = :aid AND question_id = :qid")
                ->execute(['ic' => $isCorrect, 'me' => $marks, 'aid' => $attemptId, 'qid' => $res['id']]);

            // Gamification: Award Resources
            if ($isCorrect) {
                // Determine difficulty from question or default to medium
                $difficulty = $res['difficulty_level'] ?? 3; // 1-5
                $this->gamificationService->rewardUser($_SESSION['user_id'], true, $difficulty, $attemptId);
            }
        }

        // 4. Update Attempt Status
        $stmtUpd = $this->db->getPdo()->prepare("
            UPDATE quiz_attempts 
            SET status = 'completed', completed_at = NOW(), score = :score 
        $stmtUpd->execute(['score' => $totalScore, 'id' => $attemptId]);

        // 5. Update Leaderboard Aggregates
        try {
            $totalQuestions = count($results);
            $correctAnswers = 0;
            // Iterate to count correct
            foreach ($results as $res) {
                 $userAns = json_decode($res['selected_options'], true);
                 // We need to know if this specific answer was correct.
                 // We updated 'is_correct' in DB, but we didn't track it in a variable loop above clearly.
                 // Let's query the specific answers again or better, capture it in the loop above.
                 // OPTIMIZATION: Refactor loop above to track $correctAnswers
            }
            // For now, let's just query the count of correct answers for this attempt.
            $stmtCount = $this->db->getPdo()->prepare("SELECT count(*) FROM quiz_attempt_answers WHERE attempt_id = ? AND is_correct = 1");
            $stmtCount->execute([$attemptId]);
            $correctAnswers = $stmtCount->fetchColumn();

            require_once __DIR__ . '/../../Services/LeaderboardService.php';
            $lbService = new \App\Services\LeaderboardService();
            // TODO: Pass category ID if Exam has category?
            // $exam['category_id'] is not in quiz_exams table currently, it's linked via questions or Syllabus.
            // For MVP, we pass null (Global Rank). 
            $lbService->updateUserRank($_SESSION['user_id'], $totalScore, $totalQuestions, $correctAnswers);
            
        } catch (\Exception $e) {
            // Log error but don't stop flow
            error_log("Leaderboard Update Fail: " . $e->getMessage());
        }
        
        $this->redirect('/quiz/result/' . $attemptId);
    }

    public function result($attemptId)
    {
        // ... (Show Result View) ...
         $stmt = $this->db->getPdo()->prepare("
            SELECT a.*, e.title, e.total_marks
            FROM quiz_attempts a
            JOIN quiz_exams e ON a.exam_id = e.id
            WHERE a.id = :aid AND a.user_id = :uid
        ");
        $stmt->execute(['aid' => $attemptId, 'uid' => $_SESSION['user_id']]);
        $attempt = $stmt->fetch();

        if (!$attempt) {
            $this->redirect('/quiz');
        }

        $this->view('quiz/analysis/report', ['attempt' => $attempt]);
    }
}
