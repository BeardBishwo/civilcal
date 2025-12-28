<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Services\FirmService;
use App\Services\GamificationService;
use Exception;

class FirmController extends Controller
{
    private $firmService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->firmService = new FirmService();
    }

    /**
     * Firm Discovery / Landing Page
     */
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? 0;
        $member = $this->db->findOne('guild_members', ['user_id' => $userId]);

        if ($member) {
            $this->redirect('/quiz/firms/dashboard');
        }

        $allFirms = $this->db->query("SELECT g.*, (SELECT COUNT(*) FROM guild_members WHERE guild_id = g.id) as member_count FROM guilds g ORDER BY g.level DESC, g.xp DESC")->fetchAll();

        $this->view('quiz/firms/index', [
            'firms' => $allFirms
        ]);
    }

    /**
     * Firm Dashboard
     */
    public function dashboard()
    {
        $userId = $_SESSION['user_id'] ?? 0;
        $member = $this->db->findOne('guild_members', ['user_id' => $userId]);

        if (!$member) {
            $this->redirect('/quiz/firms');
        }

        $data = $this->firmService->getFirmData($member['guild_id']);
        $wallet = (new GamificationService())->getWallet($userId);
        
        $requests = [];
        if ($member['role'] === 'Leader') {
            $requests = $this->firmService->getJoinRequests($member['guild_id']);
        }

        $this->view('quiz/firms/dashboard', array_merge($data, [
            'wallet' => $wallet,
            'my_role' => $member['role'],
            'requests' => $requests
        ]));
    }

    /**
     * API: Create Firm
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/quiz/firms');
        }

        try {
            $name = $_POST['name'] ?? '';
            $desc = $_POST['description'] ?? '';
            $userId = $_SESSION['user_id'];

            if (strlen($name) < 3) throw new Exception("Firm name must be at least 3 characters.");

            $this->firmService->createFirm($userId, $name, $desc);
            $this->redirect('/quiz/firms/dashboard');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/quiz/firms');
        }
    }

    /**
     * API: Donate
     */
    public function donate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 405);
        }

        try {
            $type = $_POST['type'] ?? '';
            $amount = (int)($_POST['amount'] ?? 0);
            $userId = $_SESSION['user_id'];

            $this->firmService->donate($userId, $type, $amount);
            $this->json(['success' => true, 'message' => 'Donation successful!']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * API: Join Request
     */
    public function join()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        try {
            $guildId = $_POST['guild_id'] ?? 0;
            $userId = $_SESSION['user_id'];
            $this->firmService->requestJoin($userId, $guildId);
            $this->json(['success' => true, 'message' => 'Request sent!']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * API: Handle Join Request
     */
    public function handleJoinRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        try {
            $requestId = $_POST['request_id'] ?? 0;
            $action = $_POST['action'] ?? ''; // 'approve' or 'decline'
            $leaderId = $_SESSION['user_id'];

            $this->firmService->handleRequest($leaderId, $requestId, $action);
            $this->json(['success' => true, 'message' => "Request " . ($action === 'approve' ? 'approved' : 'declined') . " successfully!"]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * API: Leave Firm
     */
    public function leave()
    {
        try {
            $userId = $_SESSION['user_id'];
            $this->firmService->leaveFirm($userId);
            $this->redirect('/quiz/firms');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/quiz/firms/dashboard');
        }
    }
}
