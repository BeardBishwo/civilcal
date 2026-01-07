<?php
// themes/default/views/calculators/cash_flow_analysis.php
// PREMIUM CASH FLOW CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="cashFlowCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute bottom-[20%] right-[30%] w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[100px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Cash Flow</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-chart-bar"></i>
                <span>PROJECT FINANCE</span>
            </div>
            <h1 class="calc-title">Cash Flow <span class="text-gradient">Analysis</span></h1>
            <p class="calc-subtitle">Track inflows and outflows to determine net cash flow over time.</p>
        </div>

        <div class="calc-grid max-w-6xl mx-auto">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Inputs Table -->
                <div class="calc-card animate-scale-in flex flex-col h-fit">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-white">Monthly Breakdown</h3>
                        <div class="flex items-center gap-2">
                             <span class="text-sm text-gray-400">Months:</span>
                             <input type="number" x-model.number="monthCount" @change="updateMonths()" class="calc-input w-20 text-center py-1 px-2" min="1" max="60">
                        </div>
                    </div>

                    <div class="max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                        <div class="grid grid-cols-12 gap-2 mb-2 text-xs text-gray-400 uppercase font-bold sticky top-0 bg-[#0F172A] z-10 py-2">
                            <div class="col-span-2 pt-2">Month</div>
                            <div class="col-span-5 text-center">Inflow ($)</div>
                            <div class="col-span-5 text-center">Outflow ($)</div>
                        </div>

                        <template x-for="(m, index) in months" :key="index">
                            <div class="grid grid-cols-12 gap-2 mb-3 bg-white/5 p-2 rounded-lg items-center border border-white/5">
                                <div class="col-span-2 text-sm font-bold text-gray-300" x-text="'M-' + (index + 1)"></div>
                                <div class="col-span-5">
                                    <input type="number" x-model.number="m.in" @input="calculate()" class="calc-input w-full text-green-400 text-right pr-2" placeholder="0">
                                </div>
                                <div class="col-span-5">
                                    <input type="number" x-model.number="m.out" @input="calculate()" class="calc-input w-full text-red-400 text-right pr-2" placeholder="0">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Summary & Chart -->
                <div class="flex flex-col gap-6 animate-slide-up">
                    
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                         <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-xs text-gray-400 uppercase mb-1">Total Inflow</div>
                                <div class="text-xl font-bold text-green-400">$<span x-text="fmt(totalIn)"></span></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 uppercase mb-1">Total Outflow</div>
                                <div class="text-xl font-bold text-red-400">$<span x-text="fmt(totalOut)"></span></div>
                            </div>
                            <div class="bg-primary/10 rounded-lg py-2">
                                <div class="text-xs text-primary uppercase mb-1 font-bold">Net Cash Flow</div>
                                <div class="text-2xl font-black text-white">$<span x-text="fmt(netFlow)"></span></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow bg-black/20 rounded-2xl p-4 relative min-h-[300px] border border-white/5">
                        <canvas id="cfChart"></canvas>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cashFlowCalculator', () => ({
        monthCount: 12,
        months: [],
        totalIn: 0,
        totalOut: 0,
        netFlow: 0,
        chart: null,

        init() {
            this.updateMonths();
            this.initChart();
        },

        updateMonths() {
            // Adjust array size
            const currentLen = this.months.length;
            if (this.monthCount > currentLen) {
                for(let i = currentLen; i < this.monthCount; i++) {
                    this.months.push({ in: 0, out: 0 });
                }
            } else if (this.monthCount < currentLen) {
                this.months.splice(this.monthCount);
            }
            this.calculate();
        },

        calculate() {
             let tIn = 0;
             let tOut = 0;
             
             this.months.forEach(m => {
                 tIn += (m.in || 0);
                 tOut += (m.out || 0);
             });

             this.totalIn = tIn;
             this.totalOut = tOut;
             this.netFlow = tIn - tOut;

             this.updateChart();
        },

        initChart() {
            const ctx = document.getElementById('cfChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [
                        { label: 'Net Flow', data: [], backgroundColor: [], borderRadius: 4 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                         x: { grid: { display: false }, ticks: { color: '#6B7280' } },
                        y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#6B7280' } }
                    }
                }
            });
        },

        updateChart() {
            if (!this.chart) return;
            
            const labels = [];
            const data = [];
            const colors = [];

            this.months.forEach((m, i) => {
                labels.push(`M${i+1}`);
                const net = (m.in || 0) - (m.out || 0);
                data.push(net);
                colors.push(net >= 0 ? '#10B981' : '#EF4444');
            });

            this.chart.data.labels = labels;
            this.chart.data.datasets[0].data = data;
            this.chart.data.datasets[0].backgroundColor = colors;
            this.chart.update();
        },

        fmt(n) {
            return n ? n.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : '0';
        }
    }));
});
</script>
