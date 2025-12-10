<?php

namespace App\Services;

use App\Models\Module;
use Exception;

class ModuleService
{
    private $moduleModel;

    public function __construct()
    {
        $this->moduleModel = new Module();
    }



    /**
     * Create a new module
     */
    public function createModule($data)
    {
        try {
            // Validate input data
            $validation = $this->moduleModel->validate($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $validation['errors']),
                    'errors' => $validation['errors']
                ];
            }

            $result = $this->moduleModel->create($validation['data']);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Module created successfully',
                    'module_id' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create module'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Module creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update an existing module
     */
    public function updateModule($id, $data)
    {
        try {
            // Validate input data
            $validation = $this->moduleModel->validate($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $validation['errors']),
                    'errors' => $validation['errors']
                ];
            }

            $result = $this->moduleModel->update($id, $validation['data']);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Module updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update module'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Module update failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a module
     */
    public function deleteModule($id)
    {
        try {
            $module = $this->moduleModel->find($id);
            if (!$module) {
                return [
                    'success' => false,
                    'message' => 'Module not found'
                ];
            }

            $result = $this->moduleModel->delete($id);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Module deleted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete module'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Module deletion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Activate a module
     */
    public function activateModule($id)
    {
        try {
            $result = $this->moduleModel->activate($id);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Module activated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to activate module'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Module activation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Deactivate a module
     */
    public function deactivateModule($id)
    {
        try {
            $result = $this->moduleModel->deactivate($id);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Module deactivated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to deactivate module'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Module deactivation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get modules by category
     */
    public function getModulesByCategory($category)
    {
        try {
            return $this->moduleModel->getByCategory($category);
        } catch (Exception $e) {
            error_log("ModuleService getModulesByCategory error for category {$category}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get module statistics
     */
    public function getModuleStats()
    {
        try {
            return $this->moduleModel->getStats();
        } catch (Exception $e) {
            error_log('ModuleService getModuleStats error: ' . $e->getMessage());
            return [
                'total_modules' => 0,
                'active_modules' => 0,
                'inactive_modules' => 0
            ];
        }
    }

    /**
     * Install a module from a package
     */
    public function installModuleFromPackage($packagePath)
    {
        try {
            // This is a simplified implementation
            // In a real system, you would validate the package, check dependencies,
            // and properly extract and install the module

            // For now, we'll just return a message indicating the process
            return [
                'success' => true,
                'message' => 'Module installation from package is not fully implemented yet',
                'package' => $packagePath
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Module installation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update module configuration
     */
    /**
     * Get module configuration path
     */
    private function getConfigPath()
    {
        return BASE_PATH . '/storage/app/modules_config.json';
    }

    /**
     * Load all module configurations
     */
    private function loadModuleConfig()
    {
        $path = $this->getConfigPath();
        if (!file_exists($path)) {
            return [];
        }
        $content = file_get_contents($path);
        return json_decode($content, true) ?? [];
    }

    /**
     * Save module configuration
     */
    private function saveModuleConfig($config)
    {
        $path = $this->getConfigPath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, json_encode($config, JSON_PRETTY_PRINT));
    }

    /**
     * Update module configuration
     */
    public function updateModuleConfig($moduleId, $config)
    {
        try {
            $module = $this->moduleModel->find($moduleId);
            if (!$module) {
                return [
                    'success' => false,
                    'message' => 'Module not found'
                ];
            }

            // Load existing config
            $allConfig = $this->loadModuleConfig();

            // Key by module ID (or name if preferred, ID is safer for renames)
            $moduleKey = 'module_' . $moduleId;

            // Initialize if not exists
            if (!isset($allConfig[$moduleKey])) {
                $allConfig[$moduleKey] = [];
            }

            // Merge new config
            $allConfig[$moduleKey] = array_merge($allConfig[$moduleKey], $config);

            // Save back
            $this->saveModuleConfig($allConfig);

            // Update module basic fields in DB if present in config
            // (e.g. description, status which are in DB)
            if (isset($config['description'])) {
                $this->moduleModel->update($moduleId, ['description' => $config['description']]);
            }
            if (isset($config['status'])) { // 'active'/'inactive' mapping
                $isActive = ($config['status'] === 'active') ? 1 : 0;
                $this->moduleModel->update($moduleId, ['is_active' => $isActive]);
            }

            return [
                'success' => true,
                'message' => 'Module configuration updated successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Module configuration update failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Merge config into module data
     */
    private function mergeConfigIntoModule($module)
    {
        if (!$module) return null;

        $allConfig = $this->loadModuleConfig();
        $moduleKey = 'module_' . $module['id'];

        if (isset($allConfig[$moduleKey])) {
            // Merge stored config into module array
            // Config takes precedence for settings, but DB holds truth for ID/Name
            $module = array_merge($module, $allConfig[$moduleKey]);
        }
        return $module;
    }

    /**
     * Get all modules (merged with config)
     */
    public function getAllModules()
    {
        try {
            $modules = $this->moduleModel->getAll();
            foreach ($modules as &$module) {
                $module = $this->mergeConfigIntoModule($module);
            }
            return $modules;
        } catch (Exception $e) {
            error_log('ModuleService getAllModules error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get active modules only (merged with config)
     */
    public function getActiveModules()
    {
        try {
            $modules = $this->moduleModel->getActive();
            foreach ($modules as &$module) {
                $module = $this->mergeConfigIntoModule($module);
            }
            return $modules;
        } catch (Exception $e) {
            error_log('ModuleService getActiveModules error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Find a module by ID (merged with config)
     */
    public function getModule($id)
    {
        try {
            $module = $this->moduleModel->find($id);
            return $this->mergeConfigIntoModule($module);
        } catch (Exception $e) {
            error_log("ModuleService getModule error for ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find a module by name (merged with config)
     */
    public function getModuleByName($name)
    {
        try {
            $module = $this->moduleModel->findByName($name);
            return $this->mergeConfigIntoModule($module);
        } catch (Exception $e) {
            error_log("ModuleService getModuleByName error for name {$name}: " . $e->getMessage());
            return null;
        }
    }
}
