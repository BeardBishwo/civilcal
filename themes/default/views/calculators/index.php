<?php
// themes/default/views/calculators/index.php
// PREMIUM CALCULATOR HUB
?>

<!-- Load Calculators CSS -->
<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<!-- Load Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="calculatorHub()">
    
    <!-- Animated Background Mesh -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] right-[-10%] w-[600px] h-[600px] bg-primary/20 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-[600px] h-[600px] bg-secondary/20 rounded-full blur-[120px] animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute top-[40%] left-[50%] w-[400px] h-[400px] bg-accent/10 rounded-full blur-[100px] animate-float" style="animation-delay: 2s;"></div>
    </div>

    <div class="calc-container">
        
        <!-- Hero Section -->
        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6 animate-pulse-glow">
                <i class="fas fa-calculator"></i>
                <span>PRECISION TOOLS</span>
            </div>
            
            <h1 class="calc-title">
                Calculator <span class="text-gradient">Universe</span>
            </h1>
            <p class="calc-subtitle max-w-2xl mx-auto">
                Lightning-fast calculations with premium UI. Choose from 30+ specialized calculators across 6 categories.
            </p>
        </div>

        <!-- Search & Filter Bar -->
        <div class="glass-card mb-12 animate-scale-in">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search Input -->
                <div class="relative flex-grow group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-primary transition-colors"></i>
                    <input 
                        type="text" 
                        x-model="searchQuery" 
                        @input="filterCalculators()"
                        class="glass-input w-full pl-12 pr-4"
                        placeholder="Search calculators (e.g., 'area', 'loan', 'BMI')...">
                </div>

                <!-- Category Filter -->
                <select x-model="selectedCategory" @change="filterCalculators()" class="calc-select md:w-64">
                    <option value="">All Categories</option>
                    <option value="math">Mathematics</option>
                    <option value="finance">Finance</option>
                    <option value="health">Health</option>
                    <option value="physics">Physics</option>
                    <option value="chemistry">Chemistry</option>
                    <option value="datetime">Date & Time</option>
                </select>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-white/10">
                <div class="text-center">
                    <div class="text-3xl font-black text-gradient" x-text="filteredCalculators.length"></div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Available</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-black text-gradient">6</div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Categories</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-black text-gradient">∞</div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Calculations</div>
                </div>
            </div>
        </div>

        <!-- Calculators Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <template x-for="(calc, index) in filteredCalculators" :key="calc.id">
                <a :href="calc.url" class="calc-card group stagger-item block no-underline" :style="'animation-delay: ' + (index * 0.05) + 's'">
                    <!-- Icon Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20 border border-primary/30 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                            <i :class="calc.icon" class="text-primary"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/5 border border-white/10 text-gray-400" x-text="calc.category"></span>
                    </div>

                    <!-- Title & Description -->
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-primary transition-colors" x-text="calc.title"></h3>
                    <p class="text-sm text-gray-400 mb-4 line-clamp-2" x-text="calc.description"></p>

                    <!-- Footer -->
                    <div class="flex items-center justify-between pt-4 border-t border-white/5">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <i class="fas fa-bolt text-yellow-500"></i>
                            <span>Instant Results</span>
                        </div>
                        <div class="flex items-center gap-2 text-primary font-bold text-sm group-hover:gap-3 transition-all">
                            <span>Calculate</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>

                    <!-- Hover Glow Effect -->
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-primary/0 via-primary/5 to-primary/0 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                </a>
            </template>

        </div>

        <!-- Empty State -->
        <div x-show="filteredCalculators.length === 0" class="text-center py-20 animate-fade-in">
            <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-white/5 flex items-center justify-center text-4xl text-gray-600">
                <i class="fas fa-search"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">No calculators found</h3>
            <p class="text-gray-400 mb-6">Try adjusting your search or filter</p>
            <button @click="searchQuery = ''; selectedCategory = ''; filterCalculators()" class="btn-secondary">
                <i class="fas fa-redo mr-2"></i> Reset Filters
            </button>
        </div>

        <!-- Info Section -->
        <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-card text-center stagger-item" style="animation-delay: 0.4s;">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-green-500/20 to-emerald-500/20 border border-green-500/30 flex items-center justify-center text-3xl">
                    <i class="fas fa-rocket text-green-500"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Blazing Fast</h3>
                <p class="text-sm text-gray-400">GPU-accelerated animations running at 60fps for instant feedback</p>
            </div>

            <div class="glass-card text-center stagger-item" style="animation-delay: 0.5s;">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border border-blue-500/30 flex items-center justify-center text-3xl">
                    <i class="fas fa-shield-alt text-blue-500"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">100% Accurate</h3>
                <p class="text-sm text-gray-400">Precision calculations verified against industry standards</p>
            </div>

            <div class="glass-card text-center stagger-item" style="animation-delay: 0.6s;">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-500/20 to-pink-500/20 border border-purple-500/30 flex items-center justify-center text-3xl">
                    <i class="fas fa-mobile-alt text-purple-500"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Fully Responsive</h3>
                <p class="text-sm text-gray-400">Perfect experience on desktop, tablet, and mobile devices</p>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('calculatorHub', () => ({
        searchQuery: '',
        selectedCategory: '',
        calculators: [
            // Core
            { id: 0, title: 'Scientific Calculator', description: 'Advanced scientific calculator with trigonometry, logs, and exponential functions', category: 'math', icon: 'fas fa-atom', url: '<?= app_base_url("/calculators/scientific") ?>' },
            // Mathematics
            { id: 1, title: 'Area Calculator', description: 'Calculate area of various shapes including circles, rectangles, and triangles', category: 'math', icon: 'fas fa-square', url: '<?= app_base_url("/calculators/math/area") ?>' },
            { id: 2, title: 'Volume Calculator', description: 'Compute volume of 3D shapes like spheres, cubes, and cylinders', category: 'math', icon: 'fas fa-cube', url: '<?= app_base_url("/calculators/math/volume") ?>' },
            { id: 3, title: 'Percentage Calculator', description: 'Calculate percentages, percentage increase, and percentage difference', category: 'math', icon: 'fas fa-percent', url: '<?= app_base_url("/calculators/math/percentage") ?>' },
            { id: 4, title: 'Fraction Calculator', description: 'Add, subtract, multiply, and divide fractions with step-by-step solutions', category: 'math', icon: 'fas fa-divide', url: '<?= app_base_url("/calculators/math/fraction") ?>' },
            { id: 5, title: 'Quadratic Equation Solver', description: 'Solve quadratic equations and visualize the parabola', category: 'math', icon: 'fas fa-superscript', url: '<?= app_base_url("/calculators/math/quadratic") ?>' },
            { id: 6, title: 'Trigonometry Calculator', description: 'Calculate sine, cosine, tangent, and other trig functions', category: 'math', icon: 'fas fa-wave-square', url: '<?= app_base_url("/calculators/math/trigonometry") ?>' },
            { id: 7, title: 'Statistics Calculator', description: 'Compute mean, median, mode, standard deviation, and variance', category: 'math', icon: 'fas fa-chart-bar', url: '<?= app_base_url("/calculators/math/statistics") ?>' },
            { id: 8, title: 'GCD & LCM Calculator', description: 'Find Greatest Common Divisor and Least Common Multiple', category: 'math', icon: 'fas fa-calculator', url: '<?= app_base_url("/calculators/math/gcd_lcm") ?>' },
            
            // Finance
            { id: 20, title: 'Compound Interest', description: 'Calculate compound interest with customizable compounding frequency', category: 'finance', icon: 'fas fa-chart-line', url: '<?= app_base_url("/calculators/finance/compound_interest") ?>' },
            { id: 21, title: 'Loan Calculator', description: 'Calculate monthly payments, total interest, and amortization schedule', category: 'finance', icon: 'fas fa-hand-holding-usd', url: '<?= app_base_url("/calculators/finance/loan") ?>' },
            { id: 22, title: 'Mortgage Calculator', description: 'Estimate monthly mortgage payments including taxes and insurance', category: 'finance', icon: 'fas fa-home', url: '<?= app_base_url("/calculators/finance/mortgage") ?>' },
            { id: 23, title: 'Investment Calculator', description: 'Project investment growth with regular contributions', category: 'finance', icon: 'fas fa-piggy-bank', url: '<?= app_base_url("/calculators/finance/investment") ?>' },
            { id: 24, title: 'ROI Calculator', description: 'Calculate Return on Investment for business decisions', category: 'finance', icon: 'fas fa-percentage', url: '<?= app_base_url("/calculators/finance/roi") ?>' },
            { id: 25, title: 'Salary Calculator', description: 'Convert between hourly, monthly, and annual salary', category: 'finance', icon: 'fas fa-money-bill-wave', url: '<?= app_base_url("/calculators/finance/salary") ?>' },
            
            // Health
            { id: 30, title: 'BMI Calculator', description: 'Calculate Body Mass Index and get health recommendations', category: 'health', icon: 'fas fa-weight', url: '<?= app_base_url("/calculators/math/bmi") ?>' },
            { id: 31, title: 'Age Calculator', description: 'Calculate exact age in years, months, and days', category: 'health', icon: 'fas fa-birthday-cake', url: '<?= app_base_url("/calculators/math/age") ?>' },
            
            // Special
            { id: 50, title: 'Nepali Unit Converter', description: 'Convert between Ropani, Aana, Bigha, Kattha and metric units', category: 'math', icon: 'fas fa-mountain', url: '<?= app_base_url("/calculators/nepali") ?>' },
        ],
        filteredCalculators: [],

        init() {
            this.filteredCalculators = this.calculators;
        },

        filterCalculators() {
            this.filteredCalculators = this.calculators.filter(calc => {
                const matchesSearch = calc.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                    calc.description.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchesCategory = this.selectedCategory === '' || calc.category === this.selectedCategory;
                return matchesSearch && matchesCategory;
            });
        }
    }));
});
</script>
