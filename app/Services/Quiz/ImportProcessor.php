<?php
namespace App\Services\Quiz;

use App\Models\Question;
use App\Models\SyllabusNode;
use App\Core\Database;

class ImportProcessor {

    /**
     * PROCESS A SINGLE ROW (The "Brain")
     * Converts raw CSV data into a clean Database Array
     */
    public function processRow($row, $batchId, $uploaderId) {
        
        // 1. SMART CATEGORY MAPPING
        $mainId = $this->resolveCategoryId($row['category'] ?? null);
        $subId  = $this->resolveCategoryId($row['subcategory'] ?? null);

        // 2. DETECT QUESTION TYPE (Elite Quiz Compatibility Mode)
        $type = 'MCQ'; // Default

        if (isset($row['question_type'])) {
            $rawType = $row['question_type'];
            
            // ELITE QUIZ CLEANER:
            // They sometimes put text like "{2 if true...}". We just want the number.
            // This regex extracts the first number found in the string.
            if (preg_match('/(\d+)/', $rawType, $matches)) {
                $rawType = $matches[0];
            }

            // Logic: 2 = True/False, 1 = MCQ
            if ($rawType == 2 || (empty($row['option 3']) && empty($row['option 4']))) {
                $type = 'TF';
            }
        }

        // 3. CLEAN OPTIONS
        // We pack them into a robust JSON array
        $options = [];
        if ($type == 'MCQ') {
            $options = [
                'option_1' => $this->cleanText($row['option 1'] ?? ''),
                'option_2' => $this->cleanText($row['option 2'] ?? ''),
                'option_3' => $this->cleanText($row['option 3'] ?? ''),
                'option_4' => $this->cleanText($row['option 4'] ?? ''),
                'option_5' => $this->cleanText($row['option 5'] ?? '') // Handle 5th option if present
            ];
        } else {
            // Force True/False values
            $options = ['option_1' => 'True', 'option_2' => 'False'];
        }

        // 4. MAP CORRECT ANSWER
        // CSV uses 'a', 'b', '1', '2'. We standardize to '1', '2', '3', '4'.
        $correct = $this->standardizeAnswer($row['answer'] ?? '');

        // 5. GENERATE FINGERPRINT (For Duplicate Detection)
        $qText = $row['question'] ?? '';
        $cleanQ = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $qText)));
        $hash = hash('sha256', $cleanQ);

        // 6. DETECT DUPLICATE (The "Quarantine" Logic)
        $existing = Question::where('content_hash', $hash)->first();

        // RETURN THE PACKAGED DATA
        return [
            'batch_id' => $batchId,
            'uploader_id' => $uploaderId,
            'syllabus_main_id' => $mainId,
            'syllabus_node_id' => $subId,
            'question' => $this->cleanText($qText),
            'type' => $type,
            'options' => json_encode($options),
            'correct_answer' => $correct,
            'level' => $this->mapLevel($row['level'] ?? ''), // Map 1->Easy, 2->Medium
            'answer_explanation' => $this->cleanText($row['note'] ?? ''), // Map 'note' to 'answer_explanation'
            'content_hash' => $hash,
            'is_duplicate' => $existing ? 1 : 0,
            'match_id' => $existing ? $existing->id : null,
            'status' => 'pending',
            // 'level_map' -> handled in Controller processLevelMap currently, 
            // but we can add it here if row has 'level_map' column. 
            // For now, let's assume 'level' uses simple map if distinct column, 
            // or if we want advanced level map we would capture row['level_map']
        ];
    }

    // --- HELPER FUNCTIONS ---

    private function resolveCategoryId($input) {
        if (!$input) return null;
        // If numeric, assume ID
        if (is_numeric($input)) return $input;
        
        // If string, lookup by Title (Smart Template support)
        $node = SyllabusNode::where('title', 'LIKE', "%$input%")->first();
        return $node ? $node->id : null;
    }

    private function standardizeAnswer($val) {
        $val = strtolower(trim($val));
        $map = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'true' => 1, 'false' => 2];
        return $map[$val] ?? $val;
    }

    private function mapLevel($val) {
        // CSV uses 1, 2, 3. We map to readable ENUM.
        $map = [1 => 'easy', 2 => 'medium', 3 => 'hard'];
        return $map[$val] ?? 'medium';
    }

    private function cleanText($text) {
        // Remove weird CSV artifacts
        return trim(str_replace(['Â', 'â€™'], ['', "'"], $text));
    }
}
