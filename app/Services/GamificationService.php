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
        $sql = "INSERT IGNORE INTO user_resources (user_id, bricks, cement, steel, coins) VALUES (:uid, 0, 0, 0, 0)";
        $this->db->query($sql, ['uid' => $userId]);
    }

    /**
     * Grant Reward based on Question Difficulty
     */
    public function rewardUser($userId, $isCorrect, $difficulty = 'medium', $referenceId = null)
    {
        if (!$isCorrect) return; // No reward for wrong answers

        $this->initWallet($userId);

        // Define Rewards
        $rewards = [
            'easy' => ['bricks' => 5, 'xp' => 50],
            'medium' => ['bricks' => 10, 'cement' => 2, 'xp' => 100],
            'hard' => ['bricks' => 20, 'steel' => 5, 'xp' => 200]
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
     * Construct a Building
     */
    public function constructBuilding($userId, $buildingType)
    {
        $this->initWallet($userId);
        
        // Define Costs
        $costs = [
            'house' => ['bricks' => 100],
            'road' => ['cement' => 50],
            'bridge' => ['bricks' => 500, 'steel' => 200],
            'tower' => ['bricks' => 1000, 'cement' => 500, 'steel' => 500]
        ];

        if (!isset($costs[$buildingType])) {
            throw new Exception("Invalid building type");
        }

        $cost = $costs[$buildingType];
        
        // Check Balance
        $wallet = $this->db->findOne('user_resources', ['user_id' => $userId]);
        
        foreach ($cost as $res => $amount) {
            if ($wallet[$res] < $amount) {
                return ['success' => false, 'message' => "Not enough " . ucfirst($res)];
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
