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
        $this->traditionalUnits = [
            'dhur' => [
                'name' => 'Dhur',
                'name_nepali' => 'धुर',
                'conversion_to_daam' => 1,
                'metric_unit' => 'sq_feet',
                'metric_conversion' => 1.93,
                'order' => 1
            ],
            'daam' => [
                'name' => 'Daam',
                'name_nepali' => 'दाम',
                'conversion_to_daam' => 1,
                'metric_unit' => 'sq_feet',
                'metric_conversion' => 1.93,
                'order' => 2
            ],
            'paisa' => [
                'name' => 'Paisa',
                'name_nepali' => 'पैसा',
                'conversion_to_daam' => 4,
                'metric_unit' => 'sq_feet',
                'metric_conversion' => 7.72,
                'order' => 3
            ],
            'aana' => [
                'name' => 'Aana',
                'name_nepali' => 'आना',
                'conversion_to_daam' => 16,
                'metric_unit' => 'sq_feet',
                'metric_conversion' => 30.89,
                'order' => 4
            ],
            'kattha' => [
                'name' => 'Kattha',
                'name_nepali' => 'कठ्ठा',
                'conversion_to_daam' => 20,
                'metric_unit' => 'sq_feet',
                'metric_conversion' => 38.61,
                'order' => 5
            ],
            'bigha' => [
                'name' => 'Bigha',
                'name_nepali' => 'बिघा',
                'conversion_to_daam' => 400,
                'metric_unit' => 'sq_feet',
                'metric_conversion' => 729.35,
                'order' => 6
            ],
            'ropani' => [
                'name' => 'Ropani',
                'name_nepali' => 'रोपनी',
                'conversion_to_daam' => 512,
                'metric_unit' => 'sq_feet',
                'metric_conversion' => 988.16,
                'order' => 7
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

            // Convert to base unit (daam)
            $daamValue = $inputValue * $this->traditionalUnits[$fromUnit]['conversion_to_daam'];
            
            // Convert from base unit to target unit
            $outputValue = $daamValue / $this->traditionalUnits[$toUnit]['conversion_to_daam'];
            
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
                'base_unit_value' => $daamValue
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

            // Convert to base unit first
            $daamValue = $inputValue * $this->traditionalUnits[$fromUnit]['conversion_to_daam'];
            
            // Convert to metric unit
            $metricValue = $daamValue * $this->traditionalUnits[$fromUnit]['metric_conversion'];
            
            return [
                'success' => true,
                'input_value' => $inputValue,
                'input_unit' => $fromUnit,
                'input_unit_name' => $this->traditionalUnits[$fromUnit]['name'],
                'input_unit_name_nepali' => $this->traditionalUnits[$fromUnit]['name_nepali'],
                'output_value' => round($metricValue, 6),
                'output_unit' => $metricUnit,
                'output_unit_name' => $this->getMetricUnitName($metricUnit),
                'base_unit_value' => $daamValue
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

            // Convert metric to base unit
            $daamValue = $metricValue / $this->traditionalUnits[$toUnit]['metric_conversion'];
            
            // Convert to target traditional unit
            $traditionalValue = $daamValue / $this->traditionalUnits[$toUnit]['conversion_to_daam'];
            
            return [
                'success' => true,
                'input_value' => $metricValue,
                'input_unit' => $metricUnit,
                'input_unit_name' => $this->getMetricUnitName($metricUnit),
                'output_value' => round($traditionalValue, 6),
                'output_unit' => $toUnit,
                'output_unit_name' => $this->traditionalUnits[$toUnit]['name'],
                'output_unit_name_nepali' => $this->traditionalUnits[$toUnit]['name_nepali'],
                'base_unit_value' => $daamValue
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
