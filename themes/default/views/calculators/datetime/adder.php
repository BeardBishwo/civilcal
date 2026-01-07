<?php
// themes/default/views/calculators/datetime/adder.php
// PREMIUM DATE ADDER
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="dateAdderCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] left-[10%] w-[500px] h-[500px] bg-red-500/10 rounded-full blur-[120px] animate-float"></div>
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
                <i class="fas fa-calendar-plus"></i>
                <span>CALENDAR</span>
            </div>
            <h1 class="calc-title">Date <span class="text-gradient">Adder</span></h1>
            <p class="calc-subtitle">Add or subtract Years, Months, and Days from a specific date.</p>
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
                            <label class="calc-label">Operation</label>
                            <div class="grid grid-cols-2 gap-2 bg-white/5 p-1 rounded-xl border border-white/10">
                                <button @click="operation = 'add'; calculate()" :class="operation === 'add' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Add (+)</button>
                                <button @click="operation = 'sub'; calculate()" :class="operation === 'sub' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="py-2 text-sm rounded-lg transition-all font-bold">Subtract (-)</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="calc-label">Years</label>
                                <input type="number" x-model.number="years" @input="calculate()" class="calc-input" min="0">
                            </div>
                            <div>
                                <label class="calc-label">Months</label>
                                <input type="number" x-model.number="months" @input="calculate()" class="calc-input" min="0">
                            </div>
                            <div>
                                <label class="calc-label">Days</label>
                                <input type="number" x-model.number="days" @input="calculate()" class="calc-input" min="0">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Result -->
                <div class="calc-card animate-slide-up flex flex-col justify-center items-center text-center bg-gradient-to-br from-red-900/20 to-black border border-red-500/20">
                    
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold">Resulting Date</div>
                    
                    <div class="flex flex-col items-center gap-2 mb-2">
                        <span class="text-4xl font-black text-white" x-text="resultDate"></span>
                        <span class="text-xl font-bold text-red-400" x-text="resultDay"></span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dateAdderCalculator', () => ({
        startDate: new Date().toISOString().split('T')[0],
        operation: 'add',
        years: 0,
        months: 0,
        days: 0,
        
        resultDate: '---',
        resultDay: '---',

        init() {
            this.calculate();
        },

        calculate() {
             if (!this.startDate) return;

             const date = new Date(this.startDate);
             const sign = this.operation === 'add' ? 1 : -1;

             // Add/Sub Years
             date.setFullYear(date.getFullYear() + (this.years * sign));
             
             // Add/Sub Months
             date.setMonth(date.getMonth() + (this.months * sign));
             
             // Add/Sub Days
             date.setDate(date.getDate() + (this.days * sign));

             this.resultDate = date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
             this.resultDay = date.toLocaleDateString('en-US', { weekday: 'long' });
        }
    }));
});
</script>
