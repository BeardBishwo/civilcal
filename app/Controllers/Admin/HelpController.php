<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class HelpController extends Controller
{
    public function index()
    {
        $systemInfo = $this->getSystemInfo();
        $logs = $this->getSystemLogs();
        
        // Load the help management view
        include __DIR__ . '/../../Views/admin/help/index.php';
    }

    public function clearLogs()
    {
        $result = $this->clearSystemLogs();
        
        echo json_encode($result);
        return;
    }

    public function backupSystem()
    {
        $result = $this->createBackup();
        
        echo json_encode($result);
        return;
    }

    public function exportThemes()
    {
        header('Content-Type: application/json');
        try {
            $themesDir = defined('BASE_PATH') ? BASE_PATH . '/themes' : dirname(__DIR__, 3) . '/themes';
            if (!is_dir($themesDir)) {
                echo json_encode(['success' => false, 'message' => 'Themes directory not found']);
                return;
            }
            $backupDir = (defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 3) . '/storage') . '/backups';
            if (!is_dir($backupDir)) { @mkdir($backupDir, 0755, true); }
            $zipPath = $backupDir . '/themes-export-' . date('Ymd-His') . '.zip';
            $ok = $this->zipDirectory($themesDir, $zipPath);
            echo json_encode($ok ? ['success' => true, 'message' => 'Themes exported', 'path' => $zipPath]
                                  : ['success' => false, 'message' => 'Failed to create zip']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Export failed: ' . $e->getMessage()]);
        }
    }

    public function exportPlugins()
    {
        header('Content-Type: application/json');
        try {
            $pluginsDir = defined('BASE_PATH') ? BASE_PATH . '/plugins/calculator-plugins' : dirname(__DIR__, 3) . '/plugins/calculator-plugins';
            if (!is_dir($pluginsDir)) {
                echo json_encode(['success' => false, 'message' => 'Plugins directory not found']);
                return;
            }
            $backupDir = (defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 3) . '/storage') . '/backups';
            if (!is_dir($backupDir)) { @mkdir($backupDir, 0755, true); }
            $zipPath = $backupDir . '/plugins-export-' . date('Ymd-His') . '.zip';
            $ok = $this->zipDirectory($pluginsDir, $zipPath);
            echo json_encode($ok ? ['success' => true, 'message' => 'Plugins exported', 'path' => $zipPath]
                                  : ['success' => false, 'message' => 'Failed to create zip']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Export failed: ' . $e->getMessage()]);
        }
    }

    public function restore()
    {
        header('Content-Type: application/json');
        try {
            if (!isset($_FILES['restore_zip']) || ($_FILES['restore_zip']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'No restore file uploaded']);
                return;
            }
            $tmp = $_FILES['restore_zip']['tmp_name'];
            $zip = new \ZipArchive();
            $res = $zip->open($tmp);
            if ($res !== true) {
                echo json_encode(['success' => false, 'message' => 'Invalid zip file']);
                return;
            }
            $manifestIndex = $zip->locateName('manifest.json');
            $manifest = $manifestIndex !== false ? json_decode($zip->getFromIndex($manifestIndex), true) : null;
            $hasDb = $zip->locateName('db.sql') !== false;
            $zip->close();
            echo json_encode([
                'success' => true,
                'message' => 'Restore package validated (dry run)',
                'data' => [ 'manifest' => $manifest, 'has_db_sql' => $hasDb ]
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()]);
        }
    }

    private function zipDirectory(string $source, string $zipPath): bool
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return false;
        }
        $source = realpath($source);
        if ($source === false) { return false; }
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            $filePath = realpath($file);
            $localName = ltrim(str_replace($source, '', $filePath), DIRECTORY_SEPARATOR);
            if (is_dir($file)) {
                $zip->addEmptyDir($localName);
            } else {
                $zip->addFile($filePath, $localName);
            }
        }
        return $zip->close();
    }

    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => 'MySQL 8.0+',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'loaded_extensions' => get_loaded_extensions(),
            'system_uptime' => '15 days, 2 hours',
            'last_backup' => '2024-01-14 02:00:00'
        ];
    }

    private function getSystemLogs()
    {
        // Mock data for system logs
        return [
            [
                'level' => 'INFO',
                'message' => 'User login successful: admin',
                'timestamp' => '2024-01-15 14:30:15',
                'ip' => '192.168.1.100'
            ],
            [
                'level' => 'WARNING',
                'message' => 'Failed login attempt for user: testuser',
                'timestamp' => '2024-01-15 14:25:30',
                'ip' => '192.168.1.150'
            ],
            [
                'level' => 'ERROR',
                'message' => 'Database connection timeout',
                'timestamp' => '2024-01-15 13:45:12',
                'ip' => '127.0.0.1'
            ],
            [
                'level' => 'INFO',
                'message' => 'Calculation completed: Concrete Volume',
                'timestamp' => '2024-01-15 13:30:45',
                'ip' => '192.168.1.200'
            ]
        ];
    }

    private function clearSystemLogs()
    {
        try {
            $dir = defined('STORAGE_PATH') ? STORAGE_PATH . '/logs' : (dirname(__DIR__, 3) . '/storage/logs');
            if (!is_dir($dir)) {
                return ['success' => true, 'message' => 'No logs directory found'];
            }
            $deleted = 0;
            foreach (glob($dir . '/*') as $file) {
                if (is_file($file)) {
                    @unlink($file);
                    $deleted++;
                }
            }
            return ['success' => true, 'message' => "Cleared {$deleted} log files"]; 
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to clear logs: ' . $e->getMessage()];
        }
    }

    private function createBackup()
    {
        try {
            $pdo = Database::getInstance()->getPdo();
            $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
            $backupDir = (defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 3) . '/storage') . '/backups';
            if (!is_dir($backupDir)) { @mkdir($backupDir, 0755, true); }
            $file = $backupDir . '/db-backup-' . date('Ymd-His') . '.sql';
            $fh = fopen($file, 'w');
            if (!$fh) { throw new \Exception('Cannot write backup file'); }

            fwrite($fh, "-- Bishwo Calculator SQL Backup\n");
            fwrite($fh, "-- Database: `{$dbName}`\n");
            fwrite($fh, "-- Date: " . date('Y-m-d H:i:s') . "\n\n");
            fwrite($fh, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");

            // List tables
            $tables = [];
            $res = $pdo->query('SHOW TABLES');
            while ($row = $res->fetch(\PDO::FETCH_NUM)) { $tables[] = $row[0]; }

            foreach ($tables as $table) {
                // Drop and create
                $row = $pdo->query('SHOW CREATE TABLE `' . str_replace('`','``',$table) . '`')->fetch(\PDO::FETCH_ASSOC);
                $create = $row['Create Table'] ?? $row['Create View'] ?? null;
                if ($create) {
                    fwrite($fh, "\n-- ----------------------------\n-- Table structure for `{$table}`\n-- ----------------------------\n");
                    fwrite($fh, "DROP TABLE IF EXISTS `{$table}`;\n{$create};\n\n");
                }

                // Data
                $stmt = $pdo->query('SELECT * FROM `' . str_replace('`','``',$table) . '`');
                $cols = [];
                $colRes = $pdo->query('SHOW COLUMNS FROM `' . str_replace('`','``',$table) . '`');
                while ($c = $colRes->fetch(\PDO::FETCH_ASSOC)) { $cols[] = $c['Field']; }
                $colList = '`' . implode('`,`', array_map(function($c){ return str_replace('`','``',$c); }, $cols)) . '`';

                $batch = [];
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $values = [];
                    foreach ($cols as $col) {
                        $val = array_key_exists($col, $row) ? $row[$col] : null;
                        $values[] = is_null($val) ? 'NULL' : $pdo->quote($val);
                    }
                    $batch[] = '(' . implode(',', $values) . ')';
                    if (count($batch) >= 500) {
                        fwrite($fh, "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $batch) . ";\n");
                        $batch = [];
                    }
                }
                if (!empty($batch)) {
                    fwrite($fh, "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $batch) . ";\n");
                }
            }

            fwrite($fh, "\nSET FOREIGN_KEY_CHECKS=1;\n");
            fclose($fh);
            return ['success' => true, 'message' => 'Backup created', 'path' => $file];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Backup failed: ' . $e->getMessage()];
        }
    }
}
?>
