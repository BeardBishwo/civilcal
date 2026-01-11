<?php

namespace App\Services;

use App\Core\Database;

/**
 * Estimation Service
 * 
 * Handles construction estimation calculations and management
 * 
 * @package App\Services
 */
class EstimationService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Calculate project estimation
     * 
     * @param array $projectData Project specifications
     * @return array Estimation results
     */
    public function calculateEstimate(array $projectData)
    {
        // Validate required fields
        $validation = \App\Core\Validator::validate($projectData, [
            'project_type' => 'required',
            'area' => 'required|numeric',
            'unit' => 'required'
        ]);

        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors']
            ];
        }

        $projectType = $projectData['project_type'];
        $area = (float)$projectData['area'];
        $unit = $projectData['unit'];

        // Convert to standard unit (sq ft)
        $areaInSqFt = $this->convertToSqFt($area, $unit);

        // Get rate card based on project type
        $rates = $this->getRateCard($projectType);

        // Calculate material quantities
        $materials = $this->calculateMaterials($areaInSqFt, $projectType);

        // Calculate costs
        $materialCost = $this->calculateMaterialCost($materials, $rates);
        $laborCost = $this->calculateLaborCost($areaInSqFt, $rates);
        $overheadCost = ($materialCost + $laborCost) * 0.15; // 15% overhead
        $totalCost = $materialCost + $laborCost + $overheadCost;

        return [
            'success' => true,
            'estimate' => [
                'area' => $areaInSqFt,
                'unit' => 'sq_ft',
                'materials' => $materials,
                'costs' => [
                    'material' => round($materialCost, 2),
                    'labor' => round($laborCost, 2),
                    'overhead' => round($overheadCost, 2),
                    'total' => round($totalCost, 2)
                ],
                'project_type' => $projectType,
                'calculated_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * Save estimation to database
     * 
     * @param int $userId User ID
     * @param array $estimateData Estimation data
     * @return array Result with estimate ID
     */
    public function saveEstimate($userId, array $estimateData)
    {
        try {
            $data = [
                'user_id' => $userId,
                'project_name' => $estimateData['project_name'] ?? 'Untitled Project',
                'project_type' => $estimateData['project_type'],
                'area' => $estimateData['area'],
                'estimate_data' => json_encode($estimateData),
                'total_cost' => $estimateData['costs']['total'] ?? 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('estimations', $data);
            $estimateId = $this->db->lastInsertId();

            return [
                'success' => true,
                'estimate_id' => $estimateId,
                'message' => 'Estimate saved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to save estimate: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user's estimation history
     * 
     * @param int $userId User ID
     * @param int $limit Number of records
     * @param int $offset Pagination offset
     * @return array Estimation history
     */
    public function getEstimateHistory($userId, $limit = 50, $offset = 0)
    {
        $limit = (int)$limit;
        $offset = (int)$offset;

        $sql = "SELECT * FROM estimations 
                WHERE user_id = :userId 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $estimates = $stmt->fetchAll();

        // Decode JSON data
        foreach ($estimates as &$estimate) {
            $estimate['estimate_data'] = json_decode($estimate['estimate_data'], true);
        }

        return $estimates;
    }

    /**
     * Export estimate to specified format
     * 
     * @param int $estimateId Estimate ID
     * @param string $format Export format (pdf, excel, json)
     * @return array Export result with file path
     */
    public function exportEstimate($estimateId, $format = 'pdf')
    {
        $estimate = $this->db->findOne('estimations', ['id' => $estimateId]);

        if (!$estimate) {
            return ['success' => false, 'message' => 'Estimate not found'];
        }

        $estimateData = json_decode($estimate['estimate_data'], true);

        switch ($format) {
            case 'json':
                return [
                    'success' => true,
                    'data' => $estimateData,
                    'format' => 'json'
                ];

            case 'pdf':
            case 'excel':
                // Placeholder for PDF/Excel generation
                return [
                    'success' => false,
                    'message' => 'PDF/Excel export not yet implemented'
                ];

            default:
                return ['success' => false, 'message' => 'Invalid format'];
        }
    }

    /**
     * Validate estimation data
     * 
     * @param array $data Data to validate
     * @return array Validation result
     */
    public function validateEstimateData(array $data)
    {
        return \App\Core\Validator::validate($data, [
            'project_type' => 'required',
            'area' => 'required|numeric|min:1',
            'unit' => 'required'
        ]);
    }

    /**
     * Convert area to square feet
     * 
     * @param float $area Area value
     * @param string $unit Unit type
     * @return float Area in square feet
     */
    private function convertToSqFt($area, $unit)
    {
        $conversions = [
            'sq_ft' => 1,
            'sq_m' => 10.764,
            'sq_yard' => 9,
            'acre' => 43560
        ];

        return $area * ($conversions[$unit] ?? 1);
    }

    /**
     * Get rate card for project type
     * 
     * @param string $projectType Project type
     * @return array Rate card
     */
    private function getRateCard($projectType)
    {
        // Default rate card (can be moved to database/config)
        $rateCards = [
            'residential' => [
                'cement' => 8.5,
                'bricks' => 0.5,
                'steel' => 60,
                'sand' => 2.5,
                'labor_per_sqft' => 15
            ],
            'commercial' => [
                'cement' => 9.5,
                'bricks' => 0.6,
                'steel' => 70,
                'sand' => 3.0,
                'labor_per_sqft' => 20
            ],
            'industrial' => [
                'cement' => 10.5,
                'bricks' => 0.7,
                'steel' => 80,
                'sand' => 3.5,
                'labor_per_sqft' => 25
            ]
        ];

        return $rateCards[$projectType] ?? $rateCards['residential'];
    }

    /**
     * Calculate material quantities
     * 
     * @param float $areaInSqFt Area in square feet
     * @param string $projectType Project type
     * @return array Material quantities
     */
    private function calculateMaterials($areaInSqFt, $projectType)
    {
        // Standard material requirements per sq ft
        return [
            'cement' => round($areaInSqFt * 0.4, 2), // bags
            'bricks' => round($areaInSqFt * 8, 0), // pieces
            'steel' => round($areaInSqFt * 4, 2), // kg
            'sand' => round($areaInSqFt * 1.5, 2) // cubic ft
        ];
    }

    /**
     * Calculate material cost
     * 
     * @param array $materials Material quantities
     * @param array $rates Rate card
     * @return float Total material cost
     */
    private function calculateMaterialCost($materials, $rates)
    {
        $cost = 0;
        foreach ($materials as $material => $quantity) {
            $cost += $quantity * ($rates[$material] ?? 0);
        }
        return $cost;
    }

    /**
     * Calculate labor cost
     * 
     * @param float $areaInSqFt Area in square feet
     * @param array $rates Rate card
     * @return float Total labor cost
     */
    private function calculateLaborCost($areaInSqFt, $rates)
    {
        return $areaInSqFt * ($rates['labor_per_sqft'] ?? 15);
    }
}
