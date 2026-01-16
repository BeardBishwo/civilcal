<?php $this->layout('admin/layouts/main', ['title' => $title]); ?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Firebase Configuration</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/admin/settings">Settings</a></li>
                    <li class="breadcrumb-item active">Firebase</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 1. Service Account (Server Side) -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1 text-danger">Firebase Service Account (Server Side)</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Instructions:</strong>
                    <ol class="mb-0">
                        <li>Go to <a href="https://console.firebase.google.com/project/_/settings/serviceaccounts/adminsdk" target="_blank">Firebase Console > Project Settings > Service Accounts</a>.</li>
                        <li>Click "Generate New Private Key".</li>
                        <li>Upload the generated <code>.json</code> file below.</li>
                    </ol>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <form action="/admin/settings/firebase/upload" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="mb-3">
                                <label class="form-label">Current Status</label>
                                <div>
                                    <?php if (file_exists(BASE_PATH . '/config/firebase_credentials.json')): ?>
                                        <span class="badge bg-success fs-6">
                                            <i class="ri-check-double-line align-middle me-1"></i> File Exists
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger fs-6">
                                            <i class="ri-error-warning-line align-middle me-1"></i> Not Found
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="service_account" class="form-label">Update Credentials JSON</label>
                                <input type="file" class="form-control" name="service_account" id="service_account" accept=".json" required>
                                <p class="text-muted small mt-1">Only .json files allowed.</p>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-danger">
                                    <i class="ri-upload-cloud-line align-middle me-1"></i> Upload Key
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="https://firebase.google.com/static/images/brand-guidelines/logo-vertical.png" alt="Firebase" style="max-height: 150px; opacity: 0.8;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Web Configuration (Client Side) -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1 text-danger">System Settings for Web (Client Side)</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">These values are required for the frontend (Lobby, Auth, etc.) to connect to Firebase.</p>

                <form action="/admin/settings/update" method="post" class="settings-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="setting_group" value="firebase">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">apiKey <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="firebase_apiKey" value="<?php echo $settings['firebase_apiKey'] ?? ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">authDomain <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="firebase_authDomain" value="<?php echo $settings['firebase_authDomain'] ?? ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">databaseURL (Optional)</label>
                                <input type="text" class="form-control" name="firebase_databaseURL" value="<?php echo $settings['firebase_databaseURL'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">projectId <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="firebase_projectId" value="<?php echo $settings['firebase_projectId'] ?? ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">storageBucket</label>
                                <input type="text" class="form-control" name="firebase_storageBucket" value="<?php echo $settings['firebase_storageBucket'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">messagingSenderId</label>
                                <input type="text" class="form-control" name="firebase_messagingSenderId" value="<?php echo $settings['firebase_messagingSenderId'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">appId <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="firebase_appId" value="<?php echo $settings['firebase_appId'] ?? ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">measurementId (Optional)</label>
                                <input type="text" class="form-control" name="firebase_measurementId" value="<?php echo $settings['firebase_measurementId'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-danger btn-lg w-100">
                            <i class="ri-save-line align-middle me-1"></i> Save Firebase Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple Auto-Save or AJAX handler if needed, usually global settings.js handles this
    document.querySelector('.settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line ri-spin align-middle me-1"></i> Saving...';

        fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Request failed', 'error'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    });
</script>