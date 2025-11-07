<?php
/**
 * Auth Controller
 * Handles authentication pages
 */

namespace App\Controllers;

class AuthController
{
    public function __construct()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Login page
     */
    public function login()
    {
        // Include the login page
        require 'themes/default/views/pages/auth/login.php';
    }

    /**
     * Register page
     */
    public function register()
    {
        // Include the register page
        require 'themes/default/views/pages/auth/register.php';
    }

    /**
     * Forgot password page
     */
    public function forgotPassword()
    {
        // Include the forgot password page
        require 'themes/default/views/pages/auth/forgot-password.php';
    }

    /**
     * Index - redirect to login
     */
    public function index()
    {
        header('Location: /login');
        exit;
    }
}
