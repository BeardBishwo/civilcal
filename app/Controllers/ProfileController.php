<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\FileUploadService;
use Exception;

/**
 * Profile Controller
 * Handles user profile management operations
 */
class ProfileController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Show user profile page
     */
    public function index()
    {
        $userId = $this->getCurrentUserId();
        $user = $this->userModel->find($userId);
        $stats = $this->userModel->getStatistics($userId);
        $profileCompletion = $this->userModel->getProfileCompletion($userId);

        if (!$user) {
            $_SESSION['flash_messages']['error'] = 'User not found.';
            $this->redirect('/dashboard');
        }

        // Get 2FA status
        $twoFactorData = $this->userModel->getTwoFactorData($userId);
        $twoFactorStatus = [
            'enabled' => $twoFactorData['two_factor_enabled'] ?? false,
            'confirmed_at' => $twoFactorData['two_factor_confirmed_at'] ?? null,
            // Decode recovery codes to count them if needed, or just default to 8 initially
            'recovery_codes_remaining' => 0 
        ];
        
        // Get export requests (with error handling)
        $exportRequests = [];
        try {
            $exportService = new \App\Services\DataExportService();
            $exportRequests = $exportService->getExportRequests($userId);
        } catch (\Exception $e) {
            error_log('Export Requests Error: ' . $e->getMessage());
            $exportRequests = [];
        }

        // Get rank data
        $rankService = new \App\Services\RankService();
        $gamification = new \App\Services\GamificationService();
        $wallet = $gamification->getWallet($userId);
        $rankData = $rankService->getUserRankData($stats, $wallet);


        $data = [
            'user' => $user,
            'statistics' => $stats,
            'profile_completion' => $profileCompletion,
            'notification_preferences' => $this->userModel->getNotificationPreferencesAttribute($userId),
            'social_links' => $this->userModel->getSocialLinksAttribute($userId),
            'two_factor_status' => $twoFactorStatus,
            'export_requests' => $exportRequests,
            'rank_data' => $rankData
        ];
        
        $this->view->render('user/profile', $data);
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        try {
            if (!$this->isPostRequest()) {
                throw new Exception('Invalid request method');
            }

            // CSRF Protection
            if (!\App\Services\Security::validateCsrfToken()) {
                throw new Exception('Invalid CSRF token');
            }

            $userId = $this->getCurrentUserId();
            $data = $this->getRequestData();
            
            // Handle avatar upload if present
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $avatarPath = $this->handleAvatarUpload($_FILES['avatar']);
                if ($avatarPath) {
                    $data['avatar'] = $avatarPath;
                }
            }
            
            // Handle social links JSON
            if (isset($data['social_links'])) {
                $socialLinks = json_decode($data['social_links'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->userModel->setSocialLinksAttribute($userId, $socialLinks);
                    unset($data['social_links']);
                }
            }

            // Get current user for validation
            $currentUser = $this->userModel->find($userId);
            $coreUpdates = [];

            // Validate Username
            if (isset($data['username'])) {
                $username = trim($data['username']);
                if (strlen($username) < 3) {
                    $this->json(['error' => "Username must be at least 3 characters"], 400);
                    return;
                }
                if ($username !== $currentUser['username']) {
                    $existing = User::findByUsername($username);
                    if ($existing && $existing->id != $userId) {
                        $this->json(['error' => "Username is already taken"], 400);
                        return;
                    }
                    $coreUpdates['username'] = $username;
                }
            }

            // Validate Email
            if (isset($data['email'])) {
                $email = trim($data['email']);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->json(['error' => "Invalid email format"], 400);
                    return;
                }
                if ($email !== $currentUser['email']) {
                    $existing = $this->userModel->findByEmail($email);
                    if ($existing && $existing->id != $userId) {
                        $this->json(['error' => "Email is already registered"], 400);
                        return;
                    }
                    $coreUpdates['email'] = $email;
                    // Reset verification
                    $coreUpdates['email_verified'] = 0;
                    $coreUpdates['email_verified_at'] = null;
                }
            }
            
            // Allow First/Last Name in core updates as well (if User model separates them)
            if (isset($data['first_name'])) $coreUpdates['first_name'] = $data['first_name'];
            if (isset($data['last_name'])) $coreUpdates['last_name'] = $data['last_name'];

            // Update core fields if any
            if (!empty($coreUpdates)) {
                $this->userModel->adminUpdate($userId, $coreUpdates);
            }
            
            $success = $this->userModel->updateProfile($userId, $data);
            
            if ($success) {
                $profileCompletion = $this->userModel->getProfileCompletion($userId);
                $this->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'profile_completion' => $profileCompletion
                ]);
            } else {
                throw new Exception('Failed to update profile');
            }
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications()
    {
        try {
            if (!$this->isPostRequest()) {
                throw new Exception('Invalid request method');
            }

            // CSRF Protection
            if (!\App\Services\Security::validateCsrfToken()) {
                throw new Exception('Invalid CSRF token');
            }

            $userId = $this->getCurrentUserId();
            $data = $this->getRequestData();
            
            // Validate notification preferences
            $preferences = [
                'email_notifications' => filter_var($data['email_notifications'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'notification_preferences' => [
                    'calculation_results' => filter_var($data['notification_preferences']['calculation_results'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'system_updates' => filter_var($data['notification_preferences']['system_updates'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'security_alerts' => filter_var($data['notification_preferences']['security_alerts'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'marketing' => filter_var($data['notification_preferences']['marketing'] ?? false, FILTER_VALIDATE_BOOLEAN)
                ]
            ];
            
            $success = $this->userModel->updateNotificationPreferences($userId, $preferences);
            
            if ($success) {
                $this->json([
                    'success' => true,
                    'message' => 'Notification preferences updated successfully!'
                ]);
            } else {
                throw new Exception('Failed to update preferences');
            }
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update privacy settings
     */
    public function updatePrivacy()
    {
        try {
            if (!$this->isPostRequest()) {
                throw new Exception('Invalid request method');
            }

            // CSRF Protection
            if (!\App\Services\Security::validateCsrfToken()) {
                throw new Exception('Invalid CSRF token');
            }

            $userId = $this->getCurrentUserId();
            $data = $this->getRequestData();
            
            // Validate privacy setting
            $allowedPrivacy = ['public', 'private', 'team'];
            if (!in_array($data['calculation_privacy'] ?? '', $allowedPrivacy)) {
                throw new Exception('Invalid privacy setting');
            }
            
            $success = $this->userModel->updatePrivacySettings($userId, $data);
            
            if ($success) {
                $this->json([
                    'success' => true,
                    'message' => 'Privacy settings updated successfully!'
                ]);
            } else {
                throw new Exception('Failed to update settings');
            }
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Change user password
     */
    public function changePassword()
    {
        try {
            if (!$this->isPostRequest()) {
                throw new Exception('Invalid request method');
            }

            // CSRF Protection
            if (!\App\Services\Security::validateCsrfToken()) {
                throw new Exception('Invalid CSRF token');
            }

            $userId = $this->getCurrentUserId();
            $data = $this->getRequestData();
            
            // Validate input
            if (empty($data['current_password']) || empty($data['new_password']) || empty($data['confirm_password'])) {
                throw new Exception('All password fields are required');
            }
            
            if ($data['new_password'] !== $data['confirm_password']) {
                throw new Exception('New passwords do not match');
            }
            
            if (strlen($data['new_password']) < 6) {
                throw new Exception('Password must be at least 6 characters long');
            }
            
            // Verify current password
            $user = $this->userModel->find($userId);
            if (!$user || !password_verify($data['current_password'], $user['password'])) {
                throw new Exception('Current password is incorrect');
            }
            
            // Update password
            $success = $this->userModel->changePassword($userId, $data['new_password']);
            
            if ($success) {
                $this->json([
                    'success' => true,
                    'message' => 'Password changed successfully!'
                ]);
            } else {
                throw new Exception('Failed to change password');
            }
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete user account
     */
    public function deleteAccount()
    {
        try {
            if (!$this->isPostRequest()) {
                throw new Exception('Invalid request method');
            }

            // CSRF Protection
            if (!\App\Services\Security::validateCsrfToken()) {
                throw new Exception('Invalid CSRF token');
            }

            $userId = $this->getCurrentUserId();
            $data = $this->getRequestData();
            
            // Verify password for account deletion
            if (empty($data['password'])) {
                throw new Exception('Password is required to delete account');
            }
            
            $user = $this->userModel->find($userId);
            if (!$user || !password_verify($data['password'], $user['password'])) {
                throw new Exception('Incorrect password');
            }
            
            // Confirm account deletion
            if (empty($data['confirm_delete']) || $data['confirm_delete'] !== 'DELETE') {
                throw new Exception('Please type "DELETE" to confirm account deletion');
            }
            
            // Delete account
            $success = $this->userModel->deleteAccount($userId);
            
            if ($success) {
                // Clear session and redirect
                session_destroy();
                
                $this->json([
                    'success' => true,
                    'message' => 'Account deleted successfully.',
                    'redirect' => app_base_url('/')
                ]);
            } else {
                throw new Exception('Failed to delete account');
            }
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Serve user avatar image
     */
    public function serveAvatar($filename)
    {
        $userId = $this->getCurrentUserId();
        $user = $this->userModel->find($userId);
        
        $safeFilename = basename($filename);
        // Use null-coalescing so missing avatar column does not trigger warnings
        if (!$user || (($user['avatar'] ?? null) !== $safeFilename)) {
            http_response_code(404);
            exit('Avatar not found');
        }

        $uploadDir = 'public/uploads/avatars/';
        $filePath = $uploadDir . $safeFilename;
        $realBase = realpath($uploadDir) ?: $uploadDir;
        $realFile = realpath($filePath);
        if ($realFile === false || strpos($realFile, $realBase) !== 0) {
            http_response_code(404);
            exit('Avatar not found');
        }
        
        if (file_exists($filePath)) {
            $imageInfo = getimagesize($filePath);
            $mimeType = $imageInfo['mime'];
            
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: public, max-age=31536000');
            
            readfile($filePath);
        } else {
            http_response_code(404);
            exit('Avatar file not found');
        }
    }

    /**
     * Get profile data (API endpoint)
     */
    public function getProfile()
    {
        try {
            // Support both session and HTTP Basic Auth
            $userId = null;
            
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                // Authenticate using HTTP Basic Auth
                $user = User::findByUsername($_SERVER['PHP_AUTH_USER']);
                if ($user) {
                    // Convert to array if it's an object
                    $userArray = is_array($user) ? $user : (array) $user;
                    if (password_verify($_SERVER['PHP_AUTH_PW'], $userArray['password'])) {
                        $userId = $userArray['id'];
                    }
                }
            }
            
            if (!$userId) {
                http_response_code(401);
                $this->json(['error' => 'Unauthorized'], 401);
                return;
            }
            
            $user = $this->userModel->find($userId);
            
            if (!$user) {
                http_response_code(404);
                $this->json(['error' => 'User not found'], 404);
                return;
            }
            
            // Remove sensitive data
            unset($user['password']);
            
            // Add additional profile data
            $user['statistics'] = $this->userModel->getStatistics($userId);
            $user['profile_completion'] = $this->userModel->getProfileCompletion($userId);
            
            http_response_code(200);
            $this->json($user);
            
        } catch (Exception $e) {
            http_response_code(500);
            $this->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update profile via API (supports PUT and POST)
     */
    public function updateProfileApi()
    {
        try {
            // Check authentication - support both session and HTTP Basic Auth
            $userId = null;
            
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                // Authenticate using HTTP Basic Auth
                $user = User::findByUsername($_SERVER['PHP_AUTH_USER']);
                if ($user) {
                    // Convert to array if it's an object
                    $userArray = is_array($user) ? $user : (array) $user;
                    if (password_verify($_SERVER['PHP_AUTH_PW'], $userArray['password'])) {
                        $userId = $userArray['id'];
                    }
                }
            }
            
            if (!$userId) {
                http_response_code(401);
                $this->json(['error' => 'Unauthorized'], 401);
                return;
            }
            
            // Get input from PUT/POST
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                $this->json(['error' => 'Invalid JSON'], 400);
                return;
            }
            
            // Validate data
            if (empty($data)) {
                http_response_code(400);
                $this->json(['error' => 'No data provided'], 400);
                return;
            }
            
            // Filter allowed fields
            // Filter allowed fields
            $allowedFields = ['first_name', 'last_name', 'company', 'phone', 'bio', 'username', 'email'];
            $updateData = [];
            
            // Get current user data to check for changes
            $currentUser = $this->userModel->find($userId);

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    // Validate data types
                    if (!is_string($data[$field]) && !is_null($data[$field])) {
                        http_response_code(400);
                        $this->json(['error' => "Invalid type for field '$field'"], 400);
                        return;
                    }
                    
                    if (is_string($data[$field])) {
                        $value = trim($data[$field]);
                        
                        // Check max length
                        if (strlen($value) > 255) {
                            http_response_code(400);
                            $this->json(['error' => "Field '$field' exceeds maximum length"], 400);
                            return;
                        }

                        // Special handling for username
                        if ($field === 'username') {
                            if (strlen($value) < 3) {
                                http_response_code(400);
                                $this->json(['error' => "Username must be at least 3 characters"], 400);
                                return;
                            }
                            // Check uniqueness
                            if ($value !== $currentUser['username']) {
                                $existing = User::findByUsername($value);
                                if ($existing && $existing->id != $userId) {
                                    http_response_code(400);
                                    $this->json(['error' => "Username is already taken"], 400);
                                    return;
                                }
                            }
                        }

                        // Special handling for email
                        if ($field === 'email') {
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                http_response_code(400);
                                $this->json(['error' => "Invalid email format"], 400);
                                return;
                            }
                            // Check uniqueness
                            if ($value !== $currentUser['email']) {
                                $existing = $this->userModel->findByEmail($value);
                                if ($existing && $existing->id != $userId) {
                                    http_response_code(400);
                                    $this->json(['error' => "Email is already registered"], 400);
                                    return;
                                }
                                // If email changed, reset verification
                                $updateData['email_verified'] = 0;
                                $updateData['email_verified_at'] = null;
                            }
                        }
                        
                        $updateData[$field] = $value;
                    } else {
                        $updateData[$field] = null;
                    }
                }
            }
            
            if (empty($updateData)) {
                http_response_code(400);
                $this->json(['error' => 'No valid data provided for update'], 400);
                return;
            }
            
            // Update profile
            $success = $this->userModel->updateProfile($userId, $updateData);
            // Also need to update core fields via adminUpdate if updateProfile doesn't handle them
            // The User model separates these. Let's check User::updateProfile vs adminUpdate.
            // User::updateProfile only allows specific fields. I need to update User model or use adminUpdate logic here.
            // Actually, best to use adminUpdate for core fields (email, username) and updateProfile for others.
            // OR simpler: modify updateProfile in User model to allow these fields? No, separate concerns.
            // Let's call adminUpdate for core fields if present.
            
            $coreFields = ['username', 'email', 'first_name', 'last_name'];
            $coreData = array_intersect_key($updateData, array_flip($coreFields));
            if (!empty($coreData)) {
                $this->userModel->adminUpdate($userId, $coreData);
            }
            // Remove core fields from updateData before passing to updateProfile to avoid double work/errors?
            // Actually `updateProfile` filters its own fields. So it's safe to pass all.
            
            if ($success || !empty($coreData)) { // Success if either worked
                // Get updated user data
                $user = $this->userModel->find($userId);
                unset($user['password']);
                
                // Return the updated fields
                $response = [];
                foreach ($allowedFields as $field) {
                    if (isset($user[$field])) {
                        $response[$field] = $user[$field];
                    }
                }
                
                http_response_code(200);
                $this->json($response);
            } else {
                http_response_code(500);
                $this->json(['error' => 'Failed to update profile'], 500);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            $this->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get current user ID
     */
    protected function getCurrentUserId()
    {
        return $_SESSION['user_id'] ?? 1;
    }

    /**
     * Check if current request is POST
     */
    private function isPostRequest()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    private function getRequestData()
    {
        $input = file_get_contents('php://input');
        if ($input) {
            $json = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return array_merge($_POST, $json);
            }
        }
        return $_POST;
    }

    /**
     * Send JSON response (deprecated - use parent json() method)
     */
    private function sendJsonResponse($data, $statusCode = 200)
    {
        $this->json($data, $statusCode);
    }

    /**
     * Handle avatar upload and image resizing
     */
    private function handleAvatarUpload($file)
    {
        // Delegate validation and safe move to centralized service
        $uploader = new FileUploadService();
        $dest = BASE_PATH . '/public/uploads/avatars';
        $result = $uploader->uploadImage($file, $dest);
        if (!($result['success'] ?? false)) {
            throw new Exception($result['message'] ?? 'Failed to upload file');
        }

        // Resize image to 200x200 using the stored absolute path
        $this->resizeImage($result['file_path'], 200, 200);
        return $result['filename'];
    }

    /**
     * Resize uploaded image
     */
    /**
     * Enable 2FA (Start Process)
     */
    public function enableTwoFactor()
    {
        try {
            error_log("2FA Enable: Starting process");
            
            if (!$this->isPostRequest()) {
                throw new Exception('Invalid request method');
            }

            // CSRF Protection
            if (!\App\Services\Security::validateCsrfToken()) {
                throw new Exception('Invalid CSRF token');
            }

            $userId = $this->getCurrentUserId();
            error_log("2FA Enable: User ID: " . $userId);
            
            $data = $this->getRequestData();

            // Verify password first
            $currentPassword = $data['password'] ?? '';
            if (empty($currentPassword)) {
                error_log("2FA Enable: No password provided");
                $this->json(['error' => 'Password is required'], 400);
                return;
            }
            
            $user = $this->userModel->find($userId);
            if (!$user) {
                error_log("2FA Enable: User not found");
                $this->json(['error' => 'User not found'], 404);
                return;
            }
            
            if (!password_verify($currentPassword, $user['password'])) {
                error_log("2FA Enable: Incorrect password");
                $this->json(['error' => 'Incorrect password'], 401);
                return;
            }

            error_log("2FA Enable: Password verified, generating secret");
            
            // Check if Google2FA class exists
            if (!class_exists('\\PragmaRX\\Google2FA\\Google2FA')) {
                error_log("2FA Enable: Google2FA class not found");
                $this->json(['error' => 'Google2FA library not installed'], 500);
                return;
            }
            
            // Generate Secret
            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            $secret = $google2fa->generateSecretKey();
            error_log("2FA Enable: Secret generated");
            
            // Generate QR Code URL
            $appName = 'Bishwo Calculator';
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                $appName,
                $user['email'],
                $secret
            );
            error_log("2FA Enable: QR URL generated");
            
            // Backup codes
            $recoveryCodes = [];
            for ($i = 0; $i < 8; $i++) {
                $recoveryCodes[] = bin2hex(random_bytes(5));
            }
            error_log("2FA Enable: Recovery codes generated");

            // Save secret (but not enabled yet)
            $this->userModel->enableTwoFactor($userId, $secret, $recoveryCodes);
            error_log("2FA Enable: Data saved to database");
            
            // Return secret and QR URL for frontend to display
            // We'll use a QR code library or simple API in production, but for now passing the data
            $this->json([
                'success' => true,
                'secret' => $secret,
                'qr_code_url' => $qrCodeUrl,
                'recovery_codes' => $recoveryCodes
            ]);

        } catch (Exception $e) {
            error_log("2FA Enable Error: " . $e->getMessage());
            error_log("2FA Enable Stack: " . $e->getTraceAsString());
            $this->json(['error' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            error_log("2FA Enable Fatal Error: " . $e->getMessage());
            error_log("2FA Enable Fatal Stack: " . $e->getTraceAsString());
            $this->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Confirm and Activate 2FA
     */
    public function confirmTwoFactor()
    {
        try {
            if (!$this->isPostRequest()) {
                throw new Exception('Invalid request method');
            }

            // CSRF Protection
            if (!\App\Services\Security::validateCsrfToken()) {
                throw new Exception('Invalid CSRF token');
            }

            $userId = $this->getCurrentUserId();
            $data = $this->getRequestData();
            $code = $data['code'] ?? '';

            if (empty($code)) {
                $this->json(['error' => 'Code is required'], 400);
                return;
            }

            // Get user secret
            $twoFactorData = $this->userModel->getTwoFactorData($userId);
            if (!$twoFactorData || empty($twoFactorData['two_factor_secret'])) {
                 $this->json(['error' => '2FA setup not initiated'], 400);
                 return;
            }

            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            $valid = $google2fa->verifyKey($twoFactorData['two_factor_secret'], $code);

            if ($valid) {
                // Activate
                $this->userModel->confirmTwoFactor($userId);
                
                $this->json([
                    'success' => true, 
                    'message' => 'Two-factor authentication verified and enabled!'
                ]);
            } else {
                $this->json(['error' => 'Invalid verification code'], 400);
            }

        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Disable 2FA
     */
    public function disableTwoFactor()
    {
        try {
            if (!$this->isPostRequest()) {
                throw new Exception('Invalid request method');
            }

            // CSRF Protection
            if (!\App\Services\Security::validateCsrfToken()) {
                throw new Exception('Invalid CSRF token');
            }

            $userId = $this->getCurrentUserId();
            $data = $this->getRequestData();
            $password = $data['password'] ?? '';

            // Verify password
            $user = $this->userModel->find($userId);
            if (!$user || !password_verify($password, $user['password'])) {
                $this->json(['error' => 'Incorrect password'], 401);
                return;
            }

            $this->userModel->disableTwoFactor($userId);

            $this->json([
                'success' => true,
                'message' => 'Two-factor authentication has been disabled.'
            ]);

        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function resizeImage($imagePath, $maxWidth, $maxHeight)
    {
        $imageInfo = getimagesize($imagePath);
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $imageType = $imageInfo[2];
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = round($originalWidth * $ratio);
        $newHeight = round($originalHeight * $ratio);
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($imagePath);
                // Enable transparency
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($imagePath);
                break;
            default:
                throw new Exception('Unsupported image type');
        }
        
        // Resize
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Save resized image
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $imagePath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $imagePath, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($newImage, $imagePath);
                break;
        }
        
        // Free memory
        imagedestroy($source);
        imagedestroy($newImage);
    }
}
?>
