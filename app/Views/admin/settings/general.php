<?php
/**
 * General Settings Page - MVC Partial View
 * This is now a proper partial view that integrates with the admin layout
 */

// Get current settings from controller data
$general_settings = $general_settings ?? [];
$current_section = $current_section ?? 'general';
$page_title = $page_title ?? 'General Settings - Admin Panel';
$settings_sections = $settings_sections ?? [];
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo htmlspecialchars($page_title); ?></h1>
        <p class="page-subtitle">Configure your site settings and preferences</p>
    </div>
</div>

<!-- Settings Navigation -->
<div class="settings-nav">
    <?php foreach ($settings_sections as $section): ?>
        <a href="<?php echo htmlspecialchars($section['url']); ?>"
           class="<?php echo ($current_section === $section['id']) ? 'active' : ''; ?>">
            <i class="<?php echo htmlspecialchars($section['icon']); ?>"></i>
            <?php echo htmlspecialchars($section['name']); ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Settings Form -->
<form id="settingsForm" method="POST" action="<?php echo app_base_url('/admin/settings/update'); ?>">
    <input type="hidden" name="section" value="<?php echo htmlspecialchars($current_section); ?>">

    <!-- Settings Content -->
    <div class="settings-content">
        <?php foreach ($general_settings as $groupKey => $group): ?>
            <div class="settings-card">
                <h3><?php echo htmlspecialchars($group['title'] ?? $groupKey); ?></h3>
                <p class="card-description"><?php echo htmlspecialchars($group['description'] ?? ''); ?></p>

                <?php if (isset($group['fields']) && is_array($group['fields'])): ?>
                    <?php foreach ($group['fields'] as $fieldKey => $field): ?>
                        <div class="form-group">
                            <label class="form-label<?php echo ($field['required'] ?? false) ? ' required' : ''; ?>">
                                <?php echo htmlspecialchars($field['label'] ?? $fieldKey); ?>
                            </label>

                            <?php if (($field['type'] ?? 'text') === 'textarea'): ?>
                                <textarea name="settings[<?php echo htmlspecialchars($fieldKey); ?>]"
                                          class="form-input form-textarea"
                                          <?php echo ($field['required'] ?? false) ? 'required' : ''; ?>><?php echo htmlspecialchars($field['value'] ?? ''); ?></textarea>
                            <?php elseif (($field['type'] ?? 'text') === 'select'): ?>
                                <select name="settings[<?php echo htmlspecialchars($fieldKey); ?>]"
                                        class="form-input form-select"
                                        <?php echo ($field['required'] ?? false) ? 'required' : ''; ?>>
                                    <?php foreach ($field['options'] ?? [] as $optKey => $optLabel): ?>
                                        <option value="<?php echo htmlspecialchars($optKey); ?>"
                                                <?php echo ($field['value'] ?? '') == $optKey ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($optLabel); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif (($field['type'] ?? 'text') === 'checkbox'): ?>
                                <label class="form-checkbox-container">
                                    <input type="checkbox"
                                           name="settings[<?php echo htmlspecialchars($fieldKey); ?>]"
                                           value="1"
                                           class="form-checkbox"
                                           <?php echo ($field['value'] ?? false) ? 'checked' : ''; ?>>
                                    <span><?php echo htmlspecialchars($field['description'] ?? 'Enable'); ?></span>
                                </label>
                            <?php else: ?>
                                <input type="<?php echo htmlspecialchars($field['type'] ?? 'text'); ?>"
                                       name="settings[<?php echo htmlspecialchars($fieldKey); ?>]"
                                       value="<?php echo htmlspecialchars($field['value'] ?? ''); ?>"
                                       class="form-input"
                                       <?php echo ($field['required'] ?? false) ? 'required' : ''; ?>>
                            <?php endif; ?>

                            <?php if (isset($field['description']) && ($field['type'] ?? 'text') !== 'checkbox'): ?>
                                <small class="form-description"><?php echo htmlspecialchars($field['description']); ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
            <button type="button" onclick="document.getElementById('settingsForm').reset();" class="btn btn-secondary">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>
    </div>
</form>
