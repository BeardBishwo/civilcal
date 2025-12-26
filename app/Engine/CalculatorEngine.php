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
    private array $config = [];
    
    public function __construct()
    {
        $this->validator = new ValidationEngine();
        $this->unitConverter = new UnitConverter();
        $this->formulaRegistry = new FormulaRegistry();
        $this->formatter = new ResultFormatter();
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
            
            // Execute formulas
            $results = $this->formulaRegistry->execute($config['formulas'], $normalizedInputs);
            
            // Format results
            $formattedResults = $this->formatter->format($results, $config['outputs']);
            
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
        
        // Try common categories first
        $categories = ['civil', 'electrical', 'plumbing', 'hvac', 'fire', 'site', 'structural', 'estimation', 'mep', 'project-management', 'management', 'country'];
        
        foreach ($categories as $category) {
            $file = $configDir . $category . '.php';
            if (file_exists($file)) {
                $config = require $file;
                if (isset($config[$calculatorId])) {
                    return $file;
                }
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
