<?php

namespace App\Services;

use App\Core\Database;
use Exception;

class GamificationService
{
    private $db;

    public function __construct()
    {
        $this->db = \App\Core\Database::getInstance();
    }

    /**
     * Initialize User Resources (if not exists)
     */
    public function initWallet($userId)
    {
        $sql = "INSERT IGNORE INTO user_resources (user_id, bricks, cement, steel, coins, sand, wood_logs, wood_planks) VALUES (:uid, 0, 0, 0, 0, 0, 0, 0)";
        $this->db->query($sql, ['uid' => $userId]);
    }

    /**
     * Grant Reward based on Question Difficulty
     */
    public function rewardUser($userId, $isCorrect, $difficulty = 'medium', $referenceId = null)
    {
        if (!$isCorrect) return; // No reward for wrong answers

        $this->initWallet($userId);

        // Define Rewards (Official Handbook)
        $rewards = [
            'easy' => ['coins' => 5, 'bricks' => 1, 'xp' => 50],
            'medium' => ['coins' => 10, 'bricks' => 5, 'cement' => 1, 'xp' => 100],
            'hard' => ['coins' => 20, 'steel' => 1, 'xp' => 200]
        ];

        // Map numeric difficulty (1-5) to string if needed
        if (is_numeric($difficulty)) {
            if ($difficulty <= 2) $difficulty = 'easy';
            elseif ($difficulty <= 4) $difficulty = 'medium';
            else $difficulty = 'hard';
        }

        $payout = $rewards[$difficulty] ?? $rewards['medium'];
        
        $setParts = [];
        $params = ['uid' => $userId];
        
        foreach ($payout as $resource => $amount) {
            if ($resource === 'xp') {
                $bp = new BattlePassService();
                $bp->addXp($userId, $amount);
                
                // Trigger Mission Progress
                $ms = new MissionService();
                $ms->updateProgress($userId, 'solve_questions');
                
                continue;
            }
            $setParts[] = "$resource = $resource + :$resource";
            $params[$resource] = $amount;
            
            // Log Transaction
            $this->logTransaction($userId, $resource, $amount, 'quiz_reward', $referenceId);
        }

        if (!empty($setParts)) {
            $sql = "UPDATE user_resources SET " . implode(', ', $setParts) . " WHERE user_id = :uid";
            $this->db->query($sql, $params);
        }

        return $payout;
    }

    /**
     * Process Daily Login Bonus (Grant Logs/Steel based on streak)
     */
    public function processDailyLoginBonus($userId)
    {
        $this->initWallet($userId);
        
        $user = $this->db->findOne('users', ['id' => $userId]);
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        if ($user && $user['last_login_reward_at'] !== $today) {
            $streak = (int)($user['login_streak'] ?? 0);
            
            // Check if streak is maintained
            if ($user['last_login_reward_at'] === $yesterday) {
                $streak++;
            } else {
                $streak = 1;
            }
            
            $rewards = [];
            if ($streak % 7 === 0) {
                // Day 7 Reward: 1 Steel Bundle (10 Steel)
                $rewards['steel'] = 10;
                $sql = "UPDATE user_resources SET steel = steel + 10 WHERE user_id = :uid";
                $this->db->query($sql, ['uid' => $userId]);
            } else {
                // Standard Reward: 1 Log
                $rewards['wood_logs'] = 1;
                $sql = "UPDATE user_resources SET wood_logs = wood_logs + 1 WHERE user_id = :uid";
                $this->db->query($sql, ['uid' => $userId]);
            }
            
            // Update user streak and last reward date
            $this->db->query("UPDATE users SET last_login_reward_at = :today, login_streak = :streak WHERE id = :uid", [
                'today' => $today,
                'streak' => $streak,
                'uid' => $userId
            ]);
            
            foreach ($rewards as $res => $amt) {
                $this->logTransaction($userId, $res, $amt, 'daily_login');
            }
            
            $rewards['streak'] = $streak; // Pass streak for UI notification
            return ['success' => true, 'rewards' => $rewards];
        }
        
        return ['success' => false, 'message' => 'Already claimed today'];
    }

    /**
     * Craft Planks from Logs (Sawmill)
     * 1 Log + 5 Coins -> 4 Planks
     */
    public function craftPlanks($userId, $quantity = 1)
    {
        $this->initWallet($userId);
        $wallet = $this->getWallet($userId);
        
        $logCost = $quantity;
        $coinCost = $quantity * 10; // 10 Coins labor fee (Official Handbook)
        $plankGain = $quantity * 4;
        
        if ($wallet['wood_logs'] < $logCost || $wallet['coins'] < $coinCost) {
            return ['success' => false, 'message' => 'Insufficient Logs or Coins (Fee: 10 Coins/Log)'];
        }
        
        $sql = "UPDATE user_resources 
                SET wood_logs = wood_logs - :logs, 
                    coins = coins - :coins, 
                    wood_planks = wood_planks + :planks 
                WHERE user_id = :uid";
        
        $this->db->query($sql, [
            'logs' => $logCost,
            'coins' => $coinCost,
            'planks' => $plankGain,
            'uid' => $userId
        ]);
        
        $this->logTransaction($userId, 'wood_logs', -$logCost, 'crafting');
        $this->logTransaction($userId, 'coins', -$coinCost, 'crafting');
        $this->logTransaction($userId, 'wood_planks', $plankGain, 'crafting');
        
        return ['success' => true, 'message' => "Crafted $plankGain Planks!"];
    }

    /**
     * Purchase Resources (Temple Shop)
     */
    public function purchaseResource($userId, $resource, $amount = 1)
    {
        $resources = SettingsService::get('economy_resources', []);
        
        if (!isset($resources[$resource]) || !isset($resources[$resource]['buy'])) {
            return ['success' => false, 'message' => 'Resource not available'];
        }
        
        $price = $resources[$resource]['buy'];
        if ($price <= 0) return ['success' => false, 'message' => 'This item cannot be purchased'];

        $totalCost = $price * $amount;
        $wallet = $this->getWallet($userId);
        
        if ($wallet['coins'] < $totalCost) {
            return ['success' => false, 'message' => 'Insufficient Coins'];
        }
        
        $sql = "UPDATE user_resources 
                SET coins = coins - :cost, 
                    $resource = $resource + :amt 
                WHERE user_id = :uid";
        
        $this->db->query($sql, [
            'cost' => $totalCost,
            'amt' => $amount,
            'uid' => $userId
        ]);
        
        $this->logTransaction($userId, 'coins', -$totalCost, 'shop_purchase');
        $this->logTransaction($userId, $resource, $amount, 'shop_purchase');
        
        return ['success' => true, 'message' => "Purchased $amount " . ($resources[$resource]['name'] ?? $resource)];
    }

    /**
     * Sell Resources (Quick Cash)
     */
    public function sellResource($userId, $resource, $amount = 1)
    {
        $resources = SettingsService::get('economy_resources', []);
        
        if (!isset($resources[$resource]) || !isset($resources[$resource]['sell'])) {
            return ['success' => false, 'message' => 'Resource cannot be sold'];
        }
        
        $price = $resources[$resource]['sell'];
        if ($price <= 0) return ['success' => false, 'message' => 'This item has no resale value'];

        $wallet = $this->getWallet($userId);
        if ($wallet[$resource] < $amount) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        $gain = $price * $amount;
        
        $sql = "UPDATE user_resources 
                SET coins = coins + :gain, 
                    $resource = $resource - :amt 
                WHERE user_id = :uid";
        
        $this->db->query($sql, [
            'gain' => $gain,
            'amt' => $amount,
            'uid' => $userId
        ]);
        
        $this->logTransaction($userId, $resource, -$amount, 'shop_sell');
        $this->logTransaction($userId, 'coins', $gain, 'shop_sell');
        
        return ['success' => true, 'message' => "Sold $amount for $gain Coins"];
    }

    /**
     * Construct a Building
     */
    public function constructBuilding($userId, $buildingType)
    {
        $this->initWallet($userId);
        
        // Define Costs (Updated to use more materials)
        $costs = [
            'house' => ['bricks' => 100, 'wood_planks' => 20, 'sand' => 50, 'cement' => 10],
            'road' => ['cement' => 50, 'sand' => 200],
            'bridge' => ['bricks' => 500, 'steel' => 200, 'cement' => 100],
            'tower' => ['bricks' => 1000, 'cement' => 500, 'steel' => 500, 'wood_planks' => 100]
        ];

        if (!isset($costs[$buildingType])) {
            throw new Exception("Invalid building type");
        }

        $cost = $costs[$buildingType];
        
        // Check Balance
        $wallet = $this->getWallet($userId);
        
        foreach ($cost as $res => $amount) {
            if ($wallet[$res] < $amount) {
                return ['success' => false, 'message' => "Not enough " . str_replace('_', ' ', ucfirst($res))];
            }
        }

        // Deduct Resources
        $setParts = [];
        $params = ['uid' => $userId];
        
        foreach ($cost as $res => $amount) {
            $setParts[] = "$res = $res - :$res";
            $params[$res] = $amount;
            $this->logTransaction($userId, $res, -$amount, 'building_cost');
        }

        $sql = "UPDATE user_resources SET " . implode(', ', $setParts) . " WHERE user_id = :uid";
        $this->db->query($sql, $params);

        // Add Building
        $sqlBuild = "INSERT INTO user_city_buildings (user_id, building_type, level) VALUES (:uid, :type, 1)";
        $this->db->query($sqlBuild, ['uid' => $userId, 'type' => $buildingType]);

        return ['success' => true, 'message' => "Built $buildingType successfully!"];
    }
    
    /**
     * Get User Resources
     */
    public function getWallet($userId)
    {
        $this->initWallet($userId);
        return $this->db->findOne('user_resources', ['user_id' => $userId]);
    }

    private function logTransaction($userId, $type, $amount, $source, $refId = null)
    {
        $sql = "INSERT INTO user_resource_logs (user_id, resource_type, amount, source, reference_id) 
                VALUES (:uid, :type, :amt, :src, :ref)";
        $this->db->query($sql, [
            'uid' => $userId, 
            'type' => $type, 
            'amt' => $amount, 
            'src' => $source, 
            'ref' => $refId
        ]);
    }
}
