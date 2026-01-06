<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;

/**
 * Staging Queue Manager
 * View and manage all import batches
 */
class StagingQueueController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * List all staging batches
     */
    public function index()
    {
        // Get all unique batches with stats
        $sql = "
            SELECT 
                batch_id,
                COUNT(*) as total_questions,
                SUM(CASE WHEN is_duplicate = 0 THEN 1 ELSE 0 END) as clean_count,
                SUM(CASE WHEN is_duplicate = 1 THEN 1 ELSE 0 END) as duplicate_count,
                MIN(created_at) as uploaded_at,
                uploader_id
            FROM question_import_staging
            GROUP BY batch_id
            ORDER BY uploaded_at DESC
        ";
        
        $batches = $this->db->query($sql)->fetchAll();

        // Get uploader names
        foreach ($batches as &$batch) {
            $user = $this->db->findOne('users', ['id' => $batch['uploader_id']]);
            $batch['uploader_name'] = $user ? $user['username'] : 'Unknown';
        }

        $this->view('admin/quiz/staging-queue', [
            'page_title' => 'Staging Queue',
            'batches' => $batches,
            'menu_active' => 'quiz-staging'
        ]);
    }

    /**
     * View specific batch
     */
    public function viewBatch($batchId)
    {
        // Get batch stats
        $stats = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN is_duplicate = 0 THEN 1 ELSE 0 END) as clean,
                SUM(CASE WHEN is_duplicate = 1 THEN 1 ELSE 0 END) as duplicates
            FROM question_import_staging
            WHERE batch_id = ?
        ", [$batchId])->fetch();

        // Get clean questions
        $cleanQuestions = $this->db->query("
            SELECT * FROM question_import_staging
            WHERE batch_id = ? AND is_duplicate = 0
            ORDER BY id ASC
        ", [$batchId])->fetchAll();

        // Get duplicate questions with old question info
        $duplicates = $this->db->query("
            SELECT * FROM question_import_staging
            WHERE batch_id = ? AND is_duplicate = 1
            ORDER BY id ASC
        ", [$batchId])->fetchAll();

        $duplicateDetails = [];
        foreach ($duplicates as $dup) {
            $oldQ = $this->db->findOne('quiz_questions', ['id' => $dup['duplicate_match_id']]);
            $oldContent = json_decode($oldQ['content'] ?? '{}', true);
            
            $duplicateDetails[] = [
                'id' => $dup['id'],
                'match_id' => $dup['duplicate_match_id'],
                'new_question' => $dup['question_text'],
                'old_question' => $oldContent['text'] ?? '',
                'new_answer' => $dup['correct_answer'],
                'usage_count' => $this->db->count('quiz_exam_questions', ['question_id' => $dup['duplicate_match_id']])
            ];
        }

        $this->view('admin/quiz/batch-detail', [
            'page_title' => 'Batch: ' . $batchId,
            'batch_id' => $batchId,
            'stats' => $stats,
            'clean_questions' => $cleanQuestions,
            'duplicates' => $duplicateDetails,
            'menu_active' => 'quiz-staging'
        ]);
    }

    /**
     * Delete entire batch
     */
    public function deleteBatch()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $batchId = $data['batch_id'] ?? null;

        if (!$batchId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Batch ID required']);
            return;
        }

        try {
            $this->db->delete('question_import_staging', "batch_id = :bid", ['bid' => $batchId]);
            echo json_encode(['success' => true, 'message' => 'Batch deleted successfully']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Clean old batches (older than 30 days)
     */
    public function cleanOldBatches()
    {
        try {
            $sql = "DELETE FROM question_import_staging WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $this->db->exec($sql);
            
            echo json_encode(['success' => true, 'message' => 'Old batches cleaned successfully']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
