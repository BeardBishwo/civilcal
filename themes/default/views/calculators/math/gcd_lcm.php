<?php
// themes/default/views/calculators/math/gcd_lcm.php
// PREMIUM GCD/LCM CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="gcdLcmCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] left-[20%] w-[500px] h-[500px] bg-sky-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">GCD & LCM</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Number Theory</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                GCD & <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">LCM</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate the Greatest Common Divisor and Least Common Multiple of two or more numbers.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="calc-card animate-scale-in">
                <h3 class="text-lg font-bold text-white mb-6">Enter Numbers</h3>
                
                <div class="flex flex-col md:flex-row gap-4 mb-8">
                     <div class="flex-1">
                        <label class="calc-label">Number A</label>
                        <input type="number" x-model.number="a" @input="calculate()" class="calc-input text-2xl font-bold" placeholder="12">
                     </div>
                     <div class="flex-1">
                        <label class="calc-label">Number B</label>
                        <input type="number" x-model.number="b" @input="calculate()" class="calc-input text-2xl font-bold" placeholder="18">
                     </div>
                </div>

                <!-- Results -->
                <div x-show="a && b" class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-slide-up">
                    
                    <!-- GCD Result -->
                    <div class="glass-card p-6 text-center border-l-4 border-l-primary relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-125 transition duration-500">
                            <i class="fas fa-divide text-6xl"></i>
                        </div>
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold z-10 relative">Greatest Common Divisor</div>
                        <div class="text-5xl font-mono font-black text-white z-10 relative" x-text="gcdVal">0</div>
                        <div class="mt-2 text-xs text-gray-500">Highest number that divides both</div>
                    </div>

                    <!-- LCM Result -->
                    <div class="glass-card p-6 text-center border-l-4 border-l-secondary relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-125 transition duration-500">
                            <i class="fas fa-layer-group text-6xl"></i>
                        </div>
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold z-10 relative">Least Common Multiple</div>
                        <div class="text-5xl font-mono font-black text-white z-10 relative" x-text="lcmVal">0</div>
                        <div class="mt-2 text-xs text-gray-500">Smallest number divisible by both</div>
                    </div>

                </div>

                <!-- Steps / Logic -->
                <div x-show="a && b" class="mt-8 pt-6 border-t border-white/10" x-transition>
                    <h4 class="text-sm font-bold text-gray-300 mb-2">Relationship</h4>
                    <div class="p-4 bg-white/5 rounded-xl border border-white/10 text-center font-mono text-gray-400 text-sm">
                        <span x-text="a"></span> × <span x-text="b"></span> = GCD(<span x-text="gcdVal"></span>) × LCM(<span x-text="lcmVal"></span>) = <span x-text="a*b"></span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('gcdLcmCalculator', () => ({
        a: 12, b: 18,
        gcdVal: 0,
        lcmVal: 0,

        init() {
            this.calculate();
        },

        calculate() {
            if (!this.a || !this.b) {
                this.gcdVal = 0;
                this.lcmVal = 0;
                return;
            }
            
            this.gcdVal = this.gcd(this.a, this.b);
            this.lcmVal = Math.abs(this.a * this.b) / this.gcdVal;
        },

        gcd(x, y) {
            x = Math.abs(x);
            y = Math.abs(y);
            while(y) {
                var t = y;
                y = x % y;
                x = t;
            }
            return x;
        }
    }));
});
</script>
