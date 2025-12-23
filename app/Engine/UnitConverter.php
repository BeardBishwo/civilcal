<?php

namespace App\Engine;

/**
 * Unit Converter - Universal unit conversion system
 * 
 * Handles conversion between different unit systems:
 * - Length: m, cm, mm, ft, in
 * - Area: m², ft², sqm, sqft
 * - Volume: m³, ft³, l, gal
 * - Weight: kg, g, lb, oz
 * - Temperature: °C, °F, K
 * 
 * @package App\Engine
 */
class UnitConverter
{
    // Conversion factors to base units (metric)
    private const LENGTH_TO_METERS = [
        'm' => 1.0,
        'meter' => 1.0,
        'cm' => 0.01,
        'mm' => 0.001,
        'ft' => 0.3048,
        'feet' => 0.3048,
        'in' => 0.0254,
        'inch' => 0.0254
    ];
    
    private const AREA_TO_SQM = [
        'm²' => 1.0,
        'sqm' => 1.0,
        'cm²' => 0.0001,
        'mm²' => 0.000001,
        'ft²' => 0.092903,
        'sqft' => 0.092903,
        'in²' => 0.00064516
    ];
    
    private const VOLUME_TO_CUBIC_METERS = [
        'm³' => 1.0,
        'cum' => 1.0,
        'l' => 0.001,
        'liter' => 0.001,
        'ft³' => 0.0283168,
        'cuft' => 0.0283168,
        'gal' => 0.00378541
    ];
    
    private const WEIGHT_TO_KG = [
        'kg' => 1.0,
        'g' => 0.001,
        'lb' => 0.453592,
        'oz' => 0.0283495
    ];
    
    /**
     * Normalize inputs to base units
     * 
     * @param array $inputs User inputs
     * @param array $schema Input schema with unit definitions
     * @return array Normalized inputs in base units
     */
    public function normalize(array $inputs, array $schema): array
    {
        $normalized = [];
        
        foreach ($schema as $fieldSchema) {
            $name = $fieldSchema['name'];
            $value = $inputs[$name] ?? null;
            
            if ($value === null) {
                $normalized[$name] = null;
                continue;
            }
            
            // If field has unit, convert to base unit
            if (isset($fieldSchema['unit'])) {
                $baseUnit = $fieldSchema['base_unit'] ?? $fieldSchema['unit'];
                $inputUnit = $inputs[$name . '_unit'] ?? $fieldSchema['unit'];
                
                $normalized[$name] = $this->convert(
                    $value,
                    $inputUnit,
                    $baseUnit,
                    $fieldSchema['unit_type'] ?? 'length'
                );
            } else {
                $normalized[$name] = $value;
            }
        }
        
        return $normalized;
    }
    
    /**
     * Convert value from one unit to another
     * 
     * @param float $value The value to convert
     * @param string $fromUnit Source unit
     * @param string $toUnit Target unit
     * @param string $unitType Type of unit (length, area, volume, weight)
     * @return float Converted value
     */
    public function convert(float $value, string $fromUnit, string $toUnit, string $unitType = 'length'): float
    {
        if ($fromUnit === $toUnit) {
            return $value;
        }
        
        $conversionTable = $this->getConversionTable($unitType);
        
        if (!isset($conversionTable[$fromUnit]) || !isset($conversionTable[$toUnit])) {
            throw new \Exception("Unknown unit: {$fromUnit} or {$toUnit}");
        }
        
        // Convert to base unit, then to target unit
        $baseValue = $value * $conversionTable[$fromUnit];
        $targetValue = $baseValue / $conversionTable[$toUnit];
        
        return $targetValue;
    }
    
    /**
     * Get conversion table for unit type
     */
    private function getConversionTable(string $unitType): array
    {
        switch ($unitType) {
            case 'length':
                return self::LENGTH_TO_METERS;
            case 'area':
                return self::AREA_TO_SQM;
            case 'volume':
                return self::VOLUME_TO_CUBIC_METERS;
            case 'weight':
            case 'mass':
                return self::WEIGHT_TO_KG;
            default:
                throw new \Exception("Unknown unit type: {$unitType}");
        }
    }
    
    /**
     * Convert temperature (special case)
     */
    public function convertTemperature(float $value, string $from, string $to): float
    {
        // Convert to Celsius first
        $celsius = match ($from) {
            'C', '°C' => $value,
            'F', '°F' => ($value - 32) * 5/9,
            'K' => $value - 273.15,
            default => throw new \Exception("Unknown temperature unit: {$from}")
        };
        
        // Convert from Celsius to target
        return match ($to) {
            'C', '°C' => $celsius,
            'F', '°F' => ($celsius * 9/5) + 32,
            'K' => $celsius + 273.15,
            default => throw new \Exception("Unknown temperature unit: {$to}")
        };
    }
}
