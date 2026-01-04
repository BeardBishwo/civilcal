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
            'parent_id' => $node['parent_id'] ?? null
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
                        <div class="grid-header border-r border-slate-200">Each</div>
                        <div class="grid-header border-r border-slate-200">Marks</div>
                        <div class="grid-header border-r border-slate-200 text-center">Hierarchy</div>
                        <div class="grid-header">Actions</div>
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
</div>

<style>
    /* Premium Grid Styles */
    .syllabus-grid {
        display: grid;
        grid-template-columns: 50px 40px 50px 3.5fr 100px 120px 80px 80px 90px 120px 100px;
        background-color: #f1f5f9;
        min-width: 1200px;
    }
    .grid-header {
        background-color: #f8fafc; color: #64748b; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; padding: 14px 4px; display: flex; align-items: center; justify-content: center;
    }
    .grid-row { background-color: white; border-bottom: 1px solid #f1f5f9; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .grid-row:hover { background-color: #f8fafc; }
    
    /* Phase/Level 0 Row - Dark Slate from mockup */
    .grid-row[data-type="paper"] { background-color: #f1f5f9; }
    .grid-row[data-type="paper"] .grid-cell { color: #1e293b; font-weight: 800; font-size: 0.95rem; }
    .grid-row[data-type="paper"] .input-premium { background-color: #fffbeb !important; border-color: #fef3c7; color: #92400e; }
    
    .grid-row[data-type="section"] { background-color: #f8fafc; }
    .grid-row[data-type="section"] .grid-cell { font-weight: 700; color: #334155; }
    
    .grid-cell { padding: 8px 10px; display: flex; align-items: center; font-size: 0.9rem; color: #334155; position: relative; }
    
    .input-premium {
        width: 100%; padding: 6px; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 600; font-size: 0.85rem; transition: 0.2s;
        background-color: white;
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
    
    .node-badge {
        padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; width: 100%; text-align: center;
    }
    .badge-phase { background: #1e293b; color: white; }
    .badge-section { background: #e0e7ff; color: #4338ca; }
    .badge-unit { background: #f1f5f9; color: #64748b; }

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
            { id: Date.now(), title: "Paper I: General", type: "paper", depth: 0, weight: 100, time: 0, qCount: 0, qEach: 0, selected: false }
        ];
    }

    let currentLevel = '<?php echo addslashes($level); ?>';
    let manualSettings = { 
        marks: null, 
        time: null, 
        pass: 40, 
        negValue: 20, 
        negUnit: 'percent',
        negScope: 'per-q',
        description: 'Elevate your curriculum with our high-precision syllabus engine.'
    };
    let draggedItemIndex = null;

    // --- GRID RENDERING ---
    function renderGrid() {
        const container = document.getElementById('syllabus-container');
        while(container.children.length > 11) container.removeChild(container.lastChild);

        syllabusData.forEach((row, index) => {
            // Checkbox
            container.appendChild(createCell(`<input type="checkbox" class="row-checkbox w-4 h-4 rounded" ${row.selected ? 'checked' : ''} onchange="toggleRowSelection(${index}, this.checked)">`, 'justify-center border-r border-slate-100'));
            
            // Drag Handle
            const dragCell = createCell(`<i class="fas fa-grip-vertical drag-handle"></i>`, 'justify-center border-r border-slate-100');
            dragCell.draggable = true;
            setupDragEvents(dragCell, index);
            container.appendChild(dragCell);

            // Level
            container.appendChild(createCell(row.depth, `justify-center depth-${row.depth} bg-slate-50 border-r border-slate-100 font-mono text-xs`));

            // Topic / Title
            const padding = 20 + (row.depth * 32);
            let icon = 'fa-folder';
            if(row.type === 'paper') icon = 'fa-layer-group text-slate-500';
            else if(row.type === 'section') icon = 'fa-folder-open text-slate-400';
            
            container.appendChild(createCell(
                `<div class="flex items-center w-full">
                    <i class="fas ${icon} mr-3 opacity-60 text-xs"></i> 
                    <span contenteditable="true" class="w-full outline-none focus:bg-white rounded px-1 transition duration-200" onblur="updateRow(${index}, 'title', this.innerText)">${row.title}</span>
                 </div>`,
                `depth-${row.depth} border-r border-slate-100`, `padding-left: ${padding}px;`
            ));

            // Time
            const timeBg = row.depth === 0 ? "bg-amber-100" : "bg-white";
            container.appendChild(createCell(`<input type="number" class="input-premium time-input ${timeBg}" value="${row.time || 0}" onchange="updateRow(${index}, 'time', this.value)">`, 'justify-center border-r border-slate-100'));

            // Node Type (Badge Style)
            let badgeClass = 'badge-unit';
            let typeLabel = row.type.toUpperCase();
            if(row.type === 'paper') { badgeClass = 'badge-phase'; typeLabel = 'PHASE'; }
            if(row.type === 'section') badgeClass = 'badge-section';

            container.appendChild(createCell(`
                <select class="hidden" id="select-type-${index}" onchange="updateRow(${index}, 'type', this.value)">
                    <option value="paper" ${row.type==='paper'?'selected':''}>Phase</option>
                    <option value="section" ${row.type==='section'?'selected':''}>Section</option>
                    <option value="unit" ${row.type==='unit'?'selected':''}>Unit</option>
                </select>
                <div class="node-badge ${badgeClass} cursor-pointer hover:opacity-80 transition" onclick="document.getElementById('select-type-${index}').click()">
                    ${typeLabel}
                </div>
            `, 'justify-center border-r border-slate-100 px-3'));

            // Qty
            const qtyDisabled = (row.type !== 'unit' && row.depth !== 0) ? 'disabled style="background:#f8fafc; color:#cbd5e1;"' : '';
            container.appendChild(createCell(`<input type="number" class="input-premium qty-input" value="${row.qCount || 0}" onchange="updateRow(${index}, 'qCount', this.value)" ${qtyDisabled}>`, 'justify-center border-r border-slate-100'));

            // Each
            container.appendChild(createCell(`<input type="number" class="input-premium each-input" value="${row.qEach || 0}" onchange="updateRow(${index}, 'qEach', this.value)" ${qtyDisabled}>`, 'justify-center border-r border-slate-100'));

            // Marks
            const marksBg = row.depth === 0 ? "bg-slate-100" : "bg-white";
            container.appendChild(createCell(`<input type="number" class="input-premium font-bold ${marksBg}" value="${row.weight}" onchange="updateRow(${index}, 'weight', this.value)">`, 'justify-center border-r border-slate-100'));

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
            `, 'justify-center'));
            
            // Set depth and type attributes for styling
            const rowElements = container.querySelectorAll('.grid-cell:nth-last-child(-n+11)');
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
        syllabusData.push({ id: Date.now(), title: "New Syllabus Item", type: "unit", depth: 2, weight: 0, time: 0, qCount: 0, qEach: 0, selected: false });
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

    document.addEventListener('DOMContentLoaded', () => { renderGrid(); });
</script>