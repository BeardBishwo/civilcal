<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Services\GeoLocationService;
use Exception;

class LocationController extends Controller
{
    /**
     * Get user location based on IP address
     */
    public function getLocation()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        
        try {
            $geoService = new GeoLocationService();
            $location = $geoService->getLocationDetails();
            
            echo json_encode([
                'success' => true,
                'location' => $location
            ]);
            
        } catch (Exception $e) {
            error_log('Location detection error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Unable to detect location',
                'fallback' => [
                    'country' => 'United States',
                    'country_code' => 'US',
                    'region' => 'California', 
                    'city' => 'San Francisco',
                    'timezone' => 'America/Los_Angeles'
                ]
            ]);
        }
    }
    
    /**
     * Get geolocation service status
     */
    public function getStatus()
    {
        header('Content-Type: application/json');
        
        try {
            $geoService = new GeoLocationService();
            $status = $geoService->getStatus();
            
            echo json_encode([
                'success' => true,
                'status' => $status
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Unable to get service status'
            ]);
        }
    }
}
?>
