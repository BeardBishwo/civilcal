<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Models\Question;
use App\Models\SyllabusNode;

class QuestionExportController extends Controller
{

    /**
     * INTELLIGENT EXPORT ENGINE
     * Handles huge datasets without crashing RAM.
     */
    /**
     * INTELLIGENT EXPORT ENGINE
     * Handles huge datasets without crashing RAM.
     */
    public function export()
    {
        $db = \App\Core\Database::getInstance();

        // 1. FILTER LOGIC
        $where = [];
        $params = [];

        if (!empty($_GET['category_id'])) {
            $where[] = "q.category_id = :cat";
            $params['cat'] = $_GET['category_id'];
        }
        if (!empty($_GET['stream'])) {
            $where[] = "q.course_id = :course";
            $params['course'] = $_GET['stream'];
        }
        if (!empty($_GET['type'])) {
            $where[] = "q.type = :type";
            $params['type'] = $_GET['type'];
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        // 2. FILENAME
        $filename = "bishwo_questions_" . date('Y-m-d_His') . ".csv";

        // Headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // 3. HEADERS
        fputcsv($output, [
            'ID',
            'Course',
            'Category',
            'Type',
            'Question',
            'Option A',
            'Option B',
            'Option C',
            'Option D',
            'Option E',
            'Correct Answer',
            'Difficulty',
            'Explanation'
        ]);

        // 4. STREAM DATA
        $sql = "
            SELECT q.*, 
                   sn_cat.title as category_title,
                   sn_course.title as course_title
            FROM quiz_questions q 
            LEFT JOIN syllabus_nodes sn_cat ON q.category_id = sn_cat.id 
            LEFT JOIN syllabus_nodes sn_course ON q.course_id = sn_course.id
            $whereClause 
            ORDER BY q.id DESC
        ";

        $stmt = $db->getPdo()->prepare($sql);
        $stmt->execute($params);

        while ($q = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $content = json_decode($q['content'], true);
            $options = json_decode($q['options'], true);

            $questionText = strip_tags($content['text'] ?? '');

            // Format options
            $optA = strip_tags($options[0]['text'] ?? '');
            $optB = strip_tags($options[1]['text'] ?? '');
            $optC = strip_tags($options[2]['text'] ?? '');
            $optD = strip_tags($options[3]['text'] ?? '');
            $optE = strip_tags($options[4]['text'] ?? '');

            // Correct Answer calculation
            $answerText = '-';
            if ($q['type'] == 'MCQ' || $q['type'] == 'TF') {
                foreach ($options as $idx => $opt) {
                    if (!empty($opt['is_correct'])) {
                        $answerText = ($q['type'] == 'MCQ') ? "Option " . chr(65 + $idx) : ($opt['text'] ?? '-');
                        break;
                    }
                }
            } elseif ($q['type'] == 'MULTI') {
                $answers = [];
                foreach ($options as $idx => $opt) {
                    if (!empty($opt['is_correct'])) $answers[] = chr(65 + $idx);
                }
                $answerText = implode(', ', $answers);
            } elseif ($q['type'] == 'ORDER') {
                $answerText = $q['correct_answer_json'] ?? '-';
            }

            fputcsv($output, [
                $q['id'],
                $q['course_title'] ?? '-',
                $q['category_title'] ?? '-',
                $q['type'],
                $questionText,
                $optA,
                $optB,
                $optC,
                $optD,
                $optE,
                $answerText,
                $q['difficulty_level'],
                strip_tags($q['answer_explanation'] ?? '')
            ]);
        }

        fclose($output);
        exit;
    }
}
