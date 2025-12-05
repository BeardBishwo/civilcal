<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\BackupService;
use App\Core\Auth;
use Exception;

class BackupController extends Controller
{
    private $backupService;

    public function __construct()
    {
        parent::__construct();
<<<<<<< HEAD
            }
=======
        $this->backupService = new BackupService();
    }
>>>>>>> temp-branch

    public function index()
    {
        // Debug output
        error_log("BackupController@index called");
        
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $backups = $this->backupService->getBackupList();
        
        $data = [
            'user' => $user,
            'backups' => $backups,
            'page_title' => 'Backup Management - Admin Panel',
            'currentPage' => 'backup'
        ];

        $this->view->render('admin/backup/index', $data);
    }
<<<<<<< HEAD
}
=======

    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Handle JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        $includeDatabase = $input['include_database'] ?? $_POST['include_database'] ?? true;
        $includeFiles = $input['include_files'] ?? $_POST['include_files'] ?? true;
        $backupName = $input['name'] ?? $_POST['name'] ?? null;

        $result = $this->backupService->createBackup($includeDatabase, $includeFiles, $backupName);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function download($backupName)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $backupPath = BASE_PATH . '/storage/backups/' . $backupName;

        if (file_exists($backupPath)) {
            // Set headers for download
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($backupPath) . '"');
            header('Content-Length: ' . filesize($backupPath));
            
            // Output file content
            readfile($backupPath);
            exit;
        } else {
            http_response_code(404);
            echo 'Backup file not found';
        }
    }

    public function delete($backupName)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $result = $this->backupService->deleteBackup($backupName);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            // Redirect back to backup management page
            header('Location: ' . app_base_url('/admin/backup'));
            exit;
        }
    }

    public function restore($backupName)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $result = $this->backupService->restoreBackup(BASE_PATH . '/storage/backups/' . $backupName);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function schedule()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $schedule = $_POST['schedule'] ?? 'daily';
        $retention = $_POST['retention'] ?? 7;

        $result = $this->backupService->scheduleBackup($schedule, $retention);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function settings()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Handle JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($input && isset($input['max_backup_size'])) {
            try {
                $this->backupService->setMaxBackupSize($input['max_backup_size']);
                $result = [
                    'success' => true,
                    'message' => 'Backup settings saved successfully'
                ];
            } catch (Exception $e) {
                $result = [
                    'success' => false,
                    'message' => 'Error saving backup settings: ' . $e->getMessage()
                ];
            }
        } else {
            $result = [
                'success' => false,
                'message' => 'Invalid input'
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
>>>>>>> temp-branch
