<?php
// themes/default/views/calculators/math/percentage.php
// PREMIUM PERCENTAGE CALCULATOR
?>

<!-- Load Calculators CSS -->
<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<!-- Load Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="percentageCalculator()">
    
    <!-- Animated Background -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] left-1/2 w-[600px] h-[600px] bg-primary/10 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[500px] h-[500px] bg-accent/10 rounded-full blur-[100px] animate-float" style="animation-delay: 2s;"></div>
    </div>

    <div class="calc-container">
        
        <!-- Breadcrumb -->
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/') ?>" class="hover:text-white transition"><i class="fas fa-home"></i></a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Percentage Calculator</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-percent"></i>
                <span>MATHEMATICS</span>
            </div>
            
            <h1 class="calc-title">
                Percentage <span class="text-gradient">Pro</span>
            </h1>
            <p class="calc-subtitle max-w-2xl mx-auto">
                Comprehensive tool for all percentage calculations. Find percentages, percentage change, and reverse percentages instantly.
            </p>
        </div>

        <!-- Main Calculator Card -->
        <div class="calc-grid mb-12">
            
            <!-- Input Section -->
            <div class="calc-card animate-scale-in">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary/20 to-secondary/20 border border-primary/30 flex items-center justify-center">
                        <i class="fas fa-keyboard text-primary"></i>
                    </div>
                    <span>Calculation Type</span>
                </h2>

                <!-- Type Selector -->
                <div class="calc-section">
                    <label class="calc-label">
                        <i class="fas fa-list mr-2"></i> Select Mode
                    </label>
                    <select x-model="mode" @change="resetInputs()" class="calc-select">
                        <option value="basic">What is X% of Y?</option>
                        <option value="find_percent">X is what % of Y?</option>
                        <option value="increase">Percentage Increase/Decrease</option>
                        <option value="reverse">Reverse Percentage (Find Original)</option>
                    </select>
                </div>

                <!-- Basic Percentage Inputs -->
                <div x-show="mode === 'basic'" class="space-y-6 animate-slide-up">
                    <div class="flex items-center gap-4 flex-wrap md:flex-nowrap">
                        <span class="text-xl font-bold text-white">What is</span>
                        <div class="calc-input-group flex-1">
                            <input type="number" x-model.number="val1" @input="calculate()" class="calc-input text-center" placeholder="X" step="0.01">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">%</div>
                        </div>
                        <span class="text-xl font-bold text-white">of</span>
                        <div class="calc-input-group flex-1">
                            <input type="number" x-model.number="val2" @input="calculate()" class="calc-input text-center" placeholder="Y" step="0.01">
                        </div>
                        <span class="text-xl font-bold text-white">?</span>
                    </div>
                </div>

                <!-- Find Percentage Inputs -->
                <div x-show="mode === 'find_percent'" class="space-y-6 animate-slide-up">
                     <div class="flex items-center gap-4 flex-wrap md:flex-nowrap">
                        <div class="calc-input-group flex-1">
                            <input type="number" x-model.number="val1" @input="calculate()" class="calc-input text-center" placeholder="X" step="0.01">
                        </div>
                        <span class="text-xl font-bold text-white">is what % of</span>
                        <div class="calc-input-group flex-1">
                            <input type="number" x-model.number="val2" @input="calculate()" class="calc-input text-center" placeholder="Y" step="0.01">
                        </div>
                        <span class="text-xl font-bold text-white">?</span>
                    </div>
                </div>

                <!-- Increase/Decrease Inputs -->
                <div x-show="mode === 'increase'" class="space-y-4 animate-slide-up">
                    <div class="calc-section">
                        <label class="calc-label">From Value</label>
                        <input type="number" x-model.number="val1" @input="calculate()" class="calc-input" placeholder="Start Value" step="0.01">
                    </div>
                    <div class="calc-section">
                        <label class="calc-label">To Value</label>
                        <input type="number" x-model.number="val2" @input="calculate()" class="calc-input" placeholder="End Value" step="0.01">
                    </div>
                </div>

                 <!-- Reverse Inputs -->
                 <div x-show="mode === 'reverse'" class="space-y-6 animate-slide-up">
                    <div class="flex items-center gap-4 flex-wrap md:flex-nowrap">
                        <span class="text-xl font-bold text-white">Value is</span>
                        <div class="calc-input-group flex-1">
                            <input type="number" x-model.number="val1" @input="calculate()" class="calc-input text-center" placeholder="Total" step="0.01">
                        </div>
                        <span class="text-xl font-bold text-white">which is</span>
                        <div class="calc-input-group flex-1">
                            <input type="number" x-model.number="val2" @input="calculate()" class="calc-input text-center" placeholder="%" step="0.01">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">%</div>
                        </div>
                        <span class="text-xl font-bold text-white">of what?</span>
                    </div>
                </div>


                <!-- Action Buttons -->
                <div class="flex gap-4 mt-8">
                    <button @click="calculate()" class="btn-primary flex-1">
                        <i class="fas fa-calculator mr-2"></i> Calculate
                    </button>
                    <button @click="reset()" class="btn-secondary">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </div>

            <!-- Result Section -->
            <div class="calc-card animate-scale-in" style="animation-delay: 0.1s;">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500/20 to-emerald-500/20 border border-green-500/30 flex items-center justify-center">
                        <i class="fas fa-equals text-green-500"></i>
                    </div>
                    <span>Answer</span>
                </h2>

                <!-- Dynamic Visualization (Bar) -->
                <div class="mb-6 h-12 rounded-full bg-white/5 border border-white/10 relative overflow-hidden flex items-center px-4">
                     <div class="absolute left-0 top-0 bottom-0 bg-gradient-to-r from-primary to-accent transition-all duration-700 ease-out" 
                          :style="'width: ' + Math.min(Math.max((visualPercentage || 0), 0), 100) + '%'"></div>
                     <span class="relative z-10 text-xs font-bold text-white mix-blend-difference" x-text="visualPercentage ? formatNumber(visualPercentage) + '%' : ''"></span>
                </div>

                <!-- Result Display -->
                <div x-show="result !== null" class="calc-result" x-transition>
                    <div class="calc-result-label" x-text="resultLabel">Result</div>
                    <div class="calc-result-value" x-text="formatNumber(result) + (resultSuffix || '')"></div>
                    <div class="calc-result-unit" x-text="resultSubtext"></div>
                    
                    <!-- Copy Button -->
                    <button @click="copyResult()" class="mt-6 btn-secondary w-full">
                        <i class="fas fa-copy mr-2"></i> Copy Answer
                    </button>
                </div>

                <!-- Empty State -->
                <div x-show="result === null" class="text-center py-8">
                    <div class="text-4xl text-gray-700 mb-2 animate-bounce-subtle">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <p class="text-gray-500">Enter values to compute</p>
                </div>

                <!-- Detailed Explanation -->
                <div x-show="result !== null" class="mt-8 p-4 rounded-xl bg-white/5 border border-white/10" x-transition>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Breakdown</div>
                    <div class="font-mono text-sm text-white" x-html="explanation"></div>
                </div>
            </div>

        </div>

    </div>

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition class="toast" style="display: none;">
        <i class="fas fa-check-circle text-success"></i>
        <span>Copied to clipboard!</span>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('percentageCalculator', () => ({
        mode: 'basic',
        val1: null,
        val2: null,
        result: null,
        resultLabel: 'Result',
        resultSuffix: '',
        resultSubtext: '',
        explanation: '',
        visualPercentage: 0, // For the progress bar
        showToast: false,

        calculate() {
            if (this.val1 === null || this.val2 === null) return;
            
            this.val1 = parseFloat(this.val1);
            this.val2 = parseFloat(this.val2);

            switch(this.mode) {
                case 'basic': // What is X% of Y?
                    this.result = (this.val1 / 100) * this.val2;
                    this.resultLabel = `${this.val1}% of ${this.val2}`;
                    this.resultSuffix = '';
                    this.resultSubtext = '';
                    this.explanation = `${this.val1} / 100 × ${this.val2} = ${this.formatNumber(this.result)}`;
                    this.visualPercentage = this.val1;
                    break;
                
                case 'find_percent': // X is what % of Y?
                    if (this.val2 === 0) { this.result = 0; break; }
                    this.result = (this.val1 / this.val2) * 100;
                    this.resultLabel = 'Percentage';
                    this.resultSuffix = '%';
                    this.resultSubtext = `${this.val1} out of ${this.val2}`;
                    this.explanation = `${this.val1} ÷ ${this.val2} × 100 = ${this.formatNumber(this.result)}%`;
                    this.visualPercentage = this.result;
                    break;

                case 'increase': // Percentage Change
                    if (this.val1 === 0) { this.result = 0; break; }
                    let diff = this.val2 - this.val1;
                    this.result = (diff / this.val1) * 100;
                    this.resultLabel = this.result > 0 ? 'Increase' : 'Decrease';
                    this.resultSuffix = '%';
                    this.resultSubtext = `Difference: ${this.formatNumber(diff)}`;
                    this.explanation = `(${this.val2} - ${this.val1}) ÷ ${this.val1} × 100 = ${this.formatNumber(this.result)}%`;
                    this.visualPercentage = Math.abs(this.result);
                    break;

                case 'reverse': // Finds original number. X is Y% of what?
                     // val1 = part, val2 = percent
                     if (this.val2 === 0) { this.result = 0; break; }
                     this.result = (this.val1 / this.val2) * 100;
                     this.resultLabel = 'Original Value';
                     this.resultSuffix = '';
                     this.resultSubtext = `100% Value`;
                     this.explanation = `${this.val1} ÷ ${this.val2}% = ${this.formatNumber(this.result)}`;
                     this.visualPercentage = 100; // Original is always 100%
                     break;
            }
        },

        resetInputs() {
            this.val1 = null;
            this.val2 = null;
            this.result = null;
            this.visualPercentage = 0;
        },

        reset() {
            this.resetInputs();
            this.mode = 'basic';
        },

        formatNumber(num) {
            return num ? num.toLocaleString('en-US', { maximumFractionDigits: 2 }) : '0';
        },

        copyResult() {
            if (this.result !== null) {
                navigator.clipboard.writeText(this.formatNumber(this.result) + this.resultSuffix);
                this.showToast = true;
                setTimeout(() => this.showToast = false, 2000);
            }
        }
    }));
});
</script>
