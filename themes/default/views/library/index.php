<?php
// themes/default/views/library/index.php
// PREMIUM BLUEPRINT VAULT UI - DARK MODE
?>
<div class="bg-gray-950 min-h-screen font-sans text-gray-100">
    
    <!-- Header / Nav Area -->
    <div class="bg-gray-900 border-b border-gray-800 sticky top-0 z-30 shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="text-blue-500 text-2xl"><i class="fas fa-city"></i></div>
                <h1 class="text-xl font-bold tracking-wide">
                    <a href="/" class="text-white hover:text-blue-400 transition no-underline"><?= APP_NAME ?></a> 
                    <span class="text-gray-500 font-normal border-l border-gray-700 pl-3 ml-2">Blueprint Vault</span>
                </h1>
            </div>
            
            <div class="flex items-center gap-6">
                 <!-- Simple Nav Links -->
                 <nav class="hidden md:flex gap-6 text-sm font-medium text-gray-400">
                     <a href="/" class="hover:text-white transition">Home</a>
                     <a href="/library" class="text-white">Blueprints</a>
                     <a href="/forum" class="hover:text-white transition">Forum</a>
                 </nav>

                <div class="flex items-center gap-3 bg-gray-900 border border-gray-700 px-4 py-1.5 rounded-full shadow-sm">
                    <img src="<?= app_base_url('themes/default/assets/resources/currency/coin.webp') ?>" class="w-8 h-8 object-contain filter drop-shadow-md" alt="BB Coins">
                    <div class="flex flex-col items-center leading-none min-w-[30px]">
                        <span class="font-extrabold text-yellow-400 text-base tracking-wide"><?= number_format($data['coins'] ?? 0) ?></span>
                        <span class="text-[9px] text-gray-500 font-bold uppercase tracking-wider">Coins</span>
                    </div>
                    <a href="<?= app_base_url('/shop') ?>" class="bg-yellow-600 hover:bg-yellow-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold transition ml-1 no-underline shadow-lg group">
                        <i class="fas fa-plus transform group-hover:rotate-90 transition"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Main Feed -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- Toolbar -->
            <div class="bg-gray-900 p-4 rounded-xl shadow-lg border border-gray-800 sticky top-20 z-20 backdrop-blur-md bg-opacity-95">
                <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="relative flex-grow w-full md:max-w-xl">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                        <input type="text" id="searchInput" placeholder="Search blueprints..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-950 border border-gray-800 rounded-lg focus:ring-2 focus:ring-blue-600 text-gray-200 placeholder-gray-600 focus:outline-none transition-all shadow-inner">
                    </div>
                    
                    <div class="flex gap-2 w-full md:w-auto items-center">
                        <select id="typeFilter" class="bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-lg p-2.5 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <option value="cad">CAD / DWG</option>
                            <option value="excel">Excel / XLS</option>
                            <option value="pdf">PDF Docs</option>
                        </select>
                        
                        <a href="<?= app_base_url('/library/upload') ?>" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 whitespace-nowrap shadow-lg hover:shadow-red-900/50 transition transform hover:-translate-y-0.5">
                            <i class="fas fa-cloud-upload-alt"></i> UPLOAD & EARN
                        </a>
                    </div>
                </div>
            </div>

            <!-- Grid -->
            <div id="library-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 min-h-[500px]">
                <!-- Loaded via JS -->
            </div>
            
            <!-- Pagination (Hidden by default) -->
            <div id="pagination-container" class="hidden flex justify-center mt-8">
                <div class="flex gap-2">
                    <button class="px-3 py-1 bg-gray-800 border border-gray-700 rounded text-gray-400 hover:text-white hover:bg-gray-700 transition"><i class="fas fa-chevron-left"></i></button>
                    <button class="px-3 py-1 bg-blue-600 text-white rounded shadow-lg shadow-blue-500/20 font-bold">1</button>
                    <button class="px-3 py-1 bg-gray-800 border border-gray-700 rounded text-gray-400 hover:text-white hover:bg-gray-700 transition"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Contributors -->
            <div class="bg-gray-900 p-5 rounded-xl shadow-lg border border-gray-800">
                <h3 class="font-bold text-gray-200 mb-5 flex items-center gap-2 text-sm uppercase tracking-wider">
                    <i class="fas fa-trophy text-yellow-500"></i> Top Contributors
                </h3>
                <div class="space-y-4" id="leaderboard-list">
                    <!-- Dynamic later -->
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition cursor-pointer group">
                        <img src="https://ui-avatars.com/api/?name=Eng+Sharma&background=0D8ABC&color=fff" class="w-10 h-10 rounded-full border-2 border-gray-700 shadow-sm">
                        <div class="flex-grow">
                            <div class="text-sm font-bold text-gray-200">Eng. Sharma</div>
                            <div class="text-xs text-gray-500">245 Uploads</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bounty Promo -->
            <div class="bg-gradient-to-br from-blue-900 to-indigo-900 p-6 rounded-xl shadow-lg relative overflow-hidden group">
                 <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-white opacity-5 rounded-full group-hover:scale-150 transition duration-700"></div>
                <h3 class="font-bold text-lg text-white mb-2 relative z-10">Need a Blueprint?</h3>
                <p class="text-sm text-blue-200 mb-5 relative z-10">Use our Bounty System.</p>
                <a href="<?= app_base_url('/bounty/create') ?>" class="block w-full text-center bg-white text-blue-900 font-bold py-2.5 rounded-lg text-sm hover:bg-blue-50 transition shadow-lg relative z-10">
                    Post Request
                </a>
            </div>

            <!-- Tags -->
            <div class="bg-gray-900 p-5 rounded-xl shadow-lg border border-gray-800">
                <div class="flex flex-wrap gap-2">
                    <button onclick="filterByTag('Residential')" class="tag-btn text-xs bg-gray-800 text-gray-400 px-3 py-1.5 rounded-md hover:bg-gray-700 hover:text-white transition border border-gray-700">#Residential</button>
                    <button onclick="filterByTag('Structural')" class="tag-btn text-xs bg-gray-800 text-gray-400 px-3 py-1.5 rounded-md hover:bg-gray-700 hover:text-white transition border border-gray-700">#Structural</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm">
    <div class="relative bg-gray-900 rounded-2xl border border-gray-800 max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl flex flex-col">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 id="modalTitle" class="font-bold text-white truncate mr-8">Preview</h3>
            <button onclick="closePreview()" class="text-gray-500 hover:text-white transition w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex-grow overflow-auto p-2 bg-gray-950 flex items-center justify-center min-h-[300px]">
            <img id="modalImage" src="" class="max-w-full max-h-full object-contain" alt="Blueprint Preview">
            <div id="modalLoading" class="absolute inset-0 flex items-center justify-center bg-gray-900 z-10">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            </div>
        </div>
        <div class="p-4 border-t border-gray-800 flex justify-end gap-3">
             <button id="modalActionBtn" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-lg font-bold transition flex items-center gap-2">
                 <i class="fas fa-download"></i> Download
             </button>
        </div>
    </div>
</div>

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = { darkMode: 'class', theme: { extend: { colors: { gray: { 900: '#111827', 950: '#030712' } } } } }
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    loadLibrary();
    document.getElementById('typeFilter').addEventListener('change', (e) => loadLibrary(e.target.value));
    document.getElementById('searchInput').addEventListener('keyup', (e) => { if(e.key === 'Enter') loadLibrary(null, e.target.value); });
});

function filterByTag(tag) {
    document.getElementById('searchInput').value = tag;
    loadLibrary(null, tag);
}

function loadLibrary(type = null, search = null) {
    const grid = document.getElementById('library-grid');
    const pagination = document.getElementById('pagination-container');
    
    grid.innerHTML = '<div class="col-span-full py-20 flex flex-col items-center justify-center text-gray-500 h-full"><div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mb-4"></div>Searching Vault...</div>';
    pagination.classList.add('hidden');

    let url = "<?= app_base_url('/api/library/browse') ?>?page=1";
    if (type) url += `&type=${type}`;
    
    fetch(url)
        .then(res => {
            if(!res.ok) throw new Error(`HTTP Error ${res.status}`);
            return res.json();
        })
        .then(data => {
            if (!data.success || !data.files || !data.files.length) {
                grid.innerHTML = '<div class="col-span-full flex flex-col items-center justify-center text-center text-gray-600 bg-gray-900 rounded-xl border border-gray-800 h-full min-h-[300px]"><i class="fas fa-folder-open text-5xl mb-4 text-gray-700"></i><p class="text-xl font-bold text-gray-500">Vault Empty</p><p class="text-sm">No blueprints found in this category.</p></div>';
                return;
            }

            // Show pagination if needed (logic can be improved)
            if(data.files.length > 20) pagination.classList.remove('hidden');

            grid.innerHTML = data.files.map(file => {
                let iconUrl = 'https://cdn-icons-png.flaticon.com/512/2965/2965335.png';
                if (['dwg','dxf'].includes(file.file_type)) iconUrl = 'https://cdn-icons-png.flaticon.com/512/8243/8243060.png';
                if (['xls','xlsx'].includes(file.file_type)) iconUrl = 'https://cdn-icons-png.flaticon.com/512/888/888850.png';
                if (['pdf'].includes(file.file_type)) iconUrl = 'https://cdn-icons-png.flaticon.com/512/337/337946.png';

                const isUnlocked = file.is_unlocked || file.price_coins == 0;
                const btnLabel = isUnlocked ? 'Download' : `Unlock (${file.price_coins} BB)`;
                const btnColor = isUnlocked ? 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-900/30' : 'bg-blue-600 hover:bg-blue-500 shadow-blue-900/30';
                const btnAction = isUnlocked ? `downloadFile(${file.id})` : `unlockFile(${file.id})`;
                const btnIcon = isUnlocked ? 'fa-download' : 'fa-lock';

                let previewHtml = file.preview_path ? 
                    `<img src="<?= app_base_url('/storage/library/') ?>${file.preview_path}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">` : 
                    `<div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-700"><i class="fas fa-file-alt text-6xl"></i></div>`;

                let previewBtn = file.preview_path ? 
                    `<button onclick="openPreview(${file.id}, '${file.title.replace(/'/g, "\\'")}', '${file.preview_path}', ${isUnlocked})" class="absolute top-2 right-2 z-30 bg-black/60 hover:bg-black/80 backdrop-blur text-white p-2 rounded-full shadow-lg transition opacity-0 group-hover:opacity-100">
                        <i class="fas fa-eye"></i>
                    </button>` : '';

                return `
                <div class="bg-gray-900 rounded-xl overflow-hidden shadow-lg hover:shadow-blue-900/20 transition-all border border-gray-800 group flex flex-col hover:-translate-y-1">
                    <div class="relative h-48 bg-gray-800 overflow-hidden border-b border-gray-800">
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10 opacity-30 select-none ${isUnlocked ? 'hidden' : ''}">
                            <div class="transform -rotate-12 text-white font-extrabold text-2xl whitespace-nowrap border-4 border-white px-4 py-1">PREVIEW ONLY</div>
                        </div>
                        ${previewBtn}
                        <div class="absolute bottom-2 left-2 z-20">
                            <img src="${iconUrl}" class="w-8 h-8 drop-shadow-md bg-white rounded-md p-0.5" alt="${file.file_type}">
                        </div>
                        <div class="absolute bottom-2 right-2 z-20 bg-gray-900/90 backdrop-blur text-green-400 text-xs font-bold px-2 py-1 rounded shadow-sm border border-green-900/50 flex items-center gap-1">
                            <i class="fas fa-tag"></i> ${file.price_coins > 0 ? file.price_coins + ' BB' : 'FREE'}
                        </div>
                        ${previewHtml}
                    </div>
                    <div class="p-4 flex-grow flex flex-col">
                        <h3 class="font-bold text-gray-100 text-lg mb-1 leading-tight line-clamp-1 group-hover:text-blue-400 transition" title="${file.title}">${file.title}</h3>
                        <p class="text-xs text-gray-500 mb-3 line-clamp-2">${file.description || 'No description provided.'}</p>
                        <div class="flex items-center gap-3 mb-4 text-xs text-gray-500">
                             <div class="flex items-center gap-1"><i class="fas fa-file-alt"></i> ${file.file_type.toUpperCase()}</div>
                             <div class="flex items-center gap-1"><i class="fas fa-hdd"></i> ${file.file_size_kb} KB</div>
                             <div class="flex items-center gap-1"><i class="fas fa-download"></i> ${file.downloads_count}</div>
                        </div>
                        <div class="mt-auto flex items-center justify-between pt-3 border-t border-gray-800">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-900 flex items-center justify-center text-[10px] font-bold text-blue-200 uppercase border border-blue-800">
                                    ${file.uploader_name ? file.uploader_name.substring(0,2) : 'UR'}
                                </div>
                                <span class="text-xs font-medium text-gray-400">${file.uploader_name || 'User'}</span>
                            </div>
                            <button onclick="${btnAction}" class="${btnColor} text-white text-xs px-3 py-1.5 rounded font-bold transition flex items-center gap-1">
                                <i class="fas ${btnIcon}"></i> ${btnLabel}
                            </button>
                        </div>
                    </div>
                </div>`;
            }).join('');
        })
        .catch(err => {
             console.error(err);
             grid.innerHTML = `<div class="col-span-full text-center py-20 text-red-400 bg-gray-900/50 rounded-lg p-6 border border-red-900/50">
                <i class="fas fa-exclamation-triangle text-3xl mb-3 block"></i>
                <span class="font-bold block text-lg">Failed to load vault</span>
                <span class="text-xs text-red-300/70">${err.message}</span>
             </div>`;
        });
}

function openPreview(id, title, path, unlocked) {
    const modal = document.getElementById('previewModal');
    const img = document.getElementById('modalImage');
    const loader = document.getElementById('modalLoading');
    const btn = document.getElementById('modalActionBtn');
    
    document.getElementById('modalTitle').textContent = title;
    img.src = "<?= app_base_url('/storage/library/') ?>" + path;
    loader.style.display = 'flex';
    
    img.onload = () => loader.style.display = 'none';
    
    if (unlocked) {
        btn.innerHTML = '<i class="fas fa-download"></i> Download';
        btn.onclick = () => downloadFile(id);
        btn.className = "bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2 rounded-lg font-bold transition flex items-center gap-2";
    } else {
        btn.innerHTML = '<i class="fas fa-lock"></i> Unlock Blueprint';
        btn.onclick = () => { closePreview(); unlockFile(id); };
        btn.className = "bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-lg font-bold transition flex items-center gap-2";
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function unlockFile(id) {
    if(!confirm('Unlock this blueprint for the listed BB Coins?')) return;
    
    fetch('<?= app_base_url("/api/library/unlock") ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({file_id: id})
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Unlocked! Starting download...');
            downloadFile(id);
            loadLibrary(); // Reload to update UI state
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => alert('Signal Loss: ' + err.message));
}

function downloadFile(id) {
     window.location.href = `<?= app_base_url("/api/library/download") ?>?id=${id}`;
}
</script>
