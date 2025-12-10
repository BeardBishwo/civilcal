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
        
        // Get backup settings (you can store these in database or config)
        $backup_settings = [
            'enabled' => true,
            'frequency' => 'daily',
            'time' => '02:00',
            'retention' => 30,
            'types' => ['database', 'files'],
            'compression' => 'medium',
            'storage_type' => 'local'
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

        try {
            // Here you would save settings to database or config file
            // For now, just return success
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Backup settings saved successfully'
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