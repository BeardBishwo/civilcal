<?php
/**
 * Theme Manager Service
 * Handles theme management, asset loading, and rendering
 * PHP 7.4 Compatible Version
 */

namespace App\Services;

class ThemeManager
{
    private $currentTheme;
    private $themesPath;
    private $activeTheme;
    private $baseUrl;
    private $assetsCache = [];

    public function __construct()
    {
        $this->themesPath = BASE_PATH . '/themes/';
        $this->baseUrl = $this->getBaseUrl();
        $this->activeTheme = $this->getActiveThemeName();
        $this->loadThemeConfig();
    }

    /**
     * Get base URL
     */
    private function getBaseUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);
        
        if ($baseDir === '/') {
            $baseDir = '';
        }
        
        return $protocol . '://' . $host . $baseDir;
    }

    /**
     * Get active theme from session or default
     */
    private function getActiveThemeName()
    {
        return isset($_SESSION['active_theme']) ? $_SESSION['active_theme'] : 'default';
    }

    /**
     * Load theme configuration
     */
    private function loadThemeConfig()
    {
        $configFile = $this->themesPath . $this->activeTheme . '/theme.json';
        
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->currentTheme = $config;
            }
        }
        
        // Fallback to default configuration
        if (!$this->currentTheme) {
            $this->currentTheme = $this->getDefaultThemeConfig();
        }
    }

    /**
     * Get default theme configuration
     */
    private function getDefaultThemeConfig()
    {
        return [
            'name' => 'Default Theme',
            'version' => '1.0.0',
            'description' => 'Default theme for Bishwo Calculator',
            'author' => 'Bishwo Calculator',
            'styles' => [
                'css/theme.css',
                'css/header.css',
                'css/footer.css',
                'css/home.css',
                'css/back-to-top.css'
            ],
            'scripts' => [
                'js/main.js',
                'js/header.js',
                'js/home.js',
                'js/back-to-top.js'
            ],
            'category_styles' => [
                'civil' => 'css/civil.css',
                'electrical' => 'css/electrical.css',
                'plumbing' => 'css/plumbing.css',
                'hvac' => 'css/hvac.css',
                'fire' => 'css/fire.css',
                'structural' => 'css/structural.css',
                'site' => 'css/site.css',
                'estimation' => 'css/estimation.css',
                'management' => 'css/management.css',
                'mep' => 'css/mep.css'
            ]
        ];
    }

    /**
     * Get theme asset URL
     */
    public function getThemeAsset($assetPath)
    {
        return $this->baseUrl . '/themes/' . $this->activeTheme . '/assets/' . ltrim($assetPath, '/');
    }

    /**
     * Load theme styles
     */
    public function loadThemeStyles()
    {
        if (isset($this->currentTheme['styles'])) {
            foreach ($this->currentTheme['styles'] as $style) {
                echo '<link rel="stylesheet" href="' . htmlspecialchars($this->getThemeAsset($style)) . '">' . PHP_EOL;
            }
        }
    }

    /**
     * Load theme scripts
     */
    public function loadThemeScripts()
    {
        if (isset($this->currentTheme['scripts'])) {
            foreach ($this->currentTheme['scripts'] as $script) {
                echo '<script src="' . htmlspecialchars($this->getThemeAsset($script)) . '"></script>' . PHP_EOL;
            }
        }
    }

    /**
     * Get category-specific style
     */
    public function getCategoryStyle($category)
    {
        if (isset($this->currentTheme['category_styles'][$category])) {
            return $this->currentTheme['category_styles'][$category];
        }
        return null;
    }

    /**
     * Load category-specific style
     */
    public function loadCategoryStyle($category)
    {
        $style = $this->getCategoryStyle($category);
        if ($style) {
            echo '<link rel="stylesheet" href="' . htmlspecialchars($this->getThemeAsset($style)) . '">' . PHP_EOL;
        }
    }

    /**
     * Load category-specific scripts
     */
    public function loadCategoryScripts($category)
    {
        if (isset($this->currentTheme['category_scripts'][$category])) {
            $script = $this->currentTheme['category_scripts'][$category];
            echo '<script src="' . htmlspecialchars($this->getThemeAsset($script)) . '"></script>' . PHP_EOL;
        }
    }

    /**
     * Set active theme
     */
    public function setTheme($themeName)
    {
        $themePath = $this->themesPath . $themeName;
        if (is_dir($themePath)) {
            $this->activeTheme = $themeName;
            $_SESSION['active_theme'] = $themeName;
            $this->loadThemeConfig();
            return true;
        }
        return false;
    }

    /**
     * Get active theme name
     */
    public function getActiveTheme()
    {
        return $this->activeTheme;
    }

    /**
     * Get current theme configuration
     */
    public function getThemeConfig()
    {
        return $this->currentTheme;
    }

    /**
     * Render theme partial
     */
    public function renderPartial($partial, $data = [])
    {
        $partialPath = $this->themesPath . $this->activeTheme . '/views/partials/' . $partial . '.php';
        
        if (file_exists($partialPath)) {
            ob_start();
            extract($data);
            include $partialPath;
            return ob_get_clean();
        }
        
        return "<!-- Partial not found: {$partial} -->" . PHP_EOL;
    }

    /**
     * Render theme view
     */
    public function renderView($view, $data = [])
    {
        $viewPath = $this->themesPath . $this->activeTheme . '/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            ob_start();
            extract($data);
            include $viewPath;
            return ob_get_clean();
        }
        
        return "<!-- View not found: {$view} -->" . PHP_EOL;
    }

    /**
     * Get theme URL for navigation
     */
    public function themeUrl($path = '')
    {
        return $this->baseUrl . '/themes/' . $this->activeTheme . '/' . ltrim($path, '/');
    }

    /**
     * Get theme assets URL
     */
    public function assetsUrl($path = '')
    {
        return $this->baseUrl . '/themes/' . $this->activeTheme . '/assets/' . ltrim($path, '/');
    }

    /**
     * Get available themes
     */
    public function getAvailableThemes()
    {
        $themes = [];
        $directories = glob($this->themesPath . '*', GLOB_ONLYDIR);
        
        foreach ($directories as $dir) {
            $themeName = basename($dir);
            $configFile = $dir . '/theme.json';
            
            if (file_exists($configFile)) {
                $config = json_decode(file_get_contents($configFile), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $themes[$themeName] = $config;
                }
            } else {
                $themes[$themeName] = [
                    'name' => ucfirst($themeName) . ' Theme',
                    'version' => '1.0.0',
                    'description' => 'Theme: ' . $themeName
                ];
            }
        }
        
        return $themes;
    }

    /**
     * Get theme metadata
     */
    public function getThemeMetadata()
    {
        return [
            'name' => $this->currentTheme['name'] ?? 'Default Theme',
            'version' => $this->currentTheme['version'] ?? '1.0.0',
            'author' => $this->currentTheme['author'] ?? 'Bishwo Calculator',
            'description' => $this->currentTheme['description'] ?? '',
            'active_theme' => $this->activeTheme
        ];
    }

    /**
     * Check if theme has custom category style
     */
    public function hasCategoryStyle($category)
    {
        return isset($this->currentTheme['category_styles'][$category]);
    }

    /**
     * Get all category styles
     */
    public function getAllCategoryStyles()
    {
        return $this->currentTheme['category_styles'] ?? [];
    }

    /**
     * Check if theme supports a feature
     */
    public function supportsFeature($feature)
    {
        return isset($this->currentTheme['features']) && in_array($feature, $this->currentTheme['features']);
    }

    /**
     * Get category-specific classes
     */
    public function getCategoryClasses($category)
    {
        $classes = 'category-' . htmlspecialchars($category);
        if (isset($this->currentTheme['category_classes'][$category])) {
            $classes .= ' ' . htmlspecialchars($this->currentTheme['category_classes'][$category]);
        }
        return $classes;
    }

    /* Static helpers for backward compatibility when called statically */
    public static function getAllThemes()
    {
        $tm = new self();
        return $tm->getAvailableThemes();
    }

    public static function installTheme($themeName)
    {
        // Basic placeholder: attempt to create theme directory
        $tm = new self();
        $themePath = $tm->themesPath . $themeName;
        if (!is_dir($themePath)) {
            return mkdir($themePath, 0755, true);
        }
        return false;
    }

    public static function activateTheme($themeName)
    {
        $tm = new self();
        return $tm->setTheme($themeName);
    }

    public static function deleteTheme($themeName)
    {
        $tm = new self();
        $themePath = $tm->themesPath . $themeName;
        if (is_dir($themePath)) {
            // basic recursive delete
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($themePath, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($it as $file) {
                if ($file->isDir()) rmdir($file->getRealPath()); else unlink($file->getRealPath());
            }
            return rmdir($themePath);
        }
        return false;
    }
}
?>
