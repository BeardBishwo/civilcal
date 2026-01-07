<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Converter - <?php echo $category['name']; ?></title>
    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Theme & Calculator Styles -->
    <link rel="stylesheet" href="<?php echo app_base_url('/themes/default/assets/css/theme.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo app_base_url('/themes/default/assets/css/calculator-platform.css'); ?>?v=<?php echo time(); ?>">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .converter-loading-overlay {
            position: absolute; inset: 0; background: rgba(0,0,0,0.5); 
            backdrop-filter: blur(4px); z-index: 50; display: flex; 
            align-items: center; justify-content: center; border-radius: 1rem;
        }
        .nav-category.active {
            background: rgba(var(--bs-primary-rgb), 0.2);
            border-left-color: var(--bs-primary);
            color: var(--bs-primary);
        }
    </style>
</head>
<body class="bg-dark text-light">
    
    <div class="layout-wrapper" x-data="converterSPA()">
        
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="/" class="sidebar-brand">
                    <?php 
                    $site_meta = get_site_meta();
                    if (!empty($site_meta['logo'])): ?>
                        <img src="<?php echo htmlspecialchars($site_meta['logo']); ?>" alt="Logo" style="max-height: 40px; width: auto;">
                    <?php else: ?>
                        <i class="bi bi-grid-fill me-2 text-primary"></i>Civil Calculation
                    <?php endif; ?>
                </a>
            </div>
        
            <div class="sidebar-search">
                <div class="position-relative mt-3">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="sidebarSearch" class="form-control ps-5" placeholder="Search tools..." oninput="filterSidebar()">
                </div>
            </div>
        
            <nav class="sidebar-nav custom-scrollbar">
                <!-- Unit Converters -->
                <div class="nav-label mt-2">Converters</div>
                <?php foreach ($categories as $cat): ?>
                    <a href="/calculator/converter/<?= $cat['slug'] ?>" 
                       @click.prevent="loadCategory('<?= $cat['slug'] ?>')"
                       class="nav-category"
                       :class="{ 'active': currentSlug === '<?= $cat['slug'] ?>' }"
                       data-name="<?= htmlspecialchars($cat['name']) ?>">
                        <i class="<?= $cat['icon'] ?>"></i>
                        <span><?= htmlspecialchars($cat['name']) ?></span>
                    </a>
                <?php endforeach; ?>

                <!-- Scientific Modules (Moved to Bottom) -->
                <div class="nav-label mt-4">Modules</div>
                <a href="/calculator/scientific" 
                   @click.prevent="switchToScientific()"
                   class="nav-category" 
                   :class="{'active': mode === 'scientific'}"
                   data-name="Scientific Calculator">
                    <i class="bi bi-cpu"></i>
                    <span>Scientific</span>
                </a>
                <a href="/calculator/math/trigonometry" class="nav-category" data-name="Mathematics">
                    <i class="bi bi-calculator"></i>
                    <span>Mathematics</span>
                </a>
                <a href="/calculator/datetime/duration" class="nav-category" data-name="Date & Time">
                    <i class="bi bi-calendar-range"></i>
                    <span>Date & Time</span>
                </a>
                <a href="/calculator/finance/loan" class="nav-category" data-name="Finance">
                    <i class="bi bi-cash-coin"></i>
                    <span>Finance</span>
                </a>
                <a href="/calculator/health/bmi" class="nav-category" data-name="Health & Fitness">
                    <i class="bi bi-activity"></i>
                    <span>Health</span>
                </a>
                <a href="/calculator/physics/velocity" class="nav-category" data-name="Physics">
                    <i class="bi bi-lightning-charge"></i>
                    <span>Physics</span>
                </a>
                <a href="/calculator/chemistry/molar-mass" class="nav-category" data-name="Chemistry">
                    <i class="bi bi-radioactive"></i>
                    <span>Chemistry</span>
                </a>
                <a href="/calculator/statistics/basic" class="nav-category" data-name="Statistics">
                    <i class="bi bi-bar-chart-steps"></i>
                    <span>Statistics</span>
                </a>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content position-relative">
            
            <!-- Loading Overlay -->
            <div x-show="loading" class="converter-loading-overlay" x-transition x-cloak>
                <div class="spinner-border text-primary" role="status"></div>
            </div>

            <!-- SCIENTIFIC CALCULATOR MODE -->
            <div x-show="mode === 'scientific'" x-transition.opacity.duration.300ms>
                 <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold m-0"><i class="bi bi-cpu me-3 text-primary"></i>Scientific Dashboard</h2>
                    <span class="badge bg-primary rounded-pill px-3 py-2">Standard</span>
                </div>

                <div class="calc-standalone-card glass-card shadow-2xl p-4 mx-auto" style="max-width: 500px;">
                    <!-- Display -->
                    <div class="bg-dark rounded-3 p-3 mb-4 border border-secondary border-opacity-25 text-end position-relative" style="min-height: 100px;">
                        <div class="text-secondary small mb-1" style="min-height: 1.2rem;" x-text="calcExpr"></div>
                        <div class="text-white fw-bold overflow-hidden" style="font-size: 2.5rem; letter-spacing: 1px;" x-text="calcDisplay">0</div>
                        <div x-show="calcError" class="text-danger position-absolute bottom-0 end-0 p-2 small fw-bold">Error</div>
                    </div>

                    <!-- Keypad -->
                    <div class="d-grid gap-2" style="grid-template-columns: repeat(5, 1fr);">
                        <!-- Row 1 -->
                        <button class="btn btn-outline-danger scientific-btn" @click="clearCalc()">AC</button>
                        <button class="btn btn-outline-secondary scientific-btn border-glass" @click="appendFunc('sin')">sin</button>
                        <button class="btn btn-outline-secondary scientific-btn border-glass" @click="appendFunc('cos')">cos</button>
                        <button class="btn btn-outline-secondary scientific-btn border-glass" @click="appendFunc('tan')">tan</button>
                        <button class="btn btn-outline-secondary scientific-btn border-glass" @click="deleteCalcLast()"><i class="bi bi-backspace"></i></button>

                         <!-- Row 2 -->
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('7')">7</button>
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('8')">8</button>
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('9')">9</button>
                        <button class="btn btn-outline-secondary scientific-btn border-glass" @click="appendCalcOp('(')">(</button>
                        <button class="btn btn-outline-warning scientific-btn border-glass" @click="appendCalcOp('/')">÷</button>

                         <!-- Row 3 -->
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('4')">4</button>
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('5')">5</button>
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('6')">6</button>
                        <button class="btn btn-outline-secondary scientific-btn border-glass" @click="appendCalcOp(')')">)</button>
                        <button class="btn btn-outline-warning scientific-btn border-glass" @click="appendCalcOp('*')">×</button>

                         <!-- Row 4 -->
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('1')">1</button>
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('2')">2</button>
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('3')">3</button>
                        <button class="btn btn-outline-secondary scientific-btn border-glass" @click="appendCalcOp('pow')">xʸ</button>
                        <button class="btn btn-outline-warning scientific-btn border-glass" @click="appendCalcOp('-')">-</button>

                         <!-- Row 5 -->
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('0')">0</button>
                        <button class="btn btn-outline-light scientific-btn border-glass" @click="appendCalcNum('.')">.</button>
                        <button class="btn btn-outline-secondary scientific-btn border-glass" @click="appendIcon('π')">π</button>
                        <button class="btn btn-primary scientific-btn shadow-primary" @click="performCalculate()">=</button>
                        <button class="btn btn-outline-warning scientific-btn border-glass" @click="appendCalcOp('+')">+</button>
                    </div>
                </div>

                <div class="mt-4 glass-card p-3 mx-auto" style="max-width: 500px;">
                     <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase">History</span>
                        <button class="btn btn-sm btn-link text-muted text-decoration-none" @click="calcHistory = []" x-show="calcHistory.length > 0">Clear</button>
                    </div>
                     <div class="vstack gap-1">
                        <template x-for="(item, idx) in calcHistory" :key="idx">
                            <div class="d-flex justify-content-between align-items-center p-2 rounded hover-bg-light cursor-pointer" @click="loadHistory(item)">
                                <span class="text-secondary small font-monospace" x-text="item.expr"></span>
                                <span class="fw-bold text-white small" x-text="'= ' + item.res"></span>
                            </div>
                        </template>
                         <div x-show="calcHistory.length === 0" class="text-center text-muted small py-2">No calculations yet</div>
                     </div>
                </div>
            </div>

            <!-- UNIT CONVERTER MODE -->
            <div class="converter-container" x-show="mode === 'converter' && !loading" x-transition.opacity.duration.300ms>
                
                <!-- Sponsor Banner -->
                <template x-if="campaign">
                    <div class="sponsor-banner mb-4 p-3 rounded-3 shadow-sm d-flex align-items-center justify-content-between" 
                         style="background: linear-gradient(90deg, #f0fdf4, #ffffff); border-left: 5px solid #16a34a; position: relative; overflow: hidden;">
                        <div class="d-flex align-items-center gap-3" style="z-index: 1;">
                            <template x-if="campaign.logo_path">
                                <img :src="'/public/uploads/sponsors/' + campaign.logo_path" class="img-fluid" style="max-height: 50px; max-width: 120px; object-fit: contain;">
                            </template>
                            <template x-if="!campaign.logo_path">
                                <div class="bg-light rounded p-2 fw-bold text-success border" x-text="campaign.sponsor_name.substring(0,1)"></div>
                            </template>
                            <div>
                                <small class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.65rem; letter-spacing: 1px; line-height: 1;">Sponsored By</small>
                                <div class="fw-bold text-dark fs-6 mt-1" x-text="campaign.ad_text || campaign.sponsor_name"></div>
                            </div>
                        </div>
                        <a :href="campaign.website_url" target="_blank" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-sm" style="z-index: 1;">
                            <span x-text="campaign.cta_text || 'Visit Partner'"></span> <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </template>

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold m-0 d-flex align-items-center">
                        <i :class="currentCategory.icon" class="me-3 text-primary"></i>
                        <span x-text="currentCategory.name + ' Converter'"></span>
                    </h2>
                    <span class="badge bg-primary rounded-pill px-3 py-2">Premium Tool</span>
                </div>

                <!-- Converter Card -->
                <div class="converter-card glass-card shadow-lg p-4">
                    <div class="row g-3">
                        
                        <!-- From Section -->
                        <div class="col-md-5">
                            <label class="d-block text-secondary text-uppercase fw-bold ls-1 mb-2" style="font-size: 0.9rem;">From</label>
                            <input type="number" x-model.number="fromValue" @input="convert()" class="form-control form-control-lg bg-dark text-white border-glass mb-2 fw-bold font-monospace" placeholder="0">
                            
                            <!-- Custom Dropdown Trigger -->
                            <div class="position-relative" @click.outside="openDropdown = null">
                                <div @click="openDropdown = openDropdown === 'from' ? null : 'from'" 
                                     class="form-select-custom d-flex justify-content-between align-items-center text-white border-glass px-3 py-2 rounded pe-auto"
                                     :class="{'ring-primary': openDropdown === 'from'}">
                                    <div class="d-flex flex-column text-truncate">
                                        <span class="fw-medium" x-text="fromUnit ? fromUnit.name : 'Select Unit'"></span>
                                        <small class="text-secondary text-xs" x-text="fromUnit ? fromUnit.symbol : ''"></small>
                                    </div>
                                    <i class="bi bi-chevron-down text-secondary transition-transform" :class="{'rotate-180': openDropdown === 'from'}"></i>
                                </div>

                                <!-- Dropdown Menu -->
                                <div x-show="openDropdown === 'from'" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="position-absolute w-100 bg-glass-dark border border-secondary rounded-3 mt-2 shadow-2xl overflow-hidden" 
                                     style="z-index: 1050; backdrop-filter: blur(16px);">
                                    
                                    <div class="p-2 border-bottom border-secondary bg-dark bg-opacity-50">
                                        <input type="text" x-model="searchQuery" placeholder="Search unit..." class="form-control form-control-sm bg-transparent text-white border-0 shadow-none focus-none">
                                    </div>

                                    <div class="custom-scrollbar" style="max-height: 250px; overflow-y: auto;">
                                        <template x-for="unit in filteredUnits" :key="unit.symbol">
                                            <div @click="fromUnit = unit; openDropdown = null; convert();" 
                                                 class="dropdown-item-custom p-2 d-flex justify-content-between align-items-center cursor-pointer border-bottom border-secondary border-opacity-10" 
                                                 :class="{'active-unit': fromUnit && fromUnit.symbol === unit.symbol}">
                                                <span x-text="unit.name"></span>
                                                <span class="badge bg-secondary bg-opacity-25 text-light font-monospace" x-text="unit.symbol"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredUnits.length === 0" class="p-3 text-center text-muted small">No units found</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Swap Button -->
                        <div class="col-md-2 d-flex align-items-center justify-content-center py-3 py-md-0" style="padding-top: 32px;">
                            <button @click="swapUnits()" 
                                    class="btn-swap rounded-circle d-flex align-items-center justify-content-center shadow-lg hover-scale transition-all"
                                    style="width: 50px; height: 50px; background: var(--bs-primary); border: 4px solid rgba(0,0,0,0.2);">
                                <i class="bi bi-arrow-left-right fs-5 text-white"></i>
                            </button>
                        </div>

                        <!-- To Section -->
                        <div class="col-md-5">
                            <label class="d-block text-secondary text-uppercase fw-bold ls-1 mb-2" style="font-size: 0.9rem;">To</label>
                            <input type="text" :value="toValue" readonly class="form-control form-control-lg bg-dark text-white border-glass mb-2 fw-bold font-monospace">
                            
                            <!-- Custom Dropdown Trigger -->
                            <div class="position-relative" @click.outside="openDropdown = null">
                                <div @click="openDropdown = openDropdown === 'to' ? null : 'to'" 
                                     class="form-select-custom d-flex justify-content-between align-items-center text-white border-glass px-3 py-2 rounded pe-auto"
                                     :class="{'ring-primary': openDropdown === 'to'}">
                                    <div class="d-flex flex-column text-truncate">
                                        <span class="fw-medium" x-text="toUnit ? toUnit.name : 'Select Unit'"></span>
                                        <small class="text-secondary text-xs" x-text="toUnit ? toUnit.symbol : ''"></small>
                                    </div>
                                    <i class="bi bi-chevron-down text-secondary transition-transform" :class="{'rotate-180': openDropdown === 'to'}"></i>
                                </div>

                                <!-- Dropdown Menu -->
                                <div x-show="openDropdown === 'to'" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="position-absolute w-100 bg-glass-dark border border-secondary rounded-3 mt-2 shadow-2xl overflow-hidden" 
                                     style="z-index: 1050; backdrop-filter: blur(16px);">
                                    
                                    <div class="p-2 border-bottom border-secondary bg-dark bg-opacity-50">
                                        <input type="text" x-model="searchQuery" placeholder="Search unit..." class="form-control form-control-sm bg-transparent text-white border-0 shadow-none focus-none">
                                    </div>

                                    <div class="custom-scrollbar" style="max-height: 250px; overflow-y: auto;">
                                        <template x-for="unit in filteredUnits" :key="unit.symbol">
                                            <div @click="toUnit = unit; openDropdown = null; convert();" 
                                                 class="dropdown-item-custom p-2 d-flex justify-content-between align-items-center cursor-pointer border-bottom border-secondary border-opacity-10" 
                                                 :class="{'active-unit': toUnit && toUnit.symbol === unit.symbol}">
                                                <span x-text="unit.name"></span>
                                                <span class="badge bg-secondary bg-opacity-25 text-light font-monospace" x-text="unit.symbol"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredUnits.length === 0" class="p-3 text-center text-muted small">No units found</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .form-select-custom {
                            background: rgba(255, 255, 255, 0.03);
                            cursor: pointer;
                            transition: all 0.2s;
                            border: 1px solid rgba(255, 255, 255, 0.1);
                        }
                        .form-select-custom:hover {
                            background: rgba(255, 255, 255, 0.06);
                            border-color: rgba(255, 255, 255, 0.2);
                        }
                        .dropdown-item-custom:hover, .active-unit {
                            background: rgba(var(--bs-primary-rgb), 0.15);
                            color: var(--bs-primary);
                        }
                        .bg-glass-dark {
                            background: rgba(15, 23, 42, 0.95);
                        }
                        .rotate-180 { transform: rotate(180deg); }
                        .transition-transform { transition: transform 0.2s; }
                        .scientific-btn {
                            height: 60px;
                            font-size: 1.2rem;
                            font-weight: 500;
                            border-radius: 12px;
                            transition: all 0.1s;
                        }
                        .scientific-btn:active {
                            transform: scale(0.95);
                        }
                    </style>

                    <!-- Shortcuts -->
                    <hr class="glass-divider my-4">
                    <div class="shortcuts-container">
                        <!-- Row 1: Multipliers -->
                        <div class="d-flex justify-content-center gap-2 mb-2">
                             <div class="shortcut-btn-wrapper">
                                <i class="bi bi-calculator text-primary fs-5"></i>
                             </div>
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue *= 2; convert()">x2</button>
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue *= 3; convert()">x3</button>
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue *= 4; convert()">x4</button>
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue *= 5; convert()">x5</button>
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue *= 10; convert()">x10</button>
                        </div>
                        
                        <!-- Row 2: Divisors -->
                        <div class="d-flex justify-content-center gap-2">
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue = 1/fromValue; convert()">1/x</button>
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue /= 2; convert()">÷2</button>
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue /= 3; convert()">÷3</button>
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue /= 4; convert()">÷4</button>
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue /= 5; convert()">÷5</button>
                             <button class="btn btn-outline-light border-glass shortcut-square" @click="fromValue /= 10; convert()">÷10</button>
                        </div>
                    </div>

                    <style>
                        .shortcut-square {
                            width: 48px;
                            height: 40px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-weight: 600;
                            font-size: 0.9rem;
                            transition: all 0.2s;
                        }
                        .shortcut-square:hover {
                            background: rgba(255, 255, 255, 0.1);
                            transform: translateY(-2px);
                        }
                        .shortcut-btn-wrapper {
                            width: 48px;
                            height: 40px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            background: rgba(var(--bs-primary-rgb), 0.1);
                            border-radius: 6px;
                            border: 1px solid rgba(var(--bs-primary-rgb), 0.2);
                        }
                    </style>

                    <button class="btn btn-primary w-100 mt-4 rounded-pill fw-bold" @click="addToLog()">
                        <i class="bi bi-journal-plus me-2"></i> Add to Results Log
                    </button>
                </div>

                <!-- Logs -->
                <div class="results-log mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="m-0 fw-bold">Results Log</h4>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" @click="logs = []" x-show="logs.length > 0">Clear Log</button>
                    </div>
                    <div class="vstack gap-2">
                        <template x-for="(log, index) in logs" :key="index">
                            <div class="bg-white bg-opacity-10 p-3 rounded d-flex justify-content-between align-items-center animate-slide-in">
                                <div>
                                    <span class="text-muted" x-text="log.fromVal + ' ' + log.fromSym"></span>
                                    <i class="bi bi-arrow-right mx-2 text-primary"></i>
                                    <span class="fw-bold text-white" x-text="log.toVal + ' ' + log.toSym"></span>
                                </div>
                                <small class="text-secondary" x-text="log.cat"></small>
                            </div>
                        </template>
                        <div x-show="logs.length === 0" class="text-center py-4 text-muted">No entries in the log yet.</div>
                    </div>
                </div>
                
            </div>
        </main>
    </div>

    <!-- Alpine.js Application Logic -->
    <script>
    function converterSPA() {
        return {
            mode: 'scientific', // Default mode
            showMiniCalc: false,
            miniCalcDisplay: '0',
            showMiniCalc: false,
            miniCalcDisplay: '0',
            loading: false,
            currentSlug: '<?php echo $category['slug']; ?>',
            currentCategory: <?php echo json_encode($category); ?>,
            units: <?php echo json_encode($units); ?>,
            campaign: <?php echo json_encode($campaign ?? null); ?>,
            
            // Converter State
            fromValue: 1,
            toValue: 0,
            fromUnit: null,
            toUnit: null,
            openDropdown: null,
            searchQuery: '',
            logs: [],

            // Scientific State
            calcDisplay: '0',
            calcExpr: '',
            calcError: false,
            calcHistory: [],

            get filteredUnits() {
                if (!this.searchQuery) return this.units;
                return this.units.filter(u => u.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
            },

            init() {
                // Determine mode based on slug presence
                if (!this.currentSlug || this.currentSlug === 'scientific') {
                    this.mode = 'scientific';
                    document.title = "Scientific Calculator - Civil Calculation";
                } else {
                    this.mode = 'converter';
                    this.setupUnits();
                    this.convert();
                }

                // Handle browser back/forward
                window.addEventListener('popstate', (e) => {
                    if (e.state && e.state.slug) {
                        this.loadCategory(e.state.slug, false);
                    } else {
                        // If no state or no slug, revert to default (scientific)
                        this.mode = 'scientific';
                        this.currentSlug = '';
                    }
                });

                // Attach to Brand Link
                const brandLink = document.querySelector('.sidebar-header a');
                if(brandLink) {
                    brandLink.addEventListener('click', (e) => {
                         e.preventDefault();
                         this.switchToScientific();
                    });
                }
            },

            // --- MINI CALC LOGIC ---
            appendMiniCalc(val) {
                if(this.miniCalcDisplay === '0') this.miniCalcDisplay = '';
                this.miniCalcDisplay += val;
            },
            evalMiniCalc() {
                try {
                    // Safe evaluation for simple calc
                    this.miniCalcDisplay = eval(this.miniCalcDisplay).toString();
                } catch(e) {
                    this.miniCalcDisplay = 'Error';
                }
            },

            switchToScientific() {
                this.mode = 'scientific';
                this.currentSlug = 'scientific';
                history.pushState({ slug: 'scientific' }, '', '/calculator/scientific'); // Or root
                document.title = "Scientific Calculator";
            },

            // --- CONVERTER LOGIC ---
            setupUnits() {
                // Find base unit for 'from', and first non-base for 'to'
                this.fromUnit = this.units.find(u => u.base_unit == 1) || this.units[0];
                this.toUnit = this.units.find(u => u.base_unit != 1) || this.units[1] || this.units[0];
            },

            async loadCategory(slug, pushState = true) {
                if (this.currentSlug === slug) {
                    this.mode = 'converter'; // Ensure mode switch
                    return;
                }
                
                this.loading = true;
                
                try {
                    const response = await fetch(`/calculator/api/data/${slug}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.currentCategory = data.category;
                        this.units = data.units;
                        this.campaign = data.campaign;
                        this.currentSlug = slug;
                        this.mode = 'converter';
                        
                        this.setupUnits();
                        this.convert();
                        
                        if (pushState) {
                            history.pushState({ slug: slug }, '', `/calculator/converter/${slug}`);
                            document.title = `${data.category.name} Converter - Civil Calculation`;
                        }
                    }
                } catch (error) {
                    console.error('Failed to load category:', error);
                } finally {
                    setTimeout(() => { this.loading = false; }, 300); // Min Loading time for smoothness
                }
            },

            convert() {
                if (!this.fromUnit || !this.toUnit) return;
                
                let result = 0;
                const val = parseFloat(this.fromValue) || 0;

                // Special handling for Temperature (categoryId 18)
                if (this.currentCategory.id === 18) {
                    let celsius = 0;
                    // To Celsius
                    if (this.fromUnit.symbol === '°C') celsius = val;
                    else if (this.fromUnit.symbol === '°F') celsius = (val - 32) / 1.8;
                    else if (this.fromUnit.symbol === 'K') celsius = val - 273.15;
                    else if (this.fromUnit.symbol === '°R') celsius = (val / 1.8) - 273.15;

                    // From Celsius
                    if (this.toUnit.symbol === '°C') result = celsius;
                    else if (this.toUnit.symbol === '°F') result = (celsius * 1.8) + 32;
                    else if (this.toUnit.symbol === 'K') result = celsius + 273.15;
                    else if (this.toUnit.symbol === '°R') result = (celsius + 273.15) * 1.8;
                } else {
                    // Standard Factor Conversion
                    const baseValue = val * parseFloat(this.fromUnit.to_base_multiplier);
                    result = baseValue / parseFloat(this.toUnit.to_base_multiplier);
                }

                this.toValue = Number.isInteger(result) ? result : parseFloat(result.toFixed(6));
            },

            swapUnits() {
                const temp = this.fromUnit;
                this.fromUnit = this.toUnit;
                this.toUnit = temp;
                this.convert();
            },

            addToLog() {
                this.logs.unshift({
                    fromVal: this.fromValue,
                    fromSym: this.fromUnit.symbol,
                    toVal: this.toValue,
                    toSym: this.toUnit.symbol,
                    cat: this.currentCategory.name
                });
            },

            // --- SCIENTIFIC LOGIC ---
            appendCalcNum(num) {
                if(this.calcDisplay === '0' || this.calcDisplay === 'Error') this.calcDisplay = '';
                this.calcDisplay += num;
                this.calcError = false;
            },
            
            appendCalcOp(op) {
                if(op === 'pow') op = '^';
                this.calcDisplay += op;
            },

            appendFunc(func) {
                if(this.calcDisplay === '0') this.calcDisplay = '';
                this.calcDisplay += func + '(';
            },
            
            appendIcon(icon) {
                 if(this.calcDisplay === '0') this.calcDisplay = '';
                 this.calcDisplay += icon;
            },

            deleteCalcLast() {
                this.calcDisplay = this.calcDisplay.toString().slice(0, -1);
                if(this.calcDisplay === '') this.calcDisplay = '0';
            },

            clearCalc() {
                this.calcDisplay = '0';
                this.calcExpr = '';
                this.calcError = false;
            },

            async performCalculate() {
                if(!this.calcDisplay) return;
                this.calcExpr = this.calcDisplay + ' =';
                
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
                    
                    const response = await fetch('/calculator/api/calculate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-Token': csrfToken
                        },
                        body: new URLSearchParams({ expression: this.calcDisplay })
                    });
                    
                    const data = await response.json();
                    
                    if(data.success) {
                        this.calcHistory.unshift({ expr: this.calcDisplay, res: data.result });
                        this.calcDisplay = data.result.toString();
                    } else {
                        this.calcError = true;
                        this.calcDisplay = 'Error';
                    }
                } catch(e) {
                    this.calcError = true;
                    this.calcDisplay = 'Error';
                }
            },

            loadHistory(item) {
                this.calcDisplay = item.res.toString();
                this.calcExpr = item.expr + ' =';
            }
        }
    }
    </script>
</body>
</html>
