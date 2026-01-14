<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Exam Builder: <?php echo htmlspecialchars($exam['title']); ?></h1>
            <p class="mb-0 text-gray-600">
                <span class="mr-3"><i class="fas fa-clock"></i> <?php echo $exam['duration_minutes']; ?> mins</span>
                <span class="mr-3"><i class="fas fa-question-circle"></i> <?php echo count($existing_questions); ?> Questions</span>
                <span class="mr-3"><i class="fas fa-star"></i> <?php echo $exam['total_marks'] ?? 0; ?> Marks</span>
            </p>
        </div>
        <div>
            <a href="<?php echo app_base_url('admin/quiz/exams'); ?>" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-check"></i> Finish & Save
            </a>
        </div>
    </div>

    <div class="row">
        <!-- LEFT: Question Bank Search -->
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Question Bank</h6>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form id="searchForm" class="mb-3">
                        <div class="input-group mb-2">
                            <input type="text" id="search_term" class="form-control" placeholder="Search text...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <select id="search_type" class="form-control form-control-sm">
                                    <option value="">All Types</option>
                                    <option value="mcq_single">MCQ (Single)</option>
                                    <option value="mcq_multi">MCQ (Multi)</option>
                                    <option value="numerical">Numerical</option>
                                    <option value="true_false">True/False</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <!-- Ideally Populate Topics via AJAX or preload categories -->
                                <select id="search_topic" class="form-control form-control-sm">
                                    <option value="">All Topics</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- Results List -->
                    <div id="searchResults" class="list-group" style="max-height: 600px; overflow-y: auto;">
                        <div class="text-center text-muted py-3">Search to find questions...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Current Exam Questions -->
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Exam Questions (<?php echo count($existing_questions); ?>)</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($existing_questions)): ?>
                        <div class="text-center py-5">
                            <img src="<?php echo app_base_url('assets/images/empty.svg'); ?>" alt="Empty" style="width: 100px; opacity: 0.5;">
                            <p class="mt-3 text-gray-500">No questions in this exam yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($existing_questions as $index => $q): ?>
                                <?php
                                // Handle both 'content' (from cache) and 'question' (from DB)
                                $questionData = isset($q['content']) ? $q['content'] : $q['question'];
                                if (is_string($questionData)) {
                                    $content = json_decode($questionData, true);
                                } else {
                                    $content = $questionData;
                                }
                                $text = strip_tags($content['text'] ?? $questionData ?? '');
                                ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div style="width: 85%;">
                                        <div class="font-weight-bold text-gray-800">
                                            <span class="badge badge-light border mr-2"><?php echo $index + 1; ?></span>
                                            <?php echo htmlspecialchars($text); ?>
                                        </div>
                                        <div class="small mt-1">
                                            <span class="badge badge-info"><?php echo $q['type']; ?></span>
                                            <span class="text-muted ml-2">Marks: <?php echo $q['default_marks']; ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-circle btn-danger" onclick="removeQuestion(<?php echo $q['id']; ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const examId = <?php echo $exam['id']; ?>;
    const addUrl = '<?php echo app_base_url('admin/quiz/exams/add-question'); ?>';
    const removeUrl = '<?php echo app_base_url('admin/quiz/exams/remove-question'); ?>';
    const searchUrl = '<?php echo app_base_url('admin/quiz/questions/search'); ?>';

    $('#searchForm').submit(function(e) {
        e.preventDefault();
        loadResults();
    });

    function loadResults() {
        let term = $('#search_term').val();
        let type = $('#search_type').val();
        // let topic = $('#search_topic').val();

        $('#searchResults').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');

        $.get(searchUrl, {
            q: term,
            type: type
        }, function(data) {
            if (data.length === 0) {
                $('#searchResults').html('<div class="text-center text-muted py-3">No questions found.</div>');
                return;
            }

            let html = '';
            data.forEach(q => {
                html += `
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 text-truncate" style="max-width: 80%;">${q.text}</h6>
                            <button class="btn btn-sm btn-primary" onclick="addQuestion(${q.id})"><i class="fas fa-plus"></i></button>
                        </div>
                        <small>
                            <span class="badge badge-secondary">${q.type}</span> 
                            Level ${q.difficulty_level}
                        </small>
                    </div>
                `;
            });
            $('#searchResults').html(html);
        });
    }

    function addQuestion(questionId) {
        $.post(addUrl, {
            exam_id: examId,
            question_id: questionId
        }, function(res) {
            if (res.success) {
                location.reload(); // Reload to update list and stats
            } else {
                alert('Error: ' + res.error);
            }
        });
    }

    function removeQuestion(questionId) {
        if (!confirm('Remove this question from exam?')) return;
        $.post(removeUrl, {
            exam_id: examId,
            question_id: questionId
        }, function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert('Error: ' + res.error);
            }
        });
    }

    // Initial load
    loadResults();
</script>