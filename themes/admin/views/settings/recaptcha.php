<?php
$content = '
<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-robot"></i>
            Captcha Settings
        </h1>
        <p class="page-description">Configure captcha provider and keys</p>
    </div>

    <div class="card">
        <div class="card-content">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Before you logout, make sure you are using the correct captcha keys otherwise you will be locked out of your account and would not be able to login unless you directly disable captcha via the database.
            </div>

            <form action="' . app_base_url('admin/settings/update') . '" method="POST" class="settings-form">
                <input type="hidden" name="csrf_token" value="' . ($_SESSION['csrf_token'] ?? '') . '">
                
                <div class="form-group mt-3">
                    <label class="form-label">Captcha Provider</label>
                    <select name="captcha_provider" class="form-control">
                        <option value="recaptcha_v3" ' . (($settings['captcha_provider'] ?? '') == 'recaptcha_v3' ? 'selected' : '') . '>reCaptcha v3</option>
                        <option value="recaptcha_v2" ' . (($settings['captcha_provider'] ?? '') == 'recaptcha_v2' ? 'selected' : '') . '>reCaptcha v2</option>
                        <option value="hcaptcha" ' . (($settings['captcha_provider'] ?? '') == 'hcaptcha' ? 'selected' : '') . '>hCaptcha</option>
                        <option value="turnstile" ' . (($settings['captcha_provider'] ?? '') == 'turnstile' ? 'selected' : '') . '>Cloudflare Turnstile</option>
                    </select>
                </div>
                
                <p class="text-muted small mb-4">
                    Users will be prompted to answer a captcha before processing their request. If you enable any of the captcha make sure to add your keys as well. To enable hCaptcha or Turnstile, add your "Site Key" in the Public Key field below and your "Secret Key" in the Private Key below. Altcha does not require any keys but requires PHP 8.2 or higher.
                </p>

                <div class="form-group">
                    <label class="form-label">Public Key</label>
                    <input type="text" name="recaptcha_site_key" class="form-control" value="' . htmlspecialchars($settings['recaptcha_site_key'] ?? '') . '">
                    <small class="form-text text-muted">For reCaptcha, you can get your public key for free from <a href="https://www.google.com/recaptcha" target="_blank">Google</a></small>
                </div>

                <div class="form-group">
                    <label class="form-label">Private Key</label>
                    <input type="text" name="recaptcha_secret_key" class="form-control" value="' . htmlspecialchars($settings['recaptcha_secret_key'] ?? '') . '">
                    <small class="form-text text-muted">For reCaptcha, you can get your private key for free from <a href="https://www.google.com/recaptcha" target="_blank">Google</a></small>
                </div>
                
                <div class="setting-item mt-4">
                    <div class="setting-info">
                        <label class="setting-label">Enable Captcha on Login</label>
                        <p class="setting-description">Show captcha on login form</p>
                    </div>
                    <div class="setting-control">
                        <label class="switch">
                            <input type="checkbox" name="captcha_on_login" value="1" ' . (($settings['captcha_on_login'] ?? '') == '1' ? 'checked' : '') . '>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-info">
                        <label class="setting-label">Enable Captcha on Registration</label>
                        <p class="setting-description">Show captcha on registration form</p>
                    </div>
                    <div class="setting-control">
                        <label class="switch">
                            <input type="checkbox" name="captcha_on_register" value="1" ' . (($settings['captcha_on_register'] ?? '') == '1' ? 'checked' : '') . '>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector(".settings-form");
    
    form.addEventListener("submit", async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector("button[type=\'submit\']");
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = "<i class=\'fas fa-spinner fa-spin\'></i> Saving...";
        submitBtn.disabled = true;
        
        try {
            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: "POST",
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage(result.message, "success");
            } else {
                showMessage(result.message || "Error saving settings", "error");
            }
        } catch (error) {
            showMessage("An error occurred: " + error.message, "error");
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
});

function showMessage(message, type) {
    const messageEl = document.createElement("div");
    messageEl.className = "alert alert-" + type;
    messageEl.textContent = message;
    messageEl.style.cssText = "position:fixed; top:20px; right:20px; padding:15px; border-radius:5px; z-index:9999; box-shadow: 0 4px 12px rgba(0,0,0,0.1);";
    
    if (type === "success") {
        messageEl.style.backgroundColor = "#d4edda";
        messageEl.style.color = "#155724";
        messageEl.style.border = "1px solid #c3e6cb";
    } else {
        messageEl.style.backgroundColor = "#f8d7da";
        messageEl.style.color = "#721c24";
        messageEl.style.border = "1px solid #f5c6cb";
    }
    
    document.body.appendChild(messageEl);
    
    setTimeout(() => {
        document.body.removeChild(messageEl);
    }, 5000);
}
</script>

<style>
.mt-3 { margin-top: 1rem; }
.mt-4 { margin-top: 1.5rem; }
.alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; display: flex; align-items: flex-start; gap: 10px; }
.alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
.alert i { margin-top: 4px; }
.setting-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #eee; }
.setting-info { flex: 1; padding-right: 20px; }
.setting-label { display: block; font-weight: 600; margin-bottom: 0.25rem; }
.setting-description { color: #666; font-size: 0.9rem; margin: 0; }
.switch { position: relative; display: inline-block; width: 50px; height: 26px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: var(--admin-primary); }
input:focus + .slider { box-shadow: 0 0 1px var(--admin-primary); }
input:checked + .slider:before { transform: translateX(24px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }
.form-actions { margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #eee; }
.small { font-size: 0.875rem; }
.text-muted { color: #6c757d; }
.mb-4 { margin-bottom: 1.5rem; }
</style>
';

$breadcrumbs = [
    ['title' => 'Settings', 'url' => app_base_url('admin/settings')],
    ['title' => 'Captcha Settings']
];

$page_title = 'Captcha Settings - Admin Panel';
$currentPage = 'settings';

include __DIR__ . '/../../layouts/main.php';
?>
