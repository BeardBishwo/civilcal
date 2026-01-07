<?php
// themes/default/views/calculators/health/bmi.php
// PREMIUM HEALTH BMI CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="healthBmiCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] right-[10%] w-[500px] h-[500px] bg-sky-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Health BMI</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-heart-pulse"></i>
                <span>HEALTH MONITOR</span>
            </div>
            <h1 class="calc-title">Body Mass <span class="text-gradient">Index</span></h1>
            <p class="calc-subtitle">Check your health status based on height and weight metrics.</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Input Section -->
                <div class="calc-card animate-scale-in flex flex-col justify-center">
                    
                    <div class="bg-white/5 p-1 rounded-xl border border-white/10 inline-flex w-full mb-8">
                        <button @click="unit = 'metric'" :class="unit === 'metric' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="flex-1 py-3 text-sm rounded-lg transition-all font-bold">Metric (cm/kg)</button>
                        <button @click="unit = 'imperial'" :class="unit === 'imperial' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="flex-1 py-3 text-sm rounded-lg transition-all font-bold">Imperial (ft/lbs)</button>
                    </div>

                    <div class="space-y-6">
                        <!-- Weight -->
                        <div>
                            <label class="calc-label">Weight</label>
                            <div class="relative">
                                <input type="number" x-model.number="weight" @input="calculate()" class="calc-input pr-16 text-2xl" placeholder="70">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold" x-text="unit === 'metric' ? 'kg' : 'lbs'"></span>
                            </div>
                        </div>

                        <!-- Height -->
                        <div>
                            <label class="calc-label">Height</label>
                            
                            <div x-show="unit === 'metric'" class="relative">
                                <input type="number" x-model.number="heightCm" @input="calculate()" class="calc-input pr-16 text-2xl" placeholder="175">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">cm</span>
                            </div>

                            <div x-show="unit === 'imperial'" class="grid grid-cols-2 gap-4">
                                <div class="relative">
                                    <input type="number" x-model.number="heightFt" @input="calculate()" class="calc-input pr-12 text-2xl" placeholder="5">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">ft</span>
                                </div>
                                <div class="relative">
                                    <input type="number" x-model.number="heightIn" @input="calculate()" class="calc-input pr-12 text-2xl" placeholder="9">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">in</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Result Section -->
                <div class="calc-card animate-slide-up flex flex-col justify-center items-center text-center relative overflow-hidden"
                     :class="categoryColorClass">
                    
                    <div class="absolute inset-0 opacity-10 blur-xl transition-colors duration-500" :class="bgClass"></div>
                    
                    <div class="relative z-10">
                        <div class="text-sm text-gray-300 uppercase tracking-widest mb-4 font-bold">Your BMI</div>
                        <div class="text-7xl font-black mb-2 transition-colors duration-300" :class="textClass" x-text="bmi"></div>
                        <div class="text-2xl font-bold text-white mb-8" x-text="category"></div>
                    </div>

                    <!-- Visual Scale -->
                    <div class="relative w-full h-4 bg-gray-700 rounded-full mb-6 mt-4 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 via-green-400 via-yellow-400 to-red-500"></div>
                        <!-- Marker -->
                        <div class="absolute top-0 bottom-0 w-2 bg-white border-2 border-black rounded-full shadow-lg transition-all duration-500"
                             :style="`left: ${markerPos}%`"></div>
                    </div>
                    
                    <div class="w-full flex justify-between text-xs text-gray-400 font-mono px-1">
                        <span>15</span>
                        <span>18.5</span>
                        <span>25</span>
                        <span>30</span>
                        <span>40</span>
                    </div>

                    <div class="mt-8 p-4 bg-black/20 rounded-xl w-full text-left border border-white/5">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle" :class="textClass"></i>
                            <p class="text-sm text-gray-300 leading-relaxed" x-text="message"></p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('healthBmiCalculator', () => ({
        unit: 'metric',
        weight: 70,
        heightCm: 175,
        heightFt: 5,
        heightIn: 9,
        
        bmi: 0,
        category: '',
        message: '',
        markerPos: 0,
        
        // Dynamic classes
        bgClass: 'bg-green-500',
        textClass: 'text-green-400',
        categoryColorClass: 'border border-green-500/30',

        init() {
            this.calculate();
        },

        calculate() {
             let w = 0; // kg
             let h = 0; // m

             if (this.unit === 'metric') {
                 w = this.weight;
                 h = this.heightCm / 100;
             } else {
                 w = this.weight * 0.453592;
                 h = ((this.heightFt * 12) + this.heightIn) * 0.0254;
             }

             if (h > 0 && w > 0) {
                 const bmiVal = w / (h * h);
                 this.bmi = bmiVal.toFixed(1);
                 this.updateStatus(bmiVal);
             } else {
                 this.bmi = '0.0';
                 this.markerPos = 0;
             }
        },

        updateStatus(bmi) {
            // Scale logic: 15 to 40 range map to 0-100%
            // actually simpler: map 0 to 45?
            // Let's ensure marker stays in bounds
            let pos = ((bmi - 15) / (40 - 15)) * 100;
            if (pos < 0) pos = 0;
            if (pos > 100) pos = 100;
            this.markerPos = pos;

            if (bmi < 18.5) {
                this.category = 'Underweight';
                this.message = 'You are in the underweight range. It is recommended to consult a doctor to check for nutritional deficiencies.';
                this.setColors('blue');
            } else if (bmi < 25) {
                this.category = 'Normal Weight';
                this.message = 'Great! You have a healthy body weight. Maintain your balanced diet and regular exercise.';
                this.setColors('green');
            } else if (bmi < 30) {
                this.category = 'Overweight';
                this.message = 'You are in the overweight range. Consider a balanced diet and increased physical activity.';
                this.setColors('yellow');
            } else {
                this.category = 'Obesity';
                this.message = 'You are in the obesity range. Highly recommended to consult a healthcare provider for a personalized plan.';
                this.setColors('red');
            }
        },

        setColors(color) {
            const colors = {
                blue: { bg: 'bg-blue-500', text: 'text-blue-400', border: 'border border-blue-500/30' },
                green: { bg: 'bg-green-500', text: 'text-green-400', border: 'border border-green-500/30' },
                yellow: { bg: 'bg-yellow-500', text: 'text-yellow-400', border: 'border border-yellow-500/30' },
                red: { bg: 'bg-red-500', text: 'text-red-400', border: 'border border-red-500/30' },
            };
            this.bgClass = colors[color].bg;
            this.textClass = colors[color].text;
            this.categoryColorClass = colors[color].border;
        }
    }));
});
</script>
