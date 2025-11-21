<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Theme Customization</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Customize colors, layouts, and visual appearance</p>
        </div>
    </div>
</div>

<!-- Theme Customization Overview -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-paint-brush" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Theme</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($theme_info['name'] ?? 'Default Theme'); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Currently Active</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Active</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-palette" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Colors Customized</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($customization_data['color_schemes'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Color Schemes</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> Customized</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-font" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Font Options</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($customization_data['font_options'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Available</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-font"></i> Fonts</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-sync" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Last Modified</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo $theme_info['last_modified'] ?? 'Never'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Updated</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-clock"></i> Recently</small>
    </div>
</div>

<!-- Theme Customization Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/themes'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-th-large"></i>
            <span>Themes</span>
        </a>
        <a href="<?php echo app_base_url('/admin/themes/customize'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-paint-brush"></i>
            <span>Customize</span>
        </a>
        <a href="<?php echo app_base_url('/admin/themes/layouts'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-border-all"></i>
            <span>Layouts</span>
        </a>
        <a href="<?php echo app_base_url('/admin/themes/components'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-cube"></i>
            <span>Components</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">Customization Settings</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <form method="POST" action="<?php echo app_base_url('/admin/themes/customize/update'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-palette" style="color: #4cc9f0;"></i>
                        Color Scheme
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Primary Color</label>
                        <input type="color" name="primary_color" value="<?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>" 
                               style="width: 100%; height: 40px; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; background: transparent;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Secondary Color</label>
                        <input type="color" name="secondary_color" value="<?php echo $theme_config['secondary_color'] ?? '#34d399'; ?>" 
                               style="width: 100%; height: 40px; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; background: transparent;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Background Color</label>
                        <input type="color" name="bg_color" value="<?php echo $theme_config['bg_color'] ?? '#0f172a'; ?>" 
                               style="width: 100%; height: 40px; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; background: transparent;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Text Color</label>
                        <input type="color" name="text_color" value="<?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>" 
                               style="width: 100%; height: 40px; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; background: transparent;">
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Accent Color</label>
                        <input type="color" name="accent_color" value="<?php echo $theme_config['accent_color'] ?? '#fbbf24'; ?>" 
                               style="width: 100%; height: 40px; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; background: transparent;">
                    </div>
                </div>
                
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-font" style="color: #34d399;"></i>
                        Typography
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Header Font</label>
                        <select name="header_font" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="inter" <?php echo ($theme_config['header_font'] ?? 'inter') === 'inter' ? 'selected' : ''; ?>>Inter</option>
                            <option value="roboto" <?php echo ($theme_config['header_font'] ?? 'inter') === 'roboto' ? 'selected' : ''; ?>>Roboto</option>
                            <option value="open-sans" <?php echo ($theme_config['header_font'] ?? 'inter') === 'open-sans' ? 'selected' : ''; ?>>Open Sans</option>
                            <option value="lato" <?php echo ($theme_config['header_font'] ?? 'inter') === 'lato' ? 'selected' : ''; ?>>Lato</option>
                            <option value="poppins" <?php echo ($theme_config['header_font'] ?? 'inter') === 'poppins' ? 'selected' : ''; ?>>Poppins</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Body Font</label>
                        <select name="body_font" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="inter" <?php echo ($theme_config['body_font'] ?? 'inter') === 'inter' ? 'selected' : ''; ?>>Inter</option>
                            <option value="roboto" <?php echo ($theme_config['body_font'] ?? 'inter') === 'roboto' ? 'selected' : ''; ?>>Roboto</option>
                            <option value="open-sans" <?php echo ($theme_config['body_font'] ?? 'inter') === 'open-sans' ? 'selected' : ''; ?>>Open Sans</option>
                            <option value="lato" <?php echo ($theme_config['body_font'] ?? 'inter') === 'lato' ? 'selected' : ''; ?>>Lato</option>
                            <option value="poppins" <?php echo ($theme_config['body_font'] ?? 'inter') === 'poppins' ? 'selected' : ''; ?>>Poppins</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Font Size</label>
                        <select name="font_size" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="small" <?php echo ($theme_config['font_size'] ?? 'medium') === 'small' ? 'selected' : ''; ?>>Small</option>
                            <option value="medium" <?php echo ($theme_config['font_size'] ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="large" <?php echo ($theme_config['font_size'] ?? 'medium') === 'large' ? 'selected' : ''; ?>>Large</option>
                        </select>
                    </div>
                </div>
                
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-border-all" style="color: #fbbf24;"></i>
                        Layout Options
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: rgba(15, 23, 42, 0.8); border-radius: 6px;">
                        <div>
                            <div style="color: #f9fafb; font-weight: 500;">Fixed Sidebar</div>
                            <div style="color: #9ca3af; font-size: 0.875rem;">Sidebar remains in place when scrolling</div>
                        </div>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="fixed_sidebar" <?php echo ($theme_config['fixed_sidebar'] ?? false) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($theme_config['fixed_sidebar'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: rgba(15, 23, 42, 0.8); border-radius: 6px;">
                        <div>
                            <div style="color: #f9fafb; font-weight: 500;">Compact Mode</div>
                            <div style="color: #9ca3af; font-size: 0.875rem;">Reduce spacing between elements</div>
                        </div>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="compact_mode" <?php echo ($theme_config['compact_mode'] ?? false) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($theme_config['compact_mode'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: rgba(15, 23, 42, 0.8); border-radius: 6px;">
                        <div>
                            <div style="color: #f9fafb; font-weight: 500;">Dark Theme</div>
                            <div style="color: #9ca3af; font-size: 0.875rem;">Use dark theme by default</div>
                        </div>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="dark_theme" <?php echo ($theme_config['dark_theme'] ?? true) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($theme_config['dark_theme'] ?? true) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" 
                            style="padding: 0.75rem 2rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                        <i class="fas fa-save"></i>
                        <span>Save Customizations</span>
                    </button>
                    <a href="<?php echo app_base_url('/admin/themes/customize/reset'); ?>" 
                       style="padding: 0.75rem 2rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; color: #f87171; text-decoration: none;">
                        <i class="fas fa-undo"></i>
                        <span>Reset to Default</span>
                    </a>
                </div>
            </form>
        </div>
        
        <div>
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-eye" style="color: #22d3ee;"></i>
                    Preview
                </h3>
                
                <div style="background: rgba(15, 23, 42, 0.8); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; min-height: 200px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-calculator" style="color: <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; font-size: 1.5rem;"></i>
                            <h4 style="color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; margin: 0; font-size: 1.125rem;">Calculator</h4>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; font-size: 0.875rem;">User</span>
                            <div style="width: 24px; height: 24px; background: <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: #0f172a;">
                                U
                            </div>
                        </div>
                    </div>
                    
                    <div style="background: rgba(102, 126, 234, 0.2); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                        <div style="text-align: right; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; font-size: 1.5rem; margin-bottom: 1rem; min-height: 30px;">123.45</div>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem;">
                            <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">C</button>
                            <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">±</button>
                            <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">%</button>
                            <button style="padding: 0.75rem; background: <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border: 1px solid <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border-radius: 6px; color: <?php echo ($theme_config['primary_color'] === '#0f172a' || $theme_config['primary_color'] === '#1e293b') ? '#f9fafb' : '#0f172a'; ?>; cursor: pointer;">÷</button>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 0.5rem;">
                        <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">7</button>
                        <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">8</button>
                        <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">9</button>
                        <button style="padding: 0.75rem; background: <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border: 1px solid <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border-radius: 6px; color: <?php echo ($theme_config['primary_color'] === '#0f172a' || $theme_config['primary_color'] === '#1e293b') ? '#f9fafb' : '#0f172a'; ?>; cursor: pointer;">×</button>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 0.5rem;">
                        <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">4</button>
                        <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">5</button>
                        <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">6</button>
                        <button style="padding: 0.75rem; background: <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border: 1px solid <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border-radius: 6px; color: <?php echo ($theme_config['primary_color'] === '#0f172a' || $theme_config['primary_color'] === '#1e293b') ? '#f9fafb' : '#0f172a'; ?>; cursor: pointer;">-</button>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem;">
                        <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">1</button>
                        <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">2</button>
                        <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">3</button>
                        <button style="padding: 0.75rem; background: <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border: 1px solid <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border-radius: 6px; color: <?php echo ($theme_config['primary_color'] === '#0f172a' || $theme_config['primary_color'] === '#1e293b') ? '#f9fafb' : '#0f172a'; ?>; cursor: pointer;">+</button>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; margin-top: 0.5rem;">
                        <button style="padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: <?php echo $theme_config['text_color'] ?? '#f9fafb'; ?>; cursor: pointer;">0</button>
                        <button style="padding: 0.75rem; background: <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border: 1px solid <?php echo $theme_config['primary_color'] ?? '#4cc9f0'; ?>; border-radius: 6px; color: <?php echo ($theme_config['primary_color'] === '#0f172a' || $theme_config['primary_color'] === '#1e293b') ? '#f9fafb' : '#0f172a'; ?>; cursor: pointer;">=</button>
                    </div>
                </div>
                
                <div style="text-align: center;">
                    <p style="color: #9ca3af; margin-bottom: 1rem;">Live preview of your theme customizations</p>
                    <a href="<?php echo app_base_url('/admin/themes/customize/apply'); ?>" 
                       style="display: inline-block; padding: 0.75rem 1.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; text-decoration: none; margin-right: 1rem;">
                        <i class="fas fa-sync-alt"></i>
                        <span>Apply Changes</span>
                    </a>
                    <a href="<?php echo app_base_url('/admin/themes/customize/cancel'); ?>" 
                       style="display: inline-block; padding: 0.75rem 1.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none;">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </a>
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-history" style="color: #a78bfa;"></i>
                    Recent Customizations
                </h3>
                
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php if (!empty($recent_customizations)): ?>
                        <?php foreach (array_slice($recent_customizations, 0, 5) as $customization): ?>
                            <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <div>
                                    <div style="color: #f9fafb; font-weight: 500; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($customization['setting'] ?? 'Setting'); ?></div>
                                    <div style="color: #9ca3af; font-size: 0.75rem;"><?php echo $customization['timestamp'] ?? 'Unknown'; ?></div>
                                </div>
                                <span style="color: <?php echo $customization['status'] === 'applied' ? '#34d399' : ($customization['status'] === 'pending' ? '#fbbf24' : '#f87171'); ?>; 
                                      background: <?php echo $customization['status'] === 'applied' ? 'rgba(52, 211, 153, 0.1)' : ($customization['status'] === 'pending' ? 'rgba(251, 191, 36, 0.1)' : 'rgba(248, 113, 113, 0.1)'); ?>; 
                                      padding: 0.25rem 0.5rem; 
                                      border-radius: 4px; 
                                      font-size: 0.75rem;">
                                    <?php echo ucfirst($customization['status'] ?? 'pending'); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li style="text-align: center; padding: 1.5rem; color: #9ca3af;">No recent customizations</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Preset Themes -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Presets</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-moon" style="color: #4cc9f0;"></i>
                Dark Theme
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1.5rem;">Professional dark color scheme with high contrast</p>
            <a href="<?php echo app_base_url('/admin/themes/customize/preset/dark'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-paint-brush"></i>
                <span>Apply Preset</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-sun" style="color: #34d399;"></i>
                Light Theme
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1.5rem;">Clean light theme with subtle accents</p>
            <a href="<?php echo app_base_url('/admin/themes/customize/preset/light'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-paint-brush"></i>
                <span>Apply Preset</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-palette" style="color: #fbbf24;"></i>
                Business Theme
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1.5rem;">Professional business-oriented theme</p>
            <a href="<?php echo app_base_url('/admin/themes/customize/preset/business'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-paint-brush"></i>
                <span>Apply Preset</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-heart" style="color: #22d3ee;"></i>
                Modern Theme
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1.5rem;">Contemporary clean design with modern feel</p>
            <a href="<?php echo app_base_url('/admin/themes/customize/preset/modern'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <i class="fas fa-paint-brush"></i>
                <span>Apply Preset</span>
            </a>
        </div>
    </div>
</div>

<!-- Customize Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Customization Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/themes/customize/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-export"></i>
            <span>Export Customizations</span>
        </a>

        <a href="<?php echo app_base_url('/admin/themes/customize/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-file-import"></i>
            <span>Import Customizations</span>
        </a>

        <a href="<?php echo app_base_url('/admin/themes/customize/reset'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-undo"></i>
            <span>Reset Customizations</span>
        </a>

        <a href="<?php echo app_base_url('/admin/themes/customize/preview'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-eye"></i>
            <span>Preview Changes</span>
        </a>

        <a href="<?php echo app_base_url('/admin/themes/customize/save-preset'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-save"></i>
            <span>Save as Preset</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>