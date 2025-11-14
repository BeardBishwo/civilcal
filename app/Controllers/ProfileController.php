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

        $data = [
            'user' => $user,
            'statistics' => $stats,
            'profile_completion' => $profileCompletion,
            'notification_preferences' => $this->userModel->getNotificationPreferencesAttribute($userId),
            'social_links' => $this->userModel->getSocialLinksAttribute($userId)
        ];

        $this->view('user/profile', $data);
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
