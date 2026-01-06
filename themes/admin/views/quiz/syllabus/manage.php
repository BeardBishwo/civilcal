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
            'qEach' => (float)($node['questions_weight'] > 0 ? ($node['questions_weight'] / ($node['question_count'] ?: 1)) : 0),
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
                        <!-- Pass -->
                        <div class="flex-shrink-0 bg-emerald-50 text-emerald-900 border border-emerald-200 px-3 py-1.5 rounded-lg text-[11px] font-bold shadow-sm flex items-center">
                            <i class="fas fa-flag mr-2 text-emerald-500"></i> <span class="text-emerald-700/70 mr-1 uppercase tracking-wide">Pass:</span>
                            <input type="text" id="global-pass-input" class="header-input text-emerald-900 w-8 bg-transparent border-b border-emerald-300 text-center focus:outline-none font-bold" placeholder="40" onblur="updateGlobalSetting('pass', this.value)">
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

                    <!-- Actions -->
                    <div class="flex items-center gap-2 shrink-0 print:hidden">
                        <button onclick="addTopic()" class="h-9 px-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-bold transition flex items-center shadow-sm">
                            <i class="fas fa-plus mr-2 text-blue-500"></i> Row
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
                        <div class="grid-header border-r border-slate-200"><input type="checkbox" id="select-all" class="w-4 h-4 rounded cursor-pointer" onclick="toggleSelectAll()"></div>
                        <div class="grid-header border-r border-slate-200" title="Drag to Reorder"><i class="fas fa-arrows-alt text-slate-400"></i></div>
                        <div class="grid-header border-r border-slate-200">Lvl</div>
                        <div class="grid-header text-left pl-4 border-r border-slate-200">Topic / Title</div>
                        <div class="grid-header border-r border-slate-200">Time (m)</div>
                        <div class="grid-header border-r border-slate-200">Node Type</div>
                        <div class="grid-header border-r border-slate-200">Qty</div>
                        <div class="grid-header border-r border-slate-200" title="Optional Questions">Opt</div>
                        <div class="grid-header border-r border-slate-200">Each</div>
                        <div class="grid-header border-r border-slate-200">Marks</div>
                        <div class="grid-header border-r border-slate-200 text-center">Hierarchy</div>
                        <div class="grid-header border-r border-slate-200">Actions</div>
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

    <!-- === HIERARCHY SELECTION MODAL === -->
    <div id="hierarchy-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeHierarchyModal()"></div>
        <div class="fixed top-[70px] left-[280px] right-0 bottom-0 flex items-center justify-center p-8">
            <div class="bg-white rounded-2xl shadow-2xl w-full h-full overflow-hidden transform transition-all relative flex flex-col ring-1 ring-black/5">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-slate-800">Link Hierarchy</h3>
                    <div class="flex items-center gap-3">
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
                        <div class="absolute top-full left-0 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 hidden dropdown-menu origin-top transform transition-all duration-200 max-h-96">
                             <div class="p-2 border-b border-slate-100 sticky top-0 bg-white rounded-t-xl z-10">
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
                        <div class="absolute top-full left-0 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 hidden dropdown-menu origin-top transform transition-all duration-200 max-h-96">
                             <div class="p-2 border-b border-slate-100 sticky top-0 bg-white rounded-t-xl z-10">
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
                        <div class="absolute top-full left-0 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 hidden dropdown-menu origin-top transform transition-all duration-200 max-h-96">
                             <div class="p-2 border-b border-slate-100 sticky top-0 bg-white rounded-t-xl z-10">
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
                        <div class="absolute top-full left-0 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 hidden dropdown-menu origin-top transform transition-all duration-200 max-h-96">
                             <div class="p-2 border-b border-slate-100 sticky top-0 bg-white rounded-t-xl z-10">
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
                        <div class="absolute top-full left-0 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 hidden dropdown-menu origin-top transform transition-all duration-200 max-h-96">
                             <div class="p-2 border-b border-slate-100 sticky top-0 bg-white rounded-t-xl z-10">
                                <input type="text" class="w-full text-xs px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 dropdown-search" placeholder="Search..." onkeyup="filterCustomDropdown('pos', this.value)">
                             </div>
                             <ul class="max-h-80 overflow-y-auto py-1 custom-scrollbar dropdown-list" id="list-pos">
                                <!-- JS Populated -->
                             </ul>
                        </div>
                         <input type="hidden" id="modal-filter-pos" value="">
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
    /* Premium Grid Styles */
    .syllabus-grid {
        display: grid;
            grid-template-columns: 40px 40px 35px 1fr 85px 120px 60px 60px 60px 75px 75px 80px 180px;
        background-color: #f1f5f9;
        min-width: 1400px;
    }
    .grid-header {
        background-color: #f8fafc; color: #64748b; font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 2px; display: flex; align-items: center; justify-content: center;
    }
    .grid-row { background-color: white; border-bottom: 1px solid #f1f5f9; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .grid-row:hover { background-color: #f8fafc; }
    
    /* Phase/Level 0 Row - Dark Slate from mockup */
    .grid-row[data-type="paper"] { background-color: #f1f5f9; }
    .grid-row[data-type="paper"] .grid-cell { color: #1e293b; font-weight: 800; font-size: 0.95rem; }
    .grid-row[data-type="paper"] .input-premium { background-color: #fffbeb !important; border-color: #fef3c7; color: #92400e; }
    
    .grid-row[data-type="section"] { background-color: #f8fafc; }
    .grid-row[data-type="section"] .grid-cell { font-weight: 700; color: #334155; }
    
    .grid-cell { padding: 2px 6px; display: flex; align-items: center; font-size: 0.8rem; color: #334155; position: relative; height: 32px; }
    
    .input-premium {
        width: 100%; padding: 1px 4px; border: 1px solid #e2e8f0; border-radius: 4px; text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 600; font-size: 0.75rem; transition: 0.2s;
        background-color: white; height: 24px;
    }
    .input-premium:focus { border-color: #6366f1; ring: 3px rgba(99, 102, 241, 0.1); outline: none; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
    
    .time-input { color: #b45309; }
    .qty-input { color: #059669; }
    .each-input { color: #2563eb; }
    
    .depth-0 { color: #0f172a; font-weight: 800; }
    .depth-1 { color: #1e293b; font-weight: 700; }
    .depth-2 { color: #334155; font-weight: 600; }
    .depth-3 { color: #64748b; font-weight: 500; }
    
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
        background-image: none;
        border: 1px solid transparent;
        outline: none;
        cursor: pointer;
        font-size: 9px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 9999px;
        text-align: center;
        width: 85px !important;
        transition: all 0.2s;
    }
    .type-select-styled:hover { border-color: rgba(99, 102, 241, 0.3); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    .badge-phase { background: #1e293b; color: white; }
    .badge-section { background: #e0e7ff; color: #4338ca; }
    .badge-unit { background: #f1f5f9; color: #64748b; }

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
        while(container.children.length > 13) container.removeChild(container.lastChild);

        syllabusData.forEach((row, index) => {
            // Checkbox
            container.appendChild(createCell(`<input type="checkbox" class="row-checkbox w-4 h-4 rounded" ${row.selected ? 'checked' : ''} onchange="toggleRowSelection(${index}, this.checked)">`, 'justify-center border-r border-slate-100'));
            
            // Drag Handle
            const dragCell = createCell(`<i class="fas fa-grip-vertical drag-handle"></i>`, 'justify-center border-r border-slate-100');
            dragCell.draggable = true;
            setupDragEvents(dragCell, index);
            container.appendChild(dragCell);

            // Level
            container.appendChild(createCell(row.depth, `justify-center depth-${row.depth} bg-slate-50 border-r border-slate-100 font-mono text-[10px]`));

            // Topic / Title (with depth-based styling)
            const padding = 20 + (row.depth * 32);
            let icon = 'fa-folder';
            let titleClass = '';
            let titleStyle = '';
            
            if(row.depth === 0) {
                icon = 'fa-layer-group text-white';
                titleClass = 'font-bold text-white text-xs';
                titleStyle = 'background: linear-gradient(135deg, #1e293b 0%, #334155 100%);';
            } else if(row.depth === 1) {
                icon = 'fa-folder-open text-blue-600';
                titleClass = 'font-bold text-blue-700 text-xs';
                titleStyle = 'background: #eff6ff;';
            } else {
                icon = 'fa-folder text-slate-400';
                titleClass = 'text-slate-700 text-xs';
            }
            
            container.appendChild(createCell(
                `<div class="flex items-center w-full">
                    <i class="fas ${icon} mr-3 opacity-80 text-xs"></i> 
                    <span contenteditable="true" class="w-full outline-none focus:bg-white rounded px-1 transition duration-200 ${titleClass}" onblur="updateRow(${index}, 'title', this.innerText)">${row.title}</span>
                 </div>`,
                `depth-${row.depth} border-r border-slate-100`, `padding-left: ${padding}px; ${titleStyle}`
            ));

            // Time (with formatted display)
            const timeBg = row.depth === 0 ? "bg-amber-100" : "bg-white";
            const timeDisplay = row.depth === 0 ? `<div class="font-bold text-amber-800">${formatNumber(row.time)}</div>` : `<input type="number" class="input-premium time-input ${timeBg}" value="${row.time || 0}" onchange="updateRow(${index}, 'time', this.value)">`;
            container.appendChild(createCell(timeDisplay, 'justify-center border-r border-slate-100'));

            // Node Type (Styled visible select)
            let typeColor = "bg-slate-50 text-slate-600 border border-slate-200";
            if(row.type === 'paper') typeColor = "bg-indigo-50 text-indigo-700 border border-indigo-200";
            if(row.type === 'section') typeColor = "bg-sky-50 text-sky-700 border border-sky-200";
            if(row.type === 'unit') typeColor = "bg-white text-slate-400 border border-slate-200";

            container.appendChild(createCell(`
                <select class="type-select-styled ${typeColor} text-[10px] h-6 py-0 pl-1 pr-4" onchange="updateRow(${index}, 'type', this.value)">
                    <option value="paper" ${row.type==='paper'?'selected':''}>PHASE</option>
                    <option value="section" ${row.type==='section'?'selected':''}>SECTION</option>
                    <option value="unit" ${row.type==='unit'?'selected':''}>UNIT</option>
                </select>
            `, 'justify-center border-r border-slate-100 px-1'));

            // Qty (with formatted display)
            const qtyDisabled = (row.type !== 'unit' && row.depth !== 0) ? 'disabled style="background:#f8fafc; color:#cbd5e1;"' : '';
            const qtyDisplay = (row.type !== 'unit' && row.depth !== 0) ? `<div class="text-slate-400">${formatNumber(row.qCount)}</div>` : `<input type="number" class="input-premium qty-input" value="${row.qCount || 0}" onchange="updateRow(${index}, 'qCount', this.value)">`;
            container.appendChild(createCell(qtyDisplay, 'justify-center border-r border-slate-100'));

            // Optional (new column)
            const optDisplay = (row.type !== 'unit' && row.depth !== 0) ? `<div class="text-slate-400">${row.qOptional || 0}</div>` : `<input type="number" class="input-premium opt-input bg-amber-50" value="${row.qOptional || 0}" onchange="updateRow(${index}, 'qOptional', this.value)" placeholder="0">`;
            container.appendChild(createCell(optDisplay, 'justify-center border-r border-slate-100'));

            // Each (with formatted display)
            const eachDisplay = (row.type !== 'unit' && row.depth !== 0) ? `<div class="text-slate-400">${formatNumber(row.qEach)}</div>` : `<input type="number" class="input-premium each-input" value="${row.qEach || 0}" onchange="updateRow(${index}, 'qEach', this.value)">`;
            container.appendChild(createCell(eachDisplay, 'justify-center border-r border-slate-100'));

            // Marks (with formatted display)
            const marksBg = row.depth === 0 ? "bg-slate-100" : "bg-white";
            const marksDisplay = `<div class="font-bold ${row.depth === 0 ? 'text-slate-800' : 'text-slate-700'}">${formatNumber(row.weight)}</div>`;
            container.appendChild(createCell(marksDisplay, 'justify-center border-r border-slate-100'));

            // Hierarchy
            container.appendChild(createCell(`
                <div class="flex gap-1.5 justify-center w-full">
                    <button onclick="changeDepth(${index}, -1)" class="hierarchy-btn" title="Outdent"><i class="fas fa-chevron-left text-[10px]"></i></button>
                    <button onclick="changeDepth(${index}, 1)" class="hierarchy-btn" title="Indent"><i class="fas fa-chevron-right text-[10px]"></i></button>
                </div>
            `, 'justify-center border-r border-slate-100'));

            // Actions
            container.appendChild(createCell(`
                <button onclick="handleRowDuplicate(${index})" class="text-slate-300 hover:text-blue-500 transition px-2" title="Duplicate"><i class="fas fa-clone text-sm"></i></button>
                <button onclick="deleteRow(${index})" class="text-slate-300 hover:text-rose-500 transition px-2" title="Delete"><i class="fas fa-trash-alt text-sm"></i></button>
            `, 'justify-center border-r border-slate-100'));

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
            
            // Set depth and type attributes for styling
            const rowElements = container.querySelectorAll('.grid-cell:nth-last-child(-n+12)');
            rowElements.forEach(cell => {
                cell.setAttribute('data-depth', row.depth);
                cell.setAttribute('data-type', row.type);
            });
        });

        validateSyllabus();
        updateBulkBar();
    }

    function createCell(html, classes = "", style = "") {
        const div = document.createElement('div');
        div.className = `grid-cell grid-row ${classes}`;
        div.innerHTML = html;
        if(style) div.style = style;
        return div;
    }

    // --- LOGIC FUNCTIONS ---
    function updateRow(index, field, value) {
        if(field !== 'title' && field !== 'type') value = parseFloat(value) || 0;
        syllabusData[index][field] = value;
        
        // Auto-adjust depth based on node type
        if(field === 'type') {
            const typeDepthMap = {
                'paper': 0,
                'phase': 0,
                'section': 1,
                'unit': 2,
                'topic': 3
            };
            if(typeDepthMap[value] !== undefined) {
                syllabusData[index].depth = typeDepthMap[value];
            }
        }
        
        // Auto-calculation logic: QTY * EACH = MARKS
        if(field === 'qCount' || field === 'qEach') {
            const count = parseFloat(syllabusData[index].qCount) || 0;
            const each = parseFloat(syllabusData[index].qEach) || 0;
            syllabusData[index].weight = count * each;
        }
        
        renderGrid();
    }

    function changeDepth(index, change) {
        let newDepth = syllabusData[index].depth + change;
        if(newDepth >= 0 && newDepth <= 3) {
            syllabusData[index].depth = newDepth;
            renderGrid();
        }
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
        const payload = JSON.stringify({
            level: currentLevel,
            nodes: syllabusData,
            settings: manualSettings
        });

        fetch('<?php echo app_base_url("admin/quiz/syllabus/bulk-save"); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: payload
        })
        .then(r => r.json())
        .then(d => {
            if(d.status === 'success') {
                // Use custom notification if available, else alert
                alert('Changes saved successfully!');
            } else {
                alert('Error: ' + d.message);
            }
        })
        .catch(err => alert('Communication error'));
    }

    // --- HIERARCHY MODAL LOGIC ---
    let activeRowIndex = null;
    let activeTab = 'category';
    const categoriesDB = <?php echo !empty($categories) ? json_encode($categories) : '[]'; ?>;
    const subjectsDB = <?php echo !empty($subjects) ? json_encode($subjects) : '[]'; ?>;

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
        const courseId = document.getElementById('modal-filter-course').value;
        const eduId = document.getElementById('modal-filter-edu').value;
        const posId = document.getElementById('modal-filter-pos').value;
        const catId = document.getElementById('modal-filter-cat').value;
        const topicId = document.getElementById('modal-filter-topic').value;
        
        const container = document.getElementById('hierarchy-list-container');
        if (!container) return;
        container.innerHTML = '';
        
        // --- Cascaded Dropdown Filtering (Custom Lists) ---
        const filterList = (listId, filterFn) => {
            const list = document.getElementById(listId);
            if (!list) return;
            Array.from(list.querySelectorAll('li')).forEach(li => {
                const val = li.dataset.val;
                if (!val) return; // Skip "All" option if needed or handle logic
                li.style.display = filterFn(li, val) ? '' : 'none';
            });
        };

        // Education Select
        // Education Select - DISABLED cascaded filtering to show all options by default
        /*
        filterList('list-edu', (li, val) => {
            return categoriesDB.some(c => c.edu_level_id == val && (!courseId || !c.course_id || c.course_id == courseId));
        });
        */

        // Position Select - DISABLED
        /*
        filterList('list-pos', (li, val) => {
            const courseMatch = !courseId || !li.dataset.course || li.dataset.course == courseId;
            const eduMatch = !eduId || !li.dataset.edu || li.dataset.edu == eduId;
            return courseMatch && eduMatch;
        });
        */

        // Category Select
        filterList('list-cat', (li, val) => {
            return categoriesDB.some(c => c.id == val && (!posId || !c.position_level_id || c.position_level_id == posId));
        });

        // Topic Select
        filterList('list-topic', (li, val) => {
            // Show all if no category selected, otherwise filter by category
            if (!catId) return true; 
            return !li.dataset.cat || li.dataset.cat == catId;
        });

        const curTab = typeof currentHierarchyTab !== 'undefined' ? currentHierarchyTab : 'category';
        const sourceData = curTab === 'category' ? categoriesDB : subjectsDB; // Use currentHierarchyTab
        const typeLabel = curTab === 'category' ? 'Category' : 'Sub-Category';
        const linkKey = curTab === 'category' ? 'linked_category_id' : 'linked_topic_id';

        const matches = sourceData.filter(item => {
            if (query && !item.name.toLowerCase().includes(query)) return false;
            
            // For topics, filter by category if catId is set.
            if (curTab === 'topic' && catId && item.category_id != catId && item.category_id !== null) return false;
            // Also if topicId is set, match specific topic.
            if (curTab === 'topic' && topicId && item.id != topicId && item.id !== null) return false;

            // Allow items with null hierarchy to be visible
            if (courseId && item.course_id != courseId && item.course_id !== null) return false;
            if (eduId && item.edu_level_id != eduId && item.edu_level_id !== null) return false;
            if (posId && item.position_level_id != posId && item.position_level_id !== null) return false;
            
            return true;
        });

        if (matches.length === 0) {
            container.innerHTML = `<div class="text-center py-12 bg-white rounded-xl border border-dashed border-slate-200 text-slate-400 italic font-medium">No results matching criteria</div>`;
            return;
        }

        matches.forEach(item => {
            const isSelected = syllabusData[activeRowIndex] && syllabusData[activeRowIndex][linkKey] == item.id;
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

    function populateDropdown(type, data, idKey, titleKey) {
        const list = document.getElementById(`list-${type}`);
        if (!list) return;
        
        let html = `<li class="px-3 py-2 text-xs text-slate-500 hover:bg-slate-50 cursor-pointer" onclick="selectCustomOption('${type}', '', 'Filter ${type.charAt(0).toUpperCase() + type.slice(1)}')">All ${type.charAt(0).toUpperCase() + type.slice(1)}s</li>`;
        
        if (data.length === 0) {
            html += `<li class="px-3 py-2 text-xs text-slate-400 italic cursor-default">No items found</li>`;
        }

        list.innerHTML = html;
        
        data.forEach(item => {
            const li = document.createElement('li');
            li.className = 'px-3 py-2 text-xs text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-50 last:border-0 truncate transition-colors';
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

    function selectCustomOption(type, value, name) {
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

    function initBodyClick() {
        document.body.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(el => el.classList.add('hidden'));
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => { initSettings(); renderGrid(); initCustomDropdowns(); });
</script>