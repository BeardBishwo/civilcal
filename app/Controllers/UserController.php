<?php
namespace App\Controllers;

use App\Core\Controller;

class UserController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Show user profile
     */
    public function profile() {
        $this->requireAuth();
        $this->setCategory('user');
        $this->setTitle('Profile - Bishwo Calculator');
        $this->setDescription('View and edit your Bishwo Calculator profile');
        
        $data = [
            'title' => 'Your Profile',
            'user' => $this->getUser(),
            'subtitle' => 'Manage your account settings'
        ];
        
        $this->view->render('user/profile', $data);
    }
    
    /**
     * Edit user profile
     */
    public function editProfile() {
        $this->requireAuth();
        $this->setCategory('user');
        $this->setTitle('Edit Profile - Bishwo Calculator');
        $this->setDescription('Edit your Bishwo Calculator profile information');
        
        $data = [
            'title' => 'Edit Profile',
            'user' => $this->getUser(),
            'subtitle' => 'Update your account information'
        ];
        
        $this->view->render('user/edit-profile', $data);
    }
    
    /**
     * Update user profile
     */
    public function updateProfile() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            
            // Basic validation
            if (empty($name) || empty($email)) {
                $this->json(['error' => 'Name and email are required'], 400);
                return;
            }
            
            // Update session data
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            
            $this->json(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            $this->json(['error' => 'Invalid request method'], 405);
        }
    }
    
    /**
     * Change password
     */
    public function changePassword() {
        $this->requireAuth();
        $this->setCategory('user');
        $this->setTitle('Change Password - Bishwo Calculator');
        $this->setDescription('Change your Bishwo Calculator account password');
        
        $data = [
            'title' => 'Change Password',
            'subtitle' => 'Update your account security'
        ];
        
        $this->view->render('user/change-password', $data);
    }
    
    /**
     * Update password
     */
    public function updatePassword() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
            $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            
            // Basic validation
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $this->json(['error' => 'All password fields are required'], 400);
                return;
            }
            
            if ($newPassword !== $confirmPassword) {
                $this->json(['error' => 'New passwords do not match'], 400);
                return;
            }
            
            if (strlen($newPassword) < 6) {
                $this->json(['error' => 'New password must be at least 6 characters'], 400);
                return;
            }
            
            // Placeholder password change logic
            // TODO: Implement proper password hashing and validation
            $this->json(['success' => true, 'message' => 'Password updated successfully']);
        } else {
            $this->json(['error' => 'Invalid request method'], 405);
        }
    }

    /**
     * Update Identity (Equip Avatar/Frame)
     */
    public function updateIdentity() {
        $this->requireAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            exit;
        }

        $type = $_POST['type'] ?? ''; // 'avatar' or 'frame'
        $key = $_POST['key'] ?? '';
        $userId = $this->getUser()['id'];

        if (!in_array($type, ['avatar', 'frame'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid item type']);
            exit;
        }

        // Logic: Check if owned (unless it's 'default' frame)
        if ($key === 'default' && $type === 'frame') {
            // Allow un-equipping frame
        } else {
            // Check Wardrobe
            $db = \App\Core\Database::getInstance();
            $owned = $db->query(
                "SELECT id FROM user_wardrobe WHERE user_id = ? AND item_key = ?", 
                [$userId, $key]
            )->fetch();

            // Check if it's a "Starter" avatar (which are always free)
            $isStarter = strpos($key, 'avatar_starter_') === 0;

            if (!$owned && !$isStarter) {
                echo json_encode(['success' => false, 'message' => 'You do not own this item.']);
                exit;
            }
        }

        // Update User Profile
        $col = ($type === 'avatar') ? 'avatar_id' : 'frame_id';
        $val = ($key === 'default') ? NULL : $key;

        $db = \App\Core\Database::getInstance();
        $db->query("UPDATE users SET $col = ? WHERE id = ?", [$val, $userId]);

        // Update Session
        $_SESSION['user'][$col] = $val;

        echo json_encode(['success' => true, 'message' => 'Identity updated!']);
        exit;
    }

    /**
     * Buy Identity Item (Frame)
     */
    public function buyIdentityItem() {
        $this->requireAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            exit;
        }

        $key = $_POST['key'] ?? '';
        $userId = $this->getUser()['id'];

        // Define Prices (Should ideally be in DB or Config)
        $prices = [
            'frame_hazard' => 50,
            'frame_blueprint' => 200,
            'frame_gold' => 1000
        ];

        if (!isset($prices[$key])) {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
            exit;
        }

        $price = $prices[$key];
        $db = \App\Core\Database::getInstance();

        // Check Balance
        $user = $db->findOne('users', ['id' => $userId]); // Re-fetch for fresh balance
        $coins = $user['coins'] ?? 0;

        if ($coins < $price) {
            echo json_encode(['success' => false, 'message' => 'Insufficient coins']);
            exit;
        }

        // Check ownership
        $owned = $db->query(
            "SELECT id FROM user_wardrobe WHERE user_id = ? AND item_key = ?", 
            [$userId, $key]
        )->fetch();

        if ($owned) {
            echo json_encode(['success' => false, 'message' => 'You already own this item']);
            exit;
        }

        // Transaction
        try {
            $db->beginTransaction();

            // Deduct Coins
            $db->query("UPDATE user_resources SET coins = coins - ? WHERE user_id = ?", [$price, $userId]);

            // Add Item
            $db->query("INSERT INTO user_wardrobe (user_id, item_type, item_key) VALUES (?, 'frame', ?)", [$userId, $key]);

            // Log it
            $db->query(
                "INSERT INTO user_resource_logs (user_id, resource_type, amount, source) VALUES (?, 'coins', ?, 'shop_purchase')", 
                [$userId, -$price]
            );

            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Item purchased!']);
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
        }
        exit;
    }
}
