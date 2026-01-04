<?php
/**
 * PREMIUM BLUEPRINT DRAFTING SUITE
 * Sophisticated, multi-panel editor for advanced exam architecture.
 */
$blueprint = $blueprint ?? null;
$syllabusTree = $syllabus_tree ?? [];
$isEdit = !empty($blueprint['id']);
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper pb-0">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-drafting-compass"></i>
                    <h1><?= $isEdit ? 'Template Architect' : 'Draft New Blueprint' ?></h1>
                    <div id="validationPulse" class="valid-indicator" title="Validity Status"></div>
                </div>
                <div class="header-subtitle"><?= $isEdit ? 'Refining dynamic rules for ' . htmlspecialchars($blueprint['title']) : 'Configure question distribution and exam parameters.' ?></div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">QUESTIONS</span>
                    <span class="value" id="headerQty"><?= $blueprint['total_questions'] ?? 0 ?></span>
                </div>
                <div class="stat-pill">
                    <span class="label">TIME</span>
                    <span class="value" id="headerTime"><?= $blueprint['duration_minutes'] ?? 0 ?>m</span>
                </div>
                <div class="stat-pill success">
                    <span class="label">MARKS</span>
                    <span class="value" id="headerMarks"><?= $blueprint['total_marks'] ?? 0 ?></span>
                </div>
            </div>
        </div>

        <!-- Utility Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <button type="button" class="btn-create-premium secondary" onclick="validateBlueprint()">
                    <i class="fas fa-shield-alt"></i> VALIDATE STRUCTURE
                </button>
            </div>
            <div class="toolbar-right">
                <button type="button" class="btn-create-premium" onclick="generateExam()">
                    <i class="fas fa-bolt"></i> DEPLOY INSTANCE
                </button>
            </div>
        </div>

        <div class="row g-0 flex-nowrap" style="display: flex !important;">
            <!-- Left Panel: Drafting Controls -->
            <div class="col-lg-4 border-end">
                <div class="panel-section">
                    <h5 class="section-title">Global Parameters</h5>
                    <form id="blueprintForm" class="drafting-form p-4">
                        <input type="hidden" id="blueprintId" value="<?= $blueprint['id'] ?? '' ?>">
                        
                        <div class="premium-input-group mb-3">
                            <label class="premium-label">Template Title</label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading icon"></i>
                                <input type="text" id="blueprintTitle" class="premium-input" value="<?= htmlspecialchars($blueprint['title'] ?? '') ?>" placeholder="e.g., Civil Engineering Grade 7" required>
                            </div>
                        </div>

                        <div class="premium-input-group mb-3">
                            <label class="premium-label">Target Level</label>
                            <div class="input-wrapper">
                                <i class="fas fa-layer-group icon"></i>
                                <select id="blueprintLevel" class="premium-input" required onchange="loadSyllabusForLevel(this.value)">
                                    <option value="">Select Level</option>
                                    <option value="Level 4" <?= ($blueprint['level'] ?? '') === 'Level 4' ? 'selected' : '' ?>>Level 4</option>
                                    <option value="Level 5" <?= ($blueprint['level'] ?? '') === 'Level 5' ? 'selected' : '' ?>>Level 5</option>
                                    <option value="Level 7" <?= ($blueprint['level'] ?? '') === 'Level 7' ? 'selected' : '' ?>>Level 7</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="premium-input-group mb-3">
                                    <label class="premium-label">Question Count</label>
                                    <input type="number" id="blueprintTotalQuestions" class="premium-input px-3" value="<?= $blueprint['total_questions'] ?? 50 ?>" oninput="updateStats()">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="premium-input-group mb-3">
                                    <label class="premium-label">Total Marks</label>
                                    <input type="number" id="blueprintTotalMarks" class="premium-input px-3" value="<?= $blueprint['total_marks'] ?? 100 ?>" oninput="updateStats()">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="premium-input-group mb-3">
                                    <label class="premium-label">Duration (Min)</label>
                                    <input type="number" id="blueprintDuration" class="premium-input px-3" value="<?= $blueprint['duration_minutes'] ?? 60 ?>" oninput="updateStats()">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="premium-input-group mb-3">
                                    <label class="premium-label">NEG Marking</label>
                                    <input type="number" id="blueprintNegativeMarking" class="premium-input px-3" value="<?= $blueprint['negative_marking_rate'] ?? 0 ?>" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="premium-input-group mb-3">
                            <label class="premium-label">Wildcard % <small>(Practical/Extra)</small></label>
                            <div class="range-wrapper d-flex align-items-center gap-3">
                                <input type="range" id="blueprintWildcardRange" class="form-range" min="0" max="100" value="<?= $blueprint['wildcard_percentage'] ?? 10 ?>" oninput="document.getElementById('blueprintWildcard').value = this.value">
                                <input type="number" id="blueprintWildcard" class="premium-input text-center" style="width: 70px;" value="<?= $blueprint['wildcard_percentage'] ?? 10 ?>" oninput="document.getElementById('blueprintWildcardRange').value = this.value">
                            </div>
                        </div>

                        <div class="premium-input-group mb-4">
                            <label class="premium-label">Description</label>
                            <textarea id="blueprintDescription" class="premium-input p-3" rows="2"><?= htmlspecialchars($blueprint['description'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="premium-toggle-group border-0 p-0">
                                <label class="switch scale-sm">
                                    <input type="checkbox" id="blueprintActive" <?= ($blueprint['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <span class="slider round"></span>
                                </label>
                                <span class="toggle-label ps-2">ACTIVE TEMPLATE</span>
                            </div>
                            <button type="button" class="btn-create-premium" onclick="saveBlueprint()">
                                <i class="fas fa-save"></i> UPDATE
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Panel: Distribution Rules -->
            <div class="col-lg-8 bg-light-soft">
                <div class="panel-section">
                    <div class="section-header d-flex justify-content-between align-items-center p-4 pb-2">
                        <h5 class="section-title m-0">Question Distribution Rules</h5>
                        <button class="btn-create-premium sm" onclick="showAddRuleModal()">
                            <i class="fas fa-plus"></i> ADD SOURCE
                        </button>
                    </div>

                    <div class="table-container p-4">
                        <div class="table-wrapper premium-shadow">
                            <table class="table-compact premium-table">
                                <thead>
                                    <tr>
                                        <th>Syllabus Node</th>
                                        <th class="text-center">Req. Qs</th>
                                        <th>Difficulty Distribution</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="rulesContainer">
                                    <?php if (!empty($blueprint['rules'])): ?>
                                        <?php foreach ($blueprint['rules'] as $rule): ?>
                                            <?php echo renderRuleRow($rule); ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr id="noRulesRow"><td colspan="4" class="text-center py-5 text-muted">No distribution rules defined yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-summary-row">
                                        <td class="text-end fw-bold">TOTAL COVERED:</td>
                                        <td class="text-center fw-bold" id="totalRequired">0</td>
                                        <td colspan="2" class="text-muted small italic" id="rulesTally">Calculating alignment...</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Visual Summary Progress -->
                    <div class="p-4 pt-0">
                        <div class="coverage-card">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small fw-bold">Syllabus Coverage</span>
                                <span class="small" id="coveragePercent">0%</span>
                            </div>
                            <div class="progress" style="height: 6px; background: #e2e8f0;">
                                <div id="coverageBar" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Rule Modal -->
<div class="modal fade premium-modal" id="ruleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header header-premium">
                <h5 class="modal-title m-0 text-white">Project Rule Definition</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="ruleForm" onsubmit="saveRule(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="ruleId">
                    
                    <div class="premium-input-group mb-3">
                        <label class="premium-label">Syllabus Target</label>
                        <select id="ruleSyllabusNode" class="premium-input px-3" required>
                            <option value="">Select Section/Unit</option>
                            <?php echo renderSyllabusOptions($syllabusTree); ?>
                        </select>
                    </div>

                    <div class="premium-input-group mb-3">
                        <label class="premium-label">Quantity Required</label>
                        <input type="number" id="ruleQuestionsRequired" class="premium-input px-3" min="1" required placeholder="Number of questions to pull">
                    </div>

                    <div class="premium-input-group">
                        <label class="premium-label">Hardness Targets <small>(Optional)</small></label>
                        <div class="d-flex gap-2">
                            <div class="diff-slot">
                                <label class="tiny-label">Easy</label>
                                <input type="number" id="difficultyEasy" class="premium-input px-2 text-center" placeholder="0">
                            </div>
                            <div class="diff-slot">
                                <label class="tiny-label">Medium</label>
                                <input type="number" id="difficultyMedium" class="premium-input px-2 text-center" placeholder="0">
                            </div>
                            <div class="diff-slot">
                                <label class="tiny-label">Hard</label>
                                <input type="number" id="difficultyHard" class="premium-input px-2 text-center" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn-create-premium w-100">CONFIRM SOURCE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deployment Modal -->
<div class="modal fade premium-modal" id="generateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header header-premium">
                <h5 class="modal-title m-0 text-white">Instance Deployment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="generateForm" onsubmit="confirmGenerate(event)">
                <div class="modal-body p-4 text-center">
                    <div class="deployment-icon mb-3">
                        <i class="fas fa-rocket text-primary"></i>
                    </div>
                    <h6>Ready to randomize questions?</h6>
                    <p class="text-muted small">Generating an exam will lock current blueprint rules into a live instance.</p>
                    
                    <div class="premium-input-group mb-3 text-start">
                        <label class="premium-label">Deployment Title</label>
                        <input type="text" id="generateTitle" class="premium-input px-3" value="<?= htmlspecialchars($blueprint['title'] ?? '') ?> - <?= date('Y-m-d') ?>">
                    </div>

                    <div class="d-flex justify-content-center gap-4 py-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="generateShuffle" checked>
                            <label class="form-check-label small" for="generateShuffle">Shuffle</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="generateWildcard" checked>
                            <label class="form-check-label small" for="generateWildcard">Wildcards</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn-create-premium w-100 success">DEPLOY NOW</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const baseUrl = '<?= app_base_url() ?>';
const blueprintId = <?= $blueprint['id'] ?? 'null' ?>;

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    updateStats();
    validateBlueprint(true); // Silent validation
});

function updateStats() {
    document.getElementById('headerQty').innerText = document.getElementById('blueprintTotalQuestions').value;
    document.getElementById('headerTime').innerText = document.getElementById('blueprintDuration').value + 'm';
    document.getElementById('headerMarks').innerText = document.getElementById('blueprintTotalMarks').value;
    
    // Calculate Tally
    let total = 0;
    document.querySelectorAll('.rule-qty').forEach(el => total += parseInt(el.innerText) || 0);
    document.getElementById('totalRequired').innerText = total;
    
    const target = parseInt(document.getElementById('blueprintTotalQuestions').value) || 1;
    const wildcard = parseInt(document.getElementById('blueprintWildcard').value) || 0;
    const requiredBySyllabus = Math.ceil(target * (1 - (wildcard/100)));
    
    const percent = Math.min(100, (total / requiredBySyllabus) * 100);
    document.getElementById('coverageBar').style.width = percent + '%';
    document.getElementById('coveragePercent').innerText = Math.round(percent) + '%';
    document.getElementById('rulesTally').innerText = `${total} of ${requiredBySyllabus} syllabus questions covered.`;
}

async function saveBlueprint() {
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

    try {
        const r = await fetch(url, { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: new URLSearchParams(data) });
        const res = await r.json();
        if (res.success) {
            Swal.fire({ icon:'success', title: 'Architect Saved', timer: 1500, showConfirmButton: false }).then(() => {
                if (res.redirect) window.location = res.redirect;
            });
        } else {
            Swal.fire('Error', res.error, 'error');
        }
    } catch(e) { Swal.fire('Error', 'Server Sync Failure', 'error'); }
}

function showAddRuleModal() {
    document.getElementById('ruleForm').reset();
    document.getElementById('ruleId').value = '';
    new bootstrap.Modal(document.getElementById('ruleModal')).show();
}

async function saveRule(event) {
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

    try {
        const r = await fetch(`${baseUrl}/admin/quiz/blueprints/${blueprintId}/add-rule`, {
            method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: new URLSearchParams(data)
        });
        const res = await r.json();
        if (res.success) location.reload(); else Swal.fire('Validation Error', res.error, 'error');
    } catch(e) { Swal.fire('Error', 'Communication Error', 'error'); }
}

async function deleteRule(ruleId) {
    const res = await Swal.fire({
        title: 'Delete Rule?', text: "This will remove this syllabus source from the blueprint.",
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Delete'
    });
    if (!res.isConfirmed) return;
    
    try {
        const r = await fetch(`${baseUrl}/admin/quiz/blueprints/rule/${ruleId}/delete`, { method: 'POST' });
        const data = await r.json();
        if (data.success) location.reload(); else Swal.fire('Error', data.error, 'error');
    } catch(e) { Swal.fire('Error', 'Network Failure', 'error'); }
}

async function validateBlueprint(silent = false) {
    try {
        const r = await fetch(`${baseUrl}/admin/quiz/blueprints/${blueprintId}/validate`);
        const data = await r.json();
        const pulse = document.getElementById('validationPulse');
        pulse.className = data.valid ? 'valid-indicator active' : 'valid-indicator error';
        
        if (!silent) {
            Swal.fire({
                title: data.valid ? 'Architect Valid' : 'Architect Errors',
                html: data.valid ? 'Template structure is sound and ready for deployment.' : `<div class="text-start small"><ul>${data.errors.map(e => `<li>${e}</li>`).join('')}</ul></div>`,
                icon: data.valid ? 'success' : 'error',
                confirmButtonColor: data.valid ? '#10b981' : '#667eea'
            });
        }
    } catch(e) {}
}

function generateExam() {
    new bootstrap.Modal(document.getElementById('generateModal')).show();
}

async function confirmGenerate(event) {
    event.preventDefault();
    
    const data = {
        exam_title: document.getElementById('generateTitle').value,
        shuffle: document.getElementById('generateShuffle').checked ? 1 : 0,
        include_wildcard: document.getElementById('generateWildcard').checked ? 1 : 0,
        save: 1
    };

    Swal.fire({ title: 'Generating Engine...', html: 'Selecting questions based on distribution rules...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const r = await fetch(`${baseUrl}/admin/quiz/blueprints/${blueprintId}/generate`, {
            method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: new URLSearchParams(data)
        });
        const res = await r.json();
        if (res.success) {
            Swal.fire({ icon:'success', title: 'Deployment Success', text: 'Exam has been generated.', timer: 1500, showConfirmButton:false })
            .then(() => { if (res.redirect) window.location = res.redirect; });
        } else {
            Swal.fire('Architecture Error', res.error, 'error');
        }
    } catch(e) { Swal.fire('Error', 'Fatal System Error', 'error'); }
}

function loadSyllabusForLevel(level) {
    if (level) window.location = `${baseUrl}/admin/quiz/blueprints/edit/${blueprintId}?level=${level}`;
}
</script>

<style>
/* ========================================
   PREMIUM EDITOR STYLES
   ======================================== */
:root {
    --editor-bg: #f8fafc;
    --editor-border: #e2e8f0;
    --editor-primary: #667eea;
    --editor-success: #10b981;
}

.bg-light-soft { background: #f1f5f9; }
.panel-section { height: calc(100vh - 160px); overflow-y: auto; overflow-x: hidden; }
/* Force Grid preservation */
.row.g-0 { display: flex !important; flex-wrap: nowrap !important; width: 100% !important; }
.col-lg-4 { width: 33.333333% !important; flex: 0 0 33.333333% !important; max-width: 33.333333% !important; }
.col-lg-8 { width: 66.666667% !important; flex: 0 0 66.666667% !important; max-width: 66.666667% !important; }
.section-title { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; padding: 1.5rem 1.5rem 0.5rem; }

/* Pulse Indicator */
.valid-indicator { width: 12px; height: 12px; border-radius: 50%; background: #cbd5e1; margin-left: 10px; transition: 0.3s; }
.valid-indicator.active { background: var(--editor-success); box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2); animation: pulse-valid 2s infinite; }
.valid-indicator.error { background: #ef4444; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2); }

@keyframes pulse-valid { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }

/* Inputs */
.premium-input-group { display: flex; flex-direction: column; gap: 6px; }
.premium-label { font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; }
.input-wrapper { position: relative; }
.input-wrapper .icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; }
.premium-input {
    width: 100%; height: 44px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0 1rem 0 2.5rem;
    font-size: 0.9rem; font-weight: 600; color: #1e293b; background: white; transition: 0.2s; outline: none;
}
.premium-input:focus { border-color: var(--editor-primary); box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); }
textarea.premium-input { height: auto; padding-left: 1rem; }

.btn-create-premium.secondary { background: white; border: 1px solid #cbd5e1; color: #64748b; box-shadow: none; }
.btn-create-premium.sm { height: 32px; padding: 0 1rem; font-size: 0.75rem; }
.btn-create-premium.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2); }

/* Table Improvements */
.premium-shadow { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border-radius: 12px; overflow: hidden; }
.premium-table { background: white; border: none; }
.premium-table thead th { background: #f8fafc; border: none; padding: 1rem 1.5rem; font-size: 0.65rem; }
.premium-table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; }

.node-title { font-weight: 700; color: #1e293b; font-size: 0.85rem; }
.node-type { font-size: 0.6rem; font-weight: 800; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; }
.rule-qty { font-weight: 800; color: var(--editor-primary); font-size: 1rem; }

.diff-badge { font-size: 0.6rem; font-weight: 700; padding: 2px 8px; border-radius: 10px; margin-right: 4px; }
.diff-easy { background: #d1fae5; color: #065f46; }
.diff-medium { background: #fef3c7; color: #92400e; }
.diff-hard { background: #fee2e2; color: #991b1b; }

.table-summary-row td { background: #f8fafc; border: none; border-top: 2px solid #e2e8f0; }

.deployment-icon { width: 60px; height: 60px; border-radius: 50%; background: #f0f7ff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto; }
</style>

<?php
function renderRuleRow($rule) {
    $diffDist = $rule['difficulty_distribution'] ?? [];
    $html = '<tr class="rule-row">';
    $html .= '<td><div class="node-title">' . htmlspecialchars($rule['node_title']) . '</div><div class="node-type d-inline-block mt-1">' . $rule['node_type'] . '</div></td>';
    $html .= '<td class="text-center align-middle"><span class="rule-qty">' . $rule['questions_required'] . '</span></td>';
    $html .= '<td class="align-middle">';
    
    if (!empty($diffDist)) {
        foreach ($diffDist as $level => $count) {
            $html .= '<span class="diff-badge diff-' . $level . '">' . ucfirst($level) . ': ' . $count . '</span>';
        }
    } else {
        $html .= '<span class="text-muted small italic">Random Selection</span>';
    }
    
    $html .= '</td>';
    $html .= '<td class="text-center align-middle">';
    $html .= '<div class="actions-compact justify-center">';
    $html .= '<button onclick="deleteRule(' . $rule['id'] . ')" class="action-btn-icon delete-btn"><i class="fas fa-trash-alt"></i></button>';
    $html .= '</div></td></tr>';
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
