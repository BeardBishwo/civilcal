<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\SyllabusService;

class CategoryController extends Controller
{
    protected $db;
    protected $syllabusService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->db = Database::getInstance();
        $this->syllabusService = new SyllabusService();
    }

    /**
     * Display the Premium Category Table
     */
    public function index()
    {
        $courseId = $_GET['course_id'] ?? null;
        $levelId = $_GET['level_id'] ?? null;

        // Fetch dropdown options for filters
        $courses = $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll();
        
        $eduLevelSql = "SELECT id, title FROM syllabus_nodes WHERE type = 'education_level'";
        $eduLevelParams = [];
        if ($courseId) { $eduLevelSql .= " AND parent_id = :pid"; $eduLevelParams['pid'] = $courseId; }
        $levels = $this->db->query($eduLevelSql . " ORDER BY order_index ASC", $eduLevelParams)->fetchAll();

        // Base Query (Categories -> Education Level -> Course)
        $sql = "SELECT c.*, 
                       p.title as parent_title, p.is_active as parent_active,
                       course.title as course_title, course.is_active as course_active 
                FROM syllabus_nodes c 
                LEFT JOIN syllabus_nodes p ON c.parent_id = p.id 
                LEFT JOIN syllabus_nodes course ON p.parent_id = course.id
                WHERE c.type = 'category'";

        $params = [];
        if ($courseId) { $sql .= " AND course.id = :course_id"; $params['course_id'] = $courseId; }
        if ($levelId) { $sql .= " AND c.parent_id = :level_id"; $params['level_id'] = $levelId; }

        $sql .= " ORDER BY (c.is_active = 1 AND IFNULL(p.is_active, 1) = 1 AND IFNULL(course.is_active, 1) = 1) DESC, course.title ASC, p.title ASC, c.order_index ASC";

        $categories = $this->db->query($sql, $params)->fetchAll();

        // Calculate Stats
        $stats = [
            'total' => count($categories),
            'premium' => count(array_filter($categories, fn($c) => $c['is_premium'] == 1)),
            'total_questions' => array_sum(array_column($categories, 'question_count'))
        ];

        return $this->view('admin/quiz/categories/index', [
            'categories' => $categories,
            'stats' => $stats,
            'courses' => $courses,
            'levels' => $levels,
            'selectedCourse' => $courseId,
            'selectedLevel' => $levelId
        ]);
    }

    /**
     * AJAX: Toggle Premium Status instantly
     */
    public function togglePremium()
    {
        $id = $_POST['id'] ?? null;
        $val = $_POST['val'] ?? 0;

        if ($id) {
            $this->db->update('syllabus_nodes', ['is_premium' => $val], "id = :id", ['id' => $id]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        }
    }

    /**
     * AJAX: Reorder Categories (Drag & Drop)
     */
    public function reorder()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $order = $data['order'] ?? ($_POST['order'] ?? []);

        foreach ($order as $index => $id) {
            // Update order_index
             $this->db->update('syllabus_nodes', ['order_index' => $index + 1], "id = :id", ['id' => $id]);
        }
        echo json_encode(['status' => 'success']);
    }

    /**
     * Store New Category
     */
    public function store()
    {
        $title = $_POST['title'] ?? '';
        $parentId = $_POST['parent_id'] ?? $_POST['level_id'] ?? null;
        $isPremium = isset($_POST['is_premium']) ? 1 : 0;
        $unlockPrice = $_POST['unlock_price'] ?? 0;
        $image = $_POST['image'] ?? null;

        if (empty($title)) {
            echo json_encode(['status' => 'error', 'message' => 'Title is required']);
            return;
        }

        // Generate Slug
        $slug = $this->syllabusService->slugify($title);
        
        // Get Max Order
        $sql = "SELECT MAX(order_index) FROM syllabus_nodes WHERE type = 'category'";
        $params = [];
        if($parentId) {
            $sql .= " AND parent_id = :pid";
            $params['pid'] = $parentId;
        } else {
            $sql .= " AND parent_id IS NULL";
        }

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $maxOrder = $stmt->fetchColumn();

        $data = [
            'parent_id' => !empty($parentId) ? $parentId : null, 
            'title' => $title,
            'slug' => $slug,
            'type' => 'category', 
            'is_premium' => $isPremium,
            'unlock_price' => $unlockPrice,
            'image_path' => $image,
            'order_index' => $maxOrder + 1,
            'is_active' => 1
        ];

        if ($this->syllabusService->createNode($data)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
    }

    /**
     * Toggle Status
     */
    public function toggleStatus()
    {
        $id = $_POST['id'] ?? null;
        $val = $_POST['val'] ?? 0;

        if ($id) {
            $this->db->update('syllabus_nodes', ['is_active' => $val], "id = :id", ['id' => $id]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    /**
     * Delete Category
     */
    /**
     * Get Stats for Deletion Modal
     */
    public function getDeleteStats($id)
    {
        $counts = $this->syllabusService->getChildTypeCounts($id);
        echo json_encode(['status' => 'success', 'counts' => $counts]);
    }

    /**
     * Delete Category with Selective Cascade
     */
    public function delete($id)
    {
        // Check for JSON input (flags)
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Selective Delete
        if (isset($input['delete_types']) && is_array($input['delete_types'])) {
            $deleteTypes = $input['delete_types'];
            if ($this->syllabusService->deleteWithPreservation($id, $deleteTypes)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
            return;
        }

        // Fallback
        if ($this->syllabusService->deleteNode($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    /**
     * Bulk Delete
     */
    public function bulkDelete()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'No items selected']);
            return;
        }

        foreach ($ids as $id) {
            $this->syllabusService->deleteNode($id);
        }

        echo json_encode(['status' => 'success']);
    }

    /**
     * Bulk Duplicate
     */
    public function duplicate()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'No items selected']);
            return;
        }

        foreach ($ids as $id) {
            $this->duplicateSingleNode($id);
        }

        echo json_encode(['status' => 'success']);
    }

    private function duplicateSingleNode($id)
    {
        // 1. Fetch Original
        $original = $this->db->findOne('syllabus_nodes', ['id' => $id]);
        if (!$original) return;

        // 2. Determine New Name (Handle V1, V2)
        $baseTitle = $original['title'];
        $baseTitle = preg_replace('/\s*\(V\d+\)$/', '', $baseTitle);
        
        $newTitle = $baseTitle . ' (V1)';
        $counter = 1;

        while (true) {
            $check = $this->db->findOne('syllabus_nodes', [
                'title' => $newTitle, 
                'type' => $original['type'], 
                'parent_id' => $original['parent_id'],
                'is_active' => 0
            ]);
            if (!$check) break;
            $counter++;
            $newTitle = $baseTitle . ' (V' . $counter . ')';
        }

        // 3. Insert New Record
        $data = $original;
        unset($data['id']);
        unset($data['created_at']);
        unset($data['updated_at']);
        $data['title'] = $newTitle;
        $data['slug'] = $this->syllabusService->slugify($newTitle);
        $data['is_active'] = 0; 
        $data['order_index'] = $original['order_index'] + 1;

        $this->syllabusService->createNode($data);
    }
}
