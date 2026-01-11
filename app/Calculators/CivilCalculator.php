<?php

namespace App\Calculators;

/**
 * Civil Calculator
 * 
 * Handles civil engineering calculations via method dispatching.
 * Replaces the old 'modules/' directory structure.
 */
class CivilCalculator
{
    /**
     * Main Entry Point / Dispatcher
     * 
     * @param string $slug The specific calculator slug (e.g., 'concrete-volume')
     * @param array $inputs Input data
     * @return array Calculation result
     * @throws \Exception If tool is not found
     */
    public function calculate($slug, $inputs)
    {
        // 1. Normalize Slug to Method Name
        // Example: 'concrete-volume' -> 'calculateConcreteVolume'
        // Example: 'brick-quantity'  -> 'calculateBrickQuantity'
        $methodName = 'calculate' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $slug)));

        // 2. Dispatch
        if (method_exists($this, $methodName)) {
            return $this->$methodName($inputs);
        }

        // 3. Fallback or Error
        throw new \Exception("Calculator tool '{$slug}' (Method: {$methodName}) not found in CivilCalculator.");
    }

    // ==========================================
    // MIGRATED CALCULATOR METHODS
    // ==========================================

    /**
     * Concrete Volume Calculator
     * Replaces: modules/civil/concrete-volume.php
     */
    private function calculateConcreteVolume($inputs)
    {
        $length = $inputs['length'] ?? 0;
        $width = $inputs['width'] ?? 0;
        $depth = $inputs['depth'] ?? 0;

        $volume = $length * $width * $depth;
        
        // Example logic (adapt as needed based on your original file)
        return [
            'volume' => $volume,
            'dry_volume' => $volume * 1.54,
            'bags' => ($volume * 1.54) / 0.0347 // Approx cement calculation
        ];
    }

    /**
     * Brick Quantity Calculator
     * Replaces: modules/civil/brick-quantity.php
     */
    private function calculateBrickQuantity($inputs)
    {
        $wallLength = $inputs['wall_length'] ?? 0;
        $wallHeight = $inputs['wall_height'] ?? 0;
        $wallThickness = $inputs['wall_thickness'] ?? 0; // meters

        $wallVolume = $wallLength * $wallHeight * $wallThickness;
        $brickVolume = 0.24 * 0.115 * 0.057; // Metric brick with mortar

        $bricks = $wallVolume / $brickVolume;

        return [
            'total_bricks' => ceil($bricks)
        ];
    }

    // Add more methods here as you migrate them...
}
