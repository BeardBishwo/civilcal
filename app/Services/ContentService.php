<?php

namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * Content Management Service
 * Handles pages, menus, and dynamic content
 */
class ContentService
{
    private static $cache = [];
    
    /**
     * Get page by slug
     */
    public static function getPage($slug)
    {
        if (isset(self::$cache['page_' . $slug])) {
            return self::$cache['page_' . $slug];
        }
        
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, u.username as author_username 
            FROM pages p 
            LEFT JOIN users u ON p.author_id = u.id 
            WHERE p.slug = ? AND p.status = 'published'
        ");
        $stmt->execute([$slug]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($page) {
            self::$cache['page_' . $slug] = $page;
        }
        
        return $page;
    }
    
    /**
     * Get all pages
     */
    public static function getAllPages($status = null)
    {
        $db = Database::getInstance();
        
        if ($status) {
            $stmt = $db->prepare("
                SELECT p.*, u.username as author_username 
                FROM pages p 
                LEFT JOIN users u ON p.author_id = u.id 
                WHERE p.status = ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$status]);
        } else {
            $stmt = $db->prepare("
                SELECT p.*, u.username as author_username 
                FROM pages p 
                LEFT JOIN users u ON p.author_id = u.id 
                ORDER BY p.created_at DESC
            ");
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create new page
     */
    public static function createPage($data)
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            INSERT INTO pages 
            (slug, title, content, excerpt, meta_title, meta_description, meta_keywords, status, template, author_id, published_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $publishedAt = ($data['status'] ?? 'draft') === 'published' ? date('Y-m-d H:i:s') : null;
        
        return $stmt->execute([
            $data['slug'],
            $data['title'],
            $data['content'] ?? '',
            $data['excerpt'] ?? '',
            $data['meta_title'] ?? $data['title'],
            $data['meta_description'] ?? '',
            $data['meta_keywords'] ?? '',
            $data['status'] ?? 'draft',
            $data['template'] ?? 'default',
            $data['author_id'] ?? null,
            $publishedAt
        ]);
    }
    
    /**
     * Update page
     */
    public static function updatePage($id, $data)
    {
        $db = Database::getInstance();
        
        $fields = [];
        $values = [];
        
        $allowedFields = ['slug', 'title', 'content', 'excerpt', 'meta_title', 'meta_description', 'meta_keywords', 'status', 'template'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE pages SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Delete page
     */
    public static function deletePage($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM pages WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get menu by location
     */
    public static function getMenu($location)
    {
        if (isset(self::$cache['menu_' . $location])) {
            return self::$cache['menu_' . $location];
        }
        
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM menus 
            WHERE location = ? AND is_active = 1
            ORDER BY display_order ASC
            LIMIT 1
        ");
        $stmt->execute([$location]);
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($menu && isset($menu['items'])) {
            $menu['items'] = json_decode($menu['items'], true);
            self::$cache['menu_' . $location] = $menu;
        }
        
        return $menu;
    }
    
    /**
     * Get all menus
     */
    public static function getAllMenus()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM menus ORDER BY display_order ASC");
        $stmt->execute();
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($menus as &$menu) {
            if (isset($menu['items'])) {
                $menu['items'] = json_decode($menu['items'], true);
            }
        }
        
        return $menus;
    }
    
    /**
     * Save menu
     */
    public static function saveMenu($id, $data)
    {
        $db = Database::getInstance();
        
        if (is_array($data['items'])) {
            $data['items'] = json_encode($data['items']);
        }
        
        $stmt = $db->prepare("
            UPDATE menus 
            SET name = ?, items = ?, is_active = ?, display_order = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['items'],
            $data['is_active'] ?? 1,
            $data['display_order'] ?? 0,
            $id
        ]);
    }
    
    /**
     * Clear cache
     */
    public static function clearCache()
    {
        self::$cache = [];
    }
}
