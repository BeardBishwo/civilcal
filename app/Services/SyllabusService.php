<?php

namespace App\Services;

use App\Core\Database;
use Exception;

/**
 * Syllabus Service
 * 
 * Handles recursive syllabus tree operations:
 * - CRUD operations on syllabus nodes
 * - Tree traversal and hierarchy management
 * - Node reordering
 */
class SyllabusService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get complete syllabus tree for a specific level
     */
    public function getTree($level = null, $activeOnly = true)
    {
        $conditions = [];
        $params = [];

        if ($level) {
            $conditions[] = "level = :level";
            $params['level'] = $level;
        }

        if ($activeOnly) {
            $conditions[] = "is_active = 1";
        }

        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $sql = "
            SELECT * FROM syllabus_nodes 
            $where
            ORDER BY `order` ASC
        ";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $nodes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->buildTree($nodes);
    }

    /**
     * Build hierarchical tree from flat array
     */
    private function buildTree($nodes, $parentId = null)
    {
        $branch = [];

        foreach ($nodes as $node) {
            if ($node['parent_id'] == $parentId) {
                $children = $this->buildTree($nodes, $node['id']);
                if ($children) {
                    $node['children'] = $children;
                }
                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * Get single node with its ancestors (breadcrumb)
     */
    public function getNodeWithAncestors($nodeId)
    {
        $node = $this->db->findOne('syllabus_nodes', ['id' => $nodeId]);
        if (!$node) {
            throw new Exception("Node not found");
        }

        $ancestors = [];
        $currentId = $node['parent_id'];

        while ($currentId) {
            $parent = $this->db->findOne('syllabus_nodes', ['id' => $currentId]);
            if ($parent) {
                array_unshift($ancestors, $parent);
                $currentId = $parent['parent_id'];
            } else {
                break;
            }
        }

        return [
            'node' => $node,
            'ancestors' => $ancestors
        ];
    }

    /**
     * Get all children of a node (recursive)
     */
    public function getAllChildren($nodeId)
    {
        $sql = "
            WITH RECURSIVE node_tree AS (
                SELECT id, parent_id, title, type
                FROM syllabus_nodes
                WHERE id = :node_id
                
                UNION ALL
                
                SELECT n.id, n.parent_id, n.title, n.type
                FROM syllabus_nodes n
                INNER JOIN node_tree nt ON n.parent_id = nt.id
            )
            SELECT * FROM node_tree WHERE id != :node_id
        ";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(['node_id' => $nodeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Create new syllabus node
     */
    public function createNode($data)
    {
        // Validate parent exists if provided
        if (!empty($data['parent_id'])) {
            $parent = $this->db->findOne('syllabus_nodes', ['id' => $data['parent_id']]);
            if (!$parent) {
                throw new Exception("Parent node not found");
            }
        }

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->slugify($data['title']);
        }

        // Get max order for this parent
        if (!isset($data['order'])) {
            $parentId = $data['parent_id'] ?? null;
            $sql = "SELECT MAX(`order`) FROM syllabus_nodes WHERE " . 
                   ($parentId ? "parent_id = :parent_id" : "parent_id IS NULL");
            $stmt = $this->db->getPdo()->prepare($sql);
            if ($parentId) {
                $stmt->execute(['parent_id' => $parentId]);
            } else {
                $stmt->execute();
            }
            $maxOrder = $stmt->fetchColumn() ?: 0;
            $data['order'] = $maxOrder + 1;
        }

        return $this->db->insert('syllabus_nodes', $data);
    }

    /**
     * Update syllabus node
     */
    public function updateNode($nodeId, $data)
    {
        // Prevent circular references
        if (!empty($data['parent_id'])) {
            $children = $this->getAllChildren($nodeId);
            $childIds = array_column($children, 'id');
            
            if (in_array($data['parent_id'], $childIds)) {
                throw new Exception("Cannot set parent to a child node (circular reference)");
            }
        }

        return $this->db->update('syllabus_nodes', $data, "id = :id", ['id' => $nodeId]);
    }

    /**
     * Delete node and all its children
     */
    public function deleteNode($nodeId)
    {
        // CASCADE will handle children automatically
        return $this->db->delete('syllabus_nodes', "id = :id", ['id' => $nodeId]);
    }

    /**
     * Reorder nodes within same parent
     */
    public function reorderNodes($nodeIds)
    {
        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            foreach ($nodeIds as $order => $nodeId) {
                $stmt = $pdo->prepare("UPDATE syllabus_nodes SET `order` = :order WHERE id = :id");
                $stmt->execute(['order' => $order + 1, 'id' => $nodeId]);
            }
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Get nodes by type (paper, part, section, unit)
     */
    public function getNodesByType($type, $level = null)
    {
        $conditions = ['type' => $type];
        if ($level) {
            $conditions['level'] = $level;
        }

        return $this->db->find('syllabus_nodes', $conditions, '`order` ASC');
    }

    /**
     * Search nodes by title
     */
    public function searchNodes($query, $level = null)
    {
        $sql = "SELECT * FROM syllabus_nodes WHERE title LIKE :query";
        $params = ['query' => "%$query%"];

        if ($level) {
            $sql .= " AND level = :level";
            $params['level'] = $level;
        }

        $sql .= " ORDER BY `order` ASC LIMIT 50";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Helper: Slugify text
     */
    private function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }
}
