// Catalog data: categories -> subcategories -> tools
async function loadDefaultCatalog() {
    try {
        const response = await fetch('/aec-calculator/db/catalog.json');
        return await response.json();
    } catch (e) {
        console.error('Could not load default catalog', e);
        return [];
    }
}

// Merge saved layout from localStorage (if any) with default catalog structure
async function loadCatalogFromStorage(){
    try{
        const saved = JSON.parse(localStorage.getItem('catalogLayout'));
        const defaultCatalog = await loadDefaultCatalog();
        if(!saved || !Array.isArray(saved)) return defaultCatalog;
        // Basic validation: ensure categories and subcategories exist, otherwise fall back
        return saved.map(cat => ({
            id: cat.id || ('cat_' + Math.random().toString(36).slice(2,8)),
            title: cat.title || 'Category',
            subcategories: Array.isArray(cat.subcategories) ? cat.subcategories.map(sc => ({ id: sc.id||('sc_'+Math.random().toString(36).slice(2,6)), title: sc.title||'Sub', tools: Array.isArray(sc.tools)?sc.tools:[] })) : []
        }));
    }catch(e){
        return await loadDefaultCatalog();
    }
}

function persistCatalog(catalog){
    try{ localStorage.setItem('catalogLayout', JSON.stringify(catalog)); }catch(e){ console.warn('Could not persist catalog layout', e); }
}

async function initializeDashboard(){
    const calculatorGrid = document.getElementById('calculatorGrid');
    calculatorGrid.innerHTML = '';
    const catalog = await loadCatalogFromStorage();

    // Render categories and subcategories
    catalog.forEach(cat => {
        const col = document.createElement('div');
        col.className = 'col-12 mb-4 module-container';
        const catCard = document.createElement('div');
        catCard.className = 'glass-card p-3 module-card module-enter';
        catCard.innerHTML = `
            <div class="module-header text-center mb-3">
                <span class="module-badge">Module</span>
                <div class="module-title text-white">${escapeHtml(cat.title)}</div>
            </div>
            <div class="d-flex justify-content-end mb-2">
                <button class="btn btn-sm btn-outline-light me-2" data-cat="${cat.id}" onclick="toggleCategoryEdit('${cat.id}')">Customize</button>
            </div>`;

        // Subcategories container
        const subWrap = document.createElement('div');
        subWrap.className = 'row g-3';

        cat.subcategories.forEach(sc => {
            const scCol = document.createElement('div');
            scCol.className = 'col-md-4';
            const scCard = document.createElement('div');
            scCard.className = 'glass-card p-3 category-card';
            scCard.setAttribute('data-subcategory', sc.id);
            scCard.innerHTML = `<div class="category-header"><h5>${escapeHtml(sc.title)}</h5><div><i class="fas fa-chevron-down category-toggle"></i></div></div><div class="tools-list expanded" data-sub="${sc.id}"></div>`;

            // Populate tools
            const toolsList = scCard.querySelector('.tools-list');
            sc.tools.forEach(tool => {
                const t = document.createElement('div');
                t.className = 'tool-card d-flex justify-content-between align-items-center p-2 mb-2';
                t.setAttribute('draggable','true');
                t.setAttribute('data-tool-id', tool.id);
                const favClass = isToolFavorite(tool.id) ? 'fas' : 'far';
                t.innerHTML = `<div><i class="fas fa-square-full me-2" style="opacity:0.6"></i><span class="tool-name">${escapeHtml(tool.name)}</span></div><div><i class="${favClass} fa-star fav-star" data-tool="${tool.id}" title="Favorite"></i><button class="btn btn-sm btn-light me-1 open-tool" data-tool="${tool.id}">Open</button><i class="fas fa-grip-lines drag-handle" style="cursor:grab"></i></div>`;
                toolsList.appendChild(t);
            });

            subWrap.appendChild(scCol);
            scCol.appendChild(scCard);
        });

        catCard.appendChild(subWrap);
        col.appendChild(catCard);
        calculatorGrid.appendChild(col);
    });

    // Apply saved layout style to newly rendered .tools-list containers
    try{ applySavedLayoutStyle(); }catch(e){}
    // Wire up drag-and-drop and open buttons
    setupToolDragAndDrop();
    document.querySelectorAll('.open-tool').forEach(btn => btn.addEventListener('click', function(){ openTool(this.getAttribute('data-tool')); }));
    // wire up category header collapse/expand
    document.querySelectorAll('.category-card').forEach(card => {
        const header = card.querySelector('.category-header');
        const tools = card.querySelector('.tools-list');
        if (header && tools) {
            header.addEventListener('click', () => {
                const isCollapsed = card.classList.toggle('collapsed');
                if (isCollapsed) { tools.classList.remove('expanded'); tools.classList.add('collapsed'); } else { tools.classList.remove('collapsed'); tools.classList.add('expanded'); }
            });
        }
    });
    loadCalculationHistory();
}

function openTool(toolId){
    // Map some tool ids to existing in-page calculators where appropriate
    const mapping = {
        'concrete_volume': 'civil',
        'rebar_calculation': 'civil',
        'concrete_mix_design': 'civil',
        'concrete_strength': 'civil',
        'earthwork_calc': 'civil',
        'beam_load': 'civil',
        'area_calc': 'site',
        'pipe_flow': 'plumbing',
        'wire_sizing': 'electrical',
        'voltage_drop_tool': 'electrical',
        'load_calc_tool': 'electrical',
        'conduit_sizing': 'electrical'
    };
    const target = mapping[toolId];
    if(target){ showCalculator(target); } else { showToast('Opening tool: ' + toolId, 'info', 1200); }
}

// Enable dragging of tool cards between .tools-list containers
function setupToolDragAndDrop(){
    let dragEl = null;
    // drag handlers are active only when body has .customize-mode
    document.querySelectorAll('.tool-card').forEach(el => {
        el.addEventListener('dragstart', (e) => {
            if (!document.body.classList.contains('customize-mode')) { e.preventDefault(); return; }
            dragEl = el;
            e.dataTransfer.effectAllowed = 'move';
            try{ e.dataTransfer.setData('text/plain', el.getAttribute('data-tool-id')); }catch(_){ }
            el.classList.add('dragging');
        });

        el.addEventListener('dragend', () => { if(dragEl) dragEl.classList.remove('dragging'); dragEl = null; persistCurrentCatalog(); });
    });

    document.querySelectorAll('.tools-list').forEach(list => {
        list.addEventListener('dragover', (e) => {
            if (!document.body.classList.contains('customize-mode')) return;
            e.preventDefault(); e.dataTransfer.dropEffect = 'move'; const after = getDragAfterElement(list, e.clientY); if(after == null){ list.appendChild(dragEl); } else { list.insertBefore(dragEl, after); }
        });
        list.addEventListener('drop', (e) => { if (!document.body.classList.contains('customize-mode')) return; e.preventDefault(); /* handled in dragover */ persistCurrentCatalog(); });
    });
}

function getDragAfterElement(container, y){
    const draggableElements = [...container.querySelectorAll('.tool-card:not(.dragging)')];
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element || null;
}

function persistCurrentCatalog(){
    const catalog = [];
    document.querySelectorAll('#calculatorGrid > .col-12').forEach(catCol => {
        const catTitle = catCol.querySelector('h4');
        const catId = catCol.querySelector('button[data-cat]') ? catCol.querySelector('button[data-cat]').getAttribute('data-cat') : ('cat_'+Math.random().toString(36).slice(2,6));
        const subcats = [];
        catCol.querySelectorAll('[data-subcategory]').forEach(scCard => {
            const scId = scCard.getAttribute('data-subcategory');
            const scTitle = scCard.querySelector('h5') ? scCard.querySelector('h5').textContent : scId;
            const tools = [];
            scCard.querySelectorAll('.tool-card').forEach(t => { tools.push({ id: t.getAttribute('data-tool-id'), name: t.textContent.trim() }); });
            subcats.push({ id: scId, title: scTitle, tools });
        });
        catalog.push({ id: catId, title: catTitle ? catTitle.textContent.trim() : 'Category', subcategories: subcats });
    });
    persistCatalog(catalog);
}

// Favorites handling for individual tools
function getFavoriteTools(){
    try{ return JSON.parse(localStorage.getItem('favoriteTools')) || []; }catch(e){ return []; }
}
function isToolFavorite(toolId){ return getFavoriteTools().includes(toolId); }
function toggleToolFavorite(toolId){
    let favs = getFavoriteTools();
    if (favs.includes(toolId)) favs = favs.filter(id => id !== toolId); else favs.push(toolId);
    try{ localStorage.setItem('favoriteTools', JSON.stringify(favs)); }catch(e){}
    renderFavoritesSection();
    // Update star icons in tools list
    document.querySelectorAll(`.fav-star[data-tool="${toolId}"]`).forEach(el=>{
        el.className = favs.includes(toolId) ? 'fas fa-star fav-star' : 'far fa-star fav-star';
    });
}

async function renderFavoritesSection(){
    const favs = getFavoriteTools();
    const favCol = document.getElementById('favoritesColumn');
    const favContainer = document.getElementById('favoritesContainer');
    const favList = document.getElementById('favoritesList');
    if (!favContainer || !favList) return;
    favList.innerHTML = '';
    if (!favs || favs.length === 0){ favContainer.style.display = 'none'; return; }
    favContainer.style.display = 'block';
    for (const id of favs) {
        const btn = document.createElement('button');
        btn.className = 'btn btn-sm btn-outline-light';
        btn.style.minWidth='120px';
        btn.style.whiteSpace='nowrap';
        const name = await findToolName(id) || id.replace(/_/g,' ');
        btn.textContent = name;
        btn.addEventListener('click', ()=> openTool(id));
        favList.appendChild(btn);
    }
}

async function findToolName(toolId){
    const catalog = await loadCatalogFromStorage();
    for(const cat of catalog){
        for(const sc of (cat.subcategories||[])){
            for(const t of (sc.tools||[])){
                if(t.id === toolId) return t.name;
            }
        }
    }
    return null;
}

// Wire up dynamic favorite star clicks (delegated)
document.addEventListener('click', function(e){
    const fav = e.target.closest('.fav-star');
    if (fav){ e.preventDefault(); const tool = fav.getAttribute('data-tool'); toggleToolFavorite(tool); return; }
});

// Layout style helpers
function setLayoutStyle(style){
    // Apply layout class to each tools-list (per subcategory) so layout affects tools grouping
    document.querySelectorAll('.tools-list').forEach(list => {
        list.classList.remove('layout-one','layout-two','layout-grid');
        if (style === 'one') list.classList.add('layout-one');
        else if (style === 'two') list.classList.add('layout-two');
        else list.classList.add('layout-grid');
    });
    try{ localStorage.setItem('layoutStyle', style); }catch(e){}
}
function applySavedLayoutStyle(){
    const saved = localStorage.getItem('layoutStyle') || 'one';
    const sel = document.getElementById('layoutSelect');
    if (sel) sel.value = saved;
    // Ensure we apply once the DOM and tools-lists exist
    document.querySelectorAll('.tools-list').forEach(list => {
        list.classList.remove('layout-one','layout-two','layout-grid');
        list.classList.add('layout-' + saved);
    });
    setLayoutStyle(saved);
}

function toggleCategoryEdit(catId){
    // Simple visual cue: toggle border for that category to indicate edit mode
    const btn = document.querySelector(`button[data-cat="${catId}"]`);
    if(!btn) return;
    const catCol = btn.closest('.col-12');
    if(catCol.classList.contains('editing')){
        catCol.classList.remove('editing');
        btn.textContent = 'Customize';
        persistCurrentCatalog();
    } else {
        catCol.classList.add('editing');
        btn.textContent = 'Done';
    }
}


function showCalculator(calculatorId){
    // Try to find an in-page calculator container first
    const el = document.getElementById(calculatorId + 'Calculator');
    if (!el) {
        // Fallback to the dedicated PHP page if present
        // This keeps the dashboard buttons functional when only standalone pages exist
        window.location.href = `/aec-calculator/${calculatorId}.php`;
        return;
    }
    document.getElementById('dashboardView').style.display='none';
    document.getElementById('calculatorViews').style.display='block';
    document.querySelectorAll('.calculator-container').forEach(c=>c.classList.remove('active'));
    el.classList.add('active');
}
function showDashboard(){ document.getElementById('dashboardView').style.display='block'; document.getElementById('calculatorViews').style.display='none'; initializeDashboard(); }
function toggleFavorite(calculatorId){ let favorites=JSON.parse(localStorage.getItem('favorites'))||[]; if(favorites.includes(calculatorId)) favorites=favorites.filter(id=>id!==calculatorId); else favorites.push(calculatorId); localStorage.setItem('favorites', JSON.stringify(favorites)); initializeDashboard(); }
function hideCalculator(calculatorId){ let hiddenCalculators=JSON.parse(localStorage.getItem('hiddenCalculators'))||[]; if(!hiddenCalculators.includes(calculatorId)){ hiddenCalculators.push(calculatorId); localStorage.setItem('hiddenCalculators', JSON.stringify(hiddenCalculators)); initializeDashboard(); } }

function saveLayout(){ const calculatorOrder=[]; document.querySelectorAll('.calculator-card').forEach(card=>calculatorOrder.push(card.getAttribute('data-calculator'))); localStorage.setItem('calculatorOrder', JSON.stringify(calculatorOrder)); }
function loadCalculationHistory(){
    const history=JSON.parse(localStorage.getItem('calculationHistory'))||[];
    const historyContainer=document.getElementById('calculationHistory');
    if(history.length===0){
        historyContainer.innerHTML='<p class="text-muted">No calculation history yet.</p>';
        return;
    }
    historyContainer.innerHTML='';
    history.slice(-5).reverse().forEach(item=>{
        const historyItem=document.createElement('div');
        historyItem.className='history-item';
        historyItem.innerHTML=`<div class="d-flex justify-content-between"><div><strong>${item.calculator}</strong>: ${item.calculation}</div><div class="text-muted">${new Date(item.timestamp).toLocaleString()}</div></div>`;
        historyContainer.appendChild(historyItem);
    });
}
function saveToHistory(calculator, calculation, result){
    // Persist to localStorage immediately for offline-first behaviour
    const entry = { calculator, calculation, result, timestamp: new Date().toISOString() };
    try{
        const history = JSON.parse(localStorage.getItem('calculationHistory')) || [];
        history.push(entry);
        localStorage.setItem('calculationHistory', JSON.stringify(history));
        loadCalculationHistory();
    }catch(e){ console.warn('Failed to save local history', e); }

    // Attempt to send to server asynchronously. If offline or the request fails,
    // localStorage acts as a resilient fallback.
    if (navigator.onLine) {
        fetch('/aec-calculator/api/save_history.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(entry)
        }).then(r => r.json()).then(data => {
            if (!data || !data.success) {
                // server-side save failed; keep local copy and log for debugging
                console.warn('Server save_history failed', data);
            }
        }).catch(err => {
            console.warn('Could not save history to server', err);
        });
    }
}

// Loading & toast helpers
function showLoader(){ const l=document.getElementById('globalLoader'); if(l) l.style.display='flex'; }
function hideLoader(){ const l=document.getElementById('globalLoader'); if(l) l.style.display='none'; }
function showToast(message, type='info', duration=3000){ const container=document.getElementById('toastContainer'); if(!container) return; const item=document.createElement('div'); item.className='toast-item toast-'+type;
  item.textContent=message;
  container.appendChild(item);
  // trigger animation
  requestAnimationFrame(()=>item.classList.add('visible'));
  setTimeout(()=>{ item.classList.remove('visible'); setTimeout(()=>{ try{ container.removeChild(item);}catch(e){} },250); }, duration);
}

function calculateConcreteVolume(){
    const length=parseFloat(document.getElementById('concreteLength').value);
    const width=parseFloat(document.getElementById('concreteWidth').value);
    const depth=parseFloat(document.getElementById('concreteDepth').value);
    if(isNaN(length)||isNaN(width)||isNaN(depth)){
        showToast('Please enter valid numbers for all fields', 'error', 3000);
        return;
    }
    showLoader();
    setTimeout(()=>{
        const volume=length*width*depth;
        const resultText=`Concrete Volume: ${volume.toFixed(2)} m³`;
        document.getElementById('concreteVolumeResult').textContent=resultText;
        document.getElementById('concreteResult').style.display='block';
        saveToHistory('Civil Engineering', `Concrete: ${length}m × ${width}m × ${depth}m`, resultText);
        hideLoader();
        showToast('Concrete volume calculated', 'success', 1800);
    }, 260);
}
function calculateEarthwork(){
    const cutArea=parseFloat(document.getElementById('cutArea').value);
    const fillArea=parseFloat(document.getElementById('fillArea').value);
    const length=parseFloat(document.getElementById('earthworkLength').value);
    if(isNaN(cutArea)||isNaN(fillArea)||isNaN(length)){
        showToast('Please enter valid numbers for all fields', 'error', 3000);
        return;
    }
    showLoader();
    setTimeout(()=>{
        const cutVolume=cutArea*length;
        const fillVolume=fillArea*length;
        const netVolume=cutVolume-fillVolume;
        let resultText=`Cut Volume: ${cutVolume.toFixed(2)} m³<br>`;
        resultText+=`Fill Volume: ${fillVolume.toFixed(2)} m³<br>`;
        resultText+=`Net Volume: ${netVolume.toFixed(2)} m³`;
        document.getElementById('earthworkVolumeResult').innerHTML=resultText;
        document.getElementById('earthworkResult').style.display='block';
        saveToHistory('Civil Engineering', `Earthwork: Cut=${cutArea}m², Fill=${fillArea}m², Length=${length}m`, resultText);
        hideLoader();
        showToast('Earthwork calculation complete', 'success', 1800);
    }, 260);
}
function calculateBeamLoad(){
    const length=parseFloat(document.getElementById('beamLength').value);
    const width=parseFloat(document.getElementById('beamWidth').value)/100;
    const depth=parseFloat(document.getElementById('beamDepth').value)/100;
    const load=parseFloat(document.getElementById('load').value);
    if(isNaN(length)||isNaN(width)||isNaN(depth)||isNaN(load)){
        showToast('Please enter valid numbers for all fields', 'error', 3000);
        return;
    }
    showLoader();
    setTimeout(()=>{
        const moment=(load*Math.pow(length,2))/8;
        const sectionModulus=(width*Math.pow(depth,2))/6;
        const stress=moment/sectionModulus;
        let resultText=`Maximum Bending Moment: ${moment.toFixed(2)} kN·m<br>`;
        resultText+=`Section Modulus: ${sectionModulus.toFixed(6)} m³<br>`;
        resultText+=`Bending Stress: ${stress.toFixed(2)} kPa`;
        document.getElementById('beamLoadResult').innerHTML=resultText;
        document.getElementById('beamResult').style.display='block';
        saveToHistory('Civil Engineering', `Beam: L=${length}m, W=${width*100}cm, D=${depth*100}cm, Load=${load}kN/m`, resultText);
        hideLoader();
        showToast('Beam load calculation complete', 'success', 1800);
    }, 260);
}
function convertUnits(){
    const value=parseFloat(document.getElementById('unitValue').value);
    const fromUnit=document.getElementById('fromUnit').value;
    const toUnit=document.getElementById('toUnit').value;
    if(isNaN(value)){
        showToast('Please enter a valid number', 'error', 3000);
        return;
    }
    showLoader();
    setTimeout(()=>{
        const toMeter={ meter:1, foot:0.3048, inch:0.0254, cm:0.01 };
        const valueInMeters=value*toMeter[fromUnit];
        const convertedValue=valueInMeters/toMeter[toUnit];
        document.getElementById('convertedValue').textContent=`${value} ${fromUnit} = ${convertedValue.toFixed(4)} ${toUnit}`;
        document.getElementById('conversionResult').style.display='block';
        saveToHistory('Unit Converter', `${value} ${fromUnit} to ${toUnit}`, `${convertedValue.toFixed(4)} ${toUnit}`);
        hideLoader();
        showToast('Conversion complete', 'success', 1200);
    }, 220);
}

// Initialize the app
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();

    // Set up favorite button for civil calculator
    const favBtn = document.getElementById('favoriteCivil');
    if (favBtn) {
        favBtn.addEventListener('click', function() {
            toggleFavorite('civil');
            this.innerHTML = '<i class="fas fa-star"></i> Added to Favorites';
            setTimeout(() => { this.innerHTML = '<i class="far fa-star"></i> Add to Favorites'; }, 2000);
        });
    }

    // Make sure theme icon reflects saved theme (footer also sets this, but double-safety here)
    try {
        const savedTheme = localStorage.getItem('theme');
        const themeBtn = document.getElementById('themeToggle');
        if (themeBtn) {
            const icon = themeBtn.querySelector('i');
            if (icon) { icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon'; }
            // short single-word tooltip
            const label = savedTheme === 'dark' ? 'Light' : 'Night';
            themeBtn.setAttribute('title', label);
            themeBtn.setAttribute('aria-label', label);
        }
    } catch(e) {}
    // Initialize favorites and layout UI
    renderFavoritesSection();
    applySavedLayoutStyle();
    // wire up customize toggle
    const toggleBtn = document.getElementById('toggleLayout');
    const layoutSelect = document.getElementById('layoutSelect');
    if (toggleBtn) toggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('customize-mode');
        const active = document.body.classList.contains('customize-mode');
        layoutSelect.style.display = active ? 'inline-block' : 'none';
        toggleBtn.classList.toggle('btn-primary', active);
    });
    if (layoutSelect) layoutSelect.addEventListener('change', (e) => {
        setLayoutStyle(e.target.value);
    });
});

// --- Search widget: AJAX-style suggestions ---
const searchInput = document.getElementById('globalSearch');
const suggestionBox = document.getElementById('searchSuggestions');
let searchTimer = null;
let suggestionRows = [];
let activeSuggestion = -1;
if (searchInput) {
    searchInput.setAttribute('aria-autocomplete', 'list');
    searchInput.setAttribute('aria-controls', 'searchSuggestions');
    searchInput.addEventListener('input', function(){
        const q = this.value.trim();
        if (searchTimer) clearTimeout(searchTimer);
        searchTimer = setTimeout(()=> performSearch(q), 180);
    });

    searchInput.addEventListener('keydown', function(e){
        if (!suggestionBox || suggestionBox.style.display === 'none') return;
        if (e.key === 'ArrowDown') { e.preventDefault(); moveActive(1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); moveActive(-1); }
        else if (e.key === 'Enter') {
            e.preventDefault(); if (activeSuggestion >= 0 && suggestionRows[activeSuggestion]) suggestionRows[activeSuggestion].click(); else performSearch(this.value.trim(), true);
        } else if (e.key === 'Escape') { suggestionBox.style.display='none'; }
    });
}

async function performSearch(q, openFirst=false){
    try{
        const url = '/aec-calculator/api/search.php?q=' + encodeURIComponent(q);
        const res = await fetch(url);
        const data = await res.json();
        renderSuggestions(data, q, openFirst);
    }catch(err){ renderSuggestions([], q); }
}

function renderSuggestions(items, q, openFirst){
    suggestionBox.innerHTML = '';
    if (!items || items.length === 0) {
        suggestionBox.style.display = q ? 'block' : 'none';
        suggestionBox.innerHTML = '<div class="text-muted p-2">No results found. Try a different keyword.</div>';
        return;
    }
    const ul = document.createElement('div');
    ul.style.display='flex'; ul.style.flexDirection='column'; ul.style.gap='0.5rem';
    suggestionRows = [];
    items.forEach((it, idx)=>{
        const row = document.createElement('div');
        row.className = 'p-2';
        row.style.cursor = 'pointer';
        row.style.borderRadius = '8px';
        row.setAttribute('role','option');
        row.setAttribute('tabindex','0');
        row.onmouseover = ()=> { setActive(idx); };
        row.onmouseout = ()=> { /* noop */ };
        row.innerHTML = `<strong>${escapeHtml(it.name)}</strong> <div class="text-muted small">${escapeHtml($it_desc(it))}</div>`;
        row.addEventListener('click', ()=>{ if (it.type === 'calculator' && it.id) showCalculator(it.id); suggestionBox.style.display = 'none'; });
        row.addEventListener('keydown', (e)=>{ if (e.key === 'Enter') row.click(); });
        ul.appendChild(row);
        suggestionRows.push(row);
    });
    suggestionBox.appendChild(ul);
    suggestionBox.style.display = 'block';
    activeSuggestion = -1;
    if (openFirst && items[0] && items[0].type === 'calculator') { showCalculator(items[0].id); }
}

function setActive(i){
    if (!suggestionRows || suggestionRows.length === 0) return;
    if (activeSuggestion >= 0 && suggestionRows[activeSuggestion]) suggestionRows[activeSuggestion].classList.remove('active');
    activeSuggestion = Math.max(-1, Math.min(i, suggestionRows.length - 1));
    if (activeSuggestion >= 0 && suggestionRows[activeSuggestion]) {
        suggestionRows[activeSuggestion].classList.add('active');
        suggestionRows[activeSuggestion].scrollIntoView({ block: 'nearest' });
    }
}

function moveActive(delta){
    if (!suggestionRows || suggestionRows.length === 0) return;
    let next = (activeSuggestion === -1) ? 0 : activeSuggestion + delta;
    if (next < 0) next = suggestionRows.length - 1;
    if (next >= suggestionRows.length) next = 0;
    setActive(next);
}

function $it_desc(it){ return it.description || (it.type ? it.type : ''); }

function escapeHtml(s){ if(!s) return ''; return String(s).replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','