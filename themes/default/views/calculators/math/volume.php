<?php
// themes/default/views/calculators/math/volume.php
// PREMIUM VOLUME CALCULATOR
?>

<!-- Load Calculators CSS -->
<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<!-- Load Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Three.js for 3D Previews (Lazy loaded) -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<div class="bg-background min-h-screen relative overflow-hidden" x-data="volumeCalculator()">
    
    <!-- Animated Background -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-primary/10 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-secondary/10 rounded-full blur-[120px] animate-float" style="animation-delay: 1.5s;"></div>
    </div>

    <div class="calc-container">
        
        <!-- Breadcrumb -->
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/') ?>" class="hover:text-white transition"><i class="fas fa-home"></i></a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Volume Calculator</li>
            </ol>
        </nav>

        <!-- Header -->
        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Mathematics</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Volume <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Engine</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Compute the volume of complex 3D shapes. Enter dimensions to get instant results with formula visualization.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <!-- Main Calculator Card -->
        <div class="calc-grid mb-12">
            
            <!-- Input Section -->
            <div class="calc-card animate-scale-in">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary/20 to-secondary/20 border border-primary/30 flex items-center justify-center">
                        <i class="fas fa-sliders-h text-primary"></i>
                    </div>
                    <span>Dimensions</span>
                </h2>

                <!-- Shape Selector -->
                <div class="calc-section">
                    <label class="calc-label">
                        <i class="fas fa-cubes mr-2"></i> Select 3D Shape
                    </label>
                    <select x-model="shape" @change="resetInputs()" class="calc-select">
                        <option value="cube">Cube</option>
                        <option value="sphere">Sphere</option>
                        <option value="cylinder">Cylinder</option>
                        <option value="cone">Cone</option>
                        <option value="cuboid">Rectangular Prism (Cuboid)</option>
                    </select>
                </div>

                <!-- Cube Inputs -->
                <div x-show="shape === 'cube'" class="calc-section animate-slide-up">
                    <label class="calc-label">
                        <i class="fas fa-ruler-horizontal mr-2"></i> Edge Length
                    </label>
                    <div class="calc-input-group">
                        <input type="number" x-model.number="edge" @input="calculate()" class="calc-input" placeholder="Enter edge length" step="0.01">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm">units</div>
                    </div>
                </div>

                <!-- Sphere Inputs -->
                <div x-show="shape === 'sphere'" class="calc-section animate-slide-up">
                    <label class="calc-label">
                        <i class="fas fa-circle mr-2"></i> Radius
                    </label>
                    <div class="calc-input-group">
                        <input type="number" x-model.number="radius" @input="calculate()" class="calc-input" placeholder="Enter radius" step="0.01">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm">units</div>
                    </div>
                </div>

                <!-- Cylinder Inputs -->
                <div x-show="shape === 'cylinder'" class="space-y-4 animate-slide-up">
                    <div class="calc-section">
                        <label class="calc-label">
                            <i class="fas fa-circle mr-2"></i> Radius
                        </label>
                        <input type="number" x-model.number="radius" @input="calculate()" class="calc-input" placeholder="Enter radius" step="0.01">
                    </div>
                    <div class="calc-section">
                        <label class="calc-label">
                            <i class="fas fa-arrows-alt-v mr-2"></i> Height
                        </label>
                        <input type="number" x-model.number="height" @input="calculate()" class="calc-input" placeholder="Enter height" step="0.01">
                    </div>
                </div>

                <!-- Cone Inputs -->
                <div x-show="shape === 'cone'" class="space-y-4 animate-slide-up">
                    <div class="calc-section">
                        <label class="calc-label">
                            <i class="fas fa-circle mr-2"></i> Base Radius
                        </label>
                        <input type="number" x-model.number="radius" @input="calculate()" class="calc-input" placeholder="Enter radius" step="0.01">
                    </div>
                    <div class="calc-section">
                        <label class="calc-label">
                            <i class="fas fa-arrows-alt-v mr-2"></i> Height
                        </label>
                        <input type="number" x-model.number="height" @input="calculate()" class="calc-input" placeholder="Enter height" step="0.01">
                    </div>
                </div>

                <!-- Cuboid Inputs -->
                <div x-show="shape === 'cuboid'" class="space-y-4 animate-slide-up">
                    <div class="calc-section">
                        <label class="calc-label">Length</label>
                        <input type="number" x-model.number="length" @input="calculate()" class="calc-input" placeholder="Enter length" step="0.01">
                    </div>
                    <div class="calc-section">
                        <label class="calc-label">Width</label>
                        <input type="number" x-model.number="width" @input="calculate()" class="calc-input" placeholder="Enter width" step="0.01">
                    </div>
                    <div class="calc-section">
                        <label class="calc-label">Height</label>
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
                        <i class="fas fa-flask text-green-500"></i>
                    </div>
                    <span>Result</span>
                </h2>

                <!-- Live Preview (Placeholder for 3D or visualizer) -->
                <div class="mb-6 h-32 rounded-xl bg-black/20 border border-white/5 flex items-center justify-center overflow-hidden relative">
                     <!-- Simple CSS representation of shapes -->
                     <div x-show="shape === 'cube'" class="w-16 h-16 border-2 border-primary/50 bg-primary/10 transform rotate-12 transition-all duration-500" :style="'transform: scale(' + (edge ? Math.min(edge/10, 1.5) : 1) + ') rotate3d(1, 1, 1, 45deg)'"></div>
                     <div x-show="shape === 'sphere'" class="w-16 h-16 rounded-full border-2 border-secondary/50 bg-secondary/10 shadow-[inner_0_0_20px_rgba(118,75,162,0.4)] transition-all duration-500" :style="'transform: scale(' + (radius ? Math.min(radius/10, 1.5) : 1) + ')'"></div>
                     <div x-show="shape === 'cylinder'" class="w-12 h-20 border-2 border-accent/50 bg-accent/10 rounded-[100%] rounded-t-[50%/20px] rounded-b-[50%/20px] relative transition-all duration-500"></div>
                     <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-600 font-mono pointer-events-none">VISUAL PREVIEW</div>
                </div>

                <!-- Result Display -->
                <div x-show="result !== null" class="calc-result" x-transition>
                    <div class="calc-result-label">Volume</div>
                    <div class="calc-result-value" x-text="formatNumber(result)"></div>
                    <div class="calc-result-unit">cubic units</div>
                    
                    <!-- Copy Button -->
                    <button @click="copyResult()" class="mt-6 btn-secondary w-full">
                        <i class="fas fa-copy mr-2"></i> Copy Value
                    </button>
                </div>

                <!-- Empty State -->
                <div x-show="result === null" class="text-center py-8">
                    <div class="text-4xl text-gray-700 mb-2 animate-bounce-subtle">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <p class="text-gray-500">Enter dimensions to calculate</p>
                </div>

                <!-- Formula Display -->
                <div x-show="result !== null" class="mt-8 p-4 rounded-xl bg-white/5 border border-white/10" x-transition>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Formula</div>
                    <div class="font-mono text-sm text-white" x-html="getFormula()"></div>
                </div>
            </div>

        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
            <div class="glass-card stagger-item">
                <h4 class="font-bold text-primary mb-2">Cube</h4>
                <p class="text-xs text-gray-400">V = a³</p>
            </div>
            <div class="glass-card stagger-item" style="animation-delay: 0.1s;">
                <h4 class="font-bold text-secondary mb-2">Sphere</h4>
                <p class="text-xs text-gray-400">V = (4/3)πr³</p>
            </div>
            <div class="glass-card stagger-item" style="animation-delay: 0.2s;">
                <h4 class="font-bold text-accent mb-2">Cylinder</h4>
                <p class="text-xs text-gray-400">V = πr²h</p>
            </div>
            <div class="glass-card stagger-item" style="animation-delay: 0.3s;">
                <h4 class="font-bold text-yellow-500 mb-2">Cone</h4>
                <p class="text-xs text-gray-400">V = (1/3)πr²h</p>
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
    Alpine.data('volumeCalculator', () => ({
        shape: 'cube',
        edge: null,
        radius: null,
        height: null,
        length: null,
        width: null,
        result: null,
        showToast: false,

        calculate() {
            switch(this.shape) {
                case 'cube':
                    if (this.edge) this.result = Math.pow(this.edge, 3);
                    break;
                case 'sphere':
                    if (this.radius) this.result = (4/3) * Math.PI * Math.pow(this.radius, 3);
                    break;
                case 'cylinder':
                    if (this.radius && this.height) this.result = Math.PI * Math.pow(this.radius, 2) * this.height;
                    break;
                case 'cone':
                    if (this.radius && this.height) this.result = (1/3) * Math.PI * Math.pow(this.radius, 2) * this.height;
                    break;
                case 'cuboid':
                    if (this.length && this.width && this.height) this.result = this.length * this.width * this.height;
                    break;
            }
        },

        resetInputs() {
            this.edge = null;
            this.radius = null;
            this.height = null;
            this.length = null;
            this.width = null;
            this.result = null;
        },

        reset() {
            this.resetInputs();
            this.shape = 'cube';
        },

        formatNumber(num) {
            return num ? num.toLocaleString('en-US', { maximumFractionDigits: 4 }) : '0';
        },

        getFormula() {
            const pi = 'π';
            switch(this.shape) {
                case 'cube': return `V = a³ = ${this.edge || 'a'}³`;
                case 'sphere': return `V = 4/3${pi}r³ = 4/3 × ${pi} × ${this.radius || 'r'}³`;
                case 'cylinder': return `V = ${pi}r²h = ${pi} × ${this.radius || 'r'}² × ${this.height || 'h'}`;
                case 'cone': return `V = 1/3${pi}r²h = 1/3 × ${pi} × ${this.radius || 'r'}² × ${this.height || 'h'}`;
                case 'cuboid': return `V = l×w×h = ${this.length || 'l'} × ${this.width || 'w'} × ${this.height || 'h'}`;
                default: return '';
            }
        },

        copyResult() {
            if (this.result) {
                navigator.clipboard.writeText(this.result.toFixed(4));
                this.showToast = true;
                setTimeout(() => this.showToast = false, 2000);
            }
        }
    }));
});
</script>
