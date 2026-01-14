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
        $this->requireAdmin(); // SECURITY: Ensure only admins can manage courses
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
        $sql = "SELECT * FROM syllabus_nodes WHERE type = 'course' ORDER BY is_active DESC, order_index ASC";
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
            'image_path' => $image, // Removed icon
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
    /**
     * Get Stats for Deletion Modal
     */
    public function getDeleteStats($id)
    {
        $counts = $this->syllabusService->getChildTypeCounts($id);
        echo json_encode(['status' => 'success', 'counts' => $counts]);
    }

    /**
     * Delete Course with Selective Cascade
     */
    public function delete($id)
    {
        // Check for JSON input (flags)
        $input = json_decode(file_get_contents('php://input'), true);

        // If DELETE_ALL is requested or legacy simple delete
        if (isset($input['delete_all']) && $input['delete_all'] === true) {
            // Standard recursive delete (all children go)
            // We can simulate this by passing ALL types, or just using simple deleteNode if logic permits.
            // But deleteNode might rely on DB cascade. Let's use simple deleteNode for "Delete All".
            if ($this->syllabusService->deleteNode($id)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
            return;
        }

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

        // Fallback for simple GET/POST without flags (Legacy)
        // Default to "Safe Mode" or "Delete All"? Usually legacy behavior implies "Delete All".
        if ($this->syllabusService->deleteNode($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    /**
     * Bulk Delete Courses
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
     * Bulk Duplicate Courses
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
        // Remove existing version suffix if any (e.g., "Course (V1)" -> "Course")
        $baseTitle = preg_replace('/\s*\(V\d+\)$/', '', $baseTitle);

        $newTitle = $baseTitle . ' (V1)';
        $counter = 1;

        while (true) {
            $check = $this->db->findOne('syllabus_nodes', ['title' => $newTitle, 'type' => $original['type']]);
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
        $data['is_active'] = 0; // Default to inactive for safety
        $data['order_index'] = $original['order_index'] + 1;

        $this->syllabusService->createNode($data);
    }
}
