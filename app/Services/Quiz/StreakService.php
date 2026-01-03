<?php
namespace App\Services\Quiz;

use App\Core\Database;

class StreakService {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Call this when User finishes Daily Quiz
     */
    public function processVictory($userId, $baseCoins) {
        $pdo = $this->db->getPdo();
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // 1. Get User Streak Data
        $stmt = $pdo->prepare("SELECT * FROM user_streaks WHERE user_id = ?");
        $stmt->execute([$userId]);
        $streak = $stmt->fetch();
        
        if (!$streak) {
            // New User Record
            $ins = $pdo->prepare("INSERT INTO user_streaks (user_id, current_streak, highest_streak, last_activity_date) VALUES (?, 1, 1, ?)");
            $ins->execute([$userId, $today]);
            
            return [
                'coins' => $baseCoins,
                'streak' => 1,
                'multiplier' => 1.0,
                'is_new_record' => true
            ];
        }

        // 2. Check Logic
        if ($streak['last_activity_date'] === $today) {
            // Already played today? No extra streak progress.
            return [
                'coins' => $baseCoins, 
                'streak' => $streak['current_streak'], 
                'multiplier' => 1.0,
                'is_new_record' => false
            ];
        }

        $newStreak = 1; // Default reset
        $savedByFreeze = false;

        if ($streak['last_activity_date'] === $yesterday) {
            // CONTINUED STREAK!
            $newStreak = $streak['current_streak'] + 1;
        } else {
            // STREAK BROKEN (unless they have a freeze)
            if ($streak['streak_freeze_left'] > 0) {
                // Saved by Freeze!
                $pdo->prepare("UPDATE user_streaks SET streak_freeze_left = streak_freeze_left - 1 WHERE user_id = ?")->execute([$userId]);
                $newStreak = $streak['current_streak'] + 1;
                $savedByFreeze = true;
            } else {
                // Reset to 1 :(
                $newStreak = 1;
            }
        }

        // 3. Update Database
        $newHigh = max($newStreak, $streak['highest_streak']);
        
        $upd = $pdo->prepare("UPDATE user_streaks SET current_streak = ?, last_activity_date = ?, highest_streak = ?, updated_at = NOW() WHERE user_id = ?");
        $upd->execute([$newStreak, $today, $newHigh, $userId]);

        // 4. Calculate Multiplier (Max 2.0x)
        // Day 1 = 1.0x, Day 10 = 1.5x, Day 20 = 2.0x
        $multiplier = min(1 + (($newStreak - 1) * 0.05), 2.0);
        $finalCoins = ceil($baseCoins * $multiplier);

        return [
            'coins' => $finalCoins,
            'streak' => $newStreak,
            'multiplier' => $multiplier,
            'is_new_record' => ($newStreak > $streak['highest_streak']),
            'saved_by_freeze' => $savedByFreeze
        ];
    }
    
    /**
     * Get User Streak Info
     */
    public function getStreakInfo($userId) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM user_streaks WHERE user_id = ?");
        $stmt->execute([$userId]);
        $data = $stmt->fetch();
        
        if (!$data) {
            return [
                'current_streak' => 0,
                'highest_streak' => 0,
                'streak_freeze_left' => 0,
                'last_activity_date' => null
            ];
        }
        return $data;
    }
}
