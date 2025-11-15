<?php

/**
 * Image Configuration
 * Defines image types, dimensions, and storage locations
 * All paths are dynamically resolved from theme folder
 */

return [
    // Image types configuration
    'types' => [
        'logo' => [
            'name' => 'Logo',
            'dimensions' => ['width' => 200, 'height' => 50],
            'max_size' => 5242880, // 5MB
            'description' => 'Site logo image',
        ],
        'favicon' => [
            'name' => 'Favicon',
            'dimensions' => ['width' => 32, 'height' => 32],
            'max_size' => 5242880, // 5MB
            'description' => 'Browser tab icon',
        ],
        'banner' => [
            'name' => 'Banner',
            'dimensions' => ['width' => 1920, 'height' => 400],
            'max_size' => 5242880, // 5MB
            'description' => 'Page banner image',
        ],
        'profile' => [
            'name' => 'Profile Picture',
            'dimensions' => ['width' => 200, 'height' => 200],
            'max_size' => 2097152, // 2MB
            'description' => 'User profile picture',
        ],
    ],

    // Storage paths
    'storage' => [
        'uploads' => '/storage/uploads',
        'admin' => '/storage/uploads/admin',
        'users' => '/storage/uploads/users',
        'theme' => '/themes/default/assets/images',
    ],

    // Allowed file extensions
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],

    // Allowed MIME types
    'allowed_mimes' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ],

    // Image optimization settings
    'optimization' => [
        'enabled' => true,
        'quality' => [
            'jpg' => 85,
            'png' => 9,
            'webp' => 85,
        ],
    ],

    // Theme default images (dynamically resolved)
    // These are scanned from themes/default/assets/images/ folder
    'defaults' => [
        // Automatically detected from theme folder
        // No hardcoding - uses glob() to find files
    ],
];
