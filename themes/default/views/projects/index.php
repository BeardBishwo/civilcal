<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>

<!-- CDN Utilities -->
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    dark: '#000000',
                    surface: '#0a0a0a',
                    glass: 'rgba(255, 255, 255, 0.03)',
                    'glass-border': 'rgba(255, 255, 255, 0.1)',
                    accent: '#ffffff',
                    'accent-muted': '#cbd5e1',
                },
                backgroundImage: {
                    'glow-gradient': 'radial-gradient(circle at center, rgba(255,255,255,0.05) 0%, transparent 70%)',
                }
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
    
    .glass-card {
        background: var(--glass-bg, rgba(255, 255, 255, 0.02));
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .glass-card:hover {
        border-color: rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.04);
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    .premium-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .premium-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.15);
    }

    .input-premium {
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        transition: all 0.3s ease;
    }

    .input-premium:focus {
        border-color: rgba(255, 255, 255, 0.4) !important;
        background: rgba(255, 255, 255, 0.06) !important;
        outline: none;
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.05);
    }
</style>

<div class="min-h-screen pt-12 pb-20 px-4 sm:px-6 lg:px-8 bg-dark text-white" x-data="{ openModal: false }">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 space-y-4 md:space-y-0 pb-8 border-b border-white/10">
            <div>
                <h1 class="text-4xl font-black tracking-tight text-white mb-2">My Projects</h1>
                <p class="text-accent-muted text-lg opacity-80">Organize and manage your professional estimations.</p>
            </div>
            <div>
                <button 
                    @click="openModal = true"
                    class="premium-btn bg-white text-black px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 group"
                >
                    <i class="fas fa-plus transition-transform group-hover:rotate-90"></i>
                    <span>Start New Project</span>
                </button>
            </div>
        </div>

        <!-- Success Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, 5000)"
                class="mb-8 p-4 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-between text-white backdrop-blur-md"
            >
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center">
                        <i class="fas fa-check text-black text-xs"></i>
                    </div>
                    <span><?php echo $_GET['success'] == 'deleted' ? 'Project successfully removed.' : 'Project created successfully.'; ?></span>
                </div>
                <button @click="show = false" class="text-white/40 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($projects)): ?>
                <div class="col-span-full py-32 flex flex-col items-center text-center">
                    <div class="w-24 h-24 mb-8 bg-white/5 rounded-full flex items-center justify-center border border-white/10 relative overflow-hidden">
                        <div class="absolute inset-0 bg-glow-gradient"></div>
                        <i class="fas fa-folder-open text-3xl text-white/20"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">No projects drafted yet</h3>
                    <p class="text-accent-muted mb-8 max-w-sm mx-auto">Click the button below to initialize your first project workspace.</p>
                    <button 
                        @click="openModal = true"
                        class="text-white border border-white/20 hover:border-white/40 px-6 py-2.5 rounded-xl transition-all"
                    >
                        Create Workspace
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <div 
                        class="glass-card group relative p-6 rounded-3xl"
                        x-data="{ dropdownOpen: false }"
                    >
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center group-hover:bg-white/10 transition-colors">
                                <i class="fas fa-folder text-xl text-white/60"></i>
                            </div>
                            
                            <!-- Dropdown -->
                            <div class="relative">
                                <button 
                                    @click="dropdownOpen = !dropdownOpen"
                                    @click.away="dropdownOpen = false"
                                    class="w-10 h-10 flex items-center justify-center text-white/40 hover:text-white transition-colors"
                                >
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                
                                <div 
                                    x-show="dropdownOpen"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="absolute right-0 mt-2 w-56 bg-surface border border-white/10 rounded-2xl shadow-2xl z-50 overflow-hidden backdrop-blur-xl"
                                >
                                    <a href="<?php echo app_base_url('/projects/view/' . $project['id']); ?>" class="flex items-center space-x-3 px-4 py-3 text-sm text-accent-muted hover:text-white hover:bg-white/5 transition-colors">
                                        <i class="fas fa-eye w-5"></i>
                                        <span>View Details</span>
                                    </a>
                                    <div class="h-px bg-white/5 mx-4"></div>
                                    <form action="<?php echo app_base_url('/projects/delete/' . $project['id']); ?>" method="POST" onsubmit="return confirm('Archive this project? This will not delete internal calculations.');">
                                        <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 text-sm text-red-500 hover:text-red-400 hover:bg-white/5 transition-colors text-left">
                                            <i class="fas fa-trash-alt w-5"></i>
                                            <span>Archive Project</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <a href="<?php echo app_base_url('/projects/view/' . $project['id']); ?>" class="block">
                            <h4 class="text-xl font-bold text-white mb-2 group-hover:text-accent transition-colors">
                                <?php echo htmlspecialchars($project['name']); ?>
                            </h4>
                            <p class="text-accent-muted text-sm line-clamp-2 min-h-[2.5rem] opacity-70 mb-6 font-medium">
                                <?php echo htmlspecialchars($project['description'] ?: 'Organized estimation workspace.'); ?>
                            </p>
                            
                            <div class="flex items-center justify-between pt-5 border-t border-white/5">
                                <div class="flex items-center text-xs font-bold uppercase tracking-wider text-white/40">
                                    <i class="fas fa-calculator mr-2 text-[10px]"></i>
                                    <span><?php echo $project['calculation_count']; ?> Nodes</span>
                                </div>
                                <div class="text-xs font-medium text-white/30">
                                    <?php echo date('M d, Y', strtotime($project['updated_at'])); ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Backdrop -->
    <template x-teleport="body">
        <div 
            x-show="openModal" 
            x-cloak
            class="fixed inset-0 z-[1060] flex items-center justify-center px-4"
        >
            <!-- Overlay -->
            <div 
                x-show="openModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="openModal = false"
                class="fixed inset-0 bg-black/95 backdrop-blur-sm"
            ></div>

            <!-- Modal Content -->
            <div 
                x-show="openModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                class="relative bg-surface border border-white/10 w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-[0_0_100px_rgba(255,255,255,0.05)]"
            >
                <form action="<?php echo app_base_url('/projects/store'); ?>" method="POST">
                    <div class="p-8 sm:p-12">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-3xl font-black">New Workspace</h2>
                            <button @click.prevent="openModal = false" class="text-white/40 hover:text-white transition-colors">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-3">Workspace Name</label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    required 
                                    placeholder="e.g. Modern Villa Estimation"
                                    class="input-premium w-full px-6 py-4 rounded-2xl text-white placeholder-white/20"
                                >
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-3">Scope Description</label>
                                <textarea 
                                    name="description" 
                                    rows="4" 
                                    placeholder="Brief outline of project scope..."
                                    class="input-premium w-full px-6 py-4 rounded-2xl text-white placeholder-white/20 resize-none"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 pb-12 sm:px-12">
                        <button type="submit" class="premium-btn w-full bg-white text-black py-5 rounded-3xl font-black tracking-wide">
                            CREATE WORKSPACE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
