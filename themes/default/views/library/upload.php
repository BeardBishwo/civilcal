<?php
// themes/default/views/library/upload.php
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');

:root {
    --up-bg: #0a0e1a;
    --up-card: rgba(255, 255, 255, 0.03);
    --up-border: rgba(255, 255, 255, 0.08);
    --up-primary: #7c5dff;
    --up-accent: #00d1ff;
    --up-success: #2ee6a8;
    --up-text: #e8ecf2;
    --up-muted: #9aa4b5;
}

.upload-wrapper {
    background: radial-gradient(circle at top right, #1a1f3c, #0a0e1a 60%);
    min-height: 100vh;
    color: var(--up-text);
    font-family: 'Outfit', sans-serif;
    padding: 60px 20px;
}

.premium-container {
    max-width: 800px;
    margin: 0 auto;
}

.glass-card {
    background: var(--up-card);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--up-border);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 24px 48px rgba(0, 0, 0, 0.4);
}

.gradient-banner {
    background: linear-gradient(135deg, rgba(124, 93, 255, 0.2), rgba(0, 209, 255, 0.1));
    border: 1px solid rgba(124, 93, 255, 0.3);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.banner-icon {
    width: 60px;
    height: 60px;
    background: var(--up-primary);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    box-shadow: 0 0 20px rgba(124, 93, 255, 0.4);
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--up-muted);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.form-control-premium {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--up-border);
    border-radius: 14px;
    padding: 14px 18px;
    color: #fff;
    font-size: 15px;
    transition: all 0.3s;
}

.form-control-premium:focus {
    outline: none;
    border-color: var(--up-primary);
    background: rgba(124, 93, 255, 0.05);
    box-shadow: 0 0 0 4px rgba(124, 93, 255, 0.1);
}

.helper-text {
    font-size: 12px;
    color: var(--up-muted);
    margin-top: 6px;
}

.btn-upload-premium {
    width: 100%;
    background: linear-gradient(135deg, var(--up-primary), #6366f1);
    color: white;
    border: none;
    padding: 18px;
    border-radius: 16px;
    font-weight: 800;
    font-size: 16px;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 10px 20px rgba(124, 93, 255, 0.3);
    margin-top: 10px;
}

.btn-upload-premium:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(124, 93, 255, 0.4);
    filter: brightness(1.1);
}

.btn-upload-premium:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.file-input-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
    width: 100%;
}

.file-input-wrapper input[type=file] {
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
}

.custom-file-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, 0.03);
    border: 2px dashed var(--up-border);
    border-radius: 14px;
    padding: 30px;
    justify-content: center;
    color: var(--up-muted);
    transition: all 0.3s;
}

.file-input-wrapper:hover .custom-file-btn {
    border-color: var(--up-primary);
    background: rgba(124, 93, 255, 0.05);
    color: #fff;
}
</style>

<div class="upload-wrapper">
    <div class="premium-container">
        <div class="gradient-banner">
            <div class="banner-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <h2 style="margin:0; font-size:20px; font-weight:800;">CONTRIBUTE & EARN</h2>
                <p style="margin:4px 0 0; color:var(--up-muted); font-size:14px;">Get 100 BB Coins for every approved resource. Your city needs blueprints!</p>
            </div>
        </div>

        <div class="glass-card">
            <h1 style="margin:0 0 32px; font-size:32px; font-weight:800; letter-spacing:-1px;">Blueprint Submission</h1>
            
            <form id="upload-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Resource Title</label>
                    <input type="text" name="title" required class="form-control-premium" placeholder="e.g., Structural Analysis of 3BHK Villa">
                    <div class="helper-text">Give it a clear, professional name.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Technical Description</label>
                    <textarea name="description" class="form-control-premium" style="min-height: 120px;" placeholder="Explain what makes this resource valuable..."></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Asset Category</label>
                        <select name="type" id="file-type" class="form-control-premium">
                            <option value="cad">AutoCAD (.dwg/.dxf)</option>
                            <option value="excel">Excel (.xlsx/.xlsm)</option>
                            <option value="pdf">PDF Documentation</option>
                            <option value="doc">Word / Text</option>
                            <option value="image">Image / Sketch</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Unlock Price (BB)</label>
                        <input type="number" name="price" min="0" value="0" class="form-control-premium">
                        <div class="helper-text">0 = Free. Users pay this to download.</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Primary File</label>
                    <div class="file-input-wrapper">
                        <div class="custom-file-btn" id="main-file-label">
                            <i class="fas fa-file-upload fa-2x"></i>
                            <span>Drop file here or click to browse</span>
                        </div>
                        <input type="file" name="file" id="main-file" required>
                    </div>
                    <div class="helper-text">Max 15MB. Supported: dwg, dxf, pdf, xls, doc, jpg, png.</div>
                </div>

                <div class="form-group" id="preview-section">
                    <label class="form-label" id="preview-label">Visual Preview (Watermarked)</label>
                    <div class="file-input-wrapper">
                        <div class="custom-file-btn" id="preview-file-label">
                            <i class="fas fa-image fa-2x"></i>
                            <span>Upload a screenshot/preview image</span>
                        </div>
                        <input type="file" name="preview" id="preview-file" accept="image/*">
                    </div>
                    <div class="helper-text">Required for CAD files so users can see what they're unlocking.</div>
                </div>

                <button type="submit" id="submit-btn" class="btn-upload-premium">
                    <i class="fas fa-rocket me-2"></i> TRANSMIT BLUEPRINT
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const fileType = document.getElementById('file-type');
const previewInput = document.getElementById('preview-file');
const previewLabel = document.getElementById('preview-label');
const mainFileInput = document.getElementById('main-file');
const mainFileLabel = document.querySelector('#main-file-label span');
const previewFileLabel = document.querySelector('#preview-file-label span');

fileType.addEventListener('change', () => {
    if (fileType.value === 'cad') {
        previewInput.required = true;
        previewLabel.innerHTML = 'Visual Preview <span style="color:var(--up-primary)">(REQUIRED FOR CAD)</span>';
    } else {
        previewInput.required = false;
        previewLabel.textContent = 'Visual Preview (Optional)';
    }
});

mainFileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        mainFileLabel.textContent = this.files[0].name;
    }
});

previewInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        previewFileLabel.textContent = this.files[0].name;
    }
});

document.getElementById('upload-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> TRANSMITTING...';
    btn.disabled = true;
    
    try {
        const formData = new FormData(this);
        formData.append('csrf_token', '<?php echo csrf_token(); ?>');
        
        const response = await fetch('<?php echo app_base_url("api/library/upload"); ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            btn.innerHTML = '<i class="fas fa-check"></i> SUCCESS! REDIRECTING...';
            btn.style.background = 'var(--up-success)';
            
            // Show alert for definitive feedback
            alert('Blueprint transmitted successfully to the vault! Redirecting to library...');
            
            window.location.href = '<?php echo app_base_url("/library"); ?>';
        } else {
            alert('Transmission Failed: ' + data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Upload Error:', error);
        alert('Signal Loss: Failed to connect to server.');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
});
</script>
