<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    public static function login($username, $password)
    {
        $user = User::findByUsername($username);
        
        if ($user && password_verify($password, $user->password)) {
            if (!$user->is_active) {
                return ['success' => false, 'message' => 'Account is deactivated'];
            }
            
            // Start session if needed
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // Create session
            $sessionToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $db = Database::getInstance();
            $stmt = $db->prepare("
                INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user->id, 
                $sessionToken, 
                self::getClientIp(), 
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $expiresAt
            ]);
            
            // Record login history
            $stmt = $db->prepare("
                INSERT INTO login_history (user_id, ip_address, user_agent, success) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $user->id, 
                self::getClientIp(), 
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                true
            ]);
            
            // Update user last login
            $stmt = $db->prepare("
                UPDATE users SET last_login = NOW(), login_count = login_count + 1 
                WHERE id = ?
            ");
            $stmt->execute([$user->id]);
            
            // Set session variables for backward compatibility
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['user'] = (array) $user;
            $_SESSION['is_admin'] = ($user->role === 'admin');
            
            // Set cookie when running in web server context and headers not yet sent.
            // For CLI/testing, populate $_COOKIE for subsequent checks instead.
            $cookieOptions = [
                'expires' => time() + (30 * 24 * 60 * 60),
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ];

            if (php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg') {
                $_COOKIE['auth_token'] = $sessionToken;
            } elseif (!headers_sent()) {
                setcookie('auth_token', $sessionToken, $cookieOptions);
            }
            
            return ['success' => true, 'user' => $user];
        }
        
        // Record failed login attempt
        if ($user) {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                INSERT INTO login_history (user_id, ip_address, user_agent, success, failure_reason) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user->id, 
                self::getClientIp(), 
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                false,
                'Invalid password'
            ]);
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    public static function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Clear session variables
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['user']);
        unset($_SESSION['is_admin']);
        
        if (isset($_COOKIE['auth_token'])) {
            $token = $_COOKIE['auth_token'];
            
            // Delete session from database
            $db = Database::getInstance();
            $stmt = $db->prepare("DELETE FROM user_sessions WHERE session_token = ?");
            $stmt->execute([$token]);
            
            // Clear cookie when possible; clear $_COOKIE for CLI/testing.
            $cookieOptions = [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ];

            if (php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg') {
                unset($_COOKIE['auth_token']);
            } elseif (!headers_sent()) {
                setcookie('auth_token', '', $cookieOptions);
            }
        }
        
        // Destroy session
        session_destroy();
    }
    
    public static function check()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check session-based authentication first
        if (!empty($_SESSION['user_id'])) {
            $user = User::findByUsername($_SESSION['username'] ?? '');
            if ($user) {
                // Update last activity
                $db = Database::getInstance();
                $stmt = $db->prepare("UPDATE user_sessions SET last_activity = NOW() WHERE user_id = ?");
                $stmt->execute([$user->id]);
                
                return (object) $user;
            }
        }
        
        // Fallback: Check cookie-based authentication
        if (isset($_COOKIE['auth_token'])) {
            $token = $_COOKIE['auth_token'];
            
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT u.* 
                FROM user_sessions us 
                JOIN users u ON us.user_id = u.id 
                WHERE us.session_token = ? AND us.expires_at > NOW() AND u.is_active = 1
            ");
            $stmt->execute([$token]);
            $session = $stmt->fetch();
            
            if ($session) {
                // Update last activity
                $stmt = $db->prepare("UPDATE user_sessions SET last_activity = NOW() WHERE session_token = ?");
                $stmt->execute([$token]);
                
                // Sync session variables
                $_SESSION['user_id'] = $session['id'];
                $_SESSION['username'] = $session['username'];
                $_SESSION['user'] = $session;
                $_SESSION['is_admin'] = ($session['role'] === 'admin');
                
                return (object) $session;
            }
        }
        
        return false;
    }
    
    public static function user()
    {
        return self::check();
    }
    
    public static function isAdmin()
    {
        $user = self::check();
        if (!$user) return false;
        
        // Check multiple possible admin indicators
        $isAdmin = 
            (isset($user->role) && $user->role === 'admin') ||
            (isset($user->is_admin) && $user->is_admin) ||
            (isset($_SESSION['is_admin']) && $_SESSION['is_admin']);
            
        return $isAdmin;
    }
    
    private static function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        }
    }
    
    public static function register($userData)
    {
        $db = Database::getInstance();
        
        // Check if username or email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$userData['username'], $userData['email']]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        
        // Hash password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $db->prepare("
            INSERT INTO users (username, email, password, first_name, last_name, role, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $userData['username'],
            $userData['email'],
            $hashedPassword,
            $userData['first_name'] ?? '',
            $userData['last_name'] ?? '',
            $userData['role'] ?? 'user'
        ]);
        
        if ($result) {
            $userId = $db->lastInsertId();
            return ['success' => true, 'user_id' => $userId];
        }
        
        return ['success' => false, 'message' => 'Registration failed'];
    }
}
?>
