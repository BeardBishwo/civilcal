<?php
// themes/default/views/calculators/math/discount.php
// PREMIUM DISCOUNT CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="discountCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[30%] right-[10%] w-[400px] h-[400px] bg-red-500/10 rounded-full blur-[100px] animate-bounce-subtle"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Discount Calculator</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex flex-col items-center text-center animate-slide-down mb-12">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold tracking-widest uppercase mb-6 shadow-lg shadow-primary/5 hover:bg-white/10 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Finance</span>
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight drop-shadow-xl">
                Discount <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Hunter</span>
            </h1>
            
            <!-- Description -->
            <p class="text-slate-400 font-medium text-lg max-w-2xl leading-relaxed mx-auto">
                <span class="bg-gradient-to-r from-white/10 to-transparent h-px w-20 inline-block align-middle mr-2"></span>
                Calculate final price after discount and see exactly how much you save.
                <span class="bg-gradient-to-l from-white/10 to-transparent h-px w-20 inline-block align-middle ml-2"></span>
            </p>
        </div>

        <div class="calc-grid max-w-4xl mx-auto">
            
            <div class="calc-card animate-scale-in grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                
                <!-- Inputs -->
                <div class="space-y-6">
                    <div>
                        <label class="calc-label">Original Price</label>
                        <div class="calc-input-group">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</div>
                            <input type="number" x-model.number="price" @input="calculate()" class="calc-input pl-10" placeholder="0.00">
                        </div>
                    </div>
                    
                    <div>
                        <label class="calc-label">Discount Percentage</label>
                        <div class="calc-input-group">
                            <input type="number" x-model.number="discount" @input="calculate()" class="calc-input" placeholder="0">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">%</div>
                        </div>
                        <input type="range" x-model.number="discount" @input="calculate()" class="w-full mt-3 accent-primary" min="0" max="100">
                    </div>

                    <div>
                        <label class="calc-label">Additional Discount (Optional)</label>
                         <div class="calc-input-group">
                            <input type="number" x-model.number="extraoff" @input="calculate()" class="calc-input" placeholder="0">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">%</div>
                        </div>
                    </div>
                </div>

                <!-- Results Ticket -->
                <div class="relative bg-white text-gray-900 rounded-3xl p-8 shadow-2xl rotate-1 transform transition hover:rotate-0 duration-300">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full"></div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 w-6 h-6 bg-background rounded-full"></div>
                    
                    <div class="text-center border-b-2 border-dashed border-gray-300 pb-6 mb-6">
                        <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Final Price</div>
                        <div class="text-5xl font-black text-gray-900 mt-2">$<span x-text="fmt(finalPrice)"></span></div>
                    </div>

                    <div class="space-y-3 font-mono text-sm">
                        <div class="flex justify-between text-gray-500">
                            <span>Original</span>
                            <span class="line-through">$<span x-text="fmt(price || 0)"></span></span>
                        </div>
                        <div class="flex justify-between text-red-500 font-bold">
                            <span>Discount</span>
                            <span>-$<span x-text="fmt(saved)"></span></span>
                        </div>
                        <div class="flex justify-between text-gray-500" x-show="extraoff > 0">
                            <span>Extra Off</span>
                            <span><span x-text="extraoff"></span>%</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t-2 border-dashed border-gray-300 text-center">
                        <div class="text-xs font-bold text-green-600 uppercase bg-green-100 py-1 px-3 rounded-full inline-block">
                             You Save $<span x-text="fmt(saved)"></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('discountCalculator', () => ({
        price: 100,
        discount: 20,
        extraoff: 0,
        finalPrice: 80,
        saved: 20,

        init() {
            this.calculate();
        },

        calculate() {
            let p = this.price || 0;
            let d = this.discount || 0;
            let e = this.extraoff || 0;

            // First discount
            let firstSave = p * (d / 100);
            let afterFirst = p - firstSave;

            // Second discount (Compound)
            let secondSave = afterFirst * (e / 100);
            
            this.finalPrice = afterFirst - secondSave;
            this.saved = firstSave + secondSave;
        },

        fmt(n) {
            return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }));
});
</script>
