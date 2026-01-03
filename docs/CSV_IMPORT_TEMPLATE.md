# CSV Import Template for Multi-Context Questions

This template allows you to import questions that can serve multiple difficulty levels across different exam streams.

## Template Structure

```csv
Question Text,Option A,Option B,Option C,Option D,Correct Answer,Is Practical,Global Tags,Level Map Syntax,Explanation
```

## Column Descriptions

1. **Question Text** (Required): The question content. Use `<latex>formula</latex>` for mathematical expressions.
2. **Option A-D** (Required for MCQ): Answer options. Leave blank for numerical/true-false questions.
3. **Correct Answer** (Required): For MCQ: letter (A/B/C/D). For numerical: exact value or range.
4. **Is Practical** (TRUE/FALSE): Mark TRUE for practical/world-mode questions.
5. **Global Tags** (Comma-separated): General tags like "RCC,Basic" or "Concrete,IS456".
6. **Level Map Syntax** (Required): Maps question to streams with difficulty. Format: `L4:Hard|L5:Medium|L7:Easy`
7. **Explanation** (Optional): Answer explanation for students.

## Level Map Syntax Explained

The `Level_Map_Syntax` column is the key innovation. It allows one question to serve multiple difficulty levels:

**Format:** `StreamCode:Difficulty|StreamCode:Difficulty|...`

**Stream Codes:**
- `L4` = Level 4 (Sub-Engineer)
- `L5` = Level 5 (Engineer)
- `L7` = Level 7 (Officer)

**Difficulty Levels:**
- `Easy` = 1
- `Medium` = 3
- `Hard` = 5

**Example:** `L4:Hard|L7:Easy`
- This question is HARD for Level 4 candidates
- This question is EASY for Level 7 candidates
- Not included in Level 5 exams

## Sample Data

```csv
Question Text,Option A,Option B,Option C,Option D,Correct Answer,Is Practical,Global Tags,Level Map Syntax,Explanation
"What is the unit weight of RCC?",24 kN/m³,25 kN/m³,26 kN/m³,27 kN/m³,B,FALSE,"RCC,Basic,Materials",L4:Hard|L5:Medium|L7:Easy,"RCC (Reinforced Cement Concrete) has a standard unit weight of 25 kN/m³ as per IS codes."
"Calculate minimum cement content for M20 grade concrete (kg/m³)",300,320,350,380,B,TRUE,"Concrete,IS456,Mix Design",L4:Hard|L5:Medium|L7:Easy,"As per IS 456:2000, minimum cement content for M20 grade is 320 kg/m³"
"Analyze shear lag effect in box girder bridges",Reduces effective width,Increases effective width,No effect on width,Only affects deflection,A,FALSE,"Bridges,Analysis,Advanced",L7:Hard,"Shear lag causes non-uniform stress distribution reducing the effective flange width in box girders."
"What is the safe bearing capacity of medium dense sand?",100-200 kN/m²,200-300 kN/m²,300-400 kN/m²,400-500 kN/m²,B,TRUE,"Soil,Foundation,Geotechnical",L4:Medium|L5:Easy,"Medium dense sand typically has safe bearing capacity of 200-300 kN/m² depending on density and water table."
"Calculate critical depth in open channel flow given Q=10 m³/s and b=5m",1.47m,1.74m,2.14m,2.47m,B,FALSE,"Hydraulics,Open Channel,Calculations",L5:Hard|L7:Medium,"Critical depth yc = (Q²/gb²)^(1/3) = (100/9.81×25)^(1/3) = 1.74m"
```

## Import Process

1. **Prepare CSV**: Fill the template with your questions
2. **Upload**: Go to Admin → Quiz → Question Bank → Import
3. **Map Columns**: Verify column mapping
4. **Validate**: System checks for errors
5. **Import**: Questions are created with multi-context mappings

## Multi-Context Magic

When you import the RCC unit weight question with `L4:Hard|L5:Medium|L7:Easy`:

**Database Creates:**
1. One question in `quiz_questions` table
2. Three entries in `question_stream_map`:
   - `{question_id: 101, stream: "Level 4", difficulty_in_stream: 5}`
   - `{question_id: 101, stream: "Level 5", difficulty_in_stream: 3}`
   - `{question_id: 101, stream: "Level 7", difficulty_in_stream: 1}`

**Result:**
- Level 4 exam generator sees this as a HARD question (difficulty 5)
- Level 5 exam generator sees this as a MEDIUM question (difficulty 3)
- Level 7 exam generator sees this as an EASY question (difficulty 1)

**Zero Duplication!**

## Advanced Features

### Numerical Questions
For numerical answers, use range format:
```csv
"Calculate beam deflection...",,,,,5.2-5.4,FALSE,"Structures,Deflection",L5:Hard|L7:Medium,"Answer: 5.3mm ± 0.1mm tolerance"
```

### True/False Questions
```csv
"IS 456 allows M10 grade concrete for RCC work",True,False,,,B,FALSE,"Codes,IS456",L4:Easy|L5:Easy,"False. Minimum grade for RCC is M20 as per IS 456:2000"
```

### LaTeX Support
```csv
"Solve: <latex>\int_0^1 x^2 dx</latex>",1/4,1/3,1/2,2/3,B,FALSE,"Mathematics,Calculus",L5:Medium,"Integration of x² from 0 to 1 equals [x³/3]₀¹ = 1/3"
```

## Validation Rules

- **Question Text**: Cannot be empty, max 5000 characters
- **Options**: Required for MCQ, must have 2-6 options
- **Level Map**: Must have at least one stream mapping
- **Difficulty**: Must be Easy/Medium/Hard
- **Tags**: Max 10 tags, each max 50 characters

## Error Handling

Common errors and solutions:

| Error | Solution |
|-------|----------|
| "Invalid Level Map Syntax" | Check format: `L4:Hard|L5:Easy` |
| "Duplicate question" | Question with same text already exists |
| "Missing correct answer" | Specify correct option letter or numerical value |
| "Invalid stream code" | Use L4, L5, or L7 only |

## Best Practices

1. **Start Small**: Import 10-20 questions first to test
2. **Consistent Tagging**: Use standard tag names across all questions
3. **Difficulty Calibration**: 
   - Easy: Recall/definition questions
   - Medium: Application/calculation questions
   - Hard: Analysis/synthesis questions
4. **Practical Flag**: Mark site-relevant questions as practical for World Mode
5. **Explanations**: Always provide explanations for better learning

## Sample Import File

Download: [sample_questions_import.csv](sample_questions_import.csv)

This file contains 50 sample questions across all civil engineering subjects with proper multi-context mapping.
