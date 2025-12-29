<?php

namespace App\Services;

use App\Core\Database;

/**
 * ActivityLogger - The "Black Box" of the Super-App
 * Handles secure logging of rewards and prevents cheating.
 */
class ActivityLogger
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Log an activity and award resources if clean.
     * 
     * @param int $userId
     * @param string $type Activity type (e.g., 'QUIZ_COMPLETE', 'NEWS_READ', 'TOOL_USED')
     * @param int $refId ID of the related item (post_id, tool_id, etc.)
     * @param int $coins Coins to award
     * @param int $timeSpent Time spent on page in seconds
     * @param int $scrollDepth Scroll depth percentage
     * @return array Result status and message
     */
    public function logAndReward($userId, $type, $refId, $coins, $timeSpent, $scrollDepth)
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $isSuspicious = 0;
        $flagReason = "";

        // 1. "Flash" Check - Impossible speed for reading/quizzing
        if ($type === 'NEWS_READ' && $timeSpent < 40) {
            $isSuspicious = 1;
            $flagReason .= "Time too short for reading ($timeSpent sec). ";
        }

        // 2. "Clone" Check - Same IP farming coins on the same item with multiple accounts
        $sqlClone = "SELECT COUNT(*) as count 
                     FROM activity_audit_logs 
                     WHERE device_ip = :ip 
                     AND reference_id = :ref 
                     AND activity_type = :type 
                     AND user_id != :uid 
                     AND created_at > (NOW() - INTERVAL 1 HOUR)";
        
        $cloneCount = (int)$this->db->query($sqlClone, [
            'ip' => $ip,
            'ref' => $refId,
            'type' => $type,
            'uid' => $userId
        ])->fetch()['count'];

        if ($cloneCount > 0) {
            $isSuspicious = 1;
            $flagReason .= "Potential multi-account farming detected on this IP ($ip). ";
        }

        // 3. Duplicate Claim Check - Prevent double rewards for the same item today
        $sqlDuplicate = "SELECT id 
                         FROM activity_audit_logs 
                         WHERE user_id = :uid 
                         AND reference_id = :ref 
                         AND activity_type = :type 
                         AND coins_earned > 0
                         AND DATE(created_at) = CURDATE()";
        
        $exists = $this->db->query($sqlDuplicate, [
            'uid' => $userId,
            'ref' => $refId,
            'type' => $type
        ])->fetch();

        if ($exists) {
            return ['status' => 'error', 'message' => 'You have already collected the reward for this today!'];
        }

        // 4. Shadow Ban Logic - If suspicious, award 0 coins but log it
        $actualReward = $isSuspicious ? 0 : $coins;

        // 5. Save to Audit Log
        $this->db->insert('activity_audit_logs', [
            'user_id' => $userId,
            'activity_type' => $type,
            'reference_id' => $refId,
            'coins_earned' => $actualReward,
            'time_spent_seconds' => $timeSpent,
            'scroll_depth_percent' => $scrollDepth,
            'device_ip' => $ip,
            'user_agent' => $userAgent,
            'is_suspicious' => $isSuspicious,
            'flag_reason' => $flagReason
        ]);

        // 6. Update User Balance if legitimate
        if ($actualReward > 0) {
            $this->db->query("UPDATE user_resources SET coins = coins + :amt WHERE user_id = :uid", [
                'amt' => $actualReward,
                'uid' => $userId
            ]);
        }

        return [
            'status' => $isSuspicious ? 'warning' : 'success',
            'message' => $isSuspicious ? 'Activity logged, but pending security verification.' : 'Reward added successfully!',
            'earned' => $actualReward,
            'is_flagged' => $isSuspicious
        ];
    }
}
