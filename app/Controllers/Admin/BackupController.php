<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class BackupController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkAdminAccess();
    }

    public function index()
    {
        $backupPath = BASE_PATH . '/storage/backups';
        $backups = [];
        
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        
        if (is_dir($backupPath)) {
            $files = scandir($backupPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $filePath = $backupPath . '/' . $file;
                    $backups[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'created' => filemtime($filePath),
                        'path' => $filePath
                    ];
                }
            }
        }

        // Sort by created time (newest first)
        usort($backups, fn($a, $b) => $b['created'] - $a['created']);

        $data = [
            'page_title' => 'Database Backup',
            'backups' => $backups
        ];

        $this->view('admin/backup/index', $data);
    }

    private function checkAdminAccess()
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            redirect('/login');
            exit;
        }
    }
}
