<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Services\GamificationService;

class GamificationController extends Controller
{
    private $gamificationService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->gamificationService = new GamificationService();
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
        
        $this->view('quiz/gamification/shop', [
            'title' => 'General Store',
            'inventory' => $inventory,
            'wallet' => $wallet
        ]);
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
        $lifelineService = new \App\Services\LifelineService();
        
        try {
            $result = $lifelineService->purchase($_SESSION['user_id'], $type);
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
        $lifelineService = new \App\Services\LifelineService();
        
        $result = $lifelineService->useLifeline($_SESSION['user_id'], $type);
        $this->json($result);
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
        $result = $this->gamificationService->craftPlanks($_SESSION['user_id'], $qty);
        $this->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * AJAX: Purchase Resource
     */
    public function purchaseResource()
    {
        $resource = $_POST['resource'] ?? '';
        $amount = (int)($_POST['amount'] ?? 1);
        
        $result = $this->gamificationService->purchaseResource($_SESSION['user_id'], $resource, $amount);
        $this->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Sell Resource (AJAX)
     */
    public function sellResource()
    {
        header('Content-Type: application/json');
        
        $resource = $_POST['resource'] ?? '';
        $amount = (int)($_POST['amount'] ?? 1);
        
        $result = $this->gamificationService->sellResource($_SESSION['user_id'], $resource, $amount);
        echo json_encode($result);
        exit;
    }

    /**
     * Purchase Bundle (AJAX)
     */
    public function purchaseBundle()
    {
        header('Content-Type: application/json');
        
        $bundleKey = $_POST['bundle'] ?? '';
        
        $result = $this->gamificationService->purchaseBundle($_SESSION['user_id'], $bundleKey);
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
        
        $this->view('quiz/gamification/battle_pass', array_merge($data, ['title' => 'Battle Pass: Civil Uprising']));
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
        $bpService = new \App\Services\BattlePassService();
        
        try {
            $result = $bpService->claimReward($_SESSION['user_id'], $rewardId);
            $this->json($result);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
