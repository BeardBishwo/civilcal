<?php

namespace App\Models;

use App\Core\Database;

class User {
    private $db;
    public $id;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findByEmail($email) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function find($id) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO users (email, password, first_name, last_name, company, profession, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['email'],
            $data['password'],
            isset($data['first_name']) ? $data['first_name'] : '',
            isset($data['last_name']) ? $data['last_name'] : '',
            isset($data['company']) ? $data['company'] : '',
            isset($data['profession']) ? $data['profession'] : '',
            isset($data['role']) ? $data['role'] : 'user'
        ]);
        
        return $this->db->getPdo()->lastInsertId();
    }
    
    public function getAll() {
        $stmt = $this->db->getPdo()->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function updateSubscription($userId, $subscriptionId, $status = 'active') {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE users 
            SET subscription_id = ?, subscription_status = ?, subscription_ends_at = DATE_ADD(NOW(), INTERVAL 1 MONTH)
            WHERE id = ?
        ");
        
        return $stmt->execute([$subscriptionId, $status, $userId]);
    }
    
    // Profile Management Methods
    public function getProfileCompletion($userId) {
        $user = $this->find($userId);
        if (!$user) return 0;
        
        $fields = [
            'avatar' => $user['avatar'],
            'professional_title' => $user['professional_title'],
            'company' => $user['company'],
            'phone' => $user['phone'],
            'bio' => $user['bio'],
            'website' => $user['website'],
            'location' => $user['location'],
            'email_verified_at' => $user['email_verified_at']
        ];
        
        $completed = 0;
        $total = count($fields);
        
        foreach ($fields as $field) {
            if (!empty($field)) {
                $completed++;
            }
        }
        
        return round(($completed / $total) * 100);
    }
    
    public function hasVerifiedEmail($userId) {
        $user = $this->find($userId);
        return $user && !empty($user['email_verified_at']);
    }
    
    public function markEmailAsVerified($userId) {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET email_verified_at = NOW(), updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    public function updateLastLogin($userId) {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET last_login = NOW(), login_count = login_count + 1, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    public function getStatistics($userId) {
        $user = $this->find($userId);
        if (!$user) return null;
        
        // Get calculation count
        $calcStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as calculation_count FROM calculation_history WHERE user_id = ?");
        $calcStmt->execute([$userId]);
        $calcResult = $calcStmt->fetch();
        
        // Get favorites count
        $favStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as favorites_count FROM user_favorites WHERE user_id = ?");
        $favStmt->execute([$userId]);
        $favResult = $favStmt->fetch();
        
        return [
            'calculations_count' => $calcResult['calculation_count'] ?? 0,
            'favorites_count' => $favResult['favorites_count'] ?? 0,
            'login_count' => $user['login_count'] ?? 0,
            'last_login' => $user['last_login'],
            'profile_completion' => $this->getProfileCompletion($userId)
        ];
    }
    
    public function can($userId, $permission) {
        $user = $this->find($userId);
        if (!$user) return false;
        
        $role = $user['role'] ?? 'user';
        
        $permissions = [
            'user' => ['view_own_profile', 'edit_own_profile', 'create_calculations'],
            'admin' => ['user', 'view_all_profiles', 'manage_users', 'system_settings'],
            'super_admin' => ['admin', 'delete_users', 'export_data']
        ];
        
        return in_array($permission, $permissions[$role] ?? []);
    }
    
    // Admin checking methods
    public function isAdmin($userId = null) {
        $checkId = $userId ?? $this->id;
        $user = $this->find($checkId);
        if (!$user) return false;
        
        $role = $user['role'] ?? 'user';
        return in_array($role, ['admin', 'super_admin']);
    }
    
    public function isEngineer($userId = null) {
        $checkId = $userId ?? $this->id;
        $user = $this->find($checkId);
        if (!$user) return false;
        
        $role = $user['role'] ?? 'user';
        return in_array($role, ['engineer', 'admin', 'super_admin']);
    }
    
    public function getFullName($userId = null) {
        $checkId = $userId ?? $this->id;
        $user = $this->find($checkId);
        if (!$user) return '';
        return trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
    }
    
    public function updateProfile($userId, $data) {
        $allowedFields = [
            'avatar', 'professional_title', 'company', 'phone', 'timezone', 
            'measurement_system', 'bio', 'website', 'location', 'social_links'
        ];
        
        $updateFields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updateFields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($updateFields)) return false;
        
        $values[] = $userId;
        
        $sql = "UPDATE users SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->getPdo()->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    public function updateNotificationPreferences($userId, $preferences) {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET notification_preferences = ?, email_notifications = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([json_encode($preferences['notification_preferences']), $preferences['email_notifications'], $userId]);
    }
    
    public function updatePrivacySettings($userId, $settings) {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET calculation_privacy = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$settings['calculation_privacy'], $userId]);
    }
    
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }
    
    public function changePasswordSecure($currentPassword, $newPassword) {
        $user = $this->find($this->id);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        $result = $this->changePassword($this->id, $newPassword);
        
        return [
            'success' => $result,
            'message' => $result ? 'Password updated successfully' : 'Failed to update password'
        ];
    }
    
    public function deleteAccount($userId) {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    // Attribute Accessors
    public function getNotificationPreferencesAttribute($userId) {
        $user = $this->find($userId);
        return $user ? json_decode($user['notification_preferences'] ?? '{}', true) : [];
    }
    
    public function getSocialLinksAttribute($userId) {
        $user = $this->find($userId);
        return $user ? json_decode($user['social_links'] ?? '{}', true) : [];
    }
    
    public function setSocialLinksAttribute($userId, $links) {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET social_links = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([json_encode($links), $userId]);
    }
    
    // Static methods with pagination
    public static function getAllUsers($filters = [], $page = 1, $perPage = 20) {
        $db = Database::getInstance();
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if (!empty($filters['role'])) {
            $whereClause .= " AND role = ?";
            $params[] = $filters['role'];
        }
        
        if (!empty($filters['search'])) {
            $whereClause .= " AND (username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (isset($filters['is_active'])) {
            $whereClause .= " AND is_active = ?";
            $params[] = $filters['is_active'];
        }
        
        // Count total
        $countStmt = $db->getPdo()->prepare("SELECT COUNT(*) as total FROM users $whereClause");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get users with pagination
        $offset = ($page - 1) * $perPage;
        $stmt = $db->getPdo()->prepare("
            SELECT * FROM users 
            $whereClause 
            ORDER BY created_at DESC 
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        $users = $stmt->fetchAll();
        
        return [
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
}
?>
