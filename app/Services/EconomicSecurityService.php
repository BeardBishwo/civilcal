<?php

namespace App\Services;

use App\Core\Database;

class EconomicSecurityService
{
    private Database $db;
    private array $resourceConfig;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $config = SettingsService::get('economy_resources', []);
        $this->resourceConfig = is_array($config) ? $config : [];
    }

    /**
     * Validate a purchase request using server-side pricing rules.
     */
    public function validatePurchase(int $userId, string $resource, int $amount): array
    {
        $resource = trim(strtolower($resource));

        if (!preg_match('/^[a-z0-9_]+$/', $resource)) {
            SecurityMonitor::log($userId, 'invalid_purchase_resource_key', $_SERVER['REQUEST_URI'] ?? '', [
                'resource' => $resource,
            ], 'high');

            return [
                'success' => false,
                'message' => 'Invalid resource key.',
            ];
        }

        $amount = max(1, min($amount, 1000));

        $config = $this->getResourceConfig($resource);
        if (!$config || !isset($config['buy']) || (int)$config['buy'] <= 0) {
            SecurityMonitor::log($userId, 'invalid_purchase_resource', $_SERVER['REQUEST_URI'] ?? '', [
                'resource' => $resource,
            ], 'high');

            return [
                'success' => false,
                'message' => 'Resource is not available for purchase.',
            ];
        }

        $unitPrice = (int)$config['buy'];
        $totalCost = $unitPrice * $amount;

        $wallet = $this->getWalletSnapshot($userId);
        if (!$wallet || (int)$wallet['coins'] < $totalCost) {
            return [
                'success' => false,
                'message' => 'Insufficient balance for this transaction.',
            ];
        }

        if (!SecurityMonitor::validateTransaction($userId, $totalCost, $resource)) {
            return [
                'success' => false,
                'message' => 'Transaction blocked by security policy.',
            ];
        }

        return [
            'success' => true,
            'resource' => $resource,
            'resource_label' => $config['name'] ?? null,
            'amount' => $amount,
            'unit_price' => $unitPrice,
            'total_cost' => $totalCost,
            'wallet' => $wallet,
        ];
    }

    /**
     * Validate a sell request against current inventory.
     */
    public function validateSell(int $userId, string $resource, int $amount): array
    {
        $resource = trim(strtolower($resource));

        if (!preg_match('/^[a-z0-9_]+$/', $resource)) {
            SecurityMonitor::log($userId, 'invalid_sell_resource_key', $_SERVER['REQUEST_URI'] ?? '', [
                'resource' => $resource,
            ], 'high');

            return [
                'success' => false,
                'message' => 'Invalid resource key.',
            ];
        }

        $amount = max(1, min($amount, 1000));

        $config = $this->getResourceConfig($resource);
        if (!$config || !isset($config['sell']) || (int)$config['sell'] <= 0) {
            SecurityMonitor::log($userId, 'invalid_sell_resource', $_SERVER['REQUEST_URI'] ?? '', [
                'resource' => $resource,
            ], 'high');

            return [
                'success' => false,
                'message' => 'Resource cannot be sold.',
            ];
        }

        $wallet = $this->getWalletSnapshot($userId, $resource);
        if (!$wallet || (int)$wallet[$resource] < $amount) {
            return [
                'success' => false,
                'message' => 'Not enough resources in inventory.',
            ];
        }

        $unitPrice = (int)$config['sell'];
        $totalGain = $unitPrice * $amount;

        if (!SecurityMonitor::validateTransaction($userId, $totalGain, $resource)) {
            return [
                'success' => false,
                'message' => 'Transaction blocked by security policy.',
            ];
        }

        return [
            'success' => true,
            'resource' => $resource,
            'resource_label' => $config['name'] ?? null,
            'amount' => $amount,
            'unit_price' => $unitPrice,
            'total_gain' => $totalGain,
            'wallet' => $wallet,
        ];
    }

    /**
     * Enforce cooldown on reward payouts to mitigate replay attacks.
     */
    public function canReward(int $userId, int $cooldownSeconds = 30): bool
    {
        $stmt = $this->db->query(
            "SELECT created_at FROM user_resource_logs WHERE user_id = :uid AND source = 'quiz_reward' ORDER BY created_at DESC LIMIT 1",
            ['uid' => $userId]
        );

        $last = $stmt->fetch();
        if (!$last) {
            return true;
        }

        $lastTime = strtotime($last['created_at']);
        if ($lastTime && (time() - $lastTime) < $cooldownSeconds) {
            SecurityMonitor::log($userId, 'reward_cooldown_violation', $_SERVER['REQUEST_URI'] ?? '', [
                'cooldown_seconds' => $cooldownSeconds,
                'last_reward' => $last['created_at'],
            ], 'high');
            return false;
        }

        return true;
    }

    /**
     * Snapshot of user wallet. Optionally hydrate specific resource column.
     */
    private function getWalletSnapshot(int $userId, string $resourceColumn = 'coins'): ?array
    {
        $columns = ['coins', 'bricks', 'cement', 'steel', 'sand', 'wood_logs', 'wood_planks'];
        if (!in_array($resourceColumn, $columns, true)) {
            $columns[] = $resourceColumn;
        }

        $columns = array_filter($columns, fn ($col) => preg_match('/^[a-z0-9_]+$/', $col));

        $select = implode(', ', array_map(fn ($col) => "$col", $columns));
        $stmt = $this->db->query(
            "SELECT $select FROM user_resources WHERE user_id = :uid",
            ['uid' => $userId]
        );

        $wallet = $stmt->fetch();
        return $wallet ?: null;
    }

    private function getResourceConfig(string $resource): ?array
    {
        if (!isset($this->resourceConfig[$resource])) {
            return null;
        }

        return is_array($this->resourceConfig[$resource])
            ? $this->resourceConfig[$resource]
            : null;
    }

    public function getResourceName(string $resource): ?string
    {
        $config = $this->getResourceConfig($resource);
        return $config['name'] ?? null;
    }
}
