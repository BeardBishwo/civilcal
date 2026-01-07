<?php
// themes/default/views/calculators/physics/velocity.php
// PREMIUM VELOCITY CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="velocityCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] right-[10%] w-[500px] h-[500px] bg-blue-500/10 rounded-full blur-[120px] animate-float"></div>
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
                <i class="fas fa-bolt"></i>
                <span>MOTION</span>
            </div>
            <h1 class="calc-title">Velocity <span class="text-gradient">Calculator</span></h1>
            <p class="calc-subtitle">Solve for velocity, distance, or time using the motion formula.</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Inputs -->
                <div class="calc-card animate-scale-in">
                    
                     <div class="mb-6">
                        <label class="calc-label text-center mb-4">I want to calculate:</label>
                        <div class="grid grid-cols-3 gap-2 bg-white/5 p-1 rounded-xl border border-white/10">
                            <button @click="mode = 'v'; calculate()" :class="mode === 'v' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Velocity</button>
                            <button @click="mode = 'd'; calculate()" :class="mode === 'd' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Distance</button>
                            <button @click="mode = 't'; calculate()" :class="mode === 't' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Time</button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        
                        <!-- Inputs for V -->
                        <div x-show="mode === 'v'" x-transition class="space-y-4">
                            <div>
                                <label class="calc-label">Distance (m)</label>
                                <input type="number" x-model.number="d" @input="calculate()" class="calc-input" placeholder="100">
                            </div>
                            <div>
                                <label class="calc-label">Time (s)</label>
                                <input type="number" x-model.number="t" @input="calculate()" class="calc-input" placeholder="10">
                            </div>
                        </div>

                        <!-- Inputs for D -->
                        <div x-show="mode === 'd'" x-transition class="space-y-4">
                            <div>
                                <label class="calc-label">Velocity (m/s)</label>
                                <input type="number" x-model.number="v" @input="calculate()" class="calc-input" placeholder="25">
                            </div>
                             <div>
                                <label class="calc-label">Time (s)</label>
                                <input type="number" x-model.number="t" @input="calculate()" class="calc-input" placeholder="10">
                            </div>
                        </div>

                         <!-- Inputs for T -->
                        <div x-show="mode === 't'" x-transition class="space-y-4">
                             <div>
                                <label class="calc-label">Distance (m)</label>
                                <input type="number" x-model.number="d" @input="calculate()" class="calc-input" placeholder="100">
                            </div>
                            <div>
                                <label class="calc-label">Velocity (m/s)</label>
                                <input type="number" x-model.number="v" @input="calculate()" class="calc-input" placeholder="25">
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up flex flex-col justify-center items-center text-center bg-gradient-to-br from-blue-900/20 to-black border border-blue-500/20">
                    
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold" x-text="resultLabel"></div>
                    
                    <div class="flex items-baseline gap-2 mb-2">
                        <span class="text-6xl font-black text-white" x-text="result"></span>
                        <span class="text-2xl font-bold text-blue-400" x-text="unit"></span>
                    </div>
                    
                    <div class="mt-8 p-4 bg-white/5 rounded-xl border border-white/5 w-full">
                        <div class="flex justify-between text-xs text-gray-400 font-mono mb-2">
                            <span>Equation</span>
                            <span class="text-white" x-text="equation"></span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('velocityCalculator', () => ({
        mode: 'v',
        v: 0,
        d: 100,
        t: 10,
        
        result: 0,
        unit: 'm/s',
        resultLabel: 'Velocity',
        equation: 'v = d / t',

        init() {
            this.calculate();
        },

        calculate() {
             let res = 0;
             if (this.mode === 'v') {
                 this.resultLabel = 'Velocity';
                 this.unit = 'm/s';
                 this.equation = 'v = d / t';
                 if (this.d && this.t) res = this.d / this.t;
             } 
             else if (this.mode === 'd') {
                 this.resultLabel = 'Distance';
                 this.unit = 'm';
                 this.equation = 'd = v * t';
                 if (this.v && this.t) res = this.v * this.t;
             }
             else if (this.mode === 't') {
                 this.resultLabel = 'Time';
                 this.unit = 's';
                 this.equation = 't = d / v';
                 if (this.d && this.v) res = this.d / this.v;
             }

             this.result = Number.isInteger(res) ? res : res.toFixed(2);
        }
    }));
});
</script>
