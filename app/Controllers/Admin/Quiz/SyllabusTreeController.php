<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Services\SyllabusService;
use Exception;

/**
 * Enhanced Syllabus Tree Controller
 * 
 * Manages the recursive syllabus tree structure
 */
class SyllabusTreeController extends Controller
{
    private $syllabusService;

    public function __construct()
    {
        parent::__construct();

        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            header('Location: ' . app_base_url('login'));
            exit;
        }

        $this->syllabusService = new SyllabusService();
    }

    /**
     * Main tree view
     */
    public function index()
    {
        $level = $_GET['level'] ?? null;
        $tree = $this->syllabusService->getTree($level);

        $this->view->render('admin/quiz/syllabus-tree/index', [
            'page_title' => 'Syllabus Tree Manager',
            'tree' => $tree,
            'current_level' => $level
        ]);
    }

    /**
     * Get tree as JSON (for AJAX)
     */
    public function getTreeJson()
    {
        try {
            $level = $_GET['level'] ?? null;
            $tree = $this->syllabusService->getTree($level);
            $this->jsonResponse(['success' => true, 'tree' => $tree]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Create new node
     */
    public function createNode()
    {
        try {
            $data = [
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'title' => $_POST['title'] ?? '',
                'slug' => $_POST['slug'] ?? '',
                'type' => $_POST['type'] ?? 'topic',
                'description' => $_POST['description'] ?? '',
                'level' => $_POST['level'] ?? null,
                'order' => isset($_POST['order']) ? (int)$_POST['order'] : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if (empty($data['title'])) {
                throw new Exception("Title is required");
            }

            $nodeId = $this->syllabusService->createNode($data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Node created successfully',
                'node_id' => $nodeId
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Update node
     */
    public function updateNode($nodeId)
    {
        try {
            $data = [];

            if (isset($_POST['title'])) $data['title'] = $_POST['title'];
            if (isset($_POST['slug'])) $data['slug'] = $_POST['slug'];
            if (isset($_POST['type'])) $data['type'] = $_POST['type'];
            if (isset($_POST['description'])) $data['description'] = $_POST['description'];
            if (isset($_POST['level'])) $data['level'] = $_POST['level'];
            if (isset($_POST['order'])) $data['order'] = (int)$_POST['order'];
            if (isset($_POST['parent_id'])) $data['parent_id'] = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            if (isset($_POST['is_active'])) $data['is_active'] = $_POST['is_active'] ? 1 : 0;

            $this->syllabusService->updateNode($nodeId, $data);

            $this->jsonResponse(['success' => true, 'message' => 'Node updated']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Delete node
     */
    public function deleteNode($nodeId)
    {
        try {
            $this->syllabusService->deleteNode($nodeId);
            $this->jsonResponse(['success' => true, 'message' => 'Node deleted']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Reorder nodes
     */
    public function reorderNodes()
    {
        try {
            $nodeIds = $_POST['node_ids'] ?? [];

            if (empty($nodeIds) || !is_array($nodeIds)) {
                throw new Exception("Invalid node IDs");
            }

            $this->syllabusService->reorderNodes($nodeIds);

            $this->jsonResponse(['success' => true, 'message' => 'Nodes reordered']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get node details with ancestors
     */
    public function getNodeDetails($nodeId)
    {
        try {
            $data = $this->syllabusService->getNodeWithAncestors($nodeId);
            $this->jsonResponse(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Search nodes
     */
    public function search()
    {
        try {
            $query = $_GET['q'] ?? '';
            $level = $_GET['level'] ?? null;

            if (empty($query)) {
                throw new Exception("Search query is required");
            }

            $results = $this->syllabusService->searchNodes($query, $level, true);

            $this->jsonResponse(['success' => true, 'results' => $results]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get nodes by type
     */
    public function getNodesByType()
    {
        try {
            $type = $_GET['type'] ?? 'topic';
            $level = $_GET['level'] ?? null;

            $nodes = $this->syllabusService->getNodesByType($type, $level);

            $this->jsonResponse(['success' => true, 'nodes' => $nodes]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Helper: JSON response
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
