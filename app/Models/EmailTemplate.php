<?php

namespace App\Models;

use App\Core\Database;

class EmailTemplate {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO email_templates (name, subject, content, category, created_by) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['subject'],
            $data['content'],
            $data['category'] ?? 'general',
            $data['created_by']
        ]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE email_templates SET ";
        $params = [];
        $updates = [];
        
        if (isset($data['name'])) {
            $updates[] = "name = ?";
            $params[] = $data['name'];
        }
        
        if (isset($data['subject'])) {
            $updates[] = "subject = ?";
            $params[] = $data['subject'];
        }
        
        if (isset($data['content'])) {
            $updates[] = "content = ?";
            $params[] = $data['content'];
        }
        
        if (isset($data['category'])) {
            $updates[] = "category = ?";
            $params[] = $data['category'];
        }
        
        if (isset($data['is_active'])) {
            $updates[] = "is_active = ?";
            $params[] = $data['is_active'] ? 1 : 0;
        }
        
        if (empty($updates)) return false;
        
        $sql .= implode(', ', $updates) . ", updated_at = NOW() WHERE id = ?";
        $params[] = $id;
        
        $stmt = $this->db->getPdo()->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function delete($id) {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM email_templates WHERE id = ?");
        return $stmt->execute([$id]);
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
    
    public function getAll() {
        $stmt = $this->db->getPdo()->prepare("
            SELECT et.*, u.first_name, u.last_name
            FROM email_templates et
            LEFT JOIN users u ON et.created_by = u.id
            ORDER BY et.category ASC, et.name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
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
    
    public function toggleActive($id) {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE email_templates 
            SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }
}
?>
