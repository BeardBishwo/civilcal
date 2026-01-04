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

<div class="admin-wrapper-container bg-[#F8FAFC] min-h-screen font-sans">
    <div class="admin-content-wrapper p-0 shadow-none bg-transparent">

        <!-- === HEADER: METRICS & ACTIONS === -->
        <div class="bg-white border-b border-slate-200 px-6 py-4 sticky top-0 z-50">
            <div class="max-w-full mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    
                    <!-- Left: Title & Metrics -->
                    <div class="flex-1 w-full">
                        <div class="flex items-center gap-3 mb-3">
                            <a href="<?php echo app_base_url('admin/quiz/syllabus'); ?>" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                            <h2 class="text-xl font-bold text-slate-800 mr-2"><?php echo htmlspecialchars($level); ?></h2>
                            <div id="validator-msg" class="px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase flex items-center shadow-sm">
                                <i class="fas fa-check-circle mr-1.5"></i> Tally: <span id="tally-display" class="ml-1">0/0</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-2 items-center">
                            <!-- Marks -->
                            <div class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md flex items-center border border-indigo-500">
                                TOTAL: 
                                <input type="text" id="global-marks-input" class="header-input text-white ml-2 w-12 bg-transparent border-b border-indigo-400 text-center focus:outline-none placeholder-indigo-300" placeholder="Auto" onblur="updateGlobalSetting('marks', this.value)" onkeydown="if(event.key==='Enter') this.blur()">
                            </div>
                            <!-- Time -->
                            <div class="bg-amber-400 text-amber-900 border border-amber-300 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm flex items-center">
                                <i class="fas fa-clock mr-2 text-amber-700"></i> TIME: 
                                <input type="text" id="global-time-input" class="header-input text-amber-900 w-16 ml-1 bg-transparent border-b border-amber-500 text-center focus:outline-none" placeholder="Auto" onblur="updateGlobalSetting('time', this.value)" onkeydown="if(event.key==='Enter') this.blur()">
                            </div>
                            <!-- Pass -->
                            <div class="bg-emerald-500 text-white border border-emerald-400 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm flex items-center">
                                <i class="fas fa-check-circle mr-2 text-white opacity-80"></i> PASS: 
                                <input type="text" id="global-pass-input" class="header-input text-white ml-1 w-8 bg-transparent border-b border-emerald-300 text-center focus:outline-none" placeholder="40" onblur="updateGlobalSetting('pass', this.value)" onkeydown="if(event.key==='Enter') this.blur()">
                            </div>
                            <!-- Neg -->
                            <div class="bg-rose-500 text-white border border-rose-400 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm flex items-center gap-2">
                                <i class="fas fa-minus-circle opacity-80"></i> NEG: 
                                <input type="number" id="global-neg-input" class="bg-transparent border-b border-rose-300 text-white w-10 text-center focus:outline-none font-mono" placeholder="20" step="1" min="0" onblur="updateGlobalSetting('negValue', this.value)" onkeydown="if(event.key==='Enter') this.blur()">
                                
                                <select id="neg-unit-select" class="bg-transparent text-white font-bold outline-none cursor-pointer text-[10px] border-b border-rose-300 hover:text-white" onchange="updateGlobalSetting('negUnit', this.value)">
                                    <option value="percent" class="text-rose-900">%</option>
                                    <option value="number" class="text-rose-900">No.</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center gap-3 self-end md:self-center">
                        <button onclick="addTopic()" class="h-10 px-4 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-semibold transition flex items-center shadow-sm">
                            <i class="fas fa-plus mr-2 text-blue-500"></i> Row
                        </button>
                        <button onclick="saveSyllabus()" class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition shadow-md flex items-center">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                        <button onclick="window.print()" class="h-10 px-4 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition shadow-md flex items-center">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- === MAIN GRID AREA === -->
        <div class="px-6 py-6 pb-24">
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
        <div id="bulk-action-bar" class="fixed bottom-8 left-1/2 transform -translate-x-1/2 translate-y-full bg-slate-900 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-6 z-50 transition-all duration-300 border border-slate-700">
            <span class="text-sm font-bold text-slate-400"><span id="selected-count" class="text-white text-lg mr-1">0</span> Rows Selected</span>
            <div class="h-6 w-px bg-slate-700"></div>
            <button onclick="bulkDuplicate()" class="hover:text-blue-400 transition text-xs font-bold uppercase flex items-center group"><i class="fas fa-copy mr-2 group-hover:scale-110"></i> Duplicate</button>
            <div class="h-6 w-px bg-slate-700"></div>
            <button onclick="bulkIndent(-1)" class="hover:text-blue-400 transition text-xs font-bold uppercase flex items-center group"><i class="fas fa-outdent mr-2 group-hover:scale-110"></i> Outdent</button>
            <button onclick="bulkIndent(1)" class="hover:text-blue-400 transition text-xs font-bold uppercase flex items-center group"><i class="fas fa-indent mr-2 group-hover:scale-110"></i> Indent</button>
            <div class="h-6 w-px bg-slate-700"></div>
            <button onclick="bulkDelete()" class="hover:text-rose-400 transition text-xs font-bold uppercase flex items-center group"><i class="fas fa-trash mr-2 group-hover:scale-110"></i> Delete</button>
            <button onclick="clearSelection()" class="ml-4 w-8 h-8 flex items-center justify-center rounded-full bg-slate-800 text-slate-400 hover:text-white transition"><i class="fas fa-times"></i></button>
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
    .grid-row:hover { background-color: #f0f9ff; }
    .grid-row[data-depth="0"] { background-color: #eff6ff; }
    .grid-row[data-depth="0"]:hover { background-color: #e0f2fe; }
    
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
    .hierarchy-btn:hover { background: #cbd5e1; color: #0f172a; }
</style>

<script>
    // 1. DATA INITIALIZATION
    let syllabusData = <?php echo !empty($initialData) ? json_encode($initialData) : '[]'; ?>;
    
    if(syllabusData.length === 0) {
        syllabusData = [
            { id: Date.now(), title: "Paper I: General", type: "paper", depth: 0, weight: 100, time: 0, qCount: 0, qEach: 0, selected: false }
        ];
    }

    let manualSettings = { marks: null, time: null, pass: 40, negValue: 20, negUnit: 'percent' };
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
            
            // Set depth attribute for styling
            container.lastChild.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.setAttribute('data-depth', row.depth);
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
        if(field === 'qCount' || field === 'qEach') {
            syllabusData[index].weight = (syllabusData[index].qCount * syllabusData[index].qEach);
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
            bar.classList.add('visible');
            bar.classList.remove('translate-y-full');
        } else {
            bar.classList.remove('visible');
            bar.classList.add('translate-y-full');
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
            badge.className = 'px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase flex items-center shadow-sm bg-emerald-500 text-white';
        } else {
            badge.className = 'px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase flex items-center shadow-sm bg-rose-500 text-white';
        }
    }

    function saveSyllabus() {
        const payload = JSON.stringify({
            level: '<?php echo addslashes($level); ?>',
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