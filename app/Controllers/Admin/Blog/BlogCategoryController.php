<?php

namespace App\Controllers\Admin\Blog;

use App\Core\Controller;
use App\Core\Database;

class BlogCategoryController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * List all categories
     */
    public function index()
    {
        $categories = $this->db->query("
            SELECT bc.*,
                   COUNT(ba.id) as article_count
            FROM blog_categories bc
            LEFT JOIN blog_articles ba ON bc.id = ba.category_id
            GROUP BY bc.id
            ORDER BY bc.display_order, bc.name
        ")->fetchAll();
        
        $this->view('admin/blog/categories/index', [
            'categories' => $categories,
            'page_title' => 'Blog Categories'
        ]);
    }

    /**
     * Store new category
     */
    public function store()
    {
        $slug = $this->generateSlug($_POST['name']);
        
        $data = [
            'name' => $_POST['name'],
            'slug' => $slug,
            'description' => $_POST['description'] ?? '',
            'image' => $_POST['image'] ?? '',
            'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null,
            'display_order' => $_POST['display_order'] ?? 0
        ];
        
        $id = $this->db->insert('blog_categories', $data);
        
        echo json_encode([
            'success' => true,
            'id' => $id
        ]);
    }

    /**
     * Update category
     */
    public function update($id)
    {
        $category = $this->db->findOne('blog_categories', ['id' => $id]);
        $slug = ($category['name'] !== $_POST['name']) 
            ? $this->generateSlug($_POST['name']) 
            : $category['slug'];
        
        $data = [
            'name' => $_POST['name'],
            'slug' => $slug,
            'description' => $_POST['description'] ?? '',
            'image' => $_POST['image'] ?? '',
            'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null,
            'display_order' => $_POST['display_order'] ?? 0
        ];
        
        $this->db->update('blog_categories', $data, ['id' => $id]);
        
        echo json_encode(['success' => true]);
    }

    /**
     * Delete category
     */
    public function delete($id)
    {
        $this->db->delete('blog_categories', ['id' => $id]);
        
        echo json_encode(['success' => true]);
    }

    /**
     * Generate unique slug
     */
    private function generateSlug($name, $id = null)
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure unique
        $original = $slug;
        $counter = 1;
        
        while (true) {
            $existing = $this->db->findOne('blog_categories', ['slug' => $slug]);
            if (!$existing || ($id && $existing['id'] == $id)) {
                break;
            }
            $slug = $original . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
