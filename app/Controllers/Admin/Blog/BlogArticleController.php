<?php

namespace App\Controllers\Admin\Blog;

use App\Core\Controller;
use App\Core\Database;

class BlogArticleController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * List all blog articles
     */
    public function index()
    {
        $articles = $this->db->query("
            SELECT ba.*, 
                   u.first_name, u.last_name,
                   bc.name as category_name
            FROM blog_articles ba
            LEFT JOIN users u ON ba.author_id = u.id
            LEFT JOIN blog_categories bc ON ba.category_id = bc.id
            ORDER BY ba.created_at DESC
        ")->fetchAll();
        
        $this->view('admin/blog/articles/index', [
            'articles' => $articles,
            'page_title' => 'Blog Articles'
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = $this->db->query("
            SELECT * FROM blog_categories 
            ORDER BY name
        ")->fetchAll();
        
        $this->view('admin/blog/articles/create', [
            'categories' => $categories,
            'page_title' => 'Create Blog Article'
        ]);
    }

    /**
     * Store new article
     */
    public function store()
    {
        $currentUser = current_user();
        
        // Generate slug
        $slug = $this->generateSlug($_POST['title']);
        
        // Prepare data
        $data = [
            'title' => $_POST['title'],
            'slug' => $slug,
            'content' => $_POST['content'],
            'excerpt' => $_POST['excerpt'] ?? '',
            'featured_image' => $_POST['featured_image'] ?? '',
            'author_id' => $currentUser['id'],
            'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
            'tags' => $_POST['tags'] ?? '',
            'meta_title' => $_POST['meta_title'] ?? $_POST['title'],
            'meta_description' => $_POST['meta_description'] ?? '',
            'meta_keywords' => $_POST['meta_keywords'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'published_at' => $_POST['status'] === 'published' ? date('Y-m-d H:i:s') : null,
            'scheduled_at' => $_POST['status'] === 'scheduled' ? $_POST['scheduled_at'] : null
        ];
        
        $id = $this->db->insert('blog_articles', $data);
        
        echo json_encode([
            'success' => true,
            'id' => $id,
            'slug' => $slug,
            'url' => app_base_url('blog/' . $slug)
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $article = $this->db->findOne('blog_articles', ['id' => $id]);
        
        if (!$article) {
            http_response_code(404);
            echo "Article not found";
            return;
        }
        
        $categories = $this->db->query("
            SELECT * FROM blog_categories 
            ORDER BY name
        ")->fetchAll();
        
        $this->view('admin/blog/articles/edit', [
            'article' => $article,
            'categories' => $categories,
            'page_title' => 'Edit: ' . $article['title']
        ]);
    }

    /**
     * Update article
     */
    public function update($id)
    {
        // Generate new slug if title changed
        $article = $this->db->findOne('blog_articles', ['id' => $id]);
        $slug = ($article['title'] !== $_POST['title']) 
            ? $this->generateSlug($_POST['title']) 
            : $article['slug'];
        
        $data = [
            'title' => $_POST['title'],
            'slug' => $slug,
            'content' => $_POST['content'],
            'excerpt' => $_POST['excerpt'] ?? '',
            'featured_image' => $_POST['featured_image'] ?? '',
            'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
            'tags' => $_POST['tags'] ?? '',
            'meta_title' => $_POST['meta_title'] ?? $_POST['title'],
            'meta_description' => $_POST['meta_description'] ?? '',
            'meta_keywords' => $_POST['meta_keywords'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'published_at' => $_POST['status'] === 'published' && !$article['published_at'] 
                ? date('Y-m-d H:i:s') 
                : $article['published_at'],
            'scheduled_at' => $_POST['status'] === 'scheduled' ? $_POST['scheduled_at'] : null
        ];
        
        $this->db->update('blog_articles', $data, ['id' => $id]);
        
        echo json_encode([
            'success' => true,
            'slug' => $slug,
            'url' => app_base_url('blog/' . $slug)
        ]);
    }

    /**
     * Delete article
     */
    public function delete($id)
    {
        $this->db->delete('blog_articles', ['id' => $id]);
        
        echo json_encode(['success' => true]);
    }

    /**
     * Generate unique slug
     */
    private function generateSlug($title, $id = null)
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = trim($slug, '-');
        $slug = substr($slug, 0, 100);
        
        // Ensure unique
        $original = $slug;
        $counter = 1;
        
        while (true) {
            $existing = $this->db->findOne('blog_articles', ['slug' => $slug]);
            if (!$existing || ($id && $existing['id'] == $id)) {
                break;
            }
            $slug = $original . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Public: Show single article
     */
    public function publicShow($slug)
    {
        $article = $this->db->query("
            SELECT ba.*, 
                   u.first_name, u.last_name,
                   bc.name as category_name, bc.slug as category_slug
            FROM blog_articles ba
            LEFT JOIN users u ON ba.author_id = u.id
            LEFT JOIN blog_categories bc ON ba.category_id = bc.id
            WHERE ba.slug = :slug 
            AND ba.status = 'published'
        ", ['slug' => $slug])->fetch();
        
        if (!$article) {
            http_response_code(404);
            echo "Article not found";
            return;
        }
        
        // Increment view count
        $this->db->query("
            UPDATE blog_articles 
            SET view_count = view_count + 1 
            WHERE id = :id
        ", ['id' => $article['id']]);
        
        // Get related articles
        $related = $this->db->query("
            SELECT * FROM blog_articles 
            WHERE status = 'published'
            AND id != :id
            AND (category_id = :category_id OR tags LIKE :tags)
            ORDER BY view_count DESC
            LIMIT 3
        ", [
            'id' => $article['id'],
            'category_id' => $article['category_id'],
            'tags' => '%' . explode(',', $article['tags'])[0] . '%'
        ])->fetchAll();
        
        $this->view('blog/article', [
            'article' => $article,
            'related' => $related,
            'page_title' => $article['title'],
            'meta_description' => $article['meta_description'] ?: substr(strip_tags($article['content']), 0, 155)
        ]);
    }

    /**
     * Public: Blog index
     */
    public function publicIndex()
    {
        $page = $_GET['page'] ?? 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;
        
        $articles = $this->db->query("
            SELECT ba.*, 
                   u.first_name, u.last_name,
                   bc.name as category_name
            FROM blog_articles ba
            LEFT JOIN users u ON ba.author_id = u.id
            LEFT JOIN blog_categories bc ON ba.category_id = bc.id
            WHERE ba.status = 'published'
            ORDER BY ba.published_at DESC
            LIMIT $perPage OFFSET $offset
        ")->fetchAll();
        
        $total = $this->db->query("
            SELECT COUNT(*) as count 
            FROM blog_articles 
            WHERE status = 'published'
        ")->fetch()['count'];
        
        $this->view('blog/index', [
            'articles' => $articles,
            'page' => $page,
            'total_pages' => ceil($total / $perPage),
            'page_title' => 'Blog'
        ]);
    }
}
