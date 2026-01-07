<?php
// themes/default/views/calculators/finance/loan.php
// PREMIUM FINANCE LOAN CALCULATOR (ADVANCED)
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="financeLoanCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[30%] left-[30%] w-[500px] h-[500px] bg-sky-500/10 rounded-full blur-[100px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Loan Analytics</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <h1 class="calc-title">Loan <span class="text-gradient">Manager</span></h1>
            <p class="calc-subtitle">Advanced payment calculation with principal vs interest breakdown.</p>
        </div>

        <div class="calc-grid max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Inputs -->
                <div class="calc-card animate-scale-in h-fit">
                    <h3 class="text-lg font-bold text-white mb-6">Loan Terms</h3>
                     <div class="space-y-6">
                        <div>
                            <label class="calc-label">Loan Amount ($)</label>
                            <input type="number" x-model.number="amount" @input="calculate()" class="calc-input text-2xl" placeholder="50000">
                        </div>

                         <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="calc-label">Interest Rate (%)</label>
                                <input type="number" x-model.number="rate" @input="calculate()" class="calc-input text-center" placeholder="6.5">
                            </div>
                            <div>
                                <label class="calc-label">Duration</label>
                                <div class="flex">
                                    <input type="number" x-model.number="term" @input="calculate()" class="calc-input rounded-r-none text-center" placeholder="5">
                                    <select x-model="termUnit" @change="calculate()" class="bg-white/5 border border-white/10 border-l-0 rounded-r-lg px-2 text-sm text-gray-300 focus:outline-none">
                                        <option value="years">Years</option>
                                        <option value="months">Months</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-white/5 rounded-xl border border-white/10 mt-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-400 text-sm">Monthly Payment</span>
                                <span class="text-2xl font-bold text-white">$<span x-text="fmt(monthly)"></span></span>
                            </div>
                             <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>Total <span x-text="months"></span> Payments</span>
                                <span>Excludes taxes/fees</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart & Summary -->
                <div class="calc-card animate-slide-up bg-white/5 flex flex-col justify-center items-center">
                    
                    <div class="relative w-64 h-64 mb-6">
                        <canvas id="loanChart"></canvas>
                        <!-- Center Text -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <div class="text-xs text-gray-500 uppercase">Total Payback</div>
                            <div class="text-xl font-bold text-white">$<span x-text="fmt(total)"></span></div>
                        </div>
                    </div>

                    <div class="w-full grid grid-cols-2 gap-4 text-center">
                        <div class="p-3">
                             <div class="flex items-center justify-center gap-2 text-sm text-gray-400 mb-1">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Principal
                            </div>
                            <div class="text-lg font-bold text-white">$<span x-text="fmt(amount)"></span></div>
                        </div>
                         <div class="p-3">
                             <div class="flex items-center justify-center gap-2 text-sm text-gray-400 mb-1">
                                <span class="w-3 h-3 rounded-full bg-red-500"></span> Interest
                            </div>
                            <div class="text-lg font-bold text-red-400">$<span x-text="fmt(interest)"></span></div>
                        </div>
                    </div>

                    <div class="mt-4 text-xs text-center text-gray-500 max-w-xs">
                        Tip: You will pay <strong class="text-red-400" x-text="fmt(interest)"></strong> in interest over the life of this loan.
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('financeLoanCalculator', () => ({
        amount: 25000,
        rate: 5,
        term: 60,
        termUnit: 'months',
        
        monthly: 0,
        total: 0,
        interest: 0,
        months: 0,
        chart: null,

        init() {
            this.initChart();
            this.calculate();
        },

        calculate() {
             if (!this.amount || !this.rate || !this.term) return;

             let n = this.term;
             if (this.termUnit === 'years') n = n * 12;
             this.months = n;

             const r = this.rate / 100 / 12;
             
             if (r === 0) {
                 this.monthly = this.amount / n;
             } else {
                 this.monthly = this.amount * ( (r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1) );
             }

             this.total = this.monthly * n;
             this.interest = this.total - this.amount;

             this.updateChart();
        },

        initChart() {
            const ctx = document.getElementById('loanChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Principal', 'Interest'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#10B981', '#EF4444'], // Emerald-500, Red-500
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '70%',
                    plugins: { legend: { display: false } }
                }
            });
        },

        updateChart() {
            if (!this.chart) return;
            this.chart.data.datasets[0].data = [this.amount, this.interest];
            this.chart.update();
        },

        fmt(n) {
            return n ? n.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : '0';
        }
    }));
});
</script>
