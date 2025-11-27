<style>
.create-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.form-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.2s ease;
    background-color: #fff;
}

.form-control:focus {
    outline: none;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.2s ease;
    background-color: #fff;
    min-height: 120px;
    resize: vertical;
}

.form-textarea:focus {
    outline: none;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.form-row {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-col {
    flex: 1;
}

.btn-primary {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.preview-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}

.preview-content {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.preview-card {
    flex: 1;
    min-width: 300px;
    background: #f9fafb;
    border-radius: 8px;
    padding: 1rem;
    border: 1px dashed #d1d5db;
}

.preview-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 1rem;
}

.preview-item {
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}

.preview-label {
    font-size: 0.875rem;
    color: #6b7280;
}

.preview-value {
    font-weight: 500;
    color: #1f2937;
}

.upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.2s ease;
}

.upload-area:hover {
    border-color: #8b5cf6;
    background: #f0f9ff;
}

.upload-icon {
    font-size: 2.5rem;
    color: #8b5cf6;
    margin-bottom: 1rem;
}

.upload-text {
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.upload-subtext {
    color: #9ca3af;
    font-size: 0.875rem;
}

.file-input {
    display: none;
}
</style>

<div class="create-header">
    <h1>🎨 Create Premium Theme</h1>
    <p style="color: rgba(255, 255, 255, 0.9); margin: 0.5rem 0 0 0; font-size: 1.1rem;">Create a new premium theme for the marketplace</p>
</div>

<form id="create-theme-form" method="POST" action="#" enctype="multipart/form-data">
    <div class="form-card">
        <div class="section-header">
            <h2 class="section-title">Theme Information</h2>
        </div>
        
        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="theme_name" class="form-label">Theme Name</label>
                    <input type="text" id="theme_name" name="theme_name" class="form-control" placeholder="Enter theme name" required>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="version" class="form-label">Version</label>
                    <input type="text" id="version" name="version" class="form-control" placeholder="1.0.0" value="1.0.0" required>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-textarea" placeholder="Describe your theme..." required></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" id="author" name="author" class="form-control" placeholder="Theme author" required>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="price" class="form-label">Price ($)</label>
                    <input type="number" id="price" name="price" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="tags" class="form-label">Tags</label>
            <input type="text" id="tags" name="tags" class="form-control" placeholder="calculator, dark, modern, professional (comma separated)">
        </div>
    </div>
    
    <div class="form-card">
        <div class="section-header">
            <h2 class="section-title">Theme Files</h2>
        </div>
        
        <div class="form-group">
            <label class="form-label">Theme ZIP File</label>
            <div class="upload-area" onclick="document.getElementById('theme_zip').click()">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="upload-text">Click to upload theme ZIP file</div>
                <div class="upload-subtext">ZIP files only, max 50MB</div>
                <input type="file" id="theme_zip" name="theme_zip" class="file-input" accept=".zip" required>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Preview Image</label>
            <div class="upload-area" onclick="document.getElementById('preview_image').click()">
                <div class="upload-icon">
                    <i class="fas fa-image"></i>
                </div>
                <div class="upload-text">Click to upload preview image</div>
                <div class="upload-subtext">JPG, PNG, GIF files only, max 5MB</div>
                <input type="file" id="preview_image" name="preview_image" class="file-input" accept="image/*">
            </div>
        </div>
    </div>
    
    <div class="form-card">
        <div class="section-header">
            <h2 class="section-title">Customization Options</h2>
        </div>
        
        <div class="form-group">
            <label for="color_schemes" class="form-label">Color Schemes</label>
            <textarea id="color_schemes" name="color_schemes" class="form-textarea" placeholder="Define color schemes (JSON format)"></textarea>
        </div>
        
        <div class="form-group">
            <label for="features" class="form-label">Features</label>
            <textarea id="features" name="features" class="form-textarea" placeholder="List available features (JSON format)"></textarea>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn-primary">
            <i class="fas fa-plus-circle"></i>
            <span>Create Theme</span>
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='<?php echo app_base_url('/admin/premium-themes'); ?>'">
            <i class="fas fa-times"></i>
            <span>Cancel</span>
        </button>
    </div>
</form>

<div class="preview-section">
    <div class="section-header">
        <h2 class="section-title">Theme Preview</h2>
    </div>
    
    <div class="preview-content">
        <div class="preview-card">
            <div class="preview-title">Basic Information</div>
            <div class="preview-item">
                <div class="preview-label">Name</div>
                <div class="preview-value" id="preview-name">-</div>
            </div>
            <div class="preview-item">
                <div class="preview-label">Version</div>
                <div class="preview-value" id="preview-version">-</div>
            </div>
            <div class="preview-item">
                <div class="preview-label">Author</div>
                <div class="preview-value" id="preview-author">-</div>
            </div>
            <div class="preview-item">
                <div class="preview-label">Price</div>
                <div class="preview-value" id="preview-price">-</div>
            </div>
        </div>
        
        <div class="preview-card">
            <div class="preview-title">Description</div>
            <div class="preview-item">
                <div class="preview-value" id="preview-description" style="white-space: pre-wrap;">-</div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('create-theme-form');
    const inputs = form.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('input', updatePreview);
    });
    
    function updatePreview() {
        document.getElementById('preview-name').textContent = document.getElementById('theme_name').value || '-';
        document.getElementById('preview-version').textContent = document.getElementById('version').value || '-';
        document.getElementById('preview-author').textContent = document.getElementById('author').value || '-';
        document.getElementById('preview-price').textContent = '$' + (document.getElementById('price').value || '0.00');
        document.getElementById('preview-description').textContent = document.getElementById('description').value || '-';
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Theme creation functionality would be implemented here. This is a frontend preview only.');
    });
});
</script>