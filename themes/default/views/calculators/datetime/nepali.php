<?php
// themes/default/views/calculators/datetime/nepali.php
// PREMIUM NEPALI DATE CONVERTER (API Dependent)
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="nepaliDateCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute bottom-[0%] left-[50%] -translate-x-1/2 w-[600px] h-[400px] bg-red-600/10 rounded-full blur-[120px] animate-pulse-glow"></div>
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
                <i class="fas fa-calendar-alt"></i>
                <span>CONVERTER</span>
            </div>
            <h1 class="calc-title">Nepali <span class="text-gradient">Date</span></h1>
            <p class="calc-subtitle">Convert between English (AD) and Nepali (BS) dates with precision.</p>
        </div>

        <div class="calc-grid max-w-3xl mx-auto">
            
            <div class="calc-card animate-scale-in">
                
                <!-- Toggle -->
                <div class="flex justify-center mb-8">
                     <div class="bg-white/5 p-1 rounded-full border border-white/10 inline-flex">
                        <button @click="mode = 'ad_to_bs'; result = null" :class="mode === 'ad_to_bs' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="px-6 py-2 rounded-full transition-all font-bold text-sm">
                            AD <i class="fas fa-arrow-right mx-1 text-xs"></i> BS
                        </button>
                        <button @click="mode = 'bs_to_ad'; result = null" :class="mode === 'bs_to_ad' ? 'bg-primary text-white shadow' : 'text-gray-400 hover:text-white'" class="px-6 py-2 rounded-full transition-all font-bold text-sm">
                            BS <i class="fas fa-arrow-right mx-1 text-xs"></i> AD
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    
                    <!-- AD to BS Input -->
                    <div x-show="mode === 'ad_to_bs'" x-transition class="space-y-4">
                        <label class="calc-label text-center">Enter English Date (AD)</label>
                        <div class="flex flex-col items-center">
                            <input type="date" x-model="adDate" class="calc-input text-center text-xl max-w-xs">
                        </div>
                        <button @click="convertAdToBs()" class="calc-btn w-full mt-4" :disabled="loading">
                             <span x-show="!loading"><i class="fas fa-exchange-alt mr-2"></i> Convert to Nepali</span>
                             <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i> Converting...</span>
                        </button>
                    </div>

                    <!-- BS to AD Input -->
                    <div x-show="mode === 'bs_to_ad'" x-transition class="space-y-4">
                        <label class="calc-label text-center">Enter Nepali Date (BS)</label>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Year</label>
                                <input type="number" x-model="bsYear" class="calc-input text-center" placeholder="2080">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Month</label>
                                <select x-model="bsMonth" class="calc-input text-center appearance-none">
                                    <template x-for="(m, i) in months" :key="i">
                                        <option :value="i+1" x-text="m"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Day</label>
                                <input type="number" x-model="bsDay" class="calc-input text-center" placeholder="1" min="1" max="32">
                            </div>
                        </div>
                         <button @click="convertBsToAd()" class="calc-btn w-full mt-4" :disabled="loading">
                             <span x-show="!loading"><i class="fas fa-exchange-alt mr-2"></i> Convert to English</span>
                             <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i> Converting...</span>
                        </button>
                    </div>

                </div>

                <!-- Result Section -->
                <div x-show="result" x-transition class="mt-8 pt-8 border-t border-white/5 text-center">
                    <div class="text-sm text-gray-400 uppercase tracking-widest mb-4 font-bold">Converted Date</div>
                    <div class="text-4xl font-black text-white mb-2" x-text="result"></div>
                    <div class="text-lg font-bold text-red-400" x-text="resultDay"></div>
                </div>
                 <div x-show="error" x-transition class="mt-4 text-center text-red-500 text-sm font-bold" x-text="error"></div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('nepaliDateCalculator', () => ({
        mode: 'ad_to_bs',
        adDate: new Date().toISOString().split('T')[0],
        bsYear: 2081,
        bsMonth: 1,
        bsDay: 1,
        
        result: null,
        resultDay: null,
        error: null,
        loading: false,
        
        months: ['Baishakh', 'Jestha', 'Ashad', 'Shrawan', 'Bhadra', 'Ashwin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra'],

        async convertAdToBs() {
            if (!this.adDate) return;
            const [y, m, d] = this.adDate.split('-');
            await this.performConversion('ad_to_bs', y, m, d);
        },

        async convertBsToAd() {
            if (!this.bsYear || !this.bsMonth || !this.bsDay) return;
            await this.performConversion('bs_to_ad', this.bsYear, this.bsMonth, this.bsDay);
        },

        async performConversion(type, y, m, d) {
            this.loading = true;
            this.error = null;
            this.result = null;
            
            try {
                const response = await fetch('<?= app_base_url("/calculator/api/datetime/nepali"); ?>', {
                    method: 'POST',
                    body: JSON.stringify({ type, year: y, month: m, day: d })
                });
                
                const data = await response.json();
                
                if (data.error) {
                    this.error = data.error;
                } else {
                    if (type === 'ad_to_bs') {
                        this.result = `${data.month_name} ${data.day}, ${data.year}`;
                        this.resultDay = data.day_name;
                    } else {
                        this.result = data.formatted;
                        this.resultDay = data.day_name; // API likely returns weekday name
                    }
                }
            } catch (e) {
                this.error = "Failed to connect to conversion service.";
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
