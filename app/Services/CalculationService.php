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
    
    public function performCalculation($calculatorType, $calculatorSlug, $inputData, $userId = null, $projectId = null)
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
            
            // 2. Perform calculation
            $result = $calculator->calculate($inputData);
            
            // 3. Handle Failure
            $success = $result['status'] === 'success' || ($result['success'] ?? false) === true;
            if (!$success) {
                return $result; // Pass-through engine error
            }

            // 4. Transform to Enterprise structure
            // If result comes from CalculatorEngine, it's inside 'results'
            $source = isset($result['results']) && is_array($result['results']) ? $result['results'] : $result;

            $finalResponse = [
                'status' => 'success',
                'physics' => $source['geometry'] ?? $source['result'] ?? $source['data'] ?? $source,
                'enterprise' => [
                    'materials' => $source['materials'] ?? $source['bill_of_materials'] ?? null,
                    'cost' => $source['cost'] ?? $source['bill_of_materials'] ?? null,
                    'suggestions' => $source['related_items'] ?? $source['suggestions'] ?? []
                ]
            ];

            // Clean up physics if it contains enterprise keys (redundancy)
            if (is_array($finalResponse['physics'])) {
                $eKeys = ['bill_of_materials', 'related_items', 'materials', 'cost', 'suggestions', 'geometry', 'results', 'success', 'calculator', 'inputs', 'metadata'];
                foreach ($eKeys as $k) unset($finalResponse['physics'][$k]);
            }

            // Backward compatibility for generic 'result' or legacy structures
            if (isset($source['result'])) {
                $finalResponse['result'] = $source['result'];
            }

            // Save to history if user is logged in
            $historyId = null;
            if ($userId) {
                $historyId = $this->saveToHistory($userId, $calculatorType, $calculatorSlug, $inputData, $result, $projectId);
                $finalResponse['history_id'] = $historyId;
            }
            
            $finalResponse['timestamp'] = date('Y-m-d H:i:s');
            
            return $finalResponse;
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Calculation failed: ' . $e->getMessage()
            ];
        }
    }
    
    public function saveToHistory($userId, $calculatorType, $calculatorSlug, $inputs, $results, $projectId = null)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO calculation_history 
                (user_id, calculator_type, calculator_slug, input_data, result_data, project_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([
                $userId,
                $calculatorType,
                $calculatorSlug,
                json_encode($inputs),
                json_encode($results),
                $projectId
            ])) {
                return $this->db->lastInsertId();
            }
            return false;
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
