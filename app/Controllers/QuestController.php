<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\QuestService;
use App\Services\ActivityLogger;

class QuestController extends Controller
{
    private $questService;
    private $logger;

    public function __construct()
    {
        parent::__construct();
        $this->questService = new QuestService();
        $this->logger = new ActivityLogger();
    }

    /**
     * AJAX: Record calculator use for Daily Quest
     */
    public function recordCalculation()
    {
        if (!$this->auth->check()) return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        
        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
        if (!$userId) return $this->json(['success' => false, 'message' => 'User ID not found'], 400);

        $toolId = $_POST['tool_id'] ?? ($_GET['tool_id'] ?? null);
        if (!$toolId) return $this->json(['success' => false, 'message' => 'Tool ID missing'], 400);

        // Log the activity first
        $this->logger->logAndReward($userId, 'TOOL_USED', $toolId, 5, 10, 100);

        // Check if this tool is the "Tool of the Day"
        $toolOfTheDay = $this->questService->getToolOfTheDay();
        if ($toolOfTheDay && $toolOfTheDay['id'] == $toolId) {
            $result = $this->questService->completeQuest($userId);
            return $this->json($result);
        }

        return $this->json(['success' => true, 'message' => 'Tool use recorded!']);
    }

    /**
     * AJAX: Process Read-to-Earn News
     */
    public function newsRead()
    {
        if (!$this->auth->check()) return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data || !isset($data['article_id'])) {
            return $this->json(['success' => false, 'message' => 'Invalid data'], 400);
        }

        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
        if (!$userId) return $this->json(['success' => false, 'message' => 'User ID not found'], 400);
        
        $result = $this->logger->logAndReward(
            $userId, 
            'NEWS_READ', 
            $data['article_id'], 
            10, // 10 Coins per handbook
            $data['time_spent'] ?? 0, 
            $data['scroll_depth'] ?? 0
        );

        return $this->json($result);
    }
}
