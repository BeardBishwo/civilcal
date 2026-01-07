<?php

namespace App\Helpers;

use App\Core\Database;

class BlogUrlHelper {
    
    /**
     * Generate simple blog URL: domain/{slug}
     */
    public static function generateUrl($question) {
        return app_base_url($question['slug']);
    }
    
    /**
     * Generate slug from question text
     */
    public static function generateSlug($questionText, $uniqueCode) {
        // Clean text
        $slug = strtolower($questionText);
        
        // Remove special characters
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Replace spaces with hyphens
        $slug = preg_replace('/\s+/', '-', $slug);
        
        // Limit length
        $slug = substr($slug, 0, 60);
        
        // Remove trailing hyphens
        $slug = trim($slug, '-');
        
        // Add unique code suffix
        $slug .= '-' . strtolower($uniqueCode);
        
        return $slug;
    }
    
    /**
     * Ensure slug is unique
     */
    public static function ensureUniqueSlug($slug, $questionId = null) {
        $db = Database::getInstance();
        $original = $slug;
        $counter = 1;
        
        while (true) {
            $query = "SELECT id FROM quiz_questions WHERE slug = :slug";
            $params = ['slug' => $slug];
            
            if ($questionId) {
                $query .= " AND id != :id";
                $params['id'] = $questionId;
            }
            
            $exists = $db->query($query, $params)->fetch();
            
            if (!$exists) {
                return $slug;
            }
            
            $slug = $original . '-' . $counter;
            $counter++;
        }
    }
}
