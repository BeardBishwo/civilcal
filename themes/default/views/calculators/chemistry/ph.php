<?php
// themes/default/views/calculators/chemistry/ph.php
// PREMIUM pH CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .ph-gradient {
        background: linear-gradient(to right, #ff0000, #ff7f00, #ffff00, #00ff00, #0000ff, #4b0082, #8f00ff);
    }
</style>

<div class="bg-background min-h-screen relative overflow-hidden" x-data="phCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[30%] right-[30%] w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Chemistry</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Solutions</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                pH <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Calculator</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate Acidity or Alkalinity from Hydrogen Ion Concentration.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-3xl mx-auto">
            
            <div class="calc-card animate-scale-in">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Input -->
                    <div class="space-y-6">
                        <div>
                            <label class="calc-label text-center mb-2">H+ Concentration (mol/L)</label>
                            <input type="number" step="any" x-model.number="concentration" @input="calculate()" class="calc-input text-center text-lg" placeholder="0.0000001">
                            <div class="text-center mt-2 flex gap-2 justify-center">
                                <button @click="concentration = 0.1; calculate()" class="text-xs bg-white/5 px-2 py-1 rounded hover:bg-white/10 transition">0.1 (Acid)</button>
                                <button @click="concentration = 0.0000001; calculate()" class="text-xs bg-white/5 px-2 py-1 rounded hover:bg-white/10 transition">1e-7 (Neutral)</button>
                                <button @click="concentration = 0.00000000001; calculate()" class="text-xs bg-white/5 px-2 py-1 rounded hover:bg-white/10 transition">1e-11 (Base)</button>
                            </div>
                        </div>
                    </div>

                    <!-- Result -->
                    <div class="flex flex-col items-center justify-center bg-white/5 rounded-xl border border-white/5 p-6 relative overflow-hidden text-center" :class="borderColor">
                        <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold">pH Level</div>
                         <div class="flex items-baseline gap-2 mb-2 z-10">
                            <span class="text-6xl font-black transition-colors duration-500" :class="textColor" x-text="ph"></span>
                        </div>
                        <div class="text-xl font-bold z-10 transition-colors duration-500" :class="textColor" x-text="type"></div>
                        
                         <!-- Background Glow -->
                         <div class="absolute inset-0 opacity-10 blur-2xl transition-colors duration-500" :class="bgColor"></div>
                    </div>
                </div>

                <!-- Scale Visual -->
                <div class="animate-slide-up mt-8">
                     <div class="relative h-6 w-full ph-gradient rounded-full mb-2 shadow-inner">
                         <!-- Marker -->
                         <div class="absolute top-0 bottom-0 w-2 bg-white border border-black rounded-lg shadow-xl shadow-black transition-all duration-300 transform -translate-x-1/2" :style="`left: ${markerPos}%`"></div>
                     </div>
                     <div class="flex justify-between text-xs font-mono text-gray-400">
                         <span class="text-red-500 font-bold">0 Acidic</span>
                         <span class="text-green-500 font-bold">7 Neutral</span>
                         <span class="text-purple-500 font-bold">14 Basic</span>
                     </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('phCalculator', () => ({
        concentration: 0.0000001,
        ph: 7.00,
        type: 'Neutral',
        markerPos: 50,
        
        // Colors
        textColor: 'text-green-400',
        borderColor: 'border-green-500/30',
        bgColor: 'bg-green-500',

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.concentration || this.concentration <= 0) {
                 this.ph = '---';
                 return;
             }

             const val = -Math.log10(this.concentration);
             this.ph = val.toFixed(2);
             
             // Update visuals
             this.updateVisuals(val);
        },
        
        updateVisuals(ph) {
            // Pos
            let p = (ph / 14) * 100;
            if (p < 0) p = 0; if (p > 100) p = 100;
            this.markerPos = p;
            
            if (ph < 6.9) {
                this.type = 'Acidic';
                this.textColor = 'text-red-400';
                this.borderColor = 'border-red-500/30';
                this.bgColor = 'bg-red-500';
            } else if (ph > 7.1) {
                this.type = 'Basic (Alkaline)';
                this.textColor = 'text-purple-400';
                this.borderColor = 'border-purple-500/30';
                this.bgColor = 'bg-purple-500';
            } else {
                this.type = 'Neutral';
                this.textColor = 'text-green-400';
                this.borderColor = 'border-green-500/30';
                this.bgColor = 'bg-green-500';
            }
        }
    }));
});
</script>
