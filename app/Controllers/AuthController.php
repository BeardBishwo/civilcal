<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;
use App\Services\SecurityNotificationService;
use App\Services\SecurityAlertService;
use App\Services\GeolocationService;
use Exception;

class AuthController extends Controller
{
    public function showLogin()
    {
        $this->view->render('auth/login');
    }

    public function showRegister()
    {
        if (\App\Services\SettingsService::get('allow_registration', '1') !== '1') {
            header('Location: ' . app_base_url('/login?error=registration_disabled'));
            exit;
        }
        $this->view->render('auth/register');
    }

    public function showForgotPassword()
    {
        $this->view->render('auth/forgot');
    }

    public function logout()
    {
        try {
            \App\Services\Security::startSession();

            // Unset all of the session variables.
            $_SESSION = array();

            // If it's desired to kill the session, also delete the session cookie.
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            // Finally, destroy the session.
            session_destroy();

            // Clear auth cookies
            if (isset($_COOKIE['auth_token'])) {
                setcookie('auth_token', '', time() - 3600, '/');
            }
            if (isset($_COOKIE['remember_token'])) {
                setcookie('remember_token', '', time() - 3600, '/');
            }

            // Redirect to homepage
            header('Location: ' . app_base_url());
            exit;
        } catch (Exception $e) {
            echo "Logout Error: " . $e->getMessage();
            exit;
        }
    }

    public function login()
    {
        $isJson = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;

        if ($isJson) {
            // CSRF Check for JSON
            if (!\App\Services\Security::validateCsrfToken()) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
                exit;
            }
            $input = json_decode(file_get_contents('php://input'), true);
            $username = $input['username_email'] ?? $input['username'] ?? '';
            $password = $input['password'] ?? '';
            $rememberMe = !empty($input['remember_me']);
            $captchaResponse = $input['g-recaptcha-response'] ?? $input['h-captcha-response'] ?? $input['cf-turnstile-response'] ?? '';
        } else {
            // CSRF Check for Form
            if (!\App\Services\Security::validateCsrfToken()) {
                throw new Exception('Invalid CSRF token. Please refresh and try again.');
            }
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $rememberMe = isset($_POST['remember_me']);
            $captchaResponse = $_POST['g-recaptcha-response'] ?? $_POST['h-captcha-response'] ?? $_POST['cf-turnstile-response'] ?? '';
        }

        try {
            // Verify Captcha
            if (\App\Services\SettingsService::get('captcha_on_login') == '1') {
                $recaptchaService = new \App\Services\RecaptchaService();
                if (!$recaptchaService->verify($captchaResponse, $_SERVER['REMOTE_ADDR'])) {
                    throw new Exception('Captcha verification failed. Please try again.');
                }
            }

            if (empty($username) || empty($password)) {
                throw new Exception('Please provide both username and password.');
            }

            // Find user by username or email
            $user = User::findByUsername($username);

            if ($user) {
                // Check for account lockout
                if (!empty($user->lockout_until) && strtotime($user->lockout_until) > time()) {
                    $waitMinutes = ceil((strtotime($user->lockout_until) - time()) / 60);
                    throw new Exception("Account is temporarily locked due to too many failed login attempts. Please try again in {$waitMinutes} minutes.");
                }
                // Check IP Restrictions
                $ipService = new \App\Services\IPRestrictionService();
                $ipCheck = $ipService->checkIP($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

                if (!$ipCheck['allowed']) {
                    // Log attempted login from blocked IP?
                    error_log("Blocked login attempt from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . " Reason: " . $ipCheck['reason']);
                    throw new Exception('Access denied: ' . $ipCheck['reason']);
                }

                if (password_verify($password, $user->password)) {
                    // SEC: Prevent session fixation by regenerating session ID before setting data
                    session_regenerate_id(true);
                    // Reset failed logins and log session with geolocation
                    $userModel = new User();

                    // Get geolocation data
                    $geoService = new GeolocationService();
                    $locationData = $geoService->getLocationDetails();

                    // Prepare device info
                    $deviceInfo = [
                        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
                    ];

                    // Prepare location info
                    $locationInfo = [
                        'country' => $locationData['country'] ?? null,
                        'region' => $locationData['region'] ?? null,
                        'city' => $locationData['city'] ?? null,
                        'timezone' => $locationData['timezone'] ?? null
                    ];

                    $userModel->updateLastLogin($user->id, $deviceInfo, $locationInfo);

                    // Check if 2FA is required
                    $global2fa = \App\Services\SettingsService::get('enable_2fa', '0') === '1';
                    if ($global2fa && !empty($user->two_factor_enabled) && !empty($user->two_factor_secret)) {
                        // Start 2FA session
                        $_SESSION['2fa_pending_user_id'] = $user->id;
                        $_SESSION['2fa_pending_remember'] = $rememberMe;

                        if ($isJson) {
                            echo json_encode(['success' => true, 'redirect' => app_base_url('/login/2fa')]);
                            return;
                        }
                        header('Location: ' . app_base_url('/login/2fa'));
                        exit;
                    }

                    // Check for new location and send alert
                    $securityAlertService = new \App\Services\SecurityAlertService();
                    $securityAlertService->checkNewLocation($user->id, $locationData);

                    // Analyze for suspicious login patterns
                    $suspiciousDetector = new \App\Services\SuspiciousActivityDetector();
                    $analysis = $suspiciousDetector->analyzeLogin($user->id, $locationData, $deviceInfo);

                    // Log suspicious activity (optional: could block login if high risk)
                    if ($analysis['suspicious'] && $analysis['risk_level'] === 'high') {
                        error_log("High-risk login detected for user {$user->id}: " . json_encode($analysis['alerts']));
                    }

                    $this->createUserSession($user, $rememberMe, $isJson);
                    return;
                } else {
                    // Increment failed logins
                    $userModel = new User();
                    $maxAttempts = (int)\App\Services\SettingsService::get('max_login_attempts', '5');
                    $lockoutDuration = (int)\App\Services\SettingsService::get('lockout_duration', '30'); // in minutes
                    $userModel->incrementFailedLogins($user->id, $maxAttempts, $lockoutDuration * 60);

                    throw new Exception('Invalid credentials.');
                }
            } else {
                throw new Exception('Invalid credentials.');
            }
        } catch (Exception $e) {
            if (isset($isJson) && $isJson) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
            $this->view->render('auth/login', ['error' => $e->getMessage(), 'username' => $username]);
        }
    }

    /**
     * Show 2FA Verification Page
     */
    public function show2FA()
    {
        if (!isset($_SESSION['2fa_pending_user_id'])) {
            header('Location: ' . app_base_url('/login'));
            exit;
        }

        $this->view->render('auth/2fa-verify');
    }

    /**
     * Verify 2FA Code
     */
    public function verify2FA()
    {
        try {
            if (!isset($_SESSION['2fa_pending_user_id'])) {
                throw new Exception('Login session expired.');
            }

            $userId = $_SESSION['2fa_pending_user_id'];
            $rememberMe = $_SESSION['2fa_pending_remember'] ?? false;
            $code = $_POST['code'] ?? '';

            if (empty($code)) {
                throw new Exception('Verification code is required.');
            }

            $userModel = new User();
            $user = $userModel->findById($userId);

            if (!$user || empty($user->two_factor_secret)) {
                throw new Exception('Two-factor authentication not configured.');
            }

            // Check if Google2FA class exists
            if (!class_exists('\\PragmaRX\\Google2FA\\Google2FA')) {
                throw new Exception('2FA Service unavailable.');
            }

            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            $valid = $google2fa->verifyKey($user->two_factor_secret, $code);

            // Check recovery codes if TOTP fails
            if (!$valid && !empty($user->two_factor_recovery_codes)) {
                $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
                if (is_array($recoveryCodes) && in_array($code, $recoveryCodes)) {
                    $valid = true;
                    // Remove used recovery code
                    $recoveryCodes = array_diff($recoveryCodes, [$code]);
                    // Update user (we need a method for this or use direct update)
                    $this->updateRecoveryCodes($userId, $recoveryCodes);
                }
            }

            if ($valid) {
                // Clear 2FA session
                unset($_SESSION['2fa_pending_user_id']);
                unset($_SESSION['2fa_pending_remember']);

                // Complete login
                $this->createUserSession($user, $rememberMe);
            } else {
                throw new Exception('Invalid verification code.');
            }
        } catch (Exception $e) {
            $this->view->render('auth/2fa-verify', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Helper to update recovery codes
     */
    private function updateRecoveryCodes($userId, $codes)
    {
        $db = Database::getInstance();
        $stmt = $db->getPdo()->prepare("UPDATE users SET two_factor_recovery_codes = ? WHERE id = ?");
        $stmt->execute([json_encode(array_values($codes)), $userId]);
    }

    public function loginWithGoogle()
    {
        try {
            if (\App\Services\SettingsService::get('google_login_enabled') != '1') {
                throw new Exception('Google login is disabled.');
            }

            $googleService = new \App\Services\GoogleAuthService();
            header('Location: ' . $googleService->getAuthUrl());
            exit;
        } catch (Exception $e) {
            // Redirect back with error
            $this->view->render('auth/login', ['error' => $e->getMessage()]);
        }
    }

    public function handleGoogleCallback()
    {
        try {
            $code = $_GET['code'] ?? '';
            if (empty($code)) {
                throw new Exception('Invalid request.');
            }

            $googleService = new \App\Services\GoogleAuthService();
            $googleUser = $googleService->getUserFromCode($code);

            // Check if user exists by email
            $userModel = new User();
            $email = $googleUser['email'];
            $existingUser = $userModel->findByEmail($email);

            if ($existingUser) {
                // Check if 2FA is required for this existing user
                $global2fa = \App\Services\SettingsService::get('enable_2fa', '0') === '1';
                if ($global2fa && !empty($existingUser->two_factor_enabled) && !empty($existingUser->two_factor_secret)) {
                    // Start 2FA session
                    $_SESSION['2fa_pending_user_id'] = $existingUser->id;
                    $_SESSION['2fa_pending_remember'] = false; // Default for social login

                    header('Location: ' . app_base_url('/login/2fa'));
                    exit;
                }

                // Log them in
                $this->createUserSession($existingUser);
            } else {
                // Intercept for interactive username selection
                $_SESSION['pending_google_user'] = [
                    'email' => $email,
                    'first_name' => $googleUser['given_name'] ?? '',
                    'last_name' => $googleUser['family_name'] ?? '',
                    'picture' => $googleUser['picture'] ?? null
                ];

                header('Location: ' . app_base_url('/user/login/google/confirm'));
                exit;
            }
        } catch (Exception $e) {
            $this->view->render('auth/login', ['error' => $e->getMessage()]);
        }
    }

    private function createUserSession($user, $rememberMe = false, $isJson = false)
    {
        $user = (array) $user;

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user'] = $user;
        $_SESSION['is_admin'] = $user['is_admin'] ?? false;

        // Attempt installer cleanup if this is an admin
        if (class_exists('\App\Services\InstallerService')) {
            \App\Services\InstallerService::attemptCleanup($user);
        }

        // Security Notification
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $securityService = new SecurityNotificationService();
        $securityService->checkAndNotifyNewLogin($user['id'], $ipAddress);

        // Database Session & Auth Token Cookie
        $db = Database::getInstance();
        $pdo = $db->getPdo();
        $sessionToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        $stmt = $pdo->prepare("
            INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user['id'],
            $sessionToken,
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $expiresAt
        ]);

        // Set auth_token cookie
        setcookie('auth_token', $sessionToken, [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        // Remember Me
        if ($rememberMe) {
            $rememberToken = bin2hex(random_bytes(32));
            setcookie('remember_token', $rememberToken, [
                'expires' => time() + (30 * 24 * 60 * 60),
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }

        // Redirect based on role
        $isAdmin = !empty($user['is_admin']) || ($user['role'] === 'admin');
        $redirectUrl = $isAdmin ? app_base_url('/admin') : app_base_url('/dashboard');

        if ($isJson) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect_url' => $redirectUrl]);
            exit;
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    public function register()
    {
        if (\App\Services\SettingsService::get('allow_registration', '1') !== '1') {
            throw new Exception('Registration is currently disabled.');
        }

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';

        try {
            // CSRF Check
            if (!\App\Services\Security::validateCsrfToken()) {
                throw new Exception('Invalid CSRF token. Please refresh and try again.');
            }
            // Verify Captcha
            if (\App\Services\SettingsService::get('captcha_on_register') == '1') {
                $recaptchaService = new \App\Services\RecaptchaService();
                $captchaResponse = $_POST['g-recaptcha-response'] ?? $_POST['h-captcha-response'] ?? $_POST['cf-turnstile-response'] ?? '';
                if (!$recaptchaService->verify($captchaResponse, $_SERVER['REMOTE_ADDR'])) {
                    throw new Exception('Captcha verification failed. Please try again.');
                }
            }

            // Basic validation
            if (empty($username) || empty($email) || empty($password)) {
                throw new Exception('All fields are required.');
            }

            $passwordValidation = \App\Services\Security::validatePassword($password);
            if (!$passwordValidation['valid']) {
                throw new Exception($passwordValidation['error']);
            }

            $userModel = new User();

            // Check existence
            if ($userModel->findByUsername($username)) {
                throw new Exception('Username already taken.');
            }

            $userId = $userModel->create([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'role' => 'user'
            ]);

            if ($userId) {
                header('Location: ' . app_base_url('/login?registered=1'));
                exit;
            } else {
                throw new Exception('Registration failed.');
            }
        } catch (Exception $e) {
            $this->view->render('auth/register', [
                'error' => $e->getMessage(),
                'username' => $username,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName
            ]);
        }
    }

    public function forgotPassword()
    {
        $this->view->render('auth/forgot');
    }

    /**
     * Show Google Registration Confirmation Page
     */
    public function showGoogleConfirm()
    {
        if (!isset($_SESSION['pending_google_user'])) {
            header('Location: ' . app_base_url('/login'));
            exit;
        }

        $googleData = $_SESSION['pending_google_user'];
        $baseUsername = explode('@', $googleData['email'])[0];

        // Generate 3 unique suggestions
        $suggestions = [];
        $userModel = new User();

        for ($i = 0; $i < 3; $i++) {
            $candidate = $baseUsername;
            if ($i > 0) $candidate .= rand(10, 99);

            while ($userModel->findByUsername($candidate) || in_array($candidate, $suggestions)) {
                $candidate = $baseUsername . rand(10, 999);
            }
            $suggestions[] = $candidate;
        }

        $this->view->render('auth/google-confirm', [
            'email' => $googleData['email'],
            'first_name' => $googleData['first_name'],
            'last_name' => $googleData['last_name'],
            'picture' => $googleData['picture'],
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Process Google Registration Final Step
     */
    public function processGoogleConfirm()
    {
        try {
            if (!isset($_SESSION['pending_google_user'])) {
                throw new Exception('Google session expired. Please try again.');
            }

            $username = trim($_POST['username'] ?? '');
            $googleData = $_SESSION['pending_google_user'];

            if (empty($username)) {
                throw new Exception('Username is required.');
            }

            if (!preg_match('/^[a-zA-Z0-9._]{3,20}$/', $username)) {
                throw new Exception('Username must be 3-20 characters (letters, numbers, dot, underscore only).');
            }

            $userModel = new User();
            if ($userModel->findByUsername($username)) {
                throw new Exception('That username is already taken. Please try another one.');
            }

            // Create the user
            $tempPassword = bin2hex(random_bytes(8));
            $userId = $userModel->create([
                'username' => $username,
                'email' => $googleData['email'],
                'password' => password_hash($tempPassword, PASSWORD_DEFAULT),
                'first_name' => $googleData['first_name'],
                'last_name' => $googleData['last_name'],
                'role' => 'user',
                'is_verified' => 1
            ]);

            if ($userId) {
                $newUser = $userModel->findById($userId);
                unset($_SESSION['pending_google_user']);
                $this->createUserSession($newUser);
            } else {
                throw new Exception('Failed to create account.');
            }
        } catch (Exception $e) {
            $this->view->render('auth/google-confirm', [
                'error' => $e->getMessage(),
                'email' => $googleData['email'] ?? '',
                'first_name' => $googleData['first_name'] ?? '',
                'last_name' => $googleData['last_name'] ?? '',
                'picture' => $googleData['picture'] ?? null,
                'suggestions' => $_POST['cached_suggestions'] ?? []
            ]);
        }
    }

    /**
     * Check username availability via AJAX
     */
    public function checkUsernameAvailability()
    {
        // Support both GET and POST for flexibility
        $username = trim($_REQUEST['username'] ?? '');

        if (empty($username)) {
            $this->json(['available' => false, 'error' => 'Empty username']);
            return;
        }

        $userModel = new User();
        $isTaken = (bool)$userModel->findByUsername($username);

        $this->json([
            'available' => !$isTaken,
            'message' => $isTaken ? 'Username is already taken' : 'Username is available'
        ]);
    }
}
