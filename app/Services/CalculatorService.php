<?php
namespace App\Services;

/**
 * Calculator service for performing engineering calculations
 * Provides a centralized interface for all calculator operations
 */
class CalculatorService {
    private $database;
    private $logger;
    private $cache;
    
    public function __construct($database, $logger = null, $cache = null) {
        $this->database = $database;
        $this->logger = $logger;
        $this->cache = $cache;
    }
    
    /**
     * Perform a calculation based on calculator type and input data
     */
    public function calculate(string $calculatorType, array $inputs): array {
        try {
            // Log calculation request
            $this->logCalculation('start', $calculatorType, $inputs);
            
            // Validate inputs
            $validation = $this->validateInputs($calculatorType, $inputs);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'error' => 'validation_failed',
                    'message' => 'Input validation failed',
                    'errors' => $validation['errors']
                ];
            }
            
            // Check cache first
            $cacheKey = $this->generateCacheKey($calculatorType, $inputs);
            if ($this->cache && $cachedResult = $this->cache->get($cacheKey)) {
                $this->logCalculation('cache_hit', $calculatorType, $inputs);
                return [
                    'success' => true,
                    'result' => $cachedResult,
                    'cached' => true
                ];
            }
            
            // Perform calculation
            $calculator = $this->getCalculator($calculatorType);
            if (!$calculator) {
                return [
                    'success' => false,
                    'error' => 'calculator_not_found',
                    'message' => "Calculator {$calculatorType} not found"
                ];
            }
            
            $result = $calculator->calculate($inputs);
            
            // Cache result
            if ($this->cache && $result['success']) {
                $this->cache->put($cacheKey, $result['data'], 3600); // Cache for 1 hour
            }
            
            // Save calculation history
            $this->saveCalculationHistory($calculatorType, $inputs, $result);
            
            // Log successful calculation
            $this->logCalculation('success', $calculatorType, $inputs, $result);
            
            return [
                'success' => true,
                'result' => $result['data'] ?? $result,
                'calculator' => $calculatorType,
                'cached' => false
            ];
            
        } catch (\Exception $e) {
            $this->logCalculation('error', $calculatorType, $inputs, $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'calculation_failed',
                'message' => 'Calculation failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get calculation history for a user
     */
    public function getCalculationHistory(int $userId, int $limit = 50, int $offset = 0): array {
        try {
            $query = "
                SELECT * FROM calculation_history 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ";
            
            $stmt = $this->database->query($query, [$userId, $limit, $offset]);
            $results = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $results,
                'count' => count($results)
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'history_failed',
                'message' => 'Failed to retrieve calculation history'
            ];
        }
    }
    
    /**
     * Get calculation statistics
     */
    public function getCalculationStats(int $userId = null): array {
        try {
            $query = "
                SELECT 
                    calculator_type,
                    COUNT(*) as calculation_count,
                    AVG(response_time) as avg_response_time
                FROM calculation_history
            ";
            
            $params = [];
            if ($userId) {
                $query .= " WHERE user_id = ?";
                $params = [$userId];
            }
            
            $query .= " GROUP BY calculator_type ORDER BY calculation_count DESC";
            
            $stmt = $this->database->query($query, $params);
            $results = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $results
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'stats_failed',
                'message' => 'Failed to retrieve calculation statistics'
            ];
        }
    }
    
    /**
     * Get available calculators
     */
    public function getAvailableCalculators(): array {
        $calculators = [
            'civil' => [
                'concrete_volume' => 'Concrete Volume Calculator',
                'brick_quantity' => 'Brick Quantity Calculator',
                'mortar_ratio' => 'Mortar Ratio Calculator',
                'beam_load' => 'Beam Load Calculator'
            ],
            'electrical' => [
                'wire_sizing' => 'Wire Sizing Calculator',
                'voltage_drop' => 'Voltage Drop Calculator',
                'conduit_fill' => 'Conduit Fill Calculator',
                'short_circuit' => 'Short Circuit Calculator'
            ],
            'mechanical' => [
                'force_calculator' => 'Force Calculator',
                'torque_calculator' => 'Torque Calculator',
                'power_calculator' => 'Power Calculator'
            ],
            'general' => [
                'unit_converter' => 'Unit Converter',
                'area_calculator' => 'Area Calculator',
                'volume_calculator' => 'Volume Calculator'
            ]
        ];
        
        return [
            'success' => true,
            'data' => $calculators,
            'categories' => array_keys($calculators)
        ];
    }
    
    /**
     * Validate calculator inputs
     */
    private function validateInputs(string $calculatorType, array $inputs): array {
        $errors = [];
        
        // Get validation rules for calculator type
        $rules = $this->getValidationRules($calculatorType);
        
        foreach ($rules as $field => $ruleSet) {
            $value = $inputs[$field] ?? null;
            
            foreach ($ruleSet as $rule) {
                switch ($rule) {
                    case 'required':
                        if (empty($value) && $value !== 0 && $value !== '0') {
                            $errors[$field][] = "{$field} is required";
                        }
                        break;
                        
                    case 'numeric':
                        if (!is_numeric($value)) {
                            $errors[$field][] = "{$field} must be numeric";
                        }
                        break;
                        
                    case 'positive':
                        if (!is_numeric($value) || $value <= 0) {
                            $errors[$field][] = "{$field} must be a positive number";
                        }
                        break;
                        
                    case 'string':
                        if (!is_string($value)) {
                            $errors[$field][] = "{$field} must be a string";
                        }
                        break;
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Get calculator instance
     */
    private function getCalculator(string $calculatorType) {
        $calculatorClass = "App\\Calculators\\" . ucfirst($calculatorType) . "Calculator";
        
        if (class_exists($calculatorClass)) {
            return new $calculatorClass();
        }
        
        // Try to find calculator in modules
        $modulePaths = [
            BASE_PATH . "/modules/civil/{$calculatorType}.php",
            BASE_PATH . "/modules/electrical/{$calculatorType}.php",
            BASE_PATH . "/modules/mechanical/{$calculatorType}.php",
            BASE_PATH . "/modules/general/{$calculatorType}.php"
        ];
        
        foreach ($modulePaths as $path) {
            if (file_exists($path)) {
                require_once $path;
                
                $className = ucfirst($calculatorType) . "Calculator";
                if (class_exists($className)) {
                    return new $className();
                }
            }
        }
        
        return null;
    }
    
    /**
     * Generate cache key for calculation
     */
    private function generateCacheKey(string $calculatorType, array $inputs): string {
        ksort($inputs);
        return md5("calculation_{$calculatorType}_" . serialize($inputs));
    }
    
    /**
     * Save calculation history
     */
    private function saveCalculationHistory(string $calculatorType, array $inputs, array $result): void {
        try {
            // Only save for authenticated users
            $userId = $_SESSION['user']['id'] ?? null;
            if (!$userId) {
                return;
            }
            
            $data = [
                'user_id' => $userId,
                'calculator_type' => $calculatorType,
                'inputs' => json_encode($inputs),
                'outputs' => json_encode($result),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->database->insert('calculation_history', $data);
            
        } catch (\Exception $e) {
            // Log error but don't fail the calculation
            if ($this->logger) {
                $this->logger->error("Failed to save calculation history", [
                    'calculator' => $calculatorType,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Log calculation activity
     */
    private function logCalculation(string $action, string $calculatorType, array $inputs, $data = null): void {
        if ($this->logger) {
            $logData = [
                'action' => $action,
                'calculator' => $calculatorType,
                'inputs_count' => count($inputs),
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            if ($data) {
                $logData['data'] = $data;
            }
            
            $this->logger->info("Calculation {$action}", $logData);
        }
    }
    
    /**
     * Get validation rules for calculator type
     */
    private function getValidationRules(string $calculatorType): array {
        // Load validation rules from config
        $rulesFile = BASE_PATH . "/app/Config/calculators/{$calculatorType}.php";
        
        if (file_exists($rulesFile)) {
            return include $rulesFile;
        }
        
        // Default rules
        return [
            'length' => ['numeric', 'positive'],
            'width' => ['numeric', 'positive'],
            'height' => ['numeric', 'positive'],
            'weight' => ['numeric', 'positive'],
            'force' => ['numeric', 'positive']
        ];
    }
    
    /**
     * Clear calculation cache
     */
    public function clearCache(): bool {
        if ($this->cache) {
            return $this->cache->flush();
        }
        return false;
    }
    
    /**
     * Get calculation performance metrics
     */
    public function getPerformanceMetrics(): array {
        try {
            $query = "
                SELECT 
                    AVG(response_time) as avg_response_time,
                    MAX(response_time) as max_response_time,
                    MIN(response_time) as min_response_time,
                    COUNT(*) as total_calculations
                FROM calculation_history
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ";
            
            $stmt = $this->database->query($query);
            $result = $stmt->fetch();
            
            return [
                'success' => true,
                'data' => $result ?: []
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'metrics_failed',
                'message' => 'Failed to retrieve performance metrics'
            ];
        }
    }
}
