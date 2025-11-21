<?php

/**
 * Security Settings Page
 */
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Security Settings</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Security & Authentication</h6>
                </div>
                <div class="card-body">
                    <form action="/admin/settings/save" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="enable_2fa" name="enable_2fa" value="1" <?= ($settings['enable_2fa'] ?? '0') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="enable_2fa">Enable Two-Factor Authentication (2FA)</label>
                            <div class="form-text">Require 2FA for all admin accounts.</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="force_https" name="force_https" value="1" <?= ($settings['force_https'] ?? '0') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="force_https">Force HTTPS</label>
                            <div class="form-text">Redirect all HTTP traffic to HTTPS.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password_min_length" class="form-label">Minimum Password Length</label>
                            <input type="number" class="form-control" id="password_min_length" name="password_min_length" value="<?= htmlspecialchars($settings['password_min_length'] ?? '8') ?>" min="6">
                        </div>

                        <div class="mb-3">
                            <label for="password_complexity" class="form-label">Password Complexity</label>
                            <select class="form-select" id="password_complexity" name="password_complexity">
                                <option value="low" <?= ($settings['password_complexity'] ?? '') == 'low' ? 'selected' : '' ?>>Low (Letters & Numbers)</option>
                                <option value="medium" <?= ($settings['password_complexity'] ?? '') == 'medium' ? 'selected' : '' ?>>Medium (Mixed Case & Numbers)</option>
                                <option value="high" <?= ($settings['password_complexity'] ?? '') == 'high' ? 'selected' : '' ?>>High (Special Characters required)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                            <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="<?= htmlspecialchars($settings['session_timeout'] ?? '120') ?>" min="5">
                            <div class="form-text">Automatically log out users after inactivity.</div>
                        </div>

                        <div class="mb-3">
                            <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                            <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" value="<?= htmlspecialchars($settings['max_login_attempts'] ?? '5') ?>" min="3">
                            <div class="form-text">Lock account after this many failed attempts.</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="ip_whitelist_enabled" name="ip_whitelist_enabled" value="1" <?= ($settings['ip_whitelist_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="ip_whitelist_enabled">Enable IP Whitelisting</label>
                            <div class="form-text">Only allow admin access from specific IP addresses.</div>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>