<?php
namespace App\Core;

class View {
    private $themeManager;
    private $basePath;
    
    public function __construct() {
        $this->themeManager = new \App\Services\ThemeManager();
        $this->basePath = $this->getBasePath();
    }
    
    /**
     * Get base path for URLs (handles subdirectory installations)
     */
    private function getBasePath() {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDir = dirname($scriptName);
        
        // Remove /public from path
        if (substr($scriptDir, -7) === '/public') {
            $scriptDir = substr($scriptDir, 0, -7);
        }
        
        if ($scriptDir === '/' || $scriptDir === '') {
            return '';
        }
        
        return $scriptDir;
    }
    
    /**
     * Generate URL with base path
     */
    public function url($path = '') {
        $path = ltrim($path, '/');
        return $this->basePath . '/' . $path;
    }
    
    public function render($view, $data = []) {
        // Extract data for the view
        extract($data);
        
        // Set default title
        $title = isset($title) ? $title : 'Bishwo Calculator';
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = $this->themesPath() . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Try theme view path
            $this->themeManager->renderView($view, $data);
        }
        
        // Get the content and clean buffer
        $content = ob_get_clean();
        
        // Apply theme layout
        $layoutPath = $this->themesPath() . 'layouts/main.php';
        
        if (file_exists($layoutPath)) {
            // Pass content to layout
            $data['content'] = $content;
            extract($data);
            
            ob_start();
            include $layoutPath;
            $finalOutput = ob_get_clean();
            
            echo $finalOutput;
        } else {
            // No layout, just output content
            echo $content;
        }
    }
    
    /**
     * Get themed asset URL
     */
    public function asset($assetPath) {
        return $this->themeManager->getThemeAsset($assetPath);
    }
    
    /**
     * Get theme assets URL
     */
    public function assetsUrl($path = '') {
        return $this->themeManager->assetsUrl($path);
    }
    
    /**
     * Get theme URL
     */
    public function themeUrl($path = '') {
        return $this->themeManager->themeUrl($path);
    }
    
    /**
     * Render a partial view
     */
    public function partial($partial, $data = []) {
        $this->themeManager->renderPartial($partial, $data);
    }
    
    /**
     * Load theme styles
     */
    public function loadStyles() {
        $this->themeManager->loadThemeStyles();
    }
    
    /**
     * Load theme scripts
     */
    public function loadScripts() {
        $this->themeManager->loadThemeScripts();
    }
    
    /**
     * Load category specific style
     */
    public function loadCategoryStyle($category) {
        $this->themeManager->loadCategoryStyle($category);
    }
    
    /**
     * Get theme metadata
     */
    public function getThemeMetadata() {
        return $this->themeManager->getThemeMetadata();
    }
    
    /**
     * Get current theme config
     */
    public function getThemeConfig() {
        return $this->themeManager->getThemeConfig();
    }
    
    /**
     * Get active theme name
     */
    public function getActiveTheme() {
        return $this->themeManager->getActiveTheme();
    }
    
    /**
     * Get available themes
     */
    public function getAvailableThemes() {
        return $this->themeManager->getAvailableThemes();
    }
    
    /**
     * Set active theme
     */
    public function setTheme($themeName) {
        return $this->themeManager->setTheme($themeName);
    }
    
    /**
     * Get themes path
     */
    private function themesPath() {
        return BASE_PATH . '/themes/' . $this->themeManager->getActiveTheme() . '/views/';
    }
    
    /**
     * Render JSON response
     */
    public function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Render plain text
     */
    public function plain($text, $status = 200) {
        http_response_code($status);
        header('Content-Type: text/plain');
        echo $text;
        exit;
    }
}
?>
