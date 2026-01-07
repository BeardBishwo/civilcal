<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\SyllabusService;

class SubCategoryController extends Controller
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
     * Display Sub-Categories with Parent Info
     */
    public function index()
    {
        $parentId = $_GET['parent_id'] ?? null;

        // 1. Fetch Sub-Categories (Nodes with a Parent)
        $sql = "
            SELECT child.*, 
                   parent.title as parent_title, parent.is_active as parent_active,
                   level.is_active as grandparent_active,
                   course.is_active as greatgrandparent_active
            FROM syllabus_nodes child
            LEFT JOIN syllabus_nodes parent ON child.parent_id = parent.id
            LEFT JOIN syllabus_nodes level ON parent.parent_id = level.id
            LEFT JOIN syllabus_nodes course ON level.parent_id = course.id
            WHERE child.parent_id IS NOT NULL
        ";
        
        $params = [];
        
        if ($parentId) {
            $sql .= " AND child.parent_id = :pid";
            $params['pid'] = $parentId;
        }

        $sql .= " ORDER BY (child.is_active = 1 AND IFNULL(parent.is_active, 1) = 1 AND IFNULL(level.is_active, 1) = 1 AND IFNULL(course.is_active, 1) = 1) DESC, parent.title ASC, child.order_index ASC, child.parent_id ASC";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $subCategories = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 2. Fetch Main Categories (For the Dropdown Filter & Modal)
        $parents = $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'category' ORDER BY title ASC")->fetchAll();

        return $this->view('admin/quiz/subcategories/index', [
            'subCategories' => $subCategories,
            'parents' => $parents,
            'selectedParent' => $parentId
        ]);
    }

    /**
     * Store New Sub-Category
     */
    public function store()
    {
        $title = $_POST['title'] ?? '';
        $parentId = $_POST['parent_id'] ?? $_POST['category_id'] ?? null;
        $isPremium = isset($_POST['is_premium']) ? 1 : 0;
        $unlockPrice = $_POST['unlock_price'] ?? 0;
        $image = $_POST['image'] ?? null;

        if (empty($title) || empty($parentId)) {
            echo json_encode(['status' => 'error', 'message' => 'Title and Parent are required']);
            return;
        }

        $rawSlug = $_POST['slug'] ?? $title;
        $slug = $this->syllabusService->slugify($rawSlug);
        
        // Get Max Order for this parent
        $stmt = $this->db->getPdo()->prepare("SELECT MAX(order_index) FROM syllabus_nodes WHERE parent_id = ?");
        $stmt->execute([$parentId]);
        $maxOrder = $stmt->fetchColumn();

        $data = [
            'parent_id' => $parentId,
            'title' => $title,
            'slug' => $slug,
            'type' => 'part', // Or 'section', user code used 'section', I'll use 'part' as generic child or flexible. 
                              // Wait, user code said "$data['type'] = 'section'".
                              // My migration 033 didn't change types. 
                              // I'll stick to 'section' as it seems safer for 2nd level.
                              // Actually, if Main is Paper, Child is Part.
                              // Let's assume Main=Paper, Child=Part for now. Logic is flexible.
                              // I'll set it to 'part' or 'section' depending on parent type?
                              // Simplest: 'part' if parent is 'paper'.
                              // I'll query parent type.
            'is_premium' => $isPremium,
            'unlock_price' => $unlockPrice,
            'image_path' => $image,
            'order_index' => $maxOrder + 1,
            'is_active' => 1
        ];

        // Determine type based on parent
        $parent = $this->db->findOne('syllabus_nodes', ['id' => $parentId]);
        if ($parent) {
            if ($parent['type'] == 'paper') $data['type'] = 'part';
            elseif ($parent['type'] == 'part') $data['type'] = 'section';
            elseif ($parent['type'] == 'section') $data['type'] = 'unit';
            else $data['type'] = 'unit';
        }

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
     * AJAX: Toggle Premium
     */
    public function togglePremium()
    {
        $id = $_POST['id'] ?? null;
        $val = $_POST['val'] ?? 0;

        if ($id) {
            $this->db->update('syllabus_nodes', ['is_premium' => $val], "id = :id", ['id' => $id]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
    
    /**
     * AJAX: Reorder (within parent scope mainly, but simple global ID list works)
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
            // Check collision within same parent
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
