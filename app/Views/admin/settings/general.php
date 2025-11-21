<?php

/**
 * General Settings Page
 */
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">General Settings</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Site Configuration</h6>
                </div>
                <div class="card-body">
                    <form action="/admin/settings/save" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'Bishwo Calculator') ?>">
                            <div class="form-text">The name of your website as it appears in the browser title and emails.</div>
                        </div>

                        <div class="mb-3">
                            <label for="site_description" class="form-label">Site Description</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
                            <div class="form-text">A brief description of your website for SEO purposes.</div>
                        </div>

                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Admin Email</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?= htmlspecialchars($settings['admin_email'] ?? '') ?>">
                            <div class="form-text">The email address for system notifications and admin contact.</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="maintenance_mode" name="maintenance_mode" value="1" <?= ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                            <div class="form-text">Enable to show a maintenance page to visitors (admins can still access the site).</div>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>