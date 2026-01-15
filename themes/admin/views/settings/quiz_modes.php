<?php
$content = '
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Premium Gradient Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-gamepad"></i>
                    <h1>Quiz Modes Control</h1>
                </div>
                <div class="header-subtitle">Master control panel for quiz features and game modes visibility</div>
            </div>
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">MODES</span>
                    <span class="value">' . count($modes ?? []) . '</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="modes-content" x-data="quizModesManager()">
            <div class="creation-toolbar">
                <h5 class="toolbar-title"><i class="fas fa-toggle-on mr-2"></i> Available Quiz Modes</h5>
            </div>

            <!-- Modes Grid -->
            <div class="modes-grid">
                <!-- Daily Quiz -->
                <div class="mode-card">
                    <div class="mode-icon-wrapper" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="mode-info">
                        <h3>Daily Quiz</h3>
                        <p>One quiz per day with streak tracking and rewards</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" x-model="modes.quiz_mode_daily" @change="autoSave">
                        <span class="slider round"></span>
                    </label>
                </div>

                <!-- Quiz Zone -->
                <div class="mode-card">
                    <div class="mode-icon-wrapper" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <div class="mode-info">
                        <h3>Quiz Zone</h3>
                        <p>Syllabus-based structured learning path</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" x-model="modes.quiz_mode_zone" @change="autoSave">
                        <span class="slider round"></span>
                    </label>
                </div>

                <!-- Contest Play -->
                <div class="mode-card">
                    <div class="mode-icon-wrapper" style="background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%);">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="mode-info">
                        <h3>Contest Play</h3>
                        <p>Timed contests with entry fees and prizes</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" x-model="modes.quiz_mode_contest" @change="autoSave">
                        <span class="slider round"></span>
                    </label>
                </div>

                <!-- 1 V/S 1 Battle -->
                <div class="mode-card">
                    <div class="mode-icon-wrapper" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <i class="fas fa-swords"></i>
                    </div>
                    <div class="mode-info">
                        <h3>1 V/S 1 Battle</h3>
                        <p>Head-to-head quiz battles with wagers</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" x-model="modes.quiz_mode_battle_1v1" @change="autoSave">
                        <span class="slider round"></span>
                    </label>
                </div>

                <!-- Math Mania -->
                <div class="mode-card">
                    <div class="mode-icon-wrapper" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="mode-info">
                        <h3>Math Mania</h3>
                        <p>Fast-paced math challenges and competitions</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" x-model="modes.quiz_mode_math" @change="autoSave">
                        <span class="slider round"></span>
                    </label>
                </div>

                <!-- Exam Mode -->
                <div class="mode-card">
                    <div class="mode-icon-wrapper" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="mode-info">
                        <h3>Exam Mode</h3>
                        <p>Structured exams with time limits and grading</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" x-model="modes.quiz_mode_exam" @change="autoSave">
                        <span class="slider round"></span>
                    </label>
                </div>

                <!-- Guess Word -->
                <div class="mode-card">
                    <div class="mode-icon-wrapper" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                        <i class="fas fa-spell-check"></i>
                    </div>
                    <div class="mode-info">
                        <h3>Guess Word</h3>
                        <p>Word guessing games and vocabulary challenges</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" x-model="modes.quiz_mode_guess_word" @change="autoSave">
                        <span class="slider round"></span>
                    </label>
                </div>

                <!-- Multi Match -->
                <div class="mode-card">
                    <div class="mode-icon-wrapper" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                        <i class="fas fa-puzzle-piece"></i>
                    </div>
                    <div class="mode-info">
                        <h3>Multi Match</h3>
                        <p>Match multiple items and pairs correctly</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" x-model="modes.quiz_mode_multi_match" @change="autoSave">
                        <span class="slider round"></span>
                    </label>
                </div>

                <!-- True/False -->
                <div class="mode-card">
                    <div class="mode-icon-wrapper" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="mode-info">
                        <h3>True/False</h3>
                        <p>Quick true or false statement quizzes</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" x-model="modes.quiz_mode_true_false" @change="autoSave">
                        <span class="slider round"></span>
                    </label>
                </div>

                <!-- Group Battle -->
                <div class="mode-card">
                    <div class="mode-icon-wrapper" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="mode-info">
                        <h3>Group Battle</h3>
                        <p>Team-based quiz competitions and battles</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" x-model="modes.quiz_mode_battle_group" @change="autoSave">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>

            <!-- Status Indicator -->
            <div class="form-actions-premium" style="padding: 1.5rem 2rem;">
                <div x-show="saving" class="status-indicator saving">
                    <i class="fas fa-spinner fa-spin"></i> Saving changes...
                </div>
                <div x-show="saved" x-transition class="status-indicator saved">
                    <i class="fas fa-check-circle"></i> Changes saved successfully!
                </div>
            </div>

            <!-- Preview Section -->
            <div class="creation-toolbar" style="margin-top: 2rem;">
                <h5 class="toolbar-title"><i class="fas fa-eye mr-2"></i> Dashboard Preview</h5>
            </div>
            <div class="preview-container">
                <p class="preview-subtitle">This is what users will see on the quiz dashboard:</p>
                <div class="preview-grid">
                    <div :class="modes.quiz_mode_zone ? \'preview-card visible\' : \'preview-card hidden\'" style="border-color: #3b82f6;">
                        <i class="fas fa-bullseye" style="color: #3b82f6;"></i>
                        <span>Quiz Zone</span>
                    </div>
                    <div :class="modes.quiz_mode_daily ? \'preview-card visible\' : \'preview-card hidden\'" style="border-color: #10b981;">
                        <i class="fas fa-calendar-day" style="color: #10b981;"></i>
                        <span>Daily Quiz</span>
                    </div>
                    <div :class="modes.quiz_mode_contest ? \'preview-card visible\' : \'preview-card hidden\'" style="border-color: #a855f7;">
                        <i class="fas fa-trophy" style="color: #a855f7;"></i>
                        <span>Contest Play</span>
                    </div>
                    <div :class="modes.quiz_mode_battle_1v1 ? \'preview-card visible\' : \'preview-card hidden\'" style="border-color: #ef4444;">
                        <i class="fas fa-swords" style="color: #ef4444;"></i>
                        <span>1 V/S 1 Battle</span>
                    </div>
                    <div :class="modes.quiz_mode_math ? \'preview-card visible\' : \'preview-card hidden\'" style="border-color: #f59e0b;">
                        <i class="fas fa-calculator" style="color: #f59e0b;"></i>
                        <span>Math Mania</span>
                    </div>
                    <div :class="modes.quiz_mode_exam ? \'preview-card visible\' : \'preview-card hidden\'" style="border-color: #8b5cf6;">
                        <i class="fas fa-file-alt" style="color: #8b5cf6;"></i>
                        <span>Exam Mode</span>
                    </div>
                    <div :class="modes.quiz_mode_guess_word ? \'preview-card visible\' : \'preview-card hidden\'" style="border-color: #ec4899;">
                        <i class="fas fa-spell-check" style="color: #ec4899;"></i>
                        <span>Guess Word</span>
                    </div>
                    <div :class="modes.quiz_mode_multi_match ? \'preview-card visible\' : \'preview-card hidden\'" style="border-color: #14b8a6;">
                        <i class="fas fa-puzzle-piece" style="color: #14b8a6;"></i>
                        <span>Multi Match</span>
                    </div>
                    <div :class="modes.quiz_mode_true_false ? \'preview-card visible\' : \'preview-card hidden\'" style="border-color: #06b6d4;">
                        <i class="fas fa-check-double" style="color: #06b6d4;"></i>
                        <span>True/False</span>
                    </div>
                    <div :class="modes.quiz_mode_battle_group ? \'preview-card visible\' : \'preview-card hidden\'" style="border-color: #f97316;">
                        <i class="fas fa-users" style="color: #f97316;"></i>
                        <span>Group Battle</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================
   PREMIUM CORE STYLES (MATCHING ECONOMY UI)
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-gray-50: #f8f9fa;
    --admin-gray-200: #e5e7eb;
    --admin-gray-300: #d1d5db;
    --admin-gray-400: #9ca3af;
    --admin-gray-600: #4b5563;
    --admin-gray-800: #1f2937;
}

.admin-wrapper-container {
    padding: 1rem;
    background: var(--admin-gray-50);
    min-height: calc(100vh - 70px);
}

.admin-content-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* Header */
.compact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
    color: white;
}
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.25rem; opacity: 0.9; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    display: flex; flex-direction: column; align-items: center;
    min-width: 80px;
}
.stat-pill .label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

/* Creation Toolbar */
.creation-toolbar {
    padding: 1rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid var(--admin-gray-200);
}
.toolbar-title {
    font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;
}

/* Modes Grid */
.modes-content { padding-bottom: 2rem; }
.modes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    padding: 2rem;
}

.mode-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.mode-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: var(--admin-primary);
}

.mode-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.mode-icon-wrapper i {
    font-size: 1.5rem;
    color: white;
}

.mode-info {
    flex: 1;
}

.mode-info h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
}

.mode-info p {
    margin: 0;
    font-size: 0.8rem;
    color: #64748b;
    line-height: 1.4;
}

/* Switch Toggle */
.switch { position: relative; display: inline-block; width: 48px; height: 26px; margin: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #4f46e5; }
input:checked + .slider:before { transform: translateX(22px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }

/* Status Indicators */
.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-indicator.saving {
    background: #fef3c7;
    color: #92400e;
}

.status-indicator.saved {
    background: #d1fae5;
    color: #065f46;
}

/* Preview Section */
.preview-container {
    padding: 2rem;
    background: #f8fafc;
}

.preview-subtitle {
    color: #64748b;
    font-size: 0.85rem;
    margin-bottom: 1.5rem;
}

.preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem;
}

.preview-card {
    padding: 1.5rem;
    border-radius: 8px;
    border: 2px solid;
    text-align: center;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    background: white;
}

.preview-card i {
    font-size: 1.5rem;
}

.preview-card span {
    font-size: 0.85rem;
    color: #1e293b;
}

.preview-card.visible {
    opacity: 1;
}

.preview-card.hidden {
    opacity: 0.3;
    filter: grayscale(100%);
}

.form-actions-premium {
    text-align: center;
}

@media (max-width: 768px) {
    .modes-grid {
        grid-template-columns: 1fr;
    }
    .preview-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function quizModesManager() {
    return {
        modes: ' . json_encode($modes ?? []) . ',
        saving: false,
        saved: false,
        
        autoSave() {
            this.saveSettings();
        },
        
        async saveSettings() {
            this.saving = true;
            this.saved = false;
            
            try {
                const response = await fetch(\'' . app_base_url('/admin/settings/quiz-modes') . '\', {
                    method: \'POST\',
                    headers: {
                        \'Content-Type\': \'application/json\',
                        \'X-CSRF-Token\': \'' . ($_SESSION['csrf_token'] ?? '') . '\'
                    },
                    body: JSON.stringify({
                        modes: this.modes,
                        csrf_token: \'' . ($_SESSION['csrf_token'] ?? '') . '\'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.saved = true;
                    setTimeout(() => this.saved = false, 3000);
                } else {
                    alert(\'Error: \' + data.message);
                }
            } catch (error) {
                alert(\'Network error: \' + error.message);
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
';

$breadcrumbs = [
    ['title' => 'Settings', 'url' => app_base_url('admin/settings')],
    ['title' => 'Quiz Modes']
];

$page_title = 'Quiz Modes Control - Admin Panel';
$currentPage = 'settings';

include __DIR__ . '/../../layouts/main.php';

