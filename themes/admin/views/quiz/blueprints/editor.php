<?php
/**
 * Blueprint Editor View
 * 
 * Visual editor for creating and managing exam blueprints with question distribution rules
 */
$pageTitle = $page_title ?? 'Blueprint Editor';
$blueprint = $blueprint ?? null;
$syllabusTree = $syllabus_tree ?? [];
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-muted">Configure exam structure and question distribution</p>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-success" onclick="validateBlueprint()">
                <i class="bi bi-check-circle"></i> Validate
            </button>
            <button type="button" class="btn btn-primary" onclick="generateExam()">
                <i class="bi bi-lightning"></i> Generate Exam
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left: Blueprint Settings -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Blueprint Settings</h5>
                </div>
                <div class="card-body">
                    <form id="blueprintForm">
                        <input type="hidden" id="blueprintId" value="<?= $blueprint['id'] ?? '' ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" id="blueprintTitle" value="<?= htmlspecialchars($blueprint['title'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Level *</label>
                            <select class="form-select" id="blueprintLevel" required onchange="loadSyllabusForLevel(this.value)">
                                <option value="">Select Level</option>
                                <option value="Level 4" <?= ($blueprint['level'] ?? '') === 'Level 4' ? 'selected' : '' ?>>Level 4</option>
                                <option value="Level 5" <?= ($blueprint['level'] ?? '') === 'Level 5' ? 'selected' : '' ?>>Level 5</option>
                                <option value="Level 7" <?= ($blueprint['level'] ?? '') === 'Level 7' ? 'selected' : '' ?>>Level 7</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Questions</label>
                                <input type="number" class="form-control" id="blueprintTotalQuestions" value="<?= $blueprint['total_questions'] ?? 50 ?>" min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Marks</label>
                                <input type="number" class="form-control" id="blueprintTotalMarks" value="<?= $blueprint['total_marks'] ?? 100 ?>" min="1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration (min)</label>
                                <input type="number" class="form-control" id="blueprintDuration" value="<?= $blueprint['duration_minutes'] ?? 60 ?>" min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Negative Marking</label>
                                <input type="number" class="form-control" id="blueprintNegativeMarking" value="<?= $blueprint['negative_marking_rate'] ?? 0 ?>" step="0.01" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Wildcard % <small class="text-muted">(Out-of-syllabus)</small></label>
                            <input type="number" class="form-control" id="blueprintWildcard" value="<?= $blueprint['wildcard_percentage'] ?? 10 ?>" step="0.1" min="0" max="100">
                            <small class="text-muted">Percentage of practical/out-of-syllabus questions</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="blueprintDescription" rows="3"><?= htmlspecialchars($blueprint['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="blueprintActive" <?= ($blueprint['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="blueprintActive">Active</label>
                        </div>

                        <button type="button" class="btn btn-primary w-100" onclick="saveBlueprint()">
                            <i class="bi bi-save"></i> Save Blueprint
                        </button>
                    </form>
                </div>
            </div>

            <!-- Validation Results -->
            <div class="card" id="validationCard" style="display:none;">
                <div class="card-header">
                    <h5 class="mb-0">Validation Results</h5>
                </div>
                <div class="card-body" id="validationResults"></div>
            </div>
        </div>

        <!-- Right: Question Distribution Rules -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Question Distribution Rules</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="showAddRuleModal()">
                        <i class="bi bi-plus-circle"></i> Add Rule
                    </button>
                </div>
                <div class="card-body">
                    <div id="rulesContainer">
                        <?php if (!empty($blueprint['rules'])): ?>
                            <?php foreach ($blueprint['rules'] as $rule): ?>
                                <?php echo renderRule($rule); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-5" id="noRulesMessage">
                                <i class="bi bi-diagram-2 display-1"></i>
                                <p class="mt-3">No distribution rules defined. Add rules to specify question sources.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Summary -->
                    <div class="mt-4 p-3 bg-light rounded" id="rulesSummary">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h6 class="text-muted mb-1">Total Rules</h6>
                                <h3 class="mb-0" id="totalRules"><?= count($blueprint['rules'] ?? []) ?></h3>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted mb-1">Questions Required</h6>
                                <h3 class="mb-0" id="totalRequired">
                                    <?php 
                                    $total = 0;
                                    foreach ($blueprint['rules'] ?? [] as $rule) {
                                        $total += $rule['questions_required'];
                                    }
                                    echo $total;
                                    ?>
                                </h3>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted mb-1">Blueprint Total</h6>
                                <h3 class="mb-0"><?= $blueprint['total_questions'] ?? 50 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Rule Modal -->
<div class="modal fade" id="ruleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Distribution Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="ruleForm" onsubmit="saveRule(event)">
                <div class="modal-body">
                    <input type="hidden" id="ruleId">
                    
                    <div class="mb-3">
                        <label class="form-label">Syllabus Node *</label>
                        <select class="form-select" id="ruleSyllabusNode" required>
                            <option value="">Select Section/Unit</option>
                            <?php echo renderSyllabusOptions($syllabusTree); ?>
                        </select>
                        <small class="text-muted">Questions will be pulled from this node and all its children</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Questions Required *</label>
                        <input type="number" class="form-control" id="ruleQuestionsRequired" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Difficulty Distribution (Optional)</label>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label small">Easy</label>
                                <input type="number" class="form-control" id="difficultyEasy" min="0" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Medium</label>
                                <input type="number" class="form-control" id="difficultyMedium" min="0" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Hard</label>
                                <input type="number" class="form-control" id="difficultyHard" min="0" placeholder="0">
                            </div>
                        </div>
                        <small class="text-muted">Leave blank for random distribution</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Generate Exam Modal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Exam</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="generateForm" onsubmit="confirmGenerate(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Exam Title</label>
                        <input type="text" class="form-control" id="generateTitle" value="<?= htmlspecialchars($blueprint['title'] ?? '') ?> - <?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="generateShuffle" checked>
                        <label class="form-check-label" for="generateShuffle">Shuffle Questions</label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="generateWildcard" checked>
                        <label class="form-check-label" for="generateWildcard">Include Wildcard Questions</label>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> This will create a new exam with questions selected according to the blueprint rules.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.rule-card {
    border-left: 4px solid #0d6efd;
    margin-bottom: 15px;
    transition: all 0.2s;
}

.rule-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.rule-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 15px;
    border-radius: 6px 6px 0 0;
}

.rule-body {
    padding: 15px;
    background: #f8f9fa;
}

.difficulty-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    margin-right: 5px;
}

.difficulty-easy { background: #d4edda; color: #155724; }
.difficulty-medium { background: #fff3cd; color: #856404; }
.difficulty-hard { background: #f8d7da; color: #721c24; }
</style>

<script>
const baseUrl = '<?= app_base_url() ?>';
const blueprintId = <?= $blueprint['id'] ?? 'null' ?>;

function saveBlueprint() {
    const data = {
        title: document.getElementById('blueprintTitle').value,
        level: document.getElementById('blueprintLevel').value,
        total_questions: document.getElementById('blueprintTotalQuestions').value,
        total_marks: document.getElementById('blueprintTotalMarks').value,
        duration_minutes: document.getElementById('blueprintDuration').value,
        negative_marking_rate: document.getElementById('blueprintNegativeMarking').value,
        wildcard_percentage: document.getElementById('blueprintWildcard').value,
        description: document.getElementById('blueprintDescription').value,
        is_active: document.getElementById('blueprintActive').checked ? 1 : 0
    };

    const url = blueprintId 
        ? `${baseUrl}/admin/quiz/blueprints/update/${blueprintId}`
        : `${baseUrl}/admin/quiz/blueprints/store`;

    fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(data)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.redirect) {
                window.location = data.redirect;
            } else {
                alert('Blueprint saved successfully!');
            }
        } else {
            alert('Error: ' + data.error);
        }
    });
}

function showAddRuleModal() {
    document.getElementById('ruleForm').reset();
    document.getElementById('ruleId').value = '';
    new bootstrap.Modal(document.getElementById('ruleModal')).show();
}

function saveRule(event) {
    event.preventDefault();
    
    const difficultyDist = {};
    const easy = parseInt(document.getElementById('difficultyEasy').value) || 0;
    const medium = parseInt(document.getElementById('difficultyMedium').value) || 0;
    const hard = parseInt(document.getElementById('difficultyHard').value) || 0;
    
    if (easy > 0) difficultyDist.easy = easy;
    if (medium > 0) difficultyDist.medium = medium;
    if (hard > 0) difficultyDist.hard = hard;

    const data = {
        syllabus_node_id: document.getElementById('ruleSyllabusNode').value,
        questions_required: document.getElementById('ruleQuestionsRequired').value,
        difficulty_distribution: JSON.stringify(difficultyDist)
    };

    fetch(`${baseUrl}/admin/quiz/blueprints/${blueprintId}/add-rule`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(data)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    });
}

function deleteRule(ruleId) {
    if (!confirm('Delete this rule?')) return;
    
    fetch(`${baseUrl}/admin/quiz/blueprints/rule/${ruleId}/delete`, {
        method: 'POST'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    });
}

function validateBlueprint() {
    fetch(`${baseUrl}/admin/quiz/blueprints/${blueprintId}/validate`)
        .then(r => r.json())
        .then(data => {
            const card = document.getElementById('validationCard');
            const results = document.getElementById('validationResults');
            
            if (data.valid) {
                results.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Blueprint is valid!
                        <ul class="mb-0 mt-2">
                            <li>Total Required: ${data.total_required} questions</li>
                            <li>Blueprint Total: ${data.blueprint_total} questions</li>
                        </ul>
                    </div>
                `;
            } else {
                results.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Validation Errors:
                        <ul class="mb-0 mt-2">
                            ${data.errors.map(e => `<li>${e}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            card.style.display = 'block';
        });
}

function generateExam() {
    new bootstrap.Modal(document.getElementById('generateModal')).show();
}

function confirmGenerate(event) {
    event.preventDefault();
    
    const data = {
        exam_title: document.getElementById('generateTitle').value,
        shuffle: document.getElementById('generateShuffle').checked ? 1 : 0,
        include_wildcard: document.getElementById('generateWildcard').checked ? 1 : 0,
        save: 1
    };

    fetch(`${baseUrl}/admin/quiz/blueprints/${blueprintId}/generate`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(data)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.redirect) {
                window.location = data.redirect;
            } else {
                alert('Exam generated successfully!');
            }
        } else {
            alert('Error: ' + data.error);
        }
    });
}

function loadSyllabusForLevel(level) {
    // Reload page with level filter to get correct syllabus tree
    if (level) {
        window.location = `${baseUrl}/admin/quiz/blueprints/edit/${blueprintId}?level=${level}`;
    }
}
</script>

<?php
function renderRule($rule) {
    $diffDist = $rule['difficulty_distribution'] ?? [];
    $html = '<div class="card rule-card">';
    $html .= '<div class="rule-header d-flex justify-content-between align-items-center">';
    $html .= '<div><strong>' . htmlspecialchars($rule['node_title']) . '</strong> <span class="badge bg-light text-dark ms-2">' . $rule['node_type'] . '</span></div>';
    $html .= '<button class="btn btn-sm btn-danger" onclick="deleteRule(' . $rule['id'] . ')"><i class="bi bi-trash"></i></button>';
    $html .= '</div>';
    $html .= '<div class="rule-body">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-6"><strong>Questions Required:</strong> ' . $rule['questions_required'] . '</div>';
    $html .= '<div class="col-md-6">';
    
    if (!empty($diffDist)) {
        $html .= '<strong>Difficulty:</strong> ';
        foreach ($diffDist as $level => $count) {
            $html .= '<span class="difficulty-badge difficulty-' . $level . '">' . ucfirst($level) . ': ' . $count . '</span>';
        }
    } else {
        $html .= '<span class="text-muted">Random distribution</span>';
    }
    
    $html .= '</div></div></div></div>';
    return $html;
}

function renderSyllabusOptions($nodes, $prefix = '') {
    $html = '';
    foreach ($nodes as $node) {
        $html .= '<option value="' . $node['id'] . '">' . $prefix . htmlspecialchars($node['title']) . ' (' . $node['type'] . ')</option>';
        if (!empty($node['children'])) {
            $html .= renderSyllabusOptions($node['children'], $prefix . '— ');
        }
    }
    return $html;
}
?>
