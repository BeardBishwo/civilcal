<?php
// themes/default/views/calculators/math/age.php
// PREMIUM AGE CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="ageCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[20%] left-[10%] w-[500px] h-[500px] bg-pink-500/10 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-[20%] right-[10%] w-[400px] h-[400px] bg-blue-500/10 rounded-full blur-[120px] animate-float" style="animation-delay: 2s;"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Age Calculator</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                <i class="fas fa-hourglass-half"></i>
                <span>DATE & TIME</span>
            </div>
            <h1 class="calc-title">Age <span class="text-gradient">Chronometer</span></h1>
            <p class="calc-subtitle">Calculate your precise age in years, months, and days.</p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="calc-card animate-scale-in">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="calc-label">Date of Birth</label>
                        <input type="date" x-model="dob" @change="calculate()" class="calc-input w-full">
                    </div>
                    <div>
                        <label class="calc-label">Calculate Age At</label>
                        <input type="date" x-model="target" @change="calculate()" class="calc-input w-full">
                    </div>
                </div>

                <!-- Main Display -->
                <div x-show="age" class="text-center animate-slide-up">
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-4">You are currently</div>
                    
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="glass-card p-4 border-b-4 border-primary">
                            <div class="text-4xl md:text-5xl font-black text-white" x-text="age.years">0</div>
                            <div class="text-xs text-gray-400 uppercase font-bold mt-1">Years</div>
                        </div>
                        <div class="glass-card p-4 border-b-4 border-secondary">
                            <div class="text-4xl md:text-5xl font-black text-white" x-text="age.months">0</div>
                            <div class="text-xs text-gray-400 uppercase font-bold mt-1">Months</div>
                        </div>
                        <div class="glass-card p-4 border-b-4 border-accent">
                            <div class="text-4xl md:text-5xl font-black text-white" x-text="age.days">0</div>
                            <div class="text-xs text-gray-400 uppercase font-bold mt-1">Days</div>
                        </div>
                    </div>

                    <!-- Extra Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="p-3 bg-white/5 rounded-lg">
                            <div class="text-xs text-gray-500 uppercase">Total Days</div>
                            <div class="text-lg font-bold text-white font-mono" x-text="fmt(stats.totalDays)"></div>
                        </div>
                        <div class="p-3 bg-white/5 rounded-lg">
                            <div class="text-xs text-gray-500 uppercase">Total Weeks</div>
                            <div class="text-lg font-bold text-white font-mono" x-text="fmt(stats.totalWeeks)"></div>
                        </div>
                         <div class="p-3 bg-white/5 rounded-lg">
                            <div class="text-xs text-gray-500 uppercase">Total Hours</div>
                            <div class="text-lg font-bold text-white font-mono" x-text="fmt(stats.totalHours)"></div>
                        </div>
                        <div class="p-3 bg-white/5 rounded-lg">
                            <div class="text-xs text-gray-500 uppercase">Next Birthday</div>
                            <div class="text-lg font-bold text-primary font-mono" x-text="stats.nextBirthday"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ageCalculator', () => ({
        dob: '2000-01-01',
        target: new Date().toISOString().split('T')[0],
        age: null,
        stats: {},

        init() {
            this.calculate();
        },

        calculate() {
            if (!this.dob || !this.target) return;

            const d1 = new Date(this.dob);
            const d2 = new Date(this.target);

            if (d1 > d2) {
                this.age = { years: 0, months: 0, days: 0 };
                this.stats = { totalDays: 0, totalWeeks: 0, totalHours: 0, nextBirthday: "Invalid Date" };
                return;
            }

            // Years, Months, Days logic
            let years = d2.getFullYear() - d1.getFullYear();
            let months = d2.getMonth() - d1.getMonth();
            let days = d2.getDate() - d1.getDate();

            if (days < 0) {
                months--;
                // Days in previous month
                const prevMonth = new Date(d2.getFullYear(), d2.getMonth(), 0);
                days += prevMonth.getDate();
            }

            if (months < 0) {
                years--;
                months += 12;
            }

            this.age = { years, months, days };

            // Total Stats
            const diffTime = Math.abs(d2 - d1);
            const totalDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
            
            this.stats = {
                totalDays: totalDays,
                totalWeeks: Math.floor(totalDays / 7),
                totalHours: totalDays * 24,
                nextBirthday: this.getNextBirthday(d1, d2)
            };
        },

        getNextBirthday(dob, current) {
            const next = new Date(current.getFullYear(), dob.getMonth(), dob.getDate());
            if (next < current) {
                next.setFullYear(current.getFullYear() + 1);
            }
            const diff = Math.ceil((next - current) / (1000 * 60 * 60 * 24));
            return diff === 0 ? "Today!" : diff + " days";
        },

        fmt(n) {
            return n.toLocaleString('en-US');
        }
    }));
});
</script>
