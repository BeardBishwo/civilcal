<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\User;
use Exception;
use App\Services\GamificationService;
use App\Services\LifelineService;
use App\Services\NonceService;

class ShopController extends Controller
{
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->db = Database::getInstance();
    }

    public function index() {
        $lifelineService = new \App\Services\LifelineService();
        $gamificationService = new \App\Services\GamificationService();
        $nonceService = new \App\Services\NonceService();
        
        $inventory = $lifelineService->getInventory(Auth::id());
        $wallet = $gamificationService->getWallet(Auth::id());
        $bundles = \App\Services\SettingsService::get('economy_bundles', []);
        $cashPacks = \App\Services\SettingsService::get('economy_cash_packs', []);
        $shopNonce = $nonceService->generate(Auth::id(), 'shop');
        
        $this->view('quiz/gamification/shop', [
            'title' => 'Civil Cal Market',
            'inventory' => $inventory,
            'wallet' => $wallet,
            'bundles' => $bundles,
            'cashPacks' => $cashPacks,
            'shopNonce' => $shopNonce['nonce'] ?? null,
        ]);
    }

    public function getItems() {
        try {
            $user = Auth::user();
            // Get all active items
            $stmt = $this->db->getPdo()->query("SELECT * FROM shop_items WHERE is_active = 1 ORDER BY price ASC");
            $items = $stmt->fetchAll();

            // Check what user owns
            $ownedIds = [];
            $userCoins = 0;
            if ($user) {
                $pStmt = $this->db->getPdo()->prepare("SELECT item_id FROM user_purchases WHERE user_id = ?");
                $pStmt->execute([$user->id]); // Object access
                $ownedIds = $pStmt->fetchAll(\PDO::FETCH_COLUMN);
                
                // Fetch fresh coins
                $userModel = new User();
                $userCoins = $userModel->getCoins($user->id); 
            }

            // Append 'owned' flag
            foreach ($items as &$item) {
                $item['owned'] = in_array($item['id'], $ownedIds);
            }

            $this->json(['success' => true, 'items' => $items, 'user_coins' => $userCoins]);

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function purchase() {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);

            $input = json_decode(file_get_contents('php://input'), true);
            $itemId = $input['item_id'] ?? null;
            if (!$itemId) throw new Exception('Item ID required.');

            $pdo = $this->db->getPdo();

            // 1. Get Item Details
            $stmt = $pdo->prepare("SELECT * FROM shop_items WHERE id = ?");
            $stmt->execute([$itemId]);
            $item = $stmt->fetch();
            if (!$item) throw new Exception('Item not found.');

            // 2. Check if already owned
            $check = $pdo->prepare("SELECT id FROM user_purchases WHERE user_id = ? AND item_id = ?");
            $check->execute([$user->id, $itemId]); // Object access
            if ($check->fetchColumn()) throw new Exception('You already own this item.');

            // 3. Check Balance & Deduct
            $userModel = new User();
            $currentCoins = $userModel->getCoins($user->id);

            if ($currentCoins < $item['price']) {
                throw new Exception("Insufficient funds. You need {$item['price']} coins.");
            }

            // Transaction
            $pdo->beginTransaction();

            if (!$userModel->deductCoins($user->id, $item['price'], "Purchased: " . $item['name'], $itemId)) {
                throw new Exception('Transaction failed.');
            }

            // Record Purchase
            $rec = $pdo->prepare("INSERT INTO user_purchases (user_id, item_id, cost) VALUES (?, ?, ?)");
            $rec->execute([$user->id, $itemId, $item['price']]);

            // Apply Effects (e.g., if it's a badge, set it?)
            // For now, just recording ownership is enough.
            
            $pdo->commit();

            $this->json(['success' => true, 'message' => 'Purchase successful!']);

        } catch (Exception $e) {
            if ($this->db->getPdo()->inTransaction()) $this->db->getPdo()->rollBack();
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
