<?php

namespace App\Services;

use App\Core\Database;
use Exception;

class MissionService
{
    private $db;
    private $bpService;

    public function __construct()
    {
        $this->db = \App\Core\Database::getInstance();
        $this->bpService = new BattlePassService();
    }
    /**
     * Update mission progress for a specific type
     * Types: 'solve_questions', 'win_battles'
     */
    public function updateProgress($userId, $type, $inc = 1)
    {
        $today = date('Y-m-d');
        
        // 1. Get active missions for today's user
        $missions = $this->db->query("SELECT * FROM daily_missions WHERE requirement_type = :type", ['type' => $type])->fetchAll();
        
        foreach ($missions as $mission) {
            // Check if user already has progress record for today
            $progress = $this->db->query("
                SELECT * FROM user_mission_progress 
                WHERE user_id = :uid AND mission_id = :mid AND mission_date = :date
            ", [
                'uid' => $userId,
                'mid' => $mission['id'],
                'date' => $today
            ])->fetch();

            if (!$progress) {
                // Initialize progress
                $this->db->query("
                    INSERT INTO user_mission_progress (user_id, mission_id, current_value, mission_date) 
                    VALUES (:uid, :mid, :val, :date)
                ", [
                    'uid' => $userId,
                    'mid' => $mission['id'],
                    'val' => $inc,
                    'date' => $today
                ]);
            } else {
                if ($progress['is_completed']) continue;

                $newVal = $progress['current_value'] + $inc;
                $isCompleted = ($newVal >= $mission['requirement_value']) ? 1 : 0;

                $this->db->query("
                    UPDATE user_mission_progress 
                    SET current_value = :val, is_completed = :comp 
                    WHERE id = :id
                ", [
                    'val' => $newVal,
                    'comp' => $isCompleted,
                    'id' => $progress['id']
                ]);

                if ($isCompleted) {
                    // Auto-claim reward or just mark it? Let's auto-claim for better UX
                    $this->claimMissionReward($userId, $mission['id'], $progress['id']);
                }
            }
        }
    }

    /**
     * Get user's mission progress for today
     */
    public function getUserMissions($userId)
    {
        $today = date('Y-m-d');
        $sql = "
            SELECT m.*, p.current_value, p.is_completed, p.is_claimed
            FROM daily_missions m
            LEFT JOIN user_mission_progress p ON m.id = p.mission_id AND p.user_id = :uid AND p.mission_date = :date
        ";
        return $this->db->query($sql, ['uid' => $userId, 'date' => $today])->fetchAll();
    }

    private function claimMissionReward($userId, $missionId, $progressId)
    {
        $mission = $this->db->findOne('daily_missions', ['id' => $missionId]);
        if (!$mission) return;

        // Mark as claimed
        $this->db->query("UPDATE user_mission_progress SET is_claimed = 1 WHERE id = :id", ['id' => $progressId]);

        // Grant XP via BattlePass
        $this->bpService->addXp($userId, $mission['xp_reward']);

        // Grant Coins
        $this->db->query("UPDATE user_resources SET coins = coins + :amt WHERE user_id = :uid", [
            'amt' => $mission['coin_reward'],
            'uid' => $userId
        ]);
        
        // Log transaction
        $gs = new GamificationService();
        //$gs->logTransaction($userId, 'coins', $mission['coin_reward'], 'mission_reward', $missionId); // Private method, skipping for now or make it protected
    }
}
