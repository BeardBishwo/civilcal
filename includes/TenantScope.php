<?php
class TenantScope {
    /**
     * Add tenant_id to a database query
     */
    public static function addTenantScope(string $sql, array &$params): string {
        if (!isset($_SESSION['tenant_id'])) {
            throw new Exception('No tenant context available');
        }
        
        // Simple WHERE addition if no WHERE exists
        if (stripos($sql, 'WHERE') === false) {
            return $sql . ' WHERE tenant_id = :tenant_id';
        }
        
        // Add AND tenant_id if WHERE exists
        return preg_replace('/WHERE/i', 'WHERE tenant_id = :tenant_id AND', $sql, 1);
    }

    /**
     * Add tenant_id parameter to the params array
     */
    public static function addTenantParam(array &$params): void {
        if (!isset($_SESSION['tenant_id'])) {
            throw new Exception('No tenant context available');
        }
        
        $params[':tenant_id'] = $_SESSION['tenant_id'];
    }

    /**
     * Check if data belongs to current tenant
     */
    public static function verifyTenantAccess($data): bool {
        if (!isset($_SESSION['tenant_id'])) {
            return false;
        }
        
        if (is_array($data)) {
            return isset($data['tenant_id']) && $data['tenant_id'] === $_SESSION['tenant_id'];
        }
        
        if (is_object($data)) {
            return isset($data->tenant_id) && $data->tenant_id === $_SESSION['tenant_id'];
        }
        
        return false;
    }

    /**
     * Add tenant_id to data array/object before saving
     */
    public static function addTenantId(&$data): void {
        if (!isset($_SESSION['tenant_id'])) {
            throw new Exception('No tenant context available');
        }
        
        if (is_array($data)) {
            $data['tenant_id'] = $_SESSION['tenant_id'];
        } elseif (is_object($data)) {
            $data->tenant_id = $_SESSION['tenant_id'];
        }
    }

    /**
     * Get current tenant storage usage
     */
    public static function getTenantStorageUsage(): int {
        if (!isset($_SESSION['tenant_id'])) {
            throw new Exception('No tenant context available');
        }

        try {
            $pdo = get_db();
            $sql = "SELECT SUM(size) as total FROM storage_usage WHERE tenant_id = :tenant_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':tenant_id' => $_SESSION['tenant_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (Exception $e) {
            error_log("Error getting tenant storage usage: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check if tenant has available storage quota
     */
    public static function hasStorageQuota(int $size): bool {
        $usage = self::getTenantStorageUsage();
        return ($usage + $size) <= STORAGE_LIMIT_PER_TENANT;
    }

    /**
     * Record storage usage for tenant
     */
    public static function recordStorageUsage(string $type, int $size, string $reference): void {
        if (!isset($_SESSION['tenant_id'])) {
            throw new Exception('No tenant context available');
        }

        try {
            $pdo = get_db();
            $sql = "INSERT INTO storage_usage (tenant_id, type, size, reference) VALUES (:tenant_id, :type, :size, :reference)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':tenant_id' => $_SESSION['tenant_id'],
                ':type' => $type,
                ':size' => $size,
                ':reference' => $reference
            ]);
        } catch (Exception $e) {
            error_log("Error recording storage usage: " . $e->getMessage());
        }
    }

    /**
     * Filter array to only include items belonging to current tenant
     */
    public static function filterByTenant(array $items): array {
        if (!isset($_SESSION['tenant_id'])) {
            return [];
        }
        
        return array_filter($items, function($item) {
            return self::verifyTenantAccess($item);
        });
    }
}