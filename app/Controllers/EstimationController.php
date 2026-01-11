<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Services\EstimationService;
use App\Services\CacheService;

/**
 * Estimation Controller (Refactored)
 * 
 * Handles construction estimation and BOQ using EstimationService
 * All business logic extracted to service layer
 */
class EstimationController extends Controller
{
    private $estimationService;
    private $cache;

    public function __construct()
    {
        parent::__construct();
        $this->estimationService = new EstimationService();
        $this->cache = CacheService::getInstance();
    }

    /**
     * Display the spreadsheet interface
     */
    public function sheet($projectId = null)
    {
        // Get or create default project
        if (!$projectId) {
            $userId = $this->auth->id() ?? 0;
            $projectId = $this->estimationService->getOrCreateDefaultProject($userId);
        }

        // Cache project data for 5 minutes
        $cacheKey = "project_{$projectId}";
        $project = $this->cache->remember($cacheKey, 300, function() use ($projectId) {
            return $this->estimationService->getProject($projectId);
        });

        $this->view('estimation/sheet', [
            'title' => 'Building Estimation & BOQ - ' . ($project['name'] ?? 'New Project'),
            'project' => $project,
            'gridData' => $project['grid_data'] ?? null
        ]);
    }

    /**
     * API: Get Item Master list
     */
    public function getItems()
    {
        // Cache items for 1 hour
        $items = $this->cache->remember('est_items_master', 3600, function() {
            return $this->estimationService->getItemMaster();
        });

        return $this->json(['success' => true, 'data' => $items]);
    }

    /**
     * API: Save Grid Data
     */
    public function saveGrid()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        $validation = Validator::validate($input, [
            'project_id' => 'required|integer',
            'data' => 'required'
        ]);

        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 400);
        }

        $result = $this->estimationService->saveEstimate(
            (int)$input['project_id'],
            $input['data'],
            $this->auth->id()
        );

        // Invalidate cache
        $this->cache->delete("project_{$input['project_id']}");

        return $this->json($result);
    }

    /**
     * API: Get location rates
     */
    public function get_location_rates()
    {
        $locationId = $_GET['location_id'] ?? null;
        $muni = $_GET['muni'] ?? null;
        $district = $_GET['district'] ?? null;

        // Cache rates for 1 hour
        $cacheKey = "rates_{$locationId}_{$muni}_{$district}";
        $result = $this->cache->remember($cacheKey, 3600, function() use ($locationId, $muni, $district) {
            return $this->estimationService->getLocationRates($locationId, $muni, $district);
        });

        return $this->json($result);
    }

    /**
     * API: Get project-specific rates
     */
    public function get_project_rates()
    {
        $projectId = $_GET['project_id'] ?? null;

        if (!$projectId) {
            return $this->json([]);
        }

        // Cache project rates for 30 minutes
        $cacheKey = "project_rates_{$projectId}";
        $rates = $this->cache->remember($cacheKey, 1800, function() use ($projectId) {
            return $this->estimationService->getProjectRates((int)$projectId);
        });

        return $this->json($rates);
    }

    /**
     * Export to Excel
     */
    public function export_excel()
    {
        $projectId = $_GET['project_id'] ?? null;

        if (!$projectId) {
            die("Project ID required");
        }

        $this->estimationService->exportEstimate((int)$projectId, 'excel');
    }

    /**
     * Export to PDF
     */
    public function export_pdf()
    {
        $projectId = $_GET['project_id'] ?? null;

        if (!$projectId) {
            die("Project ID required");
        }

        $this->estimationService->exportEstimate((int)$projectId, 'pdf');
    }

    /**
     * Save as template
     */
    public function save_template()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        $validation = Validator::validate($input, [
            'name' => 'required|min:3|max:255',
            'structure' => 'required'
        ]);

        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 400);
        }

        $result = $this->estimationService->saveTemplate(
            Validator::sanitize($input['name'], 'string'),
            Validator::sanitize($input['description'] ?? '', 'string'),
            $input['structure'],
            $this->auth->id()
        );

        // Invalidate templates cache
        $this->cache->delete('all_templates');

        return $this->json($result);
    }

    /**
     * Get all templates
     */
    public function get_templates()
    {
        // Cache templates for 10 minutes
        $templates = $this->cache->remember('all_templates', 600, function() {
            return $this->estimationService->getTemplates();
        });

        return $this->json(['success' => true, 'templates' => $templates]);
    }

    /**
     * Load template
     */
    public function load_template()
    {
        $templateId = $_GET['template_id'] ?? null;

        if (!$templateId) {
            return $this->json(['error' => 'Template ID required'], 400);
        }

        $result = $this->estimationService->loadTemplate((int)$templateId);

        return $this->json($result);
    }

    /**
     * Get version history
     */
    public function get_versions()
    {
        $projectId = $_GET['project_id'] ?? null;

        if (!$projectId) {
            return $this->json(['error' => 'Project ID required'], 400);
        }

        $versions = $this->estimationService->getVersionHistory((int)$projectId);

        return $this->json(['success' => true, 'versions' => $versions]);
    }

    /**
     * Restore version
     */
    public function restore_version()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $versionId = $_POST['version_id'] ?? null;

        if (!$versionId) {
            return $this->json(['error' => 'Version ID required'], 400);
        }

        $result = $this->estimationService->restoreVersion((int)$versionId);

        // Invalidate project cache
        if ($result['success'] && isset($result['project_id'])) {
            $this->cache->delete("project_{$result['project_id']}");
        }

        return $this->json($result);
    }

    /**
     * Import from Excel
     */
    public function import_excel()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        if (!isset($_FILES['excel_file']) || !isset($_POST['project_id'])) {
            return $this->json(['error' => 'Missing file or project ID'], 400);
        }

        $result = $this->estimationService->importExcel(
            $_FILES['excel_file']['tmp_name'],
            (int)$_POST['project_id']
        );

        // Invalidate project cache
        if ($result['success']) {
            $this->cache->delete("project_{$_POST['project_id']}");
        }

        return $this->json($result);
    }

    /**
     * Save bulk rates
     */
    public function save_bulk_rates()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        $validation = Validator::validate($input, [
            'location_id' => 'required|integer',
            'rates' => 'required'
        ]);

        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 400);
        }

        $result = $this->estimationService->saveBulkRates(
            (int)$input['location_id'],
            $input['rates']
        );

        // Invalidate rates cache
        $this->cache->delete("rates_{$input['location_id']}_*");

        return $this->json($result);
    }

    /**
     * Update project location
     */
    public function update_location()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        $validation = Validator::validate($input, [
            'project_id' => 'required|integer',
            'muni' => 'required',
            'district' => 'required'
        ]);

        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 400);
        }

        $result = $this->estimationService->updateProjectLocation(
            (int)$input['project_id'],
            Validator::sanitize($input['location'], 'string'),
            Validator::sanitize($input['muni'], 'string'),
            Validator::sanitize($input['district'], 'string')
        );

        // Invalidate caches
        $this->cache->delete("project_{$input['project_id']}");
        $this->cache->delete("project_rates_{$input['project_id']}");

        return $this->json($result);
    }

    /**
     * Rate manager UI
     */
    public function rates_manager()
    {
        $provinces = $this->cache->remember('provinces_list', 3600, function() {
            return $this->estimationService->getProvinces();
        });

        $this->view('estimation/rates_manager', [
            'title' => 'District Rate Manager',
            'provinces' => $provinces
        ]);
    }
}
