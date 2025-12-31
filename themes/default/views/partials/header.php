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
$logo_text = $site_meta["logo_text"] ?? (\App\Services\SettingsService::get('site_name', 'Civil Cal') ?: 'Civil Cal');
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
// Forced Navy Blue Theme (Consolidated Root Fix)
$body_class = "dark-theme";

// Mark homepage body with 'index-page' so home-specific gradient styles apply
$__req_path = parse_url($_SERVER["REQUEST_URI"] ?? "", PHP_URL_PATH);
$__base = defined("APP_BASE") ? rtrim(APP_BASE, "/") : "";
if (
    $__req_path === $__base ||
    $__req_path === $__base . "/" ||
    substr($__req_path, -10) === "/index.php"
) {
    $body_class .= " index-page";
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
    
    <?php
    // Generate Canonical URL
    $canonicalUrl = $site_meta["canonical"] ?? null;
    if (empty($canonicalUrl)) {
        // ... (existing canonical logic) ...
        if (isset($_SERVER['CALCULATOR_ID'])) {
            $canonicalUrl = app_base_url(\App\Helpers\UrlHelper::calculator($_SERVER['CALCULATOR_ID']));
        } else {
            // ... (existing canonical logic) ...
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
            $canonicalUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . $path;
        }
    }
    if ($canonicalUrl): ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <?php endif; ?>

    <?php
    // --- SCHEMA MARKUP START ---
    require_once $basePath . '/app/Helpers/SchemaHelper.php';
    require_once $basePath . '/app/Helpers/AdHelper.php';
    
    // 1. WebApplication / SoftwareApplication / Organization / BlogPosting
    if (isset($_SERVER['CALCULATOR_ID'])) {
        // Calculator Page
        echo '<script type="application/ld+json">' . 
             \App\Helpers\SchemaHelper::getCalculatorSchema($title_safe, $desc_safe) . 
             "</script>\n";
    } elseif ($__req_path === $__base || $__req_path === $__base . '/' || strpos($__req_path, 'index.php') !== false) {
        // Homepage - WebApplication + Organization
        echo '<script type="application/ld+json">' . 
             \App\Helpers\SchemaHelper::getHomepageSchema() . 
             "</script>\n";
             
        echo '<script type="application/ld+json">' . 
             \App\Helpers\SchemaHelper::getOrganizationSchema() . 
             "</script>\n";
    } elseif (isset($post) && !empty($post['slug'])) {
        // Single Blog Post
        echo '<script type="application/ld+json">' . 
             \App\Helpers\SchemaHelper::getBlogPostSchema($post) . 
             "</script>\n";
    }

    // 2. Breadcrumbs
    $bcSegments = array_filter(explode('/', ltrim(str_replace($__base, '', $__req_path), '/')));
    $bcData = [];
    $bcPath = '';
    foreach ($bcSegments as $seg) {
        if ($seg === 'modules' || $seg === 'index.php') continue; 
        $bcPath .= '/' . $seg;
        $bcName = ucwords(str_replace('-', ' ', $seg));
        $bcData[$bcName] = $bcPath;
    }
    if (!empty($bcData)) {
        echo '<script type="application/ld+json">' . 
             \App\Helpers\SchemaHelper::getBreadcrumbSchema($bcData) . 
             "</script>\n";
    }
    // --- SCHEMA MARKUP END ---
    ?>

    <script>
        window.appConfig = {
            baseUrl: "<?php echo rtrim(defined('APP_BASE') ? APP_BASE : '', '/'); ?>",
            csrfToken: "<?php echo csrf_token(); ?>"
        };
    </script>

    <?php
    // Load CSS files via ThemeManager proxy to ensure correct URL resolution
    // for both Laragon (document root c:\laragon\www) and built-in server (document root public/)
    $cssFiles = [
        "theme.css",
        "header.css",
        "footer.css",
        "back-to-top.css",
        "home.css",
        "logo-enhanced.css",
        "top-header.css",
    ];

    foreach ($cssFiles as $css) {
        $cssPath = dirname(__DIR__) . "/assets/css/" . $css;
        $atime = file_exists($cssPath) ? filemtime($cssPath) : time();

        // Use ThemeManager to generate the correct proxy URL
        if ($themeManager) {
            $url = $themeManager->themeUrl("assets/css/" . $css . "?v=" . $atime);
        } else {
            // Fallback if ThemeManager is unavailable
            $url = app_base_url(
                "themes/default/assets/css/" . $css . "?v=" . $atime,
            );
        }

        echo '<link rel="stylesheet" href="' .
            htmlspecialchars($url) .
            '">' .
            "\n    ";
    }
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <meta name="theme-color" content="#000000">
    <!-- Inline styles removed to ensure single source of truth in theme.css -->
    <!-- Google Analytics -->
    <?php 
    $gaId = \App\Services\SettingsService::get('google_analytics_id');
    if (!empty($gaId)): 
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaId) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= htmlspecialchars($gaId) ?>');
    </script>
    <?php endif; ?>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(error => {
                        console.log('ServiceWorker registration failed: ', error);
                    });
            });
        }
    </script>
</head>

<body class="<?php echo htmlspecialchars($body_class); ?>">
    <?php echo \App\Helpers\AdHelper::show('header_top'); ?>

    <!-- Top Header / Notification Bar -->
    <?php
    $topMenuItems = (new \App\Services\MenuService())->get('top_header');
    if (!empty($topMenuItems) && is_array($topMenuItems)):
    ?>
    <div class="top-header has-content" id="topHeaderBar">
        <div class="container margin-auto">
            <div class="top-header-ticker" id="topHeaderTicker">
                <ul id="topHeaderList">
                    <?php foreach ($topMenuItems as $index => $item): 
                        // Skip inactive items
                        if (isset($item['is_active']) && $item['is_active'] === false) continue;
                        
                        $tParams = $item;
                        $tUrl = $tParams['url'] ?? '#';
                        $tLabel = $tParams['name'] ?? ($tParams['title'] ?? ($tParams['label'] ?? 'Link'));
                        $tIcon = $tParams['icon'] ?? '';
                        if ($tIcon && strpos($tIcon, 'fa-') === 0) $tIcon = 'fas ' . $tIcon;
                    ?>
                    <li>
                        <a href="<?php echo (strpos($tUrl, 'http') === 0) ? $tUrl : app_base_url($tUrl); ?>">
                            <?php if($index === 0): ?><span class="top-header-notice">Notice</span><?php endif; ?>
                            <?php if($tIcon): ?><i class="<?php echo htmlspecialchars($tIcon); ?>"></i><?php endif; ?>
                            <span><?php echo htmlspecialchars($tLabel); ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button class="top-header-close" id="closeTopHeader" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php include __DIR__ . '/resource_hud.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const topHeader = document.getElementById('topHeaderBar');
            const ticker = document.getElementById('topHeaderTicker');
            const list = document.getElementById('topHeaderList');
            const closeBtn = document.getElementById('closeTopHeader');

            // 1. Close Functionality
            if (closeBtn && topHeader) {
                closeBtn.addEventListener('click', function() {
                    topHeader.style.display = 'none';
                    // Optional: Store in session or local storage to keep it closed
                    localStorage.setItem('topHeaderClosed', 'true');
                });

                // Check if it was closed before
                if (localStorage.getItem('topHeaderClosed') === 'true') {
                    topHeader.style.display = 'none';
                }
            }

            // 2. Ticker Functionality
            function checkTicker() {
                if (ticker && list) {
                    const isOverflowing = list.scrollWidth > ticker.clientWidth;
                    if (isOverflowing) {
                        list.classList.add('ticker-active');
                        // Calculate duration based on width for consistent speed
                        const speed = 50; // pixels per second
                        const duration = list.scrollWidth / speed;
                        list.style.animationDuration = duration + 's';
                    } else {
                        list.classList.remove('ticker-active');
                    }
                }
            }

            checkTicker();
            window.addEventListener('resize', checkTicker);
        });
    </script>
    <?php endif; ?>

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
                        <?php 
                        $textParts = explode(' ', $logo_text);
                        if (count($textParts) > 1): ?>
                            <span class="logo-text" style="<?php echo ($header_style === 'logo_only') ? 'display: none;' : 'display: block;'; ?>">
                                <?php foreach ($textParts as $part): ?>
                                    <span><?php echo htmlspecialchars($part); ?></span>
                                <?php endforeach; ?>
                            </span>
                        <?php else: ?>
                            <span class="logo-text" style="<?php echo ($header_style === 'logo_only') ? 'display: none;' : 'display: block;'; ?>">
                                <?php echo htmlspecialchars($logo_text); ?>
                            </span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="<?php echo ($header_style === 'logo_only') ? 'display: none;' : 'display: block;'; ?>"><?php echo htmlspecialchars($logo_text); ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="header-middle">
                <nav class="main-nav">
                    <ul id="mainNavList">
                        <?php
                        $menuSvc = new \App\Services\MenuService();
                        $primaryMenuItems = $menuSvc->get("header"); // Use 'header' location as shown in admin
                        
                        // Fallback if menu is empty
                        if (empty($primaryMenuItems)) {
                            $primaryMenuItems = [
                                ['title' => 'Civil', 'url' => '/civil', 'icon' => 'fa-hard-hat'],
                                ['title' => 'Electrical', 'url' => '/electrical', 'icon' => 'fa-bolt'],
                                ['title' => 'Plumbing', 'url' => '/plumbing', 'icon' => 'fa-faucet'],
                                ['title' => 'HVAC', 'url' => '/hvac', 'icon' => 'fa-wind'],
                                ['title' => 'Fire', 'url' => '/fire', 'icon' => 'fa-fire-extinguisher'],
                                ['title' => 'Site', 'url' => '/site', 'icon' => 'fa-map-marked-alt'],
                                ['title' => 'Structural', 'url' => '/structural', 'icon' => 'fa-building'],
                                ['title' => 'Estimation', 'url' => '/estimation', 'icon' => 'fa-calculator'],
                                ['title' => 'MEP', 'url' => '/mep', 'icon' => 'fa-tools'],
                            ];
                        }
                        
                        // Inject Quiz Menu Item if not present
                        $hasQuiz = false;
                        foreach ($primaryMenuItems as $item) {
                            $t = $item['name'] ?? ($item['title'] ?? ($item['label'] ?? ''));
                            if (stripos($t, 'Quiz') !== false) {
                                $hasQuiz = true;
                                break;
                            }
                        }
                        if (!$hasQuiz) {
                            // Inject Gamification Hub
                            array_unshift($primaryMenuItems, 
                                [
                                    'title' => 'My City', 
                                    'url' => '/quiz/city', 
                                    'icon' => 'fa-city', 
                                    'is_active' => true
                                ],
                                [
                                    'title' => 'Shop', 
                                    'url' => '/quiz/shop', 
                                    'icon' => 'fa-store', 
                                    'is_active' => true
                                ],
                                [
                                    'title' => 'Battle Pass', 
                                    'url' => '/quiz/battle-pass', 
                                    'icon' => 'fa-ticket-alt', 
                                    'is_active' => true
                                ],
                                [
                                    'title' => 'Quiz Portal', 
                                    'url' => '/quiz', 
                                    'icon' => 'fa-graduation-cap', 
                                    'is_active' => true
                                ]
                            );
                        }


                        foreach ($primaryMenuItems as $item):
                            // Skip inactive items
                            if (isset($item['is_active']) && $item['is_active'] === false) {
                                continue;
                            }
                            $title = $item['name'] ?? ($item['title'] ?? ($item['label'] ?? 'Link'));
                            $url = $item['url'] ?? '#';
                            $icon = $item['icon'] ?? 'fa-link';
                            // Ensure icon has fas/far prefix if not present
                            if (strpos($icon, 'fa-') === 0 && strpos($icon, ' ') === false) {
                                $icon = 'fas ' . $icon;
                            }
                        ?>
                            <li class="nav-item">
                                <a href="<?php echo (strpos($url, 'http') === 0) ? $url : app_base_url($url); ?>">
                                    <i class="<?php echo htmlspecialchars($icon); ?>"></i>
                                    <span><?php echo htmlspecialchars($title); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        
                        <li class="more-menu-item has-dropdown" id="dynamicMoreItem" style="display: none;">
                            <a href="#" aria-haspopup="true" aria-expanded="false" role="button">
                                More <i class="fas fa-chevron-down"></i>
                            </a>
                            <ul class="dropdown" id="moreToolsMenu" role="menu"></ul>
                        </li>
                    </ul>
                </nav>


            </div>

            <div class="header-right">
                <div class="user-actions">
                    <button class="theme-toggle-btn" id="themeToggleBtn" title="Toggle theme" data-label="Theme">
                        <i class="fas fa-moon"></i>
                    </button>
                    <button id="searchToggleBtn" class="search-toggle-btn" title="Search">
                        <i class="fas fa-search"></i>
                    </button>

                    <?php
                    $is_logged_in =
                        !empty($_SESSION["user"]) ||
                        !empty($_SESSION["user_id"]) ||
                        !empty($_SESSION["username"]) ||
                        !empty($_SESSION["full_name"]);
                    ?>
                    <!-- User greeting -->
                    <div class="user-greeting <?php echo $is_logged_in ? 'logged-in' : 'guest'; ?>" id="userGreeting">
                        <span class="greeting-text">Hi, </span>
                        <strong><?php if (!empty($userName)) {
                                         echo htmlspecialchars(explode(" ", $userName)[0]);
                                     } else {
                                         echo "Guest";
                                     } ?></strong> <?php echo $is_logged_in ? '👋' : ''; ?>
                    </div>

                    <!-- Actions -->
                    <?php if (!$is_logged_in): ?>
                        <a href="<?php echo app_base_url(
                                        "login",
                                    ); ?>" class="btn btn-primary login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="btn-text">Login</span>
                        </a>
                    <?php endif; ?>

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
                                <a href="#" class="menu-item" id="favoritesMenuItem" onclick="const w=document.getElementById('favorites-widget'); if(w){w.scrollIntoView({behavior:'smooth'});}else{window.location.href='<?php echo app_base_url('/calculator'); ?>';} return false;">
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
                    <?php foreach ($primaryMenuItems as $item): 
                        // Skip inactive items
                        if (isset($item['is_active']) && $item['is_active'] === false) {
                            continue;
                        }
                        $title = $item['name'] ?? ($item['title'] ?? ($item['label'] ?? 'Link'));
                        $url = $item['url'] ?? '#';
                        $icon = $item['icon'] ?? 'fa-link';
                        if (strpos($icon, 'fa-') === 0 && strpos($icon, ' ') === false) {
                            $icon = 'fas ' . $icon;
                        }
                    ?>
                        <li><a href="<?php echo (strpos($url, 'http') === 0) ? $url : app_base_url($url); ?>">
                            <i class="<?php echo htmlspecialchars($icon); ?>"></i> <?php echo htmlspecialchars($title); ?>
                        </a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
    </header>

    <!-- Search Overlay - Minimal -->
    <div class="search-overlay" id="searchOverlay">
        <div class="search-overlay-container">
            <button class="search-overlay-close" id="searchOverlayClose" aria-label="Close search">
                <i class="fas fa-times"></i>
            </button>

            <div class="search-input-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input 
                    type="text" 
                    id="overlaySearchInput" 
                    class="search-overlay-input"
                    placeholder="Search calculators, tools, categories..." 
                    autocomplete="off"
                    autofocus>
                <div class="search-stats" id="searchStats"></div>
            </div>

            <!-- Search Results -->
            <div class="search-results-container" id="searchResultsWrapper">
                <div class="search-results-section" id="searchResultsSection">
                    <!-- Results will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>



    <main class="main-content">



        <script>
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
                // Favorites Logic dealt with inline or via separate script
                const helpItem = document.getElementById('helpMenuItem');
            })();



            // Enhanced JavaScript for header functionality
            document.addEventListener('DOMContentLoaded', function() {
                const header = document.getElementById('siteHeader');
                const hamburgerBtn = document.getElementById('hamburgerBtn');
                const mobileNav = document.getElementById('mobileNav');
                const themeToggleBtn = document.getElementById('themeToggleBtn');

                // Scroll effect for header
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                });

                // Dynamic Menu Overflow (Priority+ Pattern)
                const mainNavList = document.getElementById('mainNavList');
                const dynamicMoreItem = document.getElementById('dynamicMoreItem');
                const moreToolsMenu = document.getElementById('moreToolsMenu');
                const navItems = Array.from(mainNavList.querySelectorAll('.nav-item'));

                function updateMenuOverflow() {
                    const headerMiddle = document.querySelector('.header-middle');
                    if (!headerMiddle || !mainNavList || !dynamicMoreItem || !moreToolsMenu) return;

                    // Available width from the middle container
                    const containerWidth = headerMiddle.getBoundingClientRect().width;
                    
                    // Show all items to measure accurately
                    dynamicMoreItem.classList.add('visible');
                    const moreItemWidth = dynamicMoreItem.getBoundingClientRect().width || 80;
                    
                    navItems.forEach(item => {
                        mainNavList.insertBefore(item, dynamicMoreItem);
                        item.style.display = 'flex';
                    });

                    let currentWidth = 0;
                    let overflowed = false;
                    const itemsToMove = [];
                    const gap = 8;

                    for (let i = 0; i < navItems.length; i++) {
                        const item = navItems[i];
                        const itemWidth = item.getBoundingClientRect().width;
                        const itemTotalWidth = (i > 0 ? gap : 0) + itemWidth;

                        if (overflowed) {
                            itemsToMove.push(item);
                            continue;
                        }

                        // Check if this item fits
                        // If it's NOT the last item, we need to consider if we'll need 'More' button
                        const isLast = (i === navItems.length - 1);
                        const spaceForMore = isLast ? 0 : gap + moreItemWidth;

                        if (currentWidth + itemTotalWidth + spaceForMore > containerWidth) {
                            // If even the first item doesn't fit with 'More', we must move it
                            overflowed = true;
                            itemsToMove.push(item);
                        } else {
                            currentWidth += itemTotalWidth;
                        }
                    }

                    // Move items to dropdown
                    itemsToMove.forEach(item => moreToolsMenu.appendChild(item));

                    if (overflowed) {
                        dynamicMoreItem.classList.add('visible');
                        dynamicMoreItem.style.display = 'flex';
                    } else {
                        dynamicMoreItem.classList.remove('visible');
                        dynamicMoreItem.style.display = 'none';
                    }
                }

                // Initial run and resize listener
                if (mainNavList && dynamicMoreItem && moreToolsMenu) {
                    window.addEventListener('resize', updateMenuOverflow);
                    // Run after a small delay to ensure styles are applied
                    setTimeout(updateMenuOverflow, 100);
                }

                // Mobile menu toggle
                hamburgerBtn.addEventListener('click', function() {
                    mobileNav.classList.toggle('active');
                    this.innerHTML = mobileNav.classList.contains('active') ?
                        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
                });

                // Theme Toggle - Icon preserved but logic moved to placeholder for New Theme
                if (themeToggleBtn) {
                    // Always force dark-theme state for now
                    if (!document.body.classList.contains('dark-theme')) {
                        document.body.classList.add('dark-theme');
                    }
                    document.body.setAttribute('data-theme', 'dark');
                    themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
                    themeToggleBtn.setAttribute('data-label', 'Navy Blue Theme');

                    themeToggleBtn.addEventListener('click', function() {
                        const icon = this.querySelector('i');
                        // Animate icon for feedback even if it doesn't switch theme yet
                        icon.style.transition = 'transform 0.4s ease';
                        icon.style.transform = 'rotate(360deg) scale(1.1)';
                        setTimeout(() => { icon.style.transform = ''; }, 400);
                        
                        showThemeNotification('🎨 Custom themes coming soon!');
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
                    border: 1px solid rgba(255, 255, 255, 0.04);
                    z-index: 2000;
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
                    if (header && !header.contains(event.target)) {
                        if (mobileNav) mobileNav.classList.remove('active');
                        if (hamburgerBtn) hamburgerBtn.innerHTML = '<i class="fas fa-bars"></i>';
                        // Close any open dropdowns
                        document.querySelectorAll('.has-dropdown.open, .has-dropdown.active').forEach(d => d.classList.remove('open', 'active'));
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

                    // Toggle on click
                    trigger.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
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
                            ${isAdmin ? `<li><a href="${escapeHtml('<?php echo app_base_url("admin"); ?>')}"><i class="fas fa-cog"></i> Admin Panel</a></li>` : ''}
                            <li><a href="<?php echo app_base_url("dashboard"); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li><a href="<?php echo app_base_url("profile"); ?>"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
                            <li><a href="<?php echo app_base_url("logout"); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
                        const searchToggleBtn = document.getElementById('searchToggleBtn');
                        if (searchToggleBtn) searchToggleBtn.click();
                    }

                    // Escape to close mobile menu
                    if (event.key === 'Escape') {
                        mobileNav.classList.remove('active');
                        hamburgerBtn.innerHTML = '<i class="fas fa-bars"></i>';
                    }
                });

                // Add search shortcut hint

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
