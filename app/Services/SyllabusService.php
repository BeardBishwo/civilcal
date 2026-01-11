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

        if ($this->db->insert('syllabus_nodes', $data)) {
            return $this->db->lastInsertId();
        }
        return false;
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
    public function searchNodes($query, $level = null, $activeOnly = false)
    {
        // Search with strict hierarchy check (Active Only mode)
        $sql = "SELECT DISTINCT t1.* FROM syllabus_nodes t1";
        
        if ($activeOnly) {
            // Join up to 4 levels of ancestors to ensure full chain is active
            $sql .= "
                LEFT JOIN syllabus_nodes t2 ON t1.parent_id = t2.id
                LEFT JOIN syllabus_nodes t3 ON t2.parent_id = t3.id
                LEFT JOIN syllabus_nodes t4 ON t3.parent_id = t4.id
                LEFT JOIN syllabus_nodes t5 ON t4.parent_id = t5.id
            ";
        }

        $sql .= " WHERE t1.title LIKE :query";
        $params = ['query' => "%$query%"];

        if ($level) {
            $sql .= " AND t1.level = :level";
            $params['level'] = $level;
        }

        if ($activeOnly) {
             // Ensure all existing ancestors are active
             // valid = (no parent OR parent is active) AND (no grandparent OR grandparent is active) ...
             $sql .= " 
                AND t1.is_active = 1
                AND (t2.is_active = 1 OR t2.id IS NULL)
                AND (t3.is_active = 1 OR t3.id IS NULL)
                AND (t4.is_active = 1 OR t4.id IS NULL)
                AND (t5.is_active = 1 OR t5.id IS NULL)
             ";
        }

        $sql .= " ORDER BY t1.`order` ASC LIMIT 50";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update Question Counts for the entire tree
     * (Performance optimization: Stores count in node table)
     */
    public function recalculateQuestionCounts()
    {
        // 1. Reset all counts
        $this->db->getPdo()->exec("UPDATE syllabus_nodes SET question_count = 0");

        // 2. Count direct questions (via question_stream_map + direct quiz_questions linkage if any)
        // Linking to `question_stream_map` is the primary way now.
        // Also checks `quiz_questions` if foreign key exists there (legacy support if needed, but we focus on stream map).

        $sql = "
            UPDATE syllabus_nodes sn
            SET question_count = (
                SELECT COUNT(DISTINCT qsm.question_id)
                FROM question_stream_map qsm
                WHERE qsm.syllabus_node_id = sn.id
            )
        ";
        $this->db->getPdo()->exec($sql);

        // 3. Recursive Rollup (Children -> Parents)
        // We do this by levels (Unit -> Section -> Part -> Paper) ideally,
        // or just iterate leaves up. A robust way is multiple passes or known depth.
        // Assuming max depth 4 (Paper->Part->Section->Unit).

        // Pass 1: Roll up Units to Sections
        $this->rollupCounts('unit', 'section');
        // Pass 2: Roll up Sections to Parts
        $this->rollupCounts('section', 'part');
        // Pass 3: Roll up Parts to Papers
        $this->rollupCounts('part', 'paper');
    }

    private function rollupCounts($childType, $parentType)
    {
        $sql = "
            UPDATE syllabus_nodes parent
            SET question_count = question_count + (
                SELECT COALESCE(SUM(child.question_count), 0)
                FROM syllabus_nodes child
                WHERE child.parent_id = parent.id
                AND child.type = :childType
            )
            WHERE parent.type = :parentType
        ";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(['childType' => $childType, 'parentType' => $parentType]);
    }

    /**
     * Resolve full filter context from a leaf node (e.g. Unit/Topic)
     * Traces ancestry to find IDs for Course, Level, and Category.
     */
    public function resolveFilterContext($nodeId)
    {
        $context = [
            'course_id' => null,
            'edu_level_id' => null,
            'category_id' => null,
            'sub_category_id' => $nodeId
        ];

        $currId = $nodeId;
        $maxSafeDepth = 10;
        $depthCount = 0;

        while ($currId && $depthCount < $maxSafeDepth) {
            $node = $this->db->findOne('syllabus_nodes', ['id' => $currId]);
            if (!$node) break;

            if ($node['type'] === 'course') {
                $context['course_id'] = $currId;
            } elseif ($node['type'] === 'education_level') {
                $context['edu_level_id'] = $currId;
            } elseif ($node['type'] === 'category' || $node['type'] === 'section') {
                // In this system, Section behaves like Main Category in structural views
                if (!$context['category_id']) $context['category_id'] = $currId;
            }

            $currId = $node['parent_id'];
            $depthCount++;
        }

        return $context;
    }

    /**
     * Delete node with selective preservation of descendants
     * 
     * @param int $nodeId The ID of the node being deleted
     * @param array $deleteTypes Array of types to delete (e.g. ['education_level', 'category']). 
     *                           Types NOT in this list will be preserved (unlinked).
     */
    public function deleteWithPreservation($nodeId, $deleteTypes = [])
    {
        // 1. Fetch Direct Children
        $sql = "SELECT id, type FROM syllabus_nodes WHERE parent_id = :id";
        $params = ['id' => $nodeId];
        $children = $this->db->query($sql, $params)->fetchAll();

        foreach ($children as $child) {
            // Check if this child type should be deleted
            if (in_array($child['type'], $deleteTypes)) {
                // If yes, recurse down (this child is also doomed, but apply same logic to its children)
                $this->deleteWithPreservation($child['id'], $deleteTypes);
            } else {
                // If no, preserve this child (unlink from parent)
                $this->db->update('syllabus_nodes', ['parent_id' => null], "id = :id", ['id' => $child['id']]);
            }
        }

        // 2. Finally, delete the node itself
        // Note: Children that were selected for deletion will have been recursively processed and deleted by now.
        // Children preserved were unlinked.
        // So this delete is safe.
        return $this->db->delete('syllabus_nodes', "id = :id", ['id' => $nodeId]);
    }

    /**
     * Get counts of all descendant types for a node
     */
    public function getChildTypeCounts($nodeId)
    {
        // Recursive query to get all descendants and their types
        $sql = "
            WITH RECURSIVE node_tree AS (
                SELECT id, parent_id, type
                FROM syllabus_nodes
                WHERE parent_id = :node_id
                
                UNION ALL
                
                SELECT n.id, n.parent_id, n.type
                FROM syllabus_nodes n
                INNER JOIN node_tree nt ON n.parent_id = nt.id
            )
            SELECT type, COUNT(*) as count FROM node_tree GROUP BY type
        ";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(['node_id' => $nodeId]);
        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR); // Returns ['type' => count, ...]
    }

    /**
     * Helper: Slugify text
     */
    public function slugify($text)
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
