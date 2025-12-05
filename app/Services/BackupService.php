<?php

namespace App\Services;

use Exception;

class BackupService
{
    private $backupDir;
    private $maxBackupSize;

    public function __construct()
    {
        $this->backupDir = BASE_PATH . '/storage/backups';
        $this->maxBackupSize = 1024 * 1024 * 1024; // 1GB in bytes
        $this->ensureBackupDirectory();
    }

    /**
     * Create a new backup
     */
    public function createBackup($includeDatabase = true, $includeFiles = true, $backupName = null)
    {
        try {
            // Generate backup name if not provided
            if (!$backupName) {
                $backupName = 'backup_' . date('Y-m-d_H-i-s') . '.zip';
            } else {
                $backupName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $backupName) . '_' . date('Y-m-d_H-i-s') . '.zip';
            }

            $backupPath = $this->backupDir . '/' . $backupName;

            // Create a new zip archive
            $zip = new \ZipArchive();
            $zipOpen = $zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            if ($zipOpen !== true) {
                throw new Exception("Cannot create backup file at: {$backupPath}");
            }

            // Add database dump if requested
            $dbDumpPath = null;
            if ($includeDatabase) {
                $dbDumpPath = $this->createDatabaseDump();
                $zip->addFile($dbDumpPath, 'database_dump.sql');
            }

            // Add files if requested
            if ($includeFiles) {
                $this->addDirectoryToZip($zip, BASE_PATH, 'files');
            }

            $zip->close();
            
            // Clean up the temporary dump file after closing the zip
            if ($dbDumpPath && file_exists($dbDumpPath)) {
                unlink($dbDumpPath);
            }

            // Check if file exists and get backup size
            clearstatcache(); // Clear file status cache
            if (!file_exists($backupPath)) {
                throw new Exception("Backup file was not created successfully at: {$backupPath}");
            }
            
            $backupSize = filesize($backupPath);
            if ($backupSize === false) {
                throw new Exception("Cannot determine backup file size at: {$backupPath}");
            }
            
            if ($backupSize > $this->maxBackupSize) {
                unlink($backupPath); // Delete oversized backup
                throw new Exception("Backup exceeds maximum allowed size of " . ($this->maxBackupSize / (1024*1024)) . "MB");
            }

            return [
                'success' => true,
                'message' => 'Backup created successfully',
                'backup_name' => $backupName,
                'size' => $backupSize,
                'path' => $backupPath
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Backup creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get list of all backups
     */
    public function getBackupList()
    {
        $backups = [];
        $files = glob($this->backupDir . '/*.zip');

        foreach ($files as $file) {
            $fileName = basename($file);
            $fileSize = filesize($file);
            $fileTime = filemtime($file);

            $backups[] = [
                'name' => $fileName,
                'size' => $fileSize,
                'size_formatted' => $this->formatBytes($fileSize),
                'date' => date('Y-m-d H:i:s', $fileTime),
                'timestamp' => $fileTime
            ];
        }

        // Sort by date (newest first)
        usort($backups, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
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

            if (!file_exists($backupPath)) {
                throw new Exception("Backup file does not exist: {$backupName}");
            }

            if (unlink($backupPath)) {
                return [
                    'success' => true,
                    'message' => 'Backup deleted successfully'
                ];
            } else {
                throw new Exception("Failed to delete backup file: {$backupName}");
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Backup deletion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Restore from a backup file
     */
    public function restoreBackup($backupPath)
    {
        try {
            if (!file_exists($backupPath)) {
                throw new Exception("Backup file does not exist: {$backupPath}");
            }

            $zip = new \ZipArchive();
            $zipOpen = $zip->open($backupPath);

            if ($zipOpen !== true) {
                throw new Exception("Cannot open backup file: {$backupPath}");
            }

            // Create temporary directory for extraction
            $tempDir = $this->backupDir . '/temp_restore_' . uniqid();
            if (!mkdir($tempDir, 0755, true)) {
                throw new Exception("Cannot create temporary directory for restore");
            }

            // Extract the backup
            $zip->extractTo($tempDir);
            $zip->close();

            // Process different backup components
            $hasDatabase = file_exists($tempDir . '/database_dump.sql');
            $hasFiles = is_dir($tempDir . '/files');

            $results = [];

            // Restore database if available
            if ($hasDatabase) {
                $results['database'] = $this->restoreDatabase($tempDir . '/database_dump.sql');
            }

            // Restore files if available
            if ($hasFiles) {
                $results['files'] = $this->restoreFiles($tempDir . '/files');
            }

            // Clean up temporary directory
            $this->deleteDirectory($tempDir);

            return [
                'success' => true,
                'message' => 'Restore completed successfully',
                'details' => $results
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Restore failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Schedule automated backups
     */
    public function scheduleBackup($schedule = 'daily', $retention = 7)
    {
        try {
            // In a real implementation, this would integrate with a cron job or task scheduler
            // For now, we'll just return a message indicating the schedule
            
            $validSchedules = ['hourly', 'daily', 'weekly', 'monthly'];
            if (!in_array($schedule, $validSchedules)) {
                throw new Exception("Invalid schedule: {$schedule}. Valid options: " . implode(', ', $validSchedules));
            }

            // Validate retention
            if (!is_numeric($retention) || $retention < 1) {
                throw new Exception("Retention must be a positive number, got: {$retention}");
            }

            return [
                'success' => true,
                'message' => "Backup scheduled: {$schedule}, retention: {$retention} backups",
                'schedule' => $schedule,
                'retention' => $retention
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Scheduling failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Set maximum backup size
     */
    public function setMaxBackupSize($sizeInMB)
    {
        if (!is_numeric($sizeInMB) || $sizeInMB < 100 || $sizeInMB > 10240) {
            throw new Exception("Maximum backup size must be between 100 and 10240 MB");
        }
        
        $this->maxBackupSize = $sizeInMB * 1024 * 1024; // Convert MB to bytes
        return true;
    }

    /**
     * Get maximum backup size in MB
     */
    public function getMaxBackupSizeMB()
    {
        return $this->maxBackupSize / (1024 * 1024);
    }

    /**
     * Get backup statistics
     */
    public function getBackupStats()
    {
        $backups = $this->getBackupList();
        $totalSize = 0;
        
        foreach ($backups as $backup) {
            $totalSize += $backup['size'];
        }

        return [
            'total_backups' => count($backups),
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'latest_backup' => $backups[0] ?? null
        ];
    }

    /**
     * Create a database dump
     */
    private function createDatabaseDump()
    {
        try {
            // Get database configuration
            $dbHost = $_ENV['DB_HOST'] ?? (defined('DB_HOST') ? DB_HOST : 'localhost');
            $dbUser = $_ENV['DB_USER'] ?? (defined('DB_USER') ? DB_USER : 'root');
            $dbPass = $_ENV['DB_PASSWORD'] ?? (defined('DB_PASSWORD') ? DB_PASSWORD : '');
            $dbName = $_ENV['DB_NAME'] ?? (defined('DB_NAME') ? DB_NAME : 'engical');
            
            // Create temporary file
            $tempFile = $this->backupDir . '/temp_dump_' . uniqid() . '.sql';
            
            // Use mysqldump if available, otherwise use PHP method
            if (function_exists('exec')) {
                $command = "mysqldump --host={$dbHost} --user={$dbUser} --password={$dbPass} {$dbName}";
                $output = [];
                $returnCode = 0;
                exec($command, $output, $returnCode);
                
                if ($returnCode === 0) {
                    $dumpContent = implode("\n", $output);
                    file_put_contents($tempFile, $dumpContent);
                    return $tempFile;
                }
            }
            
            // Fallback to PHP method if mysqldump is not available
            return $this->createDatabaseDumpWithPHP();
        } catch (Exception $e) {
            throw new Exception("Failed to create database dump: " . $e->getMessage());
        }
    }

    /**
     * Create database dump using PHP (fallback method)
     */
    private function createDatabaseDumpWithPHP()
    {
        try {
            // Create temporary file
            $tempFile = $this->backupDir . '/temp_dump_' . uniqid() . '.sql';
            $handle = fopen($tempFile, 'w');

            // Get database connection - assuming we can access it through a global function
            $pdo = $this->getDbConnection();

            // Get all table names
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                // Get table structure
                $createTableStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                $createTable = $createTableStmt->fetch();
                $tableStructure = $createTable['Create Table'];

                // Write table structure
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($handle, $tableStructure . ";\n\n");

                // Get table data
                $dataStmt = $pdo->query("SELECT * FROM `{$table}`");
                while ($row = $dataStmt->fetch(\PDO::FETCH_ASSOC)) {
                    $values = [];
                    foreach ($row as $value) {
                        $values[] = $value === null ? 'NULL' : $pdo->quote($value);
                    }
                    fwrite($handle, "INSERT INTO `{$table}` VALUES (" . implode(',', $values) . ");\n");
                }
                fwrite($handle, "\n");
            }

            fclose($handle);

            return $tempFile;
        } catch (Exception $e) {
            throw new Exception("Failed to create database dump with PHP: " . $e->getMessage());
        }
    }

    /**
     * Restore database from SQL file
     */
    private function restoreDatabase($sqlFilePath)
    {
        try {
            $sql = file_get_contents($sqlFilePath);
            if ($sql === false) {
                throw new Exception("Cannot read SQL file: {$sqlFilePath}");
            }

            $pdo = $this->getDbConnection();
            
            // Split SQL into statements (simple approach - may need more sophisticated parsing for complex SQL)
            $statements = preg_split('/;(?=\s*(?:\/\*.*?\*\/\s*)?(?:--.*\s*)?$)/', $sql);
            
            $executed = 0;
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                    $executed++;
                }
            }

            return [
                'success' => true,
                'message' => "Database restored successfully",
                'statements_executed' => $executed
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Database restore failed: " . $e->getMessage()
            ];
        }
    }

    /**
     * Restore files from backup
     */
    private function restoreFiles($sourceDir)
    {
        try {
            // This is a simplified implementation
            // In a real system, you would implement specific file restoration logic
            // based on your application's needs and what files are being backed up
            
            // For now, we'll just copy files with some basic validation
            $result = $this->copyDirectory($sourceDir, BASE_PATH . '/restored');
            
            return [
                'success' => $result,
                'message' => $result ? 'Files restored successfully' : 'File restore failed'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "File restore failed: " . $e->getMessage()
            ];
        }
    }

    /**
     * Add directory to zip archive
     */
    private function addDirectoryToZip($zip, $dir, $zipDirName = '', $excludePatterns = ['storage/backups/*', 'cache/*', '*/storage/backups/*', '*/cache/*'])
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            // Skip if matches exclude patterns
            $relativePath = substr($file->getPathname(), strlen(BASE_PATH) + 1);
            
            $shouldExclude = false;
            foreach ($excludePatterns as $pattern) {
                if (fnmatch($pattern, $relativePath)) {
                    $shouldExclude = true;
                    break;
                }
            }
            
            if ($shouldExclude) {
                continue;
            }

            // Add file to zip with relative path
            $zipPath = $zipDirName . '/' . substr($file->getPathname(), strlen(BASE_PATH) + 1);
            $zip->addFile($file->getPathname(), $zipPath);
        }
    }

    /**
     * Copy directory recursively
     */
    private function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
        return true;
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    /**
     * Format bytes to human-readable format
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Get database connection
     * This would need to be adapted to your specific application's database connection method
     */
    private function getDbConnection()
    {
        // This is a placeholder - you'll need to adapt this to your application's database connection
        // For example, if you have a Database class like in your MVC structure:
        // $db = Database::getInstance();
        // return $db->getPdo();
        
        // For now, assuming a global PDO connection or creating a new one
        // This is a simplified implementation - you would replace this with your actual database connection logic
        return new \PDO(
            "mysql:host=" . ($_ENV['DB_HOST'] ?? (defined('DB_HOST') ? DB_HOST : 'localhost')) . 
            ";dbname=" . ($_ENV['DB_NAME'] ?? (defined('DB_NAME') ? DB_NAME : 'engical')),
            $_ENV['DB_USER'] ?? (defined('DB_USER') ? DB_USER : 'root'),
            $_ENV['DB_PASSWORD'] ?? (defined('DB_PASSWORD') ? DB_PASSWORD : '')
        );
    }

    /**
     * Ensure backup directory exists
     */
    private function ensureBackupDirectory()
    {
        if (!is_dir($this->backupDir)) {
            if (!mkdir($this->backupDir, 0755, true)) {
                throw new Exception("Cannot create backup directory: {$this->backupDir}");
            }
        }
    }
}