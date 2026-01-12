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

        // 1.5. DETECT ANSWER TYPE (Brainstorming Engine)
        // 1=Multi, 2=Order (from Advanced Template 'answer_type' column)
        if (isset($row['answer_type'])) {
            $rawAnsType = $this->extractNumber($row['answer_type']);
            if ($rawAnsType == 1) $type = 'MULTI';
            if ($rawAnsType == 2) $type = 'ORDER';
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



        // 4. MAP CORRECT ANSWER (Complex Logic)
        $correctJson = null;
        $simpleCorrect = '';

        if ($type == 'MULTI' || $type == 'ORDER') {
            // Collect all non-empty answer columns (answer1...answer5)
            $answers = [];
            
            // If answer1 exists, check it. Also check numeric suffix columns from advanced template.
            // Advanced template likely uses answer1, answer2, etc.
            // But if user uses single 'answer' column with comma separation?
            // Prompt says: "answer1, answer2..." in template.
            for($i=1; $i<=5; $i++) {
                if(!empty($row["answer$i"])) {
                    $answers[] = $this->standardizeAnswer($row["answer$i"]);
                }
            }
            
            // Fallback: If answer1..5 empty, maybe mapped from 'answer' column split by comma?
            if (empty($answers) && !empty($row['answer'])) {
                $split = explode(',', $row['answer']);
                foreach($split as $s) {
                    $answers[] = $this->standardizeAnswer($s);
                }
            }
            
            $correctJson = json_encode($answers); 
        } else {
            // Standard MCQ/TF
            // CSV uses 'a', 'b', '1', '2'. We standardize to '1', '2', '3', '4'.
            $simpleCorrect = $this->standardizeAnswer($row['answer'] ?? ($row['answer1'] ?? ''));
        }

        // 5. GENERATE FINGERPRINT (For Duplicate Detection)
        // Create a unique hash for the question to prevent duplicates
        // New regex preserves mathematical operators to avoid collisions between e.g. "1+1" and "1-1"
        $qText = $row['question'] ?? '';
        $cleanQ = strtolower(trim(preg_replace('/[^a-zA-Z0-9+\-*\/=^%]/', '', $qText)));
        $hash = hash('sha256', $cleanQ);

        // 6. DETECT DUPLICATE (The "Quarantine" Logic)
        $db = \App\Core\Database::getInstance();
        $existing = $db->query("SELECT id FROM quiz_questions WHERE content_hash = ? LIMIT 1", [$hash])->fetch();

        // Object or Array Check
        $matchId = null;
        if ($existing) {
            $matchId = is_array($existing) ? ($existing['id'] ?? null) : ($existing->id ?? null);
        }

        // RETURN THE PACKAGED DATA
        return [
            'batch_id' => $batchId,
            'uploader_id' => $uploaderId,
            'contest_id' => $row['contest_id'] ?? null,
            'syllabus_main_id' => $mainId,
            'syllabus_node_id' => $subId,
            'question' => $this->cleanText($qText),
            'type' => $type,
            'options' => json_encode($options),
            'correct_answer' => $simpleCorrect,
            'correct_answer_json' => $correctJson,
            'level' => $this->mapLevel($row['level'] ?? ''), // Map 1->Easy, 2->Medium
            'answer_explanation' => $this->cleanText($row['note'] ?? ''), // Map 'note' to 'answer_explanation'
            'content_hash' => $hash,
            'is_duplicate' => $existing ? 1 : 0,
            'match_id' => $matchId,
            'status' => 'pending'
        ];
    }

    // --- HELPER FUNCTIONS ---

    private function resolveCategoryId($input) {
        if (!$input) return null;
        // If numeric, assume ID
        if (is_numeric($input)) return $input;
        
        // If string, lookup by Title (Smart Template support)
        $db = \App\Core\Database::getInstance();
        // SECURITY: Escape SQL wildcards to prevent query pollution
        $escapedInput = str_replace(['%', '_', '\\'], ['\\%', '\\_', '\\\\'], $input);
        $node = $db->query("SELECT id FROM syllabus_nodes WHERE title LIKE ? ESCAPE '\\' LIMIT 1", ["%$escapedInput%"])->fetch();
        return $node ? $node['id'] : null;
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
        // SECURITY: Strip HTML tags to prevent XSS from imported CSV data
        $text = strip_tags($text);
        // Remove weird CSV artifacts
        return trim(str_replace(['Â', 'â€™'], ['', "'"], $text));
    }

    private function extractNumber($text) {
        if (preg_match('/(\d+)/', $text, $matches)) return $matches[0];
        return 0;
    }
}
