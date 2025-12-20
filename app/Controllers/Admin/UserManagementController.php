<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;
use App\Services\EmailManager;

class UserManagementController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->checkAdminAccess();
    }

    public function index()
    {
        // Pagination Parameters
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        // Filter Parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $role = isset($_GET['role']) ? $_GET['role'] : '';

        // Build Query Conditions
        $where = [];
        $params = [];

        if ($search) {
            $where[] = "(username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($status !== '') {
            if ($status === 'active') {
                 $where[] = "is_active = 1";
            } elseif ($status === 'inactive') {
                 $where[] = "is_active = 0";
            }
        }
        
        if ($role) {
            $where[] = "role = ?";
            $params[] = $role;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get Filtered Count
        $countSql = "SELECT COUNT(*) FROM users $whereSql";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $totalFiltered = $stmt->fetchColumn();
        $totalPages = ceil($totalFiltered / $perPage);

        // Get Paginated Data
        $sql = "SELECT * FROM users $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get Global Statistics (Unfiltered)
        $statsStmt = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins
            FROM users");
        $rawStats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

        $stats = [
            'total' => $rawStats['total'] ?? 0,
            'active' => $rawStats['active'] ?? 0,
            'admins' => $rawStats['admins'] ?? 0,
            'regular' => ($rawStats['total'] ?? 0) - ($rawStats['admins'] ?? 0)
        ];

        $data = [
            'page_title' => 'User Management',
            'users' => $users,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'role' => $role,
                'page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalFiltered
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
        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // Get CSRF token - try both header and POST data
        $submittedToken = '';
        if ($isAjax) {
            $submittedToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
        } else {
            $submittedToken = $_POST['csrf_token'] ?? '';
        }
        
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (empty($submittedToken) || $submittedToken !== $sessionToken) {
            if ($isAjax) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid CSRF token'
                ]);
                exit;
            } else {
                $_SESSION['flash_messages']['error'] = 'Invalid CSRF token';
                redirect('/admin/users/create');
                return;
            }
        }

        try {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
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
                $errorMessage = implode(' ', $errors);
                if ($isAjax) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => $errorMessage
                    ]);
                    exit;
                } else {
                    $_SESSION['flash_messages']['error'] = $errorMessage;
                    redirect('/admin/users/create');
                    return;
                }
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
                'send_welcome_email' => 0, // Disable default welcome email, we send a custom one
                'force_password_change' => 1,
                'password_generated_at' => date('Y-m-d H:i:s')
            ]);

            // Send Credentials Email
            try {
                $emailManager = new EmailManager();
                $loginUrl = app_base_url('/login');
                $fullName = $firstName . ' ' . $lastName;
                $emailManager->sendNewAccountEmail($email, $fullName, $username, $password, $loginUrl);
            } catch (\Exception $e) {
                error_log('Failed to send credentials email: ' . $e->getMessage());
                // Non-blocking error
            }

            if ($isAjax) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User created successfully!',
                    'redirect' => '/admin/users/' . $userId . '/edit'
                ]);
                exit;
            } else {
                $_SESSION['flash_messages']['success'] = 'User created successfully.';
                redirect('/admin/users/' . $userId . '/edit');
            }
        } catch (\Exception $e) {
            error_log('User creation failed: ' . $e->getMessage());
            $errorMessage = 'Failed to create user: ' . $e->getMessage();
            
            if ($isAjax) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $errorMessage
                ]);
                exit;
            } else {
                $_SESSION['flash_messages']['error'] = $errorMessage;
                redirect('/admin/users/create');
            }
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
        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // CSRF validation
        $submittedToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (empty($submittedToken) || $submittedToken !== $sessionToken) {
            if ($isAjax) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
                exit;
            } else {
                $_SESSION['flash_messages']['error'] = 'Invalid CSRF token';
                redirect('/admin/users');
                return;
            }
        }

        try {
            $userModel = new User();
            $currentUser = $userModel->find($id);

            if (!$currentUser) {
                throw new \Exception('User not found');
            }

            // Prevent modifying own role/status to avoid lockout
            if ($id == $_SESSION['user_id']) {
                $role = $currentUser['role']; // Keep existing role
                $isActive = 1; // Always keep active
            } else {
                $role = $_POST['role'] ?? $currentUser['role'];
                $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : $currentUser['is_active'];
            }

            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            // Validation
            $errors = [];
            if (!$firstName) $errors[] = "First name is required";
            if (!$lastName) $errors[] = "Last name is required";
            if (!$username) $errors[] = "Username is required";
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";

            // Check uniqueness if changed
            if ($username !== $currentUser['username'] && $userModel->findByUsername($username)) {
                $errors[] = "Username already taken";
            }
            $existingEmailUser = $userModel->findByEmail($email);
            if ($existingEmailUser && $existingEmailUser->id != $id) {
                $errors[] = "Email already taken";
            }

            if (!empty($errors)) {
                throw new \Exception(implode(', ', $errors));
            }

            $updateData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'email' => $email,
                'role' => $role,
                'is_active' => $isActive,
                'email_verified' => isset($_POST['email_verified']) ? (int)$_POST['email_verified'] : 0,
                'marketing_emails' => isset($_POST['marketing_emails']) ? (int)$_POST['marketing_emails'] : 0
            ];

            // Password update (optional)
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    throw new \Exception("Password must be at least 6 characters");
                }
                $updateData['password'] = $_POST['password'];
            }

            // Account actions (reset password email, etc could go here or separate methods)
            // For now focused on data update

            $userModel->adminUpdate($id, $updateData);

            if ($isAjax) {
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
                exit;
            } else {
                $_SESSION['flash_messages']['success'] = 'User updated successfully';
                redirect('/admin/users');
            }

        } catch (\Exception $e) {
            if ($isAjax) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            } else {
                $_SESSION['flash_messages']['error'] = $e->getMessage();
                redirect("/admin/users/$id/edit");
            }
        }
    }

    public function delete($id)
    {
        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // CSRF validation
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
        
        // Also check JSON input if token is missing
        if (empty($token)) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (isset($input['csrf_token'])) {
                $token = $input['csrf_token'];
            }
        }

        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (!$token || $token !== $sessionToken) {
            error_log("Delete User Failed: CSRF Mismatch. Received: '$token', Expected: '$sessionToken'");
             if ($isAjax) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
                exit;
            } else {
                $_SESSION['flash_messages']['error'] = 'Invalid CSRF token';
                redirect('/admin/users');
                return;
            }
        }

        // Prevent deleting self
        if ($id == $_SESSION['user_id']) {
             error_log("Delete User Failed: Attempt to delete self. User ID: $id");
             if ($isAjax) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cannot delete yourself']);
                exit;
            } else {
                $_SESSION['flash_messages']['error'] = 'Cannot delete yourself';
                redirect('/admin/users');
                return;
            }
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt->execute([$id]);

            if ($result) {
                if ($isAjax) {
                    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
                    exit;
                } else {
                    $_SESSION['flash_messages']['success'] = 'User deleted successfully';
                    redirect('/admin/users');
                }
            } else {
                error_log("Delete User Failed: Database execute returned false for ID $id");
                throw new \Exception('Database error');
            }
        } catch (\Exception $e) {
            error_log("Delete User Failed: Exception " . $e->getMessage());
            if ($isAjax) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()]);
                exit;
            } else {
                $_SESSION['flash_messages']['error'] = 'Error deleting user';
                redirect('/admin/users');
            }
        }
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

    public function bulkDelete()
    {
        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if (!$isAjax) {
            redirect('/admin/users');
            exit;
        }

        // CSRF validation
        $input = json_decode(file_get_contents('php://input'), true);
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? '');
        
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

        $userIds = $input['ids'] ?? [];
        if (empty($userIds) || !is_array($userIds)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No users selected']);
            exit;
        }

        // Prevent deleting self
        if (in_array($_SESSION['user_id'], $userIds)) {
            $userIds = array_diff($userIds, [$_SESSION['user_id']]);
            if (empty($userIds)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cannot delete yourself']);
                exit;
            }
        }

        try {
            // Convert IDs to parameterized placeholder string
            $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
            $sql = "DELETE FROM users WHERE id IN ($placeholders)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(array_values($userIds));

            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => count($userIds) . ' users deleted successfully'
                ]);
            } else {
                throw new \Exception('Database error');
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting users']);
        }
        exit;
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
