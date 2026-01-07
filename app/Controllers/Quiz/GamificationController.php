<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Services\GamificationService;
use App\Services\NonceService;
use App\Services\SecurityMonitor;

class GamificationController extends Controller
{
    private $gamificationService;
    private NonceService $nonceService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->gamificationService = new GamificationService();
        $this->nonceService = new NonceService();
    }

    /**
     * Show "My City" Dashboard
     */
    public function city()
    {
        $wallet = $this->gamificationService->getWallet($_SESSION['user_id']);
        $buildings = $this->db->query(
            "SELECT * FROM user_city_buildings WHERE user_id = :uid ORDER BY created_at DESC", 
            ['uid' => $_SESSION['user_id']]
        )->fetchAll();
        // Fetch User Buildings (Need method in service or direct DB call)
        // Let's rely on Service mostly, but for MVP fetching lists is fine here or via Service.
        // Let's add a getAllBuildings method to service for cleanliness? 
        // Or just raw query here since it's read-only view logic.
                $this->view('quiz/gamification/city', [
            'wallet' => $wallet,
            'buildings' => $buildings,
            'title' => 'My Civil City'
        ]);
    }

    /**
     * AJAX: Build Structure
     */
    public function build()
    {
        $type = $_POST['type'] ?? '';
        
        try {
            $result = $this->gamificationService->constructBuilding($_SESSION['user_id'], $type);
            
            if ($result['success']) {
                $this->json($result);
            } else {
                $this->json($result, 400);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Show Lifeline Shop
     */
    public function shop()
    {
        $lifelineService = new \App\Services\LifelineService();
        $inventory = $lifelineService->getInventory($_SESSION['user_id']);
        $wallet = $this->gamificationService->getWallet($_SESSION['user_id']);
        $bundles = \App\Services\SettingsService::get('economy_bundles', []);
        $cashPacks = \App\Services\SettingsService::get('economy_cash_packs', []);
        $shopNonce = $this->nonceService->generate($_SESSION['user_id'], 'shop');
        
        // Redirect to unified shop
        header('Location: /Bishwo_Calculator/shop');
        exit;
    }

    /**
     * API: Purchase Lifeline
     */
    public function purchaseLifeline()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $type = $_POST['type'] ?? '';
        $qty = (int)($_POST['quantity'] ?? 1);
        $lifelineService = new \App\Services\LifelineService();
        
        try {
            $result = $lifelineService->purchase($_SESSION['user_id'], $type, $qty);
            $this->json($result);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * API: Use Lifeline
     */
    public function useLifeline()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $type = $_POST['type'] ?? '';
        $nonce = $_POST['nonce'] ?? '';
        $trap = $_POST['trap_answer'] ?? '';

        if (!empty($trap)) {
            SecurityMonitor::log($_SESSION['user_id'] ?? null, 'honeypot_trigger', $_SERVER['REQUEST_URI'] ?? '', ['type' => $type], 'critical');
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        if (!$this->nonceService->validateAndConsume($nonce, $_SESSION['user_id'], 'lifeline')) {
            $this->json(['success' => false, 'message' => 'Invalid or expired request token'], 400);
            return;
        }

        $rateLimiter = new \App\Services\RateLimiter();
        $rateCheck = $rateLimiter->check($_SESSION['user_id'], '/api/quiz/use-lifeline', 5, 60);
        if (!$rateCheck['allowed']) {
            $this->json(['success' => false, 'message' => 'Too many lifeline requests'], 429);
            return;
        }

        $lifelineService = new \App\Services\LifelineService();
        $result = $lifelineService->useLifeline($_SESSION['user_id'], $type);

        if ($result['success']) {
            $newNonce = $this->nonceService->generate($_SESSION['user_id'], 'lifeline');
            $result['nonce'] = $newNonce['nonce'] ?? null;
        }

        $this->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Show Sawmill (Crafting)
     */
    public function sawmill()
    {
        $wallet = $this->gamificationService->getWallet($_SESSION['user_id']);
        $this->view('quiz/gamification/sawmill', [
            'wallet' => $wallet,
            'title' => 'The Sawmill'
        ]);
    }

    /**
     * AJAX: Craft Planks
     */
    public function craft()
    {
        $qty = (int)($_POST['quantity'] ?? 1);
        $trap = $_POST['trap_answer'] ?? '';
        if (!empty($trap)) {
            SecurityMonitor::log($_SESSION['user_id'] ?? null, 'honeypot_trigger', $_SERVER['REQUEST_URI'] ?? '', [], 'critical');
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        $result = $this->gamificationService->craftPlanks($_SESSION['user_id'], $qty);
        $this->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * AJAX: Purchase Resource
     */
    public function purchaseResource()
    {
        // Security: Check IP ban
        $ip = \App\Services\SecurityValidator::getClientIp();
        if (\App\Services\SecurityValidator::isIpBanned($ip)) {
            $this->json(['success' => false, 'message' => 'Access denied'], 403);
            return;
        }
        
        // Security: Rate limiting
        $rateLimiter = new \App\Services\RateLimiter();
        $rateCheck = $rateLimiter->check($_SESSION['user_id'], '/api/shop/purchase-resource');
        
        if (!$rateCheck['allowed']) {
            $this->json([
                'success' => false, 
                'message' => 'Too many requests. Try again in ' . $rateCheck['reset_in'] . ' seconds.'
            ], 429);
            return;
        }
        
        $resource = $_POST['resource'] ?? '';
        $amount = (int)($_POST['amount'] ?? 1);
        $nonce = $_POST['nonce'] ?? '';

        if (!$this->nonceService->validateAndConsume($nonce, $_SESSION['user_id'], 'shop')) {
            $this->json(['success' => false, 'message' => 'Invalid or expired request token'], 400);
            return;
        }
        
        // Security: Validate resource key
        if (!\App\Services\SecurityValidator::validateResource($resource)) {
            $this->json(['success' => false, 'message' => 'Invalid resource'], 400);
            return;
        }
        
        // Security: Validate amount
        $amount = \App\Services\SecurityValidator::validatePurchaseAmount($amount);
        if ($amount === false) {
            $this->json(['success' => false, 'message' => 'Invalid amount'], 400);
            return;
        }
        
        // Security: Check for suspicious patterns
        if (\App\Services\SecurityMonitor::detectSuspiciousActivity($_SESSION['user_id'])) {
            $this->json(['success' => false, 'message' => 'Account flagged for review'], 403);
            return;
        }
        
        $result = $this->gamificationService->purchaseResource($_SESSION['user_id'], $resource, $amount);
        if ($result['success']) {
            $newNonce = $this->nonceService->generate($_SESSION['user_id'], 'shop');
            $result['nonce'] = $newNonce['nonce'] ?? null;
        }
        $this->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Sell Resource (AJAX)
     */
    public function sellResource()
    {
        header('Content-Type: application/json');
        
        // Security: Check IP ban
        $ip = \App\Services\SecurityValidator::getClientIp();
        if (\App\Services\SecurityValidator::isIpBanned($ip)) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        // Security: Rate limiting
        $rateLimiter = new \App\Services\RateLimiter();
        $rateCheck = $rateLimiter->check($_SESSION['user_id'], '/api/shop/sell-resource');
        
        if (!$rateCheck['allowed']) {
            echo json_encode([
                'success' => false, 
                'message' => 'Too many requests. Try again in ' . $rateCheck['reset_in'] . ' seconds.'
            ]);
            exit;
        }
        
        $resource = $_POST['resource'] ?? '';
        $amount = (int)($_POST['amount'] ?? 1);
        
        // Security: Validate resource key
        if (!\App\Services\SecurityValidator::validateResource($resource)) {
            echo json_encode(['success' => false, 'message' => 'Invalid resource']);
            exit;
        }
        
        // Security: Validate amount
        $amount = \App\Services\SecurityValidator::validateInteger($amount, 1, 1000);
        if ($amount === false) {
            echo json_encode(['success' => false, 'message' => 'Invalid amount']);
            exit;
        }
        
        $result = $this->gamificationService->sellResource($_SESSION['user_id'], $resource, $amount);
        if ($result['success']) {
            $newNonce = $this->nonceService->generate($_SESSION['user_id'], 'shop');
            $result['nonce'] = $newNonce['nonce'] ?? null;
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Purchase Bundle (AJAX)
     */
    public function purchaseBundle()
    {
        header('Content-Type: application/json');
        
        // Security: Check IP ban
        $ip = \App\Services\SecurityValidator::getClientIp();
        if (\App\Services\SecurityValidator::isIpBanned($ip)) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        // Security: Rate limiting
        $rateLimiter = new \App\Services\RateLimiter();
        $rateCheck = $rateLimiter->check($_SESSION['user_id'], '/api/shop/purchase-bundle');
        
        if (!$rateCheck['allowed']) {
            echo json_encode([
                'success' => false, 
                'message' => 'Too many requests. Try again in ' . $rateCheck['reset_in'] . ' seconds.'
            ]);
            exit;
        }
        
        $bundleKey = $_POST['bundle'] ?? '';
        $qty = (int)($_POST['quantity'] ?? 1);
        $nonce = $_POST['nonce'] ?? '';

        if (!$this->nonceService->validateAndConsume($nonce, $_SESSION['user_id'], 'shop')) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired request token']);
            exit;
        }
        
        // Security: Validate bundle key
        if (!\App\Services\SecurityValidator::validateBundle($bundleKey)) {
            echo json_encode(['success' => false, 'message' => 'Invalid bundle']);
            exit;
        }
        
        $result = $this->gamificationService->purchaseBundle($_SESSION['user_id'], $bundleKey, $qty);
        if ($result['success']) {
            $newNonce = $this->nonceService->generate($_SESSION['user_id'], 'shop');
            $result['nonce'] = $newNonce['nonce'] ?? null;
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Show Battle Pass
     */
    public function battlePass()
    {
        $bpService = new \App\Services\BattlePassService();
        $data = $bpService->getProgress($_SESSION['user_id']);
        $claimNonce = $this->nonceService->generate($_SESSION['user_id'], 'battle_pass_claim');
        
        // Flatten the data structure for the view
        $this->view('quiz/gamification/battle_pass', [
            'title' => 'Battle Pass: Civil Uprising',
            'progress' => $data['progress'],
            'rewards' => $data['rewards'],
            'season' => $data['season'],
            'claimNonce' => $claimNonce['nonce'] ?? null
        ]);
    }

    /**
     * API: Claim Battle Pass Reward
     */
    public function claimReward()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $rewardId = $_POST['reward_id'] ?? 0;
        $nonce = $_POST['nonce'] ?? '';
        $trap = $_POST['trap_answer'] ?? '';

        if (!empty($trap)) {
            SecurityMonitor::log($_SESSION['user_id'] ?? null, 'honeypot_trigger', $_SERVER['REQUEST_URI'] ?? '', ['reward_id' => $rewardId], 'critical');
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        if (!$this->nonceService->validateAndConsume($nonce, $_SESSION['user_id'], 'battle_pass_claim')) {
            $this->json(['success' => false, 'message' => 'Invalid or expired request token'], 400);
            return;
        }

        $rateLimiter = new \App\Services\RateLimiter();
        $rateCheck = $rateLimiter->check($_SESSION['user_id'], '/api/battle-pass/claim', 5, 60);
        if (!$rateCheck['allowed']) {
            $this->json(['success' => false, 'message' => 'Too many requests'], 429);
            return;
        }

        $bpService = new \App\Services\BattlePassService();
        
        try {
            $result = $bpService->claimReward($_SESSION['user_id'], $rewardId);
            if ($result['success']) {
                $newNonce = $this->nonceService->generate($_SESSION['user_id'], 'battle_pass_claim');
                $result['nonce'] = $newNonce['nonce'] ?? null;
            }
            $this->json($result);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
