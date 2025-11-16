<?php
namespace App\Models;

use App\Core\SafeModel;

/**
 * Enhanced User model using SafeModel base class
 * Demonstrates proper validation, security, and standardized patterns
 */
class EnhancedUser extends SafeModel {
    protected $table = 'users';
    
    protected array $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'company',
        'phone',
        'role',
        'email_verified_at',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    protected array $hidden = [
        'password',
        'remember_token'
    ];
    
    protected array $validationRules = [
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
        'first_name' => ['required', 'string'],
        'last_name' => ['required', 'string'],
        'role' => ['required'],
        'phone' => ['string'],
        'company' => ['string']
    ];
    
    /**
     * Find user by email with validation
     */
    public function findByEmail(string $email): ?array {
        return $this->findBy('email', $email);
    }
    
    /**
     * Find user by username with validation
     */
    public function findByUsername(string $username): ?array {
        return $this->findBy('username', $username);
    }
    
    /**
     * Create user with enhanced validation
     */
    public function createUser(array $data): array {
        // Hash password before validation
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = 'user';
        }
        
        return $this->createWithResponse($data);
    }
    
    /**
     * Update user with enhanced validation
     */
    public function updateUser(int $id, array $data): array {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->updateWithResponse($id, $data);
    }
    
    /**
     * Search users with pagination
     */
    public function searchUsers(array $filters = [], int $page = 1, int $limit = 20): array {
        return $this->search($filters, $page, $limit);
    }
    
    /**
     * Verify user password
     */
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * Generate remember token
     */
    public function generateRememberToken(): string {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Mark email as verified
     */
    public function markEmailAsVerified(int $id): array {
        return $this->updateWithResponse($id, [
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get user statistics
     */
    public function getUserStats(int $id): array {
        try {
            // This would typically join with other tables
            // For now, return basic user info
            $user = $this->find($id);
            
            if (!$user) {
                return [
                    'success' => false,
                    'error' => 'User not found'
                ];
            }
            
            return [
                'success' => true,
                'data' => [
                    'user' => $user,
                    'stats' => [
                        'account_age_days' => round((time() - strtotime($user['created_at'])) / 86400),
                        'is_verified' => !empty($user['email_verified_at']),
                        'last_login' => $user['updated_at']
                    ]
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to get user statistics',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get all users with role filtering
     */
    public function getUsersByRole(string $role, int $page = 1, int $limit = 20): array {
        return $this->search(['role' => $role], $page, $limit);
    }
    
    /**
     * Count users by role
     */
    public function countUsersByRole(string $role): int {
        try {
            $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE role = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$role]);
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (\Exception $e) {
            error_log("Error counting users by role: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Check if email exists
     */
    public function emailExists(string $email, int $excludeId = null): bool {
        try {
            $query = "SELECT id FROM {$this->table} WHERE email = ?";
            $params = [$email];
            
            if ($excludeId) {
                $query .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetch() !== false;
        } catch (\Exception $e) {
            error_log("Error checking email existence: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete user
     */
    public function deleteUser(int $id): array {
        // Enable soft delete for this model
        $this->fillable[] = 'deleted_at';
        return $this->deleteWithResponse($id);
    }
}
