<?php
namespace App\Calculators;

use App\Services\PluginManager;

class CalculatorFactory
{
    public static function create($calculatorType, $calculatorSlug = null)
    {
        $calculatorClass = "App\\Calculators\\" . ucfirst($calculatorType) . "Calculator";
        
        if (class_exists($calculatorClass)) {
            return new $calculatorClass();
        }
        
        // Try to load from modules
        if ($calculatorSlug) {
            $moduleCalculator = self::loadFromModule($calculatorType, $calculatorSlug);
            if ($moduleCalculator) {
                return $moduleCalculator;
            }
        }
        
        // Fallback to base calculator
        return new BaseCalculator();
    }
    
    private static function loadFromModule($category, $calculatorSlug)
    {
        $modulePath = __DIR__ . "/../../modules/{$category}/{$calculatorSlug}.php";
        
        if (file_exists($modulePath)) {
            require_once $modulePath;
            
            $className = ucfirst($calculatorSlug) . 'Calculator';
            if (class_exists($className)) {
                return new $className();
            }
        }
        
        return null;
    }
    
    public static function getAvailableCalculators()
    {
        $calculators = [];

        // Load from modules directory (core calculators)
        $modulesPath = __DIR__ . '/../../modules';
        if (is_dir($modulesPath)) {
            $categories = scandir($modulesPath);
            foreach ($categories as $category) {
                if ($category === '.' || $category === '..') continue;
                $categoryPath = $modulesPath . '/' . $category;
                if (is_dir($categoryPath)) {
                    $calculatorFiles = glob($categoryPath . '/*/*.php');
                    foreach ($calculatorFiles as $file) {
                        $slug = pathinfo($file, PATHINFO_FILENAME);
                        $subcategory = basename(dirname($file));
                        $calculators[] = [
                            'category' => $category,
                            'subcategory' => $subcategory,
                            'slug' => $slug,
                            'name' => self::slugToName($slug),
                            'path' => $file
                        ];
                    }
                }
            }
        }

        // Include active plugin calculators (if any)
        try {
            $pm = new PluginManager();
            $pluginCalcs = $pm->getActiveCalculators();
            foreach ($pluginCalcs as $pc) {
                // Expect keys: category, calculator (slug), file_path, name
                if (!empty($pc['category']) && !empty($pc['calculator']) && !empty($pc['file_path'])) {
                    $calculators[] = [
                        'category' => $pc['category'],
                        'subcategory' => 'plugin',
                        'slug' => $pc['calculator'],
                        'name' => $pc['name'] ?? self::slugToName($pc['calculator']),
                        'path' => $pc['file_path']
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Silent fail to avoid breaking API listing if plugins table not ready
        }

        return $calculators;
    }
    
    private static function slugToName($slug)
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }
}
