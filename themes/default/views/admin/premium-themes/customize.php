<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Customize Theme: <?php echo htmlspecialchars($themeName ?? 'Unknown Theme'); ?></h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Adjust colors, fonts, and layout options for this theme</p>
        </div>
    </div>
</div>

<!-- Theme Customization Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/premium-themes/'.$themeName.'/customize'); ?>#colors" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-palette"></i>
            <span>Colors</span>
        </a>
        <a href="<?php echo app_base_url('/admin/premium-themes/'.$themeName.'/customize'); ?>#fonts" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-font"></i>
            <span>Fonts</span>
        </a>
        <a href="<?php echo app_base_url('/admin/premium-themes/'.$themeName.'/customize'); ?>#layout" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-border-all"></i>
            <span>Layout</span>
        </a>
        <a href="<?php echo app_base_url('/admin/premium-themes/'.$themeName.'/customize'); ?>#components" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-cube"></i>
            <span>Components</span>
        </a>
    </div>
    
    <form method="POST" action="<?php echo app_base_url('/admin/premium-themes/'.$themeName.'/update-settings'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div>
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-palette" style="color: #4cc9f0;"></i>
                        Color Settings
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Primary Color</label>
                        <input type="color" name="settings[primary_color]" value="<?php echo htmlspecialchars($settings['primary_color'] ?? '#4cc9f0'); ?>" 
                               style="width: 100%; height: 40px; background: transparent; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Secondary Color</label>
                        <input type="color" name="settings[secondary_color]" value="<?php echo htmlspecialchars($settings['secondary_color'] ?? '#34d399'); ?>" 
                               style="width: 100%; height: 40px; background: transparent; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Background Color</label>
                        <input type="color" name="settings[bg_color]" value="<?php echo htmlspecialchars($settings['bg_color'] ?? '#0f172a'); ?>" 
                               style="width: 100%; height: 40px; background: transparent; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Text Color</label>
                        <input type="color" name="settings[text_color]" value="<?php echo htmlspecialchars($settings['text_color'] ?? '#f9fafb'); ?>" 
                               style="width: 100%; height: 40px; background: transparent; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px;">
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Accent Color</label>
                        <input type="color" name="settings[accent_color]" value="<?php echo htmlspecialchars($settings['accent_color'] ?? '#fbbf24'); ?>" 
                               style="width: 100%; height: 40px; background: transparent; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px;">
                    </div>
                </div>
                
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-font" style="color: #34d399;"></i>
                        Font Settings
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Header Font</label>
                        <select name="settings[header_font]" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="inter" <?php echo ($settings['header_font'] ?? 'inter') === 'inter' ? 'selected' : ''; ?>>Inter</option>
                            <option value="roboto" <?php echo ($settings['header_font'] ?? 'inter') === 'roboto' ? 'selected' : ''; ?>>Roboto</option>
                            <option value="open-sans" <?php echo ($settings['header_font'] ?? 'inter') === 'open-sans' ? 'selected' : ''; ?>>Open Sans</option>
                            <option value="lato" <?php echo ($settings['header_font'] ?? 'inter') === 'lato' ? 'selected' : ''; ?>>Lato</option>
                            <option value="poppins" <?php echo ($settings['header_font'] ?? 'inter') === 'poppins' ? 'selected' : ''; ?>>Poppins</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Body Font</label>
                        <select name="settings[body_font]" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="inter" <?php echo ($settings['body_font'] ?? 'inter') === 'inter' ? 'selected' : ''; ?>>Inter</option>
                            <option value="roboto" <?php echo ($settings['body_font'] ?? 'inter') === 'roboto' ? 'selected' : ''; ?>>Roboto</option>
                            <option value="open-sans" <?php echo ($settings['body_font'] ?? 'inter') === 'open-sans' ? 'selected' : ''; ?>>Open Sans</option>
                            <option value="lato" <?php echo ($settings['body_font'] ?? 'inter') === 'lato' ? 'selected' : ''; ?>>Lato</option>
                            <option value="poppins" <?php echo ($settings['body_font'] ?? 'inter') === 'poppins' ? 'selected' : ''; ?>>Poppins</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Font Size</label>
                        <select name="settings[font_size]" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="small" <?php echo ($settings['font_size'] ?? 'medium') === 'small' ? 'selected' : ''; ?>>Small</option>
                            <option value="medium" <?php echo ($settings['font_size'] ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="large" <?php echo ($settings['font_size'] ?? 'medium') === 'large' ? 'selected' : ''; ?>>Large</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div>
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-border-all" style="color: #fbbf24;"></i>
                        Layout Settings
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Theme Style</label>
                        <select name="settings[theme_style]" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="boxed" <?php echo ($settings['theme_style'] ?? 'boxed') === 'boxed' ? 'selected' : ''; ?>>Boxed</option>
                            <option value="wide" <?php echo ($settings['theme_style'] ?? 'boxed') === 'wide' ? 'selected' : ''; ?>>Wide</option>
                            <option value="boxed-fluid" <?php echo ($settings['theme_style'] ?? 'boxed') === 'boxed-fluid' ? 'selected' : ''; ?>>Boxed Fluid</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <div>
                                <div style="color: #f9fafb; font-weight: 500;">Sidebar Navigation</div>
                                <div style="color: #9ca3af; font-size: 0.875rem;">Toggle between fixed and collapsible sidebar</div>
                            </div>
                            <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                                <input type="checkbox" name="settings[sidebar_fixed]" <?php echo !empty($settings['sidebar_fixed']) ? 'checked' : ''; ?> 
                                       style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo !empty($settings['sidebar_fixed']) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                                <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                            </label>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <div>
                                <div style="color: #f9fafb; font-weight: 500;">Dark Mode</div>
                                <div style="color: #9ca3af; font-size: 0.875rem;">Enable dark mode by default</div>
                            </div>
                            <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                                <input type="checkbox" name="settings[dark_mode]" <?php echo !empty($settings['dark_mode']) ? 'checked' : ''; ?> 
                                       style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo !empty($settings['dark_mode']) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                                <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                            </label>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="color: #f9fafb; font-weight: 500;">Responsive Design</div>
                                <div style="color: #9ca3af; font-size: 0.875rem;">Enable responsive behavior</div>
                            </div>
                            <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                                <input type="checkbox" name="settings[responsive]" <?php echo !empty($settings['responsive']) ? 'checked' : ''; ?> 
                                       style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo !empty($settings['responsive']) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                                <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Animation Speed</label>
                        <select name="settings[animation_speed]" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="fast" <?php echo ($settings['animation_speed'] ?? 'medium') === 'fast' ? 'selected' : ''; ?>>Fast</option>
                            <option value="medium" <?php echo ($settings['animation_speed'] ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="slow" <?php echo ($settings['animation_speed'] ?? 'medium') === 'slow' ? 'selected' : ''; ?>>Slow</option>
                            <option value="none" <?php echo ($settings['animation_speed'] ?? 'medium') === 'none' ? 'selected' : ''; ?>>None</option>
                        </select>
                    </div>
                </div>
                
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-cube" style="color: #22d3ee;"></i>
                        Component Settings
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Header Style</label>
                        <select name="settings[header_style]" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="default" <?php echo ($settings['header_style'] ?? 'default') === 'default' ? 'selected' : ''; ?>>Default</option>
                            <option value="minimal" <?php echo ($settings['header_style'] ?? 'default') === 'minimal' ? 'selected' : ''; ?>>Minimal</option>
                            <option value="modern" <?php echo ($settings['header_style'] ?? 'default') === 'modern' ? 'selected' : ''; ?>>Modern</option>
                            <option value="classic" <?php echo ($settings['header_style'] ?? 'default') === 'classic' ? 'selected' : ''; ?>>Classic</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Button Style</label>
                        <select name="settings[button_style]" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="rounded" <?php echo ($settings['button_style'] ?? 'rounded') === 'rounded' ? 'selected' : ''; ?>>Rounded</option>
                            <option value="square" <?php echo ($settings['button_style'] ?? 'rounded') === 'square' ? 'selected' : ''; ?>>Square</option>
                            <option value="pill" <?php echo ($settings['button_style'] ?? 'rounded') === 'pill' ? 'selected' : ''; ?>>Pill</option>
                            <option value="outline" <?php echo ($settings['button_style'] ?? 'rounded') === 'outline' ? 'selected' : ''; ?>>Outline</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Card Radius</label>
                        <select name="settings[card_radius]" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="none" <?php echo ($settings['card_radius'] ?? 'rounded') === 'none' ? 'selected' : ''; ?>>None</option>
                            <option value="sm" <?php echo ($settings['card_radius'] ?? 'rounded') === 'sm' ? 'selected' : ''; ?>>Small</option>
                            <option value="md" <?php echo ($settings['card_radius'] ?? 'rounded') === 'md' ? 'selected' : ''; ?>>Medium</option>
                            <option value="lg" <?php echo ($settings['card_radius'] ?? 'rounded') === 'lg' ? 'selected' : ''; ?>>Large</option>
                            <option value="xl" <?php echo ($settings['card_radius'] ?? 'rounded') === 'xl' ? 'selected' : ''; ?>>Extra Large</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" 
                    style="padding: 0.75rem 2rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                <i class="fas fa-save"></i>
                <span>Save Changes</span>
            </button>
            <a href="<?php echo app_base_url('/admin/premium-themes/'.$themeName.'/preview'); ?>" 
               style="padding: 0.75rem 2rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; text-decoration: none;">
                <i class="fas fa-eye"></i>
                <span>Preview Changes</span>
            </a>
            <a href="<?php echo app_base_url('/admin/premium-themes'); ?>" 
               style="padding: 0.75rem 2rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none;">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </a>
        </div>
    </form>
</div>

<!-- Theme Preview -->
<div class="admin-card" style="background: rgba(15, 23, 42, 0.8); padding: 2rem; border-radius: 8px;">
    <h2 class="admin-card-title" style="color: #f9fafb;">Theme Preview</h2>
    <div style="background: rgba(255, 255, 255, 0.05); border-radius: 8px; padding: 2rem; text-align: center;">
        <div style="margin-bottom: 2rem;">
            <div style="display: inline-block; padding: 0.5rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; margin-bottom: 1rem;">
                <span style="color: #4cc9f0; font-weight: 600;"><?php echo htmlspecialchars($themeName ?? 'Theme Name'); ?></span>
            </div>
            <h3 style="color: #f9fafb; margin: 0 0 0.5rem 0;">Live Preview</h3>
            <p style="color: #9ca3af;">See your customizations in real-time</p>
        </div>
        
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; margin-top: 2rem;">
            <button style="padding: 0.5rem 1rem; background: <?php echo (!empty($settings['primary_color']) ? $settings['primary_color'] : '#4cc9f0'); ?>; border: none; border-radius: <?php echo (!empty($settings['button_style']) && $settings['button_style'] === 'rounded') ? '6px' : (!empty($settings['button_style']) && $settings['button_style'] === 'pill' ? '50px' : '0'); ?>; color: <?php echo (!empty($settings['primary_color']) && $settings['primary_color'] === '#0f172a') ? '#f9fafb' : '#0f172a'; ?>; cursor: pointer;">
                Primary Button
            </button>
            <button style="padding: 0.5rem 1rem; background: transparent; border: 1px solid <?php echo (!empty($settings['secondary_color']) ? $settings['secondary_color'] : '#34d399'); ?>; border-radius: <?php echo (!empty($settings['button_style']) && $settings['button_style'] === 'rounded') ? '6px' : (!empty($settings['button_style']) && $settings['button_style'] === 'pill' ? '50px' : '0'); ?>; color: <?php echo (!empty($settings['secondary_color']) ? $settings['secondary_color'] : '#34d399'); ?>; cursor: pointer;">
                Outline Button
            </button>
            <button style="padding: 0.5rem 1rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: <?php echo (!empty($settings['button_style']) && $settings['button_style'] === 'rounded') ? '6px' : (!empty($settings['button_style']) && $settings['button_style'] === 'pill' ? '50px' : '0'); ?>; color: #34d399; cursor: pointer;">
                Success Button
            </button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>