<?php
/**
 * Installer Configuration
 * Controls installation behavior and auto-deletion settings
 */

return [
    // Auto-delete installer after successful installation
    'auto_delete_installer' => false, // Set to true for production
    
    // Installation security
    'require_install_key' => false,
    'install_key' => 'bishwo-calculator-install',
    
    // Installation timeout (minutes)
    'install_timeout' => 30,
    
    // Demo data installation
    'install_demo_data' => true,
    
    // Default admin settings
    'default_admin' => [
        'username' => 'admin',
        'email' => 'admin@example.com',
        'password' => 'admin123',
        'first_name' => 'Administrator',
        'last_name' => 'User'
    ],
    
    // Installation steps
    'steps' => [
        'welcome' => [
            'title' => 'Welcome',
            'description' => 'Welcome to Bishwo Calculator Installation',
            'icon' => 'fas fa-hand-wave'
        ],
        'requirements' => [
            'title' => 'System Requirements',
            'description' => 'Checking system compatibility',
            'icon' => 'fas fa-server'
        ],
        'permissions' => [
            'title' => 'File Permissions',
            'description' => 'Verifying file and folder permissions',
            'icon' => 'fas fa-lock'
        ],
        'database' => [
            'title' => 'Database Setup',
            'description' => 'Configure database connection',
            'icon' => 'fas fa-database'
        ],
        'admin' => [
            'title' => 'Admin Account',
            'description' => 'Create administrator account',
            'icon' => 'fas fa-user-shield'
        ],
        'settings' => [
            'title' => 'Site Settings',
            'description' => 'Configure basic site settings',
            'icon' => 'fas fa-cogs'
        ],
        'finish' => [
            'title' => 'Complete',
            'description' => 'Installation completed successfully',
            'icon' => 'fas fa-check-circle'
        ]
    ],
    
    // System requirements
    'requirements' => [
        'php' => [
            'name' => 'PHP Version',
            'required' => '7.4.0',
            'recommended' => '8.0.0'
        ],
        'extensions' => [
            'pdo' => ['name' => 'PDO Extension', 'required' => true],
            'pdo_mysql' => ['name' => 'PDO MySQL', 'required' => true],
            'mbstring' => ['name' => 'Mbstring Extension', 'required' => true],
            'openssl' => ['name' => 'OpenSSL Extension', 'required' => true],
            'curl' => ['name' => 'cURL Extension', 'required' => true],
            'gd' => ['name' => 'GD Extension', 'recommended' => true],
            'zip' => ['name' => 'ZIP Extension', 'recommended' => true]
        ],
        'permissions' => [
            'storage' => ['path' => 'storage', 'permission' => 0755],
            'storage/logs' => ['path' => 'storage/logs', 'permission' => 0755],
            'storage/cache' => ['path' => 'storage/cache', 'permission' => 0755],
            'storage/uploads' => ['path' => 'storage/uploads', 'permission' => 0755],
            'config' => ['path' => 'config', 'permission' => 0644],
            '.env' => ['path' => '.env', 'permission' => 0644]
        ]
    ]
];
?>
