<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\PluginManager;

class PluginController extends Controller {
    private $pluginManager;
    
    public function __construct() {
        parent::__construct();
        $this->pluginManager = new PluginManager();
    }
    
    /**
     * Plugin management dashboard
     */
    public function index() {
        $plugins = $this->pluginManager->scanPlugins();
        $activeCalculators = $this->pluginManager->getActiveCalculators();
        
        $this->view('admin/plugins/index', [
            'title' => 'Plugin Management',
            'plugins' => $plugins,
            'activeCalculators' => $activeCalculators
        ]);
    }
    
    /**
     * Upload plugin via admin
     */
    public function upload() {
        if ($_FILES['plugin_zip']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['plugin_zip']['tmp_name'];
            
            if ($this->pluginManager->installPlugin($uploadedFile)) {
                $this->json(['success' => true, 'message' => 'Plugin installed successfully']);
            } else {
                $this->json(['success' => false, 'message' => 'Plugin installation failed']);
            }
        } else {
            $this->json(['success' => false, 'message' => 'No file uploaded or upload error occurred']);
        }
    }
    
    /**
     * Activate/deactivate plugin
     */
    public function toggle($pluginSlug, $action) {
        $result = false;
        $message = '';
        
        if ($action === 'activate') {
            $result = $this->pluginManager->activatePlugin($pluginSlug);
            $message = $result ? 'Plugin activated successfully' : 'Failed to activate plugin';
        } elseif ($action === 'deactivate') {
            $result = $this->pluginManager->deactivatePlugin($pluginSlug);
            $message = $result ? 'Plugin deactivated successfully' : 'Failed to deactivate plugin';
        }
        
        if ($result) {
            $this->json(['success' => true, 'message' => $message]);
        } else {
            $this->json(['success' => false, 'message' => $message]);
        }
    }
    
    /**
     * Delete plugin
     */
    public function delete($pluginSlug) {
        $result = $this->pluginManager->deletePlugin($pluginSlug);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Plugin deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete plugin']);
        }
    }
    
    /**
     * Get plugin details
     */
    public function details($pluginSlug) {
        $plugin = $this->pluginManager->getPlugin($pluginSlug);
        
        if ($plugin) {
            $this->json(['success' => true, 'plugin' => $plugin]);
        } else {
            $this->json(['success' => false, 'message' => 'Plugin not found']);
        }
    }
    
    /**
     * Refresh plugins (re-scan directories)
     */
    public function refresh() {
        $plugins = $this->pluginManager->scanPlugins();
        $activeCalculators = $this->pluginManager->getActiveCalculators();
        
        $this->json([
            'success' => true,
            'message' => 'Plugins refreshed successfully',
            'plugins' => $plugins,
            'calculators_count' => count($activeCalculators)
        ]);
    }
}
?>
