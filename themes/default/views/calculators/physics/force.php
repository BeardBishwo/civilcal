<?php
// themes/default/views/calculators/physics/force.php
// PREMIUM FORCE CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="forceCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute bottom-[20%] left-[10%] w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[120px] animate-float"></div>
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
                <i class="fas fa-rocket"></i>
                <span>DYNAMICS</span>
            </div>
            <h1 class="calc-title">Force <span class="text-gradient">Calculator</span></h1>
            <p class="calc-subtitle">Compute Force, Mass, or Acceleration using Newton's Second Law.</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Inputs -->
                <div class="calc-card animate-scale-in">
                    
                     <div class="mb-6">
                        <label class="calc-label text-center mb-4">Solve for:</label>
                        <div class="grid grid-cols-3 gap-2 bg-white/5 p-1 rounded-xl border border-white/10">
                            <button @click="mode = 'f'; calculate()" :class="mode === 'f' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Force</button>
                            <button @click="mode = 'm'; calculate()" :class="mode === 'm' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Mass</button>
                            <button @click="mode = 'a'; calculate()" :class="mode === 'a' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Accel.</button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        
                        <!-- Inputs for F -->
                        <div x-show="mode === 'f'" x-transition class="space-y-4">
                            <div>
                                <label class="calc-label">Mass (kg)</label>
                                <input type="number" x-model.number="m" @input="calculate()" class="calc-input" placeholder="10">
                            </div>
                            <div>
                                <label class="calc-label">Acceleration (m/s²)</label>
                                <input type="number" x-model.number="a" @input="calculate()" class="calc-input" placeholder="9.8">
                            </div>
                        </div>

                        <!-- Inputs for M -->
                        <div x-show="mode === 'm'" x-transition class="space-y-4">
                            <div>
                                <label class="calc-label">Force (N)</label>
                                <input type="number" x-model.number="f" @input="calculate()" class="calc-input" placeholder="100">
                            </div>
                             <div>
                                <label class="calc-label">Acceleration (m/s²)</label>
                                <input type="number" x-model.number="a" @input="calculate()" class="calc-input" placeholder="9.8">
                            </div>
                        </div>

                         <!-- Inputs for A -->
                        <div x-show="mode === 'a'" x-transition class="space-y-4">
                             <div>
                                <label class="calc-label">Force (N)</label>
                                <input type="number" x-model.number="f" @input="calculate()" class="calc-input" placeholder="100">
                            </div>
                            <div>
                                <label class="calc-label">Mass (kg)</label>
                                <input type="number" x-model.number="m" @input="calculate()" class="calc-input" placeholder="10">
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up flex flex-col justify-center items-center text-center bg-gradient-to-br from-purple-900/20 to-black border border-purple-500/20">
                    
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold" x-text="resultLabel"></div>
                    
                    <div class="flex items-baseline gap-2 mb-2">
                        <span class="text-6xl font-black text-white" x-text="result"></span>
                        <span class="text-2xl font-bold text-purple-400" x-text="unit"></span>
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
    Alpine.data('forceCalculator', () => ({
        mode: 'f',
        f: 0,
        m: 10,
        a: 9.8,
        
        result: 0,
        unit: 'N',
        resultLabel: 'Force',
        equation: 'F = m * a',

        init() {
            this.calculate();
        },

        calculate() {
             let res = 0;
             if (this.mode === 'f') {
                 this.resultLabel = 'Force';
                 this.unit = 'N';
                 this.equation = 'F = m × a';
                 if (this.m && this.a) res = this.m * this.a;
             } 
             else if (this.mode === 'm') {
                 this.resultLabel = 'Mass';
                 this.unit = 'kg';
                 this.equation = 'm = F / a';
                 if (this.f && this.a) res = this.f / this.a;
             }
             else if (this.mode === 'a') {
                 this.resultLabel = 'Acceleration';
                 this.unit = 'm/s²';
                 this.equation = 'a = F / m';
                 if (this.f && this.m) res = this.f / this.m;
             }

             this.result = Number.isInteger(res) ? res : res.toFixed(2);
        }
    }));
});
</script>
