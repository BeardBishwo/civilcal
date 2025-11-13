<?php
// Start session and include necessary files
session_start();
require_once '../../../app/Config/db.php';
require_once '../../../app/Helpers/functions.php';
require_once '../../../themes/default/views/partials/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../../index.php" class="text-white-50">Home</a></li>
                    <li class="breadcrumb-item"><a href="../index.php" class="text-white-50">HVAC</a></li>
                    <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Equipment Sizing</a></li>
                    <li class="breadcrumb-item active text-white">AC Unit Sizing</li>
                </ol>
            </nav>
            <h1 class="text-white"><i class="fas fa-snowflake me-3"></i>AC Unit Sizing Calculator</h1>
            <p class="text-white-50">Calculate proper air conditioning unit size for residential and commercial applications</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Back to Equipment Sizing
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Calculator -->
        <div class="col-lg-8">
            <div class="glass-card p-4">
                <h4 class="mb-4"><i class="fas fa-calculator me-2"></i>AC Unit Sizing Calculation</h4>
                
                <div class="calculator-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Cooling Load</span>
                                <input type="number" class="form-control" id="totalCoolingLoad" placeholder="BTU/hr" step="100">
                                <span class="input-group-text">BTU/hr</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Climate Zone</span>
                                <select class="form-control" id="climateZone">
                                    <option value="hot">Hot/Humid</option>
                                    <option value="mixed">Mixed Humid</option>
                                    <option value="dry">Hot/Dry</option>
                                    <option value="marine">Marine</option>
                                    <option value="cold">Cold</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Safety Factor</span>
                                <input type="number" class="form-control" id="acSafetyFactor" placeholder="%" step="5" value="15">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary w-100 h-100" onclick="calculateACSize()">
                                <i class="fas fa-calculator me-2"></i>Calculate AC Size
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="results-section">
                    <div class="result-card" id="acSizeResult">
                        <h5><i class="fas fa-snowflake me-2"></i>AC Unit Sizing Results</h5>
                        <div id="acSizeOutput"></div>
                        <button class="btn btn-light btn-sm mt-3" onclick="clearResults('acSizeResult')">
                            <i class="fas fa-times me-1"></i>Clear Results
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="col-lg-4">
            <div class="glass-card p-4">
                <h4><i class="fas fa-info-circle me-2"></i>About AC Sizing</h4>
                <div class="info-content">
                    <p>Proper AC sizing is crucial for comfort, energy efficiency, and equipment longevity. Undersized units struggle to maintain temperature, while oversized units cycle frequently, wasting energy and reducing dehumidification.</p>
                    
                    <h6>Sizing Guidelines:</h6>
                    <ul>
                        <li>Use Manual J load calculation</li>
                        <li>Consider climate zone factors</li>
                        <li>Apply appropriate safety factors</li>
                        <li>Account for future expansion</li>
                    </ul>
                    
                    <h6>Climate Zones:</h6>
                    <ul>
                        <li>Hot/Humid: High latent loads</li>
                        <li>Hot/Dry: High sensible loads</li>
                        <li>Mixed: Balanced design</li>
                        <li>Marine: Corrosion considerations</li>
                        <li>Cold: Secondary heating needs</li>
                    </ul>
                </div>
            </div>

            <div class="glass-card p-4 mt-4">
                <h4><i class="fas fa-lightbulb me-2"></i>Quick Reference</h4>
                <div class="quick-reference">
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('sizingRules')">
                        Sizing Rules
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('climateFactors')">
                        Climate Factors
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('standardSizes')">
                        Standard Sizes
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('efficiency')">
                        Efficiency Standards
                    </button>
                </div>
            </div>

            <div class="glass-card p-4 mt-4">
                <h4><i class="fas fa-history me-2"></i>Recent Calculations</h4>
                <div id="recentCalculations">
                    <!-- Recent calculations will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateACSize() {
    const coolingLoad = parseFloat(document.getElementById('totalCoolingLoad').value);
    const climateZone = document.getElementById('climateZone').value;
    const safetyFactor = parseFloat(document.getElementById('acSafetyFactor').value) || 15;
    
    if (!coolingLoad) {
        alert('Please enter cooling load');
        return;
    }
    
    if (coolingLoad <= 0) {
        alert('Cooling load must be positive');
        return;
    }
    
    // Climate zone multipliers
    const climateMultipliers = {
        'hot': 1.0,        // Hot/Humid - standard sizing
        'mixed': 0.9,      // Mixed Humid - slightly less
        'dry': 0.85,       // Hot/Dry - less latent load
        'marine': 0.8,     // Marine - mild climate
        'cold': 0.7        // Cold - minimal cooling needed
    };
    
    const multiplier = climateMultipliers[climateZone] || 1.0;
    const adjustedLoad = coolingLoad * multiplier * (1 + safetyFactor / 100);
    
    // Convert to tons and find standard size
    const tons = adjustedLoad / 12000;
    
    // Standard AC sizes (tons)
    const standardSizes = [1.5, 2, 2.5, 3, 3.5, 4, 5, 6, 7.5, 10, 12.5, 15, 20, 25, 30];
    let recommendedSize = standardSizes.find(size => size >= tons) || 30;
    
    // Find next smaller size for comparison
    const smallerSize = standardSizes.filter(size => size < tons).pop() || 1.5;
    
    // SEER efficiency recommendations
    const getSEERRecommendation = (capacity) => {
        if (capacity >= 20) return 'SEER 16+ (Commercial Grade)';
        if (capacity >= 10) return 'SEER 15+ (High Efficiency)';
        if (capacity >= 5) return 'SEER 14+ (Standard)';
        return 'SEER 13+ (Minimum)';
    };
    
    // Energy cost calculation
    const annualOperatingHours = 2000; // Typical cooling season
    const seerRating = climateZone === 'cold' ? 13 : 16; // Assume SEER rating
    const energyCost = 0.12; // $/kWh
    const annualEnergy = (recommendedSize * 12000) / seerRating * annualOperatingHours / 1000;
    const annualCost = annualEnergy * energyCost;
    
    // Oversizing analysis
    const actualCapacity = recommendedSize * 12000;
    const oversizingPercent = ((actualCapacity - adjustedLoad) / adjustedLoad * 100).toFixed(1);
    
    const climateNames = {
        'hot': 'Hot/Humid Climate',
        'mixed': 'Mixed Humid Climate', 
        'dry': 'Hot/Dry Climate',
        'marine': 'Marine Climate',
        'cold': 'Cold Climate'
    };
    
    const resultHTML = `
        <div class="result-grid">
            <div class="result-item">
                <div class="result-label">Base Cooling Load</div>
                <div class="result-value">${coolingLoad.toLocaleString()} BTU/hr</div>
            </div>
            <div class="result-item">
                <div class="result-label">Climate Factor</div>
                <div class="result-value">${(multiplier * 100).toFixed(0)}%</div>
            </div>
            <div class="result-item">
                <div class="result-label">Adjusted Load</div>
                <div class="result-value">${adjustedLoad.toLocaleString()} BTU/hr</div>
            </div>
            <div class="result-item highlight">
                <div class="result-label">Required Capacity</div>
                <div class="result-value">${tons.toFixed(2)} Tons</div>
            </div>
        </div>
        
        <div class="result-details">
            <h6><i class="fas fa-cog me-2"></i>Recommended Equipment</h6>
            <div class="equipment-section">
                <div class="equipment-item">
                    <strong>AC Unit Size:</strong> ${recommendedSize} Tons (${(recommendedSize * 12000).toLocaleString()} BTU/hr)
                </div>
                <div class="equipment-item">
                    <strong>Efficiency:</strong> ${getSEERRecommendation(recommendedSize)}
                </div>
                <div class="equipment-item">
                    <strong>Oversizing:</strong> ${oversizingPercent}% (acceptable range: 5-15%)
                </div>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-leaf me-2"></i>Energy Analysis</h6>
                <p><strong>Annual Energy Use:</strong> ${annualEnergy.toLocaleString()} kWh</p>
                <p><strong>Annual Operating Cost:</strong> $${annualCost.toLocaleString()}</p>
                <p><strong>Climate Zone:</strong> ${climateNames[climateZone]}</p>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-chart-bar me-2"></i>Sizing Comparison</h6>
                <p><strong>Next Smaller Size:</strong> ${smallerSize} Tons (${(smallerSize * 12000).toLocaleString()} BTU/hr)</p>
                <p><strong>Capacity Difference:</strong> ${(recommendedSize - smallerSize).toFixed(1)} Tons</p>
                <p><strong>Recommendation:</strong> ${tons > smallerSize + 0.5 ? 'Use recommended size' : 'Consider smaller size'}</p>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Installation Notes</h6>
                <p>• Verify electrical service capacity</p>
                <p>• Check condenser unit placement clearances</p>
                <p>• Ensure proper ductwork sizing</p>
                <p>• Consider sound levels for residential applications</p>
            </div>
        </div>
    `;
    
    document.getElementById('acSizeOutput').innerHTML = resultHTML;
    document.getElementById('acSizeResult').style.display = 'block';
    
    saveCalculation('AC Unit Sizing', `${coolingLoad.toLocaleString()}BTU/hr → ${recommendedSize} tons ${climateNames[climateZone]}`);
}

// Reference functions
function showReference(type) {
    let message = '';
    
    switch(type) {
        case 'sizingRules':
            message = 'AC Sizing Rules and Guidelines:\n\n' +
                     'Manual J Load Calculation:\n' +
                     '• Accurately calculate heat gains/losses\n' +
                     '• Account for all building components\n' +
                     '• Consider orientation and shading\n' +
                     '• Include infiltration and ventilation\n\n' +
                     'Safety Factors:\n' +
                     '• Standard: 10-15%\n' +
                     '• Uncertain loads: 15-20%\n' +
                     '• Future expansion: 20-25%\n' +
                     '• Avoid over-sizing > 25%\n\n' +
                     'Critical Factors:\n' +
                     '• Climate zone design conditions\n' +
                     '• Building thermal characteristics\n' +
                     '• Occupancy and usage patterns\n' +
                     '• Equipment efficiency ratings';
            break;
        case 'climateFactors':
            message = 'Climate Zone Sizing Factors:\n\n' +
                     'Hot/Humid (Factor 1.0):\n' +
                     '• High external latent loads\n' +
                     '• Standard sizing practices\n' +
                     '• Focus on dehumidification\n' +
                     '• Slightly higher SHR recommended\n\n' +
                     'Hot/Dry (Factor 0.85):\n' +
                     '• Low external humidity\n' +
                     '• Reduced latent requirements\n' +
                     '• Higher sensible heat ratios\n' +
                     '• Nighttime ventilation benefits\n\n' +
                     'Mixed Humid (Factor 0.9):\n' +
                     '• Moderate humidity levels\n' +
                     '• Balanced design approach\n' +
                     '• Seasonal variation considerations\n\n' +
                     'Marine (Factor 0.8):\n' +
                     '• Mild temperatures year-round\n' +
                     '• Lower peak loads\n' +
                     '• Corrosion protection needed\n\n' +
                     'Cold (Factor 0.7):\n' +
                     '• Minimal cooling requirements\n' +
                     '• Heat pump consideration\n' +
                     '• Backup heating integration';
            break;
        case 'standardSizes':
            message = 'Standard AC Unit Sizes:\n\n' +
                     'Residential Split Systems:\n' +
                     '• 1.5, 2, 2.5, 3, 3.5, 4, 5 tons\n' +
                     '• Most common: 2-3 tons\n' +
                     '• 240V single-phase power\n' +
                     '• SEER ratings: 13-22+\n\n' +
                     'Commercial Systems:\n' +
                     '• 6, 7.5, 10, 12.5, 15, 20, 25, 30 tons\n' +
                     '• 208/230V or 460V 3-phase\n' +
                     '• EER/IPLV ratings important\n' +
                     '• Variable speed options available\n\n' +
                     'Package Units:\n' +
                     '• 3-15 tons (residential light commercial)\n' +
                     '• Rooftop or ground mount\n' +
                     '• All-in-one design\n' +
                     '• Self-contained systems\n\n' +
                     'Capacity Selection:\n' +
                     '• Round up to next standard size\n' +
                     '• Avoid over-sizing by > 25%\n' +
                     '• Consider multi-stage equipment';
            break;
        case 'efficiency':
            message = 'AC Efficiency Standards:\n\n' +
                     'SEER (Seasonal Energy Efficiency):\n' +
                     '• Minimum: 13 SEER (older systems)\n' +
                     '• Standard: 14-16 SEER\n' +
                     '• High Efficiency: 17-22 SEER\n' +
                     '• ENERGY STAR: 15+ SEER\n\n' +
                     'EER (Energy Efficiency Ratio):\n' +
                     '• Instantaneous efficiency measure\n' +
                     '• Important for peak load analysis\n' +
                     '• Higher EER = lower operating costs\n\n' +
                     'IPLV (Integrated Part Load Value):\n' +
                     '• Commercial system efficiency\n' +
                     '• Weighted average at part loads\n' +
                     '• Accounts for variable demand\n\n' +
                     'Cost-Benefit Analysis:\n' +
                     '• High efficiency pays back in 5-10 years\n' +
                     '• ENERGY STAR rebate programs available\n' +
                     '• Consider local electricity rates\n' +
                     '• Longer equipment life with proper sizing';
            break;
    }
    
    alert(message);
}

// Utility functions
function saveCalculation(type, calculation) {
    let recent = JSON.parse(localStorage.getItem('recentACCalculations') || '[]');
    recent.unshift({
        type: type,
        calculation: calculation,
        timestamp: new Date().toLocaleString()
    });
    
    // Keep only last 10 calculations
    recent = recent.slice(0, 10);
    localStorage.setItem('recentACCalculations', JSON.stringify(recent));
    loadRecentCalculations();
}

function loadRecentCalculations() {
    const recent = JSON.parse(localStorage.getItem('recentACCalculations') || '[]');
    const container = document.getElementById('recentCalculations');
    
    if (recent.length === 0) {
        container.innerHTML = '<p class="text-muted small">No recent calculations</p>';
        return;
    }
    
    container.innerHTML = recent.map(calc => `
        <div class="recent-item mb-2 p-2 border rounded">
            <div class="small"><strong>${calc.type}</strong></div>
            <div class="small text-muted">${calc.calculation}</div>
            <div class="small text-muted">${calc.timestamp}</div>
        </div>
    `).join('');
}

function clearResults(resultId) {
    document.getElementById(resultId).style.display = 'none';
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadRecentCalculations();
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.id === 'totalCoolingLoad') {
            calculateACSize();
        }
    });
});
</script>

<style>
.result-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.result-item {
    text-align: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}

.result-item.highlight {
    background: rgba(76, 175, 80, 0.2);
    border-left-color: var(--success-color);
}

.result-label {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 0.5rem;
}

.result-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
}

.result-details {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 1.5rem;
}

.result-section {
    margin-bottom: 1.5rem;
}

.result-section h6 {
    color: var(--primary-color);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.equipment-section {
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.equipment-item {
    margin-bottom: 0.5rem;
    padding: 0.25rem 0;
}

.info-content h6 {
    color: var(--primary-color);
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 0.25rem;
}

.info-content h6:first-child {
    margin-top: 0;
}

.info-content ul {
    margin-bottom: 1rem;
}

.recent-item {
    background: rgba(255, 255, 255, 0.05);
    transition: background 0.3s;
}

.recent-item:hover {
    background: rgba(255, 255, 255, 0.1);
}

.input-group-text {
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--dark-color);
    min-width: 120px;
}

@media (max-width: 768px) {
    .result-grid {
        grid-template-columns: 1fr;
    }
    
    .calculator-form .row .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>

<?php require_once '../../../themes/default/views/partials/footer.php'; ?>



