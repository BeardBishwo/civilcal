<?php
// themes/default/views/calculators/nepali.php
// PREMIUM NEPALI UNIT CONVERTER
// SEO & PERFORMANCE OPTIMIZED
?>

<!-- SEO Metadata -->
<head>
    <title>Nepali Land Unit Converter | Ropani, Bigha, Sq. Feet Calculator</title>
    <meta name="description" content="Premium Nepali Land Converter: Instantly convert Ropani, Aana, Paisa, Daam to Bigha, Kattha, Dhur, Square Feet, and Meters. Best tool for real estate and engineering in Nepal.">
    <meta name="keywords" content="nepali land converter, ropani calculator, bigha to sq feet, land unit conversion nepal, aana to sq meter, jagga napne">
    <meta property="og:title" content="Nepali Land Unit Converter | Ropani, Bigha, Sq. Feet Calculator">
    <meta property="og:description" content="Premium Nepali Land Converter: Instantly convert Ropani, Aana, Paisa, Daam to Bigha, Kattha, Dhur, Square Feet, and Meters.">
    <meta property="og:type" content="website">
    
    <!-- Structured Data (JSON-LD) for Google Rich Snippets -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "Nepali Land Unit Converter",
        "category": "Calculator",
        "description": "Professional tool to convert Nepali land units like Ropani and Bigha to international standards.",
        "applicationCategory": "Utility",
        "operatingSystem": "Any"
    }
    </script>
</head>

<!-- Dependencies -->
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

<!-- Tailwind Config for Custom Colors -->
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    deep: '#09090b',
                    surface: '#18181b',
                    card: '#27272a',
                    accent: '#ec4899',
                    'accent-dim': 'rgba(236, 72, 153, 0.1)'
                },
                fontFamily: {
                    sans: ['Outfit', 'sans-serif'],
                    mono: ['JetBrains Mono', 'monospace'],
                },
                boxShadow: {
                    'glow': '0 0 20px rgba(236, 72, 153, 0.15)',
                    'glow-strong': '0 0 30px rgba(236, 72, 153, 0.3)',
                }
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
    /* Hide Number Spinners */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<!-- Main App Wrapper -->
<div class="bg-deep min-h-screen text-white font-sans flex overflow-hidden selection:bg-accent selection:text-white" x-data="nepaliCalculator()">
    
    <!-- Sidebar (Visible on LG screens) -->
    <aside class="hidden lg:flex w-80 bg-surface border-r border-zinc-800 flex-col p-6 gap-8 z-10 shadow-2xl">
        <!-- Brand -->
        <div class="border-b border-zinc-800 pb-4">
            <h1 class="text-xl font-bold tracking-tight text-white mb-1"><span class="text-accent">CIVIL</span> CAL</h1>
            <span class="text-[10px] font-bold tracking-[0.2em] text-zinc-500 uppercase">Premium Tools v2.0</span>
        </div>

        <!-- Sidebar Inputs -->
        <div class="flex flex-col gap-6">
            <!-- Hilly Inputs -->
            <div class="space-y-3">
                <label class="text-[10px] font-mono text-zinc-500 uppercase tracking-widest font-bold">Quick Input (Hilly)</label>
                <div class="space-y-2">
                    <div class="relative group">
                        <span class="absolute left-3 top-2.5 text-[10px] font-bold text-accent">R</span>
                        <input type="number" x-model="values.ropani" @input="update('ropani')" placeholder="Ropani" 
                            class="w-full bg-deep border border-zinc-800 rounded-lg py-2 pl-8 pr-3 text-sm font-mono focus:border-accent focus:shadow-glow outline-none transition-all group-hover:border-zinc-700">
                    </div>
                    <div class="relative group">
                        <span class="absolute left-3 top-2.5 text-[10px] font-bold text-zinc-500">A</span>
                        <input type="number" x-model="values.aana" @input="update('aana')" placeholder="Aana" 
                            class="w-full bg-deep border border-zinc-800 rounded-lg py-2 pl-8 pr-3 text-sm font-mono focus:border-accent focus:shadow-glow outline-none transition-all group-hover:border-zinc-700">
                    </div>
                    <!-- Combined Row -->
                    <div class="grid grid-cols-2 gap-2">
                        <div class="relative group">
                             <span class="absolute left-3 top-2.5 text-[10px] font-bold text-zinc-500">P</span>
                             <input type="number" x-model="values.paisa" @input="update('paisa')" placeholder="Paisa" class="w-full bg-deep border border-zinc-800 rounded-lg py-2 pl-8 pr-3 text-sm font-mono focus:border-accent focus:shadow-glow outline-none transition-all">
                        </div>
                        <div class="relative group">
                             <span class="absolute left-3 top-2.5 text-[10px] font-bold text-zinc-500">D</span>
                             <input type="number" x-model="values.daam" @input="update('daam')" placeholder="Daam" class="w-full bg-deep border border-zinc-800 rounded-lg py-2 pl-8 pr-3 text-sm font-mono focus:border-accent focus:shadow-glow outline-none transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-px bg-zinc-800 w-full"></div>

             <!-- Terai Inputs -->
             <div class="space-y-3">
                <label class="text-[10px] font-mono text-zinc-500 uppercase tracking-widest font-bold">Quick Input (Terai)</label>
                <div class="space-y-2">
                    <div class="relative group">
                        <span class="absolute left-3 top-2.5 text-[10px] font-bold text-green-500">B</span>
                        <input type="number" x-model="values.bigha" @input="update('bigha')" placeholder="Bigha" 
                            class="w-full bg-deep border border-zinc-800 rounded-lg py-2 pl-8 pr-3 text-sm font-mono focus:border-green-500 focus:shadow-[0_0_15px_rgba(34,197,94,0.15)] outline-none transition-all group-hover:border-zinc-700">
                    </div>
                    <div class="relative group">
                        <span class="absolute left-3 top-2.5 text-[10px] font-bold text-zinc-500">K</span>
                        <input type="number" x-model="values.kattha" @input="update('kattha')" placeholder="Kattha" 
                            class="w-full bg-deep border border-zinc-800 rounded-lg py-2 pl-8 pr-3 text-sm font-mono focus:border-green-500 outline-none transition-all group-hover:border-zinc-700">
                    </div>
                    <div class="relative group">
                        <span class="absolute left-3 top-2.5 text-[10px] font-bold text-zinc-500">D</span>
                        <input type="number" x-model="values.dhur" @input="update('dhur')" placeholder="Dhur" 
                            class="w-full bg-deep border border-zinc-800 rounded-lg py-2 pl-8 pr-3 text-sm font-mono focus:border-green-500 outline-none transition-all group-hover:border-zinc-700">
                    </div>
                </div>
            </div>

            <div class="mt-auto">
                 <a href="<?= app_base_url('/calculators') ?>" class="flex items-center justify-center gap-2 w-full py-3 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-xs font-bold uppercase tracking-wider transition-colors text-zinc-400 hover:text-white">
                    <i class="fas fa-arrow-left"></i> Back to Hub
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Console -->
    <main class="flex-1 bg-deep overflow-y-auto relative">
        <!-- Mobile Header (Visible < LG) -->
        <div class="lg:hidden sticky top-0 z-50 bg-surface/95 backdrop-blur-md border-b border-zinc-800 px-4 py-4 flex items-center justify-between shadow-lg">
             <div>
                <h1 class="text-sm font-bold text-accent tracking-wider">NEPALI LAND CONVERTER</h1>
             </div>
             <button @click="clearAll()" class="text-xs text-zinc-400 hover:text-white uppercase font-bold"><i class="fas fa-trash-alt mr-1"></i> Clear</button>
        </div>

        <div class="p-4 lg:p-12 max-w-7xl mx-auto space-y-12">
            
            <!-- Header Section -->
            <div class="flex items-end justify-between border-b border-zinc-800 pb-4">
                <div>
                     <h1 class="text-3xl lg:text-4xl font-bold text-white mb-2">Nepali Unit <span class="text-accent">Console</span></h1>
                     <p class="text-zinc-500 text-sm max-w-lg">Advanced bidirectional converter for Real Estate & Engineering. Supports Ropani System, Bigha System, Metric, and Imperial units.</p>
                </div>
                <button @click="clearAll()" class="hidden lg:flex px-4 py-2 bg-accent-dim border border-accent/20 rounded-lg text-accent text-xs font-bold uppercase tracking-wider hover:bg-accent hover:text-white transition-all items-center gap-2">
                    <i class="fas fa-refresh"></i> Clear Data
                </button>
            </div>

            <!-- Hero Row (Metric / Imperial / Cross) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Metric Hero -->
                <div class="group bg-surface border border-zinc-800 rounded-2xl p-6 hover:border-blue-500/50 hover:shadow-[0_0_30px_rgba(59,130,246,0.1)] transition-all cursor-text" @click="$refs.sqm.focus()">
                     <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold text-blue-500 uppercase tracking-[0.2em]">Metric Area</span>
                        <i class="fas fa-globe-asia text-zinc-700 group-hover:text-blue-500 transition-colors"></i>
                     </div>
                     <input x-ref="sqm" type="number" x-model="values.sq_meter" @input="update('sq_meter')" class="w-full bg-transparent text-4xl font-mono font-bold text-white placeholder-zinc-800 focus:outline-none" placeholder="0">
                     <span class="text-xs text-zinc-500 font-mono mt-1 block">Square Meters</span>
                </div>

                <!-- Imperial Hero -->
                <div class="group bg-surface border border-zinc-800 rounded-2xl p-6 hover:border-yellow-500/50 hover:shadow-[0_0_30px_rgba(234,179,8,0.1)] transition-all cursor-text" @click="$refs.sqft.focus()">
                     <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold text-yellow-500 uppercase tracking-[0.2em]">Imperial Area</span>
                        <i class="fas fa-ruler-combined text-zinc-700 group-hover:text-yellow-500 transition-colors"></i>
                     </div>
                     <input x-ref="sqft" type="number" x-model="values.sq_feet" @input="update('sq_feet')" class="w-full bg-transparent text-4xl font-mono font-bold text-white placeholder-zinc-800 focus:outline-none" placeholder="0">
                     <span class="text-xs text-zinc-500 font-mono mt-1 block">Square Feet</span>
                </div>

                <!-- Cross System Hero -->
                <div class="relative bg-gradient-to-br from-pink-900/20 to-surface border border-pink-500/30 rounded-2xl p-6 shadow-glow">
                     <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold text-pink-400 uppercase tracking-[0.2em]">Dynamic Target</span>
                        <i class="fas fa-exchange-alt text-pink-500 animate-pulse"></i>
                     </div>
                     <div class="text-4xl font-mono font-bold text-white truncate" x-text="formatDisplay(values[crossTarget] || 0)">0</div>
                     <span class="text-xs text-pink-400/70 font-mono mt-1 block uppercase" x-text="crossTarget">Target Unit</span>
                </div>
            </div>

            <!-- Matrix Sections -->
            <div class="space-y-8">
                
                <!-- Hilly Region -->
                <section>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-px bg-zinc-800 flex-1"></div>
                        <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest"><i class="fas fa-mountain mr-2 text-pink-500"></i>Hilly Region (Ropani System)</h3>
                        <div class="h-px bg-zinc-800 flex-1"></div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <template x-for="unit in ['ropani', 'aana', 'paisa', 'daam']">
                            <div class="bg-surface border border-zinc-800 rounded-xl p-4 hover:border-pink-500/50 transition-all group cursor-pointer" @click="copyToClipboard(values[unit])">
                                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1 group-hover:text-pink-400" x-text="unit"></span>
                                <input type="number" x-model="values[unit]" @input="update(unit)" class="w-full bg-transparent font-mono text-xl font-bold text-white focus:outline-none placeholder-zinc-800">
                            </div>
                        </template>
                    </div>
                </section>

                <!-- Terai Region -->
                <section>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-px bg-zinc-800 flex-1"></div>
                        <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest"><i class="fas fa-tree mr-2 text-green-500"></i>Terai Region (Bigha System)</h3>
                        <div class="h-px bg-zinc-800 flex-1"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <template x-for="unit in ['bigha', 'kattha', 'dhur']">
                            <div class="bg-surface border border-zinc-800 rounded-xl p-4 hover:border-green-500/50 transition-all group cursor-pointer">
                                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1 group-hover:text-green-400" x-text="unit"></span>
                                <input type="number" x-model="values[unit]" @input="update(unit)" class="w-full bg-transparent font-mono text-xl font-bold text-white focus:outline-none placeholder-zinc-800">
                            </div>
                        </template>
                    </div>
                </section>

                <!-- Standards (Metric & Imperial) -->
                <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Metric -->
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                             <div class="w-1 h-4 bg-blue-500 rounded-full"></div>
                             <h4 class="text-xs font-bold text-zinc-400 uppercase">Metric Standards</h4>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                             <div class="bg-surface border border-zinc-800 rounded-lg p-3 hover:border-blue-500/30">
                                 <span class="text-[10px] text-zinc-500 uppercase">Hectares</span>
                                 <input type="number" x-model="values.hectare" @input="update('hectare')" class="w-full bg-transparent font-mono text-lg text-white focus:outline-none">
                             </div>
                             <div class="bg-surface border border-zinc-800 rounded-lg p-3 hover:border-blue-500/30">
                                 <span class="text-[10px] text-zinc-500 uppercase">Sq. Millimeters</span>
                                 <input type="number" x-model="values.sq_mm" @input="update('sq_mm')" class="w-full bg-transparent font-mono text-lg text-white focus:outline-none">
                             </div>
                        </div>
                    </div>

                    <!-- Imperial -->
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                             <div class="w-1 h-4 bg-yellow-500 rounded-full"></div>
                             <h4 class="text-xs font-bold text-zinc-400 uppercase">Imperial Standards</h4>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                             <div class="bg-surface border border-zinc-800 rounded-lg p-3 hover:border-yellow-500/30">
                                 <span class="text-[10px] text-zinc-500 uppercase">Acres</span>
                                 <input type="number" x-model="values.acre" @input="update('acre')" class="w-full bg-transparent font-mono text-lg text-white focus:outline-none">
                             </div>
                             <div class="bg-surface border border-zinc-800 rounded-lg p-3 hover:border-yellow-500/30">
                                 <span class="text-[10px] text-zinc-500 uppercase">Sq. Inches</span>
                                 <input type="number" x-model="values.sq_in" @input="update('sq_in')" class="w-full bg-transparent font-mono text-lg text-white focus:outline-none">
                             </div>
                        </div>
                    </div>
                </section>

                <!-- Info Strip -->
                <div class="grid grid-cols-3 divide-x divide-zinc-800 border-t border-zinc-800 pt-8 text-center bg-zinc-900/30 rounded-xl p-6">
                    <div>
                        <h5 class="text-accent text-sm font-bold mb-1">1 Ropani</h5>
                        <p class="text-[10px] text-zinc-500 font-mono">16 Aanas<br>5476 Sq.Ft</p>
                    </div>
                    <div>
                        <h5 class="text-green-500 text-sm font-bold mb-1">1 Bigha</h5>
                        <p class="text-[10px] text-zinc-500 font-mono">20 Katthas<br>13.31 Ropani</p>
                    </div>
                    <div>
                        <h5 class="text-blue-500 text-sm font-bold mb-1">1 Kattha</h5>
                        <p class="text-[10px] text-zinc-500 font-mono">338.63 Sq.m<br>20 Dhur</p>
                    </div>
                </div>

                <!-- Glance Cards -->
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Hilly Glance -->
                    <div class="bg-surface border-t-2 border-t-pink-500 p-6 rounded-b-xl shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-white">Hilly Conversion</h3>
                            <select x-model="glanceBaseHilly" class="bg-deep border border-zinc-700 text-xs rounded px-2 py-1 text-pink-400 focus:outline-none font-bold">
                                <option value="ropani">1 Ropani</option>
                                <option value="aana">1 Aana</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="flex flex-col"><span class="text-lg font-mono font-bold text-white" x-text="formatDisplay(getGlanceVal(glanceBaseHilly, 'aana'))"></span><span class="text-[9px] text-zinc-500 uppercase">Aana</span></div>
                            <div class="flex flex-col"><span class="text-lg font-mono font-bold text-white" x-text="formatDisplay(getGlanceVal(glanceBaseHilly, 'paisa'))"></span><span class="text-[9px] text-zinc-500 uppercase">Paisa</span></div>
                            <div class="flex flex-col"><span class="text-lg font-mono font-bold text-white" x-text="formatDisplay(getGlanceVal(glanceBaseHilly, 'sq_meter'))"></span><span class="text-[9px] text-zinc-500 uppercase">Sq.m</span></div>
                            <div class="flex flex-col"><span class="text-lg font-mono font-bold text-white" x-text="formatDisplay(getGlanceVal(glanceBaseHilly, 'sq_feet'))"></span><span class="text-[9px] text-zinc-500 uppercase">Sq.ft</span></div>
                        </div>
                    </div>

                    <!-- Terai Glance -->
                    <div class="bg-surface border-t-2 border-t-green-500 p-6 rounded-b-xl shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-white">Terai Conversion</h3>
                            <select x-model="glanceBaseTerai" class="bg-deep border border-zinc-700 text-xs rounded px-2 py-1 text-green-400 focus:outline-none font-bold">
                                <option value="bigha">1 Bigha</option>
                                <option value="kattha">1 Kattha</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="flex flex-col"><span class="text-lg font-mono font-bold text-white" x-text="formatDisplay(getGlanceVal(glanceBaseTerai, 'kattha'))"></span><span class="text-[9px] text-zinc-500 uppercase">Kattha</span></div>
                            <div class="flex flex-col"><span class="text-lg font-mono font-bold text-white" x-text="formatDisplay(getGlanceVal(glanceBaseTerai, 'dhur'))"></span><span class="text-[9px] text-zinc-500 uppercase">Dhur</span></div>
                            <div class="flex flex-col"><span class="text-lg font-mono font-bold text-white" x-text="formatDisplay(getGlanceVal(glanceBaseTerai, 'sq_meter'))"></span><span class="text-[9px] text-zinc-500 uppercase">Sq.m</span></div>
                            <div class="flex flex-col"><span class="text-lg font-mono font-bold text-white" x-text="formatDisplay(getGlanceVal(glanceBaseTerai, 'ropani'))"></span><span class="text-[9px] text-zinc-500 uppercase">Ropani</span></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer/Copyright -->
            <footer class="text-center text-[10px] text-zinc-600 uppercase tracking-widest mt-12 pb-6">
                &copy; <?= date('Y') ?> Civil Calculator Premium • Engineered in Nepal
            </footer>

        </div>
    </main>

    <!-- Copy Toast Notification -->
    <div x-show="toast.visible" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-90"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-90"
         class="fixed bottom-6 right-6 bg-surface border-l-4 border-accent px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3 z-50">
        <div class="bg-accent/10 w-8 h-8 rounded-full flex items-center justify-center text-accent">
            <i class="fas fa-check text-xs"></i>
        </div>
        <div>
            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Copied to Clipboard</h4>
            <div class="text-[10px] text-zinc-400 font-mono" x-text="toast.message">Value copied</div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('nepaliCalculator', () => ({
        // Conversion Constants (Base: Square Feet)
        UNITS: {
            ropani: 5476,
            aana: 342.25,
            paisa: 85.5625,
            daam: 21.390625,
            bigha: 72900,
            kattha: 3645,
            dhur: 182.25,
            sq_meter: 10.7639104,
            sq_mm: 0.000107639104,
            hectare: 107639.104,
            sq_feet: 1,
            sq_in: 0.00694444,
            acre: 43560
        },

        // Reactive State
        values: {
            ropani: '', aana: '', paisa: '', daam: '',
            bigha: '', kattha: '', dhur: '',
            sq_meter: '', sq_mm: '', hectare: '',
            sq_feet: '', sq_in: '', acre: ''
        },
        
        crossTarget: 'bigha', // Default target unit for Cross System
        glanceBaseHilly: 'ropani',
        glanceBaseTerai: 'bigha',
        
        toast: { visible: false, message: '' },

        // Core Update Logic
        update(source) {
            let val = parseFloat(this.values[source]);
            
            // Auto-clear logic
            if (isNaN(val)) {
                if (this.values[source] === '') this.clearAll();
                return;
            }

            // 1. Convert Source -> Base (Sq. Feet)
            let baseSqFt = val * this.UNITS[source];

            // 2. Determine Cross Target (Simple Heuristic: If editing Hilly -> Show Terai, else Show Hilly)
            if (['ropani', 'aana', 'paisa', 'daam'].includes(source)) this.crossTarget = 'bigha';
            else if (['bigha', 'kattha', 'dhur'].includes(source)) this.crossTarget = 'ropani';
            
            // 3. Distribute Base -> All Others
            Object.keys(this.values).forEach(key => {
                if (key !== source) {
                    let converted = baseSqFt / this.UNITS[key];
                    this.values[key] = parseFloat(converted.toFixed(6)); // Precision limit
                }
            });
        },

        // Helper: Format for Display (removes trailing zeros via parseFloat)
        formatDisplay(num) {
            return parseFloat(Number(num).toFixed(4));
        },

        // Helper: Get Static Glance Value
        getGlanceVal(baseUnit, targetUnit) {
            let baseSqFt = 1 * this.UNITS[baseUnit];
            return baseSqFt / this.UNITS[targetUnit];
        },

        clearAll() {
            Object.keys(this.values).forEach(key => this.values[key] = '');
            this.showToast('All fields cleared');
        },

        copyToClipboard(text) {
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                this.showToast(text);
            });
        },

        showToast(msg) {
            this.toast.message = msg;
            this.toast.visible = true;
            setTimeout(() => this.toast.visible = false, 2000);
        }
    }));
});
</script>