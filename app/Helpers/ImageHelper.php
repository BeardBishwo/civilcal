<?php

namespace App\Helpers;

use App\Services\ImageManager;
use App\Services\ThemeImageLoader;
use App\Models\Image;

/**
 * Image Helper Functions
 * Easy access to images in views and templates
 */

/**
 * Get admin image or default
 * 
 * @param string $type Image type (logo, favicon, banner)
 * @return string Image URL
 */
function get_admin_image($type)
{
    return ImageManager::getImageUrl(ImageManager::getAdminImage($type));
}

/**
 * Get user profile image or default
 * 
 * @param int $userId User ID
 * @return string Image URL
 */
function get_user_image($userId)
{
    return ImageManager::getImageUrl(ImageManager::getUserImage($userId));
}

/**
 * Get default image
 * 
 * @param string $type Image type
 * @return string Image URL
 */
function get_default_image($type)
{
    return ImageManager::getImageUrl(ImageManager::getDefaultImage($type));
}

/**
 * Get logo
 * 
 * @return string Logo URL
 */
function get_logo()
{
    return get_admin_image(ImageManager::TYPE_LOGO);
}

/**
 * Get favicon
 * 
 * @return string Favicon URL
 */
function get_favicon()
{
    return get_admin_image(ImageManager::TYPE_FAVICON);
}

/**
 * Get banner
 * 
 * @return string Banner URL
 */
function get_banner()
{
    return get_admin_image(ImageManager::TYPE_BANNER);
}

/**
 * Get current user profile image
 * 
 * @return string Profile image URL
 */
function get_current_user_profile_image()
{
    if (function_exists('Auth') && Auth::check()) {
        $userId = Auth::user()->id ?? null;
        if ($userId) {
            return get_user_image($userId);
        }
    }
    return get_default_image(ImageManager::TYPE_PROFILE);
}

/**
 * Get image HTML tag
 * 
 * @param string $type Image type
 * @param array $attributes HTML attributes
 * @return string HTML img tag
 */
function image_tag($type, $attributes = [])
{
    $url = get_admin_image($type);
    $alt = $attributes['alt'] ?? ucfirst($type);
    $class = $attributes['class'] ?? '';
    $id = $attributes['id'] ?? '';
    
    $html = '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '"';
    if ($class) {
        $html .= ' class="' . htmlspecialchars($class) . '"';
    }
    if ($id) {
        $html .= ' id="' . htmlspecialchars($id) . '"';
    }
    $html .= ' />';
    
    return $html;
}

/**
 * Get profile image HTML tag
 * 
 * @param int $userId User ID
 * @param array $attributes HTML attributes
 * @return string HTML img tag
 */
function profile_image_tag($userId, $attributes = [])
{
    $url = get_user_image($userId);
    $alt = $attributes['alt'] ?? 'User Profile';
    $class = $attributes['class'] ?? '';
    $id = $attributes['id'] ?? '';
    
    $html = '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '"';
    if ($class) {
        $html .= ' class="' . htmlspecialchars($class) . '"';
    }
    if ($id) {
        $html .= ' id="' . htmlspecialchars($id) . '"';
    }
    $html .= ' />';
    
    return $html;
}

/**
 * Initialize image storage
 */
function init_image_storage()
{
    ImageManager::initializeStorage();
}

/**
 * Get theme image URL directly
 * Dynamically loads from theme folder
 * 
 * @param string $type Image type
 * @return string Image URL
 */
function get_theme_image($type)
{
    return ThemeImageLoader::getImageUrl($type);
}

/**
 * Get all available theme images
 * 
 * @return array ['type' => 'url']
 */
function get_all_theme_images()
{
    return ThemeImageLoader::getAllImagesWithUrls();
}

/**
 * Check if theme image exists
 * 
 * @param string $type Image type
 * @return bool
 */
function has_theme_image($type)
{
    return ThemeImageLoader::hasImage($type);
}

/**
 * Get theme image info
 * 
 * @param string $type Image type
 * @return array|null Image information
 */
function get_theme_image_info($type)
{
    return ThemeImageLoader::getImageInfo($type);
}

/**
 * Get all theme images info
 * 
 * @return array Array of image information
 */
function get_all_theme_images_info()
{
    return ThemeImageLoader::getAllImagesInfo();
}
