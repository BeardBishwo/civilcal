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
        
        $this->view('user/profile', $data);
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
        
        $this->view('user/edit-profile', $data);
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
        
        $this->view('user/change-password', $data);
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
}
?>
