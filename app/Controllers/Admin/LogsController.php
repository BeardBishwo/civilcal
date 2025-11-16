<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class LogsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkAdminAccess();
    }

    public function index()
    {
        $logPath = BASE_PATH . '/storage/logs';
        $logFiles = [];
        
        if (is_dir($logPath)) {
            $files = scandir($logPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                    $filePath = $logPath . '/' . $file;
                    $logFiles[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'modified' => filemtime($filePath),
                        'path' => $filePath
                    ];
                }
            }
        }

        // Sort by modified time (newest first)
        usort($logFiles, fn($a, $b) => $b['modified'] - $a['modified']);

        $data = [
            'page_title' => 'System Logs',
            'logFiles' => $logFiles
        ];

        $this->view('admin/logs/index', $data);
    }

    public function download($filename)
    {
        $logPath = BASE_PATH . '/storage/logs';
        $filePath = $logPath . '/' . basename($filename);
        
        // Security check - ensure file exists and is in the correct directory
        if (!file_exists($filePath) || dirname(realpath($filePath)) !== realpath($logPath)) {
            http_response_code(404);
            echo 'File not found';
            return;
        }
        
        // Set headers for file download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Content-Length: ' . filesize($filePath));
        
        // Output file contents
        readfile($filePath);
        exit;
    }

    public function viewLog($filename)
    {
        $logPath = BASE_PATH . '/storage/logs';
        $filePath = $logPath . '/' . basename($filename);
        
        // Security check - ensure file exists and is in the correct directory
        if (!file_exists($filePath) || dirname(realpath($filePath)) !== realpath($logPath)) {
            http_response_code(404);
            echo 'File not found';
            return;
        }
        
        // Read file contents
        $content = file_get_contents($filePath);
        
        $data = [
            'page_title' => 'View Log - ' . basename($filename),
            'filename' => basename($filename),
            'content' => $content
        ];

        $this->view('admin/logs/view', $data);
    }

    private function checkAdminAccess()
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            redirect('/login');
            exit;
        }
    }
}
