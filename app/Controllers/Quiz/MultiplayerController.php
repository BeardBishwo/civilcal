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
        $db = \App\Core\Database::getInstance();
        $exam = $db->findOne('quiz_exams', ['status' => 'published']);

        // Simple view to Create or Join
        $this->view('quiz/multiplayer/menu', [
            'title' => 'Multiplayer Battle',
            'exam_id' => $exam['id'] ?? null
        ]);
    }

    /**
     * Create a Lobby
     */
    public function create()
    {
        // Select from POST or find a fallback
        $examId = $_POST['exam_id'] ?? null;

        if (!$examId) {
            $db = \App\Core\Database::getInstance();
            $exam = $db->findOne('quiz_exams', ['status' => 'published']);
            $examId = $exam['id'] ?? null;
        }

        if (!$examId) {
            $this->redirect('/quiz/battle');
            return;
        }

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

        // Firebase Token
        $firebaseToken = '';
        try {
            $firebaseService = new \App\Services\FirebaseAuthService();
            $firebaseToken = $firebaseService->createCustomToken($_SESSION['user_id']);
        } catch (\Exception $e) {
            error_log("Firebase Token Gen Error: " . $e->getMessage());
        }

        // This is the hybrid view. JS handles "Waiting" vs "Active" state.
        $this->view('quiz/multiplayer/lobby', [
            'code' => $code,
            'wallet' => $wallet,
            'participant' => $participant,
            'wagerNonce' => $wagerNonce['nonce'] ?? null,
            'lifelineNonce' => $lifelineNonce['nonce'] ?? null,
            'firebaseToken' => $firebaseToken
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
     * API: Trigger Bot Injection (Auto-Fill)
     * Called by Host Client when timer is low and room not full.
     */
    public function injectBots($code)
    {
        try {
            $db = \App\Core\Database::getInstance();
            $lobby = $db->findOne('quiz_lobbies', ['code' => $code]);

            if (!$lobby || $lobby['status'] !== 'waiting') {
                $this->json(['message' => 'Game active or invalid']);
                return;
            }

            if ($lobby['host_user_id'] != $_SESSION['user_id']) {
                $this->json(['message' => 'Not Host'], 403);
                return;
            }

            // Trigger injection via Service
            $this->lobbyService->checkGhostInjection($lobby, count($this->lobbyService->getParticipants($lobby['id'])));

            $this->json(['success' => true]);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Finish Game & Sync Scores
     * Called by Host when game ends.
     */
    public function finish($code)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $scores = $data['scores'] ?? [];

        try {
            $db = \App\Core\Database::getInstance();
            $lobby = $db->findOne('quiz_lobbies', ['code' => $code]);

            if (!$lobby || $lobby['status'] !== 'active') {
                if ($lobby['status'] === 'finished') {
                    $this->json(['success' => true]);
                    return;
                }
            }

            if ($lobby['host_user_id'] != $_SESSION['user_id']) {
                $this->json(['message' => 'Not Host'], 403);
                return;
            }

            // 1. Sync Scores to MySQL
            $MAX_POSSIBLE_SCORE = 50 * 4; // 50 questions max * 4 pts = 200. Adjust based on Exam Config if needed.
            // Better: Get Question Count from Exam.
            $exam = $db->findOne('quiz_exams', ['id' => $lobby['exam_id']]);
            // Assume 20 questions for safety if not found.
            // Actually, let's set a hard cap to stop Infinite Money Glitch.
            // If user score > 500, flag it.
            $HARD_CAP = 500;

            foreach ($scores as $score) {
                $isBot = $score['is_bot'] ?? false;
                $pId = $score['id'];
                $finalScore = (int)$score['score'];

                // --- SECURITY CHECK ---
                if (!$isBot && $finalScore > $HARD_CAP) {
                    SecurityMonitor::log($_SESSION['user_id'], 'score_manipulation', "Lobby {$lobby['code']}", ['score' => $finalScore], 'critical');
                    // Cap the score or ban?
                    // Cap it for now to prevent economy crash.
                    $finalScore = 0;
                }
                // Check negative overflow
                if ($finalScore < -200) $finalScore = -200;

                $sql = "UPDATE quiz_lobby_participants SET current_score = :score WHERE lobby_id = :lid AND ";
                $params = ['score' => $finalScore, 'lid' => $lobby['id']];

                if ($isBot) {
                    $sql .= "bot_profile_id = :pid";
                    $params['pid'] = $pId;
                } else {
                    $sql .= "user_id = :pid";
                    $params['pid'] = $pId;
                }

                $db->query($sql, $params);
            }

            // 2. Mark Finished
            $db->query("UPDATE quiz_lobbies SET status = 'finished' WHERE id = :id", ['id' => $lobby['id']]);

            // 3. Distribute Rewards
            $this->lobbyService->distributeWagerRewards($lobby['id']);

            $this->json(['success' => true]);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
