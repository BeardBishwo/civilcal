<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Post;

class BlogController extends Controller
{
    private $postModel;

    public function __construct()
    {
        parent::__construct();
        $this->postModel = new Post();
    }

    /**
     * List all posts
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $posts = $this->postModel->getAll();

        $data = [
            'user' => $user,
            'posts' => $posts,
            'page_title' => 'Blog Management - Admin Panel',
            'currentPage' => 'blog'
        ];

        $this->view->render('admin/blog/index', $data);
    }

    /**
     * Show create post form
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $data = [
            'user' => $user,
            'page_title' => 'Create New Post - Admin Panel',
            'currentPage' => 'blog'
        ];

        $this->view->render('admin/blog/create', $data);
    }

    /**
     * Store post in database
     */
    public function store()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $content = $_POST['content'] ?? '';
        $status = $_POST['status'] ?? 'published';
        $excerpt = $_POST['excerpt'] ?? '';
        $featured_image = $_POST['featured_image'] ?? '';
        $seo_title = $_POST['seo_title'] ?? '';
        $seo_description = $_POST['seo_description'] ?? '';

        if (empty($title) || empty($slug)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Title and Slug are required']);
            return;
        }

        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $excerpt,
            'status' => $status,
            'featured_image' => $featured_image,
            'author_id' => $user->id,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description
        ];

        try {
            $this->postModel->create($data);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Post created successfully']);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show edit post form
     */
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $post = $this->postModel->find($id);
        if (!$post) {
            die('Post not found');
        }

        $data = [
            'user' => $user,
            'post' => $post,
            'page_title' => 'Edit Post - Admin Panel',
            'currentPage' => 'blog'
        ];

        $this->view->render('admin/blog/edit', $data);
    }

    /**
     * Update post in database
     */
    public function update($id)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        $post = $this->postModel->find($id);
        if (!$post) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Post not found']);
            return;
        }

        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $content = $_POST['content'] ?? '';
        $status = $_POST['status'] ?? 'published';
        $excerpt = $_POST['excerpt'] ?? '';
        $featured_image = $_POST['featured_image'] ?? '';
        $seo_title = $_POST['seo_title'] ?? '';
        $seo_description = $_POST['seo_description'] ?? '';

        if (empty($title) || empty($slug)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Title and Slug are required']);
            return;
        }

        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $excerpt,
            'status' => $status,
            'featured_image' => $featured_image,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description
        ];

        try {
            $this->postModel->update($id, $data);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Post updated successfully']);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete post
     */
    public function delete($id)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        try {
            $this->postModel->delete($id);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
