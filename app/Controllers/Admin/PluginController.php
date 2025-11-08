<?php
namespace App\Controllers\Admin;

use App\Core\Controller;

class PluginController extends Controller
{
    public function index()
    {
        // Mock data for plugins
        $plugins = [];
        $activePlugins = [];
        
        // Load the plugins management view
        include __DIR__ . '/../../Views/admin/plugins/index.php';
    }
}
?>
