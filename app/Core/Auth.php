<?php

namespace App\Core;

use App\Models\User;

class Auth {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new \App\Models\User();
    }
    
    public function attempt($email, $password) {
        $user = $this->userModel->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            \App\Core\Session::set('user_id', $user['id']);
            \App\Core\Session::set('user_email', $user['email']);
            \App\Core\Session::set('user_role', $user['role']);
            \App\Core\Session::set('user_name', $user['first_name'] . ' ' . $user['last_name']);
            
            return true;
        }
        
        return false;
    }
    
    public function check() {
        return \App\Core\Session::get('user_id') !== null;
    }
    
    public function user() {
        if (!$this->check()) {
            return null;
        }
        
        return $this->userModel->find(\App\Core\Session::get('user_id'));
    }
    
    public function logout() {
        \App\Core\Session::destroy();
    }
    
    public function isAdmin() {
        return \App\Core\Session::get('user_role') === 'admin';
    }
    
    public function register($userData) {
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        return $this->userModel->create($userData);
    }
}
