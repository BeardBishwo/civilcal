<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Services\ProfileService;
use App\Services\CacheService;
use App\Services\RankService;
use App\Services\FileService;

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
    private $rankService;

    public function __construct()
    {
        parent::__construct();
        $this->profileService = new ProfileService();
        $this->cache = CacheService::getInstance();
        $this->rankService = new RankService();
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

            if (!$profile) {
                // User not found in DB (ghost session)
                $this->auth->logout();
                $this->redirect('/login');
                return;
            }

            $this->cache->set($cacheKey, $profile, 300); // 5 min cache
        }

        // Get rank data
        $rankData = $this->rankService->getUserRankData($profile['stats'] ?? [], $profile['wallet'] ?? []);

        // Get social links (stored as JSON in users table or managed by model-like logic)
        $userModel = new \App\Models\User();
        $socialLinks = $userModel->getSocialLinksAttribute($userId);

        // Get 2FA status
        $twoFactorData = $userModel->getTwoFactorData($userId);
        $twoFactorStatus = [
            'enabled' => !empty($twoFactorData['two_factor_enabled']),
            'secret' => $twoFactorData['two_factor_secret'] ?? null
        ];

        // Fetch available streams (Courses)
        $streams = $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' AND is_active = 1 ORDER BY order_index ASC")->fetchAll();

        // Debug: Log the profile data to ensure career_interests is present
        // error_log('Profile data: ' . json_encode($profile));

        $this->view('user/profile', [
            'user' => $profile['user'] ?? [],
            'stats' => $profile['stats'] ?? [],
            'wallet' => $profile['wallet'] ?? [],
            'career_interests' => $profile['career_interests'] ?? [], // Pass career_interests to view
            'rank_data' => $rankData,
            'social_links' => $socialLinks,
            'two_factor_status' => $twoFactorStatus,
            'streams' => $streams,
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
        $stats = $this->cache->remember($cacheKey, 600, function () use ($userId) {
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
            'phone' => 'min:10|max:20',
            'bio' => 'max:1000',
            'website' => 'max:255',
            'location' => 'max:255',
            'professional_title' => 'max:255',
            'company' => 'max:255',
            'timezone' => 'max:100',
            'measurement_system' => 'max:20',
            'timezone' => 'max:100',
            'measurement_system' => 'max:20',
            'study_mode' => 'max:20',
            'stream_id' => 'integer',
            'custom_stream' => 'max:255',
            'education_level' => 'max:100'
        ]);

        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 400);
        }

        // Prepare data for service
        $data = Validator::sanitizeArray($_POST, [
            'first_name' => 'string',
            'last_name' => 'string',
            'phone' => 'string',
            'bio' => 'html',
            'location' => 'string',
            'website' => 'string',
            'professional_title' => 'string',
            'company' => 'string',
            'timezone' => 'string',
            'measurement_system' => 'string',
            'study_mode' => 'string',
            'stream_id' => 'int',
            'custom_stream' => 'string',
            'education_level' => 'string'
        ]);

        // Social Links handling
        if (isset($_POST['social']) && is_array($_POST['social'])) {
            $data['social_links'] = [];
            foreach ($_POST['social'] as $key => $val) {
                $data['social_links'][$key] = Validator::sanitize($val, 'string');
            }
        }

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
        $profile = $this->cache->remember($cacheKey, 300, function () use ($userId) {
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

        // Use FileService for "Paranoid-Grade" secure upload
        $userId = $this->auth->id();
        $uploadResult = FileService::uploadUserFile($file, $userId, 'avatar');

        if (!$uploadResult['success']) {
            $errorMsg = $uploadResult['error'] ?? 'Upload failed';
            // Frontend expects 'message' key
            return $this->json(['success' => false, 'message' => $errorMsg, 'error' => $errorMsg], 400);
        }

        $avatarPath = $uploadResult['url'] ?? $uploadResult['path'];
        $result = $this->profileService->updateAvatar($userId, $avatarPath);

        // Map 'message' to 'error' for frontend compatibility if failed
        if (!$result['success'] && !isset($result['error'])) {
            $result['error'] = $result['message'] ?? 'Unknown error';
        }

        // Invalidate cache
        $this->cache->delete("user_profile_{$userId}");

        return $this->json($result);
    }
}
