<?php
// themes/default/views/calculators/datetime/duration.php
// PREMIUM DURATION CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="durationCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] right-[10%] w-[500px] h-[500px] bg-blue-500/10 rounded-full blur-[120px] animate-float"></div>
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
                <span>Timeline</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Date <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Duration</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate the duration between two dates in years, months, and days.
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
                        
                         <div class="bg-white/5 p-4 rounded-xl border border-white/10 text-xs text-gray-400 flex gap-2">
                             <i class="fas fa-info-circle mt-1 text-blue-400"></i>
                             <p>This calculator computes the exact timespan, including leap years.</p>
                         </div>
                    </div>
                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up flex flex-col justify-center bg-gradient-to-br from-blue-900/20 to-black border border-blue-500/20">
                    
                    <div class="text-center mb-8">
                        <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold">Total Duration</div>
                        <div class="text-3xl font-black text-white leading-tight" x-html="resultDuration"></div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center border-t border-white/10 pt-6">
                        <div>
                             <div class="text-xl font-bold text-blue-400" x-text="totalDays"></div>
                             <div class="text-[10px] text-gray-500 uppercase font-bold">Total Days</div>
                        </div>
                        <div>
                             <div class="text-xl font-bold text-purple-400" x-text="totalWeeks"></div>
                             <div class="text-[10px] text-gray-500 uppercase font-bold">Weeks</div>
                        </div>
                         <div>
                             <div class="text-xl font-bold text-green-400" x-text="totalHours"></div>
                             <div class="text-[10px] text-gray-500 uppercase font-bold">Hours</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('durationCalculator', () => ({
        startDate: new Date().toISOString().split('T')[0],
        endDate: new Date().toISOString().split('T')[0],
        
        resultDuration: '0 days',
        totalDays: 0,
        totalWeeks: 0,
        totalHours: 0,

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.startDate || !this.endDate) return;

             const start = new Date(this.startDate);
             const end = new Date(this.endDate);
             
             // Time diff
             const diffMs = Math.abs(end - start);
             const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
             
             this.totalDays = days.toLocaleString();
             this.totalWeeks = (days / 7).toFixed(1);
             this.totalHours = (days * 24).toLocaleString();
             
             // YMD calc
             // Simplistic approach for YMD
             // Using logic to respect accurate calendar months
             let tempDate = new Date(start);
             let y = 0, m = 0, d = 0;
             const target = new Date(end);
             
             if (tempDate > target) {
                 this.resultDuration = "End date is before start date";
                 return;
             }
             
             // Years
             while(true) {
                 let nextYear = new Date(tempDate);
                 nextYear.setFullYear(tempDate.getFullYear() + 1);
                 if(nextYear > target) break;
                 tempDate = nextYear;
                 y++;
             }
             
             // Months
             while(true) {
                 let nextMonth = new Date(tempDate);
                 nextMonth.setMonth(tempDate.getMonth() + 1);
                 if(nextMonth > target) break;
                 tempDate = nextMonth;
                 m++;
             }
             
             // Days
             d = Math.round((target - tempDate) / (1000 * 60 * 60 * 24));
             
             let parts = [];
             if (y > 0) parts.push(`<span class="text-white">${y}</span> Years`);
             if (m > 0) parts.push(`<span class="text-white">${m}</span> Months`);
             if (d > 0) parts.push(`<span class="text-white">${d}</span> Days`);
             if (parts.length === 0) parts.push("0 Days");
             
             this.resultDuration = parts.join(', ');
        }
    }));
});
</script>
