<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        // Middleware handles the general admin check, 
        // but we can be explicit here too if needed.
    }

    /**
     * List all users with filtering and pagination
     */
    public function index()
    {
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $where = ["1=1"];
        $params = [];

        if ($search) {
            $where[] = "(username LIKE :search OR email LIKE :search OR first_name LIKE :search OR last_name LIKE :search)";
            $params['search'] = "%$search%";
        }

        if ($role) {
            $where[] = "role = :role";
            $params['role'] = $role;
        }

        if ($status) {
            $where[] = "is_active = :status";
            $params['status'] = ($status === 'active' ? 1 : 0);
        }

        $whereStr = implode(" AND ", $where);

        // Get Total for pagination
        $countSql = "SELECT COUNT(*) FROM users WHERE $whereStr";
        $stmt = $this->db->getPdo()->prepare($countSql);
        $stmt->execute($params);
        $totalRecords = $stmt->fetchColumn();

        // Get Users
        $sql = "SELECT * FROM users WHERE $whereStr ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        // Stats (Overview)
        $stats = [
            'total' => $this->db->getPdo()->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'active' => $this->db->getPdo()->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn(),
            'admins' => $this->db->getPdo()->query("SELECT COUNT(*) FROM users WHERE role IN ('admin', 'super_admin')")->fetchColumn(),
        ];

        $this->view->render('users/index', [
            'page_title' => 'User Management',
            'users' => $users,
            'stats' => $stats,
            'filters' => [
                'page' => $page,
                'total_pages' => ceil($totalRecords / $limit),
                'total_records' => $totalRecords
            ]
        ]);
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $this->view->render('users/create', [
            'page_title' => 'Create New User'
        ]);
    }

    /**
     * Store new user
     */
    public function store()
    {
        try {
            $data = $_POST;
            
            // Basic validation
            if (empty($data['email']) || empty($data['password'])) {
                throw new Exception("Email and Password are required.");
            }

            // Check if user already exists
            if ($this->userModel->findByEmail($data['email'])) {
                throw new Exception("Email address is already in use.");
            }

            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $userId = $this->userModel->create($data);
            
            if ($userId) {
                $_SESSION['flash_success'] = "User created successfully.";
                return $this->redirect('/admin/users');
            } else {
                throw new Exception("Failed to create user.");
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return $this->redirect('/admin/users/create');
        }
    }

    /**
     * Edit user form
     */
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            $_SESSION['flash_error'] = "User not found.";
            return $this->redirect('/admin/users');
        }

        $this->view->render('users/edit', [
            'page_title' => 'Edit User: ' . ($user['username'] ?? $user['email']),
            'user' => $user
        ]);
    }

    /**
     * Update user details
     */
    public function update($id)
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) throw new Exception("User not found.");

            $data = $_POST;
            
            // Don't allow changing role of super_admin if not super_admin
            if ($user['role'] === 'super_admin' && ($_SESSION['user']['role'] ?? '') !== 'super_admin') {
                $data['role'] = 'super_admin'; 
            }

            $success = $this->userModel->adminUpdate($id, $data);
            
            if ($success) {
                $_SESSION['flash_success'] = "User updated successfully.";
            } else {
                throw new Exception("No changes were made or update failed.");
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }
        
        return $this->redirect("/admin/users/$id/edit");
    }

    /**
     * Ban a user
     */
    public function ban($id)
    {
        try {
            $reason = $_POST['reason'] ?? 'No reason provided';
            
            $stmt = $this->db->getPdo()->prepare("UPDATE users SET is_active = 0, is_banned = 1, ban_reason = ?, banned_at = NOW() WHERE id = ?");
            $success = $stmt->execute([$reason, $id]);
            
            return $this->json(['success' => $success]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a user
     */
    public function delete($id)
    {
        try {
            // Security: Prevent deleting self
            if ($id == $_SESSION['user_id']) {
                throw new Exception("You cannot delete your own account.");
            }

            $success = $this->userModel->deleteAccount($id);
            return $this->json(['success' => $success]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Bulk Delete users
     */
    public function bulkDelete()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $ids = $input['ids'] ?? [];
            
            if (empty($ids)) throw new Exception("No users selected.");

            // Remove self from IDs if present
            $ids = array_filter($ids, fn($id) => $id != $_SESSION['user_id']);
            
            if (empty($ids)) throw new Exception("Invalid selection.");

            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $this->db->getPdo()->prepare("DELETE FROM users WHERE id IN ($placeholders)");
            $success = $stmt->execute($ids);
            
            return $this->json(['success' => $success, 'message' => count($ids) . " users deleted."]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
