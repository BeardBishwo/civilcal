<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ImageManager;
use App\Models\Image;

/**
 * Admin Image Controller
 * Handles admin image uploads and management
 */
class ImageController extends Controller
{
    /**
     * Show image management page
     */
    public function index()
    {
        // Get all admin images
        $images = Image::where('is_admin', true)->whereNull('deleted_at')->get();
        
        return $this->view->render('admin/images/index', [
            'images' => $images,
            'page_title' => 'Image Management',
        ]);
    }

    /**
     * Upload logo
     */
    public function uploadLogo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid request method']);
        }

        if (empty($_POST['csrf_token']) || !\App\Services\Security::validateCsrfToken($_POST['csrf_token'])) {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid CSRF token']);
        }

        if (!isset($_FILES['logo'])) {
            return $this->jsonResponse(['success' => false, 'error' => 'No file uploaded']);
        }

        $result = ImageManager::uploadAdminImage($_FILES['logo'], ImageManager::TYPE_LOGO);
        
        if ($result['success']) {
            // Save to database
            Image::create([
                'image_type' => ImageManager::TYPE_LOGO,
                'original_name' => $_FILES['logo']['name'],
                'filename' => $result['filename'],
                'path' => $result['path'],
                'file_size' => $result['size'],
                'mime_type' => $_FILES['logo']['type'],
                'is_admin' => true,
            ]);
        }

        return $this->jsonResponse($result);
    }

    /**
     * Upload favicon
     */
    public function uploadFavicon()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid request method']);
        }

        if (empty($_POST['csrf_token']) || !\App\Services\Security::validateCsrfToken($_POST['csrf_token'])) {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid CSRF token']);
        }

        if (!isset($_FILES['favicon'])) {
            return $this->jsonResponse(['success' => false, 'error' => 'No file uploaded']);
        }

        $result = ImageManager::uploadAdminImage($_FILES['favicon'], ImageManager::TYPE_FAVICON);
        
        if ($result['success']) {
            // Save to database
            Image::create([
                'image_type' => ImageManager::TYPE_FAVICON,
                'original_name' => $_FILES['favicon']['name'],
                'filename' => $result['filename'],
                'path' => $result['path'],
                'file_size' => $result['size'],
                'mime_type' => $_FILES['favicon']['type'],
                'is_admin' => true,
            ]);
        }

        return $this->jsonResponse($result);
    }

    /**
     * Upload banner
     */
    public function uploadBanner()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid request method']);
        }

        if (empty($_POST['csrf_token']) || !\App\Services\Security::validateCsrfToken($_POST['csrf_token'])) {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid CSRF token']);
        }

        if (!isset($_FILES['banner'])) {
            return $this->jsonResponse(['success' => false, 'error' => 'No file uploaded']);
        }

        $result = ImageManager::uploadAdminImage($_FILES['banner'], ImageManager::TYPE_BANNER);
        
        if ($result['success']) {
            // Save to database
            Image::create([
                'image_type' => ImageManager::TYPE_BANNER,
                'original_name' => $_FILES['banner']['name'],
                'filename' => $result['filename'],
                'path' => $result['path'],
                'file_size' => $result['size'],
                'mime_type' => $_FILES['banner']['type'],
                'is_admin' => true,
            ]);
        }

        return $this->jsonResponse($result);
    }

    /**
     * Delete image
     */
    public function deleteImage()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid request method']);
        }

        if (empty($_POST['csrf_token']) || !\App\Services\Security::validateCsrfToken($_POST['csrf_token'])) {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid CSRF token']);
        }

        $imageId = $_POST['image_id'] ?? null;
        if (!$imageId) {
            return $this->jsonResponse(['success' => false, 'error' => 'Image ID required']);
        }

        $image = Image::find($imageId);
        if (!$image) {
            return $this->jsonResponse(['success' => false, 'error' => 'Image not found']);
        }

        // Delete file
        ImageManager::deleteImage($image->attributes['path'] ?? '');
        
        // Soft delete from database
        $image->softDelete();

        return $this->jsonResponse(['success' => true, 'message' => 'Image deleted successfully']);
    }

    /**
     * Get current admin image
     */
    public function getCurrent()
    {
        $type = $_GET['type'] ?? null;
        if (!$type) {
            return $this->jsonResponse(['success' => false, 'error' => 'Image type required']);
        }

        $image = Image::where('is_admin', true)
            ->where('image_type', $type)
            ->whereNull('deleted_at')
            ->first();

        if ($image) {
            return $this->jsonResponse([
                'success' => true,
                'image' => [
                    'id' => $image->attributes['id'] ?? null,
                    'path' => $image->attributes['path'] ?? '',
                    'url' => ImageManager::getImageUrl($image->attributes['path'] ?? ''),
                ]
            ]);
        }

        // Return default
        return $this->jsonResponse([
            'success' => true,
            'image' => [
                'path' => ImageManager::getDefaultImage($type),
                'url' => ImageManager::getImageUrl(ImageManager::getDefaultImage($type)),
                'is_default' => true,
            ]
        ]);
    }

    /**
     * Return JSON response
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
