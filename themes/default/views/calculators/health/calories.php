<?php
// themes/default/views/calculators/health/calories.php
// PREMIUM CALORIE CALCULATOR (TDEE)
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="calorieCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[10%] right-[30%] w-[600px] h-[600px] bg-orange-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Calorie Calculator</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-utensils"></i>
                <span>NUTRITION</span>
            </div>
            <h1 class="calc-title">Daily <span class="text-gradient">Calories</span></h1>
            <p class="calc-subtitle">Calculate your Total Daily Energy Expenditure (TDEE) and plan your diet.</p>
        </div>

        <div class="calc-grid max-w-5xl mx-auto">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                
                <!-- Inputs -->
                <div class="calc-card animate-scale-in">
                    <h3 class="text-lg font-bold text-white mb-6">Personal Details</h3>
                    
                    <div class="space-y-6">
                        <div>
                             <label class="calc-label">Gender</label>
                             <div class="grid grid-cols-2 gap-4">
                                <button @click="gender = 'male'; calculate()" 
                                    :class="gender === 'male' ? 'bg-primary text-white ring-2 ring-primary ring-offset-2 ring-offset-[#0F172A]' : 'bg-white/5 text-gray-400 hover:bg-white/10'"
                                    class="p-3 rounded-lg transition-all font-bold">Male</button>
                                <button @click="gender = 'female'; calculate()" 
                                    :class="gender === 'female' ? 'bg-primary text-white ring-2 ring-primary ring-offset-2 ring-offset-[#0F172A]' : 'bg-white/5 text-gray-400 hover:bg-white/10'"
                                    class="p-3 rounded-lg transition-all font-bold">Female</button>
                             </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-1">
                                <label class="calc-label text-xs">Age</label>
                                <input type="number" x-model.number="age" @input="calculate()" class="calc-input text-center px-1">
                            </div>
                            <div class="col-span-1">
                                <label class="calc-label text-xs">Height (cm)</label>
                                <input type="number" x-model.number="height" @input="calculate()" class="calc-input text-center px-1">
                            </div>
                            <div class="col-span-1">
                                <label class="calc-label text-xs">Weight (kg)</label>
                                <input type="number" x-model.number="weight" @input="calculate()" class="calc-input text-center px-1">
                            </div>
                        </div>

                         <div>
                            <label class="calc-label">Activity Level</label>
                            <select x-model.number="activity" @change="calculate()" class="calc-input w-full">
                                <option value="1.2">Sedentary (Little or no exercise)</option>
                                <option value="1.375">Lightly Active (1-3 days/week)</option>
                                <option value="1.55">Moderately Active (3-5 days/week)</option>
                                <option value="1.725">Very Active (6-7 days/week)</option>
                                <option value="1.9">Super Active (Physical job/training)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div class="animate-slide-up space-y-6">
                    
                    <!-- Main TDEE -->
                    <div class="glass-card p-6 text-center border-t-4 border-t-primary">
                        <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold">Maintenance Calories</div>
                        <div class="flex items-center justify-center gap-2">
                             <span class="text-5xl font-black text-white" x-text="tdee.toLocaleString()"></span>
                             <span class="text-lg text-primary font-bold self-end mb-2">kcal</span>
                        </div>
                    </div>

                    <!-- Goals grid -->
                    <div class="grid grid-cols-1 gap-4">
                        
                        <!-- Weight Loss -->
                        <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                            <h4 class="text-white font-bold mb-3 flex items-center gap-2"><i class="fas fa-arrow-down text-red-400"></i> Weight Loss</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center bg-black/20 p-2 rounded-lg">
                                    <span class="text-gray-400 text-sm">Light (-0.25kg/wk)</span>
                                    <span class="font-bold text-white"><span x-text="(tdee - 250).toLocaleString()"></span> kcal</span>
                                </div>
                                 <div class="flex justify-between items-center bg-black/20 p-2 rounded-lg border-l-2 border-primary">
                                    <span class="text-gray-300 text-sm">Normal (-0.5kg/wk)</span>
                                    <span class="font-bold text-primary"><span x-text="(tdee - 500).toLocaleString()"></span> kcal</span>
                                </div>
                                 <div class="flex justify-between items-center bg-black/20 p-2 rounded-lg">
                                    <span class="text-gray-400 text-sm">Extreme (-1kg/wk)</span>
                                    <span class="font-bold text-red-400"><span x-text="(tdee - 1000).toLocaleString()"></span> kcal</span>
                                </div>
                            </div>
                        </div>

                         <!-- Weight Gain -->
                        <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                            <h4 class="text-white font-bold mb-3 flex items-center gap-2"><i class="fas fa-arrow-up text-green-400"></i> Weight Gain</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center bg-black/20 p-2 rounded-lg">
                                    <span class="text-gray-400 text-sm">Light (+0.25kg/wk)</span>
                                    <span class="font-bold text-white"><span x-text="(tdee + 250).toLocaleString()"></span> kcal</span>
                                </div>
                                 <div class="flex justify-between items-center bg-black/20 p-2 rounded-lg border-l-2 border-green-500">
                                    <span class="text-gray-300 text-sm">Normal (+0.5kg/wk)</span>
                                    <span class="font-bold text-green-400"><span x-text="(tdee + 500).toLocaleString()"></span> kcal</span>
                                </div>
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
    Alpine.data('calorieCalculator', () => ({
        gender: 'male',
        age: 28,
        height: 178,
        weight: 75,
        activity: 1.55,
        tdee: 0,

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.age || !this.height || !this.weight) return;

             // BMR (Mifflin-St Jeor)
             let bmr = (10 * this.weight) + (6.25 * this.height) - (5 * this.age);
             if (this.gender === 'male') bmr += 5;
             else bmr -= 161;

             this.tdee = Math.round(bmr * this.activity);
        }
    }));
});
</script>
