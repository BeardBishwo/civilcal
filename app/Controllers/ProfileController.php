<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Services\ProfileService;
use App\Services\CacheService;

/**
 * Profile Controller (Refactored)
 * 
 * Handles user profile management using ProfileService
 * All business logic extracted to service layer
 */
class ProfileController extends Controller
{
    private $profileService;
    private $cache;

    public function __construct()
    {
        parent::__construct();
        $this->profileService = new ProfileService();
        $this->cache = CacheService::getInstance();
    }

    /**
     * Show user profile page
     */
    public function index()
    {
        $userId = $this->auth->id();
        
        // Try cache first
        $cacheKey = "user_profile_{$userId}";
        $profile = $this->cache->get($cacheKey);
        
        if (!$profile) {
            $profile = $this->profileService->getUserProfile($userId);
            $this->cache->set($cacheKey, $profile, 300); // 5 min cache
        }
        
        $this->view('profile/index', [
            'profile' => $profile,
            'title' => 'My Profile'
        ]);
    }

    /**
     * User Exam History Page
     */
    public function exams()
    {
        $userId = $this->auth->id();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $history = $this->profileService->getActivityHistory($userId, $limit, $offset);
        
        $this->view('profile/exams', [
            'history' => $history,
            'page' => $page,
            'title' => 'Exam History'
        ]);
    }

    /**
     * User Analytics Page
     */
    public function analytics()
    {
        $userId = $this->auth->id();
        
        // Cache statistics for 10 minutes
        $cacheKey = "user_stats_{$userId}";
        $stats = $this->cache->remember($cacheKey, 600, function() use ($userId) {
            return $this->profileService->getStatistics($userId);
        });
        
        $this->view('profile/analytics', [
            'stats' => $stats,
            'title' => 'My Analytics'
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        // Validate CSRF token
        if (!Validator::csrf($_POST['csrf_token'] ?? '')) {
            return $this->json(['error' => 'Invalid CSRF token'], 403);
        }

        // Validate input
        $validation = Validator::validate($_POST, [
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'phone' => 'alphanumeric|min:10|max:20',
            'bio' => 'max:500',
            'website' => 'url'
        ]);

        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 400);
        }

        // Sanitize data
        $data = Validator::sanitizeArray($_POST, [
            'first_name' => 'string',
            'last_name' => 'string',
            'phone' => 'string',
            'bio' => 'html',
            'location' => 'string',
            'website' => 'url'
        ]);

        $userId = $this->auth->id();
        $result = $this->profileService->updateProfile($userId, $data);

        // Invalidate cache
        $this->cache->delete("user_profile_{$userId}");
        $this->cache->delete("user_stats_{$userId}");

        return $this->json($result);
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        if (!Validator::csrf($_POST['csrf_token'] ?? '')) {
            return $this->json(['error' => 'Invalid CSRF token'], 403);
        }

        $userId = $this->auth->id();
        $preferences = [
            'email_notifications' => isset($_POST['email_notifications']),
            'quiz_reminders' => isset($_POST['quiz_reminders']),
            'newsletter' => isset($_POST['newsletter'])
        ];

        $result = $this->profileService->updatePreferences($userId, $preferences);

        return $this->json($result);
    }

    /**
     * Change user password
     */
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        if (!Validator::csrf($_POST['csrf_token'] ?? '')) {
            return $this->json(['error' => 'Invalid CSRF token'], 403);
        }

        // Validate passwords
        $validation = Validator::validate($_POST, [
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required'
        ]);

        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 400);
        }

        // Check password confirmation
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            return $this->json(['error' => 'Passwords do not match'], 400);
        }

        $userId = $this->auth->id();
        $result = $this->profileService->updatePassword(
            $userId,
            $_POST['old_password'],
            $_POST['new_password']
        );

        return $this->json($result);
    }

    /**
     * Delete user account
     */
    public function deleteAccount()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        if (!Validator::csrf($_POST['csrf_token'] ?? '')) {
            return $this->json(['error' => 'Invalid CSRF token'], 403);
        }

        // Validate password confirmation
        $validation = Validator::validate($_POST, [
            'password' => 'required',
            'confirmation' => 'required'
        ]);

        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 400);
        }

        if ($_POST['confirmation'] !== 'DELETE') {
            return $this->json(['error' => 'Please type DELETE to confirm'], 400);
        }

        $userId = $this->auth->id();
        $result = $this->profileService->deleteAccount($userId, $_POST['password']);

        if ($result['success']) {
            // Clear cache and logout
            $this->cache->delete("user_profile_{$userId}");
            $this->cache->delete("user_stats_{$userId}");
            $this->auth->logout();
        }

        return $this->json($result);
    }

    /**
     * Get profile data (API endpoint)
     */
    public function getProfile()
    {
        $userId = $this->auth->id();
        
        // Use cache
        $cacheKey = "user_profile_{$userId}";
        $profile = $this->cache->remember($cacheKey, 300, function() use ($userId) {
            return $this->profileService->getUserProfile($userId);
        });

        return $this->json([
            'success' => true,
            'profile' => $profile
        ]);
    }

    /**
     * Update profile via API
     */
    public function updateProfileApi()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if (!in_array($method, ['POST', 'PUT', 'PATCH'])) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        // Validate CSRF for non-API requests
        if (!isset($input['api_key']) && !Validator::csrf($input['csrf_token'] ?? '')) {
            return $this->json(['error' => 'Invalid CSRF token'], 403);
        }

        // Validate input
        $validation = Validator::validate($input, [
            'first_name' => 'min:2|max:100',
            'last_name' => 'min:2|max:100',
            'email' => 'email',
            'phone' => 'alphanumeric|min:10|max:20'
        ]);

        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 400);
        }

        // Sanitize
        $data = Validator::sanitizeArray($input, [
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'email',
            'phone' => 'string',
            'bio' => 'html'
        ]);

        $userId = $this->auth->id();
        $result = $this->profileService->updateProfile($userId, $data);

        // Invalidate cache
        $this->cache->delete("user_profile_{$userId}");

        return $this->json($result);
    }

    /**
     * Handle avatar upload
     */
    public function uploadAvatar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        if (!Validator::csrf($_POST['csrf_token'] ?? '')) {
            return $this->json(['error' => 'Invalid CSRF token'], 403);
        }

        if (!isset($_FILES['avatar'])) {
            return $this->json(['error' => 'No file uploaded'], 400);
        }

        $file = $_FILES['avatar'];

        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return $this->json(['error' => 'Invalid file type'], 400);
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            return $this->json(['error' => 'File too large (max 5MB)'], 400);
        }

        // Upload file
        $uploadDir = BASE_PATH . '/public/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $userId = $this->auth->id();
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $userId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $avatarPath = '/uploads/avatars/' . $filename;
            $result = $this->profileService->updateAvatar($userId, $avatarPath);

            // Invalidate cache
            $this->cache->delete("user_profile_{$userId}");

            return $this->json($result);
        }

        return $this->json(['error' => 'Upload failed'], 500);
    }
}
