<?php
/**
 * Image System Diagnostic and Initialization
 * Comprehensive check and setup for logo, favicon, and profile image system
 */

// Prevent direct access without proper context
if (!defined("BISHWO_CALCULATOR")) {
    define("BISHWO_CALCULATOR", true);
}

// Load required files
require_once dirname(__DIR__) . "/app/Config/config.php";
require_once dirname(__DIR__) . "/app/Helpers/functions.php";
require_once dirname(__DIR__) . "/app/Services/ImageUploadService.php";
require_once dirname(__DIR__) . "/app/Services/ImageRetrievalService.php";
require_once dirname(__DIR__) . "/app/Services/ImageManager.php";

use App\Services\ImageUploadService;
use App\Services\ImageRetrievalService;
use App\Services\ImageManager;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Initialize storage directories if requested
$initializeStorage = isset($_GET["init"]) && $_GET["init"] === "1";
$initResults = [];

if ($initializeStorage) {
    $initResults = ImageUploadService::initializeDirectories();
}

// Get current images
$logoUrl = ImageRetrievalService::getLogo();
$faviconUrl = ImageRetrievalService::getFavicon();
$bannerUrl = ImageRetrievalService::getBanner();

// Get image info
$logoInfo = ImageRetrievalService::getImageInfo("logo");
$faviconInfo = ImageRetrievalService::getImageInfo("favicon");
$bannerInfo = ImageRetrievalService::getImageInfo("banner");

// Check storage directories
$storageDirs = [
    "Base Storage" => BASE_PATH . "/storage/uploads",
    "Admin Logos" => BASE_PATH . "/storage/uploads/admin/logos",
    "Admin Banners" => BASE_PATH . "/storage/uploads/admin/banners",
    "User Profiles" => BASE_PATH . "/storage/uploads/users",
    "Temp Storage" => BASE_PATH . "/storage/uploads/temp",
    "Public Icons" => BASE_PATH . "/public/assets/icons",
];

$dirStatus = [];
foreach ($storageDirs as $name => $path) {
    $dirStatus[$name] = [
        "path" => $path,
        "exists" => is_dir($path),
        "writable" => is_dir($path) && is_writable($path),
        "has_htaccess" => file_exists($path . "/.htaccess"),
    ];
}

// Check theme default images
$themeImagesPath = BASE_PATH . "/themes/default/assets/images";
$themeImages = [];
if (is_dir($themeImagesPath)) {
    $files = scandir($themeImagesPath);
    foreach ($files as $file) {
        if ($file === "." || $file === "..") {
            continue;
        }
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (
            in_array($ext, ["png", "jpg", "jpeg", "gif", "ico", "svg", "webp"])
        ) {
            $themeImages[] = [
                "name" => $file,
                "size" => filesize($themeImagesPath . "/" . $file),
                "path" => "/themes/default/assets/images/" . $file,
            ];
        }
    }
}

// System configuration
$phpConfig = [
    "PHP Version" => PHP_VERSION,
    "GD Extension" => extension_loaded("gd") ? "Enabled" : "Disabled",
    "FileInfo Extension" => extension_loaded("fileinfo")
        ? "Enabled"
        : "Disabled",
    "upload_max_filesize" => ini_get("upload_max_filesize"),
    "post_max_size" => ini_get("post_max_size"),
    "memory_limit" => ini_get("memory_limit"),
    "max_execution_time" => ini_get("max_execution_time"),
];

// Calculate overall status
$criticalIssues = 0;
$warnings = 0;

foreach ($dirStatus as $name => $status) {
    if (!$status["exists"]) {
        $criticalIssues++;
    }
    if ($status["exists"] && !$status["writable"]) {
        $criticalIssues++;
    }
}

if (!extension_loaded("gd")) {
    $warnings++;
}
if (!extension_loaded("fileinfo")) {
    $warnings++;
}

$overallStatus = $criticalIssues === 0 ? "operational" : "critical";
if ($overallStatus === "operational" && $warnings > 0) {
    $overallStatus = "warning";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image System Diagnostic - EngiCal Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #1a202c;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 10%, transparent 10%);
            background-size: 30px 30px;
            animation: pulse 20s linear infinite;
        }

        @keyframes pulse {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }

        .header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .header p {
            opacity: 0.95;
            font-size: 1.2rem;
            position: relative;
            z-index: 1;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 15px;
            position: relative;
            z-index: 1;
        }

        .status-badge.operational {
            background: #10b981;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .status-badge.warning {
            background: #f59e0b;
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        }

        .status-badge.critical {
            background: #ef4444;
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }

        .content {
            padding: 50px;
        }

        .section {
            margin-bottom: 50px;
            animation: fadeInUp 0.6s ease-out;
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

        .section-title {
            color: #4f46e5;
            font-size: 2rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 15px;
            border-bottom: 3px solid #e5e7eb;
        }

        .section-title i {
            font-size: 1.8rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 25px 0;
        }

        .card {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            padding: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-color: #4f46e5;
        }

        .card.success {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .card.error {
            border-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }

        .card.warning {
            border-color: #f59e0b;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }

        .card.success .card-icon {
            color: #10b981;
        }

        .card.error .card-icon {
            color: #ef4444;
        }

        .card.warning .card-icon {
            color: #f59e0b;
        }

        .card h3 {
            font-size: 1.3rem;
            margin-bottom: 12px;
            color: #1f2937;
        }

        .card p {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .card code {
            background: rgba(0,0,0,0.05);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin: 25px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
        }

        th, td {
            padding: 18px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        tr:hover {
            background: #f9fafb;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
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

        .badge.info {
            background: #3b82f6;
            color: white;
        }

        .image-preview {
            background: white;
            border: 3px solid #e5e7eb;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 20px 0;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .image-preview h4 {
            margin-top: 15px;
            color: #4f46e5;
            font-size: 1.1rem;
        }

        .alert {
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
            display: flex;
            align-items: flex-start;
            gap: 20px;
            border: 2px solid;
        }

        .alert.success {
            background: #f0fdf4;
            border-color: #10b981;
            color: #065f46;
        }

        .alert.error {
            background: #fef2f2;
            border-color: #ef4444;
            color: #991b1b;
        }

        .alert.warning {
            background: #fffbeb;
            border-color: #f59e0b;
            color: #92400e;
        }

        .alert.info {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #1e40af;
        }

        .alert i {
            font-size: 1.8rem;
            margin-top: 2px;
        }

        .alert-content h4 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .btn {
            display: inline-block;
            padding: 15px 35px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }

        .btn.btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
        }

        .btn.btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 30px 0;
        }

        .info-list {
            list-style: none;
            padding: 0;
        }

        .info-list li {
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .info-list strong {
            color: #374151;
        }

        .footer {
            background: #f9fafb;
            padding: 30px 50px;
            text-align: center;
            color: #6b7280;
            border-top: 3px solid #e5e7eb;
        }

        .footer p {
            margin: 8px 0;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin: 15px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 1s ease;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .content {
                padding: 30px 20px;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-image"></i>
                Image System Diagnostic
            </h1>
            <p>Comprehensive image management system status and configuration</p>
            <span class="status-badge <?php echo $overallStatus; ?>">
                <?php if ($overallStatus === "operational") {
                    echo '<i class="fas fa-check-circle"></i> All Systems Operational';
                } elseif ($overallStatus === "warning") {
                    echo '<i class="fas fa-exclamation-triangle"></i> Minor Issues Detected';
                } else {
                    echo '<i class="fas fa-times-circle"></i> Critical Issues Found';
                } ?>
            </span>
        </div>

        <div class="content">
            <!-- Initialization Results -->
            <?php if ($initializeStorage): ?>
            <div class="section">
                <div class="alert success">
                    <i class="fas fa-check-circle"></i>
                    <div class="alert-content">
                        <h4>Storage Directories Initialized</h4>
                        <p>All required storage directories have been created with proper security configurations.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Overall Status -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-tachometer-alt"></i>
                    System Status Overview
                </h2>

                <?php
                $totalChecks = count($dirStatus) + 2; // dirs + 2 extensions
                $passedChecks = 0;
                foreach ($dirStatus as $status) {
                    if ($status["exists"] && $status["writable"]) {
                        $passedChecks++;
                    }
                }
                if (extension_loaded("gd")) {
                    $passedChecks++;
                }
                if (extension_loaded("fileinfo")) {
                    $passedChecks++;
                }
                $percentage = round(($passedChecks / $totalChecks) * 100);
                ?>

                <div class="alert <?php echo $overallStatus === "operational"
                    ? "success"
                    : ($overallStatus === "warning"
                        ? "warning"
                        : "error"); ?>">
                    <i class="fas fa-<?php echo $overallStatus === "operational"
                        ? "check-circle"
                        : ($overallStatus === "warning"
                            ? "exclamation-circle"
                            : "exclamation-triangle"); ?>"></i>
                    <div class="alert-content">
                        <h4>System Health: <?php echo $percentage; ?>%</h4>
                        <p>
                            <?php if ($overallStatus === "operational") {
                                echo "All critical components are functioning properly. The image management system is ready for use.";
                            } elseif ($overallStatus === "warning") {
                                echo "System is operational but some optional features may be unavailable. Review warnings below.";
                            } else {
                                echo "Critical issues detected that prevent proper operation. Please address the errors below immediately.";
                            } ?>
                        </p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Images -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-images"></i>
                    Current Active Images
                </h2>

                <div class="grid">
                    <div class="card <?php echo $logoInfo["exists"]
                        ? "success"
                        : "warning"; ?>">
                        <i class="fas fa-image card-icon"></i>
                        <h3>Logo</h3>
                        <?php if ($logoInfo["exists"]): ?>
                            <div class="image-preview">
                                <img src="<?php echo htmlspecialchars(
                                    $logoInfo["full_url"],
                                ); ?>" alt="Logo">
                                <h4>Active Logo</h4>
                            </div>
                            <p><strong>Status:</strong> <span class="badge success">Active</span></p>
                            <p><strong>Type:</strong> <?php echo $logoInfo[
                                "is_default"
                            ]
                                ? "Default Theme"
                                : "Custom Upload"; ?></p>
                        <?php else: ?>
                            <p><strong>Status:</strong> <span class="badge warning">Using Default</span></p>
                            <p>No custom logo uploaded. Using theme default.</p>
                        <?php endif; ?>
                        <p style="margin-top: 10px;"><code><?php echo htmlspecialchars(
                            $logoInfo["url"],
                        ); ?></code></p>
                    </div>

                    <div class="card <?php echo $faviconInfo["exists"]
                        ? "success"
                        : "warning"; ?>">
                        <i class="fas fa-star card-icon"></i>
                        <h3>Favicon</h3>
                        <?php if ($faviconInfo["exists"]): ?>
                            <div class="image-preview">
                                <img src="<?php echo htmlspecialchars(
                                    $faviconInfo["full_url"],
                                ); ?>" alt="Favicon" style="max-height: 64px;">
                                <h4>Active Favicon</h4>
                            </div>
                            <p><strong>Status:</strong> <span class="badge success">Active</span></p>
                            <p><strong>Type:</strong> <?php echo $faviconInfo[
                                "is_default"
                            ]
                                ? "Default Theme"
                                : "Custom Upload"; ?></p>
                        <?php else: ?>
                            <p><strong>Status:</strong> <span class="badge warning">Using Default</span></p>
                            <p>No custom favicon uploaded. Using theme default.</p>
                        <?php endif; ?>
                        <p style="margin-top: 10px;"><code><?php echo htmlspecialchars(
                            $faviconInfo["url"],
                        ); ?></code></p>
                    </div>

                    <div class="card <?php echo $bannerInfo["exists"]
                        ? "success"
                        : "warning"; ?>">
                        <i class="fas fa-panorama card-icon"></i>
                        <h3>Banner</h3>
                        <?php if ($bannerInfo["exists"]): ?>
                            <div class="image-preview">
                                <img src="<?php echo htmlspecialchars(
                                    $bannerInfo["full_url"],
                                ); ?>" alt="Banner">
                                <h4>Active Banner</h4>
                            </div>
                            <p><strong>Status:</strong> <span class="badge success">Active</span></p>
                            <p><strong>Type:</strong> <?php echo $bannerInfo[
                                "is_default"
                            ]
                                ? "Default Theme"
                                : "Custom Upload"; ?></p>
                        <?php else: ?>
                            <p><strong>Status:</strong> <span class="badge warning">Using Default</span></p>
                            <p>No custom banner uploaded. Using theme default.</p>
                        <?php endif; ?>
                        <p style="margin-top: 10px;"><code><?php echo htmlspecialchars(
                            $bannerInfo["url"],
                        ); ?></code></p>
                    </div>
                </div>
            </div>

            <!-- Storage Directories -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-folder-open"></i>
                    Storage Directory Status
                </h2>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Directory</th>
                                <th>Status</th>
                                <th>Writable</th>
                                <th>Protected</th>
                                <th>Path</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dirStatus as $name => $status): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars(
                                    $name,
                                ); ?></strong></td>
                                <td>
                                    <?php if ($status["exists"]): ?>
                                        <span class="badge success"><i class="fas fa-check"></i> Exists</span>
                                    <?php else: ?>
                                        <span class="badge error"><i class="fas fa-times"></i> Missing</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($status["writable"]): ?>
                                        <span class="badge success"><i class="fas fa-check"></i> Yes</span>
                                    <?php else: ?>
                                        <span class="badge error"><i class="fas fa-times"></i> No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($status["has_htaccess"]): ?>
                                        <span class="badge success"><i class="fas fa-shield-alt"></i> Yes</span>
                                    <?php else: ?>
                                        <span class="badge warning"><i class="fas fa-exclamation-triangle"></i> No</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo htmlspecialchars(
                                    $status["path"],
                                ); ?></code></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($criticalIssues > 0): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="alert-content">
                        <h4>Action Required</h4>
                        <p>Some storage directories are missing or not writable. Click the button below to automatically create and configure them.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Theme Default Images -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-palette"></i>
                    Theme Default Images
                </h2>

                <?php if (!empty($themeImages)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Size</th>
                                <th>Path</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($themeImages as $img): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars(
                                    $img["name"],
                                ); ?></strong></td>
                                <td><?php echo round(
                                    $img["size"] / 1024,
                                    2,
                                ); ?> KB</td>
                                <td><code><?php echo htmlspecialchars(
                                    $img["path"],
                                ); ?></code></td>
                                <td><span class="badge success"><i class="fas fa-check"></i> Available</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert warning">
                    <i class="fas fa-exclamation-circle"></i>
                    <div class="alert-content">
                        <h4>No Default Images Found</h4>
                        <p>Theme default images directory is empty or inaccessible.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- PHP Configuration -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-code"></i>
                    PHP Configuration
                </h2>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Setting</th>
                                <th>Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($phpConfig as $key => $value): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars(
                                    $key,
                                ); ?></strong></td>
                                <td><code><?php echo htmlspecialchars(
                                    $value,
                                ); ?></code></td>
                                <td>
                                    <?php
                                    $isGood = true;
                                    if (
                                        $key === "GD Extension" &&
                                        $value === "Disabled"
                                    ) {
                                        $isGood = false;
                                    }
                                    if (
                                        $key === "FileInfo Extension" &&
                                        $value === "Disabled"
                                    ) {
                                        $isGood = false;
                                    }
                                    ?>
                                    <?php if ($isGood): ?>
                                        <span class="badge success"><i class="fas fa-check"></i> OK</span>
                                    <?php else: ?>
                                        <span class="badge warning"><i class="fas fa-exclamation-triangle"></i> Warning</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!extension_loaded("gd")): ?>
                <div class="alert warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="alert-content">
                        <h4>GD Extension Not Available</h4>
                        <p>The GD extension is not enabled. Image optimization features will be limited.</p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!extension_loaded("fileinfo")): ?>
                <div class="alert warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="alert-content">
                        <h4>FileInfo Extension Not Available</h4>
                        <p>The FileInfo extension is not enabled. MIME type validation may be limited.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-cog"></i>
                    System Actions
                </h2>

                <div class="btn-group">
                    <?php if ($criticalIssues > 0): ?>
                    <a href="?init=1" class="btn btn-success">
                        <i class="fas fa-magic"></i> Initialize Storage Directories
                    </a>
                    <?php endif; ?>

                    <a href="<?php echo app_base_url("/"); ?>" class="btn">
                        <i class="fas fa-home"></i> Go to Homepage
                    </a>

                    <a href="<?php echo app_base_url(
                        "/admin/logo-settings",
                    ); ?>" class="btn btn-secondary">
                        <i class="fas fa-upload"></i> Upload Images
                    </a>

                    <button onclick="location.reload()" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Refresh Diagnostic
                    </button>
                </div>

                <div class="alert info" style="margin-top: 30px;">
                    <i class="fas fa-info-circle"></i>
                    <div class="alert-content">
                        <h4>Modular Image System</h4>
                        <p><strong>Upload Locations:</strong></p>
                        <ul class="info-list">
                            <li>
                                <strong>Admin Logos:</strong>
                                <code>storage/uploads/admin/logos/</code>
                            </li>
                            <li>
                                <strong>Admin Banners:</strong>
                                <code>storage/uploads/admin/banners/</code>
                            </li>
                            <li>
                                <strong>Favicons:</strong>
                                <code>public/assets/icons/</code>
                            </li>
                            <li>
                                <strong>User Profiles:</strong>
                                <code>storage/uploads/users/{user_id}/</code>
                            </li>
                        </ul>
                        <p style="margin-top: 15px;"><strong>Fallback System:</strong> If no custom image is uploaded, the system automatically uses default images from the theme folder.</p>
                    </div>
                </div>
            </div>

            <!-- Documentation -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-book"></i>
                    Quick Documentation
                </h2>

                <div class="grid">
                    <div class="card">
                        <i class="fas fa-upload card-icon" style="color: #4f46e5;"></i>
                        <h3>Upload Images</h3>
                        <p>Navigate to Admin Panel → Logo Settings to upload custom logo, favicon, and banner images.</p>
                        <p style="margin-top: 10px;"><strong>Accepted Formats:</strong></p>
                        <p><code>PNG, JPG, JPEG, GIF, WEBP, SVG, ICO</code></p>
                    </div>

                    <div class="card">
                        <i class="fas fa-shield-alt card-icon" style="color: #10b981;"></i>
                        <h3>Security Features</h3>
                        <p>All upload directories are protected with .htaccess files to prevent PHP execution.</p>
                        <p style="margin-top: 10px;">Files are validated for type, size, and MIME type before upload.</p>
                    </div>

                    <div class="card">
                        <i class="fas fa-compress card-icon" style="color: #f59e0b;"></i>
                        <h3>Image Optimization</h3>
                        <p>Uploaded images are automatically optimized and resized to appropriate dimensions.</p>
                        <p style="margin-top: 10px;"><strong>Max Sizes:</strong> Logo 5MB, Profile 2MB</p>
                    </div>

                    <div class="card">
                        <i class="fas fa-code card-icon" style="color: #ef4444;"></i>
                        <h3>Developer API</h3>
                        <p>Use modular services for image management:</p>
                        <p style="margin-top: 10px;"><code>ImageUploadService</code></p>
                        <p><code>ImageRetrievalService</code></p>
                        <p><code>ImageManager</code></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>EngiCal Pro</strong> - Professional Engineering Calculator Suite</p>
            <p style="margin-top: 10px; font-size: 0.9rem;">
                Image System Diagnostic completed at <?php echo date(
                    "Y-m-d H:i:s",
                ); ?> |
                PHP <?php echo PHP_VERSION; ?>
            </p>
            <p style="margin-top: 10px; font-size: 0.85rem; opacity: 0.7;">
                <i class="fas fa-shield-alt"></i> All image uploads are secured and validated |
                <i class="fas fa-sync"></i> Automatic fallback to theme defaults
            </p>
        </div>
    </div>

    <script>
        // Auto-scroll to initialization results if present
        <?php if ($initializeStorage): ?>
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 500);
        });
        <?php endif; ?>
    </script>
</body>
</html>
