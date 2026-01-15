<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;

class MathManiaController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Entry point for Math Mania
     */
    public function index()
    {
        // Initialize user balance or stats if needed
        $user = $_SESSION['user'];

        // Render the view
        $this->view->render('quiz/games/math_mania', [
            'page_title' => 'Math Mania',
            'user' => $user
        ], 'layouts/quiz_focus'); // Using focus layout for immersive game
    }

    /**
     * Submit Math Mania High Score (API)
     */
    public function submitScore()
    {
        $score = (int)($_POST['score'] ?? 0);
        $level = (int)($_POST['level'] ?? 1);

        // Basic validation/anti-cheat placeholder
        if ($score < 0) $score = 0;

        // Save to leaderboard or activity log (simplified for now)
        // \App\Services\LeaderboardService::update('math_mania', $_SESSION['user_id'], $score);

        // Return success
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Score recorded!']);
        exit;
    }
}
