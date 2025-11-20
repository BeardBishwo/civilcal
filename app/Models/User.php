<?php

namespace App\Models;

use App\Core\Database;
use Exception;

class User
{
    private $db;
    public $id;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? (object) $row : null;
    }

    public static function findByUsername($username)
    {
        $db = Database::getInstance();
        $pdo = $db->getPdo();
        // Support login by username or email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $row = $stmt->fetch();
        return $row ? (object) $row : null;
    }

    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        // First, ensure the users table has the required columns
        $this->ensureAgreementColumns();

        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO users (
                username, email, password, first_name, last_name, company, phone, role, 
                email_verified, is_active, terms_agreed, terms_agreed_at, marketing_emails
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $termsAgreed = isset($data['terms_agree']) && $data['terms_agree'] ? 1 : 0;
        $marketingEmails = isset($data['marketing_agree']) && $data['marketing_agree'] ? 1 : 0;

        $stmt->execute([
            isset($data['username']) ? $data['username'] : null,
            $data['email'],
            $data['password'],
            isset($data['first_name']) ? $data['first_name'] : '',
            isset($data['last_name']) ? $data['last_name'] : '',
            isset($data['company']) ? $data['company'] : '',
            isset($data['phone']) ? $data['phone'] : '',
            isset($data['role']) ? $data['role'] : 'user',
            isset($data['email_verified']) ? $data['email_verified'] : 0,
            isset($data['is_active']) ? $data['is_active'] : 1,
            $termsAgreed,
            $termsAgreed ? date('Y-m-d H:i:s') : null,
            $marketingEmails
        ]);

        return $this->db->getPdo()->lastInsertId();
    }

    /**
     * Ensure users table has agreement and marketing preference columns
     */
    private function ensureAgreementColumns()
    {
        try {
            $pdo = $this->db->getPdo();

            // Check if columns exist and add them if they don't
            $columns = [
                'terms_agreed' => 'ALTER TABLE users ADD COLUMN terms_agreed TINYINT(1) DEFAULT 0',
                'terms_agreed_at' => 'ALTER TABLE users ADD COLUMN terms_agreed_at DATETIME NULL',
                'marketing_emails' => 'ALTER TABLE users ADD COLUMN marketing_emails TINYINT(1) DEFAULT 0',
                'privacy_agreed' => 'ALTER TABLE users ADD COLUMN privacy_agreed TINYINT(1) DEFAULT 0',
                'privacy_agreed_at' => 'ALTER TABLE users ADD COLUMN privacy_agreed_at DATETIME NULL'
            ];

            foreach ($columns as $columnName => $alterSql) {
                // Check if column exists
                $checkSql = "SHOW COLUMNS FROM users LIKE '{$columnName}'";
                $result = $pdo->query($checkSql);

                if ($result->rowCount() == 0) {
                    // Column doesn't exist, add it
                    $pdo->exec($alterSql);
                    error_log("Added column '{$columnName}' to users table");
                }
            }
        } catch (Exception $e) {
            error_log("Error ensuring agreement columns: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        $stmt = $this->db->getPdo()->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function updateSubscription($userId, $subscriptionId, $status = 'active')
    {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE users 
            SET subscription_id = ?, subscription_status = ?, subscription_ends_at = DATE_ADD(NOW(), INTERVAL 1 MONTH)
            WHERE id = ?
        ");

        return $stmt->execute([$subscriptionId, $status, $userId]);
    }

    // Profile Management Methods
    public function getProfileCompletion($userId)
    {
        $user = $this->find($userId);
        if (!$user || !is_array($user)) {
            return 0;
        }

        // Use null-coalescing for optional fields so missing columns don't trigger warnings
        $fields = [
            'avatar'            => $user['avatar'] ?? null,
            'professional_title' => $user['professional_title'] ?? null,
            'company'           => $user['company'] ?? null,
            'phone'             => $user['phone'] ?? null,
            'bio'               => $user['bio'] ?? null,
            'website'           => $user['website'] ?? null,
            'location'          => $user['location'] ?? null,
            'email_verified_at' => $user['email_verified_at'] ?? null,
        ];

        $completed = 0;
        $total = count($fields);

        foreach ($fields as $value) {
            if (!empty($value)) {
                $completed++;
            }
        }

        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    public function hasVerifiedEmail($userId)
    {
        $user = $this->find($userId);
        return $user && !empty($user['email_verified_at'] ?? null);
    }

    public function markEmailAsVerified($userId)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET email_verified_at = NOW(), updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function updateLastLogin($userId, $deviceInfo = null, $locationInfo = null)
    {
        // Update basic login info
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET last_login = NOW(), login_count = login_count + 1, updated_at = NOW() WHERE id = ?");
        $result = $stmt->execute([$userId]);

        // Log device and location info for ads targeting (if provided)
        if ($deviceInfo || $locationInfo) {
            $this->logLoginSession($userId, $deviceInfo, $locationInfo);
        }

        return $result;
    }

    /**
     * Log login session with device and location info for ads
     */
    private function logLoginSession($userId, $deviceInfo = null, $locationInfo = null)
    {
        try {
            // Create login_sessions table if it doesn't exist
            $createTable = "
                CREATE TABLE IF NOT EXISTS login_sessions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    device_type VARCHAR(50),
                    browser VARCHAR(100),
                    os VARCHAR(100),
                    country VARCHAR(100),
                    region VARCHAR(100), 
                    city VARCHAR(100),
                    timezone VARCHAR(100),
                    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_user_id (user_id),
                    INDEX idx_login_time (login_time),
                    INDEX idx_country (country)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";

            $this->db->getPdo()->exec($createTable);

            // Insert session data
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO login_sessions (
                    user_id, ip_address, user_agent, device_type, browser, os, 
                    country, region, city, timezone
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $userId,
                $deviceInfo['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
                $deviceInfo['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null,
                $deviceInfo['device_type'] ?? $this->detectDeviceType(),
                $deviceInfo['browser'] ?? $this->detectBrowser(),
                $deviceInfo['os'] ?? $this->detectOS(),
                $locationInfo['country'] ?? null,
                $locationInfo['region'] ?? null,
                $locationInfo['city'] ?? null,
                $locationInfo['timezone'] ?? null
            ]);
        } catch (Exception $e) {
            error_log('Login session logging error: ' . $e->getMessage());
        }
    }

    /**
     * Detect device type from user agent
     */
    private function detectDeviceType()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Detect browser from user agent
     */
    private function detectBrowser()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        if (strpos($userAgent, 'Opera') !== false) return 'Opera';

        return 'Unknown';
    }

    /**
     * Detect OS from user agent
     */
    private function detectOS()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac OS') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iOS') !== false) return 'iOS';

        return 'Unknown';
    }

    public function getStatistics($userId)
    {
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

    public function can($userId, $permission)
    {
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
    public function isAdmin($userId = null)
    {
        $checkId = $userId ?? $this->id;
        $user = $this->find($checkId);
        if (!$user) return false;

        $role = $user['role'] ?? 'user';
        return in_array($role, ['admin', 'super_admin']);
    }

    public function isEngineer($userId = null)
    {
        $checkId = $userId ?? $this->id;
        $user = $this->find($checkId);
        if (!$user) return false;

        $role = $user['role'] ?? 'user';
        return in_array($role, ['engineer', 'admin', 'super_admin']);
    }

    public function getFullName($userId = null)
    {
        $checkId = $userId ?? $this->id;
        $user = $this->find($checkId);
        if (!$user) return '';
        return trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
    }

    public function updateProfile($userId, $data)
    {
        $allowedFields = [
            'avatar',
            'professional_title',
            'company',
            'phone',
            'timezone',
            'measurement_system',
            'bio',
            'website',
            'location',
            'social_links'
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

    public function updateNotificationPreferences($userId, $preferences)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET notification_preferences = ?, email_notifications = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([json_encode($preferences['notification_preferences']), $preferences['email_notifications'], $userId]);
    }

    public function updatePrivacySettings($userId, $settings)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET calculation_privacy = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$settings['calculation_privacy'], $userId]);
    }

    /**
     * Update marketing email preferences
     */
    public function updateMarketingPreferences($userId, $allowMarketing)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET marketing_emails = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$allowMarketing ? 1 : 0, $userId]);
    }

    /**
     * Get users who opted in for marketing emails
     */
    public function getMarketingOptInUsers($limit = null)
    {
        $sql = "SELECT id, email, first_name, last_name FROM users WHERE marketing_emails = 1 AND is_active = 1";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }

        $stmt = $this->db->getPdo()->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Check if user has agreed to terms
     */
    public function hasAgreedToTerms($userId)
    {
        $user = $this->find($userId);
        return $user && !empty($user['terms_agreed']);
    }

    /**
     * Get user agreement status
     */
    public function getAgreementStatus($userId)
    {
        $user = $this->find($userId);
        if (!$user) return null;

        return [
            'terms_agreed' => !empty($user['terms_agreed']),
            'terms_agreed_at' => $user['terms_agreed_at'] ?? null,
            'privacy_agreed' => !empty($user['privacy_agreed']),
            'privacy_agreed_at' => $user['privacy_agreed_at'] ?? null,
            'marketing_emails' => !empty($user['marketing_emails'])
        ];
    }

    public function changePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }

    public function changePasswordSecure($currentPassword, $newPassword)
    {
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

    public function deleteAccount($userId)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    // Attribute Accessors
    public function getNotificationPreferencesAttribute($userId)
    {
        $user = $this->find($userId);
        return $user ? json_decode($user['notification_preferences'] ?? '{}', true) : [];
    }

    public function getSocialLinksAttribute($userId)
    {
        $user = $this->find($userId);
        return $user ? json_decode($user['social_links'] ?? '{}', true) : [];
    }

    public function setSocialLinksAttribute($userId, $links)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET social_links = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([json_encode($links), $userId]);
    }

    // Static methods with pagination
    public static function getAllUsers($filters = [], $page = 1, $perPage = 20)
    {
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
