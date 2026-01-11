<?php
// themes/default/views/calculators/statistics/basic.php
// PREMIUM BASIC STATISTICS CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="basicStatsCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] right-[10%] w-[500px] h-[500px] bg-teal-500/10 rounded-full blur-[120px] animate-float"></div>
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
                <span>Descriptive</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Basic <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Statistics</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate Mean, Median, Mode, and Range from a data set.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Input -->
                <div class="calc-card animate-scale-in">
                    
                    <div class="mb-6">
                        <label class="calc-label text-center mb-4">Enter Data Set</label>
                        <textarea x-model="dataset" @input="calculate()" class="calc-input h-32 font-mono text-sm" placeholder="12, 5, 8, 12, 20, 5, 8"></textarea>
                         <div class="text-xs text-gray-400 mt-2 text-center">
                             Split numbers by comma, space, or new line.
                         </div>
                    </div>

                    <div class="flex justify-center gap-2">
                        <button @click="dataset = '1, 2, 3, 4, 5'; calculate()" class="px-3 py-1 bg-white/5 rounded-full hover:bg-white/10 transition text-xs font-mono text-teal-400">Sequence</button>
                        <button @click="dataset = '10, 10, 20, 30, 40'; calculate()" class="px-3 py-1 bg-white/5 rounded-full hover:bg-white/10 transition text-xs font-mono text-teal-400">Repeating</button>
                         <button @click="dataset = ''; calculate()" class="px-3 py-1 bg-white/5 rounded-full hover:bg-white/10 transition text-xs font-mono text-gray-400">Clear</button>
                    </div>

                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up bg-gradient-to-br from-teal-900/20 to-black border border-teal-500/20">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5 text-center">
                            <div class="text-[10px] text-gray-500 uppercase font-bold mb-1">Count (N)</div>
                            <div class="text-2xl font-bold text-white" x-text="n"></div>
                        </div>
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5 text-center">
                            <div class="text-[10px] text-gray-500 uppercase font-bold mb-1">Sum</div>
                            <div class="text-2xl font-bold text-white" x-text="sum"></div>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/5">
                            <span class="text-gray-400 text-sm">Mean (Average)</span>
                            <span class="text-teal-400 font-bold font-mono text-xl" x-text="mean"></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/5">
                            <span class="text-gray-400 text-sm">Median</span>
                            <span class="text-teal-400 font-bold font-mono text-xl" x-text="median"></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/5">
                            <span class="text-gray-400 text-sm">Mode</span>
                            <span class="text-teal-400 font-bold font-mono text-sm break-all text-right max-w-[50%]" x-text="mode"></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-white/5">
                            <span class="text-gray-400 text-sm">Range</span>
                            <span class="text-teal-400 font-bold font-mono text-xl" x-text="range"></span>
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded-lg bg-teal-500/10 border border-teal-500/20 grid grid-cols-2 gap-4 text-center">
                        <div>
                             <div class="text-[10px] text-teal-200/60 uppercase">Min</div>
                             <div class="text-teal-300 font-bold" x-text="min"></div>
                        </div>
                        <div>
                             <div class="text-[10px] text-teal-200/60 uppercase">Max</div>
                             <div class="text-teal-300 font-bold" x-text="max"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('basicStatsCalculator', () => ({
        dataset: '12, 5, 8, 12, 20, 5, 8',
        n: 0,
        sum: 0,
        mean: 0,
        median: 0,
        mode: 'None',
        range: 0,
        min: 0,
        max: 0,

        init() {
            this.calculate();
        },

        calculate() {
             const raw = this.dataset;
             // Split by comma, space, newline, tab
             const nums = raw.split(/[\s,]+/)
                            .map(n => parseFloat(n))
                            .filter(n => !isNaN(n))
                            .sort((a,b) => a - b);
            
             if (nums.length === 0) {
                 this.reset();
                 return;
             }

             this.n = nums.length;
             this.sum = nums.reduce((a,b) => a + b, 0);
             this.mean = (this.sum / this.n).toFixed(4).replace(/\.?0+$/, "");
             this.min = nums[0];
             this.max = nums[this.n-1];
             this.range = this.max - this.min;

             // Median
             if (this.n % 2 === 0) {
                 this.median = ((nums[this.n/2 - 1] + nums[this.n/2]) / 2).toFixed(4).replace(/\.?0+$/, "");
             } else {
                 this.median = nums[Math.floor(this.n/2)];
             }

             // Mode
             const freq = {};
             let maxFreq = 0;
             nums.forEach(num => {
                 freq[num] = (freq[num] || 0) + 1;
                 if (freq[num] > maxFreq) maxFreq = freq[num];
             });
             
             let modes = [];
             if (maxFreq > 1) {
                 for (const k in freq) {
                     if (freq[k] === maxFreq) modes.push(k);
                 }
             }
             this.mode = modes.length > 0 ? modes.join(', ') : 'None';
        },

        reset() {
            this.n = 0; this.sum = 0; this.mean = 0; this.median = 0;
            this.mode = 'None'; this.range = 0; this.min = 0; this.max = 0;
        }
    }));
});
</script>
