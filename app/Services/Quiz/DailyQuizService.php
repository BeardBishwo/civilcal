<?php
namespace App\Services\Quiz;

use App\Core\Database;

class DailyQuizService {
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * GENERATE NEXT 7 DAYS (Run this via Cron)
     */
    public function autoGenerateWeek() {
        // Get all Main Streams (Categories)
        $streams = $this->db->query("SELECT * FROM quiz_categories WHERE is_active = 1")->fetchAll();
        
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));
            
            // Generate for "General" (No stream)
            $this->createDailyQuiz($date, null);

            // Generate for Specific Streams
            foreach ($streams as $stream) {
                $this->createDailyQuiz($date, $stream['id']);
            }
        }
    }

    /**
     * The Recipe: 5 Easy, 3 Medium, 2 Hard
     */
    public function createDailyQuiz($date, $streamId) {
        $pdo = $this->db->getPdo();

        // 1. Prevent Duplicates
        $checkSql = "SELECT 1 FROM daily_quiz_schedule WHERE date = ? AND target_stream_id " . ($streamId ? "= ?" : "IS NULL");
        $params = $streamId ? [$date, $streamId] : [$date];
        
        $exists = $pdo->prepare($checkSql);
        $exists->execute($params);
        if ($exists->fetch()) return; // Already exists

        // 2. Fetch Questions (The Ladder Logic)
        $qEasy   = $this->getRandomQuestions($streamId, 1, 5); // 1 = Easy
        $qMedium = $this->getRandomQuestions($streamId, 3, 3); // 3 = Medium
        $qHard   = $this->getRandomQuestions($streamId, 5, 2); // 5 = Hard
        
        $allQuestions = array_merge($qEasy, $qMedium, $qHard);
        shuffle($allQuestions); // Randomize order

        if (count($allQuestions) < 5) { // Lower threshold for testing
            // Not enough questions? Maybe try fallback or skip
            // For now, we skip to avoid broken quizzes
            return; 
        }

        // 3. Save to Database
        $stmt = $pdo->prepare("INSERT INTO daily_quiz_schedule (date, target_stream_id, questions, reward_coins, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            $date,
            $streamId,
            json_encode($allQuestions),
            50 // Base Reward
        ]);
    }

    /**
     * Helper to fetch random questions by difficulty
     */
    private function getRandomQuestions($streamId, $difficulty, $limit) {
        $pdo = $this->db->getPdo();
        
        $sql = "SELECT id FROM quiz_questions WHERE difficulty_level = ?";
        $params = [$difficulty];

        if ($streamId) {
            $sql .= " AND category_id = ?";
            $params[] = $streamId;
        }

        $sql .= " ORDER BY RAND() LIMIT " . intval($limit);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Fetch the Quiz for a specific date and stream
     */
    public function getQuizForUser($date, $userStreamId = null) {
        $pdo = $this->db->getPdo();

        // Try getting stream-specific quiz first
        if ($userStreamId) {
            $stmt = $pdo->prepare("SELECT * FROM daily_quiz_schedule WHERE date = ? AND target_stream_id = ?");
            $stmt->execute([$date, $userStreamId]);
            $quiz = $stmt->fetch();
            if ($quiz) return $quiz;
        }

        // Fallback to General Quiz
        $stmt = $pdo->prepare("SELECT * FROM daily_quiz_schedule WHERE date = ? AND target_stream_id IS NULL");
        $stmt->execute([$date]);
        return $stmt->fetch();
    }
    /**
     * Check if user already attempted today's quiz
     */
    public function checkAttempt($userId, $date) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT a.* 
            FROM daily_quiz_attempts a
            JOIN daily_quiz_schedule s ON a.daily_quiz_id = s.id
            WHERE a.user_id = ? AND s.date = ?
        ");
        $stmt->execute([$userId, $date]);
        return $stmt->fetch();
    }

    /**
     * Record a completed attempt
     */
    public function recordAttempt($userId, $dailyQuizId, $score, $coinsEarned) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO daily_quiz_attempts (user_id, daily_quiz_id, score, coins_earned, completed_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $dailyQuizId, $score, $coinsEarned]);
    }
}
