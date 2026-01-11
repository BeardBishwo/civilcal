<?php
namespace App\Router;

use App\Core\Database;
use App\Services\SettingsService;

/**
 * Calculator Router
 * Handles URL routing for calculators with configurable permalink structures
 */
class CalculatorRouter
{
    private $db;
    private $permalinkStructure;
    private $settingsService;
    private $permalinkService;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
        $this->settingsService = new SettingsService();
        $this->permalinkService = new \App\Services\PermalinkService();
        $this->loadPermalinkStructure();
    }
    
    /**
     * Load permalink structure from settings
     */
    private function loadPermalinkStructure()
    {
        $this->permalinkStructure = $this->settingsService->get('permalink_structure', 'calculator-only');
    }
    
    /**
     * Get permalink settings for enhanced routing
     */
    private function getPermalinkSettings()
    {
        return [
            'structure' => $this->permalinkStructure,
            'base_path' => $this->settingsService->get('permalink_base_path', 'tools'),
            'php_extension' => $this->settingsService->get('permalink_php_extension', false),
            'custom_pattern' => $this->settingsService->get('permalink_custom_pattern', ''),
            'redirect_old_urls' => $this->settingsService->get('permalink_redirect_old_urls', true)
        ];
    }
    
    /**
     * Handle incoming request with enhanced permalink support
     */
    public function handleRequest($route)
    {
        // Clean the route
        $route = trim($route, '/');
        
        try {
            $this->db->query("SELECT slug FROM calculator_urls LIMIT 1");
        } catch (\Exception $e) {
             throw new \Exception("DB Error in Router: " . $e->getMessage() . " - DB: " . $this->db->query('SELECT DATABASE()')->fetchColumn());
        }
        
        // Check for 301 redirects first
        $redirectUrl = $this->checkRedirects($route);
        if ($redirectUrl) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        // Try to find calculator using enhanced routing
        $calculator = $this->findCalculatorByEnhancedRoute($route);
        
        if ($calculator) {
            $this->executeCalculator($calculator);
            return true;
        }
        
        // Check if it's an old-style URL (backward compatibility)
        if ($this->isLegacyUrl($route)) {
            $this->handleLegacyUrl($route);
            return true;
        }
        
        return false;
    }
    
    /**
     * Check for 301 redirects
     */
    private function checkRedirects($route)
    {
        $settings = $this->getPermalinkSettings();
        if (!$settings['redirect_old_urls']) {
            return false;
        }
        
        return $this->permalinkService->getRedirect($route);
    }
    
    /**
     * Find calculator using enhanced routing logic
     */
    private function findCalculatorByEnhancedRoute($route)
    {
        $settings = $this->getPermalinkSettings();
        $structure = $settings['structure'];
        
        switch ($structure) {
            case 'php-extension':
                return $this->parsePhpExtension($route);
            case 'base-path':
                return $this->parseBasePath($route, $settings['base_path']);
            case 'custom':
                return $this->parseCustomPattern($route, $settings['custom_pattern']);
            case 'full-path':
                return $this->parseFullPath($route);
            case 'category-calculator':
                return $this->parseCategoryCalculator($route);
            case 'subcategory-calculator':
                return $this->parseSubcategoryCalculator($route);
            case 'calculator-only':
            default:
                return $this->parseCalculatorOnly($route);
        }
    }
    
    /**
     * Parse PHP extension structure: /calculator-name.php
     */
    private function parsePhpExtension($route)
    {
        // Remove .php extension if present
        $cleanRoute = rtrim($route, '.php');
        
        // Try to find by slug
        $calculator = $this->findCalculatorBySlug($cleanRoute);
        if ($calculator) {
            return $calculator;
        }
        
        // Try to find by calculator_id
        return $this->findCalculatorByCalculatorId($cleanRoute);
    }
    
    /**
     * Parse base path structure: /tools/calculator-name
     */
    private function parseBasePath($route, $basePath)
    {
        // Check if route starts with base path
        if (strpos($route, $basePath . '/') === 0) {
            $calculatorSlug = substr($route, strlen($basePath) + 1);
            return $this->findCalculatorBySlug($calculatorSlug);
        }
        
        return null;
    }
    
    /**
     * Parse custom pattern structure
     */
    private function parseCustomPattern($route, $pattern)
    {
        if (empty($pattern)) {
            return $this->parseCalculatorOnly($route);
        }
        
        // Simple pattern matching for common patterns
        // Pattern examples: /calc/{category}/{slug}, /tools/{slug}, etc.
        
        // For now, fallback to calculator-only parsing
        // This can be enhanced with more sophisticated pattern matching
        return $this->findCalculatorBySlug($route);
    }
    
    /**
     * Parse full path structure: /modules/category/subcategory/calculator
     */
    private function parseFullPath($route)
    {
        // Remove .php extension if present (backward compatibility)
        $route = rtrim($route, '.php');
        
        // Extract calculator ID from full path (with or without .php)
        // Modern: /category/subcategory/calculator-id
        // Legacy Support: /modules/category/subcategory/calculator-id
        if (preg_match('/^(?:modules\/)?([^\/]+)\/([^\/]+)\/([^\/]+)$/', $route, $matches)) {
            $category = $matches[1];
            $subcategory = $matches[2];
            $calculatorId = $matches[3];
            
            return $this->findCalculatorByPath($category, $subcategory, $calculatorId);
        }
        
        return null;
    }
    
    /**
     * Parse category-calculator structure: /category/calculator-name
     */
    private function parseCategoryCalculator($route)
    {
        // Remove .php extension if present (backward compatibility)
        $route = preg_replace('/\.php$/', '', $route);
        
        if (preg_match('/^([^\/]+)\/([^\/]+)$/', $route, $matches)) {
            $category = $matches[1];
            $calculatorSlug = $matches[2];
            
            return $this->findCalculatorByCategoryAndSlug($category, $calculatorSlug);
        }
        
        return null;
    }
    
    /**
     * Parse subcategory-calculator structure: /subcategory/calculator-name
     */
    private function parseSubcategoryCalculator($route)
    {
        // Remove .php extension if present (backward compatibility)
        $route = preg_replace('/\.php$/', '', $route);
        
        if (preg_match('/^([^\/]+)\/([^\/]+)$/', $route, $matches)) {
            $subcategory = $matches[1];
            $calculatorSlug = $matches[2];
            
            return $this->findCalculatorBySubcategoryAndSlug($subcategory, $calculatorSlug);
        }
        
        return null;
    }
    
    /**
     * Parse calculator-only structure: /calculator-name
     */
    private function parseCalculatorOnly($route)
    {
        // Remove .php extension if present (backward compatibility)
        $route = preg_replace('/\.php$/', '', $route);
        
        // Try to find by slug first
        $calculator = $this->findCalculatorBySlug($route);
        if ($calculator) {
            return $calculator;
        }
        
        // Try to find by calculator_id
        return $this->findCalculatorByCalculatorId($route);
    }
    
    /**
     * Find calculator by category and slug
     */
    private function findCalculatorByCategoryAndSlug($category, $slug)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM calculator_urls
            WHERE category = ? AND `slug` = ?
            LIMIT 1
        ");
        $stmt->execute([$category, $slug]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Find calculator by subcategory and slug
     */
    private function findCalculatorBySubcategoryAndSlug($subcategory, $slug)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM calculator_urls
            WHERE subcategory = ? AND `slug` = ?
            LIMIT 1
        ");
        $stmt->execute([$subcategory, $slug]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Find calculator by category, subcategory, and calculator_id
     */
    private function findCalculatorByPath($category, $subcategory, $calculatorId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM calculator_urls
            WHERE category = ? AND subcategory = ? AND calculator_id = ?
            LIMIT 1
        ");
        $stmt->execute([$category, $subcategory, $calculatorId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Find calculator by calculator_id (fallback method)
     */
    private function findCalculatorByCalculatorId($calculatorId)
    {
        $stmt = $this->db->prepare("SELECT * FROM calculator_urls WHERE calculator_id = ? LIMIT 1");
        $stmt->execute([$calculatorId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Find calculator by slug
     */
    /**
     * Find calculator by slug
     * Now includes fallback to CalculatorEngine for config-only tools
     */
    private function findCalculatorBySlug($slug)
    {
        // 1. Check Database
        $stmt = $this->db->prepare("SELECT * FROM calculator_urls WHERE `slug` = ?");
        $stmt->execute([$slug]);
        $calculator = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($calculator) {
            return $calculator;
        }

        // 2. Check Engine (Virtual Discovery)
        try {
            $engine = new \App\Engine\CalculatorEngine();
            $metadata = $engine->getMetadata($slug);
            
            if ($metadata && ($metadata['success'] ?? false)) {
                // Construct virtual calculator object
                return [
                    'calculator_id' => $slug,
                    'slug' => $slug, // Assume slug matches ID for virtual tools
                    'name' => $metadata['name'],
                    'category' => $metadata['category'],
                    'subcategory' => $metadata['subcategory'] ?? 'general',
                    'full_path' => 'virtual', // Marker for virtual tools
                    'is_active' => 1
                ];
            }
        } catch (\Exception $e) {
            // Ignore engine errors during discovery
        }
        
        return null; // Not found
    }
    
    /**
     * Check if URL is legacy format
     */
    private function isLegacyUrl($route)
    {
        return (strpos($route, 'modules/') === 0 && strpos($route, '.php') !== false);
    }
    
    /**
     * Handle legacy URL with redirect
     */
    private function handleLegacyUrl($route)
    {
        // Extract calculator ID from legacy path
        $calculatorId = $this->extractCalculatorIdFromPath($route);
        
        if ($calculatorId) {
            $calculator = $this->findCalculatorById($calculatorId);
            if ($calculator) {
                // 301 redirect to new URL
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: /' . APP_BASE . '/' . $calculator['slug']);
                exit;
            }
        }
        
        // If we can't find it, try to execute the legacy file directly
        $filePath = dirname(__DIR__, 2) . '/' . $route;
        if (file_exists($filePath)) {
            require $filePath;
            exit;
        }
    }
    
    /**
     * Extract calculator ID from legacy path
     */
    private function extractCalculatorIdFromPath($path)
    {
        // Remove .php extension
        $path = str_replace('.php', '', $path);
        
        // Get the last part (calculator name)
        $parts = explode('/', $path);
        return end($parts);
    }
    
    /**
     * Find calculator by ID
     */
    private function findCalculatorById($calculatorId)
    {
        $stmt = $this->db->prepare("SELECT * FROM calculator_urls WHERE calculator_id = ?");
        $stmt->execute([$calculatorId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Execute calculator
     */
    /**
     * Execute calculator
     */
    private function executeCalculator($calculator)
    {
        // 1. Enterprise Engine / Virtual Execution
        // We no longer rely on physical files in modules/ directory as they are deprecated/deleted.
        
        try {
            $engine = new \App\Engine\CalculatorEngine();
            // Try to load metadata (Inputs/Formulas) from DB or Config
            $metadata = $engine->getMetadata($calculator['calculator_id']);
             
            // Set calculator context globals for the template
            $_SERVER['CALCULATOR_ID'] = $calculator['calculator_id'];
            $_SERVER['CALCULATOR_CATEGORY'] = $calculator['category'];
            $_SERVER['CALCULATOR_SUBCATEGORY'] = $calculator['subcategory'] ?? 'general';

            // If metadata exists, we can render the Generic Template
            if ($metadata && ($metadata['success'] ?? false)) {
                $templatePath = dirname(__DIR__, 2) . '/themes/default/views/shared/calculator-template.php';
                
                if (file_exists($templatePath)) {
                    require_once $templatePath;
                    if (function_exists('renderCalculator')) {
                        renderCalculator($calculator['calculator_id']);
                        exit;
                    }
                } else {
                     throw new \Exception("Generic Calculator Template not found.");
                }
            }
            
            // 2. Fallback: Check if it's a new Class-Based Calculator (EnterprisePipeline)
            // If DB metadata is missing, maybe we can generate UI from the Class? (Future Feature)
            // For now, if no metadata, we can't show a form.

        } catch (\Exception $e) {
            error_log("Router Error: " . $e->getMessage());
        }

        // 3. Not Found / Error
        http_response_code(404);
        echo "<h1>Calculator Not Found</h1>";
        echo "<p>The calculator '{$calculator['calculator_id']}' is currently unavailable or being migrated.</p>";
        if (isset($e)) echo "<p>Error: " . $e->getMessage() . "</p>";
        exit;
    }
    
    /**
     * Generate URL for a calculator
     */
    public function generateUrl($calculatorId, $category = null, $subcategory = null)
    {
        switch ($this->permalinkStructure) {
            case 'full-path':
                // Modern structure: /category/subcategory/calculator-id
                return "/{$category}/{$subcategory}/{$calculatorId}";
                
            case 'category-calculator':
                return "/{$category}/{$calculatorId}";
                
            case 'subcategory-calculator':
                return "/{$subcategory}/{$calculatorId}";
                
            case 'calculator-only':
            default:
                return "/{$calculatorId}";
        }
    }
}
