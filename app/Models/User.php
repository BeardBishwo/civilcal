<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class User extends Model
{
    protected $table = 'users';
    
    public $id;
    public $username;
    public $email;
    public $password;
    public $role;
    public $first_name;
    public $last_name;
    public $phone;
    public $company;
    public $country;
    public $timezone;
    public $avatar;
    public $is_active;
    public $email_verified;
    public $last_login;
    public $login_count;
    public $created_at;
    public $updated_at;
    
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    public static function findByUsername($username)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $data = $stmt->fetch();
        
        return $data ? new self($data) : null;
    }
    
    public static function findByEmail($email)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $data = $stmt->fetch();
        
        return $data ? new self($data) : null;
    }
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    public function isEngineer()
    {
        return $this->role === 'engineer' || $this->role === 'admin';
    }
    
    public function getFullName()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
    public function updateProfile($data)
    {
        $allowedFields = ['first_name', 'last_name', 'phone', 'company', 'country', 'timezone'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (!empty($updateData)) {
            $db = Database::getInstance();
            $setClause = implode(' = ?, ', array_keys($updateData)) . ' = ?';
            $values = array_values($updateData);
            $values[] = $this->id;
            
            $stmt = $db->prepare("UPDATE users SET $setClause WHERE id = ?");
            return $stmt->execute($values);
        }
        
        return false;
    }
    
    public function changePassword($currentPassword, $newPassword)
    {
        if (!password_verify($currentPassword, $this->password)) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $result = $stmt->execute([$hashedPassword, $this->id]);
        
        return [
            'success' => $result,
            'message' => $result ? 'Password updated successfully' : 'Failed to update password'
        ];
    }
    
    public static function getAllUsers($filters = [], $page = 1, $perPage = 20)
    {
        $db = Database::getInstance();
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if (!empty($filters['role'])) {
            $whereClause .= " AND role = ?";
            $params[] = $filters['role'];
        }
        
        if (!empty($filters['search'])) {
            $whereClause .= " AND (username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (isset($filters['is_active'])) {
            $whereClause .= " AND is_active = ?";
            $params[] = $filters['is_active'];
        }
        
        // Count total
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM users $whereClause");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get users with pagination
        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare("
            SELECT * FROM users 
            $whereClause 
            ORDER BY created_at DESC 
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        $users = $stmt->fetchAll();
        
        return [
            'users' => array_map(function($user) { return new self($user); }, $users),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
}
?>
