<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;

class UserManagementController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkAdminAccess();
    }

    public function index()
    {
        // Get all users from database
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get statistics
        $totalUsers = count($users);
        $activeUsers = count(array_filter($users, fn($u) => $u['is_active'] ?? true));
        $adminUsers = count(array_filter($users, fn($u) => ($u['role'] ?? 'user') === 'admin'));

        $data = [
            'page_title' => 'User Management',
            'users' => $users,
            'stats' => [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'admins' => $adminUsers,
                'regular' => $totalUsers - $adminUsers
            ]
        ];

        $this->view('admin/users/index', $data);
    }

    public function create()
    {
        $data = ['page_title' => 'Create User'];
        $this->view('admin/users/create', $data);
    }

    public function store()
    {
        // Handle user creation
        $this->checkCSRF();
        
        // Validation and creation logic here
        $_SESSION['flash_messages']['success'] = 'User created successfully';
        redirect('/admin/users');
    }

    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['flash_messages']['error'] = 'User not found';
            redirect('/admin/users');
            return;
        }

        $data = [
            'page_title' => 'Edit User',
            'user' => $user
        ];

        $this->view('admin/users/edit', $data);
    }

    private function checkAdminAccess()
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            redirect('/login');
            exit;
        }
    }
}
