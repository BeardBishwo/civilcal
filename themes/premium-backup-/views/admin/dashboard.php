<?php
/**
 * Premium Theme Admin Dashboard
 * 
 * Main admin interface for premium theme management
 * 
 * @package PremiumTheme\Admin
 * @version 1.0.0
 */

require_once __DIR__ . '/../../../../app/Services/PremiumThemeManager.php';

// Get theme manager instance
$themeManager = $themeManager ?? new PremiumThemeManager();
$activeLicense = $themeManager->getActiveLicense();
$themeSettings = $themeManager->getThemeSettings('premium');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Theme Admin - Dashboard</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --white: #ffffff;
            --border-radius: 8px;
            --box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-header {
            background: var(--white);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--gray-900);
        }

        .admin-nav {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .nav-link {
            padding: 10px 20px;
            background: var(--white);
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            text-decoration: none;
            color: var(--gray-700);
            transition: var(--transition);
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: var(--white);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--gray-900);
        }

        .license-status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
        }

        .license-status.active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }

        .license-status.inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }

        .settings-section {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--gray-700);
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-success {
            background: var(--success-color);
            color: var(--white);
        }

        .btn-warning {
            background: var(--warning-color);
            color: var(--white);
        }

        .btn-danger {
            background: var(--danger-color);
            color: var(--white);
        }

        .theme-preview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .theme-skin {
            padding: 15px;
            border: 2px solid var(--gray-300);
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .theme-skin:hover,
        .theme-skin.active {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }

        .skin-preview {
            width: 60px;
            height: 40px;
            border-radius: 4px;
            margin: 0 auto 10px;
            border: 1px solid var(--gray-300);
        }

        .analytics-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .stat {
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 12px;
            color: var(--gray-500);
            text-transform: uppercase;
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 10px;
            }
            
            .admin-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .admin-nav {
                flex-wrap: wrap;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Premium Theme Admin</h1>
            <div>
                <a href="?tab=settings" class="nav-link <?= !isset($_GET['tab']) || $_GET['tab'] === 'settings' ? 'active' : '' ?>">Settings</a>
                <a href="?tab=licenses" class="nav-link <?= $_GET['tab'] === 'licenses' ? 'active' : '' ?>">Licenses</a>
                <a href="?tab=analytics" class="nav-link <?= $_GET['tab'] === 'analytics' ? 'active' : '' ?>">Analytics</a>
            </div>
        </div>

        <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'settings'): ?>
        <div class="dashboard-grid">
            <div class="card">
                <h2 class="card-title">License Status</h2>
                <div class="license-status <?= $activeLicense && $activeLicense['status'] === 'active' ? 'active' : 'inactive' ?>">
                    <span style="font-size: 20px;">
                        <?= $activeLicense && $activeLicense['status'] === 'active' ? '✅' : '❌' ?>
                    </span>
                    <div>
                        <strong><?= $activeLicense ? ucfirst($activeLicense['status']) : 'No License' ?></strong><br>
                        <small>
                            <?php if ($activeLicense): ?>
                                Plan: <?= ucfirst($activeLicense['plan']) ?><br>
                                Expires: <?= $activeLicense['expires_at'] ? date('Y-m-d', strtotime($activeLicense['expires_at'])) : 'Never' ?>
                            <?php else: ?>
                                No active license found
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                <?php if ($activeLicense && $activeLicense['status'] === 'active'): ?>
                    <button class="btn btn-warning" onclick="validateLicense()">Validate License</button>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2 class="card-title">Theme Customization</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_theme_settings">
                    
                    <div class="form-group">
                        <label class="form-label">Primary Color</label>
                        <input type="color" name="primary_color" class="form-control" 
                               value="<?= htmlspecialchars($themeSettings['primary_color'] ?? '#2563eb') ?>"
                               onchange="updateColorPreview()">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Dark Mode</label>
                        <select name="dark_mode" class="form-control">
                            <option value="0" <?= ($themeSettings['dark_mode'] ?? '0') === '0' ? 'selected' : '' ?>>Light Mode</option>
                            <option value="1" <?= ($themeSettings['dark_mode'] ?? '0') === '1' ? 'selected' : '' ?>>Dark Mode</option>
                            <option value="auto" <?= ($themeSettings['dark_mode'] ?? 'auto') === 'auto' ? 'selected' : '' ?>>Auto (System)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Animation Speed</label>
                        <select name="animation_speed" class="form-control">
                            <option value="fast" <?= ($themeSettings['animation_speed'] ?? 'normal') === 'fast' ? 'selected' : '' ?>>Fast</option>
                            <option value="normal" <?= ($themeSettings['animation_speed'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Normal</option>
                            <option value="slow" <?= ($themeSettings['animation_speed'] ?? 'normal') === 'slow' ? 'selected' : '' ?>>Slow</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>

            <div class="card">
                <h2 class="card-title">Calculator Skins</h2>
                <p>Choose the default calculator skin for this theme:</p>
                <div class="theme-preview">
                    <div class="theme-skin <?= ($themeSettings['calculator_skin'] ?? 'default') === 'default' ? 'active' : '' ?>" 
                         onclick="setCalculatorSkin('default')">
                        <div class="skin-preview" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                        <strong>Default</strong>
                    </div>
                    <div class="theme-skin <?= ($themeSettings['calculator_skin'] ?? 'default') === 'minimal' ? 'active' : '' ?>" 
                         onclick="setCalculatorSkin('minimal')">
                        <div class="skin-preview" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></div>
                        <strong>Minimal</strong>
                    </div>
                    <div class="theme-skin <?= ($themeSettings['calculator_skin'] ?? 'default') === 'professional' ? 'active' : '' ?>" 
                         onclick="setCalculatorSkin('professional')">
                        <div class="skin-preview" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);"></div>
                        <strong>Professional</strong>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['tab']) && $_GET['tab'] === 'licenses'): ?>
        <div class="card">
            <h2 class="card-title">License Management</h2>
            
            <h3>Current License</h3>
            <?php if ($activeLicense): ?>
                <div style="background: var(--gray-50); padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px;">
                    <p><strong>License Key:</strong> <?= htmlspecialchars($activeLicense['license_key']) ?></p>
                    <p><strong>Plan:</strong> <?= ucfirst($activeLicense['plan']) ?></p>
                    <p><strong>Status:</strong> <?= ucfirst($activeLicense['status']) ?></p>
                    <p><strong>Expires:</strong> <?= $activeLicense['expires_at'] ? date('Y-m-d H:i:s', strtotime($activeLicense['expires_at'])) : 'Never' ?></p>
                    <p><strong>Domain:</strong> <?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost') ?></p>
                </div>
                
                <div>
                    <button class="btn btn-warning" onclick="deactivateLicense()">Deactivate License</button>
                    <button class="btn btn-primary" onclick="validateLicense()">Validate License</button>
                </div>
            <?php else: ?>
                <p>No active license found. You'll have limited access to premium features.</p>
            <?php endif; ?>
            
            <hr style="margin: 30px 0;">
            
            <h3>Install New License</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="install_license">
                <div class="form-group">
                    <label class="form-label">License Key</label>
                    <input type="text" name="license_key" class="form-control" 
                           placeholder="Enter your premium license key" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email (Optional)</label>
                    <input type="email" name="email" class="form-control" 
                           placeholder="Your email address">
                </div>
                <button type="submit" class="btn btn-success">Install License</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['tab']) && $_GET['tab'] === 'analytics'): ?>
        <div class="dashboard-grid">
            <div class="card">
                <h2 class="card-title">Theme Usage Statistics</h2>
                <div class="analytics-stats">
                    <div class="stat">
                        <div class="stat-value"><?= $themeManager->getAnalyticsCount('premium', 'activation') ?></div>
                        <div class="stat-label">Activations</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?= $themeManager->getAnalyticsCount('premium', 'customization') ?></div>
                        <div class="stat-label">Customizations</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?= $themeManager->getAnalyticsCount('premium', 'feature_used') ?></div>
                        <div class="stat-label">Features Used</div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h2 class="card-title">Recent Activity</h2>
                <div id="recent-activity">
                    <!-- Activity will be loaded via JavaScript -->
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Theme management functions
        function setCalculatorSkin(skin) {
            // Update visual selection
            document.querySelectorAll('.theme-skin').forEach(el => el.classList.remove('active'));
            event.currentTarget.classList.add('active');
            
            // Save setting
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="update_theme_settings">
                <input type="hidden" name="calculator_skin" value="${skin}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function updateColorPreview() {
            // Update color preview in real-time
            const color = event.target.value;
            document.documentElement.style.setProperty('--primary-color', color);
        }

        function validateLicense() {
            fetch('?action=validate_license', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('License validated successfully!');
                    location.reload();
                } else {
                    alert('License validation failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error validating license: ' + error.message);
            });
        }

        function deactivateLicense() {
            if (confirm('Are you sure you want to deactivate this license?')) {
                fetch('?action=deactivate_license', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('License deactivated successfully!');
                        location.reload();
                    } else {
                        alert('Failed to deactivate license: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    alert('Error deactivating license: ' + error.message);
                });
            }
        }

        // Load recent activity if on analytics tab
        <?php if (isset($_GET['tab']) && $_GET['tab'] === 'analytics'): ?>
        fetch('?action=get_analytics_data')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.activities) {
                const container = document.getElementById('recent-activity');
                container.innerHTML = data.activities.map(activity => `
                    <div style="padding: 10px 0; border-bottom: 1px solid var(--gray-200);">
                        <div style="font-weight: 500;">${activity.event_type}</div>
                        <div style="font-size: 12px; color: var(--gray-500);">
                            ${new Date(activity.created_at).toLocaleString()}
                        </div>
                    </div>
                `).join('') || '<p>No recent activity</p>';
            }
        })
        .catch(error => {
            console.error('Error loading analytics:', error);
        });
        <?php endif; ?>

        // Handle form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (this.querySelector('input[name="action"]')) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const action = formData.get('action');
                    
                    fetch('?' + new URLSearchParams({ action }), {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Action completed successfully!');
                            if (action === 'update_theme_settings') {
                                location.reload();
                            }
                        } else {
                            alert('Action failed: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + error.message);
                    });
                }
            });
        });
    </script>
</body>
</html>
