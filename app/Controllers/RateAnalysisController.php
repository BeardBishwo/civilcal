<?php

namespace App\Controllers;

use App\Core\Controller;

class RateAnalysisController extends Controller
{
    /**
     * Item Rate Analysis Calculator
     */
    public function item_rate_analysis()
    {
        // Load DUDBC norms
        $norms = require __DIR__ . '/../Config/norms.php';
        
        // Get all items from master
        $items = $this->db->find('est_item_master');
        
        $this->view->render('calculators/item_rate_analysis', [
            'title' => 'Item Rate Analysis - DUDBC Based',
            'norms' => $norms,
            'items' => $items
        ]);
    }

    /**
     * API: Calculate Rate
     */
    public function calculate_rate()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        $normKey = $input['norm_key'] ?? null;
        $materialRates = $input['material_rates'] ?? [];
        $laborRates = $input['labor_rates'] ?? [];
        $overhead = $input['overhead'] ?? 0;
        
        if (!$normKey) {
            echo json_encode(['success' => false, 'error' => 'Norm key required']);
            return;
        }

        // Load norms
        $norms = require __DIR__ . '/../Config/norms.php';
        
        // Parse norm key (e.g., "concrete.pcc_124")
        $parts = explode('.', $normKey);
        $category = $parts[0];
        $item = $parts[1];
        
        if (!isset($norms[$category][$item])) {
            echo json_encode(['success' => false, 'error' => 'Norm not found']);
            return;
        }
        
        $norm = $norms[$category][$item];
        
        // Calculate material cost
        $materialCost = 0;
        $materialBreakdown = [];
        if (isset($norm['materials'])) {
            foreach ($norm['materials'] as $mat => $coeff) {
                $rate = $materialRates[$mat] ?? 0;
                $cost = $coeff * $rate;
                $materialCost += $cost;
                $materialBreakdown[] = [
                    'name' => ucfirst(str_replace('_', ' ', $mat)),
                    'coefficient' => $coeff,
                    'rate' => $rate,
                    'cost' => $cost
                ];
            }
        }
        
        // Calculate labor cost
        $laborCost = 0;
        $laborBreakdown = [];
        if (isset($norm['labor'])) {
            foreach ($norm['labor'] as $lab => $coeff) {
                $rate = $laborRates[$lab] ?? 0;
                $cost = $coeff * $rate;
                $laborCost += $cost;
                $laborBreakdown[] = [
                    'name' => ucfirst(str_replace('_', ' ', $lab)),
                    'coefficient' => $coeff,
                    'rate' => $rate,
                    'cost' => $cost
                ];
            }
        }
        
        $totalRate = $materialCost + $laborCost + $overhead;
        
        echo json_encode([
            'success' => true,
            'material_cost' => round($materialCost, 2),
            'labor_cost' => round($laborCost, 2),
            'overhead' => $overhead,
            'total_rate' => round($totalRate, 2),
            'material_breakdown' => $materialBreakdown,
            'labor_breakdown' => $laborBreakdown
        ]);
    }

    /**
     * API: Save Rate to Location
     */
    public function save_rate_to_location()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        $itemCode = $input['item_code'] ?? null;
        $locationId = $input['location_id'] ?? null;
        $rate = $input['rate'] ?? 0;
        
        if (!$itemCode || !$locationId) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            return;
        }
        
        // Check if exists
        $existing = $this->db->findOne('est_local_rates', [
            'item_code' => $itemCode,
            'location_id' => $locationId
        ]);
        
        if ($existing) {
            $this->db->update('est_local_rates', 
                ['rate' => $rate], 
                'id = :id', 
                ['id' => $existing['id']]
            );
        } else {
            $this->db->insert('est_local_rates', [
                'item_code' => $itemCode,
                'location_id' => $locationId,
                'rate' => $rate
            ]);
        }
        
        echo json_encode(['success' => true]);
    }

    /**
     * Labor Rate Analysis Calculator
     */
    public function labor_rate_analysis()
    {
        $this->view->render('calculators/labor_rate_analysis', [
            'title' => 'Labor Rate Analysis'
        ]);
    }

    /**
     * Equipment Hourly Rate Calculator
     */
    public function equipment_hourly_rate()
    {
        $this->view->render('calculators/equipment_hourly_rate', [
            'title' => 'Equipment Hourly Rate Calculator'
        ]);
    }

    /**
     * Cash Flow Analysis
     */
    public function cash_flow_analysis()
    {
        $this->view->render('calculators/cash_flow_analysis', [
            'title' => 'Cash Flow Analysis'
        ]);
    }

    /**
     * NPV/IRR Analysis
     */
    public function npv_irr_analysis()
    {
        $this->view->render('calculators/npv_irr_analysis', [
            'title' => 'NPV/IRR Analysis'
        ]);
    }
}
