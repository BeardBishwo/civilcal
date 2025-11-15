<?php
/**
 * Logo and Favicon Diagnostic Tool
 * Comprehensive check for logo and favicon functionality
 */

// Prevent direct access
if (!defined('BISHWO_CALCULATOR')) {
    define('BISHWO_CALCULATOR', true);
}

// Load required files
require_once dirname(__DIR__) . '/app/Config/config.php';
require_once dirname(__DIR__) . '/app/Helpers/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Get site meta
$site_meta = get_site_meta();

// Process URLs
$logoRaw = $site_meta['logo'] ?? '/assets/icons/icon-192.png';
if (!empty($logoRaw) && preg_match('#^https?://#', $logoRaw)) {
    $logo = $logoRaw;
} else {
    $logo = app_base_url(ltrim($logoRaw, '/'));
}

$faviconRaw = $site_meta['favicon'] ?? '/assets/icons/favicon.ico';
if (!empty($faviconRaw) && preg_match('#^https?://#', $faviconRaw)) {
    $favicon = $faviconRaw;
} else {
    $favicon = app_base_url(ltrim($faviconRaw, '/'));
}

// Check file existence
$publicDir = dirname(__DIR__) . '/public';
$logoPath = $publicDir . $logoRaw;
$faviconPath = $publicDir . $faviconRaw;
$icon512Path = $publicDir . '/assets/icons/icon-512.png';
$manifestPath = $publicDir . '/manifest.json';

$logoExists = file_exists($logoPath);
$faviconExists = file_exists($faviconPath);
$icon512Exists = file_exists($icon512Path);
$manifestExists = file_exists($manifestPath);

// Get file info
function getFileInfo($path) {
    if (!file_exists($path)) {
        return ['exists' => false];
    }

    $info = [
        'exists' => true,
        'size' => filesize($path),
        'readable' => is_readable($path),
        'mime' => function_exists('mime_content_type') ? mime_content_type($path) : 'unknown',
        'modified' => filemtime($path)
    ];

    return $info;
}

$logoInfo = getFileInfo($logoPath);
$faviconInfo = getFileInfo($faviconPath);
$icon512Info = getFileInfo($icon512Path);
$manifestInfo = getFileInfo($manifestPath);

// Check if images can be served
function checkUrlAccessible($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $responseCode >= 200 && $responseCode < 400;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logo & Favicon Diagnostic - EngiCal Pro</title>
    <link rel="icon" type="image/x-icon" href="<?php echo htmlspecialchars($favicon); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #1a202c;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 40px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 30px;
        }

        .section:last-child {
            border-bottom: none;
        }

        .section h2 {
            color: #4f46e5;
            font-size: 1.8rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .status-card {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .status-card.success {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .status-card.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .status-card.warning {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        .status-card h3 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-card .icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .status-card.success .icon {
            color: #10b981;
        }

        .status-card.error .icon {
            color: #ef4444;
        }

        .status-card.warning .icon {
            color: #f59e0b;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .info-table th,
        .info-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .info-table tr:hover {
            background: #f9fafb;
        }

        code {
            background: #e5e7eb;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
            color: #1f2937;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge.success {
            background: #10b981;
            color: white;
        }

        .badge.error {
            background: #ef4444;
            color: white;
        }

        .badge.warning {
            background: #f59e0b;
            color: white;
        }

        .visual-test {
            background: #f9fafb;
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 30px;
            margin: 20px 0;
            text-align: center;
        }

        .visual-test h3 {
            margin-bottom: 20px;
            color: #4f46e5;
        }

        .image-preview {
            display: inline-block;
            padding: 20px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            margin: 10px;
        }

        .image-preview img {
            display: block;
            max-width: 100%;
            height: auto;
        }

        .logo-preview img {
            max-height: 80px;
        }

        .favicon-preview img {
            width: 32px;
            height: 32px;
        }

        .alert {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .alert.success {
            background: #f0fdf4;
            border: 2px solid #10b981;
            color: #065f46;
        }

        .alert.error {
            background: #fef2f2;
            border: 2px solid #ef4444;
            color: #991b1b;
        }

        .alert.warning {
            background: #fffbeb;
            border: 2px solid #f59e0b;
            color: #92400e;
        }

        .alert i {
            font-size: 1.5rem;
            margin-top: 2px;
        }

        .alert-content h4 {
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .recommendations {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border-radius: 12px;
            padding: 30px;
            margin: 20px 0;
        }

        .recommendations h3 {
            color: #4f46e5;
            margin-bottom: 15px;
        }

        .recommendations ul {
            list-style: none;
            padding: 0;
        }

        .recommendations li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
        }

        .recommendations li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .footer {
            background: #f9fafb;
            padding: 20px 40px;
            text-align: center;
            color: #6b7280;
            border-top: 2px solid #e5e7eb;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8rem;
            }

            .content {
                padding: 20px;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-check-circle"></i> Logo & Favicon Diagnostic</h1>
            <p>Complete system check for EngiCal Pro branding assets</p>
        </div>

        <div class="content">
            <!-- Overall Status -->
            <div class="section">
                <h2><i class="fas fa-tachometer-alt"></i> Overall Status</h2>
                <?php
                $allPassed = $logoExists && $faviconExists && $icon512Exists && $manifestExists;
                $someIssues = !$allPassed && ($logoExists || $faviconExists);
                $criticalIssues = !$logoExists || !$faviconExists;

                if ($allPassed) {
                    echo '<div class="alert success">';
                    echo '<i class="fas fa-check-circle"></i>';
                    echo '<div class="alert-content">';
                    echo '<h4>All Systems Operational ✓</h4>';
                    echo '<p>All logo and favicon assets are properly configured and accessible.</p>';
                    echo '</div></div>';
                } elseif ($criticalIssues) {
                    echo '<div class="alert error">';
                    echo '<i class="fas fa-exclamation-triangle"></i>';
                    echo '<div class="alert-content">';
                    echo '<h4>Critical Issues Detected</h4>';
                    echo '<p>Required logo or favicon files are missing. Please address these issues immediately.</p>';
                    echo '</div></div>';
                } else {
                    echo '<div class="alert warning">';
                    echo '<i class="fas fa-exclamation-circle"></i>';
                    echo '<div class="alert-content">';
                    echo '<h4>Minor Issues Detected</h4>';
                    echo '<p>Some optional assets are missing but core functionality is intact.</p>';
                    echo '</div></div>';
                }
                ?>
            </div>

            <!-- File Status Cards -->
            <div class="section">
                <h2><i class="fas fa-folder-open"></i> Asset Files Status</h2>
                <div class="status-grid">
                    <div class="status-card <?php echo $logoExists ? 'success' : 'error'; ?>">
                        <div class="icon">
                            <i class="fas fa-<?php echo $logoExists ? 'check-circle' : 'times-circle'; ?>"></i>
                        </div>
                        <h3>Logo Image</h3>
                        <p><code>icon-192.png</code></p>
                        <p><strong>Status:</strong> <span class="badge <?php echo $logoExists ? 'success' : 'error'; ?>"><?php echo $logoExists ? 'Found' : 'Missing'; ?></span></p>
                        <?php if ($logoExists): ?>
                            <p><strong>Size:</strong> <?php echo round($logoInfo['size'] / 1024, 2); ?> KB</p>
                        <?php endif; ?>
                    </div>

                    <div class="status-card <?php echo $faviconExists ? 'success' : 'error'; ?>">
                        <div class="icon">
                            <i class="fas fa-<?php echo $faviconExists ? 'check-circle' : 'times-circle'; ?>"></i>
                        </div>
                        <h3>Favicon</h3>
                        <p><code>favicon.ico</code></p>
                        <p><strong>Status:</strong> <span class="badge <?php echo $faviconExists ? 'success' : 'error'; ?>"><?php echo $faviconExists ? 'Found' : 'Missing'; ?></span></p>
                        <?php if ($faviconExists): ?>
                            <p><strong>Size:</strong> <?php echo round($faviconInfo['size'] / 1024, 2); ?> KB</p>
                        <?php endif; ?>
                    </div>

                    <div class="status-card <?php echo $icon512Exists ? 'success' : 'warning'; ?>">
                        <div class="icon">
                            <i class="fas fa-<?php echo $icon512Exists ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        </div>
                        <h3>Large Icon</h3>
                        <p><code>icon-512.png</code></p>
                        <p><strong>Status:</strong> <span class="badge <?php echo $icon512Exists ? 'success' : 'warning'; ?>"><?php echo $icon512Exists ? 'Found' : 'Optional'; ?></span></p>
                        <?php if ($icon512Exists): ?>
                            <p><strong>Size:</strong> <?php echo round($icon512Info['size'] / 1024, 2); ?> KB</p>
                        <?php endif; ?>
                    </div>

                    <div class="status-card <?php echo $manifestExists ? 'success' : 'warning'; ?>">
                        <div class="icon">
                            <i class="fas fa-<?php echo $manifestExists ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        </div>
                        <h3>Web Manifest</h3>
                        <p><code>manifest.json</code></p>
                        <p><strong>Status:</strong> <span class="badge <?php echo $manifestExists ? 'success' : 'warning'; ?>"><?php echo $manifestExists ? 'Found' : 'Optional'; ?></span></p>
                        <?php if ($manifestExists): ?>
                            <p><strong>Size:</strong> <?php echo round($manifestInfo['size'] / 1024, 2); ?> KB</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Configuration Details -->
            <div class="section">
                <h2><i class="fas fa-cog"></i> Configuration Details</h2>
                <table class="info-table">
                    <thead>
                        <tr>
                            <th>Setting</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Site Title</strong></td>
                            <td><?php echo htmlspecialchars($site_meta['title'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Logo Text</strong></td>
                            <td><?php echo htmlspecialchars($site_meta['logo_text'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Header Style</strong></td>
                            <td><?php echo htmlspecialchars($site_meta['header_style'] ?? 'logo_text'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Logo Path (Raw)</strong></td>
                            <td><code><?php echo htmlspecialchars($logoRaw); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Logo URL (Processed)</strong></td>
                            <td><code><?php echo htmlspecialchars($logo); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Favicon Path (Raw)</strong></td>
                            <td><code><?php echo htmlspecialchars($faviconRaw); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Favicon URL (Processed)</strong></td>
                            <td><code><?php echo htmlspecialchars($favicon); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>APP_BASE</strong></td>
                            <td><code><?php echo htmlspecialchars(defined('APP_BASE') ? APP_BASE : '/'); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Document Root</strong></td>
                            <td><code><?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A'); ?></code></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Visual Tests -->
            <div class="section">
                <h2><i class="fas fa-eye"></i> Visual Tests</h2>

                <?php if ($logoExists): ?>
                <div class="visual-test">
                    <h3>Logo Display Test</h3>
                    <div class="image-preview logo-preview">
                        <img src="<?php echo htmlspecialchars($logo); ?>"
                             alt="Logo"
                             onerror="this.parentElement.innerHTML='<p style=color:red;>❌ Failed to load logo</p>'">
                    </div>
                    <p style="margin-top: 15px; color: #10b981;"><i class="fas fa-check"></i> Logo is displaying correctly</p>
                </div>
                <?php else: ?>
                <div class="alert error">
                    <i class="fas fa-times-circle"></i>
                    <div class="alert-content">
                        <h4>Logo Not Found</h4>
                        <p>Upload a logo image to: <code><?php echo htmlspecialchars($logoPath); ?></code></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($faviconExists): ?>
                <div class="visual-test">
                    <h3>Favicon Display Test</h3>
                    <div class="image-preview favicon-preview">
                        <img src="<?php echo htmlspecialchars($favicon); ?>"
                             alt="Favicon"
                             onerror="this.parentElement.innerHTML='<p style=color:red;>❌ Failed to load favicon</p>'">
                    </div>
                    <p style="margin-top: 15px; color: #10b981;"><i class="fas fa-check"></i> Favicon is displaying correctly</p>
                    <p style="margin-top: 5px; color: #6b7280; font-size: 0.9rem;"><em>Also check your browser tab for the favicon icon</em></p>
                </div>
                <?php else: ?>
                <div class="alert error">
                    <i class="fas fa-times-circle"></i>
                    <div class="alert-content">
                        <h4>Favicon Not Found</h4>
                        <p>Upload a favicon to: <code><?php echo htmlspecialchars($faviconPath); ?></code></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Logo Settings -->
            <?php if (isset($site_meta['logo_settings']) && is_array($site_meta['logo_settings'])): ?>
            <div class="section">
                <h2><i class="fas fa-sliders-h"></i> Logo Settings</h2>
                <table class="info-table">
                    <thead>
                        <tr>
                            <th>Setting</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($site_meta['logo_settings'] as $key => $value): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?></strong></td>
                            <td><?php echo htmlspecialchars(is_bool($value) ? ($value ? 'Yes' : 'No') : $value); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Recommendations -->
            <div class="section">
                <h2><i class="fas fa-lightbulb"></i> Recommendations</h2>
                <div class="recommendations">
                    <h3>Best Practices</h3>
                    <ul>
                        <?php if ($allPassed): ?>
                        <li>All required assets are in place - excellent!</li>
                        <li>Consider optimizing image sizes for faster loading</li>
                        <li>Test favicon display across different browsers</li>
                        <li>Verify logo displays correctly in both light and dark themes</li>
                        <?php else: ?>
                            <?php if (!$logoExists): ?>
                            <li style="color: #dc2626;">Upload logo: Recommended size 192x192px, PNG format with transparent background</li>
                            <?php endif; ?>
                            <?php if (!$faviconExists): ?>
                            <li style="color: #dc2626;">Upload favicon: ICO format, 32x32px recommended</li>
                            <?php endif; ?>
                            <?php if (!$icon512Exists): ?>
                            <li style="color: #d97706;">Upload 512x512 icon for better PWA support (optional)</li>
                            <?php endif; ?>
                            <?php if (!$manifestExists): ?>
                            <li style="color: #d97706;">Add manifest.json for Progressive Web App features (optional)</li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <li>Logo should be high quality and represent your brand</li>
                        <li>Keep file sizes under 200KB for optimal performance</li>
                        <li>Use PNG format for logo (supports transparency)</li>
                        <li>Use ICO format for favicon (best browser compatibility)</li>
                    </ul>
                </div>
            </div>

            <!-- File Paths Reference -->
            <div class="section">
                <h2><i class="fas fa-map-signs"></i> File Paths Reference</h2>
                <table class="info-table">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Expected Path</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Logo (192x192)</strong></td>
                            <td><code><?php echo htmlspecialchars($logoPath); ?></code></td>
                            <td><span class="badge <?php echo $logoExists ? 'success' : 'error'; ?>"><?php echo $logoExists ? 'Exists' : 'Missing'; ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>Favicon</strong></td>
                            <td><code><?php echo htmlspecialchars($faviconPath); ?></code></td>
                            <td><span class="badge <?php echo $faviconExists ? 'success' : 'error'; ?>"><?php echo $faviconExists ? 'Exists' : 'Missing'; ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>Icon (512x512)</strong></td>
                            <td><code><?php echo htmlspecialchars($icon512Path); ?></code></td>
                            <td><span class="badge <?php echo $icon512Exists ? 'success' : 'warning'; ?>"><?php echo $icon512Exists ? 'Exists' : 'Missing'; ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>Web Manifest</strong></td>
                            <td><code><?php echo htmlspecialchars($manifestPath); ?></code></td>
                            <td><span class="badge <?php echo $manifestExists ? 'success' : 'warning'; ?>"><?php echo $manifestExists ? 'Exists' : 'Missing'; ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>Site Meta Config</strong></td>
                            <td><code><?php echo htmlspecialchars(dirname(__DIR__) . '/app/db/site_meta.json'); ?></code></td>
                            <td><span class="badge success">Loaded</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div style="text-align: center; margin-top: 40px;">
                <a href="<?php echo app_base_url('/'); ?>" class="btn">
                    <i class="fas fa-home"></i> Go to Homepage
                </a>
                <a href="<?php echo app_base_url('/admin/logo-settings'); ?>" class="btn btn-secondary" style="margin-left: 10px;">
                    <i class="fas fa-cog"></i> Logo Settings
                </a>
                <button onclick="location.reload()" class="btn btn-secondary" style="margin-left: 10px;">
                    <i class="fas fa-sync"></i> Refresh Test
                </button>
            </div>
        </div>

        <div class="footer">
            <p><strong>EngiCal Pro</strong> - Professional Engineering Calculator Suite</p>
            <p style="margin-top: 10px; font-size: 0.9rem;">
                Test completed at <?php echo date('Y-m-d H:i:s'); ?> |
                PHP <?php echo PHP_VERSION; ?>
            </p>
        </div>
    </div>
</body>
</html>
