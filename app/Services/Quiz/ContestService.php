<?php

namespace App\Services\Quiz;

use App\Models\Contest;
use App\Models\ContestParticipant;
use App\Core\Database;

class ContestService
{
    protected $db;
    protected $contestModel;
    protected $participantModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->contestModel = new Contest();
        $this->participantModel = new ContestParticipant();
    }

    /**
     * Creates a Daily Contest automatically
     */
    public function autoCreateContest()
    {
        // 1. Config: Tomorrow 6 PM to 7 PM
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $startTime = $tomorrow . ' 18:00:00';
        $endTime   = $tomorrow . ' 19:00:00';

        // 2. Content Strategy
        // Pick 20 Random "Hard/Expert" questions (Level 4+)
        $pdo = $this->db->getPdo();
        $stmt = $pdo->prepare("SELECT id FROM quiz_questions WHERE difficulty_level >= 4 AND is_active = 1 ORDER BY RAND() LIMIT 20");
        $stmt->execute();
        $questions = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        if (count($questions) < 10) {
            // Log error or fallback if not enough hard questions
            return false;
        }

        // 3. Create the Event
        return $this->contestModel->create([
            'title' => 'Daily Mega Contest (' . date('M d', strtotime($tomorrow)) . ')',
            'description' => 'Compete with the best! Lucky Winner gets 500 Coins.',
            'start_time' => $startTime,
            'end_time' => $endTime,
            'entry_fee' => 10,
            'prize_pool' => 500,
            'winner_count' => 1, // FORCE LUCKY DRAW
            'is_automated' => 1,
            'questions' => json_encode($questions),
            'status' => 'upcoming'
        ]);
    }

    /**
     * JUDGE: The "Lucky Draw" Engine
     * Picks winner from the pool of top scorers
     */
    public function processResults($contestId)
    {
        $contest = $this->contestModel->find($contestId);
        if (!$contest || $contest['status'] === 'ended') return false;

        // 1. Find the Top Score
        $maxScore = $this->participantModel->getMaxScore($contestId);
        if ($maxScore <= 0) {
            $this->contestModel->update($contestId, ['status' => 'ended']);
            return false;
        }

        // 2. Get EVERYONE who got that Top Score
        $topScorers = $this->participantModel->where([
            'contest_id' => $contestId,
            'score' => $maxScore
        ]);

        if (empty($topScorers)) {
            $this->contestModel->update($contestId, ['status' => 'ended']);
            return false;
        }

        // 3. The Lucky Draw
        shuffle($topScorers);
        $winnerCount = (int)$contest['winner_count'];
        $winners = array_slice($topScorers, 0, min($winnerCount, count($topScorers)));

        $prizePerWinner = floor($contest['prize_pool'] / max(1, count($winners)));

        // 4. Distribute Prizes
        $pdo = $this->db->getPdo();
        foreach ($winners as $winner) {
            try {
                $pdo->beginTransaction();

                // Give User Coins
                $stmt = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
                $stmt->execute([$prizePerWinner, $winner['user_id']]);

                // Mark as Winner
                $this->participantModel->update($winner['id'], [
                    'is_winner' => 1,
                    'prize_awarded' => $prizePerWinner
                ]);

                // Record Transaction
                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, type, description, status, created_at) VALUES (?, ?, 'credit', ?, 'completed', NOW())");
                $stmt->execute([
                    $winner['user_id'],
                    $prizePerWinner,
                    "Won Contest: " . $contest['title']
                ]);

                // Notify User
                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, is_read, created_at) VALUES (?, ?, ?, 'contest_win', 0, NOW())");
                $stmt->execute([
                    $winner['user_id'],
                    "CONGRATULATIONS!",
                    "You won the Lucky Draw for " . $contest['title'] . "! " . $prizePerWinner . " coins added to your account."
                ]);

                $pdo->commit();
            } catch (\Exception $e) {
                $pdo->rollBack();
                // Log error
            }
        }

        // Mark Contest as Ended
        $this->contestModel->update($contestId, ['status' => 'ended']);
        return true;
    }
}
