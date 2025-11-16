<?php
namespace App\Core;

/**
 * Enhanced base controller with common patterns, validation, and error handling
 * Provides consistent structure across all controllers in the application
 */
abstract class OptimizedController extends Controller {
    protected array $middleware = [];
    protected array $validationRules = [];
    
    public function __construct() {
        parent::__construct();
        $this->runMiddleware();
    }
    
    /**
     * Run middleware stack
     */
    private function runMiddleware(): void {
        foreach ($this->middleware as $middleware) {
            $this->executeMiddleware($middleware);
        }
    }
    
    /**
     * Execute individual middleware
     */
    private function executeMiddleware(string $middleware): void {
        $middlewareClass = "App\\Middleware\\" . $middleware;
        
        if (class_exists($middlewareClass)) {
            $instance = new $middlewareClass();
            if (method_exists($instance, 'handle')) {
                $instance->handle();
            }
        }
    }
    
    /**
     * Validate request data against rules
     */
    protected function validateRequest(array $rules): array {
        $errors = [];
        
        foreach ($rules as $field => $ruleSet) {
            $value = $_POST[$field] ?? $_GET[$field] ?? null;
            $fieldRules = explode('|', $ruleSet);
            
            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty($value) && $value !== 0 && $value !== '0') {
                    $errors[$field] = "{$field} is required";
                    break;
                }
                
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "{$field} must be a valid email";
                    break;
                }
                
                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field] = "{$field} must be numeric";
                    break;
                }
                
                if ($rule === 'string' && !is_string($value)) {
                    $errors[$field] = "{$field} must be a string";
                    break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Get pagination parameters with validation
     */
    protected function getPaginationParams(): array {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(100, max(10, (int)($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        
        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => $offset
        ];
    }
    
    /**
     * Get JSON response helper
     */
    protected function jsonResponse(mixed $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $status >= 200 && $status < 300,
            'data' => $data,
            'status' => $status
        ]);
        exit;
    }
    
    /**
     * Get error response helper
     */
    protected function errorResponse(string $message, int $status = 400): void {
        $this->jsonResponse(['error' => $message], $status);
    }
    
    /**
     * Get success response helper
     */
    protected function successResponse(mixed $data, string $message = 'Success', int $status = 200): void {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
        
        $this->jsonResponse($response, $status);
    }
    
    /**
     * Get request input helper
     */
    protected function input(string $key, mixed $default = null): mixed {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
    
    /**
     * Get request method helper
     */
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Get request method helper
     */
    protected function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * CSRF token validation helper
     */
    protected function validateCsrfToken(): bool {
        $token = $this->input('csrf_token');
        $sessionToken = $_SESSION['csrf_token'] ?? null;
        
        return $token === $sessionToken && !empty($token);
    }
    
    /**
     * Flash message helper
     */
    protected function flash(string $type, string $message): void {
        $_SESSION['flash_messages'][] = [
            'type' => $type,
            'message' => $message,
            'timestamp' => time()
        ];
    }
    
    /**
     * Get flash messages helper
     */
    protected function getFlashMessages(): array {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
    
    /**
     * Upload file helper with validation
     */
    protected function uploadFile(string $inputName, array $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'], int $maxSize = 5000000): array {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'error' => 'File upload failed',
                'message' => $this->getFileUploadError($_FILES[$inputName]['error'] ?? null)
            ];
        }
        
        $file = $_FILES[$inputName];
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            return [
                'success' => false,
                'error' => 'File too large',
                'message' => 'File size must be less than ' . ($maxSize / 1000000) . 'MB'
            ];
        }
        
        // Validate file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            return [
                'success' => false,
                'error' => 'Invalid file type',
                'message' => 'File must be one of: ' . implode(', ', $allowedTypes)
            ];
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
        $uploadPath = 'uploads/' . date('Y/m') . '/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $fullPath = $uploadPath . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $fullPath,
                'size' => $file['size'],
                'type' => $file['type']
            ];
        } else {
            return [
                'success' => false,
                'error' => 'File move failed',
                'message' => 'Failed to save uploaded file'
            ];
        }
    }
    
    /**
     * Get file upload error message
     */
    private function getFileUploadError(int $errorCode): string {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds maximum upload size',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds maximum form size',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        
        return $errors[$errorCode] ?? 'Unknown upload error';
    }
    
    /**
     * Sanitize input helper
     */
    protected function sanitizeInput(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate unique token helper
     */
    protected function generateToken(int $length = 32): string {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Log action helper
     */
    protected function logAction(string $action, array $data = []): void {
        if (class_exists('\App\Services\Logger')) {
            \App\Services\Logger::info("Controller Action: {$action}", $data);
        }
    }
    
    /**
     * Check permission helper
     */
    protected function hasPermission(string $permission): bool {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }
        
        // Check user permissions (implement based on your permission system)
        return true; // Placeholder - implement actual permission checking
    }
    
    /**
     * Require permission helper
     */
    protected function requirePermission(string $permission): void {
        if (!$this->hasPermission($permission)) {
            $this->errorResponse('Access denied', 403);
        }
    }
    
    /**
     * Get user IP address helper
     */
    protected function getUserIp(): string {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }
    
    /**
     * Get user agent helper
     */
    protected function getUserAgent(): string {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }
    
    /**
     * Rate limiting helper
     */
    protected function checkRateLimit(string $key, int $maxAttempts = 10, int $window = 3600): bool {
        $attemptsKey = "rate_limit_{$key}_attempts";
        $windowKey = "rate_limit_{$key}_window";
        
        $currentTime = time();
        $windowStart = $_SESSION[$windowKey] ?? $currentTime;
        
        // Reset window if expired
        if ($currentTime - $windowStart > $window) {
            $_SESSION[$attemptsKey] = 0;
            $_SESSION[$windowKey] = $currentTime;
        }
        
        // Check if limit exceeded
        $attempts = $_SESSION[$attemptsKey] ?? 0;
        if ($attempts >= $maxAttempts) {
            return false;
        }
        
        // Increment attempts
        $_SESSION[$attemptsKey] = $attempts + 1;
        return true;
    }
}
