<?php
// themes/default/views/library/upload.php
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap');

:root {
    --p-bg: #050505;
    --p-card: rgba(15, 15, 20, 0.4);
    --p-border: rgba(255, 255, 255, 0.08);
    --p-primary: #8b5cf6;
    --p-accent: #06b6d4;
    --p-success: #10b981;
    --p-text: #f3f4f6;
    --p-muted: #9ca3af;
    --p-glass: rgba(255, 255, 255, 0.03);
    --p-glow: rgba(139, 92, 246, 0.15);
}

.upload-page-wrapper {
    background: var(--p-bg);
    min-height: 100vh;
    color: var(--p-text);
    font-family: 'Outfit', sans-serif;
    position: relative;
    overflow-x: hidden;
    padding: 80px 20px;
}

/* Mesh Gradient Background */
.mesh-gradient {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    opacity: 0.6;
    pointer-events: none;
    filter: blur(100px);
}

.mesh-ball-1 {
    position: absolute;
    top: -10%;
    right: -10%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, var(--p-primary) 0%, transparent 70%);
    animation: drift 20s infinite alternate;
}

.mesh-ball-2 {
    position: absolute;
    bottom: -10%;
    left: -10%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, var(--p-accent) 0%, transparent 70%);
    animation: drift 25s infinite alternate-reverse;
}

@keyframes drift {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(100px, 50px) scale(1.1); }
}

.noise-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    opacity: 0.03;
    pointer-events: none;
    background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIj48ZmlsdGVyIGlkPSJuIj48ZmVUdXJidWxlbmNlIHR5cGU9ImZyYWN0YWxOb2lzZSIgYmFzZUZyZXF1ZW5jeT0iMC42NSIgbnVtT2N0YXZlcz0iMyIgc3RpdGNoVGlsZXM9InN0aXRjaCIvPjwvZmlsdGVyPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbHRlcj0idXJsKCNuKSIvPjwvc3ZnPg==');
}

.premium-container {
    position: relative;
    z-index: 2;
    max-width: 900px;
    margin: 0 auto;
}

.premium-header {
    text-align: center;
    margin-bottom: 50px;
}

.premium-header h1 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    letter-spacing: -2px;
    background: linear-gradient(to right, #fff, var(--p-muted));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 20px;
}

.reward-stripe {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: rgba(139, 92, 246, 0.1);
    border: 1px solid rgba(139, 92, 246, 0.2);
    padding: clamp(8px, 2vw, 12px) clamp(16px, 3vw, 24px);
    border-radius: 100px;
    color: var(--p-primary);
    font-weight: 700;
    font-size: 14px;
    backdrop-filter: blur(10px);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 24px;
}

.glass-field-card {
    background: var(--p-card);
    backdrop-filter: blur(40px);
    -webkit-backdrop-filter: blur(40px);
    border: 1px solid var(--p-border);
    border-radius: 28px;
    padding: 32px;
    box-shadow: 0 40px 100px rgba(0, 0, 0, 0.5);
    grid-column: span 12;
}

.input-container {
    margin-bottom: 24px;
}

.input-label {
    display: block;
    font-size: 12px;
    font-weight: 800;
    color: var(--p-muted);
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.input-premium {
    width: 100%;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--p-border);
    border-radius: 16px;
    padding: 16px 20px;
    color: #fff;
    font-size: 16px;
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}

.input-premium:focus {
    outline: none;
    border-color: var(--p-primary);
    background: rgba(255, 255, 255, 0.06);
    box-shadow: 0 0 20px var(--p-glow);
}

.input-premium::placeholder {
    color: rgba(255, 255, 255, 0.2);
}

.upload-zone {
    position: relative;
    border: 2px dashed var(--p-border);
    border-radius: 20px;
    padding: 40px 20px;
    text-align: center;
    transition: all 0.3s;
    background: rgba(255, 255, 255, 0.01);
    cursor: pointer;
}

.upload-zone:hover, .upload-zone.dragover {
    border-color: var(--p-primary);
    background: rgba(139, 92, 246, 0.05);
}

.upload-zone i {
    font-size: 40px;
    color: var(--p-primary);
    margin-bottom: 15px;
    transition: transform 0.3s;
}

.upload-zone:hover i {
    transform: translateY(-5px);
}

.upload-zone p {
    margin: 0;
    font-size: 14px;
    color: var(--p-muted);
}

.upload-zone span {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    opacity: 0.6;
}

.file-input-hidden {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.btn-transmit {
    width: 100%;
    background: linear-gradient(135deg, var(--p-primary), #6366f1);
    color: #fff;
    border: none;
    padding: 22px;
    border-radius: 20px;
    font-weight: 800;
    font-size: 18px;
    font-family: 'Space Grotesk', sans-serif;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.4s;
    box-shadow: 0 20px 40px rgba(139, 92, 246, 0.3);
    margin-top: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    position: relative;
    overflow: hidden;
}

.btn-transmit::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    transition: 0.5s;
}

.btn-transmit:hover:not(:disabled) {
    transform: translateY(-4px);
    box-shadow: 0 25px 50px rgba(139, 92, 246, 0.4);
}

.btn-transmit:hover::after {
    left: 100%;
}

.btn-transmit:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    box-shadow: none;
}

.status-indicator {
    display: none;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 700;
    margin-top: 10px;
}

.status-indicator.visible { display: flex; }
.status-success { color: var(--p-success); }
.status-error { color: #ef4444; }

.section-label {
    grid-column: span 12;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 20px;
    font-weight: 700;
    margin-top: 20px;
    background: linear-gradient(to right, #fff, transparent);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

@media (max-width: 768px) {
    .form-grid { gap: 16px; }
}
</style>

<div class="upload-page-wrapper">
    <div class="mesh-gradient">
        <div class="mesh-ball-1"></div>
        <div class="mesh-ball-2"></div>
    </div>
    <div class="noise-overlay"></div>

    <div class="premium-container">
        <header class="premium-header">
            <h1>Transmitter</h1>
            <?php 
                $reward = \App\Services\SettingsService::get('library_upload_reward', 100);
            ?>
            <div class="reward-stripe">
                <i class="fas fa-sparkles"></i>
                <span>EARN <?php echo $reward; ?> BB COINS PER APPROVED ASSET</span>
            </div>
        </header>

        <form id="upload-form" enctype="multipart/form-data">
            <div class="form-grid">
                
                <div class="glass-field-card">
                    <div class="input-container">
                        <label class="input-label">Project Identity <span style="color:var(--p-primary)">*</span></label>
                        <input type="text" name="title" required class="input-premium" placeholder="Enter a professional title...">
                    </div>

                    <div class="input-container" style="margin-bottom:0">
                        <label class="input-label">Core Specifications <span style="color:var(--p-primary)">*</span></label>
                        <textarea name="description" required class="input-premium" style="min-height: 120px;" placeholder="Describe the technical value of this submission..."></textarea>
                    </div>
                </div>

                <div class="section-label">Classification</div>

                <div class="glass-field-card" style="grid-column: span 6">
                    <label class="input-label">Category</label>
                    <select name="type" id="file-type" class="input-premium">
                        <option value="cad">AutoCAD (.dwg/.dxf)</option>
                        <option value="solidworks">SolidWorks (.sldprt/.sldasm)</option>
                        <option value="excel">Excel (.xlsx/.xlsm)</option>
                        <option value="pdf">PDF Documentation</option>
                        <option value="doc">Word / Text</option>
                        <option value="image">Image / Sketch</option>
                    </select>
                </div>

                <div class="glass-field-card" style="grid-column: span 6">
                    <label class="input-label">Unlock Price (BB)</label>
                    <input type="number" name="price" min="0" value="0" class="input-premium">
                </div>

                <div class="glass-field-card" style="grid-column: span 12">
                    <label class="input-label">Discovery Tags</label>
                    <input type="text" name="tags" id="hashtags-input" class="input-premium" placeholder="structural, villa, cad, architectural">
                    <p style="font-size:11px; color:var(--p-muted); margin:8px 0 0">Max 5 hashtags. Help users find your contribution.</p>
                </div>

                <div class="section-label">Encapsulated Assets</div>

                <div class="glass-field-card" style="grid-column: span 6">
                    <label class="input-label">Main Payload <span style="color:var(--p-primary)">*</span></label>
                    <div class="upload-zone" id="main-zone">
                        <input type="file" name="file" id="main-file" class="file-input-hidden" required>
                        <i class="fas fa-file-export"></i>
                        <p id="main-filename">Deposit Primary File</p>
                        <span>Max 15MB (.dwg, .pdf, .xlsx, .zip)</span>
                    </div>
                </div>

                <div class="glass-field-card" style="grid-column: span 6">
                    <label class="input-label" id="preview-label">Visual Preview</label>
                    <div class="upload-zone" id="preview-zone">
                        <input type="file" name="preview" id="preview-file" class="file-input-hidden" accept="image/*">
                        <i class="fas fa-image"></i>
                        <p id="preview-filename">Attach Snapshot</p>
                        <span>Highly recommended for CAD/Excel</span>
                    </div>
                </div>

                <div style="grid-column: span 12">
                    <button type="submit" id="submit-btn" class="btn-transmit">
                        <i class="fas fa-satellite-dish"></i>
                        <span>INITIALIZE TRANSMISSION</span>
                    </button>
                    
                    <div id="status-indicator" class="status-indicator">
                        <i class="fas fa-circle-notch fa-spin"></i>
                        <span id="status-text">Synchronizing with vault...</span>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
const fileType = document.getElementById('file-type');
const previewInput = document.getElementById('preview-file');
const previewLabel = document.getElementById('preview-label');
const mainFileInput = document.getElementById('main-file');
const hashtagsInput = document.getElementById('hashtags-input');

const mainZone = document.getElementById('main-zone');
const previewZone = document.getElementById('preview-zone');
const mainFilename = document.getElementById('main-filename');
const previewFilename = document.getElementById('preview-filename');

const statusIndicator = document.getElementById('status-indicator');
const statusText = document.getElementById('status-text');

const extensionMap = {
    'cad': ['dwg', 'dxf'],
    'solidworks': ['sldprt', 'sldasm'],
    'excel': ['xls', 'xlsx', 'xlsm'],
    'pdf': ['pdf'],
    'image': ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    'doc': ['doc', 'docx']
};

fileType.addEventListener('change', () => {
    if (fileType.value === 'cad' || fileType.value === 'solidworks') {
        previewInput.required = true;
        previewLabel.innerHTML = 'Visual Preview <span style="color:var(--p-primary)">(REQUIRED)</span>';
    } else {
        previewInput.required = false;
        previewLabel.textContent = 'Visual Preview';
    }
});

// Drag and drop visuals
[mainFileInput, previewInput].forEach(input => {
    const zone = input.parentElement;
    
    input.addEventListener('dragenter', () => zone.classList.add('dragover'));
    input.addEventListener('dragleave', () => zone.classList.remove('dragover'));
    input.addEventListener('drop', () => zone.classList.remove('dragover'));
});

mainFileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        mainFilename.textContent = this.files[0].name;
        mainFilename.style.color = 'var(--p-primary)';
        mainFilename.style.fontWeight = '700';
    } else {
        mainFilename.textContent = 'Deposit Primary File';
    }
});

previewInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        previewFilename.textContent = this.files[0].name;
        previewFilename.style.color = 'var(--p-primary)';
        previewFilename.style.fontWeight = '700';
    } else {
        previewFilename.textContent = 'Attach Snapshot';
    }
});

document.getElementById('upload-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // 1. Validate Hashtags
    const tags = hashtagsInput.value.split(',').map(t => t.trim()).filter(t => t !== '');
    if (tags.length > 5) {
        showStatus('Error: Payload exceeds 5-tag discovery limit.', 'error');
        return;
    }

    // 2. Validate File Extension Match
    const selectedType = fileType.value;
    const file = mainFileInput.files[0];
    if (file) {
        const ext = file.name.split('.').pop().toLowerCase();
        const allowed = extensionMap[selectedType];
        if (allowed && !allowed.includes(ext)) {
            showStatus(`Error: Protocol mismatch for ${selectedType}. Expected: ${allowed.join(', ')}`, 'error');
            return;
        }
    }

    const btn = document.getElementById('submit-btn');
    
    btn.disabled = true;
    showStatus('Uplink active. Transmitting blueprint payload...', 'loading');
    
    try {
        const formData = new FormData(this);
        formData.append('csrf_token', '<?php echo csrf_token(); ?>');
        
        const response = await fetch('<?php echo app_base_url("api/library/upload"); ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showStatus('Asset secured. Vault synchronization complete.', 'success');
            setTimeout(() => {
                window.location.href = '<?php echo app_base_url("/library"); ?>';
            }, 1500);
        } else {
            showStatus('Uplink terminated: ' + data.message, 'error');
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Upload Error:', error);
        showStatus('Signal Loss: Vault connection disrupted.', 'error');
        btn.disabled = false;
    }
});

function showStatus(msg, type) {
    statusIndicator.className = 'status-indicator visible';
    statusText.textContent = msg;
    
    const icon = statusIndicator.querySelector('i');
    
    if (type === 'loading') {
        icon.className = 'fas fa-circle-notch fa-spin';
        statusIndicator.style.color = '#fff';
    } else if (type === 'success') {
        icon.className = 'fas fa-check-circle';
        statusIndicator.style.color = 'var(--p-success)';
    } else {
        icon.className = 'fas fa-exclamation-triangle';
        statusIndicator.style.color = '#ef4444';
    }
}
</script>
