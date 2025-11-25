<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing database dump creation...\n";
    
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Access the private createDatabaseDump method using reflection
    $reflection = new ReflectionClass($backupService);
    $createDatabaseDumpMethod = $reflection->getMethod('createDatabaseDump');
    $createDatabaseDumpMethod->setAccessible(true);
    
    echo "Calling createDatabaseDump...\n";
    $result = $createDatabaseDumpMethod->invoke($backupService);
    
    echo "Database dump result: " . $result . "\n";
    echo "File exists: " . (file_exists($result) ? 'Yes' : 'No') . "\n";
    
    if (file_exists($result)) {
        $size = filesize($result);
        echo "Dump file size: " . $size . " bytes\n";
        echo "Dump file content preview:\n";
        $content = file_get_contents($result);
        echo substr($content, 0, 200) . (strlen($content) > 200 ? '...' : '') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}