<?php require_once BASE_PATH . '/themes/default/views/partials/header.php'; ?>

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --card-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    --input-focus: #667eea;
}

.report-section {
    padding: 80px 0;
    background: #f8fafc;
    min-height: calc(100vh - 400px);
}

.report-container {
    max-width: 1000px;
    margin: 0 auto;
}

.report-card {
    background: #ffffff !important;
    border-radius: 24px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    display: flex;
    flex-wrap: wrap;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.report-form-side {
    flex: 1;
    min-width: 350px;
    padding: 40px;
}

.report-info-side {
    width: 350px;
    background: var(--primary-gradient);
    padding: 40px;
    color: #ffffff;
    display: flex;
    flex-direction: column;
}

.report-title {
    font-size: 2.25rem;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
}

.report-subtitle {
    color: #64748b;
    font-size: 1.1rem;
    margin-bottom: 2.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

/* 
 * AGGRESSIVE OVERRIDES FOR GLOBAL THEME CONFLICTS
 * Force Light Mode behavior within report-card
 */
div.report-card label,
div.report-card .form-label {
    display: block !important;
    font-weight: 800 !important;
    color: #000000 !important;
    margin-bottom: 0.6rem !important;
    font-size: 0.95rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.025em !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Force Input visibility regardless of global theme.css !important rules */
div.report-card .form-control,
div.report-card input.form-control,
div.report-card textarea.form-control {
    width: 100% !important;
    padding: 0.8rem 1.25rem !important;
    border: 2px solid #e2e8e9 !important;
    border-radius: 12px !important;
    font-size: 1rem !important;
    transition: all 0.3s ease !important;
    background-color: #ffffff !important;
    background-image: none !important;
    color: #1a202c !important;
    box-shadow: none !important;
}

div.report-card .form-control:focus {
    border-color: var(--input-focus) !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
    outline: none !important;
}

/* Fix Muted/Small text visibility - Global .text-muted is often too light */
div.report-card .text-muted,
div.report-card small,
div.report-card .file-upload-text {
    color: #475569 !important;
    font-weight: 500 !important;
    opacity: 1 !important;
}

/* Placeholder refinement */
div.report-card .form-control::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
}

.btn-report {
    background: var(--primary-gradient);
    color: #ffffff;
    padding: 1rem 2rem;
    border-radius: 12px;
    border: none;
    font-weight: 700;
    font-size: 1.1rem;
    width: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-report:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}

/* File Upload Premium Styling */
.file-upload-wrapper {
    position: relative;
    width: 100%;
    height: 120px;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    background: #f8fafc;
    cursor: pointer;
    overflow: hidden;
}

.file-upload-wrapper:hover {
    border-color: var(--input-focus);
    background: rgba(102, 126, 234, 0.05);
}

.file-upload-icon {
    font-size: 2rem;
    color: #64748b;
    margin-bottom: 0.5rem;
}

/* Priority Styling Refinement */
.priority-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

.priority-option input {
    display: none;
}

.priority-label {
    display: block !important;
    padding: 0.7rem !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 10px !important;
    text-align: center !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    font-size: 0.8rem !important;
    font-weight: 700 !important;
    color: #475569 !important;
    background-color: #ffffff !important;
    text-transform: uppercase !important;
}

.priority-option input:checked + .priority-label {
    border-color: #667eea !important;
    background: var(--primary-gradient) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
}

.info-item {
    margin-bottom: 2rem;
}

.info-icon {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.info-text h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.info-text p {
    opacity: 0.8;
    font-size: 0.95rem;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .report-info-side {
        width: 100%;
        order: -1;
    }
    .priority-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<section class="report-section">
    <div class="container report-container">
        <div class="report-card">
            <!-- Form Side -->
            <div class="report-form-side">
                <h1 class="report-title">Report an Issue</h1>
                <p class="report-subtitle">Tell us what's wrong and we'll fix it as soon as possible.</p>

                <form id="reportForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="link" class="form-label">Calculator Link</label>
                        <input type="url" id="link" name="link" class="form-control" placeholder="<?= app_base_url('/calculator/...') ?>" required>
                        <small class="text-muted">Only links from <strong><?= parse_url(app_base_url(), PHP_URL_HOST) ?></strong> are allowed.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Priority Level</label>
                        <div class="priority-grid">
                            <label class="priority-option">
                                <input type="radio" name="priority" value="low">
                                <span class="priority-label">Low</span>
                            </label>
                            <label class="priority-option">
                                <input type="radio" name="priority" value="medium" checked>
                                <span class="priority-label">Medium</span>
                            </label>
                            <label class="priority-option">
                                <input type="radio" name="priority" value="high">
                                <span class="priority-label">High</span>
                            </label>
                            <label class="priority-option">
                                <input type="radio" name="priority" value="urgent">
                                <span class="priority-label">Urgent</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message" class="form-label">Issue Details</label>
                        <textarea id="message" name="message" class="form-control" rows="4" placeholder="Describe what's not working..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Screenshot (Optional)</label>
                        <div class="file-upload-wrapper" id="uploadWrapper">
                            <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                            <span class="file-upload-text">Drag & drop or click to upload</span>
                            <input type="file" id="screenshot" name="screenshot" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="preview-container" id="previewContainer">
                            <img src="" class="preview-image" id="previewImage">
                            <button type="button" class="remove-preview" id="removePreview">&times;</button>
                        </div>
                        <small class="text-muted">Max file size: 5MB. Formats: JPG, PNG, WebP.</small>
                    </div>

                    <button type="submit" class="btn-report" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        <span>Submit Report</span>
                    </button>
                </form>
            </div>

            <!-- Info Side -->
            <div class="report-info-side">
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-bug"></i></div>
                    <div class="info-text">
                        <h3>Technical Bug?</h3>
                        <p>Provide the calculator link and a screenshot of the errors or incorrect results you're seeing.</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-bolt"></i></div>
                    <div class="info-text">
                        <h3>Fast Response</h3>
                        <p>Our team reviews all reports within 24 hours. Urgent priority tickets are handled with preference.</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="info-text">
                        <h3>Secure & Private</h3>
                        <p>Your data and screenshots are stored securely and used only to diagnose and fix reported issues.</p>
                    </div>
                </div>

                <div style="margin-top: auto;">
                    <p style="font-size: 0.85rem; opacity: 0.7;">
                        By submitting this form, you help us maintain the accuracy and quality of Bishwo Calculator. Thank you!
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reportForm');
    const screenshotInput = document.getElementById('screenshot');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const removePreview = document.getElementById('removePreview');
    const uploadWrapper = document.getElementById('uploadWrapper');
    const submitBtn = document.getElementById('submitBtn');

    // Screenshot Preview Logic
    screenshotInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
                uploadWrapper.style.display = 'none';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    removePreview.addEventListener('click', function() {
        screenshotInput.value = '';
        previewContainer.style.display = 'none';
        uploadWrapper.style.display = 'flex';
    });

    // Form Submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Submitting...</span>';

        const formData = new FormData(this);

        try {
            const response = await fetch('<?= app_base_url('/report/submit') ?>', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Report Submitted!',
                    text: result.message,
                    confirmButtonColor: '#667eea'
                }).then(() => {
                    form.reset();
                    removePreview.click();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: result.message,
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Could not connect to the server. Please check your internet.',
                confirmButtonColor: '#ef4444'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
});
</script>

<?php require_once BASE_PATH . '/themes/default/views/partials/footer.php'; ?>
