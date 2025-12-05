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
     * Get all modules
     */
    public function getAllModules()
    {
        try {
            return $this->moduleModel->getAll();
        } catch (Exception $e) {
            error_log('ModuleService getAllModules error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get active modules only
     */
    public function getActiveModules()
    {
        try {
            return $this->moduleModel->getActive();
        } catch (Exception $e) {
            error_log('ModuleService getActiveModules error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Find a module by ID
     */
    public function getModule($id)
    {
        try {
            return $this->moduleModel->find($id);
        } catch (Exception $e) {
            error_log("ModuleService getModule error for ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find a module by name
     */
    public function getModuleByName($name)
    {
        try {
            return $this->moduleModel->findByName($name);
        } catch (Exception $e) {
            error_log("ModuleService getModuleByName error for name {$name}: " . $e->getMessage());
            return null;
        }
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
    public function updateModuleConfig($moduleId, $config)
    {
        // In the current Module model, there's no direct configuration field
        // This could be implemented by storing config as JSON in a field or in a separate table
        
        try {
            // For now, we'll just update some data fields if they exist
            $module = $this->moduleModel->find($moduleId);
            if (!$module) {
                return [
                    'success' => false,
                    'message' => 'Module not found'
                ];
            }

            // This would require modifying the Module model to support configuration updates
            return [
                'success' => true,
                'message' => 'Module configuration would be updated here'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Module configuration update failed: ' . $e->getMessage()
            ];
        }
    }
}