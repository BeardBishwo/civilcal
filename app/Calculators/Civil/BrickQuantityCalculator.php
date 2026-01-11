<?php

namespace App\Calculators\Civil;

use App\Calculators\EnterprisePipeline;

/**
 * Robust Brick Quantity Calculator
 * 
 * Handles detailed calculation for bricks, mortar, cement, sand, and costs.
 * Supports wastage and pro-active suggestions.
 */
class BrickQuantityCalculator extends EnterprisePipeline
{
    protected function calculateGeometry(array $inputs): array
    {
        // 1. Get Inputs & Normalize Units
        $length = $this->unitService->toMeters($inputs['wall_length'] ?? 0, 'm');
        $height = $this->unitService->toMeters($inputs['wall_height'] ?? 0, 'm');
        
        $brick_l = ($inputs['brick_length'] ?? 230) / 1000;
        $brick_w = ($inputs['brick_width'] ?? 110) / 1000;
        $brick_h = ($inputs['brick_height'] ?? 70) / 1000;
        $mortar_t = ($inputs['mortar_thickness'] ?? 10) / 1000;
        
        $wall_type = $inputs['wall_type'] ?? 'single';
        // Effective wall thickness
        $wall_thickness = ($wall_type === 'double') ? ($brick_w * 2 + $mortar_t) : $brick_w;
        
        // 2. Core Geometry
        $wall_area = $length * $height;
        $wall_volume = $wall_area * $wall_thickness;
        
        // 3. Brick Calculation (Size with Mortar)
        $eff_brick_l = $brick_l + $mortar_t;
        $eff_brick_h = $brick_h + $mortar_t;
        $eff_brick_w = $brick_w + $mortar_t;
        
        // Bricks per m3
        // Formula: 1 / (eff_l * eff_h * eff_w) is for volume based, 
        // but traditionally we use: Bricks = Wall Volume / (eff_brick_l * eff_brick_h * brick_w) for single skin
        // Or simpler: Number of bricks = Wall Volume / Volume of one brick with mortar
        $brick_vol_with_mortar = $eff_brick_l * $eff_brick_h * ($wall_type === 'double' ? $eff_brick_w : $brick_w);
        
        // Actually, for a single skin wall, thickness is usually just brick width.
        // For double skin, it's 2 * brick width + mortar.
        // Let's use the Volume method for robustness.
        $bricks_needed = $wall_volume / ($eff_brick_l * $eff_brick_h * $brick_w);
        
        // Wastage
        $wastage_p = ($inputs['wastage'] ?? 5) / 100;
        $total_bricks = ceil($bricks_needed * (1 + $wastage_p));
        
        return [
            'wall_area' => $wall_area,
            'wall_volume' => $wall_volume,
            'bricks_needed' => $bricks_needed,
            'total_bricks' => $total_bricks,
            'brick_l' => $brick_l,
            'brick_w' => $brick_w,
            'brick_h' => $brick_h,
            'mortar_t' => $mortar_t,
            'inputs' => $inputs // Keep for other steps
        ];
    }

    protected function enrichMaterials(array $geometry): array
    {
        $inputs = $geometry['inputs'];
        $wall_volume = $geometry['wall_volume'];
        $total_bricks = $geometry['total_bricks'];
        
        // 1. Mortar Calculation
        // Volume of Mortar = Wall Volume - (Number of Bricks * Volume of One Brick)
        // We use actual brick volume (without mortar)
        $actual_brick_vol = $total_bricks * ($geometry['brick_l'] * $geometry['brick_w'] * $geometry['brick_h']);
        $mortar_vol_wet = $wall_volume - $actual_brick_vol;
        
        if ($mortar_vol_wet < 0) $mortar_vol_wet = $wall_volume * 0.25; // Fallback to 25% if calc goes weird
        
        // Dry Volume = Wet Volume * 1.33 (Bulking/Wastage factor for mortar)
        $mortar_vol_dry = $mortar_vol_wet * 1.33;
        
        // 2. Mix Ratio
        $ratio_str = $inputs['mortar_ratio'] ?? '1:4';
        $parts = explode(':', $ratio_str);
        $cement_part = (int)$parts[0];
        $sand_part = (int)$parts[1];
        $total_parts = $cement_part + $sand_part;
        
        // Quantities
        $cement_vol = ($mortar_vol_dry / $total_parts) * $cement_part;
        $sand_vol = ($mortar_vol_dry / $total_parts) * $sand_part;
        
        // Convert Cement Volume to Bags (1 bag = 0.035 m3 approx)
        $cement_bags = $cement_vol / 0.035;
        
        return [
            'bricks' => [
                'name' => 'Bricks',
                'quantity' => $total_bricks,
                'unit' => 'pcs',
                'description' => 'Total bricks including wastage'
            ],
            'cement' => [
                'name' => 'Cement',
                'quantity' => round($cement_bags, 2),
                'unit' => 'bags',
                'description' => 'Cement bags for ' . $ratio_str . ' mortar mix'
            ],
            'sand' => [
                'name' => 'Sand',
                'quantity' => round($sand_vol, 2),
                'unit' => 'm³',
                'description' => 'Sand volume for ' . $ratio_str . ' mortar mix'
            ],
            'meta' => [
                'wall_area' => round($geometry['wall_area'], 2),
                'total_bricks' => $total_bricks,
                'cement_bags' => round($cement_bags, 2),
                'sand_volume' => round($sand_vol, 2),
                'prices' => [
                    'bricks' => $inputs['brick_price'] ?? 18,
                    'cement' => $inputs['cement_price'] ?? 850,
                    'sand' => $inputs['sand_price'] ?? 3500
                ]
            ]
        ];
    }

    protected function enrichCost(array $materials): array
    {
        $prices = $materials['meta']['prices'];
        
        $brick_cost = $materials['bricks']['quantity'] * $prices['bricks'];
        $cement_cost = $materials['cement']['quantity'] * $prices['cement'];
        $sand_cost = $materials['sand']['quantity'] * $prices['sand'];
        
        $total_cost = $brick_cost + $cement_cost + $sand_cost;
        
        return [
            'line_items' => [
                [
                    'name' => 'Bricks',
                    'quantity' => $materials['bricks']['quantity'],
                    'unit' => 'pcs',
                    'rate' => $prices['bricks'],
                    'total' => $brick_cost
                ],
                [
                    'name' => 'Cement',
                    'quantity' => $materials['cement']['quantity'],
                    'unit' => 'bags',
                    'rate' => $prices['cement'],
                    'total' => $cement_cost
                ],
                [
                    'name' => 'Sand',
                    'quantity' => $materials['sand']['quantity'],
                    'unit' => 'm³',
                    'rate' => $prices['sand'],
                    'total' => $sand_cost
                ]
            ],
            'total_amount' => $total_cost,
            'currency' => 'NPR'
        ];
    }

    protected function getRelatedItems(array $geometry): array
    {
        $suggestions = [];
        $area = $geometry['wall_area'];

        // 1. Plastering Suggestion
        $suggestions[] = [
            'trigger' => 'always',
            'tool_slug' => 'plaster-calculator',
            'heading' => 'Add Plastering?',
            'description' => sprintf("Estimate cement and sand needed to plaster this %s m² wall.", round($area, 2)),
            'target_url' => app_base_url('/plaster-calculator?area=' . $area),
            'auto_fill' => [
                'area' => $area,
                'thickness' => 12
            ]
        ];

        // 2. Painting Suggestion
        $suggestions[] = [
            'trigger' => 'always',
            'tool_slug' => 'paint-calculator',
            'heading' => 'Add Painting?',
            'description' => "Calculate paint and primer needed for a finished look.",
            'target_url' => app_base_url('/paint-calculator?area=' . $area),
            'auto_fill' => [
                'area' => $area,
                'coats' => 2
            ]
        ];

        return $suggestions;
    }

    /**
     * Override execute to format outputs specifically for the robust calculator
     */
    public function execute($inputs)
    {
        $result = parent::execute($inputs);
        
        // Map detailed results back to flat outputs for the Generic Template to pick up
        $result['results'] = [
            'wall_area' => [
                'label' => 'Wall Surface Area',
                'value' => $result['materials']['meta']['wall_area'],
                'unit' => 'm²',
                'formatted' => $result['materials']['meta']['wall_area'] . ' m²'
            ],
            'total_bricks' => [
                'label' => 'Total Bricks (Incl. Wastage)',
                'value' => $result['materials']['meta']['total_bricks'],
                'unit' => 'bricks',
                'formatted' => number_format($result['materials']['meta']['total_bricks']) . ' Bricks'
            ],
            'cement_bags' => [
                'label' => 'Cement Required',
                'value' => $result['materials']['meta']['cement_bags'],
                'unit' => 'bags',
                'formatted' => $result['materials']['meta']['cement_bags'] . ' Bags'
            ],
            'sand_volume' => [
                'label' => 'Sand Required',
                'value' => $result['materials']['meta']['sand_volume'],
                'unit' => 'm³',
                'formatted' => $result['materials']['meta']['sand_volume'] . ' m³'
            ],
            'total_cost' => [
                'label' => 'Estimated Total Cost',
                'value' => $result['cost']['total_amount'],
                'unit' => 'NPR',
                'formatted' => 'NPR ' . number_format($result['cost']['total_amount'], 2)
            ],
            // Pass enterprise data through
            'bill_of_materials' => $result['cost'],
            'related_items' => $result['related_items'],
            'meta' => $result['meta']
        ];
        
        return $result;
    }
}
