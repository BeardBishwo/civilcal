<?php

namespace App\Services;

use Exception;
use App\Services\ThemeImageLoader;
use App\Services\ImageUploadService;
use App\Services\ImageRetrievalService;

/**
 * Image Manager Service
 * Facade for image upload and retrieval services
 * Maintains backward compatibility while using modular services
 */
class ImageManager
{
    // Storage paths (for backward compatibility)
    const STORAGE_PATH = BASE_PATH . "/storage/uploads";
    const ADMIN_IMAGES_PATH = self::STORAGE_PATH . "/admin";
    const USER_IMAGES_PATH = self::STORAGE_PATH . "/users";
    const THEME_IMAGES_PATH = BASE_PATH . "/themes/default/assets/images";

    // Image types
    const TYPE_LOGO = "logo";
    const TYPE_FAVICON = "favicon";
    const TYPE_BANNER = "banner";
    const TYPE_PROFILE = "profile";

    // Allowed extensions
    const ALLOWED_EXTENSIONS = [
        "jpg",
        "jpeg",
        "png",
        "gif",
        "webp",
        "svg",
        "ico",
    ];

    // Max file sizes (in bytes)
    const MAX_FILE_SIZE = 5242880; // 5MB
    const MAX_PROFILE_SIZE = 2097152; // 2MB

    // Image dimensions
    const IMAGE_DIMENSIONS = [
        self::TYPE_LOGO => ["width" => 200, "height" => 50],
        self::TYPE_FAVICON => ["width" => 32, "height" => 32],
        self::TYPE_BANNER => ["width" => 1920, "height" => 400],
        self::TYPE_PROFILE => ["width" => 200, "height" => 200],
    ];

    /**
     * Initialize storage directories
     * Uses modular ImageUploadService
     */
    public static function initializeStorage()
    {
        return ImageUploadService::initializeDirectories();
    }

    /**
     * Upload image for admin
     * Uses modular ImageUploadService
     *
     * @param array $file $_FILES array
     * @param string $type Image type (logo, favicon, banner)
     * @return array ['success' => bool, 'path' => string, 'error' => string]
     */
    public static function uploadAdminImage($file, $type)
    {
        return ImageUploadService::uploadAdminImage($file, $type);
    }

    /**
     * Upload image for user profile
     * Uses modular ImageUploadService
     *
     * @param array $file $_FILES array
     * @param int $userId User ID
     * @return array ['success' => bool, 'path' => string, 'error' => string]
     */
    public static function uploadUserImage($file, $userId)
    {
        return ImageUploadService::uploadUserImage($file, $userId);
    }

    /**
     * Get default image path from theme folder
     * Uses modular ImageRetrievalService
     *
     * @param string $type Image type
     * @return string Image URL path
     */
    public static function getDefaultImage($type)
    {
        return ImageRetrievalService::getDefaultImagePath($type);
    }

    /**
     * Get admin image or default
     * Uses modular ImageRetrievalService
     *
     * @param string $type Image type
     * @return string Image path
     */
    public static function getAdminImage($type)
    {
        switch ($type) {
            case self::TYPE_LOGO:
                return ImageRetrievalService::getLogo();
            case self::TYPE_FAVICON:
                return ImageRetrievalService::getFavicon();
            case self::TYPE_BANNER:
                return ImageRetrievalService::getBanner();
            default:
                return ImageRetrievalService::getDefaultImagePath($type);
        }
    }

    /**
     * Get user image or default
     * Uses modular ImageRetrievalService
     *
     * @param int $userId User ID
     * @return string Image path
     */
    public static function getUserImage($userId)
    {
        return ImageRetrievalService::getUserProfile($userId);
    }

    /**
     * Delete image
     * Uses modular ImageUploadService
     *
     * @param string $path Image path
     * @return bool
     */
    public static function deleteImage($path)
    {
        $fullPath = BASE_PATH . $path;
        return ImageUploadService::deleteImage($fullPath);
    }

    /**
     * Get image URL for web
     * Uses modular ImageRetrievalService
     *
     * @param string $path Image path
     * @return string Full URL
     */
    public static function getImageUrl($path)
    {
        return ImageRetrievalService::getFullUrl($path);
    }

    /**
     * Check if image exists
     * Uses modular ImageRetrievalService
     *
     * @param string $path Image path
     * @return bool
     */
    public static function imageExists($path)
    {
        return ImageRetrievalService::imageExists($path);
    }

    /**
     * Get all admin images
     * Uses modular ImageRetrievalService
     *
     * @return array Array of image URLs
     */
    public static function getAllAdminImages()
    {
        return ImageRetrievalService::getAllAdminImages();
    }

    /**
     * Clear image cache
     * Uses modular ImageRetrievalService
     *
     * @param string|null $type Image type or null for all
     * @return void
     */
    public static function clearCache($type = null)
    {
        ImageRetrievalService::clearCache($type);
    }

    /**
     * Get image info
     * Uses modular ImageRetrievalService
     *
     * @param string $type Image type
     * @param int|null $userId User ID for profile images
     * @return array Image information
     */
    public static function getImageInfo($type, $userId = null)
    {
        return ImageRetrievalService::getImageInfo($type, $userId);
    }
}
