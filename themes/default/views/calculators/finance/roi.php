<?php
// themes/default/views/calculators/finance/roi.php
// PREMIUM ROI CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="roiCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] right-[20%] w-[400px] h-[400px] bg-yellow-400/10 rounded-full blur-[100px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">ROI Calculator</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Profitability</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Return on <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Investment</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate the efficiency of an investment or compare the efficiency of different investments.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="calc-card animate-scale-in">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="calc-label">Initial Investment ($)</label>
                        <input type="number" x-model.number="investment" @input="calculate()" class="calc-input text-2xl font-bold" placeholder="10000">
                    </div>
                    <div>
                        <label class="calc-label">Returned Amount ($)</label>
                        <input type="number" x-model.number="returned" @input="calculate()" class="calc-input text-2xl font-bold" placeholder="15000">
                    </div>
                </div>

                <!-- Length of Investment (Optional for Annualized ROI?) Let's keep it simple first as per original, or add time? Original didn't have time. Let's add Time optionally. -->
                <div class="mb-8">
                    <label class="calc-label">Investment Period (Optional, for Annualized ROI)</label>
                    <div class="flex gap-4">
                        <input type="number" x-model.number="years" @input="calculate()" class="calc-input w-full" placeholder="Period Length">
                        <select x-model="periodUnit" @change="calculate()" class="calc-input w-1/3">
                            <option value="years">Years</option>
                            <option value="months">Months</option>
                        </select>
                    </div>
                </div>

                <!-- Result -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-slide-up">
                    
                    <div class="glass-card p-8 border-l-8 flex flex-col justify-center text-center transition-colors duration-300" 
                         :class="roi >= 0 ? 'border-l-green-500 bg-green-500/5' : 'border-l-red-500 bg-red-500/5'">
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold">Total ROI</div>
                        <div class="text-6xl font-black transition-colors duration-300" 
                             :class="roi >= 0 ? 'text-green-400' : 'text-red-400'">
                            <span x-text="fmt(roi)"></span>%
                        </div>
                        <div class="mt-4 text-sm font-bold text-white">
                            Profit: $<span x-text="fmt(profit)"></span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex justify-between items-center">
                            <span class="text-gray-400 text-sm uppercase">Total Return</span>
                            <span class="text-xl font-bold text-white">$<span x-text="fmt(returned)"></span></span>
                        </div>
                        
                        <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex justify-between items-center" x-show="annualizedRoi !== null">
                             <span class="text-gray-400 text-sm uppercase">Annualized ROI</span>
                            <span class="text-xl font-bold text-accent"><span x-text="fmt(annualizedRoi)"></span>%</span>
                        </div>

                         <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex justify-between items-center" x-show="annualizedRoi !== null">
                             <span class="text-gray-400 text-sm uppercase">Period</span>
                             <span class="text-white font-mono"><span x-text="years"></span> <span x-text="periodUnit"></span></span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('roiCalculator', () => ({
        investment: 10000,
        returned: 12500,
        years: 1,
        periodUnit: 'years',
        
        roi: 0,
        profit: 0,
        annualizedRoi: null,

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.investment) return;

             this.profit = this.returned - this.investment;
             this.roi = (this.profit / this.investment) * 100;

             // Annualized ROI Formula: [(1 + ROI)^(1/n) - 1] * 100
             if (this.years && this.years > 0) {
                 let t = this.years;
                 if (this.periodUnit === 'months') t = t / 12;
                 
                 const totalRoiDecimal = this.returned / this.investment;
                 // Avoid complex number issues if total return is negative? No, total return is amount, usually positive.
                 // If returned is < 0 (loss more than investment?), handled naturally.
                 
                 if (t > 0 && totalRoiDecimal > 0) {
                    const annualized = (Math.pow(totalRoiDecimal, 1/t) - 1) * 100;
                    this.annualizedRoi = annualized;
                 } else {
                     this.annualizedRoi = null;
                 }
             } else {
                 this.annualizedRoi = null;
             }
        },

        fmt(n) {
            return n ? n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00';
        }
    }));
});
</script>
