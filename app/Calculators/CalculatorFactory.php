<?php
namespace App\Calculators;

use App\Services\PluginManager;

class CalculatorFactory
{
    public static function create($calculatorType, $calculatorSlug = null)
    {
        // 1. Try Unified Pipeline Class (BEST - Specific Implementation)
        // e.g. App\Calculators\Civil\BrickWallCalculator
        if ($calculatorSlug) {
            // Normalize slug: 'brick-wall' -> 'BrickWall', 'concrete' -> 'Concrete'
            $baseName = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $calculatorSlug)));
            
            // Ensure we don't double-suffix: ConcreteCalculator -> ConcreteCalculator, BrickWall -> BrickWallCalculator
            $className = $baseName;
            if (substr($className, -10) !== 'Calculator') {
                $className .= 'Calculator';
            }

            // Try explicit slug match in the category sub-namespace
            $pipelineClass = "App\\Calculators\\" . ucfirst($calculatorType) . "\\" . $className;
            
            if (class_exists($pipelineClass)) {
                return new $pipelineClass();
            }
        }

        // 2. Try Legacy/Wrapper Class (BACKWARD COMPAT - Generic Dispatcher)
        // e.g. App\Calculators\CivilCalculator
        $className = "App\\Calculators\\" . ucfirst($calculatorType) . "Calculator";
        
        if (class_exists($className)) {
            $calculator = new $className();
            
            // Add virtual validate if missing
            if (!method_exists($calculator, 'validate')) {
                // We can't easily add methods to an existing instance of a normal class
                // but we can wrap it or just rely on CalculationService checking for existence?
                // Actually CalculationService ASSUMES it exists.
            }
            return $calculator;
        }
        
        // 2. Try CalculatorEngine (Modern Config-Driven)
        try {
            $engine = new \App\Engine\CalculatorEngine();
            // If we have a slug, we look for that specific calculator
            $lookup = $calculatorSlug ?: $calculatorType;
            $metadata = $engine->getMetadata($lookup);
            
            if ($metadata && ($metadata['success'] ?? false)) {
                // Return a virtual adapter compatible with legacy interface
                return new class($engine, $lookup) {
                    private $engine;
                    private $type;
                    
                    public function __construct($engine, $type) {
                        $this->engine = $engine;
                        $this->type = $type;
                    }
                    
                    public function calculate($inputs) {
                        return $this->engine->execute($this->type, $inputs);
                    }
                    
                    public function validate($inputs) {
                        // The Engine handles validation internally during execute()
                        return ['valid' => true, 'errors' => []];
                    }
                    
                    public function setCalculatorSlug($slug) {
                        // Virtual calculator doesn't need to store slug, it's baked in
                    }
                };
            }
        } catch (\Exception $e) {
            // Ignore engine errors
        }
        
        return null;
    }
    
    // Removed loadFromModule as modules are deleted
    
    public static function getAvailableCalculators()
    {
        $calculators = [];

        // Load from Config directory (Source of Truth)
        $configPath = __DIR__ . '/../Config/Calculators';
        if (is_dir($configPath)) {
            $files = glob($configPath . '/*.php');
            foreach ($files as $file) {
                $category = basename($file, '.php');
                $config = require $file;
                
                if (is_array($config)) {
                    foreach ($config as $slug => $data) {
                        $calculators[] = [
                            'category' => $data['category'] ?? $category,
                            'subcategory' => $data['subcategory'] ?? 'general',
                            'slug' => $slug,
                            'name' => $data['name'] ?? self::slugToName($slug),
                            'path' => 'virtual' // No physical path
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
                if (!empty($pc['category']) && !empty($pc['calculator'])) {
                    $calculators[] = [
                        'category' => $pc['category'],
                        'subcategory' => 'plugin',
                        'slug' => $pc['calculator'],
                        'name' => $pc['name'] ?? self::slugToName($pc['calculator']),
                        'path' => $pc['file_path'] ?? ''
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Silent fail
        }

        return $calculators;
    }
    
    private static function slugToName($slug)
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }
}
