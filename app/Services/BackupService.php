<?php

namespace App\Services;

use PDO;
use ZipArchive;
use Exception;

class BackupService
{
    private $db;
    private $backupDir;
    
    public function __construct($db = null)
    {
        $this->db = $db;
        $this->backupDir = BASE_PATH . '/storage/backups';
        
        // Create backup directory if it doesn't exist
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    /**
     * Create a new backup
     */
    public function createBackup($types = ['database'], $compression = 'medium')
    {
        try {
            $backupId = uniqid('backup_');
            $timestamp = date('Y-m-d_H-i-s');
            $backupName = "backup_{$timestamp}_{$backupId}";
            $backupPath = $this->backupDir . '/' . $backupName;
            
            // Create temporary directory for backup files
            $tempDir = $backupPath . '_temp';
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $startTime = microtime(true);
            $files = [];
            
            // Backup database
            if (in_array('database', $types)) {
                $dbFile = $this->backupDatabase($tempDir);
                if ($dbFile) {
                    $files[] = $dbFile;
                }
            }
            
            // Backup application files
            if (in_array('files', $types)) {
                $this->backupFiles($tempDir, BASE_PATH . '/app');
                $this->backupFiles($tempDir, BASE_PATH . '/modules');
                $this->backupFiles($tempDir, BASE_PATH . '/themes');
            }
            
            // Backup uploads
            if (in_array('uploads', $types)) {
                if (file_exists(BASE_PATH . '/public/uploads')) {
                    $this->backupFiles($tempDir, BASE_PATH . '/public/uploads', 'uploads');
                }
                if (file_exists(BASE_PATH . '/public/storage')) {
                    $this->backupFiles($tempDir, BASE_PATH . '/public/storage', 'storage');
                }
            }
            
            // Backup configuration
            if (in_array('config', $types)) {
                if (file_exists(BASE_PATH . '/.env')) {
                    copy(BASE_PATH . '/.env', $tempDir . '/.env');
                }
                if (file_exists(BASE_PATH . '/app/Config')) {
                    $this->backupFiles($tempDir, BASE_PATH . '/app/Config', 'config');
                }
            }
            
            // Create ZIP archive
            $zipFile = $backupPath . '.zip';
            $zip = new ZipArchive();
            
            if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
                $this->addDirectoryToZip($zip, $tempDir, '');
                $zip->close();
            } else {
                throw new Exception('Failed to create ZIP archive');
            }
            
            // Clean up temporary directory
            $this->deleteDirectory($tempDir);
            
            $duration = microtime(true) - $startTime;
            $size = filesize($zipFile);
            
            // Save backup record to database
            $backupRecord = [
                'id' => $backupId,
                'filename' => basename($zipFile),
                'path' => $zipFile,
                'type' => implode(', ', $types),
                'size' => $size,
                'compression' => $compression,
                'status' => 'completed',
                'duration' => $duration,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->saveBackupRecord($backupRecord);
            
            return [
                'success' => true,
                'backup_id' => $backupId,
                'filename' => basename($zipFile),
                'size' => $size,
                'duration' => $duration
            ];
            
        } catch (Exception $e) {
            // Clean up on error
            if (isset($tempDir) && file_exists($tempDir)) {
                $this->deleteDirectory($tempDir);
            }
            if (isset($zipFile) && file_exists($zipFile)) {
                unlink($zipFile);
            }
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Backup database to SQL file
     */
    private function backupDatabase($targetDir)
    {
        try {
            if (!$this->db) {
                return null;
            }
            
            $filename = $targetDir . '/database_' . date('Y-m-d_H-i-s') . '.sql';
            $handle = fopen($filename, 'w+');
            
            if (!$handle) {
                throw new Exception('Cannot create database backup file');
            }
            
            // Write header
            fwrite($handle, "-- Database Backup\n");
            fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");
            
            // Get all tables
            $tables = $this->db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                // Drop table statement
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                
                // Create table statement
                $createTable = $this->db->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
                fwrite($handle, $createTable['Create Table'] . ";\n\n");
                
                // Insert data
                $rows = $this->db->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $values = array_map(function($value) {
                            return $value === null ? 'NULL' : $this->db->quote($value);
                        }, array_values($row));
                        
                        $columns = '`' . implode('`, `', array_keys($row)) . '`';
                        $valuesStr = implode(', ', $values);
                        
                        fwrite($handle, "INSERT INTO `{$table}` ({$columns}) VALUES ({$valuesStr});\n");
                    }
                    fwrite($handle, "\n");
                }
            }
            
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);
            
            return $filename;
            
        } catch (Exception $e) {
            error_log("Database backup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Backup files from a directory
     */
    private function backupFiles($targetDir, $sourceDir, $subDir = null)
    {
        if (!file_exists($sourceDir)) {
            return;
        }
        
        $destination = $subDir ? $targetDir . '/' . $subDir : $targetDir;
        
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $this->copyDirectory($sourceDir, $destination);
    }
    
    /**
     * Copy directory recursively
     */
    private function copyDirectory($source, $destination)
    {
        if (!is_dir($source)) {
            return;
        }
        
        $dir = opendir($source);
        
        while (($file = readdir($dir)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            
            $srcPath = $source . '/' . $file;
            $dstPath = $destination . '/' . $file;
            
            if (is_dir($srcPath)) {
                if (!file_exists($dstPath)) {
                    mkdir($dstPath, 0755, true);
                }
                $this->copyDirectory($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
        
        closedir($dir);
    }
    
    /**
     * Add directory to ZIP archive
     */
    private function addDirectoryToZip($zip, $dir, $zipPath)
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = scandir($dir);
        
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            
            $filePath = $dir . '/' . $file;
            $zipFilePath = $zipPath ? $zipPath . '/' . $file : $file;
            
            if (is_dir($filePath)) {
                $zip->addEmptyDir($zipFilePath);
                $this->addDirectoryToZip($zip, $filePath, $zipFilePath);
            } else {
                $zip->addFile($filePath, $zipFilePath);
            }
        }
    }
    
    /**
     * Delete directory recursively
     */
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
    
    /**
     * Save backup record to database
     */
    private function saveBackupRecord($record)
    {
        if (!$this->db) {
            return;
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO backups (id, filename, path, type, size, compression, status, duration, created_at)
                VALUES (:id, :filename, :path, :type, :size, :compression, :status, :duration, :created_at)
            ");
            
            $stmt->execute($record);
        } catch (Exception $e) {
            error_log("Failed to save backup record: " . $e->getMessage());
        }
    }
    
    /**
     * Get all backup records
     */
    public function getBackupHistory($limit = 50)
    {
        if (!$this->db) {
            return [];
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM backups 
                ORDER BY created_at DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to get backup history: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete a backup
     */
    public function deleteBackup($backupId)
    {
        if (!$this->db) {
            return ['success' => false, 'message' => 'Database not available'];
        }
        
        try {
            // Get backup record
            $stmt = $this->db->prepare("SELECT * FROM backups WHERE id = :id");
            $stmt->execute(['id' => $backupId]);
            $backup = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$backup) {
                return ['success' => false, 'message' => 'Backup not found'];
            }
            
            // Delete file
            if (file_exists($backup['path'])) {
                unlink($backup['path']);
            }
            
            // Delete record
            $stmt = $this->db->prepare("DELETE FROM backups WHERE id = :id");
            $stmt->execute(['id' => $backupId]);
            
            return ['success' => true, 'message' => 'Backup deleted successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Cleanup old backups
     */
    public function cleanupOldBackups($retentionDays = 30)
    {
        if (!$this->db) {
            return ['success' => false, 'message' => 'Database not available'];
        }
        
        try {
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$retentionDays} days"));
            
            // Get old backups
            $stmt = $this->db->prepare("SELECT * FROM backups WHERE created_at < :cutoff");
            $stmt->execute(['cutoff' => $cutoffDate]);
            $oldBackups = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $deletedCount = 0;
            
            foreach ($oldBackups as $backup) {
                // Delete file
                if (file_exists($backup['path'])) {
                    unlink($backup['path']);
                }
                
                // Delete record
                $stmt = $this->db->prepare("DELETE FROM backups WHERE id = :id");
                $stmt->execute(['id' => $backup['id']]);
                
                $deletedCount++;
            }
            
            return [
                'success' => true,
                'deleted_count' => $deletedCount,
                'message' => "Cleaned up {$deletedCount} old backups"
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Test backup configuration
     */
    public function testConfiguration()
    {
        $tests = [];
        
        // Test backup directory
        $tests['backup_directory'] = [
            'name' => 'Backup Directory',
            'status' => is_writable($this->backupDir) ? 'pass' : 'fail',
            'message' => is_writable($this->backupDir) ? 'Writable' : 'Not writable'
        ];
        
        // Test ZIP extension
        $tests['zip_extension'] = [
            'name' => 'ZIP Extension',
            'status' => class_exists('ZipArchive') ? 'pass' : 'fail',
            'message' => class_exists('ZipArchive') ? 'Available' : 'Not available'
        ];
        
        // Test database connection
        $tests['database'] = [
            'name' => 'Database Connection',
            'status' => $this->db ? 'pass' : 'fail',
            'message' => $this->db ? 'Connected' : 'Not connected'
        ];
        
        // Test disk space
        $freeSpace = disk_free_space($this->backupDir);
        $tests['disk_space'] = [
            'name' => 'Disk Space',
            'status' => $freeSpace > 100 * 1024 * 1024 ? 'pass' : 'warning',
            'message' => 'Free: ' . $this->formatBytes($freeSpace)
        ];
        
        $allPassed = true;
        foreach ($tests as $test) {
            if ($test['status'] === 'fail') {
                $allPassed = false;
                break;
            }
        }
        
        return [
            'success' => $allPassed,
            'tests' => $tests,
            'message' => $allPassed ? 'All tests passed' : 'Some tests failed'
        ];
    }
    
    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}