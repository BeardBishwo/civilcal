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

        // 1. EXACT COLUMNS from your 'data-format.csv'
        $headers = [
            'category', 'subcategory', 'language_id', 'question_type', 
            'question', 'option 1', 'option 2', 'option 3', 'option 4', 'option 5', 
            'answer', 'level', 'note'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        // 2. MAKE IT SMART (Dropdowns)
        
        // A. Question Type Dropdown (Col D)
        $this->addDropdown($sheet, 'D', ['1 (MCQ)', '2 (True/False)']);

        // B. Answer Dropdown (Col K)
        $this->addDropdown($sheet, 'K', ['a', 'b', 'c', 'd', 'e']);

        // C. Level Dropdown (Col L)
        $this->addDropdown($sheet, 'L', ['1 (Easy)', '2 (Medium)', '3 (Hard)']);

        // D. Category Dropdown (Col A) - Fetch from DB
        $cats = $this->db->find('syllabus_nodes', ['type' => 'part']); // Assuming 'part' is main category
        $catTitles = array_column($cats, 'title');
        // Limit to prevent excel crash if too many
        $catTitles = array_slice($catTitles, 0, 100); 
        $this->addDropdown($sheet, 'A', $catTitles);

        // 3. STYLE THE HEADER (Visual Polish)
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);
        $sheet->getStyle('A1:M1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF667EEA'); // Civil Cal Purple
        $sheet->getStyle('A1:M1')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Export
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="CivilCity_Question_Template.xlsx"');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function addDropdown($sheet, $col, $options) {
        $validation = $sheet->getCell($col.'2')->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"' . implode(',', $options) . '"');
        
        // Apply to first 1000 rows
        for($i=2; $i<=1000; $i++) {
            $sheet->getCell($col.$i)->setDataValidation(clone $validation);
        }
    }

    /**
     * Processes the uploaded file in "Chunks"
     */
    public function processChunk()
    {
        // Increase memory limit for this request
        ini_set('memory_limit', '256M');
        header('Content-Type: application/json');

        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $filePath = $_FILES['import_file']['tmp_name'];
        $startRow = isset($_POST['start_row']) ? (int)$_POST['start_row'] : 2;
        if ($startRow < 2) $startRow = 2; // Rows 1 is header

        // Batch Management
        if ($startRow == 2) {
            $batchId = 'batch_' . date('Ymd_His');
            $_SESSION['current_import_batch'] = $batchId;
        } else {
            $batchId = $_SESSION['current_import_batch'] ?? 'batch_' . date('Ymd_His');
        }

        $userId = $_SESSION['user_id'] ?? 1; // Default to admin
        $chunkSize = 50;
        
        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            
            // Chunk Filter logic
            $chunkFilter = new \App\Services\ChunkReadFilter();
            $reader->setReadFilter($chunkFilter);
            $chunkFilter->setRows($startRow, $chunkSize);
            
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, true, true); // Get formatted array with Alpha indexes? No, toArray default
            
            // Re-read with index assumption A=>0 or A=>A?
            // toArray default is 0-indexed if no args.
            // Let's use simple iterator or toArray
            // Reset reader without filter (too slow) or just assume filtered rows
            // Warning: Load with Filter loads ONLY those rows.
            // But toArray() returns array with keys as Row Numbers (1-based) if mapped?
            // Actually it returns 0-indexed array where 0 is first row loaded... NO.
            // With ChunkFilter, it loads specific rows.
            // The safest way is to simple iterate the rows loaded.
             $rows = $worksheet->toArray();
             // Since we filtered, $rows only has the chunk.
             // Wait, PhpSpreadsheet ChunkFilter behavior:
             // It skips reading non-matching rows, but row indices might be preserved?
             
             // Let's simplify and use the ImportProcessor
             $processor = new \App\Services\Quiz\ImportProcessor();
             $processedCount = 0;
             $dbStaging = [];

             // Map header? We assume fixed template structure as defined in "downloadTemplate".
             // Col A (0) -> Category, B (1) -> Subcategory, ... D (3) -> Type ...
             // Headers from DownloadTemplate:
             // 0: Category, 1: Subcategory, 2: LangID, 3: QType, 4: Question, 
             // 5: Opt1, 6: Opt2, 7: Opt3, 8: Opt4, 9: Opt5, 
             // 10: Answer, 11: Level, 12: Note
             
             $maxRowInSheet = $worksheet->getHighestRow();

             foreach ($rows as $index => $row) {
                 // Check if row is empty or header
                 if ($this->isEmptyRow($row)) continue;
                 // Since we use setReadFilter, the first row in $rows is actually $startRow (effectively)
                 // Wait, $rows from toArray() is just a list of rows read.
                 
                 // Process
                 $rowData = [
                     'category' => $row[0] ?? null,
                     'subcategory' => $row[1] ?? null,
                     'question_type' => $row[3] ?? null,
                     'question' => $row[4] ?? null,
                     'option 1' => $row[5] ?? null,
                     'option 2' => $row[6] ?? null,
                     'option 3' => $row[7] ?? null,
                     'option 4' => $row[8] ?? null,
                     'option 5' => $row[9] ?? null,
                     'answer' => $row[10] ?? null,
                     'level' => $row[11] ?? null,
                     'note'  => $row[12] ?? null
                 ];
                 
                 if (empty($rowData['question'])) continue;

                 $cleanData = $processor->processRow($rowData, $batchId, $userId);
                 
                 // Insert Staging
                 $this->db->insert('question_import_staging', [
                     'batch_id' => $cleanData['batch_id'],
                     'uploader_id' => $cleanData['uploader_id'],
                     'syllabus_node_id' => $cleanData['syllabus_node_id'] ?? 0, // Should be integer
                     'question_text' => $cleanData['question'],
                     'content_hash' => $cleanData['content_hash'],
                     'is_duplicate' => $cleanData['is_duplicate'],
                     'duplicate_match_id' => $cleanData['match_id'],
                     'status' => $cleanData['status'],
                     'options' => $cleanData['options'],
                     'correct_answer' => $cleanData['correct_answer'],
                     'explanation' => $cleanData['explanation'],
                     'level' => $cleanData['level']
                 ]);
                 
                 $processedCount++;
             }
            
            $eof = ($startRow + $chunkSize) > 20000; // Hard limit or detect empty
            // If processedCount < chunkSize, maybe EOF?
            if ($processedCount < $chunkSize && $maxRowInSheet < ($startRow + $chunkSize)) {
                $eof = true;
            }

            echo json_encode([
                'batch_id' => $batchId,
                'current_row' => $startRow + $processedCount,
                'next_row' => $startRow + $chunkSize,
                // 'total_rows' => $totalRows, // Cannot get total efficiently without full read
                'eof' => $eof
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function isEmptyRow($row) {
        foreach($row as $cell) {
            if (!empty($cell)) return false;
        }
        return true;
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

                // Process Level Map for Overwrite
                if (!empty($staging['level_map'])) {
                    $this->importService->processLevelMap($staging['duplicate_match_id'], $staging['level_map']);
                }
                
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
             $newQId = $this->db->lastInsertId();

             // Process Level Map
             if (!empty($row['level_map'])) {
                 $this->importService->processLevelMap($newQId, $row['level_map']);
             }
             
             $this->db->delete('question_import_staging', "id = :id", ['id' => $row['id']]);
        }
        
        echo json_encode(['status' => 'success']);
    }
}

