<?php

namespace App\Services;

use App\Core\Database;

/**
 * Profile Service
 * 
 * Handles all profile-related business logic including:
 * - Profile data management
 * - Avatar updates
 * - Password changes
 * - Activity history
 * - Statistics
 * - Account deletion
 * 
 * @package App\Services
 */
class ProfileService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get user profile with all related data
     * 
     * @param int $userId User ID
     * @return array|null Profile data or null if not found
     */
    public function getUserProfile($userId)
    {
        $user = $this->db->findOne('users', ['id' => $userId]);
        
        if (!$user) {
            return null;
        }

        // Get additional profile data
        $stats = $this->db->findOne('user_stats', ['user_id' => $userId]);
        $wallet = $this->db->findOne('user_resources', ['user_id' => $userId]);
        
        return [
            'user' => $user,
            'stats' => $stats ?? [],
            'wallet' => $wallet ?? []
        ];
    }

    /**
     * Update user profile
     * 
     * @param int $userId User ID
     * @param array $data Profile data to update
     * @return array Result with success status
     */
    public function updateProfile($userId, array $data)
    {
        // Validate and sanitize data
        $allowedFields = ['first_name', 'last_name', 'bio', 'phone', 'location', 'website'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = \App\Core\Validator::sanitize($data[$field]);
            }
        }

        if (empty($updateData)) {
            return ['success' => false, 'message' => 'No valid fields to update'];
        }

        $updateData['updated_at'] = date('Y-m-d H:i:s');

        try {
            $result = $this->db->update('users', $updateData, 'id = :id', ['id' => $userId]);
            
            return [
                'success' => true,
                'message' => 'Profile updated successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update user avatar
     * 
     * @param int $userId User ID
     * @param string $avatarPath Path to avatar image
     * @return array Result with success status
     */
    public function updateAvatar($userId, $avatarPath)
    {
        try {
            $result = $this->db->update(
                'users',
                ['avatar' => $avatarPath, 'updated_at' => date('Y-m-d H:i:s')],
                'id = :id',
                ['id' => $userId]
            );
            
            return [
                'success' => true,
                'message' => 'Avatar updated successfully',
                'avatar_path' => $avatarPath
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update avatar: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Change user password
     * 
     * @param int $userId User ID
     * @param string $oldPassword Current password
     * @param string $newPassword New password
     * @return array Result with success status
     */
    public function updatePassword($userId, $oldPassword, $newPassword)
    {
        $user = $this->db->findOne('users', ['id' => $userId]);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Verify old password
        if (!password_verify($oldPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'New password must be at least 8 characters'];
        }

        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $result = $this->db->update(
                'users',
                ['password' => $hashedPassword, 'updated_at' => date('Y-m-d H:i:s')],
                'id = :id',
                ['id' => $userId]
            );
            
            return [
                'success' => true,
                'message' => 'Password changed successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to change password: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user activity history
     * 
     * @param int $userId User ID
     * @param int $limit Number of records to fetch
     * @param int $offset Offset for pagination
     * @return array Activity history
     */
    public function getActivityHistory($userId, $limit = 50, $offset = 0)
    {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT * FROM activity_logs 
                WHERE user_id = :userId 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get user statistics
     * 
     * @param int $userId User ID
     * @return array Statistics data
     */
    public function getStatistics($userId)
    {
        $stats = $this->db->findOne('user_stats', ['user_id' => $userId]);
        
        if (!$stats) {
            return [
                'calculations_count' => 0,
                'quizzes_completed' => 0,
                'total_xp' => 0,
                'rank' => 'Beginner'
            ];
        }

        // Get additional calculated stats
        $sql = "SELECT 
                    COUNT(*) as total_calculations,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as week_calculations
                FROM calculation_history 
                WHERE user_id = :userId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        $calcStats = $stmt->fetch();

        return array_merge($stats, $calcStats ?: []);
    }

    /**
     * Delete user account
     * 
     * @param int $userId User ID
     * @param string $password Password confirmation
     * @return array Result with success status
     */
    public function deleteAccount($userId, $password)
    {
        $user = $this->db->findOne('users', ['id' => $userId]);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Password is incorrect'];
        }

        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            // Delete related data (cascade should handle most, but being explicit)
            $this->db->delete('user_stats', 'user_id = :uid', ['uid' => $userId]);
            $this->db->delete('user_resources', 'user_id = :uid', ['uid' => $userId]);
            $this->db->delete('calculation_history', 'user_id = :uid', ['uid' => $userId]);
            $this->db->delete('activity_logs', 'user_id = :uid', ['uid' => $userId]);
            
            // Finally delete user
            $this->db->delete('users', 'id = :id', ['id' => $userId]);
            
            $pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Account deleted successfully'
            ];
        } catch (\Exception $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Failed to delete account: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user preferences
     * 
     * @param int $userId User ID
     * @return array User preferences
     */
    public function getPreferences($userId)
    {
        $sql = "SELECT * FROM user_preferences WHERE user_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        
        return $stmt->fetch() ?: [];
    }

    /**
     * Enable Two-Factor Authentication
     * 
     * @param int $userId
     * @return array secret and qr_code_url
     */
    public function enableTwoFactor($userId)
    {
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        // Store secret temporarily or permanently marked as 'pending'
        // For simplicity, we assume we update the user record but look for an 'is_2fa_enabled' flag
        // or just return it for the frontend to verify first.
        
        $user = $this->db->findOne('users', ['id' => $userId]);
        $email = $user['email'];
        
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'Bishwo Calculator',
            $email,
            $secret
        );
        
        return [
            'success' => true,
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl
        ];
    }

    /**
     * Verify and Activate 2FA
     * 
     * @param int $userId
     * @param string $secret The secret key being verified
     * @param string $code The 6-digit code from app
     */
    public function verifyTwoFactor($userId, $secret, $code)
    {
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        
        $valid = $google2fa->verifyKey($secret, $code);
        
        if ($valid) {
            // Persist the secret and enable 2FA
            $this->db->update('users', [
                'two_factor_secret' => $secret,
                'two_factor_enabled' => 1,
                'two_factor_confirmed_at' => date('Y-m-d H:i:s')
            ], 'id = :id', ['id' => $userId]);
            
            return ['success' => true, 'message' => '2FA enabled successfully'];
        }
        
        return ['success' => false, 'message' => 'Invalid verification code'];
    }

    /**
     * Disable 2FA
     */
    public function disableTwoFactor($userId, $password)
    {
        // verify password first
        $user = $this->db->findOne('users', ['id' => $userId]);
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid password'];
        }

        $this->db->update('users', [
            'two_factor_secret' => null,
            'two_factor_enabled' => 0,
            'two_factor_confirmed_at' => null
        ], 'id = :id', ['id' => $userId]);

        return ['success' => true, 'message' => '2FA disabled successfully'];
    }

    /**
     * Update user preferences
     * 
     * @param int $userId User ID
     * @param array $preferences Preferences to update
     * @return array Result with success status
     */
    public function updatePreferences($userId, array $preferences)
    {
        try {
            // Check if preferences exist
            $existing = $this->getPreferences($userId);
            
            if ($existing) {
                $result = $this->db->update(
                    'user_preferences',
                    $preferences,
                    'user_id = :uid',
                    ['uid' => $userId]
                );
            } else {
                $preferences['user_id'] = $userId;
                $result = $this->db->insert('user_preferences', $preferences);
            }
            
            return [
                'success' => true,
                'message' => 'Preferences updated successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update preferences: ' . $e->getMessage()
            ];
        }
    }
}
