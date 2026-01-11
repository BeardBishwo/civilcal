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
     * Purchase or Use a lifeline directly using coins (In-Quiz)
     */
    public function useInQuiz($userId, $type, $questionId = null)
    {
        $prices = [
            '50_50' => 50,
            'skip' => 20,
            'poll' => 100,
            'freeze' => 30,
        ];

        if (!isset($prices[$type])) {
            throw new Exception("Invalid lifeline type");
        }

        $cost = $prices[$type];
        // 1. Deduct Coins atomically (and check balance in the same query)
        $stmt = $this->db->query("
            UPDATE user_resources 
            SET coins = coins - :cost 
            WHERE user_id = :uid AND coins >= :cost
        ", [
            'cost' => $cost,
            'uid' => $userId
        ]);

        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => "Need $cost coins! Try a daily quest."];
        }

        // 2. Log the activity (Security & Audit)
        require_once __DIR__ . '/ActivityLogger.php';
        $logger = new ActivityLogger();
        $logger->logAndReward($userId, 'LIFELINE_USE', 0, -$cost, 0, 0);

        $responseData = ['success' => true, 'message' => "Activated " . strtoupper($type) . "!"];

        // 3. Add logic-specific data
        if ($questionId && in_array($type, ['50_50', 'poll'])) {
            $question = $this->db->findOne('quiz_questions', ['id' => $questionId]);
            if ($question) {
                $options = json_decode($question['options'], true);
                $correctIndex = -1;
                foreach ($options as $idx => $opt) {
                    if (!empty($opt['is_correct'])) {
                        $correctIndex = $idx;
                        break;
                    }
                }

                if ($type === '50_50' && $correctIndex !== -1) {
                    $incorrectIndices = [];
                    foreach ($options as $idx => $opt) {
                        if ($idx !== $correctIndex) $incorrectIndices[] = $idx;
                    }
                    shuffle($incorrectIndices);
                    $responseData['hide_indices'] = array_slice($incorrectIndices, 0, 2);
                } elseif ($type === 'poll' && $correctIndex !== -1) {
                    $votes = [];
                    $total = 100;
                    $correctVote = rand(45, 80);
                    $votes[$correctIndex] = $correctVote;
                    $remaining = $total - $correctVote;
                    
                    $incorrectIndices = [];
                    foreach ($options as $idx => $opt) {
                        if ($idx !== $correctIndex) $incorrectIndices[] = $idx;
                    }
                    
                    foreach ($incorrectIndices as $i => $idx) {
                        if ($i === count($incorrectIndices) - 1) {
                            $votes[$idx] = $remaining;
                        } else {
                            $v = rand(0, $remaining);
                            $votes[$idx] = $v;
                            $remaining -= $v;
                        }
                    }
                    $responseData['poll_results'] = $votes;
                }
            }
        }

        return $responseData;
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
            'skip' => 0,
            'poll' => 0,
            'freeze' => 0
        ];
        
        foreach ($results as $row) {
            $inventory[$row['lifeline_type']] = (int)$row['quantity'];
        }
        
        return $inventory;
    }

    /**
     * Purchase a lifeline using coins
     */
    public function purchase($userId, $type, $quantity = 1)
    {
        $prices = [
            '50_50' => 100,
            'ai_hint' => 200,
            'freeze_time' => 300
        ];

        if (!isset($prices[$type])) {
            throw new Exception("Invalid lifeline type");
        }

        $cost = $prices[$type] * $quantity;
        // Deduct Coins atomically
        $stmt = $this->db->query("
            UPDATE user_resources 
            SET coins = coins - :cost 
            WHERE user_id = :uid AND coins >= :cost
        ", [
            'cost' => $cost,
            'uid' => $userId
        ]);

        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => "Not enough coins! You need $cost coins."];
        }

        // Add to Inventory
        $this->db->query("
            INSERT INTO user_lifelines (user_id, lifeline_type, quantity) 
            VALUES (:uid, :type, :qty) 
            ON DUPLICATE KEY UPDATE quantity = quantity + :qty
        ", [
            'uid' => $userId,
            'type' => $type,
            'qty' => $quantity
        ]);

        return ['success' => true, 'message' => "Purchased " . str_replace('_', ' ', $type) . " successfully!"];
    }

    /**
     * Use a lifeline
     */
    public function useLifeline($userId, $type)
    {
        // Deduct from Inventory atomically
        $stmt = $this->db->query("
            UPDATE user_lifelines 
            SET quantity = quantity - 1 
            WHERE user_id = :uid AND lifeline_type = :type AND quantity >= 1
        ", [
            'uid' => $userId,
            'type' => $type
        ]);

        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => "You don't have any " . str_replace('_', ' ', $type) . " left!"];
        }

        return ['success' => true, 'message' => "Used " . str_replace('_', ' ', $type)];
    }
}
