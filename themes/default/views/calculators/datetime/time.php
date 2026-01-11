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

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Chronometer</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Time <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Difference</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate the duration between two times in a day.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
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
