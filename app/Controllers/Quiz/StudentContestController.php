<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Models\Contest;
use App\Models\ContestParticipant;
use App\Models\User;
use App\Services\Quiz\ShuffleService;
use Exception;

class StudentContestController extends Controller
{
    private $contestModel;
    private $participantModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->contestModel = new Contest();
        $this->participantModel = new ContestParticipant();
        $this->userModel = new User();
    }

    /**
     * List all contests
     */
    public function index()
    {
        $contests = $this->contestModel->getAll();

        // Filter: Recent or upcoming
        $activeContests = array_filter($contests, function ($c) {
            return $c['status'] !== 'ended' || strtotime($c['end_time']) > (time() - 86400);
        });

        $this->view->render('quiz/games/contests_list', [
            'page_title' => 'Battle Royale',
            'contests' => $activeContests,
            'user' => $_SESSION['user']
        ]);
    }

    /**
     * Join/Enter Contest
     */
    public function join($id)
    {
        $pdo = $this->db->getPdo();

        try {
            $contest = $this->contestModel->find($id);
            if (!$contest) throw new Exception("Contest not found");
            if ($contest['status'] == 'ended') throw new Exception("Contest has already ended");

            // Check timing
            if (time() < strtotime($contest['start_time'])) {
                throw new Exception("Contest starts at " . $contest['start_time']);
            }

            // ATOMIC TRANSACTION: Prevent double entry race condition
            $pdo->beginTransaction();

            try {
                // Re-check participation with row lock
                $stmt = $pdo->prepare("SELECT id FROM contest_participants WHERE contest_id = ? AND user_id = ? FOR UPDATE");
                $stmt->execute([$id, $_SESSION['user_id']]);

                if ($stmt->fetch()) {
                    $pdo->rollBack();
                    return $this->redirect("/contest/room/$id");
                }

                // Atomic coin deduction
                $stmt = $pdo->prepare("UPDATE users SET coins = coins - :fee WHERE id = :user_id AND coins >= :fee");
                $stmt->execute([
                    'fee' => $contest['entry_fee'],
                    'user_id' => $_SESSION['user_id']
                ]);

                if ($stmt->rowCount() === 0) {
                    $pdo->rollBack();
                    throw new Exception("Insufficient coins to join.");
                }

                // Create participant
                $stmt = $pdo->prepare("INSERT INTO contest_participants (contest_id, user_id, score, status, created_at) VALUES (?, ?, 0, 'ongoing', NOW())");
                $stmt->execute([$id, $_SESSION['user_id']]);

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }

            return $this->redirect("/contest/room/$id");
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return $this->redirect("/contests");
        }
    }

    /**
     * Contest Room
     */
    public function room($id)
    {
        $contest = $this->contestModel->find($id);
        $participant = $this->participantModel->where(['contest_id' => $id, 'user_id' => $_SESSION['user_id']])[0] ?? null;

        if (!$participant) return $this->redirect("/contests");
        if ($contest['status'] == 'ended' || $participant['is_winner'] !== null) {
            return $this->redirect("/contest/result/$id");
        }

        // Questions
        $qIds = json_decode($contest['questions'], true);
        $placeholders = str_repeat('?,', count($qIds) - 1) . '?';
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM quiz_questions WHERE id IN ($placeholders)");
        $stmt->execute($qIds);
        $questions = $stmt->fetchAll();

        // Decode JSON content/options and SANITIZE sensitive data
        foreach ($questions as &$q) {
            $q['content'] = json_decode($q['content'], true);
            $q['options'] = json_decode($q['options'], true);

            // SECURITY: Prevent leaking correct answers to the browser
            unset($q['correct_answer']);
            unset($q['correct_answer_json']);
            unset($q['answer_explanation']);
            unset($q['note']);
        }

        $this->view->render('quiz/games/contest_room', [
            'contest' => $contest,
            'questions' => $questions,
            'participant' => $participant
        ], 'layouts/quiz_focus');
    }

    /**
     * Submit Results
     */
    public function submit($id)
    {
        $contest = $this->contestModel->find($id);
        $participant = $this->participantModel->where(['contest_id' => $id, 'user_id' => $_SESSION['user_id']])[0] ?? null;

        if (!$participant) return $this->jsonResponse(['error' => 'Not participating']);

        // SECURITY: Check if contest has ended
        if (time() > strtotime($contest['end_time'])) {
            return $this->jsonResponse(['error' => 'Contest has ended. Submission rejected.'], 403);
        }

        // SECURITY: Check if already submitted
        if ($participant['status'] === 'completed') {
            return $this->jsonResponse(['error' => 'Already submitted'], 400);
        }

        // CRITICAL SECURITY: Calculate score server-side
        $userAnswers = $_POST['answers'] ?? []; // Expecting key-value pair [qId => answer]
        if (!is_array($userAnswers)) {
            $userAnswers = json_decode($userAnswers, true) ?: [];
        }

        $qIds = json_decode($contest['questions'], true);
        $placeholders = str_repeat('?,', count($qIds) - 1) . '?';
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM quiz_questions WHERE id IN ($placeholders)");
        $stmt->execute($qIds);
        $questions = $stmt->fetchAll();

        $scoringService = new \App\Services\Quiz\ScoringService();
        $totalScore = 0;

        // Mock exam settings for contest (can be pulled from contest config later)
        $settings = [
            'negative_marking_rate' => $contest['negative_marking'] ?? 0,
            'negative_marking_unit' => 'percent'
        ];

        foreach ($questions as $q) {
            $ans = $userAnswers[$q['id']] ?? null;
            $grade = $scoringService->gradeQuestion($q, $ans, $settings);
            $totalScore += $grade['marks'];
        }

        // SECURITY: Calculate time server-side (prevent client manipulation)
        $timeTaken = time() - strtotime($participant['created_at']);

        $this->participantModel->update($participant['id'], [
            'score' => max(0, $totalScore),
            'time_taken' => $timeTaken, // Server-calculated, not client-provided
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s')
        ]);

        return $this->jsonResponse(['success' => true]);
    }

    /**
     * Contest Result / Waiting Page
     */
    public function result($id)
    {
        $contest = $this->contestModel->find($id);
        $participant = $this->participantModel->where(['contest_id' => $id, 'user_id' => $_SESSION['user_id']])[0] ?? null;

        $this->view->render('quiz/games/contest_result', [
            'contest' => $contest,
            'participant' => $participant
        ]);
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
