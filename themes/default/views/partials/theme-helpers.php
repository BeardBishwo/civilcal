<?php
/**
 * Theme Helper Functions
 * Modular functions for theme asset loading and URL generation
 */

// Create ThemeManager instance if not exists
if (!isset($themeManager)) {
    $themeManager = new \App\Services\ThemeManager();
}

/**
 * Get theme asset URL with cache busting
 */
function theme_asset($path) {
    global $themeManager;
    
    if (!$themeManager) {
        return app_base_url($path);
    }
    
    // Remove leading slash
    $path = ltrim($path, '/');
    
    // Get full file path for cache busting
    $basePath = dirname(__DIR__, 4);
    $themePath = $basePath . '/themes/' . $themeManager->getActiveTheme() . '/' . $path;
    
    if (file_exists($themePath)) {
        $mtime = filemtime($themePath);
        return $themeManager->themeUrl($path . '?v=' . $mtime);
    }
    
    return $themeManager->themeUrl($path);
}

/**
 * Get theme CSS URL
 */
function theme_css($filename) {
    return theme_asset('assets/css/' . $filename);
}

/**
 * Get theme JS URL
 */
function theme_js($filename) {
    return theme_asset('assets/js/' . $filename);
}

/**
 * Get theme image URL
 */
function theme_image($filename) {
    return theme_asset('assets/images/' . $filename);
}

/**
 * Load CSS file with link tag
 */
function load_theme_css($filename) {
    $url = theme_css($filename);
    echo '<link rel="stylesheet" href="' . htmlspecialchars($url) . '">' . "\n";
}

/**
 * Load JS file with script tag
 */
function load_theme_js($filename, $defer = false) {
    $url = theme_js($filename);
    $deferAttr = $defer ? ' defer' : '';
    echo '<script src="' . htmlspecialchars($url) . '"' . $deferAttr . '></script>' . "\n";
}

?>

