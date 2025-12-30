<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Media
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = [], $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $whereSql = "WHERE 1=1";

        if (!empty($filters['type'])) {
            $whereSql .= " AND file_type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $whereSql .= " AND (filename LIKE ? OR original_filename LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (isset($filters['folder'])) {
            $whereSql .= " AND folder = ?";
            $params[] = $filters['folder'];
        }

        // Count total
        $countSql = "SELECT COUNT(*) as total FROM media $whereSql";
        $stmt = $this->db->getPdo()->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];

        // Get results
        $sql = "SELECT * FROM media $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $media = $stmt->fetchAll();

        return [
            'data' => $media,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ];
    }

    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM media WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO media ($columns) VALUES ($placeholders)";
        $stmt = $this->db->getPdo()->prepare($sql);

        if ($stmt->execute(array_values($data))) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    public function update($id, $data)
    {
        $setParts = [];
        $values = [];

        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE media SET $setClause WHERE id = ?";
        $stmt = $this->db->getPdo()->prepare($sql);

        return $stmt->execute($values);
    }

    public function delete($id)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM media WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteMultiple($ids)
    {
        if (empty($ids)) return true;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM media WHERE id IN ($placeholders)";
        $stmt = $this->db->getPdo()->prepare($sql);
        return $stmt->execute($ids);
    }

    public function getStats()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                COUNT(*) as total_files,
                SUM(file_size) as total_size,
                COUNT(DISTINCT file_type) as types_count
            FROM media
        ");
        return $stmt->fetch();
    }

    /**
     * Check usage of media items in pages, menus, and settings.
     * Returns an array of media IDs that are currently in use.
     */
    public function getUsageInfo($mediaItems)
    {
        if (empty($mediaItems)) return [];

        $pdo = $this->db->getPdo();
        $usage = [];

        foreach ($mediaItems as $item) {
            $filename = $item['filename'];
            $filePath = $item['file_path']; // e.g., media/images/x.png or themes/default/...
            $id = $item['id'];
            $isUsed = false;
            $usedIn = [];

            // Search terms: specific enough to match path, broad enough for filenames
            $searchTermFile = "%" . $filename . "%";
            $searchTermPath = "%" . $filePath . "%";

            // 1. Check in Pages (content or meta)
            $stmt = $pdo->prepare("SELECT id, title FROM pages WHERE content LIKE ? OR content LIKE ? OR meta_description LIKE ? OR meta_description LIKE ?");
            $stmt->execute([$searchTermFile, $searchTermPath, $searchTermFile, $searchTermPath]);
            $pages = $stmt->fetchAll();
            if (!empty($pages)) {
                $isUsed = true;
                foreach ($pages as $p) $usedIn[] = ['type' => 'Page', 'name' => $p['title'], 'id' => $p['id']];
            }

            // 2. Check in Menus (items JSON)
            $stmt = $pdo->prepare("SELECT id, name FROM menus WHERE items LIKE ? OR items LIKE ?");
            $stmt->execute([$searchTermFile, $searchTermPath]);
            $menus = $stmt->fetchAll();
            if (!empty($menus)) {
                $isUsed = true;
                foreach ($menus as $m) $usedIn[] = ['type' => 'Menu', 'name' => $m['name'], 'id' => $m['id']];
            }

            // 3. Check in Settings
            $stmt = $pdo->prepare("SELECT setting_key FROM settings WHERE setting_value LIKE ? OR setting_value LIKE ?");
            $stmt->execute([$searchTermFile, $searchTermPath]);
            $settings = $stmt->fetchAll();
            if (!empty($settings)) {
                $isUsed = true;
                foreach ($settings as $s) $usedIn[] = ['type' => 'Setting', 'name' => $s['setting_key']];
            }

            $usage[$id] = [
                'is_used' => $isUsed,
                'details' => $usedIn
            ];
        }

        return $usage;
    }

    /**
     * Find files in the storage folders that are NOT in the database.
     */
    public function findUntrackedFiles()
    {
        $untracked = [];

        // 1. Standard Managed paths (relative to public/storage/)
        $storageRoot = __DIR__ . '/../../public/storage/media/';
        $types = ['images', 'documents', 'other'];
        foreach ($types as $type) {
            $dir = $storageRoot . $type . '/';
            $this->scanFolder($dir, 'media/' . $type . '/', $untracked, $type);
        }

        // 2. Extra Theme paths (relative to public/storage/ or just relative to root)
        // We'll treat themes/ as relative to root for URL generation later
        $themeBase = __DIR__ . '/../../themes/default/assets/';
        $this->scanFolder($themeBase . 'resources/', 'themes/default/assets/resources/', $untracked, 'documents');
        $this->scanFolder($themeBase . 'images/', 'themes/default/assets/images/', $untracked, 'images');

        return $untracked;
    }

    private function scanFolder($dir, $relativePrefix, &$untracked, $type)
    {
        if (!is_dir($dir)) return;
        
        // Ensure directory path ends with separator
        if (substr($dir, -1) !== DIRECTORY_SEPARATOR) {
            $dir .= DIRECTORY_SEPARATOR;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $fullPath = $dir . $file;
            
            // If it's a directory, recurse into it
            if (is_dir($fullPath)) {
                $this->scanFolder(
                    $fullPath, 
                    $relativePrefix . $file . '/', 
                    $untracked, 
                    $type
                );
                continue;
            }
            
            // It's a file - check if tracked
            $relativePath = $relativePrefix . $file;
            
            $stmt = $this->db->getPdo()->prepare("SELECT id FROM media WHERE file_path = ?");
            $stmt->execute([$relativePath]);
            
            if (!$stmt->fetch()) {
                $untracked[] = [
                    'filename' => $file,
                    'type' => $type,
                    'full_path' => $fullPath,
                    'relative_path' => $relativePath
                ];
            }
        }
    }

}
