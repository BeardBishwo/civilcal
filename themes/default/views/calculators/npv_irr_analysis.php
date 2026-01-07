<?php
// themes/default/views/calculators/npv_irr_analysis.php
// PREMIUM NPV/IRR CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="npvIrrCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] left-[20%] w-[500px] h-[500px] bg-orange-500/10 rounded-full blur-[100px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">NPV & IRR</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
             <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-search-dollar"></i>
                <span>INVESTMENT APPRAISAL</span>
            </div>
            <h1 class="calc-title">Investment <span class="text-gradient">Analyzer</span></h1>
            <p class="calc-subtitle">Calculate Net Present Value and Internal Rate of Return.</p>
        </div>

        <div class="calc-grid max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                
                <!-- Setup & Cash Flows -->
                <div class="calc-card animate-scale-in">
                    <h3 class="text-lg font-bold text-white mb-6">Cash Flow Projection</h3>
                    
                    <div class="space-y-6 mb-8">
                        <div>
                            <label class="calc-label">Initial Investment ($)</label>
                            <input type="number" x-model.number="initial" @input="calculate()" class="calc-input text-xl font-bold text-red-400" placeholder="100000">
                        </div>
                         <div>
                            <label class="calc-label">Discount Rate (%)</label>
                            <input type="number" x-model.number="rate" @input="calculate()" class="calc-input" placeholder="10">
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-4 border-t border-white/10 pt-4">
                        <label class="calc-label mb-0">Project Duration (Years)</label>
                        <input type="number" x-model.number="years" @change="updateYears()" class="calc-input w-24 text-center" min="1" max="30">
                    </div>

                    <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                         <template x-for="(cf, index) in cashFlows" :key="index">
                            <div class="flex items-center gap-4 bg-white/5 p-3 rounded-lg border border-white/5">
                                <div class="w-16 font-bold text-gray-400 text-sm" x-text="'Year ' + (index + 1)"></div>
                                <input type="number" x-model.number="cf.amount" @input="calculate()" class="calc-input text-right" placeholder="Cash Flow">
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Results -->
                <div class="flex flex-col justify-center animate-slide-up">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        
                        <div class="glass-card p-6 border-l-4 border-l-primary relative overflow-hidden transition-all duration-300" :class="npv >= 0 ? 'border-l-green-500' : 'border-l-red-500'">
                             <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold z-10 relative">Net Present Value (NPV)</div>
                             <div class="text-4xl font-black z-10 relative" :class="npv >= 0 ? 'text-green-400' : 'text-red-400'">
                                 $<span x-text="fmt(npv)"></span>
                             </div>
                             <div class="mt-2 text-xs text-gray-400 z-10 relative" x-text="npv >= 0 ? 'Project creates value.' : 'Project destroys value.'"></div>
                             
                             <!-- Background Icon -->
                             <div class="absolute -bottom-4 -right-4 opacity-10">
                                 <i class="fas fa-balance-scale text-8xl"></i>
                             </div>
                        </div>

                         <div class="glass-card p-6 border-l-4 border-l-accent relative overflow-hidden">
                             <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold z-10 relative">Internal Rate of Return (IRR)</div>
                             <div class="text-4xl font-black text-accent z-10 relative">
                                 <span x-text="fmtPct(irr)"></span>%
                             </div>
                             <div class="mt-2 text-xs text-gray-400 z-10 relative">Break-even discount rate.</div>
                             
                              <div class="absolute -bottom-4 -right-4 opacity-10">
                                 <i class="fas fa-percentage text-8xl"></i>
                             </div>
                        </div>

                    </div>

                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <h4 class="text-sm font-bold text-white mb-4 uppercase">Analysis Summary</h4>
                        <div class="space-y-3 text-sm">
                             <div class="flex justify-between border-b border-white/5 pb-2">
                                <span class="text-gray-400">Total Inflow (Undiscounted)</span>
                                <span class="text-white font-mono">$<span x-text="fmt(totalInflow)"></span></span>
                            </div>
                             <div class="flex justify-between border-b border-white/5 pb-2">
                                <span class="text-gray-400">Total Profit (Undiscounted)</span>
                                <span class="font-bold font-mono" :class="profit >= 0 ? 'text-green-400' : 'text-red-400'">
                                    $<span x-text="fmt(profit)"></span>
                                </span>
                            </div>
                             <div class="flex justify-between items-center pt-2">
                                <span class="text-gray-400">Payback Period</span>
                                <span class="px-2 py-1 bg-white/10 rounded text-xs font-bold text-white" x-text="payback"></span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('npvIrrCalculator', () => ({
        initial: 100000,
        rate: 10,
        years: 5,
        cashFlows: [],
        
        npv: 0,
        irr: 0,
        totalInflow: 0,
        profit: 0,
        payback: 'N/A',

        init() {
            this.updateYears();
        },

        updateYears() {
            const currentLen = this.cashFlows.length;
            if (this.years > currentLen) {
                for(let i = currentLen; i < this.years; i++) {
                    this.cashFlows.push({ amount: 30000 }); // Default suggestion
                }
            } else if (this.years < currentLen) {
                this.cashFlows.splice(this.years);
            }
            this.calculate();
        },

        calculate() {
             const r = this.rate / 100;
             let npvVal = -this.initial;
             let totalIn = 0;
             let cumulative = -this.initial;
             let paybackYear = null;

             this.cashFlows.forEach((cf, i) => {
                 const t = i + 1;
                 const val = cf.amount || 0;
                 
                 // NPV
                 npvVal += val / Math.pow(1 + r, t);
                 
                 // Stats
                 totalIn += val;
                 
                 // Payback
                 if (paybackYear === null) {
                     cumulative += val;
                     if (cumulative >= 0) {
                         // Fraction calculation
                         const prevCum = cumulative - val;
                         const needed = -prevCum; // Positive
                         const fraction = needed / val;
                         paybackYear = (t - 1) + fraction;
                     }
                 }
             });

             this.npv = npvVal;
             this.totalInflow = totalIn;
             this.profit = totalIn - this.initial;
             this.payback = paybackYear !== null ? paybackYear.toFixed(1) + ' Years' : '> ' + this.years + ' Years';

             this.calculateIRR();
        },

        calculateIRR() {
             // Simple estimation
             // Function for NPV at rate r
             const getNPV = (r) => {
                 let res = -this.initial;
                 this.cashFlows.forEach((cf, i) => {
                     res += (cf.amount || 0) / Math.pow(1 + r, i + 1);
                 });
                 return res;
             };

             // Binary search / Bisection between -50% and 500%
             let low = -0.99;
             let high = 5.0;
             let guess = 0.1;
             
             for(let i=0; i<50; i++) {
                 guess = (low + high) / 2;
                 const npv = getNPV(guess);
                 if (Math.abs(npv) < 1) break; // Close enough
                 if (npv > 0) low = guess;
                 else high = guess;
             }
             
             this.irr = guess * 100;
        },

        fmt(n) {
            return n ? n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00';
        },
        fmtPct(n) {
             return n ? n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00';
        }
    }));
});
</script>
