<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\BackupService;
use App\Core\Auth;
use App\Core\Database;
use Exception;

class BackupController extends Controller
{
    private $backupService;

    public function __construct()
    {
        parent::__construct();
        
        // Initialize backup service with database connection
        $db = Database::getInstance()->getPdo();
        $this->backupService = new BackupService($db);
    }

    /**
     * Display backup settings page
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Get backup history
        $backup_history = $this->backupService->getBackupHistory();
        
        // Get backup settings from SettingsService
        $settings = \App\Services\SettingsService::getAll('backup');
        
        $backup_settings = [
            'enabled' => ($settings['backup_enabled'] ?? '0') == '1',
            'frequency' => $settings['backup_frequency'] ?? 'daily',
            'time' => $settings['backup_time'] ?? '02:00',
            'retention' => (int)($settings['backup_retention'] ?? 30),
            'types' => isset($settings['backup_types']) ? (is_array($settings['backup_types']) ? $settings['backup_types'] : explode(',', $settings['backup_types'])) : ['database'],
            'compression' => $settings['backup_compression'] ?? 'medium',
            'storage_type' => $settings['backup_storage_type'] ?? 'local'
        ];
        
        // System info
        $system_info = [
            'app_version' => '1.0.0',
            'php_version' => PHP_VERSION,
            'db_version' => 'MySQL',
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_size' => ini_get('upload_max_filesize'),
            'backup_storage_used' => 0
        ];

        $data = [
            'user' => $user,
            'backup_history' => $backup_history,
            'backup_settings' => $backup_settings,
            'system_info' => $system_info,
            'page_title' => 'Backup Settings - Admin Panel',
            'currentPage' => 'settings'
        ];

        $this->view->render('admin/settings/backup', $data);
    }

    /**
     * Create a new backup
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        try {
            // Get backup types from request
            $types = $_POST['types'] ?? ['database'];
            $compression = $_POST['compression'] ?? 'medium';
            
            $result = $this->backupService->createBackup($types, $compression);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Download a backup file
     */
    public function download($backupId)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $backupPath = BASE_PATH . '/storage/backups/' . $backupId . '.zip';

        if (file_exists($backupPath)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($backupPath) . '"');
            header('Content-Length: ' . filesize($backupPath));
            
            readfile($backupPath);
            exit;
        } else {
            http_response_code(404);
            echo 'Backup file not found';
        }
    }

    /**
     * Restore from backup (placeholder - requires careful implementation)
     */
    public function restore()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Restore functionality requires careful implementation and testing'
        ]);
    }

    /**
     * Restore from specific backup ID
     */
    public function restoreFromId($backupId)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Restore functionality requires careful implementation and testing'
        ]);
    }

    /**
     * Delete a backup
     */
    public function delete($backupId)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        try {
            $result = $this->backupService->deleteBackup($backupId);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cleanup old backups
     */
    public function cleanup()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        try {
            $retentionDays = $_POST['retention'] ?? 30;
            $result = $this->backupService->cleanupOldBackups($retentionDays);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Test backup configuration
     */
    public function test()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        try {
            $result = $this->backupService->testConfiguration();
            
            header('Content-Type: application/json');
            echo json_encode($result);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Save backup settings
     */
    public function save()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        try {
            $group = 'backup';
            $updated = 0;

            // Handle checkbox (backup_enabled)
            $backup_enabled = isset($_POST['backup_enabled']) ? '1' : '0';
            if (\App\Services\SettingsService::set('backup_enabled', $backup_enabled, 'string', $group)) {
                $updated++;
            }

            // Define fields to save
            $fields = [
                'backup_frequency',
                'backup_time',
                'backup_retention',
                'backup_compression',
                'backup_storage_type'
            ];

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    if (\App\Services\SettingsService::set($field, $_POST[$field], 'string', $group)) {
                        $updated++;
                    }
                }
            }

            // Handle backup types (array)
            if (isset($_POST['backup_types'])) {
                $types = is_array($_POST['backup_types']) ? implode(',', $_POST['backup_types']) : $_POST['backup_types'];
                if (\App\Services\SettingsService::set('backup_types', $types, 'string', $group)) {
                    $updated++;
                }
            }

            \App\Services\SettingsService::clearCache();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Backup settings saved successfully',
                'updated' => $updated
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate API key for backups
     */
    public function generateApiKey()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        try {
            $apiKey = bin2hex(random_bytes(32));
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'api_key' => $apiKey
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}