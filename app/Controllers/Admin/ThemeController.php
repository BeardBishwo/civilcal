<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ThemeManager;
use App\Services\FileService;
use App\Services\AuditLogger;
use Exception;

class ThemeController extends Controller
{
    private $themeManager;

    public function __construct()
    {
        parent::__construct();
        $this->themeManager = new ThemeManager();

        // Check admin authentication
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            $this->redirect('/login');
        }
    }

    public function updateSettings($id)
    {
        if (!$this->isAjax()) {
            $this->redirect('/admin/themes');
            return;
        }

        try {
            $payload = $_POST;
            if (empty($payload)) {
                $raw = file_get_contents('php://input');
                $json = json_decode($raw, true);
                if (is_array($json)) {
                    $payload = $json;
                }
            }

            $allowed = ['primary', 'secondary', 'accent', 'background', 'text', 'text_secondary', 'dark_mode_enabled', 'typography_style'];
            $settings = [];
            foreach ($allowed as $k) {
                if (array_key_exists($k, $payload)) {
                    $v = $payload[$k];
                    if ($k === 'dark_mode_enabled') {
                        $v = filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                        $v = (bool)$v;
                    }
                    $settings[$k] = $v;
                }
            }
            if (empty($settings)) {
                $this->error('No settings provided');
                return;
            }

            $res = $this->themeManager->updateThemeSettings((int)$id, $settings);
            if ($res['success']) {
                AuditLogger::info('theme_settings_updated', ['theme_id' => (int)$id, 'keys' => array_keys($settings)]);
                $this->success($res['message'], ['settings' => $settings]);
            } else {
                AuditLogger::warning('theme_settings_update_failed', ['theme_id' => (int)$id, 'message' => $res['message'] ?? null]);
                $this->error($res['message'] ?? 'Failed');
            }
        } catch (Exception $e) {
            $this->error('Update failed: ' . $e->getMessage());
        }
    }

    public function index()
    {
        try {
            $themes = $this->themeManager->getAllThemes();
            $stats = $this->themeManager->getThemeStats();
            $activeTheme = $this->getActiveThemeData();

            // Add is_active key for backward compatibility with views
            foreach ($themes as &$theme) {
                $theme['is_active'] = ($theme['status'] === 'active') ? 1 : 0;
            }
            unset($theme); // Break the reference

            // Prepare data for the view
            $data = [
                'currentPage' => 'themes',
                'themes' => $themes,
                'activeTheme' => $activeTheme,
                'stats' => $stats,
                'title' => 'Themes Management - Admin Panel'
            ];

            // Load the admin view
            $this->view->render('admin/themes/index', $data);
        } catch (Exception $e) {
            error_log("Theme Index Error: " . $e->getMessage());
            $this->view->render('admin/themes/index', [
                'error' => 'Failed to load themes: ' . $e->getMessage(),
                'title' => 'Themes Management - Admin Panel'
            ]);
        }
    }

    public function activate()
    {
        if (!$this->isAjax()) {
            $this->redirect('/admin/themes');
            return;
        }

        try {
            if (!isset($_POST['theme_id'])) {
                $this->error('Theme ID required');
                return;
            }

            $themeId = intval($_POST['theme_id']);
            $result = $this->themeManager->activateTheme($themeId);

            // Log the action
            error_log("Theme Admin Activity: theme_activated - Theme ID: {$themeId}");

            if ($result['success']) {
                AuditLogger::info('theme_activated', ['theme_id' => $themeId]);
                $this->success('Theme activated successfully', $result);
            } else {
                AuditLogger::warning('theme_activate_failed', ['theme_id' => $themeId, 'message' => $result['message'] ?? null]);
                $this->error($result['message']);
            }
        } catch (Exception $e) {
            error_log("Theme Activation Error: " . $e->getMessage());
            $this->error('Activation failed: ' . $e->getMessage());
        }
    }

    public function deactivate()
    {
        if (!$this->isAjax()) {
            $this->redirect('/admin/themes');
            return;
        }

        try {
            if (!isset($_POST['theme_id'])) {
                $this->error('Theme ID required');
                return;
            }

            $themeId = intval($_POST['theme_id']);
            $result = $this->themeManager->deactivateTheme($themeId);

            // Log the action
            error_log("Theme Admin Activity: theme_deactivated - Theme ID: {$themeId}");

            if ($result['success']) {
                AuditLogger::info('theme_deactivated', ['theme_id' => $themeId]);
                $this->success('Theme deactivated successfully', $result);
            } else {
                AuditLogger::warning('theme_deactivate_failed', ['theme_id' => $themeId, 'message' => $result['message'] ?? null]);
                $this->error($result['message']);
            }
        } catch (Exception $e) {
            error_log("Theme Deactivation Error: " . $e->getMessage());
            $this->error('Deactivation failed: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        if (!$this->isAjax()) {
            $this->redirect('/admin/themes');
            return;
        }

        try {
            if (!isset($_POST['theme_id'])) {
                $this->error('Theme ID required');
                return;
            }

            $themeId = intval($_POST['theme_id']);
            $createBackup = isset($_POST['create_backup']) ? (bool)$_POST['create_backup'] : true;
            $result = $this->themeManager->deleteTheme($themeId, $createBackup);

            // Log the action
            $action = $createBackup ? 'theme_deleted_with_backup' : 'theme_deleted';
            error_log("Theme Admin Activity: {$action} - Theme ID: {$themeId}");

            if ($result['success']) {
                AuditLogger::info($action, ['theme_id' => $themeId]);
                $this->success('Theme deleted successfully', $result);
            } else {
                AuditLogger::warning('theme_delete_failed', ['theme_id' => $themeId, 'message' => $result['message'] ?? null]);
                $this->error($result['message']);
            }
        } catch (Exception $e) {
            error_log("Theme Deletion Error: " . $e->getMessage());
            $this->error('Deletion failed: ' . $e->getMessage());
        }
    }

    public function restore()
    {
        if (!$this->isAjax()) {
            $this->redirect('/admin/themes');
            return;
        }

        try {
            if (!isset($_POST['theme_id'])) {
                $this->error('Theme ID required');
                return;
            }

            $themeId = intval($_POST['theme_id']);
            $result = $this->themeManager->restoreTheme($themeId);

            // Log the action
            error_log("Theme Admin Activity: theme_restored - Theme ID: {$themeId}");

            if ($result['success']) {
                AuditLogger::info('theme_restored', ['theme_id' => $themeId]);
                $this->success('Theme restored successfully', $result);
            } else {
                AuditLogger::warning('theme_restore_failed', ['theme_id' => $themeId, 'message' => $result['message'] ?? null]);
                $this->error($result['message']);
            }
        } catch (Exception $e) {
            error_log("Theme Restoration Error: " . $e->getMessage());
            $this->error('Restoration failed: ' . $e->getMessage());
        }
    }

    public function hardDelete()
    {
        if (!$this->isAjax()) {
            $this->redirect('/admin/themes');
            return;
        }

        try {
            if (!isset($_POST['theme_id'])) {
                $this->error('Theme ID required');
                return;
            }

            $themeId = intval($_POST['theme_id']);
            $result = $this->themeManager->hardDeleteTheme($themeId);

            // Log the action
            error_log("Theme Admin Activity: theme_hard_deleted - Theme ID: {$themeId}");

            if ($result['success']) {
                AuditLogger::info('theme_hard_deleted', ['theme_id' => $themeId]);
                $this->success('Theme permanently deleted', $result);
            } else {
                AuditLogger::warning('theme_hard_delete_failed', ['theme_id' => $themeId, 'message' => $result['message'] ?? null]);
                $this->error($result['message']);
            }
        } catch (Exception $e) {
            error_log("Theme Hard Delete Error: " . $e->getMessage());
            $this->error('Hard deletion failed: ' . $e->getMessage());
        }
    }

    public function upload()
    {
        if (!$this->isAjax()) {
            $this->redirect('/admin/themes');
            return;
        }

        try {
            if (!isset($_FILES['theme_zip']) || $_FILES['theme_zip']['error'] !== UPLOAD_ERR_OK) {
                $this->error('No file uploaded or upload error');
                return;
            }

            // Use FileService for "Paranoid-Grade" secure Theme upload
            $upload = FileService::uploadTheme($_FILES['theme_zip']);
            if (!$upload['success']) {
                $this->error($upload['error'] ?? 'Upload failed');
                return;
            }

            $result = $this->themeManager->installThemeFromZip($upload['path']);

            // Log the action
            if ($result['success']) {
                error_log("Theme Admin Activity: theme_uploaded - Theme: " . ($result['theme_name'] ?? 'Unknown'));
                AuditLogger::info('theme_uploaded', [
                    'theme_name' => $result['theme_name'] ?? null,
                    'checksum' => $result['checksum'] ?? null,
                    'file_size' => $result['file_size'] ?? null
                ]);
                $this->success('Theme uploaded successfully', $result);
            } else {
                AuditLogger::warning('theme_upload_failed', ['message' => $result['message'] ?? null]);
                $this->error($result['message']);
            }
        } catch (Exception $e) {
            error_log("Theme Upload Error: " . $e->getMessage());
            $this->error('Upload failed: ' . $e->getMessage());
        }
    }

    public function validate()
    {
        if (!$this->isAjax()) {
            $this->redirect('/admin/themes');
            return;
        }

        try {
            if (!isset($_POST['theme_name'])) {
                $this->error('Theme name required');
                return;
            }

            $themeName = $_POST['theme_name'];
            $result = $this->themeManager->validateTheme($themeName);

            $this->success('Validation completed', $result);
        } catch (Exception $e) {
            error_log("Theme Validation Error: " . $e->getMessage());
            $this->error('Validation failed: ' . $e->getMessage());
        }
    }

    public function search()
    {
        if (!$this->isAjax()) {
            $this->redirect('/admin/themes');
            return;
        }

        try {
            $query = $_GET['q'] ?? '';
            $limit = intval($_GET['limit'] ?? 20);

            $themes = $this->themeManager->searchThemes($query, $limit);

            $this->success('Search completed', ['themes' => $themes]);
        } catch (Exception $e) {
            error_log("Theme Search Error: " . $e->getMessage());
            $this->error('Search failed: ' . $e->getMessage());
        }
    }

    public function backups()
    {
        try {
            $themeId = $_GET['theme_id'] ?? null;
            $backups = $this->themeManager->getThemeBackups($themeId);

            $data = [
                'currentPage' => 'theme-backups',
                'backups' => $backups,
                'title' => 'Theme Backups - Admin Panel'
            ];

            $this->view->render('admin/themes/backups', $data);
        } catch (Exception $e) {
            error_log("Theme Backups Error: " . $e->getMessage());
            $this->view->render('admin/themes/backups', [
                'error' => 'Failed to load backups: ' . $e->getMessage(),
                'title' => 'Theme Backups - Admin Panel'
            ]);
        }
    }

    public function bulkAction()
    {
        if (!$this->isAjax()) {
            $this->redirect('/admin/themes');
            return;
        }

        try {
            if (!isset($_POST['action']) || !isset($_POST['theme_ids'])) {
                $this->error('Action and theme IDs required');
                return;
            }

            $action = $_POST['action'];
            $themeIds = array_map('intval', $_POST['theme_ids']);
            $results = [];

            foreach ($themeIds as $themeId) {
                switch ($action) {
                    case 'activate':
                        $result = $this->themeManager->activateTheme($themeId);
                        break;
                    case 'deactivate':
                        $result = $this->themeManager->deactivateTheme($themeId);
                        break;
                    case 'delete':
                        $createBackup = isset($_POST['create_backup']);
                        $result = $this->themeManager->deleteTheme($themeId, $createBackup);
                        break;
                    default:
                        $result = ['success' => false, 'message' => 'Invalid action'];
                }
                $results[] = $result;
            }

            // Log the bulk action
            error_log("Theme Admin Activity: bulk_theme_action - Action: {$action}, Count: " . count($themeIds));

            $this->success("Bulk action '{$action}' completed", [
                'results' => $results
            ]);
        } catch (Exception $e) {
            error_log("Theme Bulk Action Error: " . $e->getMessage());
            $this->error('Bulk action failed: ' . $e->getMessage());
        }
    }

    private function getActiveThemeData()
    {
        try {
            $themes = $this->themeManager->getAllThemes('active');
            return $themes[0] ?? null;
        } catch (Exception $e) {
            error_log("Get Active Theme Error: " . $e->getMessage());
            return null;
        }
    }

    private function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function preview()
    {
        $theme = $this->getActiveThemeData();
        $this->view->render('admin/themes/preview', ['currentPage' => 'themes', 'activeTheme' => $theme, 'title' => 'Theme Preview']);
    }

    public function previewById($id)
    {
        try {
            $themes = $this->themeManager->getAllThemes();
            $selected = null;
            foreach ($themes as $t) {
                if ((int)($t['id'] ?? 0) === (int)$id) {
                    $selected = $t;
                    break;
                }
            }
            if (!$selected) {
                $this->redirect('/admin/themes');
                return;
            }
            $this->view->render('admin/themes/preview', ['currentPage' => 'themes', 'activeTheme' => $selected, 'title' => 'Theme Preview']);
        } catch (Exception $e) {
            $this->redirect('/admin/themes');
        }
    }

    public function details($slug)
    {
        header('Content-Type: application/json');
        try {
            $themes = $this->themeManager->getAllThemes();
            $found = null;
            foreach ($themes as $t) {
                if (is_numeric($slug)) {
                    if ((int)($t['id'] ?? 0) === (int)$slug) {
                        $found = $t;
                        break;
                    }
                } else {
                    $candidate = $t['slug'] ?? ($t['name'] ?? null);
                    if ($candidate && strcasecmp($candidate, $slug) === 0) {
                        $found = $t;
                        break;
                    }
                }
            }
            if (!$found) {
                echo json_encode(['success' => false, 'message' => 'Theme not found']);
                return;
            }
            echo json_encode(['success' => true, 'data' => $found]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function saveColors($id)
    {
        $payload = $_POST;
        $settings = [
            'colors' => [
                'primary' => $payload['primary_color'] ?? null,
                'secondary' => $payload['secondary_color'] ?? null,
                'accent' => $payload['accent_color'] ?? null,
                'background' => $payload['background_color'] ?? null,
                'text' => $payload['text_color'] ?? null,
                'text_secondary' => $payload['text_secondary_color'] ?? null,
            ]
        ];
        $res = $this->themeManager->updateThemeSettings((int)$id, $settings);
        $this->json($res);
    }

    public function saveTypography($id)
    {
        $payload = $_POST;
        $settings = [
            'typography' => [
                'font_family' => $payload['font_family'] ?? null,
                'heading_size' => $payload['heading_size'] ?? null,
                'body_size' => $payload['body_size'] ?? null,
                'line_height' => $payload['line_height'] ?? null,
            ]
        ];
        $res = $this->themeManager->updateThemeSettings((int)$id, $settings);
        $this->json($res);
    }

    public function saveFeatures($id)
    {
        $payload = $_POST;
        $settings = [
            'features' => [
                'dark_mode' => isset($payload['dark_mode']),
                'animations' => isset($payload['animations']),
                'glassmorphism' => isset($payload['glassmorphism']),
                '3d_effects' => isset($payload['3d_effects']),
            ]
        ];
        $res = $this->themeManager->updateThemeSettings((int)$id, $settings);
        $this->json($res);
    }

    public function saveLayout($id)
    {
        $payload = $_POST;
        $settings = [
            'layout' => [
                'header_style' => $payload['header_style'] ?? null,
                'footer_layout' => $payload['footer_layout'] ?? null,
                'container_width' => $payload['container_width'] ?? null,
            ]
        ];
        $res = $this->themeManager->updateThemeSettings((int)$id, $settings);
        $this->json($res);
    }

    public function saveCustomCss($id)
    {
        $payload = $_POST;
        $settings = [
            'custom_css' => $payload['custom_css'] ?? ''
        ];
        $res = $this->themeManager->updateThemeSettings((int)$id, $settings);
        $this->json($res);
    }

    public function resetCustomizations($id)
    {
        $settings = ['colors' => [], 'typography' => [], 'features' => [], 'layout' => [], 'custom_css' => ''];
        $res = $this->themeManager->updateThemeSettings((int)$id, $settings);
        $this->json($res);
    }
}
