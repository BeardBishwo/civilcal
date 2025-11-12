<?php
// Safe session start
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/VersionChecker.php';

// Check for updates if admin is logged in
$updateAvailable = null;
if (!empty($_SESSION['is_admin'])) {
    $updateAvailable = VersionChecker::checkForUpdates();
}

$site_meta = get_site_meta();
$title_safe = htmlspecialchars($page_title ?? $site_meta['title'] ?? 'AEC Calculator');
$desc_safe = htmlspecialchars($site_meta['description'] ?? 'Professional Engineering Calculators Suite');
$logo = $site_meta['logo'] ?? app_base_url('assets/images/applogo.png');
$logo_text = $site_meta['logo_text'] ?? 'EngiCal Pro';
$header_style = $site_meta['header_style'] ?? 'logo_text';
$favicon = $site_meta['favicon'] ?? app_base_url('assets/images/favicon.png');

// User data for personalized UI
// Support both new structure ($_SESSION['user']) and legacy session keys (user_id, username, full_name, role)
$user = [];
if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    // Build a user array from legacy session vars if present
    if (!empty($_SESSION['user_id']) || !empty($_SESSION['username']) || !empty($_SESSION['full_name'])) {
        $user = [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? '',
            'full_name' => $_SESSION['full_name'] ?? '',
            'role' => $_SESSION['role'] ?? ''
        ];
    }
}

$userName = trim($user['full_name'] ?? $user['username'] ?? '');
$userInitial = !empty($userName) ? strtoupper(substr($userName, 0, 1)) : '';
$userRole = $user['role'] ?? '';
$engineerRoles = $user['engineer_roles'] ?? [];

// Calculate search statistics
$search_stats = ['categories' => 0, 'subcategories' => 0, 'tools' => 0];
$modules_dir = __DIR__ . '/../modules';
if (is_dir($modules_dir)) {
    $categories = scandir($modules_dir);
    foreach ($categories as $category) {
        if ($category === '.' || $category === '..' || !is_dir($modules_dir . '/' . $category)) continue;
        $search_stats['categories']++;
        
        $subcategories = scandir($modules_dir . '/' . $category);
        foreach ($subcategories as $subcategory) {
            if ($subcategory === '.' || $subcategory === '..' || !is_dir($modules_dir . '/' . $category . '/' . $subcategory)) continue;
            $search_stats['subcategories']++;
            
            $files = scandir($modules_dir . '/' . $category . '/' . $subcategory);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $search_stats['tools']++;
                }
            }
        }
    }
}

// Ensure is_admin flag is available (legacy support)
if (empty($_SESSION['is_admin']) && !empty($userRole) && strtolower($userRole) === 'admin') {
    $_SESSION['is_admin'] = true;
}
// Server-side theme detection via cookie (so first render can have the correct theme)
$body_class = '';
if (!empty($_COOKIE['site_theme']) && $_COOKIE['site_theme'] === 'dark') {
    $body_class = 'dark-theme';
}

// Mark homepage body with 'index-page' so home-specific gradient styles apply
$__req_path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$__base = defined('APP_BASE') ? rtrim(APP_BASE, '/') : '';
if ($__req_path === $__base || $__req_path === $__base . '/' || (substr($__req_path, -10) === '/index.php')) {
    $body_class = trim($body_class . ' index-page');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_safe; ?></title>
    <meta name="description" content="<?php echo $desc_safe; ?>">
    <link rel="manifest" href="<?php echo app_base_url('manifest.json'); ?>">
    <meta name="theme-color" content="#667eea">
    <link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo app_base_url('assets/css/theme.css?v=' . filemtime(dirname(__DIR__) . '/assets/css/theme.css')); ?>">
    <link rel="stylesheet" href="<?php echo app_base_url('assets/css/footer.css?v=' . filemtime(dirname(__DIR__) . '/assets/css/footer.css')); ?>">
    <link rel="stylesheet" href="<?php echo app_base_url('assets/css/back-to-top.css?v=' . filemtime(dirname(__DIR__) . '/assets/css/back-to-top.css')); ?>">
    <style>
        /* Enhanced Header Styles */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.1);
            --shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .site-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            width: 100%;
            left: 0;
            right: 0;
        }

        /* Override theme.css max-width constraint for true full width */
        .site-header .header-content,
        .header-content {
            max-width: none !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* Slightly reduced gap so header items fit better at non-default zoom levels */
            gap: 1.25rem;
            box-sizing: border-box;
        }

        /* Ensure no body/html margins interfere */
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            overflow-x: hidden;
        }
        
        /* Fix for black strip issue - ensure proper background */
        body {
            background: #ffffff;
            min-height: 100vh;
        }
        
        /* Ensure main content area has proper styling */
        .main-content {
            background: #ffffff;
            min-height: calc(100vh - 60px);
        }

        /* Responsive section adjustments */
        @media (max-width: 1024px) {
            .header-left {
                flex: 0 0 160px;
                max-width: 160px;
            }
        }

        @media (max-width: 768px) {
            .header-left {
                flex: 0 0 140px;
                max-width: 140px;
            }
            
            .header-middle {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .header-left {
                flex: 0 0 110px;
                max-width: 110px;
            }
        }

        .header-left {
            /* Reduced base width so header-middle gains space to the left
               without growing toward the right section. Keeps logo responsive. */
            flex: 0 0 160px;
            max-width: 160px;
            min-width: 120px;
            flex-shrink: 0;
            /* Center logo horizontally and vertically inside the left area */
            display: flex;
            align-items: center;
            justify-content: center;
            padding-left: 0.25rem;
            padding-right: 0.25rem;
            box-sizing: border-box;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.5rem;
            color: #2d3748;
            transition: transform 0.2s ease;
        }

        .logo:hover {
            transform: translateY(-1px);
        }

        .logo-img {
            height: 32px;
            width: auto;
            border-radius: 6px;
            /* keep logo tightly aligned to the left */
            display: block;
        }

        .header-middle {
            flex: 1 1 auto;
            max-width: none;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            /* reduced padding-right so middle section doesn't push into header-right at smaller widths */
            padding-right: 1.2rem;
            /* Allow dropdowns to overflow the middle area so submenus are visible */
            overflow: visible;
        }

        .main-nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            /* tighten up horizontal gap between menu items slightly */
            gap: 0.35rem;
        }

        .main-nav a {
            text-decoration: none;
            /* slightly reduced padding to help fit the login button at narrower widths/zoom */
            padding: 0.45rem 0.9rem;
            border-radius: 8px;
            color: #4a5568;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.45rem;
            white-space: nowrap;
        }

        .main-nav a:hover {
            background: #f7fafc;
            color: #667eea;
            transform: translateY(-1px);
        }

        .main-nav a i {
            font-size: 0.875rem;
            transition: transform 0.2s ease;
        }

        /* Make sure the chevron for dropdowns is always visible and not clipped */
        .main-nav a .fa-chevron-down {
            margin-left: 0.35rem;
            font-size: 0.9rem;
            color: inherit;
            opacity: 0.95;
            display: inline-block;
            vertical-align: middle;
        }

        /* Ensure parent has-dropdown establishes positioning context for dropdowns */
        .has-dropdown {
            position: relative;
            display: inline-block;
        }

        .has-dropdown:hover .dropdown {
            display: block;
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Support click-to-open for touch/mobile: .has-dropdown.open */
        .has-dropdown.open .dropdown {
            display: block;
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            padding: 0.5rem;
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
            z-index: 1200;
            list-style: none;
            margin: 0;
        }
        
        .dropdown > li {
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
        }

        /* Reset any inherited styles */
        .dropdown a.grid-item {
            display: flex !important;
            align-items: center !important;
            padding: 10px 16px !important;
            border-radius: 6px !important;
            color: #4a5568 !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
            margin: 0 !important;
            white-space: nowrap !important;
        }
        
        /* Force icon sizing and spacing */
        .dropdown a.grid-item i {
            flex: 0 0 20px !important;
            margin-right: 12px !important;
            text-align: center !important;
            font-size: 14px !important;
            width: 20px !important;
        }

        /* Hover state */
        .dropdown a.grid-item:hover {
            background: #667eea !important;
            color: white !important;
            transform: translateY(-1px) !important;
        }

        /* Dark theme support */
        body.dark-theme .dropdown a.grid-item {
            color: #e2e8f0 !important;
        }

        body.dark-theme .dropdown a.grid-item:hover {
            background: rgba(102, 126, 234, 0.2) !important;
            color: white !important;
        }

        .dropdown a i {
            place-self: center start;
        }

        .dropdown li {
            list-style: none;
            margin: 2px 0;
        }

        .dropdown a:hover {
            background: #667eea;
            color: white;
        }

        .search-container {
            position: relative;
            flex: 1 1 360px;
            max-width: 420px;
            margin-right: 1rem; /* prevent overlap with header-right */
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            background: #f8fafc;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-input::before {
            content: '\f002';
            font-family: 'Font Awesome 6 Free';
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            margin-top: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .search-input:focus + .search-suggestions,
        .search-suggestions:hover {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .header-right {
            flex: 0 0 auto;
            max-width: none;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            flex-shrink: 0;
            flex-wrap: nowrap;
            justify-content: flex-end;
            white-space: nowrap;
        }

        .user-actions {
            display: flex;
            align-items: center;
            /* slightly tighter spacing to preserve space */
            gap: 0.5rem;
            flex-shrink: 0;
            flex-wrap: nowrap;
            width: auto; /* don't force full width which can push content */
            justify-content: flex-end;
        }

        /* Move user-greeting inside user-actions */
        .user-actions .user-greeting {
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
            background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.05));
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-weight: 500;
            flex-shrink: 0;
            white-space: nowrap;
        }

        /* Profile dropdown wrapper inside user-actions */
        .user-actions .profile-dropdown-wrapper {
            flex-shrink: 0;
        }

        .theme-toggle-btn {
            background: none;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #4a5568;
        }

        /* Small search icon button for opening modal */
        .search-toggle-btn {
            background: none;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #4a5568;
        }

        .search-toggle-btn:hover {
            background: #f7fafc;
            color: #667eea;
            transform: scale(1.05);
        }

        .theme-toggle-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
            transform: scale(1.1);
        }

        /* Header-specific button sizing to keep the header compact */
        .header-right .btn {
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.15s ease;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* Make login button responsive */
        .login-btn {
            margin-left: 0.25rem !important;
        }

        @media (max-width: 480px) {
            .login-btn .btn-text {
                display: none;
            }
            .login-btn {
                padding: 0.5rem;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                justify-content: center;
            }
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f7fafc;
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #edf2f7;
            transform: translateY(-1px);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            border-color: #667eea;
        }

        .user-dropdown {
            right: 0;
            left: auto;
        }

        .user-info {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
        }

        .user-name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: #718096;
            background: #f7fafc;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            display: inline-block;
        }

        .hamburger-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #4a5568;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .hamburger-btn:hover {
            background: #f7fafc;
            color: #667eea;
        }

        .mobile-nav {
            display: none;
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 2rem;
        }

        .mobile-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.5rem;
        }

        .mobile-nav a {
            display: block;
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: #4a5568;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .mobile-nav a:hover {
            background: #667eea;
            color: white;
        }

        .update-notification {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .header-content {
                gap: 1rem;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                gap: 0.5rem;
            }
            
            .header-middle {
                display: none;
            }
            
            .hamburger-btn {
                display: block;
            }
            
            .mobile-nav.active {
                display: block;
            }
            
            .user-actions .btn {
                padding: 0.5rem 1rem;
                font-size: 0.75rem;
            }
            
            /* Responsive user greeting inside user-actions */
            .user-actions .user-greeting {
                padding: 0.3rem 0.6rem;
                font-size: 0.75rem;
            }
            
            /* Hide user greeting text on very small screens */
            @media (max-width: 640px) {
                .user-actions .user-greeting {
                    display: none;
                }
            }
        }

        @media (max-width: 480px) {
            .header-content {
                margin: 0 10px;
                gap: 0.25rem;
                width: calc(100% - 20px);
            }
            
            .logo span {
                display: none;
            }
            
            .user-actions .btn-text {
                display: none;
            }
            
            .user-actions .btn {
                padding: 0.5rem;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                justify-content: center;
            }
            
            .profile-btn {
                width: 36px;
                height: 36px;
                padding: 0.5rem;
            }
            
            .header-right {
                gap: 0.25rem;
            }
        }

        /* Modal search styles */
        .search-modal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1100;
            padding: 1rem;
        }

        .search-modal.active { display: flex; }

        .search-modal .modal-content {
            width: 100%;
            max-width: 760px;
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 20px 40px rgba(2,6,23,0.4);
        }

        body.dark-theme .search-modal .modal-content {
            background: rgba(6,8,12,0.95);
            color: #e6eefc;
        }

        .search-modal .modal-input {
            width: 100%;
            padding: 0.9rem 1rem;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-bottom: 0.75rem;
        }

        .search-modal .modal-close {
            position: absolute;
            right: 1.25rem;
            top: 1.25rem;
            background: none;
            border: none;
            color: #718096;
            font-size: 1.25rem;
            cursor: pointer;
        }

        @media (max-width: 480px) {
            .header-content {
                gap: 0.25rem;
            }
            
            .logo span {
                display: none;
            }
            
            .user-actions .btn-text {
                display: none;
            }
        }

        /* Dark theme overrides for header elements to keep contrast */
        body.dark-theme {
            --glass-bg: rgba(8,10,15,0.6);
            --glass-border: rgba(255,255,255,0.03);
            color: #e6eefc;
        }

        body.dark-theme .site-header {
            background: rgba(6,8,12,0.72);
            border-bottom: 1px solid rgba(255,255,255,0.04);
            box-shadow: 0 8px 30px rgba(2,6,23,0.6);
            color: #e6eefc;
        }

        /* Nav links in dark mode */
        body.dark-theme .main-nav a {
            color: #cbd5e1;
        }

        /* Hover state: avoid bright white background which hides text */
        body.dark-theme .main-nav a:hover {
            background: rgba(255,255,255,0.06);
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* Dropdown in dark mode */
        body.dark-theme .dropdown {
            background: linear-gradient(180deg, rgba(10,12,16,0.95), rgba(6,8,12,0.95));
            border: 1px solid rgba(255,255,255,0.04);
            color: #e6eefc;
            box-shadow: 0 10px 30px rgba(2,6,23,0.6);
        }

        body.dark-theme .dropdown a {
            color: #e6eefc;
        }

        /* Ensure buttons remain visible in dark mode */
        body.dark-theme .btn-primary {
            background: linear-gradient(135deg, #7c3aed 0%, #9f7aea 100%);
            color: #fff;
            box-shadow: 0 6px 18px rgba(124,58,237,0.25);
            border: 1px solid rgba(255,255,255,0.06);
        }

        body.dark-theme .btn-secondary {
            background: rgba(255,255,255,0.03);
            color: #e6eefc;
            border: 1px solid rgba(255,255,255,0.06);
        }

        /* Make sure search suggestions are readable in both themes */
        .search-suggestions a, .search-suggestions div {
            color: #2d3748;
        }
        body.dark-theme .search-suggestions a, body.dark-theme .search-suggestions div {
            color: #e6eefc;
        }

        /* Avoid header text blending into white backgrounds */
        .site-header, .header-content, .main-nav a, .user-actions .btn-text, .logo span {
            color: inherit;
        }

        body.dark-theme .dropdown a:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }

        /* Search input in dark mode */
        body.dark-theme .search-input {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.04);
            color: #e6eefc;
        }

        body.dark-theme .search-suggestions {
            background: rgba(6,8,12,0.95);
            border: 1px solid rgba(255,255,255,0.04);
            color: #e6eefc;
        }

        /* Explicit light-mode rules to avoid text blending into white backgrounds */
        body:not(.dark-theme) .main-nav a {
            color: #2d3748; /* dark slate */
        }

        body:not(.dark-theme) .main-nav a:hover {
            background: #f7fafc;
            color: #667eea;
        }

        body:not(.dark-theme) .dropdown {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #2d3748;
        }

        body:not(.dark-theme) .dropdown a {
            color: #4a5568;
        }

        body:not(.dark-theme) .user-actions .btn-text {
            color: #2d3748;
        }

        body:not(.dark-theme) .search-suggestions a,
        body:not(.dark-theme) .search-suggestions div {
            color: #2d3748;
        }
    </style>
    <style>
    /* Enhanced Search Toggle Button - Visible in both light and dark themes */
    .search-toggle-btn{
        background: linear-gradient(135deg, rgba(102,126,234,0.1), rgba(124,58,237,0.1));
        border: 1px solid rgba(102,126,234,0.2);
        color: #667eea;
        cursor: pointer;
        font-size: 1rem;
        padding: 0.5rem 0.6rem;
        margin-left: 0.25rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .search-toggle-btn:hover{
        background: linear-gradient(135deg, rgba(102,126,234,0.2), rgba(124,58,237,0.2));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102,126,234,0.3);
    }

    body.dark-theme .search-toggle-btn{
        background: linear-gradient(135deg, rgba(139,92,246,0.15), rgba(59,130,246,0.15));
        border: 1px solid rgba(139,92,246,0.3);
        color: #a78bfa;
    }

    body.dark-theme .search-toggle-btn:hover{
        background: linear-gradient(135deg, rgba(139,92,246,0.25), rgba(59,130,246,0.25));
        box-shadow: 0 4px 12px rgba(139,92,246,0.4);
    }

    /* Beautiful Glassmorphism Search Modal */
    .search-modal{
        display: none;
        position: fixed;
        z-index: 1200;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        background: linear-gradient(135deg, 
            rgba(102,126,234,0.1) 0%, 
            rgba(124,58,237,0.1) 50%, 
            rgba(16,185,129,0.1) 100%);
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .search-modal.active{ display: flex; }

    .search-modal .modal-content{
        background: linear-gradient(135deg, 
            rgba(255,255,255,0.25) 0%, 
            rgba(255,255,255,0.1) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.18);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25),
                    0 0 0 1px rgba(255,255,255,0.05) inset;
        width: min(800px, 95%);
        max-height: 85vh;
        border-radius: 20px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .search-modal .modal-content::before{
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(255,255,255,0.6), 
            transparent);
    }

    body.dark-theme .search-modal .modal-content{
        background: linear-gradient(135deg, 
            rgba(15,23,42,0.4) 0%, 
            rgba(30,41,59,0.2) 100%);
        border: 1px solid rgba(148,163,184,0.18);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6),
                    0 0 0 1px rgba(148,163,184,0.05) inset;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .search-modal .modal-input{
        width: 100%;
        padding: 1.25rem 1.5rem;
        font-size: 1.1rem;
        border: 2px solid rgba(102,126,234,0.2);
        border-radius: 12px;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        box-sizing: border-box;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    /* Animated gradient border effect */
    .search-modal .modal-input::before{
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #f5576c, #10b981, #3b82f6, #667eea);
        background-size: 400% 400%;
        border-radius: 14px;
        z-index: -1;
        animation: gradientBorder 3s ease infinite;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .search-modal .modal-input:focus::before{
        opacity: 1;
    }

    @keyframes gradientBorder {
        0% { background-position: 0% 50%; }
        25% { background-position: 100% 50%; }
        50% { background-position: 100% 100%; }
        75% { background-position: 0% 100%; }
        100% { background-position: 0% 50%; }
    }

    body.dark-theme .search-modal .modal-input::before{
        background: linear-gradient(45deg, #a78bfa, #3b82f6, #06b6d4, #10b981, #f59e0b, #ef4444, #a78bfa);
        background-size: 400% 400%;
    }

    .search-modal .modal-input:focus{
        outline: none;
        border-color: rgba(102,126,234,0.6);
        box-shadow: 0 0 0 3px rgba(102,126,234,0.1),
                    0 4px 20px rgba(102,126,234,0.15);
        background: rgba(255,255,255,0.95);
    }

    body.dark-theme .search-modal .modal-input{
        background: rgba(15,23,42,0.6);
        border: 2px solid rgba(139,92,246,0.3);
        color: #e2e8f0;
    }

    body.dark-theme .search-modal .modal-input:focus{
        border-color: rgba(139,92,246,0.6);
        box-shadow: 0 0 0 3px rgba(139,92,246,0.1),
                    0 4px 20px rgba(139,92,246,0.15);
        background: rgba(15,23,42,0.8);
    }

    .search-modal .modal-close{
        position: absolute;
        right: 0.75rem;
        top: 0.75rem;
        background: linear-gradient(135deg, rgba(239,68,68,0.9), rgba(220,38,38,0.9));
        border: 2px solid rgba(255,255,255,0.2);
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(239,68,68,0.4), 0 0 0 2px rgba(255,255,255,0.1);
        z-index: 15;
    }

    .search-modal .modal-close:hover{
        background: linear-gradient(135deg, rgba(220,38,38,1), rgba(185,28,28,1));
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 6px 25px rgba(239,68,68,0.6), 0 0 0 2px rgba(255,255,255,0.2);
    }

    body.dark-theme .search-modal .modal-close{
        background: linear-gradient(135deg, rgba(248,113,113,0.9), rgba(239,68,68,0.9));
        border: 2px solid rgba(148,163,184,0.2);
        color: white;
        box-shadow: 0 4px 20px rgba(248,113,113,0.4), 0 0 0 2px rgba(148,163,184,0.1);
    }

    body.dark-theme .search-modal .modal-close:hover{
        background: linear-gradient(135deg, rgba(239,68,68,1), rgba(220,38,38,1));
        box-shadow: 0 6px 25px rgba(248,113,113,0.6), 0 0 0 2px rgba(148,163,184,0.2);
    }

    @media (max-width: 768px) {
        .search-modal .modal-close {
            right: 0.5rem;
            top: 0.5rem;
            width: 32px;
            height: 32px;
            font-size: 0.7rem;
        }
    }

    /* Search Results Styling */
    .search-result-item{
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(102,126,234,0.1);
        transition: all 0.2s ease;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        background: rgba(255,255,255,0.5);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .search-result-item:hover{
        background: rgba(102,126,234,0.08);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(102,126,234,0.1);
    }

    body.dark-theme .search-result-item{
        background: rgba(15,23,42,0.3);
        border-bottom: 1px solid rgba(139,92,246,0.1);
    }

    body.dark-theme .search-result-item:hover{
        background: rgba(139,92,246,0.1);
        box-shadow: 0 4px 12px rgba(139,92,246,0.2);
    }

    .search-result-item a{
        color: inherit;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .category-badge, .subcategory-badge{
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 20px;
        margin-right: 0.5rem;
        display: inline-block;
        font-weight: 500;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .category-badge{
        background: linear-gradient(135deg, rgba(102,126,234,0.2), rgba(124,58,237,0.2));
        color: #5b67d8;
        border: 1px solid rgba(102,126,234,0.3);
    }

    .subcategory-badge{
        background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(5,150,105,0.2));
        color: #059669;
        border: 1px solid rgba(16,185,129,0.3);
    }

    body.dark-theme .category-badge{
        background: linear-gradient(135deg, rgba(139,92,246,0.2), rgba(124,58,237,0.2));
        color: #a78bfa;
        border: 1px solid rgba(139,92,246,0.3);
    }

    body.dark-theme .subcategory-badge{
        background: linear-gradient(135deg, rgba(52,211,153,0.2), rgba(34,197,94,0.2));
        color: #34d399;
        border: 1px solid rgba(52,211,153,0.3);
    }

    @media (min-width: 769px){
    /* Hide the inline search on wider screens; use the search icon */
    .search-container{ display: none; }
    }    .profile-btn {
        background: linear-gradient(135deg, rgba(139,92,246,0.1), rgba(124,58,237,0.05));
        border: 1px solid rgba(139,92,246,0.2);
        color: #8b5cf6 !important;
        cursor: pointer;
        font-size: 0.875rem;
        padding: 0.75rem;
        border-radius: 50%;
        transition: all 0.3s ease;
        display: flex !important;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        width: 44px;
        height: 44px;
        min-width: 44px;
        min-height: 44px;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .profile-btn:hover {
        background: linear-gradient(135deg, rgba(139,92,246,0.2), rgba(124,58,237,0.1));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(139,92,246,0.3);
        color: #7c3aed !important;
    }

    body.dark-theme .profile-btn {
        background: linear-gradient(135deg, rgba(168,85,247,0.15), rgba(139,92,246,0.1));
        border: 1px solid rgba(168,85,247,0.3);
        color: #a78bfa !important;
    }

    body.dark-theme .profile-btn:hover {
        background: linear-gradient(135deg, rgba(168,85,247,0.25), rgba(139,92,246,0.15));
        color: #c084fc !important;
    }

    body.dark-theme .user-actions .user-greeting {
        background: linear-gradient(135deg, rgba(52,211,153,0.15), rgba(34,197,94,0.1));
        border: 1px solid rgba(52,211,153,0.3);
        color: #a7f3d0;
    }

    /* Quick favorites button */
    .quick-favorites-btn {
        background: linear-gradient(135deg, rgba(245,158,11,0.1), rgba(217,119,6,0.05));
        border: 1px solid rgba(245,158,11,0.2);
        color: #d97706;
        cursor: pointer;
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        margin-left: 0.25rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .quick-favorites-btn:hover {
        background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(217,119,6,0.1));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245,158,11,0.3);
        color: #b45309;
    }

    body.dark-theme .quick-favorites-btn {
        background: linear-gradient(135deg, rgba(251,191,36,0.15), rgba(245,158,11,0.1));
        border: 1px solid rgba(251,191,36,0.3);
        color: #fbbf24;
    }

    body.dark-theme .quick-favorites-btn:hover {
        background: linear-gradient(135deg, rgba(251,191,36,0.25), rgba(245,158,11,0.15));
        color: #f59e0b;
    }

    /* Favorites dropdown */
    .favorites-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        min-width: 300px;
        z-index: 1000;
        margin-top: 0.5rem;
    }

    .favorites-dropdown.active { display: block; }

    .favorites-dropdown .dropdown-header {
        padding: 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.1);
        font-weight: 600;
        color: #374151;
    }

    .favorites-dropdown .favorite-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .favorites-dropdown .favorite-item:hover {
        background: rgba(102,126,234,0.1);
    }

    .favorites-dropdown .favorite-item:last-child {
        border-bottom: none;
    }

    body.dark-theme .favorites-dropdown {
        background: rgba(15,23,42,0.95);
        border: 1px solid rgba(148,163,184,0.2);
    }

    body.dark-theme .favorites-dropdown .dropdown-header {
        color: #e2e8f0;
        border-bottom-color: rgba(148,163,184,0.2);
    }

    body.dark-theme .favorites-dropdown .favorite-item {
        border-bottom-color: rgba(148,163,184,0.1);
    }

    /* Help button */
    .help-btn {
        background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(37,99,235,0.05));
        border: 1px solid rgba(59,130,246,0.2);
        color: #2563eb;
        cursor: pointer;
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        margin-left: 0.25rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .help-btn:hover {
        background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(37,99,235,0.1));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59,130,246,0.3);
        color: #1d4ed8;
    }

    body.dark-theme .help-btn {
        background: linear-gradient(135deg, rgba(96,165,250,0.15), rgba(59,130,246,0.1));
        border: 1px solid rgba(96,165,250,0.3);
m        color: #93c5fd;
    }

    body.dark-theme .help-btn:hover {
        background: linear-gradient(135deg, rgba(96,165,250,0.25), rgba(59,130,246,0.15));
        color: #60a5fa;
    }

    /* Profile dropdown */
    .profile-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        min-width: 200px;
        z-index: 1000;
        margin-top: 0.5rem;
        overflow: hidden;
    }

    .profile-dropdown.active { display: block; }

    .profile-dropdown .menu-item {
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.2s ease;
        cursor: pointer;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        text-decoration: none;
        color: inherit;
    }

    .profile-dropdown .menu-item:hover {
        background: rgba(102,126,234,0.1);
    }

    .profile-dropdown .menu-item:last-child {
        border-bottom: none;
    }

    .profile-dropdown .menu-item i {
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    .profile-dropdown .menu-item .text {
        font-weight: 500;
    }

    body.dark-theme .profile-dropdown {
        background: rgba(15,23,42,0.95);
        border: 1px solid rgba(148,163,184,0.2);
    }

    body.dark-theme .profile-dropdown .menu-item {
        border-bottom-color: rgba(148,163,184,0.1);
    }

    body.dark-theme .profile-dropdown .menu-item:hover {
        background: rgba(139,92,246,0.1);
    }

    /* Update badge for profile dropdown */
    .update-badge {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        font-size: 0.65rem;
        padding: 0.1rem 0.4rem;
        border-radius: 10px;
        margin-left: auto;
        font-weight: 600;
        animation: pulse 2s infinite;
    }

    /* Responsive profile button */
    @media (max-width: 768px) {
        .profile-btn {
            width: 36px;
            height: 36px;
            padding: 0.5rem;
        }
        
        .profile-dropdown-wrapper {
            margin-left: 0.25rem;
        }
    }

    /* Hide inline search on desktop; use icon-triggered modal instead */
    @media (min-width: 769px) {
        .search-container { display: none; }
    }
    </style>
</head>
<body class="<?php echo htmlspecialchars($body_class); ?>">
    <header class="site-header" id="siteHeader">
        <div class="header-content">
            <div class="header-left">
                <a href="<?php echo app_base_url('index.php'); ?>" class="logo">
                    <?php if ($header_style === 'logo_only' || $header_style === 'logo_text'): ?>
                        <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo $title_safe; ?> Logo" class="logo-img">
                    <?php endif; ?>
                    <?php if ($header_style === 'text_only' || $header_style === 'logo_text'): ?>
                        <span><?php echo htmlspecialchars($logo_text); ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="header-middle">
                <nav class="main-nav">
                    <ul>
                        <li>
                            <a href="<?php echo app_base_url('civil.php'); ?>">
                                <i class="fas fa-hard-hat"></i>
                                Civil
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('electrical.php'); ?>">
                                <i class="fas fa-bolt"></i>
                                Electrical
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('plumbing.php'); ?>">
                                <i class="fas fa-faucet"></i>
                                Plumbing
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('hvac.php'); ?>">
                                <i class="fas fa-wind"></i>
                                HVAC
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('fire.php'); ?>">
                                <i class="fas fa-fire-extinguisher"></i>
                                Fire Protection
                            </a>
                        </li>
                        <li class="has-dropdown">
                            <a href="#" aria-haspopup="true" aria-expanded="false" role="button" tabindex="0">
                                <i class="fas fa-layer-group"></i>
                                More Tools 
                                <i class="fas fa-chevron-down"></i>
                            </a>
                            <ul class="dropdown" role="menu">
                                <li role="none"><a href="<?php echo app_base_url('site.php'); ?>" class="grid-item" role="menuitem"><i class="fas fa-map-marked-alt"></i>Site Development</a></li>
                                <li role="none"><a href="<?php echo app_base_url('structural.php'); ?>" class="grid-item" role="menuitem"><i class="fas fa-building"></i>Structural Analysis</a></li>
                                <li role="none"><a href="<?php echo app_base_url('mep.php'); ?>" class="grid-item" role="menuitem"><i class="fas fa-cogs"></i>MEP Coordination</a></li>
                                <li role="none"><a href="<?php echo app_base_url('estimation.php'); ?>" class="grid-item" role="menuitem"><i class="fas fa-calculator"></i>Estimation Suite</a></li>
                                <li role="none"><a href="<?php echo app_base_url('management.php'); ?>" class="grid-item" role="menuitem"><i class="fas fa-project-diagram"></i>Management</a></li>
                            </ul>
                        </li>
                        </li>
                    </ul>
                </nav>

                <div class="search-container">
                    <input type="search" id="globalSearch" placeholder="Search 50+ engineering tools..." class="search-input">
                    <div id="searchSuggestions" class="search-suggestions">
                        <!-- Search suggestions will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <div class="header-right">
                <div class="user-actions">
                    <button class="theme-toggle-btn" id="themeToggleBtn" title="Toggle theme">
                        <i class="fas fa-moon"></i>
                    </button>
                    <button id="searchToggleBtn" class="search-toggle-btn" title="Search">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- User greeting (shown for all users) -->
                    <div class="user-greeting">
                        Hello, <strong><?php 
                            if (!empty($userName)) {
                                echo htmlspecialchars(explode(' ', $userName)[0]);
                            } else {
                                echo 'Guest';
                            }
                        ?></strong> 👋
                    </div>

                    <!-- Login Button (Only for guests) -->
                    <?php 
                    $is_logged_in = !empty($_SESSION['user']) || !empty($_SESSION['user_id']) || !empty($_SESSION['username']) || !empty($_SESSION['full_name']);
                    if (!$is_logged_in): ?>
                        <a href="<?php echo app_base_url('login.php'); ?>" class="btn btn-primary login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="btn-text">Login</span>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['user'])): ?>
                        <?php if (!empty($_SESSION['is_admin']) && $updateAvailable): ?>
                            <div class="update-notification" title="Update Available">
                                <i class="fas fa-download"></i>
                                v<?php echo htmlspecialchars($updateAvailable['latest']); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Profile Dropdown (Only for logged-in users) -->
                    <?php 
                    $is_logged_in = !empty($_SESSION['user']) || !empty($_SESSION['user_id']) || !empty($_SESSION['username']) || !empty($_SESSION['full_name']);
                    if ($is_logged_in): ?>
                        <div class="profile-dropdown-wrapper">
                            <button class="profile-btn" id="profileToggleBtn" title="Profile Menu">
                                <i class="fas fa-user-circle"></i>
                            </button>
                            <div class="profile-dropdown" id="profileDropdown">
                                <a href="<?php echo app_base_url('profile.php'); ?>" class="menu-item">
                                    <i class="fas fa-user-edit" style="color: #8b5cf6;"></i>
                                    <span class="text">Profile Settings</span>
                                </a>
                                <?php if (!empty($_SESSION['is_admin'])): ?>
                                    <a href="<?php echo app_base_url('admin/index.php'); ?>" class="menu-item">
                                        <i class="fas fa-shield-alt" style="color: #ef4444;"></i>
                                        <span class="text">Admin Panel</span>
                                    </a>
                                <?php endif; ?>
                                <a href="#" class="menu-item" id="favoritesMenuItem">
                                    <i class="fas fa-star" style="color: #f59e0b;"></i>
                                    <span class="text">Favorites</span>
                                </a>
                                <a href="#" class="menu-item" id="helpMenuItem">
                                    <i class="fas fa-question-circle" style="color: #3b82f6;"></i>
                                    <span class="text">Help</span>
                                </a>
                                <a href="<?php echo app_base_url('logout.php'); ?>" class="menu-item">
                                    <i class="fas fa-sign-out-alt" style="color: #6b7280;"></i>
                                    <span class="text">Logout</span>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button class="hamburger-btn" id="hamburgerBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
        </div>

        <div class="mobile-nav" id="mobileNav">
            <ul>
                <li><a href="<?php echo app_base_url('civil.php'); ?>"><i class="fas fa-hard-hat"></i> Civil</a></li>
                <li><a href="<?php echo app_base_url('electrical.php'); ?>"><i class="fas fa-bolt"></i> Electrical</a></li>
                <li><a href="<?php echo app_base_url('plumbing.php'); ?>"><i class="fas fa-faucet"></i> Plumbing</a></li>
                <li><a href="<?php echo app_base_url('hvac.php'); ?>"><i class="fas fa-wind"></i> HVAC</a></li>
                <li><a href="<?php echo app_base_url('fire.php'); ?>"><i class="fas fa-fire-extinguisher"></i> Fire Protection</a></li>
                <li><a href="<?php echo app_base_url('site.php'); ?>"><i class="fas fa-map-marked-alt"></i> Site Development</a></li>
                <li><a href="<?php echo app_base_url('estimation.php'); ?>"><i class="fas fa-calculator"></i> Estimation</a></li>
                <li><a href="<?php echo app_base_url('structural.php'); ?>"><i class="fas fa-building"></i> Structural</a></li>
            </ul>
        </div>
    </header>

    <!-- Search modal popup (hidden by default) -->
    <div id="searchModal" class="search-modal" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="searchModalLabel">
            <button class="modal-close" id="searchModalClose" aria-label="Close search">
                Esc
            </button>
            <input id="searchModalInput" class="modal-input" type="search" 
                   placeholder="Search calculators, tools, and utilities..." 
                   aria-label="Search" />
            <div id="searchModalResults" style="margin-top: 1rem; max-height: 60vh; overflow-y: auto;"></div>
        </div>
    </div>

    <main class="main-content">

    <script>
    // Header search modal toggle with typing effect
    (function(){
        const toggleBtn = document.getElementById('searchToggleBtn');
        const modal = document.getElementById('searchModal');
        const closeBtn = document.getElementById('searchModalClose');
        const input = document.getElementById('searchModalInput');
        const results = document.getElementById('searchModalResults');

        if(!toggleBtn || !modal) return;

        // Typing effect configuration
        const typingData = {
            phrases: [
                'Search 1 main category • 4 subcategories • 14 tools/calculators',
                'Search concrete volume • rebar calculation • foundation design',
                'Search civil tools • structural analysis • earthwork calculations',
                'Search calculators for engineering projects',
                'Search 14 engineering calculators and tools',
                'Search design tools and calculators',
                'Search engineering utilities and apps'
            ],
            typingSpeed: 50,
            deletingSpeed: 30,
            pauseBetween: 2000
        };

        let typingIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        let typingTimeout;

        function typeText() {
            const currentPhrase = typingData.phrases[typingIndex];
            
            if (isDeleting) {
                input.placeholder = currentPhrase.substring(0, charIndex - 1);
                charIndex--;
            } else {
                input.placeholder = currentPhrase.substring(0, charIndex + 1);
                charIndex++;
            }

            let timeout = isDeleting ? typingData.deletingSpeed : typingData.typingSpeed;

            if (!isDeleting && charIndex === currentPhrase.length) {
                timeout = typingData.pauseBetween;
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                typingIndex = (typingIndex + 1) % typingData.phrases.length;
                timeout = 500;
            }

            typingTimeout = setTimeout(typeText, timeout);
        }

        function startTypingEffect() {
            stopTypingEffect();
            charIndex = 0;
            isDeleting = false;
            typingIndex = 0;
            typeText();
        }

        function stopTypingEffect() {
            if (typingTimeout) {
                clearTimeout(typingTimeout);
            }
        }

        function openModal(){
            modal.classList.add('active');
            modal.setAttribute('aria-hidden','false');
            input.focus();
            input.select();
            startTypingEffect();
        }

        function closeModal(){
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden','true');
            results.innerHTML = '';
            input.value = '';
            stopTypingEffect();
            // Reset to default placeholder
            input.placeholder = 'Search calculators, tools, and utilities...';
        }

        toggleBtn.addEventListener('click', openModal);
        closeBtn && closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });

        // Add ESC key support to close modal
        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });

        // Stop typing effect when user starts typing
        input.addEventListener('input', function(){
            if (typingTimeout) {
                clearTimeout(typingTimeout);
                typingTimeout = null;
            }
            // User typed something, keep their text
        });

        let debounceTimer = null;
        input.addEventListener('input', function(){
            clearTimeout(debounceTimer);
            const q = input.value.trim();
            if(q.length < 2){ results.innerHTML = ''; return; }
            debounceTimer = setTimeout(()=>{
                // Use dynamic base URL to avoid path issues
                const baseUrl = window.location.pathname.includes('/aec-calculator/') ? '/aec-calculator' : '';
                fetch(`${baseUrl}/api/search.php?q=${encodeURIComponent(q)}`)
                .then(r=>r.json())
                .then(data=>{
                    // Render enhanced results with categories and direct links
                    if(!data || !Array.isArray(data)){ 
                        results.innerHTML = '<div style="text-align:center;padding:2rem;color:#64748b;"><i class="fas fa-exclamation-circle" style="margin-right:0.5rem;"></i>No results</div>'; 
                        return; 
                    }
                    if(data.length === 0){ 
                        results.innerHTML = '<div style="text-align:center;padding:2rem;color:#64748b;"><i class="fas fa-search" style="margin-right:0.5rem;"></i>No calculators found matching your search</div>'; 
                        return; 
                    }
                    
                    results.innerHTML = data.map(item=>{
                        const categoryBadge = item.category ? `<span class="category-badge">${item.category}</span>` : '';
                        const subcategoryBadge = item.subcategory ? `<span class="subcategory-badge">${item.subcategory}</span>` : '';
                        const snippet = item.description ? `<div style="font-size:.9rem;color:#64748b;margin-top:.4rem;line-height:1.4;">${item.description}</div>` : '';
                        const url = item.url || '#';
                        
                        return `<div class="search-result-item" style="cursor:pointer;" onclick="window.location.href='${url}'">
                            <div style="display:flex;align-items:flex-start;gap:1rem;">
                                <div style="flex:1;">
                                    <div style="font-weight:600;font-size:1rem;color:#1e293b;margin-bottom:0.5rem;">${item.name}</div>
                                    ${snippet}
                                    <div style="margin-top:0.75rem;display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                                        ${categoryBadge} ${subcategoryBadge}
                                        ${item.type === 'history' ? '<span class="history-badge" style="font-size:.75rem;color:#059669;background:rgba(5,150,105,0.1);padding:.25rem .6rem;border-radius:20px;border:1px solid rgba(5,150,105,0.2);"><i class="fas fa-history" style="margin-right:.25rem;"></i>Recent</span>' : ''}
                                    </div>
                                </div>
                                <div style="color:#94a3b8;font-size:0.875rem;flex-shrink:0;">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>`;
                    }).join('');
                }).catch(err=>{
                    results.innerHTML = '<div style="text-align:center;padding:2rem;color:#ef4444;"><i class="fas fa-exclamation-triangle" style="margin-right:0.5rem;"></i>Search failed. Please try again.</div>';
                    console.error('Search error', err);
                });
            }, 300);
        });
    })();

    // Profile dropdown functionality with hover and click
    (function(){
        const profileToggle = document.getElementById('profileToggleBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        const profileWrapper = document.querySelector('.profile-dropdown-wrapper');

        // Profile dropdown toggle
        if (profileToggle && profileDropdown && profileWrapper) {
            let dropdownTimeout;
            
            // Show on hover
            profileWrapper.addEventListener('mouseenter', function() {
                clearTimeout(dropdownTimeout);
                profileDropdown.classList.add('active');
            });

            // Hide on mouse leave
            profileWrapper.addEventListener('mouseleave', function() {
                dropdownTimeout = setTimeout(() => {
                    profileDropdown.classList.remove('active');
                }, 200);
            });

            // Toggle on click
            profileToggle.addEventListener('click', function(e){
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            // Close profile dropdown when clicking outside
            document.addEventListener('click', function(e){
                if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('active');
                }
            });
        }
    })();

    // Favorites and Help functionality
    (function(){
        const favoritesItem = document.getElementById('favoritesMenuItem');
        const helpItem = document.getElementById('helpMenuItem');

        if (favoritesItem) {
            favoritesItem.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Favorites feature coming soon!');
            });
        }

        if (helpItem) {
            helpItem.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Help documentation coming soon!');
            });
        }
    })();

    // Global function to open search modal from favorites
    window.openSearchModal = function() {
        const searchModal = document.getElementById('searchModal');
        const searchToggleBtn = document.getElementById('searchToggleBtn');
        if (searchModal && searchToggleBtn) {
            searchToggleBtn.click();
        }
    };

        // Enhanced JavaScript for header functionality
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.getElementById('siteHeader');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const mobileNav = document.getElementById('mobileNav');
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const globalSearch = document.getElementById('globalSearch');
            const searchSuggestions = document.getElementById('searchSuggestions');

            // Scroll effect for header
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

            // Mobile menu toggle
            hamburgerBtn.addEventListener('click', function() {
                mobileNav.classList.toggle('active');
                this.innerHTML = mobileNav.classList.contains('active') ? 
                    '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
            });

            // Theme toggle
            themeToggleBtn.addEventListener('click', function() {
                const isDark = document.body.classList.toggle('dark-theme');
                this.innerHTML = isDark ? 
                    '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                
                // Save theme preference
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                // Also persist to a cookie so server-side can read it on next page load
                const expires = new Date();
                expires.setFullYear(expires.getFullYear() + 1);
                document.cookie = `site_theme=${isDark ? 'dark' : 'light'}; path=/; expires=${expires.toUTCString()}; samesite=Lax`;
            });

            // Load saved theme or default to dark
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || !savedTheme) {
                document.body.classList.add('dark-theme');
                themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            }

            // Enhanced search functionality 
            let searchTimer = null;

            function displaySearchResults(results) {
                if (results.length === 0) {
                    searchSuggestions.innerHTML = '<div class="p-3 text-gray-500">No tools found</div>';
                    return;
                }

                searchSuggestions.innerHTML = results.map(result => `
                    <a href="${result.url}" class="block p-3 hover:bg-blue-50 border-b border-gray-100 last:border-0">
                        <div class="font-medium text-gray-800">${result.name}</div>
                        <div class="text-sm text-gray-500">${result.category}</div>
                    </a>
                `).join('');
            }

            // Close mobile menu and any open dropdowns when clicking outside the header
            document.addEventListener('click', function(event) {
                if (!header.contains(event.target)) {
                    mobileNav.classList.remove('active');
                    hamburgerBtn.innerHTML = '<i class="fas fa-bars"></i>';
                    // Close any open dropdowns
                    document.querySelectorAll('.has-dropdown.open').forEach(d => d.classList.remove('open'));
                    // Update aria-expanded attributes
                    document.querySelectorAll('.has-dropdown [aria-expanded="true"]').forEach(el => el.setAttribute('aria-expanded', 'false'));
                }
            });

            // Enhanced dropdown functionality for both hover and click
            document.querySelectorAll('.has-dropdown').forEach(dropdown => {
                const trigger = dropdown.querySelector('[role="button"]');
                const menu = dropdown.querySelector('.dropdown');
                
                // Show on hover for desktop
                dropdown.addEventListener('mouseenter', () => {
                    if (window.innerWidth > 768) {
                        dropdown.classList.add('open');
                        if (trigger) trigger.setAttribute('aria-expanded', 'true');
                    }
                });
                
                dropdown.addEventListener('mouseleave', () => {
                    if (window.innerWidth > 768) {
                        dropdown.classList.remove('open');
                        if (trigger) trigger.setAttribute('aria-expanded', 'false');
                    }
                });
                
                // Toggle on click for mobile/touch
                trigger.addEventListener('click', (e) => {
                    if (window.innerWidth <= 768 || 'ontouchstart' in window) {
                        e.preventDefault();
                        const wasOpen = dropdown.classList.contains('open');
                        
                        // Close all other dropdowns
                        document.querySelectorAll('.has-dropdown.open').forEach(d => {
                            if (d !== dropdown) {
                                d.classList.remove('open');
                                const t = d.querySelector('[aria-expanded]');
                                if (t) t.setAttribute('aria-expanded', 'false');
                            }
                        });
                        
                        // Toggle this dropdown
                        dropdown.classList.toggle('open');
                        trigger.setAttribute('aria-expanded', !wasOpen);
                    }
                });

                // Keyboard support (Enter/Space)
                if (trigger) {
                    trigger.addEventListener('keydown', function(ev) {
                        if (ev.key === 'Enter' || ev.key === ' ') {
                            ev.preventDefault();
                            drop.classList.toggle('open');
                            trigger.setAttribute('aria-expanded', drop.classList.contains('open') ? 'true' : 'false');
                        }
                    });
                }
            });

            // Header dynamic update helpers (allow login page to refresh header without full reload)
            const HEADER_STATUS_URL = '<?php echo app_base_url('api/header_status.php'); ?>';

            function buildUserActionsHtml(user, isAdmin) {
                const name = user.name || '';
                const initial = user.initial || (name ? name.charAt(0).toUpperCase() : 'U');
                const role = user.role || '';

                return `
                    <div class="has-dropdown">
                        <div class="user-avatar" title="${escapeHtml(name)}">${escapeHtml(initial)}</div>
                        <ul class="dropdown user-dropdown">
                            <li class="user-info">
                                <div class="user-name">${escapeHtml(name)}</div>
                                ${role ? `<div class="user-role">${escapeHtml(role)}</div>` : ''}
                            </li>
                            ${isAdmin ? `<li><a href="${escapeHtml('<?php echo app_base_url("admin/index.php"); ?>')}"><i class="fas fa-cog"></i> Admin Panel</a></li>` : ''}
                            <li><a href="<?php echo app_base_url('dashboard.php'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li><a href="<?php echo app_base_url('profile.php'); ?>"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
                            <li><a href="<?php echo app_base_url('logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                `;
            }

                // Allow immediate header update using server-returned user object (from login API)
                window.applyHeaderUser = function(serverUser, isAdmin) {
                    if (!serverUser) return;
                    // serverUser may contain full_name, username or name/initial from header_status
                    var name = serverUser.full_name || serverUser.name || serverUser.username || '';
                    var initial = serverUser.initial || (name ? name.charAt(0).toUpperCase() : 'U');
                    var role = serverUser.role || serverUser.user_role || serverUser.role_name || '';
                    var userObj = { name: name, initial: initial, role: role };
                    try {
                        const ua = document.querySelector('.user-actions');
                        if (!ua) return;
                        ua.innerHTML = buildUserActionsHtml(userObj, !!isAdmin);
                    } catch (e) {
                        console.warn('applyHeaderUser failed', e);
                    }
                };

            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/[&<>"'`]/g, function (s) {
                    return ({'&':'&','<':'<','>':'>','"':'"',"'":'&#39;', '`':'&#96;'})[s];
                });
            }

            window.refreshHeaderFromServer = async function() {
                try {
                    const res = await fetch(HEADER_STATUS_URL, { credentials: 'include' });
                    if (!res.ok) return;
                    const data = await res.json();
                    const ua = document.querySelector('.user-actions');
                    if (!ua) return;

                    if (data.logged_in) {
                        ua.innerHTML = buildUserActionsHtml(data.user, data.is_admin);
                    } else {
                        ua.innerHTML = '<a href="<?php echo app_base_url('login.php'); ?>" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a>';
                    }
                } catch (e) {
                    console.warn('refreshHeaderFromServer failed', e);
                }
            };

            // Apply logged-out header - now shows login button
            if (!document.querySelector('.user-actions .has-dropdown')) {
                // No user actions dropdown found, means not logged in
            }

            // Intercept logout link clicks to update header immediately (AJAX-friendly)
            document.addEventListener('click', function(ev){
                const a = ev.target.closest && ev.target.closest('a');
                if (!a) return;
                const href = a.getAttribute('href') || '';
                if (href.indexOf('logout.php') !== -1) {
                    // Prevent default navigation to allow immediate header update
                    ev.preventDefault();
                    (async function(){
                        try {
                            // Call logout endpoint (GET). include credentials to ensure session is destroyed.
                            await fetch(href, { method: 'GET', credentials: 'include' });
                        } catch (e) {
                            // ignore network errors
                        }
                        // Update header - show login button
                        try {
                            const ua = document.querySelector('.user-actions');
                            if (ua) ua.innerHTML = '<a href="<?php echo app_base_url('login.php'); ?>" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a>';
                        } catch (e) {
                            console.warn('Header update failed', e);
                        }
                        // Then navigate to the logout href (redirect) or homepage
                        window.location.href = href || '<?php echo app_base_url('index.php'); ?>';
                    })();
                }
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(event) {
                // Ctrl+K or / for search
                if ((event.ctrlKey && event.key === 'k') || event.key === '/') {
                    event.preventDefault();
                    globalSearch.focus();
                }
                
                // Escape to close mobile menu
                if (event.key === 'Escape') {
                    mobileNav.classList.remove('active');
                    hamburgerBtn.innerHTML = '<i class="fas fa-bars"></i>';
                }
            });

            // Add search shortcut hint
            globalSearch.addEventListener('focus', function() {
                this.setAttribute('placeholder', 'Press Ctrl+K to search quickly...');
            });

            globalSearch.addEventListener('blur', function() {
                this.setAttribute('placeholder', 'Search 50+ engineering tools...');
            });
        });
    </script>
</body>
</html>
