<?php

namespace App\Calculators\Civil;

use App\Calculators\EnterprisePipeline;

/**
 * Brick Wall Calculator (Prototype)
 * 
 * Demonstrates the Pipeline Architecture:
 * 1. Geometry: Wall Volume
 * 2. Materials: Bricks + Mortar (via Service)
 * 3. Related: Autotrigger Plaster
 * 4. Cost: Auto-calculated
 */
class BrickWallCalculator extends EnterprisePipeline
{
    protected function calculateGeometry(array $inputs): array
    {
        // 1. Get Inputs & Normalize
        $length = $this->unitService->toMeters($inputs['wall_length'] ?? 0, 'm');
        $height = $this->unitService->toMeters($inputs['wall_height'] ?? 0, 'm');
        // Thickness usually in mm
        $thickness = $this->unitService->toMeters($inputs['thickness'] ?? 230, 'mm');

        $volume = $length * $height * $thickness;
        $surfaceArea = $length * $height * 2; // Two sides

        return [
            'volume' => round($volume, 3),
            'surface_area' => round($surfaceArea, 2),
            'length' => $length,
            'height' => $height,
            'thickness' => $thickness
        ];
    }

    protected function enrichMaterials(array $geometry): array
    {
        // Delegate to MaterialService
        // We assume 1:6 mortar for standard walls
        return $this->materialService->getBrickworkMaterials($geometry['volume'], '1:6');
    }

    protected function getRelatedItems(array $geometry): array
    {
        // "Chain Reaction" - The Enterprise Feature
        // A Wall implies Surface Area, which implies Plaster and Paint.
        
        $suggestions = [];

        // 1. Suggest Plastering
        $suggestions[] = [
            'trigger' => 'always',
            'tool_slug' => 'plaster-calculator', // The tool to run next
            'heading' => 'Recommended: Wall Plastering',
            'reason' => 'Brick walls typically require plastering for a finished look.',
            'auto_fill' => [
                'area' => $geometry['surface_area'], // Auto-pass the result
                'thickness' => 12 // Default 12mm
            ],
            // Quick Estimate (Optional display)
            'preview_text' => sprintf("Approx %s m² of Plaster needed", $geometry['surface_area'])
        ];

        // 2. Suggest Painting
        $suggestions[] = [
            'trigger' => 'always',
            'tool_slug' => 'paint-calculator',
            'heading' => 'Recommended: Wall Painting',
            'reason' => 'Protect and beautify the plastered wall.',
            'auto_fill' => [
                'area' => $geometry['surface_area'],
                'coats' => 2
            ],
            'preview_text' => "Calculate Paint Cost"
        ];
        
        return $suggestions;
    }
}
