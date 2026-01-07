<?php
// themes/default/views/calculators/math/bmi.php
// PREMIUM BMI CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="bmiCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[10%] right-[10%] w-[300px] h-[300px] bg-green-500/10 rounded-full blur-[80px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">BMI Calculator</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-heartbeat"></i>
                <span>HEALTH</span>
            </div>
            <h1 class="calc-title">BMI <span class="text-gradient">Analyzer</span></h1>
            <p class="calc-subtitle">Check your Body Mass Index and understand your weight category.</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <!-- Inputs -->
            <div class="calc-card animate-scale-in">
                
                <div class="flex justify-center mb-8">
                    <div class="bg-white/5 p-1 rounded-lg border border-white/10 flex">
                        <button @click="unit = 'metric'; calculate()" :class="unit === 'metric' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="px-6 py-2 rounded-md transition-all font-bold">Metric (kg/cm)</button>
                        <button @click="unit = 'imperial'; calculate()" :class="unit === 'imperial' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="px-6 py-2 rounded-md transition-all font-bold">Imperial (lb/in)</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Weight -->
                    <div>
                        <label class="calc-label" x-text="unit === 'metric' ? 'Weight (kg)' : 'Weight (lb)'"></label>
                        <input type="number" x-model.number="weight" @input="calculate()" class="calc-input text-center text-3xl" placeholder="0">
                    </div>
                    <!-- Height -->
                    <div>
                        <label class="calc-label" x-text="unit === 'metric' ? 'Height (cm)' : 'Height (in)'"></label>
                        <input type="number" x-model.number="height" @input="calculate()" class="calc-input text-center text-3xl" placeholder="0">
                    </div>
                </div>

                <!-- Result -->
                <div x-show="bmi" class="text-center animate-slide-up">
                    <div class="relative inline-flex items-center justify-center w-48 h-48 rounded-full border-8 border-white/5 bg-white/5 mb-6">
                        <div class="text-center z-10">
                            <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">BMI Score</div>
                            <div class="text-5xl font-black text-white" x-text="bmi"></div>
                        </div>
                        <!-- Color Ring (Dynamic Border Color could be cool, but simplistic for now) -->
                        <div class="absolute inset-0 rounded-full border-8 border-transparent" :class="colorClass" style="transform: rotate(-45deg); border-top-color: currentColor; opacity: 0.5;"></div>
                    </div>

                    <div class="text-2xl font-bold mb-2 transition-colors duration-300" :class="textColor" x-text="category"></div>
                    <p class="text-gray-400 max-w-md mx-auto text-sm" x-text="message"></p>
                </div>
                
                <!-- Simple Scale -->
                <div x-show="bmi" class="mt-8 h-2 bg-gradient-to-r from-blue-400 via-green-400 to-red-500 rounded-full relative w-full opacity-50">
                     <!-- Marker could go here based on percentage -->
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bmiCalculator', () => ({
        unit: 'metric',
        weight: 70,
        height: 175,
        bmi: null,
        category: '',
        message: '',
        colorClass: '', // text-blue-400 etc
        textColor: '',

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.weight || !this.height) {
                this.bmi = null; return;
            }

            let bmiVal = 0;

            if (this.unit === 'metric') {
                // kg / (m * m)
                const hM = this.height / 100;
                bmiVal = this.weight / (hM * hM);
            } else {
                // (lb / in*in) * 703
                bmiVal = (this.weight / (this.height * this.height)) * 703;
            }

            this.bmi = bmiVal.toFixed(1);

            // Categorize
            if (this.bmi < 18.5) {
                this.category = "Underweight";
                this.colorClass = "border-blue-400";
                this.textColor = "text-blue-400";
                this.message = "You are in the underweight range.";
            } else if (this.bmi < 25) {
                this.category = "Normal Weight";
                this.colorClass = "border-green-400";
                this.textColor = "text-green-400";
                this.message = "You are in the healthy weight range. Keep it up!";
            } else if (this.bmi < 30) {
                this.category = "Overweight";
                this.colorClass = "border-yellow-400";
                this.textColor = "text-yellow-400";
                this.message = "You are in the overweight range.";
            } else {
                this.category = "Obese";
                this.colorClass = "border-red-500";
                this.textColor = "text-red-500";
                this.message = "You are in the obese range. Please consult a healthcare provider.";
            }
        }
    }));
});
</script>
