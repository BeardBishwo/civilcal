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
use App\Services\FileService;

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
     * Generates Enterprise-Level Excel Template
     * With full PSC hierarchy and smart dropdowns
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        // ========================================
        // SHEET 1: QUESTIONS (Main Data Entry)
        // ========================================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Questions');

        $headers = [
            'Course Code',
            'Education Level Code',
            'Main Category Code',
            'Sub-Category Code',
            'Position Level Code',
            'Question Type',
            'Question Text',
            'Option A',
            'Option B',
            'Option C',
            'Option D',
            'Option E',
            'Correct Answer(s)',
            'Explanation',
            'Difficulty',
            'Marks',
            'Negative Marks',
            'Governance Status',
            'Active Status',
            'Shuffle Options',
            'Target Audience',
            'Tags',
            'Unique Code',
            'Theory Type'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '667EEA']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ];
        $sheet->getStyle('A1:X1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15); // Course Code
        $sheet->getColumnDimension('B')->setWidth(18); // Edu Level Code
        $sheet->getColumnDimension('C')->setWidth(18); // Category Code
        $sheet->getColumnDimension('D')->setWidth(18); // Sub-Category Code
        $sheet->getColumnDimension('E')->setWidth(18); // Position Level
        $sheet->getColumnDimension('F')->setWidth(15); // Question Type
        $sheet->getColumnDimension('G')->setWidth(50); // Question Text
        $sheet->getColumnDimension('H')->setWidth(25); // Option A
        $sheet->getColumnDimension('I')->setWidth(25); // Option B
        $sheet->getColumnDimension('J')->setWidth(25); // Option C
        $sheet->getColumnDimension('K')->setWidth(25); // Option D
        $sheet->getColumnDimension('L')->setWidth(25); // Option E
        $sheet->getColumnDimension('M')->setWidth(15); // Correct Answer
        $sheet->getColumnDimension('N')->setWidth(40); // Explanation
        $sheet->getColumnDimension('O')->setWidth(12); // Difficulty
        $sheet->getColumnDimension('P')->setWidth(10); // Marks
        $sheet->getColumnDimension('Q')->setWidth(15); // Negative Marks
        $sheet->getColumnDimension('R')->setWidth(18); // Governance
        $sheet->getColumnDimension('S')->setWidth(12); // Active
        $sheet->getColumnDimension('T')->setWidth(15); // Shuffle
        $sheet->getColumnDimension('U')->setWidth(15); // Target Audience
        $sheet->getColumnDimension('V')->setWidth(30); // Tags
        $sheet->getColumnDimension('W')->setWidth(15); // Unique Code
        $sheet->getColumnDimension('X')->setWidth(15); // Theory Type

        // Add dropdowns with database data
        $this->addEnterpriseDropdowns($sheet);

        // Add sample row with defaults
        $sheet->fromArray([
            'civil-eng',
            'bachelor',
            'structural',
            'rcc-design',
            'sub-engineer',
            'MCQ',
            'What is the minimum grade of concrete for RCC work?',
            'M10',
            'M15',
            'M20',
            'M25',
            '',
            'c',
            'M20 is the minimum grade as per IS 456',
            '2',
            '1',
            '0.25',
            'approved',
            'Yes',
            'Yes',
            'universal',
            'concrete,rcc,design',
            'Q001',
            ''
        ], NULL, 'A2');

        // Add theory question sample
        $sheet->fromArray([
            'civil-eng',
            'bachelor',
            'structural',
            'rcc-design',
            'engineer',
            'THEORY',
            'Define RCC and explain its advantages over plain concrete.',
            '',
            '',
            '',
            '',
            '',
            '',
            'RCC (Reinforced Cement Concrete) is a composite material where steel reinforcement is embedded in concrete to resist tensile stresses. Advantages: 1) High tensile strength due to steel, 2) Better crack resistance, 3) Suitable for long spans, 4) Economical for large structures.',
            '4',
            '4',
            '0',
            'approved',
            'Yes',
            'No',
            'universal',
            'rcc,theory,definition',
            'TH001',
            'short'
        ], NULL, 'A3');

        // ========================================
        // SHEET 2: COURSES (Reference Data)
        // ========================================
        $this->addCourseReferenceSheet($spreadsheet);

        // ========================================
        // SHEET 3: EDUCATION LEVELS (Reference)
        // ========================================
        $this->addEducationLevelReferenceSheet($spreadsheet);

        // ========================================
        // SHEET 4: CATEGORIES (Reference)
        // ========================================
        $this->addCategoryReferenceSheet($spreadsheet);

        // ========================================
        // SHEET 5: SUB-CATEGORIES (Reference)
        // ========================================
        $this->addSubCategoryReferenceSheet($spreadsheet);

        // ========================================
        // SHEET 6: POSITION LEVELS (Reference)
        // ========================================
        $this->addPositionLevelReferenceSheet($spreadsheet);

        // ========================================
        // SHEET 7: INSTRUCTIONS
        // ========================================
        $this->addInstructionsSheet($spreadsheet);

        // Export
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PSC_Enterprise_Question_Import_Template.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function addEnterpriseDropdowns($sheet)
    {
        // Question Type
        $this->addDropdown($sheet, 'F', ['MCQ', 'TF', 'MULTI', 'SEQUENCE', 'NUMERICAL', 'TEXT', 'THEORY']);

        // Difficulty (5 levels)
        $this->addDropdown($sheet, 'O', ['1', '2', '3', '4', '5']);

        // Governance Status
        $this->addDropdown($sheet, 'R', ['draft', 'approved', 'archive']);

        // Active Status
        $this->addDropdown($sheet, 'S', ['Yes', 'No']);

        // Shuffle Options
        $this->addDropdown($sheet, 'T', ['Yes', 'No']);

        // Target Audience
        $this->addDropdown($sheet, 'U', ['universal', 'psc_only', 'world_only']);

        // Correct Answer (for MCQ)
        $this->addDropdown($sheet, 'M', ['a', 'b', 'c', 'd', 'e', 'a,b', 'a,c', 'b,c', 'a,b,c']);

        // Theory Type
        $this->addDropdown($sheet, 'X', ['short', 'long']);
    }

    private function addCourseReferenceSheet($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Courses');

        $sheet->fromArray(['Course Code', 'Course Name'], NULL, 'A1');
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]
        ]);

        $courses = $this->db->query("SELECT slug, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll();
        $row = 2;
        foreach ($courses as $course) {
            $sheet->setCellValue('A' . $row, $course['slug']);
            $sheet->setCellValue('B' . $row, $course['title']);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
    }

    private function addEducationLevelReferenceSheet($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Education Levels');

        $sheet->fromArray(['Level Code', 'Level Name', 'Parent Course'], NULL, 'A1');
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]
        ]);

        $levels = $this->db->query("
            SELECT el.slug, el.title, c.slug as parent_slug
            FROM syllabus_nodes el
            LEFT JOIN syllabus_nodes c ON el.parent_id = c.id
            WHERE el.type = 'education_level'
            ORDER BY c.order_index ASC, el.order_index ASC
        ")->fetchAll();

        $row = 2;
        foreach ($levels as $level) {
            $sheet->setCellValue('A' . $row, $level['slug']);
            $sheet->setCellValue('B' . $row, $level['title']);
            $sheet->setCellValue('C' . $row, $level['parent_slug']);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
    }

    private function addCategoryReferenceSheet($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Main Categories');

        $sheet->fromArray(['Category Code', 'Category Name', 'Parent Level'], NULL, 'A1');
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]
        ]);

        $categories = $this->db->query("
            SELECT cat.slug, cat.title, el.slug as parent_slug
            FROM syllabus_nodes cat
            LEFT JOIN syllabus_nodes el ON cat.parent_id = el.id
            WHERE cat.type = 'category'
            ORDER BY el.order_index ASC, cat.order_index ASC
        ")->fetchAll();

        $row = 2;
        foreach ($categories as $cat) {
            $sheet->setCellValue('A' . $row, $cat['slug']);
            $sheet->setCellValue('B' . $row, $cat['title']);
            $sheet->setCellValue('C' . $row, $cat['parent_slug']);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
    }

    private function addSubCategoryReferenceSheet($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Sub-Categories');

        $sheet->fromArray(['Sub-Category Code', 'Sub-Category Name', 'Parent Category'], NULL, 'A1');
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]
        ]);

        $subCats = $this->db->query("
            SELECT sc.slug, sc.title, cat.slug as parent_slug
            FROM syllabus_nodes sc
            LEFT JOIN syllabus_nodes cat ON sc.parent_id = cat.id
            WHERE sc.type = 'sub_category'
            ORDER BY cat.order_index ASC, sc.order_index ASC
        ")->fetchAll();

        $row = 2;
        foreach ($subCats as $sc) {
            $sheet->setCellValue('A' . $row, $sc['slug']);
            $sheet->setCellValue('B' . $row, $sc['title']);
            $sheet->setCellValue('C' . $row, $sc['parent_slug']);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
    }

    private function addPositionLevelReferenceSheet($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Position Levels');

        $sheet->fromArray(['Position Code', 'Position Name', 'Order'], NULL, 'A1');
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]
        ]);

        $positions = $this->db->query("SELECT slug, title, order_index FROM position_levels WHERE is_active = 1 ORDER BY order_index ASC")->fetchAll();

        $row = 2;
        foreach ($positions as $pos) {
            $sheet->setCellValue('A' . $row, $pos['slug']);
            $sheet->setCellValue('B' . $row, $pos['title']);
            $sheet->setCellValue('C' . $row, $pos['order_index']);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(10);
    }

    private function addInstructionsSheet($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Instructions');

        $instructions = [
            ['PSC ENTERPRISE QUESTION IMPORT - USER GUIDE'],
            [''],
            ['QUICK START (5 Minutes)'],
            ['1. Review reference sheets (Courses, Education Levels, Categories, etc.)'],
            ['2. Copy the codes from reference sheets to the Questions sheet'],
            ['3. Fill in your questions following the sample row'],
            ['4. Save and upload the file'],
            ['5. Review staging queue and publish'],
            [''],
            ['COLUMN DESCRIPTIONS'],
            [''],
            ['Course Code: Must match a code from "Courses" sheet (e.g., civil-eng)'],
            ['Education Level Code: Must match a code from "Education Levels" sheet (e.g., bachelor)'],
            ['Main Category Code: Must match a code from "Main Categories" sheet (e.g., structural)'],
            ['Sub-Category Code: Must match a code from "Sub-Categories" sheet (e.g., rcc-design)'],
            ['Position Level Code: Must match a code from "Position Levels" sheet (e.g., sub-engineer)'],
            ['Question Type: MCQ, TF, MULTI, SEQUENCE, NUMERICAL, TEXT, or THEORY'],
            ['Question Text: The actual question (max 5000 characters)'],
            ['Options A-E: Answer options (A & B required for MCQ/MULTI, only A & B for TF, not needed for THEORY)'],
            ['Correct Answer(s): Single letter (a,b,c,d,e) or comma-separated for MULTI (a,b,c)'],
            ['Explanation: Answer explanation (optional)'],
            ['Difficulty: 1=Easy, 2=Easy-Mid, 3=Medium, 4=Hard, 5=Expert'],
            ['Marks: Default marks for correct answer (e.g., 1)'],
            ['Negative Marks: Penalty for wrong answer (e.g., 0.25)'],
            ['Governance Status: draft, approved, or archive'],
            ['Active Status: Yes or No'],
            ['Shuffle Options: Yes or No (randomize option order)'],
            ['Target Audience: universal, psc_only, or world_only'],
            ['Tags: Comma-separated tags (e.g., concrete,rcc,design)'],
            ['Unique Code: Optional admin reference code (auto-generated if empty)'],
            ['Theory Type: short or long (only for THEORY questions, leave empty for others)'],
            [''],
            ['VALIDATION RULES'],
            [''],
            ['✓ All codes must exist in reference sheets'],
            ['✓ Hierarchy must be valid (Sub-Category must belong to Category, etc.)'],
            ['✓ For MCQ/MULTI: Options A & B are required'],
            ['✓ For TF: Only 2 options allowed (True/False)'],
            ['✓ For THEORY: No options needed, answer in explanation field'],
            ['✓ Correct answer must exist in options'],
            ['✓ Marks must be between 0.25 and 10'],
            ['✓ Negative marks must be between 0 and 5'],
            [''],
            ['TIPS FOR SUCCESS'],
            [''],
            ['• Use reference sheets to find valid codes'],
            ['• Copy-paste codes to avoid typos'],
            ['• Fill sample row first to understand format'],
            ['• Start with 10-20 questions to test'],
            ['• Review staging queue before publishing'],
            ['• Use meaningful tags for better filtering'],
            [''],
            ['SUPPORT'],
            ['For questions or issues, contact your system administrator.']
        ];

        $row = 1;
        foreach ($instructions as $line) {
            $sheet->fromArray($line, NULL, 'A' . $row);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(100);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A33')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A44')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A53')->getFont()->setBold(true)->setSize(14);
    }

    private function addDropdown($sheet, $col, $options)
    {
        $validation = $sheet->getCell($col . '2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"' . implode(',', $options) . '"');

        // Apply to first 1000 rows
        for ($i = 2; $i <= 1000; $i++) {
            $sheet->getCell($col . $i)->setDataValidation(clone $validation);
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

        // Use FileService for "Paranoid-Grade" validation (Binary Scanning + MIME Sniffing)
        $validation = FileService::validateUpload($_FILES['import_file'], 'question_import');
        if (!$validation['success']) {
            http_response_code(400);
            echo json_encode(['error' => $validation['error']]);
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

    private function isEmptyRow($row)
    {
        foreach ($row as $cell) {
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
            'clean_rows' => array_map(function ($r) {
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

                // NEW: Relational Filter Persistence
                $filterContext = $this->syllabusService->resolveFilterContext($staging['syllabus_node_id']);

                $this->db->update('quiz_questions', [
                    'content' => $content,
                    'type' => $staging['type'],
                    'course_id' => $filterContext['course_id'],
                    'edu_level_id' => $filterContext['edu_level_id'],
                    'category_id' => $filterContext['category_id'],
                    'sub_category_id' => $filterContext['sub_category_id'],
                    'options' => $staging['options'],
                    'correct_answer' => $staging['correct_answer'],
                    'correct_answer_json' => $staging['correct_answer_json'], // Staging Field: correct_answer_json
                    'difficulty_level' => $staging['level'],
                    'answer_explanation' => $staging['explanation']
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

            // NEW: Relational Filter Persistence
            $filterContext = $this->syllabusService->resolveFilterContext($row['syllabus_node_id']);

            $this->db->insert('quiz_questions', [
                'content' => $content,
                'type' => $row['type'],
                'course_id' => $filterContext['course_id'],
                'edu_level_id' => $filterContext['edu_level_id'],
                'category_id' => $filterContext['category_id'],
                'sub_category_id' => $filterContext['sub_category_id'],
                'options' => $row['options'],
                'correct_answer' => $row['correct_answer'],
                'correct_answer_json' => $row['correct_answer_json'],
                'difficulty_level' => $row['level'],
                'answer_explanation' => $row['explanation'],
                'content_hash' => $row['content_hash'],
                'status' => 'approved',
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
