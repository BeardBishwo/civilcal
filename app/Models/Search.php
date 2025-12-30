<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Search
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Perform a fulltext search
     */
    public function search($query, $limit = 20)
    {
        // Add wildcards for partial matches if needed, but BOOLEAN MODE handles keywords well
        // For partial matches often '*' is appended to words
        $searchQuery = $query;
        if (strlen($query) > 3) {
            $searchQuery .= '*';
        }

        $sql = "SELECT id, title, type, url, 
                       MATCH(title, content) AGAINST (:query IN BOOLEAN MODE) as score 
                FROM search_index 
                WHERE MATCH(title, content) AGAINST (:query IN BOOLEAN MODE)
                ORDER BY score DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':query', $searchQuery);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update or Create index entry for an entity
     */
    public function updateIndex($type, $entityId, $title, $content, $url)
    {
        // Check if exists
        $stmt = $this->db->prepare("SELECT id FROM search_index WHERE type = :type AND entity_id = :entity_id");
        $stmt->execute(['type' => $type, 'entity_id' => $entityId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $sql = "UPDATE search_index SET title = :title, content = :content, url = :url, updated_at = NOW() WHERE id = :id";
            $params = [
                'title' => $title,
                'content' => strip_tags($content), // Strip tags for cleaner indexing
                'url' => $url,
                'id' => $existing['id']
            ];
        } else {
            $sql = "INSERT INTO search_index (type, entity_id, title, content, url) VALUES (:type, :entity_id, :title, :content, :url)";
            $params = [
                'type' => $type,
                'entity_id' => $entityId,
                'title' => $title,
                'content' => strip_tags($content),
                'url' => $url
            ];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete index entry
     */
    public function deleteIndex($type, $entityId)
    {
        $stmt = $this->db->prepare("DELETE FROM search_index WHERE type = :type AND entity_id = :entity_id");
        return $stmt->execute(['type' => $type, 'entity_id' => $entityId]);
    }
    
    /**
     * Rebuild index (Truncate and start over - intended for CLI/Admin use)
     */
    public function clearIndex()
    {
        return $this->db->getPdo()->exec("TRUNCATE TABLE search_index");
    }
}
