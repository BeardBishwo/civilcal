<?php
/**
 * MARKING SCHEME INTERFACE
 * For manual grading of theory questions
 */

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;

class MarkingSchemeController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * Show marking interface for a specific exam/quiz attempt
     */
    public function markExam($attemptId)
    {
        // Get exam attempt details
        $attempt = $this->db->findOne('quiz_exam_attempts', ['id' => $attemptId]);
        
        if (!$attempt) {
            $_SESSION['error'] = 'Exam attempt not found';
            header('Location: ' . app_base_url('admin/quiz/results'));
            exit;
        }

        // Get exam details
        $exam = $this->db->findOne('quiz_exams', ['id' => $attempt['exam_id']]);
        
        // Get student details
        $student = $this->db->findOne('users', ['id' => $attempt['user_id']]);
        
        // Get all theory questions in this attempt
        $theoryAnswers = $this->db->query("
            SELECT 
                qa.*,
                q.id as question_id,
                q.content,
                q.answer_explanation,
                q.default_marks,
                q.theory_type,
                qe.marks as allocated_marks
            FROM quiz_attempt_answers qa
            JOIN quiz_exam_questions qe ON qa.exam_question_id = qe.id
            JOIN quiz_questions q ON qe.question_id = q.id
            WHERE qa.attempt_id = :attempt_id 
            AND q.type = 'THEORY'
            ORDER BY qe.order_index ASC
        ", ['attempt_id' => $attemptId])->fetchAll();

        // Calculate current scores
        $totalMarks = 0;
        $awardedMarks = 0;
        $unmarkedCount = 0;

        foreach ($theoryAnswers as $answer) {
            $totalMarks += $answer['allocated_marks'];
            if ($answer['marks_awarded'] !== null) {
                $awardedMarks += $answer['marks_awarded'];
            } else {
                $unmarkedCount++;
            }
        }

        $this->view('admin/quiz/marking-scheme', [
            'page_title' => 'Mark Theory Questions',
            'attempt' => $attempt,
            'exam' => $exam,
            'student' => $student,
            'theoryAnswers' => $theoryAnswers,
            'totalMarks' => $totalMarks,
            'awardedMarks' => $awardedMarks,
            'unmarkedCount' => $unmarkedCount,
            'menu_active' => 'quiz-results'
        ]);
    }

    /**
     * Save marks for a single answer
     */
    public function saveMarks()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $answerId = $data['answer_id'] ?? null;
        $marks = $data['marks'] ?? null;
        $feedback = $data['feedback'] ?? '';

        if (!$answerId || $marks === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        // Validate marks
        $answer = $this->db->findOne('quiz_attempt_answers', ['id' => $answerId]);
        if (!$answer) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Answer not found']);
            return;
        }

        // Get allocated marks for this question
        $examQuestion = $this->db->query("
            SELECT qe.marks 
            FROM quiz_exam_questions qe
            WHERE qe.id = :exam_question_id
        ", ['exam_question_id' => $answer['exam_question_id']])->fetch();

        $maxMarks = $examQuestion['marks'];

        if ($marks < 0 || $marks > $maxMarks) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Marks must be between 0 and {$maxMarks}"]);
            return;
        }

        // Update marks
        $this->db->update('quiz_attempt_answers', $answerId, [
            'marks_awarded' => $marks,
            'feedback' => $feedback,
            'marked_at' => date('Y-m-d H:i:s'),
            'marked_by' => $_SESSION['user_id'] ?? null
        ]);

        // Recalculate total score for attempt
        $this->recalculateAttemptScore($answer['attempt_id']);

        echo json_encode(['success' => true, 'message' => 'Marks saved successfully']);
    }

    /**
     * Recalculate total score for an attempt
     */
    private function recalculateAttemptScore($attemptId)
    {
        $result = $this->db->query("
            SELECT 
                SUM(marks_awarded) as total_score,
                COUNT(CASE WHEN marks_awarded IS NULL THEN 1 END) as unmarked_count
            FROM quiz_attempt_answers
            WHERE attempt_id = :attempt_id
        ", ['attempt_id' => $attemptId])->fetch();

        $status = $result['unmarked_count'] > 0 ? 'pending_review' : 'completed';

        $this->db->update('quiz_exam_attempts', $attemptId, [
            'score' => $result['total_score'],
            'status' => $status,
            'completed_at' => $status == 'completed' ? date('Y-m-d H:i:s') : null
        ]);
    }

    /**
     * Bulk save all marks for an attempt
     */
    public function bulkSaveMarks()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $attemptId = $data['attempt_id'] ?? null;
        $marks = $data['marks'] ?? [];

        if (!$attemptId || empty($marks)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        try {
            $pdo = $this->db->getPdo();
            $pdo->beginTransaction();

            foreach ($marks as $answerId => $markData) {
                $this->db->update('quiz_attempt_answers', $answerId, [
                    'marks_awarded' => $markData['marks'],
                    'feedback' => $markData['feedback'] ?? '',
                    'marked_at' => date('Y-m-d H:i:s'),
                    'marked_by' => $_SESSION['user_id'] ?? null
                ]);
            }

            $this->recalculateAttemptScore($attemptId);

            $pdo->commit();

            echo json_encode(['success' => true, 'message' => 'All marks saved successfully']);
        } catch (\Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
