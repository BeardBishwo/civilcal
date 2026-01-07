<?php
// themes/default/views/calculators/equipment_hourly_rate.php
// PREMIUM EQUIPMENT HOURLY RATE CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="equipmentCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] right-[10%] w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[120px] animate-float"></div>
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
                <i class="fas fa-truck-moving"></i>
                <span>COST ANALYSIS</span>
            </div>
            <h1 class="calc-title">Equipment <span class="text-gradient">Hourly Rate</span></h1>
            <p class="calc-subtitle">Calculate owning and operating costs for heavy machinery.</p>
        </div>

        <div class="calc-grid max-w-5xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Input Panel -->
                <div class="space-y-6 animate-scale-in">
                    
                    <div class="calc-card">
                        <h3 class="text-lg font-bold text-white mb-4 border-b border-white/10 pb-2">Equipment Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="calc-label">Equipment Type</label>
                                <select x-model="type" class="calc-input">
                                    <option value="excavator">Excavator</option>
                                    <option value="loader">Loader</option>
                                    <option value="dozer">Dozer</option>
                                    <option value="mixer">Concrete Mixer</option>
                                    <option value="roller">Roller</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="calc-label">Purchase Price (Rs.)</label>
                                <input type="number" x-model.number="purchasePrice" @input="calculate()" class="calc-input" placeholder="e.g. 5000000">
                            </div>
                        </div>
                    </div>

                    <div class="calc-card">
                         <h3 class="text-lg font-bold text-white mb-4 border-b border-white/10 pb-2">Depreciation Factors</h3>
                         <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="calc-label">Economic Life (Yrs)</label>
                                <input type="number" x-model.number="lifeYears" @input="calculate()" class="calc-input" placeholder="10">
                            </div>
                            <div>
                                <label class="calc-label">Salvage Value (Rs.)</label>
                                <input type="number" x-model.number="salvageValue" @input="calculate()" class="calc-input" placeholder="500000">
                            </div>
                            <div class="col-span-2">
                                <label class="calc-label">Annual Working Hours</label>
                                <input type="number" x-model.number="annualHours" @input="calculate()" class="calc-input" placeholder="2000">
                            </div>
                         </div>
                    </div>

                    <div class="calc-card">
                         <h3 class="text-lg font-bold text-white mb-4 border-b border-white/10 pb-2">Operating Costs (Hourly)</h3>
                         <div class="grid grid-cols-3 gap-4">
                             <div>
                                <label class="calc-label">Fuel</label>
                                <input type="number" x-model.number="fuelCost" @input="calculate()" class="calc-input" placeholder="500">
                            </div>
                            <div>
                                <label class="calc-label">Maint.</label>
                                <input type="number" x-model.number="maintCost" @input="calculate()" class="calc-input" placeholder="200">
                            </div>
                            <div>
                                <label class="calc-label">Operator</label>
                                <input type="number" x-model.number="operatorWage" @input="calculate()" class="calc-input" placeholder="300">
                            </div>
                         </div>
                    </div>

                </div>

                <!-- Result Panel -->
                <div class="calc-card animate-slide-up bg-gradient-to-br from-yellow-900/20 to-black border border-yellow-500/20 h-fit sticky top-4">
                    
                    <div class="text-center mb-6">
                        <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold">Total Hourly Rate</div>
                        <div class="text-5xl font-black text-white mb-2">
                            <span class="text-2xl align-top opacity-50 mr-1">Rs.</span>
                            <span x-text="totalRate.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                        <div class="text-xs text-yellow-500/80 font-mono mt-2">Per Working Hour</div>
                    </div>

                    <div class="space-y-4">
                        <!-- Depreciation Breakdown -->
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-400">Depreciation Cost</span>
                                <span class="text-lg font-bold text-white" x-text="'Rs. ' + depCost.toFixed(2)"></span>
                            </div>
                            <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full rounded-full" :style="'width: ' + (depPercent) + '%'"></div>
                            </div>
                            <div class="text-[10px] text-right text-gray-500 mt-1" x-text="depPercent.toFixed(1) + '% of Total'"></div>
                        </div>

                        <!-- Operating Breakdown -->
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-400">Operating Cost</span>
                                <span class="text-lg font-bold text-white" x-text="'Rs. ' + operatingCost.toFixed(2)"></span>
                            </div>
                            <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-green-500 h-full rounded-full" :style="'width: ' + (opPercent) + '%'"></div>
                            </div>
                             <div class="text-[10px] text-right text-gray-500 mt-1" x-text="opPercent.toFixed(1) + '% of Total'"></div>
                             
                             <!-- Detailed Op Breakdown -->
                             <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs text-gray-500">
                                 <div>
                                     <div class="text-white font-bold" x-text="fuelCost"></div>
                                     <div>Fuel</div>
                                 </div>
                                 <div>
                                     <div class="text-white font-bold" x-text="maintCost"></div>
                                     <div>Maint</div>
                                 </div>
                                 <div>
                                     <div class="text-white font-bold" x-text="operatorWage"></div>
                                     <div>Wage</div>
                                 </div>
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
    Alpine.data('equipmentCalculator', () => ({
        type: 'excavator',
        purchasePrice: 5000000,
        lifeYears: 10,
        salvageValue: 500000,
        annualHours: 2000,
        fuelCost: 500,
        maintCost: 200,
        operatorWage: 300,
        
        totalRate: 0,
        depCost: 0,
        operatingCost: 0,
        depPercent: 0,
        opPercent: 0,

        init() {
            this.calculate();
        },

        calculate() {
             // Depreciation Cost per Hour
             // (Purchase - Salvage) / (Life * Hours/Year)
             const totalDep = this.purchasePrice - this.salvageValue;
             const totalHours = this.lifeYears * this.annualHours;
             
             this.depCost = (totalHours > 0) ? (totalDep / totalHours) : 0;
             if (this.depCost < 0) this.depCost = 0;

             // Operating Cost
             this.operatingCost = this.fuelCost + this.maintCost + this.operatorWage;
             
             // Total
             this.totalRate = this.depCost + this.operatingCost;
             
             // Percentages
             if (this.totalRate > 0) {
                 this.depPercent = (this.depCost / this.totalRate) * 100;
                 this.opPercent = (this.operatingCost / this.totalRate) * 100;
             } else {
                 this.depPercent = 0;
                 this.opPercent = 0;
             }
        }
    }));
});
</script>
