<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\AuditLogger;

class HelpController extends Controller
{
    public function index()
    {
        $systemInfo = $this->getSystemInfo();
        $logs = $this->getSystemLogs();
        
        // Load the help management view
        include __DIR__ . '/../../Views/admin/help/index.php';
    }

    public function exportLogs()
    {
        header('Content-Type: application/json');
        try {
            $logsDir = (defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 3) . '/storage') . '/logs';
            if (!is_dir($logsDir)) {
                echo json_encode(['success' => false, 'message' => 'Logs directory not found']);
                return;
            }
            $backupDir = (defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 3) . '/storage') . '/backups';
            if (!is_dir($backupDir)) { @mkdir($backupDir, 0755, true); }
            $zipPath = $backupDir . '/logs-export-' . date('Ymd-His') . '.zip';
            $ok = $this->zipDirectory($logsDir, $zipPath);
            $file = basename($zipPath);
            $download = '/admin/help/download-backup?file=' . rawurlencode($file);
            $resp = $ok ? ['success' => true, 'message' => 'Logs exported', 'path' => $zipPath, 'filename' => $file, 'download_url' => $download]
                        : ['success' => false, 'message' => 'Failed to create zip'];
            AuditLogger::info($ok ? 'logs_exported' : 'logs_export_failed', $ok ? ['path' => $zipPath] : ['message' => 'Failed to create zip']);
            echo json_encode($resp);
        } catch (\Exception $e) {
            AuditLogger::error('logs_export_exception', ['error' => $e->getMessage()]);
            echo json_encode(['success' => false, 'message' => 'Export failed: ' . $e->getMessage()]);
        }
    }

    public function clearLogs()
    {
        $result = $this->clearSystemLogs();
        
        echo json_encode($result);
        return;
    }

    public function backupSystem()
    {
        // Create a full system backup package: db.sql + themes + plugins + manifest.json
        $result = $this->createSystemBackupPackage();
        if ($result['success'] ?? false) {
            AuditLogger::info('system_backup_created', ['path' => $result['path'] ?? null]);
        } else {
            AuditLogger::warning('system_backup_failed', ['message' => $result['message'] ?? null]);
        }
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
            $file = basename($zipPath);
            $download = '/admin/help/download-backup?file=' . rawurlencode($file);
            $resp = $ok ? ['success' => true, 'message' => 'Themes exported', 'path' => $zipPath, 'filename' => $file, 'download_url' => $download]
                        : ['success' => false, 'message' => 'Failed to create zip'];
            AuditLogger::info($ok ? 'themes_exported' : 'themes_export_failed', $ok ? ['path' => $zipPath] : ['message' => 'Failed to create zip']);
            echo json_encode($resp);
        } catch (\Exception $e) {
            AuditLogger::error('themes_export_exception', ['error' => $e->getMessage()]);
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
            $file = basename($zipPath);
            $download = '/admin/help/download-backup?file=' . rawurlencode($file);
            $resp = $ok ? ['success' => true, 'message' => 'Plugins exported', 'path' => $zipPath, 'filename' => $file, 'download_url' => $download]
                        : ['success' => false, 'message' => 'Failed to create zip'];
            AuditLogger::info($ok ? 'plugins_exported' : 'plugins_export_failed', $ok ? ['path' => $zipPath] : ['message' => 'Failed to create zip']);
            echo json_encode($resp);
        } catch (\Exception $e) {
            AuditLogger::error('plugins_export_exception', ['error' => $e->getMessage()]);
            echo json_encode(['success' => false, 'message' => 'Export failed: ' . $e->getMessage()]);
        }
    }

    public function restore()
    {
        header('Content-Type: application/json');
        try {
            if (!isset($_FILES['restore_zip']) || (($_FILES['restore_zip']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK)) {
                echo json_encode(['success' => false, 'message' => 'No restore file uploaded']);
                return;
            }

            $tmpZip = $_FILES['restore_zip']['tmp_name'];
            $zip = new \ZipArchive();
            if ($zip->open($tmpZip) !== true) {
                echo json_encode(['success' => false, 'message' => 'Invalid zip file']);
                return;
            }

            // Zip bomb safeguards
            [ $unzBytes, $unzFiles ] = $this->sumZipUncompressed($zip);
            $maxBytes = 200 * 1024 * 1024; // 200MB
            $maxFiles = 15000;
            if ($unzBytes > $maxBytes || $unzFiles > $maxFiles) {
                $zip->close();
                echo json_encode(['success' => false, 'message' => 'Restore package too large']);
                return;
            }

            $manifestIndex = $zip->locateName('manifest.json');
            $manifest = $manifestIndex !== false ? json_decode($zip->getFromIndex($manifestIndex), true) : null;
            $zip->close();

            $restoreBase = (defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 3) . '/storage');
            $workDir = rtrim($restoreBase, '/\\') . '/tmp/restore-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4));
            @mkdir($workDir, 0755, true);
            if (!$this->extractZipTo($tmpZip, $workDir)) {
                echo json_encode(['success' => false, 'message' => 'Failed to extract restore package']);
                return;
            }

            // Apply DB restore if present
            $dbSql = $workDir . '/db.sql';
            if (is_file($dbSql)) {
                $sql = file_get_contents($dbSql);
                $this->applySql($sql);
            }

            // Copy themes if present
            $themesSrc = $workDir . '/themes';
            $themesDst = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/themes';
            if (is_dir($themesSrc)) {
                $this->copyDirectory($themesSrc, $themesDst);
            }

            // Copy plugins if present
            $pluginsSrc = $workDir . '/plugins/calculator-plugins';
            $pluginsDst = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/plugins/calculator-plugins';
            if (is_dir($pluginsSrc)) {
                $this->copyDirectory($pluginsSrc, $pluginsDst);
            }

            // Cleanup workdir
            $this->rrmdir($workDir);

            $resp = [
                'success' => true,
                'message' => 'System restore completed',
                'data' => [ 'manifest' => $manifest ]
            ];
            AuditLogger::info('system_restore_completed', ['has_manifest' => (bool)$manifest]);
            echo json_encode($resp);
        } catch (\Throwable $e) {
            AuditLogger::error('system_restore_failed', ['error' => $e->getMessage()]);
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
        try {
            $dir = (defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 3) . '/storage') . '/logs';
            $file = $dir . '/' . date('Y-m-d') . '.log';
            if (!is_file($file)) { return []; }
            $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            $lines = array_slice($lines, -50); // last 50
            $out = [];
            foreach ($lines as $line) {
                $obj = json_decode($line, true);
                if (!is_array($obj)) { continue; }
                $out[] = [
                    'level' => strtoupper($obj['level'] ?? 'INFO'),
                    'message' => (string)($obj['message'] ?? ''),
                    'timestamp' => $obj['timestamp'] ?? date('Y-m-d H:i:s'),
                    'ip' => $obj['context']['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? '-')
                ];
            }
            return array_reverse($out); // newest last in file; reverse for UI
        } catch (\Throwable $e) {
            return [];
        }
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

    private function createSystemBackupPackage(): array
    {
        try {
            // Generate DB SQL first
            $db = $this->createBackup();
            if (!($db['success'] ?? false)) {
                return $db;
            }
            $backupDir = (defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 3) . '/storage') . '/backups';
            if (!is_dir($backupDir)) { @mkdir($backupDir, 0755, true); }
            $zipPath = $backupDir . '/system-backup-' . date('Ymd-His') . '.zip';

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                return ['success' => false, 'message' => 'Cannot create backup zip'];
            }

            // Add manifest
            $manifest = [
                'app' => 'Bishwo Calculator',
                'created_at' => date('c'),
                'version' => '1.0.0'
            ];
            $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));

            // Add DB dump
            $zip->addFile($db['path'], 'db.sql');

            // Add themes directory
            $themesDir = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/themes';
            if (is_dir($themesDir)) {
                $this->zipAddDirectory($zip, realpath($themesDir), 'themes');
            }

            // Add plugins directory (calculator-plugins)
            $pluginsDir = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/plugins/calculator-plugins';
            if (is_dir($pluginsDir)) {
                $this->zipAddDirectory($zip, realpath($pluginsDir), 'plugins/calculator-plugins');
            }

            $zip->close();
            $file = basename($zipPath);
            $download = '/admin/help/download-backup?file=' . rawurlencode($file);
            return ['success' => true, 'message' => 'System backup created', 'path' => $zipPath, 'filename' => $file, 'download_url' => $download];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Backup failed: ' . $e->getMessage()];
        }
    }

    public function downloadBackup(): void
    {
        $base = (defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 3) . '/storage');
        $dir = rtrim($base, '/\\') . '/backups';
        $file = $_GET['file'] ?? '';
        $file = basename($file);
        $path = $dir . '/' . $file;
        if (!$file || !is_file($path)) {
            http_response_code(404);
            echo 'Not found';
            return;
        }
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }

    private function zipAddDirectory(\ZipArchive $zip, string $source, string $base): void
    {
        $source = realpath($source);
        if ($source === false) return;
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            $filePath = realpath($file);
            $localName = $base . '/' . ltrim(str_replace($source, '', $filePath), DIRECTORY_SEPARATOR);
            if (is_dir($file)) {
                $zip->addEmptyDir($localName);
            } else {
                $zip->addFile($filePath, $localName);
            }
        }
    }

    private function extractZipTo(string $zipFile, string $destination): bool
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipFile) !== true) { return false; }
        $ok = $zip->extractTo($destination);
        $zip->close();
        if (!$ok) return false;
        // post-extract sanity
        [ $bytes, $files ] = $this->dirStats($destination);
        $maxBytes = 200 * 1024 * 1024; // 200MB
        $maxFiles = 15000;
        if ($bytes > $maxBytes || $files > $maxFiles) {
            $this->rrmdir($destination);
            return false;
        }
        return true;
    }

    private function dirStats(string $dir): array
    {
        $bytes = 0; $count = 0;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->isFile()) { $bytes += $file->getSize(); $count++; }
        }
        return [$bytes, $count];
    }

    private function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) return;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) {
            if ($file->isDir()) { @rmdir($file->getRealPath()); } else { @unlink($file->getRealPath()); }
        }
        @rmdir($dir);
    }

    private function copyDirectory(string $src, string $dst): void
    {
        if (!is_dir($src)) return;
        if (!is_dir($dst)) { @mkdir($dst, 0755, true); }
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        $srcReal = realpath($src) ?: $src;
        foreach ($it as $file) {
            $fileReal = $file->getRealPath();
            $rel = ltrim(str_replace($srcReal, '', $fileReal), DIRECTORY_SEPARATOR);
            $target = $dst . DIRECTORY_SEPARATOR . $rel;
            if ($file->isDir()) {
                if (!is_dir($target)) { @mkdir($target, 0755, true); }
            } else {
                @copy($file->getRealPath(), $target);
            }
        }
    }

    private function sumZipUncompressed(\ZipArchive $zip): array
    {
        $bytes = 0; $files = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (!$stat) continue;
            if (substr($stat['name'], -1) === '/') { continue; }
            $bytes += $stat['size'];
            $files++;
        }
        return [$bytes, $files];
    }

    private function applySql(string $sql): void
    {
        $pdo = Database::getInstance()->getPdo();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        // Split on semicolons at end of line; naive but works for our generated dumps
        $statements = preg_split('/;\s*\n/', $sql);
        foreach ($statements as $stmt) {
            $trim = trim($stmt);
            if ($trim === '') continue;
            $pdo->exec($trim);
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }
}
?>
