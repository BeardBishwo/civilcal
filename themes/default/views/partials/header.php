<!DOCTYPE html>
<html lang="en" class="<?php
    $dark = (isset($theme_data['features']['dark_mode']) && $theme_data['features']['dark_mode']);
    try {
        $tm0 = new \App\Services\ThemeManager();
        $md0 = $tm0->getThemeMetadata();
        if (isset($md0['settings']['dark_mode_enabled']) && $md0['settings']['dark_mode_enabled']) { $dark = true; }
    } catch (\Throwable $e) {}
    echo $dark ? 'dark' : '';
?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>Bishwo Calculator - Engineering & Construction Calculators</title>
    
    <!-- Meta Description -->
    <meta name="description" content="Professional engineering calculators for civil, electrical, plumbing, HVAC, fire protection, structural analysis, and construction estimation. Streamline your engineering workflow with precision tools.">
    <meta name="keywords" content="engineering calculator, construction calculator, civil engineering, electrical calculator, plumbing calculator, HVAC calculator, structural analysis, cost estimation, project management">
    <meta name="author" content="Bishwo Calculator">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>Bishwo Calculator">
    <meta property="og:description" content="Professional engineering calculators for civil, electrical, plumbing, HVAC, fire protection, structural analysis, and construction estimation.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo $base_url; ?>assets/images/banner.jpg">
    <meta property="og:url" content="<?php echo $base_url . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>Bishwo Calculator">
    <meta name="twitter:description" content="Professional engineering calculators for civil, electrical, plumbing, HVAC, fire protection, structural analysis, and construction estimation.">
    <meta name="twitter:image" content="<?php echo $base_url; ?>assets/images/banner.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/images/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo $base_url; ?>assets/images/applogo.png">
    
    <!-- Preconnect to External Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkfQJa+GQZ4zJQFzFZC2Z1C1QvZ1C6Zhz+Kbx5Q5v8Q7Zx1Zq9a2Yx4Yw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <?php
    // Load theme styles
    if (isset($theme) && method_exists($theme, 'loadThemeStyles')) {
        $theme->loadThemeStyles();
    }
    
    // Load category-specific styles
    if (isset($category) && isset($theme) && method_exists($theme, 'loadCategoryStyles')) {
        $theme->loadCategoryStyles($category);
    }
    ?>
    
    <?php
    try {
        $tm = new \App\Services\ThemeManager();
        $meta = $tm->getThemeMetadata();
        $cfg = $meta['config'] ?? [];
        $settings = $meta['settings'] ?? [];
        $colors = $cfg['colors'] ?? [];
        $primary = $settings['primary'] ?? ($colors['primary'] ?? '#2563eb');
        $secondary = $settings['secondary'] ?? ($colors['secondary'] ?? '#64748b');
        $accent = $settings['accent'] ?? ($colors['accent'] ?? '#0ea5e9');
        $bgPrimary = $settings['background'] ?? ($colors['background'] ?? '#ffffff');
        $textPrimary = $settings['text'] ?? ($colors['text'] ?? '#1e293b');
        $textSecondary = $settings['text_secondary'] ?? ($colors['text_secondary'] ?? '#64748b');
    } catch (\Throwable $e) {
        $primary = '#2563eb'; $secondary = '#64748b'; $accent = '#0ea5e9';
        $bgPrimary = '#ffffff'; $textPrimary = '#1e293b'; $textSecondary = '#64748b';
    }
    ?>
    <style>
        :root {
            /* Primary Colors */
            --primary-color: <?= htmlspecialchars($primary) ?>;
            --primary-dark: <?= htmlspecialchars($primary) ?>;
            --primary-light: <?= htmlspecialchars($primary) ?>;
            --secondary-color: <?= htmlspecialchars($secondary) ?>;
            --accent-color: <?= htmlspecialchars($accent) ?>;
            
            /* Text Colors */
            --text-primary: <?= htmlspecialchars($textPrimary) ?>;
            --text-secondary: <?= htmlspecialchars($textSecondary) ?>;
            --text-light: #94a3b8;
            --text-muted: #cbd5e1;
            
            /* Background Colors */
            --bg-primary: <?= htmlspecialchars($bgPrimary) ?>;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --bg-dark: #0f172a;
            
            /* Border Colors */
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            --border-dark: #cbd5e1;
            
            /* Shadow Colors */
            --shadow-light: rgba(0, 0, 0, 0.05);
            --shadow-medium: rgba(0, 0, 0, 0.1);
            --shadow-dark: rgba(0, 0, 0, 0.25);
            
            /* Typography */
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-mono: 'JetBrains Mono', 'Monaco', 'Consolas', monospace;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;
            --font-size-3xl: 1.875rem;
            --font-size-4xl: 2.25rem;
            
            /* Spacing */
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            --spacing-3xl: 4rem;
            
            /* Border Radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            
            /* Transitions */
            --transition-fast: 0.15s ease-in-out;
            --transition-normal: 0.3s ease-in-out;
            --transition-slow: 0.5s ease-in-out;
            
            /* Z-index */
            --z-dropdown: 1000;
            --z-sticky: 1020;
            --z-fixed: 1030;
            --z-modal-backdrop: 1040;
            --z-modal: 1050;
            --z-popover: 1060;
            --z-tooltip: 1070;
            --z-toast: 1080;
        }
        
        .dark {
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-light: #94a3b8;
            --text-muted: #64748b;
            
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --bg-dark: #020617;
            
            --border-color: #334155;
            --border-light: #475569;
            --border-dark: #64748b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-family);
            font-size: var(--font-size-base);
            line-height: 1.6;
            color: var(--text-primary);
            background-color: var(--bg-primary);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Header Styles */
        .header {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 1px 3px var(--shadow-light);
            position: sticky;
            top: 0;
            z-index: var(--z-sticky);
            transition: all var(--transition-normal);
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--spacing-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }
        
        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 700;
            font-size: var(--font-size-xl);
            transition: color var(--transition-fast);
        }
        
        .logo:hover {
            color: var(--primary-color);
        }
        
        .logo img {
            height: 40px;
            width: auto;
            margin-right: var(--spacing-sm);
        }
        
        /* Navigation */
        .nav {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
        }
        
        .nav-list {
            display: flex;
            list-style: none;
            gap: var(--spacing-lg);
            margin: 0;
            padding: 0;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
        }
        
        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color);
            background-color: var(--bg-secondary);
        }
        
        .nav-link i {
            font-size: var(--font-size-sm);
        }
        
        /* Dropdown */
        .dropdown {
            position: relative;
        }
        
        .dropdown-toggle {
            background: none;
            border: none;
            color: var(--text-secondary);
            font: inherit;
            cursor: pointer;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
        }
        
        .dropdown-toggle:hover {
            color: var(--primary-color);
            background-color: var(--bg-secondary);
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            box-shadow: 0 4px 12px var(--shadow-medium);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all var(--transition-normal);
            z-index: var(--z-dropdown);
        }
        
        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item {
            display: block;
            color: var(--text-secondary);
            text-decoration: none;
            padding: var(--spacing-sm) var(--spacing-md);
            transition: all var(--transition-fast);
        }
        
        .dropdown-item:hover {
            color: var(--primary-color);
            background-color: var(--bg-secondary);
        }
        
        /* Search */
        .search {
            position: relative;
            margin: 0 var(--spacing-lg);
        }
        
        .search-input {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-sm) var(--spacing-md) var(--spacing-sm) 40px;
            font-size: var(--font-size-sm);
            color: var(--text-primary);
            width: 300px;
            transition: all var(--transition-fast);
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: var(--spacing-md);
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: var(--font-size-sm);
        }
        
        /* Header Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }
        
        .theme-toggle,
        .mobile-menu-toggle {
            background: none;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: var(--spacing-sm);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all var(--transition-fast);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }
        
        .theme-toggle:hover,
        .mobile-menu-toggle:hover {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-toggle {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            color: var(--text-secondary);
            padding: var(--spacing-sm);
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
        }
        
        .user-toggle:hover {
            color: var(--primary-color);
            background-color: var(--bg-secondary);
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: var(--font-size-sm);
        }
        
        /* Mobile Menu */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: var(--z-modal-backdrop);
        }
        
        .mobile-menu-content {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            background: var(--bg-primary);
            padding: var(--spacing-xl);
            overflow-y: auto;
        }
        
        .mobile-menu-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
        }
        
        .mobile-nav {
            list-style: none;
        }
        
        .mobile-nav-item {
            margin-bottom: var(--spacing-sm);
        }
        
        .mobile-nav-link {
            display: block;
            color: var(--text-secondary);
            text-decoration: none;
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
        }
        
        .mobile-nav-link:hover,
        .mobile-nav-link.active {
            color: var(--primary-color);
            background-color: var(--bg-secondary);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .search {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .header-container {
                padding: 0 var(--spacing-sm);
            }
            
            .nav {
                display: none;
            }
            
            .mobile-menu-toggle {
                display: flex;
            }
            
            .search-input {
                width: 200px;
            }
        }
        
        @media (max-width: 480px) {
            .header-actions {
                gap: var(--spacing-sm);
            }
            
            .search-input {
                width: 150px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <!-- Logo -->
            <a href="<?php echo $base_url; ?>" class="logo">
                <img src="<?php echo $base_url; ?>assets/images/applogo.png" alt="Bishwo Calculator" onerror="this.style.display='none'">
                <span>Bishwo Calculator</span>
            </a>
            
            <!-- Navigation -->
            <nav class="nav" role="navigation" aria-label="Main navigation">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i>
                            Home
                        </a>
                    </li>
                    <?php
                    try {
                        $calcs = \App\Calculators\CalculatorFactory::getAvailableCalculators();
                        $groups = [];
                        foreach ($calcs as $c) {
                            $cat = $c['category'] ?? 'general';
                            if (!isset($groups[$cat])) $groups[$cat] = [];
                            $groups[$cat][] = $c;
                        }
                        if (!empty($groups)) {
                            echo '<li class="nav-item dropdown">';
                            echo '<button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">';
                            echo '<i class="fas fa-calculator"></i> Calculators <i class="fas fa-chevron-down"></i>';
                            echo '</button>';
                            echo '<div class="dropdown-menu" role="menu">';
                            foreach ($groups as $cat => $items) {
                                $catLabel = ucwords(str_replace(['-','_'], ' ', (string)$cat));
                                echo '<div class="dropdown-item" style="font-weight:600; cursor:default;">' . htmlspecialchars($catLabel) . '</div>';
                                foreach ($items as $it) {
                                    $slug = $it['slug'] ?? '';
                                    $name = $it['name'] ?? $slug;
                                    if ($slug) {
                                        $href = '/calculator/' . rawurlencode((string)$cat) . '/' . rawurlencode((string)$slug);
                                        echo '<a href="' . $href . '" class="dropdown-item">' . htmlspecialchars((string)$name) . '</a>';
                                    }
                                }
                                echo '<hr style="border:none;border-top:1px solid var(--border-color);margin:4px 0;">';
                            }
                            echo '</div>';
                            echo '</li>';
                        }
                    } catch (\Throwable $e) { }
                    ?>
                    
                    <!-- Civil Engineering Dropdown -->
                    <li class="nav-item dropdown">
                        <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-building"></i>
                            Civil Engineering
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            <a href="<?php echo $base_url; ?>modules/civil/concrete/" class="dropdown-item">Concrete Calculators</a>
                            <a href="<?php echo $base_url; ?>modules/civil/structural/" class="dropdown-item">Structural Analysis</a>
                            <a href="<?php echo $base_url; ?>modules/civil/brickwork/" class="dropdown-item">Brickwork & Masonry</a>
                            <a href="<?php echo $base_url; ?>modules/civil/earthwork/" class="dropdown-item">Earthwork & Excavation</a>
                        </div>
                    </li>
                    
                    <!-- Electrical Engineering Dropdown -->
                    <li class="nav-item dropdown">
                        <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bolt"></i>
                            Electrical
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            <a href="<?php echo $base_url; ?>modules/electrical/load-calculation/" class="dropdown-item">Load Calculations</a>
                            <a href="<?php echo $base_url; ?>modules/electrical/wire-sizing/" class="dropdown-item">Wire Sizing</a>
                            <a href="<?php echo $base_url; ?>modules/electrical/short-circuit/" class="dropdown-item">Short Circuit Analysis</a>
                            <a href="<?php echo $base_url; ?>modules/electrical/voltage-drop/" class="dropdown-item">Voltage Drop</a>
                        </div>
                    </li>
                    
                    <!-- Mechanical/HVAC Dropdown -->
                    <li class="nav-item dropdown">
                        <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-wind"></i>
                            Mechanical/HVAC
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            <a href="<?php echo $base_url; ?>modules/hvac/equipment-sizing/" class="dropdown-item">Equipment Sizing</a>
                            <a href="<?php echo $base_url; ?>modules/hvac/duct-sizing/" class="dropdown-item">Duct Sizing</a>
                            <a href="<?php echo $base_url; ?>modules/hvac/load-calculation/" class="dropdown-item">Load Calculations</a>
                            <a href="<?php echo $base_url; ?>modules/hvac/energy-analysis/" class="dropdown-item">Energy Analysis</a>
                        </div>
                    </li>
                    
                    <!-- Plumbing Dropdown -->
                    <li class="nav-item dropdown">
                        <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-faucet"></i>
                            Plumbing
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            <a href="<?php echo $base_url; ?>modules/plumbing/pipe_sizing/" class="dropdown-item">Pipe Sizing</a>
                            <a href="<?php echo $base_url; ?>modules/plumbing/water_supply/" class="dropdown-item">Water Supply</a>
                            <a href="<?php echo $base_url; ?>modules/plumbing/drainage/" class="dropdown-item">Drainage Systems</a>
                            <a href="<?php echo $base_url; ?>modules/plumbing/hot_water/" class="dropdown-item">Hot Water Systems</a>
                        </div>
                    </li>
                    
                    <!-- Fire Protection Dropdown -->
                    <li class="nav-item dropdown">
                        <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-fire-extinguisher"></i>
                            Fire Protection
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            <a href="<?php echo $base_url; ?>modules/fire/sprinklers/" class="dropdown-item">Sprinkler Systems</a>
                            <a href="<?php echo $base_url; ?>modules/fire/fire-pumps/" class="dropdown-item">Fire Pumps</a>
                            <a href="<?php echo $base_url; ?>modules/fire/standpipes/" class="dropdown-item">Standpipe Systems</a>
                            <a href="<?php echo $base_url; ?>modules/fire/hydraulics/" class="dropdown-item">Hydraulic Calculations</a>
                        </div>
                    </li>
                    
                    <!-- Structural Engineering -->
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/structural/" class="nav-link">
                            <i class="fas fa-cubes"></i>
                            Structural
                        </a>
                    </li>
                    
                    <!-- Site Engineering -->
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/site/" class="nav-link">
                            <i class="fas fa-hard-hat"></i>
                            Site Engineering
                        </a>
                    </li>
                    
                    <!-- Estimation & Costing -->
                    <li class="nav-item dropdown">
                        <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-calculator"></i>
                            Estimation
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            <a href="<?php echo $base_url; ?>modules/estimation/cost-estimation/" class="dropdown-item">Cost Estimation</a>
                            <a href="<?php echo $base_url; ?>modules/estimation/quantity-takeoff/" class="dropdown-item">Quantity Takeoff</a>
                            <a href="<?php echo $base_url; ?>modules/estimation/material-estimation/" class="dropdown-item">Material Estimation</a>
                            <a href="<?php echo $base_url; ?>modules/estimation/project-financials/" class="dropdown-item">Project Financials</a>
                        </div>
                    </li>
                    
                    <!-- Project Management -->
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/project-management/" class="nav-link">
                            <i class="fas fa-tasks"></i>
                            Project Management
                        </a>
                    </li>
                    
                    <!-- MEP Coordination -->
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/mep/" class="nav-link">
                            <i class="fas fa-sitemap"></i>
                            MEP Coordination
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Search -->
            <?php if (isset($theme_data['features']['search']) && $theme_data['features']['search']): ?>
            <div class="search" role="search">
                <i class="fas fa-search search-icon"></i>
                <input type="search" class="search-input" placeholder="Search calculators..." aria-label="Search calculators">
            </div>
            <?php endif; ?>
            
            <!-- Header Actions -->
            <div class="header-actions">
                <!-- Theme Toggle -->
                <button class="theme-toggle" aria-label="Toggle dark mode" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </button>
                
                <!-- User Menu -->
                <div class="user-menu">
                    <button class="user-toggle" aria-haspopup="true" aria-expanded="false">
                        <div class="user-avatar">B</div>
                        <span class="user-name">Bishwo</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a href="<?php echo $base_url; ?>profile.php" class="dropdown-item">Profile</a>
                        <a href="<?php echo $base_url; ?>settings.php" class="dropdown-item">Settings</a>
                        <a href="<?php echo $base_url; ?>help.php" class="dropdown-item">Help & Support</a>
                        <hr style="border: none; border-top: 1px solid var(--border-color); margin: var(--spacing-sm) 0;">
                        <a href="<?php echo $base_url; ?>logout.php" class="dropdown-item">Logout</a>
                    </div>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-label="Open mobile menu" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
        <div class="mobile-menu-content">
            <div class="mobile-menu-header">
                <a href="<?php echo $base_url; ?>" class="logo">
                    <img src="<?php echo $base_url; ?>assets/images/applogo.png" alt="Bishwo Calculator" onerror="this.style.display='none'">
                    <span>Bishwo Calculator</span>
                </a>
                <button class="mobile-menu-toggle" aria-label="Close mobile menu" onclick="toggleMobileMenu()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <nav class="mobile-nav" role="navigation" aria-label="Mobile navigation">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>" class="mobile-nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/civil/" class="mobile-nav-link">Civil Engineering</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/electrical/" class="mobile-nav-link">Electrical Engineering</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/hvac/" class="mobile-nav-link">Mechanical/HVAC</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/plumbing/" class="mobile-nav-link">Plumbing</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/fire/" class="mobile-nav-link">Fire Protection</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/structural/" class="mobile-nav-link">Structural Engineering</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/site/" class="mobile-nav-link">Site Engineering</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/estimation/" class="mobile-nav-link">Estimation & Costing</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/project-management/" class="mobile-nav-link">Project Management</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base_url; ?>modules/mep/" class="mobile-nav-link">MEP Coordination</a>
                    </li>
                </ul>
            </nav>
            
            <!-- Mobile Search -->
            <?php if (isset($theme_data['features']['search']) && $theme_data['features']['search']): ?>
            <div class="search" style="margin-top: var(--spacing-xl);">
                <i class="fas fa-search search-icon"></i>
                <input type="search" class="search-input" placeholder="Search calculators..." aria-label="Search calculators">
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
        
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const isOpen = mobileMenu.style.display === 'block';
            
            if (isOpen) {
                mobileMenu.style.display = 'none';
                mobileMenu.setAttribute('aria-hidden', 'true');
            } else {
                mobileMenu.style.display = 'block';
                mobileMenu.setAttribute('aria-hidden', 'false');
            }
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            
            if (mobileMenu.style.display === 'block' && 
                !mobileMenu.contains(event.target) && 
                !mobileMenuToggle.contains(event.target)) {
                toggleMobileMenu();
            }
        });
        
        // Close mobile menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const mobileMenu = document.getElementById('mobileMenu');
                if (mobileMenu.style.display === 'block') {
                    toggleMobileMenu();
                }
            }
        });
        
        // Initialize theme from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        });
        
        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInputs = document.querySelectorAll('.search-input');
            
            searchInputs.forEach(input => {
                input.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        performSearch(this.value);
                    }
                });
            });
        });
        
        function performSearch(query) {
            if (query.trim()) {
                // Redirect to search results page
                window.location.href = '<?php echo $base_url; ?>search.php?q=' + encodeURIComponent(query);
            }
        }
    </script>
</body>
</html>
