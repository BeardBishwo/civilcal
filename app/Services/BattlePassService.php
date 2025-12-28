<?php

namespace App\Services;

use App\Core\Database;
use Exception;

class BattlePassService
{
    private $db;
    private $gamificationService;

    public function __construct()
    {
        $this->db = \App\Core\Database::getInstance();
        $this->gamificationService = new GamificationService();
    }

    /**
     * Get user's battle pass progress for current season
     */
    public function getProgress($userId)
    {
        $season = $this->getActiveSeason();
        if (!$season) return null;

        $sql = "SELECT * FROM user_battle_pass WHERE user_id = :uid AND season_id = :sid";
        $progress = $this->db->query($sql, ['uid' => $userId, 'sid' => $season['id']])->fetch();

        if (!$progress) {
            // Initialize progress
            $this->db->query("INSERT INTO user_battle_pass (user_id, season_id, current_xp, current_level, claimed_rewards) VALUES (:uid, :sid, 0, 1, '[]')", [
                'uid' => $userId,
                'sid' => $season['id']
            ]);
            $progress = $this->db->query($sql, ['uid' => $userId, 'sid' => $season['id']])->fetch();
        }

        $progress['claimed_rewards'] = json_decode($progress['claimed_rewards'] ?? '[]', true);
        return [
            'season' => $season,
            'progress' => $progress,
            'rewards' => $this->getRewards($season['id'])
        ];
    }

    /**
     * Add XP to user's battle pass
     */
    public function addXp($userId, $xpAmount)
    {
        $season = $this->getActiveSeason();
        if (!$season) return;

        $progress = $this->getProgress($userId)['progress'];
        $newXp = $progress['current_xp'] + $xpAmount;
        $xpPerLevel = 1000;
        $newLevel = floor($newXp / $xpPerLevel) + 1;

        $sql = "UPDATE user_battle_pass SET current_xp = :xp, current_level = :lvl WHERE user_id = :uid AND season_id = :sid";
        $this->db->query($sql, [
            'xp' => $newXp,
            'lvl' => $newLevel,
            'uid' => $userId,
            'sid' => $season['id']
        ]);

        if ($newLevel > $progress['current_level']) {
            // Level Up logic could trigger a notification here
        }
    }

    /**
     * Claim a reward
     */
    public function claimReward($userId, $rewardId)
    {
        $season = $this->getActiveSeason();
        $reward = $this->db->findOne('battle_pass_rewards', ['id' => $rewardId]);
        
        if (!$reward || $reward['season_id'] !== $season['id']) {
            throw new Exception("Invalid reward");
        }

        $progressData = $this->getProgress($userId);
        $progress = $progressData['progress'];

        if ($progress['current_level'] < $reward['level']) {
            return ['success' => false, 'message' => "Level too low!"];
        }

        if (in_array($rewardId, $progress['claimed_rewards'])) {
            return ['success' => false, 'message' => "Already claimed!"];
        }

        if ($reward['is_premium'] && !$progress['is_premium_unlocked']) {
            return ['success' => false, 'message' => "Premium Pass required!"];
        }

        // Grant Reward
        $this->grantReward($userId, $reward['reward_type'], $reward['reward_value']);

        // Mark as claimed
        $progress['claimed_rewards'][] = (int)$rewardId;
        $sql = "UPDATE user_battle_pass SET claimed_rewards = :claimed WHERE user_id = :uid AND season_id = :sid";
        $this->db->query($sql, [
            'claimed' => json_encode($progress['claimed_rewards']),
            'uid' => $userId,
            'sid' => $season['id']
        ]);

        return ['success' => true, 'message' => "Claimed reward successfully!"];
    }

    private function getActiveSeason()
    {
        return $this->db->query("SELECT * FROM battle_pass_seasons WHERE is_active = 1 LIMIT 1")->fetch();
    }

    private function getRewards($seasonId)
    {
        return $this->db->query("SELECT * FROM battle_pass_rewards WHERE season_id = :sid ORDER BY level ASC", ['sid' => $seasonId])->fetchAll();
    }

    private function grantReward($userId, $type, $value)
    {
        switch ($type) {
            case 'bricks':
            case 'cement':
            case 'steel':
            case 'coins':
                $this->db->query("UPDATE user_resources SET $type = $type + :val WHERE user_id = :uid", ['val' => $value, 'uid' => $userId]);
                break;
            case 'lifeline':
                $this->db->query("INSERT INTO user_lifelines (user_id, lifeline_type, quantity) VALUES (:uid, :type, 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1", [
                    'uid' => $userId,
                    'type' => $value
                ]);
                break;
            case 'building':
                $this->db->query("INSERT INTO user_city_buildings (user_id, building_type, level) VALUES (:uid, :type, 1)", [
                    'uid' => $userId,
                    'type' => $value
                ]);
                break;
        }
    }
}
