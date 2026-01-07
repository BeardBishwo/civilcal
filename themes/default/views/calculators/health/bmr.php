<?php
// themes/default/views/calculators/health/bmr.php
// PREMIUM BMR CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="bmrCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute bottom-[20%] left-[10%] w-[500px] h-[500px] bg-rose-500/10 rounded-full blur-[100px] animate-float"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">BMR Calculator</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-fire"></i>
                <span>energy</span>
            </div>
            <h1 class="calc-title">Basal Metabolic <span class="text-gradient">Rate</span></h1>
            <p class="calc-subtitle">Calculate the number of calories your body needs to accomplish its most basic life-sustaining functions.</p>
        </div>

        <div class="calc-grid max-w-3xl mx-auto">
            
            <div class="calc-card animate-scale-in">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Column 1 -->
                    <div class="space-y-6">
                        <div>
                            <label class="calc-label">Gender</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button @click="gender = 'male'; calculate()" 
                                    :class="gender === 'male' ? 'bg-primary text-white border-primary' : 'bg-white/5 text-gray-400 border-white/10 hover:border-primary/50'"
                                    class="p-3 rounded-lg border transition-all flex items-center justify-center gap-2 font-bold">
                                    <i class="fas fa-mars"></i> Male
                                </button>
                                <button @click="gender = 'female'; calculate()" 
                                    :class="gender === 'female' ? 'bg-pink-500 text-white border-pink-500' : 'bg-white/5 text-gray-400 border-white/10 hover:border-pink-500/50'"
                                    class="p-3 rounded-lg border transition-all flex items-center justify-center gap-2 font-bold">
                                    <i class="fas fa-venus"></i> Female
                                </button>
                            </div>
                        </div>

                         <div>
                            <label class="calc-label">Age</label>
                            <input type="number" x-model.number="age" @input="calculate()" class="calc-input text-center" placeholder="25">
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="space-y-6">
                         <div>
                            <label class="calc-label">Height (cm)</label>
                            <input type="number" x-model.number="height" @input="calculate()" class="calc-input text-center" placeholder="175">
                        </div>

                        <div>
                            <label class="calc-label">Weight (kg)</label>
                            <input type="number" x-model.number="weight" @input="calculate()" class="calc-input text-center" placeholder="70">
                        </div>
                    </div>
                </div>

                <!-- Result -->
                <div class="mt-8 animate-slide-up">
                    <div class="bg-gradient-to-br from-gray-800 to-black rounded-2xl p-8 border border-white/10 relative overflow-hidden text-center">
                         <div class="absolute top-0 right-0 w-32 h-32 bg-primary/20 blur-[50px] rounded-full pointer-events-none"></div>
                         
                         <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Your Daily BMR</h3>
                         
                         <div class="flex items-end justify-center gap-2 mb-2">
                             <span class="text-6xl font-black text-white" x-text="bmr.toLocaleString()"></span>
                             <span class="text-xl text-primary font-bold mb-3">kcal</span>
                         </div>
                         
                         <p class="text-xs text-gray-500">Calories burned at complete rest</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bmrCalculator', () => ({
        gender: 'male',
        age: 25,
        height: 175,
        weight: 70,
        bmr: 0,

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.age || !this.height || !this.weight) return;

             // Mifflin-St Jeor
             let val = (10 * this.weight) + (6.25 * this.height) - (5 * this.age);
             
             if (this.gender === 'male') val += 5;
             else val -= 161;

             this.bmr = Math.round(val);
        }
    }));
});
</script>
