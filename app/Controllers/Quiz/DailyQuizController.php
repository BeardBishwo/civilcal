<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Services\Quiz\DailyQuizService;
use App\Services\Quiz\StreakService;

class DailyQuizController extends Controller
{
    private $dailyQuizService;
    private $streakService;

    public function __construct()
    {
        parent::__construct();

        // Require authentication
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/quiz/daily/start';
            header('Location: /login');
            exit;
        }

        $this->dailyQuizService = new DailyQuizService();
        $this->streakService = new StreakService();
    }

    /**
     * Start today's daily quiz
     */
    public function start()
    {
        $today = date('Y-m-d');
        $userId = $_SESSION['user_id'];

        // Check if already attempted today
        $existingAttempt = $this->dailyQuizService->getUserAttemptForDate($userId, $today);

        if ($existingAttempt) {
            $_SESSION['flash_info'] = 'You have already completed today\'s daily quiz!';
            return $this->redirect('/quiz/daily/result/' . $existingAttempt['id']);
        }

        // Get quiz for user (with fallback logic)
        $quiz = $this->dailyQuizService->getQuizForUser($userId, $today);

        if (!$quiz) {
            $_SESSION['flash_error'] = 'No daily quiz available today. Please check back later.';
            return $this->redirect('/portal');
        }

        // Create exam attempt
        $db = \App\Core\Database::getInstance();
        $pdo = $db->getPdo();

        $stmt = $pdo->prepare("
            INSERT INTO quiz_attempts (user_id, exam_id, status, created_at) 
            VALUES (?, ?, 'ongoing', NOW())
        ");
        $stmt->execute([$userId, $quiz['exam_id']]);
        $attemptId = $pdo->lastInsertId();

        // Mark this as a daily quiz attempt
        $stmt = $pdo->prepare("
            INSERT INTO daily_quiz_attempts (user_id, schedule_id, attempt_id, score, coins_earned, created_at)
            VALUES (?, ?, ?, 0, 0, NOW())
        ");
        $stmt->execute([$userId, $quiz['id'], $attemptId]);

        // Forward to exam engine
        return $this->redirect('/quiz/room/' . $attemptId);
    }

    /**
     * Show daily quiz history
     */
    public function history()
    {
        $userId = $_SESSION['user_id'];
        $attempts = $this->dailyQuizService->getUserHistory($userId);
        $streakInfo = $this->streakService->getStreakInfo($userId);

        $this->view('quiz/daily/history', [
            'attempts' => $attempts,
            'streakInfo' => $streakInfo,
            'title' => 'Daily Quiz History',
            'user' => \App\Core\Auth::user()
        ]);
    }

    /**
     * Show daily quiz result (with streak info)
     */
    public function result($attemptId)
    {
        $userId = $_SESSION['user_id'];

        // Verify ownership
        $db = \App\Core\Database::getInstance();
        $pdo = $db->getPdo();

        $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE id = ? AND user_id = ?");
        $stmt->execute([$attemptId, $userId]);
        $attempt = $stmt->fetch();

        if (!$attempt) {
            $_SESSION['flash_error'] = 'Attempt not found.';
            return $this->redirect('/portal');
        }

        // Get streak info if available
        $streakInfo = $_SESSION['latest_streak_info'] ?? null;
        unset($_SESSION['latest_streak_info']);

        // Forward to regular result page with streak info
        $_SESSION['show_streak_info'] = $streakInfo;
        return $this->redirect('/quiz/result/' . $attemptId);
    }
}
