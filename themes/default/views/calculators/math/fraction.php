<?php
// themes/default/views/calculators/math/fraction.php
// PREMIUM FRACTION CALCULATOR
?>

<!-- Load Calculators CSS -->
<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<!-- Load Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="fractionCalculator()">
    
    <!-- Animated Background -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] right-[-10%] w-[600px] h-[600px] bg-primary/20 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-accent/20 rounded-full blur-[100px] animate-float" style="animation-delay: 2s;"></div>
    </div>

    <div class="calc-container">
        <!-- Breadcrumb -->
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Fraction Calculator</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="calc-header animate-slide-down">
             <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-divide"></i>
                <span>MATHEMATICS</span>
            </div>
            <h1 class="calc-title">Fraction <span class="text-gradient">Master</span></h1>
            <p class="calc-subtitle">Perform addition, subtraction, multiplication, and division of fractions instantly.</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            <div class="calc-card animate-scale-in col-span-1 md:col-span-2">
                
                <!-- Operation Selector -->
                 <div class="flex justify-center gap-4 mb-8">
                    <template x-for="op in operations">
                        <button @click="operation = op.id; calculate()" 
                                :class="operation === op.id ? 'bg-primary border-primary text-white shadow-lg shadow-primary/40' : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10'"
                                class="w-14 h-14 rounded-full border text-2xl font-bold transition-all flex items-center justify-center">
                            <span x-text="op.symbol"></span>
                        </button>
                    </template>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-8">
                    
                    <!-- Fraction 1 -->
                    <div class="flex flex-col gap-2 w-32">
                        <input type="number" x-model.number="n1" @input="calculate()" class="calc-input text-center text-2xl font-bold p-2" placeholder="Num">
                        <div class="h-1 bg-white/20 rounded-full"></div>
                        <input type="number" x-model.number="d1" @input="calculate()" class="calc-input text-center text-2xl font-bold p-2" placeholder="Den">
                    </div>

                    <!-- Operator Symbol -->
                    <div class="text-4xl font-black text-primary" x-text="getSymbol()"></div>

                    <!-- Fraction 2 -->
                    <div class="flex flex-col gap-2 w-32">
                        <input type="number" x-model.number="n2" @input="calculate()" class="calc-input text-center text-2xl font-bold p-2" placeholder="Num">
                        <div class="h-1 bg-white/20 rounded-full"></div>
                        <input type="number" x-model.number="d2" @input="calculate()" class="calc-input text-center text-2xl font-bold p-2" placeholder="Den">
                    </div>

                    <!-- Equals -->
                    <div class="text-4xl font-black text-gray-500">=</div>

                    <!-- Result Fraction -->
                    <div class="flex flex-col gap-2 w-32 relative">
                         <div x-show="resultN === null" class="absolute inset-0 flex items-center justify-center opacity-50">
                            <i class="fas fa-question text-4xl text-white/10"></i>
                        </div>
                        <div x-show="resultN !== null" x-transition>
                            <div class="bg-gradient-to-r from-green-400 to-emerald-500 bg-clip-text text-transparent text-center text-3xl font-black p-2" x-text="resultN"></div>
                            <div class="h-1 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full my-1"></div>
                            <div class="bg-gradient-to-r from-green-400 to-emerald-500 bg-clip-text text-transparent text-center text-3xl font-black p-2" x-text="resultD"></div>
                        </div>
                    </div>
                </div>

                <!-- Decimal & Mixed Number -->
                <div x-show="resultDecimal !== null" class="mt-10 grid grid-cols-2 gap-4" x-transition>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 text-center">
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Decimal</div>
                        <div class="text-xl font-mono font-bold text-white" x-text="resultDecimal"></div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 text-center">
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Mixed Number</div>
                        <div class="text-xl font-mono font-bold text-white" x-text="mixedNumber"></div>
                    </div>
                </div>

                <!-- Explanation -->
                 <div x-show="explanation" class="mt-6 p-4 rounded-xl bg-white/5 border border-white/10 text-center" x-transition>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Step-by-Step</div>
                    <div class="font-mono text-sm text-gray-300" x-html="explanation"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('fractionCalculator', () => ({
        n1: 1, d1: 2,
        n2: 1, d2: 3,
        operation: 'add',
        resultN: null,
        resultD: null,
        resultDecimal: null,
        mixedNumber: null,
        explanation: null,
        operations: [
            { id: 'add', symbol: '+' },
            { id: 'subtract', symbol: '−' },
            { id: 'multiply', symbol: '×' },
            { id: 'divide', symbol: '÷' }
        ],

        init() {
            this.calculate();
        },

        getSymbol() {
            return this.operations.find(o => o.id === this.operation)?.symbol;
        },

        gcd(a, b) {
            return b === 0 ? a : this.gcd(b, a % b);
        },

        calculate() {
            if (!this.d1 || !this.d2) return; // Divide by zero prevention

            let resN, resD;

            if (this.operation === 'add') {
                resN = (this.n1 * this.d2) + (this.n2 * this.d1);
                resD = this.d1 * this.d2;
                this.explanation = `(${this.n1} × ${this.d2}) + (${this.n2} × ${this.d1}) / (${this.d1} × ${this.d2})`;
            } else if (this.operation === 'subtract') {
                resN = (this.n1 * this.d2) - (this.n2 * this.d1);
                resD = this.d1 * this.d2;
                this.explanation = `(${this.n1} × ${this.d2}) - (${this.n2} × ${this.d1}) / (${this.d1} × ${this.d2})`;
            } else if (this.operation === 'multiply') {
                resN = this.n1 * this.n2;
                resD = this.d1 * this.d2;
                this.explanation = `${this.n1} × ${this.n2} / ${this.d1} × ${this.d2}`;
            } else if (this.operation === 'divide') {
                resN = this.n1 * this.d2;
                resD = this.d1 * this.n2;
                this.explanation = `${this.n1} × ${this.d2} / ${this.d1} × ${this.n2} (Reciprocal multiply)`;
            }

            // Simplify
            const common = Math.abs(this.gcd(resN, resD));
            this.resultN = resN / common;
            this.resultD = resD / common;

            // Handle negative denominator
            if (this.resultD < 0) {
                this.resultN = -this.resultN;
                this.resultD = -this.resultD;
            }

            // Decimal
            this.resultDecimal = (this.resultN / this.resultD).toFixed(4);

            // Mixed Number
            if (Math.abs(this.resultN) >= Math.abs(this.resultD)) {
                const whole = Math.floor(Math.abs(this.resultN) / this.resultD) * (this.resultN < 0 ? -1 : 1);
                const rem = Math.abs(this.resultN) % this.resultD;
                if (rem === 0) this.mixedNumber = whole;
                else this.mixedNumber = `${whole} ${rem}/${this.resultD}`;
            } else {
                this.mixedNumber = "N/A (< 1)";
            }
        }
    }));
});
</script>
