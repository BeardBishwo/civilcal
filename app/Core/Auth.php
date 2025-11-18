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
}
?>
