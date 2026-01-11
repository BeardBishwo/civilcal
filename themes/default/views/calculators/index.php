<?php
// themes/default/views/calculators/index.php
// ULTRA PREMIUM CALCULATOR HUB - Content View
?>

<!-- Tailwind CSS & Config -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                    mono: ['JetBrains Mono', 'monospace'],
                },
                colors: {
                    brand: {
                        purple: '#8b5cf6',
                        cyan: '#06b6d4',
                        pink: '#f472b6',
                    }
                },
                animation: {
                    'blob': 'blob 10s infinite',
                    'float': 'float 6s ease-in-out infinite',
                    'glow': 'glow 2s ease-in-out infinite alternate',
                },
                keyframes: {
                    blob: {
                        '0%': { transform: 'translate(0px, 0px) scale(1)' },
                        '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                        '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                        '100%': { transform: 'translate(0px, 0px) scale(1)' },
                    },
                    float: {
                        '0%, 100%': { transform: 'translateY(0)' },
                        '50%': { transform: 'translateY(-20px)' },
                    },
                    glow: {
                        '0%': { boxShadow: '0 0 20px rgba(6, 182, 212, 0.2)' },
                        '100%': { boxShadow: '0 0 40px rgba(6, 182, 212, 0.6)' },
                    }
                }
            }
        }
    }
</script>

<!-- Fonts & Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    [x-cloak] { display: none !important; }
    /* Custom Scrollbar for this page */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    
    .mask-gradient {
        mask-image: linear-gradient(to right, transparent, black 20px, black 95%, transparent);
    }
</style>

<!-- Main Page Wrapper (No Navbar/Footer) -->
<div class="relative w-full min-h-screen bg-[#0f172a] text-slate-200 font-sans antialiased overflow-hidden" x-data="calculatorHub">

    <!-- Background Ambience -->
    <div class="absolute inset-0 pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-purple-600/20 rounded-full mix-blend-screen filter blur-[120px] animate-blob sticky top-0"></div>
        <div class="absolute bottom-0 left-0 w-[800px] h-[800px] bg-cyan-600/20 rounded-full mix-blend-screen filter blur-[120px] animate-blob animation-delay-2000 sticky bottom-0"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-pink-600/10 rounded-full mix-blend-screen filter blur-[100px] animate-blob animation-delay-4000"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] z-[1] opacity-30"></div>
    </div>

    <!-- Content Container -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-12 md:py-20 flex flex-col items-center">
        
        <!-- Hero Text -->
        <div class="text-center max-w-3xl mx-auto mb-16 relative">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-bold uppercase tracking-widest mb-6 animate-pulse">
                <i class="fas fa-sparkles"></i> <span>Premium Tools Collection</span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-slate-400 mb-6 tracking-tight drop-shadow-2xl">
                Calculator Universe
            </h1>
            
            <p class="text-lg md:text-xl text-slate-400 font-medium leading-relaxed max-w-2xl mx-auto">
                A curated suite of <span class="text-cyan-400 font-bold">high-precision</span> engineering and mathematical tools. Designed for speed, accuracy, and elegance.
            </p>
        </div>

        <!-- Spotlight Search & Navigation -->
        <div class="w-full max-w-4xl relative z-20 mb-12">
            
            <!-- Search Box -->
            <div class="relative group mb-8">
                <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500 via-purple-500 to-pink-500 rounded-2xl opacity-30 group-hover:opacity-60 blur-xl transition-opacity duration-500"></div>
                <div class="relative bg-[#0f172a]/80 backdrop-blur-xl border border-white/10 rounded-2xl flex items-center p-2 shadow-2xl focus-within:border-cyan-500/50 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-gray-400 group-focus-within:text-cyan-400 transition-colors">
                        <i class="fas fa-search text-xl"></i>
                    </div>
                    <input type="text" x-model="searchQuery" 
                            class="w-full bg-transparent border-none text-lg text-white placeholder-slate-500 focus:ring-0 px-4 h-12"
                            placeholder="Search tools (e.g., 'converter', 'loan', 'area')...">
                    <div class="hidden md:flex items-center gap-2 px-4 text-xs font-mono text-slate-500 border-l border-white/5">
                        <span class="bg-white/5 px-2 py-1 rounded border border-white/5">CTRL</span>
                        <span class="bg-white/5 px-2 py-1 rounded border border-white/5">K</span>
                    </div>
                </div>
            </div>

            <!-- Category Tabs (Glass) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-4 custom-scrollbar mask-gradient">
                <button @click="filter = 'all'" 
                        class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-300 border"
                        :class="filter === 'all' ? 'bg-white text-black border-white shadow-[0_0_20px_rgba(255,255,255,0.3)] scale-105' : 'bg-white/5 text-slate-400 border-white/5 hover:bg-white/10 hover:text-white'">
                    All Tools
                </button>
                
                <template x-for="cat in categories" :key="cat.id">
                    <button @click="filter = cat.id" 
                            class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-300 border flex items-center gap-2"
                            :class="filter === cat.id ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white border-transparent shadow-[0_0_20px_rgba(6,182,212,0.4)] scale-105' : 'bg-white/5 text-slate-400 border-white/5 hover:bg-white/10 hover:text-white'">
                        <i :class="cat.icon" class="text-xs opacity-70"></i>
                        <span x-text="cat.name"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Stats -->
        <div class="w-full max-w-4xl grid grid-cols-3 gap-4 mb-20 border-t border-white/5 pt-8">
            <div class="text-center">
                <div class="text-3xl font-black text-white" x-text="activeCalculators.length"></div>
                <div class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mt-1">Available Tools</div>
            </div>
            <div class="text-center border-l border-white/5">
                <div class="text-3xl font-black text-white" x-text="categories.length"></div>
                <div class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mt-1">Categories</div>
            </div>
            <div class="text-center border-l border-white/5">
                <div class="text-3xl font-black text-cyan-400">∞</div>
                <div class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mt-1">Possibilities</div>
            </div>
        </div>

        <!-- Calculator Grid -->
        <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative z-10 pb-20">
            
            <template x-for="calc in activeCalculators" :key="calc.title">
                <a :href="calc.url" class="group relative block h-full">
                    <!-- Glow Effect -->
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-500 to-purple-600 rounded-[24px] opacity-0 group-hover:opacity-50 blur transition duration-500 group-hover:duration-200"></div>
                    
                    <!-- Card Content -->
                    <div class="relative h-full bg-[#0f172a]/80 backdrop-blur-xl border border-white/10 rounded-[22px] p-6 flex flex-col transition-transform duration-300 group-hover:-translate-y-1">
                        
                        <!-- Card Header -->
                        <div class="flex items-start justify-between mb-5">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-white/5 to-white/0 border border-white/10 flex items-center justify-center text-2xl text-cyan-400 shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                <i :class="calc.icon"></i>
                            </div>
                            <div class="px-3 py-1 rounded-full bg-white/5 border border-white/5 text-[10px] font-bold text-slate-400 uppercase tracking-wider group-hover:bg-cyan-500/10 group-hover:text-cyan-400 transition-colors" x-text="calc.category_name"></div>
                        </div>
                        
                        <!-- Card Body -->
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors" x-text="calc.title"></h3>
                        <p class="text-sm text-slate-400 font-medium leading-relaxed mb-6 line-clamp-2" x-text="calc.description"></p>
                        
                        <!-- Card Footer -->
                        <div class="mt-auto pt-4 border-t border-white/5 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 group-hover:text-white transition-colors flex items-center gap-1.5">
                                <i class="fas fa-bolt text-yellow-500"></i> Instant
                            </span>
                            <span class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-slate-400 group-hover:bg-cyan-500 group-hover:text-black transition-all duration-300 transform group-hover:translate-x-1">
                                <i class="fas fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </template>

            <!-- No Results -->
            <div x-show="activeCalculators.length === 0" class="col-span-1 md:col-span-2 lg:col-span-3 py-20 text-center" x-transition>
                <div class="w-20 h-20 mx-auto bg-white/5 rounded-full flex items-center justify-center text-slate-600 text-3xl mb-6">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No tools found</h3>
                <p class="text-slate-400">We couldn't find any calculators matching "<span x-text="searchQuery" class="text-cyan-400"></span>".</p>
                <button @click="searchQuery = ''; filter = 'all'" class="mt-6 px-6 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm font-bold transition-colors">
                    Clear Filters
                </button>
            </div>

        </div>

        <!-- Features Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white/5 rounded-2xl p-6 border border-white/10 text-center">
                <i class="fas fa-bolt text-3xl text-yellow-400 mb-4"></i>
                <h3 class="text-white font-bold mb-2">Blazing Fast</h3>
                <p class="text-sm text-slate-400">Instant client-side updates</p>
            </div>
            <div class="bg-white/5 rounded-2xl p-6 border border-white/10 text-center">
                <i class="fas fa-shield-alt text-3xl text-blue-400 mb-4"></i>
                <h3 class="text-white font-bold mb-2">Secure & Precise</h3>
                <p class="text-sm text-slate-400">Verified algorithms</p>
            </div>
            <div class="bg-white/5 rounded-2xl p-6 border border-white/10 text-center">
                <i class="fas fa-mobile-alt text-3xl text-purple-400 mb-4"></i>
                <h3 class="text-white font-bold mb-2">Responsive</h3>
                <p class="text-sm text-slate-400">Works on any device</p>
            </div>
        </div>

    </div>
</div>

<!-- Alpine.js Logic -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('calculatorHub', () => ({
            searchQuery: '',
            filter: 'all',
            
            // Categories
            categories: [
                { id: 'math', name: 'Mathematics', icon: 'fas fa-square-root-alt' },
                { id: 'finance', name: 'Finance', icon: 'fas fa-coins' },
                { id: 'health', name: 'Health', icon: 'fas fa-heartbeat' },
                { id: 'engineering', name: 'Engineering', icon: 'fas fa-drafting-compass' },
                { id: 'nepali', name: 'Regional', icon: 'fas fa-globe-asia' },
            ],

            // Calculator Data
            calculators: [
                // MATH
                { title: 'Scientific Calculator', description: 'Advanced scientific functions including trig, logs, and exponents.', icon: 'fas fa-atom', category: 'math', category_name: 'Math', url: '<?= app_base_url('/calculators/scientific') ?>' },
                { title: 'Area Calculator', description: 'Compute area for circles, rectangles, triangles, and polygons.', icon: 'fas fa-vector-square', category: 'math', category_name: 'Math', url: '<?= app_base_url('/calculators/math/area') ?>' },
                { title: 'Volume Calculator', description: 'Calculate volume for spheres, cubes, cylinders, and more.', icon: 'fas fa-cube', category: 'math', category_name: 'Math', url: '<?= app_base_url('/calculators/math/volume') ?>' },
                { title: 'Percentage', description: 'Calculate increases, decreases, and percentage differences.', icon: 'fas fa-percent', category: 'math', category_name: 'Math', url: '<?= app_base_url('/calculators/math/percentage') ?>' },
                { title: 'Fraction Calculator', description: 'Work with fractions easily: add, subtract, multiply, and divide.', icon: 'fas fa-divide', category: 'math', category_name: 'Math', url: '<?= app_base_url('/calculators/math/fraction') ?>' },
                
                // FINANCE
                { title: 'Loan Calculator', description: 'Estimate monthly payments, total interest, and amortization.', icon: 'fas fa-hand-holding-usd', category: 'finance', category_name: 'Finance', url: '<?= app_base_url('/calculators/finance/loan') ?>' },
                { title: 'Compound Interest', description: 'Visualize how your investments grow over time with compounding.', icon: 'fas fa-chart-line', category: 'finance', category_name: 'Finance', url: '<?= app_base_url('/calculators/finance/compound_interest') ?>' },
                { title: 'Mortgage Estimator', description: 'Calculate monthly home loan payments including tax and insurance.', icon: 'fas fa-home', category: 'finance', category_name: 'Finance', url: '<?= app_base_url('/calculators/finance/mortgage') ?>' },
                { title: 'ROI Calculator', description: 'Analyze the return on investment for business decisions.', icon: 'fas fa-chart-pie', category: 'finance', category_name: 'Finance', url: '<?= app_base_url('/calculators/finance/roi') ?>' },

                // HEALTH
                { title: 'BMI Calculator', description: 'Check your Body Mass Index and get health insights.', icon: 'fas fa-weight', category: 'health', category_name: 'Health', url: '<?= app_base_url('/calculators/math/bmi') ?>' },
                { title: 'Age Calculator', description: 'Calculate your exact age in years, months, and days.', icon: 'fas fa-birthday-cake', category: 'health', category_name: 'Health', url: '<?= app_base_url('/calculators/math/age') ?>' },

                // SPECIAL / CONVERTERS
                { title: 'Unit Converter', description: 'Convert between thousands of units across 20+ categories.', icon: 'fas fa-exchange-alt', category: 'engineering', category_name: 'Tool', url: '<?= app_base_url('/convert/length') ?>' },
                { title: 'Nepali Unit Converter', description: 'Traditional Nepali land area conversions (Ropani, Aana, etc).', icon: 'fas fa-mountain', category: 'nepali', category_name: 'Regional', url: '<?= app_base_url('/nepali') ?>' },
            ],

            get activeCalculators() {
                const q = this.searchQuery.toLowerCase();
                return this.calculators.filter(c => {
                    const matchesSearch = c.title.toLowerCase().includes(q) || c.description.toLowerCase().includes(q);
                    const matchesFilter = this.filter === 'all' || c.category === this.filter;
                    return matchesSearch && matchesFilter;
                });
            },

            init() {
                // Keyboard shortcut for search
                window.addEventListener('keydown', (e) => {
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        document.querySelector('input[type="text"]').focus();
                    }
                });
            }
        }));
    });
</script>
