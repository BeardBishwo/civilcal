<?php
// themes/default/views/calculators/physics/energy.php
// PREMIUM ENERGY CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="energyCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[10%] left-[30%] w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Physics</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-atom"></i>
                <span>MECHANICS</span>
            </div>
            <h1 class="calc-title">Energy <span class="text-gradient">Calculator</span></h1>
            <p class="calc-subtitle">Calculate Kinetic Energy (motion) and Potential Energy (position).</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <!-- Type Toggle -->
             <div class="flex justify-center mb-10 animate-scale-in">
                <div class="bg-white/5 p-1 rounded-full border border-white/10 inline-flex">
                    <button @click="type = 'ke'; calculate()" :class="type === 'ke' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="px-6 py-2 rounded-full transition-all font-bold flex items-center gap-2">
                        <i class="fas fa-running"></i> Kinetic
                    </button>
                    <button @click="type = 'pe'; calculate()" :class="type === 'pe' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="px-6 py-2 rounded-full transition-all font-bold flex items-center gap-2">
                        <i class="fas fa-arrow-up"></i> Potential
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Inputs -->
                <div class="calc-card animate-slide-right min-h-[300px] flex flex-col justify-center">
                    
                    <div x-show="type === 'ke'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4">
                        <h3 class="text-lg font-bold text-white mb-2">Kinetic Energy</h3>
                        <p class="text-xs text-gray-400 mb-6">Energy possessed by an object due to its motion.</p>
                        
                        <div>
                            <label class="calc-label">Mass (kg)</label>
                            <input type="number" x-model.number="ke.m" @input="calculate()" class="calc-input" placeholder="10">
                        </div>
                        <div>
                            <label class="calc-label">Velocity (m/s)</label>
                            <input type="number" x-model.number="ke.v" @input="calculate()" class="calc-input" placeholder="20">
                        </div>
                    </div>

                    <div x-show="type === 'pe'" class="space-y-6 hidden" :class="{ 'hidden': type !== 'pe' }" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4">
                         <h3 class="text-lg font-bold text-white mb-2">Potential Energy</h3>
                        <p class="text-xs text-gray-400 mb-6">Energy held by an object because of its vertical position.</p>

                        <div>
                            <label class="calc-label">Mass (kg)</label>
                            <input type="number" x-model.number="pe.m" @input="calculate()" class="calc-input" placeholder="10">
                        </div>
                        <div>
                            <label class="calc-label">Height (m)</label>
                            <input type="number" x-model.number="pe.h" @input="calculate()" class="calc-input" placeholder="50">
                        </div>
                         <div class="text-right text-xs text-gray-500 italic">g = 9.8 m/s²</div>
                    </div>

                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-left flex flex-col justify-center items-center text-center bg-gradient-to-br from-yellow-900/20 to-black border border-yellow-500/20">
                    
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold" x-text="type === 'ke' ? 'Kinetic Energy' : 'Potential Energy'"></div>
                    
                    <div class="flex items-baseline gap-2 mb-2">
                        <span class="text-6xl font-black text-white" x-text="result"></span>
                        <span class="text-2xl font-bold text-yellow-400">J</span>
                    </div>
                    
                    <div class="mt-8 p-4 bg-white/5 rounded-xl border border-white/5 w-full text-left">
                        <div class="flex justify-between text-xs text-gray-400 font-mono mb-2">
                            <span>Equation</span>
                            <span class="text-white" x-text="type === 'ke' ? 'KE = 0.5 × m × v²' : 'PE = m × g × h'"></span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('energyCalculator', () => ({
        type: 'ke',
        ke: { m: 10, v: 20 },
        pe: { m: 10, h: 50 },
        
        result: 0,

        init() {
            this.calculate();
        },

        calculate() {
             let res = 0;
             if (this.type === 'ke') {
                 if (this.ke.m && this.ke.v) {
                     res = 0.5 * this.ke.m * this.ke.v * this.ke.v;
                 }
             } else {
                 if (this.pe.m && this.pe.h) {
                     res = this.pe.m * 9.8 * this.pe.h;
                 }
             }

             this.result = Number.isInteger(res) ? res : res.toFixed(2);
        }
    }));
});
</script>
