<?php

/**
 * Firebase Settings Page - Premium Design
 */
?>
<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<style>
    .firebase-settings-container {
        background: linear-gradient(135deg, #FF9A9E 0%, #FECFEF 100%);
        min-height: 100vh;
        padding: 2rem;
        font-family: 'Inter', sans-serif;
    }

    .settings-header {
        margin-bottom: 2rem;
        animation: slideDown 0.6s ease-out;
    }

    .settings-header h1 {
        font-size: 2.2rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .settings-header p {
        font-size: 1rem;
        color: #718096;
    }

    /* Cards */
    .settings-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: fadeInUp 0.6s ease-out;
    }

    .settings-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }

    .section-header {
        background: linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%);
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .section-header.client-side {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .section-icon {
        font-size: 1.5rem;
        background: rgba(255, 255, 255, 0.2);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .section-title-group h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
    }

    .section-title-group p {
        font-size: 0.85rem;
        opacity: 0.9;
        margin: 0;
    }

    .section-body {
        padding: 2rem;
    }

    /* Forms */
    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        transition: all 0.2s;
        font-size: 1rem;
        background: #f8fafc;
    }

    .form-control:focus {
        border-color: #ff758c;
        background: white;
        box-shadow: 0 0 0 3px rgba(255, 117, 140, 0.1);
        outline: none;
    }

    .btn-save {
        background: linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%);
        color: white;
        padding: 1rem 2rem;
        border-radius: 10px;
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(255, 117, 140, 0.4);
    }

    .btn-save.client {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .status-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
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
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="firebase-settings-container">
    <div class="settings-header">
        <h1>🔥 Firebase Configuration</h1>
        <p>Manage connection settings for Realtime Database & Authentication.</p>
    </div>

    <!-- 1. Service Account (Server Side) -->
    <div class="settings-section">
        <div class="section-header">
            <span class="section-icon"><i class="fas fa-server"></i></span>
            <div class="section-title-group">
                <h2>Service Account (Server Side)</h2>
                <p>Required for verifying tokens and backend operations.</p>
            </div>
        </div>
        <div class="section-body">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="mb-4">
                        <label class="form-label">Current Status</label>
                        <?php if (file_exists(BASE_PATH . '/config/firebase_credentials.json')): ?>
                            <span class="status-badge status-success">
                                <i class="fas fa-check-circle"></i> Service Account Active
                            </span>
                        <?php else: ?>
                            <span class="status-badge status-danger">
                                <i class="fas fa-exclamation-triangle"></i> Key Not Found
                            </span>
                        <?php endif; ?>
                    </div>

                    <form action="/admin/settings/firebase/upload" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <div class="form-group mb-4">
                            <label class="form-label">Upload New Key (.json)</label>
                            <input type="file" class="form-control" name="service_account" accept=".json" required style="padding: 0.5rem;">
                            <small class="text-muted mt-2 d-block">
                                Generate this in Firebase Console > Project Settings > Service Accounts.
                            </small>
                        </div>

                        <button type="submit" class="btn-save" style="width: auto; padding: 0.8rem 1.5rem;">
                            <i class="fas fa-upload mr-2"></i> Upload Key
                        </button>
                    </form>
                </div>
                <div class="col-md-5 text-center d-none d-md-block">
                    <i class="fas fa-file-code" style="font-size: 8rem; color: #fee2e2; transform: rotate(15deg);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Web Configuration (Client Side) -->
    <div class="settings-section">
        <div class="section-header client-side">
            <span class="section-icon"><i class="fas fa-globe"></i></span>
            <div class="section-title-group">
                <h2>Web Configuration (Client Side)</h2>
                <p>These public keys allow the frontend to connect.</p>
            </div>
        </div>
        <div class="section-body">
            <form action="/admin/settings/update" method="post" class="settings-form ajax-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="setting_group" value="firebase">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" class="form-control" name="firebase_apiKey" value="<?php echo $settings['firebase_apiKey'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Auth Domain</label>
                        <input type="text" class="form-control" name="firebase_authDomain" value="<?php echo $settings['firebase_authDomain'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Project ID</label>
                        <input type="text" class="form-control" name="firebase_projectId" value="<?php echo $settings['firebase_projectId'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Storage Bucket</label>
                        <input type="text" class="form-control" name="firebase_storageBucket" value="<?php echo $settings['firebase_storageBucket'] ?? ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Messaging Sender ID</label>
                        <input type="text" class="form-control" name="firebase_messagingSenderId" value="<?php echo $settings['firebase_messagingSenderId'] ?? ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">App ID</label>
                        <input type="text" class="form-control" name="firebase_appId" value="<?php echo $settings['firebase_appId'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label">Database URL (Realtime DB)</label>
                        <input type="text" class="form-control" name="firebase_databaseURL" value="<?php echo $settings['firebase_databaseURL'] ?? ''; ?>">
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn-save client">
                        <i class="fas fa-save mr-2"></i> Save Client Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // AJAX Save Handler for Client Settings
    document.querySelector('.ajax-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const origText = btn.innerHTML;

        btn.innerHTML = '<i class="fas fa-sync fa-spin mr-2"></i> Saving...';
        btn.disabled = true;

        try {
            const res = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            });
            const data = await res.json();

            if (data.success) {
                btn.innerHTML = '<i class="fas fa-check mr-2"></i> Saved!';
                setTimeout(() => {
                    btn.innerHTML = origText;
                    btn.disabled = false;
                }, 2000);
            } else {
                alert('Error: ' + data.message);
                btn.innerHTML = origText;
                btn.disabled = false;
            }
        } catch (e) {
            console.error(e);
            alert('Request failed');
            btn.innerHTML = origText;
            btn.disabled = false;
        }
    });
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>