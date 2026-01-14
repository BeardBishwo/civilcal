<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Services\LobbyService;
use App\Services\BotEngine;
use App\Services\NonceService;
use App\Services\SecurityValidator;
use App\Services\SecurityMonitor;
use App\Services\RateLimiter;

class MultiplayerController extends Controller
{
    private $lobbyService;
    private $botEngine;
    private NonceService $nonceService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->lobbyService = new LobbyService();
        $this->botEngine = new BotEngine();
        $this->nonceService = new NonceService();
    }

    /**
     * Show Multiplayer Menu
     */
    public function index()
    {
        // Simple view to Create or Join
        $this->view('quiz/multiplayer/menu', ['title' => 'Multiplayer Battle']);
    }

    /**
     * Create a Lobby
     */
    public function create()
    {
        // Assume Exam ID 1 for MVP or select from POST
        $examId = $_POST['exam_id'] ?? 1;

        $result = $this->lobbyService->createLobby($examId, $_SESSION['user_id']);

        $this->redirect('/quiz/lobby/' . $result['code']);
    }

    /**
     * Join a Lobby
     */
    public function join()
    {
        $code = $_POST['code'] ?? '';
        try {
            $lobby = $this->lobbyService->joinLobby($code, $_SESSION['user_id']);
            $this->redirect('/quiz/lobby/' . $lobby['code']);
        } catch (\Exception $e) {
            // Flash error
            $this->redirect('/quiz/multiplayer?error=Lobby+not+found');
        }
    }

    /**
     * Lobby Waiting Room / Game Interface
     */
    public function lobby($code)
    {
        $lobby = $this->db->findOne('quiz_lobbies', ['code' => $code]);
        if (!$lobby) $this->redirect('/quiz/multiplayer');

        $wallet = (new \App\Services\GamificationService())->getWallet($_SESSION['user_id']);
        $participant = $this->db->findOne('quiz_lobby_participants', ['lobby_id' => $lobby['id'], 'user_id' => $_SESSION['user_id']]);
        $wagerNonce = $this->nonceService->generate($_SESSION['user_id'], 'wager');
        $lifelineNonce = $this->nonceService->generate($_SESSION['user_id'], 'lifeline');

        // This is the hybrid view. JS handles "Waiting" vs "Active" state.
        $this->view('quiz/multiplayer/lobby', [
            'code' => $code,
            'wallet' => $wallet,
            'participant' => $participant,
            'wagerNonce' => $wagerNonce['nonce'] ?? null,
            'lifelineNonce' => $lifelineNonce['nonce'] ?? null,
        ]);
    }

    /**
     * API: Place Wager
     */
    public function placeWager()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $lobbyId = $_POST['lobby_id'] ?? 0;
        $amount = (int)($_POST['amount'] ?? 0);
        $nonce = $_POST['nonce'] ?? '';
        $trap = $_POST['trap_answer'] ?? '';

        if (!empty($trap)) {
            SecurityMonitor::log($_SESSION['user_id'] ?? null, 'honeypot_trigger', $_SERVER['REQUEST_URI'] ?? '', ['lobby_id' => $lobbyId], 'critical');
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        if (!$this->nonceService->validateAndConsume($nonce, $_SESSION['user_id'], 'wager')) {
            $this->json(['success' => false, 'message' => 'Invalid or expired request token'], 400);
            return;
        }

        $rateLimiter = new RateLimiter();
        $rateCheck = $rateLimiter->check($_SESSION['user_id'], '/api/lobby/wager', 5, 30);
        if (!$rateCheck['allowed']) {
            $this->json(['success' => false, 'message' => 'Too many requests, slow down'], 429);
            return;
        }

        $amount = SecurityValidator::validateInteger($amount, 1, 100000);
        if ($amount === false) {
            $this->json(['success' => false, 'message' => 'Invalid amount'], 400);
            return;
        }

        // ATOMIC UPDATE: Prevent race condition by checking balance in WHERE clause
        $result = $this->db->query(
            "UPDATE user_resources SET coins = coins - :amt WHERE user_id = :uid AND coins >= :amt",
            [
                'amt' => $amount,
                'uid' => $_SESSION['user_id']
            ]
        );

        // Check if update succeeded (rowCount will be 0 if insufficient funds)
        if ($result->rowCount() === 0) {
            $this->json(['success' => false, 'message' => 'Insufficient coins'], 400);
            return;
        }

        // Update wager amount
        $this->db->query("UPDATE quiz_lobby_participants SET wager_amount = :amt WHERE lobby_id = :lid AND user_id = :uid", [
            'amt' => $amount,
            'lid' => $lobbyId,
            'uid' => $_SESSION['user_id']
        ]);

        // Get updated balance
        $gamification = new \App\Services\GamificationService();
        $wallet = $gamification->getWallet($_SESSION['user_id']);

        $newNonce = $this->nonceService->generate($_SESSION['user_id'], 'wager');

        $this->json([
            'success' => true,
            'message' => 'Wager placed!',
            'new_balance' => $wallet['coins'],
            'nonce' => $newNonce['nonce'] ?? null
        ]);
    }

    /**
     * API: Get Lobby Status (Pulse)
     */
    public function status($code)
    {
        // Resolve ID from code (or pass ID)
        $lobby = $this->lobbyService->joinLobby($code, $_SESSION['user_id']); // Re-verify join/fetch

        if ($lobby['status'] === 'active') {
            // Trigger Bot Engine
            // Question ID is managed by client-side sync or server-side schedule?
            // "Ghost Protocol" Plan: "Server Logic: Iterate bots... Delay... Answer"
            // We need current question index.
            // For MVP, client sends what Q they are on? Or Time based?
            // Time based is safer.

            // Calc Question Index based on Time
            $elapsed = time() - strtotime($lobby['start_time']);
            // Assume 30s per question? 
            $qDuration = 20;
            $qIndex = floor($elapsed / $qDuration);
            $qStartTime = strtotime($lobby['start_time']) + ($qIndex * $qDuration);

            $this->botEngine->processGamePulse($lobby['id'], $qIndex, $qStartTime);
        }

        $data = $this->lobbyService->getLobbyStatus($lobby['id'], $_SESSION['user_id']);

        $this->json($data);
    }
}
