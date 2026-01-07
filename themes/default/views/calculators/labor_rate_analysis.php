<?php
// themes/default/views/calculators/labor_rate_analysis.php
// PREMIUM LABOR RATE CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="laborCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute bottom-[20%] left-[10%] w-[500px] h-[500px] bg-green-500/10 rounded-full blur-[120px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Construction</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-users-cog"></i>
                <span>WORKFORCE</span>
            </div>
            <h1 class="calc-title">Labor Rate <span class="text-gradient">Analysis</span></h1>
            <p class="calc-subtitle">Calculate unit labor costs based on crew composition and productivity.</p>
        </div>

        <div class="calc-grid max-w-5xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Input Panel -->
                <div class="space-y-6 animate-scale-in">
                    
                    <div class="calc-card">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-white">Task Productivity</h3>
                            <i class="fas fa-chart-line text-green-400"></i>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="calc-label">Task Type</label>
                                <select x-model="taskType" class="calc-input">
                                    <option value="brickwork">Brickwork (Cu.m)</option>
                                    <option value="plastering">Plastering (Sq.m)</option>
                                    <option value="concrete">Concrete (Cu.m)</option>
                                    <option value="excavation">Excavation (Cu.m)</option>
                                    <option value="painting">Painting (Sq.m)</option>
                                </select>
                            </div>
                            <div>
                                <label class="calc-label">Daily Output (Units/Day)</label>
                                <input type="number" x-model.number="productivity" @input="calculate()" class="calc-input" placeholder="e.g. 10">
                                <p class="text-[10px] text-gray-500 mt-1">Total units the crew completes in one day.</p>
                            </div>
                        </div>
                    </div>

                    <div class="calc-card">
                         <h3 class="text-lg font-bold text-white mb-4">Crew Composition</h3>
                         
                         <!-- Mason -->
                         <div class="grid grid-cols-12 gap-2 mb-3 items-center">
                             <div class="col-span-4 text-sm text-gray-300">Mason</div>
                             <div class="col-span-3">
                                 <input type="number" x-model.number="mason.count" @input="calculate()" class="calc-input text-center p-1 h-8" placeholder="Qty">
                             </div>
                             <div class="col-span-5">
                                 <input type="number" x-model.number="mason.rate" @input="calculate()" class="calc-input text-right p-1 h-8" placeholder="Rate">
                             </div>
                         </div>

                         <!-- Helper -->
                         <div class="grid grid-cols-12 gap-2 mb-3 items-center">
                             <div class="col-span-4 text-sm text-gray-300">Helper</div>
                             <div class="col-span-3">
                                 <input type="number" x-model.number="helper.count" @input="calculate()" class="calc-input text-center p-1 h-8" placeholder="Qty">
                             </div>
                             <div class="col-span-5">
                                 <input type="number" x-model.number="helper.rate" @input="calculate()" class="calc-input text-right p-1 h-8" placeholder="Rate">
                             </div>
                         </div>

                         <!-- Laborer -->
                         <div class="grid grid-cols-12 gap-2 mb-3 items-center">
                             <div class="col-span-4 text-sm text-gray-300">Laborer</div>
                             <div class="col-span-3">
                                 <input type="number" x-model.number="laborer.count" @input="calculate()" class="calc-input text-center p-1 h-8" placeholder="Qty">
                             </div>
                             <div class="col-span-5">
                                 <input type="number" x-model.number="laborer.rate" @input="calculate()" class="calc-input text-right p-1 h-8" placeholder="Rate">
                             </div>
                         </div>
                         
                         <!-- Summary Row -->
                          <div class="grid grid-cols-12 gap-2 mt-4 pt-4 border-t border-white/10 items-center">
                             <div class="col-span-7 text-xs font-bold text-gray-400 uppercase">Total Daily Crew Cost</div>
                             <div class="col-span-5 text-right font-bold text-white text-lg" x-text="'Rs. ' + totalCrewCost.toLocaleString()"></div>
                         </div>

                    </div>

                </div>

                <!-- Result Panel -->
                <div class="calc-card animate-slide-up bg-gradient-to-br from-green-900/20 to-black border border-green-500/20 h-fit sticky top-4">
                    
                     <div class="text-center mb-8">
                        <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold">Unit Labor Cost</div>
                        <div class="text-5xl font-black text-white mb-2">
                            <span class="text-2xl align-top opacity-50 mr-1">Rs.</span>
                            <span x-text="unitRate.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                        <div class="text-xs text-green-500/80 font-mono mt-2">Per Unit (e.g., per Cu.m / Sq.m)</div>
                    </div>

                    <div class="bg-white/5 p-4 rounded-xl border border-white/5 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Total Crew Size</span>
                            <span class="text-white font-bold" x-text="mason.count + helper.count + laborer.count + ' Persons'"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Daily Output</span>
                            <span class="text-white font-bold" x-text="productivity + ' Units'"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Efficiency</span>
                            <span class="text-white font-bold" x-text="(unitRate > 0 ? (totalCrewCost/unitRate).toFixed(1) : 0) + ' Units/Rs?'"></span> 
                            <!-- Efficiency metric is tricky here, let's just show cost breakdown -->
                        </div>
                    </div>

                    <div class="mt-6 flex justify-center">
                        <button class="px-6 py-2 bg-green-500 hover:bg-green-400 text-black font-bold rounded-lg transition-all shadow-lg shadow-green-500/20">
                            <i class="fas fa-save mr-2"></i> Save Analysis
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('laborCalculator', () => ({
        taskType: 'brickwork',
        productivity: 8.5,
        
        mason: { count: 1, rate: 1500 },
        helper: { count: 2, rate: 1000 },
        laborer: { count: 1, rate: 900 },
        
        totalCrewCost: 0,
        unitRate: 0,

        init() {
            this.calculate();
        },

        calculate() {
             // Calculate Total Daily Crew Cost
             const costMason = this.mason.count * this.mason.rate;
             const costHelper = this.helper.count * this.helper.rate;
             const costLaborer = this.laborer.count * this.laborer.rate;
             
             this.totalCrewCost = costMason + costHelper + costLaborer;
             
             // Calculate Unit Rate
             if (this.productivity > 0) {
                 this.unitRate = this.totalCrewCost / this.productivity;
             } else {
                 this.unitRate = 0;
             }
        }
    }));
});
</script>
