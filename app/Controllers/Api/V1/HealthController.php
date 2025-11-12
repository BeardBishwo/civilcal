<?php
namespace App\Controllers\Api\V1;

use App\Core\Controller;
use App\Core\Database;

class HealthController extends Controller
{
    public function health()
    {
        header('Content-Type: application/json');
        try {
            $db = Database::getInstance();
            $pdo = $db->getPdo();
            // Count active plugins
            $activePlugins = 0;
            try {
                $stmt = $pdo->query("SELECT COUNT(*) AS c FROM plugins WHERE is_active = 1");
                $activePlugins = (int)($stmt->fetch()['c'] ?? 0);
            } catch (\Throwable $e) {
                $activePlugins = 0;
            }
            // Active theme name
            $activeTheme = null;
            try {
                $stmt = $pdo->query("SELECT name FROM themes WHERE status = 'active' LIMIT 1");
                $row = $stmt->fetch();
                $activeTheme = $row['name'] ?? null;
            } catch (\Throwable $e) {
                $activeTheme = null;
            }

            echo json_encode([
                'success' => true,
                'status' => 'ok',
                'timestamp' => date('c'),
                'app' => [
                    'name' => 'Bishwo Calculator',
                    'version' => '1.0.0'
                ],
                'env' => [
                    'php' => PHP_VERSION,
                    'debug' => defined('APP_DEBUG') ? (bool)APP_DEBUG : null
                ],
                'metrics' => [
                    'active_plugins' => $activePlugins,
                    'active_theme' => $activeTheme
                ]
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
