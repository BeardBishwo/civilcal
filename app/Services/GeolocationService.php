<?php

namespace App\Services;

use Exception;
use MaxMind\Db\Reader;

/**
 * GeolocationService - IP-based location detection using MaxMind GeoLite2 database
 * 
 * This service provides automatic detection of user location based on IP address
 * and enables location-based features for the Bishwo Calculator.
 */
class GeolocationService
{
    private $cityDbPath;
    private $countryDbPath;
    private $reader;
    private $isEnabled;
    private $defaultCountry = 'US';

    /**
     * Constructor
     * 
     * @param string|null $cityDbPath Optional custom path to GeoLite2-City database
     */
    public function __construct($cityDbPath = null)
    {
        $this->cityDbPath = $cityDbPath ?? __DIR__ . '/../../storage/app/GeoLite2-City.mmdb';
        $this->countryDbPath = __DIR__ . '/../../storage/app/GeoLite2-Country.mmdb';
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
        // Check if city database file exists (primary)
        if (!file_exists($this->cityDbPath)) {
            error_log("GeoLite2 City database not found at: {$this->cityDbPath}");
            return false;
        }

        // MaxMind DB reader doesn't require specific PHP extension
        // We can use the composer package directly
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
            // Initialize MaxMind DB reader for city database
            $this->reader = new Reader($this->cityDbPath);
        } catch (Exception $e) {
            error_log("Failed to initialize GeoLite2 database: " . $e->getMessage());
            $this->isEnabled = false;
            $this->reader = null;
        }
    }

    /**
     * Get detailed location information including city, region, country, timezone
     * 
     * @param string|null $ipAddress Optional specific IP address
     * @return array
     */
    public function getLocationDetails($ipAddress = null)
    {
        $ip = $ipAddress ?? $this->getClientIP();
        
        $result = [
            'ip_address' => $ip,
            'country' => 'United States',
            'country_code' => 'US',
            'region' => 'California',
            'city' => 'San Francisco',
            'timezone' => 'America/Los_Angeles',
            'latitude' => 37.7749,
            'longitude' => -122.4194,
            'is_enabled' => $this->isEnabled,
            'detection_method' => $this->isEnabled ? 'maxmind' : 'fallback'
        ];

        if (!$this->isEnabled || !$this->reader) {
            // Fallback to online service
            return $this->getLocationFromOnlineService($ip, $result);
        }

        try {
            // Use MaxMind GeoLite2 City database
            $record = $this->reader->get($ip);
            
            if ($record) {
                // Country information
                if (isset($record['country'])) {
                    $result['country'] = $record['country']['names']['en'] ?? $result['country'];
                    $result['country_code'] = $record['country']['iso_code'] ?? $result['country_code'];
                }
                
                // Region/State information
                if (isset($record['subdivisions'][0])) {
                    $result['region'] = $record['subdivisions'][0]['names']['en'] ?? $result['region'];
                }
                
                // City information
                if (isset($record['city'])) {
                    $result['city'] = $record['city']['names']['en'] ?? $result['city'];
                }
                
                // Location coordinates
                if (isset($record['location'])) {
                    $result['latitude'] = $record['location']['latitude'] ?? $result['latitude'];
                    $result['longitude'] = $record['location']['longitude'] ?? $result['longitude'];
                    $result['timezone'] = $record['location']['time_zone'] ?? $result['timezone'];
                }
            }
        } catch (Exception $e) {
            error_log("GeoLocation MaxMind error: " . $e->getMessage());
            // Fallback to online service
            $result = $this->getLocationFromOnlineService($ip, $result);
        }

        return $result;
    }

    /**
     * Get user's country based on IP address (legacy method)
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
        try {
            // Use MaxMind GeoIP2 library
            if (class_exists('\MaxMind\Db\Reader')) {
                $reader = new \MaxMind\Db\Reader($this->cityDbPath);
                $record = $reader->get($ip);
                
                if ($record && isset($record['country'])) {
                    return [
                        'country_code' => $record['country']['iso_code'],
                        'country_name' => $record['country']['names']['en']
                    ];
                }
            }
        } catch (Exception $e) {
            error_log("MaxMind GeoIP2 error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Get detailed location from online IP geolocation service (fallback)
     * 
     * @param string $ip
     * @param array $defaultResult
     * @return array
     */
    private function getLocationFromOnlineService($ip, $defaultResult)
    {
        try {
            // Use free IP geolocation service with detailed location data
            $service = "http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,timezone,lat,lon";
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
                    $defaultResult['country'] = $data['country'] ?? $defaultResult['country'];
                    $defaultResult['country_code'] = $data['countryCode'] ?? $defaultResult['country_code'];
                    $defaultResult['region'] = $data['regionName'] ?? $defaultResult['region'];
                    $defaultResult['city'] = $data['city'] ?? $defaultResult['city'];
                    $defaultResult['timezone'] = $data['timezone'] ?? $defaultResult['timezone'];
                    $defaultResult['latitude'] = $data['lat'] ?? $defaultResult['latitude'];
                    $defaultResult['longitude'] = $data['lon'] ?? $defaultResult['longitude'];
                    $defaultResult['detection_method'] = 'online_service';
                }
            }
        } catch (Exception $e) {
            error_log("Online geolocation service error: " . $e->getMessage());
        }

        return $defaultResult;
    }

    /**
     * Get country from online IP geolocation service (fallback) - legacy
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
            'city_database_path' => $this->cityDbPath,
            'city_database_exists' => file_exists($this->cityDbPath),
            'maxmind_reader_available' => class_exists('\MaxMind\Db\Reader'),
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
