<?php

namespace App\Engine;

use App\Engine\ValidationEngine;
use App\Engine\UnitConverter;
use App\Engine\FormulaRegistry;
use App\Engine\ResultFormatter;

/**
 * Calculator Engine - Core orchestrator for all calculations
 * 
 * This class coordinates the entire calculation workflow:
 * 1. Load calculator configuration
 * 2. Validate inputs
 * 3. Convert units if needed
 * 4. Execute formulas
 * 5. Format results
 * 
 * @package App\Engine
 */
class CalculatorEngine
{
    private ValidationEngine $validator;
    private UnitConverter $unitConverter;
    private FormulaRegistry $formulaRegistry;
    private ResultFormatter $formatter;
    private \App\Services\Calculator\CostService $costService;
    private array $config = [];
    
    public function __construct()
    {
        $this->validator = new ValidationEngine();
        $this->unitConverter = new UnitConverter();
        $this->formulaRegistry = new FormulaRegistry();
        $this->formatter = new ResultFormatter();
        $this->costService = new \App\Services\Calculator\CostService();
    }
    
    /**
     * Execute a calculation
     * 
     * @param string $calculatorId The calculator identifier (e.g., 'concrete-volume')
     * @param array $inputs User input data
     * @param array $options Additional options (units, precision, etc.)
     * @return array Calculation result
     */
    public function execute(string $calculatorId, array $inputs, array $options = []): array
    {
        $startTime = microtime(true);
        
        try {
            // Load calculator configuration
            $config = $this->loadConfiguration($calculatorId);
            
            // Validate inputs
            $validation = $this->validator->validate($inputs, $config['inputs']);
            if (!$validation['valid']) {
                return $this->formatError('Validation failed', $validation['errors']);
            }
            
            // Convert units if needed
            $normalizedInputs = $this->unitConverter->normalize($inputs, $config['inputs']);
            
            // PRIORITY: Check if a specific PIPELINE CLASS exists for this ID
            // e.g. 'brick-wall' -> App\Calculators\Civil\BrickWallCalculator
            $category = $config['category'] ?? 'Civil';
            $lookupId = str_replace('-calculator', '', $calculatorId);
            $classSlug = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $lookupId)));
            $pipelineClass = "App\\Calculators\\" . ucfirst($category) . "\\" . $classSlug . "Calculator";

            if (class_exists($pipelineClass)) {
                $instance = new $pipelineClass();
                if (method_exists($instance, 'calculate')) {
                    $classResult = $instance->calculate($inputs);
                    
                    // Unified Mapping: Class Results -> Engine Results
                    $results = $classResult['geometry'] ?? [];
                    if (isset($classResult['materials'])) {
                         // Enrich materials with costs via CostService
                         $results['bill_of_materials'] = $this->costService->calculateCost($classResult['materials']);
                    }
                    if (isset($classResult['related_items'])) {
                        $results['related_items'] = $classResult['related_items'];
                    }
                }
            } else {
                // FALLBACK: Execute virtual formulas
                $results = $this->formulaRegistry->execute($config['formulas'], $normalizedInputs);

                // PROCESS VIRTUAL COMPONENTS (BOM Generation)
                $context = array_merge($normalizedInputs, $results);
                $components = $this->processComponents($config, $context);
                if (!empty($components)) {
                    // Enrich BOM with Costs
                    $enrichedBOM = $this->costService->calculateCost($components);
                    $results['bill_of_materials'] = $enrichedBOM;
                }
            }
            
            // Format results
            $formattedResults = $this->formatter->format($results, $config['outputs']);

            // Re-attach BOM if it exists (ResultFormatter strips unknown keys)
            if (isset($results['bill_of_materials'])) {
                $formattedResults['bill_of_materials'] = $results['bill_of_materials'];
            }

            // Re-attach Related Items (Suggestions)
            if (isset($results['related_items'])) {
                $formattedResults['related_items'] = $results['related_items'];
            }
            
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return [
                'success' => true,
                'calculator' => $calculatorId,
                'inputs' => $normalizedInputs,
                'results' => $formattedResults,
                'metadata' => [
                    'execution_time' => $executionTime . 'ms',
                    'formula_version' => $config['version'] ?? '1.0',
                    'units' => $options['unit_system'] ?? 'metric'
                ]
            ];
            
        } catch (\Exception $e) {
            return $this->formatError($e->getMessage());
        }
    }
    
    /**
     * Load calculator configuration
     * Checks database first, falls back to config files
     */
    private function loadConfiguration(string $calculatorId): array
    {
        // Try loading from database (admin-configured calculators)
        try {
            $managementService = new \App\Services\CalculatorManagement();
            $dbConfig = $managementService->generateConfigArray($calculatorId);
            if ($dbConfig) {
                return $dbConfig;
            }
        } catch (\Exception $e) {
            // Database not available or calculator not found, try file-based config
        }
        
        // Fallback to file-based configuration
        $configFile = $this->findConfigFile($calculatorId);
        
        if (!$configFile) {
            throw new \Exception("Calculator configuration not found: {$calculatorId}");
        }
        
        $config = require $configFile;
        
        if (!isset($config[$calculatorId])) {
            throw new \Exception("Calculator not defined in config: {$calculatorId}");
        }
        
        return $config[$calculatorId];
    }
    
    /**
     * Find configuration file for calculator
     */
    private function findConfigFile(string $calculatorId): ?string
    {
        $configDir = __DIR__ . '/../Config/Calculators/';
        
        // Dynamic scan of all calculator config files
        // This avoids hardcoding categories and missing new ones
        $files = glob($configDir . '*.php');
        
        foreach ($files as $file) {
            // We use standard PHP require to inspect the array keys without loading everything into memory permanently?
            // require will load it. But it's cached by OPCode usually.
            // This is acceptable for the scale.
            
            // Optimization: Maybe check filename? No, calculatorId doesn't always match category.
            
            $config = require $file;
            if (isset($config[$calculatorId])) {
                return $file;
            }
        }
        
        return null;
    }
    
    /**
     * Get calculator metadata
     */
    public function getMetadata(string $calculatorId): array
    {
        try {
            $config = $this->loadConfiguration($calculatorId);
            return [
                'success' => true,
                'id' => $calculatorId,
                'name' => $config['name'] ?? '',
                'description' => $config['description'] ?? '',
                'category' => $config['category'] ?? '',
                'inputs' => $config['inputs'] ?? [],
                'outputs' => $config['outputs'] ?? []
            ];
        } catch (\Exception $e) {
            return $this->formatError($e->getMessage());
        }
    }
    
    /**
     * List all available calculators
     */
    public function listCalculators(): array
    {
        $calculators = [];
        $configDir = __DIR__ . '/../Config/Calculators/';
        
        if (!is_dir($configDir)) {
            return [];
        }
        
        $files = glob($configDir . '*.php');
        
        foreach ($files as $file) {
            $category = basename($file, '.php');
            $config = require $file;
            
            foreach ($config as $id => $calc) {
                $calculators[] = [
                    'id' => $id,
                    'name' => $calc['name'] ?? '',
                    'category' => $category,
                    'description' => $calc['description'] ?? ''
                ];
            }
        }
        
        return $calculators;
    }
    
    /**
     * Process Virtual Components (BOM)
     * 
     * @param array $config Calculator Configuration
     * @param array $results Calculation Results (for variable resolution)
     * @return array Bill of Materials
     */
    private function processComponents(array $config, array $results): array
    {
        if (!isset($config['components']) || !is_array($config['components'])) {
            return [];
        }

        $bom = [];

        foreach ($config['components'] as $component) {
            try {
                // 1. Resolve Item ID (e.g., "wire_copper_{wire_size}")
                $itemId = $component['item_id'];
                foreach ($results as $key => $value) {
                    if (is_scalar($value)) {
                         $itemId = str_replace("{{$key}}", $value, $itemId);
                    }
                }

                // 2. Calculate Quantity
                // We use the FormulaRegistry for safety and consistency
                // The formula might be "length * 1.05"
                $quantity = 0;
                if (isset($component['quantity_formula'])) {
                    // We treat the quantity formula as a mini-calculation
                    // Input: The results of the main calculation
                    $qtyResults = $this->formulaRegistry->execute(
                        ['qty' => $component['quantity_formula']], 
                        $results
                    );
                    $quantity = $qtyResults['qty'] ?? 0;
                }

                $bom[] = [
                    'type' => $component['type'] ?? 'material',
                    'item_id' => $itemId,
                    'quantity' => $quantity,
                    'unit' => $component['unit'] ?? 'pcs'
                ];

            } catch (\Exception $e) {
                // Ignore component errors to prevent crashing the main calculator
                // Log it? 
                error_log("BOM Logic Error: " . $e->getMessage());
                continue;
            }
        }

        return $bom;
    }

    /**
     * Format error response
     */
    private function formatError(string $message, array $details = []): array
    {
        return [
            'success' => false,
            'error' => $message,
            'details' => $details
        ];
    }
}
