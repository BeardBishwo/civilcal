<?php
// Safe session start
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

// Get base path (4 levels up from partials: partials -> views -> default -> themes -> root)
$basePath = dirname(__DIR__, 4);

require_once $basePath . "/app/Config/config.php";
require_once $basePath . "/app/Helpers/functions.php";
// Load VersionChecker if it exists
$versionCheckerPath = $basePath . "/app/Services/VersionChecker.php";
if (file_exists($versionCheckerPath)) {
    require_once $versionCheckerPath;
}

// Create ThemeManager instance for CSS/JS loading
try {
    $themeManager = new \App\Services\ThemeManager();
} catch (Exception $e) {
    // Fallback if ThemeManager fails
    error_log("ThemeManager Error: " . $e->getMessage());
    $themeManager = null;
}

// Include theme helper functions
require_once __DIR__ . "/theme-helpers.php";

// Check for updates if admin is logged in
$updateAvailable = null;
if (!empty($_SESSION["is_admin"])) {
    $updateAvailable = VersionChecker::checkForUpdates();
}

$site_meta = get_site_meta();
$title_safe = htmlspecialchars(
    $page_title ?? ($site_meta["title"] ?? "AEC Calculator"),
);
$desc_safe = htmlspecialchars(
    $site_meta["description"] ?? "Professional Engineering Calculators Suite",
);
$logo = $site_meta["logo"] ?? app_base_url("public/theme-assets.php?path=default/assets/images/logo.png");
$logo_text = $site_meta["logo_text"] ?? (\App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro') ?: 'Calc Pro');
$header_style = $site_meta["header_style"] ?? "logo_only";
$favicon = $site_meta["favicon"] ?? app_base_url("themes/default/assets/images/favicon.png");

// User data for personalized UI
// Support both new structure ($_SESSION['user']) and legacy session keys (user_id, username, full_name, role)
$user = [];
if (!empty($_SESSION["user"]) && is_array($_SESSION["user"])) {
    $user = $_SESSION["user"];
} else {
    // Build a user array from legacy session vars if present
    if (
        !empty($_SESSION["user_id"]) ||
        !empty($_SESSION["username"]) ||
        !empty($_SESSION["full_name"])
    ) {
        $user = [
            "id" => $_SESSION["user_id"] ?? null,
            "username" => $_SESSION["username"] ?? "",
            "full_name" => $_SESSION["full_name"] ?? "",
            "role" => $_SESSION["role"] ?? "",
        ];
    }
}

$userName = trim($user["full_name"] ?? ($user["username"] ?? ""));
$userInitial = !empty($userName) ? strtoupper(substr($userName, 0, 1)) : "";
$userRole = $user["role"] ?? "";
$engineerRoles = $user["engineer_roles"] ?? [];

// Calculate search statistics
$search_stats = ["categories" => 0, "subcategories" => 0, "tools" => 0];
$modules_dir = __DIR__ . "/../modules";
if (is_dir($modules_dir)) {
    $categories = scandir($modules_dir);
    foreach ($categories as $category) {
        if (
            $category === "." ||
            $category === ".." ||
            !is_dir($modules_dir . "/" . $category)
        ) {
            continue;
        }
        $search_stats["categories"]++;

        $subcategories = scandir($modules_dir . "/" . $category);
        foreach ($subcategories as $subcategory) {
            if (
                $subcategory === "." ||
                $subcategory === ".." ||
                !is_dir($modules_dir . "/" . $category . "/" . $subcategory)
            ) {
                continue;
            }
            $search_stats["subcategories"]++;

            $files = scandir(
                $modules_dir . "/" . $category . "/" . $subcategory,
            );
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === "php") {
                    $search_stats["tools"]++;
                }
            }
        }
    }
}

// Ensure is_admin flag is available (legacy support)
if (
    empty($_SESSION["is_admin"]) &&
    !empty($userRole) &&
    strtolower($userRole) === "admin"
) {
    $_SESSION["is_admin"] = true;
}
// Server-side theme detection via cookie (so first render can have the correct theme)
$body_class = "";
if (!empty($_COOKIE["site_theme"]) && $_COOKIE["site_theme"] === "dark") {
    $body_class = "dark-theme";
}

// Mark homepage body with 'index-page' so home-specific gradient styles apply
$__req_path = parse_url($_SERVER["REQUEST_URI"] ?? "", PHP_URL_PATH);
$__base = defined("APP_BASE") ? rtrim(APP_BASE, "/") : "";
if (
    $__req_path === $__base ||
    $__req_path === $__base . "/" ||
    substr($__req_path, -10) === "/index.php"
) {
    $body_class = trim($body_class . " index-page");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_safe; ?></title>
    <meta name="description" content="<?php echo $desc_safe; ?>">
    <link rel="manifest" href="<?php echo app_base_url("manifest.json"); ?>">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <script>
        window.appConfig = {
            baseUrl: "<?php echo rtrim(defined('APP_BASE') ? APP_BASE : '', '/'); ?>",
            csrfToken: "<?php echo csrf_token(); ?>"
        };
    </script>
    <meta name="theme-color" content="#667eea">
    <link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo app_base_url('public/assets/css/global-notifications.css'); ?>">
    <?php
    // Load CSS files via ThemeManager proxy to ensure correct URL resolution
    // for both Laragon (document root c:\laragon\www) and built-in server (document root public/)
    $cssFiles = [
        "theme.css",
        "footer.css",
        "back-to-top.css",
        "home.css",
        "logo-enhanced.css",
    ];
    foreach ($cssFiles as $css) {
        $cssPath = dirname(__DIR__) . "/assets/css/" . $css;
        $mtime = file_exists($cssPath) ? filemtime($cssPath) : time();

        // Use ThemeManager to generate the correct proxy URL
        if ($themeManager) {
            $url = $themeManager->themeUrl("assets/css/" . $css . "?v=" . $mtime);
        } else {
            // Fallback if ThemeManager is unavailable
            $url = app_base_url(
                "themes/default/assets/css/" . $css . "?v=" . $mtime,
            );
        }

        echo '<link rel="stylesheet" href="' .
            htmlspecialchars($url) .
            '">' .
            "\n    ";
    }
    ?>
    <style>
        /* Logo CSS Variables from Admin Settings */
        :root {
            --logo-spacing: <?php echo $site_meta["logo_settings"]["spacing"] ??
                                "12px"; ?>;
            --logo-text-weight: <?php echo $site_meta["logo_settings"]["text_weight"] ?? "700"; ?>;
            --logo-text-size: <?php echo $site_meta["logo_settings"]["text_size"] ?? "1.5rem"; ?>;
            --logo-height: <?php echo $site_meta["logo_settings"]["logo_height"] ?? "40px"; ?>;
            --logo-border-radius: <?php echo $site_meta["logo_settings"]["border_radius"] ?? "8px"; ?>;
            --brand-primary: <?php echo $site_meta["brand_colors"]["primary"] ??
                                    "#4f46e5"; ?>;
            --brand-secondary: <?php echo $site_meta["brand_colors"]["secondary"] ?? "#10b981"; ?>;
            --brand-accent: <?php echo $site_meta["brand_colors"]["accent"] ??
                                "#f59e0b"; ?>;
        }
    </style>
    <style>
        /* Enhanced Header Styles */
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.1);
            --shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .site-header {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            width: 100%;
            left: 0;
            right: 0;
        }

        /* Light theme header */
        body:not(.dark-theme) .site-header {
            background: rgba(255, 255, 255, 0.98);
            border-bottom: 1px solid #e2e8f0;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        body:not(.dark-theme) .site-header .logo-text {
            color: #0f172a !important;
        }

        /* Dark theme header */
        body.dark-theme .site-header {
            background: rgba(6, 8, 12, 0.72);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            box-shadow: 0 8px 30px rgba(2, 6, 23, 0.6);
            color: #e6eefc;
        }

        /* Override theme.css max-width constraint for true full width */
        .site-header .header-content,
        .header-content {
            max-width: none !important;
            width: calc(100% - 20px) !important;
            margin: 0 10px !important;
            box-sizing: border-box;
            padding: 0 10px;
        }

        .header-content {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            box-sizing: border-box;
            min-width: 0;
            flex-wrap: nowrap;
        }

        /* Ensure no body/html margins interfere */
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            overflow-x: hidden;
        }

        /* Theme-aware body backgrounds */
        body {
            min-height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* Light theme (default) */
        body:not(.dark-theme) {
            background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 50%, #e1e8ed 100%) !important;
            color: #1a202c;
        }

        body:not(.dark-theme) h1,
        body:not(.dark-theme) h2,
        body:not(.dark-theme) h3,
        body:not(.dark-theme) h4,
        body:not(.dark-theme) h5,
        body:not(.dark-theme) h6 {
            color: #0f172a !important;
        }

        body:not(.dark-theme) p,
        body:not(.dark-theme) span,
        body:not(.dark-theme) .text-primary {
            color: #334155 !important;
        }

        body:not(.dark-theme) a {
            color: #3b82f6 !important;
        }

        body:not(.dark-theme) .card,
        body:not(.dark-theme) .section {
            background: #ffffff !important;
            color: #1e293b !important;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Dark theme */
        body.dark-theme {
            background: linear-gradient(135deg, #0a0e27, #1a1a4d, #0f0f2e) !important;
            color: #e2e8f0;
        }

        /* Ensure main content area follows theme */
        .main-content {
            min-height: calc(100vh - 60px);
            transition: background 0.3s ease;
        }

        body:not(.dark-theme) .main-content {
            background: inherit;
        }

        body.dark-theme .main-content {
            background: inherit;
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
                display: flex;
                flex: 1 1 auto;
                min-width: 0;
                overflow: visible;
            }
        }

        @media (max-width: 480px) {
            .header-left {
                flex: 0 0 110px;
                max-width: 110px;
            }
        }

        .header-left {
            /* Reduced base width */
            flex: 0 0 auto; /* Allow it to shrink/grow with content */
            min-width: 80px; /* Minimum width for square logo */
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center; /* Center horizontally */
            padding: 0 !important; /* Remove all padding for true centering */
            box-sizing: border-box;
            height: 100%; /* Fill header height */
        }

        .logo {
            display: flex;
            align-items: center;
            gap: <?php echo $site_meta["logo_settings"]["spacing"] ??
                        "12px"; ?>;
            text-decoration: none;
            font-weight: <?php echo $site_meta["logo_settings"]["text_weight"] ?? "700"; ?>;
            font-size: <?php echo $site_meta["logo_settings"]["text_size"] ??
                            "1.5rem"; ?>;
            color: #2d3748;
            color: #2d3748;
            /* transition removed */
            padding: 0 !important; /* Remove padding to maximize size */
            margin: 0 !important;
            height: 100%; /* Fill parent height */
            border-radius: <?php echo $site_meta["logo_settings"]["border_radius"] ?? "0"; ?>;
            justify-content: center;
            overflow: visible;
        }

        /* Ensure image logos scale correctly */
        .logo img {
            height: 100%;
            width: auto;
            max-height: 100%;
            object-fit: contain;
            display: block;
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
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .has-dropdown.open .dropdown {
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
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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

        .dropdown>li {
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

        /* Responsive dropdown for mobile/tablet */
        @media (max-width: 768px) {
            .dropdown {
                min-width: 180px;
                left: auto;
                right: 0;
            }

            .has-dropdown {
                position: relative;
            }

            .has-dropdown a {
                display: flex;
                align-items: center;
                gap: 0.35rem;
            }

            .has-dropdown a i.fa-layer-group {
                font-size: 0.875rem;
            }

            .has-dropdown a .fa-chevron-down {
                font-size: 0.75rem;
                margin-left: 0.2rem;
            }
        }

        .search-container {
            position: relative;
            flex: 1 1 360px;
            max-width: 420px;
            margin-right: 1rem;
            /* prevent overlap with header-right */
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

            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .search-input:focus+.search-suggestions,
        .search-suggestions:hover {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .header-middle {
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            min-width: 0;
            overflow: visible;
            background: inherit;
            max-width: calc(100% - 400px);
            position: relative;
            z-index: 1100;
        }

        .user-actions {
            display: flex;
            align-items: center;
            /* consistent equal spacing between all elements */
            gap: 0.75rem;
            flex-shrink: 0;
            flex-wrap: nowrap;
            width: auto;
            /* don't force full width which can push content */
            justify-content: flex-end;
            padding: 0 1rem;
            /* increased padding to prevent scrollbar overlap */
            margin-right: 15px;
            /* extra margin to ensure login button is visible */
            overflow: visible;
        }

        /* Move user-greeting inside user-actions */
        .user-actions .user-greeting {
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            font-weight: 500;
            flex-shrink: 1;
            min-width: 0;
            max-width: 200px;
            text-align: center;
            line-height: 1.2;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            /* Allow text to wrap to multiple lines */
            white-space: normal;
            /* Auto-fit text size based on container width */
            font-size: clamp(0.7rem, 2vw, 0.8rem);
        }

        /* Auto-resize text for very long names */
        .user-actions .user-greeting strong {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            white-space: nowrap;
        }

        /* Two-line layout for longer names */
        @media (max-width: 900px) {
            .user-actions .user-greeting {
                flex-direction: column;
                padding: 0.3rem 0.6rem;
                gap: 0.1rem;
                min-height: 2.2rem;
                justify-content: center;
            }

            .user-actions .user-greeting strong {
                max-width: 100px;
                font-size: 0.75rem;
            }
        }

        /* Profile dropdown wrapper inside user-actions */
        .user-actions .profile-dropdown-wrapper {
            flex-shrink: 0;
        }

        .theme-toggle-btn {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(139, 92, 246, 0.15));
            border: 2px solid rgba(99, 102, 241, 0.4);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: #6366f1;
            font-size: 1.2rem;
            font-weight: 600;
            position: relative;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        /* Animated gradient background */
        .theme-toggle-btn::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg,
                    transparent,
                    rgba(99, 102, 241, 0.3),
                    transparent);
            transform: rotate(45deg);
            transition: all 0.5s ease;
            opacity: 0;
        }

        .theme-toggle-btn:hover::before {
            opacity: 1;
            animation: shimmerEffect 1.5s ease-in-out infinite;
        }

        @keyframes shimmerEffect {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }

            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        /* Tooltip styling */
        .theme-toggle-btn::after {
            content: attr(data-label);
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%) translateY(5px);
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            z-index: 1000;
        }

        .theme-toggle-btn:hover::after {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* Small search icon button for opening modal */
        .search-toggle-btn {
            background: none;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #4a5568;
            flex-shrink: 0;
            /* prevent shrinking */
        }

        .search-toggle-btn:hover {
            background: #f7fafc;
            color: #667eea;
            transform: scale(1.05);
        }

        .theme-toggle-btn:hover {
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
            color: white;
            border-color: #8b5cf6;
            transform: scale(1.1) rotate(5deg);
            box-shadow:
                0 8px 24px rgba(99, 102, 241, 0.5),
                0 0 40px rgba(168, 85, 247, 0.4);
        }

        .theme-toggle-btn:active {
            transform: scale(0.95) rotate(0deg);
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }

        .theme-toggle-btn i {
            position: relative;
            z-index: 1;
            transition: transform 0.4s ease;
        }

        .theme-toggle-btn:hover i {
            transform: rotate(20deg) scale(1.1);
        }

        body.dark-theme .theme-toggle-btn {
            color: #a855f7;
            border-color: rgba(168, 85, 247, 0.4);
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.15), rgba(236, 72, 153, 0.15));
        }

        body.dark-theme .theme-toggle-btn:hover {
            background: linear-gradient(135deg, #a855f7, #ec4899);
            color: white;
            border-color: #a855f7;
            box-shadow:
                0 8px 24px rgba(168, 85, 247, 0.5),
                0 0 40px rgba(236, 72, 153, 0.4);
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

        /* Login button consistent spacing - no extra margins */
        .login-btn {
            margin: 0;
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
            display: none !important;
            /* Force hide on desktop */
            background: none;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 1.1rem;
            color: #4a5568;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
        }

        /* Ensure hamburger is hidden on desktop screens */
        @media (min-width: 769px) {
            .hamburger-btn {
                display: none !important;
            }
        }

        .hamburger-btn:hover {
            background: #f7fafc;
            color: #667eea;
        }

        .mobile-nav {
            display: none;
            background: rgba(30, 30, 60, 0.95);
            border-top: 1px solid rgba(0, 255, 255, 0.3);
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

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .header-content {
                gap: 1rem;
                padding: 0 15px;
                /* increased padding on smaller screens */
            }

            .user-actions {
                margin-right: 10px;
                /* more margin on smaller screens */
                padding: 0 0.75rem;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                gap: 0.5rem;
                padding: 0 12px;
                /* ensure adequate padding */
            }

            .header-middle {
                display: flex;
                flex: 1 1 auto;
                min-width: 0;
                overflow: visible;
            }

            .user-actions {
                margin-right: 8px;
                gap: 0.5rem;
                /* reduce gap on mobile to fit better */
            }

            .hamburger-btn {
                display: flex !important;
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
                padding: 0.25rem 0.5rem;
                font-size: clamp(0.65rem, 1.8vw, 0.75rem);
                max-width: 150px;
                min-height: 2rem;
            }

            .user-actions .user-greeting strong {
                max-width: 80px;
                font-size: 0.7rem;
            }
        }

        /* Compact layout for small screens */
        @media (max-width: 640px) {
            .user-actions .user-greeting {
                padding: 0.2rem 0.4rem;
                font-size: 0.65rem;
                max-width: 120px;
                min-height: 1.8rem;
            }

            .user-actions .user-greeting strong {
                max-width: 60px;
                font-size: 0.65rem;
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
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1100;
            padding: 1rem;
        }

        .search-modal.active {
            display: flex;
        }

        .search-modal .modal-content {
            width: 100%;
            max-width: 760px;
            background: rgba(30, 30, 60, 0.95);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 20px 40px rgba(2, 6, 23, 0.4);
        }

        body.dark-theme .search-modal .modal-content {
            background: rgba(6, 8, 12, 0.95);
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
            --glass-bg: rgba(8, 10, 15, 0.6);
            --glass-border: rgba(255, 255, 255, 0.03);
            color: #e6eefc;
        }

        body.dark-theme .site-header {
            background: rgba(6, 8, 12, 0.72);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            box-shadow: 0 8px 30px rgba(2, 6, 23, 0.6);
            color: #e6eefc;
        }

        /* Nav links in dark mode */
        body.dark-theme .main-nav a {
            color: #cbd5e1;
        }

        /* Hover state: avoid bright white background which hides text */
        body.dark-theme .main-nav a:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* Dropdown in dark mode */
        body.dark-theme .dropdown {
            background: linear-gradient(180deg, rgba(10, 12, 16, 0.95), rgba(6, 8, 12, 0.95));
            border: 1px solid rgba(255, 255, 255, 0.04);
            color: #e6eefc;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.6);
        }

        body.dark-theme .dropdown a {
            color: #e6eefc;
        }

        /* Ensure buttons remain visible in dark mode */
        body.dark-theme .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #a78bfa 100%);
            color: #fff;
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        body.dark-theme .btn-secondary {
            background: rgba(255, 255, 255, 0.03);
            color: #e6eefc;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* Make sure search suggestions are readable in both themes */
        .search-suggestions a,
        .search-suggestions div {
            color: #2d3748;
        }

        body.dark-theme .search-suggestions a,
        body.dark-theme .search-suggestions div {
            color: #e6eefc;
        }

        /* Avoid header text blending into white backgrounds */
        .site-header,
        .header-content,
        .main-nav a,
        .user-actions .btn-text,
        .logo span {
            color: inherit;
        }

        body.dark-theme .dropdown a:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        /* Search input in dark mode */
        body.dark-theme .search-input {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.04);
            color: #e6eefc;
        }

        body.dark-theme .search-suggestions {
            background: rgba(6, 8, 12, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.04);
            color: #e6eefc;
        }

        /* Comprehensive light theme styles */
        body:not(.dark-theme) .main-nav a {
            color: #1e293b !important;
            font-weight: 500;
        }

        body:not(.dark-theme) .main-nav a:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6 !important;
        }

        body:not(.dark-theme) .dropdown {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        body:not(.dark-theme) .dropdown a {
            color: #1e293b !important;
            font-weight: 500;
        }

        body:not(.dark-theme) .dropdown a:hover {
            background: #f1f5f9;
            color: #3b82f6 !important;
        }

        body:not(.dark-theme) .dropdown .grid-item {
            border-color: #e2e8f0;
        }

        body:not(.dark-theme) .dropdown .grid-item:hover {
            border-color: #3b82f6;
        }

        body:not(.dark-theme) .user-actions .btn-text {
            color: #1e293b !important;
            font-weight: 600;
        }

        body:not(.dark-theme) .btn-primary,
        body:not(.dark-theme) button {
            color: #ffffff !important;
        }

        body:not(.dark-theme) input,
        body:not(.dark-theme) textarea,
        body:not(.dark-theme) select {
            color: #1e293b !important;
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
        }

        body:not(.dark-theme) input::placeholder,
        body:not(.dark-theme) textarea::placeholder {
            color: #94a3b8 !important;
        }

        body:not(.dark-theme) .search-suggestions a,
        body:not(.dark-theme) .search-suggestions div {
            color: #1e293b !important;
        }

        body:not(.dark-theme) .search-input {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #1e293b !important;
        }

        body:not(.dark-theme) .search-input::placeholder {
            color: #94a3b8 !important;
        }

        body:not(.dark-theme) .search-suggestions {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        body:not(.dark-theme) .user-greeting {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #1a202c;
        }

        body:not(.dark-theme) .theme-toggle-btn {
            color: #f59e0b;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(251, 191, 36, 0.15));
            border-color: rgba(245, 158, 11, 0.4);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }

        body:not(.dark-theme) .theme-toggle-btn::before {
            background: linear-gradient(45deg,
                    transparent,
                    rgba(245, 158, 11, 0.3),
                    transparent);
        }

        body:not(.dark-theme) .theme-toggle-btn:hover {
            background: linear-gradient(135deg, #f59e0b, #fbbf24, #fb923c);
            color: white;
            border-color: #f59e0b;
            box-shadow:
                0 8px 24px rgba(245, 158, 11, 0.5),
                0 0 40px rgba(251, 191, 36, 0.4);
        }

        body:not(.dark-theme) .search-toggle-btn {
            background: linear-gradient(135deg, #f7fafc, #f7fafc);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #2d3748;
            cursor: pointer;
            font-size: 1rem;
            padding: 0.5rem 0.6rem;
            margin-left: 0.25rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        body:not(.dark-theme) .search-toggle-btn:hover {
            background: linear-gradient(135deg, #f7fafc, #f7fafc);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .search-toggle-btn:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(124, 58, 237, 0.2));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        body.dark-theme .search-toggle-btn {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(59, 130, 246, 0.15));
            border: 1px solid rgba(139, 92, 246, 0.3);
            color: #a78bfa;
        }

        body.dark-theme .search-toggle-btn:hover {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.25), rgba(59, 130, 246, 0.25));
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
        }

        /* Beautiful Glassmorphism Search Modal */
        .search-modal {
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
                    rgba(102, 126, 234, 0.1) 0%,
                    rgba(124, 58, 237, 0.1) 50%,
                    rgba(16, 185, 129, 0.1) 100%);
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .search-modal.active {
            display: flex;
        }

        .search-modal .modal-content {
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.25) 0%,
                    rgba(255, 255, 255, 0.1) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.05) inset;
            width: min(800px, 95%);
            max-height: 85vh;
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.dark-theme .search-modal .modal-content {
            background: linear-gradient(135deg,
                    rgba(15, 23, 42, 0.4) 0%,
                    rgba(30, 41, 59, 0.2) 100%);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(148, 163, 184, 0.05) inset;
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

        .search-modal .modal-input {
            width: 100%;
            padding: 1.25rem 1.5rem;
            font-size: 1.1rem;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-sizing: border-box;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Animated gradient border effect */
        .search-modal .modal-input::before {
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
            transform: translateY(-20px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        }

        .search-modal .modal-input {
            width: 100%;
            padding: 1.25rem 1.5rem;
            font-size: 1.1rem;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-sizing: border-box;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Animated gradient border effect */
        .search-modal .modal-input::before {
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

        .search-modal .modal-input:focus::before {
            opacity: 1;
        }

        @keyframes gradientBorder {
            0% {
                background-position: 0% 50%;
            }

            25% {
                background-position: 100% 50%;
            }

            50% {
                background-position: 100% 100%;
            }

            75% {
                background-position: 0% 100%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        body.dark-theme .search-modal .modal-input::before {
            background: linear-gradient(45deg, #a78bfa, #3b82f6, #06b6d4, #10b981, #f59e0b, #ef4444, #a78bfa);
            background-size: 400% 400%;
        }

        .search-modal .modal-input:focus {
            outline: none;
            border-color: rgba(102, 126, 234, 0.6);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1),
                0 4px 20px rgba(102, 126, 234, 0.15);
            background: rgba(255, 255, 255, 0.95);
        }

        body.dark-theme .search-modal .modal-input {
            background: rgba(15, 23, 42, 0.6);
            border: 2px solid rgba(139, 92, 246, 0.3);
            color: #e2e8f0;
        }

        body.dark-theme .search-modal .modal-input:focus {
            border-color: rgba(139, 92, 246, 0.6);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1),
                0 4px 20px rgba(139, 92, 246, 0.15);
            background: rgba(15, 23, 42, 0.8);
        }

        .search-modal .modal-close {
            position: absolute;
            right: 0.75rem;
            top: 0.75rem;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.9), rgba(220, 38, 38, 0.9));
            border: 2px solid rgba(255, 255, 255, 0.2);
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
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4), 0 0 0 2px rgba(255, 255, 255, 0.1);
            z-index: 15;
        }

        .search-modal .modal-close:hover {
            background: linear-gradient(135deg, rgba(220, 38, 38, 1), rgba(185, 28, 28, 1));
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 6px 25px rgba(239, 68, 68, 0.6), 0 0 0 2px rgba(255, 255, 255, 0.2);
        }

        body.dark-theme .search-modal .modal-close {
            background: linear-gradient(135deg, rgba(248, 113, 113, 0.9), rgba(239, 68, 68, 0.9));
            border: 2px solid rgba(148, 163, 184, 0.2);
            color: white;
            box-shadow: 0 4px 20px rgba(248, 113, 113, 0.4), 0 0 0 2px rgba(148, 163, 184, 0.1);
        }

        body.dark-theme .search-modal .modal-close:hover {
            background: linear-gradient(135deg, rgba(239, 68, 68, 1), rgba(220, 38, 38, 1));
            box-shadow: 0 6px 25px rgba(248, 113, 113, 0.6), 0 0 0 2px rgba(148, 163, 184, 0.2);
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
        .search-result-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
            transition: all 0.2s ease;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .search-result-item:hover {
            background: rgba(102, 126, 234, 0.08);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }

        body.dark-theme .search-result-item {
            background: rgba(15, 23, 42, 0.3);
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        }

        body.dark-theme .search-result-item:hover {
            background: rgba(139, 92, 246, 0.1);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
        }

        .search-result-item a {
            color: inherit;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            display: block;
            margin-bottom: 0.5rem;
        }

        .category-badge,
        .subcategory-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            margin-right: 0.5rem;
            display: inline-block;
            font-weight: 500;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .category-badge {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(124, 58, 237, 0.2));
            color: #5b67d8;
            border: 1px solid rgba(102, 126, 234, 0.3);
        }

        .subcategory-badge {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.2));
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        body.dark-theme .category-badge {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(124, 58, 237, 0.2));
            color: #a78bfa;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        body.dark-theme .subcategory-badge {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.2), rgba(34, 197, 94, 0.2));
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        @media (min-width: 769px) {

            /* Hide the inline search on wider screens; use the search icon */
            .search-container {
                display: none;
            }
        }

        .profile-btn {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(124, 58, 237, 0.05));
            border: 1px solid rgba(139, 92, 246, 0.2);
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
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(124, 58, 237, 0.1));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
            color: #7c3aed !important;
        }

        body.dark-theme .profile-btn {
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.15), rgba(139, 92, 246, 0.1));
            border: 1px solid rgba(168, 85, 247, 0.3);
            color: #a78bfa !important;
        }

        body.dark-theme .profile-btn:hover {
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.25), rgba(139, 92, 246, 0.15));
            color: #c084fc !important;
        }

        body.dark-theme .user-actions .user-greeting {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.15), rgba(34, 197, 94, 0.1));
            border: 1px solid rgba(52, 211, 153, 0.3);
            color: #a7f3d0;
        }

        /* Quick favorites button */
        .quick-favorites-btn {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.05));
            border: 1px solid rgba(245, 158, 11, 0.2);
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
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.1));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            color: #b45309;
        }

        body.dark-theme .quick-favorites-btn {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.15), rgba(245, 158, 11, 0.1));
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: #fbbf24;
        }

        body.dark-theme .quick-favorites-btn:hover {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.25), rgba(245, 158, 11, 0.15));
            color: #f59e0b;
        }

        /* Favorites dropdown */
        .favorites-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            min-width: 300px;
            z-index: 1000;
            margin-top: 0.5rem;
        }

        .favorites-dropdown.active {
            display: block;
        }

        .favorites-dropdown .dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            font-weight: 600;
            color: #374151;
        }

        .favorites-dropdown .favorite-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .favorites-dropdown .favorite-item:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        .favorites-dropdown .favorite-item:last-child {
            border-bottom: none;
        }

        body.dark-theme .favorites-dropdown {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        body.dark-theme .favorites-dropdown .dropdown-header {
            color: #e2e8f0;
            border-bottom-color: rgba(148, 163, 184, 0.2);
        }

        body.dark-theme .favorites-dropdown .favorite-item {
            border-bottom-color: rgba(148, 163, 184, 0.1);
        }

        /* Help button */
        .help-btn {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
            border: 1px solid rgba(59, 130, 246, 0.2);
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
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(37, 99, 235, 0.1));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            color: #1d4ed8;
        }

        body.dark-theme .help-btn {
            background: linear-gradient(135deg, rgba(96, 165, 250, 0.15), rgba(59, 130, 246, 0.1));
            border: 1px solid rgba(96, 165, 250, 0.3);
            color: #93c5fd;
        }

        body.dark-theme .help-btn:hover {
            background: linear-gradient(135deg, rgba(96, 165, 250, 0.25), rgba(59, 130, 246, 0.15));
            color: #60a5fa;
        }

        /* Profile dropdown */
        .profile-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            min-width: 200px;
            z-index: 1000;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .profile-dropdown.active {
            display: block;
        }

        .profile-dropdown .menu-item {
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            text-decoration: none;
            color: inherit;
        }

        .profile-dropdown .menu-item:hover {
            background: rgba(102, 126, 234, 0.1);
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
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        body.dark-theme .profile-dropdown .menu-item {
            border-bottom-color: rgba(148, 163, 184, 0.1);
        }

        body.dark-theme .profile-dropdown .menu-item:hover {
            background: rgba(139, 92, 246, 0.1);
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
            .search-container {
                display: none;
            }
        }

        /* Notification System Styles */
        .notification-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .notification-btn {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(79, 70, 229, 0.05));
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: #6366f1 !important;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 0.75rem;
            border-radius: 50%;
            transition: all 0.3s ease;
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            min-width: 44px;
            min-height: 44px;
            position: relative;
            margin-right: 0.5rem;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .notification-btn:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.1));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            color: #4f46e5 !important;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4);
            animation: bounceIn 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .notification-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: -10px;
            width: 360px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            z-index: 1000;
            overflow: hidden;
            flex-direction: column;
            animation: dropdownSlideIn 0.2s ease;
        }

        .notification-dropdown.active {
            display: flex;
        }

        .notification-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(248, 250, 252, 0.5);
        }

        .notification-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
        }

        .mark-all-read {
            background: none;
            border: none;
            color: #6366f1;
            font-size: 0.85rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .mark-all-read:hover {
            background: rgba(99, 102, 241, 0.1);
        }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
            padding: 0;
        }

        .notification-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            display: flex;
            gap: 1rem;
            transition: all 0.2s;
            cursor: pointer;
            position: relative;
        }

        .notification-item:hover {
            background: rgba(241, 245, 249, 0.6);
        }

        .notification-item.unread {
            background: rgba(238, 242, 255, 0.4);
        }

        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #6366f1;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .bg-info-light { background: #e0f2fe; color: #0ea5e9; }
        .bg-success-light { background: #dcfce7; color: #22c55e; }
        .bg-warning-light { background: #fef3c7; color: #f59e0b; }
        .bg-danger-light { background: #fee2e2; color: #ef4444; }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-title {
            font-weight: 600;
            color: #334155;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .notification-message {
            color: #64748b;
            font-size: 0.85rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .notification-time {
            color: #94a3b8;
            font-size: 0.75rem;
            margin-top: 0.5rem;
        }

        .notification-footer {
            padding: 0.75rem;
            text-align: center;
            background: rgba(248, 250, 252, 0.5);
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .view-all-btn {
            color: #6366f1;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
        }
        
        .view-all-btn:hover {
            text-decoration: underline;
        }

        .notification-empty {
            padding: 2.5rem 1rem;
            text-align: center;
            color: #94a3b8;
        }

        .notification-empty i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Dark Theme Support */
        body.dark-theme .notification-btn {
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.15), rgba(139, 92, 246, 0.1));
            border: 1px solid rgba(168, 85, 247, 0.3);
            color: #a78bfa !important;
        }

        body.dark-theme .notification-btn:hover {
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.25), rgba(139, 92, 246, 0.15));
            color: #c084fc !important;
        }

        body.dark-theme .notification-badge {
            background: #ef4444;
            border-color: #1e293b;
        }

        body.dark-theme .notification-dropdown {
            background: rgba(15, 23, 42, 0.95);
            border-color: rgba(148, 163, 184, 0.2);
        }

        body.dark-theme .notification-header,
        body.dark-theme .notification-footer {
            background: rgba(30, 41, 59, 0.5);
            border-color: rgba(148, 163, 184, 0.1);
        }

        body.dark-theme .notification-header h3 {
            color: #e2e8f0;
        }

        body.dark-theme .notification-item {
            border-color: rgba(148, 163, 184, 0.1);
        }

        body.dark-theme .notification-item:hover {
            background: rgba(30, 41, 59, 0.4);
        }

        body.dark-theme .notification-item.unread {
            background: rgba(139, 92, 246, 0.1);
        }

        body.dark-theme .notification-title {
            color: #e2e8f0;
        }

        body.dark-theme .notification-message {
            color: #94a3b8;
        }

        body.dark-theme .bg-info-light { background: rgba(14, 165, 233, 0.2); color: #38bdf8; }
        body.dark-theme .bg-success-light { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        body.dark-theme .bg-warning-light { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        body.dark-theme .bg-danger-light { background: rgba(239, 68, 68, 0.2); color: #f87171; }

        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        @keyframes dropdownSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .notification-btn {
                width: 36px;
                height: 36px;
                padding: 0.5rem;
                margin-right: 0.25rem;
            }
            
            .notification-dropdown {
                width: 300px;
                right: -50px;
            }
        }
    </style>
</head>

<body class="<?php echo htmlspecialchars($body_class); ?>">
    <header class="site-header" id="siteHeader">
        <div class="header-content">
            <div class="header-left">
                <?php
                // Build logo classes based on admin settings
                $logoClasses = ["logo"];
                $logoStyle =
                    $site_meta["logo_settings"]["logo_style"] ?? "modern";
                $hoverEffect =
                    $site_meta["logo_settings"]["hover_effect"] ?? "scale";
                $textPosition =
                    $site_meta["logo_settings"]["text_position"] ?? "right";
                $shadow = $site_meta["logo_settings"]["shadow"] ?? "subtle";

                // Add style classes
                if ($logoStyle !== "modern") {
                    $logoClasses[] = $logoStyle . "-style";
                }

                // Add hover effect classes
                if ($hoverEffect === "glow") {
                    $logoClasses[] = "glow-effect";
                } elseif ($hoverEffect === "bounce") {
                    $logoClasses[] = "bounce-effect";
                } elseif ($hoverEffect === "pulse") {
                    $logoClasses[] = "pulse-effect";
                }

                // Add text position classes
                if ($textPosition === "bottom") {
                    $logoClasses[] = "text-bottom";
                } elseif ($textPosition === "top") {
                    $logoClasses[] = "text-top";
                }

                $logoClassString = implode(" ", $logoClasses);
                ?>
                <a href="<?php echo app_base_url(
                                "/",
                            ); ?>" class="<?php echo $logoClassString; ?>">
                    <?php
                    $imgClasses = ["logo-img"];
                    if ($shadow === "strong") {
                        $imgClasses[] = "strong-shadow";
                    }
                    ?>
                    <?php if (!empty($logo)): ?>
                        <img src="<?php echo htmlspecialchars($logo); ?>"
                            alt="<?php echo $title_safe; ?> Logo"
                            class="<?php echo implode(' ', $imgClasses); ?>"
                            style="<?php echo ($header_style === 'text_only') ? 'display: none;' : ''; ?>"
                            onerror="console.log('Logo image failed to load:', this.src); this.style.display='none'; this.parentNode.querySelector('.logo-text').style.display='block';"
                            onload="console.log('Logo image loaded successfully:', this.src);">
                        <span class="logo-text" style="<?php echo ($header_style === 'logo_only') ? 'display: none;' : 'display: block;'; ?>"><?php echo htmlspecialchars($logo_text); ?></span>
                    <?php else: ?>
                        <span style="<?php echo ($header_style === 'logo_only') ? 'display: none;' : 'display: block;'; ?>"><?php echo htmlspecialchars($logo_text); ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="header-middle">
                <nav class="main-nav">
                    <ul>
                        <?php
                        $menuSvc = new \App\Services\MenuService();
                        $primaryMenu = $menuSvc->get("primary");
                        if (is_array($primaryMenu) && count($primaryMenu) > 0) {
                            foreach ($primaryMenu as $item) {
                                $label = htmlspecialchars($item["label"] ?? "");
                                $url = app_base_url(
                                    ltrim($item["url"] ?? "#", "/"),
                                );
                                $icon = htmlspecialchars($item["icon"] ?? "");
                                echo '<li><a href="' .
                                    $url .
                                    '">' .
                                    ($icon
                                        ? '<i class="' . $icon . '"></i> '
                                        : "") .
                                    $label .
                                    "</a></li>";
                            }
                        } else {
                        ?>
                            <li><a href="<?php echo app_base_url(
                                                "civil",
                                            ); ?>"><i class="fas fa-hard-hat"></i>Civil</a></li>
                            <li><a href="<?php echo app_base_url(
                                                "electrical",
                                            ); ?>"><i class="fas fa-bolt"></i>Electrical</a></li>
                            <li><a href="<?php echo app_base_url(
                                                "plumbing",
                                            ); ?>"><i class="fas fa-faucet"></i>Plumbing</a></li>
                            <li><a href="<?php echo app_base_url(
                                                "hvac",
                                            ); ?>"><i class="fas fa-wind"></i>HVAC</a></li>
                            <li><a href="<?php echo app_base_url(
                                                "fire",
                                            ); ?>"><i class="fas fa-fire-extinguisher"></i>Fire Protection</a></li>
                            <li class="has-dropdown">
                                <a href="#" aria-haspopup="true" aria-expanded="false" role="button" tabindex="0">
                                    <i class="fas fa-layer-group"></i>
                                    More Tools
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul class="dropdown" role="menu">
                                    <li role="none"><a href="<?php echo app_base_url(
                                                                    "site",
                                                                ); ?>" class="grid-item" role="menuitem"><i class="fas fa-map-marked-alt"></i>Site Development</a></li>
                                    <li role="none"><a href="<?php echo app_base_url(
                                                                    "structural",
                                                                ); ?>" class="grid-item" role="menuitem"><i class="fas fa-building"></i>Structural Analysis</a></li>
                                    <li role="none"><a href="<?php echo app_base_url(
                                                                    "mep",
                                                                ); ?>" class="grid-item" role="menuitem"><i class="fas fa-cogs"></i>MEP Coordination</a></li>
                                    <li role="none"><a href="<?php echo app_base_url(
                                                                    "estimation",
                                                                ); ?>" class="grid-item" role="menuitem"><i class="fas fa-calculator"></i>Estimation Suite</a></li>
                                    <li role="none"><a href="<?php echo app_base_url(
                                                                    "management",
                                                                ); ?>" class="grid-item" role="menuitem"><i class="fas fa-project-diagram"></i>Management</a></li>
                                    <li role="none"><a href="<?php echo app_base_url(
                                                                    "developers",
                                                                ); ?>" class="grid-item" role="menuitem"><i class="fas fa-code"></i>API & Developers</a></li>
                                </ul>
                            </li>
                        <?php
                        }
                        ?>
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
                    <button class="theme-toggle-btn" id="themeToggleBtn" title="Toggle theme" data-label="Theme">
                        <i class="fas fa-moon"></i>
                    </button>
                    <button id="searchToggleBtn" class="search-toggle-btn" title="Search">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- User greeting (shown for all users) -->
                    <div class="user-greeting" id="userGreeting">
                        Hi, <strong><?php if (!empty($userName)) {
                                        echo htmlspecialchars(explode(" ", $userName)[0]);
                                    } else {
                                        echo "Guest";
                                    } ?></strong> 👋
                    </div>

                    <!-- Login Button (Only for guests) -->
                    <?php
                    $is_logged_in =
                        !empty($_SESSION["user"]) ||
                        !empty($_SESSION["user_id"]) ||
                        !empty($_SESSION["username"]) ||
                        !empty($_SESSION["full_name"]);
                    if (!$is_logged_in): ?>
                        <a href="<?php echo app_base_url(
                                        "login",
                                    ); ?>" class="btn btn-primary login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="btn-text">Login</span>
                        </a>
                    <?php endif;
                    ?>

                    <?php if (!empty($_SESSION["user"])): ?>
                        <?php if (
                            !empty($_SESSION["is_admin"]) &&
                            $updateAvailable
                        ): ?>
                            <div class="update-notification" title="Update Available">
                                <i class="fas fa-download"></i>
                                v<?php echo htmlspecialchars(
                                        $updateAvailable["latest"],
                                    ); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Profile Dropdown (Only for logged-in users) -->
                    <?php
                    $is_logged_in =
                        !empty($_SESSION["user"]) ||
                        !empty($_SESSION["user_id"]) ||
                        !empty($_SESSION["username"]) ||
                        !empty($_SESSION["full_name"]);
                    if ($is_logged_in): ?>
                    <!-- Notifications (Only for logged-in users) -->
                    <?php if ($is_logged_in): ?>
                        <div class="notification-wrapper has-dropdown" id="notificationWrapper">
                            <button class="notification-btn" id="notificationToggleBtn" title="Notifications" aria-expanded="false" aria-haspopup="true">
                                <i class="fas fa-bell"></i>
                                <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                            </button>
                            <div class="dropdown notification-dropdown" id="notificationDropdown" role="menu">
                                <div class="notification-header">
                                    <h3>Notifications</h3>
                                    <button class="mark-all-read" id="markAllReadBtn" title="Mark all as read">
                                        <i class="fas fa-check-double"></i> Mark all read
                                    </button>
                                </div>
                                <div class="notification-list" id="notificationList">
                                    <div style="padding: 2rem; text-align: center; color: #94a3b8;">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                    </div>
                                </div>
                                <div class="notification-footer">
                                    <a href="<?php echo app_base_url('/user/notifications'); ?>" class="view-all-btn">View All Notifications</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="profile-dropdown-wrapper">
                            <button class="profile-btn" id="profileToggleBtn" title="Profile Menu">
                                <i class="fas fa-user-circle"></i>
                            </button>
                            <div class="profile-dropdown" id="profileDropdown">
                                <a href="<?php echo app_base_url(
                                                "user/profile",
                                            ); ?>" class="menu-item">
                                    <i class="fas fa-user-edit" style="color: #8b5cf6;"></i>
                                    <span class="text">Profile Settings</span>
                                </a>
                                <?php if (!empty($_SESSION["is_admin"])): ?>
                                    <a href="<?php echo app_base_url(
                                                    "admin",
                                                ); ?>" class="menu-item">
                                        <i class="fas fa-shield-alt" style="color: #ef4444;"></i>
                                        <span class="text">Admin Panel</span>
                                    </a>
                                <?php endif; ?>
                                <a href="#" class="menu-item" id="favoritesMenuItem">
                                    <i class="fas fa-star" style="color: #f59e0b;"></i>
                                    <span class="text">Favorites</span>
                                </a>
                                <a href="<?php echo app_base_url(
                                                "help",
                                            ); ?>" class="menu-item" id="helpMenuItem">
                                    <i class="fas fa-question-circle" style="color: #3b82f6;"></i>
                                    <span class="text">Help</span>
                                </a>
                                <a href="<?php echo app_base_url(
                                                "logout",
                                            ); ?>" class="menu-item">
                                    <i class="fas fa-sign-out-alt" style="color: #6b7280;"></i>
                                    <span class="text">Logout</span>
                                </a>
                            </div>
                        </div>
                    <?php endif;
                    ?>

                    <button class="hamburger-btn" id="hamburgerBtn" title="Toggle navigation menu" aria-label="Toggle navigation menu" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <div class="mobile-nav" id="mobileNav">
                <ul>
                    <li><a href="<?php echo app_base_url(
                                        "civil",
                                    ); ?>"><i class="fas fa-hard-hat"></i> Civil</a></li>
                    <li><a href="<?php echo app_base_url(
                                        "electrical",
                                    ); ?>"><i class="fas fa-bolt"></i> Electrical</a></li>
                    <li><a href="<?php echo app_base_url(
                                        "plumbing",
                                    ); ?>"><i class="fas fa-faucet"></i> Plumbing</a></li>
                    <li><a href="<?php echo app_base_url(
                                        "hvac",
                                    ); ?>"><i class="fas fa-wind"></i> HVAC</a></li>
                    <li><a href="<?php echo app_base_url(
                                        "fire",
                                    ); ?>"><i class="fas fa-fire-extinguisher"></i> Fire Protection</a></li>
                    <li><a href="<?php echo app_base_url(
                                        "site",
                                    ); ?>"><i class="fas fa-map-marked-alt"></i> Site Development</a></li>
                    <li><a href="<?php echo app_base_url(
                                        "estimation",
                                    ); ?>"><i class="fas fa-calculator"></i> Estimation</a></li>
                    <li><a href="<?php echo app_base_url(
                                        "structural",
                                    ); ?>"><i class="fas fa-building"></i> Structural</a></li>
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
            // Helper functions for search result styling
            function hexToRgb(hex) {
                const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                return result ?
                    `${parseInt(result[1], 16)},${parseInt(result[2], 16)},${parseInt(result[3], 16)}` :
                    '108,117,125';
            }

            function adjustColor(hex, percent) {
                const num = parseInt(hex.replace("#", ""), 16);
                const amt = Math.round(2.55 * percent);
                const R = (num >> 16) + amt;
                const G = (num >> 8 & 0x00FF) + amt;
                const B = (num & 0x0000FF) + amt;
                return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
                        (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 + (B < 255 ? B < 1 ? 0 : B : 255))
                    .toString(16).slice(1);
            }

            // Header search modal toggle with typing effect
            (function() {
                const toggleBtn = document.getElementById('searchToggleBtn');
                const modal = document.getElementById('searchModal');
                const closeBtn = document.getElementById('searchModalClose');
                const input = document.getElementById('searchModalInput');
                const results = document.getElementById('searchModalResults');

                if (!toggleBtn || !modal) return;

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

                function openModal() {
                    modal.classList.add('active');
                    modal.setAttribute('aria-hidden', 'false');
                    input.focus();
                    input.select();
                    startTypingEffect();

                    // Removed automatic loading of popular calculators
                    results.innerHTML = '';
                }

                function loadPopularCalculators() {
                    // Use globally defined appConfig if available, otherwise fallback to root
                    let baseUrl = (window.appConfig && window.appConfig.baseUrl) ? window.appConfig.baseUrl.replace(/\/$/, '') : '';

                    console.log('Loading popular calculators from:', `${baseUrl}/api/search.php`);

                    fetch(`${baseUrl}/api/search.php`)
                        .then(r => {
                            console.log('Popular calculators response status:', r.status);
                            return r.json();
                        })
                        .then(data => {
                            console.log('Popular calculators data:', data);
                            if (Array.isArray(data) && data.length > 0) {
                                renderSearchResults(data);
                            }
                        })
                        .catch(err => {
                            console.error('Failed to load popular calculators:', err);
                            results.innerHTML = '<div style="text-align:center;padding:2rem;color:#64748b;"><i class="fas fa-calculator" style="margin-right:0.5rem;"></i>Popular calculators will appear here</div>';
                        });
                }

                function renderSearchResults(data) {
                    if (!data || !Array.isArray(data)) {
                        results.innerHTML = '<div style="text-align:center;padding:2rem;color:#64748b;"><i class="fas fa-exclamation-circle" style="margin-right:0.5rem;"></i>No results</div>';
                        return;
                    }

                    if (data.length === 0) {
                        results.innerHTML = '<div style="text-align:center;padding:2rem;color:#64748b;"><i class="fas fa-search" style="margin-right:0.5rem;"></i>No calculators found matching your search</div>';
                        return;
                    }

                    results.innerHTML = data.map(item => {
                        const icon = item.icon || 'fas fa-calculator';
                        const color = item.color || '#6c757d';
                        const categoryBadge = item.category ? `<span class="category-badge" style="font-size:.75rem;color:${color};background:rgba(${hexToRgb(color)},0.1);padding:.25rem .6rem;border-radius:20px;border:1px solid rgba(${hexToRgb(color)},0.2);font-weight:500;">${item.category}</span>` : '';
                        const subcategoryBadge = item.subcategory ? `<span class="subcategory-badge" style="font-size:.75rem;color:#64748b;background:rgba(100,116,139,0.1);padding:.25rem .6rem;border-radius:20px;border:1px solid rgba(100,116,139,0.2);margin-left:0.25rem;">${item.subcategory}</span>` : '';
                        const snippet = item.description ? `<div style="font-size:.9rem;color:#64748b;margin-top:.4rem;line-height:1.4;">${item.description}</div>` : '';
                        const url = item.url || '#';

                        return `<div class="search-result-item" style="cursor:pointer;padding:1rem;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:0.75rem;transition:all 0.2s ease;background:white;"
                             onmouseover="this.style.borderColor='${color}';this.style.boxShadow='0 4px 20px rgba(${hexToRgb(color)},0.15)';this.style.transform='translateY(-2px)';"
                             onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';this.style.transform='translateY(0)';"
                             onclick="window.location.href='${url}';closeModal();">
                    <div style="display:flex;align-items:flex-start;gap:1rem;">
                        <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg, ${color}, ${adjustColor(color, -20)});display:flex;align-items:center;justify-content:center;color:white;font-size:1.2rem;flex-shrink:0;box-shadow:0 4px 12px rgba(${hexToRgb(color)},0.3);">
                            <i class="${icon}"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;font-size:1.1rem;color:#1e293b;margin-bottom:0.5rem;line-height:1.3;">${item.name}</div>
                            ${snippet}
                            <div style="margin-top:0.75rem;display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                                ${categoryBadge}${subcategoryBadge}
                                ${item.type === 'history' ? '<span class="history-badge" style="font-size:.75rem;color:#059669;background:rgba(5,150,105,0.1);padding:.25rem .6rem;border-radius:20px;border:1px solid rgba(5,150,105,0.2);font-weight:500;"><i class="fas fa-history" style="margin-right:.25rem;"></i>Recent</span>' : ''}
                            </div>
                        </div>
                        <div style="color:#94a3b8;font-size:1rem;flex-shrink:0;opacity:0.7;transition:all 0.2s ease;">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>`;
                    }).join('');
                }

                function closeModal() {
                    modal.classList.remove('active');
                    modal.setAttribute('aria-hidden', 'true');
                    results.innerHTML = '';
                    input.value = '';
                    stopTypingEffect();
                    // Reset to default placeholder
                    input.placeholder = 'Search calculators, tools, and utilities...';
                }

                toggleBtn.addEventListener('click', openModal);
                closeBtn && closeBtn.addEventListener('click', closeModal);
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) closeModal();
                });

                // Add ESC key support to close modal
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modal.classList.contains('active')) {
                        closeModal();
                    }
                });

                // Stop typing effect when user starts typing
                input.addEventListener('input', function() {
                    if (typingTimeout) {
                        clearTimeout(typingTimeout);
                        typingTimeout = null;
                    }
                    // User typed something, keep their text
                });

                let debounceTimer = null;
                input.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const q = input.value.trim();
                    if (q.length < 2) {
                        results.innerHTML = '';
                        return;
                    }
                    debounceTimer = setTimeout(() => {
                        // Use dynamic base URL to avoid path issues
                        // Use globally defined appConfig if available, otherwise fallback to root
                        let baseUrl = (window.appConfig && window.appConfig.baseUrl) ? window.appConfig.baseUrl.replace(/\/$/, '') : '';

                        console.log('Search API URL:', `${baseUrl}/api/search.php?q=${encodeURIComponent(q)}`);
                        fetch(`${baseUrl}/api/search.php?q=${encodeURIComponent(q)}`)
                            .then(r => r.json())
                            .then(data => {
                                console.log('Search results received:', data);
                                renderSearchResults(data);
                            }).catch(err => {
                                console.error('Search API Error:', err);
                                console.error('Search URL was:', `${baseUrl}/api/search.php?q=${encodeURIComponent(q)}`);

                                results.innerHTML = `<div style="text-align:center;padding:2rem;color:#ef4444;">
                        <i class="fas fa-exclamation-triangle" style="margin-right:0.5rem;"></i>
                        Search failed. Please try again.
                        <div style="font-size:0.8rem;margin-top:0.5rem;color:#64748b;">
                            Check browser console for details
                        </div>
                    </div>`;
                            });
                    }, 300);
                });
            })();

            // Profile dropdown functionality with hover and click
            (function() {
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
                    profileToggle.addEventListener('click', function(e) {
                        e.stopPropagation();
                        profileDropdown.classList.toggle('active');
                    });

                    // Close profile dropdown when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
                            profileDropdown.classList.remove('active');
                        }
                    });
                }
            })();

            // Favorites and Help functionality
            (function() {
                const favoritesItem = document.getElementById('favoritesMenuItem');
                const helpItem = document.getElementById('helpMenuItem');

                if (favoritesItem) {
                    favoritesItem.addEventListener('click', function(e) {
                        e.preventDefault();
                        showNotification('Favorites feature coming soon!', 'info');
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

                // Theme Toggle Functionality - Day/Night Mode
                if (themeToggleBtn) {
                    // Check for saved theme preference or default to dark
                    const savedTheme = localStorage.getItem('theme') || 'dark';

                    // Apply saved theme
                    if (savedTheme === 'dark') {
                        document.body.classList.add('dark-theme');
                        document.body.setAttribute('data-theme', 'dark');
                        themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
                        themeToggleBtn.setAttribute('data-label', 'Dark Mode');
                    } else {
                        document.body.classList.remove('dark-theme');
                        document.body.setAttribute('data-theme', 'light');
                        themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
                        themeToggleBtn.setAttribute('data-label', 'Light Mode');
                    }

                    // Toggle theme on button click
                    themeToggleBtn.addEventListener('click', function() {
                        const icon = this.querySelector('i');
                        const isDark = document.body.classList.contains('dark-theme');

                        // Toggle theme
                        if (isDark) {
                            // Switch to light mode
                            document.body.classList.remove('dark-theme');
                            document.body.setAttribute('data-theme', 'light');
                            icon.className = 'fas fa-sun';
                            this.setAttribute('data-label', 'Light Mode');
                            localStorage.setItem('theme', 'light');

                            // Animate icon change
                            icon.style.transform = 'rotate(180deg) scale(1.2)';
                            setTimeout(() => {
                                icon.style.transform = '';
                            }, 400);

                            showThemeNotification('☀️ Light Mode Enabled');
                        } else {
                            // Switch to dark mode
                            document.body.classList.add('dark-theme');
                            document.body.setAttribute('data-theme', 'dark');
                            icon.className = 'fas fa-moon';
                            this.setAttribute('data-label', 'Dark Mode');
                            localStorage.setItem('theme', 'dark');

                            // Animate icon change
                            icon.style.transform = 'rotate(-180deg) scale(1.2)';
                            setTimeout(() => {
                                icon.style.transform = '';
                            }, 400);

                            showThemeNotification('🌙 Dark Mode Enabled');
                        }
                    });
                }

                // Show theme change notification
                function showThemeNotification(message) {
                    // Remove existing notification if any
                    const existing = document.querySelector('.theme-notification');
                    if (existing) {
                        existing.remove();
                    }

                    // Create notification
                    const notification = document.createElement('div');
                    notification.className = 'theme-notification';
                    notification.textContent = message;
                    notification.style.cssText = `
                    position: fixed;
                    top: 80px;
                    right: 20px;
                    background: linear-gradient(135deg, #6366f1, #8b5cf6);
                    color: white;
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-size: 0.9rem;
                    font-weight: 500;
                    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
                    z-index: 10000;
                    opacity: 0;
                    transform: translateY(-10px);
                    transition: all 0.3s ease;
                `;

                    document.body.appendChild(notification);

                    // Animate in
                    setTimeout(() => {
                        notification.style.opacity = '1';
                        notification.style.transform = 'translateY(0)';
                    }, 10);

                    // Animate out and remove
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            notification.remove();
                        }, 300);
                    }, 2000);
                }

                console.log('Theme toggle enabled - Day/Night mode ready');

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
                const HEADER_STATUS_URL = '<?php echo app_base_url(
                                                "api/header_status.php",
                                            ); ?>';

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
                            ${isAdmin ? `<li><a href="${escapeHtml('<?php echo app_base_url(
                                                                        "admin",
                                                                    ); ?>')}"><i class="fas fa-cog"></i> Admin Panel</a></li>` : ''}
                            <li><a href="<?php echo app_base_url(
                                                "dashboard",
                                            ); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li><a href="<?php echo app_base_url(
                                                "profile",
                                            ); ?>"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
                            <li><a href="<?php echo app_base_url(
                                                "logout",
                                            ); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
                    var userObj = {
                        name: name,
                        initial: initial,
                        role: role
                    };
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
                    return String(str).replace(/[&<>"'`]/g, function(s) {
                        return ({
                            '&': '&',
                            '<': '<',
                            '>': '>',
                            '"': '"',
                            "'": '&#39;',
                            '`': '&#96;'
                        })[s];
                    });
                }

                window.refreshHeaderFromServer = async function() {
                    try {
                        const res = await fetch(HEADER_STATUS_URL, {
                            credentials: 'include'
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        const ua = document.querySelector('.user-actions');
                        if (!ua) return;

                        if (data.logged_in) {
                            ua.innerHTML = buildUserActionsHtml(data.user, data.is_admin);
                        } else {
                            ua.innerHTML = '<a href="<?php echo app_base_url(
                                                            "login",
                                                        ); ?>" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a>';
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
                document.addEventListener('click', function(ev) {
                    const a = ev.target.closest && ev.target.closest('a');
                    if (!a) return;
                    const href = a.getAttribute('href') || '';
                    if (href.indexOf('logout') !== -1) {
                        // Prevent default navigation to allow immediate header update
                        ev.preventDefault();
                        (async function() {
                            try {
                                // Call logout endpoint (GET). include credentials to ensure session is destroyed.
                                await fetch(href, {
                                    method: 'GET',
                                    credentials: 'include'
                                });
                            } catch (e) {
                                // ignore network errors
                            }
                            // Update header - show login button
                            try {
                                const ua = document.querySelector('.user-actions');
                                if (ua) ua.innerHTML = '<a href="<?php echo app_base_url(
                                                                        "login",
                                                                    ); ?>" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a>';
                            } catch (e) {
                                console.warn('Header update failed', e);
                            }
                            // Then navigate to the logout href (redirect) or homepage
                            window.location.href = href || '<?php echo app_base_url(
                                                                "/",
                                                            ); ?>';
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

            // Auto-fit user greeting text
            (function() {
                const userGreeting = document.getElementById('userGreeting');
                if (!userGreeting) return;

                function autoFitGreeting() {
                    const greeting = userGreeting;
                    const strongElement = greeting.querySelector('strong');
                    if (!strongElement) return;

                    const nameText = strongElement.textContent.trim();
                    const nameLength = nameText.length;

                    // Reset classes
                    greeting.classList.remove('long-name', 'very-long-name', 'extra-long-name');

                    // Add appropriate class based on name length
                    if (nameLength > 15) {
                        greeting.classList.add('extra-long-name');
                    } else if (nameLength > 10) {
                        greeting.classList.add('very-long-name');
                    } else if (nameLength > 7) {
                        greeting.classList.add('long-name');
                    }

                    // Dynamic font size adjustment
                    let fontSize = '0.8rem';
                    if (nameLength > 15) {
                        fontSize = '0.65rem';
                    } else if (nameLength > 10) {
                        fontSize = '0.7rem';
                    } else if (nameLength > 7) {
                        fontSize = '0.75rem';
                    }

                    greeting.style.fontSize = fontSize;

                    // Adjust strong element max-width
                    let maxWidth = '120px';
                    if (nameLength > 15) {
                        maxWidth = '90px';
                    } else if (nameLength > 10) {
                        maxWidth = '100px';
                    } else if (nameLength > 7) {
                        maxWidth = '110px';
                    }

                    strongElement.style.maxWidth = maxWidth;

                    console.log(`Auto-fit greeting: "${nameText}" (${nameLength} chars) -> ${fontSize}, max-width: ${maxWidth}`);
                }

                // Run on page load
                autoFitGreeting();

                // Run on window resize
                window.addEventListener('resize', autoFitGreeting);

                // Add CSS classes for different name lengths
                const style = document.createElement('style');
                style.textContent = `
                .user-greeting.long-name {
                    padding: 0.35rem 0.65rem;
                }

                .user-greeting.very-long-name {
                    padding: 0.3rem 0.6rem;
                    flex-direction: column;
                    gap: 0.1rem;
                    min-height: 2.2rem;
                }

                .user-greeting.extra-long-name {
                    padding: 0.25rem 0.5rem;
                    flex-direction: column;
                    gap: 0.05rem;
                    min-height: 2.4rem;
                    max-width: 180px;
                }

                @media (max-width: 768px) {
                    .user-greeting.long-name,
                    .user-greeting.very-long-name,
                    .user-greeting.extra-long-name {
                        flex-direction: column;
                        padding: 0.25rem 0.5rem;
                        min-height: 2rem;
                        max-width: 140px;
                    }
                }

            `;
                document.head.appendChild(style);
            })();

            // Notification System Logic
            (function() {
                const wrapper = document.getElementById('notificationWrapper');
                const toggleBtn = document.getElementById('notificationToggleBtn');
                const dropdown = document.getElementById('notificationDropdown');
                const badge = document.getElementById('notificationBadge');
                const list = document.getElementById('notificationList');
                const markAllBtn = document.getElementById('markAllReadBtn');
                const baseUrl = '<?php echo app_base_url(''); ?>';
                let isLoaded = false;

                if (!wrapper || !toggleBtn || !dropdown) return;

                // Toggle dropdown
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = dropdown.classList.contains('active');
                    
                    // Close others
                    document.querySelectorAll('.dropdown.open, .dropdown.active').forEach(d => {
                        if (d !== dropdown) d.classList.remove('active', 'open');
                    });
                    document.querySelectorAll('.profile-dropdown.active').forEach(d => d.classList.remove('active'));

                    dropdown.classList.toggle('active');
                    toggleBtn.setAttribute('aria-expanded', !isOpen);

                    if (!isOpen && !isLoaded) {
                        fetchNotifications();
                    }
                });

                // Close on outside click
                document.addEventListener('click', function(e) {
                    if (!wrapper.contains(e.target)) {
                        dropdown.classList.remove('active');
                        toggleBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                // Mark all as read
                markAllBtn.addEventListener('click', async function(e) {
                    e.stopPropagation();
                    try {
                        markAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                        const res = await fetch(`${baseUrl}/api/notifications/mark-all-read`, {
                            method: 'POST'
                        });
                        const data = await res.json();
                        
                        if (data.success) {
                            // Update UI
                            badge.style.display = 'none';
                            badge.textContent = '0';
                            // Reload list
                            fetchNotifications();
                        }
                    } catch (err) {
                        console.error('Failed to mark all read', err);
                    } finally {
                        markAllBtn.innerHTML = '<i class="fas fa-check-double"></i> Mark all read';
                    }
                });

                // Fetch notifications
                async function fetchNotifications() {
                    try {
                        const res = await fetch(`${baseUrl}/api/notifications/list?limit=10`);
                        const data = await res.json();
                        
                        if (data.success) {
                            renderNotifications(data.notifications);
                            updateBadge(data.unread_count);
                            isLoaded = true;
                        } else if (data.error === 'Access denied') {
                             // User logged out or session expired
                             list.innerHTML = `<div class="notification-empty">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Please login to view notifications</p>
                            </div>`;
                        }
                    } catch (err) {
                        console.error('Failed to fetch notifications', err);
                        list.innerHTML = `<div class="notification-empty" style="color: #ef4444;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Failed to load notifications</p>
                        </div>`;
                    }
                }

                // Initial Badge Count
                async function checkUnreadCount() {
                    try {
                        const res = await fetch(`${baseUrl}/api/notifications/unread-count`);
                        const data = await res.json();
                        if (data.success) {
                            updateBadge(data.unread_count);
                        }
                    } catch (err) {
                        // Silent fail for background check
                    }
                }

                function updateBadge(count) {
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }

                function renderNotifications(notifications) {
                    if (!notifications || notifications.length === 0) {
                        list.innerHTML = `<div class="notification-empty">
                            <i class="far fa-bell" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <p>No notifications yet</p>
                        </div>`;
                        return;
                    }

                    list.innerHTML = notifications.map(n => {
                        const icon = getIconForType(n.type);
                        const bgClass = getClassForType(n.type);
                        const timeAgo = getTimeAgo(new Date(n.created_at));
                        const unreadClass = n.read_at ? '' : 'unread';

                        return `
                        <div class="notification-item ${unreadClass}" onclick="window.location.href='${n.link || '#'}'">
                            <div class="notification-icon ${bgClass}">
                                <i class="${icon}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">${escapeHtml(n.title)}</div>
                                <div class="notification-message">${escapeHtml(n.message)}</div>
                                <div class="notification-time">${timeAgo}</div>
                            </div>
                        </div>
                        `;
                    }).join('');
                }

                function getIconForType(type) {
                    switch (type) {
                        case 'success': return 'fas fa-check-circle';
                        case 'warning': return 'fas fa-exclamation-triangle';
                        case 'error': return 'fas fa-times-circle';
                        case 'info': default: return 'fas fa-info-circle';
                    }
                }

                function getClassForType(type) {
                    switch (type) {
                        case 'success': return 'bg-success-light';
                        case 'warning': return 'bg-warning-light';
                        case 'error': return 'bg-danger-light';
                        case 'info': default: return 'bg-info-light';
                    }
                }

                function getTimeAgo(date) {
                    const seconds = Math.floor((new Date() - date) / 1000);
                    let interval = seconds / 31536000;
                    if (interval > 1) return Math.floor(interval) + " years ago";
                    interval = seconds / 2592000;
                    if (interval > 1) return Math.floor(interval) + " months ago";
                    interval = seconds / 86400;
                    if (interval > 1) return Math.floor(interval) + " days ago";
                    interval = seconds / 3600;
                    if (interval > 1) return Math.floor(interval) + " hours ago";
                    interval = seconds / 60;
                    if (interval > 1) return Math.floor(interval) + " minutes ago";
                    return Math.floor(seconds) + " seconds ago";
                }

                function escapeHtml(text) {
                    if (!text) return '';
                    return text
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                }

                // Check unread count on load
                checkUnreadCount();
                
                // Poll every 60 seconds
                setInterval(checkUnreadCount, 60000);
            })();
        </script>
    <!-- Main Content Wrapper -->
    <main class="main-content">
