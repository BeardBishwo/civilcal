<?php

namespace App\Services;

use App\Core\Database;
use Exception;

class LifelineService
{
    private $db;
    private $gamificationService;

    public function __construct()
    {
        $this->db = \App\Core\Database::getInstance();
        $this->gamificationService = new GamificationService();
    }

    /**
     * Get user's lifeline inventory
     */
    public function getInventory($userId)
    {
        $sql = "SELECT lifeline_type, quantity FROM user_lifelines WHERE user_id = :uid";
        $results = $this->db->query($sql, ['uid' => $userId])->fetchAll();
        
        $inventory = [
            '50_50' => 0,
            'ai_hint' => 0,
            'freeze_time' => 0
        ];
        
        foreach ($results as $row) {
            $inventory[$row['lifeline_type']] = (int)$row['quantity'];
        }
        
        return $inventory;
    }

    /**
     * Purchase a lifeline using coins
     */
    public function purchase($userId, $type)
    {
        $prices = [
            '50_50' => 100,
            'ai_hint' => 200,
            'freeze_time' => 300
        ];

        if (!isset($prices[$type])) {
            throw new Exception("Invalid lifeline type");
        }

        $cost = $prices[$type];
        $wallet = $this->gamificationService->getWallet($userId);

        if ($wallet['coins'] < $cost) {
            return ['success' => false, 'message' => "Not enough coins! You need $cost coins."];
        }

        // Deduct Coins
        $this->db->query("UPDATE user_resources SET coins = coins - :cost WHERE user_id = :uid", [
            'cost' => $cost,
            'uid' => $userId
        ]);

        // Add to Inventory
        $this->db->query("
            INSERT INTO user_lifelines (user_id, lifeline_type, quantity) 
            VALUES (:uid, :type, 1) 
            ON DUPLICATE KEY UPDATE quantity = quantity + 1
        ", [
            'uid' => $userId,
            'type' => $type
        ]);

        return ['success' => true, 'message' => "Purchased " . str_replace('_', ' ', $type) . " successfully!"];
    }

    /**
     * Use a lifeline
     */
    public function useLifeline($userId, $type)
    {
        $inventory = $this->getInventory($userId);
        
        if (($inventory[$type] ?? 0) <= 0) {
            return ['success' => false, 'message' => "You don't have any " . str_replace('_', ' ', $type) . " left!"];
        }

        // Deduct from Inventory
        $this->db->query("UPDATE user_lifelines SET quantity = quantity - 1 WHERE user_id = :uid AND lifeline_type = :type", [
            'uid' => $userId,
            'type' => $type
        ]);

        return ['success' => true, 'message' => "Used " . str_replace('_', ' ', $type)];
    }
}
