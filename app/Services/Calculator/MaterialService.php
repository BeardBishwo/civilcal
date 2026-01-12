<?php

namespace App\Services\Calculator;

/**
 * Material Service
 * 
 * Central Repository for:
 * - Mix Ratios (e.g., Concrete 1:2:4)
 * - Material Constants (e.g., Brick Size, Steel Density)
 * - Conversions (e.g., Wet to Dry Volume)
 */
class MaterialService
{
    // Constants
    const CEMENT_DENSITY = 1440; // kg/m3
    const CEMENT_BAG_WEIGHT = 50; // kg
    const CEMENT_BAG_VOLUME = 0.0347; // m3 (Standard 50kg bag)
    const SAND_DENSITY = 1600;   // kg/m3
    const AGGREGATE_DENSITY = 1520; // kg/m³
    const STEEL_DENSITY = 7850;  // kg/m³

    const PAINT_COVERAGE = 10;   // m2 per Litre per coat
    const PLASTER_THICKNESS_DEFAULT = 0.012; // 12mm

    
    // Standard Brick Size (Metric) with Mortar
    const BRICK_LENGTH_M = 0.24;
    const BRICK_WIDTH_M = 0.115;
    const BRICK_HEIGHT_M = 0.057;

    /**
     * Get Recipe / Mix Design
     * 
     * Centralized "Recipe Book" for the system.
     * Decouples the "What" (Ingredients) from the "How" (Calculation).
     * 
     * @param string $type Material Type (concrete, plaster, brickwork)
     * @param string $grade Grade/Ratio (M20, 1:4, 1:6)
     */
    public function getRecipe($type, $grade)
    {
        $recipes = [
            'concrete' => [
                'M10' => ['cement' => 1, 'sand' => 3, 'aggregate' => 6],
                'M15' => ['cement' => 1, 'sand' => 2, 'aggregate' => 4],
                'M20' => ['cement' => 1, 'sand' => 1.5, 'aggregate' => 3],
                'M25' => ['cement' => 1, 'sand' => 1, 'aggregate' => 2]
            ],
            'plaster' => [
                '1:6' => ['cement' => 1, 'sand' => 6],
                '1:4' => ['cement' => 1, 'sand' => 4],
                '1:3' => ['cement' => 1, 'sand' => 3]
            ],
            'brickwork' => [
                '1:6' => ['cement' => 1, 'sand' => 6],
                '1:4' => ['cement' => 1, 'sand' => 4]
            ]
        ];

        return $recipes[$type][$grade] ?? null;
    }

    /**
     * Calculate Brickwork Materials
     */
    public function getBrickworkMaterials($wallVolume, $mixRatio = '1:6')
    {
        // 1. Calculate Mortar Volume (Approx 25-30% of wall volume)
        // Industry standard for estimating: 30% of total wall volume is wet mortar
        $wetMortarVolume = $wallVolume * 0.30;

        // 2. Calculate Number of Bricks
        // Subtract mortar volume from total wall volume to get net brick volume
        $netBrickVolume = $wallVolume - $wetMortarVolume;
        $oneBrickVolume = self::BRICK_LENGTH_M * self::BRICK_WIDTH_M * self::BRICK_HEIGHT_M;
        $numberOfBricks = ceil($netBrickVolume / $oneBrickVolume);
        
        // 3. Dry Volume (Wet * 1.33)
        $dryMortarVolume = $wetMortarVolume * 1.33;

        // 4. Cement & Sand
        list($cementPart, $sandPart) = explode(':', $mixRatio);
        $totalPart = $cementPart + $sandPart;

        $cementVol = ($dryMortarVolume * $cementPart) / $totalPart;
        $sandVol = ($dryMortarVolume * $sandPart) / $totalPart;

        return [
            'bricks' => [
                'quantity' => $numberOfBricks,
                'unit' => 'pcs',
                'name' => 'First Class Bricks'
            ],
            'cement' => [
                'quantity' => ceil($cementVol * self::CEMENT_DENSITY / 50), // Bags
                'unit' => 'bags',
                'name' => 'Portland Cement (50kg)'
            ],
            'sand' => [
                'quantity' => round($sandVol, 2),
                'unit' => 'm³',
                'name' => 'River Sand'
            ]
        ];
    }

    public function getConcreteMaterials($wetVolume, $mixRatio = '1:2:4')
    {
        // 1. Resolve Recipe (DECOUPLED)
        $recipe = $this->getRecipe('concrete', $mixRatio);
        
        if (!$recipe) {
            // Fallback for manual inputs or unknown grades
            $parts = explode(':', $mixRatio);
            if (count($parts) === 3) {
                $recipe = ['cement' => (float)$parts[0], 'sand' => (float)$parts[1], 'aggregate' => (float)$parts[2]];
            } else {
                $recipe = ['cement' => 1, 'sand' => 2, 'aggregate' => 4]; // Default M15
            }
        }
        
        $c = $recipe['cement'];
        $s = $recipe['sand'];
        $a = $recipe['aggregate'];
        $totalParts = $c + $s + $a;

        // 2. Dry Volume Conversion (54% increase)
        $dryVolume = $wetVolume * 1.54;

        // 3. Calculate Individual Volumes
        $cementVol = ($dryVolume * $c) / $totalParts;
        $sandVol = ($dryVolume * $s) / $totalParts;
        $aggVol = ($dryVolume * $a) / $totalParts;

        // 4. Convert to Standard Units
        // Using 0.0347 m3 per bag for high precision as per Phase 1 specs
        $bags = ceil($cementVol / self::CEMENT_BAG_VOLUME);

        return [
            'cement' => [
                'quantity' => $bags,
                'unit' => 'bags',
                'name' => 'Portland Cement (50kg)',
                'legacy_key' => 'cement_bags'
            ],
            'sand' => [
                'quantity' => round($sandVol, 2),
                'unit' => 'm³',
                'name' => 'River Sand',
                'legacy_key' => 'sand_m3'
            ],
            'aggregate' => [
                'quantity' => round($aggVol, 2),
                'unit' => 'm³',
                'name' => 'Coarse Aggregate (20mm)',
                'legacy_key' => 'aggregate_m3'
            ]
        ];
    }

    /**
     * Calculate Plastering Materials
     * 
     * @param float $area Surface area in m2
     * @param float $thickness Thickness in m
     * @param string $mixRatio Ratio (e.g., '1:4')
     */
    public function getPlasterMaterials($area, $thickness = 0.012, $mixRatio = '1:4')
    {
        $wetVolume = $area * $thickness;
        $dryVolume = $wetVolume * 1.33; // 33% increase for dry volume

        $recipe = $this->getRecipe('plaster', $mixRatio) ?? ['cement' => 1, 'sand' => 4];
        $c = $recipe['cement'];
        $s = $recipe['sand'];
        $totalParts = $c + $s;

        $cementVol = ($dryVolume * $c) / $totalParts;
        $sandVol = ($dryVolume * $s) / $totalParts;

        return [
            'cement' => [
                'quantity' => ceil($cementVol * self::CEMENT_DENSITY / 50),
                'unit' => 'bags',
                'name' => 'Portland Cement (50kg)'
            ],
            'sand' => [
                'quantity' => round($sandVol, 2),
                'unit' => 'm³',
                'name' => 'Fine Sand'
            ]
        ];
    }

    /**
     * Calculate Paint Materials
     * 
     * @param float $area Surface area in m2
     * @param int $coats Number of coats
     */
    public function getPaintMaterials($area, $coats = 2)
    {
        $totalLitre = ceil(($area * $coats) / self::PAINT_COVERAGE);

        return [
            'paint' => [
                'quantity' => $totalLitre,
                'unit' => 'L',
                'name' => 'Emulsion Paint'
            ],
            'primer' => [
                'quantity' => ceil($area / 12), // 12m2 per litre
                'unit' => 'L',
                'name' => 'Wall Primer'
            ]
        ];
    }

}
