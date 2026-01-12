<?php
namespace App\Modules;

use App\Services\Logger;

class ModuleManager
{
    public static function load($router): void
    {
        $modulesDir = APP_PATH . '/Modules';
        if (!is_dir($modulesDir)) {
            return;
        }

        // Look for *ServiceProvider.php in each module folder
        $pattern = $modulesDir . '/*/*ServiceProvider.php';
        $files = glob($pattern);
        
        if ($files === false) return;

        foreach ($files as $file) {
            try {
                // SECURITY: Verify file is within the legitimate Modules directory
                $realFile = realpath($file);
                $realModulesDir = realpath($modulesDir);
                if (!$realFile || strpos($realFile, $realModulesDir) !== 0) {
                    continue;
                }

                // Derive expected class/file names
                $moduleName = basename(dirname($file));
                $expectedFile = "{$moduleName}ServiceProvider.php";
                
                // SECURITY: Strict naming check - directory name MUST match the provider prefix
                if (basename($file) !== $expectedFile) {
                    continue;
                }
                
                // SECURITY: Ensure module name is alphanumeric
                if (!preg_match('/^[a-zA-Z0-9]+$/', $moduleName)) {
                    continue;
                }

                $class = "App\\Modules\\{$moduleName}\\{$moduleName}ServiceProvider";
                if (!class_exists($class)) {
                    require_once $realFile;
                }
                if (class_exists($class)) {
                    $provider = new $class();
                    if ($provider instanceof BaseProvider) {
                        $provider->register($router);
                        Logger::info('Module provider registered', ['module' => $moduleName]);
                    }
                }
            } catch (\Throwable $e) {
                Logger::exception($e);
            }
        }
    }
}
