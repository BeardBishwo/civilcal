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
    <script>
        window.appConfig = {
            baseUrl: "<?php echo rtrim(defined('APP_BASE') ? APP_BASE : '', '/'); ?>",
            csrfToken: "<?php echo csrf_token(); ?>"
        };
    </script>
    <meta name="theme-color" content="#000000">

    <link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo app_base_url('public/assets/css/global-notifications.css'); ?>">
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
    <!-- Inline styles removed to ensure single source of truth in theme.css -->
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
                    <ul id="mainNavList">
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
                            <li class="has-dropdown" id="moreToolsItem">
                                <a href="#" aria-haspopup="true" aria-expanded="false" role="button" tabindex="0">
                                    <i class="fas fa-layer-group"></i>
                                    More Tools
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul class="dropdown" id="moreToolsMenu" role="menu">
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
            <div class="search-results-container">
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
                const favoritesItem = document.getElementById('favoritesMenuItem');
                const helpItem = document.getElementById('helpMenuItem');

                if (favoritesItem) {
                    favoritesItem.addEventListener('click', function(e) {
                        e.preventDefault();
                        showNotification('Favorites feature coming soon!', 'info');
                    });
                }
            })();



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
