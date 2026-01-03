<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\QuestionImportService;
use App\Services\SyllabusService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * QuestionImportController - Enterprise Staging Import System
 */
class QuestionImportController extends Controller
{
    private $db;
    private $importService;
    private $syllabusService;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
        $this->importService = new QuestionImportService();
        $this->syllabusService = new SyllabusService();
    }

    /**
     * Show the import form
     */
    public function index()
    {
        $this->view('admin/quiz/import', [
            'page_title' => 'Import Manager',
            'menu_active' => 'quiz-import'
        ]);
    }

    /**
     * Generates the "Smart" Excel Template
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Set the Headers
        $headers = ['Category', 'Question Text', 'Option A', 'Option B', 'Option C', 'Option D', 'Correct Option', 'Explanation', 'Practical Mode (Yes/No)'];
        $sheet->fromArray($headers, NULL, 'A1');

        // 2. Fetch Syllabus Nodes for the Dropdown (Unit level only)
        $nodes = $this->db->find('syllabus_nodes', ['type' => 'unit']);
        $nodeTitles = array_column($nodes, 'title');
        
        // Excel validation formulas have a limit. If too many nodes, we might need a separate sheet.
        // For now, simple list.
        $nodeListString = '"' . implode(',', $nodeTitles) . '"';

        // 3. Create the Dropdown Validation Logic
        $validation = $sheet->getCell('A2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setFormula1($nodeListString);

        // 4. Apply Dropdown to the first 1000 rows
        for ($i = 2; $i <= 1000; $i++) {
            $sheet->getCell("A$i")->setDataValidation(clone $validation);
        }
        
        // Practical Mode Dropdown
        $boolValidation = $sheet->getCell('I2')->getDataValidation();
        $boolValidation->setType(DataValidation::TYPE_LIST);
        $boolValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $boolValidation->setAllowBlank(true);
        $boolValidation->setShowDropDown(true);
        $boolValidation->setFormula1('"Yes,No"');
        for ($i = 2; $i <= 1000; $i++) {
            $sheet->getCell("I$i")->setDataValidation(clone $boolValidation);
        }

        // 5. Output the File
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="CivilCity_Question_Template.xlsx"');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Processes the uploaded file in "Chunks"
     */
    public function processChunk()
    {
        // Increase memory limit for this request
        ini_set('memory_limit', '256M');

        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $filePath = $_FILES['import_file']['tmp_name'];
        $startRow = isset($_POST['start_row']) ? (int)$_POST['start_row'] : 2;
        // If start_row is 0, it means the beginning, but Excel data starts at row 2 (1 is header)
        if ($startRow < 2) $startRow = 2;

        $batchId = 'batch_' . date('Ymd_His'); // Should be passed from frontend if continuation?
        // Actually, frontend should probably pass batchId if it's a chunk.
        // But the user JS recursively calls processChunk. It doesn't seem to pass batch_id.
        // It relies on re-uploading the file? "formData.append('import_file', file)"
        // Wait, if I re-upload the file every chunk, PHP receives a NEW temp file.
        // I need to maintain batch_id consistency.
        // But for simplicity, let's assume one "User Session" = One Batch for now, 
        // OR the JS logic handles "next_row" and expects "batch_id" at the END.
        // Re-reading usage: `ImportManager.batchId = data.batch_id` is set at the END.
        // So during processing, all chunks should belong to SAME batch?
        // If I generate a new batchId every chunk, it's fragmented.
        // Fix: Use a session-based or passed batch_id.
        // I'll grab it from POST if exists, else generate.
        
        if (isset($_POST['batch_id'])) {
            $batchId = $_POST['batch_id']; 
        } else {
             // If first chunk, maybe user provided it? Or we generate one.
             // We'll generate one and return it, frontend should send it back?
             // The JS provided doesn't send it back. It just sends 'start_row'.
             // This is a flaw in the provided JS.
             // However, I can use a session variable? Or just return it and expect frontend to store?
             // The JS provided: `ImportManager.batchId` is ONLY set when `data.eof` is true.
             // This implies the JS is flawed for batch consistency.
             // BUT, maybe the "Staging" table just accumulates by Batch ID.
             // If I generate a NEW batch ID for every chunk, the final "Load Data" will only verify the LAST chunk's batch ID.
             // This is BAD.
             // I will persist batch_id in session if not provided, or handle it.
             // Actually, I'll update the JS later to pass batch_id. 
             // For now, I'll use a fixed logic: if start_row == 2, generate new. Else, use param.
        }
        
        // Correct logic: Start Row 2 => New Batch.
        if ($startRow == 2) {
            $batchId = 'batch_' . date('Ymd_His');
            $_SESSION['current_import_batch'] = $batchId;
        } else {
            $batchId = $_SESSION['current_import_batch'] ?? 'batch_unknown';
        }

        $chunkSize = 50;
        
        try {
            /**  Create a new Reader of the type that defines the file type  **/
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            
            /**  Create a generic ChunkReadFilter  **/
            // We need a class for this. I'll include it internally or define it here.
            
            $chunkFilter = new \App\Services\ChunkReadFilter();
            $reader->setReadFilter($chunkFilter);
            
            $chunkFilter->setRows($startRow, $chunkSize);
            
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Remove rows that are before startRow (toArray respects filter? Yes, but filter returns array with keys matching row numbers)
            // We iterate.

            $processedCount = 0;
            $maxRowFound = 0;

            foreach ($rows as $rowIndex => $row) {
                if ($rowIndex < $startRow) continue;
                if ($rowIndex >= $startRow + $chunkSize) break;
                
                $maxRowFound = max($maxRowFound, $rowIndex);
                
                // Process Row: A=Category, B=Question, ...
                // Index 0 based? toArray() returns 0-indexed columns. A=0.
                
                $questionText = $row[1] ?? '';
                if (empty($questionText)) continue;

                // Strip Tags & Hash
                $cleanText = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $questionText)));
                $hash = hash('sha256', $cleanText);

                // Check Duplicate
                $existing = $this->db->findOne('quiz_questions', ['content_hash' => $hash]);

                // Insert Staging
                $this->db->insert('question_import_staging', [
                    'batch_id' => $batchId,
                    'uploader_id' => $_SESSION['user_id'] ?? 1,
                    'question_text' => $row[1],
                    'content_hash' => $hash,
                    'is_duplicate' => $existing ? 1 : 0,
                    'duplicate_match_id' => $existing ? $existing['id'] : null,
                    'status' => 'pending',
                    
                    // Extra fields
                    'options' => json_encode([
                        'a' => $row[2] ?? '',
                        'b' => $row[3] ?? '',
                        'c' => $row[4] ?? '',
                        'd' => $row[5] ?? ''
                    ]),
                    'correct_answer' => $row[6] ?? '',
                    'explanation' => $row[7] ?? '',
                    'practical_mode' => (isset($row[8]) && strtolower($row[8]) === 'yes') ? 1 : 0
                ]);
                
                $processedCount++;
            }
            
            $totalRows = $worksheet->getHighestRow(); // Approximate
            // Better: use startRow + 50 loop logic.
            
            // Check if EOF
            $eof = ($startRow + $chunkSize) > $totalRows;

            header('Content-Type: application/json');
            echo json_encode([
                'batch_id' => $batchId,
                'current_row' => $startRow + $processedCount,
                'next_row' => $startRow + $chunkSize,
                'total_rows' => $totalRows,
                'eof' => $eof
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get Stats for Staging Area
     */
    public function stagingStats($batchId)
    {
        // Clean questions
        $cleanRows = $this->db->query("SELECT * FROM question_import_staging WHERE batch_id = ? AND is_duplicate = 0", [$batchId])->fetchAll();
        
        // Duplicate rows with extra info
        $duplicateRows = [];
        $dups = $this->db->query("SELECT * FROM question_import_staging WHERE batch_id = ? AND is_duplicate = 1", [$batchId])->fetchAll();
        
        foreach ($dups as $dup) {
            // Fetch old question data
            $oldQ = $this->db->findOne('quiz_questions', ['id' => $dup['duplicate_match_id']]);
            $oldContent = json_decode($oldQ['content'], true);
            $oldText = $oldContent['text'] ?? '';
            
            $newOpts = json_decode($dup['options'], true);
            $newCount = count(array_filter($newOpts));

            $usageCount = $this->db->count('quiz_exam_questions', ['question_id' => $dup['duplicate_match_id']]);

            $duplicateRows[] = [
                'id' => $dup['id'],
                'match_id' => $dup['duplicate_match_id'],
                'new_question' => $dup['question_text'],
                'old_question' => $oldText,
                'new_options_count' => $newCount,
                'new_answer' => $dup['correct_answer'],
                'usage_count' => $usageCount
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'clean_count' => count($cleanRows),
            'duplicate_count' => count($duplicateRows),
            'clean_rows' => array_map(function($r) {
                return [
                    'category' => 'Mapped ID ' . $r['syllabus_node_id'], // Todo: fetch title
                    'question_text' => $r['question_text'],
                    'type' => 'MCQ'
                ];
            }, $cleanRows),
            'duplicate_rows' => $duplicateRows
        ]);
    }

    /**
     * Resolve Conflict
     */
    public function resolve()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $action = $data['action'];

        if ($action === 'skip') {
            $this->db->delete('question_import_staging', "id = :id", ['id' => $id]);
        } elseif ($action === 'overwrite') {
            // Find staging row
            $staging = $this->db->findOne('question_import_staging', ['id' => $id]);
            if ($staging) {
                // Update live question
                $content = json_encode(['text' => $staging['question_text']]);
                // Options JSON is already in staging['options']
                
                $this->db->update('quiz_questions', [
                    'content' => $content,
                    'options' => $staging['options'],
                    'correct_answer' => $staging['correct_answer'],
                    'explanation' => $staging['explanation']
                ], "id = :id", ['id' => $staging['duplicate_match_id']]);
                
                // Remove from staging
                $this->db->delete('question_import_staging', "id = :id", ['id' => $id]);
            }
        }

        echo json_encode(['status' => 'success']);
    }

    /**
     * Publish All Clean
     */
    public function publishClean()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $batchId = $data['batch_id'];

        $cleanRows = $this->db->find('question_import_staging', ['batch_id' => $batchId, 'is_duplicate' => 0]);
        
        foreach ($cleanRows as $row) {
             $content = json_encode(['text' => $row['question_text']]);
             
             $this->db->insert('quiz_questions', [
                 'content' => $content,
                 'options' => $row['options'],
                 'correct_answer' => $row['correct_answer'],
                 'explanation' => $row['explanation'],
                 'content_hash' => $row['content_hash'],
                 'status' => 1,
                 'created_at' => date('Y-m-d H:i:s')
             ]);
             
             $this->db->delete('question_import_staging', "id = :id", ['id' => $row['id']]);
        }
        
        echo json_encode(['status' => 'success']);
    }
}

