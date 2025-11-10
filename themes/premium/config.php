<?php
/**
 * Premium Theme Configuration
 * 
 * Advanced configuration for the Premium Calculator Theme
 * 
 * @package PremiumTheme
 * @version 1.0.0
 * @author Bishwo Team
 */

// Prevent direct access
if (!defined('PREMIUM_THEME_ACCESS')) {
    exit('Direct access not allowed');
}

return [
    // Theme Identity
    'name' => 'Premium Calculator Theme',
    'version' => '1.0.0',
    'author' => 'Bishwo Team',
    
    // Theme Settings
    'settings' => [
        'dark_mode_enabled' => false,
        'animation_speed' => 'medium',
        'calculator_skin' => 'premium-dark',
        'custom_css' => '',
        'show_animations' => true,
        'typography_style' => 'modern',
        'enable_premium_features' => true,
        'custom_header_text' => '',
        'footer_text' => '© 2025 Bishwo Calculator. All rights reserved.',
        'enable_live_chat' => false,
        'custom_color_scheme' => '',
    ],
    
    // Color Schemes
    'color_schemes' => [
        'premium-dark' => [
            'primary' => '#2c3e50',
            'secondary' => '#34495e',
            'accent' => '#e74c3c',
            'background' => '#1a252f',
            'surface' => '#2c3e50',
            'text' => '#ecf0f1',
            'text_secondary' => '#bdc3c7',
            'border' => '#34495e',
            'success' => '#27ae60',
            'warning' => '#f39c12',
            'error' => '#e74c3c',
        ],
        'premium-light' => [
            'primary' => '#3498db',
            'secondary' => '#2980b9',
            'accent' => '#e74c3c',
            'background' => '#ffffff',
            'surface' => '#f8f9fa',
            'text' => '#2c3e50',
            'text_secondary' => '#7f8c8d',
            'border' => '#e9ecef',
            'success' => '#27ae60',
            'warning' => '#f39c12',
            'error' => '#e74c3c',
        ],
        'professional-blue' => [
            'primary' => '#1e3a8a',
            'secondary' => '#3b82f6',
            'accent' => '#10b981',
            'background' => '#f8fafc',
            'surface' => '#ffffff',
            'text' => '#1f2937',
            'text_secondary' => '#6b7280',
            'border' => '#e5e7eb',
            'success' => '#059669',
            'warning' => '#d97706',
            'error' => '#dc2626',
        ],
        'modern-gray' => [
            'primary' => '#374151',
            'secondary' => '#6b7280',
            'accent' => '#8b5cf6',
            'background' => '#f9fafb',
            'surface' => '#ffffff',
            'text' => '#111827',
            'text_secondary' => '#6b7280',
            'border' => '#e5e7eb',
            'success' => '#059669',
            'warning' => '#d97706',
            'error' => '#dc2626',
        ]
    ],
    
    // Typography Settings
    'typography' => [
        'fonts' => [
            'primary' => 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
            'secondary' => 'Poppins, Inter, sans-serif',
            'monospace' => '"Fira Code", "SF Mono", Monaco, "Cascadia Code", monospace'
        ],
        'sizes' => [
            'xs' => '0.75rem',
            'sm' => '0.875rem',
            'base' => '1rem',
            'lg' => '1.125rem',
            'xl' => '1.25rem',
            '2xl' => '1.5rem',
            '3xl' => '1.875rem',
            '4xl' => '2.25rem'
        ],
        'weights' => [
            'light' => 300,
            'normal' => 400,
            'medium' => 500,
            'semibold' => 600,
            'bold' => 700,
            'extrabold' => 800
        ]
    ],
    
    // Animation Settings
    'animations' => [
        'duration' => [
            'fast' => 150,
            'normal' => 300,
            'slow' => 500
        ],
        'easing' => [
            'ease' => 'ease',
            'ease-in' => 'ease-in',
            'ease-out' => 'ease-out',
            'ease-in-out' => 'ease-in-out',
            'bounce' => 'cubic-bezier(0.68, -0.55, 0.265, 1.55)'
        ],
        'effects' => [
            'slide_in' => 'slideInAnimation',
            'fade_in' => 'fadeInAnimation',
            'bounce' => 'bounceAnimation',
            'pulse' => 'pulseAnimation',
            'shimmer' => 'shimmerAnimation'
        ]
    ],
    
    // Component Configuration
    'components' => [
        'header' => [
            'height' => '70px',
            'background' => 'var(--surface)',
            'border_bottom' => '1px solid var(--border)',
            'sticky' => true
        ],
        'footer' => [
            'background' => 'var(--primary)',
            'color' => 'var(--text)',
            'padding' => '2rem 0'
        ],
        'calculator' => [
            'max_width' => '800px',
            'border_radius' => '12px',
            'box_shadow' => '0 10px 25px rgba(0,0,0,0.1)',
            'background' => 'var(--surface)',
            'animation_duration' => 300
        ],
        'button' => [
            'border_radius' => '8px',
            'padding' => '0.75rem 1.5rem',
            'font_weight' => 500,
            'transition' => 'all 0.3s ease'
        ]
    ],
    
    // Premium Features
    'premium_features' => [
        'dark_mode' => true,
        'animations' => true,
        'custom_skins' => true,
        'advanced_typography' => true,
        'theme_customization' => true,
        'premium_support' => true,
        'regular_updates' => true,
        'live_chat' => false
    ],
    
    // Custom Hooks
    'hooks' => [
        'init' => 'premium_theme_init',
        'header' => 'premium_theme_header',
        'footer' => 'premium_theme_footer',
        'calculator_before' => 'premium_before_calculator',
        'calculator_after' => 'premium_after_calculator',
        'enqueue_assets' => 'premium_enqueue_assets'
    ],
    
    // Assets
    'assets' => [
        'css' => [
            'premium-theme.css',
            'premium-calculator.css',
            'premium-animations.css',
            'premium-responsive.css'
        ],
        'js' => [
            'premium-theme.js',
            'premium-animations.js',
            'dark-mode-toggle.js',
            'theme-customizer.js'
        ]
    ]
];
