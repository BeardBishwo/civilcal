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

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-project-diagram"></i>
                <span>NUMBER THEORY</span>
            </div>
            <h1 class="calc-title">GCD & <span class="text-gradient">LCM</span></h1>
            <p class="calc-subtitle">Calculate the Greatest Common Divisor and Least Common Multiple of two or more numbers.</p>
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
