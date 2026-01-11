<?php

namespace App\Models;

use App\Core\Database;
use Exception;

class User
{
    private $db;
    public $id;
    public $coins;

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

    public static function findById($id)
    {
        $db = Database::getInstance();
        $pdo = $db->getPdo();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
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
                email_verified, is_active, terms_agreed, terms_agreed_at, marketing_emails,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $emailVerified = !empty($data['email_verified']) ? 1 : 0;
        $termsAgreed = !empty($data['terms_agreed']) ? 1 : 0;
        $marketingEmails = !empty($data['marketing_emails']) ? 1 : 0;

        $executed = $stmt->execute([
            $data['username'] ?? null,
            $data['email'],
            $data['password'],
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['company'] ?? '',
            $data['phone'] ?? '',
            $data['role'] ?? 'user',
            $emailVerified,
            $data['is_active'] ?? 1,
            $termsAgreed,
            $termsAgreed ? date('Y-m-d H:i:s') : null,
            $marketingEmails
        ]);

        if (!$executed) {
            $errorInfo = $stmt->errorInfo();
            $message = $errorInfo[2] ?? 'Unknown database error';
            throw new \RuntimeException('Database insert failed: ' . $message);
        }

        $userId = $this->db->getPdo()->lastInsertId();

        // Optionally send welcome email if requested
        if (!empty($data['send_welcome_email'])) {
            try {
                $emailManager = new \App\Services\EmailManager();
                $emailManager->sendWelcomeEmail(
                    $data['email'],
                    trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                    $data['username'] ?? ''
                );
            } catch (\Exception $e) {
                error_log('Failed to send welcome email: ' . $e->getMessage());
            }
        }

        return $userId;
    }

    /**
     * Ensure users table has agreement and marketing preference columns
     */
    private function ensureAgreementColumns()
    {
        try {
            $pdo = $this->db->getPdo();

            $columns = [];
            $columnsStmt = $pdo->query('SHOW COLUMNS FROM users');
            while ($row = $columnsStmt->fetch(\PDO::FETCH_ASSOC)) {
                $columns[$row['Field']] = $row;
            }

            $requiredColumns = [
                'username' => "ALTER TABLE users ADD COLUMN username VARCHAR(150) NULL AFTER id",
                'phone' => "ALTER TABLE users ADD COLUMN phone VARCHAR(100) NULL AFTER company",
                'is_active' => "ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER email_verified_at",
                'email_verified' => "ALTER TABLE users ADD COLUMN email_verified TINYINT(1) DEFAULT 0 AFTER is_active",
                'terms_agreed' => "ALTER TABLE users ADD COLUMN terms_agreed TINYINT(1) DEFAULT 0 AFTER email_verified",
                'terms_agreed_at' => "ALTER TABLE users ADD COLUMN terms_agreed_at DATETIME NULL AFTER terms_agreed",
                'marketing_emails' => "ALTER TABLE users ADD COLUMN marketing_emails TINYINT(1) DEFAULT 0 AFTER terms_agreed_at",
                'privacy_agreed' => "ALTER TABLE users ADD COLUMN privacy_agreed TINYINT(1) DEFAULT 0 AFTER marketing_emails",
                'privacy_agreed_at' => "ALTER TABLE users ADD COLUMN privacy_agreed_at DATETIME NULL AFTER privacy_agreed",
                'force_password_change' => "ALTER TABLE users ADD COLUMN force_password_change TINYINT(1) DEFAULT 0 AFTER privacy_agreed_at",
                'password_generated_at' => "ALTER TABLE users ADD COLUMN password_generated_at DATETIME NULL AFTER force_password_change",
                'failed_logins' => "ALTER TABLE users ADD COLUMN failed_logins TINYINT(3) DEFAULT 0 AFTER password_generated_at",
                'lockout_until' => "ALTER TABLE users ADD COLUMN lockout_until DATETIME NULL AFTER failed_logins",
                'is_banned' => "ALTER TABLE users ADD COLUMN is_banned TINYINT(1) DEFAULT 0 AFTER lockout_until",
                'ban_reason' => "ALTER TABLE users ADD COLUMN ban_reason TEXT NULL AFTER is_banned",
                'ban_reason' => "ALTER TABLE users ADD COLUMN ban_reason TEXT NULL AFTER is_banned",
                'banned_at' => "ALTER TABLE users ADD COLUMN banned_at DATETIME NULL AFTER ban_reason",
                'remember_token' => "ALTER TABLE users ADD COLUMN remember_token VARCHAR(100) NULL AFTER banned_at"
            ];

            foreach ($requiredColumns as $columnName => $alterSql) {
                if (!isset($columns[$columnName])) {
                    $pdo->exec($alterSql);
                    error_log("Added column '{$columnName}' to users table");
                }
            }

            // Ensure username is unique if column exists
            if (isset($columns['username'])) {
                $indexStmt = $pdo->query("SHOW INDEX FROM users WHERE Key_name = 'idx_users_username'");
                if ($indexStmt->rowCount() === 0) {
                    $pdo->exec('CREATE UNIQUE INDEX idx_users_username ON users(username)');
                }
            }

            // Ensure role column supports all roles used in the app
            if (isset($columns['role'])) {
                $type = strtolower($columns['role']['Type'] ?? '');
                if (strpos($type, "'engineer'") === false) {
                    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('user','admin','engineer') DEFAULT 'user'");
                    error_log("Updated users.role enum to include 'engineer'");
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
        // Update basic login info and reset failed attempts
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET last_login = NOW(), login_count = login_count + 1, failed_logins = 0, lockout_until = NULL, updated_at = NOW() WHERE id = ?");
        $result = $stmt->execute([$userId]);

        // Log device and location info for security and analytics
        $this->logLoginSession($userId, $deviceInfo, $locationInfo);

        return $result;
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedLogins($userId, $maxAttempts, $lockoutSeconds)
    {
        $pdo = $this->db->getPdo();
        
        // Get current attempts
        $stmt = $pdo->prepare("SELECT failed_logins FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $currentRow = $stmt->fetch();
        $attempts = ($currentRow['failed_logins'] ?? 0) + 1;
        
        $lockoutUntil = null;
        if ($attempts >= $maxAttempts) {
            $lockoutUntil = date('Y-m-d H:i:s', time() + $lockoutSeconds);
        }
        
        $stmt = $pdo->prepare("UPDATE users SET failed_logins = ?, lockout_until = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$attempts, $lockoutUntil, $userId]);
    }

    /**
     * Log login session with device and location info for ads
     */
    public function logLoginSession($userId, $deviceInfo = null, $locationInfo = null)
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
        $calcStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as calculation_count FROM activity_audit_logs WHERE user_id = ? AND activity_type = 'TOOL_USED'");
        $calcStmt->execute([$userId]);
        $calcResult = $calcStmt->fetch();

        // Get favorites count
        $favStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as favorites_count FROM user_favorites WHERE user_id = ?");
        $favStmt->execute([$userId]);
        $favResult = $favStmt->fetch();

        // Get News Reads
        $newsStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as news_count FROM activity_audit_logs WHERE user_id = ? AND activity_type = 'NEWS_READ'");
        $newsStmt->execute([$userId]);
        $newsResult = $newsStmt->fetch();

        // Get Quizzes Completed (Legacy)
        $quizStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as quiz_count FROM quiz_attempts WHERE user_id = ? AND status = 'completed'");
        $quizStmt->execute([$userId]);
        $quizResult = $quizStmt->fetch();

        // Get Exams Completed (New Phase 17)
        $examStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as exam_count FROM exam_sessions WHERE user_id = ? AND status = 'completed'");
        $examStmt->execute([$userId]);
        $examResult = $examStmt->fetch();

        $totalExams = ($quizResult['quiz_count'] ?? 0) + ($examResult['exam_count'] ?? 0);

        return [
            'calculations_count' => $calcResult['calculation_count'] ?? 0,
            'favorites_count' => $favResult['favorites_count'] ?? 0,
            'news_reads_count' => $newsResult['news_count'] ?? 0,
            'quizzes_completed_count' => $totalExams,
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

    public function adminUpdate($userId, $data)
    {
        // Fields that can be updated by admin
        $allowedFields = [
            'first_name', 'last_name', 'username', 'email', 
            'role', 'is_active', 'email_verified', 'marketing_emails', 'force_password_change'
        ];

        $updateFields = [];
        $values = [];

        // Handle standard fields
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updateFields[] = "$key = ?";
                $values[] = $value;
            }
        }

        // Handle password if provided
        if (!empty($data['password'])) {
            $updateFields[] = "password = ?";
            $values[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (empty($updateFields)) return false;

        $values[] = $userId;

        $sql = "UPDATE users SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->getPdo()->prepare($sql);

        return $stmt->execute($values);
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

        $passwordValidation = \App\Services\Security::validatePassword($newPassword);
        if (!$passwordValidation['valid']) {
            return ['success' => false, 'message' => $passwordValidation['error']];
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

    // Career & Rank System
    public function setStudyMode($userId, $mode)
    {
        if (!in_array($mode, ['psc', 'world'])) return false;
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET study_mode = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$mode, $userId]);
    }

    public function addXp($userId, $amount)
    {
        if ($amount <= 0) return false;
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET xp = xp + ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$amount, $userId]);
    }

    public function getCareerStats($userId)
    {
        $user = $this->find($userId);
        if (!$user) return null;
        return [
            'rank_title' => $user['rank_title'] ?? 'Intern',
            'study_mode' => $user['study_mode'] ?? 'psc',
            'xp' => $user['xp'] ?? 0
        ];
    }
    
    public function updateRank($userId, $newRank)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET rank_title = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$newRank, $userId]);
    }

    // Economy / Coins Methods

    public function getCoins($userId = null)
    {
        $checkId = $userId ?? $this->id;
        $user = $this->find($checkId);
        return $user ? ($user['coins'] ?? 0) : 0;
    }

    public function addCoins($userId, $amount, $reason, $referenceId = null)
    {
        if ($amount <= 0) return false;

        $pdo = $this->db->getPdo();
        try {
            $pdo->beginTransaction();

            // Update user balance
            $stmt = $pdo->prepare("UPDATE users SET coins = coins + ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$amount, $userId]);

            // Log transaction
            $txnStmt = $pdo->prepare("INSERT INTO user_transactions (user_id, amount, type, reference_id, description) VALUES (?, ?, 'upload_reward', ?, ?)");
            $txnStmt->execute([$userId, $amount, $referenceId, $reason]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Failed to add coins: " . $e->getMessage());
            return false;
        }
    }

    public function deductCoins($userId, $amount, $reason, $referenceId = null)
    {
        if ($amount <= 0) return false;

        $pdo = $this->db->getPdo();
        
        // Check balance first
        $currentBalance = $this->getCoins($userId);
        if ($currentBalance < $amount) {
            return false; // Insufficient funds
        }

        try {
            $pdo->beginTransaction();

            // Update user balance
            $stmt = $pdo->prepare("UPDATE users SET coins = coins - ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$amount, $userId]);

            // Log transaction
            $txnStmt = $pdo->prepare("INSERT INTO user_transactions (user_id, amount, type, reference_id, description) VALUES (?, ?, 'download_cost', ?, ?)");
            $txnStmt->execute([$userId, -1 * $amount, $referenceId, $reason]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Failed to deduct coins: " . $e->getMessage());
            return false;
        }
    }

    public function setReferral($userId, $referredByCode)
    {
        if (!$referredByCode) return;
        
        $pdo = $this->db->getPdo();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE referral_code = ?");
        $stmt->execute([$referredByCode]);
        $referrerId = $stmt->fetchColumn();
        
        if ($referrerId && $referrerId != $userId) {
            $upd = $pdo->prepare("UPDATE users SET referred_by = ? WHERE id = ?");
            $upd->execute([$referrerId, $userId]);
        }
    }

    public function incrementQuizCount($userId)
    {
        $pdo = $this->db->getPdo();
        
        // Ensure referral code exists for self if missing (simple fix for legacy users)
        $checkRef = $pdo->prepare("SELECT referral_code FROM users WHERE id = ?");
        $checkRef->execute([$userId]);
        if (!$checkRef->fetchColumn()) {
            $pdo->prepare("UPDATE users SET referral_code = ? WHERE id = ?")
                ->execute([uniqid(substr(md5($userId . time()), 0, 5)), $userId]);
        }

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE users SET quiz_solved_count = quiz_solved_count + 1 WHERE id = ?");
            $stmt->execute([$userId]);
            
            // Check if hit 5
            $check = $pdo->prepare("SELECT quiz_solved_count, referred_by FROM users WHERE id = ?");
            $check->execute([$userId]);
            $user = $check->fetch(\PDO::FETCH_ASSOC);
            
            if ($user && $user['quiz_solved_count'] == 5 && $user['referred_by']) {
                // Reward Referrer
                $this->addCoins($user['referred_by'], 50, "Referral Bonus", $userId);
                // Reward User
                $this->addCoins($userId, 20, "Referral Welcome Bonus", $user['referred_by']);
                
                // Notify Referrer (Quick inline notification logic)
                $notifSql = "INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())";
                $pdo->prepare($notifSql)->execute([$user['referred_by'], "You earned 50 Coins from a referral!"]);
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
        }
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

    // 2FA Methods
    public function enableTwoFactor($userId, $secret, $recoveryCodes)
    {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE users 
            SET two_factor_secret = ?, 
                two_factor_recovery_codes = ?, 
                two_factor_enabled = 0, -- Not enabled until confirmed
                updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$secret, json_encode($recoveryCodes), $userId]);
    }

    public function confirmTwoFactor($userId)
    {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE users 
            SET two_factor_enabled = 1, 
                two_factor_confirmed_at = NOW(), 
                updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$userId]);
    }

    public function disableTwoFactor($userId)
    {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE users 
            SET two_factor_enabled = 0, 
                two_factor_secret = NULL, 
                two_factor_recovery_codes = NULL, 
                two_factor_confirmed_at = NULL, 
                updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$userId]);
    }

    public function getTwoFactorData($userId)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT two_factor_secret, two_factor_enabled, two_factor_recovery_codes FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
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

    /**
     * Get user statistics for analytics
     */
    public function getUserStats()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT role, COUNT(*) as count
            FROM users
            GROUP BY role
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get new user count for a specific period
     */
    public function getNewUserCount($days = 30)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as count
            FROM users
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$days]);
        $result = $stmt->fetch();
        return $result ? $result['count'] : 0;
    }

    /**
     * Get user growth data
     */
    public function getUserGrowthData($days = 90)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM users
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    /**
     * Get total user count
     */
    public function getTotalUserCount()
    {
        $stmt = $this->db->getPdo()->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        return $result ? $result['count'] : 0;
    }

    /**
     * Get database connection
     */
    public function getDb()
    {
        return $this->db;
    }
}
