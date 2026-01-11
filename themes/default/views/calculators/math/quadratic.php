<?php
// themes/default/views/calculators/math/quadratic.php
// PREMIUM QUADRATIC CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="quadraticCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-purple-500/10 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-blue-500/10 rounded-full blur-[100px] animate-float" style="animation-delay: 2s;"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Quadratic Solver</li>
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
                Quadratic <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Solver</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Solve quadratic equations (ax² + bx + c = 0) and visualize the roots.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            <div class="calc-card animate-scale-in col-span-1 md:col-span-2">
                
                <!-- Equation Display -->
                <div class="text-center mb-10">
                    <div class="text-3xl md:text-5xl font-black text-white font-mono tracking-wider">
                        <span class="text-primary" x-text="a || 'a'"></span>x² 
                        <span x-text="b >= 0 ? '+' : '-'"></span> <span class="text-secondary" x-text="Math.abs(b) || 'b'"></span>x 
                        <span x-text="c >= 0 ? '+' : '-'"></span> <span class="text-accent" x-text="Math.abs(c) || 'c'"></span> = 0
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div>
                        <label class="calc-label text-center">Coefficient a</label>
                        <input type="number" x-model.number="a" @input="calculate()" class="calc-input text-center text-xl font-bold border-primary/50 focus:border-primary" placeholder="a">
                    </div>
                    <div>
                        <label class="calc-label text-center">Coefficient b</label>
                        <input type="number" x-model.number="b" @input="calculate()" class="calc-input text-center text-xl font-bold border-secondary/50 focus:border-secondary" placeholder="b">
                    </div>
                    <div>
                        <label class="calc-label text-center">Constant c</label>
                        <input type="number" x-model.number="c" @input="calculate()" class="calc-input text-center text-xl font-bold border-accent/50 focus:border-accent" placeholder="c">
                    </div>
                </div>

                <!-- Results -->
                <div x-show="roots !== null" class="mt-8 border-t border-white/10 pt-8" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Roots Card -->
                        <div class="bg-gradient-to-br from-white/5 to-white/0 border border-white/10 rounded-xl p-6 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition">
                                <i class="fas fa-square-root-alt text-6xl"></i>
                            </div>
                            <div class="text-sm text-gray-400 uppercase tracking-widest mb-4">Roots (Solutions)</div>
                            <div class="text-2xl font-mono font-bold text-white leading-loose" x-html="roots"></div>
                            <div class="mt-4 text-xs text-secondary font-bold" x-text="rootType"></div>
                        </div>

                        <!-- Discriminant Card -->
                        <div class="bg-gradient-to-br from-white/5 to-white/0 border border-white/10 rounded-xl p-6 relative overflow-hidden group">
                             <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition">
                                <i class="fas fa-info-circle text-6xl"></i>
                            </div>
                            <div class="text-sm text-gray-400 uppercase tracking-widest mb-4">Discriminant (Δ)</div>
                            <div class="text-4xl font-mono font-black text-white mb-2" x-text="discriminant"></div>
                            <div class="text-xs text-gray-500">Δ = b² - 4ac</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('quadraticCalculator', () => ({
        a: 1, b: -3, c: 2,
        roots: null,
        discriminant: null,
        rootType: null,

        init() {
            this.calculate();
        },

        calculate() {
            if (this.a === 0 || this.a === null) {
                this.roots = "Not a quadratic equation (a ≠ 0)";
                return;
            }

            const D = (this.b * this.b) - (4 * this.a * this.c);
            this.discriminant = D.toFixed(2);

            if (D > 0) {
                const x1 = (-this.b + Math.sqrt(D)) / (2 * this.a);
                const x2 = (-this.b - Math.sqrt(D)) / (2 * this.a);
                this.roots = `x₁ = ${x1.toFixed(4)}<br>x₂ = ${x2.toFixed(4)}`;
                this.rootType = "Two Real Distinct Roots";
            } else if (D === 0) {
                const x = -this.b / (2 * this.a);
                this.roots = `x = ${x.toFixed(4)}`;
                this.rootType = "One Real Root (Repeated)";
            } else {
                const real = (-this.b / (2 * this.a)).toFixed(4);
                const imag = (Math.sqrt(Math.abs(D)) / (2 * this.a)).toFixed(4);
                this.roots = `x₁ = ${real} + ${imag}i<br>x₂ = ${real} - ${imag}i`;
                this.rootType = "Complex Conjugate Roots";
            }
        }
    }));
});
</script>
