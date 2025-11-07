<?php
/**
 * Bishwo Calculator - Installation Wizard
 * System Requirements Checking Class
 * 
 * @package BishwoCalculator
 * @version 1.0.0
 */

class Requirements {
    
    private $checks = [];
    private $passed = true;
    
    public function __construct() {
        $this->performChecks();
    }
    
    private function performChecks() {
        // PHP Version Check
        $this->checkPhpVersion();
        
        // Required PHP Extensions
        $this->checkPhpExtension('pdo');
        $this->checkPhpExtension('pdo_mysql');
        $this->checkPhpExtension('json');
        $this->checkPhpExtension('curl');
        $this->checkPhpExtension('openssl');
        $this->checkPhpExtension('mbstring');
        $this->checkPhpExtension('gd');
        
        // PHP Settings
        $this->checkPhpSetting('memory_limit', '128M');
        $this->checkPhpSetting('upload_max_filesize', '10M');
        $this->checkPhpSetting('post_max_size', '10M');
        $this->checkPhpSetting('max_execution_time', '60');
        
        // File System Permissions
        $this->checkDirectoryPermissions();
        
        // Server Software
        $this->checkServerSoftware();
        
        // Database Support
        $this->checkDatabaseSupport();
    }
    
    private function checkPhpVersion() {
        $current = PHP_VERSION;
        $required = '7.4.0';
        
        $this->addCheck([
            'name' => 'PHP Version',
            'description' => 'PHP 7.4 or higher is required for optimal performance and security.',
            'current' => $current,
            'required' => $required . ' or higher',
            'status' => version_compare($current, $required, '>=') ? 'pass' : 'fail'
        ]);
    }
    
    private function checkPhpExtension($extension) {
        $extensionNames = [
            'pdo' => 'PDO',
            'pdo_mysql' => 'PDO MySQL',
            'json' => 'JSON',
            'curl' => 'cURL',
            'openssl' => 'OpenSSL',
            'mbstring' => 'Multibyte String',
            'gd' => 'GD Library'
        ];
        
        $loaded = extension_loaded($extension);
        
        $this->addCheck([
            'name' => $extensionNames[$extension] ?? ucfirst($extension),
            'description' => 'Required for database operations and core functionality.',
            'current' => $loaded ? 'Loaded' : 'Not Loaded',
            'required' => 'Must be loaded',
            'status' => $loaded ? 'pass' : 'fail'
        ]);
    }
    
    private function checkPhpSetting($setting, $minimum) {
        $settingNames = [
            'memory_limit' => 'Memory Limit',
            'upload_max_filesize' => 'Upload Max Filesize',
            'post_max_size' => 'Post Max Size',
            'max_execution_time' => 'Max Execution Time'
        ];
        
        $current = ini_get($setting);
        $currentBytes = $this->convertToBytes($current);
        $requiredBytes = $this->convertToBytes($minimum);
        
        $status = $currentBytes >= $requiredBytes ? 'pass' : 'fail';
        if ($status === 'fail') {
            $this->passed = false;
        }
        
        $this->addCheck([
            'name' => $settingNames[$setting] ?? $setting,
            'description' => 'PHP configuration setting that affects application performance.',
            'current' => $current,
            'required' => $minimum . ' or higher',
            'status' => $status
        ]);
    }
    
    private function convertToBytes($value) {
        $value = trim($value);
        $last = strtolower($value[strlen($value)-1]);
        $value = (int) $value;
        
        switch($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
    
    private function checkDirectoryPermissions() {
        $directories = [
            __DIR__ . '/../../storage' => 'Storage Directory',
            __DIR__ . '/../../storage/logs' => 'Logs Directory',
            __DIR__ . '/../../storage/cache' => 'Cache Directory'
        ];
        
        foreach ($directories as $directory => $name) {
            $exists = file_exists($directory);
            $writable = $exists ? is_writable($directory) : false;
            
            // Try to create directory if it doesn't exist
            if (!$exists) {
                @mkdir($directory, 0755, true);
                $writable = is_writable($directory);
            }
            
            $this->addCheck([
                'name' => $name,
                'description' => 'Directory must exist and be writable for file operations.',
                'current' => $exists ? ($writable ? 'Writable' : 'Not Writable') : 'Does Not Exist',
                'required' => 'Must exist and be writable',
                'status' => $writable ? 'pass' : 'warning'
            ]);
        }
    }
    
    private function checkServerSoftware() {
        $software = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        $isApache = strpos($software, 'Apache') !== false;
        $isNginx = strpos($software, 'nginx') !== false;
        
        $this->addCheck([
            'name' => 'Web Server',
            'description' => 'Web server software running on the server.',
            'current' => $software,
            'required' => 'Apache, Nginx, or compatible',
            'status' => ($isApache || $isNginx) ? 'pass' : 'warning'
        ]);
    }
    
    private function checkDatabaseSupport() {
        $pdoAvailable = class_exists('PDO');
        $mysqlAvailable = false;
        
        if ($pdoAvailable) {
            try {
                $drivers = PDO::getAvailableDrivers();
                $mysqlAvailable = in_array('mysql', $drivers);
            } catch (Exception $e) {
                $mysqlAvailable = false;
            }
        }
        
        $this->addCheck([
            'name' => 'MySQL Support',
            'description' => 'MySQL database support is required for data storage.',
            'current' => $mysqlAvailable ? 'Available' : 'Not Available',
            'required' => 'MySQL/MariaDB support',
            'status' => $mysqlAvailable ? 'pass' : 'fail'
        ]);
    }
    
    private function addCheck($check) {
        if ($check['status'] === 'fail') {
            $this->passed = false;
        }
        $this->checks[] = $check;
    }
    
    public function check() {
        return [
            'passed' => $this->passed,
            'checks' => $this->checks
        ];
    }
    
    public function getSummary() {
        $total = count($this->checks);
        $passed = count(array_filter($this->checks, function($check) { 
            return $check['status'] === 'pass'; 
        }));
        $warnings = count(array_filter($this->checks, function($check) { 
            return $check['status'] === 'warning'; 
        }));
        $failures = count(array_filter($this->checks, function($check) { 
            return $check['status'] === 'fail'; 
        }));
        
        return [
            'total' => $total,
            'passed' => $passed,
            'warnings' => $warnings,
            'failures' => $failures,
            'percentage' => $total > 0 ? round(($passed / $total) * 100) : 0
        ];
    }
}
?>
