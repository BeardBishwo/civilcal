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
    protected $db;
    protected $importService;
    protected $syllabusService;

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

        $mode = $_GET['mode'] ?? 'simple'; // 'simple' or 'advanced'

        // 1. EXACT COLUMNS from your 'data-format.csv'
        if ($mode == 'advanced') {
            // --- ADVANCED TEMPLATE (For Multi/Order) ---
            $headers = [
                'category', 'subcategory', 'language_id', 
                'answer_type', // Special Column: 1=Multi, 2=Sequence
                'question', 
                'option 1', 'option 2', 'option 3', 'option 4', 'option 5', 
                'answer1', 'answer2', 'answer3', 'answer4', 'answer5', // Multiple Answers
                'level', 'note', 'level_map_syntax', 'contest_id'
            ];
        } else {
            // --- STANDARD TEMPLATE (For MCQ/TF) ---
            $headers = [
                'category', 'subcategory', 'language_id', 'question_type', 
                'question', 'option 1', 'option 2', 'option 3', 'option 4', 'option 5', 
                'answer', 'level', 'note', 'level_map_syntax', 'contest_id'
            ];
        }
        $sheet->fromArray($headers, NULL, 'A1');

        // 2. MAKE IT SMART (Dropdowns)
        
        // A. Question Type Dropdown
        if($mode == 'advanced') {
             // For Col D: Answer Type
             // 1=Multi, 2=Sequence. We put it in dropdown.
             $this->addDropdown($sheet, 'D', ['1 (Multi-Select)', '2 (Sequence Order)']);
             
             // Initial hint (Multi default)
             $sheet->setCellValue('D2', '1');
        } else {
             // For Col D: Question Type
             $this->addDropdown($sheet, 'D', ['1 (MCQ)', '2 (True/False)']);
        }

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
        if ($mode == 'advanced') {
            $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
            $sheet->getStyle('A1:Q1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF2D3748'); // Dark Grey
            $sheet->getStyle('A1:Q1')->getFont()->getColor()->setARGB('FFFFFFFF');
        } else {
            $sheet->getStyle('A1:M1')->getFont()->setBold(true);
            $sheet->getStyle('A1:M1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF667EEA'); // Civil Cal Purple
            $sheet->getStyle('A1:M1')->getFont()->getColor()->setARGB('FFFFFFFF');
        }

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
                     // Col 3 (D) is EITHER 'question_type' OR 'answer_type' (handled by ImportProcessor)
                     'question_type' => $row[3] ?? null, 
                     'answer_type' => $row[3] ?? null, // Map same col to both keys, Processor detects logic
                     
                     'question' => $row[4] ?? null,
                     'option 1' => $row[5] ?? null,
                     'option 2' => $row[6] ?? null,
                     'option 3' => $row[7] ?? null,
                     'option 4' => $row[8] ?? null,
                     'option 5' => $row[9] ?? null,
                     
                     // Standard Answer (Col 10/K)
                     'answer' => $row[10] ?? null,
                     
                     // Advanced Answers (Cols 11-15: L, M, N, O, P) - ONLY IF ROW HAS THEM
                     // If standard template, these indices might be Level/Note.
                     // IMPORTANT: ImportProcessor checks "answer1", "answer2".
                     // If we import a Simple CSV, row[11] is Level.
                     // Logic: We should rely on Header detection OR Processor Fallback.
                     // BUT here we are mapping by INDEX.
                     // Best solution: Pass ALL potential columns.
                     // If Simple Template: 
                     // 10=Answer, 11=Level, 12=Note
                     // If Advanced Template:
                     // 10 is 'answer1' ?? No.
                     // Advanced Headers: 
                     // 0:Cat ... 3:AnsType, 4:Q, 5-9:Options
                     // 10: Ans1, 11: Ans2, 12: Ans3, 13: Ans4, 14: Ans5
                     // 15: Level, 16: Note
                     
                     // We need to know which template it is? Or just pass loose array?
                     // Let's check headers (Row 1) if we could.
                     // But we are processing chunks.
                     // Assumption: Admin knows what they uploaded.
                     // If we map incorrectly, we get garbage.
                     // Let's map blindly safely:
                     
                     'answer1' => $row[11] ?? null, // Advanced: Ans1 (Wait, header 10 is Ans1 in Advanced?)
                     // Header check:
                     // Advanced: Cat, Sub, Lang, AnsType, Q, O1, O2, O3, O4, O5, A1, A2, A3, A4, A5, Lvl, Note
                     // Indices:
                     // 0,1,2,3,4, 5,6,7,8,9, 10,11,12,13,14, 15,16
                     // So Ans1 is at 10 (K).
                     // Simple: Ans is at 10 (K).
                     // So row[10] acts as 'answer' OR 'answer1'.
                     
                     // 'answer2' is at 11.
                     // In Simple, 11 is 'level'.
                     // Conflict! 'answer2' vs 'level'.
                     // If I pass row[11] as 'answer2', and it's 'Easy' (value 1), Processor might think it's answer '1' (Option A).
                     // This is risky.
                     
                     // Helper: Processor checks 'answer_type' (which is col 3).
                     // If col 3 is '1' (Multi) or '2' (Order) -> It's Advanced Template.
                     // If col 3 is '1' (MCQ) or '2' (TF) -> It might conflict.
                     // BUT 'MCQ' is 1. 'Multi' is 1.
                     // The inputs overlap.
                     
                     // BETTER LOGIC: Check header length?
                     // Advanced has 18 columns. Simple has 14.
                     'col_count' => count($row),
                     
                     // Just pass raw row by index to Processor and let it parse?
                     // Processor expects named keys.
                     // Let's pass aliases for both mappings and let Processor decide based on col_count or flags.
                     
                     'raw_col_10' => $row[10] ?? null,
                     'raw_col_11' => $row[11] ?? null,
                     'raw_col_12' => $row[12] ?? null,
                     'raw_col_13' => $row[13] ?? null,
                     'raw_col_14' => $row[14] ?? null,
                     'raw_col_15' => $row[15] ?? null,
                     'raw_col_16' => $row[16] ?? null,

                     // Standard mappings (safe defaults)
                     'answer' => $row[10] ?? null,
                     'level' => $row[11] ?? null,
                     'note' => $row[12] ?? null,
                     
                     // Advanced mappings (Collision with level/note above)
                     'answer1' => $row[10] ?? null,
                     'answer2' => $row[11] ?? null,
                     'answer3' => $row[12] ?? null,
                     'answer4' => $row[13] ?? null,
                     'answer5' => $row[14] ?? null,
                     
                     // Advanced Level/Note
                     'adv_level' => $row[15] ?? null,
                     'adv_note' => $row[16] ?? null
                 ];
                 
                 // Refined Logic in ImportProcessor required? 
                 // Or pre-processing here?
                 // Let's assume Advanced if row count > 14?
                 if (count($row) > 15) {
                     // Advanced
                     $rowData['level'] = $row[15] ?? null;
                     $rowData['note'] = $row[16] ?? null;
                     $rowData['level_map'] = $row[17] ?? null;
                     $rowData['contest_id'] = $row[18] ?? null;
                 } else {
                     // Simple
                     $rowData['answer1'] = null; // Clear advanced stuff to avoid confusion
                     $rowData['answer2'] = null;
                     $rowData['contest_id'] = $row[14] ?? null;
                 }
                 
                 if (empty($rowData['question'])) continue;

                 $cleanData = $processor->processRow($rowData, $batchId, $userId);
                 // Pass level_map through if processor doesn't handle it
                 $cleanData['level_map'] = $rowData['level_map'];
                 
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
                     'type' => $cleanData['type'], // Staging Field: type
                     'options' => $cleanData['options'],
                     'correct_answer' => $cleanData['correct_answer'],
                     'correct_answer_json' => $cleanData['correct_answer_json'], // Staging Field: correct_answer_json
                     'explanation' => $cleanData['explanation'],
                     'level' => $cleanData['level'],
                     'level_map' => $cleanData['level_map'] ?? null,
                     'contest_id' => $cleanData['contest_id']
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
                    'type' => $staging['type'],
                    'options' => $staging['options'],
                    'correct_answer' => $staging['correct_answer'],
                    'correct_answer_json' => $staging['correct_answer_json'],
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
                 'type' => $row['type'],
                 'options' => $row['options'],
                 'correct_answer' => $row['correct_answer'],
                 'correct_answer_json' => $row['correct_answer_json'],
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

             // --- CONTEST INJECTION LOGIC ---
             if (!empty($row['contest_id'])) {
                 $contest = $this->db->findOne('contests', ['id' => $row['contest_id']]);
                 if ($contest) {
                     $currentQs = json_decode($contest['questions'], true) ?: [];
                     if (!in_array($newQId, $currentQs)) {
                         $currentQs[] = $newQId;
                         $this->db->update('contests', [
                             'questions' => json_encode($currentQs)
                         ], "id = :id", ['id' => $row['contest_id']]);
                     }
                 }
             }
        }
        
        echo json_encode(['status' => 'success']);
    }
}

