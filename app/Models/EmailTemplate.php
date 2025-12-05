<?php

namespace App\Models;

use App\Core\Database;
use Exception;

class EmailTemplate {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find an email template by ID
     */
    public function find($id) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Find an email template by name
     */
    public function findByName($name) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM email_templates WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    /**
     * Create a new email template
     */
    public function create($data) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO email_templates (
                name,
                subject,
                content,
                category,
                created_by,
                description,
                is_active,
                variables,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $result = $stmt->execute([
            $data['name'],
            $data['subject'],
            $data['content'],
            $data['category'] ?? 'general',
            $data['created_by'],
            $data['description'] ?? null,
            $data['is_active'] ?? 1,
            !empty($data['variables']) ? json_encode($data['variables']) : null
        ]);

        if ($result) {
            return $this->db->getPdo()->lastInsertId();
        }

        return false;
    }

    /**
     * Update an existing email template
     */
    public function update($id, $data) {
        $updateFields = [];
        $values = [];

        // Prepare fields to update
        if (isset($data['name'])) {
            $updateFields[] = 'name = ?';
            $values[] = $data['name'];
        }
        if (isset($data['subject'])) {
            $updateFields[] = 'subject = ?';
            $values[] = $data['subject'];
        }
        if (isset($data['content'])) {
            $updateFields[] = 'content = ?';
            $values[] = $data['content'];
        }
        if (isset($data['category'])) {
            $updateFields[] = 'category = ?';
            $values[] = $data['category'];
        }
        if (isset($data['description'])) {
            $updateFields[] = 'description = ?';
            $values[] = $data['description'];
        }
        if (isset($data['is_active'])) {
            $updateFields[] = 'is_active = ?';
            $values[] = $data['is_active'] ? 1 : 0;
        }
        if (isset($data['variables'])) {
            $updateFields[] = 'variables = ?';
            $values[] = json_encode($data['variables']);
        }

        $updateFields[] = 'updated_at = NOW()';

        if (empty($updateFields)) {
            return false;
        }

        $values[] = $id;

        $sql = "UPDATE email_templates SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $this->db->getPdo()->prepare($sql);

        return $stmt->execute($values);
    }

    /**
     * Delete an email template by ID
     */
    public function delete($id) {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM email_templates WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get all email templates
     */
    public function getAll($filters = [], $page = 1, $perPage = 20) {
        $whereClause = "WHERE 1=1";
        $params = [];

        if (!empty($filters['category'])) {
            $whereClause .= " AND category = ?";
            $params[] = $filters['category'];
        }

        if (isset($filters['is_active'])) {
            $whereClause .= " AND is_active = ?";
            $params[] = $filters['is_active'];
        }

        if (!empty($filters['search'])) {
            $whereClause .= " AND (name LIKE ? OR subject LIKE ? OR content LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }

        // Count total
        $countStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as total FROM email_templates $whereClause");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];

        // Get templates with pagination
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->getPdo()->prepare("
            SELECT et.*, u.first_name, u.last_name
            FROM email_templates et
            LEFT JOIN users u ON et.created_by = u.id
            $whereClause
            ORDER BY et.category ASC, et.name ASC
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        $templates = $stmt->fetchAll();

        return [
            'templates' => $templates,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    public function getByCategory($category) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM email_templates
            WHERE category = ? AND is_active = 1
            ORDER BY name ASC
        ");
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }

    public function getActiveTemplates() {
        $stmt = $this->db->getPdo()->prepare("
            SELECT et.*, u.first_name, u.last_name
            FROM email_templates et
            LEFT JOIN users u ON et.created_by = u.id
            WHERE et.is_active = 1
            ORDER BY et.category ASC, et.name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Process an email template with variables
     */
    public function processTemplate($id, $variables = []) {
        $template = $this->find($id);

        if (!$template) {
            return false;
        }

        $content = $template['content'];
        $subject = $template['subject'];

        foreach ($variables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
            $subject = str_replace("{{$key}}", $value, $subject);
        }

        return [
            'subject' => $subject,
            'content' => $content
        ];
    }

    /**
     * Toggle template active status
     */
    public function toggleActive($id) {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE email_templates
            SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    /**
     * Activate an email template
     */
    public function activate($id) {
        $stmt = $this->db->getPdo()->prepare("UPDATE email_templates SET is_active = 1, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Deactivate an email template
     */
    public function deactivate($id) {
        $stmt = $this->db->getPdo()->prepare("UPDATE email_templates SET is_active = 0, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get available variables for a template
     */
    public function getVariables($templateId) {
        $template = $this->find($templateId);
        if ($template && !empty($template['variables'])) {
            return json_decode($template['variables'], true);
        }
        return [];
    }

    /**
     * Validate email template data before saving
     */
    public function validate($data) {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Template name is required';
        }

        if (empty($data['subject'])) {
            $errors[] = 'Template subject is required';
        }

        if (empty($data['content'])) {
            $errors[] = 'Template content is required';
        }

        if (empty($data['created_by'])) {
            $errors[] = 'Created by user ID is required';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }

    /**
     * Get template statistics
     */
    public function getStats() {
        $stmt = $this->db->getPdo()->query("
            SELECT
                COUNT(*) as total_templates,
                SUM(is_active) as active_templates,
                COUNT(*) - SUM(is_active) as inactive_templates
            FROM email_templates
        ");
        return $stmt->fetch();
    }

    /**
     * Get common template types
     */
    public function getTemplateTypes() {
        $stmt = $this->db->getPdo()->query("
            SELECT DISTINCT category
            FROM email_templates
            WHERE category IS NOT NULL
            ORDER BY category ASC
        ");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    // Compatibility wrappers expected by controllers
    public function getAllTemplates() {
        return $this->getAll();
    }

    public function createTemplate($data) {
        $success = $this->create($data);
        if ($success) {
            $lastId = $this->db->getPdo()->lastInsertId();
            return $this->find($lastId);
        }
        return false;
    }

    public function updateTemplate($id, $data) {
        return $this->update($id, $data);
    }

    public function getTemplateById($id) {
        return $this->find($id);
    }

    public function deleteTemplate($id) {
        return $this->delete($id);
    }

    /**
     * Process template content string with variables
     */
    public function processTemplateContent($content, $variables = []) {
        foreach ($variables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }
        return $content;
    }

    // Make processTemplate flexible: accept id or content
    public function processTemplateFlexible($input, $variables = []) {
        if (is_numeric($input)) {
            $result = $this->processTemplate(intval($input), $variables);
            if ($result === false) return false;
            return $result['content'];
        }
        return $this->processTemplateContent($input, $variables);
    }
}
?>
