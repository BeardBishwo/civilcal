<?php

namespace App\Services;

use App\Core\Database;
use Exception;

/**
 * SuspiciousActivityDetector - Detects suspicious login patterns
 * 
 * Detection Rules:
 * - Impossible Travel: Logins from different countries within threshold
 * - Rapid Location Changes: Multiple cities in short time
 * - New Device + New Location: First-time device from unknown location
 * - High-Risk Countries: Configurable list of countries to flag
 */
class SuspiciousActivityDetector
{
    private $db;
    private $securityAlertService;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->securityAlertService = new SecurityAlertService();
    }
    
    /**
     * Analyze login for suspicious patterns
     * 
     * @param int $userId
     * @param array $locationData
     * @param array $deviceInfo
     * @return array Analysis results
     */
    public function analyzeLogin($userId, $locationData, $deviceInfo)
    {
        if (!SettingsService::get('enable_suspicious_detection', '1')) {
            return ['suspicious' => false, 'alerts' => []];
        }
        
        $alerts = [];
        $riskLevel = 'low';
        
        // Check for impossible travel
        $impossibleTravel = $this->checkImpossibleTravel($userId, $locationData);
        if ($impossibleTravel) {
            $alerts[] = $impossibleTravel;
            $riskLevel = 'high';
        }
        
        // Check for rapid location changes
        $rapidChanges = $this->checkRapidLocationChanges($userId, $locationData);
        if ($rapidChanges) {
            $alerts[] = $rapidChanges;
            $riskLevel = max($riskLevel, 'medium');
        }
        
        // Check for new device + new location
        $newDeviceLocation = $this->checkNewDeviceAndLocation($userId, $locationData, $deviceInfo);
        if ($newDeviceLocation) {
            $alerts[] = $newDeviceLocation;
            $riskLevel = max($riskLevel, 'medium');
        }
        
        // Check for high-risk country
        $highRiskCountry = $this->checkHighRiskCountry($locationData);
        if ($highRiskCountry) {
            $alerts[] = $highRiskCountry;
            $riskLevel = max($riskLevel, 'medium');
        }
        
        // Create security alerts if suspicious
        if (!empty($alerts)) {
            foreach ($alerts as $alert) {
                $this->securityAlertService->createAlert(
                    $userId,
                    $alert['type'],
                    $riskLevel,
                    $alert['description'],
                    $alert['metadata']
                );
            }
        }
        
        return [
            'suspicious' => !empty($alerts),
            'risk_level' => $riskLevel,
            'alerts' => $alerts
        ];
    }
    
    /**
     * Check for impossible travel (different countries in short time)
     * 
     * @param int $userId
     * @param array $locationData
     * @return array|null
     */
    private function checkImpossibleTravel($userId, $locationData)
    {
        $threshold = (int)SettingsService::get('impossible_travel_threshold', '1');
        $currentCountry = $locationData['country'] ?? null;
        
        if (!$currentCountry) {
            return null;
        }
        
        // Get last login from different country within threshold
        $stmt = $this->db->getPdo()->prepare("
            SELECT country, city, login_time, ip_address
            FROM login_sessions
            WHERE user_id = ? 
            AND country != ? 
            AND country IS NOT NULL
            AND login_time > DATE_SUB(NOW(), INTERVAL ? HOUR)
            ORDER BY login_time DESC
            LIMIT 1
        ");
        
        $stmt->execute([$userId, $currentCountry, $threshold]);
        $previousLogin = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($previousLogin) {
            return [
                'type' => 'impossible_travel',
                'description' => "Login from {$currentCountry} within {$threshold} hour(s) of login from {$previousLogin['country']}",
                'metadata' => [
                    'current_country' => $currentCountry,
                    'previous_country' => $previousLogin['country'],
                    'previous_city' => $previousLogin['city'],
                    'previous_time' => $previousLogin['login_time'],
                    'previous_ip' => $previousLogin['ip_address'],
                    'threshold_hours' => $threshold
                ]
            ];
        }
        
        return null;
    }
    
    /**
     * Check for rapid location changes (3+ cities in 24 hours)
     * 
     * @param int $userId
     * @param array $locationData
     * @return array|null
     */
    private function checkRapidLocationChanges($userId, $locationData)
    {
        // Get distinct cities in last 24 hours
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(DISTINCT city) as city_count,
                   GROUP_CONCAT(DISTINCT city) as cities
            FROM login_sessions
            WHERE user_id = ?
            AND city IS NOT NULL
            AND login_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        
        $stmt->execute([$userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($result && $result['city_count'] >= 3) {
            return [
                'type' => 'rapid_location_changes',
                'description' => "Logins from {$result['city_count']} different cities in 24 hours",
                'metadata' => [
                    'city_count' => $result['city_count'],
                    'cities' => $result['cities'],
                    'timeframe' => '24 hours'
                ]
            ];
        }
        
        return null;
    }
    
    /**
     * Check for new device from new location
     * 
     * @param int $userId
     * @param array $locationData
     * @param array $deviceInfo
     * @return array|null
     */
    private function checkNewDeviceAndLocation($userId, $locationData, $deviceInfo)
    {
        $userAgent = $deviceInfo['user_agent'] ?? '';
        $city = $locationData['city'] ?? null;
        
        if (!$userAgent || !$city) {
            return null;
        }
        
        // Check if this user agent has been seen before
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as count
            FROM login_sessions
            WHERE user_id = ?
            AND user_agent = ?
        ");
        
        $stmt->execute([$userId, $userAgent]);
        $deviceSeen = $stmt->fetchColumn() > 0;
        
        // Check if this city has been seen before
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as count
            FROM user_login_locations
            WHERE user_id = ?
            AND city = ?
        ");
        
        $stmt->execute([$userId, $city]);
        $citySeen = $stmt->fetchColumn() > 0;
        
        // Alert if both device and location are new
        if (!$deviceSeen && !$citySeen) {
            return [
                'type' => 'new_device_new_location',
                'description' => "First login from new device in new location: {$city}",
                'metadata' => [
                    'city' => $city,
                    'country' => $locationData['country'] ?? null,
                    'device_type' => $deviceInfo['device_type'] ?? 'Unknown',
                    'browser' => $deviceInfo['browser'] ?? 'Unknown',
                    'os' => $deviceInfo['os'] ?? 'Unknown'
                ]
            ];
        }
        
        return null;
    }
    
    /**
     * Check if login is from high-risk country
     * 
     * @param array $locationData
     * @return array|null
     */
    private function checkHighRiskCountry($locationData)
    {
        $countryCode = $locationData['country_code'] ?? null;
        
        if (!$countryCode) {
            return null;
        }
        
        $highRiskCountries = json_decode(
            SettingsService::get('high_risk_countries', '[]'),
            true
        );
        
        if (in_array($countryCode, $highRiskCountries)) {
            return [
                'type' => 'high_risk_country',
                'description' => "Login from high-risk country: {$locationData['country']}",
                'metadata' => [
                    'country' => $locationData['country'],
                    'country_code' => $countryCode,
                    'city' => $locationData['city'] ?? null
                ]
            ];
        }
        
        return null;
    }
    
    /**
     * Get recent security alerts for user
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecentAlerts($userId, $limit = 10)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT *
            FROM security_alerts
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all unresolved alerts
     * 
     * @param string $riskLevel Optional filter by risk level
     * @return array
     */
    public function getUnresolvedAlerts($riskLevel = null)
    {
        $sql = "
            SELECT sa.*, u.username, u.email
            FROM security_alerts sa
            LEFT JOIN users u ON sa.user_id = u.id
            WHERE sa.is_resolved = 0
        ";
        
        $params = [];
        
        if ($riskLevel) {
            $sql .= " AND sa.risk_level = ?";
            $params[] = $riskLevel;
        }
        
        $sql .= " ORDER BY sa.created_at DESC";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Resolve a security alert
     * 
     * @param int $alertId
     * @param int $resolvedBy Admin user ID
     * @return bool
     */
    public function resolveAlert($alertId, $resolvedBy)
    {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE security_alerts
            SET is_resolved = 1,
                resolved_by = ?,
                resolved_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([$resolvedBy, $alertId]);
    }
}
