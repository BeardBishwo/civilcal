<?php
// themes/default/views/calculators/math/area.php
// PREMIUM AREA CALCULATOR
?>

<!-- Load Calculators CSS -->
<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<!-- Load Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="areaCalculator()">
    
    <!-- Animated Background -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] right-[-10%] w-[600px] h-[600px] bg-primary/20 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-[600px] h-[600px] bg-secondary/20 rounded-full blur-[120px] animate-float" style="animation-delay: 1s;"></div>
    </div>

    <div class="calc-container">
        
        <!-- Breadcrumb -->
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/') ?>" class="hover:text-white transition"><i class="fas fa-home"></i></a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Area Calculator</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-square"></i>
                <span>MATHEMATICS</span>
            </div>
            
            <h1 class="calc-title">
                Area <span class="text-gradient">Calculator</span>
            </h1>
            <p class="calc-subtitle max-w-2xl mx-auto">
                Calculate the area of various geometric shapes with precision. Choose your shape and enter the dimensions.
            </p>
        </div>

        <!-- Main Calculator Card -->
        <div class="calc-grid mb-12">
            
            <!-- Input Section -->
            <div class="calc-card animate-scale-in">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary/20 to-secondary/20 border border-primary/30 flex items-center justify-center">
                        <i class="fas fa-edit text-primary"></i>
                    </div>
                    <span>Input</span>
                </h2>

                <!-- Shape Selector -->
                <div class="calc-section">
                    <label class="calc-label">
                        <i class="fas fa-shapes mr-2"></i> Select Shape
                    </label>
                    <select x-model="shape" @change="resetInputs()" class="calc-select">
                        <option value="square">Square</option>
                        <option value="rectangle">Rectangle</option>
                        <option value="circle">Circle</option>
                        <option value="triangle">Triangle</option>
                    </select>
                </div>

                <!-- Dynamic Inputs Based on Shape -->
                <div x-show="shape === 'square'" class="calc-section animate-slide-up">
                    <label class="calc-label">
                        <i class="fas fa-ruler mr-2"></i> Side Length
                    </label>
                    <div class="calc-input-group">
                        <input type="number" x-model.number="side" @input="calculate()" class="calc-input" placeholder="Enter side length" step="0.01">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm">units</div>
                    </div>
                </div>

                <div x-show="shape === 'rectangle'" class="space-y-4 animate-slide-up">
                    <div class="calc-section">
                        <label class="calc-label">
                            <i class="fas fa-arrows-alt-h mr-2"></i> Length
                        </label>
                        <input type="number" x-model.number="length" @input="calculate()" class="calc-input" placeholder="Enter length" step="0.01">
                    </div>
                    <div class="calc-section">
                        <label class="calc-label">
                            <i class="fas fa-arrows-alt-v mr-2"></i> Width
                        </label>
                        <input type="number" x-model.number="width" @input="calculate()" class="calc-input" placeholder="Enter width" step="0.01">
                    </div>
                </div>

                <div x-show="shape === 'circle'" class="calc-section animate-slide-up">
                    <label class="calc-label">
                        <i class="fas fa-circle mr-2"></i> Radius
                    </label>
                    <input type="number" x-model.number="radius" @input="calculate()" class="calc-input" placeholder="Enter radius" step="0.01">
                </div>

                <div x-show="shape === 'triangle'" class="space-y-4 animate-slide-up">
                    <div class="calc-section">
                        <label class="calc-label">
                            <i class="fas fa-arrows-alt-h mr-2"></i> Base
                        </label>
                        <input type="number" x-model.number="base" @input="calculate()" class="calc-input" placeholder="Enter base" step="0.01">
                    </div>
                    <div class="calc-section">
                        <label class="calc-label">
                            <i class="fas fa-arrows-alt-v mr-2"></i> Height
                        </label>
                        <input type="number" x-model.number="height" @input="calculate()" class="calc-input" placeholder="Enter height" step="0.01">
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
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <span>Result</span>
                </h2>

                <!-- Result Display -->
                <div x-show="result !== null" class="calc-result" x-transition>
                    <div class="calc-result-label">Area</div>
                    <div class="calc-result-value" x-text="formatNumber(result)"></div>
                    <div class="calc-result-unit">square units</div>
                    
                    <!-- Copy Button -->
                    <button @click="copyResult()" class="mt-6 btn-secondary w-full">
                        <i class="fas fa-copy mr-2"></i> Copy Result
                    </button>
                </div>

                <!-- Empty State -->
                <div x-show="result === null" class="text-center py-12">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-white/5 flex items-center justify-center text-3xl text-gray-600 animate-pulse">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <p class="text-gray-400">Enter dimensions to calculate area</p>
                </div>

                <!-- Formula Display -->
                <div x-show="result !== null" class="mt-8 p-4 rounded-xl bg-white/5 border border-white/10" x-transition>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Formula Used</div>
                    <div class="font-mono text-sm text-white" x-html="getFormula()"></div>
                </div>
            </div>

        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="glass-card stagger-item">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border border-blue-500/30 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-square text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white mb-1">Square</h3>
                        <p class="text-sm text-gray-400">Area = side²</p>
                    </div>
                </div>
            </div>

            <div class="glass-card stagger-item" style="animation-delay: 0.1s;">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500/20 to-pink-500/20 border border-purple-500/30 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-circle text-purple-500 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white mb-1">Circle</h3>
                        <p class="text-sm text-gray-400">Area = πr²</p>
                    </div>
                </div>
            </div>

            <div class="glass-card stagger-item" style="animation-delay: 0.2s;">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-green-500/20 to-emerald-500/20 border border-green-500/30 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-play text-green-500 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white mb-1">Triangle</h3>
                        <p class="text-sm text-gray-400">Area = ½ × base × height</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition class="toast" style="display: none;">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>Result copied to clipboard!</span>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('areaCalculator', () => ({
        shape: 'square',
        side: null,
        length: null,
        width: null,
        radius: null,
        base: null,
        height: null,
        result: null,
        showToast: false,

        calculate() {
            switch(this.shape) {
                case 'square':
                    if (this.side) this.result = this.side * this.side;
                    break;
                case 'rectangle':
                    if (this.length && this.width) this.result = this.length * this.width;
                    break;
                case 'circle':
                    if (this.radius) this.result = Math.PI * this.radius * this.radius;
                    break;
                case 'triangle':
                    if (this.base && this.height) this.result = 0.5 * this.base * this.height;
                    break;
            }
        },

        resetInputs() {
            this.side = null;
            this.length = null;
            this.width = null;
            this.radius = null;
            this.base = null;
            this.height = null;
            this.result = null;
        },

        reset() {
            this.resetInputs();
            this.shape = 'square';
        },

        formatNumber(num) {
            return num ? num.toFixed(2) : '0.00';
        },

        getFormula() {
            const formulas = {
                'square': 'Area = side² = ' + (this.side || 0) + '² = ' + this.formatNumber(this.result),
                'rectangle': 'Area = length × width = ' + (this.length || 0) + ' × ' + (this.width || 0) + ' = ' + this.formatNumber(this.result),
                'circle': 'Area = πr² = π × ' + (this.radius || 0) + '² = ' + this.formatNumber(this.result),
                'triangle': 'Area = ½ × base × height = ½ × ' + (this.base || 0) + ' × ' + (this.height || 0) + ' = ' + this.formatNumber(this.result)
            };
            return formulas[this.shape] || '';
        },

        copyResult() {
            if (this.result) {
                navigator.clipboard.writeText(this.formatNumber(this.result));
                this.showToast = true;
                setTimeout(() => this.showToast = false, 2000);
            }
        }
    }));
});
</script>
