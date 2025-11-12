<?php
// Admin Theme Customization View
$pageTitle = 'Customize: ' . htmlspecialchars($theme['display_name']);
$bodyClass = 'admin-page theme-customize-page';
?>

<div class="admin-container">
    <div class="page-header">
        <h1><?php echo htmlspecialchars($theme['display_name']); ?> - Customization</h1>
        <div class="header-actions">
            <a href="/admin/themes" class="btn btn-secondary">Back to Themes</a>
            <button class="btn btn-primary" id="preview-btn">Preview Changes</button>
        </div>
    </div>

    <div class="customize-container">
        <!-- Settings Panel -->
        <div class="settings-panel">
            <div class="tabs">
                <button class="tab-btn active" data-tab="colors">Colors</button>
                <button class="tab-btn" data-tab="typography">Typography</button>
                <button class="tab-btn" data-tab="features">Features</button>
                <button class="tab-btn" data-tab="layout">Layout</button>
                <button class="tab-btn" data-tab="advanced">Advanced</button>
            </div>

            <!-- Colors Tab -->
            <div class="tab-content active" id="colors-tab">
                <h3>Color Customization</h3>
                <form id="colors-form" class="customization-form">
                    <div class="form-group">
                        <label for="primary_color">Primary Color</label>
                        <div class="color-input-group">
                            <input type="color" id="primary_color" name="primary_color" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['primary'] ?? $themeConfig['config']['colors']['primary']); ?>">
                            <input type="text" class="color-text" placeholder="#667eea" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['primary'] ?? $themeConfig['config']['colors']['primary']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="secondary_color">Secondary Color</label>
                        <div class="color-input-group">
                            <input type="color" id="secondary_color" name="secondary_color" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['secondary'] ?? $themeConfig['config']['colors']['secondary']); ?>">
                            <input type="text" class="color-text" placeholder="#764ba2" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['secondary'] ?? $themeConfig['config']['colors']['secondary']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="accent_color">Accent Color</label>
                        <div class="color-input-group">
                            <input type="color" id="accent_color" name="accent_color" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['accent'] ?? $themeConfig['config']['colors']['accent']); ?>">
                            <input type="text" class="color-text" placeholder="#f093fb" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['accent'] ?? $themeConfig['config']['colors']['accent']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="background_color">Background Color</label>
                        <div class="color-input-group">
                            <input type="color" id="background_color" name="background_color" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['background'] ?? $themeConfig['config']['colors']['background']); ?>">
                            <input type="text" class="color-text" placeholder="#0f0c29" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['background'] ?? $themeConfig['config']['colors']['background']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="text_color">Text Color</label>
                        <div class="color-input-group">
                            <input type="color" id="text_color" name="text_color" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['text'] ?? $themeConfig['config']['colors']['text']); ?>">
                            <input type="text" class="color-text" placeholder="#f7fafc" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['text'] ?? $themeConfig['config']['colors']['text']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="text_secondary_color">Secondary Text Color</label>
                        <div class="color-input-group">
                            <input type="color" id="text_secondary_color" name="text_secondary_color" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['text_secondary'] ?? $themeConfig['config']['colors']['text_secondary']); ?>">
                            <input type="text" class="color-text" placeholder="#a0aec0" 
                                   value="<?php echo htmlspecialchars($customizations['colors']['text_secondary'] ?? $themeConfig['config']['colors']['text_secondary']); ?>">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary save-btn" data-section="colors">Save Colors</button>
                </form>
            </div>

            <!-- Typography Tab -->
            <div class="tab-content" id="typography-tab">
                <h3>Typography Settings</h3>
                <form id="typography-form" class="customization-form">
                    <div class="form-group">
                        <label for="font_family">Font Family</label>
                        <select id="font_family" name="font_family">
                            <option value="Segoe UI, Tahoma, Geneva, Verdana, sans-serif" 
                                    <?php echo ($customizations['typography']['font_family'] ?? 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif') === 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ? 'selected' : ''; ?>>
                                Segoe UI
                            </option>
                            <option value="Inter, sans-serif" 
                                    <?php echo ($customizations['typography']['font_family'] ?? '') === 'Inter, sans-serif' ? 'selected' : ''; ?>>
                                Inter
                            </option>
                            <option value="Roboto, sans-serif" 
                                    <?php echo ($customizations['typography']['font_family'] ?? '') === 'Roboto, sans-serif' ? 'selected' : ''; ?>>
                                Roboto
                            </option>
                            <option value="Open Sans, sans-serif" 
                                    <?php echo ($customizations['typography']['font_family'] ?? '') === 'Open Sans, sans-serif' ? 'selected' : ''; ?>>
                                Open Sans
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="heading_size">Heading Size</label>
                        <input type="text" id="heading_size" name="heading_size" placeholder="4rem"
                               value="<?php echo htmlspecialchars($customizations['typography']['heading_size'] ?? '4rem'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="body_size">Body Size</label>
                        <input type="text" id="body_size" name="body_size" placeholder="1rem"
                               value="<?php echo htmlspecialchars($customizations['typography']['body_size'] ?? '1rem'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="line_height">Line Height</label>
                        <input type="text" id="line_height" name="line_height" placeholder="1.6"
                               value="<?php echo htmlspecialchars($customizations['typography']['line_height'] ?? '1.6'); ?>">
                    </div>

                    <button type="button" class="btn btn-primary save-btn" data-section="typography">Save Typography</button>
                </form>
            </div>

            <!-- Features Tab -->
            <div class="tab-content" id="features-tab">
                <h3>Feature Toggles</h3>
                <form id="features-form" class="customization-form">
                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="dark_mode" 
                                   <?php echo ($customizations['features']['dark_mode'] ?? $themeConfig['config']['features']['dark_mode']) ? 'checked' : ''; ?>>
                            Dark Mode
                        </label>
                    </div>

                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="animations" 
                                   <?php echo ($customizations['features']['animations'] ?? $themeConfig['config']['features']['animations']) ? 'checked' : ''; ?>>
                            Animations
                        </label>
                    </div>

                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="glassmorphism" 
                                   <?php echo ($customizations['features']['glassmorphism'] ?? $themeConfig['config']['features']['glassmorphism']) ? 'checked' : ''; ?>>
                            Glassmorphism
                        </label>
                    </div>

                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="3d_effects" 
                                   <?php echo ($customizations['features']['3d_effects'] ?? $themeConfig['config']['features']['3d_effects']) ? 'checked' : ''; ?>>
                            3D Effects
                        </label>
                    </div>

                    <button type="button" class="btn btn-primary save-btn" data-section="features">Save Features</button>
                </form>
            </div>

            <!-- Layout Tab -->
            <div class="tab-content" id="layout-tab">
                <h3>Layout Settings</h3>
                <form id="layout-form" class="customization-form">
                    <div class="form-group">
                        <label for="header_style">Header Style</label>
                        <select id="header_style" name="header_style">
                            <option value="logo_text" <?php echo ($customizations['layout']['header_style'] ?? 'logo_text') === 'logo_text' ? 'selected' : ''; ?>>Logo + Text</option>
                            <option value="logo_only" <?php echo ($customizations['layout']['header_style'] ?? '') === 'logo_only' ? 'selected' : ''; ?>>Logo Only</option>
                            <option value="text_only" <?php echo ($customizations['layout']['header_style'] ?? '') === 'text_only' ? 'selected' : ''; ?>>Text Only</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="footer_layout">Footer Layout</label>
                        <select id="footer_layout" name="footer_layout">
                            <option value="standard" <?php echo ($customizations['layout']['footer_layout'] ?? 'standard') === 'standard' ? 'selected' : ''; ?>>Standard</option>
                            <option value="minimal" <?php echo ($customizations['layout']['footer_layout'] ?? '') === 'minimal' ? 'selected' : ''; ?>>Minimal</option>
                            <option value="extended" <?php echo ($customizations['layout']['footer_layout'] ?? '') === 'extended' ? 'selected' : ''; ?>>Extended</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="container_width">Container Width</label>
                        <input type="text" id="container_width" name="container_width" placeholder="1200px"
                               value="<?php echo htmlspecialchars($customizations['layout']['container_width'] ?? '1200px'); ?>">
                    </div>

                    <button type="button" class="btn btn-primary save-btn" data-section="layout">Save Layout</button>
                </form>
            </div>

            <!-- Advanced Tab -->
            <div class="tab-content" id="advanced-tab">
                <h3>Advanced Settings</h3>
                <form id="advanced-form" class="customization-form">
                    <div class="form-group">
                        <label for="custom_css">Custom CSS</label>
                        <textarea id="custom_css" name="custom_css" rows="15" placeholder="/* Add custom CSS here */"><?php echo htmlspecialchars($customizations['custom_css'] ?? ''); ?></textarea>
                    </div>

                    <button type="button" class="btn btn-primary save-btn" data-section="custom_css">Save Custom CSS</button>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn btn-danger" id="reset-btn">Reset to Default</button>
            </div>
        </div>

        <!-- Preview Panel -->
        <div class="preview-panel">
            <div class="preview-header">
                <h3>Live Preview</h3>
                <div class="preview-controls">
                    <button class="preview-size-btn active" data-size="desktop">Desktop</button>
                    <button class="preview-size-btn" data-size="tablet">Tablet</button>
                    <button class="preview-size-btn" data-size="mobile">Mobile</button>
                </div>
            </div>
            <iframe id="preview-frame" class="preview-frame" src="/admin/themes/<?php echo $theme['id']; ?>/preview"></iframe>
        </div>
    </div>
</div>

<style>
.customize-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 20px;
}

.settings-panel {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.tabs {
    display: flex;
    border-bottom: 1px solid #e0e0e0;
    background: #f5f5f5;
}

.tab-btn {
    flex: 1;
    padding: 12px;
    border: none;
    background: none;
    cursor: pointer;
    font-weight: 500;
    color: #666;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}

.tab-btn.active {
    color: #667eea;
    border-bottom-color: #667eea;
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.color-input-group {
    display: flex;
    gap: 10px;
}

.color-input-group input[type="color"] {
    width: 50px;
    height: 40px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
}

.color-input-group input[type="text"] {
    flex: 1;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.checkbox-group input[type="checkbox"] {
    margin-right: 10px;
    cursor: pointer;
}

textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
}

.save-btn {
    width: 100%;
    margin-top: 10px;
}

.action-buttons {
    padding: 20px;
    border-top: 1px solid #e0e0e0;
}

.preview-panel {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.preview-header {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.preview-controls {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.preview-size-btn {
    padding: 6px 12px;
    border: 1px solid #ddd;
    background: white;
    cursor: pointer;
    border-radius: 4px;
    font-size: 12px;
}

.preview-size-btn.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.preview-frame {
    flex: 1;
    border: none;
    border-radius: 4px;
    min-height: 600px;
}

@media (max-width: 1200px) {
    .customize-container {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        });
    });

    // Color input sync
    document.querySelectorAll('.color-input-group').forEach(group => {
        const colorInput = group.querySelector('input[type="color"]');
        const textInput = group.querySelector('input[type="text"]');
        
        colorInput.addEventListener('change', () => {
            textInput.value = colorInput.value;
        });
        
        textInput.addEventListener('change', () => {
            if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value)) {
                colorInput.value = textInput.value;
            }
        });
    });

    // Save buttons
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const section = this.dataset.section;
            const form = this.closest('form');
            const formData = new FormData(form);
            
            fetch('/admin/themes/<?php echo $theme['id']; ?>/save-' + section, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Saved successfully!');
                    document.getElementById('preview-frame').src = document.getElementById('preview-frame').src;
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });
    });

    // Reset button
    document.getElementById('reset-btn').addEventListener('click', function() {
        if (confirm('Reset all customizations to default?')) {
            fetch('/admin/themes/<?php echo $theme['id']; ?>/reset', {
                method: 'POST'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    });

    // Preview size buttons
    document.querySelectorAll('.preview-size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const size = this.dataset.size;
            const frame = document.getElementById('preview-frame');
            
            document.querySelectorAll('.preview-size-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            if (size === 'mobile') {
                frame.style.width = '375px';
            } else if (size === 'tablet') {
                frame.style.width = '768px';
            } else {
                frame.style.width = '100%';
            }
        });
    });
});
</script>
