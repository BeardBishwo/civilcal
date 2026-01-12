<?php

namespace App\Services;

use App\Core\Database;
use Exception;


class GamificationService
{
    private $db;
    private EconomicSecurityService $economicSecurity;

    public function __construct()
    {
        $this->db = \App\Core\Database::getInstance();
        $this->economicSecurity = new EconomicSecurityService();
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
        if (!$isCorrect) {
            return;
        }

        if (!$this->economicSecurity->canReward($userId)) {
            return;
        }

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

                // Identity System (Dual XP)
                $this->db->query(
                    "UPDATE users SET xp = xp + :xp, total_xp = total_xp + :xp, season_xp = season_xp + :xp WHERE id = :uid",
                    ['xp' => $amount, 'uid' => $userId]
                );

                $this->checkAvatarUnlocks($userId);

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
     * Process Batch Rewards for Exam Completion
     * Prevents rate-limit issues by handling all rewards in one transaction
     */
    public function processExamRewards($userId, $correctAnswers, $attemptId)
    {
        if (empty($correctAnswers)) return;


        $this->db->getPdo()->beginTransaction();

        try {
            $this->initWallet($userId);

            // Calculate Total Loot
            $totalLoot = ['coins' => 0, 'bricks' => 0, 'cement' => 0, 'steel' => 0, 'xp' => 0];

            $rewards = [
                'easy' => ['coins' => 5, 'bricks' => 1, 'xp' => 50],
                'medium' => ['coins' => 10, 'bricks' => 5, 'cement' => 1, 'xp' => 100],
                'hard' => ['coins' => 20, 'steel' => 1, 'xp' => 200]
            ];

            foreach ($correctAnswers as $ans) {
                $diff = $ans['difficulty'] ?? 'medium';
                if (is_numeric($diff)) {
                    if ($diff <= 2) $diff = 'easy';
                    elseif ($diff <= 4) $diff = 'medium';
                    else $diff = 'hard';
                }

                $payout = $rewards[$diff] ?? $rewards['medium'];

                foreach ($payout as $res => $amt) {
                    if (isset($totalLoot[$res])) {
                        $totalLoot[$res] += $amt;
                    }
                }
            }

            // Apply to User Wallet in ONE transaction
            $setParts = [];
            $params = ['uid' => $userId];

            // Handle XP Separately
            if ($totalLoot['xp'] > 0) {
                $xpAmount = $totalLoot['xp'];

                // 1. Battle Pass & Missions
                $bp = new BattlePassService();
                $bp->addXp($userId, $xpAmount);
                $ms = new MissionService();
                $ms->updateProgress($userId, 'solve_questions');

                // 2. Identity System (Dual XP)
                $this->db->query(
                    "UPDATE users SET xp = xp + :xp1, total_xp = total_xp + :xp2, season_xp = season_xp + :xp3 WHERE id = :uid",
                    ['xp1' => $xpAmount, 'xp2' => $xpAmount, 'xp3' => $xpAmount, 'uid' => $userId]
                );

                // 3. Check for Rank Unlocks
                $this->checkAvatarUnlocks($userId);

                unset($totalLoot['xp']);
            }

            foreach ($totalLoot as $res => $amount) {
                if ($amount > 0) {
                    $setParts[] = "$res = $res + :$res";
                    $params[$res] = $amount;
                }
            }

            if (!empty($setParts)) {
                $sql = "UPDATE user_resources SET " . implode(', ', $setParts) . " WHERE user_id = :uid";
                $this->db->query($sql, $params);

                // Log aggregated transaction
                foreach ($totalLoot as $res => $amount) {
                    if ($amount > 0) {
                        $this->logTransaction($userId, $res, $amount, 'exam_reward', $attemptId);
                    }
                }
            }

            $this->db->getPdo()->commit();
        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            error_log("Gamification Transaction Failed: " . $e->getMessage());
        }
    }
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
        if ($quantity < 1) {
            return ['success' => false, 'message' => 'Invalid quantity'];
        }

        $this->initWallet($userId);

        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            // Lock wallet row
            $stmt = $pdo->prepare("
                SELECT wood_logs, coins FROM user_resources 
                WHERE user_id = :uid 
                FOR UPDATE
            ");
            $stmt->execute(['uid' => $userId]);
            $wallet = $stmt->fetch();

            if (!$wallet) {
                throw new \Exception('Wallet not found');
            }

            $logCost = $quantity;
            $coinCost = $quantity * 10; // 10 Coins labor fee (Official Handbook)
            $plankGain = $quantity * 4;

            if ($wallet['wood_logs'] < $logCost || $wallet['coins'] < $coinCost) {
                throw new \Exception('Insufficient Logs or Coins (Fee: 10 Coins/Log)');
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

            $pdo->commit();

            return ['success' => true, 'message' => "Crafted $plankGain Planks!"];
        } catch (\Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Purchase Resources (Temple Shop)
     */
    public function purchaseResource($userId, $resource, $amount = 1)
    {
        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            // Lock the user's wallet row to prevent race conditions
            $stmt = $pdo->prepare("
                SELECT coins FROM user_resources 
                WHERE user_id = :uid 
                FOR UPDATE
            ");
            $stmt->execute(['uid' => $userId]);
            $wallet = $stmt->fetch();

            if (!$wallet) {
                throw new \Exception('Wallet not found');
            }

            // Validate purchase AFTER locking
            $validation = $this->economicSecurity->validatePurchase($userId, $resource, $amount);

            if (!$validation['success']) {
                throw new \Exception($validation['message'] ?? 'Validation failed');
            }

            $resource = $validation['resource'];
            $amount = $validation['amount'];
            $totalCost = $validation['total_cost'];

            // Double-check balance after lock (redundant but safe)
            if ($wallet['coins'] < $totalCost) {
                throw new \Exception('Insufficient funds');
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

            $label = $validation['resource_label'] ?? $resource;

            $pdo->commit();

            return [
                'success' => true,
                'message' => "Purchased $amount " . $label,
                'resource' => $resource,
                'resource_label' => $label,
                'total_cost' => $totalCost
            ];
        } catch (\Exception $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Sell Resources (Quick Cash)
     */
    public function sellResource($userId, $resource, $amount = 1)
    {
        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            // Lock the user's wallet row
            $stmt = $pdo->prepare("
                SELECT * FROM user_resources 
                WHERE user_id = :uid 
                FOR UPDATE
            ");
            $stmt->execute(['uid' => $userId]);
            $wallet = $stmt->fetch();

            if (!$wallet) {
                throw new \Exception('Wallet not found');
            }

            // Validate sell AFTER locking
            $validation = $this->economicSecurity->validateSell($userId, $resource, $amount);

            if (!$validation['success']) {
                throw new \Exception($validation['message'] ?? 'Validation failed');
            }

            $resource = $validation['resource'];
            $amount = $validation['amount'];
            $gain = $validation['total_gain'];

            // Check if user has enough resources
            if (!isset($wallet[$resource]) || $wallet[$resource] < $amount) {
                throw new \Exception('Insufficient resources to sell');
            }

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

            $label = $validation['resource_label'] ?? $resource;

            $pdo->commit();

            return [
                'success' => true,
                'message' => "Sold $amount $label for $gain Coins",
                'resource' => $resource,
                'resource_label' => $label,
                'total_gain' => $gain
            ];
        } catch (\Exception $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Purchase Bundle (Bulk Offer)
     */
    public function purchaseBundle($userId, $bundleKey, $quantity = 1)
    {
        $bundles = SettingsService::get('economy_bundles', []);

        if (!isset($bundles[$bundleKey])) {
            return ['success' => false, 'message' => 'Bundle not found'];
        }

        $bundle = $bundles[$bundleKey];
        $wallet = $this->getWallet($userId);
        $cost = $bundle['buy'] * $quantity;

        if ($wallet['coins'] < $cost) {
            return ['success' => false, 'message' => 'Insufficient Coins'];
        }

        $resource = $bundle['resource'];
        $qtyGained = $bundle['qty'] * $quantity;

        $sql = "UPDATE user_resources 
                SET coins = coins - :cost, 
                    $resource = $resource + :qty 
                WHERE user_id = :uid";

        $this->db->query($sql, [
            'cost' => $cost,
            'qty' => $qtyGained,
            'uid' => $userId
        ]);

        $this->logTransaction($userId, 'coins', -$cost, 'bundle_purchase');
        $this->logTransaction($userId, $resource, $qtyGained, 'bundle_purchase');

        return ['success' => true, 'message' => "Purchased $quantity" . "x {$bundle['name']}!"];
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
            return ['success' => false, 'message' => 'Invalid building type'];
        }

        $cost = $costs[$buildingType];

        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            // Lock wallet row
            $stmt = $pdo->prepare("
                SELECT * FROM user_resources 
                WHERE user_id = :uid 
                FOR UPDATE
            ");
            $stmt->execute(['uid' => $userId]);
            $wallet = $stmt->fetch();

            if (!$wallet) {
                throw new \Exception('Wallet not found');
            }

            // Check Balance
            foreach ($cost as $res => $amount) {
                if (!isset($wallet[$res]) || $wallet[$res] < $amount) {
                    throw new \Exception("Not enough " . str_replace('_', ' ', ucfirst($res)));
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

            $pdo->commit();

            return ['success' => true, 'message' => "Built $buildingType successfully!"];
        } catch (\Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
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

    /**
     * Check and Unlock Avatars based on Rank/XP
     */
    public function checkAvatarUnlocks($userId)
    {
        $user = $this->db->findOne('users', ['id' => $userId]);
        if (!$user) return;

        $xp = $user['total_xp'] ?? 0;

        // Rank Thresholds for Avatar Unlocks
        $unlocks = [
            2000 => 'avatar_rank_03_supervisor', // Rank 3: Supervisor
            15000 => 'avatar_rank_05_senior',    // Rank 5: Senior Engineer
            100000 => 'avatar_rank_07_chief'     // Rank 7: Chief Engineer
        ];

        foreach ($unlocks as $threshold => $avatarKey) {
            if ($xp >= $threshold) {
                // Check if already owned
                $owned = $this->db->query(
                    "SELECT id FROM user_wardrobe WHERE user_id = ? AND item_key = ?",
                    [$userId, $avatarKey]
                )->fetch();

                if (!$owned) {
                    // Unlock It!
                    $this->db->query(
                        "INSERT INTO user_wardrobe (user_id, item_type, item_key) VALUES (?, 'avatar', ?)",
                        [$userId, $avatarKey]
                    );
                }
            }
        }
    }
}
