<?php

namespace App\Calculators\Civil;

use App\Calculators\EnterprisePipeline;

/**
 * Paint Calculator
 * 
 * Calculates paint, primer, and putty required for walls.
 * Uses coverage rates defined in MaterialService.
 */
class PaintCalculator extends EnterprisePipeline
{
    protected function calculateGeometry(array $inputs): array
    {
        // 1. Normalize Inputs
        if (!empty($inputs['area'])) {
            $area = $this->unitService->toSquareMeters($inputs['area'], $inputs['unit_area'] ?? 'sqm');
        } else {
            $length = $this->unitService->toMeters($inputs['length'] ?? 0, $inputs['unit_length'] ?? 'm');
            $height = $this->unitService->toMeters($inputs['height'] ?? 0, $inputs['unit_height'] ?? 'm');
            $quantity = $inputs['quantity'] ?? 1;
            $area = $length * $height * $quantity;
        }

        // Deductions (Doors/Windows)
        $deduction = $inputs['deduction'] ?? 0;
        $netArea = max(0, $area - $deduction);

        return [
            'total_area' => round($area, 2),
            'net_area' => round($netArea, 2),
            'paint_type' => $inputs['paint_type'] ?? 'emulsion',
            'coats' => $inputs['coats'] ?? 2
        ];
    }

    protected function enrichMaterials(array $geometry): array
    {
        return $this->materialService->getPaintMaterials(
            $geometry['net_area'],
            $geometry['coats']
        );
    }

    protected function getRelatedItems(array $geometry): array
    {
        // Paint is usually the final step, but maybe labor?
        // CostService adds labor automatically usually.
        return [
            'maintenance_tip' => [
                'description' => 'Recommended repainting interval: 3-5 years for ' . $geometry['paint_type']
            ]
        ];
    }
}
