<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Page;
use App\Models\Menu;
use App\Models\Media;

class ContentController extends Controller
{
    private $pageModel;
    private $menuModel;
    private $mediaModel;

    public function __construct()
    {
        parent::__construct();
        $this->pageModel = new Page();
        $this->menuModel = new Menu();
        $this->mediaModel = new Media();
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Get Summary Stats
        $pageStats = $this->pageModel->getStats();
        $menuStats = $this->menuModel->getStats();
        $mediaStats = $this->mediaModel->getStats();

        $data = [
            'user' => $user,
            'page_title' => 'Content Management - Admin Panel',
            'currentPage' => 'content',
            'stats' => [
                'pages' => $pageStats,
                'menus' => $menuStats,
                'media' => $mediaStats
            ]
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

        // Get filters
        $filters = [
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        $page = $_GET['page'] ?? 1;

        // Get pages using Model
        $pagesData = $this->pageModel->getAll($filters, $page, 10);

        $data = [
            'user' => $user,
            'pages' => $pagesData['data'], // Pass the array of pages
            'pagination' => $pagesData,    // Pass full pagination object if needed
            'filters' => $filters,
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

        $filters = [
            'search' => $_GET['search'] ?? null,
            'location' => $_GET['location'] ?? null,
            'is_active' => isset($_GET['status']) && $_GET['status'] === 'active' ? 1 : (isset($_GET['status']) && $_GET['status'] === 'inactive' ? 0 : null)
        ];

        // Get menus using Model
        $menus = $this->menuModel->getAll($filters);

        // Calculate item counts for display if not done in SQL
        foreach ($menus as &$menu) {
            $items = is_string($menu['items']) ? json_decode($menu['items'], true) : $menu['items'];
            $menu['items_count'] = is_array($items) ? count($items) : 0;
            $menu['status'] = $menu['is_active'] ? 'active' : 'inactive'; // Map for view compatibility
            $menu['modified_at'] = $menu['updated_at']; // Map for view compatibility
        }

        $data = [
            'user' => $user,
            'menus' => $menus,
            'filters' => $filters,
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

        $page = $_GET['page'] ?? 1;
        $filters = [
            'search' => $_GET['search'] ?? null,
            'type' => $_GET['type'] ?? null
        ];

        // Get media using Model
        $mediaData = $this->mediaModel->getAll($filters, $page, 20);

        // Transform data for view if necessary
        $media = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'filename' => $item['original_filename'],
                'url' => app_base_url('/storage/' . $item['file_path']), // Ensure correct path
                'type' => $item['mime_type'],
                'size' => $this->formatSize($item['file_size']),
                'uploaded_at' => $item['created_at']
            ];
        }, $mediaData['data']);

        $data = [
            'user' => $user,
            'media' => $media,
            'pagination' => $mediaData,
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

        $page = $this->pageModel->find($id);

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

        // Validate CSRF here preferably

        $id = $_POST['id'] ?? null;
        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'meta_title' => $_POST['meta_title'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'template' => $_POST['template'] ?? 'default',
            'author_id' => $user->id
        ];

        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        if ($id) {
            $this->pageModel->update($id, $data);
        } else {
            $this->pageModel->create($data);
        }

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

        $this->pageModel->delete($id);

        // Redirect back to pages list
        header('Location: ' . app_base_url('admin/content/pages'));
        exit;
    }

    public function saveMenus()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $id = $_POST['id'] ?? null;
        $data = [
            'name' => $_POST['name'] ?? '',
            'location' => $_POST['location'] ?? '',
            'items' => $_POST['items'] ?? '[]', // Expected JSON string
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        if ($id) {
            $this->menuModel->update($id, $data);
        } else {
            $this->menuModel->create($data);
        }

        header('Location: ' . app_base_url('admin/content/menus'));
        exit;
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
