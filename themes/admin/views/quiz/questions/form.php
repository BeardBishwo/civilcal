<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo isset($question) ? 'Edit Question' : 'Add New Question'; ?></h1>
        <a href="<?php echo app_base_url('admin/quiz/questions'); ?>" class="btn btn-secondary btn-sm shadow-sm rounded-pill px-3">
            <i class="fas fa-arrow-left fa-sm me-1"></i> Back to List
        </a>
    </div>

    <form id="questionForm" method="POST" action="<?php echo $action; ?>">
        <div class="row">
            
            <!-- Left Column: Classification & Content -->
            <div class="col-lg-8">
                
                <!-- 1. Syllabus Classification (New Engine) -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-primary text-white py-3 d-flex align-items-center" style="border-radius: 12px 12px 0 0;">
                        <i class="bi bi-diagram-3-fill me-2"></i>
                        <h6 class="m-0 font-weight-bold">1. Syllabus Classification</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i> 
                            Questions are now linked to the <strong>Syllabus Tree</strong>. 
                            Your last selection is remembered automatically.
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold text-gray-700">Main Category (Paper)</label>
                                    <select id="syllabus_main_id" class="form-select form-select-lg" name="syllabus_main_id" required>
                                        <option value="">Select Main Category...</option>
                                        <?php foreach ($mainCategories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>">
                                                <?php echo htmlspecialchars($cat['title']); ?> 
                                                <?php if($cat['is_premium']) echo ' (Premium)'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold text-gray-700">Sub-Category (Section/Unit) <span class="text-danger">*</span></label>
                                    <select id="syllabus_node_id" class="form-select form-select-lg" name="syllabus_node_id" required disabled>
                                        <option value="">Select Main Category First</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Legacy Hidden Fields (Bypass) -->
                        <div class="row mt-3 d-none">
                            <div class="col-md-4">
                                <label>Type</label>
                                <select id="type" class="form-control" name="type" required>
                                    <option value="mcq_single" <?php echo (isset($question) && $question['type'] == 'mcq_single') ? 'selected' : ''; ?>>MCQ (Single)</option>
                                    <option value="mcq_multi" <?php echo (isset($question) && $question['type'] == 'mcq_multi') ? 'selected' : ''; ?>>MCQ (Multi)</option>
                                    <option value="true_false" <?php echo (isset($question) && $question['type'] == 'true_false') ? 'selected' : ''; ?>>True/False</option>
                                    <option value="numerical" <?php echo (isset($question) && $question['type'] == 'numerical') ? 'selected' : ''; ?>>Numerical</option>
                                    <option value="text" <?php echo (isset($question) && $question['type'] == 'text') ? 'selected' : ''; ?>>Text</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Question Content -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">2. Question Content</h6>
                        
                        <!-- Type Selector (Moved here for better UX) -->
                        <div class="d-flex align-items-center">
                            <label class="mb-0 me-2 small text-muted">Type:</label>
                            <select id="visible_type_selector" class="form-select form-select-sm w-auto">
                                <option value="mcq_single">Multiple Choice</option>
                                <option value="true_false">True / False</option>
                                <option value="numerical">Numerical</option>
                                <option value="text">Theory</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Question Text -->
                        <div class="form-group mb-4">
                            <label class="fw-bold">Question Text</label>
                            <div class="input-group">
                                <textarea id="question_text" name="question_text" class="form-control" rows="4" placeholder="Type question here..." required><?php echo htmlspecialchars($question['content_decoded']['text'] ?? ''); ?></textarea>
                            </div>
                            <div class="mt-2 text-end">
                                <button type="button" class="btn btn-outline-info btn-sm rounded-pill" onclick="previewMath('question_text', 'math-preview-q')">
                                    <i class="fas fa-eye me-1"></i> Preview Math
                                </button>
                            </div>
                            <div id="math-preview-q" class="mt-2 p-3 border rounded bg-light" style="min-height: 40px; display:none;"></div>
                        </div>
                        
                        <!-- Image URL -->
                        <div class="form-group mb-4">
                            <label class="small text-uppercase fw-bold text-muted">Image Attachment (Optional)</label>
                            <div class="input-group">
                                <input type="text" name="question_image" id="qImage" class="form-control" placeholder="https://..." value="<?php echo htmlspecialchars($question['content_decoded']['image'] ?? ''); ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="MediaManager.open('qImage')">Browse</button>
                            </div>
                        </div>

                        <!-- Options Section -->
                        <div id="options-section" class="bg-gray-100 p-3 rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="font-weight-bold mb-0 text-gray-700">Answer Options</h6>
                                <button type="button" class="btn btn-sm btn-success rounded-pill shadow-sm" id="add-option-btn">
                                    <i class="fas fa-plus me-1"></i> Add Option
                                </button>
                            </div>
                            
                            <div id="options-container">
                                <!-- Options injected via JS -->
                            </div>

                            <!-- Numerical Specific -->
                            <div id="numerical-container" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Correct Answer (Number)</label>
                                            <input type="number" step="any" name="numerical_answer" class="form-control" id="num-answer">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tolerance (+/-)</label>
                                            <input type="number" step="any" name="numerical_tolerance" class="form-control" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right Column: Settings -->
            <div class="col-lg-4">
                
                <!-- Scoring -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 font-weight-bold text-primary">3. Configuration</h6>
                    </div>
                    <div class="card-body">
                         <div class="row g-2">
                            <div class="col-6">
                                <label class="small fw-bold">Marks</label>
                                <input type="number" step="0.1" name="default_marks" class="form-control" value="<?php echo $question['default_marks'] ?? '1.0'; ?>">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-danger">Negative</label>
                                <input type="number" step="0.1" name="default_negative_marks" class="form-control" value="<?php echo $question['default_negative_marks'] ?? '0.2'; ?>">
                            </div>
                            <div class="col-12 mt-3">
                                <label class="small fw-bold">Difficulty</label>
                                <select name="difficulty_level" class="form-select">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <option value="<?= $i ?>" <?= (isset($question) && $question['difficulty_level'] == $i) ? 'selected' : '' ?>>Level <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                             <div class="col-12 mt-3">
                                <label class="small fw-bold">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Draft</option>
                                </select>
                            </div>
                         </div>
                    </div>
                </div>

                <!-- Explanation -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-body">
                        <label class="fw-bold mb-2">Detailed Explanation</label>
                        <textarea name="answer_explanation" class="form-control" rows="5" placeholder="Why is the answer correct?"><?php echo htmlspecialchars($question['answer_explanation'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-save me-2"></i> Save Question
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

<!-- Option Template -->
<template id="option-template">
    <div class="option-row input-group mb-2 shadow-sm">
        <div class="input-group-text bg-white border-end-0">
            <input type="radio" name="correct_option_dummy" class="option-correct-input form-check-input mt-0" title="Mark as Correct">
            <input type="hidden" class="is-correct-hidden" name="options[{index}][is_correct]" value="0">
        </div>
        <input type="text" class="form-control border-start-0" name="options[{index}][text]" placeholder="Option Text" required>
        <button class="btn btn-light border" type="button" onclick="removeOption(this)"><i class="fas fa-times text-danger"></i></button>
    </div>
</template>

<script>
    // Data from Controller
    const subCategories = <?php echo json_encode($subCategories ?? []); ?>;
    const lastMainId = '<?php echo $last_main_id; ?>';
    const lastSubId = '<?php echo $last_sub_id; ?>';
    
    // UI State
    let questionType = 'mcq_single';
    let optionCount = 0;

    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Smart Dropdown Logic
        const mainSelect = document.getElementById('syllabus_main_id');
        const subSelect = document.getElementById('syllabus_node_id');

        function populateSubCategories(mainId) {
            subSelect.innerHTML = '<option value="">Select Section...</option>';
            subSelect.disabled = true;

            if(mainId && subCategories[mainId]) {
                subCategories[mainId].forEach(node => {
                    let opt = document.createElement('option');
                    opt.value = node.id;
                    opt.textContent = node.title + (node.is_premium == 1 ? ' 💎' : '');
                    subSelect.appendChild(opt);
                });
                subSelect.disabled = false;
            }
        }

        mainSelect.addEventListener('change', function() {
            populateSubCategories(this.value);
        });

        // Auto-Select Memory
        if(lastMainId) {
            mainSelect.value = lastMainId;
            populateSubCategories(lastMainId);
            if(lastSubId) {
                subSelect.value = lastSubId;
            }
        }

        // 2. Type Selector Sync
        const typeSelect = document.getElementById('visible_type_selector');
        const hiddenType = document.getElementById('type');
        
        typeSelect.addEventListener('change', function() {
            hiddenType.value = this.value;
            questionType = this.value;
            renderUIForType();
        });

        // Initialize Type
        if(<?php echo isset($question) ? 'true' : 'false'; ?>) {
            questionType = '<?php echo $question['type'] ?? 'mcq_single'; ?>';
            typeSelect.value = questionType;
            hiddenType.value = questionType;
        }

        // 3. Render Options UI
        function renderUIForType() {
            const optSection = document.getElementById('options-section');
            const addBtn = document.getElementById('add-option-btn');
            const numContainer = document.getElementById('numerical-container');
            const optContainer = document.getElementById('options-container');

            optContainer.style.display = 'block';
            numContainer.style.display = 'none';
            addBtn.style.display = 'block';

            if(questionType === 'numerical') {
                optContainer.style.display = 'none';
                numContainer.style.display = 'block';
                addBtn.style.display = 'none';
            } else if (questionType === 'true_false') {
                ensureTrueFalse();
                addBtn.style.display = 'none';
            } else {
                 // MCQ
                 // Sync Radio/Checkbox type
                 const type = (questionType === 'mcq_multi') ? 'checkbox' : 'radio';
                 document.querySelectorAll('.option-correct-input').forEach(el => el.type = type);
            }
        }

        function addOption(data = null) {
            let tpl = document.getElementById('option-template').innerHTML;
            tpl = tpl.replace(/{index}/g, optionCount);
            document.getElementById('options-container').insertAdjacentHTML('beforeend', tpl);
            
            let row = document.getElementById('options-container').lastElementChild;
            let input = row.querySelector('.option-correct-input');
            
            // Set type
             input.type = (questionType === 'mcq_multi') ? 'checkbox' : 'radio';
             if(questionType === 'mcq_single' || questionType === 'true_false') {
                 input.name = 'correct_selection_group'; 
             }

             // Listen for check
             input.addEventListener('change', function() {
                 if(input.type === 'radio') {
                     document.querySelectorAll('.is-correct-hidden').forEach(h => h.value = '0');
                     row.querySelector('.is-correct-hidden').value = '1';
                 } else {
                     row.querySelector('.is-correct-hidden').value = this.checked ? '1' : '0';
                 }
             });

             if(data) {
                 row.querySelector('input[type="text"]').value = data.text;
                 if(data.is_correct) {
                     input.checked = true;
                     row.querySelector('.is-correct-hidden').value = '1';
                 }
             }

             optionCount++;
        }

        window.removeOption = function(btn) {
            btn.closest('.option-row').remove();
        }

        document.getElementById('add-option-btn').addEventListener('click', () => addOption());

        function ensureTrueFalse() {
            const container = document.getElementById('options-container');
            container.innerHTML = ''; 
            optionCount = 0;
            addOption({text: 'True', is_correct: true}); // Default true?
            addOption({text: 'False', is_correct: false});
        }

        // Initialize Default Options
        const initialOptions = <?php echo json_encode($question['options_decoded'] ?? []); ?>;
        if(initialOptions.length > 0) {
            initialOptions.forEach(opt => addOption(opt));
        } else {
            // Default 4 options if new
            if(questionType.startsWith('mcq')) {
                for(let i=0; i<4; i++) addOption();
            }
        }

        renderUIForType();

        // 4. Form Submit
         document.getElementById('questionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Basic validation
            if(questionType.startsWith('mcq') || questionType === 'true_false') {
                 if(!document.querySelector('.option-correct-input:checked')) {
                     alert('Please mark a correct answer.');
                     return;
                 }
            }

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if(res.success) window.location.href = res.redirect;
                else alert(res.error || 'Saved failed');
            })
            .catch(() => alert('Network error'));
        });

    });

    // MathJax Preview
    window.previewMath = function(sourceId, targetId) {
        let content = document.getElementById(sourceId).value;
        let target = document.getElementById(targetId);
        target.style.display = 'block';
        target.innerHTML = content;
        if(window.MathJax) MathJax.typesetPromise([target]);
    };
</script>

<!-- MathJax -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
