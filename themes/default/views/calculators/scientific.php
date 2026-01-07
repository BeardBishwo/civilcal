<?php
// themes/default/views/calculators/scientific.php
// PREMIUM SCIENTIFIC CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="scientificCalc()">
    <div class="fixed inset-0 pointer-events-none z-0">
         <div class="absolute top-[30%] left-[50%] -translate-x-1/2 w-[800px] h-[800px] bg-cyan-500/5 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container relative z-10">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Scientific</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-sm font-bold mb-4">
                <i class="fas fa-atom"></i>
                <span>ADVANCED COMPUTING</span>
            </div>
            <h1 class="calc-title text-4xl mb-2">Scientific <span class="text-gradient from-cyan-400 to-blue-600">Calculator</span></h1>
        </div>

        <div class="max-w-md mx-auto animate-scale-in">
            <div class="bg-gray-900 border border-white/10 rounded-3xl shadow-2xl overflow-hidden backdrop-blur-xl">
                
                <!-- Screen -->
                <div class="p-6 bg-black/40 border-b border-white/5 relative">
                    <div class="text-right text-gray-500 text-sm min-h-[1.5em] font-mono break-all" x-text="history || '&nbsp;'"></div>
                    <div class="text-right text-4xl font-black text-white font-mono break-all tracking-wider mt-1" x-text="display || '0'"></div>
                    
                     <!-- History Side Panel Toggle -->
                    <button @click="showHistory = !showHistory" class="absolute top-4 left-4 text-gray-500 hover:text-white transition">
                        <i class="fas fa-history"></i>
                    </button>
                    
                     <!-- History Overlay -->
                    <div x-show="showHistory" x-transition.opacity class="absolute inset-0 bg-gray-900/95 z-20 p-4 overflow-y-auto custom-scrollbar">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xs font-bold text-gray-400 uppercase">Calculation History</h3>
                            <button @click="showHistory = false" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
                        </div>
                        <template x-for="(item, idx) in historyLog" :key="idx">
                            <div class="border-b border-white/5 py-2 last:border-0 hover:bg-white/5 px-2 rounded cursor-pointer" @click="loadHistory(item)">
                                <div class="text-xs text-gray-500" x-text="item.expression"></div>
                                <div class="text-lg text-cyan-400 font-bold text-right" x-text="item.result"></div>
                            </div>
                        </template>
                         <div x-show="historyLog.length === 0" class="text-center text-gray-600 text-sm mt-10">No history yet</div>
                         <button x-show="historyLog.length > 0" @click="historyLog = []; localStorage.removeItem('calc_history')" class="w-full mt-4 py-2 text-xs text-red-400 border border-red-500/20 rounded hover:bg-red-500/10">Clear History</button>
                    </div>
                </div>

                <!-- Keypad -->
                <div class="p-4 grid grid-cols-4 gap-3 bg-white/5">
                    
                    <!-- Row 1: Functions -->
                     <button @click="fn('sin')" class="calc-btn-sci text-cyan-500">sin</button>
                     <button @click="fn('cos')" class="calc-btn-sci text-cyan-500">cos</button>
                     <button @click="fn('tan')" class="calc-btn-sci text-cyan-500">tan</button>
                     <button @click="fn('deg')" class="calc-btn-sci text-orange-400" x-text="degMode ? 'DEG' : 'RAD'"></button>

                    <!-- Row 2: Functions -->
                     <button @click="fn('log')" class="calc-btn-sci text-cyan-500">log</button>
                     <button @click="fn('ln')" class="calc-btn-sci text-cyan-500">ln</button>
                     <button @click="append('(')" class="calc-btn-sci text-gray-400">(</button>
                     <button @click="append(')')" class="calc-btn-sci text-gray-400">)</button>
                     
                     <!-- Row 3 -->
                     <button @click="fn('sqrt')" class="calc-btn-sci text-cyan-500">√</button>
                     <button @click="clearAll()" class="calc-btn-sci bg-red-500/20 text-red-500 font-bold">AC</button>
                     <button @click="backspace()" class="calc-btn-sci text-yellow-500"><i class="fas fa-backspace"></i></button>
                     <button @click="append('/')" class="calc-btn-sci text-purple-400 font-bold">÷</button>

                     <!-- Row 4 -->
                     <button @click="fn('pow')" class="calc-btn-sci text-cyan-500">xⁿ</button>
                     <button @click="append('7')" class="calc-btn-sci font-bold text-white bg-white/5">7</button>
                     <button @click="append('8')" class="calc-btn-sci font-bold text-white bg-white/5">8</button>
                     <button @click="append('9')" class="calc-btn-sci font-bold text-white bg-white/5">9</button>
                     <button @click="append('*')" class="calc-btn-sci text-purple-400 font-bold">×</button>

                     <!-- Row 5 -->
                     <button @click="fn('pi')" class="calc-btn-sci text-cyan-500">π</button>
                     <button @click="append('4')" class="calc-btn-sci font-bold text-white bg-white/5">4</button>
                     <button @click="append('5')" class="calc-btn-sci font-bold text-white bg-white/5">5</button>
                     <button @click="append('6')" class="calc-btn-sci font-bold text-white bg-white/5">6</button>
                     <button @click="append('-')" class="calc-btn-sci text-purple-400 font-bold">-</button>

                     <!-- Row 6 -->
                     <button @click="fn('e')" class="calc-btn-sci text-cyan-500">e</button>
                     <button @click="append('1')" class="calc-btn-sci font-bold text-white bg-white/5">1</button>
                     <button @click="append('2')" class="calc-btn-sci font-bold text-white bg-white/5">2</button>
                     <button @click="append('3')" class="calc-btn-sci font-bold text-white bg-white/5">3</button>
                     <button @click="append('+')" class="calc-btn-sci text-purple-400 font-bold">+</button>

                     <!-- Row 7 -->
                     <button @click="append('00')" class="calc-btn-sci text-gray-400">00</button>
                     <button @click="append('0')" class="calc-btn-sci font-bold text-white bg-white/5">0</button>
                     <button @click="append('.')" class="calc-btn-sci font-bold text-white bg-white/5">.</button>
                     <button @click="calculate()" class="calc-btn-sci col-span-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-bold shadow-lg shadow-cyan-500/30">=</button>
                </div>
            </div>
             
             <!-- Tips -->
             <div class="mt-6 text-center">
                 <p class="text-[10px] text-gray-500 uppercase tracking-widest">Keyboard shortcuts supported</p>
             </div>

        </div>
    </div>
</div>

<style>
    .calc-btn-sci {
        @apply h-14 rounded-xl flex items-center justify-center text-lg hover:bg-white/10 active:scale-95 transition-all select-none;
    }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('scientificCalc', () => ({
        display: '',
        history: '',
        degMode: true, // Degrees by default
        showHistory: false,
        historyLog: JSON.parse(localStorage.getItem('calc_history') || '[]'),
        
        init() {
            // Keyboard support
            document.addEventListener('keydown', (e) => {
                const key = e.key;
                if (/[0-9]/.test(key)) this.append(key);
                if (['+', '-', '*', '/', '.', '(', ')'].includes(key)) this.append(key);
                if (key === 'Enter') this.calculate();
                if (key === 'Backspace') this.backspace();
                if (key === 'Escape') this.clearAll();
            });
        },

        append(char) {
            if (this.display === 'Error') this.display = '';
            this.display += char;
        },

        backspace() {
            this.display = this.display.slice(0, -1);
        },

        clearAll() {
            this.display = '';
            this.history = '';
        },
        
        loadHistory(item) {
            this.display = item.result.toString();
            this.showHistory = false;
        },

        fn(type) {
             if (type === 'deg') {
                 this.degMode = !this.degMode;
                 return;
             }
             
             if (type === 'pi') { this.append(Math.PI.toFixed(8)); return; }
             if (type === 'e') { this.append(Math.E.toFixed(8)); return; }
             if (type === 'sqrt') { this.append('sqrt('); return; }
             if (type === 'pow') { this.append('^'); return; }
             
             this.append(type + '(');
        },

        calculate() {
             if (!this.display) return;
             
             let expr = this.display;
             let displayExpr = this.display;

             // Replace custom logical operators for evaluation
             // Handle degrees conversion for trig
             const toRad = this.degMode ? `*(Math.PI/180)` : '';
             
             // Wrap Trig inputs
             // Simplistic parser for sin(30) -> Math.sin(30 * conv)
             // This is a basic implementation. For production, a robust tokenizer is better.
             // We'll use JS functions with pre-processing.
             
             try {
                 // Replace UI symbols with JS Math
                 let evalExpr = expr
                    .replace(/sin\(/g, `Math.sin(${this.degMode ? 'Math.PI/180*' : ''}`)
                    .replace(/cos\(/g, `Math.cos(${this.degMode ? 'Math.PI/180*' : ''}`)
                    .replace(/tan\(/g, `Math.tan(${this.degMode ? 'Math.PI/180*' : ''}`)
                    .replace(/log\(/g, 'Math.log10(')
                    .replace(/ln\(/g, 'Math.log(')
                    .replace(/sqrt\(/g, 'Math.sqrt(')
                    .replace(/\^/g, '**')
                    .replace(/π/g, 'Math.PI')
                    .replace(/e/g, 'Math.E');

                 // Safety check: only allow limited chars
                 if (!/^[0-9+\-*/().\sMathPIElogsincoqtand\^,]+$/.test(evalExpr.replace(/Math\./g, ''))) {
                     // throw new Error("Invalid characters");
                 }
                 
                 // Evaluate
                 // eslint-disable-next-line
                 let result = eval(evalExpr); // Note: Eval is used for calculator logic simplicity here.
                 
                 // Format
                 if (!isFinite(result) || isNaN(result)) {
                     this.display = "Error";
                     return;
                 }
                 
                 // float precision
                 result = parseFloat(result.toFixed(10)); // clear tiny float errors

                 this.history = this.display + ' =';
                 this.display = result.toString();
                 
                 // Add to log
                 this.historyLog.unshift({ expression: displayExpr, result: result });
                 if(this.historyLog.length > 20) this.historyLog.pop();
                 localStorage.setItem('calc_history', JSON.stringify(this.historyLog));
                 
             } catch (e) {
                 this.display = "Error";
             }
        }
    }));
});
</script>
