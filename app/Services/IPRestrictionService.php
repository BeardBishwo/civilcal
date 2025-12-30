<?php

namespace App\Services;

use App\Core\Database;
use App\Services\SettingsService;

class IPRestrictionService
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Check if an IP is allowed to access the application
     * 
     * @param string $ipAddress
     * @return array ['allowed' => bool, 'reason' => string|null]
     */
    public function checkIP($ipAddress)
    {
        // 1. Check if IP restrictions are enabled
        if (SettingsService::get('enable_ip_restrictions', '0') !== '1') {
            return ['allowed' => true, 'reason' => null];
        }

        // 2. Check Whitelist (If whitelist exists, IP MUST be in it)
        $hasWhitelist = $this->hasActiveWhitelist();
        if ($hasWhitelist) {
            if (!$this->isWhitelisted($ipAddress)) {
                return ['allowed' => false, 'reason' => 'IP not in whitelist'];
            }
            // If whitelisted, we can skip blacklist check? Usually yes.
            // But let's check blacklist too for explicit bans?
            // Standard approach: allow if in whitelist, deny otherwise.
            return ['allowed' => true, 'reason' => 'Whitelisted'];
        }

        // 3. Check Blacklist
        $blacklistEntry = $this->getBlacklistEntry($ipAddress);
        if ($blacklistEntry) {
            return ['allowed' => false, 'reason' => $blacklistEntry['reason'] ?? 'IP Blacklisted'];
        }

        // 4. Check Country restriction (if MaxMind/GeoIP is available)
        // This requires GeolocationService integration. 
        // For now, we'll placeholder this or integrate if easy.
        
        return ['allowed' => true, 'reason' => null];
    }
    
    private function hasActiveWhitelist()
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) FROM ip_restrictions 
            WHERE restriction_type = 'whitelist' 
            AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    private function isWhitelisted($ip)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) FROM ip_restrictions 
            WHERE ip_address = ? 
            AND restriction_type = 'whitelist'
            AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute([$ip]);
        return $stmt->fetchColumn() > 0;
    }
    
    private function getBlacklistEntry($ip)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM ip_restrictions 
            WHERE ip_address = ? 
            AND restriction_type = 'blacklist'
            AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute([$ip]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Add an IP to restrictions
     */
    public function addRestriction($ip, $type, $reason = null, $expiresAt = null, $countryCode = null)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO ip_restrictions (ip_address, restriction_type, reason, expires_at, country_code, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$ip, $type, $reason, $expiresAt, $countryCode]);
    }

    /**
     * Remove a restriction
     */
    public function removeRestriction($id)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM ip_restrictions WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get all restrictions
     */
    public function getRestrictions($type = null)
    {
        $sql = "SELECT * FROM ip_restrictions";
        $params = [];
        
        if ($type) {
            $sql .= " WHERE restriction_type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
