
<?php
/**
 * General Settings Page - Premium Modern UI/UX Design
 * Features: Beautiful gradients, smooth animations, enhanced spacing, additional fields
 */
?>

<style>
    .general-settings-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 2.5rem;
    }

    .settings-breadcrumb {
        margin-bottom: 2rem;
        animation: slideDown 0.6s ease-out;
    }

    .settings-breadcrumb h1 {
        font-size: 2.8rem;
        font-weight: 800;
        color: white;
        margin-bottom: 0.5rem;
        letter-spacing: -0.8px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .settings-breadcrumb p {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0;
    }

    .general-form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .settings-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        margin-bottom: 2rem;
        animation: fadeInUp 0.7s ease-out;
    }

    .card-icon-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .card-icon {
        font-size: 2.2rem;
        line-height: 1;
    }

    .card-header-text h2 {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
        color: white;
    }

    .card-header-text p {
        font-size: 0.95rem;
        opacity: 0.95;
        margin-bottom: 0;
    }

    .card-body {
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2.5rem;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .section-divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
        margin: 2rem 0;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }

    .section-title-icon {
        font-size: 1.4rem;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.7rem;
        letter-spacing: 0.3px;
    }

    .form-control,
    .form-select {
        padding: 1rem 1.2rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        font-family: inherit;
        background-color: #f8f9fa;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        background-color: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        outline: none;
    }

    .form-text {
        font-size: 0.85rem;
        color: #718096;
        margin-top: 0.5rem;
    }

    .form-hint {
        display: inline-block;
        background: linear-gradient(135deg, #ffecd2, #fcb69f);
        border-left: 4px solid #f5576c;
        padding: 0.8rem 1rem;
        border-radius: 6px;
        font-size: 0.9rem;
        color: #6b3410;
        margin-top: 1rem;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e2e8f0;
        margin-top: 2rem;
        justify-content: flex-end;
    }

    .btn {
        padding: 1rem 2rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        border: none;
        transition: all 0.3s ease;
        letter-spacing: 0.5px;
    }

    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .success-message {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        animation: slideDown 0.5s ease-out;
    }
    
    /* Logo and Favicon Display Styles */
    .current-logo-display,
    .current-favicon-display {
        text-align: center;
    }
    
    .logo-preview-container,
    .favicon-preview-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100px;
    }
    
    .favicon-preview-container {
        min-height: 80px;
        min-width: 80px;
    }
    
    .current-logo-display img,
    .current-favicon-display img {
        display: block;
        margin: 0 auto;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .general-settings-container {
            padding: 1rem;
        }

        .settings-breadcrumb h1 {
            font-size: 2rem;
        }

        .card-icon-header {
            padding: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .button-group {
            flex-direction: column;
            justify-content: stretch;
        }

        .btn {
            width: 100%;
        }
    }

    /* File Upload Styles */
    .file-upload-container {
        position: relative;
        border: 2px dashed #e2e8f0;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload-container:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .file-upload-container input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .upload-hint {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: #64748b;
        font-size: 0.95rem;
    }

    .upload-hint i {
        font-size: 1.2rem;
        color: #cbd5e1;
    }

    .image-preview {
        position: relative;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .image-preview img {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
    }

    .btn-remove {
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.8rem;
    }

    .btn-remove:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    .current-image {
        margin-top: 0.5rem;
        padding: 0.75rem;
        background: #f1f5f9;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }

    .current-image img {
        border-radius: 4px;
        margin-left: 0.5rem;
    }

    /* Current Image Display Styles */
    .current-logo-display,
    .current-favicon-display {
        margin-bottom: 1rem;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }

    .logo-actions,
    .favicon-actions {
        display: flex;
        flex-direction: row;
        gap: 0.75rem;
        width: 100%;
        justify-content: flex-start;
        align-items: center;
        margin-top: 0.5rem;
    }

    /* Align buttons horizontally when both sections have images */
    .form-row .logo-actions,
    .form-row .favicon-actions {
        margin-top: 0;
        margin-bottom: 0;
    }

    /* Responsive button alignment */
    @media (min-width: 768px) {
        /* On desktop, align buttons at the same level */
        .form-row {
            align-items: flex-end;
        }
        
        .form-row .form-group {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            height: 100%;
        }
        
        .form-row .current-logo-display,
        .form-row .current-favicon-display {
            flex-shrink: 0;
        }
        
        /* Make buttons align horizontally at the bottom */
        .form-row .logo-actions,
        .form-row .favicon-actions {
            margin-top: auto;
        }
    }

    /* Mobile responsive */
    @media (max-width: 767px) {
        .form-row {
            flex-direction: column;
            gap: 2rem;
        }
        
        .form-row .form-group {
            width: 100%;
        }
    }

    .logo-actions,
    .favicon-actions {
        display: flex;
        flex-direction: row;
        gap: 1rem;
        width: 100%;
        justify-content: flex-start;
        align-items: center;
        margin-top: 0.75rem;
    }

    .btn-change {
        background: #10b981;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.875rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        min-width: 140px;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
        white-space: nowrap;
        text-transform: none;
        letter-spacing: -0.01em;
    }

    .btn-change:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
    }

    .btn-change i {
        font-size: 1rem;
        margin: 0;
        flex-shrink: 0;
        width: 16px;
        text-align: center;
    }

    .btn-change span {
        line-height: 1.3;
        font-weight: 600;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .logo-actions,
        .favicon-actions {
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }
        
        .btn-change,
        .btn-remove {
            width: 100%;
            justify-content: flex-start;
            min-width: auto;
        }
        
        .current-logo-image,
        .current-favicon-image {
            min-height: 150px;
            padding: 1rem;
        }
    }

    /* Ensure text stays on one line */
    .btn-change span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
        text-align: left;
        font-weight: 600 !important;
        color: white !important;
    }

    /* Make buttons more prominent */
    .btn-change {
        position: relative;
        z-index: 1;
        border: 2px solid transparent;
    }

    .btn-change:hover {
        border-color: rgba(255, 255, 255, 0.3);
    }

    /* Improve text readability */
    .btn-change span {
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }
</style>

<div class="admin-content">
    <div class="general-settings-container">
        <div class="settings-breadcrumb">
            <h1>⚙️ General Settings</h1>
            <p>Manage your website's core configuration and information</p>
        </div>

        <div class="general-form-container">
            <!-- Site Identity Card -->
            <div class="settings-card">
                <div class="card-icon-header">
                    <span class="card-icon">🌍</span>
                    <div class="card-header-text">
                        <h2>Site Identity</h2>
                        <p>Configure your website's name, description, and branding</p>
                    </div>
                </div>

                <div class="card-body">
                    <form id="generalSettingsForm" action="<?php echo app_base_url('/admin/settings/update'); ?>" method="POST" enctype="multipart/form-data" class="ajax-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    
                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="site_name" class="form-label">📝 Site Name</label>
                                <input type="text" class="form-control" 
                                       id="site_name" 
                                       name="site_name" 
                                       value="<?= htmlspecialchars($settings['site_name'] ?? 'Bishwo Calculator') ?>" 
                                       placeholder="Enter your site name">
                                <div class="form-text">The name displayed in browser tabs and search results.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_description" class="form-label">📄 Site Description</label>
                                <textarea class="form-control" 
                                          id="site_description" 
                                          name="site_description" 
                                          rows="3" 
                                          placeholder="Describe your website briefly"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
                                <div class="form-text">A brief description for search engines and social sharing.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Site Branding -->
                    <div class="section-divider"></div>
                    <div class="form-section">
                        <div class="section-title">
                            <span class="section-title-icon">🎨</span>
                            Site Branding
                        </div>

                        <div class="form-row">
                            <div class="form-row">
                            <div class="form-group">
                                <label for="site_logo" class="form-label">🖼️ Site Logo</label>
                                <?php if (!empty($settings['site_logo'])): ?>
                                <!-- Show current logo directly -->
                                <div class="current-logo-display mb-3">
                                    <div class="logo-preview-container p-4 bg-light rounded-3 border border-2 border-primary-subtle shadow-sm">
                                        <img src="<?= htmlspecialchars(app_base_url($settings['site_logo'])) ?>" alt="Current Logo" class="img-fluid mx-auto d-block" style="max-width: 250px; max-height: 180px; object-fit: contain;">
                                    </div>
                                </div>
                                <div class="logo-actions">
                                    <button type="button" class="btn-change" onclick="document.getElementById('site_logo').click()">
                                        <i class="fas fa-edit"></i><span>Change Logo</span>
                                    </button>
                                </div>
                                <?php endif; ?>
                                
                                <!-- File input (hidden) -->
                                <div class="image-preview" id="logoPreview" style="display: none;">
                                    <img src="" alt="Logo Preview" style="max-width: 100px; max-height: 100px; border-radius: 8px; border: 2px solid #e2e8f0;">
                                    <button type="button" class="btn-remove" onclick="removeImage('site_logo', 'logoPreview')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="form-text">Upload your site logo. Square format (512x512px) recommended for best display.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="favicon" class="form-label">🌟 Favicon</label>
                                <?php if (!empty($settings['favicon'])): ?>
                                <!-- Show current favicon directly -->
                                <div class="current-favicon-display mb-3">
                                    <div class="favicon-preview-container p-3 bg-light rounded-3 border border-2 border-primary-subtle shadow-sm d-inline-block mx-auto">
                                        <img src="<?= htmlspecialchars(app_base_url($settings['favicon'])) ?>" alt="Current Favicon" class="img-fluid mx-auto d-block" style="max-width: 80px; max-height: 80px; object-fit: contain;">
                                    </div>
                                </div>
                                <div class="favicon-actions">
                                    <button type="button" class="btn-change" onclick="document.getElementById('favicon').click()">
                                        <i class="fas fa-edit"></i><span>Change Favicon</span>
                                    </button>
                                </div>
                                <input type="file" 
                                       class="form-control-file" 
                                       id="favicon" 
                                       name="favicon" 
                                       accept="image/x-icon,image/png"
                                       onchange="previewImage(event, 'faviconPreview')"
                                       style="display: none;">
                                <?php else: ?>
                                <!-- Show upload area when no favicon -->
                                <div class="file-upload-container">
                                    <input type="file" 
                                           class="form-control-file" 
                                           id="favicon" 
                                           name="favicon" 
                                           accept="image/x-icon,image/png"
                                           onchange="previewImage(event, 'faviconPreview')">
                                    <div class="upload-hint">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Choose a square image (32x32px or 64x64px)</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="image-preview" id="faviconPreview" style="display: none;">
                                    <img src="" alt="Favicon Preview" style="max-width: 32px; max-height: 32px; border-radius: 8px; border: 2px solid #e2e8f0;">
                                    <button type="button" class="btn-remove" onclick="removeImage('favicon', 'faviconPreview')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="form-text">Upload your site favicon. Square format (32x32px or 64x64px) recommended.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="logo_text" class="form-label">🔤 Logo Text</label>
                                <input type="text" class="form-control" 
                                       id="logo_text" 
                                       name="logo_text" 
                                       value="<?= htmlspecialchars($settings['logo_text'] ?? 'EngiCal Pro') ?>" 
                                       placeholder="EngiCal Pro">
                                <div class="form-text">Custom text to display in the header alongside or instead of the logo.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="header_style" class="form-label">🎨 Header Style</label>
                                <select class="form-select" id="header_style" name="header_style">
                                    <option value="logo_only" <?php echo ($settings['header_style'] ?? 'logo_text') === 'logo_only' ? 'selected' : ''; ?>>Logo Only</option>
                                    <option value="text_only" <?php echo ($settings['header_style'] ?? 'logo_text') === 'text_only' ? 'selected' : ''; ?>>Text Only</option>
                                </select>
                                <div class="form-text">Choose how the header displays: logo only or text only.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Site Information -->
                    <div class="section-divider"></div>
                    <div class="form-section">
                        <div class="section-title">
                            <span class="section-title-icon">📋</span>
                            Additional Information
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="footer_text" class="form-label">📜 Footer Text</label>
                                <input type="text" class="form-control" 
                                       id="footer_text" 
                                       name="footer_text" 
                                       value="<?= htmlspecialchars($settings['footer_text'] ?? '') ?>" 
                                       placeholder="Copyright © 2024 Your Company">
                                <div class="form-text">Text displayed in your website footer.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="support_email" class="form-label">💌 Support Email</label>
                                <input type="email" class="form-control" 
                                       id="support_email" 
                                       name="support_email" 
                                       value="<?= htmlspecialchars($settings['support_email'] ?? '') ?>" 
                                       placeholder="support@example.com">
                                <div class="form-text">Email address for customer support inquiries.</div>
                            </div>
                        </div>

                        <div class="form-hint">
                            💡 <strong>Pro Tip:</strong> Ensure all information is up-to-date and reflects your current business details for better user experience.
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="button-group">
                        <button type="submit" class="btn btn-save">💾 Save All Changes</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Image preview functionality
    function previewImage(event, previewId) {
        const file = event.target.files[0];
        if (file) {
            console.log('File selected:', file.name, file.type, file.size);
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                const img = preview.querySelector('img');
                img.src = e.target.result;
                preview.style.display = 'block';
                
                // Hide the current image display and show preview
                const currentContainer = preview.closest('.form-group').querySelector('.current-logo-container, .current-favicon-container');
                if (currentContainer) {
                    currentContainer.style.display = 'none';
                }
                
                console.log('Preview updated for:', previewId);
            };
            reader.readAsDataURL(file);
        }
    }

    // Remove uploaded image (for preview)
    function removeImage(inputId, previewId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).style.display = 'none';
        
        // Show current image container again if it exists
        const currentContainer = document.getElementById(previewId).closest('.form-group').querySelector('.current-logo-container, .current-favicon-container');
        if (currentContainer) {
            currentContainer.style.display = 'block';
        }
        
        console.log('Image removed:', inputId);
    }

    document.getElementById('generalSettingsForm').addEventListener('submit', function(e) {
        console.log('General settings form submitted');
        
        // Debug: Check if files are being sent
        const logoInput = document.getElementById('site_logo');
        const faviconInput = document.getElementById('favicon');
        
        if (logoInput && logoInput.files.length > 0) {
            console.log('Logo file selected:', logoInput.files[0].name);
        }
        if (faviconInput && faviconInput.files.length > 0) {
            console.log('Favicon file selected:', faviconInput.files[0].name);
        }
        
        // Show FormData contents when form is submitted
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            if (value instanceof File) {
                console.log('File in FormData:', key, value.name, value.size);
            } else {
                console.log('Field in FormData:', key, value);
            }
        }
    });

    // Add smooth animations on page load
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.settings-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = (index * 0.15) + 's';
        });
        
        console.log('General settings page loaded');
    });
</script>
