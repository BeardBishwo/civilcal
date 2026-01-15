<?php

namespace App\Services;

use Exception;
use App\Services\GDPRService;

/**
 * File Service
 * Unified, secure file upload and management service
 * Handles images, documents, and themes with robust validation and security
 */
class FileService
{
    // Storage configuration
    private const STORAGE_BASE = BASE_PATH . '/storage/uploads';
    private const PUBLIC_BASE = BASE_PATH . '/public/assets';

    // Upload directories
    private const UPLOAD_PATHS = [
        'admin' => [
            'logo' => self::STORAGE_BASE . '/admin/logos',
            'favicon' => self::PUBLIC_BASE . '/icons',
            'banner' => self::STORAGE_BASE . '/admin/banners',
            'document' => self::STORAGE_BASE . '/admin/documents',
            'import' => self::STORAGE_BASE . '/admin/imports',
            'plugin' => self::STORAGE_BASE . '/admin/plugins',
            'marketplace' => self::STORAGE_BASE . '/admin/marketplace',
        ],
        'user' => [
            'profile' => self::STORAGE_BASE . '/users',
            'avatar' => self::STORAGE_BASE . '/users',
            'document' => self::STORAGE_BASE . '/users/documents',
            'library' => self::STORAGE_BASE . '/library/quarantine',
            'preview' => self::STORAGE_BASE . '/library/previews',
            'bounty' => self::STORAGE_BASE . '/bounty/quarantine',
            'report' => self::STORAGE_BASE . '/reports',
        ],
        'theme' => BASE_PATH . '/themes', // Special path for themes
        'media' => self::STORAGE_BASE . '/admin/media',
        'temp' => self::STORAGE_BASE . '/temp',
    ];

    // Testing flag to bypass is_uploaded_file check for CLI tests
    private static $testing = false;

    public static function setTesting(bool $testing): void
    {
        self::$testing = $testing;
    }

    // Public URL paths
    private const PUBLIC_URLS = [
        'admin' => [
            'logo' => '/storage/uploads/admin/logos',
            'favicon' => '/assets/icons',
            'banner' => '/storage/uploads/admin/banners',
            'document' => '/storage/uploads/admin/documents',
            'media' => '/storage/uploads/admin/media',
        ],
        'user' => [
            'profile' => '/storage/uploads/users',
            'avatar' => '/storage/uploads/users',
            'document' => '/storage/uploads/users/documents',
        ],
    ];

    // File configurations
    private const FILE_CONFIGS = [
        'logo' => [
            'max_size' => 5242880, // 5MB
            'allowed_types' => ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'],
            'extensions' => ['png', 'jpg', 'jpeg', 'svg', 'webp'],
            'dimensions' => ['max_width' => 500, 'max_height' => 200],
            'optimize' => true,
        ],
        'favicon' => [
            'max_size' => 1048576, // 1MB
            'allowed_types' => ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/jpeg'],
            'extensions' => ['ico', 'png', 'jpg', 'jpeg'],
            'dimensions' => ['max_width' => 512, 'max_height' => 512],
            'optimize' => true,
        ],
        'banner' => [
            'max_size' => 10485760, // 10MB
            'allowed_types' => ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'],
            'extensions' => ['png', 'jpg', 'jpeg', 'webp'],
            'dimensions' => ['max_width' => 2560, 'max_height' => 800],
            'optimize' => true,
        ],
        'profile' => [
            'max_size' => 2097152, // 2MB
            'allowed_types' => ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'],
            'extensions' => ['png', 'jpg', 'jpeg', 'webp'],
            'dimensions' => ['max_width' => 400, 'max_height' => 400],
            'optimize' => true,
        ],
        'avatar' => [
            'max_size' => 2097152, // 2MB
            'allowed_types' => ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'],
            'extensions' => ['png', 'jpg', 'jpeg', 'webp'],
            'dimensions' => ['max_width' => 400, 'max_height' => 400],
            'optimize' => true,
        ],
        'document' => [
            'max_size' => 10485760, // 10MB
            'allowed_types' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'],
            'extensions' => ['pdf', 'doc', 'docx', 'txt'],
            'optimize' => false,
        ],
        'theme' => [
            'max_size' => 52428800, // 50MB
            'allowed_types' => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/octet-stream'],
            'extensions' => ['zip'],
            'optimize' => false,
        ],
        'media' => [
            'max_size' => 20971520, // 20MB
            'allowed_types' => [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/zip',
                'text/plain'
            ],
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'txt'],
            'optimize' => true,
        ],
        'plugin' => [
            'max_size' => 52428800, // 50MB
            'allowed_types' => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/octet-stream'],
            'extensions' => ['zip'],
            'optimize' => false,
        ],
        'marketplace' => [
            'max_size' => 52428800, // 50MB
            'allowed_types' => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/octet-stream'],
            'extensions' => ['zip'],
            'optimize' => false,
        ],
        'question_import' => [
            'max_size' => 10485760, // 10MB
            'allowed_types' => [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
                'text/csv',
                'text/plain',
                'application/octet-stream'
            ],
            'extensions' => ['xlsx', 'xls', 'csv', 'txt'],
            'optimize' => false,
        ],
        'library_file' => [
            'max_size' => 15728640, // 15MB
            'allowed_types' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/octet-stream',
                'application/zip',
                'application/x-rar-compressed',
                'image/jpeg',
                'image/png',
                'image/webp'
            ],
            'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'xlsm', 'dwg', 'dxf', 'sldprt', 'sldasm', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'rar'],
            'optimize' => false,
        ],
        'library_preview' => [
            'max_size' => 5242880, // 5MB
            'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
            'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
            'optimize' => true,
        ],
        'bounty_file' => [
            'max_size' => 20971520, // 20MB
            'allowed_types' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/octet-stream',
                'application/zip',
                'application/x-rar-compressed',
                'image/jpeg',
                'image/png'
            ],
            'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'dwg', 'dxf', 'zip', 'rar', 'jpg', 'png'],
            'optimize' => false,
        ],
        'bounty_preview' => [
            'max_size' => 5242880, // 5MB
            'allowed_types' => ['image/jpeg', 'image/png'],
            'extensions' => ['jpg', 'png'],
            'optimize' => true,
        ],
        'report_screenshot' => [
            'max_size' => 5242880, // 5MB
            'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
            'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
            'optimize' => true,
        ]
    ];

    /**
     * Initialize storage directories
     */
    public static function initializeDirectories(): array
    {
        $results = [];
        $allPaths = [];

        foreach (self::UPLOAD_PATHS as $key => $paths) {
            if (is_array($paths)) {
                foreach ($paths as $path) {
                    $allPaths[] = $path;
                }
            } else {
                $allPaths[] = $paths;
            }
        }

        foreach ($allPaths as $path) {
            $created = self::createDirectory($path);
            $results[$path] = $created;
        }

        return $results;
    }

    /**
     * Create directory with security (index.php, .htaccess)
     */
    public static function createDirectory(string $path): bool
    {
        try {
            if (!is_dir($path)) {
                if (!mkdir($path, 0755, true)) {
                    return false;
                }
            }

            // Create .htaccess for security (Ruthless Security Protocol)
            $htaccess = $path . '/.htaccess';
            if (!file_exists($htaccess)) {
                $content = "# Kills PHP execution in this folder\n";
                $content .= "<FilesMatch \"\\.(php|php5|phtml|php7|phar|exe|sh|bat|pl|py|cgi|asp|aspx|js)$\">\n";
                $content .= "    Order Deny,Allow\n";
                $content .= "    Deny from all\n";
                $content .= "</FilesMatch>\n\n";
                $content .= "# Prevents listing files (Directory Traversal)\n";
                $content .= "Options -Indexes -ExecCGI\n\n";
                $content .= "# Remove handlers\n";
                $content .= "RemoveHandler .php .phtml .php3 .php4 .php5 .php7 .phps .phar .pl .py .cgi\n";
                file_put_contents($htaccess, $content);
            }

            // Create index.php
            $index = $path . '/index.php';
            if (!file_exists($index)) {
                file_put_contents($index, "<?php\n// Silence is golden\n");
            }

            return true;
        } catch (Exception $e) {
            error_log("Directory creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload admin image/file
     */
    public static function uploadAdminFile(array $file, string $type): array
    {
        $uploadPath = self::UPLOAD_PATHS['admin'][$type] ?? null;
        if (!$uploadPath) {
            return ['success' => false, 'error' => 'Upload path not configured'];
        }

        $result = self::processUpload($file, $type, $uploadPath);

        if ($result['success'] && in_array($type, ['logo', 'favicon'])) {
            $publicUrl = (self::PUBLIC_URLS['admin'][$type] ?? '') . '/' . $result['filename'];
            $result['url'] = $publicUrl;
            self::deleteOldFiles($uploadPath, $type, $result['filename']);
        }

        return $result;
    }

    public static function uploadAdminImage(array $file, string $type): array
    {
        return self::uploadAdminFile($file, $type);
    }

    /**
     * Upload user file (avatar, profile, document)
     */
    public static function uploadUserFile(array $file, int $userId, string $type = 'profile'): array
    {
        $userPath = (self::UPLOAD_PATHS['user'][$type] ?? self::UPLOAD_PATHS['user']['profile']) . '/' . $userId;

        $result = self::processUpload($file, $type, $userPath);

        if ($result['success']) {
            $publicBase = self::PUBLIC_URLS['user'][$type] ?? self::PUBLIC_URLS['user']['profile'];
            $result['url'] = $publicBase . '/' . $userId . '/' . $result['filename'];

            // Clean up old profile/avatar images
            if (in_array($type, ['profile', 'avatar'])) {
                self::deleteOldFiles($userPath, $type, $result['filename']);
            }
        }

        return $result;
    }

    public static function uploadUserImage(array $file, int $userId): array
    {
        return self::uploadUserFile($file, $userId, 'profile');
    }

    /**
     * Upload and Extract Theme
     */
    public static function uploadTheme(array $file): array
    {
        $tempPath = self::UPLOAD_PATHS['temp'];
        $result = self::processUpload($file, 'theme', $tempPath, 'theme_pkg');

        if ($result['success']) {
            // Theme specific logic: we return the path for extraction
            return $result;
        }

        return $result;
    }

    /**
     * Extract Theme Zip
     */
    public static function extractTheme(string $zipPath, string $extractTo = null): array
    {
        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'message' => 'ZipArchive extension is not available'];
        }

        if ($extractTo === null) {
            $extractTo = self::UPLOAD_PATHS['theme'];
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            if (!is_dir($extractTo)) {
                mkdir($extractTo, 0755, true);
            }

            $zip->extractTo($extractTo);
            $zip->close();

            // Find theme folder
            $themeFolder = self::findThemeFolder($extractTo);
            if ($themeFolder) {
                return [
                    'success' => true,
                    'theme_folder' => $themeFolder,
                    'extract_path' => $extractTo,
                    'message' => 'Theme extracted successfully'
                ];
            }

            return ['success' => false, 'message' => 'No theme.json found in extracted files'];
        }

        return ['success' => false, 'message' => 'Failed to open ZIP file'];
    }

    private static function findThemeFolder($path)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'theme.json') {
                return $file->getPath();
            }
        }
        return null;
    }

    /**
     * Standardized Upload Processor (The Mother of All Uploads)
     */
    public static function processUpload(array $file, string $type, string $targetDir, string $prefix = ''): array
    {
        try {
            // 1. Validation pipeline
            $validation = self::validateUpload($file, $type);
            if (!$validation['success']) {
                self::logSecurityAlert($file['name'] ?? 'unknown', $validation['error'], $type);
                return $validation;
            }

            // 2. Directory preparation
            if (!self::createDirectory($targetDir)) {
                return ['success' => false, 'error' => 'Failed to prepare storage directory'];
            }

            // 3. Secure filename generation
            $filename = self::generateSecureFilename($prefix ?: $type, $file['name']);
            $filepath = rtrim($targetDir, '/') . '/' . $filename;

            // 4. Secure Move
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'error' => 'Critical: File transfer failed'];
            }

            // 5. Hardened Permissions
            @chmod($filepath, 0644);

            // 6. Image Sanitization (Strip EXIF metadata to prevent code injection)
            $config = self::FILE_CONFIGS[$type] ?? [];
            if (!empty($config['optimize']) || in_array($type, ['profile', 'avatar', 'logo', 'banner', 'favicon', 'library_preview', 'bounty_preview', 'report_screenshot'])) {
                $mimeType = mime_content_type($filepath);
                if (strpos($mimeType, 'image/') === 0) {
                    if (!self::sanitizeImage($filepath, $mimeType)) {
                        @unlink($filepath); // Delete if sanitization fails
                        self::logSecurityAlert($file['name'], 'Image sanitization failed', $type);
                        return ['success' => false, 'error' => 'Image processing failed for security reasons'];
                    }
                }
            }

            // 7. Post-processing (Additional Optimization)
            // Note: Basic sanitization already done above
            if (!empty($config['optimize'])) {
                self::optimizeImage($filepath, $type);
            }

            // 8. Audit Logging (Success)
            self::logUploadSuccess($file['name'], $filename, $type);

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filepath,
                'size' => @filesize($filepath) ?: 0,
                'type' => $type
            ];
        } catch (Exception $e) {
            error_log("Upload process exception: " . $e->getMessage());
            return ['success' => false, 'error' => 'System error during upload processing'];
        }
    }

    /**
     * Log Security Alert
     */
    private static function logSecurityAlert(string $originalName, string $reason, string $type): void
    {
        $userId = $_SESSION['user_id'] ?? 0;
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        GDPRService::logActivity(
            $userId,
            'file_upload_denied',
            'security',
            null,
            "Security Alert: Upload of '$originalName' (Type: $type) denied. Reason: $reason",
            null,
            ['ip' => $ip, 'filename' => $originalName, 'type' => $type, 'reason' => $reason]
        );
    }

    /**
     * Log Upload Success
     */
    private static function logUploadSuccess(string $originalName, string $secureName, string $type): void
    {
        $userId = $_SESSION['user_id'] ?? 0;

        GDPRService::logActivity(
            $userId,
            'file_upload_success',
            'content',
            null,
            "File uploaded successfully: $originalName -> $secureName",
            null,
            ['original' => $originalName, 'secure' => $secureName, 'type' => $type]
        );
    }

    /**
     * Validate upload (Paranoid-Grade)
     */
    public static function validateUpload(array $file, string $type): array
    {
        $config = self::FILE_CONFIGS[$type] ?? null;
        if (!$config) {
            return ['success' => false, 'error' => 'Invalid upload type'];
        }

        // Check if file was uploaded (skip strict check in testing mode)
        $isUploaded = self::$testing ? file_exists($file['tmp_name']) : is_uploaded_file($file['tmp_name']);

        if (!isset($file['tmp_name']) || !$isUploaded) {
            return ['success' => false, 'error' => 'No file uploaded'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Upload error code: ' . $file['error']];
        }

        if ($file['size'] > $config['max_size']) {
            return ['success' => false, 'error' => 'File exceeds maximum size'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $config['extensions'])) {
            return ['success' => false, 'error' => 'Invalid file extension'];
        }

        // 1. Strict MIME Sniffing
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $config['allowed_types'])) {
            // Basic fallback check for zip types which can vary wildly
            if ($type === 'theme' && strpos($mimeType, 'zip') !== false) {
                // allow
            } else {
                return ['success' => false, 'error' => 'Invalid file type: ' . $mimeType];
            }
        }

        // 2. Deep Binary Content Scan (Anti-Hacker)
        $content = file_get_contents($file['tmp_name']);
        $maliciousPatterns = [
            '<?php',
            'eval(',
            '<script',
            'base64_decode',
            'shell_exec',
            'system(',
            'passthru(',
            'popen(',
            'proc_open(',
            'pcntl_exec(',
            'assert(',
            'preg_replace(.*\/e',
            '`ssh`'
        ];
        foreach ($maliciousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return ['success' => false, 'error' => 'Security Alert: Malicious content detected in file binary.'];
            }
        }

        // 3. Image Dimension Check
        if ($config['optimize'] || in_array('image/jpeg', $config['allowed_types'])) {
            $imageInfo = @getimagesize($file['tmp_name']);
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
                if (isset($config['dimensions']['max_width']) && $width > $config['dimensions']['max_width']) {
                    return ['success' => false, 'error' => 'Image width exceeds limit'];
                }
                if (isset($config['dimensions']['max_height']) && $height > $config['dimensions']['max_height']) {
                    return ['success' => false, 'error' => 'Image height exceeds limit'];
                }
            }
        }

        return ['success' => true];
    }

    /**
     * Sanitize Image - Strip EXIF Metadata (Anti-Code Injection)
     * 
     * Hackers can hide PHP code inside EXIF data of valid images.
     * This function re-processes images to strip all metadata.
     * 
     * @param string $filepath Path to the uploaded image
     * @param string $mimeType Verified MIME type of the image
     * @return bool True if sanitization successful, false otherwise
     */
    private static function sanitizeImage(string $filepath, string $mimeType): bool
    {
        try {
            // Verify file exists
            if (!file_exists($filepath)) {
                return false;
            }

            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $img = @imagecreatefromjpeg($filepath);
                    if ($img === false) {
                        error_log("Failed to process JPEG: $filepath");
                        return false;
                    }
                    // Re-save strips EXIF data and any embedded code
                    $result = @imagejpeg($img, $filepath, 90);
                    imagedestroy($img);
                    return $result !== false;

                case 'image/png':
                    $img = @imagecreatefrompng($filepath);
                    if ($img === false) {
                        error_log("Failed to process PNG: $filepath");
                        return false;
                    }
                    // Disable alpha blending and save alpha channel
                    imagesavealpha($img, true);
                    $result = @imagepng($img, $filepath, 9);
                    imagedestroy($img);
                    return $result !== false;

                case 'image/gif':
                    $img = @imagecreatefromgif($filepath);
                    if ($img === false) {
                        error_log("Failed to process GIF: $filepath");
                        return false;
                    }
                    $result = @imagegif($img, $filepath);
                    imagedestroy($img);
                    return $result !== false;

                case 'image/webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $img = @imagecreatefromwebp($filepath);
                        if ($img === false) {
                            error_log("Failed to process WebP: $filepath");
                            return false;
                        }
                        $result = @imagewebp($img, $filepath, 90);
                        imagedestroy($img);
                        return $result !== false;
                    }
                    // If WebP not supported, log warning but don't fail
                    error_log("WebP support not available, skipping sanitization: $filepath");
                    return true;

                default:
                    // For unsupported image types, log and skip
                    error_log("Unsupported image type for sanitization: $mimeType");
                    return true; // Don't fail upload for unsupported types
            }
        } catch (Exception $e) {
            error_log("Image sanitization exception: " . $e->getMessage() . " for file: $filepath");
            return false;
        }
    }

    /**
     * Generate secure filename (High Entropy)
     */
    private static function generateSecureFilename(string $type, string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $random = bin2hex(random_bytes(12));
        return $type . '_' . $random . '.' . $extension;
    }

    /**
     * Delete Old Files
     */
    private static function deleteOldFiles(string $directory, string $type, string $keepFilename = null): int
    {
        if (!is_dir($directory)) return 0;
        $deleted = 0;
        $pattern = $directory . '/' . $type . '_*';
        $files = glob($pattern);
        foreach ($files as $file) {
            if ($keepFilename && basename($file) === $keepFilename) continue;
            if (@unlink($file)) $deleted++;
        }
        return $deleted;
    }

    /**
     * Delete File
     */
    public static function deleteFile(string $filepath): bool
    {
        $realPath = realpath($filepath);
        if (!$realPath || !file_exists($realPath)) return false;

        // Security: Ensure it's in storage or public
        $storageBase = realpath(self::STORAGE_BASE);
        $publicBase = realpath(self::PUBLIC_BASE);
        $tempBase = realpath(self::UPLOAD_PATHS['temp']);

        $safe = false;
        if ($storageBase && strpos($realPath, $storageBase) === 0) $safe = true;
        if ($publicBase && strpos($realPath, $publicBase) === 0) $safe = true;
        if ($tempBase && strpos($realPath, $tempBase) === 0) $safe = true;

        if ($safe) {
            return @unlink($realPath);
        }
        return false;
    }


    /**
     * Optimize Image
     */
    private static function optimizeImage(string $filepath, string $type): bool
    {
        if (!extension_loaded('gd')) return false;
        try {
            $config = self::FILE_CONFIGS[$type];
            $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
            if ($extension === 'svg') return true;

            $image = null;
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $image = @imagecreatefromjpeg($filepath);
                    break;
                case 'png':
                    $image = @imagecreatefrompng($filepath);
                    break;
                case 'gif':
                    $image = @imagecreatefromgif($filepath);
                    break;
                case 'webp':
                    $image = @imagecreatefromwebp($filepath);
                    break;
            }
            if (!$image) return false;

            $currentWidth = imagesx($image);
            $currentHeight = imagesy($image);
            $maxWidth = $config['target_dimensions']['max_width'] ?? $config['dimensions']['max_width'] ?? $currentWidth;
            $maxHeight = $config['target_dimensions']['max_height'] ?? $config['dimensions']['max_height'] ?? $currentHeight;

            if ($currentWidth > $maxWidth || $currentHeight > $maxHeight) {
                $ratio = min($maxWidth / $currentWidth, $maxHeight / $currentHeight);
                $newWidth = (int)($currentWidth * $ratio);
                $newHeight = (int)($currentHeight * $ratio);

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                if ($extension === 'png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                }
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight);
                imagedestroy($image);
                $image = $resized;
            }

            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($image, $filepath, 85);
                    break;
                case 'png':
                    imagepng($image, $filepath, 8);
                    break;
                case 'gif':
                    imagegif($image, $filepath);
                    break;
                case 'webp':
                    imagewebp($image, $filepath, 85);
                    break;
            }
            imagedestroy($image);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getImageConfig(string $type): ?array
    {
        return self::FILE_CONFIGS[$type] ?? null;
    }
}
