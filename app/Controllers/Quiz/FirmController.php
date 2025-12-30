<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Services\FirmService;
use App\Services\GamificationService;
use App\Services\NonceService;
use App\Services\SecurityMonitor;
use App\Services\SecurityValidator;
use App\Services\RateLimiter;
use Exception;

class FirmController extends Controller
{
    private $firmService;
    private NonceService $nonceService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->firmService = new FirmService();
        $this->nonceService = new NonceService();
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
        $joinNonce = $this->nonceService->generate($userId, 'firm_join');
        $createNonce = $this->nonceService->generate($userId, 'firm_create');

        $this->view('quiz/firms/index', [
            'firms' => $allFirms,
            'joinNonce' => $joinNonce['nonce'] ?? null,
            'createNonce' => $createNonce['nonce'] ?? null
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
        $donateNonce = $this->nonceService->generate($userId, 'firm_donate');
        
        $requests = [];
        if ($member['role'] === 'Leader') {
            $requests = $this->firmService->getJoinRequests($member['guild_id']);
        }

        $this->view('quiz/firms/dashboard', array_merge($data, [
            'wallet' => $wallet,
            'my_role' => $member['role'],
            'requests' => $requests,
            'donateNonce' => $donateNonce['nonce'] ?? null
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
            $nonce = $_POST['nonce'] ?? '';
            $trap = $_POST['trap_answer'] ?? '';

            if (!empty($trap)) {
                SecurityMonitor::log($userId ?? null, 'honeypot_trigger', $_SERVER['REQUEST_URI'] ?? '', ['action' => 'create_firm'], 'critical');
                $this->redirect('/quiz/firms?error=Invalid+request');
            }

            if (!$this->nonceService->validateAndConsume($nonce, $userId, 'firm_create')) {
                $this->redirect('/quiz/firms?error=Invalid+token');
            }

            $rateLimiter = new RateLimiter();
            $rateCheck = $rateLimiter->check($userId, '/api/firms/create', 3, 300);
            if (!$rateCheck['allowed']) {
                $this->redirect('/quiz/firms?error=Too+many+requests');
            }

            $this->firmService->create($userId, $name, $desc);
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
            $nonce = $_POST['nonce'] ?? '';
            $trap = $_POST['trap_answer'] ?? '';

            if (!empty($trap)) {
                SecurityMonitor::log($userId ?? null, 'honeypot_trigger', $_SERVER['REQUEST_URI'] ?? '', ['type' => $type], 'critical');
                $this->json(['success' => false, 'message' => 'Invalid request'], 400);
                return;
            }

            if (!$this->nonceService->validateAndConsume($nonce, $userId, 'firm_donate')) {
                $this->json(['success' => false, 'message' => 'Invalid or expired request token'], 400);
                return;
            }

            $rateLimiter = new RateLimiter();
            $rateCheck = $rateLimiter->check($userId, '/api/firms/donate', 5, 60);
            if (!$rateCheck['allowed']) {
                $this->json(['success' => false, 'message' => 'Too many requests'], 429);
                return;
            }

            if (!SecurityValidator::validateResource($type)) {
                $this->json(['success' => false, 'message' => 'Invalid resource'], 400);
                return;
            }

            $amount = SecurityValidator::validateInteger($amount, 1, 1000000);
            if ($amount === false) {
                $this->json(['success' => false, 'message' => 'Invalid amount'], 400);
                return;
            }

            $this->firmService->donate($userId, $type, $amount);
            $newNonce = $this->nonceService->generate($userId, 'firm_donate');
            $this->json(['success' => true, 'message' => 'Donation successful!', 'nonce' => $newNonce['nonce'] ?? null]);
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
            $nonce = $_POST['nonce'] ?? '';
            $trap = $_POST['trap_answer'] ?? '';

            if (!empty($trap)) {
                SecurityMonitor::log($userId ?? null, 'honeypot_trigger', $_SERVER['REQUEST_URI'] ?? '', ['guild_id' => $guildId], 'critical');
                $this->json(['success' => false, 'message' => 'Invalid request'], 400);
                return;
            }

            if (!$this->nonceService->validateAndConsume($nonce, $userId, 'firm_join')) {
                $this->json(['success' => false, 'message' => 'Invalid or expired request token'], 400);
                return;
            }

            $rateLimiter = new RateLimiter();
            $rateCheck = $rateLimiter->check($userId, '/api/firms/join', 5, 60);
            if (!$rateCheck['allowed']) {
                $this->json(['success' => false, 'message' => 'Too many requests'], 429);
                return;
            }

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
