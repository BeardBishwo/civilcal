<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\BountyRequest;
use App\Models\BountySubmission;
use App\Models\User;
use App\Services\FileService;
use Exception;

class BountyApiController extends Controller
{
    public function browse()
    {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;

            $bountyModel = new BountyRequest();
            $bounties = $bountyModel->getOpenBounties($limit, $offset);

            $this->json(['success' => true, 'bounties' => $bounties]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);

            $input = json_decode(file_get_contents('php://input'), true);
            $title = trim($input['title'] ?? '');
            $description = trim($input['description'] ?? '');
            $amount = intval($input['amount'] ?? 0);

            if (empty($title) || $amount <= 0) {
                throw new Exception('Invalid Title or Amount', 400);
            }

            $userModel = new User();
            if ($userModel->getCoins($user->id) < $amount) {
                throw new Exception('Insufficient Coins', 400);
            }

            $this->db->beginTransaction();

            // Lock Coins (Deduct immediately)
            if (!$userModel->deductCoins($user->id, $amount, "Created Bounty: $title")) {
                throw new Exception('Failed to process coin deduction', 500);
            }

            $bountyModel = new BountyRequest();
            $id = $bountyModel->create([
                'requester_id' => $user->id,
                'title' => $title,
                'description' => $description,
                'bounty_amount' => $amount
            ]);

            $this->db->commit();
            $this->json(['success' => true, 'message' => 'Bounty Posted!', 'id' => $id]);
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $this->json(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function submit()
    {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);

            $bountyId = $_POST['bounty_id'] ?? null;
            if (!$bountyId) throw new Exception('Bounty ID required', 400);

            if (!isset($_FILES['file'])) throw new Exception('File required', 400);

            // Verify file logic
            $file = $_FILES['file'];
            // Use FileService for secure Bounty submission (Binary Scanning + Entropy Filenames)
            $upload = FileService::uploadUserFile($file, $user->id, 'bounty_file');

            if (!$upload['success']) {
                throw new Exception($upload['error'] ?? 'Upload failed', 400);
            }

            $targetPath = $upload['path'];
            $filename = $upload['filename'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            // Compute Hash for logical integrity and duplicate check
            $fileHash = hash_file('sha256', $targetPath);
            $submissionModel = new BountySubmission();

            // Duplicate Check
            $existing = $submissionModel->findByHash($fileHash);
            if ($existing) {
                // Delete the file we just uploaded since it's a duplicate
                @unlink($targetPath);

                if ($existing->uploader_id == $user->id) {
                    throw new Exception('You have already submitted this file.', 409);
                } else {
                    throw new Exception('Duplicate file detected. This work has already been submitted by another user.', 409);
                }
            }

            // Watermark / Preview Generation
            $previewPath = null;
            $watermarkService = new \App\Services\WatermarkService();
            $previewDir = STORAGE_PATH . '/public/previews'; // Maps to public/previews via symlink or simple serving?
            // Actually, usually public access needs to be in public/ folder.
            // Let's rely on copying to public/previews for the "dirty" file which is intended to be public.
            $publicPreviewDir = __DIR__ . '/../../../public/previews'; // Assuming app/Controllers/Api/../../../public
            if (!is_dir($publicPreviewDir)) mkdir($publicPreviewDir, 0755, true);

            $previewFilename = 'preview_' . uniqid() . '.jpg';
            $previewTarget = $publicPreviewDir . '/' . $previewFilename;
            $previewGenerated = false;

            // Scenario A: Image or PDF -> Auto Generate
            if (in_array($ext, ['jpg', 'png', 'pdf'])) {
                try {
                    $previewGenerated = $watermarkService->createDirtyPreview($targetPath, $previewTarget);
                } catch (Exception $e) {
                    error_log("Watermark Auto-Gen Failed: " . $e->getMessage());
                }
            }

            // Scenario B: CAD/Excel -> Check for Screenshot Upload
            if (!$previewGenerated && isset($_FILES['preview_file']) && $_FILES['preview_file']['error'] === UPLOAD_ERR_OK) {
                // User provided screenshot
                $ssFile = $_FILES['preview_file'];
                $ssExt = strtolower(pathinfo($ssFile['name'], PATHINFO_EXTENSION));
                if (in_array($ssExt, ['jpg', 'png'])) {
                    try {
                        // Pass tmp_name directly to watermark service
                        $previewGenerated = $watermarkService->createDirtyPreview($ssFile['tmp_name'], $previewTarget);
                    } catch (Exception $e) {
                        error_log("Watermark Screenshot Failed: " . $e->getMessage());
                    }
                }
            }

            if ($previewGenerated) {
                $previewPath = 'previews/' . $previewFilename; // Relative to public root
            }

            // Re-instantiate model (should be stateless but good practice)
            // Or just use the one created earlier
            $submissionModel->create([
                'bounty_id' => $bountyId,
                'uploader_id' => $user->id,
                'file_path' => 'quarantine/' . $filename,
                'preview_path' => $previewPath,
                'file_hash' => $fileHash
            ]);

            $this->json(['success' => true, 'message' => 'Submission received! Pending review.']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function clientDecide()
    {
        try {
            $user = Auth::user();
            $input = json_decode(file_get_contents('php://input'), true);
            $submissionId = $input['submission_id'] ?? null;
            $decision = $input['decision'] ?? null; // 'accept' or 'reject'

            if (!$user || !$submissionId || !in_array($decision, ['accept', 'reject'])) {
                throw new Exception('Invalid Request');
            }

            $submissionModel = new BountySubmission();
            $submission = $submissionModel->find($submissionId);
            $bountyModel = new BountyRequest();
            $bounty = $bountyModel->find($submission->bounty_id);

            // Verify Ownership
            if ($bounty->requester_id != $user->id) {
                throw new Exception('You are not the bounty owner', 403);
            }

            if ($decision === 'accept') {
                // Verify Bounty is still open
                if ($bounty->status !== 'open') throw new Exception('Bounty already closed/filled');

                $this->db->beginTransaction();

                // 1. Release Coins to Engineer
                $userModel = new User();
                $userModel->addCoins($submission->uploader_id, $bounty->bounty_amount, "Bounty Reward: $bounty->title", $bounty->id);

                // 2. Mark Submission Accepted
                $submissionModel->updateClientStatus($submissionId, 'accepted');

                // 3. Close Bounty
                $bountyModel->updateStatus($bounty->id, 'filled');

                $this->db->commit();

                $this->json(['success' => true, 'message' => 'Accepted! Payment Released.']);
            } else {
                $reason = $input['reason'] ?? 'Client rejected';
                $submissionModel->updateClientStatus($submissionId, 'rejected', $reason);
                $this->json(['success' => true, 'message' => 'Submission Rejected.']);
            }
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
