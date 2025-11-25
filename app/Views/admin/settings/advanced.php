<?php
/**
 * Advanced Settings Page
 * Fixed version that works with current Bishwo Calculator setup
 */

// Security check
if (!defined('ABSPATH')) {
    exit('Access denied');
}

// Initialize container if not available
if (!isset($container)) {
    $container = \App\Core\Container::create();
}

// Get current advanced settings (mock data for demonstration)
$advanced_settings = [
    'custom_css' => [
        'label' => 'Custom CSS',
        'value' => '/* Add your custom CSS here */\n\n.admin-layout {\n    /* Custom admin layout styles */\n}\n\n.calculator-widget {\n    /* Custom calculator widget styles */\n}',
        'type' => 'textarea',
        'required' => false,
        'description' => 'Custom CSS to override default styles'
    ],
    'custom_js' => [
        'label' => 'Custom JavaScript',
        'value' => '// Add your custom JavaScript here\n\n$(document).ready(function() {\n    // Custom initialization code\n    console.log("Bishwo Calculator Custom JS Loaded");\n});\n\n// Custom calculator functions\nfunction customCalculation() {\n    // Add custom calculation logic\n}',
        'type' => 'textarea',
        'required' => false,
        'description' => 'Custom JavaScript for additional functionality'
    ],
    'google_analytics_id' => [
        'label' => 'Google Analytics ID',
        'value' => '',
        'type' => 'text',
        'required' => false,
        'description' => 'Google Analytics tracking ID (UA-XXXXX-X or G-XXXXX)'
    ],
    'custom_meta_title' => [
        'label' => 'Custom Meta Title',
        'value' => '',
        'type' => 'text',
        'required' => false,
        'description' => 'Override default page title for SEO'
    ],
    'custom_meta_description' => [
        'label' => 'Custom Meta Description',
        'value' => '',
        'type' => 'textarea',
        'required' => false,
        'description' => 'Custom meta description for SEO (150-160 characters)'
    ],
    'maintenance_mode_message' => [
        'label' => 'Maintenance Mode Message',
        'value' => 'We are currently performing scheduled maintenance. Please check back soon.',
        'type' => 'textarea',
        'required' => false,
        'description' => 'Custom message displayed during maintenance mode'
    ],
    'backup_retention_days' => [
        'label' => 'Backup Retention (days)',
        'value' => '30',
        'type' => 'number',
        'required' => true,
        'description' => 'How many days to keep backup files',
        'min' => '1',
        'max' => '3650'
    ],
    'log_retention_days' => [
        'label' => 'Log Retention (days)',
        'value' => '90',
        'type' => 'number',
        'required' => true,
        'description' => 'How many days to keep system logs',
        'min' => '1',
        'max' => '3650'
    ],
    'session_cleanup_interval' => [
        'label' => 'Session Cleanup Interval (hours)',
        'value' => '24',
        'type' => 'number',
        'required' => true,
        'description' => 'How often to clean expired sessions',
        'min' => '1',
        'max' => '168'
    ],
    'cache_cleanup_interval' => [
        'label' => 'Cache Cleanup Interval (hours)',
        'value' => '12',
        'type' => 'number',
        'required' => true,
        'description' => 'How often to clean expired cache entries',
        'min' => '1',
        'max' => '168'
    ],
    'enable_debug_toolbar' => [
        'label' => 'Enable Debug Toolbar',
        'value' => false,
        'type' => 'checkbox',
        'required' => false,
        'description' => 'Show debug information in admin interface'
    ],
    'enable_profiler' => [
        'label' => 'Enable Performance Profiler',
        'value' => false,
        'type' => 'checkbox',
        'required' => false,
        'description' => 'Enable detailed performance profiling'
    ],
    'custom_error_pages' => [
        'label' => 'Custom Error Pages',
        'value' => true,
        'type' => 'checkbox',
        'required' => false,
        'description' => 'Use custom error page templates'
    ],
    'enable_cdn' => [
        'label' => 'Enable CDN',
        'value' => false,
        'type' => 'checkbox',
        'required' => false,
        'description' => 'Use CDN for static assets'
    ],
    'cdn_url' => [
        'label' => 'CDN Base URL',
        'value' => '',
        'type' => 'text',
        'required' => false,
        'description' => 'Base URL for CDN assets (e.g., https://cdn.example.com)'
    ],
    'system_health_check' => [
        'label' => 'System Health Check',
        'value' => true,
        'type' => 'checkbox',
        'required' => false,
        'description' => 'Enable automatic system health monitoring'
    ]
];

$current_section = 'advanced';
$page_title = 'Advanced Settings - Admin Panel';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Admin CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        /* Admin Layout */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: 250px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .admin-content {
            flex: 1;
            padding: 2rem;
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        
        /* Sidebar Styles */
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .sidebar-menu ul {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.25rem;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #666;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #667eea;
            color: white;
        }
        
        .menu-divider {
            margin: 1.5rem 0 0.5rem 0;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .page-header p {
            color: rgba(255, 255, 255, 0.8);
            margin-top: 0.5rem;
        }
        
        /* Settings Navigation */
        .settings-nav {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .settings-nav a {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            color: #9ca3af;
            border: 1px solid transparent;
        }
        
        .settings-nav a:hover {
            background: rgba(67, 97, 238, 0.1);
            color: #4cc9f0;
            border: 1px solid rgba(67, 97, 234, 0.3);
        }
        
        .settings-nav a.active {
            background: rgba(67, 97, 238, 0.2);
            color: #4cc9f0;
            border: 1px solid rgba(67, 97, 238, 0.3);
        }
        
        /* Settings Form */
        .settings-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .settings-card h3 {
            color: #333;
            margin: 0 0 1.5rem 0;
            font-size: 1.125rem;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            color: #333;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .form-label.required::after {
            content: ' *';
            color: #ef4444;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            background: rgba(15, 15, 46, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 8px;
            color: #333;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }
        
        .form-select {
            background: rgba(15, 15, 46, 0.1);
            cursor: pointer;
        }
        
        .form-checkbox-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .form-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .form-description {
            display: block;
            color: #666;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            border: none;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #666;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #333;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            
            .admin-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }
            
            .settings-nav {
                flex-direction: column;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-calculator-alt"></i>
                    <span class="sidebar-logo-text"><?php echo htmlspecialchars(\App\Services\SettingsService::get('site_name', 'Admin Panel')); ?></span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <ul>
                    <li>
                        <a href="configured-dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#">
                            <i class="fas fa-calculator"></i>
                            <span>Calculations</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#">
                            <i class="fas fa-cubes"></i>
                            <span>Modules</span>
                        </a>
                    </li>
                    
                    <li class="menu-divider">
                        <span>Configuration</span>
                    </li>
                    
                    <li>
                        <a href="general.php">
                            <i class="fas fa-globe"></i>
                            <span>General</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="application.php">
                            <i class="fas fa-cog"></i>
                            <span>Application</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="users.php">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="security.php">
                            <i class="fas fa-shield-alt"></i>
                            <span>Security</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="email.php">
                            <i class="fas fa-envelope"></i>
                            <span>Email</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="api.php">
                            <i class="fas fa-plug"></i>
                            <span>API</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="performance-dashboard.php">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    
                    <li class="active">
                        <a href="#">
                            <i class="fas fa-tools"></i>
                            <span>Advanced</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1><?= htmlspecialchars($page_title) ?></h1>
                    <p>Configure your site settings and preferences</p>
                </div>
            </div>
            
            <!-- Settings Navigation -->
            <div class="settings-nav">
                <a href="general.php">
                    <i class="fas fa-globe"></i>
                    General
                </a>
                <a href="application.php">
                    <i class="fas fa-cog"></i>
                    Application
                </a>
                <a href="users.php">
                    <i class="fas fa-users"></i>
                    Users
                </a>
                <a href="security.php">
                    <i class="fas fa-shield-alt"></i>
                    Security
                </a>
                <a href="email.php">
                    <i class="fas fa-envelope"></i>
                    Email
                </a>
                <a href="api.php">
                    <i class="fas fa-plug"></i>
                    API
                </a>
                <a href="performance-dashboard.php">
                    <i class="fas fa-chart-line"></i>
                    Performance
                </a>
                <a href="advanced.php" class="active">
                    <i class="fas fa-tools"></i>
                    Advanced
                </a>
            </div>
            
            <!-- Settings Form -->
            <form id="settingsForm" method="POST" action="#">
                <input type="hidden" name="section" value="advanced">
                
                <div class="settings-card">
                    <h3>Advanced Configuration</h3>
                    
                    <?php foreach ($advanced_settings as $key => $setting): ?>
                        <div class="form-group">
                            <label class="form-label<?php echo ($setting['required'] ?? false) ? ' required' : ''; ?>">
                                <?= htmlspecialchars($setting['label'] ?? $key) ?>
                            </label>
                            
                            <?php if (($setting['type'] ?? 'text') === 'textarea'): ?>
                                <textarea name="settings[<?php echo $key; ?>]" 
                                          class="form-input form-textarea"
                                          <?php echo ($setting['required'] ?? false) ? 'required' : ''; ?>><?php echo htmlspecialchars($setting['value'] ?? ''); ?></textarea>
                            <?php elseif (($setting['type'] ?? 'text') === 'select'): ?>
                                <select name="settings[<?php echo $key; ?>]" 
                                        class="form-input form-select"
                                        <?php echo ($setting['required'] ?? false) ? 'required' : ''; ?>>
                                    <?php foreach ($setting['options'] ?? [] as $optKey => $optLabel): ?>
                                        <option value="<?= htmlspecialchars($optKey); ?>" <?php echo ($setting['value'] ?? '') == $optKey ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($optLabel); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif (($setting['type'] ?? 'text') === 'checkbox'): ?>
                                <label class="form-checkbox-container">
                                    <input type="checkbox" 
                                           name="settings[<?php echo $key; ?>]" 
                                           value="1" 
                                           class="form-checkbox"
                                           <?php echo ($setting['value'] ?? false) ? 'checked' : ''; ?>>
                                    <span><?= htmlspecialchars($setting['description'] ?? 'Enable'); ?></span>
                                </label>
                            <?php elseif (($setting['type'] ?? 'text') === 'number'): ?>
                                <input type="number" 
                                       name="settings[<?php echo $key; ?>]" 
                                       value="<?= htmlspecialchars($setting['value'] ?? ''); ?>" 
                                       class="form-input"
                                       <?php echo ($setting['required'] ?? false) ? 'required' : ''; ?>
                                       <?php if (isset($setting['min'])): ?>min="<?= htmlspecialchars($setting['min']); ?>"<?php endif; ?>
                                       <?php if (isset($setting['max'])): ?>max="<?= htmlspecialchars($setting['max']); ?>"<?php endif; ?>>
                            <?php else: ?>
                                <input type="<?= htmlspecialchars($setting['type'] ?? 'text'); ?>" 
                                       name="settings[<?php echo $key; ?>]" 
                                       value="<?= htmlspecialchars($setting['value'] ?? ''); ?>" 
                                       class="form-input"
                                       <?php echo ($setting['required'] ?? false) ? 'required' : ''; ?>>
                            <?php endif; ?>
                            
                            <?php if (isset($setting['description']) && ($setting['type'] ?? 'text') !== 'checkbox'): ?>
                                <small class="form-description"><?= htmlspecialchars($setting['description']); ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                    <button type="button" onclick="document.getElementById('settingsForm').reset();" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Form submission handler
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Advanced settings saved successfully!');
            // In a real implementation, this would submit to the server
        });
        
        // Sidebar navigation (basic functionality)
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.sidebar-menu a');
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    
                    // In a real implementation, this would load the actual page
                    alert('Navigation to: ' + this.textContent.trim());
                });
            });
        });
    </script>
</body>
</html>
