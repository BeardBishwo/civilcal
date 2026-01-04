<?php
/**
 * PREMIUM BLUEPRINT ARCHITECT SUITE
 * High-performance, multi-panel editor for advanced examination frameworks.
 */
$blueprint = $blueprint ?? null;
$syllabusTree = $syllabus_tree ?? [];
$isEdit = !empty($blueprint['id']);
?>

<!-- Architect Font & Main Styles -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<div class="arch-master-container">
    <div class="arch-content-wrapper">

        <!-- Phase 1: Interactive Header -->
        <header class="arch-header">
            <div class="header-main">
                <div class="title-block">
                    <div class="icon-orb">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <h1><?= $isEdit ? 'Template Architect' : 'Draft New Blueprint' ?></h1>
                        <p class="subtitle"><?= $isEdit ? 'Refining dynamic rules for ' . htmlspecialchars($blueprint['title']) : 'Configure question distribution and exam parameters.' ?></p>
                    </div>
                    <div id="validationPulse" class="arch-pulse" title="Structure Validity"></div>
                </div>
                
                <div class="arch-stats">
                    <div class="arch-pill">
                        <span class="pill-label">QUESTIONS</span>
                        <span class="pill-value" id="headerQty"><?= $blueprint['total_questions'] ?? 0 ?></span>
                    </div>
                    <div class="arch-pill">
                        <span class="pill-label">DURATION</span>
                        <span class="pill-value" id="headerTime"><?= $blueprint['duration_minutes'] ?? 0 ?>m</span>
                    </div>
                    <div class="arch-pill success">
                        <span class="pill-label">TOTAL MARKS</span>
                        <span class="pill-value" id="headerMarks"><?= $blueprint['total_marks'] ?? 0 ?></span>
                    </div>
                </div>
            </div>

            <!-- Command Bar -->
            <div class="arch-command-bar">
                <div class="command-group">
                    <button type="button" class="arch-btn secondary" onclick="validateBlueprint()">
                        <i class="fas fa-microscope"></i> VALIDATE STRUCTURE
                    </button>
                    <a href="<?= app_base_url('admin/quiz/blueprints') ?>" class="arch-btn flat">
                        <i class="fas fa-arrow-left"></i> RETURN TO VAULT
                    </a>
                </div>
                <div class="command-group">
                    <button type="button" class="arch-btn primary gradient" onclick="generateExam()">
                        <i class="fas fa-rocket"></i> DEPLOY INSTANCE
                    </button>
                </div>
            </div>
        </header>

        <!-- Phase 2: Dual-Panel Workbench -->
        <main class="arch-workbench">
            
            <!-- Sideboard: Template Parameters -->
            <aside class="arch-sideboard">
                <div class="workbench-section">
                    <h5 class="workbench-label">Global Parameters</h5>
                    <form id="blueprintForm" class="arch-form">
                        <input type="hidden" id="blueprintId" value="<?= $blueprint['id'] ?? '' ?>">
                        
                        <div class="arch-input-group">
                            <label>Template Title</label>
                            <div class="input-with-icon">
                                <i class="fas fa-pen-nib"></i>
                                <input type="text" id="blueprintTitle" value="<?= htmlspecialchars($blueprint['title'] ?? '') ?>" placeholder="e.g., Civil Level 5 Final" required>
                            </div>
                        </div>

                        <div class="arch-input-group">
                            <label>Target Level</label>
                            <div class="input-with-icon">
                                <i class="fas fa-signal"></i>
                                <select id="blueprintLevel" required onchange="loadSyllabusForLevel(this.value)">
                                    <option value="">Select Level</option>
                                    <?php 
                                    $levels = ['Level 4', 'Level 5', 'Level 6', 'Level 7'];
                                    foreach($levels as $lvl): ?>
                                        <option value="<?= $lvl ?>" <?= ($blueprint['level'] ?? '') === $lvl ? 'selected' : '' ?>><?= $lvl ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="arch-row">
                            <div class="arch-input-group">
                                <label>Qty</label>
                                <input type="number" id="blueprintTotalQuestions" value="<?= $blueprint['total_questions'] ?? 50 ?>" oninput="updateStats()">
                            </div>
                            <div class="arch-input-group">
                                <label>Marks</label>
                                <input type="number" id="blueprintTotalMarks" value="<?= $blueprint['total_marks'] ?? 100 ?>" oninput="updateStats()">
                            </div>
                        </div>

                        <div class="arch-row">
                            <div class="arch-input-group">
                                <label>Time (m)</label>
                                <input type="number" id="blueprintDuration" value="<?= $blueprint['duration_minutes'] ?? 60 ?>" oninput="updateStats()">
                            </div>
                            <div class="arch-input-group">
                                <label>Neg Rate</label>
                                <input type="number" id="blueprintNegativeMarking" value="<?= $blueprint['negative_marking_rate'] ?? 0 ?>" step="0.01">
                            </div>
                        </div>

                        <div class="arch-input-group">
                            <label>Wildcard Allocation (%)</label>
                            <div class="range-box">
                                <input type="range" id="blueprintWildcardRange" min="0" max="100" value="<?= $blueprint['wildcard_percentage'] ?? 10 ?>" oninput="document.getElementById('blueprintWildcard').value = this.value; updateStats();">
                                <input type="number" id="blueprintWildcard" value="<?= $blueprint['wildcard_percentage'] ?? 10 ?>" oninput="document.getElementById('blueprintWildcardRange').value = this.value; updateStats();">
                            </div>
                        </div>

                        <div class="arch-input-group">
                            <label>Architectural Notes</label>
                            <textarea id="blueprintDescription" rows="3"><?= htmlspecialchars($blueprint['description'] ?? '') ?></textarea>
                        </div>

                        <div class="arch-footer-actions">
                            <label class="arch-switch">
                                <input type="checkbox" id="blueprintActive" <?= ($blueprint['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <span class="arch-slider"></span>
                                <span class="switch-label">ACTIVE</span>
                            </label>
                            <button type="button" class="arch-btn primary" onclick="saveBlueprint()">
                                <i class="fas fa-sync-alt"></i> UPDATE
                            </button>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Main Canvas: Rule Engine -->
            <section class="arch-canvas">
                <div class="workbench-section">
                    <div class="canvas-header">
                        <h5 class="workbench-label">Question Distribution Rules</h5>
                        <button class="arch-btn ultra-sm primary" onclick="showAddRuleModal()">
                            <i class="fas fa-plus"></i> ADD SOURCE
                        </button>
                    </div>

                    <div class="arch-table-hull">
                        <table class="arch-table">
                            <thead>
                                <tr>
                                    <th>Syllabus Node</th>
                                    <th class="text-center">Required</th>
                                    <th>Distribution</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="rulesContainer">
                                <?php if (!empty($blueprint['rules'])): ?>
                                    <?php foreach ($blueprint['rules'] as $rule): ?>
                                        <?php echo renderRuleRow($rule); ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr id="noRulesRow">
                                        <td colspan="4" class="arch-empty-row">
                                            <i class="fas fa-project-diagram"></i>
                                            <p>No distribution rules defined yet.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="arch-summary">
                                    <td class="text-end">ALLOCATION TALLY:</td>
                                    <td class="text-center font-bold" id="totalRequired">0</td>
                                    <td colspan="2" class="arch-alignment-text" id="rulesTally">Calculating...</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Coverage Visualizer -->
                    <div class="arch-coverage">
                        <div class="coverage-header">
                            <span>SYLLABUS COVERAGE</span>
                            <span id="coveragePercent">0%</span>
                        </div>
                        <div class="arch-progress">
                            <div id="coverageBar" class="progress-fill"></div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<!-- ========================================
     MODALS: ISOLATED DOM ELEMENTS
     ======================================== -->

<!-- Add Rule Modal -->
<div class="modal fade arch-modal" id="ruleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Engineering Source Definition</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="ruleForm" onsubmit="saveRule(event)">
                <div class="modal-body">
                    <input type="hidden" id="ruleId">
                    <div class="arch-input-group inverted">
                        <label>Syllabus Target Node</label>
                        <select id="ruleSyllabusNode" required>
                            <option value="">Select Section/Unit</option>
                            <?php echo renderSyllabusOptions($syllabusTree); ?>
                        </select>
                    </div>

                    <div class="arch-input-group inverted">
                        <label>Target Quantity</label>
                        <input type="number" id="ruleQuestionsRequired" min="1" required placeholder="Number of questions">
                    </div>

                    <div class="arch-input-group inverted">
                        <label>Difficulty Balancing (Easy / Med / Hard)</label>
                        <div class="triple-input">
                            <input type="number" id="difficultyEasy" placeholder="0">
                            <input type="number" id="difficultyMedium" placeholder="0">
                            <input type="number" id="difficultyHard" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="arch-btn arch-btn-full primary">CONFIRM SYLLABUS SOURCE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deployment Modal -->
<div class="modal fade arch-modal" id="generateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deployment Protocol</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="generateForm" onsubmit="confirmGenerate(event)">
                <div class="modal-body text-center">
                    <div class="rocket-orb">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4 class="mt-3">Initialize Exam Engine?</h4>
                    <p class="text-muted small mb-4">This will lock current rules and generate a live session.</p>
                    
                    <div class="arch-input-group inverted text-start">
                        <label>Deployment Instance Title</label>
                        <input type="text" id="generateTitle" value="<?= htmlspecialchars($blueprint['title'] ?? '') ?> - <?= date('Y-m-d') ?>">
                    </div>

                    <div class="arch-checkbox-group">
                        <label class="arch-checkbox">
                            <input type="checkbox" id="generateShuffle" checked>
                            <span>SHUFFLE SELECTION</span>
                        </label>
                        <label class="arch-checkbox">
                            <input type="checkbox" id="generateWildcard" checked>
                            <span>INCLUDE WILDCARDS</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="arch-btn arch-btn-full primary success">DEPLOY LIVE SESSION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ========================================
   ARCHITECT DESIGN SYSTEM
   ======================================== */
:root {
    --arch-bg: #f3f4f6;
    --arch-primary: #6366f1;
    --arch-primary-dark: #4f46e5;
    --arch-secondary: #ec4899;
    --arch-dark: #0f172a;
    --arch-border: #e2e8f0;
    --arch-text-main: #1e293b;
    --arch-text-muted: #64748b;
    --arch-white: #ffffff;
    --arch-success: #10b981;
}

.arch-master-container {
    font-family: 'Outfit', sans-serif;
    background: var(--arch-bg);
    min-height: 100vh;
    padding: 1.5rem;
}

.arch-content-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    background: var(--arch-white);
    border-radius: 20px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* Header Styles */
.arch-header {
    background: var(--arch-dark);
    color: var(--arch-white);
    padding: 2rem;
}

.header-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.title-block {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.icon-orb {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.1);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--arch-primary);
    border: 1px solid rgba(255,255,255,0.1);
}

.title-block h1 {
    font-size: 1.75rem;
    font-weight: 800;
    margin: 0;
    color: var(--arch-white);
}

.title-block .subtitle {
    font-size: 0.9rem;
    color: var(--arch-text-muted);
    margin: 0.25rem 0 0;
}

/* Pulse State */
.arch-pulse {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #475569;
    margin-left: 10px;
    box-shadow: 0 0 0 4px rgba(71, 85, 105, 0.2);
}
.arch-pulse.active { background: var(--arch-success); box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2); animation: apulse 2s infinite; }
.arch-pulse.error { background: #ef4444; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2); }

@keyframes apulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }

/* Stats */
.arch-stats { display: flex; gap: 1rem; }
.arch-pill {
    background: rgba(255,255,255,0.05);
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.1);
    display: flex;
    flex-direction: column;
    min-width: 100px;
    text-align: center;
}
.arch-pill.success { background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2); }
.pill-label { font-size: 0.65rem; font-weight: 700; color: var(--arch-text-muted); letter-spacing: 1px; }
.pill-value { font-size: 1.25rem; font-weight: 800; }

/* Command Bar */
.arch-command-bar {
    display: flex;
    justify-content: space-between;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.command-group { display: flex; gap: 0.75rem; }

/* Workbench */
.arch-workbench {
    display: flex;
    min-height: 600px;
}

.arch-sideboard {
    width: 380px;
    background: #f8fafc;
    border-right: 1px solid var(--arch-border);
    padding: 2rem;
}

.arch-canvas {
    flex: 1;
    padding: 2rem;
    background: var(--arch-white);
}

.workbench-label {
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--arch-text-muted);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 2rem;
    display: block;
}

/* Forms */
.arch-form { display: flex; flex-direction: column; gap: 1.5rem; }
.arch-input-group { display: flex; flex-direction: column; gap: 0.5rem; }
.arch-input-group label { font-size: 0.75rem; font-weight: 700; color: var(--arch-text-main); }
.arch-input-group input, .arch-input-group select, .arch-input-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    border: 1px solid var(--arch-border);
    font-size: 0.9rem;
    font-weight: 500;
    transition: 0.2s;
}
.arch-input-group input:focus { border-color: var(--arch-primary); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); outline: none; }

.input-with-icon { position: relative; }
.input-with-icon i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--arch-text-muted); }
.input-with-icon input, .input-with-icon select { padding-left: 2.75rem; }

.arch-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

.range-box { display: flex; align-items: center; gap: 1rem; }
.range-box input[type="number"] { width: 70px; text-align: center; }

/* Buttons */
.arch-btn {
    height: 44px;
    padding: 0 1.5rem;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    transition: 0.2s;
    border: none;
    text-decoration: none;
}
.arch-btn.primary { background: var(--arch-primary); color: var(--arch-white); }
.arch-btn.primary.gradient { background: linear-gradient(135deg, var(--arch-primary) 0%, var(--arch-primary-dark) 100%); }
.arch-btn.secondary { background: rgba(255,255,255,0.1); color: var(--arch-white); border: 1px solid rgba(255,255,255,0.1); }
.arch-btn.flat { background: transparent; color: var(--arch-text-muted); }
.arch-btn.ultra-sm { height: 32px; padding: 0 1rem; font-size: 0.7rem; }
.arch-btn.success { background: var(--arch-success); }
.arch-btn.arch-btn-full { width: 100%; justify-content: center; height: 50px; font-size: 0.95rem; }

.arch-btn:hover { transform: translateY(-2px); opacity: 0.9; }

/* Table */
.canvas-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; }
.arch-table-hull { background: var(--arch-white); border-radius: 12px; border: 1px solid var(--arch-border); overflow: hidden; }
.arch-table { width: 100%; border-collapse: collapse; }
.arch-table thead th { background: #f8fafc; padding: 1rem 1.5rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: var(--arch-text-muted); text-transform: uppercase; }
.arch-table td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--arch-border); vertical-align: middle; }

.arch-empty-row { padding: 5rem !important; text-align: center; color: var(--arch-text-muted); }
.arch-empty-row i { font-size: 3rem; margin-bottom: 1.5rem; opacity: 0.2; display: block; }
.arch-empty-row p { font-weight: 600; margin: 0; }

.arch-summary td { background: #f8fafc; font-weight: 800; font-size: 0.85rem; border: none; }
.arch-alignment-text { color: var(--arch-text-muted); font-style: italic; font-weight: 500 !important; font-size: 0.8rem; }

/* Modal Custom styling */
.arch-modal .modal-content { border-radius: 20px; border: none; background: #f8fafc; overflow: hidden; }
.arch-modal .modal-header { background: var(--arch-dark); border: none; padding: 1.5rem 2rem; }
.arch-modal .modal-title { color: white; font-weight: 800; font-size: 1.1rem; }
.arch-modal .modal-body { padding: 2rem; }
.arch-modal .modal-footer { padding: 0 2rem 2rem; border: none; }

.arch-input-group.inverted label { color: var(--arch-text-muted); font-weight: 800; text-transform: uppercase; font-size: 0.7rem; }
.arch-input-group.inverted input, .arch-input-group.inverted select { background: white; border-color: #cbd5e1; }

.triple-input { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
.triple-input input { text-align: center; font-weight: 800; font-size: 1.1rem; color: var(--arch-primary); }

.rocket-orb { width: 80px; height: 80px; border-radius: 50%; background: #e0e7ff; color: var(--arch-primary); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto; }

/* Switch */
.arch-switch { position: relative; display: flex; align-items: center; gap: 0.75rem; cursor: pointer; }
.arch-switch input { opacity: 0; width: 0; height: 0; }
.arch-slider { width: 44px; height: 22px; background: #cbd5e1; border-radius: 20px; position: relative; transition: 0.3s; }
.arch-slider:before { content: ""; position: absolute; height: 16px; width: 16px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.3s; }
input:checked + .arch-slider { background: var(--arch-success); }
input:checked + .arch-slider:before { transform: translateX(22px); }
.switch-label { font-size: 0.7rem; font-weight: 800; color: var(--arch-text-muted); }

/* Progress */
.arch-coverage { margin-top: 2rem; }
.coverage-header { display: flex; justify-content: space-between; font-size: 0.7rem; font-weight: 800; color: var(--arch-text-muted); margin-bottom: 0.5rem; }
.arch-progress { height: 10px; background: #e2e8f0; border-radius: 20px; overflow: hidden; }
.progress-fill { height: 100%; background: var(--arch-primary); transition: 0.5s; width: 0; }

/* Checkbox */
.arch-checkbox-group { display: flex; justify-content: center; gap: 2rem; margin-top: 1.5rem; }
.arch-checkbox { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 800; color: var(--arch-text-main); cursor: pointer; }
.arch-checkbox input { width: 18px; height: 18px; }
</style>

<script>
const baseUrl = '<?= app_base_url() ?>';
const blueprintId = <?= $blueprint['id'] ?? 'null' ?>;

document.addEventListener('DOMContentLoaded', () => {
    updateStats();
    validateBlueprint(true);
});

function updateStats() {
    const qty = document.getElementById('blueprintTotalQuestions').value;
    const time = document.getElementById('blueprintDuration').value;
    const marks = document.getElementById('blueprintTotalMarks').value;
    
    document.getElementById('headerQty').innerText = qty;
    document.getElementById('headerTime').innerText = time + 'm';
    document.getElementById('headerMarks').innerText = marks;
    
    let totalRulesQty = 0;
    document.querySelectorAll('.rule-qty').forEach(el => totalRulesQty += parseInt(el.innerText) || 0);
    document.getElementById('totalRequired', totalRulesQty); // Fallback for ID setter
    if(document.getElementById('totalRequired')) document.getElementById('totalRequired').innerText = totalRulesQty;
    
    const wildcard = parseInt(document.getElementById('blueprintWildcard').value) || 0;
    const requiredBySyllabus = Math.ceil(qty * (1 - (wildcard/100)));
    
    const percent = Math.min(100, (totalRulesQty / requiredBySyllabus) * 100);
    if(document.getElementById('coverageBar')) document.getElementById('coverageBar').style.width = percent + '%';
    if(document.getElementById('coveragePercent')) document.getElementById('coveragePercent').innerText = Math.round(percent) + '%';
    if(document.getElementById('rulesTally')) document.getElementById('rulesTally').innerText = `${totalRulesQty} of ${requiredBySyllabus} syllabus questions assigned.`;
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

    Swal.fire({ title: 'Architecting...', didOpen: () => Swal.showLoading() });

    try {
        const r = await fetch(url, { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: new URLSearchParams(data) });
        const res = await r.json();
        if (res.success) {
            Swal.fire({ icon:'success', title: 'Blueprint Synced', timer: 1500, showConfirmButton: false }).then(() => {
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
    const modal = new bootstrap.Modal(document.getElementById('ruleModal'));
    modal.show();
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

    Swal.fire({ title: 'Mapping Source...', didOpen: () => Swal.showLoading() });

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
        title: 'Purge Rule?', text: "This will remove this syllabus mapping from the template.",
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Purge'
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
        if(pulse) pulse.className = data.valid ? 'arch-pulse active' : 'arch-pulse error';
        
        if (!silent) {
            Swal.fire({
                title: data.valid ? 'Architecture Verified' : 'Architecture Exceptions',
                html: data.valid ? 'Design is stable and ready for deployment.' : `<div class="text-start small"><ul>${data.errors.map(e => `<li>${e}</li>`).join('')}</ul></div>`,
                icon: data.valid ? 'success' : 'error',
                confirmButtonColor: data.valid ? '#10b981' : '#6366f1'
            });
        }
    } catch(e) {}
}

function generateExam() {
    const modal = new bootstrap.Modal(document.getElementById('generateModal'));
    modal.show();
}

async function confirmGenerate(event) {
    event.preventDefault();
    const data = {
        exam_title: document.getElementById('generateTitle').value,
        shuffle: document.getElementById('generateShuffle').checked ? 1 : 0,
        include_wildcard: document.getElementById('generateWildcard').checked ? 1 : 0,
        save: 1
    };

    Swal.fire({ title: 'Executing Engine...', html: 'Selecting optimal question paths...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const r = await fetch(`${baseUrl}/admin/quiz/blueprints/${blueprintId}/generate`, {
            method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: new URLSearchParams(data)
        });
        const res = await r.json();
        if (res.success) {
            Swal.fire({ icon:'success', title: 'Instance Deployed', text: 'Exam engine initialized.', timer: 1500, showConfirmButton:false })
            .then(() => { if (res.redirect) window.location = res.redirect; });
        } else {
            Swal.fire('Engine Error', res.error, 'error');
        }
    } catch(e) { Swal.fire('Error', 'Fatal Core Error', 'error'); }
}

function loadSyllabusForLevel(level) {
    if (level) window.location = `${baseUrl}/admin/quiz/blueprints/edit/${blueprintId}?level=${level}`;
}
</script>

<?php
function renderRuleRow($rule) {
    $diffDist = $rule['difficulty_distribution'] ?? [];
    $html = '<tr class="rule-row">';
    $html .= '<td><div class="arch-node-title">' . htmlspecialchars($rule['node_title']) . '</div><div class="arch-node-type">' . $rule['node_type'] . '</div></td>';
    $html .= '<td class="text-center"><span class="rule-qty">' . $rule['questions_required'] . '</span></td>';
    $html .= '<td>';
    if (!empty($diffDist)) {
        foreach ($diffDist as $level => $count) {
            $html .= '<span class="arch-diff-badge arch-diff-' . $level . '">' . strtoupper($level) . ': ' . $count . '</span>';
        }
    } else {
        $html .= '<span class="arch-alignment-text">Random Select</span>';
    }
    $html .= '</td>';
    $html .= '<td class="text-center">';
    $html .= '<button onclick="deleteRule(' . $rule['id'] . ')" class="arch-btn-icon-del"><i class="fas fa-trash-alt"></i></button>';
    $html .= '</td></tr>';
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

<style>
/* Additional Table Inner Styles */
.arch-node-title { font-weight: 700; color: var(--arch-text-main); font-size: 0.9rem; }
.arch-node-type { font-size: 0.6rem; font-weight: 800; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; display: inline-block; margin-top: 4px; }
.rule-qty { font-weight: 800; color: var(--arch-primary); font-size: 1.1rem; }
.arch-diff-badge { font-size: 0.6rem; font-weight: 700; padding: 2px 8px; border-radius: 10px; margin-right: 4px; }
.arch-diff-easy { background: #d1fae5; color: #065f46; }
.arch-diff-medium { background: #fef3c7; color: #92400e; }
.arch-diff-hard { background: #fee2e2; color: #991b1b; }
.arch-btn-icon-del { width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; color: #94a3b8; cursor: pointer; transition: 0.2s; }
.arch-btn-icon-del:hover { background: #fee2e2; color: #ef4444; border-color: #fecaca; }
</style>
