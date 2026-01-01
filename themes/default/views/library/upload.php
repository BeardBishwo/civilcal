<?php
// themes/default/views/library/upload.php
?>
<style>
    .up-shell { max-width: 960px; margin: 0 auto; padding: 32px 16px; color: #e8edf5; }
    .up-card { background: rgba(21,26,38,0.78); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.35); padding: 24px; }
    .up-label { display: block; margin-bottom: 6px; font-size: 13px; color: #cbd5e1; font-weight: 600; }
    .up-input, .up-textarea, .up-select, .up-file { width: 100%; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #e8edf5; border-radius: 10px; padding: 12px 14px; font-size: 14px; }
    .up-textarea { min-height: 100px; resize: vertical; }
    .up-helper { font-size: 12px; color: #9aa7b8; margin-top: 4px; }
    .up-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap: 14px; }
    .up-banner { background: linear-gradient(90deg,#0ea5e9,#6366f1); border-radius: 14px; padding: 14px; border: 1px solid rgba(255,255,255,0.2); margin-bottom: 16px; }
    .up-btn-primary { background: #22c55e; color: #0b1b2c; border: none; border-radius: 12px; padding: 14px 16px; font-weight: 700; width: 100%; cursor: pointer; box-shadow: 0 12px 30px rgba(34,197,94,0.35); }
    .up-btn-primary:hover { background: #16a34a; }
    .up-required { color: #f87171; margin-left: 4px; }
</style>
<div class="up-shell">
    <div class="up-banner">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <div>
                <div style="font-size:12px; color:#dbeafe;">Earn Coins</div>
                <div style="font-size:18px; font-weight:700; color:#fff;">Upload premium resources to earn 100 coins per approved file</div>
                <div style="font-size:12px; color:#e2e8f0; margin-top:2px;">Files are reviewed by admins before publishing.</div>
            </div>
            <div style="background: rgba(255,255,255,0.16); border:1px solid rgba(255,255,255,0.25); border-radius:12px; padding:10px 14px; color:#fff; font-weight:700;">
                Max size 15MB • Allowed: dwg, dxf, pdf, xlsx, xlsm, docx, jpg, png
            </div>
        </div>
    </div>

    <div class="up-card">
        <h1 style="margin:0 0 12px; font-size:26px; font-weight:800; color:#fff;">Upload Resource</h1>
        <form id="upload-form" class="space-y-4">
            <div>
                <label class="up-label">Resource Title <span class="up-required">*</span></label>
                <input type="text" name="title" required class="up-input" placeholder="e.g., 2-Storey House Plan 30x40">
            </div>

            <div>
                <label class="up-label">Description</label>
                <textarea name="description" class="up-textarea" placeholder="Describe contents, units, scale, etc."></textarea>
            </div>

            <div class="up-grid">
                <div>
                    <label class="up-label">Category</label>
                    <select name="type" id="file-type" class="up-select">
                        <option value="cad">AutoCAD / DWG</option>
                        <option value="excel">Excel Sheet</option>
                        <option value="pdf">PDF Document</option>
                        <option value="doc">Word Document</option>
                        <option value="image">Image / Sketch</option>
                    </select>
                </div>
                <div>
                    <label class="up-label">File <span class="up-required">*</span></label>
                    <input type="file" name="file" required class="up-file">
                    <div class="up-helper">Max 15MB. dwg, dxf, pdf, xlsx, xlsm, docx, jpg, png.</div>
                </div>
            </div>

            <div>
                <label class="up-label">Price (Coins) <span class="up-helper" style="font-weight:400;">Set to 0 for free</span></label>
                <input type="number" name="price" min="0" value="0" class="up-input">
                <div class="up-helper">Files priced &gt; 0 will require users to pay to unlock. You earn commission.</div>
            </div>

            <div id="preview-section" class="up-card" style="background: rgba(255,255,255,0.03); border-style:dashed; margin-top:6px;">
                <label class="up-label">Preview Image (Optional - Watermarked)</label>
                <input type="file" name="preview" id="preview-file" accept="image/*" class="up-file">
                <div class="up-helper">Upload a JPG/PNG screenshot so users can preview (required for CAD).</div>
            </div>

            <button type="submit" id="submit-btn" class="up-btn-primary">🚀 Upload Resource</button>
        </form>
    </div>
</div>

<script>
const fileType = document.getElementById('file-type');
const previewInput = document.getElementById('preview-file');
const previewLabel = document.querySelector('#preview-section label');

fileType.addEventListener('change', () => {
    if (fileType.value === 'cad') {
        previewInput.required = true;
        previewLabel.textContent = 'Preview Image (Required for CAD)';
    } else {
        previewInput.required = false;
        previewLabel.textContent = 'Preview Image (Optional - Watermarked)';
    }
});

document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerText = 'Uploading...';

    const formData = new FormData(this);

    fetch('/api/library/upload', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        let payload;
        try {
            payload = await response.clone().json();
        } catch (_) {
            const text = await response.clone().text();
            throw new Error(text || 'Upload failed (non-JSON response)');
        }
        if (!response.ok || !payload.success) {
            throw new Error(payload.message || `Upload failed (HTTP ${response.status})`);
        }
        alert('Upload successful! Your file is under review.');
        window.location.href = '/library';
    })
    .catch(err => {
        console.error(err);
        alert('Upload failed: ' + err.message);
        btn.disabled = false;
        btn.innerText = originalText;
    });
});
</script>
