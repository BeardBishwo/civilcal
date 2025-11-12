<?php
namespace App\Services;

class FileUploadService
{
    private $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $allowedDocumentTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    private $maxFileSize = 10 * 1024 * 1024; // 10MB
    
    public function uploadImage($file, $destination, $filename = null)
    {
        return $this->uploadFile($file, $destination, $filename, $this->allowedImageTypes);
    }
    
    public function uploadDocument($file, $destination, $filename = null)
    {
        return $this->uploadFile($file, $destination, $filename, $this->allowedDocumentTypes);
    }
    
    public function uploadTheme($file, $destination)
    {
        // Check if it's a ZIP file using finfo and extension
        $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
        $detected = $finfo ? finfo_file($finfo, $file['tmp_name']) : null;
        if ($finfo) { finfo_close($finfo); }
        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $zipTypes = ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip'];
        if ($ext !== 'zip' || !$detected || !in_array($detected, $zipTypes, true)) {
            return ['success' => false, 'message' => 'Only ZIP files are allowed for themes'];
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'message' => 'File size exceeds maximum limit of 10MB'];
        }
        
        // Create destination directory if it doesn't exist
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        // Generate unique filename
        $base = preg_replace('/[^a-z0-9-_]+/i', '-', pathinfo($file['name'], PATHINFO_FILENAME));
        $filename = rtrim($base, '-') . '_' . uniqid('', true) . '.zip';
        $filePath = $destination . '/' . $filename;
        
        // Move uploaded file
        if (is_uploaded_file($file['tmp_name']) && move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => true, 
                'file_path' => $filePath,
                'filename' => $filename,
                'message' => 'Theme uploaded successfully'
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to upload theme file'];
    }
    
    public function extractTheme($zipPath, $extractTo)
    {
        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'message' => 'ZipArchive extension is not available'];
        }
        
        $zip = new \ZipArchive;
        $result = $zip->open($zipPath);
        
        if ($result === TRUE) {
            // Create extraction directory
            if (!is_dir($extractTo)) {
                mkdir($extractTo, 0755, true);
            }
            
            // Extract files
            $zip->extractTo($extractTo);
            $zip->close();
            
            // Look for theme.json to find theme folder
            $themeFolder = $this->findThemeFolder($extractTo);
            
            if ($themeFolder) {
                return [
                    'success' => true,
                    'theme_folder' => $themeFolder,
                    'extract_path' => $extractTo,
                    'message' => 'Theme extracted successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'No theme.json found in the extracted files'];
            }
        }
        
        return ['success' => false, 'message' => 'Failed to extract theme file'];
    }
    
    private function findThemeFolder($extractPath)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($extractPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'theme.json') {
                return $file->getPath();
            }
        }
        
        return null;
    }
    
    private function uploadFile($file, $destination, $filename = null, $allowedTypes = [])
    {
        // Check for upload errors
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload error: ' . $file['error']];
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'message' => 'Invalid uploaded file'];
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
            $detected = $finfo ? finfo_file($finfo, $file['tmp_name']) : null;
            if ($finfo) { finfo_close($finfo); }
            if (!$detected || !in_array($detected, $allowedTypes, true)) {
                return ['success' => false, 'message' => 'File type not allowed'];
            }
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'message' => 'File size exceeds maximum limit'];
        }
        
        // Create destination directory if it doesn't exist
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        // Generate filename
        if (!$filename) {
            $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
            $safeBase = preg_replace('/[^a-z0-9-_]+/i', '-', pathinfo($file['name'] ?? 'file', PATHINFO_FILENAME));
            $filename = rtrim($safeBase, '-') . '_' . uniqid('', true) . ($extension ? '.' . $extension : '');
        }
        
        $filePath = $destination . '/' . $filename;
        
        // Move uploaded file
        if (is_uploaded_file($file['tmp_name']) && move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => true, 
                'file_path' => $filePath,
                'filename' => $filename,
                'url' => $this->getPublicUrl($filePath),
                'message' => 'File uploaded successfully'
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to upload file'];
    }
    
    private function getPublicUrl($filePath)
    {
        // Convert absolute path to public URL
        $publicPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);
        return $publicPath;
    }
    
    public function deleteFile($filePath)
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
    
    public function cleanTempFiles($directory, $maxAge = 3600) // 1 hour
    {
        if (!is_dir($directory)) return;
        
        $files = glob($directory . '/*');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= $maxAge) {
                    unlink($file);
                }
            }
        }
    }
}
?>
