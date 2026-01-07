<?php
// themes/default/views/calculators/finance/salary.php
// PREMIUM SALARY CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="salaryCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] left-[10%] w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Salary Calculator</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-wallet"></i>
                <span>INCOME</span>
            </div>
            <h1 class="calc-title">Net Pay <span class="text-gradient">Estimator</span></h1>
            <p class="calc-subtitle">Calculate your take-home pay based on salary, tax rate, and deductions.</p>
        </div>

        <div class="calc-grid max-w-5xl mx-auto">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                
                <!-- Inputs -->
                <div class="calc-card animate-scale-in">
                    <h3 class="text-lg font-bold text-white mb-6">Salary Details</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="calc-label">Gross Salary</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-4 rounded-l-lg border border-white/10 bg-white/5 text-gray-400 font-bold">$</span>
                                <input type="number" x-model.number="gross" @input="calculate()" class="calc-input rounded-l-none text-xl" placeholder="60000">
                            </div>
                        </div>

                         <div>
                            <label class="calc-label">Pay Period (for Input)</label>
                            <select x-model="period" @change="calculate()" class="calc-input w-full">
                                <option value="year">Annually (Yearly)</option>
                                <option value="month">Monthly</option>
                                <option value="hour">Hourly (40h/wk)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="calc-label mr-2">Tax Rate (%)</label>
                                <input type="number" x-model.number="tax" @input="calculate()" class="calc-input text-center" placeholder="20">
                            </div>
                            <div>
                                <label class="calc-label">Deductions ($)</label>
                                <input type="number" x-model.number="deduc" @input="calculate()" class="calc-input text-center" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div class="bg-white/5 rounded-3xl border border-white/10 p-2 animate-slide-up">
                    <div class="bg-background rounded-2xl p-6 h-full flex flex-col justify-center">
                        
                        <!-- Toggle Result View -->
                        <div class="flex justify-center mb-6">
                             <div class="bg-white/5 p-1 rounded-lg border border-white/10 inline-flex">
                                <button @click="view = 'year'" :class="view === 'year' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-sm rounded-md transition-all font-bold">Yearly</button>
                                <button @click="view = 'month'" :class="view === 'month' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-sm rounded-md transition-all font-bold">Monthly</button>
                                <button @click="view = 'biweek'" :class="view === 'biweek' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-sm rounded-md transition-all font-bold">Bi-Weekly</button>
                                <button @click="view = 'week'" :class="view === 'week' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="px-3 py-1 text-sm rounded-md transition-all font-bold">Weekly</button>
                            </div>
                        </div>

                        <div class="text-center mb-8">
                            <div class="text-sm text-gray-400 uppercase tracking-widest mb-1">Estimated Take Home</div>
                            <div class="text-5xl font-black text-white">$<span x-text="fmt(stats[view].net)"></span></div>
                             <div class="text-xs text-gray-500 mt-2">per <span x-text="viewLabel(view)"></span></div>
                        </div>

                        <div class="space-y-4">
                             <div class="flex justify-between items-center text-sm border-b border-white/5 pb-2">
                                <span class="text-gray-400">Gross Pay</span>
                                <span class="font-mono text-white">$<span x-text="fmt(stats[view].gross)"></span></span>
                            </div>
                            <div class="flex justify-between items-center text-sm border-b border-white/5 pb-2">
                                <span class="text-red-400">Tax (<span x-text="tax"></span>%)</span>
                                <span class="font-mono text-red-400">-$<span x-text="fmt(stats[view].tax)"></span></span>
                            </div>
                             <div class="flex justify-between items-center text-sm border-b border-white/5 pb-2">
                                <span class="text-yellow-400">Deductions</span>
                                <span class="font-mono text-yellow-400">-$<span x-text="fmt(stats[view].deduc)"></span></span>
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
    Alpine.data('salaryCalculator', () => ({
        gross: 60000,
        period: 'year',
        tax: 20,
        deduc: 0,
        view: 'year',
        
        stats: {
            year: { gross: 0, tax: 0, deduc: 0, net: 0 },
            month: { gross: 0, tax: 0, deduc: 0, net: 0 },
            biweek: { gross: 0, tax: 0, deduc: 0, net: 0 },
            week:  { gross: 0, tax: 0, deduc: 0, net: 0 }
        },

        init() {
            this.calculate();
        },

        calculate() {
             let annualGross = this.gross;
             
             // Normalize to Annual
             if (this.period === 'month') annualGross = this.gross * 12;
             if (this.period === 'hour') annualGross = this.gross * 40 * 52;

             const annualTax = annualGross * (this.tax / 100);
             // Deductions input is generic. Let's assume input is Annual deductions? Or matching period?
             // Usually simpler to ask "Annual Deductions". Let's assume Annual for now or scale it?
             // Actually, if someone enters $100 deduction and selects "Monthly", does it mean $100/mo? Likely.
             // But my deduction input doesn't have a specific period selector. Let's assume it matches the "Pay Period" selector.
             
             let annualDeduc = this.deduc;
             if (this.period === 'month') annualDeduc = this.deduc * 12;
             if (this.period === 'hour') annualDeduc = this.deduc * 40 * 52; // Deduction per hour?! Rare. Maybe deduction is always annual?
             // Let's refine logic: Assume input matches the select box period.
             
             const annualNet = annualGross - annualTax - annualDeduc;

             this.stats.year = { 
                 gross: annualGross, 
                 tax: annualTax, 
                 deduc: annualDeduc, 
                 net: annualNet 
             };
             
             this.stats.month = this.divideStats(this.stats.year, 12);
             this.stats.biweek = this.divideStats(this.stats.year, 26);
             this.stats.week = this.divideStats(this.stats.year, 52);
        },
        
        divideStats(source, divisor) {
            return {
                gross: source.gross / divisor,
                tax: source.tax / divisor,
                deduc: source.deduc / divisor,
                net: source.net / divisor
            };
        },

        viewLabel(v) {
            if(v==='biweek') return 'two weeks';
            return v;
        },

        fmt(n) {
            return n ? n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00';
        }
    }));
});
</script>
