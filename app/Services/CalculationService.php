<?php
namespace App\Services;

use App\Core\Database;
use App\Calculators\CalculatorFactory;

class CalculationService
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function performCalculation($calculatorType, $calculatorSlug, $inputData, $userId = null)
    {
        try {
            $calculator = CalculatorFactory::create($calculatorType, $calculatorSlug);
            
            if (!$calculator) {
                return [
                    'success' => false,
                    'error' => 'Calculator not found'
                ];
            }
            
            // Validate inputs
            $validation = $calculator->validate($inputData);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'errors' => $validation['errors']
                ];
            }
            
            // Perform calculation
            $result = $calculator->calculate($inputData);
            
            // Save to history if user is logged in
            if ($userId) {
                $this->saveToHistory($userId, $calculatorType, $calculatorSlug, $inputData, $result);
            }
            
            // Extract numeric result if it's in a nested structure
            $numericResult = $result;
            if (is_array($result) && isset($result['result'])) {
                $numericResult = $result['result'];
            }
            
            return [
                'success' => true,
                'result' => $numericResult,  // Direct numeric value for API compatibility
                'data' => $result,           // Full result data for backward compatibility
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Calculation failed: ' . $e->getMessage()
            ];
        }
    }
    
    public function saveToHistory($userId, $calculatorType, $calculatorSlug, $inputs, $results)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO calculation_history 
                (user_id, calculator_type, calculator_slug, input_data, result_data, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            return $stmt->execute([
                $userId,
                $calculatorType,
                $calculatorSlug,
                json_encode($inputs),
                json_encode($results)
            ]);
        } catch (\Exception $e) {
            // If table doesn't have calculator_slug, try without it
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO calculation_history 
                    (user_id, calculator_type, input_data, result_data, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                
                return $stmt->execute([
                    $userId,
                    $calculatorType,
                    json_encode($inputs),
                    json_encode($results)
                ]);
            } catch (\Exception $e2) {
                // Log but don't fail the calculation
                error_log("Failed to save calculation history: " . $e2->getMessage());
                return false;
            }
        }
    }
    
    public function getUserHistory($userId, $limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM calculation_history 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getCalculationById($calculationId, $userId = null)
    {
        $sql = "SELECT * FROM calculation_history WHERE id = ?";
        $params = [$calculationId];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch();
    }
    
    public function deleteCalculation($calculationId, $userId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM calculation_history 
            WHERE id = ? AND user_id = ?
        ");
        
        return $stmt->execute([$calculationId, $userId]);
    }
    
    public function getCalculatorUsageStats($period = '30 days')
    {
        $stmt = $this->db->prepare("
            SELECT 
                calculator_type,
                calculator_slug,
                COUNT(*) as usage_count,
                DATE(created_at) as date
            FROM calculation_history 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY calculator_type, calculator_slug, DATE(created_at)
            ORDER BY usage_count DESC
        ");
        
        $stmt->execute([$period]);
        return $stmt->fetchAll();
    }
}
