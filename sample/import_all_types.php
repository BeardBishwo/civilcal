<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../app/bootstrap.php';

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
        $nodes = $db->query("SELECT id, title, type FROM syllabus_nodes WHERE type IN ('category', 'topic', 'sub_category')")->fetchAll(PDO::FETCH_ASSOC);
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

    $directories = [
        'mcq' => 'MCQ',
        'true_false' => 'TF',
        'sequence' => 'ORDER',
        'multi_tick' => 'MULTI'
    ];

    $importCount = 0;

    foreach ($directories as $dirName => $qType) {
        $dirPath = __DIR__ . '/' . $dirName;
        if (!is_dir($dirPath)) {
            echo "Skipping non-existent directory: $dirName\n";
            continue;
        }

        $files = glob($dirPath . '/*.csv');
        foreach ($files as $absPath) {
            $fileName = basename($absPath);
            $courseKey = (stripos($fileName, 'GK') !== false || stripos($fileName, 'PSC') !== false) ? 'psc' : 'civil';

            echo "Importing $fileName (Type: $qType, Course: $courseKey)...\n";

            if (($handle = fopen($absPath, "r")) !== FALSE) {
                $headers = fgetcsv($handle, 2000, ","); // Skip headers

                $rowNum = 1;
                while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                    $rowNum++;
                    try {
                        if (count($data) < 4) continue;

                        $category = trim($data[0]);
                        $questionText = trim($data[1]);
                        if (empty($category) || empty($questionText)) continue;

                        $optionsRaw = [];
                        $correctAnswerArr = [];
                        $explanation = '';

                        if ($qType === 'TF') {
                            $optionsRaw = [
                                ['text' => 'TRUE', 'is_correct' => false],
                                ['text' => 'FALSE', 'is_correct' => false]
                            ];
                            $correctVal = strtoupper(trim($data[2]));
                            if ($correctVal === 'TRUE') $optionsRaw[0]['is_correct'] = true;
                            else $optionsRaw[1]['is_correct'] = true;
                            $correctAnswerArr = [$correctVal];
                            $explanation = isset($data[3]) ? trim($data[3]) : '';
                        } elseif ($qType === 'MCQ' || $qType === 'ORDER' || $qType === 'MULTI') {
                            if (count($data) < 7) continue;

                            $optionsRaw = [
                                ['text' => trim($data[2]), 'is_correct' => false],
                                ['text' => trim($data[3]), 'is_correct' => false],
                                ['text' => trim($data[4]), 'is_correct' => false],
                                ['text' => trim($data[5]), 'is_correct' => false]
                            ];
                            $correctStr = trim($data[6]);
                            $explanation = isset($data[7]) ? trim($data[7]) : '';

                            if ($qType === 'MCQ') {
                                $idx = -1;
                                if (stripos($correctStr, 'Option A') !== false) $idx = 0;
                                elseif (stripos($correctStr, 'Option B') !== false) $idx = 1;
                                elseif (stripos($correctStr, 'Option C') !== false) $idx = 2;
                                elseif (stripos($correctStr, 'Option D') !== false) $idx = 3;

                                if ($idx !== -1) {
                                    $optionsRaw[$idx]['is_correct'] = true;
                                    $correctAnswerArr = [$optionsRaw[$idx]['text']];
                                }
                            } elseif ($qType === 'ORDER') {
                                // For ORDER, we expect the correct option (e.g. Option A) to contain the sequence string "2 - 1 - 4 - 3"
                                $idx = -1;
                                if (stripos($correctStr, 'Option A') !== false) $idx = 0;
                                elseif (stripos($correctStr, 'Option B') !== false) $idx = 1;
                                elseif (stripos($correctStr, 'Option C') !== false) $idx = 2;
                                elseif (stripos($correctStr, 'Option D') !== false) $idx = 3;

                                if ($idx !== -1) {
                                    $optionsRaw[$idx]['is_correct'] = true;
                                    $correctAnswerArr = [$optionsRaw[$idx]['text']];
                                }
                            } elseif ($qType === 'MULTI') {
                                // Correct Options like "A, B, C"
                                $letters = explode(',', $correctStr);
                                foreach ($letters as $l) {
                                    $l = trim(strtoupper($l));
                                    if ($l === 'A') {
                                        $optionsRaw[0]['is_correct'] = true;
                                        $correctAnswerArr[] = $optionsRaw[0]['text'];
                                    }
                                    if ($l === 'B') {
                                        $optionsRaw[1]['is_correct'] = true;
                                        $correctAnswerArr[] = $optionsRaw[1]['text'];
                                    }
                                    if ($l === 'C') {
                                        $optionsRaw[2]['is_correct'] = true;
                                        $correctAnswerArr[] = $optionsRaw[2]['text'];
                                    }
                                    if ($l === 'D') {
                                        $optionsRaw[3]['is_correct'] = true;
                                        $correctAnswerArr[] = $optionsRaw[3]['text'];
                                    }
                                }
                            }
                        }

                        $nodeId = null;
                        $catLower = strtolower($category);
                        if (isset($syllabusMap[$catLower])) {
                            $nodeId = $syllabusMap[$catLower]['id'];
                        }

                        $contentHash = hash('sha256', $questionText . $qType);

                        // Check duplicate
                        $exists = $db->query("SELECT id FROM quiz_questions WHERE content_hash = ?", [$contentHash])->fetch();
                        if ($exists) continue;

                        $contentJson = json_encode(['text' => $questionText], JSON_UNESCAPED_UNICODE);
                        $optionsJson = json_encode($optionsRaw, JSON_UNESCAPED_UNICODE);
                        $correctJson = json_encode($correctAnswerArr, JSON_UNESCAPED_UNICODE);
                        $slug = createSlug($questionText);
                        $difficulty = rand(1, 5);

                        $sql = "INSERT INTO quiz_questions 
                                (course_id, edu_level_id, category_id, type, content, content_hash, options, correct_answer_json, answer_explanation, difficulty_level, status, slug, target_audience) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved', ?, 'universal')";

                        $db->query($sql, [
                            $courseMapping[$courseKey],
                            $eduMapping[$courseKey],
                            $nodeId,
                            $qType,
                            $contentJson,
                            $contentHash,
                            $optionsJson,
                            $correctJson,
                            $explanation,
                            $difficulty,
                            $slug
                        ]);
                        $importCount++;
                    } catch (Exception $e) {
                        echo "Error at row $rowNum of $fileName: " . $e->getMessage() . "\n";
                    }
                }
                fclose($handle);
            }
        }
    }

    echo "Import Finished. Total Questions Added: $importCount\n";
} catch (Exception $ge) {
    echo "GLOBAL ERROR: " . $ge->getMessage() . "\n";
}
