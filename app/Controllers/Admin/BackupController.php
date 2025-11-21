<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\BackupService;
use App\Core\Auth;

class BackupController extends Controller
{
    private $backupService;

    public function __construct()
    {
        parent::__construct();
        $this->backupService = new BackupService();
    }

    public function index()
    {
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

    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $includeDatabase = $_POST['include_database'] ?? true;
        $includeFiles = $_POST['include_files'] ?? true;
        $backupName = $_POST['name'] ?? null;

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
}