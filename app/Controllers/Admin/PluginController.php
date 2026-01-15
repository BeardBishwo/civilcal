<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\FileService;
use App\Services\AuditLogger;
use App\Services\PluginManager;
use App\Core\Database;

class PluginController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Check admin authentication
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        // Load plugins from PluginManager
        $pm = new PluginManager();
        $plugins = $pm->scanPlugins();
        $activePlugins = array_values(array_filter($plugins, function ($p) {
            return !empty($p['is_active']);
        }));

        // Load the plugins management view
        $data = [
            'plugins' => $plugins,
            'activePlugins' => $activePlugins,
            'page_title' => 'Plugin Management'
        ];
        $this->view->render('admin/plugins/index', $data);
    }

    public function upload()
    {
        header('Content-Type: application/json');
        try {
            if (!isset($_FILES['plugin_zip']) || ($_FILES['plugin_zip']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
                return;
            }

            // Use FileService for secure Plugin upload (Binary Scanning + Entropy Filenames)
            $upload = FileService::uploadAdminFile($_FILES['plugin_zip'], 'plugin');
            if (!$upload['success']) {
                echo json_encode(['success' => false, 'message' => $upload['error'] ?? 'Upload failed']);
                return;
            }

            $zipPath = $upload['path'];
            $pluginName = 'Unknown';
            $checksum = @hash_file('sha256', $zipPath) ?: null;
            $size = @filesize($zipPath) ?: null;

            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === true) {
                $manifestIndex = $zip->locateName('plugin.json');
                if ($manifestIndex !== false) {
                    $manifest = $zip->getFromIndex($manifestIndex);
                    $data = json_decode($manifest, true);
                    if (is_array($data)) {
                        $pluginName = $data['name'] ?? ($data['id'] ?? $pluginName);
                    }
                }
                $zip->close();
            }

            AuditLogger::info('plugin_uploaded', [
                'plugin_name' => $pluginName,
                'checksum' => $checksum,
                'file_size' => $size
            ]);

            // Install plugin from uploaded zip
            $pm = new PluginManager();
            $installed = $pm->installPlugin($zipPath);
            if ($installed) {
                AuditLogger::info('plugin_installed', [
                    'plugin_name' => $pluginName,
                    'checksum' => $checksum
                ]);
                echo json_encode([
                    'success' => true,
                    'message' => 'Plugin uploaded and installed successfully',
                    'data' => [
                        'plugin_name' => $pluginName,
                        'checksum' => $checksum,
                        'file_size' => $size
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Plugin upload succeeded but installation failed'
                ]);
            }
        } catch (\Exception $e) {
            AuditLogger::error('plugin_upload_exception', ['message' => $e->getMessage()]);
            echo json_encode(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    public function toggle($slugParam = null, $actionParam = null)
    {
        header('Content-Type: application/json');
        try {
            $plugin = $_POST['plugin'] ?? $_POST['plugin_name'] ?? $slugParam;
            $action = $_POST['action'] ?? $actionParam;
            if (!$plugin || !$action) {
                echo json_encode(['success' => false, 'message' => 'Missing plugin or action']);
                return;
            }

            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT slug, name FROM plugins WHERE slug = ? OR name = ? LIMIT 1");
            $stmt->execute([$plugin, $plugin]);
            $row = $stmt->fetch();
            if (!$row) {
                echo json_encode(['success' => false, 'message' => 'Plugin not found']);
                return;
            }
            $slug = $row['slug'];

            $manager = new PluginManager();
            if ($action === 'enable') {
                $ok = $manager->activatePlugin($slug);
                if ($ok) {
                    AuditLogger::info('plugin_enabled', ['slug' => $slug, 'name' => $row['name']]);
                    echo json_encode(['success' => true, 'message' => 'Plugin enabled']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to enable plugin']);
                }
                return;
            }
            if ($action === 'disable') {
                $ok = $manager->deactivatePlugin($slug);
                if ($ok) {
                    AuditLogger::info('plugin_disabled', ['slug' => $slug, 'name' => $row['name']]);
                    echo json_encode(['success' => true, 'message' => 'Plugin disabled']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to disable plugin']);
                }
                return;
            }

            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        } catch (\Throwable $e) {
            AuditLogger::error('plugin_toggle_exception', ['message' => $e->getMessage()]);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function details($slug)
    {
        header('Content-Type: application/json');
        try {
            $pm = new PluginManager();
            $plugin = $pm->getPlugin($slug);
            if (!$plugin) {
                echo json_encode(['success' => false, 'message' => 'Plugin not found']);
                return;
            }
            echo json_encode(['success' => true, 'data' => $plugin]);
        } catch (\Throwable $e) {
            AuditLogger::error('plugin_details_exception', ['slug' => $slug, 'message' => $e->getMessage()]);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function delete($slug)
    {
        header('Content-Type: application/json');
        try {
            $pm = new PluginManager();
            $ok = $pm->deletePlugin($slug);
            if ($ok) {
                AuditLogger::info('plugin_deleted', ['slug' => $slug]);
                echo json_encode(['success' => true, 'message' => 'Plugin deleted']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete plugin']);
            }
        } catch (\Throwable $e) {
            AuditLogger::error('plugin_delete_exception', ['slug' => $slug, 'message' => $e->getMessage()]);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function refresh()
    {
        header('Content-Type: application/json');
        try {
            $pm = new PluginManager();
            $list = $pm->scanPlugins();
            echo json_encode(['success' => true, 'count' => count($list)]);
        } catch (\Throwable $e) {
            AuditLogger::error('plugin_refresh_exception', ['message' => $e->getMessage()]);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
