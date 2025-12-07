<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;

class ContentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $data = [
            'user' => $user,
            'page_title' => 'Content Management - Admin Panel',
            'currentPage' => 'content'
        ];

        $this->view->render('admin/content/index', $data);
    }

    public function pages()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Get all pages from the database
        $pages = $this->getPages();
        
        $data = [
            'user' => $user,
            'pages' => $pages,
            'page_title' => 'Manage Pages - Admin Panel',
            'currentPage' => 'content'
        ];

        $this->view->render('admin/content/pages', $data);
    }

    public function menus()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Get all menus from the database
        $menus = $this->getMenus();
        
        $data = [
            'user' => $user,
            'menus' => $menus,
            'page_title' => 'Manage Menus - Admin Panel',
            'currentPage' => 'content'
        ];

        $this->view->render('admin/content/menus', $data);
    }

    public function media()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Get all media files
        $media = $this->getMedia();
        
        $data = [
            'user' => $user,
            'media' => $media,
            'page_title' => 'Media Library - Admin Panel',
            'currentPage' => 'content'
        ];

        $this->view->render('admin/content/media', $data);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }
        
        $data = [
            'user' => $user,
            'page_title' => 'Create New Page - Admin Panel',
            'currentPage' => 'content',
            'is_edit' => false,
            'page' => null
        ];

        $this->view->render('admin/content/create', $data);
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }
        
        // Get page by ID (placeholder logic)
        $page = $this->getPageById($id);
        
        if (!$page) {
            http_response_code(404);
            die('Page not found');
        }
        
        $data = [
            'user' => $user,
            'page_title' => 'Edit Page - Admin Panel',
            'currentPage' => 'content',
            'is_edit' => true,
            'page' => $page
        ];

        $this->view->render('admin/content/create', $data);
    }

    public function save()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Handle page creation/update
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $status = $_POST['status'] ?? 'draft';

        // Placeholder for save logic
        // In a real implementation, this would save to database
        
        // Redirect back to pages list
        header('Location: ' . app_base_url('admin/content/pages'));
        exit;
    }

    public function delete($id)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Placeholder for delete logic
        // In a real implementation, this would delete from database
        
        // Redirect back to pages list
        header('Location: ' . app_base_url('admin/content/pages'));
        exit;
    }

    private function getPages()
    {
        // Placeholder - in real implementation, this would query the database
        return [
            [
                'id' => 1,
                'title' => 'Home',
                'slug' => 'home',
                'status' => 'published',
                'created_at' => '2024-10-15',
                'author' => 'Admin'
            ],
            [
                'id' => 2,
                'title' => 'About',
                'slug' => 'about',
                'status' => 'published',
                'created_at' => '2024-10-16',
                'author' => 'Admin'
            ],
            [
                'id' => 3,
                'title' => 'Contact',
                'slug' => 'contact',
                'status' => 'draft',
                'created_at' => '2024-10-17',
                'author' => 'Admin'
            ]
        ];
    }

    private function getMenus()
    {
        // Placeholder - in real implementation, this would query the database
        return [
            [
                'id' => 1,
                'name' => 'Main Menu',
                'location' => 'header',
                'items_count' => 5,
                'modified_at' => '2024-10-15'
            ],
            [
                'id' => 2,
                'name' => 'Footer Menu',
                'location' => 'footer',
                'items_count' => 3,
                'modified_at' => '2024-10-14'
            ]
        ];
    }

    private function getMedia()
    {
        // Placeholder - in real implementation, this would scan the media directory
        return [
            [
                'id' => 1,
                'filename' => 'logo.png',
                'url' => '/storage/media/logo.png',
                'type' => 'image/png',
                'size' => '24.5 KB',
                'uploaded_at' => '2024-10-15'
            ],
            [
                'id' => 2,
                'filename' => 'hero-image.jpg',
                'url' => '/storage/media/hero-image.jpg',
                'type' => 'image/jpeg',
                'size' => '128.7 KB',
                'uploaded_at' => '2024-10-16'
            ]
        ];
    }

    private function getPageById($id)
    {
        // Placeholder - in real implementation, this would query the database
        $pages = $this->getPages();
        foreach ($pages as $page) {
            if ($page['id'] == $id) {
                return $page;
            }
        }
        return null;
    }
}