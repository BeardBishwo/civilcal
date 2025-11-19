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
            return (object) [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? '',
                'role' => $_SESSION['role'] ?? 'user',
                'is_admin' => $_SESSION['is_admin'] ?? false
            ];
        }
        
        return false;
    }
    
    public static function isAdmin()
    {
        $user = self::check();
        if (!$user) return false;
        
        return ($user->role === 'admin');
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
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }
        
        // Verify password
        if (!password_verify($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }
        
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
?>
