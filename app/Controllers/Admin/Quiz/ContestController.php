<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Models\Contest;
use App\Models\ContestParticipant;
use App\Services\Quiz\ContestService;
use Exception;

class ContestController extends Controller
{
    private $contestModel;
    private $participantModel;
    private $contestService;

    public function __construct()
    {
        parent::__construct();
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
             header('Location: ' . app_base_url('login'));
             exit;
        }
        $this->contestModel = new Contest();
        $this->participantModel = new ContestParticipant();
        $this->contestService = new ContestService();
    }

    /**
     * Contest Dashboard
     */
    public function index()
    {
        $contests = $this->contestModel->findAll();
        
        // Get AI Manager status from settings
        $stmt = $this->db->getPdo()->prepare("SELECT value FROM settings WHERE `key` = 'contest_auto_manager' LIMIT 1");
        $stmt->execute();
        $res = $stmt->fetch();
        $autoManager = $res ? (bool)$res['value'] : false;

        $this->view->render('admin/quiz/contests/index', [
            'page_title' => 'Contest Engine',
            'contests' => $contests,
            'autoManager' => $autoManager
        ]);
    }

    /**
     * Create Manual Contest
     */
    public function store()
    {
        try {
            if (empty($_POST['title'])) throw new Exception("Title is required");
            if (empty($_POST['start_time'])) throw new Exception("Start time is required");
            if (empty($_POST['questions'])) throw new Exception("Questions are required");

            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'] ?? '',
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'entry_fee' => (int)($_POST['entry_fee'] ?? 0),
                'prize_pool' => (int)($_POST['prize_pool'] ?? 1000),
                'winner_count' => (int)($_POST['winner_count'] ?? 1),
                'questions' => json_encode(explode(',', $_POST['questions'])),
                'status' => 'upcoming'
            ];

            $this->contestModel->create($data);
            $this->jsonResponse(['success' => true, 'message' => 'Contest Created Successfully']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Toggle AI Auto-Manager
     */
    public function toggleAuto()
    {
        try {
            $status = isset($_POST['status']) && $_POST['status'] == '1' ? '1' : '0';
            
            // Upsert setting
            $stmt = $this->db->getPdo()->prepare("INSERT INTO settings (`key`, `value`) VALUES ('contest_auto_manager', ?) ON DUPLICATE KEY UPDATE `value` = ?");
            $stmt->execute([$status, $status]);

            $this->jsonResponse(['success' => true, 'message' => 'AI Manager Status Updated']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Manually Process Results (Lucky Draw)
     */
    public function process($id)
    {
        try {
            $success = $this->contestService->processResults($id);
            if ($success) {
                $this->jsonResponse(['success' => true, 'message' => 'Results processed and winners awarded!']);
            } else {
                throw new Exception("Failed to process results. Check if contest has participants.");
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
