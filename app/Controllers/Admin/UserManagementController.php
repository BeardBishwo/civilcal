<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;

class UserManagementController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Admin access check is handled by AdminMiddleware
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
        $this->checkCSRF();

        try {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['password_confirmation'] ?? '';
            $role = $_POST['role'] ?? '';

            $errors = [];

            if (!$firstName) {
                $errors[] = 'First name is required.';
            }
            if (!$lastName) {
                $errors[] = 'Last name is required.';
            }
            if (!$username) {
                $errors[] = 'Username is required.';
            }
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required.';
            }
            if (!$password || strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters.';
            }
            if ($password !== $confirm) {
                $errors[] = 'Passwords do not match.';
            }
            if (!$role) {
                $errors[] = 'Role selection is required.';
            }

            $userModel = new User();

            if ($userModel->findByEmail($email)) {
                $errors[] = 'Email already exists.';
            }
            if ($userModel->findByUsername($username)) {
                $errors[] = 'Username already exists.';
            }

            if (!empty($errors)) {
                $_SESSION['flash_messages']['error'] = implode('\n', $errors);
                redirect('/admin/users/create');
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $userId = $userModel->create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role,
                'is_active' => isset($_POST['is_active']) ? (int) $_POST['is_active'] : 1,
                'email_verified' => !empty($_POST['email_verified']) ? 1 : 0,
                'terms_agreed' => !empty($_POST['terms_agreed']) ? 1 : 0,
                'marketing_emails' => !empty($_POST['marketing_emails']) ? 1 : 0,
                'send_welcome_email' => !empty($_POST['send_welcome_email']) ? 1 : 0,
            ]);

            $_SESSION['flash_messages']['success'] = 'User created successfully.';
            redirect('/admin/users/' . $userId . '/edit');
        } catch (\Exception $e) {
            error_log('User creation failed: ' . $e->getMessage());
            $_SESSION['flash_messages']['error'] = 'Failed to create user: ' . $e->getMessage();
            redirect('/admin/users/create');
        }
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

<<<<<<< HEAD
=======
    private function checkAdminAccess()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
            exit;
        }
    }
>>>>>>> temp-branch
}
