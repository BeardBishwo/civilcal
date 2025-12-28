<?php

namespace App\Controllers;

use App\Core\Controller;
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
     * Show all published posts
     */
    public function index()
    {
        $posts = $this->postModel->getAll('published');

        $data = [
            'posts' => $posts,
            'page_title' => 'Blog - ' . (defined('APP_NAME') ? APP_NAME : 'Calculator Platform'),
            'currentPage' => 'blog'
        ];

        $this->view->render('blog/index', $data);
    }

    /**
     * Show single post
     */
    public function show($slug)
    {
        $post = $this->postModel->findBySlug($slug);

        if (!$post) {
            http_response_code(404);
            die('Post not found');
        }

        $recentPosts = $this->postModel->getRecent(5);

        $data = [
            'post' => $post,
            'recentPosts' => $recentPosts,
            'page_title' => $post['seo_title'] ?: ($post['title'] . ' - Blog'),
            'meta_description' => $post['seo_description'] ?: $post['excerpt'],
            'currentPage' => 'blog'
        ];

        $this->view->render('blog/show', $data);
    }
}
