<?php
// themes/default/views/calculators/finance/mortgage.php
// PREMIUM MORTGAGE CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="mortgageCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] left-[20%] w-[600px] h-[600px] bg-indigo-500/10 rounded-full blur-[120px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Mortgage Calculator</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Real Estate</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Mortgage <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Planner</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Estimate your monthly mortgage payments including tax and insurance.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-5xl mx-auto">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                
                <!-- Inputs -->
                <div class="calc-card animate-scale-in">
                    <h3 class="text-lg font-bold text-white mb-6">Property Details</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="calc-label">Home Price ($)</label>
                            <input type="number" x-model.number="price" @input="calculate()" class="calc-input text-2xl" placeholder="300000">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="calc-label">Down Payment ($)</label>
                                <input type="number" x-model.number="downPayment" @input="calculate()" class="calc-input" placeholder="60000">
                            </div>
                            <div>
                                <label class="calc-label">Down Payment (%)</label>
                                <div class="relative">
                                     <input type="number" :value="((downPayment/price)*100).toFixed(1)" @input="downPayment = price * ($el.value/100); calculate()" class="calc-input text-center" placeholder="20">
                                     <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                </div>
                            </div>
                        </div>

                         <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="calc-label">Interest Rate (%)</label>
                                <input type="number" x-model.number="rate" @input="calculate()" class="calc-input text-center" placeholder="3.5">
                            </div>
                            <div>
                                <label class="calc-label">Term (Years)</label>
                                <input type="number" x-model.number="term" @input="calculate()" class="calc-input text-center" placeholder="30">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-white/10">
                            <h4 class="text-sm font-bold text-gray-400 mb-4 uppercase">Allowances (Monthly)</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="calc-label text-xs">Prop. Tax</label>
                                    <input type="number" x-model.number="tax" @input="calculate()" class="calc-input text-sm p-2" placeholder="250">
                                </div>
                                <div>
                                    <label class="calc-label text-xs">Insurance</label>
                                    <input type="number" x-model.number="insurance" @input="calculate()" class="calc-input text-sm p-2" placeholder="100">
                                </div>
                                <div>
                                    <label class="calc-label text-xs">HOA</label>
                                    <input type="number" x-model.number="hoa" @input="calculate()" class="calc-input text-sm p-2" placeholder="50">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div class="flex flex-col gap-6 animate-slide-up">
                    
                    <div class="glass-card p-8 border-l-8 border-l-primary flex flex-col justify-center items-center text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-8 opacity-5">
                            <i class="fas fa-money-check-alt text-9xl text-white"></i>
                        </div>
                        <div class="text-sm text-gray-400 uppercase tracking-widest mb-2 font-bold z-10">Monthly Payment</div>
                        <div class="text-6xl font-black text-white z-10">$<span x-text="fmt(totalMonthly)"></span></div>
                        
                        <div class="mt-6 w-full space-y-2 text-sm z-10">
                             <div class="flex justify-between text-gray-400">
                                <span>Principal & Interest</span>
                                <span class="text-white">$<span x-text="fmt(pi)"></span></span>
                            </div>
                             <div class="flex justify-between text-gray-400">
                                <span>Tax & Insurance & HOA</span>
                                <span class="text-white">$<span x-text="fmt(extras)"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-white/5 border border-white/10 grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase">Loan Amount</div>
                            <div class="text-xl font-bold text-white">$<span x-text="fmt(loanAmount)"></span></div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase">Total Interest</div>
                            <div class="text-xl font-bold text-accent">$<span x-text="fmt(totalInterest)"></span></div>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-white/5">
                            <div class="text-xs text-gray-500 uppercase">Total Cost of Loan</div>
                            <div class="text-2xl font-bold text-white">$<span x-text="fmt(totalCost)"></span></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mortgageCalculator', () => ({
        price: 300000,
        downPayment: 60000,
        rate: 3.5,
        term: 30,
        tax: 250,
        insurance: 100,
        hoa: 0,
        
        pi: 0,
        extras: 0,
        totalMonthly: 0,
        loanAmount: 0,
        totalInterest: 0,
        totalCost: 0,

        init() {
            this.calculate();
        },

        calculate() {
             this.loanAmount = this.price - this.downPayment;
             if (this.loanAmount < 0) this.loanAmount = 0;

             const r = this.rate / 100 / 12;
             const n = this.term * 12;

             // P & I
             if (r === 0) {
                 this.pi = this.loanAmount / n;
             } else {
                 this.pi = this.loanAmount * ( (r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1) );
             }

             if (!isFinite(this.pi)) this.pi = 0;

             // Extras
             this.extras = (this.tax || 0) + (this.insurance || 0) + (this.hoa || 0);

             this.totalMonthly = this.pi + this.extras;

             // Totals
             const totalPayments = this.pi * n;
             this.totalInterest = totalPayments - this.loanAmount;
             this.totalCost = this.price + this.totalInterest + (this.extras * n); // Approx total over life including taxes? Maybe just loan cost.
             // Usually "Total Cost of Loan" implies Principal + Interest. Let's stick to that for clarity + Downpayment maybe?
             // Let's display Total Principal + Interest paid.
             this.totalCost = totalPayments + this.downPayment; 
        },

        fmt(n) {
            return n ? n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00';
        }
    }));
});
</script>
