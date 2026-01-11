<?php
// themes/default/views/calculators/statistics/dispersion.php
// PREMIUM DISPERSION CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="dispersionCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[30%] left-[20%] w-[500px] h-[500px] bg-red-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Statistics</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Variability</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Dispersion <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Calculator</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate Standard Deviation and Variance for Sample or Population.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Input -->
                <div class="calc-card animate-scale-in">
                    
                    <div class="mb-6">
                        <label class="calc-label text-center mb-4">Enter Data Set</label>
                        <textarea x-model="dataset" @input="calculate()" class="calc-input h-32 font-mono text-sm" placeholder="10, 12, 23, 23, 16, 23, 21, 16"></textarea>
                         <div class="text-xs text-gray-400 mt-2 text-center">
                             Split numbers by comma, space, or new line.
                         </div>
                    </div>

                    <div class="mb-6 bg-white/5 p-3 rounded-lg border border-white/5">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-bold text-gray-300">Calculate as Sample</label>
                            <button 
                                @click="isSample = !isSample; calculate()" 
                                class="w-12 h-6 rounded-full p-1 transition-colors duration-300 focus:outline-none" 
                                :class="isSample ? 'bg-primary' : 'bg-gray-700'"
                            >
                                <div 
                                    class="w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-300"
                                    :class="isSample ? 'translate-x-6' : 'translate-x-0'"
                                ></div>
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2" x-text="isSample ? 'Divides by N-1 (Bessel\'s correction)' : 'Divides by N (Population)'"></p>
                    </div>

                    <div class="flex justify-center gap-2">
                         <button @click="dataset = ''; calculate()" class="px-3 py-1 bg-white/5 rounded-full hover:bg-white/10 transition text-xs font-mono text-gray-400">Clear</button>
                    </div>

                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up bg-gradient-to-br from-red-900/20 to-black border border-red-500/20">
                    
                    <div class="text-center mb-6">
                        <div class="text-[10px] text-gray-500 uppercase font-bold mb-1">Standard Deviation (σ)</div>
                        <div class="text-4xl font-black text-white" x-text="sd"></div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/5">
                            <span class="text-gray-400 text-sm">Variance (σ²)</span>
                            <span class="text-red-400 font-bold font-mono text-xl" x-text="variance"></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/5">
                            <span class="text-gray-400 text-sm">Mean (Average)</span>
                            <span class="text-red-400 font-bold font-mono text-xl" x-text="mean"></span>
                        </div>
                         <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/5">
                            <span class="text-gray-400 text-sm">Count (N)</span>
                            <span class="text-white font-bold font-mono text-xl" x-text="n"></span>
                        </div>
                    </div>

                    <div class="mt-4 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-center">
                        <div class="text-[10px] text-red-200/60 uppercase">Coef. of Variation (CV)</div>
                        <div class="text-2xl font-bold text-red-300" x-text="cv"></div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dispersionCalculator', () => ({
        dataset: '10, 12, 23, 23, 16, 23, 21, 16',
        isSample: true,
        n: 0,
        mean: 0,
        sd: 0,
        variance: 0,
        cv: '0%',

        init() {
            this.calculate();
        },

        calculate() {
             const raw = this.dataset;
             const nums = raw.split(/[\s,]+/)
                            .map(n => parseFloat(n))
                            .filter(n => !isNaN(n));
            
             if (nums.length < 2) {
                 this.reset();
                 return;
             }

             this.n = nums.length;
             const sum = nums.reduce((a,b) => a + b, 0);
             this.mean = sum / this.n;

             // Variance
             let sumSqDiff = 0;
             nums.forEach(num => {
                 sumSqDiff += Math.pow(num - this.mean, 2);
             });
             
             const divisor = this.isSample ? (this.n - 1) : this.n;
             const v = sumSqDiff / divisor;
             const s = Math.sqrt(v);
             const c = (s / this.mean) * 100;

             this.variance = v.toFixed(4).replace(/\.?0+$/, "");
             this.sd = s.toFixed(4).replace(/\.?0+$/, "");
             this.mean = this.mean.toFixed(4).replace(/\.?0+$/, "");
             this.cv = c.toFixed(2).replace(/\.?0+$/, "") + '%';
        },

        reset() {
            this.n = 0; this.mean = 0; this.sd = 0; this.variance = 0; this.cv = '0%';
        }
    }));
});
</script>
