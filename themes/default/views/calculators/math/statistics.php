<?php
// themes/default/views/calculators/math/statistics.php
// PREMIUM STATISTICS CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="statsCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute bottom-0 right-0 w-[800px] h-[800px] bg-blue-500/5 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Statistics Calculator</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Data Analysis</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Statistics <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Engine</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Instant descriptive statistics: Mean, Median, Mode, Standard Deviation, and more.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-6xl mx-auto">
            
            <!-- Input Panel -->
            <div class="calc-card animate-scale-in col-span-1 md:col-span-1">
                <h3 class="text-lg font-bold text-white mb-4">Input Data</h3>
                <textarea x-model="input" @input="calculate()" class="calc-input h-64 font-mono text-sm leading-relaxed" placeholder="Enter numbers separated by commas, spaces, or newlines...&#10;Example:&#10;10, 25, 30&#10;45, 50"></textarea>
                <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                    <span x-text="count + ' Data points'"></span>
                    <button @click="input=''; calculate()" class="text-red-400 hover:text-red-300">Clear All</button>
                </div>
            </div>

            <!-- Results Panel -->
            <div class="calc-card animate-scale-in col-span-1 md:col-span-2">
                <h3 class="text-lg font-bold text-white mb-6">Results Overview</h3>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Mean -->
                    <div class="glass-card p-4 text-center">
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Mean</div>
                        <div class="text-2xl font-bold text-white" x-text="fmt(mean)">-</div>
                    </div>

                     <!-- Median -->
                     <div class="glass-card p-4 text-center">
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Median</div>
                        <div class="text-2xl font-bold text-white" x-text="fmt(median)">-</div>
                    </div>

                     <!-- Min -->
                     <div class="glass-card p-4 text-center">
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Minimum</div>
                        <div class="text-2xl font-bold text-white" x-text="fmt(min)">-</div>
                    </div>

                     <!-- Max -->
                     <div class="glass-card p-4 text-center">
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Maximum</div>
                        <div class="text-2xl font-bold text-white" x-text="fmt(max)">-</div>
                    </div>

                    <!-- Sum -->
                     <div class="glass-card p-4 text-center">
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Sum</div>
                        <div class="text-2xl font-bold text-white" x-text="fmt(sum)">-</div>
                    </div>

                    <!-- Range -->
                     <div class="glass-card p-4 text-center">
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Range</div>
                        <div class="text-2xl font-bold text-white" x-text="fmt(range)">-</div>
                    </div>

                    <!-- Std Dev -->
                     <div class="glass-card p-4 text-center col-span-2">
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Standard Deviation (Population)</div>
                        <div class="text-2xl font-bold text-accent" x-text="fmt(stdDev)">-</div>
                    </div>
                </div>

                <!-- Mode Section -->
                <div class="mt-6 p-4 rounded-xl bg-white/5 border border-white/10">
                    <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold">Mode (Most Frequent)</div>
                    <div class="text-sm font-mono text-gray-300" x-text="mode || 'No mode (all unique or empty)'"></div>
                </div>

                <div x-show="sorted" class="mt-4 text-xs text-gray-500 font-mono break-all">
                    Sorted: <span x-text="sorted"></span>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('statsCalculator', () => ({
        input: '10, 20, 25, 30, 45, 50, 50',
        count: 0,
        mean: null, median: null, mode: null,
        min: null, max: null, range: null, sum: null,
        stdDev: null,
        sorted: null,

        init() {
            this.calculate();
        },

        calculate() {
             // Parse input (split by comma, space, newline)
            const raw = this.input.split(/[\n, \t]+/).map(n => parseFloat(n)).filter(n => !isNaN(n));
            this.count = raw.length;

            if (this.count === 0) {
                this.mean = this.median = this.mode = this.min = this.max = this.range = this.sum = this.stdDev = this.sorted = null;
                return;
            }

            // Calculations
            // Sort
            raw.sort((a,b) => a - b);
            this.sorted = raw.join(', ');

            this.sum = raw.reduce((a,b) => a + b, 0);
            this.mean = this.sum / this.count;
            this.min = raw[0];
            this.max = raw[this.count - 1];
            this.range = this.max - this.min;

            // Median
            const mid = Math.floor(this.count / 2);
            if (this.count % 2 === 0) {
                this.median = (raw[mid-1] + raw[mid]) / 2;
            } else {
                this.median = raw[mid];
            }

            // Mode
            const freq = {};
            let maxFreq = 0;
            for(let n of raw) {
                freq[n] = (freq[n] || 0) + 1;
                if(freq[n] > maxFreq) maxFreq = freq[n];
            }
            if (maxFreq > 1) {
                this.mode = Object.keys(freq).filter(k => freq[k] === maxFreq).join(', ');
            } else {
                this.mode = "All values unique";
            }

            // Std Dev (Population)
            const sumSqDiff = raw.reduce((a, b) => a + Math.pow(b - this.mean, 2), 0);
            this.stdDev = Math.sqrt(sumSqDiff / this.count);
        },

        fmt(n) {
            return n !== null ? n.toLocaleString('en-US', { maximumFractionDigits: 4 }) : '-';
        }
    }));
});
</script>
