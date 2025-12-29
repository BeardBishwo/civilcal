<?php

namespace App\Services;

class RankService
{
    /**
     * Get Rank and Power Metrics for a user
     */
    public function getUserRankData($stats, $coins)
    {
        $newsReads = $stats['news_reads_count'] ?? 0;
        $quizzes = $stats['quizzes_completed_count'] ?? 0;
        $calcs = $stats['calculations_count'] ?? 0;

        // Calculate segment scores
        $knowledgeScore = ($newsReads * 10) + ($quizzes * 50);
        $precisionScore = ($calcs * 5);
        $statusScore = (int)($coins * 0.1);

        $totalPower = $knowledgeScore + $precisionScore + $statusScore;

        $rank = $this->calculateRank($totalPower);
        
        return [
            'rank' => $rank['name'],
            'rank_level' => $rank['level'],
            'total_power' => $totalPower,
            'next_rank' => $rank['next_name'],
            'next_rank_power' => $rank['next_power'],
            'rank_progress' => $rank['progress'],
            'meters' => [
                'knowledge' => min(100, round(($knowledgeScore / 1000) * 100)), // Goal: 1000 pts
                'precision' => min(100, round(($precisionScore / 5000) * 100)), // Goal: 5000 pts
                'status' => $rank['progress']
            ]
        ];
    }

    private function calculateRank($power)
    {
        $tiers = [
            ['name' => 'Intern', 'level' => 1, 'min' => 0],
            ['name' => 'Assistant Engineer', 'level' => 2, 'min' => 1000],
            ['name' => 'Engineer', 'level' => 3, 'min' => 5000],
            ['name' => 'Senior Engineer', 'level' => 4, 'min' => 20000],
            ['name' => 'Chief Engineer', 'level' => 5, 'min' => 100000],
        ];

        $currentTier = $tiers[0];
        $nextTier = $tiers[1];

        foreach ($tiers as $index => $tier) {
            if ($power >= $tier['min']) {
                $currentTier = $tier;
                $nextTier = $tiers[$index + 1] ?? null;
            } else {
                break;
            }
        }

        $progress = 0;
        $nextMin = 0;
        $nextName = 'Max Rank';

        if ($nextTier) {
            $range = $nextTier['min'] - $currentTier['min'];
            $currentInTier = $power - $currentTier['min'];
            $progress = round(($currentInTier / $range) * 100);
            $nextMin = $nextTier['min'];
            $nextName = $nextTier['name'];
        } else {
            $progress = 100;
        }

        return [
            'name' => $currentTier['name'],
            'level' => $currentTier['level'],
            'next_name' => $nextName,
            'next_power' => $nextMin,
            'progress' => $progress
        ];
    }
}
