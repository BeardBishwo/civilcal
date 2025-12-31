<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class MediaApiController extends Controller
{
    public function index()
    {
        // Check authentication
        if (!current_user()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $db = Database::getInstance();
        $page = $_GET['page'] ?? 1;
        $perPage = 40;
        $offset = ($page - 1) * $perPage;
        
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';

        $query = "SELECT * FROM media WHERE 1=1";
        $params = [];

        if ($search) {
            $query .= " AND filename LIKE ?";
            $params[] = "%$search%";
        }

        if ($type === 'images') {
            $query .= " AND type LIKE 'image/%'";
        }

        // Count total
        $countQuery = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
        $stmt = $db->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];

        // Fetch items
        $query .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        // Process items for display
        foreach ($items as &$item) {
            $item['url'] = app_base_url('uploads/' . $item['filename']);
            $item['thumb'] = $item['url']; // Uses full image as thumb for now
            $item['is_image'] = strpos($item['type'], 'image') === 0;
            $item['date'] = date('M j, Y', strtotime($item['created_at']));
        }

        header('Content-Type: application/json');
        echo json_encode([
            'data' => $items,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => (int)$page,
                'last_page' => ceil($total / $perPage)
            ]
        ]);
    }

    public function upload()
    {
        // Simple upload handler for the modal
        // Reuse logic from MediaController if possible, or implement simple version
        if (!current_user()) {
            http_response_code(401);
            exit;
        }

        if (empty($_FILES['file'])) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['file'];
        $uploadDir = BASE_PATH . '/uploads/';
        
        // Ensure directory exists
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $file['name']);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $db = Database::getInstance();
            $db->prepare("INSERT INTO media (filename, type, size, created_at) VALUES (?, ?, ?, NOW())")
               ->execute([$filename, $file['type'], $file['size']]);
            
            $id = $db->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'url' => app_base_url('uploads/' . $filename),
                    'filename' => $filename
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Upload failed']);
        }
    }
}
