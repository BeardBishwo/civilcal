<?php
// themes/default/views/calculators/finance/compound_interest.php
// PREMIUM COMPOUND INTEREST CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="compoundCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] right-[10%] w-[500px] h-[500px] bg-green-500/10 rounded-full blur-[120px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Compound Profit</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Wealth Building</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Compound <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Growth</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Visualize how your money grows exponentially over time with compound interest.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-6xl mx-auto">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Inputs -->
                <div class="calc-card animate-scale-in col-span-1 lg:col-span-1 h-fit">
                    <h3 class="text-lg font-bold text-white mb-6">Parameters</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="calc-label">Principal Amount ($)</label>
                            <input type="number" x-model.number="principal" @input="calculate()" class="calc-input text-xl font-bold" placeholder="10000">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="calc-label">Rate (%)</label>
                                <input type="number" x-model.number="rate" @input="calculate()" class="calc-input text-center" placeholder="5">
                            </div>
                            <div>
                                <label class="calc-label">Time (Years)</label>
                                <input type="number" x-model.number="years" @input="calculate()" class="calc-input text-center" placeholder="10">
                            </div>
                        </div>

                         <div>
                            <label class="calc-label">Compound Frequency</label>
                            <select x-model.number="frequency" @change="calculate()" class="calc-input w-full">
                                <option value="12">Monthly (12/yr)</option>
                                <option value="4">Quarterly (4/yr)</option>
                                <option value="2">Semi-Annually (2/yr)</option>
                                <option value="1">Annually (1/yr)</option>
                                <option value="365">Daily (365/yr)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Results & Chart -->
                <div class="calc-card animate-slide-up col-span-1 lg:col-span-2 flex flex-col">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="p-6 rounded-2xl bg-gradient-to-br from-green-500/20 to-emerald-600/5 border border-green-500/20">
                            <div class="text-xs text-green-300 uppercase tracking-widest mb-1 font-bold">Future Balance</div>
                            <div class="text-4xl font-black text-white">$<span x-text="fmt(amount)"></span></div>
                        </div>
                         <div class="p-6 rounded-2xl bg-white/5 border border-white/10">
                            <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Total Interest</div>
                            <div class="text-3xl font-bold text-accent">$<span x-text="fmt(interest)"></span></div>
                        </div>
                    </div>

                    <div class="flex-grow bg-black/20 rounded-xl p-4 relative min-h-[300px]">
                        <canvas id="compoundChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('compoundCalculator', () => ({
        principal: 5000,
        rate: 5,
        years: 10,
        frequency: 12, // Monthly
        amount: 0,
        interest: 0,
        chart: null,

        init() {
            this.initChart();
            this.calculate();
        },

        calculate() {
             if (!this.principal || !this.rate || !this.years) return;

             const P = this.principal;
             const r = this.rate / 100;
             const n = this.frequency;
             const t = this.years;

             // A = P(1 + r/n)^(nt)
             this.amount = P * Math.pow((1 + r/n), (n*t));
             this.interest = this.amount - P;

             this.updateChart();
        },

        initChart() {
            const ctx = document.getElementById('compoundChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Balance Growth',
                        data: [],
                        borderColor: '#10B981', // Emerald-500
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Principal',
                        data: [],
                        borderColor: '#6B7280', // Gray-500
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: '#9CA3AF' } },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#6B7280' } },
                        y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#6B7280', callback: (v) => '$' + v/1000 + 'k' } }
                    }
                }
            });
        },

        updateChart() {
            if (!this.chart) return;

            const labels = [];
            const growthData = [];
            const principalData = [];
            
            const P = this.principal;
            const r = this.rate / 100;
            const n = this.frequency;
            
            // Plot points (e.g., every year or more granular for short terms)
            const steps = this.years <= 5 ? 12 : 1; // Monthly points for short term, Yearly for long
            const totalSteps = this.years * (this.years <= 5 ? 12 : 1);

            for(let i=0; i <= totalSteps; i++) {
                const t = this.years <= 5 ? i/12 : i;
                const amt = P * Math.pow((1 + r/n), (n*t));
                
                labels.push(this.years <= 5 ? `Month ${i}` : `Year ${i}`);
                growthData.push(amt);
                principalData.push(P);
            }

            this.chart.data.labels = labels;
            this.chart.data.datasets[0].data = growthData;
            this.chart.data.datasets[1].data = principalData;
            this.chart.update();
        },

        fmt(n) {
            return n ? n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00';
        }
    }));
});
</script>
