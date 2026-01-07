<?php
// themes/default/views/library/index.php
// PREMIUM BLUEPRINT VAULT UI - DARK MODE
?>
<!-- Load Local Library CSS -->
<link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/library.min.css?v=' . time()); ?>">

<!-- Load Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="bg-background min-h-screen font-sans text-gray-100 relative overflow-hidden" x-data="libraryVault()">
    
    <!-- Background Gradient Orbs -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-primary/20 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-secondary/20 rounded-full blur-[100px] animate-pulse delay-1000"></div>
    </div>

    <!-- Header / Nav Area -->
    <div class="glass border-b border-white/5 sticky top-0 z-30">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center text-white shadow-lg shadow-blue-900/50">
                    <i class="fas fa-city"></i>
                </div>
                <h1 class="text-xl font-bold tracking-wide">
                    <a href="/" class="text-white hover:text-blue-400 transition no-underline"><?= APP_NAME ?></a> 
                    <span class="text-gray-500 font-normal border-l border-white/10 pl-3 ml-2">Blueprint Vault</span>
                </h1>
            </div>
            
            <div class="flex items-center gap-6">
                 <!-- Simple Nav Links -->
                 <nav class="hidden md:flex gap-6 text-sm font-medium text-gray-400">
                     <a href="/" class="hover:text-white transition">Home</a>
                     <a href="/library" class="text-white">Blueprints</a>
                     <a href="/forum" class="hover:text-white transition">Forum</a>
                 </nav>

                <div class="flex items-center gap-3 bg-black/40 border border-white/10 px-4 py-1.5 rounded-full shadow-inner">
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
    <div class="container mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-4 gap-8 relative z-10">
        
        <!-- Main Feed -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- Toolbar -->
            <div class="glass p-4 rounded-xl sticky top-24 z-20">
                <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="relative flex-grow w-full md:max-w-xl group">
                        <i class="fas fa-search absolute left-4 top-3.5 text-gray-500 group-focus-within:text-blue-400 transition-colors"></i>
                        <input type="text" x-model="searchQuery" @keyup.enter="loadFiles()" 
                               class="glass-input w-full pl-12 pr-4 py-3 bg-black/20"
                               placeholder="Search blueprints (e.g., 'Villa', 'Structural') ...">
                    </div>
                    
                    <div class="flex gap-2 w-full md:w-auto items-center">
                        <select x-model="selectedType" @change="loadFiles()" class="glass-input bg-black/20 py-3">
                            <option value="">All Types</option>
                            <option value="cad">CAD / DWG</option>
                            <option value="excel">Excel / XLS</option>
                            <option value="pdf">PDF Docs</option>
                        </select>
                        
                        <a href="<?= app_base_url('/library/upload') ?>" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white px-5 py-3 rounded-xl text-sm font-bold flex items-center gap-2 whitespace-nowrap shadow-lg hover:shadow-red-900/50 transition transform hover:-translate-y-0.5">
                            <i class="fas fa-cloud-upload-alt"></i> UPLOAD
                        </a>
                    </div>
                </div>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 min-h-[500px]">
                
                <!-- Loading State -->
                <div x-show="loading" class="col-span-full py-20 flex flex-col items-center justify-center text-gray-500">
                    <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-blue-400 animate-pulse">Accessing Vault...</span>
                </div>

                <!-- Empty State -->
                <div x-show="!loading && files.length === 0" class="col-span-full flex flex-col items-center justify-center text-center text-gray-400 glass p-12 rounded-2xl" style="display: none;">
                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-6 text-3xl text-gray-600">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Vault Empty</h3>
                    <p class="max-w-sm mx-auto">No blueprints found matching your criteria. Try adjusting your search filters.</p>
                </div>

                <!-- File Cards -->
                <template x-for="file in files" :key="file.id">
                    <div class="glass-card p-0 overflow-hidden group flex flex-col h-full hover:-translate-y-2">
                        <!-- Preview Header -->
                        <div class="relative h-48 bg-black/40 overflow-hidden border-b border-white/5 group-hover:border-primary/30 transition-colors">
                            <!-- Locked Overlay -->
                            <div x-show="!isUnlocked(file)" class="absolute inset-0 flex items-center justify-center pointer-events-none z-10 bg-black/20 backdrop-blur-[1px]">
                                <div class="transform -rotate-12 bg-white/10 border-2 border-white/20 px-4 py-1 rounded text-white font-black text-xl tracking-widest shadow-2xl">PREVIEW</div>
                            </div>

                            <!-- Preview Image -->
                            <template x-if="file.preview_path">
                                <img :src="'<?= app_base_url('/storage/library/') ?>' + file.preview_path" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                            </template>
                            <template x-if="!file.preview_path">
                                <div class="w-full h-full flex items-center justify-center bg-white/5 text-white/20">
                                    <i class="fas fa-file-alt text-6xl"></i>
                                </div>
                            </template>

                            <!-- Actions Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-4">
                                <button @click="openModal(file)" class="w-full py-2 bg-white text-black font-bold rounded-lg hover:bg-blue-50 transition shadow-lg transform translate-y-4 group-hover:translate-y-0 duration-300 flex items-center justify-center gap-2">
                                    <i class="fas fa-eye"></i> Quick View
                                </button>
                            </div>

                            <!-- Price Tag -->
                            <div class="absolute top-3 right-3 z-20">
                                <span class="px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider shadow-lg flex items-center gap-1"
                                      :class="file.price_coins > 0 ? 'bg-black/60 text-yellow-400 backdrop-blur border border-yellow-500/30' : 'bg-green-500/90 text-white'">
                                    <i class="fas" :class="file.price_coins > 0 ? 'fa-coins' : 'fa-check'"></i>
                                    <span x-text="file.price_coins > 0 ? file.price_coins + ' BB' : 'FREE'"></span>
                                </span>
                            </div>

                             <!-- Type Icon -->
                             <div class="absolute bottom-3 left-3 z-20">
                                <div class="w-10 h-10 bg-surface backdrop-blur border border-white/10 rounded-lg flex items-center justify-center shadow-lg">
                                    <img :src="getIcon(file.file_type)" class="w-6 h-6 object-contain">
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-grow flex flex-col">
                            <h3 class="font-bold text-lg mb-2 leading-snug text-white line-clamp-1 group-hover:text-primary transition-colors" x-text="file.title"></h3>
                            <p class="text-xs text-gray-400 mb-4 line-clamp-2" x-text="file.description || 'No description provided.'"></p>
                            
                            <div class="grid grid-cols-2 gap-2 mb-4 text-[10px] uppercase font-bold tracking-wider text-gray-500">
                                <div class="flex items-center gap-2 bg-white/5 rounded px-2 py-1"><i class="fas fa-file"></i> <span x-text="file.file_type"></span></div>
                                <div class="flex items-center gap-2 bg-white/5 rounded px-2 py-1"><i class="fas fa-hdd"></i> <span x-text="file.file_size_kb + ' KB'"></span></div>
                            </div>

                            <div class="mt-auto flex items-center justify-between pt-4 border-t border-white/5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-gray-700 to-gray-800 flex items-center justify-center text-xs font-bold text-gray-300 ring-2 ring-black">
                                        <span x-text="(file.uploader_name || 'U').substring(0,2)"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-300" x-text="file.uploader_name || 'User'"></span>
                                        <span class="text-[10px] text-gray-600" x-text="file.downloads_count + ' Downloads'"></span>
                                    </div>
                                </div>
                                
                                <button @click="handleAction(file)" 
                                        class="px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition-all shadow-lg"
                                        :class="isUnlocked(file) ? 'bg-green-600 hover:bg-green-500 text-white shadow-green-900/20' : 'bg-primary hover:bg-blue-500 text-white shadow-blue-900/20'">
                                    <i class="fas" :class="isUnlocked(file) ? 'fa-download' : 'fa-lock'"></i>
                                    <span x-text="isUnlocked(file) ? 'GET' : 'UNLOCK'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-center mt-8" x-show="files.length > 0">
                <div class="glass px-4 py-2 rounded-full flex gap-4">
                     <button @click="changePage(page - 1)" :disabled="page <= 1" class="text-gray-400 hover:text-white disabled:opacity-50 transition"><i class="fas fa-chevron-left"></i></button>
                     <span class="font-bold text-white px-2" x-text="page"></span>
                     <button @click="changePage(page + 1)" :disabled="files.length < 12" class="text-gray-400 hover:text-white disabled:opacity-50 transition"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Contributors -->
            <div class="glass-card">
                <h3 class="font-bold text-white mb-5 flex items-center gap-2 text-sm uppercase tracking-wider">
                    <i class="fas fa-trophy text-yellow-500"></i> Elite Contributors
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition cursor-pointer group border border-white/5">
                        <img src="https://ui-avatars.com/api/?name=Eng+Sharma&background=0D8ABC&color=fff" class="w-10 h-10 rounded-full border-2 border-primary/50 shadow-sm">
                        <div class="flex-grow">
                            <div class="text-sm font-bold text-white group-hover:text-primary transition">Eng. Sharma</div>
                            <div class="text-[10px] text-gray-400 uppercase tracking-widest">Master Architect</div>
                        </div>
                        <div class="text-yellow-500 font-bold text-xs"><i class="fas fa-star"></i> 4.9</div>
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="glass-card">
                <h3 class="font-bold text-white mb-4 text-sm uppercase tracking-wider">Popular Tags</h3>
                <div class="flex flex-wrap gap-2">
                    <button @click="setSearch('Residential')" class="text-xs bg-white/5 text-gray-400 px-3 py-1.5 rounded-lg hover:bg-primary hover:text-white transition border border-white/10">#Residential</button>
                    <button @click="setSearch('Structural')" class="text-xs bg-white/5 text-gray-400 px-3 py-1.5 rounded-lg hover:bg-primary hover:text-white transition border border-white/10">#Structural</button>
                    <button @click="setSearch('Electrical')" class="text-xs bg-white/5 text-gray-400 px-3 py-1.5 rounded-lg hover:bg-primary hover:text-white transition border border-white/10">#Electrical</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="modalOpen" style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
         <div class="relative bg-surface border border-white/10 rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden shadow-2xl flex flex-col" @click.outside="modalOpen = false">
            
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-black/40">
                <div>
                    <h3 class="font-bold text-2xl text-white truncate max-w-md" x-text="activeFile?.title"></h3>
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                         <span class="flex items-center gap-1"><i class="fas fa-file"></i> <span x-text="activeFile?.file_type.toUpperCase()"></span></span>
                         <span class="flex items-center gap-1"><i class="fas fa-user"></i> <span x-text="activeFile?.uploader_name"></span></span>
                    </div>
                </div>
                <button @click="modalOpen = false" class="w-10 h-10 rounded-full bg-white/5 hover:bg-red-500/20 hover:text-red-500 transition flex items-center justify-center text-gray-400">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-grow overflow-auto p-8 bg-black/20 flex items-center justify-center min-h-[400px] relative">
                 <template x-if="activeFile?.preview_path">
                    <img :src="'<?= app_base_url('/storage/library/') ?>' + activeFile.preview_path" class="max-w-full max-h-[60vh] object-contain rounded-lg shadow-2xl border border-white/5">
                 </template>
                 <template x-if="!activeFile?.preview_path">
                    <div class="text-center text-gray-600">
                        <i class="fas fa-eye-slash text-6xl mb-4"></i>
                        <p>No preview available for this file.</p>
                    </div>
                 </template>
            </div>

            <div class="p-6 border-t border-white/10 bg-black/40 flex justify-between items-center">
                <div class="text-sm text-gray-400">
                    Price: <span class="font-bold text-white" x-text="activeFile?.price_coins > 0 ? activeFile.price_coins + ' BB' : 'Free'"></span>
                </div>
                <button @click="handleAction(activeFile)" 
                        class="px-8 py-3 rounded-xl font-bold transition flex items-center gap-2 shadow-lg"
                        :class="isUnlocked(activeFile) ? 'bg-green-600 hover:bg-green-500 text-white shadow-green-900/30' : 'bg-primary hover:bg-blue-500 text-white shadow-blue-900/30'">
                    <i class="fas" :class="isUnlocked(activeFile) ? 'fa-download' : 'fa-lock'"></i>
                    <span x-text="isUnlocked(activeFile) ? 'Download Now' : 'Unlock Asset'"></span>
                </button>
            </div>

         </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('libraryVault', () => ({
        files: [],
        loading: true,
        searchQuery: '',
        selectedType: '',
        page: 1,
        modalOpen: false,
        activeFile: null,
        
        init() {
            this.loadFiles();
        },

        loadFiles() {
            this.loading = true;
            let url = '<?= app_base_url("/api/library/browse") ?>?page=' + this.page;
            if (this.searchQuery) url += '&search=' + encodeURIComponent(this.searchQuery);
            if (this.selectedType) url += '&type=' + this.selectedType;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    this.files = data.files || [];
                    this.loading = false;
                })
                .catch(err => {
                    console.error('Vault Access Error:', err);
                    this.loading = false;
                    this.files = [];
                });
        },

        changePage(newPage) {
            this.page = newPage;
            this.loadFiles();
        },

        setSearch(term) {
            this.searchQuery = term;
            this.loadFiles();
        },

        isUnlocked(file) {
            if (!file) return false;
            return file.is_unlocked || file.price_coins == 0;
        },

        openModal(file) {
            this.activeFile = file;
            this.modalOpen = true;
        },

        handleAction(file) {
            if (!file) return;

            if (this.isUnlocked(file)) {
                window.location.href = `<?= app_base_url("/api/library/download") ?>?id=${file.id}`;
            } else {
                this.unlockFile(file);
            }
        },

        unlockFile(file) {
            if(!confirm(`Unlock "${file.title}" for ${file.price_coins} BB Coins?`)) return;

            fetch('<?= app_base_url("/api/library/unlock") ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({file_id: file.id})
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('Asset Unlocked! Initializing download...');
                    file.is_unlocked = true; // Optimistic update
                    window.location.href = `<?= app_base_url("/api/library/download") ?>?id=${file.id}`;
                } else {
                    alert('Transaction Failed: ' + data.message);
                }
            })
            .catch(err => alert('Network Error: ' + err.message));
        },

        getIcon(type) {
            const icons = {
                'dwg': 'https://cdn-icons-png.flaticon.com/512/8243/8243060.png',
                'dxf': 'https://cdn-icons-png.flaticon.com/512/8243/8243060.png',
                'xls': 'https://cdn-icons-png.flaticon.com/512/888/888850.png',
                'xlsx': 'https://cdn-icons-png.flaticon.com/512/888/888850.png',
                'pdf': 'https://cdn-icons-png.flaticon.com/512/337/337946.png',
                'doc': 'https://cdn-icons-png.flaticon.com/512/281/281760.png',
                'docx': 'https://cdn-icons-png.flaticon.com/512/281/281760.png',
                'default': 'https://cdn-icons-png.flaticon.com/512/2965/2965335.png' // Folder icon
            };
            return icons[type] || icons['default'];
        }
    }));
});
</script>
