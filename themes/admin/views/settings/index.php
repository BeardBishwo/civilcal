<?php

/**
 * Admin Settings - Comprehensive Settings Management Interface
 * Modern, premium, fully responsive and modular design
 */

use App\Services\SettingsService;

$currentUser = $_SESSION['user'] ?? null;

// Function to render a settings group with proper styling
function renderSettingsGroup($group, $settings, $sections) {
    $output = '';
    
    foreach ($sections as $sectionKey => $section) {
        $output .= '<div class="settings-section-card">';
        $output .= '<div class="settings-section-header">';
        $output .= '<h3><i class="fas ' . htmlspecialchars($section['icon']) . '"></i> ' . htmlspecialchars($section['title']) . '</h3>';
        $output .= '<p class="text-muted">' . htmlspecialchars($section['description']) . '</p>';
        $output .= '</div>';
        
        $output .= '<div class="settings-section-content">';
        
        // Filter settings for this section
        $sectionSettings = array_filter($settings, function($setting) use ($sectionKey) {
            return isset($setting['section']) && $setting['section'] === $sectionKey;
        });
        
        if (empty($sectionSettings)) {
            $output .= '<div class="alert alert-info">No settings found in this section.</div>';
        } else {
            foreach ($sectionSettings as $key => $setting) {
                $output .= renderSettingField($key, $setting);
            }
        }
        
        $output .= '</div>'; // .settings-section-content
        $output .= '</div>'; // .settings-section-card
    }
    
    return $output;
}

// Function to render individual setting fields with beautiful styling
function renderSettingField($key, $setting) {
    $value = $setting['value'] ?? '';
    $type = $setting['type'] ?? 'text';
    $label = $setting['label'] ?? ucfirst(str_replace('_', ' ', $key));
    $description = $setting['description'] ?? '';
    $options = $setting['options'] ?? [];
    
    $output = '<div class="form-group">';
    $output .= '<label for="' . htmlspecialchars($key) . '" class="form-label">' . htmlspecialchars($label) . '</label>';
    
    if (!empty($description)) {
        $output .= '<small class="form-text text-muted">' . htmlspecialchars($description) . '</small>';
    }
    
    switch ($type) {
        case 'textarea':
            $output .= '<textarea name="' . $key . '" id="' . $key . '" class="form-control" rows="4">' . htmlspecialchars($value) . '</textarea>';
            break;
            
        case 'select':
            $output .= '<select name="' . $key . '" id="' . $key . '" class="form-control">';
            foreach ($options as $optionValue => $optionLabel) {
                $selected = ($value == $optionValue) ? 'selected' : '';
                $output .= '<option value="' . htmlspecialchars($optionValue) . '" ' . $selected . '>' . htmlspecialchars($optionLabel) . '</option>';
            }
            $output .= '</select>';
            break;
            
        case 'checkbox':
            $checked = ($value == '1' || $value == 'true') ? 'checked' : '';
            $output .= '<div class="form-check">';
            $output .= '<input type="checkbox" name="' . $key . '" id="' . $key . '" class="form-check-input" value="1" ' . $checked . '>';
            $output .= '<label class="form-check-label" for="' . $key . '">Enable this feature</label>';
            $output .= '</div>';
            break;
            
        case 'radio':
            foreach ($options as $optionValue => $optionLabel) {
                $checked = ($value == $optionValue) ? 'checked' : '';
                $output .= '<div class="form-check">';
                $output .= '<input type="radio" name="' . $key . '" id="' . $key . '_' . $optionValue . '" class="form-check-input" value="' . htmlspecialchars($optionValue) . '" ' . $checked . '>';
                $output .= '<label class="form-check-label" for="' . $key . '_' . $optionValue . '">' . htmlspecialchars($optionLabel) . '</label>';
                $output .= '</div>';
            }
            break;
            
        case 'integer':
        case 'float':
            $output .= '<input type="number" name="' . $key . '" id="' . $key . '" value="' . htmlspecialchars($value) . '" class="form-control">';
            break;
            
        case 'email':
            $output .= '<input type="email" name="' . $key . '" id="' . $key . '" value="' . htmlspecialchars($value) . '" class="form-control">';
            break;
            
        case 'url':
            $output .= '<input type="url" name="' . $key . '" id="' . $key . '" value="' . htmlspecialchars($value) . '" class="form-control">';
            break;
            
        case 'password':
            $output .= '<input type="password" name="' . $key . '" id="' . $key . '" value="" class="form-control" autocomplete="new-password">';
            $output .= '<small class="form-text text-muted">Leave blank to keep current value</small>';
            break;
            
        default:
            $output .= '<input type="text" name="' . $key . '" id="' . $key . '" value="' . htmlspecialchars($value) . '" class="form-control">';
            break;
    }
    
    $output .= '</div>';
    
    return $output;
}
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cog"></i> Settings Management</h1>
    <p class="page-description">Configure all aspects of your application from this centralized dashboard</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-sliders-h"></i>
            </div>
        </div>
        <div class="stat-value">8</div>
        <div class="stat-label">Setting Groups</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo count($settingsByGroup['general'] ?? []); ?></div>
        <div class="stat-label">General Settings</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo count($settingsByGroup['security'] ?? []); ?></div>
        <div class="stat-label">Security Items</div>
    </div>
</div>

<!-- Settings Navigation Tabs -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-th-list"></i>
            Settings Categories
        </h5>
    </div>
    
    <div class="card-content">
        <div class="settings-tabs-container">
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
        </div>
        
        <!-- Settings Form -->
        <form id="settings-form" method="POST" action="<?php echo app_base_url('/admin/settings/save'); ?>" enctype="multipart/form-data">
            <div class="settings-content">
                <!-- General Settings -->
                <div id="general-settings" class="settings-section active">
                    <?php echo renderSettingsGroup('general', $settingsByGroup['general'] ?? [], [
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
                    <?php echo renderSettingsGroup('appearance', $settingsByGroup['appearance'] ?? [], [
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
                    <?php echo renderSettingsGroup('email', $settingsByGroup['email'] ?? [], [
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
                    <?php echo renderSettingsGroup('security', $settingsByGroup['security'] ?? [], [
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
                    <?php echo renderSettingsGroup('privacy', $settingsByGroup['privacy'] ?? [], [
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
                    <?php echo renderSettingsGroup('performance', $settingsByGroup['performance'] ?? [], [
                        'caching' => [
                            'title' => 'Caching',
                            'icon' => 'fa-bolt',
                            'description' => 'Performance optimization settings'
                        ],
                        'database' => [
                            'title' => 'Database',
                            'icon' => 'fa-database',
                            'description' => 'Database optimization settings'
                        ],
                        'assets' => [
                            'title' => 'Assets',
                            'icon' => 'fa-file-image',
                            'description' => 'Image and file optimization'
                        ]
                    ]); ?>
                </div>
                
                <!-- System Settings -->
                <div id="system-settings" class="settings-section">
                    <?php echo renderSettingsGroup('system', $settingsByGroup['system'] ?? [], [
                        'maintenance' => [
                            'title' => 'Maintenance',
                            'icon' => 'fa-tools',
                            'description' => 'System maintenance settings'
                        ],
                        'logging' => [
                            'title' => 'Logging',
                            'icon' => 'fa-clipboard-list',
                            'description' => 'System logging configuration'
                        ],
                        'backup' => [
                            'title' => 'Backup',
                            'icon' => 'fa-download',
                            'description' => 'Backup and restore settings'
                        ]
                    ]); ?>
                </div>
                
                <!-- API Settings -->
                <div id="api-settings" class="settings-section">
                    <?php echo renderSettingsGroup('api', $settingsByGroup['api'] ?? [], [
                        'access' => [
                            'title' => 'API Access',
                            'icon' => 'fa-key',
                            'description' => 'API key management and access control'
                        ],
                        'rate_limiting' => [
                            'title' => 'Rate Limiting',
                            'icon' => 'fa-tachometer-alt',
                            'description' => 'API request rate limiting'
                        ],
                        'documentation' => [
                            'title' => 'Documentation',
                            'icon' => 'fa-book',
                            'description' => 'API documentation settings'
                        ]
                    ]); ?>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="settings-actions">
                <button type="button" class="btn btn-secondary" onclick="window.settingsManager.resetSettings()">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary" id="save-settings">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal" id="testEmailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-paper-plane"></i> Test Email Configuration</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Send a test email to verify your SMTP configuration is working correctly.</p>
                <div class="form-group">
                    <label for="testEmailRecipient">Recipient Email</label>
                    <input type="email" class="form-control" id="testEmailRecipient" placeholder="Enter recipient email">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendTestEmail">
                    <i class="fas fa-paper-plane"></i> Send Test Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Settings Manager JavaScript -->
<script src="/assets/js/admin/settings-manager.js"></script>