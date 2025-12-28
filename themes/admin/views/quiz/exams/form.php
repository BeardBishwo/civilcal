<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo isset($exam) ? 'Edit Exam Settings' : 'Create New Exam'; ?></h1>
    </div>

    <form method="POST" action="<?php echo $action; ?>">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Exam Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($exam['title'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Slug (URL Friendly) <small class="text-muted">Leave empty to auto-generate</small></label>
                            <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($exam['slug'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($exam['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Configuration</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Exam Type</label>
                                    <select name="type" class="form-control">
                                        <option value="practice" <?php echo ($exam['type'] ?? '') == 'practice' ? 'selected' : ''; ?>>Practice Set</option>
                                        <option value="mock_test" <?php echo ($exam['type'] ?? '') == 'mock_test' ? 'selected' : ''; ?>>Mock Test (Full Syllabus)</option>
                                        <option value="past_paper" <?php echo ($exam['type'] ?? '') == 'past_paper' ? 'selected' : ''; ?>>Past Paper</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mode</label>
                                    <select name="mode" class="form-control">
                                        <option value="practice" <?php echo ($exam['mode'] ?? '') == 'practice' ? 'selected' : ''; ?>>Practice (Instant Feedback)</option>
                                        <option value="exam" <?php echo ($exam['mode'] ?? '') == 'exam' ? 'selected' : ''; ?>>Exam (Timed, No Feedback until end)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Duration (Minutes)</label>
                                    <input type="number" name="duration_minutes" class="form-control" value="<?php echo $exam['duration_minutes'] ?? 0; ?>">
                                    <small class="text-muted">0 for unlimited</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pass Percentage</label>
                                    <input type="number" step="0.1" name="pass_percentage" class="form-control" value="<?php echo $exam['pass_percentage'] ?? 40; ?>">
                                </div>
                            </div>
                                    <input type="number" step="0.01" name="negative_marking_rate" class="form-control" value="<?php echo $exam['negative_marking_rate'] ?? 0; ?>">
                                    <small class="text-muted">e.g. 0.2 per wrong answer</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="shuffle_questions" name="shuffle_questions" value="1" <?php echo !empty($exam['shuffle_questions']) ? 'checked' : ''; ?>>
                            <label class="form-check-label font-weight-bold ml-1" for="shuffle_questions">
                                Shuffle Questions <smal class="text-muted font-weight-normal ml-1">(Randomize order for each attempt)</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Availability</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="draft" <?php echo ($exam['status'] ?? '') == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo ($exam['status'] ?? '') == 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="archived" <?php echo ($exam['status'] ?? '') == 'archived' ? 'selected' : ''; ?>>Archived</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Schedule Start (Optional)</label>
                            <input type="datetime-local" name="start_datetime" class="form-control" value="<?php echo isset($exam['start_datetime']) ? date('Y-m-d\TH:i', strtotime($exam['start_datetime'])) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Schedule End (Optional)</label>
                            <input type="datetime-local" name="end_datetime" class="form-control" value="<?php echo isset($exam['end_datetime']) ? date('Y-m-d\TH:i', strtotime($exam['end_datetime'])) : ''; ?>">
                        </div>

                        <hr>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="is_premium" name="is_premium" value="1" <?php echo !empty($exam['is_premium']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_premium">Premium (Paid) Content</label>
                        </div>
                        
                        <div class="form-group" id="price_div" style="<?php echo empty($exam['is_premium']) ? 'display:none;' : ''; ?>">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $exam['price'] ?? 0; ?>">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg mt-3">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('is_premium').addEventListener('change', function() {
        document.getElementById('price_div').style.display = this.checked ? 'block' : 'none';
    });
</script>
