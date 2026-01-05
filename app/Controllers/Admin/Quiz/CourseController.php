<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\SyllabusService;

class CourseController extends Controller
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
     * Display the Premium Course Table
     */
    public function index()
    {
        // Fetch only Courses (type = 'course')
        // Parent ID is typically NULL for root courses
        $sql = "SELECT * FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC";
        $courses = $this->db->query($sql)->fetchAll();

        // Calculate Stats
        $stats = [
            'total' => count($courses),
            'active' => count(array_filter($courses, fn($c) => $c['is_active'] == 1)),
        ];

        return $this->view('admin/quiz/courses/index', [
            'courses' => $courses,
            'stats' => $stats
        ]);
    }

    /**
     * AJAX: Toggle Status
     */
    public function toggleStatus()
    {
        $id = $_POST['id'] ?? null;
        $val = $_POST['val'] ?? 0;

        if ($id) {
            $this->db->update('syllabus_nodes', ['is_active' => $val], "id = :id", ['id' => $id]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        }
    }

    /**
     * AJAX: Reorder Courses (Drag & Drop)
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
     * Store New Course
     */
    public function store()
    {
        $title = $_POST['title'] ?? '';
        $icon = $_POST['icon'] ?? 'fa-graduation-cap';
        $image = $_POST['image'] ?? null;

        if (empty($title)) {
            echo json_encode(['status' => 'error', 'message' => 'Title is required']);
            return;
        }

        // Generate Slug
        $slug = $this->syllabusService->slugify($title);
        
        // Get Max Order
        $stmt = $this->db->getPdo()->query("SELECT MAX(order_index) FROM syllabus_nodes WHERE type = 'course'");
        $maxOrder = $stmt->fetchColumn();

        $data = [
            'parent_id' => null, // Root
            'title' => $title,
            'slug' => $slug,
            'type' => 'course', // New Type
            'icon' => $icon,
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
     * Delete Course
     */
    public function delete($id)
    {
        if ($this->syllabusService->deleteNode($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
}
