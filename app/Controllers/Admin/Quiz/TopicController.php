<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\SyllabusService;

class TopicController extends Controller
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
     * Display the Premium Topic Table
     */
    public function index()
    {
        $courseId = $_GET['course_id'] ?? null;
        $educationLevelId = $_GET['education_level_id'] ?? null;
        $categoryId = $_GET['category_id'] ?? null;
        $subCategoryId = $_GET['subcategory_id'] ?? null;

        // Fetch dropdown options for filters
        $courses = $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll();
        
        $eduLevelSql = "SELECT id, title FROM syllabus_nodes WHERE type = 'education_level'";
        $eduLevelParams = [];
        if ($courseId) { $eduLevelSql .= " AND parent_id = :pid"; $eduLevelParams['pid'] = $courseId; }
        $educationLevels = $this->db->query($eduLevelSql . " ORDER BY order_index ASC", $eduLevelParams)->fetchAll();

        $categorySql = "SELECT id, title FROM syllabus_nodes WHERE type = 'category'";
        $categoryParams = [];
        if ($educationLevelId) { $categorySql .= " AND parent_id = :pid"; $categoryParams['pid'] = $educationLevelId; }
        $categories = $this->db->query($categorySql . " ORDER BY order_index ASC", $categoryParams)->fetchAll();

        $subCategorySql = "SELECT id, title FROM syllabus_nodes WHERE type IN ('unit', 'section', 'part')";
        $subCategoryParams = [];
        if ($categoryId) { $subCategorySql .= " AND parent_id = :pid"; $subCategoryParams['pid'] = $categoryId; }
        $subCategories = $this->db->query($subCategorySql . " ORDER BY order_index ASC", $subCategoryParams)->fetchAll();

        // Base Query with Full Joins
        $sql = "SELECT t.*, 
                       sc.title as parent_title, sc.is_active as parent_active,
                       c.id as category_id, c.title as category_title, c.is_active as category_active,
                       el.id as education_level_id, el.title as education_level_title, el.is_active as education_level_active,
                       co.id as course_id, co.title as course_title, co.is_active as course_active
                FROM syllabus_nodes t 
                LEFT JOIN syllabus_nodes sc ON t.parent_id = sc.id 
                LEFT JOIN syllabus_nodes c ON sc.parent_id = c.id
                LEFT JOIN syllabus_nodes el ON c.parent_id = el.id
                LEFT JOIN syllabus_nodes co ON el.parent_id = co.id
                WHERE t.type = 'topic'";

        $params = [];
        if ($courseId) { $sql .= " AND co.id = :course_id"; $params['course_id'] = $courseId; }
        if ($educationLevelId) { $sql .= " AND el.id = :ed_level_id"; $params['ed_level_id'] = $educationLevelId; }
        if ($categoryId) { $sql .= " AND c.id = :category_id"; $params['category_id'] = $categoryId; }
        if ($subCategoryId) { $sql .= " AND t.parent_id = :subcategory_id"; $params['subcategory_id'] = $subCategoryId; }

        $sql .= " ORDER BY (t.is_active = 1 AND IFNULL(sc.is_active, 1) = 1 AND IFNULL(c.is_active, 1) = 1) DESC, co.title ASC, el.title ASC, c.title ASC, sc.title ASC, t.order_index ASC";

        $topics = $this->db->query($sql, $params)->fetchAll();

        // Calculate Stats
        $stats = [
            'total' => count($topics),
            'premium' => count(array_filter($topics, fn($t) => $t['is_premium'] == 1)),
            'total_questions' => array_sum(array_column($topics, 'question_count'))
        ];

        return $this->view('admin/quiz/topics/index', [
            'topics' => $topics,
            'stats' => $stats,
            'courses' => $courses,
            'educationLevels' => $educationLevels,
            'categories' => $categories,
            'subCategories' => $subCategories,
            'selectedCourse' => $courseId,
            'selectedEducationLevel' => $educationLevelId,
            'selectedCategory' => $categoryId,
            'selectedSubCategory' => $subCategoryId
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
     * AJAX: Reorder Topics (Drag & Drop)
     */
    public function reorder()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $order = $data['order'] ?? ($_POST['order'] ?? []);

        foreach ($order as $index => $id) {
             $this->db->update('syllabus_nodes', ['order_index' => $index + 1], "id = :id", ['id' => $id]);
        }
        echo json_encode(['status' => 'success']);
    }

    /**
     * Store New Topic
     */
    public function store()
    {
        $title = $_POST['title'] ?? '';
        $parentId = $_POST['parent_id'] ?? $_POST['subcategory_id'] ?? null;
        $isPremium = isset($_POST['is_premium']) ? 1 : 0;
        $unlockPrice = $_POST['unlock_price'] ?? 0;
        $image = $_POST['image'] ?? null;

        if (empty($title)) {
            echo json_encode(['status' => 'error', 'message' => 'Title is required']);
            return;
        }

        if (empty($parentId)) {
            echo json_encode(['status' => 'error', 'message' => 'Sub Category is required']);
            return;
        }

        // Generate Slug
        $slug = $this->syllabusService->slugify($title);
        
        // Get Max Order
        $sql = "SELECT MAX(order_index) FROM syllabus_nodes WHERE type = 'topic'";
        $params = [];
        if($parentId) {
            $sql .= " AND parent_id = :pid";
            $params['pid'] = $parentId;
        }

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $maxOrder = $stmt->fetchColumn();

        $data = [
            'parent_id' => $parentId, 
            'title' => $title,
            'slug' => $slug,
            'type' => 'topic', 
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
     * Delete Topic
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
     * Delete Topic with Selective Cascade
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
