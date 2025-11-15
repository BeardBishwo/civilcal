<?php
/**
 * Theme Images Visual Test
 * Quick visual verification that default theme images are showing
 */

// Prevent direct access
if (!defined('BISHWO_CALCULATOR')) {
    define('BISHWO_CALCULATOR', true);
}

// Load required files
require_once __DIR__ . '/app/Config/config.php';
require_once __DIR__ . '/app/Helpers/functions.php';
require_once __DIR__ . '/app/Services/ImageRetrievalService.php';

use App\Services\ImageRetrievalService;

// Get all images
$logo = ImageRetrievalService::getLogo();
$favicon = ImageRetrievalService::getFavicon();
$banner = ImageRetrievalService::getBanner();
$defaultProfile = ImageRetrievalService::getDefaultImagePath('profile');

// Get full URLs
$logoFull = ImageRetrievalService::getFullUrl($logo);
$faviconFull = ImageRetrievalService::getFullUrl($favicon);
$bannerFull = ImageRetrievalService::getFullUrl($banner);
$profileFull = ImageRetrievalService::getFullUrl($defaultProfile);

// Check if files exist
$logoExists = file_exists(__DIR__ . $logo) || file_exists(dirname(__DIR__) . $logo);
$faviconExists = file_exists(__DIR__ . $favicon) || file_exists(dirname(__DIR__) . $favicon);
$bannerExists = file_exists(__DIR__ . $banner) || file_exists(dirname(__DIR__) . $banner);
$profileExists = file_exists(__DIR__ . $defaultProfile) || file_exists(dirname(__DIR__) . $defaultProfile);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Images Test - EngiCal Pro</title>
    <link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #1a202c;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.3);
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
            opacity: 0.95;
            font-size: 1.1rem;
        }

        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section h2 {
            color: #4f46e5;
            font-size: 1.8rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 30px 0;
        }

        .image-card {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-color: #4f46e5;
        }

        .image-card.success {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .image-card.error {
            border-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }

        .image-card h3 {
            color: #1f2937;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .image-preview {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 180px;
            border-radius: 8px;
        }

        .image-preview.favicon img {
            max-height: 64px;
        }

        .image-preview.banner img {
            max-height: 150px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin: 10px 0;
        }

        .status-badge.success {
            background: #10b981;
            color: white;
        }

        .status-badge.error {
            background: #ef4444;
            color: white;
        }

        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
        }

        .info-box code {
            background: rgba(0,0,0,0.05);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9rem;
            word-break: break-all;
        }

        .alert {
            padding: 20px;
            border-radius: 12px;
            margin: 25px 0;
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

        .alert i {
            font-size: 1.5rem;
            margin-top: 2px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 10px 5px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }

        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            border-top: 2px solid #e5e7eb;
        }

        @media (max-width: 768px) {
            .image-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-images"></i>
                Theme Images Visual Test
            </h1>
            <p>Verifying default theme images are displaying correctly</p>
        </div>

        <div class="content">
            <!-- Overall Status -->
            <div class="section">
                <?php
                $allImagesExist = $logoExists && $faviconExists && $bannerExists && $profileExists;
                ?>
                <?php if ($allImagesExist): ?>
                    <div class="alert success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h3>✅ All Theme Images Found!</h3>
                            <p>All default theme images are present and should be displaying on your website.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <h3>❌ Some Images Missing</h3>
                            <p>Some theme images could not be found. Check the details below.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Image Gallery -->
            <div class="section">
                <h2>
                    <i class="fas fa-image"></i>
                    Theme Images Preview
                </h2>

                <div class="image-grid">
                    <!-- Logo -->
                    <div class="image-card <?php echo $logoExists ? 'success' : 'error'; ?>">
                        <h3><i class="fas fa-image"></i> Logo</h3>
                        <div class="image-preview">
                            <?php if ($logoExists): ?>
                                <img src="<?php echo htmlspecialchars($logo); ?>"
                                     alt="Logo"
                                     onerror="this.parentElement.innerHTML='<p style=color:red;>Failed to load</p>'">
                            <?php else: ?>
                                <p style="color: #ef4444;">Image not found</p>
                            <?php endif; ?>
                        </div>
                        <span class="status-badge <?php echo $logoExists ? 'success' : 'error'; ?>">
                            <?php echo $logoExists ? '✅ Found' : '❌ Missing'; ?>
                        </span>
                        <div class="info-box">
                            <strong>Path:</strong><br>
                            <code><?php echo htmlspecialchars($logo); ?></code>
                        </div>
                    </div>

                    <!-- Favicon -->
                    <div class="image-card <?php echo $faviconExists ? 'success' : 'error'; ?>">
                        <h3><i class="fas fa-star"></i> Favicon</h3>
                        <div class="image-preview favicon">
                            <?php if ($faviconExists): ?>
                                <img src="<?php echo htmlspecialchars($favicon); ?>"
                                     alt="Favicon"
                                     onerror="this.parentElement.innerHTML='<p style=color:red;>Failed to load</p>'">
                            <?php else: ?>
                                <p style="color: #ef4444;">Image not found</p>
                            <?php endif; ?>
                        </div>
                        <span class="status-badge <?php echo $faviconExists ? 'success' : 'error'; ?>">
                            <?php echo $faviconExists ? '✅ Found' : '❌ Missing'; ?>
                        </span>
                        <div class="info-box">
                            <strong>Path:</strong><br>
                            <code><?php echo htmlspecialchars($favicon); ?></code><br>
                            <small><em>Check your browser tab for the favicon icon!</em></small>
                        </div>
                    </div>

                    <!-- Banner -->
                    <div class="image-card <?php echo $bannerExists ? 'success' : 'error'; ?>">
                        <h3><i class="fas fa-panorama"></i> Banner</h3>
                        <div class="image-preview banner">
                            <?php if ($bannerExists): ?>
                                <img src="<?php echo htmlspecialchars($banner); ?>"
                                     alt="Banner"
                                     onerror="this.parentElement.innerHTML='<p style=color:red;>Failed to load</p>'">
                            <?php else: ?>
                                <p style="color: #ef4444;">Image not found</p>
                            <?php endif; ?>
                        </div>
                        <span class="status-badge <?php echo $bannerExists ? 'success' : 'error'; ?>">
                            <?php echo $bannerExists ? '✅ Found' : '❌ Missing'; ?>
                        </span>
                        <div class="info-box">
                            <strong>Path:</strong><br>
                            <code><?php echo htmlspecialchars($banner); ?></code>
                        </div>
                    </div>

                    <!-- Default Profile -->
                    <div class="image-card <?php echo $profileExists ? 'success' : 'error'; ?>">
                        <h3><i class="fas fa-user-circle"></i> Default Profile</h3>
                        <div class="image-preview">
                            <?php if ($profileExists): ?>
                                <img src="<?php echo htmlspecialchars($defaultProfile); ?>"
                                     alt="Default Profile"
                                     onerror="this.parentElement.innerHTML='<p style=color:red;>Failed to load</p>'">
                            <?php else: ?>
                                <p style="color: #ef4444;">Image not found</p>
                            <?php endif; ?>
                        </div>
                        <span class="status-badge <?php echo $profileExists ? 'success' : 'error'; ?>">
                            <?php echo $profileExists ? '✅ Found' : '❌ Missing'; ?>
                        </span>
                        <div class="info-box">
                            <strong>Path:</strong><br>
                            <code><?php echo htmlspecialchars($defaultProfile); ?></code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Info -->
            <div class="section">
                <h2>
                    <i class="fas fa-cog"></i>
                    Configuration Details
                </h2>

                <div class="info-box">
                    <h4 style="margin-bottom: 10px;">📁 Theme Images Location:</h4>
                    <code>themes/default/assets/images/</code>
                </div>

                <div class="info-box">
                    <h4 style="margin-bottom: 10px;">⚙️ Configuration File:</h4>
                    <code>app/db/site_meta.json</code>
                </div>

                <div class="info-box">
                    <h4 style="margin-bottom: 10px;">🔄 Fallback System:</h4>
                    <ol style="margin-left: 20px; margin-top: 10px;">
                        <li>Check for uploaded custom image</li>
                        <li>Check site_meta.json configuration</li>
                        <li>Fall back to theme default images</li>
                    </ol>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="text-align: center; margin: 40px 0;">
                <a href="<?php echo app_base_url('/'); ?>" class="btn">
                    <i class="fas fa-home"></i> View Website
                </a>
                <a href="<?php echo app_base_url('/image_system_diagnostic'); ?>" class="btn btn-secondary">
                    <i class="fas fa-tools"></i> Full Diagnostic
                </a>
                <a href="<?php echo app_base_url('/admin/logo-settings'); ?>" class="btn btn-secondary">
                    <i class="fas fa-upload"></i> Upload Custom Images
                </a>
            </div>

            <!-- Instructions -->
            <div class="section">
                <h2>
                    <i class="fas fa-question-circle"></i>
                    How It Works
                </h2>

                <div style="background: #f9fafb; padding: 25px; border-radius: 12px;">
                    <h4 style="color: #4f46e5; margin-bottom: 15px;">📝 Default Theme Images:</h4>
                    <ul style="line-height: 1.8; margin-left: 20px;">
                        <li>Located in <code>themes/default/assets/images/</code></li>
                        <li>Used automatically when no custom images are uploaded</li>
                        <li>Logo: <code>logo.png</code> (197 KB)</li>
                        <li>Favicon: <code>favicon.png</code> (439 KB)</li>
                        <li>Banner: <code>banner.jpg</code> (193 KB)</li>
                        <li>Profile: <code>profile.png</code> (951 KB)</li>
                    </ul>

                    <h4 style="color: #4f46e5; margin: 20px 0 15px;">📤 Uploading Custom Images:</h4>
                    <ul style="line-height: 1.8; margin-left: 20px;">
                        <li>Go to Admin Panel → Logo Settings</li>
                        <li>Upload your custom logo, favicon, or banner</li>
                        <li>Custom uploads are saved to <code>storage/uploads/</code></li>
                        <li>System automatically uses custom images over defaults</li>
                    </ul>

                    <h4 style="color: #4f46e5; margin: 20px 0 15px;">🔄 Reverting to Defaults:</h4>
                    <ul style="line-height: 1.8; margin-left: 20px;">
                        <li>Delete custom uploads to revert to theme defaults</li>
                        <li>Theme defaults are never deleted</li>
                        <li>Always available as fallback</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>EngiCal Pro</strong> - Theme Images Test</p>
            <p style="margin-top: 10px; font-size: 0.9rem;">
                Test completed at <?php echo date('Y-m-d H:i:s'); ?>
            </p>
        </div>
    </div>
</body>
</html>
