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
