<?php
// themes/default/views/calculators/chemistry/gas_laws.php
// PREMIUM GAS LAWS CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="gasLawsCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute bottom-[20%] right-[10%] w-[500px] h-[500px] bg-cyan-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
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
                <span>Thermodynamics</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Gas Laws <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Calculator</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Solve problems using Boyle's, Charles's, and Ideal Gas Laws.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Inputs -->
                <div class="calc-card animate-scale-in">
                    
                     <div class="mb-6">
                        <label class="calc-label text-center mb-4">Select Law:</label>
                        <div class="flex gap-2 bg-white/5 p-1 rounded-xl border border-white/10 overflow-x-auto">
                            <button @click="mode = 'boyle'; clear()" :class="mode === 'boyle' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="flex-1 px-4 py-2 text-sm rounded-lg transition-all font-bold whitespace-nowrap">Boyle's</button>
                            <button @click="mode = 'charles'; clear()" :class="mode === 'charles' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="flex-1 px-4 py-2 text-sm rounded-lg transition-all font-bold whitespace-nowrap">Charles's</button>
                            <button @click="mode = 'ideal'; clear()" :class="mode === 'ideal' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="flex-1 px-4 py-2 text-sm rounded-lg transition-all font-bold whitespace-nowrap">Ideal Gas</button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        
                        <!-- Boyle's: P1V1 = P2V2 -->
                        <div x-show="mode === 'boyle'" x-transition class="space-y-4">
                            <div class="text-center text-xs text-gray-400 font-mono mb-2 border-b border-white/5 pb-2">Formula: P₁V₁ = P₂V₂</div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="calc-label">P₁ (Initial Pressure)</label><input type="number" x-model.number="boyle.p1" @input="calculate()" class="calc-input"></div>
                                <div><label class="calc-label">V₁ (Initial Volume)</label><input type="number" x-model.number="boyle.v1" @input="calculate()" class="calc-input"></div>
                                <div><label class="calc-label">P₂ (Final Pressure)</label><input type="number" x-model.number="boyle.p2" @input="calculate()" class="calc-input"></div>
                                <div><label class="calc-label">V₂ (Final Volume)</label><input type="number" x-model.number="boyle.v2" @input="calculate()" class="calc-input"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 italic mt-2 text-center">Leave one field empty to calculate it.</p>
                        </div>

                        <!-- Charles's: V1/T1 = V2/T2 -->
                        <div x-show="mode === 'charles'" x-transition class="space-y-4">
                            <div class="text-center text-xs text-gray-400 font-mono mb-2 border-b border-white/5 pb-2">Formula: V₁/T₁ = V₂/T₂</div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="calc-label">V₁ (Initial Vol)</label><input type="number" x-model.number="charles.v1" @input="calculate()" class="calc-input"></div>
                                <div><label class="calc-label">T₁ (Init Temp K)</label><input type="number" x-model.number="charles.t1" @input="calculate()" class="calc-input"></div>
                                <div><label class="calc-label">V₂ (Final Vol)</label><input type="number" x-model.number="charles.v2" @input="calculate()" class="calc-input"></div>
                                <div><label class="calc-label">T₂ (Final Temp K)</label><input type="number" x-model.number="charles.t2" @input="calculate()" class="calc-input"></div>
                            </div>
                             <p class="text-[10px] text-gray-500 italic mt-2 text-center">Leave one field empty to calculate it.</p>
                        </div>

                         <!-- Ideal: PV=nRT -->
                        <div x-show="mode === 'ideal'" x-transition class="space-y-4">
                            <div class="text-center text-xs text-gray-400 font-mono mb-2 border-b border-white/5 pb-2">Formula: PV = nRT</div>
                             <div class="grid grid-cols-2 gap-4">
                                <div><label class="calc-label">Pressure (P) atm</label><input type="number" x-model.number="ideal.p" @input="calculate()" class="calc-input"></div>
                                <div><label class="calc-label">Volume (V) L</label><input type="number" x-model.number="ideal.v" @input="calculate()" class="calc-input"></div>
                                <div><label class="calc-label">Moles (n)</label><input type="number" x-model.number="ideal.n" @input="calculate()" class="calc-input"></div>
                                <div><label class="calc-label">Temp (T) K</label><input type="number" x-model.number="ideal.t" @input="calculate()" class="calc-input"></div>
                            </div>
                             <p class="text-[10px] text-gray-500 italic mt-2 text-center">Leave one field empty. R = 0.0821 L·atm/(mol·K)</p>
                        </div>

                    </div>

                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up flex flex-col justify-center items-center text-center bg-gradient-to-br from-cyan-900/20 to-black border border-cyan-500/20">
                    
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold" x-text="resultLabel"></div>
                    
                    <div class="flex items-baseline gap-2 mb-2">
                        <span class="text-6xl font-black text-white" x-text="result"></span>
                    </div>
                     <div class="text-xl font-bold text-cyan-400 mb-8" x-text="unit"></div>
                    
                    <div class="w-full bg-white/5 p-4 rounded-xl border border-white/5">
                        <div class="flex items-start gap-3 text-left">
                            <i class="fas fa-info-circle text-cyan-400 mt-1"></i>
                            <div class="text-xs text-gray-400 leading-relaxed">
                                <span x-show="mode === 'boyle'">Boyle's Law states that pressure and volume are inversely proportional at constant temperature.</span>
                                <span x-show="mode === 'charles'">Charles's Law states that volume is directly proportional to temperature (in Kelvin) at constant pressure.</span>
                                <span x-show="mode === 'ideal'">The Ideal Gas Law relates the macroscopic properties of ideal gases.</span>
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
    Alpine.data('gasLawsCalculator', () => ({
        mode: 'boyle',
        boyle: { p1: null, v1: null, p2: null, v2: null },
        charles: { v1: null, t1: null, v2: null, t2: null },
        ideal: { p: null, v: null, n: null, t: null },
        
        result: '---',
        unit: '',
        resultLabel: 'Calculated Value',

        clear() {
            this.boyle = { p1: null, v1: null, p2: null, v2: null };
            this.charles = { v1: null, t1: null, v2: null, t2: null };
            this.ideal = { p: null, v: null, n: null, t: null };
            this.result = '---';
            this.unit = '';
        },

        calculate() {
             let res = null;
             
             if (this.mode === 'boyle') {
                 const { p1, v1, p2, v2 } = this.boyle;
                 if (p1 && v1 && p2 && !v2) { res = (p1 * v1) / p2; this.resultLabel = 'Final Volume (V₂)'; this.unit = 'Units of V₁'; }
                 else if (p1 && v1 && !p2 && v2) { res = (p1 * v1) / v2; this.resultLabel = 'Final Pressure (P₂)'; this.unit = 'Units of P₁'; }
                 else if (p1 && !v1 && p2 && v2) { res = (p2 * v2) / p1; this.resultLabel = 'Initial Volume (V₁)'; this.unit = 'Units of V₂'; }
                 else if (!p1 && v1 && p2 && v2) { res = (p2 * v2) / v1; this.resultLabel = 'Initial Pressure (P₁)'; this.unit = 'Units of P₂'; }
             }
             else if (this.mode === 'charles') {
                 const { v1, t1, v2, t2 } = this.charles;
                 if (v1 && t1 && v2 && !t2) { res = (v2 * t1) / v1; this.resultLabel = 'Final Temp (T₂)'; this.unit = 'K'; }
                 else if (v1 && t1 && !v2 && t2) { res = (v1 * t2) / t1; this.resultLabel = 'Final Volume (V₂)'; this.unit = 'Units of V₁'; }
                 else if (v1 && !t1 && v2 && t2) { res = (v1 * t2) / v2; this.resultLabel = 'Initial Temp (T₁)'; this.unit = 'K'; }
                 else if (!v1 && t1 && v2 && t2) { res = (v2 * t1) / t2; this.resultLabel = 'Initial Volume (V₁)'; this.unit = 'Units of V₂'; }
             }
             else if (this.mode === 'ideal') {
                 const { p, v, n, t } = this.ideal;
                 const R = 0.0821;
                 if (!p && v && n && t) { res = (n * R * t) / v; this.resultLabel = 'Pressure (P)'; this.unit = 'atm'; }
                 else if (p && !v && n && t) { res = (n * R * t) / p; this.resultLabel = 'Volume (V)'; this.unit = 'L'; }
                 else if (p && v && !n && t) { res = (p * v) / (R * t); this.resultLabel = 'Moles (n)'; this.unit = 'mol'; }
                 else if (p && v && n && !t) { res = (p * v) / (n * R); this.resultLabel = 'Temp (T)'; this.unit = 'K'; }
             }

             if (res !== null) {
                 this.result = Number.isInteger(res) ? res : res.toFixed(4);
             } else {
                 this.result = '---';
                 this.unit = '';
             }
        }
    }));
});
</script>
