<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Models\User;
use Exception;

class ShopController extends BaseController
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index() {
        // Render Shop View
        $this->view('shop/index');
    }

    public function getItems() {
        try {
            $user = Auth::user();
            // Get all active items
            $stmt = $this->db->getPdo()->query("SELECT * FROM shop_items WHERE is_active = 1 ORDER BY price ASC");
            $items = $stmt->fetchAll();

            // Check what user owns
            $ownedIds = [];
            if ($user) {
                $pStmt = $this->db->getPdo()->prepare("SELECT item_id FROM user_purchases WHERE user_id = ?");
                $pStmt->execute([$user['id']]);
                $ownedIds = $pStmt->fetchAll(\PDO::FETCH_COLUMN);
            }

            // Append 'owned' flag
            foreach ($items as &$item) {
                $item['owned'] = in_array($item['id'], $ownedIds);
            }

            $this->json(['success' => true, 'items' => $items, 'user_coins' => $user ? $user['coins'] : 0]);

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

            // 2. Check if already owned (unless consumable? Assumed permanent for now)
            $check = $pdo->prepare("SELECT id FROM user_purchases WHERE user_id = ? AND item_id = ?");
            $check->execute([$user['id'], $itemId]);
            if ($check->fetchColumn()) throw new Exception('You already own this item.');

            // 3. Check Balance & Deduct
            $userModel = new User();
            if ($user['coins'] < $item['price']) {
                throw new Exception("Insufficient funds. You need {$item['price']} coins.");
            }

            // Transaction
            $pdo->beginTransaction();

            if (!$userModel->deductCoins($user['id'], $item['price'], "Purchased: " . $item['name'], $itemId)) {
                throw new Exception('Transaction failed.');
            }

            // Record Purchase
            $rec = $pdo->prepare("INSERT INTO user_purchases (user_id, item_id, cost) VALUES (?, ?, ?)");
            $rec->execute([$user['id'], $itemId, $item['price']]);

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
