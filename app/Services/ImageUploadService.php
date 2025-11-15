<?php

namespace App\Services;

use Exception;

/**
 * Image Upload Service
 * Modular and secure image upload handling system
 * Handles logo, favicon, banner, and profile images
 */
class ImageUploadService
{
    // Storage configuration
    private const STORAGE_BASE = BASE_PATH . '/storage/uploads';
    private const PUBLIC_BASE = BASE_PATH . '/public/assets';

    // Upload directories (modular structure)
    private const UPLOAD_PATHS = [
        'admin' => [
            'logo' => self::STORAGE_BASE . '/admin/logos',
            'favicon' => self::PUBLIC_BASE . '/icons',
            'banner' => self::STORAGE_BASE . '/admin/banners',
        ],
        'user' => [
            'profile' => self::STORAGE_BASE . '/users',
            'avatar' => self::STORAGE_BASE . '/users',
        ],
        'temp' => self::STORAGE_BASE . '/temp',
    ];

    // Public URL paths
    private const PUBLIC_URLS = [
        'admin' => [
            'logo' => '/storage/uploads/admin/logos',
            'favicon' => '/assets/icons',
            'banner' => '/storage/uploads/admin/banners',
        ],
        'user' => [
            'profile' => '/storage/uploads/users',
            'avatar' => '/storage/uploads/users',
        ],
    ];

    // Image type configurations
    private const IMAGE_CONFIGS = [
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
    ];

    /**
     * Initialize storage directories
     * Creates all required directories with proper permissions
     *
     * @return array Status of directory creation
     */
    public static function initializeDirectories(): array
    {
        $results = [];
        $allPaths = [];

        // Collect all paths
        foreach (self::UPLOAD_PATHS as $category => $paths) {
            if (is_array($paths)) {
                foreach ($paths as $type => $path) {
                    $allPaths[] = $path;
                }
            } else {
                $allPaths[] = $paths;
            }
        }

        // Create directories
        foreach ($allPaths as $path) {
            $created = self::createDirectory($path);
            $results[$path] = $created;
        }

        return $results;
    }

    /**
     * Create directory with proper permissions and security
     *
     * @param string $path Directory path
     * @return bool Success status
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
                $content .= "AddType text/plain .php .phtml .php3 .php4 .php5 .php6 .phps .pht .phar\n";
                $content .= "php_flag engine off\n\n";
                $content .= "# Allow image access\n";
                $content .= "<FilesMatch \"\\.(jpg|jpeg|png|gif|ico|svg|webp)$\">\n";
                $content .= "    Order Allow,Deny\n";
                $content .= "    Allow from all\n";
                $content .= "</FilesMatch>\n";

                file_put_contents($htaccess, $content);
            }

            // Create index.php to prevent directory listing
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
     * Upload admin image (logo, favicon, banner)
     *
     * @param array $file $_FILES array element
     * @param string $type Image type (logo, favicon, banner)
     * @return array Upload result
     */
    public static function uploadAdminImage(array $file, string $type): array
    {
        try {
            // Validate image type
            if (!isset(self::IMAGE_CONFIGS[$type])) {
                return ['success' => false, 'error' => 'Invalid image type'];
            }

            // Validate upload
            $validation = self::validateUpload($file, $type);
            if (!$validation['success']) {
                return $validation;
            }

            // Get upload path
            $uploadPath = self::UPLOAD_PATHS['admin'][$type] ?? null;
            if (!$uploadPath) {
                return ['success' => false, 'error' => 'Upload path not configured'];
            }

            // Ensure directory exists
            self::createDirectory($uploadPath);

            // Generate secure filename
            $filename = self::generateSecureFilename($type, $file['name']);
            $filepath = $uploadPath . '/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'error' => 'Failed to move uploaded file'];
            }

            // Set proper permissions
            chmod($filepath, 0644);

            // Optimize image if configured
            if (self::IMAGE_CONFIGS[$type]['optimize']) {
                self::optimizeImage($filepath, $type);
            }

            // Get public URL
            $publicUrl = (self::PUBLIC_URLS['admin'][$type] ?? '') . '/' . $filename;

            // Delete old images of the same type
            self::deleteOldImages($uploadPath, $type, $filename);

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filepath,
                'url' => $publicUrl,
                'size' => filesize($filepath),
                'type' => $type,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Upload user profile image
     *
     * @param array $file $_FILES array element
     * @param int $userId User ID
     * @return array Upload result
     */
    public static function uploadUserImage(array $file, int $userId): array
    {
        try {
            $type = 'profile';

            // Validate upload
            $validation = self::validateUpload($file, $type);
            if (!$validation['success']) {
                return $validation;
            }

            // Create user-specific directory
            $userPath = self::UPLOAD_PATHS['user']['profile'] . '/' . $userId;
            self::createDirectory($userPath);

            // Generate secure filename
            $filename = self::generateSecureFilename('profile', $file['name']);
            $filepath = $userPath . '/' . $filename;

            // Delete old profile images for this user
            self::deleteOldImages($userPath, 'profile');

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'error' => 'Failed to move uploaded file'];
            }

            // Set proper permissions
            chmod($filepath, 0644);

            // Optimize image
            if (self::IMAGE_CONFIGS[$type]['optimize']) {
                self::optimizeImage($filepath, $type);
            }

            // Get public URL
            $publicUrl = self::PUBLIC_URLS['user']['profile'] . '/' . $userId . '/' . $filename;

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
            return [
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate uploaded file
     *
     * @param array $file $_FILES array element
     * @param string $type Image type
     * @return array Validation result
     */
    private static function validateUpload(array $file, string $type): array
    {
        $config = self::IMAGE_CONFIGS[$type] ?? null;
        if (!$config) {
            return ['success' => false, 'error' => 'Invalid image type'];
        }

        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'error' => 'No file uploaded'];
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => self::getUploadErrorMessage($file['error'])];
        }

        // Check file size
        if ($file['size'] > $config['max_size']) {
            $maxSizeMB = round($config['max_size'] / 1048576, 2);
            return ['success' => false, 'error' => "File size exceeds maximum allowed size of {$maxSizeMB}MB"];
        }

        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $config['extensions'])) {
            return [
                'success' => false,
                'error' => 'Invalid file extension. Allowed: ' . implode(', ', $config['extensions'])
            ];
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $config['allowed_types'])) {
            return [
                'success' => false,
                'error' => 'Invalid file type. Expected image file.'
            ];
        }

        // Validate image dimensions
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['success' => false, 'error' => 'Invalid image file'];
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        if (isset($config['dimensions']['max_width']) && $width > $config['dimensions']['max_width']) {
            return [
                'success' => false,
                'error' => 'Image width exceeds maximum allowed width of ' . $config['dimensions']['max_width'] . 'px'
            ];
        }

        if (isset($config['dimensions']['max_height']) && $height > $config['dimensions']['max_height']) {
            return [
                'success' => false,
                'error' => 'Image height exceeds maximum allowed height of ' . $config['dimensions']['max_height'] . 'px'
            ];
        }

        return ['success' => true];
    }

    /**
     * Generate secure filename
     *
     * @param string $type Image type
     * @param string $originalName Original filename
     * @return string Secure filename
     */
    private static function generateSecureFilename(string $type, string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = time();
        $random = bin2hex(random_bytes(8));

        return $type . '_' . $timestamp . '_' . $random . '.' . $extension;
    }

    /**
     * Optimize image (resize and compress)
     *
     * @param string $filepath Path to image file
     * @param string $type Image type
     * @return bool Success status
     */
    private static function optimizeImage(string $filepath, string $type): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        try {
            $config = self::IMAGE_CONFIGS[$type];
            $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

            // Skip SVG optimization
            if ($extension === 'svg') {
                return true;
            }

            // Load image
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

            if (!$image) {
                return false;
            }

            // Get current dimensions
            $currentWidth = imagesx($image);
            $currentHeight = imagesy($image);

            // Calculate new dimensions if needed
            $maxWidth = $config['dimensions']['max_width'] ?? $currentWidth;
            $maxHeight = $config['dimensions']['max_height'] ?? $currentHeight;

            $needsResize = ($currentWidth > $maxWidth || $currentHeight > $maxHeight);

            if ($needsResize) {
                // Calculate aspect ratio
                $ratio = min($maxWidth / $currentWidth, $maxHeight / $currentHeight);
                $newWidth = (int)($currentWidth * $ratio);
                $newHeight = (int)($currentHeight * $ratio);

                // Create resized image
                $resized = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve transparency for PNG
                if ($extension === 'png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
                }

                // Resize image
                imagecopyresampled(
                    $resized, $image,
                    0, 0, 0, 0,
                    $newWidth, $newHeight,
                    $currentWidth, $currentHeight
                );

                imagedestroy($image);
                $image = $resized;
            }

            // Save optimized image
            $success = false;
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $success = imagejpeg($image, $filepath, 85);
                    break;
                case 'png':
                    $success = imagepng($image, $filepath, 8);
                    break;
                case 'gif':
                    $success = imagegif($image, $filepath);
                    break;
                case 'webp':
                    $success = imagewebp($image, $filepath, 85);
                    break;
            }

            imagedestroy($image);
            return $success;

        } catch (Exception $e) {
            error_log("Image optimization failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete old images of the same type
     *
     * @param string $directory Directory path
     * @param string $type Image type
     * @param string $keepFilename Filename to keep (optional)
     * @return int Number of files deleted
     */
    private static function deleteOldImages(string $directory, string $type, string $keepFilename = null): int
    {
        $deleted = 0;

        if (!is_dir($directory)) {
            return $deleted;
        }

        $pattern = $directory . '/' . $type . '_*';
        $files = glob($pattern);

        foreach ($files as $file) {
            $filename = basename($file);

            // Skip the file we want to keep
            if ($keepFilename && $filename === $keepFilename) {
                continue;
            }

            if (@unlink($file)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Delete specific image
     *
     * @param string $filepath Path to image file
     * @return bool Success status
     */
    public static function deleteImage(string $filepath): bool
    {
        // Security check - only allow deletion from uploads directory
        $realPath = realpath($filepath);
        $storageBase = realpath(self::STORAGE_BASE);
        $publicBase = realpath(self::PUBLIC_BASE);

        if (!$realPath) {
            return false;
        }

        // Check if path is within allowed directories
        $isInStorage = $storageBase && strpos($realPath, $storageBase) === 0;
        $isInPublic = $publicBase && strpos($realPath, $publicBase) === 0;

        if (!$isInStorage && !$isInPublic) {
            return false;
        }

        return @unlink($realPath);
    }

    /**
     * Get upload error message
     *
     * @param int $errorCode PHP upload error code
     * @return string Error message
     */
    private static function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE directive in HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }

    /**
     * Get image configuration
     *
     * @param string $type Image type
     * @return array|null Configuration or null if not found
     */
    public static function getImageConfig(string $type): ?array
    {
        return self::IMAGE_CONFIGS[$type] ?? null;
    }

    /**
     * Get all image configurations
     *
     * @return array All configurations
     */
    public static function getAllImageConfigs(): array
    {
        return self::IMAGE_CONFIGS;
    }

    /**
     * Get upload paths configuration
     *
     * @return array Upload paths
     */
    public static function getUploadPaths(): array
    {
        return self::UPLOAD_PATHS;
    }

    /**
     * Get public URLs configuration
     *
     * @return array Public URLs
     */
    public static function getPublicUrls(): array
    {
        return self::PUBLIC_URLS;
    }
}
