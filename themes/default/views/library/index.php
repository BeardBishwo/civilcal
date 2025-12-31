<?php
// themes/default/views/library/index.php
?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Blueprint Vault</h1>
            <p class="text-gray-600">Premium Civil Engineering Resources</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full font-bold flex items-center gap-2">
                <span>🪙</span>
                <span id="user-coins"><?= $data['coins'] ?></span> Coins
            </div>
            <a href="/library/upload" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                Upload Resource (+100 Coins)
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-4 mb-8 overflow-x-auto pb-2">
        <button class="filter-btn active bg-gray-800 text-white px-4 py-1 rounded-full" data-type="">All</button>
        <button class="filter-btn bg-gray-200 text-gray-700 px-4 py-1 rounded-full hover:bg-gray-300" data-type="cad">CAD / DWG</button>
        <button class="filter-btn bg-gray-200 text-gray-700 px-4 py-1 rounded-full hover:bg-gray-300" data-type="excel">Excel Sheets</button>
        <button class="filter-btn bg-gray-200 text-gray-700 px-4 py-1 rounded-full hover:bg-gray-300" data-type="pdf">PDF Docs</button>
    </div>

    <!-- Grid -->
    <div id="library-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Items will be loaded here via AJAX -->
        <div class="col-span-full text-center py-12 text-gray-500">
            Loading resources...
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadResources();

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('bg-gray-800', 'text-white');
                b.classList.add('bg-gray-200', 'text-gray-700');
            });
            this.classList.remove('bg-gray-200', 'text-gray-700');
            this.classList.add('bg-gray-800', 'text-white');
            loadResources(this.dataset.type);
        });
    });
});

function loadResources(type = '') {
    const grid = document.getElementById('library-grid');
    grid.innerHTML = '<div class="col-span-full text-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div></div>';

    fetch(`/api/library/browse?type=${type}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.files.length) {
                grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500">No resources found.</div>';
                return;
            }

            grid.innerHTML = data.files.map(file => `
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100">
                    <div class="h-40 bg-gray-50 flex items-center justify-center border-b">
                        <span class="text-4xl">${getFileIcon(file.file_type)}</span>
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded">${file.file_type}</span>
                            <span class="text-xs text-gray-400">${formatDate(file.created_at)}</span>
                        </div>
                        <h3 class="font-bold text-lg mb-1 line-clamp-1" title="${file.title}">${file.title}</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">${file.description || 'No description provided.'}</p>
                        
                        <div class="flex items-center justify-between mt-4">
                            <div class="text-sm text-gray-500">
                                ⬇ ${file.downloads_count}
                            </div>
                            <button onclick="downloadFile(${file.id}, ${file.price_coins})" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                                <span>🔒 Unlock</span>
                                <span class="bg-gray-700 px-1.5 py-0.5 rounded text-xs">${file.price_coins} C</span>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => {
            console.error(err);
            grid.innerHTML = '<div class="col-span-full text-center py-12 text-red-500">Failed to load resources.</div>';
        });
}

function getFileIcon(type) {
    const icons = {
        'cad': '📐',
        'excel': '📊',
        'pdf': '📄',
        'image': '🖼️',
        'doc': '📝'
    };
    return icons[type] || '📁';
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString();
}

function downloadFile(id, price) {
    if (!confirm(`Unlock this resource for ${price} Coins?`)) return;

    // Trigger download
    window.location.href = `/api/library/download?id=${id}`;
    
    // Optimistically update coins (or reload page)
    // setTimeout(() => location.reload(), 1000); 
}
</script>
