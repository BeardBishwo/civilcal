<?php
ob_start();
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($page_title ?? 'Settings'); ?></h1>
        <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Configure your site settings and preferences</p>
    </div>
</div>

<!-- Settings Navigation -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1rem; margin-bottom: 2rem; display: flex; flex-wrap: wrap; gap: 0.5rem;">
    <?php foreach ($settings_sections ?? [] as $key => $section): ?>
        <a href="<?php echo app_base_url('/admin/settings/' . $key); ?>" 
           style="padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all 0.2s ease; <?php echo ($current_section ?? '') === $key ? 'background: rgba(67, 97, 238, 0.2); color: #4cc9f0; border: 1px solid rgba(67, 97, 238, 0.3);' : 'color: #9ca3af; border: 1px solid transparent;'; ?>">
            <i class="<?php echo $section['icon'] ?? 'fas fa-cog'; ?>" style="margin-right: 0.5rem;"></i>
            <?php echo htmlspecialchars($section['title'] ?? $section); ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Settings Form -->
<form id="settingsForm" method="POST" action="<?php echo app_base_url('/admin/settings/update'); ?>">
    <input type="hidden" name="section" value="<?php echo htmlspecialchars($current_section ?? 'general'); ?>">
    
    <?php if ($current_section === 'general' && isset($general_settings)): ?>
        <?php echo renderGeneralSettings($general_settings); ?>
    <?php elseif ($current_section === 'application' && isset($app_settings)): ?>
        <?php echo renderApplicationSettings($app_settings); ?>
    <?php elseif ($current_section === 'users' && isset($user_settings)): ?>
        <?php echo renderUserSettings($user_settings); ?>
    <?php elseif ($current_section === 'security' && isset($security_settings)): ?>
        <?php echo renderSecuritySettings($security_settings); ?>
    <?php elseif ($current_section === 'email' && isset($email_settings)): ?>
        <?php echo renderEmailSettings($email_settings); ?>
    <?php elseif ($current_section === 'api' && isset($api_settings)): ?>
        <?php echo renderApiSettings($api_settings); ?>
    <?php elseif ($current_section === 'performance' && isset($performance_settings)): ?>
        <?php echo renderPerformanceSettings($performance_settings); ?>
    <?php elseif ($current_section === 'advanced' && isset($advanced_settings)): ?>
        <?php echo renderAdvancedSettings($advanced_settings); ?>
    <?php endif; ?>
    
    <!-- Action Buttons -->
    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" style="background: #4361ee; color: white; border: none; padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
            <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Save Settings
        </button>
        <button type="button" onclick="document.getElementById('settingsForm').reset();" style="background: rgba(255, 255, 255, 0.05); color: #9ca3af; border: 1px solid rgba(102, 126, 234, 0.2); padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
            <i class="fas fa-undo" style="margin-right: 0.5rem;"></i> Reset
        </button>
    </div>
</form>

<?php
// Helper functions to render settings sections
function renderGeneralSettings($settings) {
    ob_start();
    ?>
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 2rem; margin-bottom: 1.5rem;">
        <h3 style="color: #f9fafb; margin: 0 0 1.5rem 0; font-size: 1.125rem; font-weight: 600;">Site Information</h3>
        <?php foreach ($settings as $key => $setting): ?>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; color: #e5e7eb; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                    <?php echo htmlspecialchars($setting['label'] ?? $key); ?>
                    <?php if ($setting['required'] ?? false): ?><span style="color: #ef4444;">*</span><?php endif; ?>
                </label>
                <?php if (($setting['type'] ?? 'text') === 'textarea'): ?>
                    <textarea name="settings[<?php echo $key; ?>]" 
                              style="width: 100%; padding: 0.75rem; background: rgba(15, 15, 46, 0.5); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 8px; color: #e5e7eb; font-size: 0.875rem; min-height: 100px;" 
                              <?php echo ($setting['required'] ?? false) ? 'required' : ''; ?>><?php echo htmlspecialchars($setting['value'] ?? ''); ?></textarea>
                <?php else: ?>
                    <input type="<?php echo htmlspecialchars($setting['type'] ?? 'text'); ?>" 
                           name="settings[<?php echo $key; ?>]" 
                           value="<?php echo htmlspecialchars($setting['value'] ?? ''); ?>" 
                           style="width: 100%; padding: 0.75rem; background: rgba(15, 15, 46, 0.5); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 8px; color: #e5e7eb; font-size: 0.875rem;" 
                           <?php echo ($setting['required'] ?? false) ? 'required' : ''; ?>>
                <?php endif; ?>
                <?php if (isset($setting['description'])): ?>
                    <small style="display: block; color: #9ca3af; font-size: 0.75rem; margin-top: 0.25rem;"><?php echo htmlspecialchars($setting['description']); ?></small>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

function renderApplicationSettings($settings) {
    return renderSettingsGroup($settings, 'Application Configuration');
}

function renderUserSettings($settings) {
    return renderSettingsGroup($settings, 'User Management');
}

function renderSecuritySettings($settings) {
    return renderSettingsGroup($settings, 'Security & Authentication');
}

function renderEmailSettings($settings) {
    return renderSettingsGroup($settings, 'Email Configuration');
}

function renderApiSettings($settings) {
    return renderSettingsGroup($settings, 'API Configuration');
}

function renderPerformanceSettings($settings) {
    return renderSettingsGroup($settings, 'Performance & Optimization');
}

function renderAdvancedSettings($settings) {
    return renderSettingsGroup($settings, 'Advanced Configuration');
}

function renderSettingsGroup($settings, $title) {
    ob_start();
    ?>
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 2rem; margin-bottom: 1.5rem;">
        <h3 style="color: #f9fafb; margin: 0 0 1.5rem 0; font-size: 1.125rem; font-weight: 600;"><?php echo $title; ?></h3>
        <?php foreach ($settings as $key => $setting): ?>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; color: #e5e7eb; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                    <?php echo htmlspecialchars($setting['label'] ?? $key); ?>
                </label>
                <?php if (($setting['type'] ?? 'text') === 'select'): ?>
                    <select name="settings[<?php echo $key; ?>]" 
                            style="width: 100%; padding: 0.75rem; background: rgba(15, 15, 46, 0.5); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 8px; color: #e5e7eb; font-size: 0.875rem;">
                        <?php foreach ($setting['options'] ?? [] as $optKey => $optLabel): ?>
                            <option value="<?php echo htmlspecialchars($optKey); ?>" <?php echo ($setting['value'] ?? '') == $optKey ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($optLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif (($setting['type'] ?? 'text') === 'checkbox'): ?>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" 
                               name="settings[<?php echo $key; ?>]" 
                               value="1" 
                               <?php echo ($setting['value'] ?? false) ? 'checked' : ''; ?>
                               style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="color: #9ca3af; font-size: 0.875rem;"><?php echo htmlspecialchars($setting['description'] ?? 'Enable'); ?></span>
                    </label>
                <?php else: ?>
                    <input type="<?php echo htmlspecialchars($setting['type'] ?? 'text'); ?>" 
                           name="settings[<?php echo $key; ?>]" 
                           value="<?php echo htmlspecialchars($setting['value'] ?? ''); ?>" 
                           style="width: 100%; padding: 0.75rem; background: rgba(15, 15, 46, 0.5); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 8px; color: #e5e7eb; font-size: 0.875rem;">
                <?php endif; ?>
                <?php if (isset($setting['description']) && ($setting['type'] ?? 'text') !== 'checkbox'): ?>
                    <small style="display: block; color: #9ca3af; font-size: 0.75rem; margin-top: 0.25rem;"><?php echo htmlspecialchars($setting['description']); ?></small>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
    .admin-layout {
        display: flex;
        min-height: calc(100vh - 80px);
        background: #f8fafc;
    }
    
    body.dark-theme .admin-layout {
        background: #0f172a;
    }
    
    .admin-sidebar {
        width: 280px;
        background: white;
        border-right: 1px solid #e2e8f0;
        padding: 0;
        position: sticky;
        top: 80px;
        height: calc(100vh - 80px);
        overflow-y: auto;
    }
    
    body.dark-theme .admin-sidebar {
        background: #1e293b;
        border-color: #334155;
    }
    
    .admin-content {
        flex: 1;
        padding: 2rem;
        max-width: calc(100% - 280px);
        overflow-y: auto;
    }
    
    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f9fafb;
    }
    
    body.dark-theme .sidebar-header {
        background: #0f172a;
        border-color: #334155;
    }
    
    .sidebar-nav {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        color: #4b5563;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }
    
    .sidebar-nav a:hover {
        background: #f3f4f6;
        color: #1f2937;
        border-left-color: #3b82f6;
    }
    
    .sidebar-nav a.active {
        background: #eff6ff;
        color: #2563eb;
        border-left-color: #3b82f6;
    }
    
    body.dark-theme .sidebar-nav a {
        color: #d1d5db;
    }
    
    body.dark-theme .sidebar-nav a:hover {
        background: #374151;
        color: #f9fafb;
    }
    
    body.dark-theme .sidebar-nav a.active {
        background: #1e3a8a;
        color: #93c5fd;
    }
    
    .settings-header {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }
    
    body.dark-theme .settings-header {
        background: #1e293b;
        border-color: #334155;
    }
    
    .settings-section {
        background: white;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    body.dark-theme .settings-section {
        background: #1e293b;
        border-color: #334155;
    }
    
    .section-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
    }
    
    body.dark-theme .section-header {
        background: #0f172a;
        border-color: #334155;
    }
    
    .section-title {
        margin: 0 0 0.5rem 0;
        color: #1f2937;
        font-size: 1.25rem;
        font-weight: 600;
    }
    
    body.dark-theme .section-title {
        color: #f9fafb;
    }
    
    .section-description {
        margin: 0;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    body.dark-theme .section-description {
        color: #9ca3af;
    }
    
    .section-content {
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 2rem;
    }
    
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    body.dark-theme .form-label {
        color: #d1d5db;
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: white;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    body.dark-theme .form-input {
        background: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
    
    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
    
    .form-description {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.5rem;
    }
    
    body.dark-theme .form-description {
        color: #9ca3af;
    }
    
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }
    
    .toggle-input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #d1d5db;
        transition: 0.3s;
        border-radius: 24px;
    }
    
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }
    
    .toggle-input:checked + .toggle-slider {
        background-color: #3b82f6;
    }
    
    .toggle-input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
    
    .save-button {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.875rem;
    }
    
    .save-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
    
    .save-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .success-message {
        background: #dcfdf7;
        color: #065f46;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid #a7f3d0;
    }
    
    body.dark-theme .success-message {
        background: #064e3b;
        color: #6ee7b7;
        border-color: #047857;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    @media (max-width: 1024px) {
        .admin-layout {
            flex-direction: column;
        }
        
        .admin-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            top: 0;
        }
        
        .admin-content {
            max-width: 100%;
        }
    }
</style>

<div class="admin-layout">
    <!-- Admin Sidebar -->
    <nav class="admin-sidebar">
        <div class="sidebar-header">
            <h3 style="margin: 0; color: #1f2937; font-size: 1.125rem; font-weight: 600;">
                <i class="fas fa-cogs me-2"></i> Settings
            </h3>
        </div>
        
        <ul class="sidebar-nav">
            <?php foreach ($settings_sections as $section): ?>
            <li>
                <a href="<?php echo $section['url']; ?>" 
                   class="<?php echo $current_section === $section['id'] ? 'active' : ''; ?>">
                    <i class="<?php echo $section['icon']; ?>"></i>
                    <div>
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($section['name']); ?></div>
                        <div style="font-size: 0.75rem; color: #9ca3af; font-weight: normal;">
                            <?php echo htmlspecialchars($section['description']); ?>
                        </div>
                    </div>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <main class="admin-content">
        <!-- Settings Header -->
        <div class="settings-header">
            <h1 style="margin: 0 0 0.5rem 0; color: #1f2937; font-size: 2rem; font-weight: 700;">
                <?php echo htmlspecialchars($page_title); ?>
            </h1>
            <p style="margin: 0; color: #6b7280; font-size: 1.125rem;">
                Configure your site settings and preferences
            </p>
        </div>
        
        <?php if (isset($_GET['updated'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle me-2"></i>
            Settings updated successfully!
        </div>
        <?php endif; ?>
        
        <!-- Settings Form -->
        <form id="settingsForm" method="POST" action="/admin/settings/update">
            <input type="hidden" name="section" value="<?php echo htmlspecialchars($current_section); ?>">
            
            <?php
            // Get the appropriate settings based on current section
            $settings_data = null;
            switch ($current_section) {
                case 'general':
                    $settings_data = $general_settings ?? [];
                    break;
                case 'application':
                    $settings_data = $app_settings ?? [];
                    break;
                case 'users':
                    $settings_data = $user_settings ?? [];
                    break;
                case 'security':
                    $settings_data = $security_settings ?? [];
                    break;
                case 'email':
                    $settings_data = $email_settings ?? [];
                    break;
                case 'api':
                    $settings_data = $api_settings ?? [];
                    break;
                case 'performance':
                    $settings_data = $performance_settings ?? [];
                    break;
                case 'advanced':
                    $settings_data = $advanced_settings ?? [];
                    break;
            }
            ?>
            
            <?php if ($settings_data): ?>
                <?php foreach ($settings_data as $section_key => $section_data): ?>
                <div class="settings-section">
                    <div class="section-header">
                        <h3 class="section-title"><?php echo htmlspecialchars($section_data['title']); ?></h3>
                        <p class="section-description"><?php echo htmlspecialchars($section_data['description']); ?></p>
                    </div>
                    
                    <div class="section-content">
                        <?php foreach ($section_data['fields'] as $field): ?>
                        <div class="form-group">
                            <label class="form-label" for="<?php echo htmlspecialchars($field['name']); ?>">
                                <?php echo htmlspecialchars($field['label']); ?>
                                <?php if ($field['required'] ?? false): ?>
                                    <span style="color: #ef4444;">*</span>
                                <?php endif; ?>
                            </label>
                            
                            <?php if ($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'url'): ?>
                                <input type="<?php echo htmlspecialchars($field['type']); ?>" 
                                       class="form-input" 
                                       id="<?php echo htmlspecialchars($field['name']); ?>"
                                       name="settings[<?php echo htmlspecialchars($field['name']); ?>]"
                                       value="<?php echo htmlspecialchars($field['value'] ?? ''); ?>"
                                       <?php echo ($field['required'] ?? false) ? 'required' : ''; ?>>
                                       
                            <?php elseif ($field['type'] === 'number'): ?>
                                <input type="number" 
                                       class="form-input" 
                                       id="<?php echo htmlspecialchars($field['name']); ?>"
                                       name="settings[<?php echo htmlspecialchars($field['name']); ?>]"
                                       value="<?php echo htmlspecialchars($field['value'] ?? ''); ?>"
                                       min="<?php echo $field['min'] ?? ''; ?>"
                                       max="<?php echo $field['max'] ?? ''; ?>"
                                       <?php echo ($field['required'] ?? false) ? 'required' : ''; ?>>
                                       
                            <?php elseif ($field['type'] === 'password'): ?>
                                <input type="password" 
                                       class="form-input" 
                                       id="<?php echo htmlspecialchars($field['name']); ?>"
                                       name="settings[<?php echo htmlspecialchars($field['name']); ?>]"
                                       value="<?php echo htmlspecialchars($field['value'] ?? ''); ?>">
                                       
                            <?php elseif ($field['type'] === 'textarea'): ?>
                                <textarea class="form-input form-textarea" 
                                          id="<?php echo htmlspecialchars($field['name']); ?>"
                                          name="settings[<?php echo htmlspecialchars($field['name']); ?>]"
                                          <?php echo ($field['required'] ?? false) ? 'required' : ''; ?>><?php echo htmlspecialchars($field['value'] ?? ''); ?></textarea>
                                          
                            <?php elseif ($field['type'] === 'select'): ?>
                                <select class="form-input form-select" 
                                        id="<?php echo htmlspecialchars($field['name']); ?>"
                                        name="settings[<?php echo htmlspecialchars($field['name']); ?>]"
                                        <?php echo ($field['required'] ?? false) ? 'required' : ''; ?>>
                                    <?php foreach ($field['options'] as $value => $label): ?>
                                        <option value="<?php echo htmlspecialchars($value); ?>"
                                                <?php echo ($field['value'] ?? '') === $value ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                
                            <?php elseif ($field['type'] === 'toggle'): ?>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <label class="toggle-switch">
                                        <input type="checkbox" 
                                               class="toggle-input"
                                               id="<?php echo htmlspecialchars($field['name']); ?>"
                                               name="settings[<?php echo htmlspecialchars($field['name']); ?>]"
                                               value="1"
                                               <?php echo ($field['value'] ?? false) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <span style="font-size: 0.875rem; color: #6b7280;">
                                        <?php echo ($field['value'] ?? false) ? 'Enabled' : 'Disabled'; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($field['description'])): ?>
                                <p class="form-description"><?php echo htmlspecialchars($field['description']); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Save Button -->
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="save-button" style="background: #6b7280;" onclick="resetForm()">
                        <i class="fas fa-undo me-2"></i> Reset
                    </button>
                    <button type="submit" class="save-button" id="saveButton">
                        <i class="fas fa-save me-2"></i> Save Settings
                    </button>
                </div>
            <?php else: ?>
                <div class="settings-section">
                    <div class="section-content" style="text-align: center; padding: 4rem;">
                        <i class="fas fa-cog" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                        <h3 style="color: #6b7280; margin: 0 0 0.5rem 0;">Settings Section</h3>
                        <p style="color: #9ca3af; margin: 0;">This settings section is coming soon.</p>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('settingsForm');
    const saveButton = document.getElementById('saveButton');
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
        
        // Submit form data
        const formData = new FormData(form);
        formData.append('ajax', '1');
        
        fetch('/admin/settings/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Settings saved successfully!', 'success');
            } else {
                showNotification('Failed to save settings: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error occurred', 'error');
        })
        .finally(() => {
            // Reset button state
            saveButton.disabled = false;
            saveButton.innerHTML = '<i class="fas fa-save me-2"></i> Save Settings';
        });
    });
    
    // Toggle switch labels
    document.querySelectorAll('.toggle-input').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const label = this.closest('.form-group').querySelector('span');
            if (label && (label.textContent.includes('Enabled') || label.textContent.includes('Disabled'))) {
                label.textContent = this.checked ? 'Enabled' : 'Disabled';
            }
        });
    });
});

function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.getElementById('settingsForm').reset();
        showNotification('Form reset successfully', 'info');
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        transition: all 0.3s ease;
        transform: translateX(100%);
        ${type === 'success' ? 'background: #10b981;' : 
          type === 'error' ? 'background: #ef4444;' : 'background: #3b82f6;'}
    `;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle me-2"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>

<?php require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php'; ?>
