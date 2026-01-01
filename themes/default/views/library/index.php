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
                <h1 class="text-xl font-bold tracking-wide"><?= APP_NAME ?> <span class="text-gray-500 font-normal border-l border-gray-700 pl-3 ml-2">Blueprint Vault</span></h1>
            </div>
            
            <div class="flex items-center gap-6">
                 <!-- Simple Nav Links -->
                 <nav class="hidden md:flex gap-6 text-sm font-medium text-gray-400">
                     <a href="/" class="hover:text-white transition">Home</a>
                     <a href="/library" class="text-white">Blueprints</a>
                     <a href="/forum" class="hover:text-white transition">Forum</a>
                 </nav>

                <div class="flex items-center gap-3 bg-gray-800 px-3 py-1.5 rounded-full border border-gray-700">
                    <i class="fas fa-coins text-yellow-500 text-lg"></i>
                    <div class="flex flex-col leading-none">
                        <span class="font-bold text-yellow-500 text-sm"><?= number_format($data['coins'] ?? 0) ?></span>
                        <span class="text-[10px] text-gray-500 uppercase">BB Coins</span>
                    </div>
                    <a href="/shop" class="bg-yellow-600 hover:bg-yellow-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs transition ml-1 no-underline">+</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Main Feed (3 Columns on Large) -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- Search & Filters Toolbar -->
            <div class="bg-gray-900 p-4 rounded-xl shadow-lg border border-gray-800 sticky top-20 z-20 backdrop-blur-md bg-opacity-95">
                <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                    <!-- Search -->
                    <div class="relative flex-grow w-full md:max-w-xl">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                        <input type="text" id="searchInput" placeholder="Search blueprints, Excel sheets..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-950 border border-gray-800 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent text-gray-200 placeholder-gray-600 focus:outline-none transition-all shadow-inner">
                    </div>
                    
                    <!-- Filters -->
                    <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-1 md:pb-0 items-center">
                        <select id="typeFilter" class="bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                            <option value="">All Types</option>
                            <option value="cad">CAD / DWG</option>
                            <option value="excel">Excel / XLS</option>
                            <option value="pdf">PDF Docs</option>
                        </select>
                        
                        <a href="/library/upload" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 whitespace-nowrap shadow-lg hover:shadow-red-900/50 transition transform hover:-translate-y-0.5">
                            <i class="fas fa-cloud-upload-alt"></i> UPLOAD & EARN
                        </a>
                    </div>
                </div>
            </div>

            <!-- Library Grid -->
            <div id="library-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Javascript will populate this -->
                <div class="col-span-full py-20 flex flex-col items-center justify-center text-gray-600">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mb-4"></div>
                    <p>Loading Vault...</p>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-center mt-8">
                <div class="flex gap-2">
                    <button class="px-3 py-1 bg-gray-800 border border-gray-700 rounded text-gray-400 hover:text-white hover:bg-gray-700 transition"><i class="fas fa-chevron-left"></i></button>
                    <button class="px-3 py-1 bg-blue-600 text-white rounded shadow-lg shadow-blue-500/20 font-bold">1</button>
                    <button class="px-3 py-1 bg-gray-800 border border-gray-700 rounded text-gray-400 hover:text-white hover:bg-gray-700 transition">2</button>
                    <button class="px-3 py-1 bg-gray-800 border border-gray-700 rounded text-gray-400 hover:text-white hover:bg-gray-700 transition">3</button>
                    <button class="px-3 py-1 bg-gray-800 border border-gray-700 rounded text-gray-400 hover:text-white hover:bg-gray-700 transition"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

        </div>

        <!-- Sidebar (Right) -->
        <div class="space-y-6">
            
            <!-- Top Contributors -->
            <div class="bg-gray-900 p-5 rounded-xl shadow-lg border border-gray-800">
                <h3 class="font-bold text-gray-200 mb-5 flex items-center gap-2 text-sm uppercase tracking-wider">
                    <i class="fas fa-trophy text-yellow-500"></i> Top Contributors
                </h3>
                <div class="space-y-4" id="leaderboard-list">
                    <!-- Static for now, can be dynamic -->
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition cursor-pointer group">
                        <div class="relative">
                             <img src="https://ui-avatars.com/api/?name=Eng+Sharma&background=0D8ABC&color=fff" class="w-10 h-10 rounded-full border-2 border-gray-700 shadow-sm group-hover:border-blue-500 transition">
                             <div class="absolute -top-1 -right-1 bg-yellow-500 text-black text-[10px] font-bold px-1.5 rounded-full border border-gray-900">1</div>
                        </div>
                        <div class="flex-grow">
                            <div class="text-sm font-bold text-gray-200 group-hover:text-blue-400 transition">Eng. Sharma</div>
                            <div class="text-xs text-gray-500">245 Uploads • 12.5k Coins</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition cursor-pointer group">
                         <div class="relative">
                             <img src="https://ui-avatars.com/api/?name=Rahul+K&background=random" class="w-10 h-10 rounded-full border-2 border-gray-700 shadow-sm group-hover:border-blue-500 transition">
                             <div class="absolute -top-1 -right-1 bg-gray-400 text-black text-[10px] font-bold px-1.5 rounded-full border border-gray-900">2</div>
                        </div>
                        <div class="flex-grow">
                            <div class="text-sm font-bold text-gray-200 group-hover:text-blue-400 transition">Rahul K.</div>
                            <div class="text-xs text-gray-500">120 Uploads • 5.2k Coins</div>
                        </div>
                    </div>
                </div>
                <button onclick="document.getElementById('leaderboard-list').innerHTML += '<div class=\'text-center py-2 text-xs text-gray-500\'>Loading more...</div>'" class="w-full mt-4 text-xs text-blue-500 font-medium hover:text-blue-400 transition border-t border-gray-800 pt-3">View Full Leaderboard</button>
            </div>

            <!-- Request / Promo -->
            <div class="bg-gradient-to-br from-blue-900 to-indigo-900 p-6 rounded-xl shadow-lg relative overflow-hidden group">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-white opacity-5 rounded-full group-hover:scale-150 transition duration-700"></div>
                
                <h3 class="font-bold text-lg text-white mb-2 relative z-10">Need a Blueprint?</h3>
                <p class="text-sm text-blue-200 mb-5 relative z-10">Use our Bounty System to request specific files from the community.</p>
                <a href="/bounty/create" class="block w-full text-center bg-white text-blue-900 font-bold py-2.5 rounded-lg text-sm hover:bg-blue-50 transition shadow-lg relative z-10">
                    Post Request
                </a>
            </div>

            <!-- Tags -->
            <div class="bg-gray-900 p-5 rounded-xl shadow-lg border border-gray-800">
                <h3 class="font-bold text-gray-200 mb-4 text-sm uppercase tracking-wide">Popular Tags</h3>
                <div class="flex flex-wrap gap-2">
                    <button onclick="filterByTag('Residential')" class="tag-btn text-xs bg-gray-800 text-gray-400 px-3 py-1.5 rounded-md hover:bg-gray-700 hover:text-white transition border border-gray-700">#Residential</button>
                    <button onclick="filterByTag('Structural')" class="tag-btn text-xs bg-gray-800 text-gray-400 px-3 py-1.5 rounded-md hover:bg-gray-700 hover:text-white transition border border-gray-700">#Structural</button>
                    <button onclick="filterByTag('Excel')" class="tag-btn text-xs bg-gray-800 text-gray-400 px-3 py-1.5 rounded-md hover:bg-gray-700 hover:text-white transition border border-gray-700">#Excel</button>
                    <button onclick="filterByTag('Plumbing')" class="tag-btn text-xs bg-gray-800 text-gray-400 px-3 py-1.5 rounded-md hover:bg-gray-700 hover:text-white transition border border-gray-700">#Plumbing</button>
                    <button onclick="filterByTag('HVAC')" class="tag-btn text-xs bg-gray-800 text-gray-400 px-3 py-1.5 rounded-md hover:bg-gray-700 hover:text-white transition border border-gray-700">#HVAC</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    gray: {
                        900: '#111827',
                        950: '#030712',
                    }
                }
            }
        }
    }
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    loadLibrary();
    
    document.getElementById('typeFilter').addEventListener('change', (e) => {
        loadLibrary(e.target.value);
    });
    
    document.getElementById('searchInput').addEventListener('keyup', (e) => {
        // Simple search debounce could be added here
        if(e.key === 'Enter') loadLibrary(null, e.target.value);
    });
});

function filterByTag(tag) {
    document.getElementById('searchInput').value = tag;
    loadLibrary(null, tag);
}

function loadLibrary(type = null, search = null) {
    const grid = document.getElementById('library-grid');
    grid.innerHTML = '<div class="col-span-full py-20 flex flex-col items-center justify-center text-gray-500"><div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mb-4"></div>Searching Vault...</div>';

    let url = `/api/library/browse?page=1`;
    if (type) url += `&type=${type}`;
    // Search would handle server side ideally, but for now we simulate or basic type filtering
    
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.files.length) {
                grid.innerHTML = '<div class="col-span-full text-center py-20 text-gray-600 bg-gray-900 rounded-xl border border-gray-800"><i class="fas fa-folder-open text-4xl mb-4 text-gray-700"></i><p>No blueprints found.</p></div>';
                return;
            }

            grid.innerHTML = data.files.map(file => {
                // Determine Icon
                let iconUrl = 'https://cdn-icons-png.flaticon.com/512/2965/2965335.png'; // File
                if (['dwg','dxf'].includes(file.file_type)) iconUrl = 'https://cdn-icons-png.flaticon.com/512/8243/8243060.png';
                if (['xls','xlsx'].includes(file.file_type)) iconUrl = 'https://cdn-icons-png.flaticon.com/512/888/888850.png';
                if (['pdf'].includes(file.file_type)) iconUrl = 'https://cdn-icons-png.flaticon.com/512/337/337946.png';

                // Determine Preview
                let previewHtml = '';
                if (file.preview_path) {
                    previewHtml = `<img src="/storage/library/${file.preview_path}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500 filter blur-[1px] group-hover:blur-0">`;
                } else {
                    // Placeholder based on type
                    previewHtml = `<div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-700"><i class="fas fa-file-alt text-6xl"></i></div>`;
                }

                return `
                <div class="bg-gray-900 rounded-xl overflow-hidden shadow-lg hover:shadow-blue-900/20 transition-all border border-gray-800 group flex flex-col hover:-translate-y-1">
                    <!-- Preview Image Area -->
                    <div class="relative h-48 bg-gray-800 overflow-hidden border-b border-gray-800">
                        <!-- Watermark Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10 opacity-30 select-none">
                            <div class="transform -rotate-12 text-white font-extrabold text-2xl whitespace-nowrap border-4 border-white px-4 py-1">PREVIEW ONLY</div>
                        </div>
                        
                        <!-- File Type Icon -->
                        <div class="absolute bottom-2 left-2 z-20">
                            <img src="${iconUrl}" class="w-8 h-8 drop-shadow-md bg-white rounded-md p-0.5" alt="${file.file_type}">
                        </div>
                        
                        <!-- Price Tag -->
                        <div class="absolute bottom-2 right-2 z-20 bg-gray-900/90 backdrop-blur text-green-400 text-xs font-bold px-2 py-1 rounded shadow-sm border border-green-900/50 flex items-center gap-1">
                            <i class="fas fa-tag"></i> ${file.price_coins > 0 ? file.price_coins + ' BB' : 'FREE'}
                        </div>

                        ${previewHtml}
                    </div>

                    <!-- Content -->
                    <div class="p-4 flex-grow flex flex-col">
                        <h3 class="font-bold text-gray-100 text-lg mb-1 leading-tight line-clamp-1 group-hover:text-blue-400 transition" title="${file.title}">${file.title}</h3>
                        <p class="text-xs text-gray-500 mb-3 line-clamp-2">${file.description || 'No description provided.'}</p>
                        
                        <!-- Meta -->
                        <div class="flex items-center gap-3 mb-4 text-xs text-gray-500">
                             <div class="flex items-center gap-1"><i class="fas fa-file-alt"></i> ${file.file_type.toUpperCase()}</div>
                             <div class="flex items-center gap-1"><i class="fas fa-hdd"></i> ${file.file_size_kb} KB</div>
                             <div class="flex items-center gap-1"><i class="fas fa-download"></i> ${file.downloads_count}</div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-auto flex items-center justify-between pt-3 border-t border-gray-800">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-900 flex items-center justify-center text-[10px] font-bold text-blue-200 uppercase border border-blue-800">
                                    ${file.uploader_name ? file.uploader_name.substring(0,2) : 'UR'}
                                </div>
                                <span class="text-xs font-medium text-gray-400">${file.uploader_name || 'User'}</span>
                            </div>
                            
                            <!-- Action Button -->
                            ${file.price_coins > 0 ? 
                                `<button onclick="unlockFile(${file.id})" class="bg-blue-600 hover:bg-blue-500 text-white text-xs px-3 py-1.5 rounded font-bold transition shadow-lg shadow-blue-900/30 flex items-center gap-1">
                                    <i class="fas fa-lock"></i> Unlock
                                </button>` : 
                                `<button onclick="downloadFile(${file.id})" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs px-3 py-1.5 rounded font-bold transition shadow-lg shadow-emerald-900/30 flex items-center gap-1">
                                    <i class="fas fa-download"></i> Download
                                </button>`
                            }
                        </div>
                    </div>
                </div>
                `;
            }).join('');
        })
        .catch(err => {
             console.error(err);
             grid.innerHTML = '<div class="col-span-full text-center py-20 text-red-400">Failed to load vault contents.</div>';
        });
}

function unlockFile(id) {
    if(!confirm('Unlock this blueprint for the listed BB Coins?')) return;
    
    fetch('/api/library/unlock', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({file_id: id})
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Unlocked! Starting download...');
            window.location.href = `/api/library/download?id=${id}`;
            loadLibrary(); // Reload to update UI state
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function downloadFile(id) {
     window.location.href = `/api/library/download?id=${id}`;
}
</script>
