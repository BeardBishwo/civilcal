<?php
// themes/default/views/calculators/statistics/probability.php
// PREMIUM PROBABILITY CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="bg-background min-h-screen relative overflow-hidden" x-data="probabilityCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] left-[10%] w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[120px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Statistics</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-dice"></i>
                <span>COMBINATORICS</span>
            </div>
            <h1 class="calc-title">Probability <span class="text-gradient">Engine</span></h1>
            <p class="calc-subtitle">Calculate Permutations (nPr) and Combinations (nCr) instantly.</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Input -->
                <div class="calc-card animate-scale-in">
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="calc-label">n (Total Set)</label>
                                <input type="number" x-model.number="n" @input="calculate()" class="calc-input" placeholder="10" min="0">
                            </div>
                            <div>
                                <label class="calc-label">r (Subset)</label>
                                <input type="number" x-model.number="r" @input="calculate()" class="calc-input" placeholder="3" min="0">
                            </div>
                        </div>

                        <div class="bg-purple-500/10 p-4 rounded-xl border border-purple-500/20 text-xs text-purple-200">
                            <h4 class="font-bold mb-2">Definitions:</h4>
                            <ul class="space-y-1 list-disc list-inside opacity-80">
                                <li><strong>n</strong>: Total number of items in the set.</li>
                                <li><strong>r</strong>: Number of items to choose/arrange.</li>
                                <li>Must satisfy: n ≥ r ≥ 0</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up bg-gradient-to-br from-purple-900/20 to-black border border-purple-500/20 flex flex-col justify-center">
                    
                     <div class="space-y-8">
                         
                         <!-- Permutations -->
                         <div class="text-center">
                             <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold flex justify-center items-center gap-2">
                                 <span>Permutations</span>
                                 <span class="text-[10px] bg-white/10 px-2 rounded">Order Matters</span>
                             </div>
                             <div class="flex items-center justify-center gap-4">
                                 <div class="text-gray-500 font-mono text-sm">nPr =</div>
                                 <div class="text-4xl font-black text-white" x-text="fmt(nPr)"></div>
                             </div>
                             <div class="text-[10px] text-gray-500 mt-1 font-mono">n! / (n-r)!</div>
                         </div>

                         <div class="w-full h-px bg-white/10"></div>

                         <!-- Combinations -->
                         <div class="text-center">
                             <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold flex justify-center items-center gap-2">
                                 <span>Combinations</span>
                                 <span class="text-[10px] bg-white/10 px-2 rounded">Any Order</span>
                             </div>
                             <div class="flex items-center justify-center gap-4">
                                 <div class="text-gray-500 font-mono text-sm">nCr =</div>
                                 <div class="text-4xl font-black text-white" x-text="fmt(nCr)"></div>
                             </div>
                             <div class="text-[10px] text-gray-500 mt-1 font-mono">n! / [r!(n-r)!]</div>
                         </div>

                     </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('probabilityCalculator', () => ({
        n: 10,
        r: 3,
        nPr: 0,
        nCr: 0,

        init() {
            this.calculate();
        },

        calculate() {
             if (this.n === '' || this.r === '' || this.n < 0 || this.r < 0) {
                 this.nPr = 0; this.nCr = 0; return;
             }
             
             if (this.r > this.n) {
                 this.nPr = 0; this.nCr = 0; return;
             }

             // Factorial function
             const fact = (num) => {
                 if (num < 0) return -1;
                 if (num === 0) return 1;
                 let res = 1;
                 for(let i=2; i<=num; i++) {
                     res *= i;
                     // Prevent infinity for very large numbers
                     if(!isFinite(res)) return Infinity;
                 }
                 return res;
             };

             const fn = fact(this.n);
             const fr = fact(this.r);
             const fnr = fact(this.n - this.r);

             if (fn === Infinity || fr === Infinity || fnr === Infinity) {
                 this.nPr = Infinity;
                 this.nCr = Infinity;
                 return;
             }

             this.nPr = fn / fnr;
             this.nCr = fn / (fr * fnr);
        },
        
        fmt(num) {
            if (num === Infinity) return "Too Large";
            return num.toLocaleString('en-US');
        }
    }));
});
</script>
