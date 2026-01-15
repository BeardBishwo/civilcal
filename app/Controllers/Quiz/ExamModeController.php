<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Core\Database;

class ExamModeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function index()
    {
        $db = Database::getInstance();

        // Fetch all exams
        // Add course/level names via JOIN if tables exist, but for now just raw list
        $stmt = $db->prepare("SELECT * FROM quiz_exams ORDER BY created_at DESC");
        $stmt->execute();
        $exams = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Separate by type if needed
        $grouped = [];
        foreach ($exams as $exam) {
            $type = ucfirst(str_replace('_', ' ', $exam['type']));
            $grouped[$type][] = $exam;
        }

        $this->view->render('quiz/games/exam_list', [
            'page_title' => 'Exam Hall',
            'exams' => $exams,
            'grouped_exams' => $grouped,
            'user' => $_SESSION['user']
        ], 'layouts/quiz_focus');
    }
}
