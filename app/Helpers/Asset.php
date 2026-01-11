<?php

namespace App\Helpers;

/**
 * Asset Helper
 * 
 * Handles asset URL generation, including CDN support and versioning.
 */
class Asset
{
    /**
     * Get the URL for an asset
     * 
     * @param string $path Path relative to public directory (e.g. 'css/style.css')
     * @return string Full URL to the asset
     */
    public static function url($path)
    {
        // Remove leading slash
        $path = ltrim($path, '/');
        
        // Check for CDN configuration
        $cdnUrl = $_ENV['CDN_URL'] ?? null;
        
        // Add versioning timestamp for cache busting
        $version = '';
        
        // Normalize path for file check: if it starts with 'public/', looks in public dir
        // otherwise, assume it is relative to public dir
        $checkPath = $path;
        if (strpos($path, 'public/') === 0) {
             // If path is 'public/assets/css...', we want to check BASE_PATH . '/public/assets/css...'
             // So we strip 'public/' because we append it below? 
             // Actually, let's just make it absolute.
             $realPath = BASE_PATH . '/' . $path;
        } else {
             $realPath = BASE_PATH . '/public/' . $path;
        }

        if (file_exists($realPath)) {
            $version = '?v=' . filemtime($realPath);
        }
        
        if ($cdnUrl) {
            return rtrim($cdnUrl, '/') . '/' . $path . $version;
        }
        
        // Fallback to local
        return app_base_url('/' . $path . $version);
    }
    
    /**
     * Get image URL (with optional optimization params if using an image service)
     */
    public static function image($path, $width = null, $height = null)
    {
        $url = self::url($path);
        
        // Example: If using Cloudinary or similar
        // if ($provider === 'cloudinary') { ... }
        
        return $url;
    }
}
