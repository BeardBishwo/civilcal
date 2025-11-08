<?php

namespace App\Services;

use Exception;

/**
 * GeolocationService - IP-based country detection using MaxMind GeoLite2 database
 * 
 * This service provides automatic detection of user location based on IP address
 * and enables location-based features for the Bishwo Calculator.
 */
class GeolocationService
{
    private $dbPath;
    private $database;
    private $isEnabled;
    private $defaultCountry = 'US';

    /**
     * Constructor
     * 
     * @param string|null $dbPath Optional custom path to GeoLite2 database
     */
    public function __construct($dbPath = null)
    {
        $this->dbPath = $dbPath ?? storage_path('database/GeoLite2-Country.mmdb');
        $this->isEnabled = $this->checkDatabaseAvailability();
        
        if ($this->isEnabled) {
            $this->initializeDatabase();
        }
    }

    /**
     * Check if GeoLite2 database is available
     * 
     * @return bool
     */
    private function checkDatabaseAvailability()
    {
        // Check if database file exists
        if (!file_exists($this->dbPath)) {
            error_log("GeoLite2 database not found at: {$this->dbPath}");
            return false;
        }

        // Check if required PHP extension is available
        if (!extension_loaded('geoip')) {
            // For MaxMind GeoLite2, we use the geoip extension
            // If not available, we'll use alternative IP geolocation methods
            return false;
        }

        return true;
    }

    /**
     * Initialize MaxMind GeoLite2 database
     * 
     * @throws Exception
     */
    private function initializeDatabase()
    {
        try {
            // Initialize MaxMind GeoLite2 database reader
            // Note: This requires the geoip extension or maxmind-db-reader
            if (extension_loaded('geoip')) {
                // Using built-in geoip extension
                $this->database = true; // geoip_open() equivalent
            } else {
                // Alternative: Use online IP geolocation service as fallback
                $this->database = null;
            }
        } catch (Exception $e) {
            error_log("Failed to initialize GeoLite2 database: " . $e->getMessage());
            $this->isEnabled = false;
        }
    }

    /**
     * Get user's country based on IP address
     * 
     * @param string|null $ipAddress Optional specific IP address
     * @return array
     */
    public function getUserCountry($ipAddress = null)
    {
        $ip = $ipAddress ?? $this->getClientIP();
        
        $result = [
            'country_code' => $this->defaultCountry,
            'country_name' => 'United States',
            'is_enabled' => $this->isEnabled,
            'detection_method' => $this->isEnabled ? 'geoip' : 'fallback',
            'ip_address' => $ip,
            'is_nepali_user' => false
        ];

        if (!$this->isEnabled) {
            // Fallback: Use online IP geolocation service
            return $this->getCountryFromOnlineService($ip, $result);
        }

        try {
            // Use MaxMind GeoLite2 for accurate geolocation
            $countryData = $this->getCountryFromMaxMind($ip);
            
            if ($countryData) {
                $result['country_code'] = $countryData['country_code'];
                $result['country_name'] = $countryData['country_name'];
                $result['is_nepali_user'] = $this->isNepaliUser($countryData['country_code']);
            }
        } catch (Exception $e) {
            error_log("Geolocation error: " . $e->getMessage());
            // Fallback to online service
            $result = $this->getCountryFromOnlineService($ip, $result);
        }

        return $result;
    }

    /**
     * Get country from MaxMind GeoLite2 database
     * 
     * @param string $ip
     * @return array|null
     */
    private function getCountryFromMaxMind($ip)
    {
        if (extension_loaded('geoip')) {
            // Using PHP geoip extension
            $countryCode = geoip_country_code_by_name($ip);
            $countryName = geoip_country_name_by_name($ip);
            
            if ($countryCode) {
                return [
                    'country_code' => $countryCode,
                    'country_name' => $countryName ?: $countryCode
                ];
            }
        }

        return null;
    }

    /**
     * Get country from online IP geolocation service (fallback)
     * 
     * @param string $ip
     * @param array $defaultResult
     * @return array
     */
    private function getCountryFromOnlineService($ip, $defaultResult)
    {
        try {
            // Use free IP geolocation service as fallback
            $service = "http://ip-api.com/json/{$ip}";
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Bishwo-Calculator/1.0'
                ]
            ]);
            
            $response = @file_get_contents($service, false, $context);
            
            if ($response) {
                $data = json_decode($response, true);
                
                if ($data && $data['status'] === 'success') {
                    $defaultResult['country_code'] = $data['countryCode'] ?? $this->defaultCountry;
                    $defaultResult['country_name'] = $data['country'] ?? 'Unknown';
                    $defaultResult['is_nepali_user'] = $this->isNepaliUser($data['countryCode'] ?? '');
                    $defaultResult['detection_method'] = 'online_service';
                }
            }
        } catch (Exception $e) {
            error_log("Online geolocation service error: " . $e->getMessage());
        }

        return $defaultResult;
    }

    /**
     * Check if user is from Nepal
     * 
     * @param string $countryCode
     * @return bool
     */
    public function isNepaliUser($countryCode = null)
    {
        $nepaliCountries = ['NP', 'NPL']; // Nepal country codes
        return in_array(strtoupper($countryCode), $nepaliCountries);
    }

    /**
     * Get client IP address
     * 
     * @return string
     */
    private function getClientIP()
    {
        $ipKeys = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                
                // Handle comma-separated IPs (load balancers)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                // Validate IP format
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback to REMOTE_ADDR if no valid public IP found
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Enable/disable geolocation service
     * 
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->isEnabled = $enabled;
    }

    /**
     * Set default country for fallback
     * 
     * @param string $countryCode
     */
    public function setDefaultCountry($countryCode)
    {
        $this->defaultCountry = strtoupper($countryCode);
    }

    /**
     * Get geolocation service status
     * 
     * @return array
     */
    public function getStatus()
    {
        return [
            'enabled' => $this->isEnabled,
            'database_path' => $this->dbPath,
            'database_exists' => file_exists($this->dbPath),
            'geoip_extension' => extension_loaded('geoip'),
            'default_country' => $this->defaultCountry
        ];
    }

    /**
     * Get user's preferred locale based on country
     * 
     * @param string $countryCode
     * @return string
     */
    public function getLocaleForCountry($countryCode = null)
    {
        if (!$countryCode) {
            $countryData = $this->getUserCountry();
            $countryCode = $countryData['country_code'];
        }

        $localeMap = [
            'NP' => 'ne_NP', // Nepali (Nepal)
            'US' => 'en_US', // English (United States)
            'GB' => 'en_GB', // English (United Kingdom)
            'IN' => 'hi_IN', // Hindi (India)
        ];

        return $localeMap[strtoupper($countryCode)] ?? 'en_US';
    }
}

/**
 * Helper function to get storage path
 * 
 * @param string $path
 * @return string
 */
function storage_path($path = '')
{
    $basePath = __DIR__ . '/../../storage/';
    return rtrim($basePath . ltrim($path, '/'), '/');
}
