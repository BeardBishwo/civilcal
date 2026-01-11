<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Notification;
use App\Models\LibraryFile;
use App\Models\User;
use Exception;

class HumanApiController extends Controller
{
    public function getNotifications()
    {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);

            $notificationModel = new Notification();
            $notifications = $notificationModel->getUnread($user->id);

            $this->json(['success' => true, 'notifications' => $notifications]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 401);
        }
    }

    public function markNotificationRead()
    {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);

            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            $all = $input['all'] ?? false;

            $notificationModel = new Notification();
            
            if ($all) {
                $notificationModel->markAllAsRead($user->id);
            } elseif ($id) {
                $notificationModel->markAsRead($id, $user->id);
            }

            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function submitReview()
    {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);

            $input = json_decode(file_get_contents('php://input'), true);
            $fileId = $input['file_id'] ?? null;
            $rating = intval($input['rating'] ?? 0);
            $comment = trim($input['comment'] ?? '');

            if (!$fileId || $rating < 1 || $rating > 5) {
                throw new Exception('Invalid Data', 400);
            }

            $libraryModel = new LibraryFile();
            
            // SECURITY: Prevent fake reviews. User must have unlocked/downloaded the file.
            $db = \App\Core\Database::getInstance();
            $file = $libraryModel->find($fileId);
            if (!$file) throw new Exception('File not found', 404);

            $hasAccess = false;
            if ($user->id == $file->uploader_id) {
                $hasAccess = true; // Uploader can review their own (or we could block this too, but for now allow)
            } else {
                // Check library_unlocks
                $stmtAccess = $db->getPdo()->prepare("SELECT id FROM library_unlocks WHERE user_id = ? AND file_id = ?");
                $stmtAccess->execute([$user->id, $fileId]);
                if ($stmtAccess->fetchColumn()) {
                    $hasAccess = true;
                } else {
                    // Check user_transactions for free/legacy downloads
                    $stmtTrans = $db->getPdo()->prepare("SELECT id FROM user_transactions WHERE user_id = ? AND reference_id = ? AND type = 'download_cost'");
                    $stmtTrans->execute([$user->id, $fileId]);
                    if ($stmtTrans->fetchColumn()) {
                        $hasAccess = true;
                    }
                }
            }

            if (!$hasAccess) {
                throw new Exception('You must download or unlock this file before you can review it.', 403);
            }
            
            $libraryModel->addReview($fileId, $user->id, $rating, $comment);
            
            $this->json(['success' => true, 'message' => 'Review Submitted!']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function submitReport()
    {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);

            $input = json_decode(file_get_contents('php://input'), true);
            $fileId = $input['file_id'] ?? null;
            $reason = trim($input['reason'] ?? '');

            if (!$fileId || empty($reason)) throw new Exception('Reason required', 400);

            $libraryModel = new LibraryFile();
            $result = $libraryModel->report($fileId, $user->id, $reason);

            if ($result) {
                 // Notify All Admins
                 $db = \App\Core\Database::getInstance();
                 $adminStmt = $db->getPdo()->query("SELECT id FROM users WHERE role = 'admin'");
                 $admins = $adminStmt->fetchAll(\PDO::FETCH_COLUMN);

                 $notificationModel = new Notification();
                 foreach ($admins as $adminId) {
                     $notificationModel->create($adminId, "Flagged Content", "New Report on File #$fileId: $reason", "/admin/library/pending");
                 }
            }

            $this->json(['success' => true, 'message' => 'Report received. Admins will review.']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
