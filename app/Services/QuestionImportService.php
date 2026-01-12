<?php

namespace App\Services;

use App\Models\Question;
use App\Models\SyllabusNode;
use App\Models\QuestionStreamMap;
use App\Core\Database; // Assuming use of Core DB wrapper or Eloquent if available. Code provided used Eloquent syntax (Question::create).
// However, earlier files used $this->db->query.
// The provided code uses Eloquent syntax: Question::create, DB::beginTransaction.
// I should verify if Eloquent is active or strictly PDO.
// Looking at ImportProcessor.php: use App\Models\Question; using `where()->first()` -> Looks like Eloquent or similar ORM.
// I will stick to the provided code but ensure namespace consistency.

use Exception; // PHP Exception

class QuestionImportService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * The Master Import Function
     */
    public function bulkImport($csvData, $uploaderId)
    {
        $results = ['success' => 0, 'errors' => []];

        foreach ($csvData as $index => $row) {
            try {
                // 1. Validate Row
                if (!$this->validateRow($row)) {
                    throw new Exception("Missing required fields (Question Text or Correct Answer)");
                }

                // 2. Create the Core Question
                $questionData = [
                    'content' => $this->formatContent($row['Question Text']),
                    'type' => $this->detectType($row),
                    'options' => $this->formatOptions($row),
                    'correct_answer' => $row['Correct Answer'],
                    'answer_explanation' => $row['Explanation'] ?? null,
                    'global_tags' => $row['Global Tags'] ?? null,
                    'uploader_id' => $uploaderId,
                    'content_hash' => hash('sha256', strtolower(trim($row['Question Text']))) // Duplicate Check
                ];

                $this->db->insert('quiz_questions', $questionData);
                $questionId = $this->db->lastInsertId();

                // 3. The "Multi-Context" Magic (Level Map Parser)
                // Syntax: "L4:Hard|L7:Easy"
                if (!empty($row['Level Map Syntax'])) {
                    $this->processLevelMap($questionId, $row['Level Map Syntax']);
                }

                $results['success']++;
            } catch (Exception $e) {
                $results['errors'][] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Parses "L4:Hard|L7:Easy" and creates mapping entries
     */
    public function processLevelMap($questionId, $syntax)
    {
        if (empty($syntax)) return;

        $mappings = explode('|', $syntax);

        foreach ($mappings as $map) {
            // Split "L4:Hard" into ["L4", "Hard"]
            $parts = explode(':', trim($map));
            if (count($parts) !== 2) continue;

            $streamCode = $parts[0];
            $difficulty = $parts[1];

            // Resolve Stream Code (L4) to Database ID
            $streamNode = $this->db->findOne('syllabus_nodes', ['slug' => $streamCode]); // Using slug or title? Syntax suggests code/slug.

            if ($streamNode) {
                $this->db->insert('question_stream_map', [
                    'question_id' => $questionId,
                    'syllabus_node_id' => $streamNode['id'], // Maps to the specific stream
                    'difficulty_in_stream' => $this->normalizeDifficulty($difficulty) // Converts 'Hard' to 4
                ]);
            }
        }
    }

    private function normalizeDifficulty($diff)
    {
        $diff = strtolower(trim($diff));
        $map = [
            'easy' => 1,
            'easy-mid' => 2,
            'easy_mid' => 2,
            'medium' => 3,
            'hard' => 4,
            'expert' => 5,
            'elite' => 5,
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5
        ];
        return $map[$diff] ?? 3;
    }

    private function detectType($row)
    {
        // If Option A/B are empty, assume True/False or Numerical
        if (empty($row['Option A'])) return 'NUMERICAL';
        return 'MCQ';
    }

    private function formatOptions($row)
    {
        if (empty($row['Option A'])) return null;
        return json_encode([
            'option_1' => $row['Option A'],
            'option_2' => $row['Option B'],
            'option_3' => $row['Option C'],
            'option_4' => $row['Option D']
        ]);
    }

    private function formatContent($text)
    {
        // Wraps raw text in JSON structure for the frontend renderer
        return json_encode(['text' => $text]);
    }

    private function validateRow($row)
    {
        return !empty($row['Question Text']) && !empty($row['Correct Answer']);
    }
}
