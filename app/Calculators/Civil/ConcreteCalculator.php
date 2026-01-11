<?php

namespace App\Calculators\Civil;

use App\Calculators\EnterprisePipeline;

/**
 * Concrete Calculator
 * 
 * Calculates volume of concrete for Slabs, Columns, Beams, etc.
 * Chains into MaterialService for M15/M20/M25 mix ratios.
 */
class ConcreteCalculator extends EnterprisePipeline
{
    protected function calculateGeometry(array $inputs): array
    {
        // 1. Normalize Inputs
        $length = $this->unitService->toMeters($inputs['length'] ?? 0, $inputs['unit_length'] ?? 'm');
        $width = $this->unitService->toMeters($inputs['width'] ?? 0, $inputs['unit_width'] ?? 'm');
        $depth = $this->unitService->toMeters($inputs['depth'] ?? 0, $inputs['unit_depth'] ?? 'm');
        $quantity = $inputs['quantity'] ?? 1;

        // 2. Calculate Volume
        $volumePerItem = $length * $width * $depth;
        $totalVolume = $volumePerItem * $quantity;

        return [
            'volume' => round($totalVolume, 3),
            'area' => round($length * $width * $quantity, 2),
            'length' => $length,
            'width' => $width,
            'depth' => $depth,
            'quantity' => $quantity,
            'mix_ratio' => $inputs['mix_ratio'] ?? '1:2:4' // Default M15
        ];
    }

    protected function enrichMaterials(array $geometry): array
    {
        // Delegate to MaterialService
        return $this->materialService->getConcreteMaterials(
            $geometry['volume'], 
            $geometry['mix_ratio']
        );
    }

    protected function getRelatedItems(array $geometry): array
    {
        // Concrete usually requires Shuttering (Formwork)
        // Area of Shuttering = (Length + Width) * 2 * Depth (for sides) + (Length * Width) (for bottom if slab)
        // Simplified Logic: 
        // For a generic block, we assume sides.
        
        $sideArea = ($geometry['length'] + $geometry['width']) * 2 * $geometry['depth'] * $geometry['quantity'];
        
        return [
            'formwork_estimation' => [
                'area' => round($sideArea, 2),
                'unit' => 'm²',
                'description' => 'Estimated Formwork (Sides Only)',
                'approx_cost' => 'Calculated in BoQ'
            ]
        ];
    }
}
