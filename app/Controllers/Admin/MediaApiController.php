<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\FileService;
use App\Models\Media;

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

        // Process items for display (Secure URLs)
        foreach ($items as &$item) {
            $item['url'] = '/storage/uploads/admin/media/' . $item['filename'];
            $item['thumb'] = $item['url'];
            $item['is_image'] = strpos($item['file_type'] ?? $item['type'] ?? '', 'image') === 0;
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
        // Hardened upload handler for the modal
        if (!current_user()) {
            http_response_code(401);
            exit;
        }

        if (empty($_FILES['file'])) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
            return;
        }

        // Use FileService for "Paranoid-Grade" upload
        $upload = FileService::uploadAdminFile($_FILES['file'], 'media');

        if ($upload['success']) {
            $filename = $upload['filename'];
            $db = Database::getInstance();

            // Insert into media table using standard columns
            $db->prepare("INSERT INTO media (original_filename, filename, file_path, file_type, file_size, mime_type, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())")
                ->execute([
                    $_FILES['file']['name'],
                    $filename,
                    $filename,
                    strpos($_FILES['file']['type'], 'image') === 0 ? 'images' : 'documents',
                    $_FILES['file']['size'],
                    $_FILES['file']['type']
                ]);

            $id = $db->lastInsertId();

            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'url' => '/storage/uploads/admin/media/' . $filename,
                    'filename' => $filename
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => $upload['error']]);
        }
    }
}
