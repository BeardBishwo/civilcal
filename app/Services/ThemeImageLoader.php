<?php

namespace App\Services;

/**
 * Theme Image Loader
 * Dynamically loads and manages default images from theme folder
 * No hardcoding - automatically detects available images
 */
class ThemeImageLoader
{
    /**
     * Theme images directory
     */
    private static $themeDir = null;

    /**
     * Cached image list
     */
    private static $imageCache = [];

    /**
     * Initialize theme directory
     */
    private static function init()
    {
        if (self::$themeDir === null) {
            self::$themeDir = BASE_PATH . '/themes/default/assets/images';
        }
    }

    /**
     * Get all available theme images
     * Scans theme folder and returns all images
     * 
     * @return array ['type' => 'filename']
     */
    public static function getAvailableImages()
    {
        self::init();

        if (!empty(self::$imageCache)) {
            return self::$imageCache;
        }

        $images = [];

        if (!is_dir(self::$themeDir)) {
            return $images;
        }

        // Scan for all image files
        $files = scandir(self::$themeDir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $fullPath = self::$themeDir . '/' . $file;

            // Skip directories
            if (is_dir($fullPath)) {
                continue;
            }

            // Get file extension
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            // Check if it's an image
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                continue;
            }

            // Extract image type from filename (e.g., 'logo.png' => 'logo')
            $type = strtolower(pathinfo($file, PATHINFO_FILENAME));

            // Store image
            $images[$type] = $file;
        }

        self::$imageCache = $images;
        return $images;
    }

    /**
     * Get image by type
     * 
     * @param string $type Image type (logo, favicon, banner, profile)
     * @return string|null Filename or null if not found
     */
    public static function getImage($type)
    {
        $images = self::getAvailableImages();
        return $images[$type] ?? null;
    }

    /**
     * Get image path by type
     * 
     * @param string $type Image type
     * @return string Full path to image
     */
    public static function getImagePath($type)
    {
        self::init();

        $filename = self::getImage($type);
        if ($filename) {
            return self::$themeDir . '/' . $filename;
        }

        // Return default profile if not found
        $profileFile = self::getImage('profile');
        if ($profileFile) {
            return self::$themeDir . '/' . $profileFile;
        }

        return null;
    }

    /**
     * Get image URL by type
     * 
     * @param string $type Image type
     * @return string URL to image
     */
    public static function getImageUrl($type)
    {
        $filename = self::getImage($type);
        if ($filename) {
            return '/themes/default/assets/images/' . $filename;
        }

        // Return default profile if not found
        $profileFile = self::getImage('profile');
        if ($profileFile) {
            return '/themes/default/assets/images/' . $profileFile;
        }

        return '/themes/default/assets/images/profile.png';
    }

    /**
     * Check if image exists
     * 
     * @param string $type Image type
     * @return bool
     */
    public static function hasImage($type)
    {
        return self::getImage($type) !== null;
    }

    /**
     * Get all images as array with URLs
     * 
     * @return array ['type' => 'url']
     */
    public static function getAllImagesWithUrls()
    {
        $images = self::getAvailableImages();
        $result = [];

        foreach ($images as $type => $filename) {
            $result[$type] = '/themes/default/assets/images/' . $filename;
        }

        return $result;
    }

    /**
     * Get all images as array with paths
     * 
     * @return array ['type' => 'path']
     */
    public static function getAllImagesWithPaths()
    {
        self::init();
        $images = self::getAvailableImages();
        $result = [];

        foreach ($images as $type => $filename) {
            $result[$type] = self::$themeDir . '/' . $filename;
        }

        return $result;
    }

    /**
     * Clear cache (useful for testing)
     */
    public static function clearCache()
    {
        self::$imageCache = [];
    }

    /**
     * Get image info
     * 
     * @param string $type Image type
     * @return array|null Image information
     */
    public static function getImageInfo($type)
    {
        $path = self::getImagePath($type);
        if (!$path || !file_exists($path)) {
            return null;
        }

        $size = filesize($path);
        $dimensions = @getimagesize($path);

        return [
            'type' => $type,
            'filename' => self::getImage($type),
            'path' => $path,
            'url' => self::getImageUrl($type),
            'size' => $size,
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,
            'mime' => $dimensions['mime'] ?? null,
        ];
    }

    /**
     * Get all images info
     * 
     * @return array Array of image information
     */
    public static function getAllImagesInfo()
    {
        $images = self::getAvailableImages();
        $result = [];

        foreach (array_keys($images) as $type) {
            $info = self::getImageInfo($type);
            if ($info) {
                $result[$type] = $info;
            }
        }

        return $result;
    }
}
