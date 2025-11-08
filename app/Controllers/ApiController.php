<?php
namespace App\Controllers;

use App\Calculators\TraditionalUnitsCalculator;
use App\Services\GeolocationService;

/**
 * ApiController - API endpoints for the Bishwo Calculator system
 * 
 * This controller handles AJAX API requests for calculator operations
 */
class ApiController {
    
    private $calculator;
    private $geolocationService;
    
    public function __construct() {
        $this->calculator = new TraditionalUnitsCalculator();
        $this->geolocationService = new GeolocationService();
    }
    
    /**
     * Convert between traditional units
     */
    public function traditionalUnitsConvert() {
        header('Content-Type: application/json');
        
        try {
            // Get input data
            $input = json_decode(file_get_contents('php://input'), true);
            $value = floatval($input['value'] ?? 0);
            $fromUnit = $input['from_unit'] ?? '';
            $toUnit = $input['to_unit'] ?? '';
            
            // Validate input
            if (!$value || !$fromUnit || !$toUnit) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Missing required parameters'
                ]);
                return;
            }
            
            // Perform conversion
            $result = $this->calculator->convertBetweenUnits($value, $fromUnit, $toUnit);
            
            // If conversion failed, try metric conversion
            if (!$result['success']) {
                $metricUnits = ['sq_feet', 'sq_meter', 'sq_yard'];
                
                if (in_array($toUnit, $metricUnits)) {
                    $result = $this->calculator->convertToMetric($value, $fromUnit, $toUnit);
                } elseif (in_array($fromUnit, $metricUnits)) {
                    $result = $this->calculator->convertFromMetric($value, $fromUnit, $toUnit);
                }
            }
            
            if ($result['success']) {
                // Add user location info
                $countryData = $this->geolocationService->getUserCountry();
                $result['user_country'] = $countryData['country_name'];
                $result['is_nepali_user'] = $countryData['is_nepali_user'];
                $result['detection_method'] = $countryData['detection_method'];
            }
            
            echo json_encode($result);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Conversion failed: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get all conversions for a value
     */
    public function traditionalUnitsAllConversions() {
        header('Content-Type: application/json');
        
        try {
            // Get input data
            $input = json_decode(file_get_contents('php://input'), true);
            $value = floatval($input['value'] ?? 0);
            $fromUnit = $input['from_unit'] ?? '';
            $metricUnit = $input['metric_unit'] ?? 'sq_feet';
            
            // Validate input
            if (!$value || !$fromUnit) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Missing required parameters'
                ]);
                return;
            }
            
            // Get all conversions
            $result = $this->calculator->getAllConversions($value, $fromUnit, $metricUnit);
            
            // Add user location info
            $countryData = $this->geolocationService->getUserCountry();
            $result['user_country'] = $countryData['country_name'];
            $result['is_nepali_user'] = $countryData['is_nepali_user'];
            $result['detection_method'] = $countryData['detection_method'];
            
            echo json_encode($result);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to get conversions: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Calculate endpoint for general calculators
     */
    public function calculate($calculator) {
        header('Content-Type: application/json');
        
        try {
            // Get input data
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Route to appropriate calculator
            $result = $this->routeCalculation($calculator, $input);
            
            echo json_encode($result);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Calculation failed: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Route calculation to appropriate calculator
     */
    private function routeCalculation($calculator, $input) {
        switch ($calculator) {
            case 'traditional-units':
                return $this->calculator->convertBetweenUnits(
                    floatval($input['value'] ?? 0),
                    $input['from_unit'] ?? '',
                    $input['to_unit'] ?? ''
                );
            
            case 'civil-concrete':
                return $this->calculateConcrete($input);
            
            case 'electrical-load':
                return $this->calculateElectricalLoad($input);
            
            // Add more calculator cases as needed
            
            default:
                return [
                    'success' => false,
                    'error' => 'Unknown calculator: ' . $calculator
                ];
        }
    }
    
    /**
     * Calculate concrete requirements
     */
    private function calculateConcrete($input) {
        // Basic concrete calculation
        $length = floatval($input['length'] ?? 0);
        $width = floatval($input['width'] ?? 0);
        $thickness = floatval($input['thickness'] ?? 0);
        
        if ($length > 0 && $width > 0 && $thickness > 0) {
            $volume = ($length * $width * $thickness) / 27; // Convert to cubic yards
            $bags = $volume * 8; // Approximate bags per cubic yard
            
            return [
                'success' => true,
                'volume' => round($volume, 2),
                'volume_unit' => 'cubic_yards',
                'bags_60lb' => round($bags, 0),
                'bags_80lb' => round($bags * 0.75, 0)
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Invalid concrete parameters'
        ];
    }
    
    /**
     * Calculate electrical load
     */
    private function calculateElectricalLoad($input) {
        // Basic electrical load calculation
        $circuits = intval($input['circuits'] ?? 0);
        $average_watts = floatval($input['average_watts'] ?? 1500);
        $concurrent_usage = floatval($input['concurrent_usage'] ?? 0.8);
        
        if ($circuits > 0) {
            $total_load = $circuits * $average_watts * $concurrent_usage;
            $current_240v = $total_load / 240;
            $current_120v = $total_load / 120;
            
            return [
                'success' => true,
                'total_load_watts' => round($total_load, 0),
                'current_120v' => round($current_120v, 1),
                'current_240v' => round($current_240v, 1),
                'recommendation' => $current_240v > 100 ? 'Consider split load panel' : 'Standard panel adequate'
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Invalid electrical parameters'
        ];
    }
    
    /**
     * Get geolocation information
     */
    public function getGeolocation() {
        header('Content-Type: application/json');
        
        try {
            $countryData = $this->geolocationService->getUserCountry();
            
            echo json_encode([
                'success' => true,
                'data' => $countryData
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to get geolocation: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Render widgets
     */
    public function renderWidgets() {
        header('Content-Type: application/json');
        
        try {
            require_once __DIR__ . '/../Services/WidgetManager.php';
            require_once __DIR__ . '/../Widgets/TraditionalUnitsWidget.php';
            
            $widgetManager = new \App\Services\WidgetManager();
            $rendered = $widgetManager->renderWidgets();
            
            echo json_encode([
                'success' => true,
                'widgets' => $rendered
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to render widgets: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update widget setting
     */
    public function updateWidgetSetting($id) {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $setting = $input['setting'] ?? '';
            $value = $input['value'] ?? null;
            
            // This would typically update the widget setting in the database
            // For now, just return success
            
            echo json_encode([
                'success' => true,
                'message' => 'Widget setting updated'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update setting: ' . $e->getMessage()
            ]);
        }
    }
}
?>
