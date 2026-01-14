<?php
namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Models\QuestionReport;
use App\Models\Question;

class ReportController extends Controller {

    public function index() {
        try {
            $db = \App\Core\Database::getInstance();
            $reports = $db->query("
                SELECT r.*, u.username as user_name, q.question as question_text
                FROM question_reports r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN quiz_questions q ON r.question_id = q.id
                WHERE r.status = 'pending'
                ORDER BY r.created_at DESC
            ")->fetchAll();
            
            // Format for View
            $formatted = [];
            foreach($reports as $r) {
                $formatted[] = (object) [
                    'id' => $r['id'],
                    'issue_type' => $r['issue_type'],
                    'description' => $r['description'] ?? '',
                    'created_at' => $r['created_at'],
                    'user' => (object) ['name' => $r['user_name'] ?? 'Unknown'],
                    'question_id' => $r['question_id'],
                    'question' => (object) [
                        'question' => $r['question_text'] ?? 'Question not found',
                        'options' => '',
                        'correct_answer' => ''
                    ]
                ];
            }

            $this->view('admin/quiz/reports/index', [
                'reports' => $formatted,
                'count' => count($formatted)
            ]);
        } catch (\Exception $e) {
            // Log the error and show a friendly message
            error_log("Report Controller Error: " . $e->getMessage());
            $this->view('admin/quiz/reports/index', [
                'reports' => [],
                'count' => 0,
                'error' => 'Unable to load reports. Please check the database connection.'
            ]);
        }
    }

    /**
     * Action: Mark as Resolved (e.g. after you fixed the typo)
     */
    public function resolve() {
        $id = $_POST['id'];
        $db = \App\Core\Database::getInstance();
        $db->update('question_reports', ['status' => 'resolved'], "id = :id", ['id' => $id]);
        echo json_encode(['status' => 'success']);
    }

    /**
     * Action: Ignore (If student was wrong)
     */
    public function ignore() {
        $id = $_POST['id'];
        $db = \App\Core\Database::getInstance();
        $db->update('question_reports', ['status' => 'ignored'], "id = :id", ['id' => $id]);
        echo json_encode(['status' => 'success']);
    }
}
