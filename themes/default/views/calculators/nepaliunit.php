<?php // Layout is handled automatically by App\Core\View::render ?>

<div class="pink-precision-wrapper">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-deep: #09090b;
            --bg-surface: #18181b;
            --bg-card: #27272a;
            --accent: #ec4899; /* Pink Accent */
            --accent-glow: rgba(236, 72, 153, 0.2);
            --text-primary: #fafafa;
            --text-secondary: #a1a1aa;
            --text-dim: #71717a;
            --border-subtle: #3f3f46;
            --font-main: 'Outfit', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        body {
            background-color: var(--bg-deep) !important;
            color: var(--text-primary);
        }

        .pink-precision-wrapper {
            font-family: var(--font-main);
            height: 100vh;
            display: flex;
            background-color: var(--bg-deep);
            overflow: hidden;
        }

        /* --- Workspace Layout --- */
        .workspace {
            display: block;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        /* --- Sidebar (Inputs) --- */
        .sidebar {
            background: var(--bg-surface);
            border-right: 1px solid var(--border-subtle);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            z-index: 10;
        }

        .brand-sec {
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 1rem;
        }

        .brand-sec h1 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .brand-sec span {
            color: var(--accent);
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .label-mono {
            font-family: var(--font-mono);
            font-size: 0.65rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.4rem;
            display: block;
        }

        .input-field {
            background: var(--bg-deep);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            color: #fff;
            padding: 0.65rem 0.85rem;
            font-family: var(--font-mono);
            font-size: 1rem;
            width: 100%;
            transition: all 0.2s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px var(--accent-glow);
        }

        .select-field {
            background: var(--bg-deep);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            color: #fff;
            padding: 0.65rem 0.85rem;
            font-family: var(--font-main);
            font-size: 0.9rem;
            width: 100%;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2371717a'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.85rem center;
            background-size: 0.9rem;
        }

        .btn-action {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 0.85rem;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 0.5rem;
        }

        .btn-action:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);
        }

        /* --- Main Console --- */
        .main-console {
            background: var(--bg-deep);
            padding: 2rem 4rem;
            height: 100vh;
            overflow-y: auto;
            position: relative;
        }

        .dash-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 1rem;
        }

        .dash-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: #fff;
        }

        .dash-header .brand {
            color: var(--accent);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .hero-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.25rem; /* Reduced margin */
            opacity: 0;
            transform: translateY(15px);
            transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .hero-row.active {
            opacity: 1;
            transform: translateY(0);
        }

        .hero-slot {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 1rem 1.25rem; /* Compact vertical padding */
            display: flex;
            flex-direction: column;
            gap: 0.35rem; /* Tighter gap */
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            justify-content: center;
        }

        .hero-slot:hover {
            border-color: var(--accent);
            background: var(--bg-card);
            transform: translateY(-2px);
        }

        .hero-slot .label {
            color: var(--accent);
            font-weight: 700;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .hero-slot .value {
            font-family: var(--font-mono);
            font-size: 1.75rem; /* Big and Clear kept */
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.02em;
            word-break: break-all;
            line-height: 1.1;
        }

        .hero-slot .unit {
            font-size: 0.8rem;
            color: var(--text-dim);
            font-weight: 500;
        }

        .hero-slot.highlight {
            border-color: var(--accent);
            box-shadow: 0 0 20px var(--accent-glow);
        }

        /* --- Matrix Sections --- */
        .matrix-section {
            margin-bottom: 0.85rem; /* Aggressively reduced margin */
        }

        .matrix-header {
            font-size: 0.7rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 600;
            margin-bottom: 0.5rem; /* Reduced margin */
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .matrix-header::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-subtle);
        }

        .unit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.75rem; /* Tighter gap */
        }

        .unit-node {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 0.75rem 1rem; /* Compact padding */
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .unit-node:hover {
            border-color: var(--accent);
            background: var(--bg-card);
            transform: translateY(-2px);
        }

        .unit-node.highlight {
            border-color: var(--accent);
            box-shadow: inset 0 0 15px rgba(236, 72, 153, 0.05);
        }

        .unit-node .val {
            font-family: var(--font-mono);
            font-size: 1.25rem; /* Big and Clear kept */
            font-weight: 700;
            word-break: break-all;
            line-height: 1.2;
        }

        .unit-node .sys {
            font-size: 0.6rem;
            color: var(--accent);
            background: rgba(236, 72, 153, 0.1);
            padding: 1px 7px;
            border-radius: 4px;
            width: fit-content;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* --- Dashboard Inputs --- */
        .dash-input {
            background: transparent;
            border: none;
            color: #fff;
            font-family: var(--font-mono);
            font-weight: 700;
            width: 100%;
            padding: 0;
            margin: 0;
            outline: none;
            transition: color 0.2s ease;
        }

        .dash-input:focus {
            color: var(--accent);
        }

        .hero-slot .dash-input {
            font-size: 1.75rem;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .unit-node .dash-input {
            font-size: 1.25rem;
            line-height: 1.2;
        }

        /* Hide arrows/spinners */
        .dash-input::-webkit-outer-spin-button,
        .dash-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            appearance: none;
            margin: 0;
        }
        .dash-input[type=number] {
            -moz-appearance: textfield;
            appearance: textfield;
        }

        /* --- Information Strip --- */
        .info-strip {
            margin-top: 2rem; /* Reduced from 3rem */
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            border-top: 1px solid var(--border-subtle);
            padding: 1.5rem 0; /* Reduced padding */
        }

        .info-card {
            text-align: center;
            padding: 0.5rem;
        }

        .info-card h4 {
            color: var(--accent);
            font-size: 0.95rem; /* Restored clarity */
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .info-card p {
            color: var(--text-secondary);
            text-align: center;
        }

        /* --- Glance Cards --- */
        .glance-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .glance-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-top: 3px solid var(--accent);
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .glance-header {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .glance-header h3 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }

        .glance-header span {
            font-size: 0.8rem;
            color: var(--text-dim);
            font-weight: 500;
        }

        .glance-selector-box {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 0.85rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .glance-selector-box .unit-label {
            position: relative;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            font-family: var(--font-mono);
            display: flex;
            align-items: center;
            flex-grow: 1;
            max-width: 65%; /* Constrain width to prevent overlap */
        }

        .glance-selector-box .equiv-text {
            font-size: 0.75rem;
            color: var(--text-dim);
            white-space: nowrap;
            margin-left: 1rem;
            flex-shrink: 0;
            text-align: right;
            flex-grow: 1;
        }

        .glance-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: none;
            color: var(--accent);
            font-family: inherit;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            padding-right: 1.5rem;
            width: 100%;
            outline: none;
            z-index: 2;
        }

        .custom-chevron {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: var(--accent);
            font-size: 0.8rem;
            opacity: 0.8;
            z-index: 1;
        }

        .glance-values {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 1rem;
            text-align: center;
        }

        .glance-val-node {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .glance-val-node .v {
            font-family: var(--font-mono);
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            transition: all 0.2s ease;
        }

        .glance-val-node .v.blurred {
            filter: blur(4px);
            opacity: 0.5;
            transform: scale(0.95);
        }

        .glance-val-node .l {
            font-size: 0.65rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        @media (max-width: 992px) {
            .glance-section {
                grid-template-columns: 1fr;
            }
        }

        /* --- Toast Notification --- */
        .copy-toast {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: var(--accent);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.4);
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .copy-toast.active {
            transform: translateX(-50%) translateY(0);
        }

        /* --- Empty State --- */
        .empty-state {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            color: var(--text-dim);
        }

        .empty-state.d-none {
            display: none !important;
        }

        /* Scrollbar */
        .main-console::-webkit-scrollbar { width: 6px; }
        .main-console::-webkit-scrollbar-track { background: var(--bg-deep); }
        .main-console::-webkit-scrollbar-thumb { background: var(--border-subtle); border-radius: 10px; }
        .main-console::-webkit-scrollbar-thumb:hover { background: var(--text-dim); }

        @media (max-width: 1024px) {
            .workspace { 
                display: flex; /* Using flex column for better stack control */
                flex-direction: column;
                height: auto; 
                overflow: visible; 
            }
            .sidebar { 
                border-right: none; 
                border-bottom: 1px solid var(--border-subtle); 
                height: auto; 
                padding: 1.25rem;
                position: -webkit-sticky;
                position: sticky;
                top: 0;
                background: var(--bg-surface);
                z-index: 1000;
                width: 100%;
                box-shadow: 0 4px 20px rgba(0,0,0,0.6);
            }
            .main-console { 
                height: auto; 
                overflow: visible; 
                padding: 1.5rem 1.25rem;
                flex: 1;
            }
            .info-strip { grid-template-columns: 1fr; gap: 1rem; }
            .pink-precision-wrapper { height: auto; overflow: visible; }
            .hero-row { gap: 0.5rem; margin-bottom: 1.25rem; }
            .hero-slot { padding: 0.75rem 0.5rem; border-radius: 8px; }
            .hero-slot .label { font-size: 0.5rem; margin-bottom: 0.2rem; }
            .hero-slot .value { font-size: 1rem; }
            .hero-slot .unit { font-size: 0.55rem; }
            .unit-grid { 
                grid-template-columns: 1fr !important; /* Force 1 column strictly */
                gap: 0.75rem;
            }
            .matrix-header { font-size: 0.65rem; margin-top: 1.25rem; }
            .hero-result .unit { font-size: 1rem; }
        }

        @media (max-width: 480px) {
            .hero-result .value { font-size: 1.8rem; }
            .brand-sec { display: none; } /* Hide for compact sticky */
            .sidebar { padding: 0.75rem 1rem; }
            .input-field, .select-field { font-size: 0.8rem; height: 36px; padding: 0 0.5rem; }
            .label-mono { font-size: 0.6rem; margin-bottom: 0.2rem; }
            .control-group { gap: 0.5rem; }
        }
    </style>

    <div class="workspace">
        <main class="main-console">
            <div class="dash-header">
                <div>
                    <span class="brand">Pink Precision Toolkit</span>
                    <h1>Nepali Unit Calculator</h1>
                </div>
            </div>
            <div id="analysis-view" class="pink-precision-view">
                <div id="hero-row" class="hero-row">
                    <div id="hero-slot-sqm" class="hero-slot">
                        <span class="label">Metric Area</span>
                        <input type="number" step="any" class="dash-input" id="hero-val-sqm" value="0">
                        <span class="unit">Square Meters</span>
                    </div>
                    <div id="hero-slot-sqft" class="hero-slot">
                        <span class="label">Imperial Area</span>
                        <input type="number" step="any" class="dash-input" id="hero-val-sqft" value="0">
                        <span class="unit">Square Feet</span>
                    </div>
                    <div id="hero-slot-cross" class="hero-slot highlight" title="Click to copy full breakdown">
                        <span class="label">Cross System</span>
                        <input type="number" step="any" class="dash-input" id="hero-val-cross" value="0">
                        <span class="unit">Target Unit</span>
                    </div>
                </div>

                <div id="hilly-section" class="matrix-section">
                    <div class="matrix-header">Hilly Region (Ropani)</div>
                    <div id="hilly-grid" class="unit-grid">
                        <div class="unit-node" data-unit="ropani"><span class="sys">Ropani</span><input type="number" step="any" class="dash-input" id="dash-ropani" value="0"></div>
                        <div class="unit-node" data-unit="aana"><span class="sys">Aana</span><input type="number" step="any" class="dash-input" id="dash-aana" value="0"></div>
                        <div class="unit-node" data-unit="paisa"><span class="sys">Paisa</span><input type="number" step="any" class="dash-input" id="dash-paisa" value="0"></div>
                        <div class="unit-node" data-unit="daam"><span class="sys">Daam</span><input type="number" step="any" class="dash-input" id="dash-daam" value="0"></div>
                    </div>
                </div>

                <div id="terai-section" class="matrix-section">
                    <div class="matrix-header">Terai Region (Bigha)</div>
                    <div id="terai-grid" class="unit-grid">
                        <div class="unit-node" data-unit="bigha"><span class="sys">Bigha</span><input type="number" step="any" class="dash-input" id="dash-bigha" value="0"></div>
                        <div class="unit-node" data-unit="kattha"><span class="sys">Kattha</span><input type="number" step="any" class="dash-input" id="dash-kattha" value="0"></div>
                        <div class="unit-node" data-unit="dhur"><span class="sys">Dhur</span><input type="number" step="any" class="dash-input" id="dash-dhur" value="0"></div>
                    </div>
                </div>

                <div id="metric-section" class="matrix-section">
                    <div class="matrix-header">Metric Standards</div>
                    <div id="metric-grid" class="unit-grid">
                        <div class="unit-node" data-unit="sq_meter"><span class="sys">Sq. Meters</span><input type="number" step="any" class="dash-input" id="dash-sq_meter" value="0"></div>
                        <div class="unit-node" data-unit="sq_mm"><span class="sys">Sq. mm</span><input type="number" step="any" class="dash-input" id="dash-sq_mm" value="0"></div>
                        <div class="unit-node" data-unit="hectare"><span class="sys">Hectares</span><input type="number" step="any" class="dash-input" id="dash-hectare" value="0"></div>
                    </div>
                </div>

                <div id="imperial-section" class="matrix-section">
                    <div class="matrix-header">Imperial Standards</div>
                    <div id="imperial-grid" class="unit-grid">
                        <div class="unit-node" data-unit="sq_feet"><span class="sys">Sq. Feet</span><input type="number" step="any" class="dash-input" id="dash-sq_feet" value="0"></div>
                        <div class="unit-node" data-unit="sq_in"><span class="sys">Sq. Inches</span><input type="number" step="any" class="dash-input" id="dash-sq_in" value="0"></div>
                        <div class="unit-node" data-unit="acre"><span class="sys">Acres</span><input type="number" step="any" class="dash-input" id="dash-acre" value="0"></div>
                    </div>
                </div>

                <div class="info-strip">
                    <div class="info-card">
                        <h4>1 Ropani</h4>
                        <p>74ft x 74ft</p>
                        <p>16 Aana</p>
                    </div>
                    <div class="info-card">
                        <h4>1 Bigha</h4>
                        <p>13.31 Ropani</p>
                        <p>20 Kattha</p>
                    </div>
                    <div class="info-card">
                        <h4>1 Kattha</h4>
                        <p>442 sq. yards</p>
                        <p>338.63 sq. m</p>
                    </div>
                </div>

                <div class="glance-section">
                    <!-- Hilly Card -->
                    <div class="glance-card" data-region="hilly">
                        <div class="glance-header">
                            <h3>Hilly Area Conversion</h3>
                        </div>
                        <div class="glance-selector-box">
                            <div class="unit-label">
                                <select class="glance-select" data-region="hilly">
                                    <option value="ropani" selected>1 Ropani</option>
                                    <option value="aana">1 Aana</option>
                                    <option value="paisa">1 Paisa</option>
                                    <option value="daam">1 Daam</option>
                                </select>
                                <i class="fas fa-chevron-down custom-chevron"></i>
                            </div>
                            <div class="equiv-text">is equivalent to:</div>
                        </div>
                        <div class="glance-values" id="glance-hilly-values">
                            <div class="glance-val-node"><span class="v" data-unit="aana">16</span><span class="l">Aana</span></div>
                            <div class="glance-val-node"><span class="v" data-unit="paisa">64</span><span class="l">Paisa</span></div>
                            <div class="glance-val-node"><span class="v" data-unit="daam">256</span><span class="l">Daam</span></div>
                            <div class="glance-val-node"><span class="v" data-unit="sqm">508.72</span><span class="l">Sq. Mtr</span></div>
                            <div class="glance-val-node"><span class="v" data-unit="sqft">5476</span><span class="l">Sq. Feet</span></div>
                        </div>
                    </div>

                    <!-- Terai Card -->
                    <div class="glance-card" data-region="terai">
                        <div class="glance-header">
                            <h3>Terai Area Conversion</h3>
                        </div>
                        <div class="glance-selector-box">
                            <div class="unit-label">
                                <select class="glance-select" data-region="terai">
                                    <option value="bigha" selected>1 Bigha</option>
                                    <option value="kattha">1 Kattha</option>
                                    <option value="dhur">1 Dhur</option>
                                </select>
                                <i class="fas fa-chevron-down custom-chevron"></i>
                            </div>
                            <div class="equiv-text">is equivalent to:</div>
                        </div>
                        <div class="glance-values" id="glance-terai-values">
                            <div class="glance-val-node"><span class="v" data-unit="kattha">20</span><span class="l">Kattha</span></div>
                            <div class="glance-val-node"><span class="v" data-unit="dhur">400</span><span class="l">Dhur</span></div>
                            <div class="glance-val-node"><span class="v" data-unit="sqm">6772.63</span><span class="l">Sq. Mtr</span></div>
                            <div class="glance-val-node"><span class="v" data-unit="sqft">72900</span><span class="l">Sq. Feet</span></div>
                            <div class="glance-val-node"><span class="v" data-unit="ropani">13.31</span><span class="l">Ropani</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Copy Notification -->
    <div id="copy-toast" class="copy-toast">
        <i class="fas fa-check"></i>
        <span>COPIED TO CLIPBOARD</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Constants (Sq. Feet)
    const UNITS = {
        ropani: 5476, aana: 342.25, paisa: 85.5625, daam: 21.390625,
        bigha: 72900, kattha: 3645, dhur: 182.25,
        sq_meter: 10.7639104, sq_mm: 0.000107639104, hectare: 107639.104,
        sq_feet: 1, sq_in: 0.00694444, acre: 43560
    };

    const dashboardInputs = document.querySelectorAll('.dash-input');
    const crossUnitLabel = document.querySelector('#hero-slot-cross .unit');
    const crossSystemLabel = document.querySelector('#hero-slot-cross .label');
    const copyToast = document.getElementById('copy-toast');

    let currentSqFt = 0;
    let crossTargetUnit = 'bigha'; // Default cross target

    function format(val) {
        if (val === 0) return '0';
        return parseFloat(val.toFixed(6)).toString();
    }

    function syncAll(sourceId) {
        // Update Hero Slots
        if (sourceId !== 'hero-val-sqm') document.getElementById('hero-val-sqm').value = format(currentSqFt / UNITS.sq_meter);
        if (sourceId !== 'hero-val-sqft') document.getElementById('hero-val-sqft').value = format(currentSqFt);
        
        // Update Matrix - Hilly
        if (sourceId !== 'dash-ropani') document.getElementById('dash-ropani').value = format(currentSqFt / UNITS.ropani);
        if (sourceId !== 'dash-aana') document.getElementById('dash-aana').value = format(currentSqFt / UNITS.aana);
        if (sourceId !== 'dash-paisa') document.getElementById('dash-paisa').value = format(currentSqFt / UNITS.paisa);
        if (sourceId !== 'dash-daam') document.getElementById('dash-daam').value = format(currentSqFt / UNITS.daam);

        // Update Matrix - Terai
        if (sourceId !== 'dash-bigha') document.getElementById('dash-bigha').value = format(currentSqFt / UNITS.bigha);
        if (sourceId !== 'dash-kattha') document.getElementById('dash-kattha').value = format(currentSqFt / UNITS.kattha);
        if (sourceId !== 'dash-dhur') document.getElementById('dash-dhur').value = format(currentSqFt / UNITS.dhur);

        // Update Standards
        if (sourceId !== 'dash-sq_meter') document.getElementById('dash-sq_meter').value = format(currentSqFt / UNITS.sq_meter);
        if (sourceId !== 'dash-sq_mm') document.getElementById('dash-sq_mm').value = format(currentSqFt / UNITS.sq_mm);
        if (sourceId !== 'dash-hectare') document.getElementById('dash-hectare').value = format(currentSqFt / UNITS.hectare);
        if (sourceId !== 'dash-sq_feet') document.getElementById('dash-sq_feet').value = format(currentSqFt / UNITS.sq_feet);
        if (sourceId !== 'dash-sq_in') document.getElementById('dash-sq_in').value = format(currentSqFt / UNITS.sq_in);
        if (sourceId !== 'dash-acre') document.getElementById('dash-acre').value = format(currentSqFt / UNITS.acre);

        // Update Cross Hero
        const targetVal = currentSqFt / UNITS[crossTargetUnit];
        if (sourceId !== 'hero-val-cross') document.getElementById('hero-val-cross').value = format(targetVal);
        crossUnitLabel.innerText = crossTargetUnit.charAt(0).toUpperCase() + crossTargetUnit.slice(1);
        crossSystemLabel.innerText = crossTargetUnit === 'bigha' ? 'Terai Conversion' : 'Hilly Conversion';

        // Update Glance Values
        updateGlance();
    }

    function updateGlance() {
        // Hilly Glance
        const hillySel = document.querySelector('.glance-select[data-region="hilly"]');
        const hillyUnit = hillySel.value;
        const hBase = UNITS[hillyUnit];
        const hContainer = document.getElementById('glance-hilly-values');
        hContainer.querySelector('[data-unit="aana"]').innerText = format(hBase / UNITS.aana);
        hContainer.querySelector('[data-unit="paisa"]').innerText = format(hBase / UNITS.paisa);
        hContainer.querySelector('[data-unit="daam"]').innerText = format(hBase / UNITS.daam);
        hContainer.querySelector('[data-unit="sqm"]').innerText = format(hBase / UNITS.sq_meter);
        hContainer.querySelector('[data-unit="sqft"]').innerText = format(hBase / UNITS.sq_feet);

        // Terai Glance
        const teraiSel = document.querySelector('.glance-select[data-region="terai"]');
        const teraiUnit = teraiSel.value;
        const tBase = UNITS[teraiUnit];
        const tContainer = document.getElementById('glance-terai-values');
        tContainer.querySelector('[data-unit="kattha"]').innerText = format(tBase / UNITS.kattha);
        tContainer.querySelector('[data-unit="dhur"]').innerText = format(tBase / UNITS.dhur);
        tContainer.querySelector('[data-unit="sqm"]').innerText = format(tBase / UNITS.sq_meter);
        tContainer.querySelector('[data-unit="sqft"]').innerText = format(tBase / UNITS.sq_feet);
        tContainer.querySelector('[data-unit="ropani"]').innerText = format(tBase / UNITS.ropani);
    }

    // Dashboard Input Listeners
    dashboardInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            const id = e.target.id;
            const val = parseFloat(e.target.value) || 0;
            
            if (id.startsWith('dash-')) {
                const unit = id.replace('dash-', '');
                
                // Special handling for compound Hilly
                if (['ropani', 'aana', 'paisa', 'daam'].includes(unit)) {
                    currentSqFt = 
                        (parseFloat(document.getElementById('dash-ropani').value) || 0) * UNITS.ropani +
                        (parseFloat(document.getElementById('dash-aana').value) || 0) * UNITS.aana +
                        (parseFloat(document.getElementById('dash-paisa').value) || 0) * UNITS.paisa +
                        (parseFloat(document.getElementById('dash-daam').value) || 0) * UNITS.daam;
                    crossTargetUnit = 'bigha';
                } 
                // Special handling for compound Terai
                else if (['bigha', 'kattha', 'dhur'].includes(unit)) {
                    currentSqFt = 
                        (parseFloat(document.getElementById('dash-bigha').value) || 0) * UNITS.bigha +
                        (parseFloat(document.getElementById('dash-kattha').value) || 0) * UNITS.kattha +
                        (parseFloat(document.getElementById('dash-dhur').value) || 0) * UNITS.dhur;
                    crossTargetUnit = 'ropani';
                }
                else {
                    currentSqFt = val * UNITS[unit];
                }
            } else if (id === 'hero-val-sqm') {
                currentSqFt = val * UNITS.sq_meter;
            } else if (id === 'hero-val-sqft') {
                currentSqFt = val * UNITS.sq_feet;
            } else if (id === 'hero-val-cross') {
                currentSqFt = val * UNITS[crossTargetUnit];
            }
            
            syncAll(id);
        });
    });

    // Glance Listeners
    document.querySelectorAll('.glance-select').forEach(sel => {
        sel.addEventListener('change', updateGlance);
    });

    // Node Clicks -> Copy Breakdown
    document.querySelectorAll('.unit-node, .hero-slot').forEach(node => {
        node.addEventListener('click', (e) => {
            if (e.target.tagName === 'INPUT') return;
            
            let text = '';
            if (node.id === 'hero-slot-cross') {
                text = crossTargetUnit === 'bigha' ? getTeraiBreakdown(currentSqFt) : getHillyBreakdown(currentSqFt);
            } else if (node.closest('.matrix-section')?.id === 'hilly-section') {
                text = getHillyBreakdown(currentSqFt);
            } else if (node.closest('.matrix-section')?.id === 'terai-section') {
                text = getTeraiBreakdown(currentSqFt);
            } else {
                const input = node.querySelector('input');
                text = input ? input.value : '';
            }
            
            if (text) {
                navigator.clipboard.writeText(text).then(() => {
                    copyToast.classList.add('active');
                    setTimeout(() => copyToast.classList.remove('active'), 1500);
                });
            }
        });
    });

    function getHillyBreakdown(sqFt) {
        let rem = sqFt;
        const r = Math.floor(rem / UNITS.ropani); rem %= UNITS.ropani;
        const a = Math.floor(rem / UNITS.aana); rem %= UNITS.aana;
        const p = Math.floor(rem / UNITS.paisa); rem %= UNITS.paisa;
        const d = (rem / UNITS.daam).toFixed(2);
        return `${r} Ropani, ${a} Aana, ${p} Paisa, ${d} Daam`;
    }

    function getTeraiBreakdown(sqFt) {
        let rem = sqFt;
        const b = Math.floor(rem / UNITS.bigha); rem %= UNITS.bigha;
        const k = Math.floor(rem / UNITS.kattha); rem %= UNITS.kattha;
        const d = (rem / UNITS.dhur).toFixed(2);
        return `${b} Bigha, ${k} Kattha, ${d} Dhur`;
    }

    // Initial Sync
    document.getElementById('analysis-view').classList.remove('d-none');
    document.getElementById('hero-row').classList.add('active');
    syncAll('init');
});
</script>
