<?php

namespace App\Console\Commands;

use App\Services\Quiz\DailyQuizService;

class GenerateDailyQuizzes
{
    private $service;

    public function __construct()
    {
        $this->service = new DailyQuizService();
    }

    /**
     * Execute the command
     */
    public function handle()
    {
        echo "===========================================\n";
        echo "Daily Quiz Generator\n";
        echo "===========================================\n\n";

        echo "[" . date('Y-m-d H:i:s') . "] Starting generation...\n";

        try {
            $result = $this->service->autoGenerateWeek();

            if ($result) {
                echo "[" . date('Y-m-d H:i:s') . "] ✅ SUCCESS: Generated daily quizzes for the week\n";

                // Show what was generated
                $this->showGeneratedQuizzes();

                return 0; // Success
            } else {
                echo "[" . date('Y-m-d H:i:s') . "] ❌ FAILED: Could not generate quizzes\n";
                echo "Check error logs for details.\n";
                return 1; // Failure
            }
        } catch (\Exception $e) {
            echo "[" . date('Y-m-d H:i:s') . "] ❌ ERROR: " . $e->getMessage() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
            return 1; // Failure
        }
    }

    /**
     * Display generated quizzes
     */
    private function showGeneratedQuizzes()
    {
        $db = \App\Core\Database::getInstance();
        $pdo = $db->getPdo();

        $stmt = $pdo->prepare("
            SELECT date, reward_coins, target_stream_id, target_edu_level_id
            FROM daily_quiz_schedule 
            WHERE date >= CURDATE() 
            ORDER BY date ASC 
            LIMIT 7
        ");
        $stmt->execute();
        $quizzes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($quizzes) {
            echo "\nGenerated Quizzes:\n";
            echo "-------------------------------------------\n";
            foreach ($quizzes as $quiz) {
                $target = 'Global';
                if ($quiz['target_stream_id']) $target = 'Course #' . $quiz['target_stream_id'];
                if ($quiz['target_edu_level_id']) $target .= ' / Level #' . $quiz['target_edu_level_id'];

                echo sprintf(
                    "  %s | %s | Reward: %d coins\n",
                    $quiz['date'],
                    $target,
                    $quiz['reward_coins']
                );
            }
            echo "-------------------------------------------\n";
        }
    }
}
