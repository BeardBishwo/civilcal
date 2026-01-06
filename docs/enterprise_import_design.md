# Enterprise Question Import System - Complete Design

## 🎯 Problem Analysis

Based on your screenshots, the PSC question system has a **complex hierarchical structure**:

### Question Categorization Hierarchy

```
1. Course (e.g., Civil Engineering)
   ↓
2. Education Level (e.g., Bachelor, Diploma)
   ↓
3. Main Category (e.g., Structural Engineering)
   ↓
4. Sub-Category (e.g., RCC Design)
   ↓
5. Position Level (e.g., Sub-Engineer, Engineer)
   ↓
6. Difficulty (Easy, Medium, Hard)
   ↓
7. Question Type (MCQ, True/False, Multi-Select, Sequence)
   ↓
8. Governance Status (Draft, Approved, Archive)
```

### Additional Metadata
- **Marks**: Default marks for correct answer
- **Negative Marks**: Penalty for wrong answer
- **Shuffle Options**: Whether to randomize options
- **Active Status**: Is question active?
- **Target Audience**: Universal, PSC Only, World Only

## 📊 Excel Template Design

### Template Structure (Columns)

| Col | Field Name | Type | Required | Description | Validation |
|-----|------------|------|----------|-------------|------------|
| A | Course Code | Dropdown | Yes | Course identifier | From `syllabus_nodes` where `type='course'` |
| B | Education Level Code | Dropdown | Yes | Education level | From `syllabus_nodes` where `type='education_level'` |
| C | Main Category Code | Dropdown | Yes | Main category | From `syllabus_nodes` where `type='category'` |
| D | Sub-Category Code | Dropdown | Yes | Sub-category | From `syllabus_nodes` where `type='sub_category'` |
| E | Position Level Code | Dropdown | Yes | Target position | From `position_levels` table |
| F | Question Type | Dropdown | Yes | MCQ, TF, MULTI, SEQUENCE, NUMERICAL, TEXT | Fixed list |
| G | Question Text | Text | Yes | The question | Max 5000 chars |
| H | Option A | Text | Conditional | First option | Required for MCQ/MULTI |
| I | Option B | Text | Conditional | Second option | Required for MCQ/MULTI |
| J | Option C | Text | Conditional | Third option | Optional |
| K | Option D | Text | Conditional | Fourth option | Optional |
| L | Option E | Text | Conditional | Fifth option | Optional |
| M | Correct Answer(s) | Text | Yes | a, b, c, d, e OR a,b,c for multi | Comma-separated for MULTI |
| N | Explanation | Text | Optional | Answer explanation | Max 2000 chars |
| O | Difficulty | Dropdown | Yes | 1=Easy, 2=Medium, 3=Hard | 1-3 |
| P | Marks | Number | Yes | Default marks | Min 0.25, Max 10 |
| Q | Negative Marks | Number | Yes | Negative marking | Min 0, Max 5 |
| R | Governance Status | Dropdown | Yes | draft, approved, archive | Fixed list |
| S | Active Status | Dropdown | Yes | Yes/No | Boolean |
| T | Shuffle Options | Dropdown | Yes | Yes/No | Boolean |
| U | Target Audience | Dropdown | Yes | universal, psc_only, world_only | Fixed list |
| V | Tags | Text | Optional | Comma-separated tags | e.g., "concrete,design,beam" |
| W | Unique Code | Text | Optional | Admin reference code | Auto-generated if empty |

## 🔧 Smart Features

### 1. Cascading Dropdowns
- **Course** → Filters **Education Levels** (only levels under that course)
- **Education Level** → Filters **Main Categories** (only categories under that level)
- **Main Category** → Filters **Sub-Categories** (only sub-cats under that category)

### 2. Auto-Validation
- **Question Type** validation:
  - If MCQ/MULTI: Options A & B required
  - If TF: Only 2 options allowed (True/False)
  - If NUMERICAL: No options needed
  - If TEXT: No options needed

### 3. Auto-Mapping Logic
```
Excel Row → System Mapping:

1. Course Code → Find syllabus_node_id (course)
2. Education Level Code → Find syllabus_node_id (edu_level)
3. Main Category Code → Find syllabus_node_id (category)
4. Sub-Category Code → Find syllabus_node_id (sub_category)
5. Position Level Code → Find position_level_id

Then create question with:
- course_id = course syllabus_node_id
- edu_level_id = edu_level syllabus_node_id
- category_id = category syllabus_node_id
- sub_category_id = sub_category syllabus_node_id

AND insert into:
- question_stream_map (for filtering)
- question_position_levels (for position targeting)
```

## 📋 Template Sheets

### Sheet 1: "Questions" (Main Data Entry)
- All question data as per columns above
- Smart dropdowns with data validation
- Conditional formatting for errors

### Sheet 2: "Courses" (Reference Data)
- Course Code | Course Name
- Auto-populated from database
- Read-only

### Sheet 3: "Education Levels" (Reference Data)
- Level Code | Level Name | Parent Course Code
- Auto-populated from database
- Read-only

### Sheet 4: "Main Categories" (Reference Data)
- Category Code | Category Name | Parent Level Code
- Auto-populated from database
- Read-only

### Sheet 5: "Sub-Categories" (Reference Data)
- Sub-Cat Code | Sub-Cat Name | Parent Category Code
- Auto-populated from database
- Read-only

### Sheet 6: "Position Levels" (Reference Data)
- Position Code | Position Name | Order
- Auto-populated from database
- Read-only

### Sheet 7: "Instructions" (User Guide)
- Step-by-step guide
- Examples
- Common errors
- FAQ

## 🎨 Template Generation Code

### Enhanced `downloadTemplate()` Method

```php
public function downloadTemplate()
{
    $spreadsheet = new Spreadsheet();
    
    // Sheet 1: Questions (Main)
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Questions');
    
    // Headers
    $headers = [
        'Course Code', 'Education Level Code', 'Main Category Code', 
        'Sub-Category Code', 'Position Level Code', 'Question Type',
        'Question Text', 'Option A', 'Option B', 'Option C', 'Option D', 'Option E',
        'Correct Answer(s)', 'Explanation', 'Difficulty', 'Marks', 'Negative Marks',
        'Governance Status', 'Active Status', 'Shuffle Options', 'Target Audience',
        'Tags', 'Unique Code'
    ];
    $sheet->fromArray($headers, NULL, 'A1');
    
    // Style header
    $sheet->getStyle('A1:W1')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667EEA']]
    ]);
    
    // Add dropdowns
    $this->addCourseDropdown($sheet, 'A');
    $this->addQuestionTypeDropdown($sheet, 'F');
    $this->addDifficultyDropdown($sheet, 'O');
    $this->addStatusDropdown($sheet, 'R');
    $this->addYesNoDropdown($sheet, 'S');
    $this->addYesNoDropdown($sheet, 'T');
    $this->addAudienceDropdown($sheet, 'U');
    
    // Add reference sheets
    $this->addCourseSheet($spreadsheet);
    $this->addEducationLevelSheet($spreadsheet);
    $this->addCategorySheet($spreadsheet);
    $this->addSubCategorySheet($spreadsheet);
    $this->addPositionLevelSheet($spreadsheet);
    $this->addInstructionsSheet($spreadsheet);
    
    // Export
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="PSC_Question_Import_Template.xlsx"');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
```

## 🔄 Import Processing Logic

### Step 1: Parse Excel Row
```php
$rowData = [
    'course_code' => $row[0],
    'edu_level_code' => $row[1],
    'category_code' => $row[2],
    'sub_category_code' => $row[3],
    'position_level_code' => $row[4],
    'question_type' => $row[5],
    'question_text' => $row[6],
    // ... etc
];
```

### Step 2: Resolve Hierarchy
```php
// Find syllabus node IDs
$courseNode = $this->db->findOne('syllabus_nodes', [
    'type' => 'course',
    'slug' => $rowData['course_code']
]);

$eduLevelNode = $this->db->findOne('syllabus_nodes', [
    'type' => 'education_level',
    'slug' => $rowData['edu_level_code'],
    'parent_id' => $courseNode['id']
]);

$categoryNode = $this->db->findOne('syllabus_nodes', [
    'type' => 'category',
    'slug' => $rowData['category_code'],
    'parent_id' => $eduLevelNode['id']
]);

$subCategoryNode = $this->db->findOne('syllabus_nodes', [
    'type' => 'sub_category',
    'slug' => $rowData['sub_category_code'],
    'parent_id' => $categoryNode['id']
]);

$positionLevel = $this->db->findOne('position_levels', [
    'slug' => $rowData['position_level_code']
]);
```

### Step 3: Create Question
```php
$questionId = $this->db->insert('quiz_questions', [
    'course_id' => $courseNode['id'],
    'edu_level_id' => $eduLevelNode['id'],
    'category_id' => $categoryNode['id'],
    'sub_category_id' => $subCategoryNode['id'],
    'type' => $this->mapQuestionType($rowData['question_type']),
    'content' => json_encode(['text' => $rowData['question_text']]),
    'options' => $this->buildOptions($rowData),
    'correct_answer' => $rowData['correct_answer'],
    'answer_explanation' => $rowData['explanation'],
    'difficulty_level' => $rowData['difficulty'],
    'default_marks' => $rowData['marks'],
    'default_negative_marks' => $rowData['negative_marks'],
    'status' => $rowData['governance_status'],
    'is_active' => $rowData['active_status'] === 'Yes' ? 1 : 0,
    'target_audience' => $rowData['target_audience'],
    'tags' => json_encode(explode(',', $rowData['tags'])),
    'unique_code' => $rowData['unique_code'] ?: $this->generateUniqueCode()
]);
```

### Step 4: Create Associations
```php
// Link to position level
$this->db->insert('question_position_levels', [
    'question_id' => $questionId,
    'position_level_id' => $positionLevel['id']
]);

// Create stream map for filtering
$this->db->insert('question_stream_map', [
    'question_id' => $questionId,
    'course_id' => $courseNode['id'],
    'edu_level_id' => $eduLevelNode['id'],
    'category_id' => $categoryNode['id'],
    'sub_category_id' => $subCategoryNode['id']
]);
```

## 📝 Sample CSV Format

```csv
Course Code,Education Level Code,Main Category Code,Sub-Category Code,Position Level Code,Question Type,Question Text,Option A,Option B,Option C,Option D,Option E,Correct Answer(s),Explanation,Difficulty,Marks,Negative Marks,Governance Status,Active Status,Shuffle Options,Target Audience,Tags,Unique Code
civil-eng,bachelor,structural,rcc-design,sub-engineer,MCQ,"What is the minimum grade of concrete for RCC work?",M10,M15,M20,M25,,c,"M20 is the minimum grade as per IS 456",2,1,0.25,approved,Yes,Yes,universal,"concrete,rcc,design",Q001
civil-eng,bachelor,structural,rcc-design,engineer,TF,"Is M10 grade concrete suitable for RCC work?",True,False,,,,b,"M10 is only for PCC, not RCC",1,1,0.25,approved,Yes,No,psc_only,"concrete,rcc",Q002
```

## ✅ Validation Rules

### Pre-Import Validation
1. **Required Fields**: Check all required columns are filled
2. **Code Validation**: Verify all codes exist in database
3. **Hierarchy Validation**: Ensure parent-child relationships are valid
4. **Question Type Validation**: Check options match question type
5. **Answer Validation**: Verify correct answer exists in options
6. **Numeric Validation**: Marks, negative marks are valid numbers
7. **Duplicate Detection**: Check content hash against existing questions

### Error Reporting
```
Row 5: Invalid course code 'xyz-course'
Row 7: Missing required field 'Question Text'
Row 12: Answer 'f' not found in options (a, b, c, d, e)
Row 15: Sub-category 'abc' does not belong to category 'structural'
```

## 🚀 Zero Manual Work Features

1. **Auto-Complete**: Codes auto-suggest as you type
2. **Smart Defaults**: 
   - Marks = 1
   - Negative Marks = 0.25
   - Difficulty = 2 (Medium)
   - Status = approved
   - Active = Yes
3. **Bulk Operations**: Import 1000+ questions in one go
4. **Error Recovery**: Skip invalid rows, continue processing
5. **Rollback**: If import fails, no partial data saved
6. **Audit Trail**: Log who imported what and when

## 📊 Success Metrics

- **Import Speed**: 100 questions/second
- **Accuracy**: 99.9% correct categorization
- **Error Rate**: < 0.1% due to validation
- **User Effort**: Zero manual mapping needed

## 🎓 User Training

### Quick Start (5 minutes)
1. Download template
2. See reference sheets for valid codes
3. Fill in questions
4. Upload
5. Review staging queue
6. Publish

### Advanced Features (10 minutes)
- Cascading dropdowns
- Multi-select questions
- Position level targeting
- Governance workflow
