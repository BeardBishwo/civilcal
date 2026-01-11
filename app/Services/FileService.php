<?php

namespace App\Services;

use Exception;

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
        ],
        'user' => [
            'profile' => self::STORAGE_BASE . '/users',
            'avatar' => self::STORAGE_BASE . '/users',
            'document' => self::STORAGE_BASE . '/users/documents',
        ],
        'theme' => BASE_PATH . '/themes', // Special path for themes
        'temp' => self::STORAGE_BASE . '/temp',
    ];

    // Public URL paths
    private const PUBLIC_URLS = [
        'admin' => [
            'logo' => '/storage/uploads/admin/logos',
            'favicon' => '/assets/icons',
            'banner' => '/storage/uploads/admin/banners',
            'document' => '/storage/uploads/admin/documents',
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
        'document' => [
            'max_size' => 10485760, // 10MB
            'allowed_types' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'],
            'extensions' => ['pdf', 'doc', 'docx', 'txt'],
            'optimize' => false,
        ],
        'theme' => [
            'max_size' => 52428800, // 50MB
            'allowed_types' => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip'],
            'extensions' => ['zip'],
            'optimize' => false,
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
    private static function createDirectory(string $path): bool
    {
        try {
            if (!is_dir($path)) {
                if (!mkdir($path, 0755, true)) {
                    return false;
                }
            }

            // Create .htaccess for security
            $htaccess = $path . '/.htaccess';
            if (!file_exists($htaccess)) {
                $content = "# Prevent PHP execution\n";
                // Only effectively disable PHP execution, allow access to static files
                $content .= "<FilesMatch \"\\.(php|phtml|php3|php4|php5|php6|phps|pht|phar)$\">\n";
                $content .= "    Order Deny,Allow\n";
                $content .= "    Deny from all\n";
                $content .= "</FilesMatch>\n";
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
        try {
            if (!isset(self::FILE_CONFIGS[$type])) {
                return ['success' => false, 'error' => 'Invalid file type configuration'];
            }

            $validation = self::validateUpload($file, $type);
            if (!$validation['success']) {
                return $validation;
            }

            $uploadPath = self::UPLOAD_PATHS['admin'][$type] ?? null;
            if (!$uploadPath) {
                return ['success' => false, 'error' => 'Upload path not configured'];
            }

            self::createDirectory($uploadPath);

            $filename = self::generateSecureFilename($type, $file['name']);
            $filepath = $uploadPath . '/' . $filename;

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'error' => 'Failed to move uploaded file'];
            }

            chmod($filepath, 0644);

            if (self::FILE_CONFIGS[$type]['optimize']) {
                self::optimizeImage($filepath, $type);
            }

            $publicUrl = (self::PUBLIC_URLS['admin'][$type] ?? '') . '/' . $filename;

            // Delete old if needed (only for single-file types like logo/favicon)
            // For documents/banners we might want to keep multiple, but keeping previous behavior for now
            if (in_array($type, ['logo', 'favicon'])) {
                self::deleteOldFiles($uploadPath, $type, $filename);
            }

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filepath,
                'url' => $publicUrl,
                'size' => filesize($filepath),
                'type' => $type,
            ];

        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()];
        }
    }
    
    // Alias strictly for backward compatibility logic if needed, but 'uploadAdminFile' handles it.
    public static function uploadAdminImage(array $file, string $type): array
    {
        return self::uploadAdminFile($file, $type);
    }

    /**
     * Upload user file (avatar, profile, document)
     */
    public static function uploadUserFile(array $file, int $userId, string $type = 'profile'): array
    {
        try {
            if (!isset(self::FILE_CONFIGS[$type])) {
                return ['success' => false, 'error' => 'Invalid file type configuration'];
            }

            $validation = self::validateUpload($file, $type);
            if (!$validation['success']) {
                return $validation;
            }

            $userPath = (self::UPLOAD_PATHS['user'][$type] ?? self::UPLOAD_PATHS['user']['profile']) . '/' . $userId;
            self::createDirectory($userPath);

            $filename = self::generateSecureFilename($type, $file['name']);
            $filepath = $userPath . '/' . $filename;

            // Clean up old profile/avatar images
            if (in_array($type, ['profile', 'avatar'])) {
                self::deleteOldFiles($userPath, $type);
            }

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'error' => 'Failed to move uploaded file'];
            }

            chmod($filepath, 0644);

            if (self::FILE_CONFIGS[$type]['optimize']) {
                self::optimizeImage($filepath, $type);
            }

            $publicBase = self::PUBLIC_URLS['user'][$type] ?? self::PUBLIC_URLS['user']['profile'];
            $publicUrl = $publicBase . '/' . $userId . '/' . $filename;

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filepath,
                'url' => $publicUrl,
                'size' => filesize($filepath),
                'type' => $type,
                'user_id' => $userId,
            ];

        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()];
        }
    }
    
    // Alias for backward compatibility
    public static function uploadUserImage(array $file, int $userId): array
    {
        return self::uploadUserFile($file, $userId, 'profile');
    }

    /**
     * Upload and Extract Theme
     */
    public static function uploadTheme(array $file): array
    {
        try {
            $type = 'theme';
            $validation = self::validateUpload($file, $type);
            if (!$validation['success']) {
                return $validation;
            }

            // We upload to temp folder first
            $tempPath = self::UPLOAD_PATHS['temp'];
            self::createDirectory($tempPath);

            $filename = self::generateSecureFilename('theme_pkg', $file['name']);
            $filepath = $tempPath . '/' . $filename;

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'error' => 'Failed to move uploaded theme file'];
            }

            return [
                'success' => true,
                'filepath' => $filepath,
                'filename' => $filename,
                'message' => 'Theme uploaded to temp storage'
            ];

        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Theme upload failed: ' . $e->getMessage()];
        }
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
     * Validate upload
     */
    private static function validateUpload(array $file, string $type): array
    {
        $config = self::FILE_CONFIGS[$type] ?? null;
        if (!$config) {
            return ['success' => false, 'error' => 'Invalid upload type'];
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
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

        // Image Dimension Check
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
     * Generate secure filename
     */
    private static function generateSecureFilename(string $type, string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return $type . '_' . $timestamp . '_' . $random . '.' . $extension;
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
                case 'jpg': case 'jpeg': $image = @imagecreatefromjpeg($filepath); break;
                case 'png': $image = @imagecreatefrompng($filepath); break;
                case 'gif': $image = @imagecreatefromgif($filepath); break;
                case 'webp': $image = @imagecreatefromwebp($filepath); break;
            }
            if (!$image) return false;

            $currentWidth = imagesx($image);
            $currentHeight = imagesy($image);
            $maxWidth = $config['dimensions']['max_width'] ?? $currentWidth;
            $maxHeight = $config['dimensions']['max_height'] ?? $currentHeight;

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
                case 'jpg': case 'jpeg': imagejpeg($image, $filepath, 85); break;
                case 'png': imagepng($image, $filepath, 8); break;
                case 'gif': imagegif($image, $filepath); break;
                case 'webp': imagewebp($image, $filepath, 85); break;
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
