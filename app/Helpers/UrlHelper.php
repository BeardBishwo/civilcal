<?php
namespace App\Helpers;

use App\Core\Database;

/**
 * URL Helper
 * Generates URLs for calculators based on permalink structure
 */
class UrlHelper
{
    private static $permalinkStructure = null;
    private static $urlCache = [];
    
    /**
     * Get permalink structure from settings
     */
    private static function getPermalinkStructure()
    {
        if (self::$permalinkStructure === null) {
            $db = Database::getInstance()->getPdo();
            $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'permalink_structure'");
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            self::$permalinkStructure = $result['setting_value'] ?? 'calculator-only';
        }
        return self::$permalinkStructure;
    }
    
    /**
     * Generate calculator URL
     * 
     * @param string $calculatorId Calculator identifier
     * @return string Generated URL
     */
    public static function calculator($calculatorId)
    {
        // Check cache first
        if (isset(self::$urlCache[$calculatorId])) {
            return self::$urlCache[$calculatorId];
        }
        
        // Get calculator data from database
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT slug, category, subcategory FROM calculator_urls WHERE calculator_id = ?");
        $stmt->execute([$calculatorId]);
        $calculator = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$calculator) {
            // Fallback to legacy URL if not found
            return "/modules/calculator/{$calculatorId}.php";
        }
        
        $structure = self::getPermalinkStructure();
        $baseUrl = defined('APP_BASE') ? '/' . APP_BASE : '';
        
        switch ($structure) {
            case 'full-path':
                $url = "{$baseUrl}/modules/{$calculator['category']}/{$calculator['subcategory']}/{$calculatorId}.php";
                break;
            case 'category-calculator':
                $url = "{$baseUrl}/{$calculator['category']}/{$calculatorId}.php";
                break;
            case 'subcategory-calculator':
                $url = "{$baseUrl}/{$calculator['subcategory']}/{$calculatorId}.php";
                break;
            case 'calculator-only':
                $url = "{$baseUrl}/{$calculator['slug']}.php";
                break;
            case 'custom':
                // Get custom pattern from settings
                $pattern = \App\Services\SettingsService::get('permalink_custom_pattern', '{slug}');
                $pattern = str_replace('{category}', $calculator['category'], $pattern);
                $pattern = str_replace('{subcategory}', $calculator['subcategory'], $pattern);
                $pattern = str_replace('{slug}', $calculator['slug'], $pattern);
                // Ensure pattern starts with /
                if (substr($pattern, 0, 1) !== '/') {
                    $pattern = '/' . $pattern;
                }
                $url = "{$baseUrl}{$pattern}";
                break;
            default:
                $url = "{$baseUrl}/{$calculator['slug']}.php";
                break;
        }
        
        // Cache the result
        self::$urlCache[$calculatorId] = $url;
        
        return $url;
    }
    
    /**
     * Generate URL with custom structure
     */
    public static function customUrl($calculatorId, $structure)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT slug, category, subcategory FROM calculator_urls WHERE calculator_id = ?");
        $stmt->execute([$calculatorId]);
        $calculator = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$calculator) {
            return "#";
        }
        
        $baseUrl = defined('APP_BASE') ? '/' . APP_BASE : '';
        
        switch ($structure) {
            case 'full-path':
                return "{$baseUrl}/modules/{$calculator['category']}/{$calculator['subcategory']}/{$calculatorId}.php";
            case 'category-calculator':
                return "{$baseUrl}/{$calculator['category']}/{$calculatorId}.php";
            case 'subcategory-calculator':
                return "{$baseUrl}/{$calculator['subcategory']}/{$calculatorId}.php";
            case 'calculator-only':
                return "{$baseUrl}/{$calculator['slug']}.php";
            case 'custom':
                $pattern = \App\Services\SettingsService::get('permalink_custom_pattern', '{slug}');
                $pattern = str_replace('{category}', $calculator['category'], $pattern);
                $pattern = str_replace('{subcategory}', $calculator['subcategory'], $pattern);
                $pattern = str_replace('{slug}', $calculator['slug'], $pattern);
                if (substr($pattern, 0, 1) !== '/') {
                    $pattern = '/' . $pattern;
                }
                return "{$baseUrl}{$pattern}";
            default:
                return "{$baseUrl}/{$calculator['slug']}.php";
        }
    }
    
    /**
     * Clear URL cache
     */
    public static function clearCache()
    {
        self::$urlCache = [];
        self::$permalinkStructure = null;
    }
    
    /**
     * Get all available permalink structures
     */
    public static function getAvailableStructures()
    {
        return [
            'full-path' => [
                'label' => 'Full Path with Modules',
                'example' => '/modules/civil/concrete/concrete-volume.php',
                'description' => 'Complete path with modules, category, and subcategory'
            ],
            'category-calculator' => [
                'label' => 'Category + Calculator',
                'example' => '/civil/concrete-volume.php',
                'description' => 'Category followed by calculator name'
            ],
            'subcategory-calculator' => [
                'label' => 'Subcategory + Calculator',
                'example' => '/concrete/concrete-volume.php',
                'description' => 'Subcategory followed by calculator name'
            ],
            'calculator-only' => [
                'label' => 'Calculator Only (Shortest)',
                'example' => '/concrete-volume.php',
                'description' => 'Just the calculator name (recommended for SEO)'
            ],
            'custom' => [
                'label' => 'Custom Pattern',
                'example' => '{category}/{slug}',
                'description' => 'Define your own URL pattern using placeholders: {category}, {subcategory}, {slug}'
            ]
        ];
    }
}

