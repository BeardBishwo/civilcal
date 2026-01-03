<?php

namespace App\Services\Quiz;

/**
 * ShuffleService - The "Chaos Engine"
 * 
 * Handles randomization of:
 * 1. Vertical Shuffle: Question Order
 * 2. Horizontal Shuffle: Option Order (Option A becomes Option D)
 * 3. Seeded Swaps: Deterministic shuffling for Fair Matches using a seed.
 */
class ShuffleService
{
    /**
     * Randomize a list of questions and their internal options.
     * 
     * @param array $questions Array of Question arrays (from DB)
     * @param int|null $seed Optional seed for deterministic shuffle
     * @return array Shuffled questions
     */
    public function randomize(array $questions, $seed = null)
    {
        if (empty($questions)) {
            return [];
        }

        // 1. Set Seed if provided
        if ($seed !== null) {
            mt_srand($seed);
        }

        // 2. Vertical Shuffle (Questions)
        // Note: Using shuffle() directly affects the array.
        // For custom seed behavior in PHP, standard shuffle() usually uses standard rand seed.
        // To be strictly deterministic with mt_srand, we should use a Fisher-Yates shuffle with mt_rand.
        // Standard shuffle() behavior with mt_srand varies by PHP version (post 7.1 it changed).
        // For robustness, we'll use a simple sort with random weight.
        
        if ($seed !== null) {
            // Deterministic Shuffle using provided seed
            // We use array_multisort with a generated weight array to avoid creating a custom algo
            $order = array_map(function() { return mt_rand(); }, $questions);
            array_multisort($order, SORT_NUMERIC, $questions);
        } else {
            // True Random
            shuffle($questions);
        }

        // 3. Horizontal Shuffle (Options inside each question)
        foreach ($questions as &$q) {
            $q = $this->shuffleOptions($q, $seed);
        }

        return $questions;
    }

    /**
     * Shuffle options within a single question
     */
    private function shuffleOptions($question, $seed = null)
    {
        $optionsVal = is_array($question) ? ($question['options'] ?? null) : ($question->options ?? null);

        // Check if options exist
        if (empty($optionsVal)) {
            return $question;
        }

        // Decode if string
        $rawOptions = is_string($optionsVal) ? json_decode($optionsVal, true) : $optionsVal;

        if (!is_array($rawOptions)) {
            return $question;
        }

        /**
         * Transform to Mappable Array
         * We need to separate the "Visual ID" (Option A, B) from the "Data ID" (1, 2).
         * Standard Format stored in DB: [{"id":1, "text":"Red"}, {"id":2, "text":"Blue"}] 
         * Or key-value: {"option_1": "Red", ...} - Need to handle Legacy format too!
         */

        $mappable = [];

        // Scenario A: Modern JSON Format (Array of Objects)
        if (isset($rawOptions[0]) && is_array($rawOptions[0]) && isset($rawOptions[0]['id'])) {
             $mappable = $rawOptions;
        } 
        // Scenario B: Legacy/Simple Format ({"option_1": "Red"}) - Likely from ImportProcessor
        // Wait, ImportProcessor creates a simple key-value pair for options?
        // Let's verify ImportProcessor format later. Assuming Standard Array of Objects is target.
        // If it is Scenario B, we convert.
        else {
             foreach ($rawOptions as $key => $val) {
                 if (empty($val)) continue;
                 // Extract ID if key matches 'option_X'
                 $id = str_replace('option_', '', $key);
                 $mappable[] = [
                     'id' => $id, 
                     'text' => $val,
                     // Add other fields if needed
                 ];
             }
        }

        // SHUFFLE
        if ($seed !== null) {
            $qId = is_array($question) ? ($question['id'] ?? 0) : ($question->id ?? 0);
            $localSeed = $seed + (int)$qId;
            mt_srand($localSeed);
            
            // Deterministic sort
            $this->deterministicShuffle($mappable);
        } else {
             shuffle($mappable);
        }

        // Translate Answers (MCQ / MULTI)
        $question = $this->translateAnswers($question, $mappable);

        // Inject back
        if (is_array($question)) {
            $question['shuffled_options'] = $mappable;
        } else {
            $question->shuffled_options = $mappable;
        }
        
        return $question;
    }

    /**
     * Translates original correct answers to new shuffled indices.
     */
    private function translateAnswers($question, $newOrder)
    {
        $type = is_array($question) ? ($question['type'] ?? 'MCQ') : ($question->type ?? 'MCQ');
        
        // Map: Original ID -> New Position (1-indexed)
        $idMap = [];
        foreach ($newOrder as $index => $item) {
            $idMap[$item['id']] = $index + 1;
        }

        if ($type == 'MCQ' || $type == 'TF' || $type == 'mcq_single' || $type == 'true_false') {
            $oldAns = is_array($question) ? ($question['correct_answer'] ?? '') : ($question->correct_answer ?? '');
            if (isset($idMap[$oldAns])) {
                if (is_array($question)) $question['correct_answer'] = (string)$idMap[$oldAns];
                else $question->correct_answer = (string)$idMap[$oldAns];
            }
        } 
        elseif ($type == 'MULTI') {
            $json = is_array($question) ? ($question['correct_answer_json'] ?? '[]') : ($question->correct_answer_json ?? '[]');
            $oldAnsArray = is_string($json) ? json_decode($json, true) : $json;
            
            if (is_array($oldAnsArray)) {
                $newAnsArray = [];
                foreach ($oldAnsArray as $oldId) {
                    if (isset($idMap[$oldId])) $newAnsArray[] = (string)$idMap[$oldId];
                }
                sort($newAnsArray); // Aesthetic: 1, 2 instead of 2, 1
                
                if (is_array($question)) $question['correct_answer_json'] = json_encode($newAnsArray);
                else $question->correct_answer_json = json_encode($newAnsArray);
            }
        }
        // ORDER type usually doesn't need translation if it's evaluated by Original ID sequence.
        
        return $question;
    }
    
    private function deterministicShuffle(&$items) {
        $order = array_map(function() { return mt_rand(); }, $items);
        array_multisort($order, SORT_NUMERIC, $items);
    }
}
