<?php

namespace App\Calculators\Civil;

use App\Calculators\EnterprisePipeline;

/**
 * Plaster Calculator
 * 
 * Calculates cement and sand for wall/ceiling plastering.
 * Includes wastage and dry volume factors automatically via MaterialService.
 */
class PlasterCalculator extends EnterprisePipeline
{
    protected function calculateGeometry(array $inputs): array
    {
        // 1. Normalize Inputs
        // Can be direct Area OR Length x Height
        if (!empty($inputs['area'])) {
            $area = $this->unitService->toSquareMeters($inputs['area'], $inputs['unit_area'] ?? 'sqm');
        } else {
            $length = $this->unitService->toMeters($inputs['length'] ?? 0, $inputs['unit_length'] ?? 'm');
            $height = $this->unitService->toMeters($inputs['height'] ?? 0, $inputs['unit_height'] ?? 'm');
            $quantity = $inputs['quantity'] ?? 1;
            
            $sides = $inputs['sides'] ?? 1; // 1 for single side, 2 for both
            $area = $length * $height * $quantity * $sides;
        }

        // Thickness: user input in mm, convert to meters
        $thicknessMm = $inputs['thickness'] ?? 12; // Default 12mm
        $thickness = $thicknessMm / 1000;

        return [
            'area' => round($area, 2),
            'thickness' => $thickness,
            'thickness_mm' => $thicknessMm,
            'mix_ratio' => $inputs['mix_ratio'] ?? '1:4',
            'volume' => round($area * $thickness, 4) // Raw volume
        ];
    }

    protected function enrichMaterials(array $geometry): array
    {
        return $this->materialService->getPlasterMaterials(
            $geometry['area'],
            $geometry['thickness'],
            $geometry['mix_ratio']
        );
    }

    protected function getRelatedItems(array $geometry): array
    {
        // Plaster usually followed by Paint/Primer
        return [
            'finish_estimation' => [
                'description' => 'Recommended Paint Estimation',
                'approx_area' => $geometry['area'] . ' m²',
                'link' => '/calculators/civil/paint-calculator' // Suggest next tool
            ]
        ];
    }
}
