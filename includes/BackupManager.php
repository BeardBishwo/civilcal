<?php
class BackupManager {
    private static string $backupDir = __DIR__ . '/../backups';
    private const TABLES_TO_BACKUP = [
        'users',
        'tenants',
        'history',
        'contacts',
        'payments',
        'storage_usage'
    ];
    
    /**
     * Create database backup
     */
    public static function createBackup(): ?string {
        try {
            $pdo = get_db();
            
            // Create backup directory if it doesn't exist
            if (!file_exists(self::$backupDir)) {
                mkdir(self::$backupDir, 0755, true);
            }
            
            // Generate backup filename
            $timestamp = date('Y-m-d_H-i-s');
            $filename = self::$backupDir . "/backup_{$timestamp}.sql.gz";
            
            // Start output buffering
            ob_start();
            
            // Add metadata
            echo "-- AEC Calculator Backup\n";
            echo "-- Date: " . date('Y-m-d H:i:s') . "\n";
            echo "-- Environment: " . ENVIRONMENT . "\n\n";
            
            // Backup each table
            foreach (self::TABLES_TO_BACKUP as $table) {
                self::backupTable($pdo, $table);
            }
            
            // Get buffered content
            $content = ob_get_clean();
            
            // Encrypt if encryption key is available
            if (defined('BACKUP_ENCRYPTION_KEY') && BACKUP_ENCRYPTION_KEY) {
                $content = self::encrypt($content, BACKUP_ENCRYPTION_KEY);
            }
            
            // Compress and save
            $compressed = gzencode($content, 9);
            if ($compressed === false) {
                throw new Exception("Failed to compress backup");
            }
            
            if (file_put_contents($filename, $compressed) === false) {
                throw new Exception("Failed to write backup file");
            }
            
            // Clean old backups
            self::cleanOldBackups();
            
            return $filename;
            
        } catch (Exception $e) {
            error_log("Backup creation failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Backup a single table
     */
    private static function backupTable(PDO $pdo, string $table): void {
        // Get create table statement
        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        echo $row[1] . ";\n\n";
        
        // Get table data
        $stmt = $pdo->query("SELECT * FROM `$table`");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $values = array_map(function($value) use ($pdo) {
                if ($value === null) return 'NULL';
                return $pdo->quote($value);
            }, $row);
            
            echo "INSERT INTO `$table` VALUES (" . implode(',', $values) . ");\n";
        }
        echo "\n";
    }
    
    /**
     * Restore from backup
     */
    public static function restore(string $backupFile): bool {
        try {
            if (!file_exists($backupFile)) {
                throw new Exception("Backup file not found");
            }
            
            // Read and decompress
            $compressed = file_get_contents($backupFile);
            if ($compressed === false) {
                throw new Exception("Failed to read backup file");
            }
            
            $content = gzdecode($compressed);
            if ($content === false) {
                throw new Exception("Failed to decompress backup");
            }
            
            // Decrypt if necessary
            if (defined('BACKUP_ENCRYPTION_KEY') && BACKUP_ENCRYPTION_KEY) {
                $content = self::decrypt($content, BACKUP_ENCRYPTION_KEY);
            }
            
            // Execute SQL
            $pdo = get_db();
            $pdo->beginTransaction();
            
            foreach (explode(";\n", $content) as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
            
            $pdo->commit();
            return true;
            
        } catch (Exception $e) {
            error_log("Restore failed: " . $e->getMessage());
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            return false;
        }
    }
    
    /**
     * Clean old backups
     */
    private static function cleanOldBackups(): void {
        $files = glob(self::$backupDir . '/backup_*.sql.gz');
        $now = time();
        
        foreach ($files as $file) {
            if ($now - filemtime($file) > BACKUP_RETENTION_DAYS * 86400) {
                unlink($file);
            }
        }
    }
    
    /**
     * Encrypt data
     */
    private static function encrypt(string $data, string $key): string {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt(
            $data,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        return base64_encode($iv . $tag . $encrypted);
    }
    
    /**
     * Decrypt data
     */
    private static function decrypt(string $data, string $key): string {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $tag = substr($data, 16, 16);
        $ciphertext = substr($data, 32);
        
        return openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
    }
    
    /**
     * Schedule next backup
     */
    public static function scheduleNextBackup(): void {
        $lastBackup = glob(self::$backupDir . '/backup_*.sql.gz');
        if (empty($lastBackup)) {
            self::createBackup();
            return;
        }
        
        $lastBackupTime = filemtime(end($lastBackup));
        if (time() - $lastBackupTime >= BACKUP_MIN_INTERVAL) {
            self::createBackup();
        }
    }
}