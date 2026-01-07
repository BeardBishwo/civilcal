<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\SyllabusService;

class EducationLevelController extends Controller
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
     * Display the Premium Education Level Table
     */
    public function index()
    {
        // Fetch Education Levels with Parent Course Name
        // Assumes Education Levels are children of Courses in syllabus_nodes
        // Fetch Education Levels with Parent Course Name
        // Assumes Education Levels are children of Courses in syllabus_nodes
        $sql = "SELECT el.*, c.title as parent_title, c.is_active as parent_active 
                FROM syllabus_nodes el 
                LEFT JOIN syllabus_nodes c ON el.parent_id = c.id
                WHERE el.type = 'education_level' 
                ORDER BY (el.is_active = 1 AND IFNULL(c.is_active, 1) = 1) DESC, c.order_index ASC, el.order_index ASC";
        
        $levels = $this->db->query($sql)->fetchAll();

        // Fetch Courses for Dropdown
        $courses = $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll();

        $stats = [
            'total' => count($levels),
            'active' => count(array_filter($levels, fn($l) => $l['is_active'] == 1)),
        ];

        return $this->view('admin/quiz/education_levels/index', [
            'levels' => $levels,
            'courses' => $courses,
            'stats' => $stats
        ]);
    }

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
     * Store New Education Level
     */
    public function store()
    {
        $title = $_POST['title'] ?? '';
        $courseId = $_POST['parent_id'] ?? $_POST['course_id'] ?? null;
        
        if (empty($title) || empty($courseId)) {
            echo json_encode(['status' => 'error', 'message' => 'Title and Course (Parent) are required']);
            return;
        }

        $slug = $this->syllabusService->slugify($title);
        
        $stmt = $this->db->getPdo()->prepare("SELECT MAX(order_index) FROM syllabus_nodes WHERE type = 'education_level' AND parent_id = ?");
        $stmt->execute([$courseId]);
        $maxOrder = $stmt->fetchColumn();

        $data = [
            'parent_id' => $courseId,
            'title' => $title,
            'slug' => $slug,
            'type' => 'education_level',
            'order_index' => $maxOrder + 1,
            'is_active' => 1
        ];

        if ($this->syllabusService->createNode($data)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
    }

    public function delete($id)
    {
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
