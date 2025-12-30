<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Page;
use App\Models\Menu;
use App\Models\Media;

class ContentController extends Controller
{
    private $pageModel;
    private $menuModel;
    private $mediaModel;

    public function __construct()
    {
        parent::__construct();
        $this->pageModel = new Page();
        $this->menuModel = new Menu();
        $this->mediaModel = new Media();
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Get Summary Stats
        $pageStats = $this->pageModel->getStats();
        $menuStats = $this->menuModel->getStats();
        $mediaStats = $this->mediaModel->getStats();

        $data = [
            'user' => $user,
            'page_title' => 'Content Management - Admin Panel',
            'currentPage' => 'content',
            'stats' => [
                'pages' => $pageStats,
                'menus' => $menuStats,
                'media' => $mediaStats
            ]
        ];

        $this->view->render('admin/content/index', $data);
    }

    public function pages()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        // Get filters
        $filters = [
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        $page = $_GET['page'] ?? 1;

        // Get pages using Model
        $pagesData = $this->pageModel->getAll($filters, $page, 10);

        $data = [
            'user' => $user,
            'pages' => $pagesData['data'], // Pass the array of pages
            'pagination' => $pagesData,    // Pass full pagination object if needed
            'filters' => $filters,
            'page_title' => 'Manage Pages - Admin Panel',
            'currentPage' => 'content'
        ];

        $this->view->render('admin/content/pages', $data);
    }

    public function menus()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $filters = [
            'search' => $_GET['search'] ?? null,
            'location' => $_GET['location'] ?? null,
            'is_active' => isset($_GET['status']) && $_GET['status'] === 'active' ? 1 : (isset($_GET['status']) && $_GET['status'] === 'inactive' ? 0 : null)
        ];

        // Get menus using Model
        $menus = $this->menuModel->getAll($filters);

        // Calculate item counts for display if not done in SQL
        foreach ($menus as &$menu) {
            $items = is_string($menu['items']) ? json_decode($menu['items'], true) : $menu['items'];
            $menu['items_count'] = is_array($items) ? count($items) : 0;
            $menu['status'] = $menu['is_active'] ? 'active' : 'inactive'; // Map for view compatibility
            $menu['modified_at'] = $menu['updated_at']; // Map for view compatibility
        }

        $data = [
            'user' => $user,
            'menus' => $menus,
            'filters' => $filters,
            'page_title' => 'Manage Menus - Admin Panel',
            'currentPage' => 'content'
        ];

        $this->view->render('admin/content/menus', $data);
    }

    public function media()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->is_admin) {
                http_response_code(403);
                $this->logError('Unauthorized media access attempt', ['user_id' => $user->id ?? 'guest']);
                die('Access denied');
            }

            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
            
            // Limit perPage to reasonable values
            $perPage = max(20, min($perPage, 200));
            
            $filters = [
                'search' => isset($_GET['search']) ? trim($_GET['search']) : null,
                'type' => isset($_GET['type']) ? trim($_GET['type']) : null
            ];

            // Get media using Model with error handling
            $mediaData = $this->mediaModel->getAll($filters, $page, $perPage);

            // Get usage info for these items
            $usageInfo = $this->mediaModel->getUsageInfo($mediaData['data']);

            // Transform data for view if necessary
            $media = array_map(function ($item) use ($usageInfo) {
                $filePath = $item['file_path'];
                $url = app_base_url('/public/storage/' . $filePath);
                
                // If it's a theme path, don't prefix with public/storage/
                if (strpos($filePath, 'themes/') === 0) {
                    $url = app_base_url($filePath);
                }

                return [
                    'id' => $item['id'],
                    'filename' => $item['original_filename'],
                    'url' => $url,
                    'type' => $item['mime_type'],
                    'size' => $this->formatSize($item['file_size']),
                    'width' => $item['width'] ?? null,
                    'height' => $item['height'] ?? null,
                    'uploaded_at' => $item['created_at'],
                    'usage' => $usageInfo[$item['id']] ?? ['is_used' => false, 'details' => []]
                ];
            }, $mediaData['data']);

            $data = [
                'user' => $user,
                'media' => $media,
                'pagination' => $mediaData,
                'page_title' => 'Media Library - Admin Panel',
                'currentPage' => 'content'
            ];

            $this->view->render('admin/content/media', $data);
        } catch (\Exception $e) {
            $this->logError('Media page error', ['error' => $e->getMessage()]);
            http_response_code(500);
            die('An error occurred while loading the media library.');
        }
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $data = [
            'user' => $user,
            'page_title' => 'Create New Page - Admin Panel',
            'currentPage' => 'content',
            'is_edit' => false,
            'page' => null,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view->render('admin/content/create', $data);
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $page = $this->pageModel->find($id);

        if (!$page) {
            http_response_code(404);
            die('Page not found');
        }

        $data = [
            'user' => $user,
            'page_title' => 'Edit Page - Admin Panel',
            'currentPage' => 'content',
            'is_edit' => true,
            'page' => $page,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view->render('admin/content/create', $data);
    }

    public function save()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }


        // Validate CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            http_response_code(403);
            die('Invalid CSRF Token');
        }

        $id = $_POST['id'] ?? null;
        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'meta_title' => $_POST['meta_title'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'template' => $_POST['template'] ?? 'default',
            'author_id' => $user->id
        ];

        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        if ($id) {
            $this->pageModel->update($id, $data);
        } else {
            $this->pageModel->create($data);
        }

        // Redirect back to pages list
        header('Location: ' . app_base_url('admin/content/pages'));
        exit;
    }

    public function delete($id)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $this->pageModel->delete($id);

        // Redirect back to pages list
        header('Location: ' . app_base_url('admin/content/pages'));
        exit;
    }

    public function createMenu()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $data = [
            'user' => $user,
            'page_title' => 'Create New Menu - Admin Panel',
            'currentPage' => 'content',
            'is_edit' => false,
            'menu' => null,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view->render('admin/content/menu_edit', $data);
    }

    public function editMenu($id)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $menu = $this->menuModel->find($id);

        if (!$menu) {
            http_response_code(404);
            die('Menu not found');
        }

        $data = [
            'user' => $user,
            'page_title' => 'Edit Menu - Admin Panel',
            'currentPage' => 'content',
            'is_edit' => true,
            'menu' => $menu,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view->render('admin/content/menu_edit', $data);
    }

    public function saveMenus()
    {
        $this->requireAdmin();
        $id = $_POST['id'] ?? null;
        $data = [
            'name' => $_POST['name'] ?? '',
            'location' => $_POST['location'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'items' => $_POST['items'] ?? '[]'
        ];

        $menuModel = new Menu();
        if ($id) {
            $menuModel->update($id, $data);
        } else {
            $menuModel->create($data);
        }

        $this->redirect('/admin/content/menus');
    }

    /**
     * AJAX Method: Quick assign a menu to a location
     */
    public function quickAssignLocation()
    {
        $this->requireAdmin();
        
        $location = $_POST['location'] ?? null;
        $menuId = $_POST['menu_id'] ?? null;

        if (!$location) {
            return $this->json(['success' => false, 'message' => 'Location is required']);
        }

        $menuModel = new Menu();

        // 1. Clear this location from any other menu (optional but recommended for exclusivity)
        // Find if any other menu has this location
        $existing = $menuModel->getAll(['location' => $location]);
        foreach ($existing as $ex) {
            if ($ex['id'] != $menuId) {
                $menuModel->update($ex['id'], ['location' => '']); // Clear it
            }
        }

        // 2. Assign to the new menu
        if ($menuId) {
            $result = $menuModel->update($menuId, ['location' => $location]);
            if ($result) {
                return $this->json(['success' => true, 'message' => 'Menu assigned successfully']);
            }
        } else {
            // If menuId is null/0, it means the user selected "Select Menu" (Unassign)
            return $this->json(['success' => true, 'message' => 'Location cleared successfully']);
        }

        return $this->json(['success' => false, 'message' => 'Failed to assign menu']);
    }

    public function uploadMedia()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->is_admin) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                exit;
            }

            // Validate CSRF token
            $token = $_POST['csrf_token'] ?? '';
            if (!$this->validateCsrfToken($token)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
                exit;
            }

            // Check if files were uploaded
            if (!isset($_FILES['files']) || empty($_FILES['files']['name'][0])) {
                echo json_encode(['success' => false, 'message' => 'No files uploaded']);
                exit;
            }

            $uploadedFiles = [];
            $errors = [];
            $maxFileSize = 10 * 1024 * 1024; // 10MB - make this configurable later

            // Process each file
            $fileCount = count($_FILES['files']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
                    $errors[] = $_FILES['files']['name'][$i] . ': Upload error';
                    continue;
                }

                $originalFilename = $_FILES['files']['name'][$i];
                $tmpName = $_FILES['files']['tmp_name'][$i];
                $fileSize = $_FILES['files']['size'][$i];

                // Sanitize original filename
                $originalFilename = $this->sanitizeFilename($originalFilename);

                // Validate filename length
                if (strlen($originalFilename) > 255) {
                    $errors[] = $originalFilename . ': Filename too long (max 255 characters)';
                    continue;
                }

                // Get mime type
                $mimeType = mime_content_type($tmpName);

                // Validate file type
                $allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/zip'
                ];

                if (!in_array($mimeType, $allowedTypes)) {
                    $errors[] = $originalFilename . ': File type not allowed';
                    $this->logError('Invalid file type upload attempt', [
                        'filename' => $originalFilename,
                        'mime_type' => $mimeType,
                        'user_id' => $user->id
                    ]);
                    continue;
                }

                // Validate file size
                if ($fileSize > $maxFileSize) {
                    $errors[] = $originalFilename . ': File too large (max ' . $this->formatSize($maxFileSize) . ')';
                    continue;
                }

                // Determine file type category
                $fileType = 'other';
                if (strpos($mimeType, 'image/') === 0) {
                    $fileType = 'images';
                } elseif (
                    strpos($mimeType, 'application/pdf') === 0 ||
                    strpos($mimeType, 'application/msword') === 0 ||
                    strpos($mimeType, 'application/vnd.') === 0
                ) {
                    $fileType = 'documents';
                }

                // Generate unique filename
                $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . strtolower($extension);

                // Create storage path
                $storagePath = __DIR__ . '/../../../public/storage/media/' . $fileType . '/';
                if (!is_dir($storagePath)) {
                    if (!mkdir($storagePath, 0755, true)) {
                        $errors[] = $originalFilename . ': Failed to create storage directory';
                        $this->logError('Failed to create storage directory', ['path' => $storagePath]);
                        continue;
                    }
                }

                $filePath = $storagePath . $filename;
                $relativeFilePath = 'media/' . $fileType . '/' . $filename;

                // Check for duplicate files (by hash)
                $fileHash = md5_file($tmpName);
                if ($this->isDuplicateFile($fileHash)) {
                    $errors[] = $originalFilename . ': Duplicate file already exists';
                    continue;
                }

                // Move uploaded file
                if (move_uploaded_file($tmpName, $filePath)) {
                    // Collect image dimensions if applicable
                    $width = null;
                    $height = null;
                    if ($fileType === 'images') {
                        $imageInfo = @getimagesize($filePath);
                        if ($imageInfo) {
                            $width = $imageInfo[0];
                            $height = $imageInfo[1];
                        }
                    }

                    // Save to database
                    $mediaId = $this->mediaModel->create([
                        'original_filename' => $originalFilename,
                        'filename' => $filename,
                        'file_path' => $relativeFilePath,
                        'file_size' => $fileSize,
                        'file_type' => $fileType,
                        'mime_type' => $mimeType,
                        'width' => $width,
                        'height' => $height,
                        'uploaded_by' => $user->id
                    ]);

                    if ($mediaId) {
                        $uploadedFiles[] = [
                            'id' => $mediaId,
                            'filename' => $originalFilename,
                            'url' => app_base_url('/storage/' . $relativeFilePath)
                        ];

                        // Log successful upload
                        $this->logInfo('Media uploaded', [
                            'media_id' => $mediaId,
                            'filename' => $originalFilename,
                            'user_id' => $user->id
                        ]);
                    } else {
                        // Delete file if database insert failed
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        $errors[] = $originalFilename . ': Database error';
                        $this->logError('Database insert failed for media', ['filename' => $originalFilename]);
                    }
                } else {
                    $errors[] = $originalFilename . ': Failed to save file';
                    $this->logError('Failed to move uploaded file', [
                        'filename' => $originalFilename,
                        'destination' => $filePath
                    ]);
                }
            }

            echo json_encode([
                'success' => count($uploadedFiles) > 0,
                'uploaded' => $uploadedFiles,
                'errors' => $errors,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully'
            ]);
        } catch (\Exception $e) {
            $this->logError('Media upload exception', ['error' => $e->getMessage()]);
            echo json_encode([
                'success' => false,
                'message' => 'An unexpected error occurred during upload'
            ]);
        }
        exit;
    }

    public function deleteMedia($id)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->is_admin) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                exit;
            }

            // Validate CSRF token
            $token = $_POST['csrf_token'] ?? '';
            if (!$this->validateCsrfToken($token)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
                exit;
            }

            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid media ID']);
                exit;
            }

            // Get media record
            $media = $this->mediaModel->find($id);
            if (!$media) {
                echo json_encode(['success' => false, 'message' => 'Media not found']);
                exit;
            }

            // Delete physical file
            $filePath = __DIR__ . '/../../../public/storage/' . $media['file_path'];
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    $this->logError('Failed to delete physical file', ['path' => $filePath]);
                    echo json_encode(['success' => false, 'message' => 'Failed to delete file from storage']);
                    exit;
                }
            }

            // Delete from database
            if ($this->mediaModel->delete($id)) {
                $this->logInfo('Media deleted', [
                    'media_id' => $id,
                    'filename' => $media['original_filename'],
                    'user_id' => $user->id
                ]);
                echo json_encode(['success' => true, 'message' => 'Media deleted successfully']);
            } else {
                $this->logError('Failed to delete media from database', ['media_id' => $id]);
                echo json_encode(['success' => false, 'message' => 'Failed to delete media from database']);
            }
        } catch (\Exception $e) {
            $this->logError('Media deletion exception', [
                'media_id' => $id,
                'error' => $e->getMessage()
            ]);
            echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
        }
        exit;
    }


    /**
     * AJAX Method: Toggle menu active status
     */
    public function toggleMenuStatus()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $menuId = $_POST['id'] ?? null;
        if (!$menuId) {
            echo json_encode(['success' => false, 'message' => 'Menu ID is required']);
            return;
        }

        $menu = $this->menuModel->find($menuId);
        if (!$menu) {
            echo json_encode(['success' => false, 'message' => 'Menu not found']);
            return;
        }

        $newActive = $menu['is_active'] ? 0 : 1;
        $result = $this->menuModel->update($menuId, ['is_active' => $newActive]);

        if ($result) {
            $status = $newActive ? 'activated' : 'deactivated';
            echo json_encode(['success' => true, 'message' => "Menu $status successfully"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to toggle status']);
        }
    }

    public function updateMedia($id)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }

        // Validate CSRF token
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

        // Get media record
        $media = $this->mediaModel->find($id);
        if (!$media) {
            echo json_encode(['success' => false, 'message' => 'Media not found']);
            exit;
        }

        // Update filename if provided
        $newFilename = $_POST['filename'] ?? null;
        if ($newFilename && $newFilename !== $media['original_filename']) {
            // Sanitize filename
            $newFilename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $newFilename);

            // Update in database
            $this->mediaModel->update($id, [
                'original_filename' => $newFilename
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Media updated successfully']);
        exit;
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * Sanitize filename to prevent security issues
     */
    private function sanitizeFilename($filename)
    {
        // Remove any path components
        $filename = basename($filename);

        // Remove special characters except dots, dashes, and underscores
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Remove multiple consecutive underscores
        $filename = preg_replace('/_+/', '_', $filename);

        // Trim underscores from start and end
        $filename = trim($filename, '_');

        return $filename;
    }

    /**
     * Check if file is duplicate based on hash
     */
    private function isDuplicateFile($hash)
    {
        // For now, return false - implement hash-based duplicate detection later
        // This would require adding a hash column to the media table
        return false;
    }

    /**
     * Log error message
     */
    private function logError($message, $context = [])
    {
        $logFile = __DIR__ . '/../../logs/media_errors.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        $logMessage = "[$timestamp] ERROR: $message $contextStr" . PHP_EOL;

        error_log($logMessage, 3, $logFile);
    }

    /**
     * AJAX Method: Scan disk for untracked files and add them to DB
     */
    public function syncMedia()
    {
        $this->requireAdmin();
        
        // Validate CSRF token
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        $untracked = $this->mediaModel->findUntrackedFiles();
        $syncedCount = 0;
        $user = Auth::user();

        foreach ($untracked as $file) {
            $fullPath = $file['full_path'];
            if (!file_exists($fullPath)) continue;

            $fileSize = filesize($fullPath);
            $mimeType = mime_content_type($fullPath);
            
            $width = null;
            $height = null;
            if (strpos($mimeType, 'image/') === 0) {
                $imageInfo = @getimagesize($fullPath);
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
            }

            // Create DB record
            $mediaId = $this->mediaModel->create([
                'original_filename' => $file['filename'],
                'filename' => $file['filename'],
                'file_path' => $file['relative_path'],
                'file_size' => $fileSize,
                'file_type' => $file['type'],
                'mime_type' => $mimeType,
                'width' => $width,
                'height' => $height,
                'uploaded_by' => $user->id
            ]);

            if ($mediaId) $syncedCount++;
        }

        echo json_encode([
            'success' => true, 
            'message' => "$syncedCount new files discovered and added to library."
        ]);
    }

    /**
     * AJAX Method: Delete all media items NOT in use
     */
    public function bulkDeleteUnused()
    {
        $this->requireAdmin();
        
        // Validate CSRF token
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        // 1. Get ALL media
        $allMedia = $this->mediaModel->getAll([], 1, 1000); // Limit to 1000 for safety
        if (empty($allMedia['data'])) {
            echo json_encode(['success' => false, 'message' => 'No media found to cleanup.']);
            return;
        }

        // 2. Get usage info
        $usageInfo = $this->mediaModel->getUsageInfo($allMedia['data']);
        $deletedCount = 0;

        foreach ($allMedia['data'] as $item) {
            $id = $item['id'];
            if (!isset($usageInfo[$id]) || !$usageInfo[$id]['is_used']) {
                // Not used - Delete physical file
                $filePath = $item['file_path'];
                $fullPath = __DIR__ . '/../../../public/storage/' . $filePath;
                if (strpos($filePath, 'themes/') === 0) {
                    $fullPath = __DIR__ . '/../../../' . $filePath;
                }

                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
                // Delete from DB
                if ($this->mediaModel->delete($id)) {
                    $deletedCount++;
                }
            }
        }

        echo json_encode([
            'success' => true, 
            'message' => "Cleanup complete. Removed $deletedCount unused files."
        ]);
    }

    /**
     * AJAX Method: Delete multiple selected media items
     */
    public function bulkDeleteMedia()
    {
        $this->requireAdmin();
        
        // Validate CSRF token
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        $ids = $_POST['ids'] ?? [];
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['success' => false, 'message' => 'No items selected for deletion.']);
            return;
        }

        $deletedCount = 0;
        foreach ($ids as $id) {
            $item = $this->mediaModel->find($id);
            if ($item) {
                // Delete physical file
                $filePath = $item['file_path'];
                $fullPath = __DIR__ . '/../../../public/storage/' . $filePath;
                if (strpos($filePath, 'themes/') === 0) {
                    $fullPath = __DIR__ . '/../../../' . $filePath;
                }

                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
                
                // Delete from DB
                if ($this->mediaModel->delete($id)) {
                    $deletedCount++;
                }
            }
        }

        echo json_encode([
            'success' => true, 
            'message' => "Successfully deleted $deletedCount items."
        ]);
    }

    /**
     * Log info message
     */
    private function logInfo($message, $context = [])
    {
        $logFile = __DIR__ . '/../../logs/media_info.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        $logMessage = "[$timestamp] INFO: $message $contextStr" . PHP_EOL;

        error_log($logMessage, 3, $logFile);
    }
}
