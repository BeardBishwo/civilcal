<?php
/**
 * Premium Theme Functions
 * 
 * Theme-specific functions and hooks for the Premium Calculator Theme
 * 
 * @package PremiumTheme
 * @version 1.0.0
 * @author Bishwo Team
 */

// Prevent direct access
if (!defined('PREMIUM_THEME_ACCESS')) {
    exit('Direct access not allowed');
}

// Set theme constants
define('PREMIUM_THEME_VERSION', '1.0.0');
define('PREMIUM_THEME_PATH', __DIR__);
define('PREMIUM_THEME_URL', '/themes/premium');

/**
 * Theme initialization hook
 */
function premium_theme_init()
{
    // Load theme configuration
    $config = require PREMIUM_THEME_PATH . '/config.php';
    
    // Initialize theme settings
    premium_theme_init_settings();
    
    // Register theme hooks
    premium_theme_register_hooks();
    
    // Apply theme customizations
    premium_theme_apply_customizations();
}

/**
 * Initialize theme settings
 */
function premium_theme_init_settings()
{
    // Get theme settings from database or config
    $settings = $_SESSION['premium_theme_settings'] ?? [];
    
    // Merge with default settings
    $default_settings = [
        'dark_mode_enabled' => false,
        'animation_speed' => 'medium',
        'calculator_skin' => 'premium-dark',
        'custom_css' => '',
        'show_animations' => true,
        'typography_style' => 'modern',
        'enable_premium_features' => true
    ];
    
    $settings = array_merge($default_settings, $settings);
    
    // Update theme settings in session
    $_SESSION['premium_theme_settings'] = $settings;
    
    return $settings;
}

/**
 * Register theme hooks
 */
function premium_theme_register_hooks()
{
    // Enqueue theme assets on init
    add_action('theme_enqueue_assets', 'premium_theme_enqueue_assets');
    add_action('theme_admin_enqueue_assets', 'premium_theme_admin_enqueue_assets');
    
    // Theme customizations
    add_action('theme_head', 'premium_theme_custom_css');
    add_action('theme_footer', 'premium_theme_custom_js');
}

/**
 * Apply theme customizations
 */
function premium_theme_apply_customizations()
{
    $settings = $_SESSION['premium_theme_settings'] ?? [];
    
    // Store settings globally for theme access
    $GLOBALS['premium_theme_settings'] = $settings;
    
    // Apply custom styles to head
    add_action('theme_head', 'premium_theme_inline_styles');
}

/**
 * Enqueue theme assets
 */
function premium_theme_enqueue_assets()
{
    $config = require PREMIUM_THEME_PATH . '/config.php';
    $assets = [];
    
    // Add CSS files
    foreach ($config['assets']['css'] as $css_file) {
        $assets['css'][] = PREMIUM_THEME_URL . '/assets/css/' . $css_file;
    }
    
    // Add JavaScript files
    foreach ($config['assets']['js'] as $js_file) {
        $assets['js'][] = PREMIUM_THEME_URL . '/assets/js/' . $js_file;
    }
    
    return $assets;
}

/**
 * Enqueue admin assets
 */
function premium_theme_admin_enqueue_assets()
{
    return [
        'css' => [
            PREMIUM_THEME_URL . '/assets/css/premium-admin.css'
        ],
        'js' => [
            PREMIUM_THEME_URL . '/assets/js/premium-admin.js'
        ]
    ];
}

/**
 * Custom CSS output
 */
function premium_theme_custom_css()
{
    $settings = $_SESSION['premium_theme_settings'] ?? [];
    
    if (!empty($settings['custom_css'])) {
        return '<style type="text/css">' . $settings['custom_css'] . '</style>';
    }
    
    return '';
}

/**
 * Custom JavaScript output
 */
function premium_theme_custom_js()
{
    $settings = $_SESSION['premium_theme_settings'] ?? [];
    
    if ($settings['show_animations'] ?? true) {
        return '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                premiumThemeInit();
            });
        </script>';
    }
    
    return '';
}

/**
 * Inline styles
 */
function premium_theme_inline_styles()
{
    $settings = $_SESSION['premium_theme_settings'] ?? [];
    $config = require PREMIUM_THEME_PATH . '/config.php';
    
    $color_scheme = $config['color_schemes'][$settings['calculator_skin'] ?? 'premium-dark'];
    $animation_speed = $settings['animation_speed'] ?? 'medium';
    $duration = $config['animations']['duration'][$animation_speed] ?? 300;
    
    echo '<style type="text/css">
        :root {
            --premium-primary: ' . $color_scheme['primary'] . ';
            --premium-secondary: ' . $color_scheme['secondary'] . ';
            --premium-accent: ' . $color_scheme['accent'] . ';
            --premium-background: ' . $color_scheme['background'] . ';
            --premium-surface: ' . $color_scheme['surface'] . ';
            --premium-text: ' . $color_scheme['text'] . ';
            --premium-text-secondary: ' . $color_scheme['text_secondary'] . ';
            --premium-border: ' . $color_scheme['border'] . ';
            --premium-animation-duration: ' . $duration . 'ms;
        }
    </style>';
}

/**
 * Get current theme settings
 */
function premium_get_theme_settings()
{
    return $_SESSION['premium_theme_settings'] ?? [];
}

/**
 * Update theme settings
 */
function premium_update_theme_settings($settings)
{
    $current = $_SESSION['premium_theme_settings'] ?? [];
    $_SESSION['premium_theme_settings'] = array_merge($current, $settings);
    return true;
}

/**
 * Filter calculator output for premium effects
 */
function premium_calculator_render($output, $calculation)
{
    $settings = $_SESSION['premium_theme_settings'] ?? [];
    
    if ($settings['show_animations'] ?? true) {
        $output = '<div class="premium-result-container animated-result" data-animation="fadeInUp">' . $output . '</div>';
    }
    
    if ($settings['dark_mode_enabled'] ?? false) {
        $output = '<div class="premium-result-container dark-mode-result">' . $output . '</div>';
    }
    
    return $output;
}

// Global hook system for theme events
$GLOBALS['premium_theme_hooks'] = [
    'init' => [],
    'enqueue_assets' => [],
    'render_calculator' => []
];

function premium_add_hook($hook_name, $callback)
{
    if (isset($GLOBALS['premium_theme_hooks'][$hook_name])) {
        $GLOBALS['premium_theme_hooks'][$hook_name][] = $callback;
    }
}

function premium_do_hook($hook_name, $args = [])
{
    if (isset($GLOBALS['premium_theme_hooks'][$hook_name])) {
        foreach ($GLOBALS['premium_theme_hooks'][$hook_name] as $callback) {
            if (is_callable($callback)) {
                call_user_func_array($callback, $args);
            }
        }
    }
}

// Initialize theme
premium_theme_init();

// Register hooks
premium_add_hook('init', 'premium_theme_init');
premium_add_hook('enqueue_assets', 'premium_theme_enqueue_assets');
premium_add_hook('render_calculator', 'premium_calculator_render');
