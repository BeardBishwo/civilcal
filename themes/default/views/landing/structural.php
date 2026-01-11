<?php
$page_title = 'Structural Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Structural', 'url' => '#']
];
?>

<!-- CDN Utilities -->
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    dark: '#050505',
                    surface: '#0a0a0a',
                    glass: 'rgba(255, 255, 255, 0.03)',
                    'glass-border': 'rgba(255, 255, 255, 0.08)',
                    accent: '#ffffff',
                    'accent-muted': '#a1a1aa',
                    'struct-steel': '#64748b', // Slate-500
                }
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    }
    
    .glass-card:hover {
        border-color: rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.04);
        transform: translateY(-4px);
    }

    .glass-card.card-focused {
        border-color: rgba(100, 116, 139, 0.4);
        background: rgba(100, 116, 139, 0.04);
        transform: scale(1.05);
        box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.5), 0 0 40px rgba(100, 116, 139, 0.1);
        z-index: 20;
    }

    .glass-card.card-blurred {
        opacity: 0.2;
        filter: blur(4px);
        transform: scale(0.95);
        pointer-events: none;
    }

    .hero-glow {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 800px;
        height: 400px;
        background: radial-gradient(circle at center, rgba(100, 116, 139, 0.1) 0%, transparent 70%);
        pointer-events: none;
        z-index: 0;
    }

    .premium-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .premium-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .tool-item {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }

    .tool-item:last-child {
        border-bottom: none;
    }

    .tool-item:hover {
        background: rgba(255, 255, 255, 0.03);
        padding-left: 1.25rem;
    }

    .sticky-nav {
        background: rgba(5, 5, 5, 0.8) !important;
        backdrop-filter: blur(20px) !important;
        border-bottom: 1px solid rgba(100, 116, 139, 0.2) !important;
        border-radius: 0 0 1.5rem 1.5rem !important;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
    }
</style>

<div 
    class="min-h-screen bg-dark text-white selection:bg-struct-steel/30 selection:text-struct-steel" 
    x-data="{ 
        activeSect: null, 
        focusSect: null, 
        isSticky: false, 
        timer: null,
        highlight(id) {
            if (this.timer) clearTimeout(this.timer);
            this.activeSect = id;
            this.focusSect = id;
            this.timer = setTimeout(() => {
                this.focusSect = null;
            }, 2000);
        },
        init() {
            // Check for hash on load
            if (window.location.hash) {
                this.highlight(window.location.hash.slice(1));
            }
        }
    }" 
    @scroll.window="isSticky = window.pageYOffset > 250"
>
    <div class="hero-glow"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-20 pb-32">
        <!-- Hero Section -->
        <div class="text-center mb-20">
            <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-struct-steel/10 border border-struct-steel/20 text-struct-steel text-xs font-bold tracking-widest uppercase mb-8">
                <i class="fas fa-industry"></i>
                <span>Structural Suite</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-black tracking-tight mb-6 bg-clip-text text-transparent bg-gradient-to-b from-white to-white/40">
                Structural Engineering
            </h1>
            <p class="text-accent-muted text-xl max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
                Advanced calculators for beam analysis, column design, steel structures, and foundations.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#beam" class="premium-btn bg-white text-black px-10 py-4 rounded-2xl font-black text-sm tracking-wide">
                    EXPLORE TOOLS
                </a>
            </div>
        </div>

        <!-- Sticky Sub Nav -->
        <div :class="isSticky ? 'h-[74px]' : ''">
            <div 
                class="z-[1001] transition-all duration-500"
                :class="isSticky ? 'fixed top-0 left-0 w-full' : 'relative mb-16'"
            >
                <div 
                    class="glass-card p-2 flex items-center justify-center space-x-1 overflow-x-auto scrollbar-hide no-scrollbar transition-all duration-500"
                    :class="isSticky ? 'sticky-nav rounded-none border-x-0 border-t-0 shadow-2xl' : 'rounded-2xl'"
                >
                <?php 
                $navItems = [
                    ['id' => 'beam', 'label' => 'Beam', 'icon' => 'fa-ruler-horizontal'],
                    ['id' => 'column', 'label' => 'Column', 'icon' => 'fa-columns'],
                    ['id' => 'steel', 'label' => 'Steel', 'icon' => 'fa-industry'],
                    ['id' => 'foundation', 'label' => 'Foundation', 'icon' => 'fa-university'],
                    ['id' => 'slab', 'label' => 'Slab', 'icon' => 'fa-square'],
                    ['id' => 'loads', 'label' => 'Loads', 'icon' => 'fa-weight-hanging'],
                    ['id' => 'reinforce', 'label' => 'Reinforce', 'icon' => 'fa-hashtag'],
                    ['id' => 'reports', 'label' => 'Reports', 'icon' => 'fa-file-alt'],
                ];
                foreach ($navItems as $item): 
                ?>
                <a 
                    href="#<?php echo $item['id']; ?>" 
                    @click="highlight('<?php echo $item['id']; ?>')"
                    class="flex items-center space-x-2 px-5 py-2.5 rounded-xl transition-all font-bold text-xs whitespace-nowrap"
                    :class="activeSect === '<?php echo $item['id']; ?>' ? 'bg-white text-black' : 'text-accent-muted hover:text-white hover:bg-white/5'"
                >
                    <i class="fas <?php echo $item['icon']; ?>"></i>
                    <span><?php echo $item['label']; ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pb-32">
        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Beam Analysis -->
            <div id="beam" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'beam' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-500">
                        <i class="fas fa-ruler-horizontal text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Beam Analysis</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Design & Loads</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('beam-design'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Beam Design</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('beam-load-combination'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Load Combination</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('cantilever-beam'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Cantilever Beam</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('continuous-beam'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Continuous Beam</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Column Design -->
            <div id="column" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'column' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500">
                        <i class="fas fa-columns text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Column Design</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Vertical Members</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('biaxial-column'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Biaxial Column</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('long-column'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Long Column</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('short-column'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Short Column</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Steel Structure -->
            <div id="steel" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'steel' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-slate-400/10 border border-slate-400/20 flex items-center justify-center text-slate-400">
                        <i class="fas fa-industry text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Steel Structure</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Connections & Purlins</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('connection-design'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Connection Design</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('purlin-design'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Purlin Design</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('steel-truss-analysis'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Truss Analysis</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Foundation -->
            <div id="foundation" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'foundation' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-stone-500/10 border border-stone-500/20 flex items-center justify-center text-stone-500">
                        <i class="fas fa-university text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Foundation</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Substructure</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('combined-footing'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Combined Footing</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('isolated-footing'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Isolated Footing</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                     <a href="<?php echo \App\Helpers\UrlHelper::calculator('pile-foundation'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Pile Foundation</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

             <!-- Slab Design -->
            <div id="slab" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'slab' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-500">
                        <i class="fas fa-square text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Slab Design</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Flooring Systems</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('flat-slab'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Flat Slab</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('one-way-slab'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">One Way Slab</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                     <a href="<?php echo \App\Helpers\UrlHelper::calculator('two-way-slab'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Two Way Slab</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

             <!-- Loads -->
            <div id="loads" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'loads' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-500">
                        <i class="fas fa-weight-hanging text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Load Analysis</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Forces & Stresses</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('dead-load'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Dead Load</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('live-load'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Live Load</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                     <a href="<?php echo \App\Helpers\UrlHelper::calculator('seismic-load'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Seismic Load</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Reinforcement -->
            <div id="reinforce" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'reinforce' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500">
                        <i class="fas fa-hashtag text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Reinforcement</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Rebar Tools</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('bar-bending-schedule'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Bar Bending Schedule</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('rebar-anchorage'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Rebar Anchorage</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                     <a href="<?php echo \App\Helpers\UrlHelper::calculator('reinforcement-optimizer'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Optimizer</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Reports -->
            <div id="reports" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'reports' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-500">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Reports</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Documentation</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('full-structure-summary'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Full Structure Summary</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('load-analysis-summary'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Load Analysis Summary</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Smooth scroll handling for sub-nav
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const headerOffset = 100;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                // Call highlight via Alpine
                const root = document.querySelector('[x-data]');
                if (root) {
                    if (root.__x_data_stack) {
                       root.__x_data_stack[0].highlight(this.getAttribute('href').slice(1));
                    } else if (window.Alpine) {
                       window.Alpine.$data(root).highlight(this.getAttribute('href').slice(1));
                    }
                }

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
        });
    });
</script>
