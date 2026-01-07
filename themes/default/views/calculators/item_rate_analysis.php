<?php
// themes/default/views/calculators/item_rate_analysis.php
// PREMIUM ITEM RATE ANALYSIS (Integrated with API)
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="itemRateAnalysis()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[30%] left-[50%] -translate-x-1/2 w-[800px] h-[600px] bg-indigo-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
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
                <i class="fas fa-cube"></i>
                <span>PROJECT ESTIMATION</span>
            </div>
            <h1 class="calc-title">Item Rate <span class="text-gradient">Analysis</span></h1>
            <p class="calc-subtitle">Detailed unit rate analysis using DUDBC norms for Materials and Labor.</p>
        </div>

        <div class="calc-grid max-w-6xl mx-auto">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Setup Panel -->
                <div class="lg:col-span-5 space-y-6 animate-scale-in">
                    
                    <div class="calc-card">
                        <h3 class="text-lg font-bold text-white mb-4">Selection</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="calc-label">Work Category</label>
                                <select x-model="selectedCategory" @change="updateItems()" class="calc-input">
                                    <option value="">-- Select Category --</option>
                                    <?php foreach ($norms as $key => $cat): ?>
                                        <option value="<?php echo $key; ?>"><?php echo ucfirst($key); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="calc-label">Specific Item</label>
                                <select x-model="selectedItem" @change="loadNormDetails()" class="calc-input" :disabled="!selectedCategory">
                                    <option value="">-- Select Item --</option>
                                    <template x-for="(item, key) in availableItems" :key="key">
                                        <option :value="key" x-text="item.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Inputs -->
                    <div x-show="currentNorm" x-transition class="space-y-6">
                        
                        <!-- Materials -->
                        <div x-show="Object.keys(currentNorm?.materials || {}).length > 0" class="calc-card">
                             <h3 class="text-sm font-bold text-blue-400 uppercase tracking-widest mb-4"><i class="fas fa-cubes mr-2"></i>Material Rates</h3>
                             <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                 <template x-for="(coeff, mat) in currentNorm.materials" :key="mat">
                                     <div class="grid grid-cols-2 gap-2 items-center">
                                         <div>
                                             <div class="text-xs text-gray-400 uppercase" x-text="mat.replace(/_/g, ' ')"></div>
                                             <div class="text-[10px] text-gray-600">Coeff: <span x-text="coeff"></span></div>
                                         </div>
                                         <input type="number" x-model="inputRates.materials[mat]" class="calc-input text-right py-1 text-sm" placeholder="Rate">
                                     </div>
                                 </template>
                             </div>
                        </div>

                         <!-- Labor -->
                        <div x-show="Object.keys(currentNorm?.labor || {}).length > 0" class="calc-card">
                             <h3 class="text-sm font-bold text-green-400 uppercase tracking-widest mb-4"><i class="fas fa-users mr-2"></i>Labor Rates</h3>
                             <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                 <template x-for="(coeff, lab) in currentNorm.labor" :key="lab">
                                     <div class="grid grid-cols-2 gap-2 items-center">
                                         <div>
                                             <div class="text-xs text-gray-400 uppercase" x-text="lab.replace(/_/g, ' ')"></div>
                                             <div class="text-[10px] text-gray-600">Coeff: <span x-text="coeff"></span></div>
                                         </div>
                                         <input type="number" x-model="inputRates.labor[lab]" class="calc-input text-right py-1 text-sm" placeholder="Wage">
                                     </div>
                                 </template>
                             </div>
                        </div>

                        <!-- Overhead -->
                        <div class="calc-card">
                            <label class="calc-label">Overhead & Profit % / Amount</label>
                            <input type="number" x-model="overhead" class="calc-input" placeholder="e.g. 15% or fixed amount">
                            <p class="text-[10px] text-gray-500 mt-1">Currently treated as fixed amount add-on in calculation.</p>
                        </div>
                        
                        <button @click="calculate()" class="calc-btn w-full" :disabled="loading">
                             <span x-show="!loading">Compute Rate Analysis</span>
                             <span x-show="loading"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                        </button>

                    </div>

                </div>

                <!-- Result Panel -->
                <div class="lg:col-span-7 animate-slide-up">
                    
                    <!-- Placeholder -->
                    <div x-show="!results" class="h-full flex flex-col items-center justify-center text-gray-600 p-12 border border-white/5 rounded-2xl bg-white/5">
                        <i class="fas fa-calculator text-4xl mb-4 opacity-50"></i>
                        <p>Select an item and enter rates to view analysis.</p>
                    </div>

                    <!-- Actual Results -->
                    <div x-show="results" class="space-y-6">
                        
                        <!-- Top Card -->
                        <div class="calc-card bg-gradient-to-r from-indigo-900/40 to-black border-indigo-500/30">
                            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                                <div>
                                    <div class="text-sm text-gray-400 uppercase font-bold">Total Unit Rate</div>
                                    <div class="text-4xl font-black text-white" x-text="'Rs. ' + results?.total_rate.toFixed(2)"></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">Material Cost: <span class="text-blue-400 font-bold" x-text="results?.material_cost.toFixed(2)"></span></div>
                                    <div class="text-xs text-gray-500">Labor Cost: <span class="text-green-400 font-bold" x-text="results?.labor_cost.toFixed(2)"></span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Breakdown Tables -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Materials -->
                             <div class="bg-white/5 rounded-xl border border-white/10 overflow-hidden">
                                 <div class="bg-white/5 px-4 py-2 text-xs font-bold text-blue-400 uppercase">Materials Breakdown</div>
                                 <div class="p-2 overflow-x-auto">
                                     <table class="w-full text-left text-xs text-gray-300">
                                         <thead>
                                             <tr class="border-b border-white/5 text-gray-500">
                                                 <th class="py-2 px-2">Item</th>
                                                 <th class="py-2 px-2 text-right">Qty</th>
                                                 <th class="py-2 px-2 text-right">Rate</th>
                                                 <th class="py-2 px-2 text-right">Cost</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             <template x-for="m in results?.material_breakdown || []">
                                                 <tr class="border-b border-white/5 last:border-0 hover:bg-white/5">
                                                     <td class="py-2 px-2" x-text="m.name"></td>
                                                     <td class="py-2 px-2 text-right font-mono" x-text="m.coefficient"></td>
                                                     <td class="py-2 px-2 text-right font-mono" x-text="m.rate.toFixed(2)"></td>
                                                     <td class="py-2 px-2 text-right font-bold text-white font-mono" x-text="m.cost.toFixed(2)"></td>
                                                 </tr>
                                             </template>
                                         </tbody>
                                     </table>
                                 </div>
                             </div>

                             <!-- Labor -->
                             <div class="bg-white/5 rounded-xl border border-white/10 overflow-hidden">
                                 <div class="bg-white/5 px-4 py-2 text-xs font-bold text-green-400 uppercase">Labor Breakdown</div>
                                 <div class="p-2 overflow-x-auto">
                                     <table class="w-full text-left text-xs text-gray-300">
                                         <thead>
                                             <tr class="border-b border-white/5 text-gray-500">
                                                 <th class="py-2 px-2">Type</th>
                                                 <th class="py-2 px-2 text-right">Coeff</th>
                                                 <th class="py-2 px-2 text-right">Wage</th>
                                                 <th class="py-2 px-2 text-right">Cost</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             <template x-for="l in results?.labor_breakdown || []">
                                                 <tr class="border-b border-white/5 last:border-0 hover:bg-white/5">
                                                     <td class="py-2 px-2" x-text="l.name"></td>
                                                     <td class="py-2 px-2 text-right font-mono" x-text="l.coefficient"></td>
                                                     <td class="py-2 px-2 text-right font-mono" x-text="l.rate.toFixed(2)"></td>
                                                     <td class="py-2 px-2 text-right font-bold text-white font-mono" x-text="l.cost.toFixed(2)"></td>
                                                 </tr>
                                             </template>
                                         </tbody>
                                     </table>
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
const NORMS_DATA = <?php echo json_encode($norms); ?>;
const APP_URL = "<?php echo rtrim(app_base_url(), '/'); ?>";

document.addEventListener('alpine:init', () => {
    Alpine.data('itemRateAnalysis', () => ({
        norms: NORMS_DATA,
        selectedCategory: '',
        selectedItem: '',
        availableItems: {},
        currentNorm: null,
        
        inputRates: {
            materials: {},
            labor: {}
        },
        overhead: 0,
        
        loading: false,
        results: null,

        updateItems() {
            if (this.selectedCategory && this.norms[this.selectedCategory]) {
                this.availableItems = this.norms[this.selectedCategory];
                this.selectedItem = '';
                this.currentNorm = null;
                this.results = null;
            } else {
                this.availableItems = {};
            }
        },

        loadNormDetails() {
             if (this.selectedCategory && this.selectedItem) {
                 this.currentNorm = this.norms[this.selectedCategory][this.selectedItem];
                 
                 // Reset inputs
                 this.inputRates = { materials: {}, labor: {} };
                 
                 // Initialize inputs with 0 or stored defaults if any
                 if (this.currentNorm.materials) {
                     Object.keys(this.currentNorm.materials).forEach(k => {
                         this.inputRates.materials[k] = '';
                     });
                 }
                 if (this.currentNorm.labor) {
                     Object.keys(this.currentNorm.labor).forEach(k => {
                         this.inputRates.labor[k] = '';
                     });
                 }
                 
                 this.results = null;
             }
        },

        async calculate() {
            this.loading = true;
            try {
                // Prepare numeric payload
                const matRates = {};
                Object.keys(this.inputRates.materials).forEach(k => {
                    matRates[k] = parseFloat(this.inputRates.materials[k]) || 0;
                });
                
                const labRates = {};
                Object.keys(this.inputRates.labor).forEach(k => {
                    labRates[k] = parseFloat(this.inputRates.labor[k]) || 0;
                });

                const payload = {
                    norm_key: `${this.selectedCategory}.${this.selectedItem}`,
                    material_rates: matRates,
                    labor_rates: labRates,
                    overhead: parseFloat(this.overhead) || 0
                };

                const res = await fetch(`${APP_URL}/rate-analysis/calculate`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const data = await res.json();
                
                if (data.success) {
                    this.results = data;
                } else {
                    alert('Calculation failed: ' + (data.message || 'Unknown error'));
                }
            } catch (e) {
                console.error(e);
                alert('API Error');
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
