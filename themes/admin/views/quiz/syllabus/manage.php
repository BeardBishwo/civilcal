<?php
/**
 * PREMIUM SYLLABUS EDITOR - PRO GRID UI
 * Replaces the old tree table with the interactive Grid System
 */

// Helper to flatten the recursive tree for the JS Grid
function flattenTreeForGrid($tree, $depth = 0, &$result = []) {
    foreach ($tree as $node) {
        $flatNode = [
            'id' => $node['id'],
            'title' => $node['title'],
            'type' => $node['type'] ?? 'unit',
            'depth' => $depth,
            // Map DB 'questions_weight' to grid 'weight' (Marks)
            'weight' => (float)($node['questions_weight'] ?? 0),
            // Default missing fields since DB might not have them yet
            'time' => (int)($node['time_minutes'] ?? 0), 
            'qCount' => (int)($node['question_count'] ?? 0), 
            'qOptional' => (int)($node['question_optional'] ?? 0),
            'qType' => $node['question_type'] ?? 'any',
            'difficulty' => $node['difficulty_constraint'] ?? 'any',
            'qEach' => (float)($node['question_marks_each'] ?? 0),
            'selected' => false,
            // Keep DB references if needed
            'parent_id' => $node['parent_id'] ?? null,
            // Hierarchy Links
            'linked_category_id' => $node['linked_category_id'] ?? null,
            'category_name' => $node['category_name'] ?? null,
            'linked_topic_id' => $node['linked_topic_id'] ?? null,
            'topic_name' => $node['topic_name'] ?? null,
            'linked_subject_id' => $node['linked_subject_id'] ?? null,
            'subject_name' => $node['subject_name'] ?? null 
        ];
        
        $result[] = $flatNode;
        
        if (!empty($node['children'])) {
            flattenTreeForGrid($node['children'], $depth + 1, $result);
        }
    }
    return $result;
}

// Prepare initial data for JS
$initialData = [];
if (!empty($nodesTree)) {
    flattenTreeForGrid($nodesTree, 0, $initialData);
}
?>

<!-- Load Tailwind CSS for the Pro UI look -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="admin-wrapper-container antialiased">
    <div class="admin-content-wrapper p-0 shadow-none bg-transparent">

        <!-- === HEADER: METRICS & ACTIONS === -->
        <div class="bg-white border-b border-slate-200 px-6 py-3 sticky top-0 z-50 print:relative print:border-none print:px-0">
            <div class="max-w-full mx-auto flex flex-col gap-3">
                
                <!-- TOP LINE: Back, Title | Description -->
                <div class="flex items-center gap-3">
                    <a href="<?php echo app_base_url('admin/quiz/syllabus'); ?>" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition shrink-0 print:hidden">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                    <div class="flex items-center gap-2">
                        <h2 contenteditable="true" id="syllabus-level-title" class="text-xl font-extrabold text-slate-800 outline-none focus:bg-slate-50 rounded px-1 transition whitespace-nowrap" onblur="updateGlobalLevel(this.innerText)"><?php echo htmlspecialchars($level); ?></h2>
                        <span class="text-slate-300 font-light mx-2">|</span>
                        <div contenteditable="true" id="syllabus-description-el" class="text-[12px] text-slate-400 font-medium italic outline-none focus:bg-slate-50 rounded px-1 whitespace-nowrap" onblur="updateGlobalSetting('description', this.innerText)">Elevate your curriculum with high-precision metrics.</div>
                    </div>
                </div>

                <!-- BOTTOM LINE: Metrics & Actions -->
                <div class="flex flex-nowrap items-center justify-between gap-4 header-metrics-row">
                    <!-- Metrics -->
                    <div class="flex flex-nowrap items-center gap-2 overflow-x-auto no-scrollbar print:overflow-visible min-w-0">
                        <!-- Marks -->
                        <div class="flex-shrink-0 bg-slate-900 text-white px-3 py-1.5 rounded-lg text-[11px] font-bold shadow-sm flex items-center border border-slate-800">
                            <span class="text-slate-400 mr-2 uppercase tracking-wide">Total Marks:</span>
                            <input type="text" id="global-marks-input" class="header-input text-white w-10 bg-transparent border-b border-slate-600 text-center focus:outline-none placeholder-slate-500 font-bold" placeholder="230" onblur="updateGlobalSetting('marks', this.value)">
                        </div>
                        <!-- Time -->
                        <div class="flex-shrink-0 bg-amber-50 text-amber-900 border border-amber-200 px-3 py-1.5 rounded-lg text-[11px] font-bold shadow-sm flex items-center">
                            <i class="fas fa-clock mr-2 text-amber-500"></i> <span class="text-amber-700/70 mr-1 uppercase tracking-wide">Time:</span>
                            <input type="text" id="global-time-input" class="header-input text-amber-900 w-16 bg-transparent border-b border-amber-300 text-center focus:outline-none font-bold" placeholder="3h 30m" onblur="updateGlobalSetting('time', this.value)">
                        </div>
                        <!-- Tally -->
                        <div id="validator-msg" class="flex-shrink-0 bg-emerald-50 text-emerald-900 border border-emerald-200 px-3 py-1.5 rounded-lg text-[11px] font-bold shadow-sm flex items-center" title="Curriculum Balance Tally">
                            <i class="fas fa-check-circle mr-2 text-emerald-500"></i>
                            <span id="tally-display" class="font-bold">230/230</span>
                        </div>
                        <div class="flex-shrink-0 bg-emerald-50 text-emerald-900 border border-emerald-200 px-3 py-1.5 rounded-lg text-[11px] font-bold shadow-sm flex items-center">
                            <i class="fas fa-flag mr-2 text-emerald-500"></i> <span class="text-emerald-700/70 mr-1 uppercase tracking-wide">Pass:</span>
                            <span id="global-pass-display" class="font-bold text-emerald-900">40</span>
                            <input type="hidden" id="global-pass-input" value="40">
                        </div>
                        <!-- Neg -->
                        <div class="flex-shrink-0 bg-rose-50 text-rose-900 border border-rose-200 px-3 py-1.5 rounded-lg text-[11px] font-bold shadow-sm flex items-center gap-1.5">
                            <i class="fas fa-minus-circle text-rose-500"></i> <span class="text-rose-700/70 mr-1 uppercase tracking-wide">Neg:</span>
                            <input type="number" id="global-neg-input" class="bg-transparent border-b border-rose-300 text-rose-900 w-8 text-center focus:outline-none font-bold placeholder-rose-300" placeholder="20" onblur="updateGlobalSetting('negValue', this.value)">
                            <select id="neg-unit-select" class="bg-transparent text-rose-900 font-bold outline-none cursor-pointer appearance-none px-1 rounded hover:bg-white/50" onchange="updateGlobalSetting('negUnit', this.value)">
                                <option value="percent">%</option>
                                <option value="number">No.</option>
                            </select>
                            <div class="h-3 w-px bg-rose-300 mx-0.5"></div>
                            <select id="neg-scope-select" class="bg-transparent text-rose-900 font-bold outline-none cursor-pointer appearance-none px-1 rounded hover:bg-white/50" onchange="updateGlobalSetting('negScope', this.value)">
                                <option value="per-q">Per Q.</option>
                                <option value="total">Total</option>
                            </select>
                        </div>
                    </div>

                    <!-- Right Actions -->
                <div class="flex items-center gap-2 print:hidden ml-auto">
                    <button onclick="loadSubEngineerTemplate()" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-xs font-bold transition-all border border-slate-200">
                        <i class="fas fa-magic text-blue-500"></i> Sub-Engineer
                    </button>
                    <button onclick="addTopic()" class="flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-xs font-bold transition-all border border-slate-200 shadow-sm">
                        <i class="fas fa-plus text-blue-500"></i> Row
                    </button>
                    <button onclick="openCloneModal()" class="flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-xs font-bold transition-all border border-slate-200 shadow-sm">
                        <i class="fas fa-copy text-indigo-500"></i> Clone
                    </button>
                        <button onclick="saveSyllabus()" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-md flex items-center">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                        <button onclick="window.print()" class="h-9 w-9 bg-slate-800 hover:bg-slate-900 text-white rounded-full flex items-center justify-center transition shadow-md" title="Print Syllabus">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- === MAIN GRID AREA === -->
        <div class="p-0">
            <div class="bg-white border border-slate-200 shadow-xl rounded-xl overflow-hidden">
                <div class="syllabus-grid-wrapper overflow-x-auto">
                    <div class="syllabus-grid" id="syllabus-container">
                        <!-- Headers -->
                        <div class="grid-header"><input type="checkbox" id="select-all" class="w-4 h-4 rounded cursor-pointer" onclick="toggleSelectAll()"></div>
                        <div class="grid-header" title="Drag to Reorder"><i class="fas fa-arrows-alt text-slate-400"></i></div>
                        <div class="grid-header">Lvl</div>
                        <div class="grid-header text-left pl-4">Topic / Title</div>
                        <div class="grid-header">Time (m)</div>
                        <div class="grid-header">Node Type</div>
                        <div class="grid-header">Qty</div>
                        <div class="grid-header" title="Optional Questions">Opt</div>
                        <div class="grid-header">Each</div>
                        <div class="grid-header" title="Question Type Constraint">Q-Type</div>
                        <div class="grid-header" title="Difficulty Constraint">Diff</div>
                        <div class="grid-header">Marks</div>
                        <div class="grid-header text-center">Hierarchy</div>
                        <div class="grid-header">Actions</div>
                        <div class="grid-header text-center">Linked</div>
                        <!-- Rows via JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Action Bar -->
        <div id="bulk-action-bar" class="fixed bottom-8 left-1/2 transform -translate-x-1/2 translate-y-[200%] bg-[#101827] text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-6 z-[100] transition-all duration-500 border border-slate-700 select-none print:hidden opacity-0 invisible">
            <span class="text-xs font-bold text-slate-400 whitespace-nowrap"><span id="selected-count" class="text-white text-base mr-1">0</span> Rows Selected</span>
            <div class="h-6 w-px bg-slate-700"></div>
            <button onclick="bulkDuplicate()" class="hover:text-blue-400 transition text-[10px] font-extrabold uppercase flex items-center group whitespace-nowrap"><i class="fas fa-copy mr-2 group-hover:scale-110"></i> Duplicate</button>
            <div class="h-6 w-px bg-slate-700"></div>
            <button onclick="bulkIndent(-1)" class="hover:text-blue-400 transition text-[10px] font-extrabold uppercase flex items-center group whitespace-nowrap"><i class="fas fa-outdent mr-2 group-hover:scale-110"></i> Outdent</button>
            <button onclick="bulkIndent(1)" class="hover:text-blue-400 transition text-[10px] font-extrabold uppercase flex items-center group whitespace-nowrap"><i class="fas fa-indent mr-2 group-hover:scale-110"></i> Indent</button>
            <div class="h-6 w-px bg-slate-700"></div>
            <button onclick="bulkDelete()" class="hover:text-rose-400 transition text-[10px] font-extrabold uppercase flex items-center group whitespace-nowrap"><i class="fas fa-trash mr-2 group-hover:scale-110"></i> Delete</button>
            <button onclick="clearSelection()" class="ml-2 w-8 h-8 flex items-center justify-center rounded-full bg-slate-800 text-slate-400 hover:text-white transition shadow-inner"><i class="fas fa-times text-xs"></i></button>
        </div>

    </div>

<style>
    /* Link Hierarchy Modal Responsive Positioning */
    .hierarchy-modal-position { left: 280px; transition: left 0.3s ease; }
    .sidebar-collapsed .hierarchy-modal-position,
    .admin-main.sidebar-collapsed .hierarchy-modal-position { left: 70px !important; }
    @media (max-width: 991px) { .hierarchy-modal-position { left: 0 !important; } }
</style>

    <!-- === HIERARCHY SELECTION MODAL === -->
    <div id="hierarchy-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeHierarchyModal()"></div>
        <div class="fixed top-[70px] right-0 bottom-0 flex items-center justify-center p-8 hierarchy-modal-position">
            <div class="bg-white rounded-2xl shadow-2xl w-full h-full overflow-hidden transform transition-all relative flex flex-col ring-1 ring-black/5">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-slate-800">Link Hierarchy</h3>
                    <div class="flex items-center gap-3">
                        <button onclick="toggleLinkedOnly()" id="btn-show-linked" class="text-slate-400 hover:text-blue-600 px-3 py-1.5 rounded-lg transition text-xs font-bold flex items-center border border-transparent hover:border-blue-100 hover:bg-blue-50" title="Show Linked Only">
                            <i class="fas fa-link mr-1"></i> Linked
                        </button>
                        <button onclick="removeLink()" class="text-rose-500 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition text-xs font-bold flex items-center"><i class="fas fa-unlink mr-1"></i> Unlink</button>
                        <button onclick="closeHierarchyModal()" class="text-slate-400 hover:text-slate-600 transition"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <!-- Search -->
                <div class="px-6 py-4 border-b border-slate-100">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="hierarchy-search" class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none text-sm font-medium transition" placeholder="Search categories, topics..." onkeyup="filterHierarchyList()">
                    </div>
                </div>
                <!-- Filters Row (Searchable Selects) -->
                <div class="link-filter-row flex items-center justify-between gap-3 mb-4 sticky top-0 z-30">
                    
                    <!-- Course Filter -->
                    <div class="relative flex-1 min-w-[100px] custom-dropdown" id="dropdown-course">
                        <button type="button" class="w-full flex items-center justify-between px-2 py-1 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-600 hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all dropdown-trigger" onclick="toggleCustomDropdown('course')">
                            <span class="truncate dropdown-label">Course</span>
                             <i class="fas fa-chevron-down text-[10px] opacity-50 ml-1"></i>
                        </button>
                        <div class="absolute top-full left-0 min-w-full w-auto mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 hidden dropdown-menu origin-top transform transition-all duration-200 max-h-96">
                             <div class="p-2 border-b border-slate-100 sticky top-0 bg-white rounded-t-xl z-10 w-full min-w-[200px]">
                                <input type="text" class="w-full text-xs px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 dropdown-search" placeholder="Search..." onkeyup="filterCustomDropdown('course', this.value)">
                             </div>
                             <ul class="max-h-80 overflow-y-auto py-1 custom-scrollbar dropdown-list" id="list-course">
                                <!-- JS Populated -->
                             </ul>
                        </div>
                        <input type="hidden" id="modal-filter-course" value="">
                    </div>

                    <!-- Education Level Filter -->
                    <div class="relative flex-1 min-w-[100px] custom-dropdown" id="dropdown-edu">
                        <button type="button" class="w-full flex items-center justify-between px-2 py-1 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-600 hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all dropdown-trigger" onclick="toggleCustomDropdown('edu')">
                            <span class="truncate dropdown-label">Edu. Level</span>
                             <i class="fas fa-chevron-down text-[10px] opacity-50 ml-1"></i>
                        </button>
                        <div class="absolute top-full left-0 min-w-full w-auto mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 hidden dropdown-menu origin-top transform transition-all duration-200 max-h-96">
                             <div class="p-2 border-b border-slate-100 sticky top-0 bg-white rounded-t-xl z-10 w-full min-w-[200px]">
                                <input type="text" class="w-full text-xs px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 dropdown-search" placeholder="Search..." onkeyup="filterCustomDropdown('edu', this.value)">
                             </div>
                             <ul class="max-h-80 overflow-y-auto py-1 custom-scrollbar dropdown-list" id="list-edu">
                                <!-- JS Populated -->
                             </ul>
                        </div>
                         <input type="hidden" id="modal-filter-edu" value="">
                    </div>

                    <!-- Category Filter -->
                    <div class="relative flex-1 min-w-[100px] custom-dropdown" id="dropdown-cat">
                         <button type="button" class="w-full flex items-center justify-between px-2 py-1 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-600 hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all dropdown-trigger" onclick="toggleCustomDropdown('cat')">
                            <span class="truncate dropdown-label">Category</span>
                             <i class="fas fa-chevron-down text-[10px] opacity-50 ml-1"></i>
                        </button>
                        <div class="absolute top-full left-0 min-w-full w-auto mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 hidden dropdown-menu origin-top transform transition-all duration-200 max-h-96">
                             <div class="p-2 border-b border-slate-100 sticky top-0 bg-white rounded-t-xl z-10 w-full min-w-[200px]">
                                <input type="text" class="w-full text-xs px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 dropdown-search" placeholder="Search..." onkeyup="filterCustomDropdown('cat', this.value)">
                             </div>
                             <ul class="max-h-80 overflow-y-auto py-1 custom-scrollbar dropdown-list" id="list-cat">
                                <!-- JS Populated -->
                             </ul>
                        </div>
                        <input type="hidden" id="modal-filter-cat" value="">
                    </div>

                     <!-- Sub-Category Filter -->
                    <div class="relative flex-1 min-w-[100px] custom-dropdown" id="dropdown-topic">
                        <button type="button" class="w-full flex items-center justify-between px-2 py-1 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-600 hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all dropdown-trigger" onclick="toggleCustomDropdown('topic')">
                            <span class="truncate dropdown-label">Sub-Cat</span>
                             <i class="fas fa-chevron-down text-[10px] opacity-50 ml-1"></i>
                        </button>
                        <div class="absolute top-full left-0 min-w-full w-auto mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 hidden dropdown-menu origin-top transform transition-all duration-200 max-h-96">
                             <div class="p-2 border-b border-slate-100 sticky top-0 bg-white rounded-t-xl z-10 w-full min-w-[200px]">
                                <input type="text" class="w-full text-xs px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 dropdown-search" placeholder="Search..." onkeyup="filterCustomDropdown('topic', this.value)">
                             </div>
                             <ul class="max-h-80 overflow-y-auto py-1 custom-scrollbar dropdown-list" id="list-topic">
                                <!-- JS Populated -->
                             </ul>
                        </div>
                        <input type="hidden" id="modal-filter-topic" value="">
                     </div>

                    <!-- Position Level Filter (Moved to End) -->
                    <div class="relative flex-1 min-w-[100px] custom-dropdown" id="dropdown-pos">
                        <button type="button" class="w-full flex items-center justify-between px-2 py-1 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-600 hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all dropdown-trigger" onclick="toggleCustomDropdown('pos')">
                            <span class="truncate dropdown-label">Pos. Level</span>
                             <i class="fas fa-chevron-down text-[10px] opacity-50 ml-1"></i>
                        </button>
                        <div class="absolute top-full right-0 min-w-full w-auto mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 hidden dropdown-menu origin-top transform transition-all duration-200 max-h-96">
                             <div class="p-2 border-b border-slate-100 sticky top-0 bg-white rounded-t-xl z-10 w-full min-w-[200px]">
                                <input type="text" class="w-full text-xs px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 dropdown-search" placeholder="Search..." onkeyup="filterCustomDropdown('pos', this.value)">
                                 </div>

                        <ul class="max-h-80 overflow-y-auto py-1 custom-scrollbar dropdown-list" id="list-pos">
                            <!-- JS Populated -->
                        </ul>
                    </div>
                    <input type="hidden" id="modal-filter-pos" value="">
                </div>

                    <!-- Filter Actions -->
                    <div class="flex items-center gap-1 ml-2">
                        <button onclick="saveLinkFromFilter()" class="w-7 h-7 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Link Selected Item">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                        <button onclick="resetHierarchyFilters()" class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition" title="Clear Filters">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                </div>
                <div class="px-6 py-3 bg-white border-b border-slate-100 flex gap-2">
                    <button onclick="switchHierarchyTab('category')" class="h-tab px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm active-tab" data-tab="category">Categories</button>
                    <button onclick="switchHierarchyTab('topic')" class="h-tab px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm" data-tab="topic">Sub-Categories</button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-2 bg-slate-50/50" id="hierarchy-list-container"></div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Grid Styles - Beautiful Grid Transformation */
    .syllabus-grid {
        display: grid;
        /* Columns: Ck(40), Drag(35), LVL(35), Title(1fr), Time(65), Type(110), Qty(60), Opt(60), Each(60), Q-Type(90), Diff(90), Marks(65), Hier(80), Act(90), Linked(250) */
        grid-template-columns: 40px 35px 35px 1fr 65px 110px 60px 60px 60px 90px 90px 65px 80px 90px 250px;
        background-color: #cbd5e1; /* The color of ALL grid lines */
        gap: 1.5px; /* Distinct 1.5px grid lines everywhere */
        min-width: 1450px;
        border: 2px solid #94a3b8; /* Stronger outer frame */
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border-radius: 8px 8px 0 0;
        overflow: hidden;
    }
    .grid-header {
        background-color: #f8fafc; 
        color: #1e293b; 
        font-size: 0.65rem; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 0.1em; 
        padding: 14px 4px; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        position: sticky;
        top: 0;
        z-index: 20;
    }
    .grid-row { display: none; } /* Legacy row container not used in this flat grid model */
    
    /* Clean Title Cells - Matching Sample */
    .cell-title-phase { 
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; 
        color: #f8fafc !important;
        position: relative;
    }
    .cell-title-section { 
        background-color: #f1f5f9 !important; 
        color: #1e40af !important;
    }
    
    .grid-cell { 
        padding: 0 14px; 
        display: flex; 
        align-items: center; 
        font-size: 0.825rem; 
        color: #334155; 
        background-color: white; /* Base background for data cells */
        position: relative; 
        height: 54px; /* Luxury spacing */
        overflow: hidden; 
    }

    /* Row Background Tints */
    .row-phase { background-color: #f8fafc !important; color: #0f172a; font-weight: 800; }
    .row-section { background-color: #f1f5f9 !important; color: #1e40af; font-weight: 700; }
    .row-unit { background-color: white !important; }

    /* Hierarchy Indicators - Still useful overlay on first cell */
    .row-phase-indicator { border-left: 5px solid #0f172a !important; }
    .row-section-indicator { border-left: 5px solid #3b82f6 !important; }
    .row-unit-indicator { border-left: 5px solid #e2e8f0 !important; }
    
    .input-premium {
        width: 100%; 
        padding: 0 6px; 
        border: 1px solid #e2e8f0; 
        border-radius: 6px; 
        text-align: center; 
        font-family: 'Inter', system-ui, sans-serif; 
        font-weight: 700; 
        font-size: 0.75rem; 
        transition: all 0.2s;
        background-color: white; 
        height: 28px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.03);
    }
    .input-premium:focus { 
        border-color: #3b82f6; 
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); 
        outline: none; 
    }
    
    .input-readonly { 
        width: 100%; 
        height: 28px; 
        background-color: #f8fafc !important; 
        border: 1px solid #e2e8f0 !important; 
        border-radius: 6px; 
        color: #94a3b8 !important; 
        font-size: 0.725rem; 
        text-align: center; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-weight: 800; 
        cursor: not-allowed;
        opacity: 0.8;
    }
    
    .time-input { color: #b45309; }
    .qty-input { color: #059669; }
    .each-input { color: #2563eb; }
    
    .depth-0 { color: #0f172a; font-weight: 800; }
    .depth-1 { color: #1e293b; font-weight: 700; }
    .depth-2 { color: #334155; font-weight: 600; }
    .depth-3 { color: #64748b; font-weight: 500; }

    /* Tree Guide Lines */
    .tree-guide {
        position: absolute;
        left: var(--guide-x);
        top: 0;
        bottom: 0;
        width: 1.5px;
        background-color: #cbd5e1;
        pointer-events: none;
    }
    .tree-guide-horizontal {
        position: absolute;
        left: var(--guide-x);
        top: 50%;
        width: 14px;
        height: 1.5px;
        background-color: #cbd5e1;
        pointer-events: none;
    }
    
    .row-checkbox { cursor: pointer; accent-color: #6366f1; }
    .drag-handle { cursor: grab; color: #cbd5e1; padding: 6px; transition: 0.2s; }
    .drag-handle:hover { color: #94a3b8; }
    .drag-handle:active { cursor: grabbing; scale: 0.9; }

    #bulk-action-bar.visible { transform: translate(-50%, 0); }
    
    /* Node Types & Breadcrumbs */
    .node-badge {
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s;
        justify-content: center;
    }
    .badge-breadcrumb {
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
        margin: 2px;
    }
    .breadcrumb-separator {
        color: #94a3b8;
        font-size: 8px;
        margin: 0 2px;
    }
    .linked-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 4px;
    }
    .type-select-styled {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 10px;
        border: 1px solid #e2e8f0;
        outline: none;
        cursor: pointer;
        font-size: 10px;
        font-weight: 800;
        padding: 0 24px 0 12px;
        border-radius: 8px; /* Slightly more modern than pill */
        text-align: center;
        width: 100% !important;
        height: 30px;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .type-select-styled:hover { 
        border-color: #3b82f6; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
    }
    .type-select-styled:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .badge-phase { background: #1e293b; color: white; }
    .badge-section { background: #e0e7ff; color: #4338ca; }
    .badge-unit { background: #f1f5f9; color: #64748b; }

    /* Hierarchy Indicators - Subtle Bar on first cell only */
    .row-phase-indicator { border-left: 4px solid #1e293b !important; }
    .row-section-indicator { border-left: 4px solid #6366f1 !important; }
    .row-unit-indicator { border-left: 4px solid #e2e8f0 !important; }
    
    .input-readonly { 
        background-color: #f8fafc !important; 
        color: #94a3b8 !important; 
        font-weight: 800 !important;
        border: 1px solid #f1f5f9 !important;
        pointer-events: none;
    }

    /* Modal Filters */
    .link-filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        background: #f8fafc;
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
    }
    .filter-group {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1;
        min-width: 0;
    }
    .filter-label {
        font-size: 9px;
        font-weight: 800;
        color: #1e40af;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        opacity: 0.8;
    }
    .filter-select {
        flex: 1;
        min-width: 110px;
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background-color: white;
        font-size: 11px;
        color: #475569;
        outline: none;
        transition: all 0.2s;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 12px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .filter-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .filter-select::placeholder {
        color: #94a3b8;
    }

    .hierarchy-btn {
        width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; background: #f8fafc; color: #64748b; transition: 0.2s; border: 1px solid #e2e8f0;
    }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    @media print {
        /* Hide Global Infrastructure */
        #admin-sidebar, .admin-sidebar, header.admin-header, .admin-header, 
        .loading-overlay, .notification-toast, .search-overlay,
        .print:hidden, #bulk-action-bar, .actions-cell, .drag-handle, 
        .row-checkbox, .hierarchy-btn, .sidebar-spacer, header, nav, 
        .admin-footer, footer {
            display: none !important;
        }
        
        /* Reset Main Containers */
        #admin-wrapper, .admin-wrapper, #admin-main, .admin-main {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            display: block !important;
            position: static !important;
            background: white !important;
        }
        
        .admin-wrapper-container, .admin-content-wrapper { 
            background: white !important; 
            padding: 0 !important; 
            margin: 0 !important; 
            box-shadow: none !important; 
            width: 100% !important;
        }
        
        /* Syllabus Header Branding */
        .sticky { 
            position: static !important; 
            display: block !important; 
            border: none !important; 
            padding: 2.5cm 0 1cm 0 !important; /* Some breathing room at top */
        }
        .header-metrics-row { display: none !important; }
        
        /* Force Grid to Professional Table */
        .syllabus-grid { 
            display: table !important;
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin-top: 10px !important;
        }
        .grid-header { 
            display: table-cell !important; 
            padding: 10px 8px !important; 
            border: 1px solid #333 !important; 
            font-size: 11px !important; 
            font-weight: bold !important;
            background: #eee !important;
            text-transform: uppercase !important;
        }
        .grid-row { display: table-row !important; page-break-inside: avoid !important; }
        .grid-cell { 
            display: table-cell !important; 
            border: 1px solid #333 !important; 
            padding: 10px 8px !important; 
            font-size: 11px !important; 
            vertical-align: middle !important;
            word-wrap: break-word !important;
        }
        
        /* Precise Column Widths */
        .grid-header:nth-child(3), .grid-cell:nth-child(3) { width: 35px !important; text-align: center; }
        .grid-header:nth-child(4), .grid-cell:nth-child(4) { width: auto !important; }
        .grid-header:nth-child(5) { width: 60px !important; text-align: center; } 
        .grid-cell:nth-child(5) { width: 60px !important; text-align: center; font-weight: bold; }
        .grid-header:nth-child(6), .grid-cell:nth-child(6) { width: 85px !important; text-align: center; }
        .grid-header:nth-child(7), .grid-cell:nth-child(7) { width: 45px !important; text-align: center; }
        .grid-header:nth-child(8), .grid-cell:nth-child(8) { width: 45px !important; text-align: center; }
        .grid-header:nth-child(9) { width: 55px !important; text-align: center; }
        .grid-cell:nth-child(9) { width: 55px !important; text-align: center; font-weight: bold; }
        
        /* Hide Interaction Columns */
        .grid-header:nth-child(1), .grid-cell:nth-child(1),
        .grid-header:nth-child(2), .grid-cell:nth-child(2),
        .grid-header:nth-child(10), .grid-cell:nth-child(10),
        .grid-header:nth-child(11), .grid-cell:nth-child(11) {
            display: none !important;
        }

        /* Prevent Text Truncation */
        .line-clamp-1 { -webkit-line-clamp: unset !important; display: block !important; }
        .whitespace-nowrap { white-space: normal !important; }
    }
</style>

<script>
    // 1. DATA INITIALIZATION
    let syllabusData = <?php echo !empty($initialData) ? json_encode($initialData) : '[]'; ?>;
    
    if(syllabusData.length === 0) {
        syllabusData = [
            { id: Date.now(), title: "Paper I: General", type: "paper", depth: 0, weight: 100, time: 0, qCount: 0, qOptional: 0, qEach: 0, selected: false }
        ];
    }

    let currentLevel = '<?php echo addslashes($level); ?>';
    const coursesData = <?php echo json_encode($allCourses); ?>;
    const eduData = <?php echo json_encode($allEduLevels); ?>;
    const posData = <?php echo json_encode($allPosLevels); ?>;
    let manualSettings = <?php echo !empty($settings) ? json_encode($settings) : "{ 
        marks: null, 
        time: null, 
        pass: 40, 
        negValue: 20, 
        negUnit: 'percent',
        negScope: 'per-q',
        description: 'Elevate your curriculum with our high-precision syllabus engine.'
    }"; ?>;

    // --- INITIALIZATION ---
    function initSettings() {
        if(manualSettings.marks) document.getElementById('global-marks-input').value = manualSettings.marks;
        if(manualSettings.time) document.getElementById('global-time-input').value = manualSettings.time;
        if(manualSettings.pass) document.getElementById('global-pass-input').value = manualSettings.pass;
        if(manualSettings.negValue) document.getElementById('global-neg-input').value = manualSettings.negValue;
        if(manualSettings.negUnit) document.getElementById('neg-unit-select').value = manualSettings.negUnit;
        if(manualSettings.negScope) document.getElementById('neg-scope-select').value = manualSettings.negScope;
        if(manualSettings.description) document.getElementById('syllabus-description-el').innerText = manualSettings.description;
    }
    let draggedItemIndex = null;

    // --- HELPER FUNCTIONS ---
    function formatNumber(num) {
        if (!num || num === 0) return '0';
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // --- GRID RENDERING ---
    function renderGrid() {
        const container = document.getElementById('syllabus-container');
        while(container.children.length > 15) container.removeChild(container.lastChild);

        syllabusData.forEach((row, index) => {
            const rowIndicatorClass = row.type === 'paper' ? 'row-phase-indicator' : (row.type === 'section' ? 'row-section-indicator' : 'row-unit-indicator');
            
            // Checkbox (The first cell gets the indicator bar)
            container.appendChild(createCell(`<input type="checkbox" class="row-checkbox w-4 h-4 rounded" ${row.selected ? 'checked' : ''} onchange="toggleRowSelection(${index}, this.checked)">`, `justify-center ${rowIndicatorClass}`));
            
            // Drag Handle
            const dragCell = createCell(`<i class="fas fa-grip-vertical drag-handle"></i>`, 'justify-center');
            dragCell.draggable = true;
            setupDragEvents(dragCell, index);
            container.appendChild(dragCell);

            // Level
            container.appendChild(createCell(row.depth, `justify-center depth-${row.depth} bg-slate-50 font-mono text-[10px]`));

            // Topic / Title (with depth-based styling)
            const indentSize = 38;
            const padding = 16 + (row.depth * indentSize);
            let icon = 'fa-folder';
            let titleClass = '';
            let extraCellClass = '';
            
            if(row.depth === 0 || row.type === 'paper') {
                icon = 'fa-layer-group';
                titleClass = 'font-bold text-white';
                extraCellClass = 'cell-title-phase';
            } else if(row.type === 'section' || row.depth === 1) {
                icon = 'fa-folder-open text-blue-500';
                titleClass = 'font-bold text-blue-700';
                extraCellClass = 'cell-title-section';
            } else {
                icon = 'fa-folder text-slate-400';
                titleClass = 'text-slate-600';
            }
            
            // Generate Tree Lines
            let treeLinesHtml = '';
            for(let i = 0; i < row.depth; i++) {
                const xPos = 16 + (i * indentSize) + 6; // Center of icon position for that level
                treeLinesHtml += `<div class="tree-guide" style="--guide-x: ${xPos}px;"></div>`;
                if(i === row.depth - 1) {
                    treeLinesHtml += `<div class="tree-guide-horizontal" style="--guide-x: ${xPos}px;"></div>`;
                }
            }

            container.appendChild(createCell(
                `${treeLinesHtml}
                 <div class="flex items-center w-full relative z-10">
                    <i class="fas ${icon} mr-3 opacity-90 text-sm ${row.type === 'paper' ? 'text-white/60' : ''}"></i> 
                    <span contenteditable="true" class="w-full outline-none focus:bg-white rounded px-1 transition duration-200 ${titleClass}" onblur="updateRow(${index}, 'title', this.innerText)">${row.title}</span>
                 </div>`,
                `depth-${row.depth} ${extraCellClass}`, `padding-left: ${padding}px;`
            ));

            // Time (with formatted display)
            const timeBg = row.depth === 0 ? "bg-amber-100/50" : "bg-white";
            const timeDisplay = row.depth === 0 ? `<div class="font-bold text-amber-800">${formatNumber(row.time)}</div>` : `<input type="number" class="input-premium time-input ${timeBg}" value="${row.time || 0}" onchange="updateRow(${index}, 'time', this.value)">`;
            container.appendChild(createCell(timeDisplay, 'justify-center'));

            // Node Type (Styled visible select)
            let typeColor = "bg-slate-100/50 text-slate-500";
            if(row.type === 'paper') typeColor = "bg-slate-800 text-white font-bold";
            if(row.type === 'section') typeColor = "bg-slate-200 text-slate-700 font-bold";
            if(row.type === 'unit') typeColor = "bg-slate-50 text-slate-400";
            if(row.type === 'topic') typeColor = "bg-blue-50 text-blue-600 font-bold";

            container.appendChild(createCell(`
                <select class="type-select-styled ${typeColor}" onchange="updateRow(${index}, 'type', this.value)">
                    <option value="paper" ${row.type==='paper'?'selected':''}>Paper</option>
                    <option value="section" ${row.type==='section'?'selected':''}>Main Category</option>
                    <option value="unit" ${row.type==='unit'?'selected':''}>Sub Category</option>
                    <option value="topic" ${row.type==='topic'?'selected':''}>Topic</option>
                </select>
            `, 'justify-center px-2'));

            // Qty (with formatted display)
            const isTopic = row.type === 'unit';
            const qtyClass = isTopic ? 'input-premium' : 'input-readonly';
            const qtyDisplay = `<input type="number" class="${qtyClass} qty-input" value="${row.qCount || 0}" ${!isTopic ? 'readonly' : ''} onchange="updateRow(${index}, 'qCount', this.value)">`;
            container.appendChild(createCell(qtyDisplay, 'justify-center'));

            // Optional (new column)
            const optClass = isTopic ? 'input-premium bg-amber-50' : 'input-readonly';
            const optDisplay = `<input type="number" class="${optClass} opt-input" value="${row.qOptional || 0}" ${!isTopic ? 'readonly' : ''} onchange="updateRow(${index}, 'qOptional', this.value)" placeholder="0">`;
            container.appendChild(createCell(optDisplay, 'justify-center'));

            // Each (with formatted display)
            const eachClass = isTopic ? 'input-premium' : 'input-readonly';
            const eachDisplay = isTopic 
                ? `<input type="number" class="${eachClass} each-input" value="${row.qEach || 0}" onchange="updateRow(${index}, 'qEach', this.value)">`
                : `<div class="text-slate-400 font-bold text-[10px]">-</div>`;
            container.appendChild(createCell(eachDisplay, 'justify-center'));

            // Q-Type (Question Type dropdown)
            const qTypeOptions = [
                {value: 'any', label: 'Any', color: 'bg-slate-50 text-slate-600'},
                {value: 'mcq_single', label: 'MCQ', color: 'bg-blue-50 text-blue-700'},
                {value: 'true_false', label: 'T/F', color: 'bg-green-50 text-green-700'},
                {value: 'multi_select', label: 'Multi', color: 'bg-purple-50 text-purple-700'},
                {value: 'subjective', label: 'Theory', color: 'bg-amber-50 text-amber-700'}
            ];
            const selectedQType = row.qType || 'any';
            const qTypeColor = qTypeOptions.find(opt => opt.value === selectedQType)?.color || 'bg-slate-50 text-slate-600';
            const qTypeDisplay = (row.type !== 'unit' && row.depth !== 0) ? `<div class="text-slate-400 text-[10px]">-</div>` : `
                <select class="type-select-styled ${qTypeColor} !text-slate-500 opacity-60 hover:opacity-100" onchange="updateRow(${index}, 'qType', this.value)">
                    ${qTypeOptions.map(opt => `<option value="${opt.value}" ${selectedQType === opt.value ? 'selected' : ''}>${opt.label}</option>`).join('')}
                </select>
            `;
            container.appendChild(createCell(qTypeDisplay, 'justify-center px-2'));

            // Difficulty dropdown
            const diffOptions = [
                {value: 'any', label: 'Any', color: 'bg-slate-50 text-slate-600'},
                {value: 'easy', label: 'Easy', color: 'bg-green-50 text-green-600 border-green-100'},
                {value: 'easy_mid', label: 'Easy-Mid', color: 'bg-lime-50 text-lime-600 border-lime-100'},
                {value: 'medium', label: 'Medium', color: 'bg-yellow-50 text-yellow-600 border-yellow-100'},
                {value: 'hard', label: 'Hard', color: 'bg-orange-50 text-orange-600 border-orange-100'},
                {value: 'expert', label: 'Expert', color: 'bg-red-50 text-red-600 border-red-100'}
            ];
            const selectedDiff = row.difficulty || 'any';
            const diffColor = diffOptions.find(opt => opt.value === selectedDiff)?.color || 'bg-slate-50 text-slate-600';
            const diffDisplay = (row.type !== 'unit' && row.depth !== 0) ? `<div class="text-slate-400 text-[10px]">-</div>` : `
                <select class="type-select-styled ${diffColor} !text-slate-500 opacity-60 hover:opacity-100" onchange="updateRow(${index}, 'difficulty', this.value)">
                    ${diffOptions.map(opt => `<option value="${opt.value}" ${selectedDiff === opt.value ? 'selected' : ''}>${opt.label}</option>`).join('')}
                </select>
            `;
            container.appendChild(createCell(diffDisplay, 'justify-center px-2'));

            // Marks (with formatted display)
            const marksBg = row.depth === 0 ? "bg-slate-100" : "bg-white";
            const marksDisplay = `<div class="font-bold ${row.depth === 0 ? 'text-slate-800' : 'text-slate-700'}">${formatNumber(row.weight)}</div>`;
            container.appendChild(createCell(marksDisplay, 'justify-center'));

            // Hierarchy
            container.appendChild(createCell(`
                <div class="flex gap-1.5 justify-center w-full">
                    <button onclick="changeDepth(${index}, -1)" class="hierarchy-btn" title="Outdent"><i class="fas fa-chevron-left text-[10px]"></i></button>
                    <button onclick="changeDepth(${index}, 1)" class="hierarchy-btn" title="Indent"><i class="fas fa-chevron-right text-[10px]"></i></button>
                </div>
            `, 'justify-center'));

            // Actions
            container.appendChild(createCell(`
                <button onclick="handleRowDuplicate(${index})" class="text-slate-300 hover:text-blue-500 transition px-2" title="Duplicate"><i class="fas fa-clone text-sm"></i></button>
                <button onclick="deleteRow(${index})" class="text-slate-300 hover:text-rose-500 transition px-2" title="Delete"><i class="fas fa-trash-alt text-sm"></i></button>
            `, 'justify-center'));

            // Set depth and type attributes for styling
            const rowElements = container.querySelectorAll('.grid-cell:nth-last-child(-n+15)');
            const rowClass = row.type === 'paper' ? 'row-phase' : (row.type === 'section' ? 'row-section' : 'row-unit');
            rowElements.forEach(cell => {
                cell.setAttribute('data-depth', row.depth);
                cell.setAttribute('data-type', row.type);
                cell.className += ' ' + rowClass;
            });

            // Linked (Breadcrumbs)
            let linkedHtml = '';
            const breadcrumbBase = <?php echo json_encode($breadcrumbBase ?? []); ?>;
            
            if (row.linked_category_id || row.linked_topic_id) {
                let crumbs = [...breadcrumbBase];
                if (row.category_name) crumbs.push({name: row.category_name, type: 'category'});
                if (row.topic_name) crumbs.push({name: row.topic_name, type: 'topic'});
                
                let crumbsHtml = crumbs.map((c, i) => `
                    <div class="node-badge badge-breadcrumb" title="${c.type.toUpperCase()}">${c.name}</div>
                    ${i < crumbs.length - 1 ? '<i class="fas fa-chevron-right breadcrumb-separator"></i>' : ''}
                `).join('');
                
                linkedHtml = `<div class="linked-container cursor-pointer hover:opacity-80" onclick="openHierarchyModal(${index})">${crumbsHtml}</div>`;
            } else {
                linkedHtml = `<button onclick="openHierarchyModal(${index})" class="hierarchy-btn text-blue-500 hover:bg-blue-50 hover:border-blue-200" title="Link Category/Topic"><i class="fas fa-plus text-xs"></i></button>`;
            }
            container.appendChild(createCell(linkedHtml, 'justify-center px-1'));
        });

        validateSyllabus();
        updateBulkBar();
    }

    function createCell(html, classes = "", style = "") {
        const div = document.createElement('div');
        div.className = `grid-cell ${classes}`;
        div.innerHTML = html;
        if(style) div.style = style;
        return div;
    }

    // --- LOGIC FUNCTIONS ---
    function updateRow(index, field, value) {
        const stringFields = ['title', 'type', 'difficulty', 'qType'];
        if(!stringFields.includes(field)) value = parseFloat(value) || 0;
        syllabusData[index][field] = value;
        
        // Auto-adjust depth based on node type (Enterprise Rule)
        if(field === 'type') {
            const typeDepthMap = { 'paper': 0, 'section': 1, 'unit': 2 };
            if(typeDepthMap[value] !== undefined) {
                // If changing to 'unit', and current depth is < 2, force level 2
                // If changing to 'section', force level 1
                // If changing to 'paper', force level 0
                syllabusData[index].depth = typeDepthMap[value];
            }
        }
        
        // Auto-calculation for Unit types: QTY * EACH = MARKS
        if(syllabusData[index].type === 'unit') {
            if(field === 'qCount' || field === 'qEach') {
                const count = parseFloat(syllabusData[index].qCount) || 0;
                const each = parseFloat(syllabusData[index].qEach) || 0;
                syllabusData[index].weight = count * each;
            }
        }
        
        recalculateTotals();
        renderGrid();
    }

    /**
     * Enterprise Intelligent Recalculation
     * Aggregates totals from Topics up to Sections and Phases.
     */
    function recalculateTotals() {
        for (let i = syllabusData.length - 1; i >= 0; i--) {
            const row = syllabusData[i];
            
            // Phases and Sections are read-only containers for metrics
            if (row.type !== 'unit') {
                let totalQty = 0;
                let totalMarks = 0;
                let totalTime = 0;
                let totalOptional = 0;
                
                // Scan descendants
                for (let j = i + 1; j < syllabusData.length; j++) {
                    // Stop if we hit a row at the same or higher (parent) level
                    if (syllabusData[j].depth <= row.depth) break;
                    
                    // Only sum up Topic (unit) values to avoid double counting nested groups
                    // (Phase sums its Topics, Section sums its Topics)
                    // Actually, if we want Section to show sum of its Topics, 
                    // and Phase to show sum of its Sections (which already have Topic sums),
                    // we should sum DIRECT children.
                    
                    // But wait, the flat array might have nesting like: Section -> Unit, Unit.
                    // Summing direct descendants that are units works.
                    if (syllabusData[j].type === 'unit') {
                        // Check if this Unit belongs to this specific parent i
                        // (i.e., no other Parent of the same depth as i exists between i and j)
                        let isDirectDescendant = true;
                        for (let k = i + 1; k < j; k++) {
                            if (syllabusData[k].depth === row.depth) {
                                isDirectDescendant = false;
                                break;
                            }
                        }
                        
                        if (isDirectDescendant) {
                            totalQty += parseFloat(syllabusData[j].qCount) || 0;
                            totalMarks += parseFloat(syllabusData[j].weight) || 0;
                            totalTime += parseFloat(syllabusData[j].time) || 0;
                            totalOptional += parseFloat(syllabusData[j].qOptional) || 0;
                        }
                    }
                }
                
                row.qCount = totalQty;
                row.weight = totalMarks;
                row.time = totalTime;
                row.qOptional = totalOptional;
            }
        }
    }

    function changeDepth(index, change) {
        let newDepth = syllabusData[index].depth + change;
        if(newDepth >= 0 && newDepth <= 5) { // Support deeper nesting
            syllabusData[index].depth = newDepth;
            
            // Auto-adjust node type based on indentation (Auto-Hierarchy Rule)
            // Phase is strictly Level 0
            if (newDepth === 0) syllabusData[index].type = 'paper';
            // Section is strictly Level 1
            else if (newDepth === 1) syllabusData[index].type = 'section';
            // Valid units can be Level 2 or deeper
            else syllabusData[index].type = 'unit';
            
            recalculateTotals();
            renderGrid();
        }
    }

    function loadSubEngineerTemplate() {
        Swal.fire({
            title: 'Sub-Engineer Template',
            text: "This will clear your current grid and load the standard Sub-Engineer Syllabus (Paper I, II, III). Proceed?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1e293b',
            cancelButtonColor: '#f1f5f9',
            confirmButtonText: 'Yes, Load Template',
            customClass: { confirmButton: 'text-white', cancelButton: 'text-slate-600' }
        }).then((result) => {
            if (result.isConfirmed) {
                syllabusData = [
                    // PAPER I
                    { id: 'p1', title: "Paper I: General Subject (MCQ)", type: "paper", depth: 0, weight: 100, time: 45, qCount: 100, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "any", selected: false },
                    { id: 's1', title: "Part I: Gen. Awareness & Reasoning", type: "section", depth: 1, weight: 20, time: 0, qCount: 20, qOptional: 0, qEach: 1, selected: false },
                    { id: 'u1', title: "General Awareness", type: "unit", depth: 2, weight: 10, time: 0, qCount: 10, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "medium", selected: false },
                    { id: 'u2', title: "Public Management & Reasoning", type: "unit", depth: 2, weight: 10, time: 0, qCount: 10, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "medium", selected: false },
                    { id: 's2', title: "Part II: General Technical Subject", type: "section", depth: 1, weight: 80, time: 0, qCount: 80, qOptional: 0, qEach: 1, selected: false },
                    { id: 'u3', title: "Surveying", type: "unit", depth: 2, weight: 10, time: 0, qCount: 10, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "medium", selected: false },
                    { id: 'u4', title: "Construction Materials", type: "unit", depth: 2, weight: 10, time: 0, qCount: 10, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "medium", selected: false },
                    { id: 'u5', title: "Soil Mechanics", type: "unit", depth: 2, weight: 10, time: 0, qCount: 10, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "medium", selected: false },
                    { id: 'u6', title: "Hydraulics", type: "unit", depth: 2, weight: 10, time: 0, qCount: 10, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "medium", selected: false },
                    { id: 'u7', title: "Structural Engineering", type: "unit", depth: 2, weight: 10, time: 0, qCount: 10, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "medium", selected: false },
                    { id: 'u8', title: "Water Supply & Sanitary", type: "unit", depth: 2, weight: 10, time: 0, qCount: 10, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "medium", selected: false },
                    { id: 'u9', title: "Highway Engineering", type: "unit", depth: 2, weight: 10, time: 0, qCount: 10, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "medium", selected: false },
                    { id: 'u10', title: "Estimating & Costing", type: "unit", depth: 2, weight: 10, time: 0, qCount: 10, qOptional: 0, qEach: 1, qType: "mcq", difficulty: "medium", selected: false },
                    
                    // PAPER II
                    { id: 'p2', title: "Paper II: Technical Subjective (Theoretical)", type: "paper", depth: 0, weight: 100, time: 135, qCount: 14, qOptional: 0, qEach: 0, qType: "subjective", difficulty: "any", selected: false },
                    { id: 's3', title: "Section A: Structures", type: "section", depth: 1, weight: 20, time: 0, qCount: 2, qOptional: 0, qEach: 10, selected: false },
                    { id: 'u11', title: "Structural Analysis (Subjective)", type: "unit", depth: 2, weight: 20, time: 0, qCount: 2, qOptional: 0, qEach: 10, qType: "subjective", difficulty: "hard", selected: false },
                    { id: 's4', title: "Section B: Water Resources", type: "section", depth: 1, weight: 20, time: 0, qCount: 2, qOptional: 0, qEach: 10, selected: false },
                    { id: 'u12', title: "Irrigation Engineering", type: "unit", depth: 2, weight: 20, time: 0, qCount: 2, qOptional: 0, qEach: 10, qType: "subjective", difficulty: "hard", selected: false },
                    { id: 's5', title: "Section C: Transportation", type: "section", depth: 1, weight: 20, time: 0, qCount: 2, qOptional: 0, qEach: 10, selected: false },
                    { id: 'u13', title: "Highway Design", type: "unit", depth: 2, weight: 20, time: 0, qCount: 2, qOptional: 0, qEach: 10, qType: "subjective", difficulty: "hard", selected: false },
                    { id: 's6', title: "Section D: Public Health", type: "section", depth: 1, weight: 20, time: 0, qCount: 2, qOptional: 0, qEach: 10, selected: false },
                    { id: 'u14', title: "Sanitary Engineering", type: "unit", depth: 2, weight: 20, time: 0, qCount: 2, qOptional: 0, qEach: 10, qType: "subjective", difficulty: "hard", selected: false },
                    { id: 's7', title: "Section E: Management", type: "section", depth: 1, weight: 20, time: 0, qCount: 2, qOptional: 0, qEach: 10, selected: false },
                    { id: 'u15', title: "Project Planning", type: "unit", depth: 2, weight: 20, time: 0, qCount: 2, qOptional: 0, qEach: 10, qType: "subjective", difficulty: "hard", selected: false },
                    
                    // PHASE III
                    { id: 'p3', title: "Phase III: Interview", type: "paper", depth: 0, weight: 30, time: 30, qCount: 1, qOptional: 0, qEach: 30, qType: "oral", difficulty: "medium", selected: false },
                    { id: 'u16', title: "Personal Interview", type: "unit", depth: 1, weight: 30, time: 30, qCount: 1, qOptional: 0, qEach: 30, qType: "oral", difficulty: "medium", selected: false }
                ];
                
                // Update global metrics from images
                document.getElementById('global-marks-input').value = 230;
                document.getElementById('global-time-input').value = "3h 30m";
                document.getElementById('global-pass-display').innerText = 40;
                document.getElementById('global-pass-input').value = 40;
                
                recalculateTotals();
                renderGrid();
                Swal.fire('Loaded!', 'Standard Sub-Engineer curriculum has been generated.', 'success');
            }
        });
    }

    function addTopic() {
        syllabusData.push({ id: Date.now(), title: "New Syllabus Item", type: "unit", depth: 2, weight: 0, time: 0, qCount: 0, qOptional: 0, qEach: 0, selected: false });
        renderGrid();
    }

    function deleteRow(index) {
        syllabusData.splice(index, 1);
        renderGrid();
    }

    function handleRowDuplicate(index) {
        const clone = {...syllabusData[index], id: Date.now(), title: syllabusData[index].title + ' (Copy)'};
        syllabusData.splice(index + 1, 0, clone);
        renderGrid();
    }

    // --- BULK & DRAG ---
    function setupDragEvents(el, index) {
        el.addEventListener('dragstart', (e) => { draggedItemIndex = index; e.dataTransfer.effectAllowed = 'move'; });
        el.addEventListener('dragover', (e) => e.preventDefault());
        el.addEventListener('drop', (e) => {
            e.preventDefault();
            if(draggedItemIndex !== null && draggedItemIndex !== index) {
                const item = syllabusData.splice(draggedItemIndex, 1)[0];
                syllabusData.splice(index, 0, item);
                draggedItemIndex = null;
                renderGrid();
            }
        });
    }

    function toggleSelectAll() {
        const checked = document.getElementById('select-all').checked;
        syllabusData.forEach(r => r.selected = checked);
        renderGrid();
    }
    
    function toggleRowSelection(index, checked) {
        syllabusData[index].selected = checked;
        updateBulkBar();
    }

    function updateBulkBar() {
        const count = syllabusData.filter(r => r.selected).length;
        const bar = document.getElementById('bulk-action-bar');
        document.getElementById('selected-count').innerText = count;
        if(count > 0) {
            bar.classList.remove('opacity-0', 'invisible', 'translate-y-[200%]');
            bar.classList.add('translate-y-0');
        } else {
            bar.classList.add('opacity-0', 'invisible', 'translate-y-[200%]');
            bar.classList.remove('translate-y-0');
        }
    }

    function bulkDuplicate() {
        const selected = syllabusData.filter(r => r.selected).map(r => ({...r, id: Date.now() + Math.random(), selected: false, title: r.title + ' (Copy)'}));
        syllabusData.push(...selected);
        clearSelection();
    }

    function bulkIndent(dir) {
        syllabusData.forEach(r => {
            if(r.selected) {
                let d = r.depth + dir;
                if(d >= 0 && d <= 3) r.depth = d;
            }
        });
        renderGrid();
    }

    function bulkDelete() {
        syllabusData = syllabusData.filter(r => !r.selected);
        clearSelection();
    }

    function clearSelection() {
        syllabusData.forEach(r => r.selected = false);
        const selectAll = document.getElementById('select-all');
        if(selectAll) selectAll.checked = false;
        renderGrid();
    }

    // --- VALIDATION & SAVING ---
    function updateGlobalLevel(value) {
        currentLevel = value.trim() || 'New Syllabus';
    }

    function updateGlobalSetting(type, value) {
        if(value.trim() === '') value = null;
        manualSettings[type] = value;
        validateSyllabus();
    }

    function validateSyllabus() {
        const roots = syllabusData.filter(r => r.depth === 0);
        const autoTotal = roots.reduce((s, r) => s + r.weight, 0);
        const unitSum = syllabusData.filter(r => r.type === 'unit').reduce((s, r) => s + r.weight, 0);
        
        const target = manualSettings.marks ? parseFloat(manualSettings.marks) : autoTotal;
        const badge = document.getElementById('validator-msg');
        const tallyDisplay = document.getElementById('tally-display');
        
        tallyDisplay.innerText = `${Math.round(unitSum)}/${Math.round(target)}`;
        
        if(Math.abs(unitSum - target) < 0.01) {
            badge.className = 'flex-shrink-0 bg-emerald-50 text-emerald-900 border border-emerald-200 px-3 py-1.5 rounded-lg text-[11px] font-bold shadow-sm flex items-center';
        } else {
            badge.className = 'flex-shrink-0 bg-rose-50 text-rose-900 border border-rose-200 px-3 py-1.5 rounded-lg text-[11px] font-bold shadow-sm flex items-center';
        }
    }

    function saveSyllabus() {
        // Validation: Must have at least one node
        if(syllabusData.length === 0) {
            Swal.fire('Error', 'Syllabus cannot be empty.', 'error');
            return;
        }

        Swal.fire({
            title: 'Saving...',
            didOpen: () => { Swal.showLoading(); }
        });

        const payload = {
            level: "<?php echo $level; ?>",
            nodes: syllabusData,
            settings: {
                time: document.getElementById('global-time-input').value,
                marks: document.getElementById('global-marks-input').value,
                pass: document.getElementById('global-pass-input').value,
                negValue: document.getElementById('global-neg-input').value
            }
        };

        fetch('<?php echo get_app_url(); ?>/admin/quiz/syllabus/bulk-save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to save', 'error');
            }
        });
    }

    async function openCloneModal() {
        const { value: newLabel } = await Swal.fire({
            title: 'Clone Syllabus',
            text: 'Enter a version label to identify this new clone (e.g., "Revision 2025")',
            input: 'text',
            inputValue: "Revision " + new Date().getFullYear(),
            showCancelButton: true,
            confirmButtonText: 'Create Clone',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) return 'Version label is required!';
            }
        });

        if (!newLabel) return;

        const currentLevel = "<?php echo $level; ?>";
        
        Swal.fire({
            title: 'Creating Clone...',
            text: 'Please wait while we duplicate the structure and settings.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('<?php echo get_app_url(); ?>/admin/quiz/syllabus/clone', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                level: currentLevel,
                version_label: newLabel
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Syllabus Cloned!',
                    text: 'Redirecting to the new version...',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '<?php echo get_app_url(); ?>/admin/quiz/syllabus/manage/' + data.new_level;
                });
            } else {
                Swal.fire('Error', data.message || 'Clone failed', 'error');
            }
        });
    }

    // --- HIERARCHY MODAL LOGIC ---
    let activeRowIndex = null;
    let activeTab = 'category';
    const categoriesDB = <?php echo !empty($categories) ? json_encode($categories) : '[]'; ?>;
    const subjectsDB = <?php echo !empty($subjects) ? json_encode($subjects) : '[]'; ?>;
    let showLinkedOnly = false;

    function openHierarchyModal(index) {
        activeRowIndex = index;
        const row = syllabusData[index];
        
        // Reset 5 Filters
        document.getElementById('modal-filter-course').value = '';
        document.getElementById('modal-filter-edu').value = '';
        document.getElementById('modal-filter-pos').value = '';
        document.getElementById('modal-filter-cat').value = '';
        document.getElementById('modal-filter-topic').value = '';
        document.getElementById('hierarchy-search').value = '';
        
        // Reset Dropdown UI Text
        resetDropdownUI('dropdown-course', 'Course');
        resetDropdownUI('dropdown-edu', 'Edu. Level');
        resetDropdownUI('dropdown-cat', 'Category');
        resetDropdownUI('dropdown-topic', 'Sub-Cat');
        resetDropdownUI('dropdown-pos', 'Pos. Level');

        showLinkedOnly = false;
        updateLinkedBtnState();

        if (row.linked_topic_id) switchHierarchyTab('topic');
        else switchHierarchyTab('category');
        
        document.getElementById('hierarchy-modal').classList.remove('hidden');
        filterHierarchyList();
        setTimeout(() => document.getElementById('hierarchy-search').focus(), 50);
    }

    function closeHierarchyModal() {
        document.getElementById('hierarchy-modal').classList.add('hidden');
        activeRowIndex = null;
    }

    function switchHierarchyTab(tab) {
        currentHierarchyTab = tab;
        document.querySelectorAll('.h-tab').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-600');
        });
        const activeBtn = document.querySelector(`.h-tab[data-tab="${tab}"]`); // Changed selector to data-tab
        if (activeBtn) {
            activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
            activeBtn.classList.remove('bg-gray-100', 'text-gray-600');
        }

        // Keep all 5 filters visible to maintain the 5-column grid layout
        // but maybe disable the Category filter when on the Categories tab
        const catFilter = document.getElementById('modal-filter-cat');
        if (catFilter) {
            if (tab === 'category') { // Changed 'categories' to 'category' to match the tab name
                catFilter.disabled = true;
                catFilter.classList.add('opacity-50', 'bg-gray-50');
                catFilter.value = ""; // Reset it
            } else {
                catFilter.disabled = false;
                catFilter.classList.remove('opacity-50', 'bg-gray-50');
            }
        }

        filterHierarchyList();
    }

    function filterHierarchyList() {
        const query = document.getElementById('hierarchy-search').value.toLowerCase();
        // Filters not applied to list items directly due to data simplification, but we keep Search and Linked logic.
        const catId = document.getElementById('modal-filter-cat').value;
        const topicId = document.getElementById('modal-filter-topic').value;
        
        const container = document.getElementById('hierarchy-list-container');
        if (!container) return;
        container.innerHTML = '';
        
        const curTab = typeof currentHierarchyTab !== 'undefined' ? currentHierarchyTab : 'category';
        const sourceData = curTab === 'category' ? categoriesDB : subjectsDB;
        const typeLabel = curTab === 'category' ? 'Category' : 'Sub-Category';
        const linkKey = curTab === 'category' ? 'linked_category_id' : 'linked_topic_id';
        
        // Active Row for Linking Check
        const row = syllabusData[activeRowIndex];

        const matches = sourceData.filter(item => {
            // SHOW LINKED ONLY LOGIC
            if (showLinkedOnly) {
                if (!row) return false;
                if (row[linkKey] != item.id) return false;
            }

            if (query && !item.name.toLowerCase().includes(query)) return false;
            
            // For topics, filter by category if catId is set.
            if (curTab === 'topic' && catId && item.category_id != catId && item.category_id !== null) return false;
            
            return true;
        });

        if (matches.length === 0) {
            container.innerHTML = `<div class="p-8 text-center text-slate-400 italic">No items found matching criteria</div>`;
            return;
        }

        matches.forEach(item => {
            const isSelected = row && row[linkKey] == item.id;
            const el = document.createElement('div');
            el.className = `flex items-center justify-between p-4 mb-2 rounded-xl cursor-pointer transition-all duration-200 group ${isSelected ? 'bg-blue-600 shadow-lg shadow-blue-100' : 'bg-white border border-slate-100 hover:border-blue-300 hover:shadow-md'}`;
            
            const textClass = isSelected ? 'text-white' : 'text-slate-700';
            const labelClass = isSelected ? 'bg-blue-500/50 text-blue-100' : 'bg-slate-100 text-slate-500';
            const parentName = curTab === 'topic' ? item.category_name : item.parent_name;

            el.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg ${isSelected ? 'bg-white/20' : 'bg-blue-50 text-blue-500'}">
                        <i class="fas ${curTab === 'category' ? 'fa-layer-group' : 'fa-tag'} text-xs"></i>
                    </div>
                    <div>
                        <div class="font-bold text-sm ${textClass}">${item.name}</div>
                        ${parentName ? `<div class="text-[10px] opacity-70 ${textClass}">${parentName}</div>` : ''}
                    </div>
                </div>
                <div class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-widest ${labelClass}">${typeLabel}</div>
            `;
            el.onclick = () => selectHierarchyItem(curTab, item.id, item.name);
            container.appendChild(el);
        });
    }

    function selectHierarchyItem(type, id, name) {
        if (activeRowIndex === null) return;
        syllabusData[activeRowIndex].linked_category_id = null;
        syllabusData[activeRowIndex].category_name = null;
        syllabusData[activeRowIndex].linked_topic_id = null;
        syllabusData[activeRowIndex].topic_name = null;
        syllabusData[activeRowIndex].linked_subject_id = null;

        if (type === 'category') {
            syllabusData[activeRowIndex].linked_category_id = id;
            syllabusData[activeRowIndex].category_name = name;
        } else {
            syllabusData[activeRowIndex].linked_topic_id = id;
            syllabusData[activeRowIndex].topic_name = name;
        }
        renderGrid();
        closeHierarchyModal();
    }

    function removeLink() {
        if (activeRowIndex === null) return;
        syllabusData[activeRowIndex].linked_category_id = null;
        syllabusData[activeRowIndex].category_name = null;
        syllabusData[activeRowIndex].linked_topic_id = null;
        syllabusData[activeRowIndex].topic_name = null;
        renderGrid();
        closeHierarchyModal();
    }

    function toggleLinkedOnly() {
        showLinkedOnly = !showLinkedOnly;
        updateLinkedBtnState();
        filterHierarchyList();
    }
    
    function updateLinkedBtnState() {
        const btn = document.getElementById('btn-show-linked');
        if (!btn) return;
        if (showLinkedOnly) {
            btn.classList.add('text-blue-600', 'bg-blue-50', 'border-blue-100');
            btn.classList.remove('text-slate-400', 'border-transparent');
        } else {
            btn.classList.remove('text-blue-600', 'bg-blue-50', 'border-blue-100');
            btn.classList.add('text-slate-400', 'border-transparent');
        }
    }
    
    function resetHierarchyFilters() {
        document.getElementById('modal-filter-course').value = '';
        document.getElementById('modal-filter-edu').value = '';
        document.getElementById('modal-filter-pos').value = '';
        document.getElementById('modal-filter-cat').value = '';
        document.getElementById('modal-filter-topic').value = '';
        
        resetDropdownUI('dropdown-course', 'Course');
        resetDropdownUI('dropdown-edu', 'Edu. Level');
        resetDropdownUI('dropdown-cat', 'Category');
        resetDropdownUI('dropdown-topic', 'Sub-Cat');
        resetDropdownUI('dropdown-pos', 'Pos. Level');
        
        filterHierarchyList();
    }
    
    function resetDropdownUI(id, label) {
        const el = document.getElementById(id);
        if(el) {
            const labelEl = el.querySelector('.dropdown-label');
            if(labelEl) labelEl.innerText = label;
        }
    }
    
    function applyFilters() { 
        filterHierarchyList(); 
    }

    // --- SEARCHABLE DROPDOWNS LOGIC ---
    function initCustomDropdowns() {
        populateDropdown('course', coursesData, 'id', 'title');
        populateDropdown('edu', eduData, 'id', 'title');
        populateDropdown('pos', posData, 'id', 'title');
        
        // Categories & Topics might be large, limit initial render or render all?
        // Rendering all for now as browser handles 1-2k nodes okay usually.
        populateDropdown('cat', categoriesDB, 'id', 'name');
        populateDropdown('topic', subjectsDB, 'id', 'name');
        
        initBodyClick();
    }

    function populateDropdown_OLD(type, data, idKey, titleKey) {
        const list = document.getElementById(`list-${type}`);
        if (!list) return;
        
        let html = `<li class="px-3 py-2 text-xs text-slate-500 hover:bg-slate-50 cursor-pointer" onclick="selectCustomOption('${type}', '', 'Filter ${type.charAt(0).toUpperCase() + type.slice(1)}')">All ${type.charAt(0).toUpperCase() + type.slice(1)}s</li>`;
        
        if (data.length === 0) {
            html += `<li class="px-3 py-2 text-xs text-slate-400 italic cursor-default">No items found</li>`;
        }

        list.innerHTML = html;
        
        data.forEach(item => {
            const li = document.createElement('li');
            li.className = 'px-3 py-2 text-xs text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-50 last:border-0 whitespace-nowrap transition-colors';
            li.innerText = item[titleKey];
            li.dataset.val = item[idKey];
            
            // Store hierarchy data for cascading (optional visual dimming)
            if(type === 'pos') {
                li.dataset.course = item.course_id;
                li.dataset.edu = item.education_level_id;
            }
            if(type === 'cat') li.dataset.pos = item.position_level_id;
            if(type === 'topic') li.dataset.cat = item.category_id;

            li.onclick = () => selectCustomOption(type, item[idKey], item[titleKey]);
            list.appendChild(li);
        });
    }

    function toggleCustomDropdown(type) {
        const dd = document.querySelector(`#dropdown-${type} .dropdown-menu`);
        const isOpen = !dd.classList.contains('hidden');
        
        // Close all others
        document.querySelectorAll('.dropdown-menu').forEach(el => el.classList.add('hidden'));
        
        if (!isOpen) {
            dd.classList.remove('hidden');
            // Auto focus search
            setTimeout(() => {
                const input = dd.querySelector('.dropdown-search');
                if(input) input.focus();
            }, 50);
        }
    }

    function filterCustomDropdown(type, query) {
        const filter = query.toLowerCase();
        const list = document.getElementById(`list-${type}`);
        const items = list.getElementsByTagName('li');
        
        for (let i = 1; i < items.length; i++) { // Skip first "All" option
            const txt = items[i].textContent || items[i].innerText;
            if (txt.toLowerCase().indexOf(filter) > -1) {
                items[i].style.display = "";
            } else {
                items[i].style.display = "none";
            }
        }
    }

    function selectCustomOption_OLD(type, value, name) {
        // Update Hidden Input
        document.getElementById(`modal-filter-${type}`).value = value;
        
        // Update Trigger Label
        const trigger = document.querySelector(`#dropdown-${type} .dropdown-trigger span`);
        if(trigger) trigger.innerText = name;
        
        // Close Dropdown
        document.querySelector(`#dropdown-${type} .dropdown-menu`).classList.add('hidden');
        
        // Trigger Main Filter
        filterHierarchyList();
    }

    // --- NEW LOGIC FOR CASCADING & SAVING ---

    function populateDropdown(type, data, idKey, titleKey) {
        const list = document.getElementById(`list-${type}`);
        if (!list) return;
        
        let html = `<li class="px-3 py-2 text-xs text-slate-500 hover:bg-slate-50 cursor-pointer" 
                    onclick="selectCustomOption('${type}', '', 'Filter ${type.charAt(0).toUpperCase() + type.slice(1)}')"
                    data-val="">All ${type.charAt(0).toUpperCase() + type.slice(1)}s</li>`;
        
        if (data.length === 0) {
            html += `<li class="px-3 py-2 text-xs text-slate-400 italic cursor-default">No items found</li>`;
        }

        list.innerHTML = html;
        
        data.forEach(item => {
            const li = document.createElement('li');
            li.className = 'px-3 py-2 text-xs text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-50 last:border-0 whitespace-nowrap transition-colors';
            li.innerText = item[titleKey];
            li.dataset.val = item[idKey];
            
            // Store Hierarchy IDs for Cascading Logic
            if(type === 'edu') {
                li.dataset.course = item.parent_id;
            }
            if(type === 'cat') {
                li.dataset.edu = item.edu_level_id;
                li.dataset.course = item.course_id;
            }
            if(type === 'topic') {
                li.dataset.cat = item.category_id;
                li.dataset.edu = item.edu_level_id;
                li.dataset.course = item.course_id;
            }
            if(type === 'pos') {
                li.dataset.course = item.course_id;
                li.dataset.edu = item.education_level_id;
            }

            li.onclick = () => selectCustomOption(type, item[idKey], item[titleKey]);
            list.appendChild(li);
        });
    }

    function selectCustomOption(type, value, name) {
        // Update Hidden Input
        document.getElementById(`modal-filter-${type}`).value = value;
        
        // Update Trigger Label
        const trigger = document.querySelector(`#dropdown-${type} .dropdown-trigger span`);
        if(trigger) trigger.innerText = name;
        
        // CASCADING RESET LOGIC
        const resetChild = (childType, defaultLabel) => {
             const input = document.getElementById(`modal-filter-${childType}`);
             if(input) input.value = '';
             resetDropdownUI(`dropdown-${childType}`, defaultLabel);
        };

        if(type === 'course') {
            resetChild('edu', 'Edu. Level');
            resetChild('cat', 'Category');
            resetChild('topic', 'Sub-Cat');
            resetChild('pos', 'Pos. Level');
        } else if (type === 'edu') {
            resetChild('cat', 'Category');
            resetChild('topic', 'Sub-Cat');
            resetChild('pos', 'Pos. Level');
        } else if (type === 'cat') {
            resetChild('topic', 'Sub-Cat');
        }

        // Apply Cascading Visibility
        updateCascadingFilters();
        
        // Close Dropdown
        document.querySelector(`#dropdown-${type} .dropdown-menu`).classList.add('hidden');
        
        // Trigger Main Filter List
        filterHierarchyList();
    }
    
    function updateCascadingFilters() {
        const courseId = document.getElementById('modal-filter-course').value;
        const eduId = document.getElementById('modal-filter-edu').value;
        const catId = document.getElementById('modal-filter-cat').value;

        const applyVisibility = (listId, predicate) => {
            const list = document.getElementById(listId);
            if(!list) return;
            const items = list.querySelectorAll('li');
            items.forEach(li => {
                if(li.dataset.val === '') return; 
                li.style.display = predicate(li) ? '' : 'none';
            });
        };

        applyVisibility('list-edu', (li) => {
            if (courseId && li.dataset.course && li.dataset.course != courseId) return false;
            return true;
        });

        applyVisibility('list-cat', (li) => {
            if (courseId && li.dataset.course && li.dataset.course != courseId) return false;
            if (eduId && li.dataset.edu && li.dataset.edu != eduId) return false;
            return true;
        });

        applyVisibility('list-topic', (li) => {
            if (courseId && li.dataset.course && li.dataset.course != courseId) return false;
            if (eduId && li.dataset.edu && li.dataset.edu != eduId) return false;
            if (catId && li.dataset.cat && li.dataset.cat != catId) return false;
            return true;
        });

        applyVisibility('list-pos', (li) => {
            if (courseId && li.dataset.course && li.dataset.course != courseId) return false;
            if (eduId && li.dataset.edu && li.dataset.edu != eduId) return false;
            return true;
        });
    }

    function saveLinkFromFilter() {
        if(activeRowIndex === null || !syllabusData[activeRowIndex]) return;
        const curTab = currentHierarchyTab;
        
        if (curTab === 'category') {
            const catId = document.getElementById('modal-filter-cat').value;
            if(catId) {
                const li = document.querySelector(`#list-cat li[data-val="${catId}"]`);
                const name = li ? li.innerText : 'Unknown';
                selectHierarchyItem('category', catId, name);
            } else {
                alert("Please select a Category from the dropdown first.");
            }
        } else {
            const topicId = document.getElementById('modal-filter-topic').value;
            if(topicId) {
                 const li = document.querySelector(`#list-topic li[data-val="${topicId}"]`);
                 const name = li ? li.innerText : 'Unknown';
                 selectHierarchyItem('topic', topicId, name);
            } else {
                 alert("Please select a Sub-Category from the dropdown first.");
            }
        }
    }

    function filterHierarchyList() {
        const query = document.getElementById('hierarchy-search').value.toLowerCase();
        const curTab = currentHierarchyTab; // global
        const sourceData = curTab === 'category' ? categoriesDB : subjectsDB;
        const linkKey = curTab === 'category' ? 'linked_category_id' : 'linked_topic_id';
        const typeLabel = curTab === 'category' ? 'Category' : 'Sub-Category';
        const row = syllabusData[activeRowIndex];
        
        // Filter Values
        const courseId = document.getElementById('modal-filter-course').value;
        const eduId = document.getElementById('modal-filter-edu').value;
        const posId = document.getElementById('modal-filter-pos').value;
        const catId = document.getElementById('modal-filter-cat').value;
        const topicId = document.getElementById('modal-filter-topic').value;
        
        const container = document.getElementById('hierarchy-list-container');
        if (!container) return;
        container.innerHTML = '';

        const matches = sourceData.filter(item => {
            // SHOW LINKED ONLY LOGIC
            if (showLinkedOnly) {
                if (!row) return false;
                if (row[linkKey] != item.id) return false;
            }

            // Search Text
            if (query && !item.name.toLowerCase().includes(query)) return false;
            
            // Course Filter
            if (courseId && item.course_id && item.course_id != courseId) return false;
            
            // Edu Filter
            if (eduId && item.edu_level_id && item.edu_level_id != eduId) return false;
            
            // Category Filter
            if (catId) {
                if (curTab === 'category') {
                     if (item.id != catId) return false;
                } else {
                     if (item.category_id && item.category_id != catId) return false;
                }
            }
            
            // Topic Filter (Only relevant for Topic Tab really, or specific item match)
            if (topicId && curTab === 'topic') {
                if (item.id != topicId) return false;
            }
            
            // Pos Level Filter (Optional, checking if Item belongs to pos level? Usually Position is Child of Sub-Cat, or orthogonal?)
            // Controller for Subjects didn't join Position.
            // But Categories check `position_level_id`?
            // Existing `manage.php` previously had no Pos logic, except disabled code.
            // I'll skip Pos logic for List Filtering unless data supports it.
            // (Controller does NOT return position_level_id for categories/subjects).
            
            return true;
        });

        if (matches.length === 0) {
            container.innerHTML = `<div class="p-8 text-center text-slate-400 italic">No items found matching criteria</div>`;
            return;
        }

        matches.forEach(item => {
            const isSelected = row && row[linkKey] == item.id;
            const el = document.createElement('div');
            el.className = `flex items-center justify-between p-4 mb-2 rounded-xl cursor-pointer transition-all duration-200 group ${isSelected ? 'bg-blue-600 shadow-lg shadow-blue-100' : 'bg-white border border-slate-100 hover:border-blue-300 hover:shadow-md'}`;
            
            const textClass = isSelected ? 'text-white' : 'text-slate-700';
            const labelClass = isSelected ? 'bg-blue-500/50 text-blue-100' : 'bg-slate-100 text-slate-500';
            const parentName = curTab === 'topic' ? item.category_name : (item.edu_level_id ? 'Edu Level ' + item.edu_level_id : ''); 
            // Better parent name? Categories have edu_level_id. Subjects have category_name.
            // For Categories, we don't have edu level NAME, just ID.
            // We can leave parent name empty for Categories or show ID. User didn't ask.

            el.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg ${isSelected ? 'bg-white/20' : 'bg-blue-50 text-blue-500'}">
                        <i class="fas ${curTab === 'category' ? 'fa-layer-group' : 'fa-tag'} text-xs"></i>
                    </div>
                    <div>
                        <div class="font-bold text-sm ${textClass}">${item.name}</div>
                        ${curTab === 'topic' && item.category_name ? `<div class="text-[10px] opacity-70 ${textClass}">${item.category_name}</div>` : ''}
                    </div>
                </div>
                <div class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-widest ${labelClass}">${typeLabel}</div>
            `;
            el.onclick = () => selectHierarchyItem(curTab, item.id, item.name);
            container.appendChild(el);
        });
    }

    document.addEventListener('DOMContentLoaded', () => { initSettings(); renderGrid(); initCustomDropdowns(); });
</script>