<?php
// themes/default/views/calculators/nepali.php
// PREMIUM NEPALI UNIT CONVERTER
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="nepaliConverter()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[10%] left-[20%] w-[600px] h-[600px] bg-pink-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
        <div class="absolute bottom-[10%] right-[20%] w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[120px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Others</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-ruler-combined"></i>
                <span>UNIT CONVERTER</span>
            </div>
            <h1 class="calc-title">Nepali <span class="text-gradient">Land Units</span></h1>
            <p class="calc-subtitle">Convert between Ropani, Bigha, Metric, and Imperial units instantly.</p>
        </div>

        <div class="calc-grid max-w-6xl mx-auto">
            
            <!-- Hero Inputs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="calc-card animate-scale-in flex flex-col items-center justify-center p-6 bg-white/5 border-white/10 hover:border-primary/50 transition-colors group cursor-text" @click="$refs.sqm.focus()">
                    <span class="text-xs text-primary font-bold uppercase mb-2">Metric Area</span>
                    <input x-ref="sqm" type="number" x-model="values.sq_meter" @input="update('sq_meter')" class="text-3xl font-black text-white bg-transparent text-center focus:outline-none w-full border-none p-0" placeholder="0">
                    <span class="text-xs text-gray-500 font-mono mt-1">Square Meters</span>
                </div>
                
                <div class="calc-card animate-scale-in flex flex-col items-center justify-center p-6 bg-white/5 border-white/10 hover:border-primary/50 transition-colors group cursor-text" @click="$refs.sqft.focus()">
                    <span class="text-xs text-purple-400 font-bold uppercase mb-2">Imperial Area</span>
                    <input x-ref="sqft" type="number" x-model="values.sq_feet" @input="update('sq_feet')" class="text-3xl font-black text-white bg-transparent text-center focus:outline-none w-full border-none p-0" placeholder="0">
                    <span class="text-xs text-gray-500 font-mono mt-1">Square Feet</span>
                </div>

                <div class="calc-card animate-scale-in flex flex-col items-center justify-center p-6 border-pink-500/20 bg-gradient-to-br from-pink-900/10 to-transparent hover:border-pink-500/50 transition-colors">
                    <span class="text-xs text-pink-400 font-bold uppercase mb-2">Cross System</span>
                    <div class="text-3xl font-black text-white" x-text="values.ropani || '0'"></div>
                    <span class="text-xs text-gray-500 font-mono mt-1">Ropani Equivalent</span>
                </div>
            </div>

            <!-- Conversion Grids -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Hilly Region -->
                <div class="space-y-4 animate-slide-up" style="animation-delay: 0.1s">
                    <div class="flex items-center gap-2 mb-2">
                         <div class="h-px bg-white/10 flex-1"></div>
                         <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Hilly Region (Ropani)</span>
                         <div class="h-px bg-white/10 flex-1"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-pink-400 font-bold uppercase bg-pink-500/10 px-2 py-0.5 rounded">Ropani</span>
                             </div>
                             <input type="number" x-model="values.ropani" @input="update('ropani')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-pink-400 font-bold uppercase bg-pink-500/10 px-2 py-0.5 rounded">Aana</span>
                             </div>
                             <input type="number" x-model="values.aana" @input="update('aana')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-pink-400 font-bold uppercase bg-pink-500/10 px-2 py-0.5 rounded">Paisa</span>
                             </div>
                             <input type="number" x-model="values.paisa" @input="update('paisa')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-pink-400 font-bold uppercase bg-pink-500/10 px-2 py-0.5 rounded">Daam</span>
                             </div>
                             <input type="number" x-model="values.daam" @input="update('daam')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                    </div>
                </div>

                <!-- Terai Region -->
                <div class="space-y-4 animate-slide-up" style="animation-delay: 0.2s">
                    <div class="flex items-center gap-2 mb-2">
                         <div class="h-px bg-white/10 flex-1"></div>
                         <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Terai Region (Bigha)</span>
                         <div class="h-px bg-white/10 flex-1"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors col-span-2">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-green-400 font-bold uppercase bg-green-500/10 px-2 py-0.5 rounded">Bigha</span>
                             </div>
                             <input type="number" x-model="values.bigha" @input="update('bigha')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-green-400 font-bold uppercase bg-green-500/10 px-2 py-0.5 rounded">Kattha</span>
                             </div>
                             <input type="number" x-model="values.kattha" @input="update('kattha')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-green-400 font-bold uppercase bg-green-500/10 px-2 py-0.5 rounded">Dhur</span>
                             </div>
                             <input type="number" x-model="values.dhur" @input="update('dhur')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                    </div>
                </div>

                 <!-- Metric -->
                <div class="space-y-4 animate-slide-up" style="animation-delay: 0.3s">
                    <div class="flex items-center gap-2 mb-2">
                         <div class="h-px bg-white/10 flex-1"></div>
                         <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Metric Standards</span>
                         <div class="h-px bg-white/10 flex-1"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-blue-400 font-bold uppercase bg-blue-500/10 px-2 py-0.5 rounded">Sq. Meters</span>
                             </div>
                             <input type="number" x-model="values.sq_meter" @input="update('sq_meter')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-blue-400 font-bold uppercase bg-blue-500/10 px-2 py-0.5 rounded">Hectares</span>
                             </div>
                             <input type="number" x-model="values.hectare" @input="update('hectare')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                    </div>
                </div>

                 <!-- Imperial -->
                <div class="space-y-4 animate-slide-up" style="animation-delay: 0.4s">
                    <div class="flex items-center gap-2 mb-2">
                         <div class="h-px bg-white/10 flex-1"></div>
                         <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Imperial Standards</span>
                         <div class="h-px bg-white/10 flex-1"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-yellow-400 font-bold uppercase bg-yellow-500/10 px-2 py-0.5 rounded">Sq. Feet</span>
                             </div>
                             <input type="number" x-model="values.sq_feet" @input="update('sq_feet')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                         <div class="glass-input-wrapper p-3 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                             <div class="flex justify-between items-center mb-1">
                                 <span class="text-[10px] text-yellow-400 font-bold uppercase bg-yellow-500/10 px-2 py-0.5 rounded">Acres</span>
                             </div>
                             <input type="number" x-model="values.acre" @input="update('acre')" class="w-full bg-transparent text-white font-mono font-bold text-xl focus:outline-none" placeholder="0">
                         </div>
                    </div>
                </div>

            </div>
            
            <div class="mt-8 flex justify-center">
                <button @click="clear()" class="px-6 py-2 rounded-full border border-red-500/30 text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all text-sm font-bold uppercase tracking-wider">
                    Clear All
                </button>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('nepaliConverter', () => ({
        // Conversion factors to Square Feet
        factors: {
            ropani: 5476,
            aana: 342.25,
            paisa: 85.5625,
            daam: 21.390625,
            bigha: 72900,
            kattha: 3645,
            dhur: 182.25,
            sq_meter: 10.7639104,
            sq_mm: 0.000107639104, // Not used in UI but kept for completeness if needed
            hectare: 107639.104,
            sq_feet: 1,
            sq_in: 0.00694444, // Not used in UI
            acre: 43560
        },

        values: {
            ropani: '', aana: '', paisa: '', daam: '',
            bigha: '', kattha: '', dhur: '',
            sq_meter: '', hectare: '',
            sq_feet: '', acre: ''
        },

        init() {
            // Optional: Set default or listen for events
        },

        update(source) {
            const val = parseFloat(this.values[source]);
            
            if (isNaN(val)) {
                // If input is cleared/invalid, optionally clear all? 
                // Currently just doing nothing or handling as zero might be annoying if typing.
                // Let's check if it's empty string.
                if (this.values[source] === '') {
                     this.clear();
                }
                return;
            }

            // Convert to base unit (Sq Feet)
            const sqFt = val * this.factors[source];

            // Convert Base to all others
            Object.keys(this.values).forEach(key => {
                if (key !== source) {
                    const converted = sqFt / this.factors[key];
                    // Format to reasonable decimals, e.g. 4
                    // Avoid scientific notation if possible for normal range
                    this.values[key] = parseFloat(converted.toFixed(6));
                }
            });
        },

        clear() {
            Object.keys(this.values).forEach(key => this.values[key] = '');
        }
    }));
});
</script>
