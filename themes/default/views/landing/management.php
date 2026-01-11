<?php
$page_title = 'Project Management Suite';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Project Management', 'url' => '#']
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
                    'management-gold': '#feca57',
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
        border-color: rgba(254, 202, 87, 0.4);
        background: rgba(254, 202, 87, 0.04);
        transform: scale(1.05);
        box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.5), 0 0 40px rgba(254, 202, 87, 0.1);
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
        background: radial-gradient(circle at center, rgba(254, 202, 87, 0.05) 0%, transparent 70%);
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
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 0 0 1.5rem 1.5rem !important;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
    }
</style>

<div 
    class="min-h-screen bg-dark text-white selection:bg-management-gold/30 selection:text-management-gold" 
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
                const id = window.location.hash.slice(1);
                const validIds = ['dashboard','scheduling','resources','financial','quality','documents'];
                if (validIds.includes(id)) {
                    this.highlight(id);
                }
            }
        }
    }" 
    @scroll.window="isSticky = window.pageYOffset > 250"
>
    <div class="hero-glow"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-20 pb-32">
        <!-- Hero Section -->
        <div class="text-center mb-20">
            <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-management-gold/10 border border-management-gold/20 text-management-gold text-xs font-bold tracking-widest uppercase mb-8">
                <i class="fas fa-project-diagram"></i>
                <span>Professional Suite</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-black tracking-tight mb-6 bg-clip-text text-transparent bg-gradient-to-b from-white to-white/40">
                Project Management
            </h1>
            <p class="text-accent-muted text-xl max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
                Comprehensive tools for construction project planning, tracking, and control. Streamline your workflow from site to office.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#dashboard" class="premium-btn bg-white text-black px-10 py-4 rounded-2xl font-black text-sm tracking-wide">
                    EXPLORE MODULES
                </a>
                <a href="<?php echo app_base_url('/projects'); ?>" class="premium-btn bg-white/5 border border-white/10 hover:bg-white/10 px-10 py-4 rounded-2xl font-black text-sm tracking-wide flex items-center space-x-2">
                    <i class="fas fa-folder-open"></i>
                    <span>MY WORKSPACE</span>
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
                    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-tachometer-alt'],
                    ['id' => 'scheduling', 'label' => 'Scheduling', 'icon' => 'fa-calendar-alt'],
                    ['id' => 'resources', 'label' => 'Resources', 'icon' => 'fa-users'],
                    ['id' => 'financial', 'label' => 'Financial', 'icon' => 'fa-dollar-sign'],
                    ['id' => 'quality', 'label' => 'Quality', 'icon' => 'fa-check-circle'],
                    ['id' => 'documents', 'label' => 'Documents', 'icon' => 'fa-file-alt']
                ];
                foreach ($navItems as $item): 
                ?>
                <a 
                    href="#<?php echo $item['id']; ?>" 
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
            <!-- Dashboard -->
            <div id="dashboard" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'dashboard' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-management-gold/10 border border-management-gold/20 flex items-center justify-center text-management-gold">
                        <i class="fas fa-tachometer-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Dashboard</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Monitoring Node</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('project-overview'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Project Overview</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('gantt-chart'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Gantt Chart</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('milestone-tracker'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Milestone Tracker</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Scheduling -->
            <div id="scheduling" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'scheduling' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Scheduling</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Chronos Module</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('create-task'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Create Task</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('assign-task'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Assign Task</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('task-dependency'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Task Dependencies</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Resources -->
            <div id="resources" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'resources' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Resources</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Logistics Node</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('manpower-planning'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Manpower Planning</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('equipment-allocation'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Equipment Allocation</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('material-tracking'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Material Tracking</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Financial -->
            <div id="financial" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'financial' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-500">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Financial</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Capital Module</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('budget-tracking'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Budget Tracking</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('cost-control'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Cost Control</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('forecast-analysis'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Forecast Analysis</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Quality -->
            <div id="quality" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'quality' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-500">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Quality & Safety</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Compliance Node</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('quality-checklist'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Quality Checklist</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('safety-incidents'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Safety Incidents</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('audit-reports'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Audit Reports</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Documents -->
            <div id="documents" class="glass-card group rounded-[2.5rem] p-8" :class="focusSect === 'documents' ? 'card-focused' : (focusSect ? 'card-blurred' : '')">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Documents</h3>
                        <p class="text-xs font-bold text-accent-muted uppercase tracking-tighter opacity-50">Archive Module</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('document-repository'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Document Repository</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('drawing-register'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Drawing Register</span>
                        <i class="fas fa-arrow-right text-[10px] text-white/20 group-hover/item:text-white group-hover/item:translate-x-1 transition-all"></i>
                    </a>
                    <a href="<?php echo \App\Helpers\UrlHelper::calculator('submittal-tracking'); ?>" class="tool-item flex items-center justify-between p-4 rounded-xl group/item">
                        <span class="font-bold text-sm text-accent-muted group-hover/item:text-white">Submittal Tracking</span>
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
                     // Check if Alpine is ready
                    if (root.__x_data_stack) {
                       root.__x_data_stack[0].highlight(this.getAttribute('href').slice(1));
                    } else if (window.Alpine) {
                       // Fallback for different Alpine initialization timing
                       Alpine.$data(root).highlight(this.getAttribute('href').slice(1));
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
