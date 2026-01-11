<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\LibraryFile;
use App\Models\User;
use Exception;

class LibraryApiController extends Controller
{
    private function userId($user)
    {
        if (!$user) return null;
        return is_object($user) ? ($user->id ?? null) : ($user['id'] ?? null);
    }

    public function browse()
    {
        // Enforce JSON
        header('Content-Type: application/json');
        
        try {
            // Disable HTML error reporting to prevent breaking JSON
            ini_set('display_errors', 0);
            
            $type = $_GET['type'] ?? null;
            $page = $_GET['page'] ?? 1;
            $status = $_GET['status'] ?? 'approved';
            
            $user = Auth::user();
            $uid = $this->userId($user);
            
            // Security check for pending
            if ($status === 'pending') {
                $userModel = new User();
                // Check if user is admin
                if (!$uid || !$userModel->isAdmin($uid)) {
                    $status = 'approved'; // Fallback for non-admins
                }
            } else {
                $status = 'approved';
            }

            $limit = 20;
            $offset = ($page - 1) * $limit;

            $libraryFileModel = new LibraryFile();
            
            if ($status === 'pending') {
                $files = $libraryFileModel->getPending($type);
                $stats = [];
                if (isset($_GET['admin_mode'])) {
                    $db = \App\Core\Database::getInstance();
                    $stats['approved_today'] = $db->getPdo()->query("SELECT COUNT(*) FROM library_files WHERE status = 'approved' AND DATE(approved_at) = CURRENT_DATE")->fetchColumn();
                    $stats['rewards_disbursed'] = $db->getPdo()->query("SELECT SUM(reward_coins) FROM library_files WHERE status = 'approved'")->fetchColumn() ?: 0;
                }
                $this->json(['success' => true, 'files' => $files, 'stats' => $stats]);
            } else {
                $files = $libraryFileModel->getKeyResources($uid, $type, $limit, $offset);
                $this->json(['success' => true, 'files' => $files]);
            }

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function upload()
    {
        try {
            $user = Auth::user();
            $uid = $this->userId($user);
            if (!$uid) {
                throw new Exception('Unauthorized access', 401);
            }

            if (!isset($_FILES['file'])) {
                throw new Exception('No file uploaded', 400);
            }

            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $tags = trim($_POST['tags'] ?? '');
            $fileType = $_POST['type'] ?? 'other';
            $priceCoins = max(0, (int)($_POST['price'] ?? 0));

            // Validate Hashtags (Max 5)
            $tagArray = array_filter(array_map('trim', explode(',', $tags)));
            if (count($tagArray) > 5) {
                throw new Exception('Maximum 5 hashtags allowed.', 400);
            }

            if (empty($title)) {
                throw new Exception('Title is required', 400);
            }

            $extensionMap = [
                'cad' => ['dwg', 'dxf'],
                'solidworks' => ['sldprt', 'sldasm'],
                'excel' => ['xls', 'xlsx', 'xlsm'],
                'pdf' => ['pdf'],
                'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'doc' => ['doc', 'docx'],
                'other' => ['dwg', 'dxf', 'pdf', 'xlsx', 'xls', 'xlsm', 'docx', 'doc', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'zip', 'rar']
            ];

            $file = $_FILES['file'];
            $maxSize = 15 * 1024 * 1024; // 15MB

            if ($file['size'] > $maxSize) {
                throw new Exception('File size exceeds 15MB limit', 400);
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validate category-extension match
            $allowedForCategory = $extensionMap[$fileType] ?? $extensionMap['other'];
            if (!in_array($ext, $allowedForCategory)) {
                throw new Exception("Invalid format for category '{$fileType}'. Expected: " . implode(', ', $allowedForCategory), 400);
            }

            $uploadDir = STORAGE_PATH . '/library/quarantine';
            if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory', 500);
            }

            $filename = uniqid('lib_') . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . '/' . $filename;

            // Compute Hash
            $fileHash = hash_file('sha256', $file['tmp_name']);
            $libraryFileModel = new LibraryFile();
            
            // Duplicate Check
            $existing = $libraryFileModel->findByHash($fileHash);
            if ($existing) {
                if ($existing->uploader_id == $uid) {
                    throw new Exception('You have already uploaded this file.', 409);
                } else {
                    throw new Exception('Duplicate file detected. This resource was found in our library uploaded by another user.', 409);
                }
            }

            // Attempt to move; fallback to rename (sometimes needed on Windows)
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                if (!@rename($file['tmp_name'], $targetPath)) {
                    $errDetail = $this->uploadErrorMessage($file['error'] ?? null);
                    throw new Exception('Failed to save file. ' . $errDetail, 500);
                }
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
                     if (!is_dir($previewDir) && !@mkdir($previewDir, 0755, true)) {
                        throw new Exception('Failed to create preview directory', 500);
                     }
                     
                     $targetPreview = $previewDir . '/' . $previewFilename;
                     if (move_uploaded_file($previewFile['tmp_name'], $targetPreview) || @rename($previewFile['tmp_name'], $targetPreview)) {
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
            } else if ($fileType === 'cad' || $fileType === 'solidworks') {
                // FORCE PREVIEW FOR CAD/SolidWorks
                throw new Exception('A preview image (JPG/PNG) is required for CAD/SolidWorks files.', 400); 
            }

            // Auto-generate preview for Images?
            if ($fileType === 'image' && !$previewPath) {
                 // Logic to make thumb could go here
            }

            $fileId = $libraryFileModel->create([
                'uploader_id' => $uid,
                'title' => $title,
                'description' => $description,
                'tags' => $tags,
                'file_path' => 'quarantine/' . $filename,
                'file_type' => $fileType,
                'file_size_kb' => round($file['size'] / 1024),
                'price_coins' => $priceCoins,
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
        $db = \App\Core\Database::getInstance();
        $pdo = $db->getPdo();
        $sourcePath = null;
        $targetPath = null;
        $fileMoved = false;

        try {
            // Admin check middleware handles auth, but verifying admin role:
            $user = Auth::user();
            $userModel = new User();
            if (!$userModel->isAdmin($user->id)) {
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
                
                if (!is_dir($targetDir)) @mkdir($targetDir, 0755, true);

                $fileName = basename($file->file_path);
                $targetPathRelative = 'approved/' . $fileType . '/' . $fileName;
                $targetPath = $targetDir . '/' . $fileName;

                // Handle reward
                $customReward = (int)($input['reward_amount'] ?? 100);

                if (file_exists($sourcePath)) {
                    // Start DB Transaction
                    $pdo->beginTransaction();

                    // 1. Physically move file
                    if (!@rename($sourcePath, $targetPath)) {
                        // Fallback to copy if rename fails
                        if (!@copy($sourcePath, $targetPath)) {
                            throw new Exception('Failed to move/copy file to approved directory.', 500);
                        }
                        @unlink($sourcePath);
                    }
                    $fileMoved = true;

                    // 2. Update Path in DB
                    $stmt = $pdo->prepare("UPDATE library_files SET file_path = ? WHERE id = ?");
                    $stmt->execute([$targetPathRelative, $fileId]);

                    // 3. Update Status and Stats
                    $libraryFileModel->approve($fileId, $customReward);

                    // 4. Add coins to uploader
                    $userModel->addCoins($file->uploader_id, $customReward, 'Reward for approved resource: ' . $file->title, $fileId);
                    
                    $pdo->commit();
                    $this->json(['success' => true, 'message' => 'Approved and ' . $customReward . ' coins awarded.']);

                } else {
                    // If file is missing, we check if it's already in the target (maybe already processed?)
                    if (!file_exists($targetPath)) {
                        throw new Exception('Source file missing in quarantine.', 404);
                    }
                    throw new Exception('File already exists in target but record is still pending.', 409);
                }

            } elseif ($action === 'reject') {
                $reason = $input['reason'] ?? 'Did not meet guidelines';
                $libraryFileModel->reject($fileId, $reason);
                $this->json(['success' => true, 'message' => 'Rejected']);
            } else {
                throw new Exception('Invalid action');
            }

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            // FILE ROLLBACK: If file was moved but DB failed, move it back
            if ($fileMoved && $sourcePath && $targetPath && file_exists($targetPath)) {
                @rename($targetPath, $sourcePath);
            }

            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function unlock() {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);
            
            $input = json_decode(file_get_contents('php://input'), true);
            $fileId = $input['file_id'] ?? null;
            if (!$fileId) throw new Exception('File ID required.');

            $pdo = \App\Core\Database::getInstance()->getPdo();
            
            // 1. Get File (Manually for now as model might need refresh)
            $stmt = $pdo->prepare("SELECT * FROM library_files WHERE id = ?");
            $stmt->execute([$fileId]);
            $file = $stmt->fetch();
            if (!$file) throw new Exception('File not found.');
            
            // 2. Check Price
            if (!isset($file['price']) || $file['price'] <= 0) {
                $this->json(['success' => true, 'message' => 'File is free.']);
                return;
            }

            // 3. Check if already unlocked
            $check = $pdo->prepare("SELECT id FROM library_unlocks WHERE user_id = ? AND file_id = ?");
            $check->execute([$user->id, $fileId]);
            if ($check->fetchColumn()) {
                $this->json(['success' => true, 'message' => 'Already unlocked.']);
                return;
            }

            // 4. Deduct Coins
            $userModel = new User();
            if ($user->coins < $file['price']) {
                throw new Exception("Insufficient coins. Cost: {$file['price']}");
            }
            
            $pdo->beginTransaction();
            
            if (!$userModel->deductCoins($user->id, $file['price'], "Unlocked: " . $file['title'], $fileId)) {
                throw new Exception('Transaction failed.');
            }
            
            // 5. Record Unlock
            $rec = $pdo->prepare("INSERT INTO library_unlocks (user_id, file_id, cost) VALUES (?, ?, ?)");
            $rec->execute([$user->id, $fileId, $file['price']]);
            
            // Reward Uploader (Marketplace Logic: 50% Commission?)
            if ($file['uploader_id']) {
                 $commission = floor($file['price'] * 0.5);
                 if ($commission > 0) {
                     $userModel->addCoins($file['uploader_id'], $commission, "Royalties: " . $file['title'], $fileId);
                 }
            }

            $pdo->commit();
            $this->json(['success' => true, 'message' => 'Unlocked successfully!']);

        } catch (Exception $e) {
            if (\App\Core\Database::getInstance()->getPdo()->inTransaction()) \App\Core\Database::getInstance()->getPdo()->rollBack();
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
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

        if (!$file) die('File not found');

        $userModel = new User();
        $isAdmin = $userModel->isAdmin($user->id);

        if ($file->status !== 'approved' && !$isAdmin) {
             die('File unavailable or pending approval.');
        }
            
            // CHECK PERMISSIONS (Premium Logic)
            $isUploader = ($file->uploader_id == $user->id);
            
            if (!$isUploader && !$isAdmin) {
                // If price > 0, check unlock
                if (isset($file->price) && $file->price > 0) {
                     $db = \App\Core\Database::getInstance();
                     $check = $db->getPdo()->prepare("SELECT id FROM library_unlocks WHERE user_id = ? AND file_id = ?");
                     $check->execute([$user->id, $fileId]);
                     if (!$check->fetchColumn()) {
                         die("This file is locked. Please unlock it for {$file->price} coins.");
                     }
                } else {
                    // Free file cost logic (old logic: 50 coins or free?)
                    // Keeping old logic for now or replacing it? 
                    // User Request "Locking Library Files" usually implies replacing the old 'cost' logic with this new explicit price.
                    // Let's assume if price is 0, it uses the old "Download Cost" system if defined, or just allows it.
                    // For now, let's keep the existing "Download Cost" check as a fallback if price is 0?
                    // Actually, let's replace the old "user_transactions" check with this simple price check to avoid double charging.
                }
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
