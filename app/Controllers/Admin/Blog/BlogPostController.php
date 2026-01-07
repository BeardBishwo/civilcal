<?php

namespace App\Controllers\Admin\Blog;

use App\Core\Controller;
use App\Core\Database;

class BlogPostController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * List all blog posts
     */
    public function index()
    {
        $posts = $this->db->query("
            SELECT bp.*,
                   JSON_LENGTH(bp.question_ids) as question_count
            FROM blog_posts bp
            ORDER BY bp.created_at DESC
        ")->fetchAll();
        
        $this->view('admin/blog/posts/index', [
            'posts' => $posts,
            'page_title' => 'Blog Posts'
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = $this->db->query("
            SELECT * FROM syllabus_nodes 
            WHERE parent_id IS NULL 
            ORDER BY title
        ")->fetchAll();
        
        $this->view('admin/blog/posts/create', [
            'categories' => $categories,
            'page_title' => 'Create Blog Post'
        ]);
    }

    /**
     * Generate and save blog post
     */
    public function store()
    {
        $type = $_POST['type'];
        $params = json_decode($_POST['params'] ?? '{}', true);
        
        // Get questions based on type and params
        $questions = $this->getQuestions($type, $params);
        
        if (empty($questions)) {
            echo json_encode(['success' => false, 'message' => 'No questions found']);
            return;
        }
        
        // Generate slug
        $slug = $this->generateSlug($_POST['title']);
        
        // Prepare data
        $data = [
            'title' => $_POST['title'],
            'slug' => $slug,
            'type' => $type,
            'introduction' => $_POST['introduction'] ?? '',
            'conclusion' => $_POST['conclusion'] ?? '',
            'meta_description' => substr($_POST['title'], 0, 155),
            'auto_generate' => isset($_POST['auto_generate']) ? 1 : 0,
            'generation_type' => $type,
            'generation_params' => json_encode($params),
            'question_ids' => json_encode(array_column($questions, 'id')),
            'question_count' => count($questions),
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
            'published_at' => date('Y-m-d H:i:s')
        ];
        
        $id = $this->db->insert('blog_posts', $data);
        
        echo json_encode([
            'success' => true,
            'id' => $id,
            'slug' => $slug,
            'url' => app_base_url('blog/' . $slug)
        ]);
    }

    /**
     * Get questions based on type and params
     */
    private function getQuestions($type, $params)
    {
        $query = "SELECT * FROM quiz_questions WHERE is_active = 1";
        $queryParams = [];
        
        switch($type) {
            case 'popular':
                $query .= " ORDER BY view_count DESC";
                break;
                
            case 'category':
                if (!empty($params['category_id'])) {
                    $query .= " AND category_id = :category_id";
                    $queryParams['category_id'] = $params['category_id'];
                }
                $query .= " ORDER BY view_count DESC";
                break;
                
            case 'difficulty':
                if (!empty($params['difficulty'])) {
                    $query .= " AND difficulty_level = :difficulty";
                    $queryParams['difficulty'] = $params['difficulty'];
                }
                $query .= " ORDER BY RAND()";
                break;
                
            case 'recent':
                $query .= " ORDER BY created_at DESC";
                break;
                
            case 'featured':
                $query .= " AND status = 'approved' ORDER BY view_count DESC";
                break;
        }
        
        $limit = $params['limit'] ?? 10;
        $query .= " LIMIT " . intval($limit);
        
        return $this->db->query($query, $queryParams)->fetchAll();
    }

    /**
     * Generate unique slug
     */
    private function generateSlug($title)
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure unique
        $original = $slug;
        $counter = 1;
        
        while ($this->db->findOne('blog_posts', ['slug' => $slug])) {
            $slug = $original . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Preview blog post
     */
    public function show($id)
    {
        $post = $this->db->findOne('blog_posts', ['id' => $id]);
        
        if (!$post) {
            http_response_code(404);
            echo "Post not found";
            return;
        }
        
        $questionIds = json_decode($post['question_ids'], true);
        
        if (empty($questionIds)) {
            $questions = [];
        } else {
            $placeholders = implode(',', array_fill(0, count($questionIds), '?'));
            $questions = $this->db->query("
                SELECT * FROM quiz_questions 
                WHERE id IN ($placeholders)
            ", $questionIds)->fetchAll();
        }
        
        $this->view('admin/blog/posts/preview', [
            'post' => $post,
            'questions' => $questions,
            'page_title' => $post['title']
        ]);
    }

    /**
     * Delete blog post
     */
    public function delete($id)
    {
        $this->db->delete('blog_posts', ['id' => $id]);
        
        echo json_encode(['success' => true]);
    }
}
