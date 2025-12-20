<?php
/**
 * Admin Logo Settings Page
 * Allows admin to configure logo image, text, and styling options
 */

// Ensure admin access
if (empty($_SESSION['is_admin'])) {
    header('Location: /login');
    exit;
}

$pageTitle = 'Logo Settings - Admin Panel';
require_once __DIR__ . '/../../default/views/partials/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_meta = get_site_meta();
    
    // Update logo settings
    if (isset($_POST['logo_url'])) {
        $site_meta['logo'] = sanitize_input($_POST['logo_url']);
    }
    
    if (isset($_POST['logo_text'])) {
        $site_meta['logo_text'] = sanitize_input($_POST['logo_text']);
    }
    
    if (isset($_POST['header_style'])) {
        $site_meta['header_style'] = sanitize_input($_POST['header_style']);
    }
    
    // Logo settings
    $site_meta['logo_settings'] = [
        'show_logo' => isset($_POST['show_logo']),
        'show_text' => isset($_POST['show_text']),
        'text_position' => sanitize_input($_POST['text_position'] ?? 'right'),
        'logo_height' => sanitize_input($_POST['logo_height'] ?? '40px'),
        'text_size' => sanitize_input($_POST['text_size'] ?? '1.5rem'),
        'text_weight' => sanitize_input($_POST['text_weight'] ?? '700'),
        'spacing' => sanitize_input($_POST['spacing'] ?? '12px'),
        'border_radius' => sanitize_input($_POST['border_radius'] ?? '8px'),
        'shadow' => sanitize_input($_POST['shadow'] ?? 'subtle'),
        'hover_effect' => sanitize_input($_POST['hover_effect'] ?? 'scale'),
        'logo_style' => sanitize_input($_POST['logo_style'] ?? 'modern')
    ];
    
    // Brand colors
    $site_meta['brand_colors'] = [
        'primary' => sanitize_input($_POST['brand_primary'] ?? '#4f46e5'),
        'secondary' => sanitize_input($_POST['brand_secondary'] ?? '#10b981'),
        'accent' => sanitize_input($_POST['brand_accent'] ?? '#f59e0b')
    ];
    
    // Save to file
    $metaFile = __DIR__ . '/../../../app/db/site_meta.json';
    if (file_put_contents($metaFile, json_encode($site_meta, JSON_PRETTY_PRINT))) {
        $success_message = "Logo settings updated successfully!";
    } else {
        $error_message = "Failed to save logo settings.";
    }
}

$site_meta = get_site_meta();

function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
?>

<style>
    .admin-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .settings-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .preview-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 200px;
        border-radius: 12px;
        margin-bottom: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #374151;
    }
    
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
    }
    
    .form-group input[type="checkbox"] {
        width: auto;
        margin-right: 0.5rem;
    }
    
    .color-input {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .color-input input[type="color"] {
        width: 60px;
        height: 40px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }
    
    .btn {
        background: #4f46e5;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .btn:hover {
        background: #3730a3;
    }
    
    .btn-secondary {
        background: #6b7280;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    .logo-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.5rem;
        font-weight: 700;
        text-decoration: none;
        color: white;
        padding: 1rem;
        border-radius: 8px;
        background: rgba(255,255,255,0.1);
        transition: all 0.3s ease;
    }
    
    .logo-preview:hover {
        transform: scale(1.05);
        background: rgba(255,255,255,0.2);
    }
    
    .logo-preview img {
        height: 40px;
        width: auto;
        border-radius: 8px;
    }
    
    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-container">
    <h1>🎨 Logo Settings</h1>
    <p>Configure your site logo, text, and branding options</p>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <!-- Logo Preview -->
    <div class="preview-card">
        <div class="logo-preview" id="logoPreview">
            <img src="<?php echo htmlspecialchars($site_meta['logo'] ?? app_base_url('assets/icons/icon-192.png')); ?>" 
                 alt="Logo" id="previewImage">
            <span id="previewText"><?php echo htmlspecialchars($site_meta['logo_text'] ?? (\App\Services\SettingsService::get('site_name', 'Bishwo Calculator') ?: 'Bishwo Calculator')); ?></span>
        </div>
    </div>
    
    <form method="POST" class="settings-form">
        <div class="settings-grid">
            <!-- Basic Logo Settings -->
            <div class="settings-card">
                <h3>📷 Logo Image & Text</h3>
                
                <div class="form-group">
                    <label for="logo_url">Logo Image URL</label>
                    <input type="url" id="logo_url" name="logo_url" 
                           value="<?php echo htmlspecialchars($site_meta['logo'] ?? ''); ?>"
                           placeholder="/path/to/your/logo.png">
                </div>
                
                <div class="form-group mb-4">
                    <label for="logo_text" class="form-label">Logo Text</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-font"></i></span>
                        <input type="text" class="form-control" id="logo_text" name="logo_text" 
                               value="<?php echo htmlspecialchars($site_meta['logo_text'] ?? (\App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro') ?: 'Engineering Calculator Pro')); ?>"
                               placeholder="Your Site Name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="header_style">Display Style</label>
                    <select id="header_style" name="header_style">
                        <option value="logo_only" <?php echo ($site_meta['header_style'] ?? 'logo_text') === 'logo_only' ? 'selected' : ''; ?>>Logo Only</option>
                        <option value="text_only" <?php echo ($site_meta['header_style'] ?? 'logo_text') === 'text_only' ? 'selected' : ''; ?>>Text Only</option>
                        <option value="logo_text" <?php echo ($site_meta['header_style'] ?? 'logo_text') === 'logo_text' ? 'selected' : ''; ?>>Logo + Text</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="show_logo" <?php echo ($site_meta['logo_settings']['show_logo'] ?? true) ? 'checked' : ''; ?>>
                        Show Logo Image
                    </label>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="show_text" <?php echo ($site_meta['logo_settings']['show_text'] ?? true) ? 'checked' : ''; ?>>
                        Show Logo Text
                    </label>
                </div>
            </div>
            
            <!-- Styling Options -->
            <div class="settings-card">
                <h3>🎨 Styling Options</h3>
                
                <div class="form-group">
                    <label for="logo_style">Logo Style</label>
                    <select id="logo_style" name="logo_style">
                        <option value="modern" <?php echo ($site_meta['logo_settings']['logo_style'] ?? 'modern') === 'modern' ? 'selected' : ''; ?>>Modern</option>
                        <option value="minimal" <?php echo ($site_meta['logo_settings']['logo_style'] ?? 'modern') === 'minimal' ? 'selected' : ''; ?>>Minimal</option>
                        <option value="premium" <?php echo ($site_meta['logo_settings']['logo_style'] ?? 'modern') === 'premium' ? 'selected' : ''; ?>>Premium</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="logo_height">Logo Height</label>
                    <input type="text" id="logo_height" name="logo_height" 
                           value="<?php echo htmlspecialchars($site_meta['logo_settings']['logo_height'] ?? '40px'); ?>"
                           placeholder="40px">
                </div>
                
                <div class="form-group">
                    <label for="text_size">Text Size</label>
                    <input type="text" id="text_size" name="text_size" 
                           value="<?php echo htmlspecialchars($site_meta['logo_settings']['text_size'] ?? '1.5rem'); ?>"
                           placeholder="1.5rem">
                </div>
                
                <div class="form-group">
                    <label for="text_weight">Text Weight</label>
                    <select id="text_weight" name="text_weight">
                        <option value="400" <?php echo ($site_meta['logo_settings']['text_weight'] ?? '700') === '400' ? 'selected' : ''; ?>>Normal</option>
                        <option value="500" <?php echo ($site_meta['logo_settings']['text_weight'] ?? '700') === '500' ? 'selected' : ''; ?>>Medium</option>
                        <option value="600" <?php echo ($site_meta['logo_settings']['text_weight'] ?? '700') === '600' ? 'selected' : ''; ?>>Semi Bold</option>
                        <option value="700" <?php echo ($site_meta['logo_settings']['text_weight'] ?? '700') === '700' ? 'selected' : ''; ?>>Bold</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="hover_effect">Hover Effect</label>
                    <select id="hover_effect" name="hover_effect">
                        <option value="scale" <?php echo ($site_meta['logo_settings']['hover_effect'] ?? 'scale') === 'scale' ? 'selected' : ''; ?>>Scale</option>
                        <option value="glow" <?php echo ($site_meta['logo_settings']['hover_effect'] ?? 'scale') === 'glow' ? 'selected' : ''; ?>>Glow</option>
                        <option value="bounce" <?php echo ($site_meta['logo_settings']['hover_effect'] ?? 'scale') === 'bounce' ? 'selected' : ''; ?>>Bounce</option>
                        <option value="pulse" <?php echo ($site_meta['logo_settings']['hover_effect'] ?? 'scale') === 'pulse' ? 'selected' : ''; ?>>Pulse</option>
                    </select>
                </div>
            </div>
            
            <!-- Brand Colors -->
            <div class="settings-card">
                <h3>🌈 Brand Colors</h3>
                
                <div class="form-group">
                    <label for="brand_primary">Primary Color</label>
                    <div class="color-input">
                        <input type="color" id="brand_primary" name="brand_primary" 
                               value="<?php echo htmlspecialchars($site_meta['brand_colors']['primary'] ?? '#4f46e5'); ?>">
                        <input type="text" value="<?php echo htmlspecialchars($site_meta['brand_colors']['primary'] ?? '#4f46e5'); ?>" readonly>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="brand_secondary">Secondary Color</label>
                    <div class="color-input">
                        <input type="color" id="brand_secondary" name="brand_secondary" 
                               value="<?php echo htmlspecialchars($site_meta['brand_colors']['secondary'] ?? '#10b981'); ?>">
                        <input type="text" value="<?php echo htmlspecialchars($site_meta['brand_colors']['secondary'] ?? '#10b981'); ?>" readonly>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="brand_accent">Accent Color</label>
                    <div class="color-input">
                        <input type="color" id="brand_accent" name="brand_accent" 
                               value="<?php echo htmlspecialchars($site_meta['brand_colors']['accent'] ?? '#f59e0b'); ?>">
                        <input type="text" value="<?php echo htmlspecialchars($site_meta['brand_colors']['accent'] ?? '#f59e0b'); ?>" readonly>
                    </div>
                </div>
            </div>
            
            <!-- Advanced Settings -->
            <div class="settings-card">
                <h3>⚙️ Advanced Settings</h3>
                
                <div class="form-group">
                    <label for="spacing">Logo Spacing</label>
                    <input type="text" id="spacing" name="spacing" 
                           value="<?php echo htmlspecialchars($site_meta['logo_settings']['spacing'] ?? '12px'); ?>"
                           placeholder="12px">
                </div>
                
                <div class="form-group">
                    <label for="border_radius">Border Radius</label>
                    <input type="text" id="border_radius" name="border_radius" 
                           value="<?php echo htmlspecialchars($site_meta['logo_settings']['border_radius'] ?? '8px'); ?>"
                           placeholder="8px">
                </div>
                
                <div class="form-group">
                    <label for="shadow">Shadow Style</label>
                    <select id="shadow" name="shadow">
                        <option value="none" <?php echo ($site_meta['logo_settings']['shadow'] ?? 'subtle') === 'none' ? 'selected' : ''; ?>>None</option>
                        <option value="subtle" <?php echo ($site_meta['logo_settings']['shadow'] ?? 'subtle') === 'subtle' ? 'selected' : ''; ?>>Subtle</option>
                        <option value="strong" <?php echo ($site_meta['logo_settings']['shadow'] ?? 'subtle') === 'strong' ? 'selected' : ''; ?>>Strong</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="text_position">Text Position</label>
                    <select id="text_position" name="text_position">
                        <option value="right" <?php echo ($site_meta['logo_settings']['text_position'] ?? 'right') === 'right' ? 'selected' : ''; ?>>Right</option>
                        <option value="bottom" <?php echo ($site_meta['logo_settings']['text_position'] ?? 'right') === 'bottom' ? 'selected' : ''; ?>>Bottom</option>
                        <option value="top" <?php echo ($site_meta['logo_settings']['text_position'] ?? 'right') === 'top' ? 'selected' : ''; ?>>Top</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Save
            </button>
            <button type="button" class="btn btn-secondary" onclick="resetPreview()">
                <i class="fas fa-undo"></i> Reset Preview
            </button>
        </div>
    </form>
</div>

<script>
    // Live preview functionality
    function updatePreview() {
        const logoUrl = document.getElementById('logo_url').value;
        const logoText = document.getElementById('logo_text').value;
        const headerStyle = document.getElementById('header_style').value;
        
        const previewImage = document.getElementById('previewImage');
        const previewText = document.getElementById('previewText');
        
        // Update image
        if (logoUrl) {
            previewImage.src = logoUrl;
            previewImage.style.display = (headerStyle === 'text_only') ? 'none' : 'block';
        }
        
        // Update text
        previewText.textContent = logoText;
        previewText.style.display = (headerStyle === 'logo_only') ? 'none' : 'block';
        
        // Update colors
        const primary = document.getElementById('brand_primary').value;
        const secondary = document.getElementById('brand_secondary').value;
        
        previewText.style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
        previewText.style.webkitBackgroundClip = 'text';
        previewText.style.webkitTextFillColor = 'transparent';
        previewText.style.backgroundClip = 'text';
    }
    
    function resetPreview() {
        document.getElementById('logo_url').value = "<?php echo app_base_url('assets/icons/icon-192.png'); ?>";
        document.getElementById('logo_text').value = 'EngiCal Pro';
        document.getElementById('header_style').value = 'logo_text';
        updatePreview();
    }
    
    // Color input sync
    document.querySelectorAll('input[type="color"]').forEach(input => {
        input.addEventListener('change', function() {
            this.nextElementSibling.value = this.value;
            updatePreview();
        });
    });
    
    // Live preview on input changes
    document.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });
    
    // Initialize preview
    updatePreview();
</script>

<?php require_once __DIR__ . '/../../default/views/partials/footer.php'; ?>
