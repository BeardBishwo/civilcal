<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;

class PositionLevelController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->db = Database::getInstance();
    }

    /**
     * Display Position Levels
     */
    public function index()
    {
        $sql = "SELECT * FROM position_levels ORDER BY order_index ASC, level_number ASC";
        $levels = $this->db->query($sql)->fetchAll();

        $stats = [
            'total' => count($levels),
            'active' => count(array_filter($levels, fn($l) => $l['is_active'] == 1)),
        ];

        return $this->view('admin/quiz/position_levels/index', [
            'levels' => $levels,
            'stats' => $stats
        ]);
    }

    /**
     * Store New Position Level
     */
    public function store()
    {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $levelNumber = $_POST['level_number'] ?? 0;
        $color = $_POST['color'] ?? '#667eea';
        $icon = $_POST['icon'] ?? 'fa-user';

        if (empty($title)) {
            echo json_encode(['status' => 'error', 'message' => 'Title is required']);
            return;
        }

        // Generate slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        // Get max order
        $stmt = $this->db->getPdo()->prepare("SELECT MAX(order_index) FROM position_levels");
        $stmt->execute();
        $maxOrder = $stmt->fetchColumn();

        $data = [
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'level_number' => $levelNumber,
            'color' => $color,
            'icon' => $icon,
            'order_index' => $maxOrder + 1,
            'is_active' => 1
        ];

        $result = $this->db->insert('position_levels', $data);

        if ($result) {
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
            $this->db->update('position_levels', ['is_active' => $val], "id = :id", ['id' => $id]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    /**
     * Reorder
     */
    public function reorder()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $order = $data['order'] ?? ($_POST['order'] ?? []);

        foreach ($order as $index => $id) {
            $this->db->update('position_levels', ['order_index' => $index + 1], "id = :id", ['id' => $id]);
        }
        echo json_encode(['status' => 'success']);
    }

    /**
     * Delete
     */
    public function delete($id)
    {
        // Check if level is used in questions
        $stmt = $this->db->getPdo()->prepare("SELECT COUNT(*) FROM question_position_levels WHERE position_level_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo json_encode(['status' => 'error', 'message' => "Cannot delete. This level is used in {$count} question(s)."]);
            return;
        }

        if ($this->db->delete('position_levels', "id = :id", ['id' => $id])) {
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

        $errors = [];
        foreach ($ids as $id) {
            // Check usage
            $stmt = $this->db->getPdo()->prepare("SELECT COUNT(*) FROM question_position_levels WHERE position_level_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $stmt2 = $this->db->getPdo()->prepare("SELECT title FROM position_levels WHERE id = ?");
                $stmt2->execute([$id]);
                $title = $stmt2->fetchColumn();
                $errors[] = "{$title} (used in {$count} questions)";
            } else {
                $this->db->delete('position_levels', "id = :id", ['id' => $id]);
            }
        }

        if (!empty($errors)) {
            echo json_encode(['status' => 'warning', 'message' => 'Some items could not be deleted: ' . implode(', ', $errors)]);
        } else {
            echo json_encode(['status' => 'success']);
        }
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
            $this->duplicateSingleLevel($id);
        }

        echo json_encode(['status' => 'success']);
    }

    private function duplicateSingleLevel($id)
    {
        $original = $this->db->findOne('position_levels', ['id' => $id]);
        if (!$original) return;

        // Determine new name
        $baseTitle = $original['title'];
        $baseTitle = preg_replace('/\s*\(V\d+\)$/', '', $baseTitle);
        
        $newTitle = $baseTitle . ' (V1)';
        $counter = 1;

        while (true) {
            $check = $this->db->findOne('position_levels', [
                'title' => $newTitle,
                'is_active' => 0
            ]);
            if (!$check) break;
            $counter++;
            $newTitle = $baseTitle . ' (V' . $counter . ')';
        }

        // Insert new record
        $data = $original;
        unset($data['id']);
        unset($data['created_at']);
        unset($data['updated_at']);
        $data['title'] = $newTitle;
        $data['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $newTitle)));
        $data['is_active'] = 0;
        $data['order_index'] = $original['order_index'] + 1;

        $this->db->insert('position_levels', $data);
    }
}
