<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Services\LifelineService;

/**
 * LifelineController - AJAX Endpoints for In-Quiz Lifelines
 */
class LifelineController extends Controller
{
    private $lifelineService;

    public function __construct()
    {
        parent::__construct();
        $this->lifelineService = new LifelineService();
    }

    /**
     * AJAX: Activate a lifeline
     */
    public function use()
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Please login to use lifelines.']);
        }

        $type = $_POST['type'] ?? '';
        $questionId = $_POST['question_id'] ?? null;
        $userId = $_SESSION['user_id'];

        try {
            $result = $this->lifelineService->useInQuiz($userId, $type, $questionId);
            return $this->json($result);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
