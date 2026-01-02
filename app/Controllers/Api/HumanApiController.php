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
            // TODO: Check if user actually downloaded the file via transaction log or downloads table
            // For now, assuming UI logic is strict enough or we update check logic later
            
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
                 // Notify Admin (optional impl)
                 $notificationModel = new Notification();
                 // Assuming Admin ID 1 exists or similar logic
                 // $notificationModel->create(1, "New Report on File #$fileId");
            }

            $this->json(['success' => true, 'message' => 'Report received. Admins will review.']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
