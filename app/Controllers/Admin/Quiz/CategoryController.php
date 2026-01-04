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
        // Fetch only Root Level Categories (Main Categories)
        $sql = "SELECT * FROM syllabus_nodes WHERE parent_id IS NULL ORDER BY order_index ASC";
        $categories = $this->db->query($sql)->fetchAll();

        // Calculate Stats
        $stats = [
            'total' => count($categories),
            'premium' => count(array_filter($categories, fn($c) => $c['is_premium'] == 1)),
            'total_questions' => array_sum(array_column($categories, 'question_count'))
        ];

        return $this->view('admin/quiz/categories/index', [
            'categories' => $categories,
            'stats' => $stats
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
        $stmt = $this->db->getPdo()->query("SELECT MAX(order_index) FROM syllabus_nodes WHERE parent_id IS NULL");
        $maxOrder = $stmt->fetchColumn();

        $data = [
            'parent_id' => null, // Root
            'title' => $title,
            'slug' => $slug,
            'type' => 'paper', // Assuming 'paper' is the root type
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
     * Delete Category
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
