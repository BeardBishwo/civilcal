<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;

class TrueFalseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function index()
    {
        $this->view->render('quiz/games/true_false', [
            'page_title' => 'True / False Challenge',
            'user' => $_SESSION['user']
        ], 'layouts/quiz_focus');
    }

    /**
     * API: Get Questions
     */
    public function getQuestions()
    {
        $db = \App\Core\Database::getInstance();

        // Fetch 20 random boolean questions
        // content column is JSON, we need to decode in frontend or here.
        // But usually content -> question text.
        $stmt = $db->prepare("SELECT id, content, correct_answer FROM quiz_questions WHERE type = 'TF' ORDER BY RAND() LIMIT 20");
        $stmt->execute();
        $questions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($questions as &$q) {
            // Decode content if it's JSON
            $decoded = json_decode($q['content'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $q['text'] = is_array($decoded) ? ($decoded['text'] ?? ($decoded['question'] ?? 'Question Error')) : $decoded;
            } else {
                $q['text'] = $q['content'];
            }

            // SECURITY: Hash answer? Or just send it (for speed/educational mode).
            // sending it for speed.
            unset($q['content']);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'questions' => $questions]);
        exit;
    }
}
