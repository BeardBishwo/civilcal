<?php

namespace App\Services\Quiz;

/**
 * Scoring Engine Service
 * 
 * High-precision engine for:
 * - Validating question correctness (MCQ, MULTI, ORDER, etc.)
 * - Calculating marks and complex negative marking deductions
 */
class ScoringService
{
    /**
     * Grade a single question response
     * 
     * @param array $question The question data from JSON/DB
     * @param mixed $userAnswer The answer provided by the student
     * @param array $examSettings Settings including neg marking rules
     * @return array ['isCorrect' => bool, 'marks' => float]
     */
    public function gradeQuestion($question, $userAnswer, $examSettings)
    {
        $isCorrect = $this->checkCorrectness($question, $userAnswer);
        $marks = 0;

        if ($isCorrect) {
            $marks = floatval($question['default_marks'] ?? 1);
        } elseif ($userAnswer !== null) {
            $marks = $this->calculateDeduction($question, $examSettings);
        }

        return [
            'isCorrect' => $isCorrect,
            'marks' => $marks
        ];
    }

    /**
     * Core Correctness Logic
     */
    public function checkCorrectness($question, $userAnswer)
    {
        if ($userAnswer === null) return false;

        $type = $question['type'] ?? 'mcq_single';

        switch ($type) {
            case 'MCQ':
            case 'TF':
            case 'mcq_single':
            case 'true_false':
                return (string)$userAnswer === (string)($question['correct_answer'] ?? '');

            case 'MULTI':
                $ansArray = is_array($userAnswer) ? $userAnswer : json_decode($userAnswer, true);

                // Handle both string (from DB) and array (from cache)
                $correctData = $question['correct_answer_json'] ?? '[]';
                $correctArray = is_string($correctData) ? json_decode($correctData, true) : $correctData;

                if (!is_array($ansArray) || !is_array($correctArray)) return false;

                // Normalise types to string to avoid "1" !== 1 issues
                $ansArray = array_map('strval', $ansArray);
                $correctArray = array_map('strval', $correctArray);

                sort($ansArray);
                sort($correctArray);
                return json_encode($ansArray) === json_encode($correctArray);

            case 'ORDER':
                $ansArray = is_array($userAnswer) ? $userAnswer : json_decode($userAnswer, true);

                // Handle both string (from DB) and array (from cache)
                $correctData = $question['correct_answer_json'] ?? '[]';
                $correctArray = is_string($correctData) ? json_decode($correctData, true) : $correctData;

                if (!is_array($ansArray) || !is_array($correctArray)) return false;

                // Normalise types
                $ansArray = array_map('strval', $ansArray);
                $correctArray = array_map('strval', $correctArray);

                return json_encode($ansArray) === json_encode($correctArray);

            default:
                return false;
        }
    }

    /**
     * Complex Negative Marking Calculation
     */
    private function calculateDeduction($question, $examSettings)
    {
        $negRate = floatval($examSettings['negative_marking_rate'] ?? 0);
        $negUnit = $examSettings['negative_marking_unit'] ?? 'percent';

        if ($negRate <= 0) return 0;

        if ($negUnit === 'percent') {
            // Deduction = X% of the question's marks
            $qMarks = floatval($question['default_marks'] ?? 1);
            return -1 * abs($qMarks * ($negRate / 100));
        } else {
            // Deduction = Fixed numeric value
            return -1 * abs($negRate);
        }
    }
}
