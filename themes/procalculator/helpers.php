<?php
/**
 * ProCalculator Premium Theme Helper Functions
 * 
 * Ultra-premium $100K quality helper functions for theme integration
 * 
 * @package ProCalculator
 * @version 1.0.0
 * @author Bishwo Calculator Team
 * @license Premium
 */

// Prevent direct access
if (!defined('PROCALCULATOR_THEME_VERSION')) {
    define('PROCALCULATOR_THEME_VERSION', '1.0.0');
}

/**
 * Theme Configuration Manager
 */
class ProCalculatorThemeConfig
{
    private static $config = null;
    private static $themePath = '/themes/procalculator/';

    /**
     * Load theme configuration
     */
    public static function getConfig()
    {
        if (self::$config === null) {
            $configFile = self::$themePath . 'theme.json';
            if (file_exists($configFile)) {
                self::$config = json_decode(file_get_contents($configFile), true);
            } else {
                self::$config = self::getDefaultConfig();
            }
        }
        return self::$config;
    }

    /**
     * Get specific config value
     */
    public static function get($key, $default = null)
    {
        $config = self::getConfig();
        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Get default configuration
     */
    private static function getDefaultConfig()
    {
        return [
            'name' => 'ProCalculator - Premium $100K Theme',
            'version' => '1.0.0',
            'premium' => true,
            'colors' => [
                'primary' => '#1a1a2e',
                'secondary' => '#16213e',
                'accent' => '#0f4c75',
                'premium' => '#3f72af'
            ],
            'features' => [
                'dark_mode' => true,
                'glassmorphism' => true,
                'animations' => true,
                'premium_ui' => true
            ]
        ];
    }
}

/**
 * Asset Manager for Premium Theme
 */
class ProCalculatorAssets
{
    private static $basePath = '/themes/procalculator/';
    private static $loaded = [];

    /**
     * Enqueue premium CSS files
     */
    public static function enqueueStyles()
    {
        $config = ProCalculatorThemeConfig::getConfig();
        $styles = $config['styles'] ?? [];

        foreach ($styles as $style) {
            $path = self::$basePath . $style;
            if (!in_array($path, self::$loaded)) {
                echo '<link rel="stylesheet" href="' . htmlspecialchars($path) . '">' . "\n";
                self::$loaded[] = $path;
            }
        }
    }

    /**
     * Enqueue premium JavaScript files
     */
    public static function enqueueScripts()
    {
        $config = ProCalculatorThemeConfig::getConfig();
        $scripts = $config['scripts'] ?? [];

        foreach ($scripts as $script) {
            $path = self::$basePath . $script;
            if (!in_array($path, self::$loaded)) {
                echo '<script src="' . htmlspecialchars($path) . '"></script>' . "\n";
                self::$loaded[] = $path;
            }
        }
    }

    /**
     * Get asset URL
     */
    public static function getAssetUrl($path)
    {
        return self::$basePath . 'assets/' . ltrim($path, '/');
    }

    /**
     * Get theme CSS URL
     */
    public static function getCSSUrl($filename)
    {
        return self::$basePath . 'assets/css/' . $filename;
    }

    /**
     * Get theme JS URL
     */
    public static function getJSUrl($filename)
    {
        return self::$basePath . 'assets/js/' . $filename;
    }

    /**
     * Get theme image URL
     */
    public static function getImageUrl($filename)
    {
        return self::$basePath . 'assets/images/' . $filename;
    }
}

/**
 * Premium Authentication Helper
 */
class ProCalculatorAuth
{
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get current user data
     */
    public static function getCurrentUser()
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'role' => $_SESSION['user_role'] ?? 'engineer',
            'avatar' => $_SESSION['user_avatar'] ?? '/themes/procalculator/assets/images/default-avatar.png',
            'premium' => $_SESSION['user_premium'] ?? false
        ];
    }

    /**
     * Check if current user has premium access
     */
    public static function hasPremiumAccess()
    {
        $user = self::getCurrentUser();
        return $user && ($user['premium'] || in_array($user['role'], ['admin', 'premium']));
    }

    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Check username availability
     */
    public static function checkUsernameAvailability($username)
    {
        try {
            // Database connection
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] == 0;
        } catch (PDOException $e) {
            error_log("ProCalculator Auth Error: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Calculator Integration Helper
 */
class ProCalculatorCalculators
{
    /**
     * Get available calculator categories
     */
    public static function getCategories()
    {
        return [
            'civil' => [
                'name' => 'Civil Engineering',
                'icon' => 'fas fa-building',
                'color' => '#4F46E5',
                'calculators' => ['beam-analysis', 'concrete-design', 'steel-design', 'foundation']
            ],
            'electrical' => [
                'name' => 'Electrical',
                'icon' => 'fas fa-bolt',
                'color' => '#F59E0B',
                'calculators' => ['load-calculation', 'circuit-design', 'wiring', 'electrical-safety']
            ],
            'plumbing' => [
                'name' => 'Plumbing',
                'icon' => 'fas fa-faucet',
                'color' => '#10B981',
                'calculators' => ['pipe-sizing', 'water-flow', 'drainage', 'backflow-prevention']
            ],
            'hvac' => [
                'name' => 'HVAC',
                'icon' => 'fas fa-wind',
                'color' => '#6B7280',
                'calculators' => ['load-calculation', 'duct-sizing', 'equipment-selection', 'energy-efficiency']
            ],
            'fire' => [
                'name' => 'Fire Safety',
                'icon' => 'fas fa-fire-extinguisher',
                'color' => '#EF4444',
                'calculators' => ['sprinkler-design', 'egress-calculations', 'fire-load', 'suppression']
            ],
            'structural' => [
                'name' => 'Structural',
                'icon' => 'fas fa-drafting-compass',
                'color' => '#8B5CF6',
                'calculators' => ['load-analysis', 'member-design', 'connection-design', 'seismic']
            ]
        ];
    }

    /**
     * Get premium calculators
     */
    public static function getPremiumCalculators()
    {
        return [
            'advanced-beam-analysis',
            'finite-element-analysis',
            'optimization-tools',
            '3d-visualization',
            'collaboration-tools',
            'export-pdf',
            'api-access'
        ];
    }

    /**
     * Check if calculator requires premium access
     */
    public static function requiresPremium($calculator)
    {
        return in_array($calculator, self::getPremiumCalculators());
    }
}

/**
 * Database Integration Helper
 */
class ProCalculatorDatabase
{
    /**
     * Get user calculation history
     */
    public static function getUserHistory($userId, $limit = 50)
    {
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $db->prepare("
                SELECT * FROM calculation_history 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProCalculator Database Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Save calculation result
     */
    public static function saveCalculation($userId, $calculator, $input, $output, $favorites = false)
    {
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $db->prepare("
                INSERT INTO calculation_history (user_id, calculator, input_data, output_data, is_favorite, created_at)
                VALUES (:user_id, :calculator, :input_data, :output_data, :is_favorite, NOW())
            ");

            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':calculator', $calculator, PDO::PARAM_STR);
            $stmt->bindParam(':input_data', json_encode($input), PDO::PARAM_STR);
            $stmt->bindParam(':output_data', json_encode($output), PDO::PARAM_STR);
            $stmt->bindParam(':is_favorite', $favorites, PDO::PARAM_BOOL);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("ProCalculator Database Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user favorites
     */
    public static function getUserFavorites($userId)
    {
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $db->prepare("
                SELECT * FROM calculation_history 
                WHERE user_id = :user_id AND is_favorite = 1 
                ORDER BY created_at DESC
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProCalculator Database Error: " . $e->getMessage());
            return [];
        }
    }
}

/**
 * Premium Theme Helper Functions
 */
class ProCalculatorHelper
{
    /**
     * Render premium theme header
     */
    public static function renderHeader($title = null, $description = null, $additionalStyles = [], $additionalScripts = [])
    {
        $title = $title ?? 'ProCalculator - Premium Engineering Platform';
        $description = $description ?? 'Ultra-premium $100,000 quality engineering calculator platform';
        
        $config = ProCalculatorThemeConfig::getConfig();
        
        include PROCALC_THEME_PATH . 'views/partials/header.php';
    }

    /**
     * Render premium theme footer
     */
    public static function renderFooter($additionalScripts = [])
    {
        include PROCALC_THEME_PATH . 'views/partials/footer.php';
    }

    /**
     * Generate premium color CSS custom properties
     */
    public static function generateCSSVariables()
    {
        $config = ProCalculatorThemeConfig::getConfig();
        $colors = $config['colors'] ?? [];
        $gradients = $config['gradients'] ?? [];

        echo "<style>\n";
        echo ":root {\n";
        
        // Colors
        foreach ($colors as $name => $value) {
            echo "  --color-{$name}: {$value};\n";
        }
        
        // Gradients
        foreach ($gradients as $name => $value) {
            echo "  --gradient-{$name}: {$value};\n";
        }
        
        echo "}\n";
        echo "</style>\n";
    }

    /**
     * Get theme version
     */
    public static function getThemeVersion()
    {
        return ProCalculatorThemeConfig::get('version', '1.0.0');
    }

    /**
     * Check if feature is enabled
     */
    public static function hasFeature($feature)
    {
        return ProCalculatorThemeConfig::get("features.{$feature}", false);
    }

    /**
     * Format currency for premium features
     */
    public static function formatCurrency($amount, $currency = '$')
    {
        return $currency . number_format($amount, 0, '.', ',');
    }

    /**
     * Get premium upgrade link
     */
    public static function getUpgradeLink()
    {
        return '/upgrade?theme=procalculator&plan=premium';
    }

    /**
     * Get support contact information
     */
    public static function getSupportContact()
    {
        return [
            'email' => 'support@bishwocalculator.com',
            'phone' => '+1-800-PRO-CALC',
            'chat' => '/support/chat',
            'documentation' => '/docs/procalculator'
        ];
    }

    /**
     * Log premium theme activity
     */
    public static function logActivity($action, $data = [])
    {
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
            error_log("ProCalculator Theme Activity: {$action} - " . json_encode($data));
        }
    }
}

/**
 * Initialize theme constants
 */
if (!defined('PROCALC_THEME_PATH')) {
    define('PROCALC_THEME_PATH', __DIR__ . '/');
}

/**
 * Initialize CSRF token if not exists
 */
if (!isset($_SESSION['csrf_token'])) {
    ProCalculatorAuth::generateCSRFToken();
}

/**
 * Auto-load required classes (PSR-4 style)
 */
spl_autoload_register(function ($class) {
    if (strpos($class, 'ProCalculator\\') === 0) {
        $class = str_replace('ProCalculator\\', '', $class);
        $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

/**
 * Theme initialization hook
 */
function procalculator_theme_init()
{
    // Generate CSS variables
    ProCalculatorHelper::generateCSSVariables();
    
    // Log initialization
    ProCalculatorHelper::logActivity('theme_initialized', [
        'version' => ProCalculatorHelper::getThemeVersion(),
        'features' => ProCalculatorThemeConfig::get('features', [])
    ]);
}

// Initialize theme on every request
procalculator_theme_init();

/**
 * Premium Theme Helper Functions
 * These are simple wrapper functions for easier usage
 */

/**
 * Get theme configuration
 */
function procalc_config($key = null, $default = null)
{
    return ProCalculatorThemeConfig::get($key, $default);
}

/**
 * Get asset URL
 */
function procalc_asset($path)
{
    return ProCalculatorAssets::getAssetUrl($path);
}

/**
 * Check if user is logged in
 */
function procalc_is_logged_in()
{
    return ProCalculatorAuth::isLoggedIn();
}

/**
 * Get current user
 */
function procalc_current_user()
{
    return ProCalculatorAuth::getCurrentUser();
}

/**
 * Check if user has premium access
 */
function procalc_has_premium()
{
    return ProCalculatorAuth::hasPremiumAccess();
}

/**
 * Generate CSRF token
 */
function procalc_csrf_token()
{
    return ProCalculatorAuth::generateCSRFToken();
}

/**
 * Get calculator categories
 */
function procalc_categories()
{
    return ProCalculatorCalculators::getCategories();
}

/**
 * Check username availability
 */
function procalc_check_username($username)
{
    return ProCalculatorAuth::checkUsernameAvailability($username);
}
