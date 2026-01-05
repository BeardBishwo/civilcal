<?php
/**
 * PREMIUM CREATE QUESTION UI - GAMENTA STYLE
 * Compact, Tabbed, and Modern
 */
?>

<!-- Load Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    /* Custom Scrollbar for sleek look */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    /* Tab Active State */
    .tab-btn.active {
        color: #4F46E5; /* Indigo-600 */
        background-color: #EEF2FF; /* Indigo-50 */
        border-bottom-color: #4F46E5;
    }
    
    /* Smooth Transitions */
    .transition-all-300 { transition: all 0.3s ease; }
</style>

<div class="admin-wrapper-container bg-slate-50 min-h-screen font-sans">
    <div class="admin-content-wrapper p-6 max-w-[1600px] mx-auto">

        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-indigo-100 text-indigo-600 w-10 h-10 rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-pen-nib"></i>
                    </span>
                    Create Question
                </h1>
                <p class="text-sm text-slate-500 mt-1 ml-14">Add a new question to your bank with precise categorization.</p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo app_base_url('admin/quiz/questions'); ?>" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-50 transition shadow-sm">
                    Cancel
                </a>
                <button type="submit" form="createQuestionForm" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Question
                </button>
            </div>
        </div>

        <form id="createQuestionForm" action="<?php echo app_base_url('admin/quiz/questions/store'); ?>" method="POST" class="flex flex-col lg:flex-row gap-8">
            
            <!-- LEFT COLUMN: Main Editor (65%) -->
            <div class="w-full lg:w-[65%] space-y-6">
                
                <!-- 1. Question Type Tabs & Content -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Tabs -->
                    <div class="flex border-b border-slate-100 overflow-x-auto no-scrollbar" id="typeTabs">
                        <input type="hidden" name="type" id="selectedType" value="MCQ">

                        <button type="button" class="tab-btn active flex-1 min-w-[120px] py-4 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 border-b-2 border-transparent transition-all" onclick="switchType('MCQ', this)">
                            <i class="fas fa-list-ul mb-1 block text-lg"></i> MCQ
                        </button>

                        <button type="button" class="tab-btn flex-1 min-w-[120px] py-4 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 border-b-2 border-transparent transition-all" onclick="switchType('TF', this)">
                            <i class="fas fa-check-circle mb-1 block text-lg"></i> True/False
                        </button>

                        <button type="button" class="tab-btn flex-1 min-w-[120px] py-4 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 border-b-2 border-transparent transition-all" onclick="switchType('MULTI', this)">
                            <i class="fas fa-check-double mb-1 block text-lg"></i> Multi-Select
                        </button>

                        <button type="button" class="tab-btn flex-1 min-w-[120px] py-4 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 border-b-2 border-transparent transition-all" onclick="switchType('ORDER', this)">
                            <i class="fas fa-sort-amount-down mb-1 block text-lg"></i> Sequence
                        </button>
                    </div>

                    <!-- Editor Area -->
                    <div class="p-6">
                        <div class="mb-3 flex justify-between items-center">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Question Stem</label>
                            <div class="flex gap-2">
                                <button type="button" class="text-xs font-bold text-indigo-600 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition" onclick="MediaManager.open('q_img')">
                                    <i class="fas fa-image mr-1"></i> Add Media
                                </button>
                                <button type="button" class="text-xs font-bold text-slate-600 hover:bg-slate-100 px-3 py-1.5 rounded-lg transition" onclick="toggleHint()">
                                    <i class="fas fa-lightbulb mr-1"></i> Add Hint
                                </button>
                            </div>
                        </div>
                        
                        <div class="relative group">
                            <textarea name="question_text" id="q_editor" class="w-full min-h-[140px] p-5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition resize-y text-slate-700 text-base leading-relaxed placeholder:text-slate-400" placeholder="Type your question here..." required></textarea>
                            
                            <!-- Simple Toolbar -->
                            <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity bg-white border border-slate-200 rounded-lg shadow-sm flex overflow-hidden">
                                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 text-xs w-8" title="Bold"><i class="fas fa-bold"></i></button>
                                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 text-xs w-8 border-l border-slate-100" title="Italic"><i class="fas fa-italic"></i></button>
                                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 text-xs w-8 border-l border-slate-100" title="Code"><i class="fas fa-code"></i></button>
                            </div>
                        </div>

                        <!-- Optional Hint Field -->
                        <div id="hint_field" class="mt-4 hidden animate__animated animate__fadeIn">
                            <label class="text-xs font-bold text-amber-500 uppercase tracking-wider mb-2 block">Answer Explanation</label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 text-amber-400"><i class="fas fa-info-circle"></i></div>
                                <textarea name="answer_explanation" class="w-full pl-10 p-3 bg-amber-50/50 border border-amber-200 rounded-xl text-sm text-slate-700 focus:border-amber-400 outline-none transition" rows="2" placeholder="Explain why the answer is correct..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Options Area -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">A</div>
                            <h3 class="text-lg font-bold text-slate-800">Answer Options</h3>
                        </div>
                        <div id="options_toolbar">
                            <button type="button" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-lg transition" onclick="addOption()">
                                <i class="fas fa-plus-circle mr-2"></i> Add Option
                            </button>
                        </div>
                    </div>
                    
                    <div id="options_container" class="space-y-4">
                        <!-- Options injected via JS -->
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Settings Sidebar (35%) -->
            <div class="w-full lg:w-[35%] space-y-6">
                
                <!-- Org Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-5">Categorization</h4>
                    
                    <div class="space-y-5">
                        
                        <!-- Course / Stream -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                <i class="fas fa-graduation-cap mr-1.5 text-indigo-400"></i> Course / Stream
                            </label>
                            <div class="relative">
                                <select name="stream" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition appearance-none">
                                    <?php if(!empty($streams)): ?>
                                        <?php foreach($streams as $val => $label): ?>
                                            <option value="<?= $val ?>"><?= $label ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Education Level -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                <i class="fas fa-layer-group mr-1.5 text-indigo-400"></i> Academic Level
                            </label>
                            <div class="relative">
                                <select name="education_level" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition appearance-none">
                                    <?php if(!empty($educationLevels)): ?>
                                        <?php foreach($educationLevels as $val => $label): ?>
                                            <option value="<?= $val ?>"><?= $label ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-chevron-down text-xs"></i></div>
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        <!-- Main Category (Subject) -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                <i class="fas fa-folder mr-1.5 text-indigo-400"></i> Subject (Main)
                            </label>
                            <div class="relative">
                                <select name="syllabus_main_id" id="main_cat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition appearance-none" onchange="filterSubTopics()">
                                    <?php if(!empty($mainCategories)): ?>
                                        <?php foreach($mainCategories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= (isset($last_main_id) && $last_main_id == $cat['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['title']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No subjects found</option>
                                    <?php endif; ?>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Sub Category (Topic) -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                <i class="fas fa-tag mr-1.5 text-indigo-400"></i> Topic (Sub)
                            </label>
                            <div class="relative">
                                <select name="syllabus_node_id" id="sub_cat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition appearance-none disabled:opacity-50 disabled:cursor-not-allowed" required>
                                    <option value="">-- Select Subject First --</option>
                                    <?php if(!empty($subCategories)): ?>
                                        <?php foreach($subCategories as $parentId => $nodes): ?>
                                            <?php foreach($nodes as $sub): ?>
                                                <option value="<?= $sub['id'] ?>" data-parent="<?= $parentId ?>" 
                                                    <?= (isset($last_sub_id) && $last_sub_id == $sub['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($sub['title']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Target Position Level Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 pl-2">Target Position Level</h4>
                    <p class="text-[10px] text-slate-400 mb-3 pl-2 italic">Select all exams this question is suitable for:</p>
                    
                    <div class="space-y-2 pl-2">
                        <?php if(!empty($pscLevels)): ?>
                            <?php foreach($pscLevels as $level): ?>
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="checkbox" name="level_tags[]" value="<?= $level['id'] ?>" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                <span class="text-sm text-slate-600 group-hover:text-indigo-600 transition font-medium"><?= htmlspecialchars($level['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Configuration Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-5">Settings</h4>
                    
                    <!-- Difficulty Slider -->
                    <div class="mb-6">
                        <div class="flex justify-between mb-3">
                            <label class="text-sm font-bold text-slate-700">Difficulty</label>
                            <span id="diff_label" class="text-xs font-bold px-2.5 py-1 rounded-md bg-yellow-100 text-yellow-700 border border-yellow-200">Medium</span>
                        </div>
                        <input type="range" name="difficulty_level" min="1" max="5" value="3" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="updateDiffLabel(this.value)">
                        <div class="flex justify-between text-[10px] font-bold text-slate-400 mt-2">
                            <span>Easy</span>
                            <span>Expert</span>
                        </div>
                    </div>

                    <!-- Marks Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Marks</label>
                            <div class="relative">
                                <input type="number" name="default_marks" value="1.0" step="0.1" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-sm font-bold text-slate-700 focus:border-indigo-500 outline-none">
                            </div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Neg. Marks</label>
                            <div class="relative">
                                <input type="number" name="default_negative_marks" value="0.2" step="0.01" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-sm font-bold text-red-600 focus:border-red-500 outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="space-y-3">
                        <label class="flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer transition group">
                            <span class="text-sm font-semibold text-slate-700 group-hover:text-indigo-600 transition">Active Status</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </div>
                        </label>
                        
                        <label class="flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer transition group">
                            <span class="text-sm font-semibold text-slate-700 group-hover:text-indigo-600 transition">Shuffle Options</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="shuffle_options" value="1" checked class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </div>
                        </label>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Config
    const state = {
        type: 'MCQ',
        optionCount: 4
    };

    // UI Templates
    const UI = {
        renderOptionRow: (idx, type) => {
            const letter = String.fromCharCode(65 + idx); // A, B, C...
            let inputControl = '';
            
            // Logic for Correct Answer Selection
            if (type === 'MCQ') {
                inputControl = `<input type="radio" name="correct_answer_dummy" class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500 cursor-pointer" 
                                onclick="setSingleCorrect(${idx})" required>`;
                inputControl += `<input type="hidden" name="options[${idx}][is_correct]" class="is_correct_val" value="0">`;
            } else if (type === 'TF') {
               // Handled separately
            } else if (type === 'MULTI') {
                inputControl = `<input type="checkbox" name="options[${idx}][is_correct]" value="1" class="w-5 h-5 text-amber-600 rounded border-gray-300 focus:ring-amber-500 cursor-pointer">`;
            } else if (type === 'ORDER') {
                inputControl = `<div class="handle cursor-grab text-slate-300 hover:text-slate-500"><i class="fas fa-grip-vertical"></i></div>
                                <input type="hidden" name="options[${idx}][is_correct]" value="1">`; // Sequence uses order index automatically
            }

            return `
            <div class="option-row group flex items-start gap-3 animate__animated animate__fadeIn mb-3">
                <div class="pt-3">
                    ${inputControl}
                </div>
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-bold text-sm">${letter}.</span>
                        </div>
                        <input type="text" name="options[${idx}][text]" 
                               class="w-full pl-8 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition placeholder:text-slate-400"
                               placeholder="Enter option text..." required>
                        
                        <!-- Actions -->
                        <div class="absolute right-2 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1 bg-white rounded-lg shadow-sm border border-slate-100">
                            <button type="button" class="p-1.5 text-slate-400 hover:text-indigo-500 transition" title="Add Image"><i class="fas fa-image"></i></button>
                            ${state.optionCount > 2 ? `<button type="button" class="p-1.5 text-slate-400 hover:text-red-500 transition border-l border-slate-100" onclick="removeOption(this)"><i class="fas fa-times"></i></button>` : ''}
                        </div>
                    </div>
                </div>
            </div>`;
        }
    };

    // Logic
    function switchType(newType, btn) {
        state.type = newType;
        document.getElementById('selectedType').value = newType;
        
        // Tab Styling
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active', 'text-indigo-600', 'bg-indigo-50', 'border-indigo-600');
            b.classList.add('text-slate-500', 'border-transparent');
        });
        if(btn) {
            btn.classList.remove('text-slate-500', 'border-transparent');
            btn.classList.add('active', 'text-indigo-600', 'bg-indigo-50', 'border-indigo-600');
        }

        const container = document.getElementById('options_container');
        const toolbar = document.getElementById('options_toolbar');
        container.innerHTML = '';

        if (newType === 'TF') {
            // True/False Special Layout
            container.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <label class="cursor-pointer relative group">
                        <input type="radio" name="tf_selection" value="1" class="peer sr-only" onchange="setTFCorrect(0)">
                        <div class="p-6 rounded-2xl border-2 border-slate-200 hover:border-emerald-200 bg-slate-50 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 text-center transition-all group-hover:shadow-md">
                            <div class="text-emerald-600 font-bold text-xl"><i class="fas fa-check-circle mb-2 block text-3xl"></i> TRUE</div>
                        </div>
                        <input type="hidden" name="options[0][text]" value="True">
                        <input type="hidden" name="options[0][is_correct]" id="tf_0_val" value="0">
                    </label>
                    <label class="cursor-pointer relative group">
                        <input type="radio" name="tf_selection" value="0" class="peer sr-only" onchange="setTFCorrect(1)">
                        <div class="p-6 rounded-2xl border-2 border-slate-200 hover:border-red-200 bg-slate-50 peer-checked:bg-red-50 peer-checked:border-red-500 text-center transition-all group-hover:shadow-md">
                            <div class="text-red-600 font-bold text-xl"><i class="fas fa-times-circle mb-2 block text-3xl"></i> FALSE</div>
                        </div>
                        <input type="hidden" name="options[1][text]" value="False">
                        <input type="hidden" name="options[1][is_correct]" id="tf_1_val" value="0">
                    </label>
                </div>
            `;
            toolbar.style.display = 'none'; 
        } else {
            // Standard Options List
            state.optionCount = 4;
            renderOptionsList();
            toolbar.style.display = 'block';
        }

        // Setup Drag for Order
        if (newType === 'ORDER') {
            new Sortable(container, { handle: '.handle', animation: 150 });
        }
    }

    function setSingleCorrect(selectedIndex) {
        document.querySelectorAll('.is_correct_val').forEach((el, idx) => {
            el.value = (idx === selectedIndex) ? '1' : '0';
        });
    }

    function setTFCorrect(idx) {
        document.getElementById('tf_0_val').value = (idx === 0) ? '1' : '0';
        document.getElementById('tf_1_val').value = (idx === 1) ? '1' : '0';
    }

    function renderOptionsList() {
        const container = document.getElementById('options_container');
        let html = '';
        for (let i = 0; i < state.optionCount; i++) {
            html += UI.renderOptionRow(i, state.type);
        }
        container.innerHTML = html;
    }

    function addOption() {
        state.optionCount++;
        const container = document.getElementById('options_container');
        const div = document.createElement('div');
        div.innerHTML = UI.renderOptionRow(state.optionCount - 1, state.type);
        container.appendChild(div.firstElementChild); // Extract from wrapper
    }

    function removeOption(btn) {
        if (state.optionCount <= 2) return;
        btn.closest('.option-row').remove();
        state.optionCount--;
        renderOptionsList(); 
    }

    function updateDiffLabel(val) {
        const map = {1: ['Easy', 'bg-green-100 text-green-700'], 2: ['Easy-Med', 'bg-lime-100 text-lime-700'], 3: ['Medium', 'bg-yellow-100 text-yellow-700'], 4: ['Hard', 'bg-orange-100 text-orange-700'], 5: ['Expert', 'bg-red-100 text-red-700']};
        const el = document.getElementById('diff_label');
        el.innerText = map[val][0];
        el.className = `text-xs font-bold px-2.5 py-1 rounded-md ${map[val][1]}`;
    }

    function filterSubTopics() {
        const mainId = document.getElementById('main_cat').value;
        const subSelect = document.getElementById('sub_cat');
        const subs = document.querySelectorAll('#sub_cat option');
        let hasVisible = false;
        
        subs.forEach(opt => {
            if (opt.value === "") return;
            const parent = opt.getAttribute('data-parent');
            
            if (parent == mainId || !mainId) { 
                opt.style.display = 'block';
                if (!hasVisible) hasVisible = true; 
            } else {
                opt.style.display = 'none';
            }
        });
        
        subSelect.value = ""; // Reset sub selection on main change
        subSelect.disabled = !hasVisible; // Disable if no subs
    }

    function toggleHint() {
        document.getElementById('hint_field').classList.toggle('hidden');
    }

    // Init
    document.addEventListener('DOMContentLoaded', () => {
        // Initial Tab Selection Styling
        const firstBtn = document.querySelector('#typeTabs button');
        switchType('MCQ', firstBtn);
        
        // Trigger filter init
        filterSubTopics();
    });
</script>