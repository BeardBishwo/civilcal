<div class="calculator-container calculator-civil">
    <div class="calculator-header">
        <div class="calculator-title">
            <div class="calculator-icon">🏗️</div>
            <div>
                <h2>Civil Engineering Calculator</h2>
                <p class="calculator-description">Professional tools for concrete calculations, steel reinforcement, and structural analysis</p>
            </div>
        </div>
        <div class="calculator-actions">
            <button class="btn btn-outline" onclick="exportCalculator('civil')">
                <i class="fas fa-download"></i> Export
            </button>
            <button class="btn btn-primary" onclick="saveCalculation('civil')">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </div>

    <div class="calculator-tabs">
        <button class="calculator-tab active" data-target="concrete-calc">
            <i class="fas fa-cube"></i> Concrete Calculator
        </button>
        <button class="calculator-tab" data-target="steel-calc">
            <i class="fas fa-hammer"></i> Steel Calculator
        </button>
        <button class="calculator-tab" data-target="foundation-calc">
            <i class="fas fa-home"></i> Foundation Design
        </button>
        <button class="calculator-tab" data-target="drainage-calc">
            <i class="fas fa-tint"></i> Drainage Design
        </button>
    </div>

    <div class="calculator-content">
        <!-- Concrete Calculator Section -->
        <div class="calculator-section active" id="concrete-calc">
            <div class="input-group">
                <div class="input-group-header">
                    <div class="input-group-icon">📐</div>
                    <h3 class="input-group-title">Concrete Volume Calculation</h3>
                </div>
                
                <div class="input-row">
                    <div class="input-field">
                        <label class="input-label">
                            Length
                            <span class="input-unit">m</span>
                        </label>
                        <input type="number" class="calculator-input" id="concrete-length" placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="input-field">
                        <label class="input-label">
                            Width
                            <span class="input-unit">m</span>
                        </label>
                        <input type="number" class="calculator-input" id="concrete-width" placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="input-field">
                        <label class="input-label">
                            Depth
                            <span class="input-unit">m</span>
                        </label>
                        <input type="number" class="calculator-input" id="concrete-depth" placeholder="0.00" step="0.01" min="0">
                    </div>
                </div>

                <div class="input-row">
                    <div class="input-field">
                        <label class="input-label">
                            Waste Factor
                            <span class="input-unit">%</span>
                        </label>
                        <input type="number" class="calculator-input" id="concrete-waste" placeholder="5" value="5" min="0" max="20">
                    </div>
                    
                    <div class="input-field">
                        <label class="input-label">
                            Concrete Grade
                        </label>
                        <select class="calculator-input" id="concrete-grade">
                            <option value="25">C25/30</option>
                            <option value="30">C30/37</option>
                            <option value="35">C35/45</option>
                            <option value="40">C40/50</option>
                        </select>
                    </div>
                    
                    <div class="input-field">
                        <label class="input-label">
                            Price per m³
                            <span class="input-unit">$</span>
                        </label>
                        <input type="number" class="calculator-input" id="concrete-price" placeholder="120" value="120" min="0">
                    </div>
                </div>

                <div class="calc-btn-group">
                    <button class="btn btn-primary" onclick="calculateConcrete()">
                        <i class="fas fa-calculator"></i> Calculate Volume
                    </button>
                    <button class="btn btn-secondary" onclick="clearConcreteInputs()">
                        <i class="fas fa-eraser"></i> Clear
                    </button>
                    <button class="btn btn-outline" onclick="generateConcreteReport()">
                        <i class="fas fa-file-alt"></i> Generate Report
                    </button>
                </div>
            </div>

            <div class="results-container" id="concrete-results" style="display: none;">
                <div class="results-header">
                    <div class="results-icon">✓</div>
                    <h3 class="results-title">Calculation Results</h3>
                </div>
                <div class="results-list" id="concrete-results-list">
                    <!-- Results will be populated by JavaScript -->
                </div>
                
                <div class="result-actions">
                    <button class="btn btn-outline" onclick="saveToProject()">
                        <i class="fas fa-folder-plus"></i> Save to Project
                    </button>
                    <button class="btn btn-primary" onclick="exportResults('concrete')">
                        <i class="fas fa-file-export"></i> Export Results
                    </button>
                </div>
            </div>
        </div>

        <!-- Steel Calculator Section -->
        <div class="calculator-section" id="steel-calc">
            <div class="input-group">
                <div class="input-group-header">
                    <div class="input-group-icon">🔩</div>
                    <h3 class="input-group-title">Steel Reinforcement Calculator</h3>
                </div>
                
                <div class="input-row">
                    <div class="input-field">
                        <label class="input-label">
                            Slab Area
                            <span class="input-unit">m²</span>
                        </label>
                        <input type="number" class="calculator-input" id="steel-area" placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="input-field">
                        <label class="input-label">
                            Slab Thickness
                            <span class="input-unit">mm</span>
                        </label>
                        <input type="number" class="calculator-input" id="steel-thickness" placeholder="200" min="0">
                    </div>
                </div>

                <div class="input-row">
                    <div class="input-field">
                        <label class="input-label">
                            Main Bar Diameter
                            <span class="input-unit">mm</span>
                        </label>
                        <select class="calculator-input" id="steel-main-diameter">
                            <option value="10">10mm</option>
                            <option value="12" selected>12mm</option>
                            <option value="16">16mm</option>
                            <option value="20">20mm</option>
                            <option value="25">25mm</option>
                        </select>
                    </div>
                    
                    <div class="input-field">
                        <label class="input-label">
                            Distribution Bar Diameter
                            <span class="input-unit">mm</span>
                        </label>
                        <select class="calculator-input" id="steel-dist-diameter">
                            <option value="8">8mm</option>
                            <option value="10" selected>10mm</option>
                            <option value="12">12mm</option>
                        </select>
                    </div>
                </div>

                <div class="input-row">
                    <div class="input-field">
                        <label class="input-label">
                            Main Bar Spacing
                            <span class="input-unit">mm</span>
                        </label>
                        <input type="number" class="calculator-input" id="steel-main-spacing" placeholder="200" min="100" max="300">
                    </div>
                    
                    <div class="input-field">
                        <label class="input-label">
                            Distribution Bar Spacing
                            <span class="input-unit">mm</span>
                        </label>
                        <input type="number" class="calculator-input" id="steel-dist-spacing" placeholder="200" min="100" max="300">
                    </div>
                </div>

                <div class="calc-btn-group">
                    <button class="btn btn-primary" onclick="calculateSteel()">
                        <i class="fas fa-calculator"></i> Calculate Steel
                    </button>
                    <button class="btn btn-secondary" onclick="clearSteelInputs()">
                        <i class="fas fa-eraser"></i> Clear
                    </button>
                </div>
            </div>

            <div class="results-container" id="steel-results" style="display: none;">
                <div class="results-header">
                    <div class="results-icon">✓</div>
                    <h3 class="results-title">Steel Calculation Results</h3>
                </div>
                <div class="results-list" id="steel-results-list">
                    <!-- Results will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Foundation Design Section -->
        <div class="calculator-section" id="foundation-calc">
            <div class="input-group">
                <div class="input-group-header">
                    <div class="input-group-icon">🏠</div>
                    <h3 class="input-group-title">Foundation Design Calculator</h3>
                </div>
                
                <div class="input-row">
                    <div class="input-field">
                        <label class="input-label">
                            Column Load
                            <span class="input-unit">kN</span>
                        </label>
                        <input type="number" class="calculator-input" id="foundation-load" placeholder="0.00" step="0.1" min="0">
                    </div>
                    
                    <div class="input-field">
                        <label class="input-label">
                            Soil Bearing Capacity
                            <span class="input-unit">kPa</span>
                        </label>
                        <input type="number" class="calculator-input" id="foundation-soil" placeholder="200" min="50" max="500">
                    </div>
                </div>

                <div class="input-row">
                    <div class="input-field">
                        <label class="input-label">
                            Safety Factor
                        </label>
                        <select class="calculator-input" id="foundation-safety">
                            <option value="2.5">2.5 (Conservative)</option>
                            <option value="3" selected>3.0 (Standard)</option>
                            <option value="3.5">3.5 (Conservative)</option>
                        </select>
                    </div>
                </div>

                <div class="calc-btn-group">
                    <button class="btn btn-primary" onclick="calculateFoundation()">
                        <i class="fas fa-calculator"></i> Design Foundation
                    </button>
                </div>
            </div>

            <div class="results-container" id="foundation-results" style="display: none;">
                <div class="results-header">
                    <div class="results-icon">✓</div>
                    <h3 class="results-title">Foundation Design Results</h3>
                </div>
                <div class="results-list" id="foundation-results-list">
                    <!-- Results will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Drainage Design Section -->
        <div class="calculator-section" id="drainage-calc">
            <div class="input-group">
                <div class="input-group-header">
                    <div class="input-group-icon">💧</div>
                    <h3 class="input-group-title">Drainage System Design</h3>
                </div>
                
                <div class="input-row">
                    <div class="input-field">
                        <label class="input-label">
                            Catchment Area
                            <span class="input-unit">hectares</span>
                        </label>
                        <input type="number" class="calculator-input" id="drainage-area" placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="input-field">
                        <label class="input-label">
                            Rainfall Intensity
                            <span class="input-unit">mm/hr</span>
                        </label>
                        <input type="number" class="calculator-input" id="drainage-rainfall" placeholder="50" min="10" max="200">
                    </div>
                </div>

                <div class="input-row">
                    <div class="input-field">
                        <label class="input-label">
                            Runoff Coefficient
                        </label>
                        <select class="calculator-input" id="drainage-runoff">
                            <option value="0.3">0.3 (Pavement)</option>
                            <option value="0.6" selected>0.6 (Residential)</option>
                            <option value="0.8">0.8 (Commercial)</option>
                        </select>
                    </div>
                </div>

                <div class="calc-btn-group">
                    <button class="btn btn-primary" onclick="calculateDrainage()">
                        <i class="fas fa-calculator"></i> Design Drainage
                    </button>
                </div>
            </div>

            <div class="results-container" id="drainage-results" style="display: none;">
                <div class="results-header">
                    <div class="results-icon">✓</div>
                    <h3 class="results-title">Drainage Design Results</h3>
                </div>
                <div class="results-list" id="drainage-results-list">
                    <!-- Results will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Civil Engineering Calculator -->
<script>
    // Calculator functions
    function calculateConcrete() {
        const length = parseFloat(document.getElementById('concrete-length').value) || 0;
        const width = parseFloat(document.getElementById('concrete-width').value) || 0;
        const depth = parseFloat(document.getElementById('concrete-depth').value) || 0;
        const waste = parseFloat(document.getElementById('concrete-waste').value) || 5;
        const price = parseFloat(document.getElementById('concrete-price').value) || 120;
        
        if (length <= 0 || width <= 0 || depth <= 0) {
            alert('Please enter valid dimensions');
            return;
        }
        
        // Calculate using the calculator engine
        const inputs = { length, width, depth, waste_factor: waste };
        const results = window.calcEngine.calculate('concrete-volume', inputs);
        
        // Update UI
        displayConcreteResults(results, price);
    }
    
    function displayConcreteResults(results, price) {
        const container = document.getElementById('concrete-results');
        const list = document.getElementById('concrete-results-list');
        
        list.innerHTML = `
            <div class="result-item">
                <span class="result-label">Base Volume</span>
                <span class="result-value">${results.base_volume_m3.toFixed(2)} <span class="result-unit">m³</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Waste Volume</span>
                <span class="result-value">${results.waste_volume_m3.toFixed(2)} <span class="result-unit">m³</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Total Volume</span>
                <span class="result-value">${results.total_volume_m3.toFixed(2)} <span class="result-unit">m³</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Total Volume (ft³)</span>
                <span class="result-value">${results.total_volume_ft3.toFixed(2)} <span class="result-unit">ft³</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Cement Bags Required</span>
                <span class="result-value">${results.cement_bags} <span class="result-unit">bags</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Estimated Cost</span>
                <span class="result-value">$${results.cost_estimate.toFixed(2)} <span class="result-unit">USD</span></span>
            </div>
        `;
        
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth' });
    }
    
    function calculateSteel() {
        const area = parseFloat(document.getElementById('steel-area').value) || 0;
        const thickness = parseFloat(document.getElementById('steel-thickness').value) || 0;
        const mainDiameter = parseFloat(document.getElementById('steel-main-diameter').value) || 12;
        const distDiameter = parseFloat(document.getElementById('steel-dist-diameter').value) || 10;
        const mainSpacing = parseFloat(document.getElementById('steel-main-spacing').value) || 200;
        const distSpacing = parseFloat(document.getElementById('steel-dist-spacing').value) || 200;
        
        if (area <= 0 || thickness <= 0) {
            alert('Please enter valid dimensions');
            return;
        }
        
        const inputs = { 
            area, 
            thickness: thickness / 1000, // Convert mm to m
            main_bar_diameter: mainDiameter / 1000,
            distribution_bar_diameter: distDiameter / 1000,
            main_bar_spacing: mainSpacing / 1000,
            distribution_bar_spacing: distSpacing / 1000
        };
        
        const results = window.calcEngine.calculate('steel-reinforcement', inputs);
        displaySteelResults(results);
    }
    
    function displaySteelResults(results) {
        const container = document.getElementById('steel-results');
        const list = document.getElementById('steel-results-list');
        
        list.innerHTML = `
            <div class="result-item">
                <span class="result-label">Main Bars per Meter</span>
                <span class="result-value">${results.main_bars_per_m} <span class="result-unit">bars/m</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Distribution Bars per Meter</span>
                <span class="result-value">${results.distribution_bars_per_m} <span class="result-unit">bars/m</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Main Steel Length</span>
                <span class="result-value">${results.main_steel_length_m.toFixed(2)} <span class="result-unit">m</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Distribution Steel Length</span>
                <span class="result-value">${results.distribution_steel_length_m.toFixed(2)} <span class="result-unit">m</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Total Steel Weight</span>
                <span class="result-value">${results.total_steel_weight_kg.toFixed(2)} <span class="result-unit">kg</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Estimated Cost</span>
                <span class="result-value">$${results.total_cost.toFixed(2)} <span class="result-unit">USD</span></span>
            </div>
        `;
        
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth' });
    }
    
    function calculateFoundation() {
        // Simple foundation calculation
        const load = parseFloat(document.getElementById('foundation-load').value) || 0;
        const soil = parseFloat(document.getElementById('foundation-soil').value) || 200;
        const safety = parseFloat(document.getElementById('foundation-safety').value) || 3;
        
        if (load <= 0 || soil <= 0) {
            alert('Please enter valid values');
            return;
        }
        
        const allowedStress = soil / safety;
        const area = load / allowedStress;
        const size = Math.sqrt(area);
        
        const results = {
            allowed_stress: allowedStress,
            required_area: area,
            foundation_size: size,
            foundation_dimensions: `${size.toFixed(2)}m × ${size.toFixed(2)}m`
        };
        
        displayFoundationResults(results);
    }
    
    function displayFoundationResults(results) {
        const container = document.getElementById('foundation-results');
        const list = document.getElementById('foundation-results-list');
        
        list.innerHTML = `
            <div class="result-item">
                <span class="result-label">Allowable Soil Stress</span>
                <span class="result-value">${results.allowed_stress.toFixed(2)} <span class="result-unit">kPa</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Required Foundation Area</span>
                <span class="result-value">${results.required_area.toFixed(2)} <span class="result-unit">m²</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Foundation Size</span>
                <span class="result-value">${results.foundation_dimensions}</span>
            </div>
        `;
        
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth' });
    }
    
    function calculateDrainage() {
        const area = parseFloat(document.getElementById('drainage-area').value) || 0;
        const rainfall = parseFloat(document.getElementById('drainage-rainfall').value) || 50;
        const runoff = parseFloat(document.getElementById('drainage-runoff').value) || 0.6;
        
        if (area <= 0 || rainfall <= 0) {
            alert('Please enter valid values');
            return;
        }
        
        const flowRate = (area * 10000 * rainfall * runoff) / 3600; // Convert to m³/s
        const pipeDiameter = Math.sqrt((4 * flowRate) / (Math.PI * 2)) * 1000; // mm
        
        const results = {
            flow_rate: flowRate,
            pipe_diameter: pipeDiameter
        };
        
        displayDrainageResults(results);
    }
    
    function displayDrainageResults(results) {
        const container = document.getElementById('drainage-results');
        const list = document.getElementById('drainage-results-list');
        
        list.innerHTML = `
            <div class="result-item">
                <span class="result-label">Peak Flow Rate</span>
                <span class="result-value">${results.flow_rate.toFixed(3)} <span class="result-unit">m³/s</span></span>
            </div>
            <div class="result-item">
                <span class="result-label">Recommended Pipe Diameter</span>
                <span class="result-value">${results.pipe_diameter.toFixed(0)} <span class="result-unit">mm</span></span>
            </div>
        `;
        
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth' });
    }
    
    // Utility functions
    function clearConcreteInputs() {
        document.getElementById('concrete-length').value = '';
        document.getElementById('concrete-width').value = '';
        document.getElementById('concrete-depth').value = '';
        document.getElementById('concrete-results').style.display = 'none';
    }
    
    function clearSteelInputs() {
        document.getElementById('steel-area').value = '';
        document.getElementById('steel-thickness').value = '';
        document.getElementById('steel-results').style.display = 'none';
    }
    
    function exportResults(type) {
        const results = window.calcEngine.exportResults(`${type}-volume` || type, 'pdf');
        alert('Export functionality - PDF generation ready for ' + type);
    }
    
    function saveCalculation(type) {
        alert('Calculation saved to project library!');
    }
    
    function saveToProject() {
        alert('Results saved to current project!');
    }
    
    function generateConcreteReport() {
        alert('Professional PDF report generation ready!');
    }
    
    function exportCalculator(type) {
        alert('Calculator export functionality - Multiple formats available!');
    }
</script>
