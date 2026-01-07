<?php
// themes/default/views/calculators/math/trigonometry.php
// PREMIUM TRIGONOMETRY CALCULATOR
?>

<link rel="stylesheet" href="<?= app_base_url('themes/default/assets/css/calculators.min.css?v=' . time()) ?>">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-background min-h-screen relative overflow-hidden" x-data="trigCalculator()">
    <div class="fixed inset-0 pointer-events-none z-0">
         <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
        <div class="absolute top-[-10%] right-[20%] w-[400px] h-[400px] bg-indigo-500/20 rounded-full blur-[100px] animate-pulse-glow"></div>
    </div>

    <div class="calc-container">
        <nav class="mb-6 animate-slide-down">
            <ol class="flex items-center gap-2 text-sm text-gray-400">
                <li><a href="<?= app_base_url('/calculators') ?>" class="hover:text-white transition">Calculators</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-primary font-bold">Trigonometry</li>
            </ol>
        </nav>

        <div class="calc-header animate-slide-down">
            <h1 class="calc-title">Trig <span class="text-gradient">Functions</span></h1>
            <p class="calc-subtitle">Calculate Sine, Cosine, Tangent and more for any angle instantly.</p>
        </div>

        <div class="calc-grid max-w-5xl mx-auto">
            
            <!-- Input Panel -->
            <div class="calc-card animate-scale-in">
                <div class="flex flex-col md:flex-row gap-6 items-end">
                    <div class="flex-grow w-full">
                        <label class="calc-label">Enter Angle</label>
                        <input type="number" x-model.number="angle" @input="calculate()" class="calc-input text-2xl font-bold" placeholder="Angle">
                    </div>
                </div>
                 
                 <div class="flex mt-6 bg-white/5 p-1 rounded-lg border border-white/10 w-full md:w-auto self-start">
                    <button @click="unit = 'deg'; calculate()" :class="unit === 'deg' ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:text-white'" class="flex-1 py-2 px-6 rounded-md font-bold transition-all">Degree (°)</button>
                    <button @click="unit = 'rad'; calculate()" :class="unit === 'rad' ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:text-white'" class="flex-1 py-2 px-6 rounded-md font-bold transition-all">Radian (rad)</button>
                </div>
            </div>

            <!-- Results Grid -->
            <div class="col-span-1 md:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-4 animate-slide-up">
                
                <div class="glass-card p-6 flex flex-col items-center justify-center text-center group hover:bg-primary/20 transition duration-500">
                    <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold">Sine (sin)</div>
                    <div class="text-3xl font-mono font-black text-white group-hover:scale-110 transition" x-text="results.sin"></div>
                </div>

                <div class="glass-card p-6 flex flex-col items-center justify-center text-center group hover:bg-secondary/20 transition duration-500">
                    <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold">Cosine (cos)</div>
                    <div class="text-3xl font-mono font-black text-white group-hover:scale-110 transition" x-text="results.cos"></div>
                </div>

                <div class="glass-card p-6 flex flex-col items-center justify-center text-center group hover:bg-accent/20 transition duration-500">
                    <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold">Tangent (tan)</div>
                    <div class="text-3xl font-mono font-black text-white group-hover:scale-110 transition" x-text="results.tan"></div>
                </div>

                <div class="glass-card p-6 flex flex-col items-center justify-center text-center opacity-75 hover:opacity-100 transition">
                    <div class="text-xs text-gray-400 uppercase tracking-widest mb-2">Cosecant (csc)</div>
                    <div class="text-xl font-mono font-bold text-gray-200" x-text="results.csc"></div>
                </div>

                <div class="glass-card p-6 flex flex-col items-center justify-center text-center opacity-75 hover:opacity-100 transition">
                    <div class="text-xs text-gray-400 uppercase tracking-widest mb-2">Secant (sec)</div>
                    <div class="text-xl font-mono font-bold text-gray-200" x-text="results.sec"></div>
                </div>

                <div class="glass-card p-6 flex flex-col items-center justify-center text-center opacity-75 hover:opacity-100 transition">
                    <div class="text-xs text-gray-400 uppercase tracking-widest mb-2">Cotangent (cot)</div>
                    <div class="text-xl font-mono font-bold text-gray-200" x-text="results.cot"></div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('trigCalculator', () => ({
        angle: 45,
        unit: 'deg',
        results: { sin: 0, cos: 0, tan: 0, csc: 0, sec: 0, cot: 0 },

        init() {
            this.calculate();
        },

        calculate() {
            if (this.angle === null) return;
            
            let rad = this.angle;
            if (this.unit === 'deg') {
                rad = this.angle * (Math.PI / 180);
            }

            const sin = Math.sin(rad);
            const cos = Math.cos(rad);
            const tan = Math.tan(rad);

            this.results.sin = this.fmt(sin);
            this.results.cos = this.fmt(cos);
            this.results.tan = this.fmt(tan);
            this.results.csc = this.fmt(1/sin);
            this.results.sec = this.fmt(1/cos);
            this.results.cot = this.fmt(1/tan);
        },

        fmt(val) {
            if (!isFinite(val)) return "Undefined";
            if (Math.abs(val) < 1e-10) return "0";
            if (Math.abs(val) > 1e10) return "∞";
            return val.toFixed(4).replace(/\.?0+$/, "");
        }
    }));
});
</script>
