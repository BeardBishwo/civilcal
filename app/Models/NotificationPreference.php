<?php

namespace App\Models;

use App\Core\Model;

class NotificationPreference extends Model
{
    protected $table = 'notification_preferences';

    /**
     * Get user preferences
     */
    public function getUserPreferences($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        $prefs = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Create default preferences if none exist
        if (!$prefs) {
            $this->createDefaultPreferences($userId);
            return $this->getUserPreferences($userId);
        }
        
        return $prefs;
    }

    /**
     * Create default preferences for user
     */
    public function createDefaultPreferences($userId)
    {
        $sql = "INSERT INTO {$this->table} (user_id) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }

    /**
     * Update user preferences
     */
    public function updatePreferences($userId, $preferences)
    {
        $allowedFields = [
            'email_notifications',
            'push_notifications',
            'system_notifications',
            'user_action_notifications',
            'email_frequency',
            'quiet_hours_start',
            'quiet_hours_end'
        ];

        $updates = [];
        $params = [];

        foreach ($preferences as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updates[] = "$key = ?";
                $params[] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = $userId;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Check if should send email notification
     */
    public function shouldSendEmail($userId, $type)
    {
        $prefs = $this->getUserPreferences($userId);
        
        if (!$prefs || !$prefs['email_notifications']) {
            return false;
        }

        // Check type-specific preferences
        $typeField = $type . '_notifications';
        if (isset($prefs[$typeField]) && !$prefs[$typeField]) {
            return false;
        }

        // Check quiet hours
        if ($prefs['quiet_hours_start'] && $prefs['quiet_hours_end']) {
            $currentTime = date('H:i:s');
            $start = $prefs['quiet_hours_start'];
            $end = $prefs['quiet_hours_end'];
            
            if ($start < $end) {
                // Normal range (e.g., 22:00 to 08:00 next day)
                if ($currentTime >= $start && $currentTime <= $end) {
                    return false;
                }
            } else {
                // Overnight range (e.g., 22:00 to 08:00)
                if ($currentTime >= $start || $currentTime <= $end) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if should send push notification
     */
    public function shouldSendPush($userId)
    {
        $prefs = $this->getUserPreferences($userId);
        return $prefs && $prefs['push_notifications'];
    }
}
