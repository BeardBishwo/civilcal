<?php
// themes/default/views/calculators/labor_rate_analysis.php
// PREMIUM LABOR RATE CALCULATOR (Estimation Suite)
?>

<!-- CDN Utilities -->
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    dark: '#050505',
                    surface: '#0a0a0a',
                    glass: 'rgba(255, 255, 255, 0.03)',
                    'glass-border': 'rgba(255, 255, 255, 0.08)',
                    'est-green': '#10b981', // Emerald-500
                }
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    
    .calc-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        color: white;
        outline: none;
        transition: all 0.2s;
    }
    
    .calc-input:focus {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.05);
    }

    .calc-label {
        display: block;
        font-size: 0.875rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .text-gradient {
        background: linear-gradient(to right, #ffffff, #10b981);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<div class="bg-dark min-h-screen relative overflow-hidden text-white" x-data="laborCalculator()">
    
    <!-- Hero Glow -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-[400px] bg-[radial-gradient(circle_at_center,rgba(16,185,129,0.1)_0%,transparent_70%)]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
        
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/estimation') ?>" class="hover:text-white transition">Estimation</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-est-green font-bold">Labor Rate</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-est-green/10 border border-est-green/20 text-est-green text-xs font-bold tracking-widest uppercase mb-6">
                <i class="fas fa-users-cog"></i>
                <span>LABOR ESTIMATION</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black mb-4">Labor Rate <span class="text-gradient">Analysis</span></h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">Calculate unit labor costs based on crew composition and productivity.</p>
        </div>

        <!-- Calculator Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Panel: Inputs (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Task Productivity Card -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Task Productivity</h3>
                        <i class="fas fa-chart-line text-est-green"></i>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                <!-- Crew Composition Card -->
                <div class="glass-card p-6 rounded-2xl">
                     <h3 class="text-lg font-bold mb-6">Crew Composition</h3>
                     
                     <!-- Headers -->
                     <div class="grid grid-cols-12 gap-4 mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">
                         <div class="col-span-4">Role</div>
                         <div class="col-span-3 text-center">Qty</div>
                         <div class="col-span-5 text-right">Daily Rate</div>
                     </div>

                     <!-- Mason -->
                     <div class="grid grid-cols-12 gap-4 mb-4 items-center">
                         <div class="col-span-4 text-sm font-medium">Mason</div>
                         <div class="col-span-3">
                             <input type="number" x-model.number="mason.count" @input="calculate()" class="calc-input text-center h-10" placeholder="Qty">
                         </div>
                         <div class="col-span-5">
                             <input type="number" x-model.number="mason.rate" @input="calculate()" class="calc-input text-right h-10" placeholder="Rate">
                         </div>
                     </div>

                     <!-- Helper -->
                     <div class="grid grid-cols-12 gap-4 mb-4 items-center">
                         <div class="col-span-4 text-sm font-medium">Helper</div>
                         <div class="col-span-3">
                             <input type="number" x-model.number="helper.count" @input="calculate()" class="calc-input text-center h-10" placeholder="Qty">
                         </div>
                         <div class="col-span-5">
                             <input type="number" x-model.number="helper.rate" @input="calculate()" class="calc-input text-right h-10" placeholder="Rate">
                         </div>
                     </div>

                     <!-- Laborer -->
                     <div class="grid grid-cols-12 gap-4 mb-4 items-center">
                         <div class="col-span-4 text-sm font-medium">Laborer</div>
                         <div class="col-span-3">
                             <input type="number" x-model.number="laborer.count" @input="calculate()" class="calc-input text-center h-10" placeholder="Qty">
                         </div>
                         <div class="col-span-5">
                             <input type="number" x-model.number="laborer.rate" @input="calculate()" class="calc-input text-right h-10" placeholder="Rate">
                         </div>
                     </div>
                     
                     <!-- Summary Row -->
                      <div class="grid grid-cols-12 gap-4 mt-6 pt-6 border-t border-white/10 items-center">
                         <div class="col-span-7 text-sm font-bold text-gray-400 uppercase">Total Daily Crew Cost</div>
                         <div class="col-span-5 text-right font-black text-2xl text-white" x-text="'Rs. ' + totalCrewCost.toLocaleString()"></div>
                     </div>

                </div>

            </div>

             <!-- Right Panel: Results (4 Cols) -->
            <div class="lg:col-span-4">
                <div class="glass-card p-8 rounded-2xl bg-gradient-to-br from-est-green/10 to-transparent border border-est-green/20 sticky top-4">
                    
                     <div class="text-center mb-8">
                        <div class="text-xs text-est-green/80 font-bold uppercase tracking-widest mb-4">Unit Labor Cost</div>
                        <div class="text-5xl font-black text-white mb-2 tracking-tight">
                            <span class="text-2xl align-top opacity-50 mr-1">Rs.</span>
                            <span x-text="unitRate.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                        <div class="text-xs text-gray-500 font-mono mt-2">Per Unit Output</div>
                    </div>

                    <div class="bg-black/20 p-4 rounded-xl border border-white/5 space-y-3 mb-8">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Total Crew Size</span>
                            <span class="text-white font-bold" x-text="mason.count + helper.count + laborer.count + ' Persons'"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Daily Output</span>
                            <span class="text-white font-bold" x-text="productivity + ' Units'"></span>
                        </div>
                         <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Man-Hours/Unit</span>
                            <span class="text-white font-bold" x-text="productivity > 0 ? ((mason.count + helper.count + laborer.count) * 8 / productivity).toFixed(2) + ' Hrs' : '0 Hrs'"></span>
                        </div>
                    </div>

                    <button class="w-full py-4 bg-est-green hover:bg-emerald-400 text-black font-black rounded-xl transition-all shadow-lg shadow-est-green/20 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> 
                        <span>SAVE ANALYSIS</span>
                    </button>

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
