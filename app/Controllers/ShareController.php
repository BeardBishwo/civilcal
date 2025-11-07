<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Share;
use App\Models\CalculationHistory;
use Exception;

/**
 * Share Controller
 * Handles public/private sharing of calculations with unique tokens
 */
class ShareController extends Controller
{
    private $shareModel;
    
    public function __construct() {
        parent::__construct();
        $this->shareModel = new Share();
    }
    
    /**
     * Create a new share for a calculation
     * POST /shares/create
     */
    public function create() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $calculationId = $input['calculation_id'] ?? null;
        $isPublic = filter_var($input['is_public'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $expiryDate = $input['expiry_date'] ?? null;
        
        if (!$calculationId) {
            $this->json(['success' => false, 'message' => 'Calculation ID is required'], 400);
            return;
        }
        
        // Verify the calculation belongs to the current user
        $historyModel = new CalculationHistory();
        $calculation = $historyModel->getCalculationById($calculationId, $this->getUser()['id']);
        
        if (!$calculation) {
            $this->json(['success' => false, 'message' => 'Calculation not found or access denied'], 404);
            return;
        }
        
        // Create the share
        try {
            $share = $this->shareModel->createShare(
                $calculationId,
                $this->getUser()['id'],
                $isPublic,
                $title,
                $description,
                $expiryDate
            );
            
            if ($share) {
                $shareUrl = $this->generateShareUrl($share['token']);
                $embedCode = $this->generateEmbedCode($share['token']);
                
                $this->json([
                    'success' => true,
                    'message' => 'Share created successfully',
                    'data' => [
                        'share_id' => $share['id'],
                        'token' => $share['token'],
                        'share_url' => $shareUrl,
                        'embed_code' => $embedCode,
                        'is_public' => $share['is_public'],
                        'view_count' => $share['view_count'],
                        'created_at' => $share['created_at']
                    ]
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to create share'], 500);
            }
        } catch (Exception $e) {
            error_log("Share creation error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while creating the share'], 500);
        }
    }
    
    /**
     * View a shared calculation (public access)
     * GET /shares/view/{token}
     */
    public function publicView($token) {
        try {
            $share = $this->shareModel->getShareByToken($token);
            
            if (!$share) {
                http_response_code(404);
                $this->view('errors/404', [
                    'title' => 'Share Not Found',
                    'message' => 'The shared calculation you are looking for does not exist or has been removed.'
                ]);
                return;
            }
            
            // Check if share has expired
            if ($share['expires_at'] && strtotime($share['expires_at']) < time()) {
                http_response_code(410);
                $this->view('errors/410', [
                    'title' => 'Share Expired',
                    'message' => 'This shared calculation has expired and is no longer available.'
                ]);
                return;
            }
            
            // Increment view count
            $this->shareModel->incrementViewCount($token);
            
            // Get the calculation data
            $historyModel = new CalculationHistory();
            $calculation = $historyModel->getCalculationById($share['calculation_id'], null, true); // Allow public access
            
            if (!$calculation) {
                http_response_code(500);
                $this->view('errors/500', [
                    'title' => 'Calculation Error',
                    'message' => 'An error occurred while loading the calculation data.'
                ]);
                return;
            }
            
            // Prepare data for public view
            $data = [
                'title' => $share['title'] ?: 'Shared Calculation - ' . ucfirst(str_replace('_', ' ', $calculation['calculator_type'])),
                'share' => $share,
                'calculation' => $calculation,
                'view_count' => $share['view_count'] + 1,
                'is_owner' => $this->isAuthenticated() && $this->getUser()['id'] == $share['user_id'],
                'show_comments' => true,
                'share_url' => $this->generateShareUrl($token),
                'embed_code' => $this->generateEmbedCode($token)
            ];
            
            // Set page metadata
            $this->setCategory('share');
            $this->setTitle($data['title']);
            $this->setDescription($share['description'] ?: 'View this shared engineering calculation');
            $this->setKeywords('calculator, engineering, calculation, share, ' . $calculation['calculator_type']);
            
            $this->view('share/public-view', $data);
            
        } catch (Exception $e) {
            error_log("Share view error: " . $e->getMessage());
            http_response_code(500);
            $this->view('errors/500', [
                'title' => 'Server Error',
                'message' => 'An error occurred while processing your request.'
            ]);
        }
    }
    
    /**
     * Get user's shares
     * GET /shares/my-shares
     */
    public function myShares() {
        $this->requireAuth();
        
        try {
            $shares = $this->shareModel->getSharesByUser($this->getUser()['id']);
            
            $data = [
                'title' => 'My Shares',
                'shares' => $shares,
                'total_shares' => count($shares),
                'active_shares' => count(array_filter($shares, function($share) {
                    return $share['is_public'] && (!$share['expires_at'] || strtotime($share['expires_at']) > time());
                })),
                'total_views' => array_sum(array_column($shares, 'view_count'))
            ];
            
            $this->setCategory('share');
            $this->setTitle('My Shares - ' . $data['title']);
            
            $this->view('share/my-shares', $data);
            
        } catch (Exception $e) {
            error_log("My shares error: " . $e->getMessage());
            $this->redirect('/dashboard?error=Failed to load shares');
        }
    }
    
    /**
     * Update share settings
     * POST /shares/{id}/update
     */
    public function update($shareId) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $share = $this->shareModel->getShareById($shareId, $this->getUser()['id']);
            
            if (!$share) {
                $this->json(['success' => false, 'message' => 'Share not found or access denied'], 404);
                return;
            }
            
            $updates = [];
            
            if (isset($input['title'])) {
                $updates['title'] = trim($input['title']);
            }
            
            if (isset($input['description'])) {
                $updates['description'] = trim($input['description']);
            }
            
            if (isset($input['is_public'])) {
                $updates['is_public'] = filter_var($input['is_public'], FILTER_VALIDATE_BOOLEAN);
            }
            
            if (isset($input['expiry_date'])) {
                $updates['expires_at'] = $input['expiry_date'] ?: null;
            }
            
            if (isset($input['password'])) {
                $updates['password'] = $input['password'] ?: null;
            }
            
            if (empty($updates)) {
                $this->json(['success' => false, 'message' => 'No valid updates provided'], 400);
                return;
            }
            
            $success = $this->shareModel->updateShare($shareId, $updates);
            
            if ($success) {
                $this->json([
                    'success' => true,
                    'message' => 'Share updated successfully',
                    'data' => array_merge($share, $updates)
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to update share'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Share update error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while updating the share'], 500);
        }
    }
    
    /**
     * Delete a share
     * DELETE /shares/{id}/delete
     */
    public function delete($shareId) {
        $this->requireAuth();
        
        try {
            $success = $this->shareModel->deleteShare($shareId, $this->getUser()['id']);
            
            if ($success) {
                if ($this->isAjaxRequest()) {
                    $this->json(['success' => true, 'message' => 'Share deleted successfully']);
                } else {
                    $this->redirect('/shares/my-shares?success=Share deleted successfully');
                }
            } else {
                if ($this->isAjaxRequest()) {
                    $this->json(['success' => false, 'message' => 'Failed to delete share'], 500);
                } else {
                    $this->redirect('/shares/my-shares?error=Failed to delete share');
                }
            }
            
        } catch (Exception $e) {
            error_log("Share delete error: " . $e->getMessage());
            
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'message' => 'An error occurred while deleting the share'], 500);
            } else {
                $this->redirect('/shares/my-shares?error=Failed to delete share');
            }
        }
    }
    
    /**
     * Generate embed code for a share
     * GET /shares/{id}/embed
     */
    public function embed($shareId) {
        $this->requireAuth();
        
        try {
            $share = $this->shareModel->getShareById($shareId, $this->getUser()['id']);
            
            if (!$share) {
                $this->json(['success' => false, 'message' => 'Share not found'], 404);
                return;
            }
            
            if (!$share['is_public']) {
                $this->json(['success' => false, 'message' => 'Cannot generate embed code for private shares'], 400);
                return;
            }
            
            $embedCode = $this->generateEmbedCode($share['token']);
            $shareUrl = $this->generateShareUrl($share['token']);
            
            $this->json([
                'success' => true,
                'data' => [
                    'embed_code' => $embedCode,
                    'share_url' => $shareUrl,
                    'preview' => $this->getEmbedPreview($share['token'])
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Embed generation error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to generate embed code'], 500);
        }
    }
    
    /**
     * Get share statistics
     * GET /shares/{id}/stats
     */
    public function stats($shareId) {
        $this->requireAuth();
        
        try {
            $share = $this->shareModel->getShareById($shareId, $this->getUser()['id']);
            
            if (!$share) {
                $this->json(['success' => false, 'message' => 'Share not found'], 404);
                return;
            }
            
            $stats = $this->shareModel->getShareStats($shareId, $this->getUser()['id']);
            
            $this->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (Exception $e) {
            error_log("Share stats error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to load share statistics'], 500);
        }
    }
    
    /**
     * Generate share URL
     */
    private function generateShareUrl($token) {
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/shares/view/' . $token;
    }
    
    /**
     * Generate embed code
     */
    private function generateEmbedCode($token) {
        $shareUrl = $this->generateShareUrl($token);
        return '<iframe src="' . htmlspecialchars($shareUrl) . '" width="100%" height="600" frameborder="0" title="Bishwo Calculator - Shared Calculation"></iframe>';
    }
    
    /**
     * Generate embed preview HTML
     */
    private function getEmbedPreview($token) {
        $shareUrl = $this->generateShareUrl($token);
        return '<div class="embed-preview"><iframe src="' . htmlspecialchars($shareUrl) . '" width="100%" height="400" frameborder="0"></iframe><p>Embed Preview</p></div>';
    }
    
    /**
     * Check if request is AJAX
     */
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
?>
