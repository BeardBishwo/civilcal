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
        $activeContests = array_filter($contests, function($c) {
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
        try {
            $contest = $this->contestModel->find($id);
            if (!$contest) throw new Exception("Contest not found");
            if ($contest['status'] == 'ended') throw new Exception("Contest has already ended");
            
            // Check if already participating
            $existing = $this->participantModel->where(['contest_id' => $id, 'user_id' => $_SESSION['user_id']]);
            if ($existing) {
                return $this->redirect("/contest/room/$id");
            }

            // Check timing
            if (time() < strtotime($contest['start_time'])) {
                throw new Exception("Contest starts at " . $contest['start_time']);
            }

            // Pay Entry Fee
            $user = $this->userModel->find($_SESSION['user_id']);
            if ($user['coins'] < $contest['entry_fee']) {
                throw new Exception("Not enough coins to join. Entry fee: " . $contest['entry_fee']);
            }

            // Deduct Coins
            $this->userModel->update($user['id'], [
                'coins' => $user['coins'] - $contest['entry_fee']
            ]);

            // Create Participant
            $this->participantModel->create([
                'contest_id' => $id,
                'user_id' => $user['id'],
                'score' => 0,
                'status' => 'ongoing'
            ]);

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

        // Decode JSon content/options
        foreach($questions as &$q) {
            $q['content'] = json_decode($q['content'], true);
            $q['options'] = json_decode($q['options'], true);
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

        // Score logic
        $score = (int)$_POST['score'];
        $timeTaken = (int)$_POST['time_taken'];

        $this->participantModel->update($participant['id'], [
            'score' => $score,
            'time_taken' => $timeTaken,
            'created_at' => date('Y-m-d H:i:s') // Track finish time
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
