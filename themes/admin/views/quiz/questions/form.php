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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<!-- TinyMCE Rich Text Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<style>
    /* Custom Scrollbar for sleek look */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Tab Active State */
    .tab-btn.active {
        color: #4F46E5;
        /* Indigo-600 */
        background-color: #EEF2FF;
        /* Indigo-50 */
        border-bottom-color: #4F46E5;
    }

    /* Smooth Transitions */
    .transition-all-300 {
        transition: all 0.3s ease;
    }
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
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

            <!-- LEFT COLUMN: Main Editor (65%) -->
            <div class="w-full lg:w-[65%] space-y-6">

                <?php if (!empty($report_count) && $report_count > 0): ?>
                    <!-- Shadow Fix Warning Alert (Phase 5) -->
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-red-200 rounded-2xl p-5 shadow-sm animate__animated animate__pulse">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-red-800">Shadow-Fix Warning!</h4>
                                <p class="text-xs text-red-600 mt-1">This question has <strong><?php echo $report_count; ?> pending reports</strong>. Fixing it here without resolving the reports will ignore the users' efforts.</p>

                                <div class="mt-4 flex items-center gap-4 p-3 bg-white/50 rounded-xl border border-red-100">
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <div class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="resolve_reports" value="1" checked class="sr-only peer">
                                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 group-hover:text-emerald-600 transition">Auto-Resolve & Reward Reporters on Save</span>
                                    </label>
                                </div>
                                <div class="mt-2 pl-1">
                                    <input type="text" name="resolve_message" placeholder="Optional personal message (e.g. 'Fixed the typo!')"
                                        class="w-full px-3 py-2 text-[11px] bg-white border border-red-100 rounded-lg focus:border-red-400 outline-none transition">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 1. Question Type Tabs & Content -->
                <div id="card_main" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all duration-300">
                    <!-- Tabs Header with Pin -->
                    <div class="flex items-center justify-between border-b border-slate-100 bg-white relative z-10">
                        <div class="flex overflow-x-auto no-scrollbar flex-1" id="typeTabs">
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

                            <button type="button" class="tab-btn flex-1 min-w-[120px] py-4 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 border-b-2 border-transparent transition-all" onclick="switchType('THEORY', this)">
                                <i class="fas fa-file-alt mb-1 block text-lg"></i> Theory
                            </button>
                        </div>
                        <div class="px-3 border-l border-slate-100">
                            <button type="button" id="pin_btn_card_main" onclick="togglePin('card_main')" class="text-slate-300 hover:text-slate-500 transition p-2" title="Pin Section">
                                <i class="fas fa-thumbtack"></i>
                            </button>
                        </div>
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
                            <textarea name="question_text" id="q_editor" class="w-full min-h-[140px] p-5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition resize-y text-slate-700 text-base leading-relaxed placeholder:text-slate-400" placeholder="Type your question here..." required><?php echo $question ? htmlspecialchars($question['content']['text']) : ''; ?></textarea>

                            <!-- Simple Toolbar -->
                            <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity bg-white border border-slate-200 rounded-lg shadow-sm flex overflow-hidden">
                                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 text-xs w-8" title="Bold"><i class="fas fa-bold"></i></button>
                                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 text-xs w-8 border-l border-slate-100" title="Italic"><i class="fas fa-italic"></i></button>
                                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 text-xs w-8 border-l border-slate-100" title="Code"><i class="fas fa-code"></i></button>
                            </div>
                        </div>

                        <!-- Optional Hint Field -->
                        <div id="hint_field" class="mt-4 hidden animate__animated animate__fadeIn border-t border-dashed border-slate-200 pt-4">
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-xs font-bold text-amber-500 uppercase tracking-wider">Answer Explanation</label>
                                <button type="button" id="pin_btn_hint" onclick="toggleHintPin()" class="text-slate-300 hover:text-amber-500 transition p-1" title="Pin Explanation (Keep Open)">
                                    <i class="fas fa-thumbtack"></i>
                                </button>
                            </div>
                            <div class="relative">
                                <div class="absolute top-3 left-3 text-amber-400"><i class="fas fa-info-circle"></i></div>
                                <textarea name="answer_explanation" class="w-full pl-10 p-3 bg-amber-50/50 border border-amber-200 rounded-xl text-sm text-slate-700 focus:border-amber-400 outline-none transition" rows="2" placeholder="Explain why the answer is correct..."><?php echo $question ? htmlspecialchars($question['answer_explanation']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Options Area -->
                <div id="card_opts" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 transition-all duration-300">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">A</div>
                            <h3 class="text-lg font-bold text-slate-800">Answer Options</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <div id="pin_wrapper_card_opts">
                                <button type="button" id="pin_btn_card_opts" onclick="togglePin('card_opts')" class="text-slate-300 hover:text-slate-500 transition p-2 mr-2" title="Pin Section">
                                    <i class="fas fa-thumbtack"></i>
                                </button>
                            </div>
                            <div id="options_toolbar" class="flex items-center gap-2">
                                <button type="button" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-lg transition" onclick="addOption()">
                                    <i class="fas fa-plus-circle mr-2"></i> Add Option
                                </button>
                            </div>
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
                <div id="card_org" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 p-3 z-10">
                        <button type="button" id="pin_btn_card_org" onclick="togglePin('card_org')" class="text-slate-300 hover:text-slate-500 transition p-1" title="Pin Section">
                            <i class="fas fa-thumbtack"></i>
                        </button>
                    </div>
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-5">Syllabus Associations</h4>

                    <div id="syllabus-associations-container" class="space-y-4">
                        <!-- Repeater rows will be added here -->
                    </div>

                    <button type="button" onclick="addSyllabusMapping()" class="w-full mt-4 px-4 py-3 bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-dashed border-indigo-200 text-indigo-600 rounded-xl text-sm font-bold hover:from-indigo-100 hover:to-purple-100 hover:border-indigo-300 transition flex items-center justify-center gap-2">
                        <i class="fas fa-plus-circle"></i> Add Syllabus Mapping
                    </button>

                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-blue-700 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span>Link this question to multiple courses/levels. Higher priority mappings are preferred during exam generation.</span>
                        </p>
                    </div>
                </div>

                <!-- Target Position Level Card -->
                <div id="card_target" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 p-3 z-10">
                        <button type="button" id="pin_btn_card_target" onclick="togglePin('card_target')" class="text-slate-300 hover:text-slate-500 transition p-1" title="Pin Section">
                            <i class="fas fa-thumbtack"></i>
                        </button>
                    </div>
                    <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                    <h3 class="font-bold text-slate-700 mb-2 flex items-center">
                        <i class="fas fa-crosshairs text-indigo-500 mr-2"></i> Target Position Level
                    </h3>

                    <!-- Search Input -->
                    <div class="relative mb-4">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                        <input type="text" id="target_pos_search" placeholder="Search position levels..."
                            class="w-full pl-9 pr-3 py-2 text-[11px] bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all duration-200">
                    </div>

                    <div id="pos_level_list" class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                        <?php if (!empty($positionLevels)): ?>
                            <?php foreach ($positionLevels as $level): ?>
                                <label class="pos-level-label flex items-center p-3 rounded-xl border border-slate-100 cursor-pointer transition-all duration-200 hover:border-indigo-200 hover:bg-indigo-50/30 group relative">
                                    <input type="checkbox" name="position_levels[]" value="<?php echo $level['id']; ?>" class="hidden peer">
                                    <div class="flex-1">
                                        <span class="text-[11px] font-bold text-slate-700 group-hover:text-indigo-600 transition-colors block leading-tight"><?php echo htmlspecialchars($level['title']); ?></span>
                                        <span class="text-[9px] text-slate-400 block mt-0.5">Level <?php echo $level['level_number']; ?></span>
                                    </div>
                                    <div class="peer-checked:flex hidden w-5 h-5 items-center justify-center rounded-full bg-indigo-600 text-white shadow-sm shadow-indigo-100 transition-all scale-0 peer-checked:scale-100 absolute right-2 top-1/2 -translate-y-1/2">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-xs text-slate-400 col-span-2">No position levels found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Configuration Card -->
                <div id="card_settings" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 transition-all duration-300 relative">
                    <div class="absolute top-0 right-0 p-3 z-10">
                        <button type="button" id="pin_btn_card_settings" onclick="togglePin('card_settings')" class="text-slate-300 hover:text-slate-500 transition p-1" title="Pin Section">
                            <i class="fas fa-thumbtack"></i>
                        </button>
                    </div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-5">Settings</h4>

                    <!-- Question Status -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-shield-check text-indigo-500"></i> Governance Status
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer group">
                                <input type="radio" name="status" value="draft" <?php echo ($question && $question['status'] === 'draft') ? 'checked' : ''; ?> class="peer sr-only">
                                <div class="py-2.5 px-1 rounded-xl border border-slate-200 bg-slate-50 text-center peer-checked:bg-amber-50 peer-checked:border-amber-500 peer-checked:text-amber-700 transition shadow-sm group-hover:bg-slate-100">
                                    <span class="text-[10px] font-extrabold uppercase tracking-tighter">Draft</span>
                                </div>
                            </label>
                            <label class="cursor-pointer group">
                                <input type="radio" name="status" value="approved" <?php echo (!$question || $question['status'] === 'approved') ? 'checked' : ''; ?> class="peer sr-only">
                                <div class="py-2.5 px-1 rounded-xl border border-slate-200 bg-slate-50 text-center peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 transition shadow-sm group-hover:bg-slate-100">
                                    <span class="text-[10px] font-extrabold uppercase tracking-tighter">Approved</span>
                                </div>
                            </label>
                            <label class="cursor-pointer group">
                                <input type="radio" name="status" value="archived" <?php echo ($question && $question['status'] === 'archived') ? 'checked' : ''; ?> class="peer sr-only">
                                <div class="py-2.5 px-1 rounded-xl border border-slate-200 bg-slate-50 text-center peer-checked:bg-slate-100 peer-checked:border-slate-500 peer-checked:text-slate-700 transition shadow-sm group-hover:bg-slate-100">
                                    <span class="text-[10px] font-extrabold uppercase tracking-tighter">Archive</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Difficulty Slider -->
                    <div class="mb-6">
                        <div class="flex justify-between mb-3">
                            <label class="text-sm font-bold text-slate-700">Difficulty</label>
                            <span id="diff_label" class="text-xs font-bold px-2.5 py-1 rounded-md bg-yellow-100 text-yellow-700 border border-yellow-200">Medium</span>
                        </div>
                        <input type="range" name="difficulty_level" min="1" max="5" value="<?php echo $question['difficulty_level'] ?? 3; ?>" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="updateDiffLabel(this.value)">
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
                                <input type="number" name="default_marks" value="<?php echo $question['default_marks'] ?? 1.0; ?>" step="0.1" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-sm font-bold text-slate-700 focus:border-indigo-500 outline-none">
                            </div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Neg. Marks</label>
                            <div class="relative">
                                <input type="number" name="default_negative_marks" value="<?php echo $question['default_negative_marks'] ?? 0.2; ?>" step="0.01" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-sm font-bold text-red-600 focus:border-red-500 outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="space-y-3">
                        <label class="flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer transition group">
                            <span class="text-sm font-semibold text-slate-700 group-hover:text-indigo-600 transition">Active Status</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="active_status_toggle" name="is_active" value="1" <?php echo (!$question || $question['is_active']) ? 'checked' : ''; ?> class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </div>
                        </label>

                        <label class="flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer transition group">
                            <span class="text-sm font-semibold text-slate-700 group-hover:text-indigo-600 transition">Shuffle Options</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="shuffle_options_toggle" name="shuffle_options" value="1" checked class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </div>
                        </label>

                        <!-- Publish to Blog Toggle -->
                        <label class="flex items-center justify-between p-3 border border-amber-100 rounded-xl hover:bg-amber-50 cursor-pointer transition group bg-gradient-to-r from-amber-50/50 to-orange-50/50">
                            <div>
                                <span class="text-sm font-semibold text-slate-700 group-hover:text-amber-600 transition flex items-center gap-2">
                                    <i class="fas fa-blog text-amber-500"></i> Publish as Blog Post
                                </span>
                                <div class="text-xs text-slate-500 mt-0.5">Make publicly accessible for SEO</div>
                            </div>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="publish_blog_toggle" name="is_published_as_blog" value="1" class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-600"></div>
                            </div>
                        </label>

                        <!-- URL Preview (shown when toggle is ON) -->
                        <div id="blog_url_preview_box" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-xl animate__animated animate__fadeIn">
                            <div class="text-xs font-bold text-blue-800 mb-1 flex items-center gap-2">
                                <i class="fas fa-link"></i> Blog URL Preview:
                            </div>
                            <div id="blog_url_text" class="text-xs text-blue-600 font-mono break-all bg-white p-2 rounded border border-blue-100"></div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Select2 Customization to match Tailwind */
    .select2-container .select2-selection--single {
        height: 42px !important;
        background-color: #f8fafc !important;
        /* bg-slate-50 */
        border-color: #e2e8f0 !important;
        /* border-slate-200 */
        border-radius: 0.5rem !important;
        /* rounded-lg */
        padding-top: 6px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }

    .select2-dropdown {
        border-color: #e2e8f0 !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    }

    .select2-search__field {
        border-radius: 0.375rem !important;
        /* rounded-md */
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<script>
    // Config
    const state = {
        type: 'MCQ',
        optionCount: 4,
        pinnedId: null,
        hintPinned: false
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

    // Unified Data Init
    const coursesData = <?php echo json_encode($courses); ?>;
    const eduLevelsData = <?php echo json_encode($educationLevels); ?>;
    const subCategories = <?php echo json_encode($subCategories); ?>;
    const totalPositionLevels = <?php echo json_encode($positionLevels); ?>;


    // Logic
    function switchType(newType, btn) {
        state.type = newType;
        document.getElementById('selectedType').value = newType;
        localStorage.setItem('q_form_type', newType); // Persist Type

        // Tab Styling
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active', 'text-indigo-600', 'bg-indigo-50', 'border-indigo-600');
            b.classList.add('text-slate-500', 'border-transparent');

            // If manual button passed
            if (btn && b === btn) {
                b.classList.remove('text-slate-500', 'border-transparent');
                b.classList.add('active', 'text-indigo-600', 'bg-indigo-50', 'border-indigo-600');
            }
            // If restored from storage (no btn passed), match loosely
            else if (!btn && b.getAttribute('onclick').includes(`'${newType}'`)) {
                b.classList.remove('text-slate-500', 'border-transparent');
                b.classList.add('active', 'text-indigo-600', 'bg-indigo-50', 'border-indigo-600');
            }
        });

        const container = document.getElementById('options_container');
        const optsToolbar = document.getElementById('options_toolbar');

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
            if (optsToolbar) optsToolbar.style.display = 'none';
        } else if (newType === 'THEORY') {
            // Theory Question Layout
            container.innerHTML = `
                <div class="space-y-6">
                    <!-- Theory Type Selector -->
                    <div class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-6">
                        <label class="block text-sm font-bold text-amber-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-graduation-cap"></i> Theory Question Type
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer group">
                                <input type="radio" name="theory_type" value="short" checked class="peer sr-only" onchange="updateTheoryMarks('short')">
                                <div class="p-5 rounded-xl border-2 border-amber-200 bg-white peer-checked:bg-amber-100 peer-checked:border-amber-500 transition-all group-hover:shadow-md">
                                    <div class="text-center">
                                        <i class="fas fa-file-alt text-3xl text-amber-600 mb-2"></i>
                                        <div class="font-bold text-slate-800">Short Answer</div>
                                        <div class="text-xs text-slate-500 mt-1">4 Marks • 100-150 words</div>
                                    </div>
                                </div>
                            </label>
                            <label class="cursor-pointer group">
                                <input type="radio" name="theory_type" value="long" class="peer sr-only" onchange="updateTheoryMarks('long')">
                                <div class="p-5 rounded-xl border-2 border-amber-200 bg-white peer-checked:bg-amber-100 peer-checked:border-amber-500 transition-all group-hover:shadow-md">
                                    <div class="text-center">
                                        <i class="fas fa-file-alt text-3xl text-amber-600 mb-2"></i>
                                        <div class="font-bold text-slate-800">Long Answer</div>
                                        <div class="text-xs text-slate-500 mt-1">8 Marks • 300-500 words</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Model Answer / Marking Scheme -->
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
                        <label class="block text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-book-open text-emerald-600"></i> Model Answer & Marking Scheme
                        </label>
                        <textarea id="theory_model_answer" name="model_answer" rows="10" class="w-full p-4 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition" placeholder="Provide the complete model answer with point-wise marking scheme..."></textarea>
                        <div class="mt-2 text-xs text-slate-500 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span>Include the complete answer with point-wise marks distribution. This will be used for manual grading.</span>
                        </div>
                    </div>

                    <!-- Expected Answer Guidelines -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-lightbulb text-blue-600 text-lg mt-0.5"></i>
                            <div class="text-xs text-blue-800">
                                <div class="font-bold mb-1">Tips for Theory Questions:</div>
                                <ul class="list-disc list-inside space-y-1 ml-2">
                                    <li>Specify expected answer length in question text</li>
                                    <li>Break down marking scheme into clear points</li>
                                    <li>Include all acceptable answer variations</li>
                                    <li>Mention if diagrams/sketches are required</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            if (optsToolbar) optsToolbar.style.display = 'none';

            // Initialize TinyMCE for model answer
            setTimeout(() => {
                if (typeof tinymce !== 'undefined') {
                    tinymce.init({
                        selector: '#theory_model_answer',
                        height: 400,
                        menubar: false,
                        plugins: 'lists link image code',
                        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
                        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; }'
                    });
                }
            }, 100);
        } else {
            // Standard Options List
            state.optionCount = 4;
            renderOptionsList();
            if (optsToolbar) optsToolbar.style.display = 'flex';
        }

        // Ensure Pin Button remains visible
        const pinWrapper = document.getElementById('pin_wrapper_card_opts');
        if (pinWrapper) pinWrapper.style.display = 'block';

        // Setup Drag for Order
        if (newType === 'ORDER') {
            new Sortable(container, {
                handle: '.handle',
                animation: 150
            });
        }
    }

    function togglePin(id) {
        const el = document.getElementById(id);
        const btn = document.getElementById('pin_btn_' + id);
        if (!el || !btn) return;

        let pins = JSON.parse(localStorage.getItem('q_form_pinned_ids') || '[]');
        const isPinned = pins.includes(id);

        if (isPinned) {
            // Unpin
            el.classList.remove('sticky', 'top-4', 'z-40', 'ring-2', 'ring-indigo-500/20', 'w-full');
            btn.classList.remove('text-indigo-600', '-rotate-45');
            btn.classList.add('text-slate-300');
            pins = pins.filter(pid => pid !== id);
        } else {
            // Pin
            el.classList.add('sticky', 'top-4', 'z-40', 'ring-2', 'ring-indigo-500/20', 'w-full');
            btn.classList.add('text-indigo-600', '-rotate-45');
            btn.classList.remove('text-slate-300');
            if (!pins.includes(id)) pins.push(id);
        }
        localStorage.setItem('q_form_pinned_ids', JSON.stringify(pins));
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
        reindexOptions();
    }

    function reindexOptions() {
        const rows = document.querySelectorAll('.option-row');
        rows.forEach((row, index) => {
            // Update Label (A, B, C...)
            const letter = String.fromCharCode(65 + index);
            const label = row.querySelector('.text-slate-400.font-bold.text-sm');
            if (label) label.innerText = letter + '.';

            // Update Text Input Name
            const textInput = row.querySelector('input[type="text"]');
            if (textInput) textInput.name = `options[${index}][text]`;

            // Update Hidden Correct Value
            const hiddenCorrect = row.querySelector('input[type="hidden"].is_correct_val');
            if (hiddenCorrect) hiddenCorrect.name = `options[${index}][is_correct]`;

            // Update Radio Button (MCQ) - Reset onclick index
            const radio = row.querySelector('input[type="radio"][onclick^="setSingleCorrect"]');
            if (radio) {
                radio.setAttribute('onclick', `setSingleCorrect(${index})`);
            }

            // Update Checkbox (Multi)
            const checkbox = row.querySelector('input[type="checkbox"][name^="options"]');
            if (checkbox && checkbox.name.includes('[is_correct]')) {
                checkbox.name = `options[${index}][is_correct]`;
            }
        });
        state.optionCount = rows.length;
    }

    function updateDiffLabel(val) {
        const map = {
            1: ['Easy', 'bg-green-100 text-green-700'],
            2: ['Easy-Med', 'bg-lime-100 text-lime-700'],
            3: ['Medium', 'bg-yellow-100 text-yellow-700'],
            4: ['Hard', 'bg-orange-100 text-orange-700'],
            5: ['Expert', 'bg-red-100 text-red-700']
        };
        const el = document.getElementById('diff_label');
        el.innerText = map[val][0];
        el.className = `text-xs font-bold px-2.5 py-1 rounded-md ${map[val][1]}`;
    }

    function updateTheoryMarks(type) {
        const marksInput = document.querySelector('input[name="default_marks"]');
        const negMarksInput = document.querySelector('input[name="default_negative_marks"]');

        if (type === 'short') {
            if (marksInput) marksInput.value = 4;
            if (negMarksInput) negMarksInput.value = 0;
        } else if (type === 'long') {
            if (marksInput) marksInput.value = 8;
            if (negMarksInput) negMarksInput.value = 0;
        }
    }


    function toggleHint() {
        const hintEl = document.getElementById('hint_field');
        if (state.hintPinned) {
            // If pinned, we only allow un-hiding if it somehow got hidden, but we should not hide it.
            // Actually, if pinned, clicking "Add Hint" shouldn't toggle it to hidden.
            // We just ensure it's visible.
            hintEl.classList.remove('hidden');
        } else {
            // Normal toggle behavior
            hintEl.classList.toggle('hidden');
        }
    }

    function toggleHintPin() {
        const btn = document.getElementById('pin_btn_hint');
        const hintEl = document.getElementById('hint_field');

        if (state.hintPinned) {
            // Unpin
            state.hintPinned = false;
            btn.classList.remove('text-amber-500', '-rotate-45');
            btn.classList.add('text-slate-300');
            localStorage.removeItem('q_hint_pinned');
        } else {
            // Pin
            state.hintPinned = true;
            btn.classList.add('text-amber-500', '-rotate-45');
            btn.classList.remove('text-slate-300');
            // Ensure visible
            hintEl.classList.remove('hidden');
            localStorage.setItem('q_hint_pinned', '1');
        }
    }

    function persistData(key, val) {
        saveFormState();
    }

    function saveFormState() {
        const data = {
            stream: document.getElementById('stream_select')?.value,
            level: document.getElementById('level_select')?.value,
            main_cat: document.getElementById('main_cat')?.value,
            sub_cat: document.getElementById('sub_cat')?.value,
            diff: document.querySelector('input[name="difficulty_level"]')?.value,
            marks: document.querySelector('input[name="default_marks"]')?.value,
            neg: document.querySelector('input[name="default_negative_marks"]')?.value,
            active: document.getElementById('active_status_toggle')?.checked,
            shuffle: document.getElementById('shuffle_options_toggle')?.checked,
            targets: Array.from(document.querySelectorAll('.target-level-checkbox')).map(cb => ({
                id: cb.value,
                checked: cb.checked
            }))
        };
        localStorage.setItem('q_form_data_v3', JSON.stringify(data));
    }

    function restoreFormState() {
        const raw = localStorage.getItem('q_form_data_v3');
        if (!raw) return;
        try {
            const data = JSON.parse(raw);
            if (data.stream) document.getElementById('stream_select').value = data.stream;
            if (data.level) document.getElementById('level_select').value = data.level;
            if (data.main_cat) {
                document.getElementById('main_cat').value = data.main_cat;
                if (typeof filterSubTopics === 'function') filterSubTopics();
                if (data.sub_cat) {
                    setTimeout(() => {
                        const sc = document.getElementById('sub_cat');
                        if (sc) sc.value = data.sub_cat;
                    }, 200);
                }
            }
            if (data.diff) {
                const el = document.querySelector('input[name="difficulty_level"]');
                if (el) {
                    el.value = data.diff;
                    if (typeof updateDiffLabel === 'function') updateDiffLabel(data.diff);
                }
            }
            const marksEl = document.querySelector('input[name="default_marks"]');
            if (data.marks && marksEl) marksEl.value = data.marks;

            const negEl = document.querySelector('input[name="default_negative_marks"]');
            if (data.neg && negEl) negEl.value = data.neg;

            const activeEl = document.getElementById('active_status_toggle');
            if (data.active !== undefined && activeEl) activeEl.checked = data.active;

            const shuffleEl = document.getElementById('shuffle_options_toggle');
            if (data.shuffle !== undefined && shuffleEl) shuffleEl.checked = data.shuffle;

            if (data.targets) {
                data.targets.forEach(t => {
                    const cb = document.querySelector('.target-level-checkbox[value="' + t.id + '"]');
                    if (cb) cb.checked = t.checked;
                });
            }
        } catch (e) {
            console.error("Restore failed", e);
        }
    }

    function togglePin(id) {
        const el = document.getElementById(id);
        const btn = document.getElementById('pin_btn_' + id);
        if (!el || !btn) return;

        let pins = JSON.parse(localStorage.getItem('q_form_pinned_ids') || '[]');
        const isPinned = pins.includes(id);

        if (isPinned) {
            // Unpin
            el.classList.remove('sticky', 'top-4', 'z-40', 'ring-2', 'ring-indigo-500/20', 'w-full');
            btn.classList.remove('text-indigo-600', '-rotate-45');
            btn.classList.add('text-slate-300');
            pins = pins.filter(pid => pid !== id);
        } else {
            // Pin
            el.classList.add('sticky', 'top-4', 'z-40', 'ring-2', 'ring-indigo-500/20', 'w-full');
            btn.classList.add('text-indigo-600', '-rotate-45');
            btn.classList.remove('text-slate-300');
            if (!pins.includes(id)) pins.push(id);
        }
        localStorage.setItem('q_form_pinned_ids', JSON.stringify(pins));
    }

    // === MULTI-SYLLABUS MAPPER ===
    let mappingIndex = 0;

    function addSyllabusMapping() {
        const container = document.getElementById('syllabus-associations-container');
        if (!container) return;
        const idx = mappingIndex++;

        const row = document.createElement('div');
        row.className = 'mapping-row p-4 bg-slate-50 border border-slate-200 rounded-xl animate__animated animate__fadeIn mb-4';
        row.dataset.index = idx;

        row.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px] font-bold">
                        ${idx + 1}
                    </div>
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Mapping Context</span>
                    ${idx === 0 ? '<span class="bg-indigo-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-sm">PRIMARY</span>' : ''}
                </div>
                <button type="button" onclick="removeSyllabusMapping(${idx})" class="w-6 h-6 flex items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500 transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div class="space-y-1">
                    <label class="flex items-center gap-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-tight ml-1">
                        <i class="fas fa-graduation-cap text-indigo-400"></i> Course
                    </label>
                    <select name="mappings[${idx}][course_id]" class="mapping-course w-full bg-white border border-slate-200 text-slate-700 text-xs rounded-lg p-2 focus:ring-4 focus:ring-indigo-500/10 transition" onchange="updateMappingCascade(${idx}, 'course')">
                        <option value="">Select Course</option>
                        ${coursesData.map(c => `<option value="${c.id}">${c.title}</option>`).join('')}
                    </select>
                </div>
                
                <div class="space-y-1">
                    <label class="flex items-center gap-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-tight ml-1">
                        <i class="fas fa-signal text-purple-400"></i> Level
                    </label>
                    <select name="mappings[${idx}][level_id]" class="mapping-level w-full bg-white border border-slate-200 text-slate-700 text-xs rounded-lg p-2 focus:ring-4 focus:ring-indigo-500/10 transition" onchange="updateMappingCascade(${idx}, 'level')" disabled>
                        <option value="">Select Level</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div class="space-y-1">
                    <label class="flex items-center gap-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-tight ml-1">
                        <i class="fas fa-tags text-sky-400"></i> Category
                    </label>
                    <select name="mappings[${idx}][category_id]" class="mapping-category w-full bg-white border border-slate-200 text-slate-700 text-xs rounded-lg p-2 focus:ring-4 focus:ring-indigo-500/10 transition" onchange="updateMappingCascade(${idx}, 'category')" disabled>
                        <option value="">Select Category</option>
                    </select>
                </div>
                
                <div class="space-y-1">
                    <label class="flex items-center gap-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-tight ml-1">
                        <i class="fas fa-book-open text-emerald-400"></i> Sub Category
                    </label>
                    <select name="mappings[${idx}][unit_id]" class="mapping-unit w-full bg-white border border-slate-200 text-slate-700 text-xs rounded-lg p-2 focus:ring-4 focus:ring-indigo-500/10 transition" onchange="updateMappingCascade(${idx}, 'unit')" disabled>
                        <option value="">Select Sub Category</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1 mb-3">
                <label class="flex items-center gap-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-tight ml-1">
                    <i class="fas fa-file-contract text-blue-400"></i> Topic
                </label>
                <select name="mappings[${idx}][topic_id]" class="mapping-topic w-full bg-white border border-slate-200 text-slate-700 text-xs rounded-lg p-2 focus:ring-4 focus:ring-indigo-500/10 transition" disabled>
                    <option value="">Select Topic (Optional)</option>
                </select>
            </div>

            <div class="flex items-center gap-3 p-2 bg-white border border-slate-100 rounded-lg shadow-sm">
                <div class="flex-1 flex items-center gap-2">
                    <i class="fas fa-star text-amber-400 text-xs"></i>
                    <span class="text-[10px] font-bold text-slate-500 uppercase">Importance / Priority</span>
                </div>
                <div class="w-16">
                    <input type="number" name="mappings[${idx}][priority]" value="5" min="1" max="10" 
                           class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-bold rounded-lg px-2 py-1 text-center focus:bg-white focus:border-amber-400 transition" 
                           title="Priority (1-10)">
                </div>
            </div>
        `;

        container.appendChild(row);
    }

    function removeSyllabusMapping(idx) {
        const row = document.querySelector(`.mapping-row[data-index="${idx}"]`);
        if (row) {
            row.classList.add('animate__fadeOut');
            setTimeout(() => row.remove(), 300);
        }
    }

    function updateMappingCascade(idx, changedLevel) {
        const row = document.querySelector(`.mapping-row[data-index="${idx}"]`);
        if (!row) return;

        const courseSelect = row.querySelector('.mapping-course');
        const levelSelect = row.querySelector('.mapping-level');
        const categorySelect = row.querySelector('.mapping-category');
        const unitSelect = row.querySelector('.mapping-unit');
        const topicSelect = row.querySelector('.mapping-topic');

        let parentId = null;
        let targetSelect = null;

        if (changedLevel === 'course') {
            parentId = courseSelect.value;
            targetSelect = levelSelect;

            // Reset children
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            unitSelect.innerHTML = '<option value="">Select Sub Category</option>';
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
            categorySelect.disabled = true;
            unitSelect.disabled = true;
            topicSelect.disabled = true;
        } else if (changedLevel === 'level') {
            parentId = levelSelect.value;
            targetSelect = categorySelect;

            // Reset children
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            unitSelect.innerHTML = '<option value="">Select Sub Category</option>';
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
            unitSelect.disabled = true;
            topicSelect.disabled = true;
        } else if (changedLevel === 'category') {
            parentId = categorySelect.value;
            targetSelect = unitSelect;

            // Reset children
            unitSelect.innerHTML = '<option value="">Select Sub Category</option>';
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
            topicSelect.disabled = true;
        } else if (changedLevel === 'unit') {
            parentId = unitSelect.value;
            targetSelect = topicSelect;

            // Reset children
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
        }

        if (parentId && targetSelect) {
            targetSelect.disabled = true;
            targetSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`<?php echo app_base_url('admin/quiz/syllabus/getChildren'); ?>?parent_id=${parentId}`)
                .then(res => res.json())
                .then(data => {
                    targetSelect.innerHTML = `<option value="">Select ${changedLevel === 'course' ? 'Level' : (changedLevel === 'level' ? 'Category' : (changedLevel === 'category' ? 'Sub Category' : 'Topic'))}</option>`;
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            targetSelect.innerHTML += `<option value="${item.id}">${item.title}</option>`;
                        });
                        targetSelect.disabled = false;
                    } else {
                        targetSelect.innerHTML = '<option value="">No items found</option>';
                        targetSelect.disabled = true;
                    }
                })
                .catch(err => {
                    console.error("Fetch failed", err);
                    targetSelect.innerHTML = '<option value="">Error loading</option>';
                });
        }
    }

    // Init
    document.addEventListener('DOMContentLoaded', () => {
        const questionData = <?php echo json_encode($question); ?>;
        const savedType = questionData ? questionData.type : (localStorage.getItem('q_form_type') || 'MCQ');

        if (typeof switchType === 'function') switchType(savedType, null);

        // Populate options if editing
        if (questionData && questionData.options) {
            if (savedType !== 'TF') {
                state.optionCount = questionData.options.length;
                renderOptionsList();

                // Set values
                const container = document.getElementById('options_container');
                const optRows = container.querySelectorAll('.option-row');
                questionData.options.forEach((opt, idx) => {
                    if (optRows[idx]) {
                        const input = optRows[idx].querySelector('input[type=\"text\"]');
                        input.value = opt.text;

                        // Set correct answer
                        if (savedType === 'MCQ') {
                            const radio = optRows[idx].querySelector('input[type=\"radio\"]');
                            if (opt.is_correct) {
                                radio.checked = true;
                                setSingleCorrect(idx);
                            }
                        } else if (savedType === 'MULTI') {
                            const cb = optRows[idx].querySelector('input[type=\"checkbox\"]');
                            if (opt.is_correct) cb.checked = true;
                        }
                    }
                });
            } else {
                // TF handles separately
                const isCorrectTrue = questionData.options[0].is_correct == 1;
                document.querySelector(`input[name=\"tf_selection\"][value=\"${isCorrectTrue ? '1' : '0'}\"]`).checked = true;
                setTFCorrect(isCorrectTrue ? 0 : 1);
            }
        }

        // Attach Global Listener
        const qForm = document.getElementById('createQuestionForm');
        if (qForm) {
            ['input', 'change'].forEach(evt => {
                qForm.addEventListener(evt, saveFormState);
            });

            // Auto-clear on submit
            qForm.addEventListener('submit', () => {
                localStorage.removeItem('q_form_data_v3');
                localStorage.removeItem('q_form_type');
                localStorage.removeItem('q_form_pinned_ids');
                localStorage.removeItem('q_hint_pinned');
            });
        }

        // Restore Multi-Pins
        const savedPins = JSON.parse(localStorage.getItem('q_form_pinned_ids') || '[]');
        savedPins.forEach(pid => {
            setTimeout(() => {
                const el = document.getElementById(pid);
                const btn = document.getElementById('pin_btn_' + pid);
                if (el && btn) {
                    el.classList.add('sticky', 'top-4', 'z-40', 'ring-2', 'ring-indigo-500/20', 'w-full');
                    btn.classList.add('text-indigo-600', '-rotate-45');
                    btn.classList.remove('text-slate-300');
                }
            }, 300);
        });

        const savedHintPin = localStorage.getItem('q_hint_pinned');
        if (savedHintPin === '1' && typeof toggleHintPin === 'function') toggleHintPin();

        // Target Position Visibility & Search Logic
        const targetSearch = document.getElementById('target_pos_search');
        const posList = document.getElementById('pos_level_list');

        function filterTargetPositions() {
            const query = targetSearch.value.toLowerCase();
            const items = posList.querySelectorAll('.pos-level-label');
            items.forEach(item => {
                const isChecked = item.querySelector('input').checked;
                const text = item.innerText.toLowerCase();
                if (query.trim() === '') {
                    item.style.display = isChecked ? 'flex' : 'none';
                } else {
                    item.style.display = text.includes(query) ? 'flex' : 'none';
                }
            });
        }

        if (targetSearch && posList) {
            targetSearch.addEventListener('input', filterTargetPositions);
            posList.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', filterTargetPositions);
            });
            // Initial run after a short delay for restoration
            setTimeout(filterTargetPositions, 500);
        }

        // Add one default syllabus mapping row
        if (typeof addSyllabusMapping === 'function') addSyllabusMapping();
    });
</script>