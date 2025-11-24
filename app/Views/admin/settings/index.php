<?php

/**
 * Admin Settings - Comprehensive Settings Management Interface
 * Modern, premium, fully responsive and modular design
 */

use App\Services\SettingsService;

$currentUser = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Settings Management' ?> - Admin Panel</title>

    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/admin/settings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="settings-container">
        <!-- Header -->
        <div class="settings-header">
            <div>
                <h1><i class="fas fa-cog"></i> Settings Management</h1>
                <p style="color: var(--admin-text-muted); margin-top: 0.5rem;">
                    Configure all aspects of your application from this centralized dashboard
                </p>
            </div>
            <div class="settings-header-actions">
                <button class="btn btn-secondary" onclick="window.settingsManager.resetSettings()">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button class="btn btn-primary" id="save-settings">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="settings-tabs">
            <button class="settings-tab active" data-group="general">
                <i class="fas fa-home"></i> General
            </button>
            <button class="settings-tab" data-group="appearance">
                <i class="fas fa-palette"></i> Appearance
            </button>
            <button class="settings-tab" data-group="email">
                <i class="fas fa-envelope"></i> Email
            </button>
            <button class="settings-tab" data-group="security">
                <i class="fas fa-shield-alt"></i> Security
            </button>
            <button class="settings-tab" data-group="privacy">
                <i class="fas fa-user-shield"></i> Privacy & GDPR
            </button>
            <button class="settings-tab" data-group="performance">
                <i class="fas fa-tachometer-alt"></i> Performance
            </button>
            <button class="settings-tab" data-group="system">
                <i class="fas fa-server"></i> System
            </button>
            <button class="settings-tab" data-group="api">
                <i class="fas fa-code"></i> API
            </button>
        </div>

        <!-- Settings Form -->
        <form id="settings-form" method="POST" action="<?php echo app_base_url('/admin/settings/save'); ?>" enctype="multipart/form-data">
            <div class="settings-content">

                <!-- General Settings -->
                <div id="general-settings" class="settings-section active">
                    <?php echo $this->renderSettingsGroup('general', $settingsByGroup['general'] ?? [], [
                        'site_identity' => [
                            'title' => 'Site Identity',
                            'icon' => 'fa-id-card',
                            'description' => 'Basic information about your website'
                        ],
                        'regional' => [
                            'title' => 'Regional Settings',
                            'icon' => 'fa-globe',
                            'description' => 'Timezone, language, and format preferences'
                        ],
                        'display' => [
                            'title' => 'Display Options',
                            'icon' => 'fa-desktop',
                            'description' => 'Control how content is displayed'
                        ]
                    ]); ?>
                </div>

                <!-- Appearance Settings -->
                <div id="appearance-settings" class="settings-section">
                    <?php echo $this->renderSettingsGroup('appearance', $settingsByGroup['appearance'] ?? [], [
                        'branding' => [
                            'title' => 'Branding',
                            'icon' => 'fa-image',
                            'description' => 'Logo, favicon, and visual identity'
                        ],
                        'colors' => [
                            'title' => 'Color Scheme',
                            'icon' => 'fa-palette',
                            'description' => 'Customize your brand colors'
                        ],
                        'typography' => [
                            'title' => 'Typography',
                            'icon' => 'fa-font',
                            'description' => 'Font settings and text styles'
                        ],
                        'layout' => [
                            'title' => 'Layout',
                            'icon' => 'fa-th-large',
                            'description' => 'Page structure and container settings'
                        ],
                        'theme' => [
                            'title' => 'Theme',
                            'icon' => 'fa-brush',
                            'description' => 'Theme selection and mode settings'
                        ],
                        'advanced' => [
                            'title' => 'Advanced Customization',
                            'icon' => 'fa-code',
                            'description' => 'Custom CSS and JavaScript'
                        ]
                    ]); ?>
                </div>

                <!-- Email Settings -->
                <div id="email-settings" class="settings-section">
                    <?php echo $this->renderSettingsGroup('email', $settingsByGroup['email'] ?? [], [
                        'smtp' => [
                            'title' => 'SMTP Configuration',
                            'icon' => 'fa-server',
                            'description' => 'Email server settings'
                        ],
                        'sender' => [
                            'title' => 'Sender Information',
                            'icon' => 'fa-user',
                            'description' => 'Default sender name and email'
                        ],
                        'templates' => [
                            'title' => 'Email Templates',
                            'icon' => 'fa-file-alt',
                            'description' => 'Customize email content'
                        ]
                    ]); ?>
                </div>

                <!-- Security Settings -->
                <div id="security-settings" class="settings-section">
                    <?php echo $this->renderSettingsGroup('security', $settingsByGroup['security'] ?? [], [
                        'authentication' => [
                            'title' => 'Authentication',
                            'icon' => 'fa-key',
                            'description' => 'Login and registration settings'
                        ],
                        'passwords' => [
                            'title' => 'Password Policy',
                            'icon' => 'fa-lock',
                            'description' => 'Password strength requirements'
                        ],
                        'sessions' => [
                            'title' => 'Session Management',
                            'icon' => 'fa-clock',
                            'description' => 'Session timeout and security'
                        ],
                        'spam_protection' => [
                            'title' => 'Spam Protection',
                            'icon' => 'fa-shield-alt',
                            'description' => 'CAPTCHA and anti-spam settings'
                        ]
                    ]); ?>
                </div>

                <!-- Privacy & GDPR Settings -->
                <div id="privacy-settings" class="settings-section">
                    <?php echo $this->renderSettingsGroup('privacy', $settingsByGroup['privacy'] ?? [], [
                        'gdpr' => [
                            'title' => 'GDPR Compliance',
                            'icon' => 'fa-gavel',
                            'description' => 'Cookie consent and data management'
                        ],
                        'legal' => [
                            'title' => 'Legal Pages',
                            'icon' => 'fa-file-contract',
                            'description' => 'Privacy policy and terms of service'
                        ],
                        'tracking' => [
                            'title' => 'Analytics & Tracking',
                            'icon' => 'fa-chart-line',
                            'description' => 'Website analytics configuration'
                        ]
                    ]); ?>
                </div>

                <!-- Performance Settings -->
                <div id="performance-settings" class="settings-section">
                    <?php echo $this->renderSettingsGroup('performance', $settingsByGroup['performance'] ?? [], [
                        'caching' => [
                            'title' => 'Caching',
                            'icon' => 'fa-bolt',
                            'description' => 'Cache settings and drivers'
                        ],
                        'optimization' => [
                            'title' => 'Optimization',
                            'icon' => 'fa-rocket',
                            'description' => 'Performance optimization features'
                        ]
                    ]); ?>
                </div>

                <!-- System Settings -->
                <div id="system-settings" class="settings-section">
                    <?php echo $this->renderSettingsGroup('system', $settingsByGroup['system'] ?? [], [
                        'status' => [
                            'title' => 'System Status',
                            'icon' => 'fa-heartbeat',
                            'description' => 'Maintenance mode and system health'
                        ],
                        'development' => [
                            'title' => 'Development',
                            'icon' => 'fa-bug',
                            'description' => 'Debug and development settings'
                        ],
                        'logging' => [
                            'title' => 'Logging',
                            'icon' => 'fa-file-alt',
                            'description' => 'Error logging configuration'
                        ],
                        'backup' => [
                            'title' => 'Backup',
                            'icon' => 'fa-database',
                            'description' => 'Automated backup settings'
                        ]
                    ]); ?>
                </div>

                <!-- API Settings -->
                <div id="api-settings" class="settings-section">
                    <?php echo $this->renderSettingsGroup('api', $settingsByGroup['api'] ?? [], [
                        'general' => [
                            'title' => 'API Configuration',
                            'icon' => 'fa-plug',
                            'description' => 'Enable or disable API access'
                        ],
                        'limits' => [
                            'title' => 'Rate Limiting',
                            'icon' => 'fa-tachometer-alt',
                            'description' => 'API rate limit settings'
                        ],
                        'security' => [
                            'title' => 'API Security',
                            'icon' => 'fa-key',
                            'description' => 'API authentication and security'
                        ]
                    ]); ?>
                </div>

            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="/assets/js/admin/settings-manager.js"></script>
</body>

</html>

<?php
/**
 * Helper function to render settings groups
 */
function renderSettingsGroup($group, $settings, $categories)
{
    if (empty($settings)) {
        return '<div class="settings-group">
            <div class="settings-group-header">
                <div class="settings-group-icon"><i class="fas fa-info-circle"></i></div>
                <div class="settings-group-title">
                    <h3>No Settings Available</h3>
                    <p>No settings found for this group.</p>
                </div>
            </div>
        </div>';
    }

    // Group settings by category
    $settingsByCategory = [];
    foreach ($settings as $setting) {
        $category = $setting['setting_category'] ?? 'general';
        if (!isset($settingsByCategory[$category])) {
            $settingsByCategory[$category] = [];
        }
        $settingsByCategory[$category][] = $setting;
    }

    $output = '';

    foreach ($settingsByCategory as $categoryKey => $categorySettings) {
        $categoryInfo = $categories[$categoryKey] ?? [
            'title' => ucfirst(str_replace('_', ' ', $categoryKey)),
            'icon' => 'fa-cog',
            'description' => ''
        ];

        $output .= '<div class="settings-group">';
        $output .= '<div class="settings-group-header">';
        $output .= '<div class="settings-group-icon"><i class="fas ' . $categoryInfo['icon'] . '"></i></div>';
        $output .= '<div class="settings-group-title">';
        $output .= '<h3>' . htmlspecialchars($categoryInfo['title']) . '</h3>';
        $output .= '<p>' . htmlspecialchars($categoryInfo['description']) . '</p>';
        $output .= '</div>';
        $output .= '</div>';

        $output .= '<div class="form-row">';

        foreach ($categorySettings as $setting) {
            $output .= renderSettingField($setting);
        }

        $output .= '</div>';
        $output .= '</div>';
    }

    return $output;
}

/**
 * Render individual setting field based on type
 */
function renderSettingField($setting)
{
    $key = $setting['setting_key'];
    $value = $setting['setting_value'];
    $type = $setting['setting_type'];
    $description = $setting['description'] ?? '';
    $label = ucfirst(str_replace('_', ' ', $key));

    $output = '<div class="form-group">';
    $output .= '<label class="form-label">' . htmlspecialchars($label);

    if ($description) {
        $output .= '<span class="form-label-description">' . htmlspecialchars($description) . '</span>';
    }

    $output .= '</label>';

    switch ($type) {
        case 'boolean':
            $checked = $value == '1' ? 'checked' : '';
            $output .= '<label class="toggle-switch">';
            $output .= '<input type="checkbox" name="' . $key . '" value="1" ' . $checked . '>';
            $output .= '<span class="toggle-slider"></span>';
            $output .= '</label>';
            break;

        case 'color':
            $output .= '<div class="color-input-group">';
            $output .= '<input type="color" name="' . $key . '" value="' . htmlspecialchars($value) . '" class="form-control">';
            $output .= '<input type="text" value="' . htmlspecialchars($value) . '" class="form-control" readonly>';
            $output .= '</div>';
            break;

        case 'textarea':
        case 'text':
            $output .= '<textarea name="' . $key . '" class="form-control" rows="4">' . htmlspecialchars($value) . '</textarea>';
            break;

        case 'image':
        case 'file':
            $output .= '<div class="image-upload-wrapper">';
            if ($value && file_exists($_SERVER['DOCUMENT_ROOT'] . $value)) {
                $output .= '<img src="' . htmlspecialchars($value) . '" alt="Preview" class="image-preview">';
            }
            $output .= '<label class="image-upload-button">';
            $output .= '<i class="fas fa-upload"></i> Choose File';
            $output .= '<input type="file" name="' . $key . '" accept="image/*" style="display: none;">';
            $output .= '</label>';
            $output .= '</div>';
            break;

        case 'select':
            $validationRules = json_decode($setting['validation_rules'] ?? '{}', true);
            $options = $validationRules['options'] ?? [];
            $output .= '<select name="' . $key . '" class="form-control">';
            foreach ($options as $option) {
                $selected = $value == $option ? 'selected' : '';
                $output .= '<option value="' . htmlspecialchars($option) . '" ' . $selected . '>' . htmlspecialchars($option) . '</option>';
            }
            $output .= '</select>';
            break;

        case 'integer':
        case 'float':
            $output .= '<input type="number" name="' . $key . '" value="' . htmlspecialchars($value) . '" class="form-control">';
            break;

        case 'email':
            $output .= '<input type="email" name="' . $key . '" value="' . htmlspecialchars($value) . '" class="form-control">';
            break;

        case 'url':
            $output .= '<input type="url" name="' . $key . '" value="' . htmlspecialchars($value) . '" class="form-control">';
            break;

        default:
            $output .= '<input type="text" name="' . $key . '" value="' . htmlspecialchars($value) . '" class="form-control">';
            break;
    }

    $output .= '</div>';

    return $output;
}
?>