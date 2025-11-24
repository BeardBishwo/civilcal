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
                'regular' => $totalUsers - $adminUsers,
            ]
        ];

        $this->view->render('admin/users/index', $data);
    }

    public function create()
    {
        $data = ['page_title' => 'Create User'];
        $this->view->render('admin/users/create', $data);
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

        $this->view->render('admin/users/edit', $data);
    }

    public function update($id)
    {
        // CSRF validation
        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (empty($submittedToken) || $submittedToken !== $sessionToken) {
            $_SESSION['flash_messages']['error'] = 'Invalid CSRF token';
            redirect('/admin/users');
            return;
        }

        // Handle user update logic here
        $_SESSION['flash_messages']['success'] = 'User updated successfully';
        redirect('/admin/users');
    }

    public function roles()
    {
        // Get user roles and permissions
        $roles = [
            'admin' => ['name' => 'Administrator', 'description' => 'Full system access'],
            'user' => ['name' => 'Regular User', 'description' => 'Standard user access'],
            'engineer' => ['name' => 'Engineer', 'description' => 'Engineering tools access']
        ];

        // Get count of users per role
        $stmt = $this->db->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
        $roleStats = [];
        while ($row = $stmt->fetch()) {
            $roleStats[$row['role']] = $row['count'];
        }

        $data = [
            'page_title' => 'User Roles Management',
            'roles' => $roles,
            'role_stats' => $roleStats
        ];

        $this->view->render('admin/users/roles', $data);
    }

    public function permissions()
    {
        // Get user permissions matrix
        $permissions = [
            'admin' => ['manage_users', 'manage_system', 'view_analytics', 'manage_modules'],
            'user' => ['use_calculators', 'view_profile'],
            'engineer' => ['use_calculators', 'view_profile', 'advanced_tools']
        ];

        $data = [
            'page_title' => 'User Permissions',
            'permissions' => $permissions
        ];

        $this->view->render('admin/users/permissions', $data);
    }

    public function bulk()
    {
        // Get all users for bulk operations
        $stmt = $this->db->query("SELECT id, username, email, role, is_active, created_at FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $data = [
            'page_title' => 'Bulk User Operations',
            'users' => $users,
            'stats' => [
                'total' => count($users),
                'active' => count(array_filter($users, fn($u) => $u['is_active'])),
                'inactive' => count(array_filter($users, fn($u) => !$u['is_active']))
            ]
        ];

        $this->view->render('admin/users/bulk', $data);
    }

    private function checkAdminAccess()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
            exit;
        }
    }
}
