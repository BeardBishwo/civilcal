<?php
// themes/default/views/calculators/datetime/time.php
// PREMIUM TIME DIFFERENCE CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="timeCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[30%] left-[30%] w-[500px] h-[500px] bg-indigo-500/10 rounded-full blur-[120px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Date & Time</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-clock"></i>
                <span>CHRONOMETER</span>
            </div>
            <h1 class="calc-title">Time <span class="text-gradient">Difference</span></h1>
            <p class="calc-subtitle">Calculate the duration between two times in a day.</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Input -->
                <div class="calc-card animate-scale-in">
                    
                    <div class="space-y-6">
                        <div>
                            <label class="calc-label">Start Time</label>
                            <input type="time" x-model="startTime" @input="calculate()" class="calc-input text-2xl font-mono text-center">
                        </div>
                        <div>
                            <label class="calc-label">End Time</label>
                            <input type="time" x-model="endTime" @input="calculate()" class="calc-input text-2xl font-mono text-center">
                        </div>
                    </div>

                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up flex flex-col justify-center items-center text-center bg-gradient-to-br from-indigo-900/20 to-black border border-indigo-500/20">
                    
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold">Duration</div>
                    
                    <div class="flex items-baseline gap-2 mb-8">
                        <span class="text-6xl font-black text-white" x-text="diffHours + 'h ' + diffMins + 'm'"></span>
                    </div>

                    <div class="w-full flex justify-between px-8 py-4 bg-white/5 rounded-xl border border-white/5">
                         <div class="text-center">
                             <div class="text-lg font-bold text-white" x-text="totalHours"></div>
                             <div class="text-[10px] text-gray-500 uppercase">Total Hours</div>
                         </div>
                         <div class="w-px bg-white/10"></div>
                         <div class="text-center">
                             <div class="text-lg font-bold text-white" x-text="totalMinutes"></div>
                             <div class="text-[10px] text-gray-500 uppercase">Total Minutes</div>
                         </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('timeCalculator', () => ({
        startTime: '09:00',
        endTime: '17:00',
        
        diffHours: 0,
        diffMins: 0,
        totalHours: 0,
        totalMinutes: 0,

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.startTime || !this.endTime) return;

             const d1 = new Date(`2000-01-01T${this.startTime}`);
             let d2 = new Date(`2000-01-01T${this.endTime}`);
             
             // If d2 < d1, assume next day
             if (d2 < d1) {
                 d2.setDate(d2.getDate() + 1);
             }
             
             const diffMs = d2 - d1;
             
             this.totalMinutes = Math.floor(diffMs / 60000);
             this.totalHours = (diffMs / 3600000).toFixed(2);
             
             this.diffHours = Math.floor(diffMs / 3600000);
             this.diffMins = Math.floor((diffMs % 3600000) / 60000);
        }
    }));
});
</script>
