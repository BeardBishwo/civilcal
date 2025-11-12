<?php
namespace App\Controllers\Admin;

use App\Core\Controller;

class AuditLogController extends Controller
{
    public function index()
    {
        $logsDir = (defined('STORAGE_PATH') ? STORAGE_PATH : (defined('BASE_PATH') ? BASE_PATH . '/storage' : __DIR__ . '/../../..')) . '/logs';
        $files = glob($logsDir . '/audit-*.log') ?: [];
        usort($files, function($a,$b){ return strcmp($b, $a); });
        $dates = array_map(function($f){ return substr(basename($f), 6, 10); }, $files);
        $selected = $_GET['date'] ?? ($dates[0] ?? date('Y-m-d'));
        $level = strtoupper(trim($_GET['level'] ?? ''));
        $q = trim($_GET['q'] ?? '');
        $perPage = max(1, min(200, intval($_GET['per_page'] ?? 50)));
        $page = max(1, intval($_GET['page'] ?? 1));

        $filePath = $logsDir . '/audit-' . $selected . '.log';
        $entries = [];
        if (is_file($filePath)) {
            $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($lines as $line) {
                $obj = json_decode($line, true);
                if (!is_array($obj)) { continue; }
                if ($level && strtoupper($obj['level'] ?? '') !== $level) { continue; }
                if ($q) {
                    $hay = ($obj['action'] ?? '') . ' ' . json_encode($obj['details'] ?? []);
                    if (stripos($hay, $q) === false) { continue; }
                }
                $entries[] = $obj;
            }
        }
        $total = count($entries);
        $offset = ($page - 1) * $perPage;
        $paged = array_slice($entries, $offset, $perPage);

        $data = [
            'currentPage' => 'audit-logs',
            'entries' => $paged,
            'dates' => $dates,
            'selectedDate' => $selected,
            'level' => $level,
            'q' => $q,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'title' => 'Audit Logs - Admin Panel'
        ];
        $this->adminView('admin/audit/index', $data);
    }

    public function download()
    {
        $logsDir = (defined('STORAGE_PATH') ? STORAGE_PATH : (defined('BASE_PATH') ? BASE_PATH . '/storage' : __DIR__ . '/../../..')) . '/logs';
        $date = $_GET['date'] ?? date('Y-m-d');
        $file = $logsDir . '/audit-' . $date . '.log';
        if (!is_file($file)) {
            http_response_code(404);
            echo 'Not found';
            return;
        }
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }
}
