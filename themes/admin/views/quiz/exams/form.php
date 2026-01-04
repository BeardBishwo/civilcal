<?php
/**
 * PREMIUM EXAM FORM
 * Modern, clean two-column layout for exam configuration.
 */
$isEdit = isset($exam);
$title = $isEdit ? 'Edit Exam Settings' : 'Create New Exam';
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        
        <!-- Premium Header -->
        <div class="compact-header">
            <div class="header-left">
                <a href="<?php echo app_base_url('admin/quiz/exams'); ?>" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-title">
                    <i class="fas fa-edit"></i>
                    <h1><?php echo $title; ?></h1>
                </div>
            </div>
            <div class="header-actions">
                <!-- Additional actions if needed -->
            </div>
        </div>

        <div class="form-container">
            <form method="POST" action="<?php echo $action; ?>" id="examForm">
                <div class="grid-layout">
                    
                    <!-- LEFT COLUMN: Main Info -->
                    <div class="grid-main">
                        <div class="card-premium">
                            <div class="card-header-simple">
                                <h3>Basic Information</h3>
                            </div>
                            <div class="card-body-premium">
                                
                                <div class="form-group-premium">
                                    <label>Exam Title <span class="required">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-heading"></i>
                                        <input type="text" name="title" required value="<?php echo htmlspecialchars($exam['title'] ?? ''); ?>" placeholder="Enter exam title">
                                    </div>
                                </div>

                                <div class="form-group-premium">
                                    <label>Slug (URL) <span class="hint">Leave empty to auto-generate</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-link"></i>
                                        <input type="text" name="slug" value="<?php echo htmlspecialchars($exam['slug'] ?? ''); ?>" placeholder="custom-slug">
                                    </div>
                                </div>

                                <div class="form-group-premium">
                                    <label>Description</label>
                                    <textarea name="description" rows="4" placeholder="Describe the exam content..."><?php echo htmlspecialchars($exam['description'] ?? ''); ?></textarea>
                                </div>

                            </div>
                        </div>

                        <div class="card-premium mt-4">
                            <div class="card-header-simple">
                                <h3>Configuration</h3>
                            </div>
                            <div class="card-body-premium">
                                <div class="row-flex">
                                    <div class="col-flex">
                                        <label>Exam Type</label>
                                        <div class="select-wrapper">
                                            <select name="type">
                                                <option value="practice" <?php echo ($exam['type'] ?? '') == 'practice' ? 'selected' : ''; ?>>Practice Set</option>
                                                <option value="mock_test" <?php echo ($exam['type'] ?? '') == 'mock_test' ? 'selected' : ''; ?>>Mock Test</option>
                                                <option value="past_paper" <?php echo ($exam['type'] ?? '') == 'past_paper' ? 'selected' : ''; ?>>Past Paper</option>
                                            </select>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                    <div class="col-flex">
                                        <label>Mode</label>
                                        <div class="select-wrapper">
                                            <select name="mode">
                                                <option value="practice" <?php echo ($exam['mode'] ?? '') == 'practice' ? 'selected' : ''; ?>>Practice (Instant Feedback)</option>
                                                <option value="exam" <?php echo ($exam['mode'] ?? '') == 'exam' ? 'selected' : ''; ?>>Exam (Timed, No Feedback)</option>
                                            </select>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-flex mt-3">
                                    <div class="col-flex">
                                        <label>Duration (Minutes)</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-clock"></i>
                                            <input type="number" name="duration_minutes" value="<?php echo $exam['duration_minutes'] ?? 0; ?>" min="0">
                                        </div>
                                        <span class="field-hint">0 for unlimited time</span>
                                    </div>
                                    <div class="col-flex">
                                        <label>Pass Percentage</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-percentage"></i>
                                            <input type="number" step="0.1" name="pass_percentage" value="<?php echo $exam['pass_percentage'] ?? 40; ?>" min="0" max="100">
                                        </div>
                                    </div>
                                </div>

                                <div class="row-flex mt-3">
                                    <div class="col-flex">
                                        <label>Marks</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-star"></i>
                                            <input type="number" name="total_marks" class="form-control" value="<?php echo $exam['total_marks'] ?? 0; ?>">
                                        </div>
                                    </div>
                                    <div class="col-flex">
                                        <label>Negative Marking</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-minus-circle"></i>
                                            <input type="number" step="0.01" name="negative_marking_rate" value="<?php echo $exam['negative_marking_rate'] ?? 0; ?>" min="0">
                                        </div>
                                        <span class="field-hint">e.g. 0.2 per wrong answer</span>
                                    </div>
                                </div>
                                
                                <div class="toggle-row mt-4">
                                    <label class="switch">
                                        <input type="checkbox" name="shuffle_questions" value="1" <?php echo !empty($exam['shuffle_questions']) ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                    <div class="toggle-text">
                                        <strong>Shuffle Questions</strong>
                                        <p>Randomize question order for each attempt</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Sidebar -->
                    <div class="grid-sidebar">
                        <div class="card-premium">
                            <div class="card-header-simple">
                                <h3>Publishing</h3>
                            </div>
                            <div class="card-body-premium">
                                <div class="form-group-premium">
                                    <label>Status</label>
                                    <div class="select-wrapper">
                                        <select name="status">
                                            <option value="draft" <?php echo ($exam['status'] ?? '') == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                            <option value="published" <?php echo ($exam['status'] ?? '') == 'published' ? 'selected' : ''; ?>>Published</option>
                                            <option value="archived" <?php echo ($exam['status'] ?? '') == 'archived' ? 'selected' : ''; ?>>Archived</option>
                                        </select>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>

                                <div class="form-group-premium">
                                    <label>Start Date (Optional)</label>
                                    <input type="datetime-local" name="start_datetime" class="form-input-simple" value="<?php echo isset($exam['start_datetime']) ? date('Y-m-d\TH:i', strtotime($exam['start_datetime'])) : ''; ?>">
                                </div>

                                <div class="form-group-premium">
                                    <label>End Date (Optional)</label>
                                    <input type="datetime-local" name="end_datetime" class="form-input-simple" value="<?php echo isset($exam['end_datetime']) ? date('Y-m-d\TH:i', strtotime($exam['end_datetime'])) : ''; ?>">
                                </div>

                                <div class="divider"></div>

                                <div class="toggle-row">
                                    <label class="switch">
                                        <input type="checkbox" id="is_premium" name="is_premium" value="1" <?php echo !empty($exam['is_premium']) ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                    <div class="toggle-text">
                                        <strong>Premium Content</strong>
                                        <p>Requires payment/subscription</p>
                                    </div>
                                </div>

                                <div id="price_div" class="form-group-premium mt-3" style="<?php echo empty($exam['is_premium']) ? 'display:none;' : ''; ?>">
                                    <label>Unlock Price</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-coins text-warning"></i>
                                        <input type="number" step="0.01" name="price" value="<?php echo $exam['price'] ?? 0; ?>">
                                    </div>
                                </div>

                                <button type="submit" class="btn-primary-block mt-4">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('is_premium').addEventListener('change', function() {
        document.getElementById('price_div').style.display = this.checked ? 'block' : 'none';
        
        if(this.checked) {
             const input = document.querySelector('input[name="price"]');
             if(input) setTimeout(() => input.focus(), 100);
        }
    });

    // Optional: Add a simple confirmation or toast on submit if needed, 
    // but standard form submission via controller redirect is fine.
</script>

<style>
/* ========================================
   PREMIUM FORM STYLES
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-bg: #f8f9fa;
    --admin-text: #1f2937;
    --admin-text-light: #6b7280;
    --admin-border: #e5e7eb;
}

.admin-wrapper-container { padding: 1rem; background: var(--admin-bg); min-height: 100vh; }
.admin-content-wrapper { background: transparent; }

/* Header */
.compact-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1rem 1.5rem; background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
    color: white; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}
.header-left { display: flex; align-items: center; gap: 1rem; }
.back-link { color: white; opacity: 0.8; font-size: 1.1rem; transition: 0.2s; }
.back-link:hover { opacity: 1; transform: translateX(-3px); }
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.25rem; font-weight: 700; color: white; }

/* Grid Layout */
.grid-layout { display: grid; grid-template-columns: 1fr 350px; gap: 1.5rem; }
.form-container { max-width: 1200px; margin: 0 auto; }

/* Cards */
.card-premium { background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid var(--admin-border); }
.card-header-simple { padding: 1rem 1.5rem; border-bottom: 1px solid var(--admin-border); background: #fff; }
.card-header-simple h3 { margin: 0; font-size: 1rem; font-weight: 700; color: #374151; }
.card-body-premium { padding: 1.5rem; }

/* Inputs */
.form-group-premium { margin-bottom: 1.25rem; }
.form-group-premium label { display: block; font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; }
.required { color: #ef4444; }
.hint { font-weight: 400; color: #9ca3af; font-size: 0.75rem; margin-left: 5px; }

.input-with-icon { position: relative; }
.input-with-icon i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.9rem; pointer-events: none; }
.input-with-icon input { padding-left: 2.5rem !important; }

.form-input-simple, .input-with-icon input, textarea {
    width: 100%; padding: 0.6rem 1rem; font-size: 0.9rem; color: #1f2937;
    border: 1px solid #d1d5db; border-radius: 8px; outline: none; transition: 0.2s;
}
.form-input-simple:focus, .input-with-icon input:focus, textarea:focus { border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }

textarea { resize: vertical; }

/* Select Custom */
.select-wrapper { position: relative; }
.select-wrapper select {
    width: 100%; padding: 0.6rem 2.5rem 0.6rem 1rem; font-size: 0.9rem; color: #1f2937;
    border: 1px solid #d1d5db; border-radius: 8px; outline: none; appearance: none; background: white; cursor: pointer;
}
.select-wrapper i { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; font-size: 0.8rem; }
.select-wrapper select:focus { border-color: var(--admin-primary); }

/* Flex Rows */
.row-flex { display: flex; gap: 1rem; }
.col-flex { flex: 1; }
.field-hint { display: block; font-size: 0.75rem; color: #6b7280; margin-top: 4px; }

/* Toggles */
.toggle-row { display: flex; align-items: flex-start; gap: 10px; padding: 0.5rem 0; }
.toggle-text strong { display: block; font-size: 0.9rem; color: #374151; }
.toggle-text p { margin: 0; font-size: 0.8rem; color: #6b7280; }

.switch { position: relative; display: inline-block; width: 40px; height: 22px; flex-shrink: 0; margin-top: 2px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e5e7eb; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 2px; bottom: 2px; background-color: white; transition: .4s; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
input:checked + .slider { background-color: var(--admin-primary); }
input:checked + .slider:before { transform: translateX(18px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }

/* Buttons */
.btn-primary-block {
    width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; transition: 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-primary-block:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

/* Utilities */
.mt-3 { margin-top: 1rem; }
.mt-4 { margin-top: 1.5rem; }
.divider { height: 1px; background: #e5e7eb; margin: 1.5rem 0; }
.text-warning { color: #d97706 !important; }

@media (max-width: 900px) {
    .grid-layout { grid-template-columns: 1fr; }
    .col-flex { min-width: 100%; }
    .row-flex { flex-wrap: wrap; gap: 0; }
}
</style>
