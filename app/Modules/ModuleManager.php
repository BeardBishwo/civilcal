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
        foreach (glob($pattern) as $file) {
            try {
                // Derive class name from path: app/Modules/{Name}/{Name}ServiceProvider.php
                $moduleName = basename(dirname($file));
                $class = "App\\Modules\\{$moduleName}\\{$moduleName}ServiceProvider";
                if (!class_exists($class)) {
                    require_once $file;
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
