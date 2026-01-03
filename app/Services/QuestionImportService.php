<?php

namespace App\Services;

use App\Core\Database;
use Exception;

class QuestionImportService
{
    private $db;
    private $pdo;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPdo();
    }

    /**
     * Parse and import CSV file
     * 
     * @param string $filePath Path to the uploaded CSV file
     * @return array Import summary (success_count, error_count, errors)
     */
    public function importCSV($filePath)
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Could not open file.");
        }

        // Remove UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom != "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Skip header
        fgetcsv($handle);

        $rowNumber = 1;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        $inTransaction = $this->pdo->inTransaction();
        if (!$inTransaction) {
            $this->pdo->beginTransaction();
        }

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) continue;

                try {
                    $this->processRow($row);
                    $successCount++;
                } catch (Exception $e) {
                    $errorCount++;
                    $errors[] = "Row $rowNumber: " . $e->getMessage();
                }
            }

            if (!$inTransaction) {
                $this->pdo->commit();
            }
        } catch (Exception $e) {
            if (!$inTransaction) {
                $this->pdo->rollBack();
            }
            throw $e;
        } finally {
            fclose($handle);
        }

        return [
            'success_count' => $successCount,
            'error_count'   => $errorCount,
            'errors'        => $errors
        ];
    }

    /**
     * Process a single CSV row
     */
    private function processRow($row)
    {
        // validate basic columns
        if (count($row) < 9) {
            throw new Exception("Insufficient columns. Expected at least 9.");
        }

        $data = [
            'question_text' => trim($row[0]),
            'option_a'      => trim($row[1]),
            'option_b'      => trim($row[2]),
            'option_c'      => trim($row[3]),
            'option_d'      => trim($row[4]),
            'correct_answer'=> strtoupper(trim($row[5])),
            'is_practical'  => (strtolower(trim($row[6])) === 'true' || trim($row[6]) === '1') ? 1 : 0,
            'tags'          => trim($row[7]),
            'level_map'     => trim($row[8]),
            'explanation'   => isset($row[9]) ? trim($row[9]) : ''
        ];

        if (empty($data['question_text'])) {
            throw new Exception("Question text is required.");
        }

        // 1. Create Question
        $questionId = $this->createQuestion($data);

        // 2. Process Level Mapping (The Multi-Context Magic)
        if (!empty($data['level_map'])) {
            $this->processLevelMap($questionId, $data['level_map']);
        }
    }

    /**
     * Create the question record
     */
    private function createQuestion($data)
    {
        // Construct JSON content
        $content = json_encode([
            'text' => $data['question_text']
        ]);

        // Construct JSON options
        $options = json_encode([
            'a' => $data['option_a'],
            'b' => $data['option_b'],
            'c' => $data['option_c'],
            'd' => $data['option_d']
        ]);

        $sql = "INSERT INTO `quiz_questions` (`content`) VALUES (:content)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['content' => $content]);
        
        return $this->pdo->lastInsertId();
    }

    /**
     * Parse Level_Map_Syntax and create connections
     * Format: L4:Hard|L5:Medium|L7:Easy
     */
    public function processLevelMap($questionId, $syntax)
    {
        $mappings = explode('|', $syntax);
        
        foreach ($mappings as $map) {
            $parts = explode(':', trim($map));
            if (count($parts) !== 2) continue;

            $levelCode = strtoupper(trim($parts[0]));
            $difficultyStr = strtolower(trim($parts[1]));

            // Convert codes to full stream names
            $streamName = $this->getStreamName($levelCode);
            
            // Convert difficulty string to integer
            $difficulty = $this->getDifficultyLevel($difficultyStr);

            if ($streamName && $difficulty) {
                // Determine syllabus_node_id based on stream if possible, 
                // or leave null for 'General' pool for that stream.
                // For now, we connect to the stream foundation.
                
                $sql = "INSERT INTO `question_stream_map` 
                        (`question_id`, `stream`, `difficulty_in_stream`, `created_at`) 
                        VALUES (:qid, :stream, :diff, NOW())
                        ON DUPLICATE KEY UPDATE `difficulty_in_stream` = :diff_update";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'qid' => $questionId,
                    'stream' => $streamName,
                    'diff' => $difficulty,
                    'diff_update' => $difficulty
                ]);
            }
        }
    }

    private function getStreamName($code)
    {
        $map = [
            'L4' => 'Level 4',
            'L5' => 'Level 5',
            'L7' => 'Level 7'
        ];
        return $map[$code] ?? null;
    }

    private function getDifficultyLevel($str)
    {
        $map = [
            'easy' => 1,
            'medium' => 3,
            'hard' => 5
        ];
        return $map[$str] ?? 3; // Default to medium
    }
}
