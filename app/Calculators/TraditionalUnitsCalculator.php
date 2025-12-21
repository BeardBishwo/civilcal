<?php

namespace App\Calculators;

use App\Services\GeolocationService;

/**
 * Traditional Nepali Units Calculator
 * 
 * Provides conversion between traditional Nepali land measurement units
 * and metric measurements, with geolocation awareness for Nepali users.
 */
class TraditionalUnitsCalculator
{
    private GeolocationService $geolocationService;
    private array $traditionalUnits;
    private string $baseUnit = 'daam';

    public function __construct()
    {
        $this->geolocationService = new GeolocationService();
        $this->initializeTraditionalUnits();
    }

    /**
     * Initialize traditional Nepali units with conversion factors
     * Base unit is 'daam' (smallest unit)
     */
    private function initializeTraditionalUnits(): void
    {
        // Base unit for internal calculation will be Square Feet
        $this->traditionalUnits = [
            // Hilly Region Units (Ropani system)
            'daam' => [
                'name' => 'Daam',
                'name_nepali' => 'दाम',
                'sq_feet' => 21.390625,
                'system' => 'hilly',
                'order' => 1
            ],
            'paisa' => [
                'name' => 'Paisa',
                'name_nepali' => 'पैसा',
                'sq_feet' => 85.5625,
                'system' => 'hilly',
                'order' => 2
            ],
            'aana' => [
                'name' => 'Aana',
                'name_nepali' => 'आना',
                'sq_feet' => 342.25,
                'system' => 'hilly',
                'order' => 3
            ],
            'ropani' => [
                'name' => 'Ropani',
                'name_nepali' => 'रोपनी',
                'sq_feet' => 5476,
                'system' => 'hilly',
                'order' => 4
            ],
            // Terai Region Units (Bigha system)
            'dhur' => [
                'name' => 'Dhur',
                'name_nepali' => 'धुर',
                'sq_feet' => 182.25,
                'system' => 'terai',
                'order' => 5
            ],
            'kattha' => [
                'name' => 'Kattha',
                'name_nepali' => 'कठ्ठा',
                'sq_feet' => 3645,
                'system' => 'terai',
                'order' => 6
            ],
            'bigha' => [
                'name' => 'Bigha',
                'name_nepali' => 'बिघा',
                'sq_feet' => 72900,
                'system' => 'terai',
                'order' => 7
            ],
            // Metric units for reference
            'sq_feet' => [
                'name' => 'Square Feet',
                'name_nepali' => 'वर्ग फिट',
                'sq_feet' => 1,
                'system' => 'metric',
                'order' => 8
            ]
        ];
    }

    /**
     * Get calculator information
     */
    public function getCalculatorInfo(): array
    {
        $userCountry = $this->geolocationService->getUserCountry();
        
        return [
            'name' => 'Traditional Nepali Units Calculator',
            'version' => '1.0.0',
            'description' => 'Convert between traditional Nepali land measurement units',
            'base_unit' => $this->baseUnit,
            'supported_units' => array_keys($this->traditionalUnits),
            'nepali_user' => $userCountry['is_nepali_user'],
            'user_country' => $userCountry['country_name'],
            'available_languages' => ['en', 'ne']
        ];
    }

    /**
     * Convert between traditional units
     */
    public function convertBetweenUnits(float $inputValue, string $fromUnit, string $toUnit): array
    {
        try {
            // Validate units
            if (!isset($this->traditionalUnits[$fromUnit]) || !isset($this->traditionalUnits[$toUnit])) {
                return [
                    'success' => false,
                    'error' => 'Invalid unit. Supported units: ' . implode(', ', array_keys($this->traditionalUnits))
                ];
            }

            // Convert to Square Feet first
            $sqFeetValue = $inputValue * $this->traditionalUnits[$fromUnit]['sq_feet'];
            
            // Convert from Square Feet to target unit
            $outputValue = $sqFeetValue / $this->traditionalUnits[$toUnit]['sq_feet'];
            
            return [
                'success' => true,
                'input_value' => $inputValue,
                'input_unit' => $fromUnit,
                'input_unit_name' => $this->traditionalUnits[$fromUnit]['name'],
                'input_unit_name_nepali' => $this->traditionalUnits[$fromUnit]['name_nepali'],
                'output_value' => round($outputValue, 6),
                'output_unit' => $toUnit,
                'output_unit_name' => $this->traditionalUnits[$toUnit]['name'],
                'output_unit_name_nepali' => $this->traditionalUnits[$toUnit]['name_nepali'],
                'system' => $this->traditionalUnits[$toUnit]['system'] ?? 'hilly',
                'sq_feet_value' => $sqFeetValue
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Conversion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Convert to metric units
     */
    public function convertToMetric(float $inputValue, string $fromUnit, string $metricUnit = 'sq_feet'): array
    {
        try {
            if (!isset($this->traditionalUnits[$fromUnit])) {
                return [
                    'success' => false,
                    'error' => 'Invalid unit. Supported units: ' . implode(', ', array_keys($this->traditionalUnits))
                ];
            }

            // Convert to Square Feet first
            $sqFeetValue = $inputValue * $this->traditionalUnits[$fromUnit]['sq_feet'];
            
            // Handle target metric units based on Square Feet
            $outputValue = $sqFeetValue;
            $outputUnitName = 'Square Feet';
            
            switch ($metricUnit) {
                case 'sq_meter':
                    $outputValue = $sqFeetValue * 0.092903;
                    $outputUnitName = 'Square Meters';
                    break;
                case 'sq_yard':
                    $outputValue = $sqFeetValue * 0.111111;
                    $outputUnitName = 'Square Yards';
                    break;
                case 'acre':
                    $outputValue = $sqFeetValue / 43560;
                    $outputUnitName = 'Acres';
                    break;
                case 'hectare':
                    $outputValue = $sqFeetValue / 107639;
                    $outputUnitName = 'Hectares';
                    break;
                default: // sq_feet
                    $outputValue = $sqFeetValue;
                    $outputUnitName = 'Square Feet';
                    break;
            }
            
            return [
                'success' => true,
                'input_value' => $inputValue,
                'input_unit' => $fromUnit,
                'input_unit_name' => $this->traditionalUnits[$fromUnit]['name'],
                'input_unit_name_nepali' => $this->traditionalUnits[$fromUnit]['name_nepali'],
                'output_value' => round($outputValue, 6),
                'output_unit' => $metricUnit,
                'output_unit_name' => $outputUnitName,
                'sq_feet_value' => $sqFeetValue
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Metric conversion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Convert from metric to traditional units
     */
    public function convertFromMetric(float $metricValue, string $metricUnit, string $toUnit): array
    {
        try {
            if (!isset($this->traditionalUnits[$toUnit])) {
                return [
                    'success' => false,
                    'error' => 'Invalid traditional unit. Supported units: ' . implode(', ', array_keys($this->traditionalUnits))
                ];
            }

            // Value is already in metric (sq_feet)
            $sqFeetValue = $metricValue;
            
            // Convert to target traditional unit
            $traditionalValue = $sqFeetValue / $this->traditionalUnits[$toUnit]['sq_feet'];
            
            return [
                'success' => true,
                'input_value' => $metricValue,
                'input_unit' => $metricUnit,
                'input_unit_name' => $this->getMetricUnitName($metricUnit),
                'output_value' => round($traditionalValue, 6),
                'output_unit' => $toUnit,
                'output_unit_name' => $this->traditionalUnits[$toUnit]['name'],
                'output_unit_name_nepali' => $this->traditionalUnits[$toUnit]['name_nepali'],
                'sq_feet_value' => $sqFeetValue
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Traditional conversion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all possible conversions for a given value and unit
     */
    public function getAllConversions(float $inputValue, string $fromUnit, string $metricUnit = 'sq_feet'): array
    {
        $result = [
            'success' => true,
            'input_value' => $inputValue,
            'input_unit' => $fromUnit,
            'conversions' => []
        ];

        // Convert to all traditional units
        foreach ($this->traditionalUnits as $unit => $unitData) {
            $conversion = $this->convertBetweenUnits($inputValue, $fromUnit, $unit);
            if ($conversion['success']) {
                $result['conversions']['traditional'][$unit] = $conversion;
            }
        }

        // Convert to metric unit
        $metricConversion = $this->convertToMetric($inputValue, $fromUnit, $metricUnit);
        if ($metricConversion['success']) {
            $result['conversions']['metric'] = $metricConversion;
        }

        return $result;
    }

    /**
     * Get unit information
     */
    public function getUnitInfo(string $unit): ?array
    {
        return $this->traditionalUnits[$unit] ?? null;
    }

    /**
     * Get all available units
     */
    public function getAvailableUnits(): array
    {
        return $this->traditionalUnits;
    }

    /**
     * Get supported metric units
     */
    public function getMetricUnits(): array
    {
        return [
            'sq_feet' => 'Square Feet',
            'sq_meter' => 'Square Meters',
            'sq_yard' => 'Square Yards',
            'acre' => 'Acres',
            'hectare' => 'Hectares'
        ];
    }

    /**
     * Get metric unit name
     */
    private function getMetricUnitName(string $metricUnit): string
    {
        $metricUnits = $this->getMetricUnits();
        return $metricUnits[$metricUnit] ?? $metricUnit;
    }

    /**
     * Format conversion result for display
     */
    public function formatConversionResult(array $result, string $language = 'en'): string
    {
        if (!$result['success']) {
            return "Error: " . $result['error'];
        }

        $fromUnitName = $language === 'ne' ? $result['input_unit_name_nepali'] : $result['input_unit_name'];
        $toUnitName = $language === 'ne' ? $result['output_unit_name_nepali'] : $result['output_unit_name'];

        return sprintf(
            "%.6f %s = %.6f %s",
            $result['input_value'],
            $fromUnitName,
            $result['output_value'],
            $toUnitName
        );
    }

    /**
     * Calculate area equivalence
     */
    public function calculateAreaEquivalence(float $value, string $unit, string $targetUnit): array
    {
        $conversion = $this->convertBetweenUnits($value, $unit, $targetUnit);
        
        if (!$conversion['success']) {
            return $conversion;
        }

        // Add equivalence information
        $conversion['equivalence'] = sprintf(
            "%.6f %s is equivalent to %.6f %s",
            $value,
            $this->traditionalUnits[$unit]['name'],
            $conversion['output_value'],
            $this->traditionalUnits[$targetUnit]['name']
        );

        return $conversion;
    }
}
