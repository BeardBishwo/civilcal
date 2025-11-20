<?php

namespace App\Core;

class Auth
{
    public static function check()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['user_id'])) {
            // Get role from session, or from user array if available
            $role = $_SESSION['role'] ?? ($_SESSION['user']['role'] ?? 'user');
            $isAdmin = $_SESSION['is_admin'] ?? ($_SESSION['user']['is_admin'] ?? false);

            return (object) [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? '',
                'role' => $role,
                'is_admin' => $isAdmin
            ];
        }

        return false;
    }

    /**
     * Get the currently authenticated user
     * Alias for check() method
     */
    public static function user()
    {
        return self::check();
    }

    public static function isAdmin()
    {
        $user = self::check();
        if (!$user) return false;

        // Check both is_admin flag and role
        return ($user->is_admin == 1) || ($user->role === 'admin') || ($user->role === 'super_admin');
    }

    /**
     * Logout the current user
     */
    public static function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Clear all session variables
        $_SESSION = [];

        // Delete the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        // Destroy the session
        session_destroy();

        return true;
    }

    /**
     * Login a user
     */
    public static function login($identity, $password)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $logFile = __DIR__ . '/../../storage/logs/auth_debug.log';
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Auth::login called for identity: " . $identity . "\n", FILE_APPEND);

        // Use User model to find user by email or username
        $userModel = new \App\Models\User();
        $user = null;

        // Try to find user by email first
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $user = $userModel->findByEmail($identity);
        } else {
            // Try to find by username
            $user = $userModel->findByUsername($identity);
        }

        if (!$user) {
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Auth::login failed: User not found\n", FILE_APPEND);
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Auth::login: User found (ID: " . $user->id . ")\n", FILE_APPEND);

        // Verify password
        if (!password_verify($password, $user->password)) {
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Auth::login failed: Password mismatch\n", FILE_APPEND);
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Auth::login: Password verified. Setting session.\n", FILE_APPEND);

        // Set session variables
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username ?? $user->email;
        $_SESSION['email'] = $user->email;
        $_SESSION['role'] = $user->role ?? 'user';
        $_SESSION['is_admin'] = ($user->role === 'admin');
        $_SESSION['first_name'] = $user->first_name ?? '';
        $_SESSION['last_name'] = $user->last_name ?? '';

        return [
            'success' => true,
            'user' => $user
        ];
    }
}
