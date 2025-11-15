<?php

namespace App\Services;

use Exception;

/**
 * Image Retrieval Service
 * Modular system for retrieving logo, favicon, banner, and profile images
 * Handles fallback to default images when uploads don't exist
 */
class ImageRetrievalService
{
    // Storage paths
    private const STORAGE_BASE = BASE_PATH . "/storage/uploads";
    private const PUBLIC_BASE = BASE_PATH . "/public/assets";
    private const THEME_BASE = BASE_PATH . "/themes/default/assets";

    // Default image paths (from theme)
    private const DEFAULT_IMAGES = [
        "logo" => "/themes/default/assets/images/logo.png",
        "favicon" => "/themes/default/assets/images/favicon.png",
        "banner" => "/themes/default/assets/images/banner.jpg",
        "profile" => "/themes/default/assets/images/profile.png",
    ];

    // Upload URL paths
    private const UPLOAD_URLS = [
        "logo" => "/storage/uploads/admin/logos",
        "favicon" => "/assets/icons",
        "banner" => "/storage/uploads/admin/banners",
        "profile" => "/storage/uploads/users",
    ];

    // Cache for retrieved images
    private static $cache = [];

    /**
     * Get logo image URL
     * Returns uploaded logo or default from theme
     *
     * @param bool $forceRefresh Force cache refresh
     * @return string Image URL
     */
    public static function getLogo(bool $forceRefresh = false): string
    {
        $cacheKey = "logo";

        if (!$forceRefresh && isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        // Check for uploaded logo
        $uploadedLogo = self::getUploadedImage("logo", "admin/logos");

        if ($uploadedLogo) {
            self::$cache[$cacheKey] = $uploadedLogo;
            return $uploadedLogo;
        }

        // Check site_meta.json for logo path
        $metaLogo = self::getLogoFromMeta();
        if ($metaLogo) {
            self::$cache[$cacheKey] = $metaLogo;
            return $metaLogo;
        }

        // Return default logo
        $defaultLogo = self::DEFAULT_IMAGES["logo"];
        self::$cache[$cacheKey] = $defaultLogo;
        return $defaultLogo;
    }

    /**
     * Get favicon URL
     * Returns uploaded favicon or default
     *
     * @param bool $forceRefresh Force cache refresh
     * @return string Image URL
     */
    public static function getFavicon(bool $forceRefresh = false): string
    {
        $cacheKey = "favicon";

        if (!$forceRefresh && isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        // Check for uploaded favicon in public/assets/icons
        $iconsPath = self::PUBLIC_BASE . "/icons";
        $faviconFiles = glob($iconsPath . "/favicon_*");

        if (!empty($faviconFiles)) {
            // Get the most recent favicon
            usort($faviconFiles, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });

            $filename = basename($faviconFiles[0]);
            $url = "/assets/icons/" . $filename;
            self::$cache[$cacheKey] = $url;
            return $url;
        }

        // Check site_meta.json for favicon path
        $metaFavicon = self::getFaviconFromMeta();
        if ($metaFavicon) {
            self::$cache[$cacheKey] = $metaFavicon;
            return $metaFavicon;
        }

        // Return default favicon
        $defaultFavicon = self::DEFAULT_IMAGES["favicon"];
        self::$cache[$cacheKey] = $defaultFavicon;
        return $defaultFavicon;
    }

    /**
     * Get banner image URL
     * Returns uploaded banner or default
     *
     * @param bool $forceRefresh Force cache refresh
     * @return string Image URL
     */
    public static function getBanner(bool $forceRefresh = false): string
    {
        $cacheKey = "banner";

        if (!$forceRefresh && isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        // Check for uploaded banner
        $uploadedBanner = self::getUploadedImage("banner", "admin/banners");

        if ($uploadedBanner) {
            self::$cache[$cacheKey] = $uploadedBanner;
            return $uploadedBanner;
        }

        // Return default banner
        $defaultBanner = self::DEFAULT_IMAGES["banner"];
        self::$cache[$cacheKey] = $defaultBanner;
        return $defaultBanner;
    }

    /**
     * Get user profile image URL
     * Returns uploaded profile image or default
     *
     * @param int $userId User ID
     * @param bool $forceRefresh Force cache refresh
     * @return string Image URL
     */
    public static function getUserProfile(
        int $userId,
        bool $forceRefresh = false,
    ): string {
        $cacheKey = "profile_" . $userId;

        if (!$forceRefresh && isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        // Check for uploaded profile image
        $userPath = self::STORAGE_BASE . "/users/" . $userId;

        if (is_dir($userPath)) {
            $profileFiles = glob($userPath . "/profile_*");

            if (!empty($profileFiles)) {
                // Get the most recent profile image
                usort($profileFiles, function ($a, $b) {
                    return filemtime($b) - filemtime($a);
                });

                $filename = basename($profileFiles[0]);
                $url = "/storage/uploads/users/" . $userId . "/" . $filename;
                self::$cache[$cacheKey] = $url;
                return $url;
            }
        }

        // Return default profile image
        $defaultProfile = self::DEFAULT_IMAGES["profile"];
        self::$cache[$cacheKey] = $defaultProfile;
        return $defaultProfile;
    }

    /**
     * Get uploaded image for admin images
     *
     * @param string $type Image type
     * @param string $subPath Sub-directory path
     * @return string|null Image URL or null
     */
    private static function getUploadedImage(
        string $type,
        string $subPath,
    ): ?string {
        $uploadPath = self::STORAGE_BASE . "/" . $subPath;

        if (!is_dir($uploadPath)) {
            return null;
        }

        $files = glob($uploadPath . "/" . $type . "_*");

        if (empty($files)) {
            return null;
        }

        // Get the most recent file
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $filename = basename($files[0]);
        return self::UPLOAD_URLS[$type] . "/" . $filename;
    }

    /**
     * Get logo path from site_meta.json
     *
     * @return string|null Logo URL or null
     */
    private static function getLogoFromMeta(): ?string
    {
        try {
            $metaFile = BASE_PATH . "/app/db/site_meta.json";

            if (!file_exists($metaFile)) {
                return null;
            }

            $content = file_get_contents($metaFile);
            $data = json_decode($content, true);

            if (!$data || !isset($data["logo"])) {
                return null;
            }

            $logoPath = $data["logo"];

            // Check if it's an absolute URL
            if (preg_match("#^https?://#", $logoPath)) {
                return $logoPath;
            }

            // Check if file exists
            $fullPath = BASE_PATH . "/public" . $logoPath;
            if (file_exists($fullPath)) {
                return $logoPath;
            }

            // Check in theme folder
            $themePath = self::THEME_BASE . "/images/" . basename($logoPath);
            if (file_exists($themePath)) {
                return "/themes/default/assets/images/" . basename($logoPath);
            }

            return null;
        } catch (Exception $e) {
            error_log("Error reading logo from meta: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get favicon path from site_meta.json
     *
     * @return string|null Favicon URL or null
     */
    private static function getFaviconFromMeta(): ?string
    {
        try {
            $metaFile = BASE_PATH . "/app/db/site_meta.json";

            if (!file_exists($metaFile)) {
                return null;
            }

            $content = file_get_contents($metaFile);
            $data = json_decode($content, true);

            if (!$data || !isset($data["favicon"])) {
                return null;
            }

            $faviconPath = $data["favicon"];

            // Check if it's an absolute URL
            if (preg_match("#^https?://#", $faviconPath)) {
                return $faviconPath;
            }

            // Check if file exists
            $fullPath = BASE_PATH . "/public" . $faviconPath;
            if (file_exists($fullPath)) {
                return $faviconPath;
            }

            // Check in theme folder
            $themePath = self::THEME_BASE . "/images/" . basename($faviconPath);
            if (file_exists($themePath)) {
                return "/themes/default/assets/images/" .
                    basename($faviconPath);
            }

            return null;
        } catch (Exception $e) {
            error_log("Error reading favicon from meta: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get image with fallback chain
     * Tries: uploaded → meta config → theme default
     *
     * @param string $type Image type
     * @param int|null $userId User ID for profile images
     * @return string Image URL
     */
    public static function getImage(string $type, ?int $userId = null): string
    {
        switch ($type) {
            case "logo":
                return self::getLogo();
            case "favicon":
                return self::getFavicon();
            case "banner":
                return self::getBanner();
            case "profile":
                if ($userId) {
                    return self::getUserProfile($userId);
                }
                return self::DEFAULT_IMAGES["profile"];
            default:
                return self::DEFAULT_IMAGES["profile"];
        }
    }

    /**
     * Get full URL for image
     * Converts relative path to full URL with app base
     *
     * @param string $path Image path
     * @return string Full URL
     */
    public static function getFullUrl(string $path): string
    {
        // If already a full URL, return as is
        if (preg_match("#^https?://#", $path)) {
            return $path;
        }

        // Use app_base_url helper if available
        if (function_exists("app_base_url")) {
            return app_base_url($path);
        }

        // Build URL manually
        $base = "";
        if (defined("APP_BASE")) {
            $base = rtrim(APP_BASE, "/");
        }

        return $base . "/" . ltrim($path, "/");
    }

    /**
     * Check if image exists
     *
     * @param string $path Image path (relative)
     * @return bool True if exists
     */
    public static function imageExists(string $path): bool
    {
        // Remove leading slash and app base
        $cleanPath = ltrim($path, "/");

        if (defined("APP_BASE") && !empty(APP_BASE)) {
            $base = trim(APP_BASE, "/");
            if (strpos($cleanPath, $base) === 0) {
                $cleanPath = substr($cleanPath, strlen($base));
                $cleanPath = ltrim($cleanPath, "/");
            }
        }

        $fullPath = BASE_PATH . "/public/" . $cleanPath;

        if (file_exists($fullPath)) {
            return true;
        }

        // Try storage path
        $storagePath = BASE_PATH . "/" . $cleanPath;
        if (file_exists($storagePath)) {
            return true;
        }

        return false;
    }

    /**
     * Get all admin images
     * Returns array of all admin images (logo, favicon, banner)
     *
     * @return array Associative array of image URLs
     */
    public static function getAllAdminImages(): array
    {
        return [
            "logo" => self::getLogo(),
            "favicon" => self::getFavicon(),
            "banner" => self::getBanner(),
        ];
    }

    /**
     * Get image info
     * Returns detailed information about an image
     *
     * @param string $type Image type
     * @param int|null $userId User ID for profile images
     * @return array Image information
     */
    public static function getImageInfo(
        string $type,
        ?int $userId = null,
    ): array {
        $url = self::getImage($type, $userId);
        $isDefault = self::isDefaultImage($url, $type);

        return [
            "type" => $type,
            "url" => $url,
            "full_url" => self::getFullUrl($url),
            "is_default" => $isDefault,
            "exists" => self::imageExists($url),
        ];
    }

    /**
     * Check if image is a default image
     *
     * @param string $url Image URL
     * @param string $type Image type
     * @return bool True if default
     */
    private static function isDefaultImage(string $url, string $type): bool
    {
        $defaultUrl = self::DEFAULT_IMAGES[$type] ?? "";
        return $url === $defaultUrl;
    }

    /**
     * Clear cache
     * Useful when images are updated
     *
     * @param string|null $type Specific type to clear, or null for all
     * @return void
     */
    public static function clearCache(?string $type = null): void
    {
        if ($type === null) {
            self::$cache = [];
        } else {
            unset(self::$cache[$type]);

            // Clear user profile caches if type is profile
            if ($type === "profile") {
                foreach (array_keys(self::$cache) as $key) {
                    if (strpos($key, "profile_") === 0) {
                        unset(self::$cache[$key]);
                    }
                }
            }
        }
    }

    /**
     * Refresh all caches
     * Forces re-read of all images
     *
     * @return void
     */
    public static function refreshAll(): void
    {
        self::clearCache();
        self::getLogo(true);
        self::getFavicon(true);
        self::getBanner(true);
    }

    /**
     * Get default image path
     *
     * @param string $type Image type
     * @return string Default image path
     */
    public static function getDefaultImagePath(string $type): string
    {
        return self::DEFAULT_IMAGES[$type] ?? self::DEFAULT_IMAGES["profile"];
    }

    /**
     * Serve image file
     * Outputs image file with proper headers
     *
     * @param string $path Image path
     * @return void
     */
    public static function serveImage(string $path): void
    {
        $fullPath = BASE_PATH . "/public" . $path;

        if (!file_exists($fullPath)) {
            $fullPath = BASE_PATH . $path;
        }

        if (!file_exists($fullPath)) {
            http_response_code(404);
            exit();
        }

        $mimeType = mime_content_type($fullPath);
        $fileSize = filesize($fullPath);

        header("Content-Type: " . $mimeType);
        header("Content-Length: " . $fileSize);
        header("Cache-Control: public, max-age=31536000");

        readfile($fullPath);
        exit();
    }
}
