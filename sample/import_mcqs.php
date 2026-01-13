<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    if (!$db) die("DB Fail\n");

    function createSlug($text)
    {
        $slug = substr(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text))), 0, 100);
        return $slug . '-' . uniqid();
    }

    function getSyllabusMap($db)
    {
        $nodes = $db->query("SELECT id, title, parent_id FROM syllabus_nodes WHERE type IN ('category', 'topic', 'sub_category')")->fetchAll(PDO::FETCH_ASSOC);
        $map = [];
        foreach ($nodes as $n) {
            $map[strtolower(trim($n['title']))] = $n;
        }
        return $map;
    }

    $syllabusMap = getSyllabusMap($db);
    $courseMapping = [
        'civil' => 1,
        'psc' => 308
    ];
    $eduMapping = [
        'civil' => 2,
        'psc' => null
    ];

    $csvFiles = [
        'sample/mcq/Engineering .csv' => 'civil',
        'sample/mcq/GK Questions.csv' => 'psc',
        'sample/mcq/Question .csv' => 'civil',
        'sample/mcq/ok - ok.csv' => 'civil'
    ];

    $importCount = 0;

    foreach ($csvFiles as $filePath => $courseKey) {
        $absPath = __DIR__ . '/' . $filePath;
        if (!file_exists($absPath)) {
            echo "File not found: $filePath\n";
            continue;
        }

        echo "Importing $filePath...\n";
        if (($handle = fopen($absPath, "r")) !== FALSE) {
            $headers = fgetcsv($handle, 1000, ","); // Skip headers

            $rowNum = 1;
            while (($data = fgetcsv($handle, 1200, ",")) !== FALSE) {
                $rowNum++;
                try {
                    if (count($data) < 7) continue;

                    $category = trim($data[0]);
                    $questionText = trim($data[1]);
                    if (empty($category) || empty($questionText)) continue;

                    $optionsRaw = [
                        ['text' => trim($data[2]), 'is_correct' => false],
                        ['text' => trim($data[3]), 'is_correct' => false],
                        ['text' => trim($data[4]), 'is_correct' => false],
                        ['text' => trim($data[5]), 'is_correct' => false]
                    ];
                    $correctLetter = trim($data[6]);
                    $explanation = isset($data[7]) ? trim($data[7]) : '';

                    $correctIndex = -1;
                    if (stripos($correctLetter, 'Option A') !== false) $correctIndex = 0;
                    elseif (stripos($correctLetter, 'Option B') !== false) $correctIndex = 1;
                    elseif (stripos($correctLetter, 'Option C') !== false) $correctIndex = 2;
                    elseif (stripos($correctLetter, 'Option D') !== false) $correctIndex = 3;

                    if ($correctIndex !== -1) {
                        $optionsRaw[$correctIndex]['is_correct'] = true;
                        $correctAnswerArr = [$optionsRaw[$correctIndex]['text']];
                    } else {
                        // Fallback: search for option match if letter logic fails
                        $correctAnswerArr = [];
                    }

                    $nodeId = null;
                    $catLower = strtolower($category);
                    if (isset($syllabusMap[$catLower])) {
                        $nodeId = $syllabusMap[$catLower]['id'];
                    }

                    $contentJson = json_encode(['text' => $questionText], JSON_UNESCAPED_UNICODE);
                    $optionsJson = json_encode($optionsRaw, JSON_UNESCAPED_UNICODE);
                    $correctJson = json_encode($correctAnswerArr, JSON_UNESCAPED_UNICODE);

                    if ($contentJson === false || $optionsJson === false) {
                        echo "JSON Error at row $rowNum: " . json_last_error_msg() . "\n";
                        continue;
                    }

                    $contentHash = hash('sha256', $questionText);
                    $slug = createSlug($questionText);
                    $difficulty = rand(1, 4); // Keep difficulty slightly varied

                    // Check duplicate
                    $exists = $db->query("SELECT id FROM quiz_questions WHERE content_hash = ?", [$contentHash])->fetch();
                    if ($exists) {
                        continue;
                    }

                    // We use category_id for the syllabus node, leaving topic_id NULL for now to avoid FK issues with quiz_topics
                    $sql = "INSERT INTO quiz_questions 
                            (course_id, edu_level_id, category_id, type, content, content_hash, options, correct_answer_json, answer_explanation, difficulty_level, status, slug, target_audience) 
                            VALUES (?, ?, ?, 'MCQ', ?, ?, ?, ?, ?, ?, 'approved', ?, 'universal')";

                    $db->query($sql, [
                        $courseMapping[$courseKey],
                        $eduMapping[$courseKey],
                        $nodeId,
                        $contentJson,
                        $contentHash,
                        $optionsJson,
                        $correctJson,
                        $explanation,
                        $difficulty,
                        $slug
                    ]);
                    $importCount++;
                    if ($importCount % 20 === 0) echo "Processed $importCount items...\n";
                } catch (Exception $e) {
                    echo "Error at row $rowNum of $filePath: " . $e->getMessage() . "\n";
                }
            }
            fclose($handle);
        }
    }

    echo "Import Finished. Total Questions Added: $importCount\n";
} catch (Exception $ge) {
    echo "GLOBAL ERROR: " . $ge->getMessage() . "\n";
}
