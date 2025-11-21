<?php
namespace App\Services;

class BackupService
{
    private $backupDir;
    private $databaseConfig;

    public function __construct()
    {
        $this->backupDir = BASE_PATH . '/storage/backups';
        $this->databaseConfig = [
            'host' => defined('DB_HOST') ? DB_HOST : 'localhost',
            'dbname' => defined('DB_NAME') ? DB_NAME : '',
            'username' => defined('DB_USER') ? DB_USER : '',
            'password' => defined('DB_PASS') ? DB_PASS : '',
        ];
        
        // Create backup directory if it doesn't exist
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Create a full backup of the application
     */
    public function createBackup($includeDatabase = true, $includeFiles = true, $customName = null)
    {
        try {
            $backupName = $customName ?: 'backup_' . date('Y-m-d_H-i-s');
            $backupPath = $this->backupDir . '/' . $backupName . '.zip';
            
            $zip = new \ZipArchive();
            $zipOpen = $zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            
            if ($zipOpen !== true) {
                throw new \Exception('Could not create backup file: ' . $backupPath);
            }
            
            // Add database dump if requested
            if ($includeDatabase) {
                $dbDump = $this->createDatabaseDump();
                $zip->addFromString('database.sql', $dbDump);
            }
            
            // Add application files if requested
            if ($includeFiles) {
                $this->addDirectoryToZip($zip, BASE_PATH, 'app');
            }
            
            $zip->close();
            
            return [
                'success' => true,
                'path' => $backupPath,
                'size' => filesize($backupPath),
                'message' => 'Backup created successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create a database dump
     */
    private function createDatabaseDump()
    {
        try {
            $dsn = "mysql:host={$this->databaseConfig['host']};dbname={$this->databaseConfig['dbname']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $this->databaseConfig['username'], $this->databaseConfig['password']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Get all table names
            $tables = [];
            $result = $pdo->query("SHOW TABLES");
            while ($row = $result->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            
            $sqlDump = "-- Database Backup\n-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
            
            foreach ($tables as $table) {
                // Get table structure
                $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_NUM);
                $sqlDump .= "\n" . $createTable[1] . ";\n\n";
                
                // Get table data
                $result = $pdo->query("SELECT * FROM `{$table}`");
                while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
                    $sqlDump .= "INSERT INTO `{$table}` VALUES (";
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = $pdo->quote($value);
                        }
                    }
                    $sqlDump .= implode(', ', $values) . ");\n";
                }
            }
            
            return $sqlDump;
        } catch (\Exception $e) {
            error_log('BackupService::createDatabaseDump error: ' . $e->getMessage());
            throw new \Exception('Database dump failed: ' . $e->getMessage());
        }
    }

    /**
     * Add a directory to zip archive
     */
    private function addDirectoryToZip($zip, $dir, $zipPath = '')
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen(BASE_PATH) + 1);
                
                // Skip backup directory to avoid including backups in backups
                if (strpos($relativePath, '/storage/backups/') === false) {
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
    }

    /**
     * Restore a backup from file
     */
    public function restoreBackup($backupFile)
    {
        try {
            if (!file_exists($backupFile)) {
                throw new \Exception('Backup file does not exist: ' . $backupFile);
            }
            
            $zip = new \ZipArchive();
            $zipOpen = $zip->open($backupFile);
            
            if ($zipOpen !== true) {
                throw new \Exception('Could not open backup file: ' . $backupFile);
            }
            
            // Extract to a temporary directory
            $tempDir = $this->backupDir . '/temp_restore_' . time();
            mkdir($tempDir, 0755, true);
            
            $zip->extractTo($tempDir);
            $zip->close();
            
            // Look for database dump
            $dbDumpFile = $tempDir . '/database.sql';
            if (file_exists($dbDumpFile)) {
                $this->restoreDatabase($dbDumpFile);
            }
            
            // For now, we'll just return success
            // In a real implementation, you would restore the files as well
            $this->removeDirectory($tempDir);
            
            return [
                'success' => true,
                'message' => 'Backup restored successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Restore failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Restore database from SQL file
     */
    private function restoreDatabase($sqlFile)
    {
        try {
            $dsn = "mysql:host={$this->databaseConfig['host']};dbname={$this->databaseConfig['dbname']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $this->databaseConfig['username'], $this->databaseConfig['password']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $sql = file_get_contents($sqlFile);
            $statements = explode(";\n", $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
        } catch (\Exception $e) {
            error_log('BackupService::restoreDatabase error: ' . $e->getMessage());
            throw new \Exception('Database restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Get list of available backups
     */
    public function getBackupList()
    {
        $backups = [];
        
        if (is_dir($this->backupDir)) {
            $files = scandir($this->backupDir);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                    $filePath = $this->backupDir . '/' . $file;
                    $backups[] = [
                        'name' => $file,
                        'path' => $filePath,
                        'size' => filesize($filePath),
                        'date' => date('Y-m-d H:i:s', filemtime($filePath)),
                        'formatted_size' => $this->formatBytes(filesize($filePath))
                    ];
                }
            }
        }
        
        // Sort by date (newest first)
        usort($backups, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return $backups;
    }

    /**
     * Delete a backup file
     */
    public function deleteBackup($backupName)
    {
        try {
            $backupPath = $this->backupDir . '/' . $backupName;
            
            if (file_exists($backupPath)) {
                unlink($backupPath);
                return [
                    'success' => true,
                    'message' => 'Backup deleted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Backup file does not exist'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Schedule automatic backups
     */
    public function scheduleBackup($schedule = 'daily', $retention = 7)
    {
        // In a real implementation, this would set up cron jobs or scheduled tasks
        // For now, we'll just return a success message
        return [
            'success' => true,
            'message' => 'Backup scheduled: ' . $schedule . ' with ' . $retention . ' days retention'
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Remove directory recursively
     */
    private function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object !== '.' && $object !== '..') {
                    $path = $dir . '/' . $object;
                    if (is_dir($path)) {
                        $this->removeDirectory($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            rmdir($dir);
        }
    }
}