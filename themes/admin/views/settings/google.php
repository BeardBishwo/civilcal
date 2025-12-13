<?php
$content = '
<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fab fa-google"></i>
            Login with Google
        </h1>
        <p class="page-description">Configure Google OAuth login settings</p>
    </div>

    <div class="card">
        <div class="card-content">
            <form action="' . app_base_url('admin/settings/update') . '" method="POST" class="settings-form">
                <input type="hidden" name="csrf_token" value="' . ($_SESSION['csrf_token'] ?? '') . '">
                
                <div class="setting-item">
                    <div class="setting-info">
                        <label class="setting-label">Login with Google</label>
                        <p class="setting-description">Users can login and get registered using their google account.</p>
                    </div>
                    <div class="setting-control">
                        <label class="switch">
                            <input type="checkbox" name="google_login_enabled" value="1" ' . (($settings['google_login_enabled'] ?? '') == '1' ? 'checked' : '') . '>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label class="form-label">Google Client ID</label>
                    <input type="text" name="google_client_id" class="form-control" value="' . htmlspecialchars($settings['google_client_id'] ?? '') . '">
                </div>

                <div class="form-group">
                    <label class="form-label">Google Client Secret</label>
                    <input type="text" name="google_client_secret" class="form-control" value="' . htmlspecialchars($settings['google_client_secret'] ?? '') . '">
                </div>

                <div class="form-group">
                    <label class="form-label">Google Callback URL</label>
                    <input type="text" class="form-control" value="' . app_base_url('user/login/google') . '" readonly style="background-color: #f3f4f6;">
                    <small class="form-text text-muted">Please use the link above as the authorized callback URL in your Google Cloud Console.</small>
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
.alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
.alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
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
</style>
';

$breadcrumbs = [
    ['title' => 'Settings', 'url' => app_base_url('admin/settings')],
    ['title' => 'Google Login']
];

$page_title = 'Google Login Settings - Admin Panel';
$currentPage = 'settings';

include __DIR__ . '/../../layouts/main.php';
?>
