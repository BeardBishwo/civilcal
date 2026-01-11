<?php
// themes/default/views/calculators/finance/investment.php
// PREMIUM INVESTMENT CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="investmentCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute bottom-[0%] left-[0%] w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Investment Calculator</li>
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
                Smart <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Investing</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Simulate your portfolio growth with recurring contributions.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Inputs -->
                <div class="calc-card animate-scale-in col-span-1">
                    <h3 class="text-lg font-bold text-white mb-6">Plan Details</h3>
                     <div class="space-y-6">
                        <div>
                            <label class="calc-label">Starting Amount ($)</label>
                            <input type="number" x-model.number="initial" @input="calculate()" class="calc-input text-xl font-bold" placeholder="10000">
                        </div>
                        <div>
                            <label class="calc-label">Monthly Contribution ($)</label>
                            <input type="number" x-model.number="monthly" @input="calculate()" class="calc-input text-xl font-bold" placeholder="500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="calc-label">Annual Return (%)</label>
                                <input type="number" x-model.number="rate" @input="calculate()" class="calc-input text-center" placeholder="7.0">
                            </div>
                            <div>
                                <label class="calc-label">Years to Grow</label>
                                <input type="number" x-model.number="years" @input="calculate()" class="calc-input text-center" placeholder="10">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div class="calc-card animate-slide-up col-span-1 lg:col-span-2 flex flex-col">
                    
                     <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="glass-card p-4 text-center">
                            <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Total Contributions</div>
                            <div class="text-xl md:text-2xl font-bold text-white">$<span x-text="fmt(totalContr)"></span></div>
                        </div>
                        <div class="glass-card p-4 text-center">
                            <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Total Interest</div>
                            <div class="text-xl md:text-2xl font-bold text-accent">$<span x-text="fmt(totalInterest)"></span></div>
                        </div>
                         <div class="glass-card p-4 text-center border border-primary/30 bg-primary/10">
                            <div class="text-xs text-primary uppercase tracking-widest mb-1 font-bold">Future Value</div>
                            <div class="text-xl md:text-3xl font-black text-white">$<span x-text="fmt(endBalance)"></span></div>
                        </div>
                    </div>

                     <div class="flex-grow bg-black/20 rounded-xl p-4 relative min-h-[350px]">
                        <canvas id="investChart"></canvas>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('investmentCalculator', () => ({
        initial: 10000,
        monthly: 500,
        rate: 8,
        years: 20,
        endBalance: 0,
        totalContr: 0,
        totalInterest: 0,
        chart: null,

        init() {
            this.initChart();
            this.calculate();
        },

        calculate() {
             const r = this.rate / 100 / 12; // Monthly rate
             const n = this.years * 12; // Total months
             
             let balance = this.initial;
             let totalC = this.initial;
             
             // Simple loop for final calculation? No, use formula for instant calc if possible, but for chart we need loop anyway.
             // We'll regenerate chart data which serves both.
             
             this.updateChart();
        },

        initChart() {
            const ctx = document.getElementById('investChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'bar', // Stacked bar often looks nice for invest vs interest
                data: {
                    labels: [],
                    datasets: [
                        { label: 'Interest', data: [], backgroundColor: '#F472B6', stack: 'Stack 0' },
                        { label: 'Contributions', data: [], backgroundColor: '#60A5FA', stack: 'Stack 0' },
                        { label: 'Initial', data: [], backgroundColor: '#34D399', stack: 'Stack 0' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { color: '#9CA3AF' } } },
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { color: '#6B7280' } },
                        y: { stacked: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#6B7280', callback: v => '$' + v/1000 + 'k' } }
                    }
                }
            });
        },

        updateChart() {
            if (!this.chart || !this.years) return;

            const labels = [];
            const dataInitial = [];
            const dataContr = [];
            const dataInterest = [];

            let balance = this.initial;
            let contrSum = 0;
            
            const r = this.rate / 100 / 12;

            for(let y=0; y <= this.years; y++) {
                labels.push('Year ' + y);
                
                // Current breakdown
                const investBase = this.initial + contrSum;
                const interestPart = balance - investBase;
                
                dataInitial.push(this.initial);
                dataContr.push(contrSum);
                dataInterest.push(interestPart);

                // Advance 12 months for next iteration
                if(y < this.years) {
                    for(let m=0; m<12; m++) {
                        balance += this.monthly;
                        contrSum += this.monthly;
                        balance *= (1 + r);
                    }
                }
            }

            this.endBalance = balance;
            this.totalContr = this.initial + contrSum;
            this.totalInterest = balance - this.totalContr;

            this.chart.data.labels = labels;
            this.chart.data.datasets[0].data = dataInterest;
            this.chart.data.datasets[1].data = dataContr;
            this.chart.data.datasets[2].data = dataInitial;
            this.chart.update();
        },

        fmt(n) {
             return n ? n.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : '0';
        }
    }));
});
</script>
