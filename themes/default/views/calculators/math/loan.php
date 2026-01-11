<?php
// themes/default/views/calculators/math/loan.php
// PREMIUM LOAN CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="loanCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] left-[20%] w-[600px] h-[600px] bg-yellow-500/5 rounded-full blur-[120px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Loan Calculator</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Finance</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Loan <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Estimator</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate monthly payments and total interest for personal loans, car loans, or mortgages.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-5xl mx-auto">
            
            <div class="calc-card animate-scale-in grid grid-cols-1 lg:grid-cols-2 gap-10">
                
                <!-- Inputs -->
                <div class="space-y-6">
                    <div>
                        <label class="calc-label">Loan Amount ($)</label>
                        <input type="number" x-model.number="amount" @input="calculate()" class="calc-input text-2xl" placeholder="10000">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="calc-label">Interest Rate (%)</label>
                            <input type="number" x-model.number="rate" @input="calculate()" class="calc-input text-center" placeholder="5.0">
                        </div>
                        <div>
                             <label class="calc-label">Loan Term</label>
                             <div class="flex">
                                <input type="number" x-model.number="term" @input="calculate()" class="calc-input rounded-r-none text-center" placeholder="1">
                                <select x-model="termUnit" @change="calculate()" class="bg-white/5 border border-white/10 border-l-0 rounded-r-lg px-2 text-sm text-gray-300 focus:outline-none">
                                    <option value="years">Years</option>
                                    <option value="months">Months</option>
                                </select>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div class="bg-gradient-to-br from-white/10 to-white/5 rounded-2xl p-8 border border-white/10 flex flex-col justify-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <i class="fas fa-chart-pie text-9xl text-white"></i>
                    </div>

                    <div class="relative z-10">
                        <div class="text-sm text-gray-400 uppercase tracking-widest mb-1">Monthly Payment</div>
                        <div class="text-5xl font-black text-white mb-8">$<span x-text="fmt(monthly)"></span></div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm border-b border-white/10 pb-2">
                                <span class="text-gray-400">Total Principal</span>
                                <span class="font-mono text-white">$<span x-text="fmt(amount)"></span></span>
                            </div>
                            <div class="flex justify-between items-center text-sm border-b border-white/10 pb-2">
                                <span class="text-gray-400">Total Interest</span>
                                <span class="font-mono text-accent font-bold">$<span x-text="fmt(totalInterest)"></span></span>
                            </div>
                            <div class="flex justify-between items-center text-lg font-bold">
                                <span class="text-gray-300">Total Repayment</span>
                                <span class="font-mono text-primary">$<span x-text="fmt(totalPayment)"></span></span>
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
    Alpine.data('loanCalculator', () => ({
        amount: 10000,
        rate: 5.5,
        term: 3,
        termUnit: 'years',
        monthly: 0,
        totalPayment: 0,
        totalInterest: 0,

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.amount || !this.rate || !this.term) {
                this.monthly = 0; return;
            }

            const P = this.amount;
            const r = (this.rate / 100) / 12; // Monthly rate
            
            let n = this.term;
            if (this.termUnit === 'years') n = n * 12; // Convert to months

            // Amortization Formula
            // M = P [ i(1 + i)^n ] / [ (1 + i)^n – 1 ]
            
            if (r === 0) {
                this.monthly = P / n;
            } else {
                 this.monthly = P * ( (r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1) );
            }

            this.totalPayment = this.monthly * n;
            this.totalInterest = this.totalPayment - P;
        },

        fmt(n) {
            return n ? n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00';
        }
    }));
});
</script>
