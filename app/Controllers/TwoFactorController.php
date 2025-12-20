<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\TwoFactorAuthService;
use App\Models\User;
use Exception;

/**
 * Two-Factor Authentication Controller
 */
class TwoFactorController extends Controller
{
    private $twoFactorService;
    private $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->twoFactorService = new TwoFactorAuthService();
        $this->userModel = new User();
    }
    
    /**
     * Show 2FA setup page
     */
    public function setup()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            $this->redirect('/login');
            return;
        }
        
        // Check if already enabled
        $status = $this->twoFactorService->getStatus($userId);
        
        if ($status && $status['enabled']) {
            $_SESSION['flash_messages']['info'] = '2FA is already enabled';
            $this->redirect('/user/profile');
            return;
        }
        
        // Generate new secret
        $secret = $this->twoFactorService->generateSecret();
        $_SESSION['2fa_setup_secret'] = $secret;
        
        $user = $this->userModel->find($userId);
        $qrCodeUrl = $this->twoFactorService->getQRCodeUrl($user['email'], $secret);
        
        $data = [
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'user' => $user
        ];
        
        $this->view->render('user/2fa-setup', $data);
    }
    
    /**
     * Enable 2FA
     */
    public function enable()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                $this->json(['error' => 'Not authenticated'], 401);
                return;
            }
            
            $secret = $_SESSION['2fa_setup_secret'] ?? null;
            $code = $_POST['code'] ?? '';
            
            if (!$secret) {
                throw new Exception('Invalid setup session');
            }
            
            if (empty($code)) {
                throw new Exception('Verification code is required');
            }
            
            $result = $this->twoFactorService->enable($userId, $secret, $code);
            
            // Clear setup session
            unset($_SESSION['2fa_setup_secret']);
            
            $this->json([
                'success' => true,
                'message' => '2FA enabled successfully',
                'recovery_codes' => $result['recovery_codes']
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Disable 2FA
     */
    public function disable()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                $this->json(['error' => 'Not authenticated'], 401);
                return;
            }
            
            $password = $_POST['password'] ?? '';
            
            if (empty($password)) {
                throw new Exception('Password is required');
            }
            
            $result = $this->twoFactorService->disable($userId, $password);
            
            $this->json([
                'success' => true,
                'message' => '2FA disabled successfully'
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Verify 2FA code during login
     */
    public function verify()
    {
        try {
            $userId = $_SESSION['2fa_user_id'] ?? null;
            $code = $_POST['code'] ?? '';
            $trustDevice = $_POST['trust_device'] ?? false;
            
            if (!$userId) {
                throw new Exception('Invalid session');
            }
            
            if (empty($code)) {
                throw new Exception('Verification code is required');
            }
            
            $user = $this->userModel->find($userId);
            
            if (!$user || !$user['two_factor_enabled']) {
                throw new Exception('2FA not enabled for this user');
            }
            
            // Check if it's a recovery code
            $isRecoveryCode = strlen($code) === 8 && ctype_xdigit($code);
            
            if ($isRecoveryCode) {
                $valid = $this->twoFactorService->verifyRecoveryCode($userId, $code);
            } else {
                $valid = $this->twoFactorService->verifyCode($user['two_factor_secret'], $code);
            }
            
            if (!$valid) {
                throw new Exception('Invalid verification code');
            }
            
            // Complete login
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['2fa_verified'] = true;
            
            // Add trusted device if requested
            if ($trustDevice) {
                $this->twoFactorService->addTrustedDevice($userId, $this->getDeviceName(), 30);
            }
            
            // Clear 2FA session
            unset($_SESSION['2fa_user_id']);
            
            $this->json([
                'success' => true,
                'message' => '2FA verification successful',
                'redirect' => app_base_url('/dashboard')
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Show 2FA verification page
     */
    public function showVerify()
    {
        $userId = $_SESSION['2fa_user_id'] ?? null;
        
        if (!$userId) {
            $this->redirect('/login');
            return;
        }
        
        $this->view->render('auth/2fa-verify', []);
    }
    
    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                $this->json(['error' => 'Not authenticated'], 401);
                return;
            }
            
            $password = $_POST['password'] ?? '';
            
            if (empty($password)) {
                throw new Exception('Password is required');
            }
            
            $newCodes = $this->twoFactorService->regenerateRecoveryCodes($userId, $password);
            
            $this->json([
                'success' => true,
                'message' => 'Recovery codes regenerated',
                'recovery_codes' => $newCodes
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Get trusted devices
     */
    public function getTrustedDevices()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                $this->json(['error' => 'Not authenticated'], 401);
                return;
            }
            
            $devices = $this->twoFactorService->getTrustedDevices($userId);
            
            $this->json([
                'success' => true,
                'devices' => $devices
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Revoke trusted device
     */
    public function revokeTrustedDevice()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                $this->json(['error' => 'Not authenticated'], 401);
                return;
            }
            
            $deviceId = $_POST['device_id'] ?? null;
            
            if (!$deviceId) {
                throw new Exception('Device ID is required');
            }
            
            $this->twoFactorService->revokeTrustedDevice($userId, $deviceId);
            
            $this->json([
                'success' => true,
                'message' => 'Device revoked successfully'
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Get device name from user agent
     */
    private function getDeviceName()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Device';
        
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows PC';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'Mac';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux PC';
        } elseif (strpos($userAgent, 'iPhone') !== false) {
            return 'iPhone';
        } elseif (strpos($userAgent, 'iPad') !== false) {
            return 'iPad';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android Device';
        }
        
        return 'Unknown Device';
    }
}
