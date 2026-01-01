<?php

namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Services\Auth;
use App\Models\LibraryFile;
use App\Models\User;
use Exception;

class LibraryApiController extends Controller
{
    public function browse()
    {
        try {
            $type = $_GET['type'] ?? null;
            $page = $_GET['page'] ?? 1;
            $status = $_GET['status'] ?? 'approved';
            
            // Security check for pending
            if ($status === 'pending') {
                $user = Auth::user();
                $userModel = new User();
                // Check if user is admin
                if (!$user || !$userModel->isAdmin($user['id'])) {
                    $status = 'approved'; // Fallback for non-admins
                }
            } else {
                $status = 'approved';
            }

            $limit = 20;
            $offset = ($page - 1) * $limit;

            $libraryFileModel = new LibraryFile();
            
            if ($status === 'pending') {
                $files = $libraryFileModel->getPending(); // Pending usually small list, ignoring paging for now
            } else {
                $files = $libraryFileModel->getKeyResources($type, $limit, $offset);
            }

            $this->json(['success' => true, 'files' => $files]);

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function upload()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new Exception('Unauthorized access', 401);
            }

            if (!isset($_FILES['file'])) {
                throw new Exception('No file uploaded', 400);
            }

            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $fileType = $_POST['type'] ?? 'other';

            if (empty($title)) {
                throw new Exception('Title is required', 400);
            }

            $allowedExtensions = ['dwg', 'dxf', 'pdf', 'xlsx', 'xls', 'xlsm', 'docx', 'doc', 'jpg', 'png'];
            $maxSize = 15 * 1024 * 1024; // 15MB

            $file = $_FILES['file'];
            
            if ($file['size'] > $maxSize) {
                throw new Exception('File size exceeds 15MB limit', 400);
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExtensions)) {
                throw new Exception('Invalid file type. Allowed: ' . implode(', ', $allowedExtensions), 400);
            }

            $uploadDir = STORAGE_PATH . '/library/quarantine';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = uniqid('lib_') . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . '/' . $filename;

            // Compute Hash
            $fileHash = hash_file('sha256', $file['tmp_name']);
            $libraryFileModel = new LibraryFile();
            
            // Duplicate Check
            $existing = $libraryFileModel->findByHash($fileHash);
            if ($existing) {
                if ($existing->uploader_id == $user['id']) {
                    throw new Exception('You have already uploaded this file.', 409);
                } else {
                    throw new Exception('Duplicate file detected. This resource was found in our library uploaded by another user.', 409);
                }
            }

            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new Exception('Failed to save file', 500);
            }

            // Preview Handling (CAD requires screenshot)
            $previewPath = null;
            if (isset($_FILES['preview']) && $_FILES['preview']['error'] === UPLOAD_ERR_OK) {
                $previewFile = $_FILES['preview'];
                $previewExt = strtolower(pathinfo($previewFile['name'], PATHINFO_EXTENSION));
                if (in_array($previewExt, ['jpg', 'jpeg', 'png', 'webp'])) {
                     // Upload Preview
                     $previewFilename = uniqid('preview_') . '_' . time() . '.' . $previewExt;
                     $previewDir = STORAGE_PATH . '/library/previews';
                     if (!is_dir($previewDir)) mkdir($previewDir, 0755, true);
                     
                     $targetPreview = $previewDir . '/' . $previewFilename;
                     if(move_uploaded_file($previewFile['tmp_name'], $targetPreview)) {
                         $previewPath = 'previews/' . $previewFilename;

                         // WATERMARK LOGIC
                         try {
                             if (class_exists(\Intervention\Image\ImageManager::class)) {
                                 // v3
                                 $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                                 $image = $manager->read($targetPreview);
                                 
                                 // Add text watermark
                                 $image->text('PREVIEW ONLY - UNPAID', $image->width() / 2, $image->height() / 2, function ($font) {
                                     $font->size(48);
                                     $font->color('rgba(255, 0, 0, 0.5)');
                                     $font->align('center');
                                     $font->valign('middle');
                                     $font->angle(45);
                                 });
                                 
                                 $image->save($targetPreview);
                             } elseif (class_exists(\Intervention\Image\ImageManagerStatic::class)) {
                                 // v2 Fallback (Unlikely given composer.json, but safe)
                                 $img = \Intervention\Image\ImageManagerStatic::make($targetPreview);
                                 $img->text('PREVIEW ONLY', $img->width()/2, $img->height()/2, function($font) {
                                     $font->size(48);
                                     $font->color(array(255, 0, 0, 0.5));
                                     $font->align('center');
                                     $font->valign('middle');
                                     $font->angle(45);
                                 });
                                 $img->save($targetPreview);
                             }
                         } catch (Exception $e) {
                             // Log or ignore watermark failure, file is still saved
                             error_log("Watermark failed: " . $e->getMessage());
                         }
                     }
                }
            } else if ($fileType === 'cad') {
                // FORCE PREVIEW FOR CAD
                throw new Exception('A preview image (JPG/PNG) is required for CAD files.', 400); 
            }

            // Auto-generate preview for Images?
            // If file_type is 'image' (jpg/png), we can use the file itself as preview or make a thumb.
            if ($fileType === 'image' && !$previewPath) {
                 // For now, simple: use the file path itself if we copy it to public? 
                 // Actually file is in quarantine (protected). better to wait for admin approval logic to move it.
                 // But we can flag it.
            }

            $fileId = $libraryFileModel->create([
                'uploader_id' => $user['id'],
                'title' => $title,
                'description' => $description,
                'file_path' => 'quarantine/' . $filename,
                'file_type' => $fileType,
                'file_size_kb' => round($file['size'] / 1024),
                'price_coins' => 50,
                'status' => 'pending',
                'file_hash' => $fileHash,
                'preview_path' => $previewPath
            ]);

            $this->json([
                'success' => true,
                'message' => 'File uploaded successfully! It is now pending admin approval.',
                'file_id' => $fileId
            ]);

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function approve()
    {
        try {
            // Admin check middleware handles auth, but verifying admin role:
            $user = Auth::user();
            $userModel = new User();
            if (!$userModel->isAdmin($user['id'])) {
                throw new Exception('Unauthorized: Admin access required', 403);
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $fileId = $input['file_id'] ?? null;
            $action = $input['action'] ?? null;

            if (!$fileId) throw new Exception('File ID is required');

            $libraryFileModel = new LibraryFile();
            $file = $libraryFileModel->find($fileId);

            if (!$file) throw new Exception('File not found', 404);
            if ($file->status !== 'pending') throw new Exception('File is not pending approval');

            if ($action === 'approve') {
                $sourcePath = STORAGE_PATH . '/library/' . $file->file_path;
                $fileType = $file->file_type;
                $targetDir = STORAGE_PATH . '/library/approved/' . $fileType;
                
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

                $fileName = basename($file->file_path);
                $targetLink = 'approved/' . $fileType . '/' . $fileName;
                $targetPath = $targetDir . '/' . $fileName;

                if (file_exists($sourcePath)) {
                    if (!rename($sourcePath, $targetPath)) {
                        throw new Exception('Failed to move file', 500);
                    }
                    
                    // Update Path manually via DB as model doesn't have update method for path yet
                    $db = \App\Core\Database::getInstance();
                    $stmt = $db->getPdo()->prepare("UPDATE library_files SET file_path = ? WHERE id = ?");
                    $stmt->execute([$targetLink, $fileId]);

                } else {
                     // Log warning but proceed if testing
                }

                $libraryFileModel->approve($fileId);

                $reward = 100;
                if ($fileType === 'cad') $reward = 200;
                if ($fileType === 'pdf') $reward = 30;

                $userModel->addCoins($file->uploader_id, $reward, 'Reward for uploading: ' . $file->title, $fileId);
                
                $this->json(['success' => true, 'message' => 'Approved and coins awarded']);

            } elseif ($action === 'reject') {
                $reason = $input['reason'] ?? 'Did not meet guidelines';
                $libraryFileModel->reject($fileId, $reason);
                $this->json(['success' => true, 'message' => 'Rejected']);
            } else {
                throw new Exception('Invalid action');
            }

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function download()
    {
        try {
            $user = Auth::user();
            if (!$user) die('Unauthorized');

            $fileId = $_GET['id'] ?? null;
            if (!$fileId) die('File ID required');

            $libraryFileModel = new LibraryFile();
            $file = $libraryFileModel->find($fileId);

            if (!$file || $file->status !== 'approved') die('File unavailable');

            $isUploader = ($file->uploader_id == $user['id']);
            $userModel = new User();
            
            // Check previous purchase
             $db = \App\Core\Database::getInstance();
            $stmt = $db->getPdo()->prepare("SELECT COUNT(*) as count FROM user_transactions WHERE user_id = ? AND reference_id = ? AND type = 'download_cost'");
            $stmt->execute([$user['id'], $fileId]);
            $hasPurchased = $stmt->fetch()['count'] > 0;

            if (!$isUploader && !$hasPurchased) {
                $cost = $file->price_coins;
                if (!$userModel->deductCoins($user['id'], $cost, 'Purchase: ' . $file->title, $fileId)) {
                     die("Insufficient Coins. Need $cost Coins.");
                }
                
                // Royalty
                $userModel->addCoins($file->uploader_id, 5, 'Royalty: ' . $file->title, $fileId);
            }

            $filePath = STORAGE_PATH . '/library/' . $file->file_path;
            
            if (!file_exists($filePath)) die('File missing');

            $libraryFileModel->incrementDownloads($fileId);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file->title . '.' . $file->file_type) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function stream()
    {
        try {
             $fileId = $_GET['id'] ?? null;
             if (!$fileId) die('ID Required');

             $libraryFileModel = new LibraryFile();
             $file = $libraryFileModel->find($fileId);
             if (!$file) die('Not found');

             // Check permissions logic if needed
             // For now, allow viewing if file exists (preview logic)
             
             $path = STORAGE_PATH . '/library/' . $file->file_path;
             if (!file_exists($path)) die('File missing');
             
             $mime = mime_content_type($path);
             header('Content-Type: ' . $mime);
             header('Content-Disposition: inline; filename="' . basename($path) . '"');
             readfile($path);
             exit;
        } catch (Exception $e) {
             die($e->getMessage());
        }
    }

    public function previewImage()
    {
         $fileId = $_GET['id'] ?? null;
         if (!$fileId) die('ID Required');
         
         $libraryFileModel = new LibraryFile();
         $file = $libraryFileModel->find($fileId);
         if (!$file || empty($file->preview_path)) die('No preview');
         
         $path = STORAGE_PATH . '/library/' . $file->preview_path;
         if (!file_exists($path)) die('Preview missing');
         
         $mime = mime_content_type($path);
         header('Content-Type: ' . $mime);
         header('Content-Disposition: inline');
         readfile($path);
         exit;
    }
}
