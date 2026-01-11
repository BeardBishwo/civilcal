<?php
$page_title = 'Fire Protection Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Fire Protection', 'url' => '#']
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
                    'fire-red': '#e11d48', // Rose-600
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
        border-color: rgba(225, 29, 72, 0.4);
        background: rgba(225, 29, 72, 0.04);
        transform: scale(1.05);
        box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.5), 0 0 40px rgba(225, 29, 72, 0.1);
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
        background: radial-gradient(circle at center, rgba(225, 29, 72, 0.1) 0%, transparent 70%);
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
        border-bottom: 1px solid rgba(225, 29, 72, 0.2) !important;
        border-radius: 0 0 1.5rem 1.5rem !important;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
    }
</style>

<div 
    class="min-h-screen bg-dark text-white selection:bg-fire-red/30 selection:text-fire-red" 
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
            <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-fire-red/10 border border-fire-red/20 text-fire-red text-xs font-bold tracking-widest uppercase mb-8">
                <i class="fas fa-fire-extinguisher"></i>
                <span>Safety Systems</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-black tracking-tight mb-6 bg-clip-text text-transparent bg-gradient-to-b from-white to-white/40">
                Fire Protection
            </h1>
            <p class="text-accent-muted text-xl max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
                Professional calculators for sprinkler layout, hydraulic calculations, standpipe systems, and hazard assessment.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#sprinklers" class="premium-btn bg-white text-black px-10 py-4 rounded-2xl font-black text-sm tracking-wide">
                    EXPLORE TOOLS
                </a>
            </div>
        </div>

        <!-- Sticky Sub Nav -->
        <div :class="isSticky ? 'h-[74px]' : ''">
            <div 
                class="z-[1001] transition-all duration-500"
                :class="isSticky ? 'fixed top-0 left-0 w-full' : 'relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16'"
            >
                <div 
                    class="glass-card p-2 flex items-center justify-center space-x-1 overflow-x-auto scrollbar-hide no-scrollbar transition-all duration-500"
                    :class="isSticky ? 'sticky-nav rounded-none border-x-0 border-t-0 shadow-2xl' : 'rounded-2xl'"
                >
                <?php 
                $navItems = [
                    ['id' => 'sprinklers', 'label' => 'Sprinklers', 'icon' => 'fa-spray-can'],
                    ['id' => 'hydraulics', 'label' => 'Hydraulics', 'icon' => 'fa-tint'],
                    ['id' => 'standpipes', 'label' => 'Standpipes', 'icon' => 'fa-building'],
                    ['id' => 'hazard', 'label' => 'Hazards', 'icon' => 'fa-exclamation-triangle'],
                    ['id' => 'firePumps', 'label' => 'Fire Pumps', 'icon' => 'fa-pump-medical'],
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

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Sprinkler Systems -->
            <div id="sprinklers" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'sprinklers' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-500">
                        <i class="fas fa-spray-can text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Sprinklers</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Suppression</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('sprinkler-layout'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Layout Design</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('discharge-calculations'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Discharge Calc</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('pipe-sizing'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Pipe Sizing</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Hydraulics -->
            <div id="hydraulics" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'hydraulics' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500">
                        <i class="fas fa-tint text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Hydraulics</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Flow & Pressure</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('hazen-williams'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Hazen-Williams</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Standpipes -->
            <div id="standpipes" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'standpipes' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-gray-500/10 border border-gray-500/20 flex items-center justify-center text-gray-500">
                        <i class="fas fa-building text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Standpipes</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Hose Systems</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('standpipe-classification'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Classification</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('hose-demand'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Hose Demand</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                     <a href="<?php echo \App\Helpers\UrlHelper::calculator('pressure-calculations'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Pressure Calc</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Hazard -->
            <div id="hazard" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'hazard' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center text-yellow-500">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Hazards</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Risk Assessment</p>
                    </div>
                </div>
                 <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('occupancy-assessment'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Occupancy Class</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('commodity-classification'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Commodity Class</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('design-density'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Design Density</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Fire Pumps -->
            <div id="firePumps" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'firePumps' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500">
                        <i class="fas fa-pump-medical text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Fire Pumps</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Drivers & Sizing</p>
                    </div>
                </div>
                 <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('pump-sizing'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Pump Sizing</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('driver-power'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Driver Power</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                     <a href="<?php echo \App\Helpers\UrlHelper::calculator('jockey-pump'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Jockey Pump</span>
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
