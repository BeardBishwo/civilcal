<?php
namespace App\Models;

use App\Core\Model;

/**
 * Export Template Model
 * Handles user export templates for calculations
 */
class ExportTemplate extends Model {
    protected $table = 'export_templates';

    /**
     * Get user's templates by type
     */
    public function getUserTemplates($userId, $type = null) {
        $conditions = ['user_id' => $userId];
        if ($type) {
            $conditions['template_type'] = $type;
        }
        $templates = $this->where($conditions);
        
        // Sort by template_name
        usort($templates, function($a, $b) {
            return strcmp($a['template_name'], $b['template_name']);
        });
        
        return $templates;
    }

    /**
     * Get default templates (system templates)
     */
    public function getDefaultTemplates($type = null) {
        $conditions = ['user_id' => 0, 'is_default' => 1];
        if ($type) {
            $conditions['template_type'] = $type;
        }
        return $this->where($conditions);
    }

    /**
     * Get templates accessible to user (user templates + public templates)
     */
    public function getAccessibleTemplates($userId, $type = null) {
        // This is simplified - in a real app you'd use more complex SQL
        $allTemplates = $this->findAll();
        $accessible = [];
        
        foreach ($allTemplates as $template) {
            if ($template['user_id'] == $userId || 
                ($template['is_public'] == 1 && $template['is_default'] == 1)) {
                if (!$type || $template['template_type'] == $type) {
                    $accessible[] = $template;
                }
            }
        }
        
        // Sort by user_id, then by template_name
        usort($accessible, function($a, $b) {
            if ($a['user_id'] != $b['user_id']) {
                return $a['user_id'] - $b['user_id'];
            }
            return strcmp($a['template_name'], $b['template_name']);
        });
        
        return $accessible;
    }

    /**
     * Create a new user template
     */
    public function createUserTemplate($userId, $data) {
        $templateData = [
            'user_id' => $userId,
            'template_name' => $data['template_name'],
            'template_type' => $data['template_type'],
            'template_config' => json_encode($data['template_config'] ?? []),
            'is_public' => $data['is_public'] ?? 0,
            'is_default' => 0
        ];
        
        return $this->create($templateData);
    }

    /**
     * Update template
     */
    public function updateTemplate($id, $data) {
        $template = $this->find($id);
        if (!$template) return false;
        
        $updateData = [];
        if (isset($data['template_name'])) $updateData['template_name'] = $data['template_name'];
        if (isset($data['template_type'])) $updateData['template_type'] = $data['template_type'];
        if (isset($data['template_config'])) $updateData['template_config'] = json_encode($data['template_config']);
        if (isset($data['is_public'])) $updateData['is_public'] = $data['is_public'];
        
        return $this->update($id, $updateData);
    }

    /**
     * Check if template is deletable (not a default template)
     */
    public function isDeletable($id) {
        $template = $this->find($id);
        return $template && $template['is_default'] == 0;
    }

    /**
     * Get template configuration with defaults
     */
    public function getConfigWithDefaults($id) {
        $template = $this->find($id);
        if (!$template) return [];
        
        $config = json_decode($template['template_config'], true) ?: [];
        $defaults = $this->getDefaultConfig($template['template_type']);
        
        return array_merge($defaults, $config);
    }

    /**
     * Get default configuration for export type
     */
    private function getDefaultConfig($type) {
        switch ($type) {
            case 'pdf':
                return [
                    'include_logo' => true,
                    'include_header' => true,
                    'include_footer' => true,
                    'include_timestamp' => true,
                    'color_scheme' => 'professional',
                    'font_size' => 'medium',
                    'page_size' => 'a4',
                    'orientation' => 'portrait'
                ];
            
            case 'excel':
                return [
                    'include_formulas' => true,
                    'include_charts' => false,
                    'auto_format' => true,
                    'freeze_panes' => true
                ];
            
            case 'csv':
                return [
                    'delimiter' => ',',
                    'include_headers' => true,
                    'utf8_bom' => true
                ];
            
            case 'json':
                return [
                    'include_metadata' => true,
                    'pretty_print' => true,
                    'include_timestamp' => true
                ];
            
            default:
                return [];
        }
    }

    /**
     * Get template statistics
     */
    public function getTemplateStats($userId = null) {
        $allTemplates = $this->findAll();
        
        $stats = [
            'total' => count($allTemplates),
            'by_type' => [
                'pdf' => 0,
                'excel' => 0,
                'csv' => 0,
                'json' => 0
            ],
            'default' => 0,
            'public' => 0,
            'user_templates' => 0
        ];
        
        foreach ($allTemplates as $template) {
            $stats['by_type'][$template['template_type']]++;
            
            if ($template['is_default'] == 1) $stats['default']++;
            if ($template['is_public'] == 1) $stats['public']++;
            if ($template['user_id'] > 0) $stats['user_templates']++;
        }
        
        return $stats;
    }

    /**
     * Delete template with validation
     */
    public function deleteTemplate($id) {
        if (!$this->isDeletable($id)) {
            return false;
        }
        
        return $this->delete($id);
    }

    /**
     * Get recent templates for user
     */
    public function getRecentTemplates($userId, $limit = 10) {
        $allTemplates = $this->findAll();
        $userTemplates = [];
        
        foreach ($allTemplates as $template) {
            if ($template['user_id'] == $userId) {
                $userTemplates[] = $template;
            }
        }
        
        // Sort by updated_at descending
        usort($userTemplates, function($a, $b) {
            return strcmp($b['updated_at'], $a['updated_at']);
        });
        
        return array_slice($userTemplates, 0, $limit);
    }

    /**
     * Search templates by name
     */
    public function searchTemplates($userId, $searchTerm) {
        $allTemplates = $this->findAll();
        $matching = [];
        
        foreach ($allTemplates as $template) {
            if ($template['user_id'] == $userId && 
                stripos($template['template_name'], $searchTerm) !== false) {
                $matching[] = $template;
            }
        }
        
        // Sort by template_name
        usort($matching, function($a, $b) {
            return strcmp($a['template_name'], $b['template_name']);
        });
        
        return $matching;
    }

    /**
     * Duplicate a template for a user
     */
    public function duplicateForUser($userId, $templateId, $newName = null) {
        $original = $this->find($templateId);
        if (!$original) return false;
        
        $newName = $newName ?: ($original['template_name'] . ' (Copy)');
        
        $duplicateData = [
            'user_id' => $userId,
            'template_name' => $newName,
            'template_type' => $original['template_type'],
            'template_config' => $original['template_config'],
            'is_public' => 0,
            'is_default' => 0
        ];
        
        return $this->create($duplicateData);
    }

    /**
     * Get all template names for a user
     */
    public function getTemplateNames($userId) {
        $templates = $this->getUserTemplates($userId);
        $names = [];
        foreach ($templates as $template) {
            $names[] = $template['template_name'];
        }
        return $names;
    }

    /**
     * Check if template name exists for user
     */
    public function templateNameExists($userId, $name, $excludeId = null) {
        $templates = $this->getUserTemplates($userId);
        foreach ($templates as $template) {
            if ($template['template_name'] == $name && $template['id'] != $excludeId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Static method to get user templates
     */
    public static function getUserTemplatesStatic($userId, $type = null) {
        $instance = new self();
        return $instance->getUserTemplates($userId, $type);
    }

    /**
     * Static method to get default templates
     */
    public static function getDefaultTemplatesStatic($type = null) {
        $instance = new self();
        return $instance->getDefaultTemplates($type);
    }

    /**
     * Static method to get accessible templates
     */
    public static function getAccessibleTemplatesStatic($userId, $type = null) {
        $instance = new self();
        return $instance->getAccessibleTemplates($userId, $type);
    }

    /**
     * Static method to create user template
     */
    public static function createUserTemplateStatic($userId, $data) {
        $instance = new self();
        return $instance->createUserTemplate($userId, $data);
    }
}
