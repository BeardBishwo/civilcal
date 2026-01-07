<?php
// themes/default/views/calculators/health/body_fat.php
// PREMIUM BODY FAT CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="bodyFatCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[30%] left-[20%] w-[500px] h-[500px] bg-indigo-500/10 rounded-full blur-[100px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
             <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Body Fat</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-child"></i>
                <span>COMPOSITION</span>
            </div>
            <h1 class="calc-title">Body Fat <span class="text-gradient">Percentage</span></h1>
            <p class="calc-subtitle">Estimate body fat using the US Navy Method based on body measurements.</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Input -->
                <div class="calc-card animate-scale-in">
                    
                     <div class="bg-white/5 p-1 rounded-xl border border-white/10 inline-flex w-full mb-8">
                        <button @click="gender = 'male'; calculate()" :class="gender === 'male' ? 'bg-indigo-500 text-white shadow' : 'text-gray-400 hover:text-white'" class="flex-1 py-3 text-sm rounded-lg transition-all font-bold">Male</button>
                        <button @click="gender = 'female'; calculate()" :class="gender === 'female' ? 'bg-indigo-500 text-white shadow' : 'text-gray-400 hover:text-white'" class="flex-1 py-3 text-sm rounded-lg transition-all font-bold">Female</button>
                    </div>

                    <div class="space-y-5">
                        <!-- Measurements -->
                         <div class="grid grid-cols-1 gap-4">
                             <div>
                                <label class="calc-label">Height (cm)</label>
                                <input type="number" x-model.number="height" @input="calculate()" class="calc-input text-lg" placeholder="175">
                            </div>
                             <div>
                                <label class="calc-label">Neck (cm)</label>
                                <input type="number" x-model.number="neck" @input="calculate()" class="calc-input text-lg" placeholder="38">
                            </div>
                             <div>
                                <label class="calc-label">Waist (cm)</label>
                                <input type="number" x-model.number="waist" @input="calculate()" class="calc-input text-lg" placeholder="85">
                            </div>
                            <!-- Hip only for female -->
                            <div x-show="gender === 'female'" x-transition>
                                <label class="calc-label">Hip (cm)</label>
                                <input type="number" x-model.number="hip" @input="calculate()" class="calc-input text-lg" placeholder="95">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up flex flex-col justify-center items-center text-center bg-indigo-900/10 border border-indigo-500/20">
                    
                     <div class="flex items-center justify-center relative w-48 h-48 mb-6">
                         <!-- Circular Progress Placeholder or just text? Let's use simple text with ring -->
                         <div class="absolute inset-0 rounded-full border-8 border-white/5"></div>
                         <div class="absolute inset-0 rounded-full border-8 border-indigo-500 border-l-transparent rotate-45"></div>
                         
                         <div class="flex flex-col items-center z-10">
                             <div class="text-5xl font-black text-white" x-text="bodyFat"></div>
                             <div class="text-xl text-indigo-400 font-bold">%</div>
                         </div>
                     </div>

                     <div class="text-2xl font-bold text-white mb-2" x-text="category"></div>
                     <div class="text-sm text-gray-400 max-w-xs leading-relaxed">
                         Estimated body fat percentage based on circumferential measurements.
                     </div>

                     <div class="mt-8 w-full p-4 bg-white/5 rounded-xl border border-white/5 text-left text-xs text-gray-400 space-y-1">
                         <div class="flex justify-between"><span>Esssential Fat:</span> <span class="text-white" x-text="gender==='male'?'2-5%':'10-13%'"></span></div>
                         <div class="flex justify-between"><span>Athletes:</span> <span class="text-white" x-text="gender==='male'?'6-13%':'14-20%'"></span></div>
                         <div class="flex justify-between"><span>Fitness:</span> <span class="text-white" x-text="gender==='male'?'14-17%':'21-24%'"></span></div>
                         <div class="flex justify-between"><span>Average:</span> <span class="text-white" x-text="gender==='male'?'18-24%':'25-31%'"></span></div>
                     </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bodyFatCalculator', () => ({
        gender: 'male',
        height: 175,
        neck: 38,
        waist: 85,
        hip: 95,
        
        bodyFat: 0,
        category: '',

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.height || !this.neck || !this.waist) return;
             if (this.gender === 'female' && !this.hip) return;

             let bf = 0;

             if (this.gender === 'male') {
                 // 495 / (1.0324 - 0.19077 * log10(waist - neck) + 0.15456 * log10(height)) - 450
                 if (this.waist - this.neck <= 0) return; // Prevent log error
                 bf = 495 / (1.0324 - 0.19077 * Math.log10(this.waist - this.neck) + 0.15456 * Math.log10(this.height)) - 450;
             } else {
                 // 495 / (1.29579 - 0.35004 * log10(waist + hip - neck) + 0.22100 * log10(height)) - 450
                  if (this.waist + this.hip - this.neck <= 0) return;
                 bf = 495 / (1.29579 - 0.35004 * Math.log10(this.waist + this.hip - this.neck) + 0.22100 * Math.log10(this.height)) - 450;
             }

             if (bf < 0) bf = 0;
             this.bodyFat = bf.toFixed(1);
             this.updateCategory(bf);
        },

        updateCategory(bf) {
            let cat = '';
            if (this.gender === 'male') {
                if (bf < 6) cat = 'Essential Fat';
                else if (bf < 14) cat = 'Athlete';
                else if (bf < 18) cat = 'Fitness';
                else if (bf < 25) cat = 'Average';
                else cat = 'Obese';
            } else {
                if (bf < 14) cat = 'Essential Fat';
                else if (bf < 21) cat = 'Athlete';
                else if (bf < 25) cat = 'Fitness';
                else if (bf < 32) cat = 'Average';
                else cat = 'Obese';
            }
            this.category = cat;
        }

    }));
});
</script>
