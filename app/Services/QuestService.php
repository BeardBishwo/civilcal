<?php

namespace App\Services;

use App\Core\Database;

/**
 * QuestService - Manages Daily Tool Quests
 */
class QuestService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get the Tool of the Day
     * Uses a deterministic seed based on date so all users see the same tool
     */
    public function getToolOfTheDay()
    {
        $dateId = date('Ymd');
        srand($dateId); // Seed random with date
        
        // This is a placeholder since we don't have a direct tools table 
        // We know tools are files in modules/ directory.
        // For production, we would query a 'calculators' table.
        // Let's assume there's a list or we fetch from DB.
        $tools = $this->db->find('calculators', ['status' => 'active']);
        
        if (empty($tools)) return null;

        $index = rand(0, count($tools) - 1);
        $tool = $tools[$index];
        
        srand(); // Reset seed
        return $tool;
    }

    /**
     * Check if user completed the daily quest
     */
    public function isCompleted($userId)
    {
        $sql = "SELECT id FROM activity_audit_logs 
                WHERE user_id = :uid 
                AND activity_type = 'DAILY_QUEST_COMPLETE' 
                AND DATE(created_at) = CURDATE()";
        
        return (bool)$this->db->query($sql, ['uid' => $userId])->fetch();
    }

    /**
     * Reward the user for completing the daily quest
     */
    public function completeQuest($userId)
    {
        if ($this->isCompleted($userId)) {
            return ['success' => false, 'message' => 'Quest already completed today!'];
        }

        require_once __DIR__ . '/ActivityLogger.php';
        $logger = new ActivityLogger();
        
        // Reward: 100 Coins + 5 Bricks
        return $logger->logAndReward($userId, 'DAILY_QUEST_COMPLETE', 0, 100, 0, 0);
    }
}
