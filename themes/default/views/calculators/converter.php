<?php
// themes/default/views/calculators/converter.php
// PREMIUM VIBRANT UNIT CONVERTER
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Converter - <?= $category['name'] ?></title>
    
    <!-- Tailwind CSS (CDN for immediate rendering of arbitrary values if build doesn't support them) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        glass: {
                            10: 'rgba(255, 255, 255, 0.05)',
                            20: 'rgba(255, 255, 255, 0.1)',
                            30: 'rgba(255, 255, 255, 0.2)',
                            dark: 'rgba(15, 23, 42, 0.6)',
                        },
                        brand: {
                            primary: '#8b5cf6',   // Violet
                            secondary: '#06b6d4', // Cyan
                            accent: '#f472b6',    // Pink
                        }
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'fade-in-up': 'fadeInUp 0.5s ease-out forwards',
                        'pulse-glow': 'pulseGlow 2s infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        pulseGlow: {
                            '0%, 100%': { boxShadow: '0 0 15px rgba(139, 92, 246, 0.3)' },
                            '50%': { boxShadow: '0 0 25px rgba(139, 92, 246, 0.6)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        body {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        .glass-card {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-glass {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(4px);
            transition: all 0.3s ease;
        }
        
        .input-glass:focus-within {
            background: rgba(0, 0, 0, 0.5);
            border-color: rgba(139, 92, 246, 0.5);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
        }

        .gradient-text {
            background: linear-gradient(135deg, #22d3ee 0%, #a78bfa 50%, #f472b6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-item-active {
            background: linear-gradient(90deg, rgba(139, 92, 246, 0.15), transparent);
            border-left: 3px solid #8b5cf6;
            color: #fff;
        }

        .animate-delay-100 { animation-delay: 100ms; }
        .animate-delay-200 { animation-delay: 200ms; }
        .animate-delay-300 { animation-delay: 300ms; }
    </style>
</head>
<body class="antialiased selection:bg-purple-500/30 selection:text-white" x-data="converterSPA()">

    <!-- Animated Background -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-purple-500/20 rounded-full mix-blend-screen filter blur-[100px] opacity-50 animate-blob"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-cyan-500/20 rounded-full mix-blend-screen filter blur-[100px] opacity-50 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-pink-500/10 rounded-full mix-blend-screen filter blur-[120px] opacity-40 animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative z-10 flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside class="w-72 flex-shrink-0 flex flex-col glass-card border-r-0 border-white/5 transition-transform duration-300 z-50 h-full"
               :class="sidebarOpen ? 'translate-x-0 absolute md:relative' : '-translate-x-full md:translate-x-0 absolute md:relative'">
            
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center px-4 border-b border-white/5 bg-black/20 backdrop-blur-xl">
                <a href="/" class="flex items-center gap-3 group w-full">
                    <?php $site_meta = get_site_meta(); if (!empty($site_meta['logo'])): ?>
                        <img src="<?= htmlspecialchars($site_meta['logo']) ?>" class="h-8 w-auto drop-shadow-[0_0_8px_rgba(34,211,238,0.5)]">
                    <?php else: ?>
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-400 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-cyan-500/40 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-cube text-sm"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-black text-lg tracking-tight text-white leading-none">Civil<span class="text-cyan-400">Cal</span></span>
                            <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">Premium</span>
                        </div>
                    <?php endif; ?>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden ml-auto text-gray-400 hover:text-white p-2">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Search -->
            <div class="p-4 pb-2">
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-purple-500 rounded-lg blur opacity-20 group-hover:opacity-40 transition duration-500"></div>
                    <div class="relative bg-black/40 border border-white/10 rounded-lg flex items-center px-3 py-2.5 focus-within:border-cyan-500/50 transition-colors">
                        <i class="fas fa-search text-gray-400 text-xs group-focus-within:text-cyan-400 transition-colors"></i>
                        <input type="text" x-model="searchQuery" placeholder="Search..." 
                               class="w-full bg-transparent border-none text-xs text-white placeholder-gray-500 focus:ring-0 ml-2 h-full py-0">
                    </div>
                </div>
            </div>

            <!-- Nav Items -->
            <div class="flex-1 overflow-y-auto px-3 pb-4 space-y-1 custom-scrollbar">
                <div class="flex items-center justify-between px-2 py-2 mb-1">
                    <span class="text-[9px] font-black text-cyan-500 uppercase tracking-widest">Tools</span>
                    <span class="text-[9px] font-bold text-gray-600 bg-white/5 px-1.5 py-0.5 rounded-full" x-text="filteredCategories.length"></span>
                </div>
                
                <template x-for="cat in filteredCategories" :key="cat.slug">
                    <a :href="'/convert/' + cat.slug"
                       @click.prevent="loadCategory(cat.slug)"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-300 group border border-transparent"
                       :class="currentSlug === cat.slug ? 'bg-white/10 border-white/10 shadow-[0_0_10px_rgba(0,0,0,0.2)] backdrop-blur-md' : 'hover:bg-white/5 hover:border-white/5'">
                        
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-300 shadow-inner flex-shrink-0"
                             :class="currentSlug === cat.slug ? 'bg-gradient-to-br from-cyan-500 to-blue-600 text-white shadow-cyan-500/20' : 'bg-white/5 text-gray-400 group-hover:bg-white/10 group-hover:text-cyan-300'">
                            <i :class="cat.icon" class="text-sm"></i>
                        </div>
                        
                        <div class="flex flex-col overflow-hidden">
                            <span class="text-xs font-bold transition-colors truncate" 
                                  :class="currentSlug === cat.slug ? 'text-white' : 'text-gray-400 group-hover:text-gray-200'" 
                                  x-text="cat.name"></span>
                        </div>
                        
                        <i class="fas fa-chevron-right ml-auto text-[10px] transition-transform duration-300"
                           :class="currentSlug === cat.slug ? 'text-cyan-400 translate-x-0' : 'text-gray-700 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0'"></i>
                    </a>
                </template>
            </div>
            
            <!-- Sidebar Footer -->
            <div class="p-3 border-t border-white/5 bg-black/40 backdrop-blur-xl z-10">
                <a href="/calculators" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl relative group overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900 border border-white/10 group-hover:border-white/20 transition-colors rounded-xl"></div>
                    <div class="relative z-10 flex items-center gap-2 text-gray-400 group-hover:text-white transition-colors">
                        <i class="fas fa-th-large text-xs"></i> 
                        <span class="text-xs font-bold">All Calculators</span>
                    </div>
                </a>
            </div>
        </aside>

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-black/90 z-40 md:hidden backdrop-blur-md"></div>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 relative flex flex-col h-full overflow-hidden bg-gray-950">
            
            <!-- Navbar -->
            <header class="h-16 flex items-center justify-between px-6 md:px-10 z-20">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="md:hidden w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white border border-white/10 active:scale-95 transition hover:bg-white/10">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <div class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></div>
                            <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-widest">Converter</span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight drop-shadow-lg" x-text="currentCategory.name"></h1>
                    </div>
                </div>
                
                <div class="hidden md:flex flex-col items-end">
                     <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20">
                        <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="text-[9px] font-bold text-green-400 uppercase tracking-wide">Online</span>
                    </div>
                </div>
            </header>

            <!-- Loading Indicator -->
            <div class="absolute top-0 left-0 w-full h-0.5 z-50 pointer-events-none opacity-0 transition-opacity duration-300" :class="{'opacity-100': loading}">
                <div class="h-full bg-gradient-to-r from-cyan-400 via-violet-500 to-pink-500 w-full animate-[progress_1s_infinite_linear]"></div>
            </div>

            <!-- Content Scrollable -->
            <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-32 md:pb-8 custom-scrollbar relative z-0">
                
                <div class="max-w-5xl mx-auto space-y-8 animate-fade-in-up pb-8">
                    
                    <!-- CONVERTER CARD -->
                    <div class="relative group perspective-1000 z-10">
                        <div class="absolute -inset-1 bg-gradient-to-r from-cyan-600 via-violet-600 to-pink-600 rounded-[2rem] blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                        <div class="relative glass-card rounded-[2rem] p-6 md:p-12 overflow-visible border-white/10 bg-[#0f172a]/90 backdrop-blur-2xl">
                            
                            <div class="flex flex-col xl:flex-row items-center gap-8 md:gap-12 relative z-10">
                                
                                <!-- INPUT SECTION -->
                                <div class="flex-1 w-full space-y-3 z-30">
                                    <div class="flex justify-between items-end px-2">
                                        <label class="text-[10px] font-black text-cyan-300 uppercase tracking-[0.2em]">Input Value</label>
                                    </div>
                                    
                                    <div class="relative group/input">
                                        <div class="absolute inset-0 bg-cyan-500/20 rounded-2xl blur-xl opacity-0 group-focus-within/input:opacity-100 transition duration-500"></div>
                                        <div class="relative bg-black/60 border border-white/10 rounded-2xl p-2 transition-all duration-300 group-focus-within/input:border-cyan-500/50 group-focus-within/input:bg-black/80">
                                            
                                            <!-- Value Input -->
                                            <input type="number" x-model.number="fromValue" @input="convert()" placeholder="0"
                                                   class="w-full bg-transparent border-none text-4xl md:text-5xl font-mono font-bold text-white p-4 pb-2 focus:ring-0 placeholder-white/10">
                                            
                                            <!-- Unit Selector -->
                                            <div class="p-1">
                                                <div class="relative" @click.outside="openDropdown = null">
                                                    <button @click="openDropdown = openDropdown === 'from' ? null : 'from'" 
                                                            class="w-full flex items-center justify-between bg-white/5 hover:bg-white/10 rounded-xl px-4 py-3 transition-all duration-200 group/btn border border-transparent hover:border-white/10 relative z-20">
                                                        <div class="flex flex-col items-start gap-0.5">
                                                            <span class="text-base font-bold text-white tracking-tight" x-text="fromUnit ? fromUnit.name : 'Select Unit'"></span>
                                                            <span class="text-[10px] font-mono text-cyan-400 bg-cyan-950/50 px-1.5 py-0.5 rounded border border-cyan-500/20" x-text="fromUnit ? fromUnit.symbol : '---'"></span>
                                                        </div>
                                                        <div class="w-6 h-6 rounded-full bg-white/5 flex items-center justify-center group-hover/btn:bg-white/10 transition-colors">
                                                            <i class="fas fa-chevron-down text-xs text-gray-400 group-hover/btn:text-white transition-colors"></i>
                                                        </div>
                                                    </button>

                                                    <!-- Dropdown -->
                                                    <div x-show="openDropdown === 'from'" x-transition.origin.top 
                                                         class="absolute top-full left-0 right-0 mt-2 bg-[#0f172a] border border-white/10 rounded-xl shadow-2xl z-50 overflow-hidden max-h-64 flex flex-col ring-1 ring-white/10 w-full" style="z-index: 100;">
                                                         <div class="p-2 border-b border-white/10 sticky top-0 bg-[#0f172a]/95 backdrop-blur z-10">
                                                             <div class="relative">
                                                                 <i class="fas fa-search absolute left-3 top-2.5 text-gray-500 text-xs"></i>
                                                                 <input type="text" x-model="unitSearch" placeholder="Find unit..." 
                                                                        class="w-full bg-black/50 border border-white/10 rounded-lg pl-8 pr-3 py-2 text-xs text-white focus:border-cyan-500/50 focus:outline-none placeholder-gray-600">
                                                             </div>
                                                         </div>
                                                         <div class="overflow-y-auto custom-scrollbar p-1 space-y-0.5">
                                                             <template x-for="unit in filteredUnitsList" :key="unit.symbol">
                                                                 <button @click="fromUnit = unit; openDropdown = null; convert()"
                                                                         class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-cyan-500/10 text-left transition group border border-transparent hover:border-cyan-500/10"
                                                                         :class="fromUnit && fromUnit.symbol === unit.symbol ? 'bg-cyan-500/20 border-cyan-500/20' : ''">
                                                                     <span class="text-xs font-bold text-gray-300 group-hover:text-white" x-text="unit.name"></span>
                                                                     <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-black/40 text-gray-400 group-hover:text-cyan-400" x-text="unit.symbol"></span>
                                                                 </button>
                                                             </template>
                                                         </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SWAP ACTION -->
                                <div class="relative z-20">
                                    <div class="absolute inset-0 bg-violet-600 blur opacity-40 rounded-full animate-pulse"></div>
                                    <button @click="swapUnits()" 
                                            class="relative w-14 h-14 rounded-full bg-[#0f172a] border border-white/10 flex items-center justify-center text-white hover:scale-110 active:scale-95 transition-all duration-300 group shadow-2xl">
                                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-cyan-500 to-violet-600 opacity-20 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        <i class="fas fa-exchange-alt text-lg relative z-10 text-gray-400 group-hover:text-white group-hover:rotate-180 transition-all duration-500"></i>
                                    </button>
                                </div>

                                <!-- OUTPUT SECTION -->
                                <div class="flex-1 w-full space-y-3 z-30">
                                    <div class="flex justify-between items-end px-2">
                                        <label class="text-[10px] font-black text-violet-300 uppercase tracking-[0.2em]">Result</label>
                                        <button @click="copyResult()" class="text-[9px] font-bold text-gray-500 hover:text-white flex items-center gap-1 transition-colors uppercase tracking-wider">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                    
                                    <div class="relative group/output">
                                        <div class="absolute inset-0 bg-violet-500/20 rounded-2xl blur-xl opacity-0 group-hover/output:opacity-100 transition duration-500"></div>
                                        <div class="relative bg-black/60 border border-white/10 rounded-2xl p-2 transition-all duration-300 group-hover:bg-black/80">
                                            
                                            <!-- Value Output -->
                                            <input type="text" :value="toValue" readonly
                                                   class="w-full bg-transparent border-none text-4xl md:text-5xl font-mono font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-200 to-cyan-200 p-4 pb-2 focus:ring-0 cursor-copy">
                                            
                                            <!-- Unit Selector -->
                                            <div class="p-1">
                                                <div class="relative" @click.outside="openDropdown = null">
                                                    <button @click="openDropdown = openDropdown === 'to' ? null : 'to'" 
                                                            class="w-full flex items-center justify-between bg-white/5 hover:bg-white/10 rounded-xl px-4 py-3 transition-all duration-200 group/btn border border-transparent hover:border-white/10 relative z-20">
                                                        <div class="flex flex-col items-start gap-0.5">
                                                            <span class="text-base font-bold text-white tracking-tight" x-text="toUnit ? toUnit.name : 'Select Unit'"></span>
                                                            <span class="text-[10px] font-mono text-violet-400 bg-violet-950/50 px-1.5 py-0.5 rounded border border-violet-500/20" x-text="toUnit ? toUnit.symbol : '---'"></span>
                                                        </div>
                                                        <div class="w-6 h-6 rounded-full bg-white/5 flex items-center justify-center group-hover/btn:bg-white/10 transition-colors">
                                                            <i class="fas fa-chevron-down text-xs text-gray-400 group-hover/btn:text-white transition-colors"></i>
                                                        </div>
                                                    </button>

                                                    <!-- Dropdown -->
                                                    <div x-show="openDropdown === 'to'" x-transition.origin.top 
                                                         class="absolute top-full left-0 right-0 mt-2 bg-[#0f172a] border border-white/10 rounded-xl shadow-2xl z-50 overflow-hidden max-h-64 flex flex-col ring-1 ring-white/10 w-full" style="z-index: 100;">
                                                         <div class="p-2 border-b border-white/10 sticky top-0 bg-[#0f172a]/95 backdrop-blur z-10">
                                                             <div class="relative">
                                                                 <i class="fas fa-search absolute left-3 top-2.5 text-gray-500 text-xs"></i>
                                                                 <input type="text" x-model="unitSearch" placeholder="Find unit..." 
                                                                        class="w-full bg-black/50 border border-white/10 rounded-lg pl-8 pr-3 py-2 text-xs text-white focus:border-violet-500/50 focus:outline-none placeholder-gray-600">
                                                             </div>
                                                         </div>
                                                         <div class="overflow-y-auto custom-scrollbar p-1 space-y-0.5">
                                                             <template x-for="unit in filteredUnitsList" :key="unit.symbol">
                                                                 <button @click="toUnit = unit; openDropdown = null; convert()"
                                                                         class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-violet-500/10 text-left transition group border border-transparent hover:border-violet-500/10"
                                                                         :class="toUnit && toUnit.symbol === unit.symbol ? 'bg-violet-500/20 border-violet-500/20' : ''">
                                                                     <span class="text-xs font-bold text-gray-300 group-hover:text-white" x-text="unit.name"></span>
                                                                     <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-black/40 text-gray-400 group-hover:text-violet-400" x-text="unit.symbol"></span>
                                                                 </button>
                                                             </template>
                                                         </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            <!-- ACTIONS BAR -->
                            <div class="mt-12 pt-8 border-t border-white/5 flex flex-wrap items-center justify-between gap-4">
                                
                                <div class="flex flex-wrap gap-3">
                                    <button @click="fromValue = fromValue * 2; convert()" class="group relative px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 overflow-hidden transition-all active:scale-95">
                                        <span class="relative z-10 text-xs font-bold text-gray-300 group-hover:text-white">x2</span>
                                        <div class="absolute inset-0 bg-cyan-500/10 translate-y-full group-hover:translate-y-0 transition-transform"></div>
                                    </button>
                                    <button @click="fromValue = fromValue / 2; convert()" class="group relative px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 overflow-hidden transition-all active:scale-95">
                                        <span class="relative z-10 text-xs font-bold text-gray-300 group-hover:text-white">/2</span>
                                        <div class="absolute inset-0 bg-cyan-500/10 translate-y-full group-hover:translate-y-0 transition-transform"></div>
                                    </button>
                                    <button @click="fromValue = 10; convert()" class="group relative px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 overflow-hidden transition-all active:scale-95">
                                        <span class="relative z-10 text-xs font-bold text-gray-300 group-hover:text-white">10</span>
                                        <div class="absolute inset-0 bg-cyan-500/10 translate-y-full group-hover:translate-y-0 transition-transform"></div>
                                    </button>
                                    <button @click="fromValue = 100; convert()" class="group relative px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 overflow-hidden transition-all active:scale-95">
                                        <span class="relative z-10 text-xs font-bold text-gray-300 group-hover:text-white">100</span>
                                        <div class="absolute inset-0 bg-cyan-500/10 translate-y-full group-hover:translate-y-0 transition-transform"></div>
                                    </button>
                                </div>

                                <button @click="fromValue = 0; convert()" class="ml-auto px-6 py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-xs font-bold text-red-400 hover:text-red-300 transition-all active:scale-95 flex items-center gap-2 group">
                                    <i class="fas fa-trash-alt group-hover:rotate-12 transition-transform"></i> Clear
                                </button>
                            </div>
                        </div>

                    <!-- Additional Info / Sponsor -->
                    <div class="grid md:grid-cols-2 gap-6 animate-fade-in-up animate-delay-200">
                        <div class="glass-card rounded-2xl p-6">
                            <h3 class="text-sm font-bold text-gray-400 mb-2">Formula</h3>
                            <div class="font-mono text-sm text-gray-300 bg-black/30 p-4 rounded-xl border border-white/5 leading-relaxed">
                                <span class="text-violet-400">result</span> = <span class="text-cyan-400">value</span> * <span class="text-white" x-text="fromUnit ? Number(fromUnit.to_base_multiplier).toExponential(2) : 1"></span> / <span class="text-white" x-text="toUnit ? Number(toUnit.to_base_multiplier).toExponential(2) : 1"></span>
                            </div>
                        </div>

                        <?php if(!empty($campaign)): ?>
                        <a href="<?= $campaign['website_url'] ?>" target="_blank" class="glass-card rounded-2xl p-6 relative overflow-hidden group block">
                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-green-500/10 opacity-50 group-hover:opacity-100 transition"></div>
                            <div class="relative z-10 flex flex-col h-full justify-center">
                                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-1">Sponsored</span>
                                <div class="text-lg font-bold text-white"><?= $campaign['ad_text'] ?: 'Recommended Tool' ?></div>
                                <div class="mt-2 text-xs text-gray-400 group-hover:text-emerald-300 transition flex items-center gap-2">
                                    Visit Partner <i class="fas fa-external-link-alt"></i>
                                </div>
                            </div>
                        </a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </main>
        
        <!-- Toast -->
        <div x-show="toast.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="translate-y-full opacity-0"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-6 py-3 rounded-full bg-white text-gray-900 font-bold shadow-[0_0_20px_rgba(255,255,255,0.3)] flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i>
            <span x-text="toast.message"></span>
        </div>

    </div>

    <script>
        function converterSPA() {
            return {
                sidebarOpen: false,
                loading: false,
                currentSlug: '<?= $category['slug'] ?>',
                currentCategory: <?= json_encode($category) ?>,
                allCategories: <?= json_encode($categories) ?>,
                units: <?= json_encode($units) ?>,
                
                searchQuery: '',
                unitSearch: '',
                openDropdown: null,
                
                fromValue: 1,
                toValue: 0,
                fromUnit: null,
                toUnit: null,
                
                toast: { show: false, message: '' },

                get filteredCategories() {
                    const q = this.searchQuery.toLowerCase();
                    return this.allCategories.filter(c => c.name.toLowerCase().includes(q));
                },

                get filteredUnitsList() {
                    const q = this.unitSearch.toLowerCase();
                    return this.units.filter(u => u.name.toLowerCase().includes(q) || u.symbol.toLowerCase().includes(q));
                },

                init() {
                    this.setupUnits();
                    this.convert();
                    
                    window.addEventListener('popstate', (e) => {
                        if (e.state && e.state.slug) this.loadCategory(e.state.slug, false);
                    });
                },

                setupUnits() {
                    this.fromUnit = this.units.find(u => u.base_unit == 1) || this.units[0];
                    this.toUnit = this.units.find(u => u.base_unit != 1) || this.units[1] || this.units[0];
                },

                async loadCategory(slug, pushState = true) {
                    if (this.currentSlug === slug) return;
                    this.loading = true;
                    this.sidebarOpen = false;
                    
                    try {
                    try {
                        const res = await fetch('<?= app_base_url('/calculator/api/data/') ?>' + slug);
                        const data = await res.json();
                        if (data.success) {
                            this.currentCategory = data.category;
                            this.units = data.units;
                            this.currentSlug = slug;
                            this.setupUnits();
                            this.convert();
                            if (pushState) {
                                history.pushState({ slug }, '', `/convert/${slug}`);
                                document.title = `Unit Converter - ${data.category.name}`;
                            }
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        setTimeout(() => this.loading = false, 500);
                    }
                },

                convert() {
                    if (!this.fromUnit || !this.toUnit) return;
                    let val = parseFloat(this.fromValue);
                    if (isNaN(val)) val = 0;
                    
                    let result = 0;

                    // Temperature Logic
                    if (this.currentCategory.id === 18 || this.currentCategory.slug === 'temperature') {
                        let celsius = 0;
                        if (this.fromUnit.symbol === '°C') celsius = val;
                        else if (this.fromUnit.symbol === '°F') celsius = (val - 32) / 1.8;
                        else if (this.fromUnit.symbol === 'K') celsius = val - 273.15;
                        else if (this.fromUnit.symbol === '°R') celsius = (val / 1.8) - 273.15;

                        if (this.toUnit.symbol === '°C') result = celsius;
                        else if (this.toUnit.symbol === '°F') result = (celsius * 1.8) + 32;
                        else if (this.toUnit.symbol === 'K') result = celsius + 273.15;
                        else if (this.toUnit.symbol === '°R') result = (celsius + 273.15) * 1.8;
                    } else {
                        // Standard
                        let base = val * parseFloat(this.fromUnit.to_base_multiplier);
                        result = base / parseFloat(this.toUnit.to_base_multiplier);
                    }
                    
                    this.toValue = Number.isInteger(result) ? result : parseFloat(result.toFixed(8));
                },

                swapUnits() {
                    [this.fromUnit, this.toUnit] = [this.toUnit, this.fromUnit];
                    this.convert();
                },

                copyResult() {
                    navigator.clipboard.writeText(this.toValue);
                    this.toast.message = 'Copied to clipboard!';
                    this.toast.show = true;
                    setTimeout(() => this.toast.show = false, 2000);
                }
            }
        }
    </script>
</body>
</html>
