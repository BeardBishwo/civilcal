<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo isset($question) ? 'Edit Question' : 'Add New Question'; ?></h1>
        <a href="<?php echo app_base_url('admin/quiz/questions'); ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Back to List
        </a>
    </div>

    <form id="questionForm" method="POST" action="<?php echo $action; ?>">
        <div class="row">
            
            <!-- Left Column: Classification & Content -->
            <div class="col-lg-8">
                
                <!-- Classification -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">1. Classification</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Stream / Category <span class="text-danger">*</span></label>
                                    <select id="category_id" class="form-control" name="category_id" required>
                                        <option value="">Select Stream</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo (isset($question) && $question['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Subject <span class="text-danger">*</span></label>
                                    <select id="subject_id" class="form-control" name="subject_id" required disabled>
                                        <option value="">Select Category First</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Topic <span class="text-danger">*</span></label>
                                    <select id="topic_id" class="form-control" name="topic_id" required disabled>
                                        <option value="">Select Subject First</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Question Type <span class="text-danger">*</span></label>
                                    <select id="type" class="form-control" name="type" required>
                                        <option value="mcq_single" <?php echo (isset($question) && $question['type'] == 'mcq_single') ? 'selected' : ''; ?>>Multiple Choice (Single Correct)</option>
                                        <option value="mcq_multi" <?php echo (isset($question) && $question['type'] == 'mcq_multi') ? 'selected' : ''; ?>>Multiple Choice (Multiple Correct)</option>
                                        <option value="true_false" <?php echo (isset($question) && $question['type'] == 'true_false') ? 'selected' : ''; ?>>True / False</option>
                                        <option value="numerical" <?php echo (isset($question) && $question['type'] == 'numerical') ? 'selected' : ''; ?>>Numerical Input</option>
                                        <option value="text" <?php echo (isset($question) && $question['type'] == 'text') ? 'selected' : ''; ?>>Text Descriptive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Difficulty <span class="text-danger">*</span></label>
                                    <select name="difficulty_level" class="form-control">
                                        <option value="1" <?php echo (isset($question) && $question['difficulty_level'] == 1) ? 'selected' : ''; ?>>Level 1 (Very Easy)</option>
                                        <option value="2" <?php echo (isset($question) && $question['difficulty_level'] == 2) ? 'selected' : ''; ?>>Level 2 (Easy)</option>
                                        <option value="3" <?php echo (isset($question) && $question['difficulty_level'] == 3) ? 'selected' : ''; ?>>Level 3 (Medium)</option>
                                        <option value="4" <?php echo (isset($question) && $question['difficulty_level'] == 4) ? 'selected' : ''; ?>>Level 4 (Hard)</option>
                                        <option value="5" <?php echo (isset($question) && $question['difficulty_level'] == 5) ? 'selected' : ''; ?>>Level 5 (Advanced)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="is_active" class="form-control">
                                        <option value="1" <?php echo (isset($question) && $question['is_active'] == 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo (isset($question) && $question['is_active'] == 0) ? 'selected' : ''; ?>>Draft/Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content & Options -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">2. Question Content</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Question Text -->
                        <div class="form-group">
                            <label>Question Text (HTML/Latex Supported) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <textarea id="question_text" name="question_text" class="form-control" rows="5" placeholder="Enter valid HTML or plain text" required><?php echo htmlspecialchars($question['content_decoded']['text'] ?? ''); ?></textarea>
                            </div>
                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                <small class="form-text text-muted">
                                    Use <code>\( ... \)</code> for inline math and <code>\[ ... \]</code> for block math.
                                    <br>Example: <code>\( \frac{a}{b} \)</code>
                                </small>
                                <button type="button" class="btn btn-info btn-sm" onclick="previewMath('question_text', 'math-preview-q')">
                                    <i class="fas fa-eye"></i> Preview Math
                                </button>
                            </div>
                            <div id="math-preview-q" class="mt-3 p-3 border rounded bg-light" style="min-height: 50px; display:none;"></div>
                        </div>
                        
                        <!-- Image URL -->
                        <div class="form-group">
                            <label>Question Image URL (Optional)</label>
                            <input type="text" name="question_image" class="form-control" placeholder="https://..." value="<?php echo htmlspecialchars($question['content_decoded']['image'] ?? ''); ?>">
                        </div>

                        <hr>
                        
                        <!-- Dynamic Options Section -->
                        <div id="options-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="font-weight-bold">Answer Options</h6>
                                <button type="button" class="btn btn-sm btn-success" id="add-option-btn"><i class="fas fa-plus"></i> Add Option</button>
                            </div>
                            
                            <div id="options-container">
                                <!-- Options will be injected via JS -->
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

            <!-- Right Column: Settings & Meta -->
            <div class="col-lg-4">
                
                <!-- Scoring -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">3. Scoring</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Default Marks</label>
                            <input type="number" step="0.1" name="default_marks" class="form-control" value="<?php echo $question['default_marks'] ?? '1.0'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Negative Marks</label>
                            <input type="number" step="0.1" name="default_negative_marks" class="form-control text-danger" value="<?php echo $question['default_negative_marks'] ?? '0.2'; ?>">
                            <small>Positive value here (e.g. 0.2) means 0.2 marks deducted.</small>
                        </div>
                    </div>
                </div>

                <!-- Explanation -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">4. Explanation</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Answer Explanation</label>
                            <div class="input-group">
                                <textarea id="answer_explanation" name="answer_explanation" class="form-control" rows="6" placeholder="Explain why the answer is correct..."><?php echo htmlspecialchars($question['answer_explanation'] ?? ''); ?></textarea>
                            </div>
                            <div class="mt-2 text-right">
                                <button type="button" class="btn btn-info btn-sm" onclick="previewMath('answer_explanation', 'math-preview-e')">
                                    <i class="fas fa-eye"></i> Preview Math
                                </button>
                            </div>
                            <div id="math-preview-e" class="mt-3 p-3 border rounded bg-light" style="min-height: 50px; display:none;"></div>
                        </div>
                    </div>
                </div>

                <!-- Meta -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">5. Meta Tags</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Tags (Comma separated)</label>
                            <input type="text" name="tags" class="form-control" placeholder="e.g. Loksewa, 2080, Past Paper" value="<?php echo isset($question['tags_decoded']) ? implode(',', $question['tags_decoded']) : ''; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">
                            <i class="fas fa-save"></i> Save Question
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<!-- Option Template (Hidden) -->
<template id="option-template">
    <div class="option-row input-group mb-2">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <input type="radio" name="correct_option_dummy" class="option-correct-input" title="Mark as Correct">
                <!-- Hidden input that actually submits the 'is_correct' value corresponding to this row index -->
                <input type="hidden" class="is-correct-hidden" name="options[{index}][is_correct]" value="0">
            </div>
        </div>
        <input type="text" class="form-control" name="options[{index}][text]" placeholder="Option Text" required>
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" title="Add Image" onclick="toggleImageInput(this)"><i class="fas fa-image"></i></button>
            <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)"><i class="fas fa-times"></i></button>
        </div>
        <!-- Image URL input (initially hidden) -->
        <input type="text" class="form-control d-none mt-1 w-100 option-image-input" name="options[{index}][image]" placeholder="Option Image URL">
    </div>
</template>

<script>
    // State
    const baseUrl = '<?php echo app_base_url('admin/quiz/'); ?>';
    let questionType = '<?php echo $question['type'] ?? 'mcq_single'; ?>';
    let optionCount = 0;
    
    // Initial Data
    const initialSubjectId = '<?php echo $question['subject_id'] ?? ''; ?>';
    const initialTopicId = '<?php echo $question['topic_id'] ?? ''; ?>';
    const initialOptions = <?php echo json_encode($question['options_decoded'] ?? []); ?>;

    $(document).ready(function() {
        
        // 1. Classification Chain
        $('#category_id').change(function() {
            let catId = $(this).val();
            if(!catId) {
                $('#subject_id').html('<option value="">Select Category First</option>').prop('disabled', true);
                $('#topic_id').html('<option value="">Select Subject First</option>').prop('disabled', true);
                return;
            }
            
            $.get(baseUrl + 'get-subjects/' + catId, function(data) {
                let html = '<option value="">Select Subject</option>';
                data.forEach(sub => {
                    html += `<option value="${sub.id}">${sub.name}</option>`;
                });
                $('#subject_id').html(html).prop('disabled', false);
                
                // Pre-select if editing
                if(initialSubjectId) {
                    $('#subject_id').val(initialSubjectId).trigger('change');
                }
            });
        });

        $('#subject_id').change(function() {
            let subId = $(this).val();
            if(!subId) {
                $('#topic_id').html('<option value="">Select Subject First</option>').prop('disabled', true);
                return;
            }

            $.get(baseUrl + 'get-topics/' + subId, function(data) {
                let html = '<option value="">Select Topic</option>';
                data.forEach(topic => {
                    html += `<option value="${topic.id}">${topic.name}</option>`;
                });
                $('#topic_id').html(html).prop('disabled', false);

                // Pre-select if editing
                if(initialTopicId) {
                    $('#topic_id').val(initialTopicId);
                }
            });
        });

        // Trigger chain if editing
        if($('#category_id').val()) {
            $('#category_id').trigger('change');
        }

        // 2. Question Type Logic
        $('#type').change(function() {
            questionType = $(this).val();
            renderUIForType();
        });

        function renderUIForType() {
            $('#options-section').show(); // Default show
            $('#add-option-btn').show(); // Default show
            $('#numerical-container').hide();
            $('#options-container').show();

            const templateInput = document.getElementById('option-template').content.querySelector('.option-correct-input');

            if(questionType === 'mcq_single' || questionType === 'true_false') {
                // Radio buttons for correct answer
                $('.option-correct-input').attr('type', 'radio').attr('name', 'correct_option_group');
                if(questionType === 'true_false') {
                     $('#add-option-btn').hide();
                     // If switching TO true/false, strictly enforce 2 options
                     // For now, let user clear manually or we can auto-reset. 
                     // Let's user handle it or implement auto-fill.
                     ensureTrueFalseOptions();
                }
            } else if (questionType === 'mcq_multi') {
                // Checkboxes for correct answer
                $('.option-correct-input').attr('type', 'checkbox').attr('name', 'correct_option_group[]');
            } else if (questionType === 'numerical') {
                $('#options-container').hide();
                $('#add-option-btn').hide();
                $('#numerical-container').show();
            } else if (questionType === 'text') {
                $('#options-section').hide(); // No options for text questions usually, or just keywords
            }
        }

        // 3. Options Logic
        
        function addOption(data = null) {
            let template = document.getElementById('option-template').innerHTML;
            template = template.replace(/{index}/g, optionCount);
            
            $('#options-container').append(template);
            
            let row = $('#options-container').children().last();
            let checkbox = row.find('.option-correct-input');
            let hidden = row.find('.is-correct-hidden');
            
            // Sync correct status
            checkbox.on('change', function() {
                if(questionType === 'mcq_single' || questionType === 'true_false') {
                    // Reset all hidden inputs
                     $('.is-correct-hidden').val('0');
                     hidden.val('1');
                } else {
                    hidden.val($(this).is(':checked') ? '1' : '0');
                }
            });

            if(questionType === 'mcq_multi') {
                checkbox.attr('type', 'checkbox');
            } else {
                checkbox.attr('type', 'radio').attr('name', 'correct_selection');
            }

            if(data) {
                row.find('input[type="text"]').val(data.text);
                if(data.is_correct) {
                    checkbox.prop('checked', true);
                    hidden.val('1');
                }
                if(data.image) {
                    row.find('.option-image-input').val(data.image).removeClass('d-none');
                }
            }

            optionCount++;
        }

        window.removeOption = function(btn) {
            $(btn).closest('.option-row').remove();
        };

        window.toggleImageInput = function(btn) {
            $(btn).closest('.option-row').find('.option-image-input').toggleClass('d-none');
        };

        $('#add-option-btn').click(function() {
            addOption();
        });
        
        function ensureTrueFalseOptions() {
             $('#options-container').empty();
             optionCount = 0;
             addOption({text: 'True', is_correct: false});
             addOption({text: 'False', is_correct: false});
        }

        // Initialize Options
        if(initialOptions && initialOptions.length > 0) {
            initialOptions.forEach(opt => addOption(opt));
            
            if(questionType === 'numerical' && initialOptions[0]) {
                 $('#num-answer').val(initialOptions[0].text);
                 $('input[name="numerical_tolerance"]').val(initialOptions[0].tolerance || 0);
            }
        } else {
            // Add default 4 options for new MCQ
            if(!initialOptions || initialOptions.length === 0) {
                 if(questionType.startsWith('mcq')) {
                     for(let i=0; i<4; i++) addOption();
                 }
            }
        }

        renderUIForType(); // Apply type rules initially
    });
    
    // MathJax Preview Logic
    window.previewMath = function(sourceId, targetId) {
        let content = $('#' + sourceId).val();
        let target = $('#' + targetId);
        
        target.show().html(content);
        
        if (window.MathJax) {
            MathJax.typesetPromise([document.querySelector('#' + targetId)]).then(() => {
                console.log('Math rendered');
            }).catch((err) => console.log('MathJax error:', err));
        } else {
            console.warn('MathJax not loaded');
        }
    };

    // Form Submit Handler for AJAX
    $('#questionForm').submit(function(e) {
        e.preventDefault();
        
        // Basic Client Validation
        if(questionType === 'mcq_single' || questionType === 'mcq_multi' || questionType === 'true_false') {
             if($('.option-correct-input:checked').length === 0) {
                 alert('Please mark at least one option as correct.');
                 return;
             }
        }

        $.post($(this).attr('action'), $(this).serialize(), function(res) {
            if(res.success) {
                window.location.href = res.redirect;
            } else {
                alert('Error: ' + res.error);
            }
        }).fail(function() {
            alert('Server error.');
        });
    });
</script>

<!-- Load MathJax -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>


<style>
    .option-row .input-group-text { background: transparent; border: 1px solid #d1d3e2; border-right: 0; }
    .option-row .form-control:focus { box-shadow: none; }
</style>
