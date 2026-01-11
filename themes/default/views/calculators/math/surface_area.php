<?php
// themes/default/views/calculators/math/surface_area.php
// PREMIUM SURFACE AREA CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="surfaceAreaCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] left-1/2 w-[600px] h-[600px] bg-green-500/10 rounded-full blur-[120px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Surface Area</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Geometry</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Surface <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Area</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Compute the total surface area of common 3D geometric shapes.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="calc-card animate-scale-in">
                <!-- Shape Selector -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <template x-for="s in shapes">
                        <button @click="shape = s.id; resetInputs()" 
                                :class="shape === s.id ? 'bg-primary border-primary text-white scale-105' : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10'"
                                class="flex flex-col items-center justify-center p-4 rounded-xl border transition-all duration-300">
                            <i :class="s.icon" class="text-3xl mb-2"></i>
                            <span class="text-sm font-bold" x-text="s.name"></span>
                        </button>
                    </template>
                </div>

                <!-- Cube -->
                <div x-show="shape === 'cube'" class="animate-slide-up">
                    <label class="calc-label">Side Length (a)</label>
                    <input type="number" x-model.number="a" @input="calculate()" class="calc-input" placeholder="a">
                </div>

                <!-- Sphere -->
                <div x-show="shape === 'sphere'" class="animate-slide-up">
                    <label class="calc-label">Radius (r)</label>
                    <input type="number" x-model.number="r" @input="calculate()" class="calc-input" placeholder="r">
                </div>

                <!-- Cylinder -->
                <div x-show="shape === 'cylinder'" class="grid grid-cols-2 gap-4 animate-slide-up">
                    <div>
                        <label class="calc-label">Radius (r)</label>
                        <input type="number" x-model.number="r" @input="calculate()" class="calc-input" placeholder="r">
                    </div>
                    <div>
                        <label class="calc-label">Height (h)</label>
                        <input type="number" x-model.number="h" @input="calculate()" class="calc-input" placeholder="h">
                    </div>
                </div>

                 <!-- Cuboid -->
                <div x-show="shape === 'cuboid'" class="grid grid-cols-3 gap-4 animate-slide-up">
                    <div>
                        <label class="calc-label">Length (l)</label>
                        <input type="number" x-model.number="l" @input="calculate()" class="calc-input" placeholder="l">
                    </div>
                    <div>
                        <label class="calc-label">Width (w)</label>
                        <input type="number" x-model.number="w" @input="calculate()" class="calc-input" placeholder="w">
                    </div>
                     <div>
                        <label class="calc-label">Height (h)</label>
                        <input type="number" x-model.number="h" @input="calculate()" class="calc-input" placeholder="h">
                    </div>
                </div>

                <!-- Result -->
                <div x-show="result !== null" class="mt-8 pt-8 border-t border-white/10 text-center" x-transition>
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-2">Total Surface Area</div>
                    <div class="text-4xl font-mono font-black text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-500" x-text="fmt(result)"></div>
                    <div class="mt-4 p-3 bg-white/5 inline-block rounded-lg text-sm text-gray-300 font-mono" x-html="formula"></div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('surfaceAreaCalculator', () => ({
        shape: 'cube',
        a: null, r: null, h: null, l: null, w: null,
        result: null,
        formula: null,
        shapes: [
            { id: 'cube', name: 'Cube', icon: 'fas fa-cube' },
            { id: 'cuboid', name: 'Cuboid', icon: 'fas fa-vector-square' },
            { id: 'cylinder', name: 'Cylinder', icon: 'fas fa-database' },
            { id: 'sphere', name: 'Sphere', icon: 'fas fa-globe' }
        ],

        resetInputs() {
            this.a = null; this.r = null; this.h = null; this.l = null; this.w = null;
            this.result = null;
        },

        calculate() {
            if (this.shape === 'cube' && this.a) {
                this.result = 6 * this.a ** 2;
                this.formula = `A = 6a² = 6 × ${this.a}²`;
            } else if (this.shape === 'sphere' && this.r) {
                this.result = 4 * Math.PI * this.r ** 2;
                this.formula = `A = 4πr² = 4π × ${this.r}²`;
            } else if (this.shape === 'cylinder' && this.r && this.h) {
                this.result = (2 * Math.PI * this.r * this.h) + (2 * Math.PI * this.r ** 2);
                this.formula = `A = 2πrh + 2πr²`;
            } else if (this.shape === 'cuboid' && this.l && this.w && this.h) {
                this.result = 2 * (this.l*this.w + this.l*this.h + this.w*this.h);
                this.formula = `A = 2(lw + lh + wh)`;
            } else {
                this.result = null;
            }
        },

        fmt(n) {
            return n ? n.toLocaleString('en-US', { maximumFractionDigits: 2 }) : '';
        }
    }));
});
</script>
