<?php
// themes/default/views/calculators/math/right_triangle.php
// PREMIUM RIGHT TRIANGLE SOLVER
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="triangleCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-primary/10 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-secondary/10 rounded-full blur-[100px] animate-float" style="animation-delay: 2s;"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Right Triangle Solver</li>
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
                Right <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Triangle</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Enter any two values (sides or angles) to instantly solve the entire triangle.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-5xl mx-auto">
            
            <!-- Visualization -->
            <div class="calc-card animate-scale-in flex items-center justify-center p-8 bg-black/20">
                <div class="relative w-64 h-64">
                    <svg viewBox="-20 -20 140 140" class="w-full h-full drop-shadow-2xl">
                        <!-- Triangle -->
                        <path d="M0,0 L0,100 L100,100 Z" fill="none" class="stroke-primary" stroke-width="2" />
                        <!-- Right Angle Mark -->
                        <path d="M0,90 L10,90 L10,100" fill="none" class="stroke-white/50" stroke-width="1" />
                        
                        <!-- Labels -->
                        <text x="-15" y="50" class="fill-secondary text-[8px] font-bold">a</text>
                        <text x="50" y="115" class="fill-secondary text-[8px] font-bold">b</text>
                        <text x="55" y="45" class="fill-secondary text-[8px] font-bold">c</text>
                        
                        <text x="85" y="95" class="fill-accent text-[8px] font-bold">A</text>
                        <text x="5" y="15" class="fill-accent text-[8px] font-bold">B</text>
                        <text x="-15" y="115" class="fill-white/50 text-[8px]">C=90°</text>
                    </svg>
                    
                     <!-- Live Values Overlay -->
                     <div class="absolute top-1/2 left-[-20px] -translate-y-1/2 text-xs text-secondary font-mono" x-show="a" x-text="'a=' + fmt(a)"></div>
                     <div class="absolute bottom-[-20px] left-1/2 -translate-x-1/2 text-xs text-secondary font-mono" x-show="b" x-text="'b=' + fmt(b)"></div>
                     <div class="absolute top-[30%] left-[60%] text-xs text-secondary font-mono rotate-45" x-show="c" x-text="'c=' + fmt(c)"></div>
                </div>
            </div>

            <!-- Inputs & Results -->
            <div class="calc-card animate-slide-up">
                <h3 class="text-lg font-bold text-white mb-4">Inputs</h3>
                
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="calc-label text-secondary">Side a (Leg)</label>
                        <input type="number" x-model.number="a" @input="solve()" class="calc-input" placeholder="a">
                    </div>
                    <div>
                        <label class="calc-label text-secondary">Side b (Leg)</label>
                        <input type="number" x-model.number="b" @input="solve()" class="calc-input" placeholder="b">
                    </div>
                    <div>
                        <label class="calc-label text-secondary">Side c (Hypotenuse)</label>
                        <input type="number" x-model.number="c" @input="solve()" class="calc-input" placeholder="c">
                    </div>
                     <div>
                        <label class="calc-label text-accent">Angle A (°)</label>
                        <input type="number" x-model.number="angleA" @input="solve()" class="calc-input" placeholder="A">
                    </div>
                    <div>
                        <label class="calc-label text-accent">Angle B (°)</label>
                        <input type="number" x-model.number="angleB" @input="solve()" class="calc-input" placeholder="B">
                    </div>
                    <div class="flex items-end">
                        <button @click="reset()" class="btn-secondary w-full">Clear</button>
                    </div>
                </div>

                <div x-show="status" class="text-sm text-yellow-500 mb-4 font-mono">
                    <i class="fas fa-info-circle mr-1"></i> <span x-text="status"></span>
                </div>

                <div x-show="area" class="border-t border-white/10 pt-4 grid grid-cols-2 gap-4" x-transition>
                    <div class="p-3 bg-white/5 rounded-lg border border-white/10">
                        <div class="text-xs text-gray-400 uppercase">Area</div>
                        <div class="text-xl font-bold text-white" x-text="fmt(area)"></div>
                    </div>
                    <div class="p-3 bg-white/5 rounded-lg border border-white/10">
                        <div class="text-xs text-gray-400 uppercase">Perimeter</div>
                        <div class="text-xl font-bold text-white" x-text="fmt(perimeter)"></div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('triangleCalculator', () => ({
        a: null, b: null, c: null,
        angleA: null, angleB: null,
        area: null, perimeter: null,
        status: null,

        solve() {
            // Collect defined inputs
            const inputs = { a: this.a, b: this.b, c: this.c, A: this.angleA, B: this.angleB };
            let count = 0;
            for (let k in inputs) if (inputs[k]) count++;

            if (count < 2) {
                this.status = "Enter at least 2 values to solve.";
                // Don't clear derived values yet to allow editing
                return;
            } else {
                this.status = null;
            }

            const rad = Math.PI / 180;
            const deg = 180 / Math.PI;

            // Solver Logic
            // 1. Two Sides
            if (this.a && this.b) {
                this.c = Math.hypot(this.a, this.b);
                this.angleA = Math.atan(this.a / this.b) * deg;
                this.angleB = 90 - this.angleA;
            } else if (this.a && this.c) {
                if (this.c <= this.a) { this.status = "Error: Hypotenuse must be > Leg"; return; }
                this.b = Math.sqrt(this.c**2 - this.a**2);
                this.angleA = Math.asin(this.a / this.c) * deg;
                this.angleB = 90 - this.angleA;
            } else if (this.b && this.c) {
                if (this.c <= this.b) { this.status = "Error: Hypotenuse must be > Leg"; return; }
                this.a = Math.sqrt(this.c**2 - this.b**2);
                this.angleB = Math.asin(this.b / this.c) * deg;
                this.angleA = 90 - this.angleB;
            }
            // 2. One Side + One Angle
            else if (this.a && this.angleA) {
                this.angleB = 90 - this.angleA;
                this.c = this.a / Math.sin(this.angleA * rad);
                this.b = this.c * Math.cos(this.angleA * rad);
            } else if (this.a && this.angleB) {
                this.angleA = 90 - this.angleB;
                this.c = this.a / Math.cos(this.angleB * rad);
                this.b = this.c * Math.sin(this.angleB * rad);
            } else if (this.b && this.angleA) {
                this.angleB = 90 - this.angleA;
                this.c = this.b / Math.cos(this.angleA * rad);
                this.a = this.c * Math.sin(this.angleA * rad);
            } else if (this.b && this.angleB) {
                this.angleA = 90 - this.angleB;
                this.c = this.b / Math.sin(this.angleB * rad);
                this.a = this.c * Math.cos(this.angleB * rad);
            } else if (this.c && this.angleA) {
                this.angleB = 90 - this.angleA;
                this.a = this.c * Math.sin(this.angleA * rad);
                this.b = this.c * Math.cos(this.angleA * rad);
            } else if (this.c && this.angleB) {
                this.angleA = 90 - this.angleB;
                this.b = this.c * Math.sin(this.angleB * rad);
                this.a = this.c * Math.cos(this.angleB * rad);
            }

            // Results
            if (this.a && this.b) {
                this.area = 0.5 * this.a * this.b;
                this.perimeter = this.a + this.b + (this.c || 0);
            }
        },

        reset() {
            this.a = null; this.b = null; this.c = null;
            this.angleA = null; this.angleB = null;
            this.area = null; this.perimeter = null;
            this.status = null;
        },

        fmt(n) {
            return n ? n.toLocaleString('en-US', { maximumFractionDigits: 2 }) : '';
        }
    }));
});
</script>
