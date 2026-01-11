<?php
// themes/default/views/calculators/datetime/workdays.php
// PREMIUM BUSINESS DAYS CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="workDaysCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute bottom-[20%] right-[10%] w-[500px] h-[500px] bg-orange-500/10 rounded-full blur-[120px] animate-float"></div>
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
                <span>Business</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Work Days <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Calculator</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate number of business days between two dates (excluding weekends).
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Input -->
                <div class="calc-card animate-scale-in">
                    
                    <div class="space-y-6">
                        <div>
                            <label class="calc-label">Start Date</label>
                            <input type="date" x-model="startDate" @input="calculate()" class="calc-input">
                        </div>
                        <div>
                            <label class="calc-label">End Date</label>
                            <input type="date" x-model="endDate" @input="calculate()" class="calc-input">
                        </div>
                        
                         <div class="bg-orange-500/10 p-4 rounded-xl border border-orange-500/20 text-xs text-orange-200 flex gap-2">
                             <i class="fas fa-exclamation-circle mt-1"></i>
                             <p>Counts days excluding Saturday and Sunday. Holidays are not automatically excluded in this version.</p>
                         </div>
                    </div>
                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up flex flex-col justify-center items-center text-center bg-gradient-to-br from-orange-900/20 to-black border border-orange-500/20">
                    
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold">Business Days</div>
                    
                    <div class="flex items-baseline gap-2 mb-2">
                        <span class="text-8xl font-black text-white" x-text="workDays"></span>
                        <span class="text-xl font-bold text-orange-400">days</span>
                    </div>
                    
                    <div class="text-xs text-gray-500 mt-4 font-mono">
                        Including start, Excluding weekend
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('workDaysCalculator', () => ({
        startDate: new Date().toISOString().split('T')[0],
        endDate: new Date().toISOString().split('T')[0],
        workDays: 0,

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.startDate || !this.endDate) return;

             const start = new Date(this.startDate);
             const end = new Date(this.endDate);
             
             if (start > end) {
                 this.workDays = 0;
                 return;
             }

             let count = 0;
             let cur = new Date(start);
             
             while (cur <= end) {
                 const day = cur.getDay();
                 // 0 = Sunday, 6 = Saturday
                 if (day !== 0 && day !== 6) {
                     count++;
                 }
                 cur.setDate(cur.getDate() + 1);
             }
             
             this.workDays = count;
        }
    }));
});
</script>
