<?php
// themes/default/views/calculators/chemistry/molar_mass.php
// PREMIUM MOLAR MASS CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="molarMassCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] left-[10%] w-[500px] h-[500px] bg-green-500/10 rounded-full blur-[120px] animate-float"></div>
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
                <span>Stoichiometry</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Molar <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Mass</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate the molecular weight of chemical compounds.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <!-- Main Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Input -->
                <div class="calc-card animate-scale-in">
                    
                    <div class="space-y-6">
                        <div>
                            <label class="calc-label">Chemical Formula</label>
                            <div class="relative">
                                <input type="text" x-model="formula" @input="calculate()" class="calc-input pr-10 font-mono tracking-wider uppercase" placeholder="H2SO4">
                                <button @click="formula = ''; calculate()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                             <div class="mt-4 flex flex-wrap gap-2 text-xs">
                                <span class="text-gray-400">Try:</span>
                                <button @click="formula = 'H2O'; calculate()" class="px-3 py-1 bg-white/5 rounded-full hover:bg-white/10 transition text-green-400 font-mono">H2O</button>
                                <button @click="formula = 'CO2'; calculate()" class="px-3 py-1 bg-white/5 rounded-full hover:bg-white/10 transition text-green-400 font-mono">CO2</button>
                                <button @click="formula = 'C6H12O6'; calculate()" class="px-3 py-1 bg-white/5 rounded-full hover:bg-white/10 transition text-green-400 font-mono">Glucose</button>
                                <button @click="formula = 'NaCl'; calculate()" class="px-3 py-1 bg-white/5 rounded-full hover:bg-white/10 transition text-green-400 font-mono">NaCl</button>
                            </div>
                        </div>

                         <div class="p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-xl flex gap-3 text-sm text-yellow-200">
                             <i class="fas fa-exclamation-triangle mt-1"></i>
                             <p>Case matters! "Co" is Cobalt, "CO" is Carbon Monoxide. Basic formulas supported.</p>
                         </div>
                    </div>

                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up bg-gradient-to-br from-green-900/20 to-black border border-green-500/20 max-h-[500px] overflow-hidden flex flex-col">
                    
                     <div class="text-center p-6 border-b border-white/5">
                        <div class="text-sm text-gray-400 uppercase tracking-widest mb-2 font-bold">Molar Mass</div>
                    
                        <div class="flex items-baseline justify-center gap-2">
                             <span class="text-5xl font-black text-white" x-text="mass"></span>
                             <span class="text-xl font-bold text-green-400">g/mol</span>
                        </div>
                     </div>
                    
                    <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
                        <h4 class="text-xs uppercase text-gray-500 font-bold mb-4">Composition Breakdown</h4>
                        <div class="space-y-2">
                            <template x-for="(val, el) in breakdown" :key="el">
                                <div class="flex justify-between items-center bg-white/5 p-3 rounded-lg border border-white/5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-400 font-bold text-xs" x-text="el"></div>
                                        <div class="flex flex-col">
                                            <span class="text-white font-bold" x-text="elementNames[el] || 'Unknown'"></span>
                                            <span class="text-[10px] text-gray-400">Atomic Mass: <span x-text="atomicWeights[el]"></span></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-white font-mono font-bold">× <span x-text="val.count"></span></div>
                                        <div class="text-xs text-green-400" x-text="val.pct + '%'"></div>
                                    </div>
                                </div>
                            </template>
                             <div x-show="Object.keys(breakdown).length === 0" class="text-center text-gray-600 italic py-10">
                                Enter a formula to see composition.
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
    Alpine.data('molarMassCalculator', () => ({
        formula: 'H2SO4',
        mass: 0,
        breakdown: {},
        
        atomicWeights: {
            'H': 1.008, 'He': 4.003, 'Li': 6.941, 'Be': 9.012, 'B': 10.811, 'C': 12.011, 'N': 14.007, 'O': 15.999,
            'F': 18.998, 'Ne': 20.180, 'Na': 22.990, 'Mg': 24.305, 'Al': 26.982, 'Si': 28.086, 'P': 30.974, 'S': 32.065,
            'Cl': 35.453, 'K': 39.098, 'Ca': 40.078, 'Fe': 55.845, 'Cu': 63.546, 'Zn': 65.38, 'Ag': 107.87, 'Au': 196.97,
            'Hg': 200.59, 'Pb': 207.2, 'U': 238.03, 'Br': 79.904, 'I': 126.90, 'Mn': 54.938, 'Cr': 51.996, 'Ni': 58.693
        },
        
        elementNames: {
            'H': 'Hydrogen', 'He': 'Helium', 'Li': 'Lithium', 'Be': 'Beryllium', 'B': 'Boron', 'C': 'Carbon', 'N': 'Nitrogen', 'O': 'Oxygen',
            'F': 'Fluorine', 'Ne': 'Neon', 'Na': 'Sodium', 'Mg': 'Magnesium', 'Al': 'Aluminium', 'Si': 'Silicon', 'P': 'Phosphorus', 'S': 'Sulfur',
            'Cl': 'Chlorine', 'K': 'Potassium', 'Ca': 'Calcium', 'Fe': 'Iron', 'Cu': 'Copper', 'Zn': 'Zinc', 'Ag': 'Silver', 'Au': 'Gold',
            'Hg': 'Mercury', 'Pb': 'Lead', 'U': 'Uranium', 'Br': 'Bromine', 'I': 'Iodine', 'Mn': 'Manganese', 'Cr': 'Chromium', 'Ni': 'Nickel'
        },

        init() {
            this.calculate();
        },

        calculate() {
             const f = this.formula.trim();
             if (!f) { this.mass = 0; this.breakdown = {}; return; }

             // Basic parser logic for MVP (AlpineJS friendly)
             let totalMass = 0;
             let bd = {};
             
             const regex = /([A-Z][a-z]?)(\d*)/g;
             let match;
             
             // Check valid chars roughly
             try {
                 while ((match = regex.exec(f)) !== null) {
                    const el = match[1];
                    const count = match[2] ? parseInt(match[2]) : 1;
                    
                    if (this.atomicWeights[el]) {
                        const w = this.atomicWeights[el] * count;
                        totalMass += w;
                        
                        if (!bd[el]) bd[el] = { count: 0, mass: 0 };
                        bd[el].count += count;
                        bd[el].mass += w;
                    } 
                 }
                 
                 // Calc percentages
                 for(let key in bd) {
                     bd[key].pct = ((bd[key].mass / totalMass) * 100).toFixed(1);
                 }

                 this.breakdown = bd;
                 this.mass = totalMass.toFixed(3);
             } catch(e) {
                 this.mass = "Error";
             }
        }
    }));
});
</script>
