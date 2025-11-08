<?php
namespace App\Controllers;

use App\Services\ExportService;
use App\Models\ExportTemplate;
use App\Core\Controller;
use Exception;

/**
 * Export Controller
 * Handles export-related operations and template management
 */
class ExportController extends Controller {
    private $exportService;
    private $exportTemplateModel;

    public function __construct() {
        parent::__construct();
        $this->exportService = new ExportService();
        $this->exportTemplateModel = new ExportTemplate();
    }

    /**
     * Show export templates management page
     */
    public function templates() {
        try {
            $userId = $this->getCurrentUserId();
            
            // Get template statistics
            $stats = $this->exportTemplateModel->getTemplateStats();
            $userTemplates = $this->exportTemplateModel->getUserTemplates($userId);
            $defaultTemplates = $this->exportTemplateModel->getDefaultTemplates();
            
            $data = [
                'page_title' => 'Export Templates',
                'stats' => $stats,
                'user_templates' => $userTemplates,
                'default_templates' => $defaultTemplates,
                'formats' => ['pdf', 'excel', 'csv', 'json']
            ];
            
            $this->view('user/exports', $data);
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Error loading templates: ' . $e->getMessage());
            $this->redirect('/user/history');
        }
    }

    /**
     * Create new export template
     */
    public function createTemplate() {
        try {
            if (!$this->isPostRequest()) {
                throw new Exception('Invalid request method');
            }

            $userId = $this->getCurrentUserId();
            $data = $this->getRequestData();
            
            // Validate required fields
            $required = ['template_name', 'template_type'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field '{$field}' is required");
                }
            }

            // Check if template name already exists for this user
            if ($this->exportTemplateModel->templateNameExists($userId, $data['template_name'])) {
                throw new Exception('Template name already exists');
            }

            // Validate template type
            $validTypes = ['pdf', 'excel', 'csv', 'json'];
            if (!in_array($data['template_type'], $validTypes)) {
                throw new Exception('Invalid template type');
            }

            // Create template
            $result = $this->exportTemplateModel->createUserTemplate($userId, [
                'template_name' => trim($data['template_name']),
                'template_type' => $data['template_type'],
                'template_config' => $this->getTemplateConfigFromData($data),
                'is_public' => isset($data['is_public']) ? 1 : 0
            ]);

            if ($result) {
                $this->setFlashMessage('success', 'Template created successfully');
            } else {
                throw new Exception('Failed to create template');
            }

        } catch (Exception $e) {
            $this->setFlashMessage('error', $e->getMessage());
        }
        
        $this->redirect('/user/exports/templates');
    }

    /**
     * Export calculations in specified format
     */
    public function export() {
        try {
            if (!$this->isPostRequest()) {
                throw new Exception('Invalid request method');
            }

            $userId = $this->getCurrentUserId();
            $data = $this->getRequestData();
            
            // Validate required fields
            $format = $data['format'] ?? '';
            if (empty($format)) {
                throw new Exception('Export format is required');
            }

            // Validate format
            $validFormats = ['pdf', 'excel', 'xlsx', 'csv', 'json'];
            if (!in_array(strtolower($format), $validFormats)) {
                throw new Exception('Invalid export format');
            }

            // Prepare export options
            $exportOptions = [
                'format' => strtolower($format),
                'template_id' => $data['template_id'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'calculator_type' => $data['calculator_type'] ?? null,
                'record_ids' => isset($data['record_ids']) ? explode(',', $data['record_ids']) : null
            ];

            // Perform export
            $result = $this->exportService->exportCalculations($userId, $exportOptions);

            if ($result['success']) {
                $this->setFlashMessage('success', 
                    'Export completed successfully. File size: ' . $this->formatBytes($result['size']));
                
                // Return download URL
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'download_url' => $result['download_url'],
                    'filename' => $result['filename'],
                    'size' => $result['size']
                ]);
                exit;
            } else {
                throw new Exception('Export failed');
            }

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Get current user ID (implement based on your authentication system)
     */
    private function getCurrentUserId() {
        return $_SESSION['user_id'] ?? 1;
    }

    /**
     * Check if current request is POST
     */
    private function isPostRequest() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Get request data
     */
    private function getRequestData() {
        return $_POST;
    }

    /**
     * Set flash message
     */
    private function setFlashMessage($type, $message) {
        $_SESSION['flash_messages'][$type] = $message;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Get template configuration from form data
     */
    private function getTemplateConfigFromData($data) {
        $config = [];

        // Common settings
        if (isset($data['include_logo'])) $config['include_logo'] = true;
        if (isset($data['include_header'])) $config['include_header'] = true;
        if (isset($data['include_footer'])) $config['include_footer'] = true;
        if (isset($data['include_timestamp'])) $config['include_timestamp'] = true;
        if (isset($data['color_scheme'])) $config['color_scheme'] = $data['color_scheme'];
        if (isset($data['font_size'])) $config['font_size'] = $data['font_size'];

        // PDF specific
        if (isset($data['page_size'])) $config['page_size'] = $data['page_size'];
        if (isset($data['orientation'])) $config['orientation'] = $data['orientation'];

        // Excel specific
        if (isset($data['include_formulas'])) $config['include_formulas'] = true;
        if (isset($data['include_charts'])) $config['include_charts'] = true;
        if (isset($data['auto_format'])) $config['auto_format'] = true;
        if (isset($data['freeze_panes'])) $config['freeze_panes'] = true;

        // CSV specific
        if (isset($data['delimiter'])) $config['delimiter'] = $data['delimiter'];
        if (isset($data['include_headers'])) $config['include_headers'] = true;
        if (isset($data['utf8_bom'])) $config['utf8_bom'] = true;

        // JSON specific
        if (isset($data['include_metadata'])) $config['include_metadata'] = true;
        if (isset($data['pretty_print'])) $config['pretty_print'] = true;

        return $config;
    }
}
?>
