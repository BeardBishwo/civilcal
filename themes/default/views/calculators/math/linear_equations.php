<?php
// themes/default/views/calculators/math/linear_equations.php
// PREMIUM LINEAR EQUATIONS SOLVER
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="linearSolver()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[10%] content-center w-full h-[1px] bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
        <div class="absolute left-[30%] top-0 h-full w-[1px] bg-gradient-to-b from-transparent via-white/5 to-transparent"></div>
        <div class="absolute top-[-20%] right-[-10%] w-[600px] h-[600px] bg-purple-500/10 rounded-full blur-[120px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Linear Solver</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Algebra</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                System <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Solver</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Solve systems of two linear equations using Cramer's Rule.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-5xl mx-auto">
            
            <div class="calc-card animate-scale-in">
                <div class="mb-6 text-center text-sm text-gray-400">Format: <span class="font-mono text-white">ax + by = c</span></div>

                <!-- Equation 1 -->
                <div class="flex flex-col md:flex-row items-center justify-center gap-4 mb-4 p-4 rounded-xl bg-white/5 border border-white/5">
                    <div class="font-bold text-gray-500 w-8 text-center">Eq 1</div>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model.number="a1" @input="solve()" class="calc-input w-20 text-center font-bold" placeholder="a1">
                        <span class="text-lg font-mono text-secondary">x</span>
                    </div>
                    <div class="text-xl text-gray-500 font-light">+</div>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model.number="b1" @input="solve()" class="calc-input w-20 text-center font-bold" placeholder="b1">
                        <span class="text-lg font-mono text-secondary">y</span>
                    </div>
                    <div class="text-xl text-gray-500 font-light">=</div>
                    <input type="number" x-model.number="c1" @input="solve()" class="calc-input w-24 text-center font-bold bg-white/10" placeholder="c1">
                </div>

                <!-- Equation 2 -->
                <div class="flex flex-col md:flex-row items-center justify-center gap-4 mb-8 p-4 rounded-xl bg-white/5 border border-white/5">
                    <div class="font-bold text-gray-500 w-8 text-center">Eq 2</div>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model.number="a2" @input="solve()" class="calc-input w-20 text-center font-bold" placeholder="a2">
                        <span class="text-lg font-mono text-secondary">x</span>
                    </div>
                    <div class="text-xl text-gray-500 font-light">+</div>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model.number="b2" @input="solve()" class="calc-input w-20 text-center font-bold" placeholder="b2">
                        <span class="text-lg font-mono text-secondary">y</span>
                    </div>
                    <div class="text-xl text-gray-500 font-light">=</div>
                    <input type="number" x-model.number="c2" @input="solve()" class="calc-input w-24 text-center font-bold bg-white/10" placeholder="c2">
                </div>

                <!-- Results -->
                <div x-show="xVal !== null && yVal !== null" class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-slide-up" x-transition>
                    
                    <div class="glass-card p-6 border-l-4 border-l-primary flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Value of x</div>
                            <div class="text-3xl font-mono font-bold text-white" x-text="fmt(xVal)"></div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary text-xl font-bold font-mono">x</div>
                    </div>

                    <div class="glass-card p-6 border-l-4 border-l-accent flex items-center justify-between">
                         <div>
                            <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Value of y</div>
                            <div class="text-3xl font-mono font-bold text-white" x-text="fmt(yVal)"></div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-accent/20 flex items-center justify-center text-accent text-xl font-bold font-mono">y</div>
                    </div>

                </div>

                <div x-show="status" class="mt-8 text-center p-4 rounded-lg bg-yellow-500/10 border border-yellow-500/20 text-yellow-500" x-text="status" x-transition></div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('linearSolver', () => ({
        a1: 2, b1: 3, c1: 13,
        a2: 5, b2: -1, c2: 7,
        xVal: null, yVal: null,
        status: null,

        init() {
            this.solve();
        },

        solve() {
            if (this.a1==null || this.b1==null || this.c1==null || 
                this.a2==null || this.b2==null || this.c2==null) {
                this.xVal = null; this.yVal = null;
                return;
            }

            // Cramer's Rule
            const D = (this.a1 * this.b2) - (this.a2 * this.b1);
            const Dx = (this.c1 * this.b2) - (this.c2 * this.b1);
            const Dy = (this.a1 * this.c2) - (this.a2 * this.c1);

            if (D !== 0) {
                this.xVal = Dx / D;
                this.yVal = Dy / D;
                this.status = null;
            } else {
                this.xVal = null;
                this.yVal = null;
                if (Dx === 0 && Dy === 0) {
                    this.status = "Infinite Solutions (Dependent System)";
                } else {
                    this.status = "No Solution (Inconsistent System)";
                }
            }
        },

        fmt(n) {
            return n !== null ? Number(n.toFixed(4)).toString() : '';
        }
    }));
});
</script>
