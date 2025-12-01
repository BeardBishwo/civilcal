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

        // Get 2FA status (with error handling)
        $twoFactorStatus = null;
        try {
            $twoFactorService = new \App\Services\TwoFactorAuthService();
            $twoFactorStatus = $twoFactorService->getStatus($userId);
        } catch (\Exception $e) {
            error_log('2FA Status Error: ' . $e->getMessage());
            $twoFactorStatus = ['enabled' => false, 'confirmed_at' => null, 'recovery_codes_remaining' => 0];
        }
        
        // Get export requests (with error handling)
        $exportRequests = [];
        try {
            $exportService = new \App\Services\DataExportService();
            $exportRequests = $exportService->getExportRequests($userId);
        } catch (\Exception $e) {
            error_log('Export Requests Error: ' . $e->getMessage());
            $exportRequests = [];
        }

        $data = [
            'user' => $user,
            'statistics' => $stats,
            'profile_completion' => $profileCompletion,
            'notification_preferences' => $this->userModel->getNotificationPreferencesAttribute($userId),
            'social_links' => $this->userModel->getSocialLinksAttribute($userId),
            'two_factor_status' => $twoFactorStatus,
            'export_requests' => $exportRequests
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
                    'redirect' => '/'
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
            $allowedFields = ['first_name', 'last_name', 'company', 'phone', 'bio'];
            $updateData = [];
            
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
            
            if ($success) {
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

    /**
     * Get request data
     */
    private function getRequestData()
    {
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
