<?php

namespace App\Services\Quiz;

use App\Core\Database;

class DailyQuizService
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * GENERATE NEXT 7 DAYS (Run this via Cron)
     */
    public function autoGenerateWeek()
    {
        $pdo = $this->db->getPdo();

        // Get all Main Courses
        $courses = $this->db->query("SELECT * FROM syllabus_nodes WHERE type = 'course' AND is_active = 1")->fetchAll();

        // Get all Education Levels
        $eduLevels = $this->db->query("SELECT * FROM syllabus_nodes WHERE type = 'education_level' AND is_active = 1")->fetchAll();

        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));

            // 1. Generate "Mixed/Global" Quiz (Default fallback)
            $this->createDailyQuiz($date, null, null);

            // 2. Generate for Specific Courses
            foreach ($courses as $course) {
                $this->createDailyQuiz($date, $course['id'], null);
            }

            // 3. Generate for Specific Education Levels
            foreach ($eduLevels as $edu) {
                $this->createDailyQuiz($date, null, $edu['id']);
            }

            // 4. Generate for Course + Education Combinations
            foreach ($courses as $course) {
                foreach ($eduLevels as $edu) {
                    $this->createDailyQuiz($date, $course['id'], $edu['id']);
                }
            }
        }
    }

    /**
     * Create a balanced 10-question quiz
     */
    public function createDailyQuiz($date, $courseId, $eduLevelId)
    {
        $pdo = $this->db->getPdo();

        // Prevent Duplicates
        $checkSql = "SELECT 1 FROM daily_quiz_schedule WHERE date = ? AND target_stream_id " . ($courseId ? "= ?" : "IS NULL") . " AND target_edu_level_id " . ($eduLevelId ? "= ?" : "IS NULL");
        $params = [$date];
        if ($courseId) $params[] = $courseId;
        if ($eduLevelId) $params[] = $eduLevelId;

        $exists = $pdo->prepare($checkSql);
        $exists->execute($params);
        if ($exists->fetch()) return;

        // Fetch Questions suivant the "Ladder Logic"
        $qEasy     = $this->getRandomQuestions($courseId, $eduLevelId, 1, 3);
        $qEasyMid  = $this->getRandomQuestions($courseId, $eduLevelId, 2, 2);
        $qMedium   = $this->getRandomQuestions($courseId, $eduLevelId, 3, 2);
        $qHard     = $this->getRandomQuestions($courseId, $eduLevelId, 4, 2);
        $qExpert   = $this->getRandomQuestions($courseId, $eduLevelId, 5, 1);

        $allQuestions = array_merge($qEasy, $qEasyMid, $qMedium, $qHard, $qExpert);
        shuffle($allQuestions);

        // Required 10 questions for Course/Edu quizzes, otherwise fallback logic handles it
        if (count($allQuestions) < 10 && ($courseId !== null || $eduLevelId !== null)) {
            return;
        }

        if (count($allQuestions) < 5) return; // Final safety for Mixed quiz

        // Save to Database
        $stmt = $pdo->prepare("INSERT INTO daily_quiz_schedule (date, target_stream_id, target_edu_level_id, questions, reward_coins, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $date,
            $courseId,
            $eduLevelId,
            json_encode($allQuestions),
            50
        ]);
    }

    /**
     * Helper to fetch random questions by difficulty and context
     */
    private function getRandomQuestions($courseId, $eduLevelId, $difficulty, $limit, $studyMode = 'psc')
    {
        $pdo = $this->db->getPdo();

        $sql = "SELECT id FROM quiz_questions WHERE difficulty_level = ? AND status = 'approved'";
        $params = [$difficulty];

        // Filter by Course
        if ($courseId) {
            $sql .= " AND course_id = ?";
            $params[] = $courseId;
        }

        // Filter by Education Level
        if ($eduLevelId) {
            $sql .= " AND edu_level_id = ?";
            $params[] = $eduLevelId;
        }

        // Filter by Study Mode
        if ($studyMode === 'world') {
            $sql .= " AND (target_audience = 'world_only' OR target_audience = 'universal')";
        } else {
            $sql .= " AND (target_audience = 'psc_only' OR target_audience = 'universal')";
        }

        $sql .= " ORDER BY RAND() LIMIT " . intval($limit);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Fetch the Quiz for a specific date and user context
     */
    public function getQuizForUser($date, $userCourseId = null, $userEduLevelId = null)
    {
        $pdo = $this->db->getPdo();

        // 1. Priority: Exact Course + Education Match
        if ($userCourseId && $userEduLevelId) {
            $stmt = $pdo->prepare("SELECT * FROM daily_quiz_schedule WHERE date = ? AND target_stream_id = ? AND target_edu_level_id = ?");
            $stmt->execute([$date, $userCourseId, $userEduLevelId]);
            $quiz = $stmt->fetch();
            if ($quiz) return $quiz;
        }

        // 2. Secondary: Course Only Match
        if ($userCourseId) {
            $stmt = $pdo->prepare("SELECT * FROM daily_quiz_schedule WHERE date = ? AND target_stream_id = ? AND target_edu_level_id IS NULL");
            $stmt->execute([$date, $userCourseId]);
            $quiz = $stmt->fetch();
            if ($quiz) return $quiz;
        }

        // 3. Tertiary: Education Level Only Match
        if ($userEduLevelId) {
            $stmt = $pdo->prepare("SELECT * FROM daily_quiz_schedule WHERE date = ? AND target_stream_id IS NULL AND target_edu_level_id = ?");
            $stmt->execute([$date, $userEduLevelId]);
            $quiz = $stmt->fetch();
            if ($quiz) return $quiz;
        }

        // 4. Fallback: Global Mixed Quiz
        $stmt = $pdo->prepare("SELECT * FROM daily_quiz_schedule WHERE date = ? AND target_stream_id IS NULL AND target_edu_level_id IS NULL");
        $stmt->execute([$date]);
        return $stmt->fetch();
    }
    /**
     * Check if user already attempted today's quiz
     */
    public function checkAttempt($userId, $date)
    {
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
    public function recordAttempt($userId, $dailyQuizId, $score, $coinsEarned)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO daily_quiz_attempts (user_id, daily_quiz_id, score, coins_earned, completed_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $dailyQuizId, $score, $coinsEarned]);
    }
}
