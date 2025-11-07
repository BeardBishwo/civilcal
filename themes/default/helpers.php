<?php
/**
 * Theme Helper Functions
 * 
 * Provides convenient helper functions for theme integration
 * with the MVC structure and existing application.
 * 
 * @package Bishwo_Calculator
 * @version 2.1.0
 */

if (!defined('BISHWO_CALCULATOR')) {
    die('Direct access not allowed');
}

/**
 * Get the current theme instance
 * 
 * @return \Bishwo_Calculator\Services\ThemeManager|null
 */
function get_theme() {
    static $theme = null;
    
    if ($theme === null) {
        // Use absolute path to avoid include issues
        $themeManagerPath = dirname(__DIR__, 2) . '/app/Services/ThemeManager.php';
        if (file_exists($themeManagerPath)) {
            require_once $themeManagerPath;
            $theme = new \Bishwo_Calculator\Services\ThemeManager();
        }
    }
    
    return $theme;
}

/**
 * Render a theme partial
 * 
 * @param string $partial Partial name (without .php extension)
 * @param array $data Data to pass to the partial
 * @return string Rendered HTML
 */
function render_partial($partial, $data = []) {
    $theme = get_theme();
    if ($theme) {
        return $theme->renderPartial($partial, $data);
    }
    return '';
}

/**
 * Render a theme view
 * 
 * @param string $view View name (without .php extension)
 * @param array $data Data to pass to the view
 * @return string Rendered HTML
 */
function render_view($view, $data = []) {
    $theme = get_theme();
    if ($theme) {
        return $theme->renderView($view, $data);
    }
    return '';
}

/**
 * Load theme styles
 * 
 * @param string|null $category Category-specific styles to load
 * @return void
 */
function load_theme_styles($category = null) {
    $theme = get_theme();
    if ($theme) {
        $theme->loadThemeStyles();
        
        if ($category) {
            $theme->loadCategoryStyles($category);
        }
    }
}

/**
 * Load theme scripts
 * 
 * @param string|null $category Category-specific scripts to load
 * @return void
 */
function load_theme_scripts($category = null) {
    $theme = get_theme();
    if ($theme) {
        $theme->loadThemeScripts();
        
        if ($category) {
            $theme->loadCategoryScripts($category);
        }
    }
}

/**
 * Get theme asset URL
 * 
 * @param string $path Asset path relative to theme assets directory
 * @return string Full URL to the asset
 */
function theme_asset($path) {
    $theme = get_theme();
    if ($theme) {
        return $theme->getAssetUrl($path);
    }
    return get_base_url() . 'assets/' . ltrim($path, '/');
}

/**
 * Get current theme name
 * 
 * @return string
 */
function get_current_theme() {
    $theme = get_theme();
    if ($theme) {
        return $theme->getCurrentTheme();
    }
    return 'default';
}

/**
 * Set active theme
 * 
 * @param string $theme_name Theme name to activate
 * @return bool Success status
 */
function set_active_theme($theme_name) {
    $theme = get_theme();
    if ($theme) {
        return $theme->setActiveTheme($theme_name);
    }
    return false;
}

/**
 * Get available themes
 * 
 * @return array List of available themes
 */
function get_available_themes() {
    $theme = get_theme();
    if ($theme) {
        return $theme->getAvailableThemes();
    }
    return ['default'];
}

/**
 * Get theme configuration
 * 
 * @param string|null $key Configuration key (null for full config)
 * @param mixed $default Default value if key not found
 * @return mixed
 */
function get_theme_config($key = null, $default = null) {
    $theme = get_theme();
    if ($theme) {
        $config = $theme->getConfig();
        
        if ($key === null) {
            return $config;
        }
        
        return isset($config[$key]) ? $config[$key] : $default;
    }
    return $default;
}

/**
 * Check if theme supports a feature
 * 
 * @param string $feature Feature name
 * @return bool
 */
function theme_supports($feature) {
    $theme = get_theme();
    if ($theme) {
        return $theme->supportsFeature($feature);
    }
    return false;
}

/**
 * Get category-specific classes
 * 
 * @param string $category Category name
 * @return string CSS classes
 */
function get_category_classes($category) {
    $theme = get_theme();
    if ($theme) {
        return $theme->getCategoryClasses($category);
    }
    return 'category-' . $category;
}

/**
 * Get navigation menu items
 * 
 * @return array Navigation menu structure
 */
function get_navigation_menu() {
    return [
        [
            'title' => 'Home',
            'url' => get_base_url(),
            'icon' => 'fas fa-home',
            'active' => is_current_page('index.php')
        ],
        [
            'title' => 'Civil Engineering',
            'url' => get_base_url() . 'modules/civil/',
            'icon' => 'fas fa-building',
            'active' => is_current_category('civil'),
            'submenu' => [
                ['title' => 'Concrete', 'url' => get_base_url() . 'modules/civil/concrete/'],
                ['title' => 'Structural', 'url' => get_base_url() . 'modules/civil/structural/'],
                ['title' => 'Brickwork', 'url' => get_base_url() . 'modules/civil/brickwork/'],
                ['title' => 'Earthwork', 'url' => get_base_url() . 'modules/civil/earthwork/']
            ]
        ],
        [
            'title' => 'Electrical',
            'url' => get_base_url() . 'modules/electrical/',
            'icon' => 'fas fa-bolt',
            'active' => is_current_category('electrical'),
            'submenu' => [
                ['title' => 'Load Calculations', 'url' => get_base_url() . 'modules/electrical/load-calculation/'],
                ['title' => 'Wire Sizing', 'url' => get_base_url() . 'modules/electrical/wire-sizing/'],
                ['title' => 'Short Circuit', 'url' => get_base_url() . 'modules/electrical/short-circuit/'],
                ['title' => 'Voltage Drop', 'url' => get_base_url() . 'modules/electrical/voltage-drop/']
            ]
        ],
        [
            'title' => 'Mechanical/HVAC',
            'url' => get_base_url() . 'modules/hvac/',
            'icon' => 'fas fa-wind',
            'active' => is_current_category('hvac'),
            'submenu' => [
                ['title' => 'Equipment Sizing', 'url' => get_base_url() . 'modules/hvac/equipment-sizing/'],
                ['title' => 'Duct Sizing', 'url' => get_base_url() . 'modules/hvac/duct-sizing/'],
                ['title' => 'Load Calculations', 'url' => get_base_url() . 'modules/hvac/load-calculation/'],
                ['title' => 'Energy Analysis', 'url' => get_base_url() . 'modules/hvac/energy-analysis/']
            ]
        ],
        [
            'title' => 'Plumbing',
            'url' => get_base_url() . 'modules/plumbing/',
            'icon' => 'fas fa-faucet',
            'active' => is_current_category('plumbing'),
            'submenu' => [
                ['title' => 'Pipe Sizing', 'url' => get_base_url() . 'modules/plumbing/pipe_sizing/'],
                ['title' => 'Water Supply', 'url' => get_base_url() . 'modules/plumbing/water_supply/'],
                ['title' => 'Drainage', 'url' => get_base_url() . 'modules/plumbing/drainage/'],
                ['title' => 'Hot Water', 'url' => get_base_url() . 'modules/plumbing/hot_water/']
            ]
        ],
        [
            'title' => 'Fire Protection',
            'url' => get_base_url() . 'modules/fire/',
            'icon' => 'fas fa-fire-extinguisher',
            'active' => is_current_category('fire'),
            'submenu' => [
                ['title' => 'Sprinklers', 'url' => get_base_url() . 'modules/fire/sprinklers/'],
                ['title' => 'Fire Pumps', 'url' => get_base_url() . 'modules/fire/fire-pumps/'],
                ['title' => 'Standpipes', 'url' => get_base_url() . 'modules/fire/standpipes/'],
                ['title' => 'Hydraulics', 'url' => get_base_url() . 'modules/fire/hydraulics/']
            ]
        ],
        [
            'title' => 'Structural',
            'url' => get_base_url() . 'modules/structural/',
            'icon' => 'fas fa-cubes',
            'active' => is_current_category('structural')
        ],
        [
            'title' => 'Site Engineering',
            'url' => get_base_url() . 'modules/site/',
            'icon' => 'fas fa-hard-hat',
            'active' => is_current_category('site')
        ],
        [
            'title' => 'Estimation',
            'url' => get_base_url() . 'modules/estimation/',
            'icon' => 'fas fa-calculator',
            'active' => is_current_category('estimation'),
            'submenu' => [
                ['title' => 'Cost Estimation', 'url' => get_base_url() . 'modules/estimation/cost-estimation/'],
                ['title' => 'Quantity Takeoff', 'url' => get_base_url() . 'modules/estimation/quantity-takeoff/'],
                ['title' => 'Material Estimation', 'url' => get_base_url() . 'modules/estimation/material-estimation/'],
                ['title' => 'Project Financials', 'url' => get_base_url() . 'modules/estimation/project-financials/']
            ]
        ],
        [
            'title' => 'Project Management',
            'url' => get_base_url() . 'modules/project-management/',
            'icon' => 'fas fa-tasks',
            'active' => is_current_category('management')
        ],
        [
            'title' => 'MEP Coordination',
            'url' => get_base_url() . 'modules/mep/',
            'icon' => 'fas fa-sitemap',
            'active' => is_current_category('mep')
        ]
    ];
}

/**
 * Get breadcrumb navigation
 * 
 * @param array $additional Additional breadcrumb items
 * @return array Breadcrumb items
 */
function get_breadcrumb($additional = []) {
    $current_page = basename($_SERVER['PHP_SELF']);
    $breadcrumb = [
        [
            'title' => 'Home',
            'url' => get_base_url()
        ]
    ];
    
    // Add category-based breadcrumbs
    if (strpos($current_page, 'civil') !== false) {
        $breadcrumb[] = [
            'title' => 'Civil Engineering',
            'url' => get_base_url() . 'modules/civil/'
        ];
    } elseif (strpos($current_page, 'electrical') !== false) {
        $breadcrumb[] = [
            'title' => 'Electrical Engineering',
            'url' => get_base_url() . 'modules/electrical/'
        ];
    } elseif (strpos($current_page, 'hvac') !== false) {
        $breadcrumb[] = [
            'title' => 'Mechanical/HVAC',
            'url' => get_base_url() . 'modules/hvac/'
        ];
    } elseif (strpos($current_page, 'plumbing') !== false) {
        $breadcrumb[] = [
            'title' => 'Plumbing',
            'url' => get_base_url() . 'modules/plumbing/'
        ];
    } elseif (strpos($current_page, 'fire') !== false) {
        $breadcrumb[] = [
            'title' => 'Fire Protection',
            'url' => get_base_url() . 'modules/fire/'
        ];
    } elseif (strpos($current_page, 'structural') !== false) {
        $breadcrumb[] = [
            'title' => 'Structural Engineering',
            'url' => get_base_url() . 'modules/structural/'
        ];
    } elseif (strpos($current_page, 'site') !== false) {
        $breadcrumb[] = [
            'title' => 'Site Engineering',
            'url' => get_base_url() . 'modules/site/'
        ];
    } elseif (strpos($current_page, 'estimation') !== false) {
        $breadcrumb[] = [
            'title' => 'Estimation & Costing',
            'url' => get_base_url() . 'modules/estimation/'
        ];
    } elseif (strpos($current_page, 'management') !== false) {
        $breadcrumb[] = [
            'title' => 'Project Management',
            'url' => get_base_url() . 'modules/project-management/'
        ];
    } elseif (strpos($current_page, 'mep') !== false) {
        $breadcrumb[] = [
            'title' => 'MEP Coordination',
            'url' => get_base_url() . 'modules/mep/'
        ];
    }
    
    // Add additional breadcrumb items
    if (!empty($additional)) {
        $breadcrumb = array_merge($breadcrumb, $additional);
    }
    
    // Add current page
    $page_titles = [
        'index.php' => 'Home',
        'concrete-mix.php' => 'Concrete Mix Design',
        'rebar-calculation.php' => 'Rebar Calculation',
        'wire-sizing.php' => 'Wire Sizing',
        'load-calculation.php' => 'Load Calculation',
        'duct-sizing.php' => 'Duct Sizing',
        'pipe-sizing.php' => 'Pipe Sizing',
        'water-supply.php' => 'Water Supply',
        'sprinkler-system.php' => 'Sprinkler System',
        'beam-analysis.php' => 'Beam Analysis',
        'cost-estimation.php' => 'Cost Estimation',
        'boq-preparation.php' => 'BOQ Preparation',
        'dashboard.php' => 'Dashboard',
        'analytics.php' => 'Analytics'
    ];
    
    $current_title = isset($page_titles[$current_page]) ? $page_titles[$current_page] : ucfirst(str_replace(['.php', '-', '_'], [' ', ' ', ' '], $current_page));
    
    $breadcrumb[] = [
        'title' => $current_title,
        'url' => null // Current page
    ];
    
    return $breadcrumb;
}

/**
 * Check if current page matches a specific page
 * 
 * @param string $page Page filename
 * @return bool
 */
function is_current_page($page) {
    return basename($_SERVER['PHP_SELF']) === $page;
}

/**
 * Check if current category matches
 * 
 * @param string $category Category name
 * @return bool
 */
function is_current_category($category) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return strpos($current_page, $category) !== false;
}

/**
 * Get page title with fallback
 * 
 * @param string|null $custom_title Custom title
 * @param string|null $category Category for context
 * @return string
 */
function get_page_title($custom_title = null, $category = null) {
    if ($custom_title) {
        return $custom_title;
    }
    
    $current_page = basename($_SERVER['PHP_SELF']);
    $page_titles = [
        'index.php' => 'Professional Engineering Calculators',
        'civil.php' => 'Civil Engineering Calculators',
        'electrical.php' => 'Electrical Engineering Calculators',
        'hvac.php' => 'Mechanical/HVAC Calculators',
        'plumbing.php' => 'Plumbing Calculators',
        'fire.php' => 'Fire Protection Calculators',
        'structural.php' => 'Structural Engineering Calculators',
        'site.php' => 'Site Engineering Calculators',
        'estimation.php' => 'Estimation & Costing Tools',
        'management.php' => 'Project Management Tools',
        'mep.php' => 'MEP Coordination Tools'
    ];
    
    return isset($page_titles[$current_page]) ? $page_titles[$current_page] : 'Engineering Calculator';
}

/**
 * Get base URL
 * 
 * @return string
 */
function get_base_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    
    // Remove filename from script name to get directory
    $base_path = dirname($script_name);
    if ($base_path === '/') {
        $base_path = '';
    }
    
    return $protocol . '://' . $host . $base_path . '/';
}

/**
 * Get meta description for current page
 * 
 * @param string|null $custom_description Custom description
 * @return string
 */
function get_meta_description($custom_description = null) {
    if ($custom_description) {
        return $custom_description;
    }
    
    $current_page = basename($_SERVER['PHP_SELF']);
    $descriptions = [
        'index.php' => 'Professional engineering calculators for civil, electrical, mechanical, plumbing, fire protection, structural analysis, and construction estimation.',
        'civil.php' => 'Civil engineering calculators for concrete design, structural analysis, brickwork, earthwork, and foundation calculations.',
        'electrical.php' => 'Electrical engineering calculators for load calculations, wire sizing, short circuit analysis, and voltage drop calculations.',
        'hvac.php' => 'Mechanical and HVAC calculators for equipment sizing, duct design, load calculations, and energy analysis.',
        'plumbing.php' => 'Plumbing calculators for pipe sizing, water supply design, drainage systems, and hot water calculations.',
        'fire.php' => 'Fire protection calculators for sprinkler systems, fire pumps, standpipes, and hydraulic calculations.',
        'structural.php' => 'Structural engineering calculators for beam analysis, column design, foundation design, and reinforcement.',
        'site.php' => 'Site engineering tools for surveying, earthwork, productivity analysis, and safety calculations.',
        'estimation.php' => 'Construction estimation tools for cost estimation, quantity takeoff, material estimation, and project financials.',
        'management.php' => 'Project management tools for scheduling, cost control, quality management, and progress tracking.',
        'mep.php' => 'MEP coordination tools for mechanical, electrical, and plumbing system integration and clash detection.'
    ];
    
    return isset($descriptions[$current_page]) ? $descriptions[$current_page] : 'Professional engineering calculators and tools for construction and design projects.';
}

/**
 * Get structured data (JSON-LD) for SEO
 * 
 * @param string $type Schema.org type
 * @param array $data Additional data
 * @return string JSON-LD script tag
 */
function get_structured_data($type = 'WebApplication', $data = []) {
    $default_data = [
        '@context' => 'https://schema.org',
        '@type' => $type,
        'name' => 'Bishwo Calculator',
        'description' => get_meta_description(),
        'url' => get_base_url(),
        'applicationCategory' => 'Engineering',
        'operatingSystem' => 'Web Browser',
        'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'USD'
        ],
        'author' => [
            '@type' => 'Organization',
            'name' => 'Bishwo Calculator'
        ]
    ];
    
    $structured_data = array_merge($default_data, $data);
    
    return '<script type="application/ld+json">' . json_encode($structured_data, JSON_UNESCAPED_SLASHES) . '</script>';
}

/**
 * Generate page meta tags
 * 
 * @param array $options Meta tag options
 * @return string Meta tags HTML
 */
function generate_meta_tags($options = []) {
    $defaults = [
        'title' => get_page_title($options['title'] ?? null),
        'description' => get_meta_description($options['description'] ?? null),
        'keywords' => 'engineering calculator, construction calculator, civil engineering, electrical calculator, structural analysis',
        'og:image' => get_base_url() . 'assets/images/banner.jpg',
        'twitter:card' => 'summary_large_image'
    ];
    
    $meta = array_merge($defaults, $options);
    
    $html = '';
    $html .= '<meta name="description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
    $html .= '<meta name="keywords" content="' . htmlspecialchars($meta['keywords']) . '">' . "\n";
    $html .= '<meta property="og:title" content="' . htmlspecialchars($meta['title']) . '">' . "\n";
    $html .= '<meta property="og:description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
    $html .= '<meta property="og:image" content="' . htmlspecialchars($meta['og:image']) . '">' . "\n";
    $html .= '<meta property="og:url" content="' . htmlspecialchars(get_base_url() . $_SERVER['REQUEST_URI']) . '">' . "\n";
    $html .= '<meta name="twitter:card" content="' . htmlspecialchars($meta['twitter:card']) . '">' . "\n";
    $html .= '<meta name="twitter:title" content="' . htmlspecialchars($meta['title']) . '">' . "\n";
    $html .= '<meta name="twitter:description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
    $html .= '<meta name="twitter:image" content="' . htmlspecialchars($meta['og:image']) . '">' . "\n";
    
    return $html;
}

/**
 * Display flash message
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message text
 * @return void
 */
function flash_message($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

/**
 * Get flash message and clear it
 * 
 * @param string $type Message type
 * @param string $default Default value if no message
 * @return string|null
 */
function get_flash_message($type, $default = null) {
    $key = 'flash_' . $type;
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return $default;
}

/**
 * Sanitize output for HTML
 * 
 * @param string $string String to sanitize
 * @return string Sanitized string
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate text to specified length
 * 
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to add when truncated
 * @return string Truncated text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length - strlen($suffix)) . $suffix;
}
?>
