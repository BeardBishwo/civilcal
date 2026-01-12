<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ImageManager;
use App\Models\Image;
use App\Core\Auth;
use App\Services\Security;

/**
 * Profile Image Controller
 * Handles user profile image uploads
 */
class ProfileImageController extends Controller
{
    private function requireCsrfToken()
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($token) || !Security::validateCsrfToken($token)) {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid CSRF token'], 403);
        }
    }

    /**
     * Upload user profile image
     */
    public function upload()
    {
        // Check authentication
        if (!Auth::check()) {
            return $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid request method']);
        }

        $this->requireCsrfToken();

        if (!isset($_FILES['profile_image'])) {
            return $this->jsonResponse(['success' => false, 'error' => 'No file uploaded']);
        }

        $userId = Auth::user()->id ?? null;
        if (!$userId) {
            return $this->jsonResponse(['success' => false, 'error' => 'User not found']);
        }

        $result = ImageManager::uploadUserImage($_FILES['profile_image'], $userId);

        if ($result['success']) {
            // Delete old image record
            Image::where('user_id', $userId)
                ->where('image_type', ImageManager::TYPE_PROFILE)
                ->whereNull('deleted_at')
                ->delete();

            // Verify MIME type safely
            $realMimeType = mime_content_type($_FILES['profile_image']['tmp_name']);

            // Save new image to database
            Image::create([
                'user_id' => $userId,
                'image_type' => ImageManager::TYPE_PROFILE,
                'original_name' => $_FILES['profile_image']['name'],
                'filename' => $result['filename'],
                'path' => $result['path'],
                'file_size' => $result['size'],
                'mime_type' => $realMimeType, // Secure MIME type
                'is_admin' => false,
            ]);
        }

        return $this->jsonResponse($result);
    }

    /**
     * Get user profile image
     */
    public function get()
    {
        if (!Auth::check()) {
            return $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $userId = Auth::user()->id ?? null;
        $imagePath = ImageManager::getUserImage($userId);

        return $this->jsonResponse([
            'success' => true,
            'image' => [
                'path' => $imagePath,
                'url' => ImageManager::getImageUrl($imagePath),
            ]
        ]);
    }

    /**
     * Delete user profile image
     */
    public function delete()
    {
        if (!Auth::check()) {
            return $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid request method']);
        }

        $this->requireCsrfToken();

        $userId = Auth::user()->id ?? null;

        // Delete image file
        $image = Image::where('user_id', $userId)
            ->where('image_type', ImageManager::TYPE_PROFILE)
            ->whereNull('deleted_at')
            ->first();

        if ($image) {
            ImageManager::deleteImage($image->attributes['path'] ?? '');
            $image->softDelete();
        }

        return $this->jsonResponse(['success' => true, 'message' => 'Profile image deleted']);
    }

    /**
     * Return JSON response
     */
    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
