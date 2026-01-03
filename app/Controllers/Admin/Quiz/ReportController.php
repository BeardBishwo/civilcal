<?php
namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Models\QuestionReport;
use App\Models\Question;

class ReportController extends Controller {

    public function index() {
        // Fetch pending reports with the Question and User data
        // Assuming simple ORM. If 'with' not supported, use loop or join.
        // Bishwo ORM usually simple.
        // Let's check Model... find() returns array. Relations not built-in like Eloquent.
        // I need to join manually or loop.
        
        $db = \App\Core\Database::getInstance();
        $reports = $db->query("
            SELECT r.*, u.username as user_name, q.content as question_content, q.options as question_options, q.correct_answer as question_answer 
            FROM question_reports r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN quiz_questions q ON r.question_id = q.id
            WHERE r.status = 'pending'
            ORDER BY r.created_at DESC
        ")->fetchAll();
        
        // Format for View
        $formatted = [];
        foreach($reports as $r) {
            $qContent = json_decode($r['question_content'] ?? '{}', true);
            $qText = $qContent['text'] ?? ($r['question_content'] ?? 'Error loading question');
            
            $formatted[] = (object) [
                'id' => $r['id'],
                'issue_type' => $r['issue_type'],
                'description' => $r['description'],
                'created_at' => $r['created_at'],
                'user' => (object) ['name' => $r['user_name'] ?? 'Unknown'],
                'question_id' => $r['question_id'],
                'question' => (object) [
                    'question' => $qText,
                    'options' => $r['question_options'],
                    'correct_answer' => $r['question_answer']
                ]
            ];
        }

        $this->view('admin/quiz/reports/index', [
            'reports' => $formatted,
            'count' => count($formatted)
        ]);
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
