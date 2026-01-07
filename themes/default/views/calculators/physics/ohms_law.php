<?php
// themes/default/views/calculators/physics/ohms_law.php
// PREMIUM OHM'S LAW CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="ohmsCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] right-[10%] w-[500px] h-[500px] bg-yellow-400/10 rounded-full blur-[120px] animate-float"></div>
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
                <i class="fas fa-plug"></i>
                <span>ELECTRONICS</span>
            </div>
            <h1 class="calc-title">Ohm's <span class="text-gradient">Law</span></h1>
            <p class="calc-subtitle">Calculate Voltage, Current, Resistance, and Power.</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Inputs -->
                <div class="calc-card animate-scale-in">
                    
                     <div class="mb-6">
                        <label class="calc-label text-center mb-4">Solve for:</label>
                        <div class="grid grid-cols-3 gap-2 bg-white/5 p-1 rounded-xl border border-white/10">
                            <button @click="mode = 'v'; calculate()" :class="mode === 'v' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Voltage</button>
                            <button @click="mode = 'i'; calculate()" :class="mode === 'i' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Current</button>
                            <button @click="mode = 'r'; calculate()" :class="mode === 'r' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Resist.</button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        
                        <!-- Inputs for V -->
                        <div x-show="mode === 'v'" x-transition class="space-y-4">
                            <div>
                                <label class="calc-label">Current (A)</label>
                                <input type="number" x-model.number="i" @input="calculate()" class="calc-input" placeholder="2">
                            </div>
                            <div>
                                <label class="calc-label">Resistance (Ω)</label>
                                <input type="number" x-model.number="r" @input="calculate()" class="calc-input" placeholder="10">
                            </div>
                        </div>

                        <!-- Inputs for I -->
                        <div x-show="mode === 'i'" x-transition class="space-y-4">
                            <div>
                                <label class="calc-label">Voltage (V)</label>
                                <input type="number" x-model.number="v" @input="calculate()" class="calc-input" placeholder="20">
                            </div>
                             <div>
                                <label class="calc-label">Resistance (Ω)</label>
                                <input type="number" x-model.number="r" @input="calculate()" class="calc-input" placeholder="10">
                            </div>
                        </div>

                         <!-- Inputs for R -->
                        <div x-show="mode === 'r'" x-transition class="space-y-4">
                             <div>
                                <label class="calc-label">Voltage (V)</label>
                                <input type="number" x-model.number="v" @input="calculate()" class="calc-input" placeholder="20">
                            </div>
                            <div>
                                <label class="calc-label">Current (A)</label>
                                <input type="number" x-model.number="i" @input="calculate()" class="calc-input" placeholder="2">
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up flex flex-col justify-center items-center text-center bg-gradient-to-br from-yellow-900/20 to-black border border-yellow-500/20">
                     <div class="absolute top-0 right-0 p-4 opacity-5">
                            <i class="fas fa-bolt text-9xl text-white"></i>
                     </div>
                    
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold" x-text="resultLabel"></div>
                    
                    <div class="flex items-baseline gap-2 mb-2 z-10">
                        <span class="text-6xl font-black text-white" x-text="result"></span>
                        <span class="text-2xl font-bold text-yellow-400" x-text="unit"></span>
                    </div>
                    
                    <div class="mt-8 p-4 bg-white/5 rounded-xl border border-white/5 w-full z-10">
                        <div class="flex justify-between items-center text-sm mb-2">
                             <span class="text-gray-400">Power (P)</span>
                             <span class="font-bold text-white"><span x-text="power"></span> W</span>
                        </div>
                        <div class="text-[10px] text-gray-500 text-right uppercase">calculated</div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ohmsCalculator', () => ({
        mode: 'v',
        v: 20,
        i: 2,
        r: 10,
        
        result: 0,
        unit: 'V',
        power: 0,
        resultLabel: 'Result',

        init() {
            this.calculate();
        },

        calculate() {
             let res = 0;
             let p = 0;

             if (this.mode === 'v') {
                 this.resultLabel = 'Voltage';
                 this.unit = 'V';
                 if (this.i && this.r) {
                     res = this.i * this.r;
                     p = this.i * res; // P = IV
                 }
             } 
             else if (this.mode === 'i') {
                 this.resultLabel = 'Current';
                 this.unit = 'A';
                 if (this.v && this.r) {
                     res = this.v / this.r;
                     p = this.v * res;
                 }
             }
             else if (this.mode === 'r') {
                 this.resultLabel = 'Resistance';
                 this.unit = 'Ω';
                 if (this.v && this.i) {
                     res = this.v / this.i;
                     p = this.v * this.i;
                 }
             }

             this.result = Number.isInteger(res) ? res : res.toFixed(2);
             this.power = Number.isInteger(p) ? p : p.toFixed(2);
        }
    }));
});
</script>
