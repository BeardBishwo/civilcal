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
                    <li class="breadcrumb-item active text-white">Furnace Sizing</li>
                </ol>
            </nav>
            <h1 class="text-white"><i class="fas fa-fire me-3"></i>Furnace Sizing Calculator</h1>
            <p class="text-white-50">Calculate proper furnace size and capacity for heating applications</p>
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
                <h4 class="mb-4"><i class="fas fa-calculator me-2"></i>Furnace Sizing Calculation</h4>

                <div class="calculator-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Heating Load</span>
                                <input type="number" class="form-control" id="totalHeatingLoad" placeholder="BTU/hr" step="100">
                                <span class="input-group-text">BTU/hr</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Efficiency</span>
                                <select class="form-control" id="furnaceEfficiency">
                                    <option value="80">80% AFUE</option>
                                    <option value="90">90% AFUE</option>
                                    <option value="95">95% AFUE</option>
                                    <option value="98">98% AFUE</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Fuel Type</span>
                                <select class="form-control" id="fuelType">
                                    <option value="naturalGas">Natural Gas</option>
                                    <option value="propane">Propane</option>
                                    <option value="oil">Fuel Oil</option>
                                    <option value="electric">Electric</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary w-100 h-100" onclick="calculateFurnaceSize()">
                                <i class="fas fa-calculator me-2"></i>Calculate Furnace Size
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="results-section">
                    <div class="result-card" id="furnaceSizeResult">
                        <h5><i class="fas fa-fire me-2"></i>Furnace Sizing Results</h5>
                        <div id="furnaceSizeOutput"></div>
                        <button class="btn btn-light btn-sm mt-3" onclick="clearResults('furnaceSizeResult')">
                            <i class="fas fa-times me-1"></i>Clear Results
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="col-lg-4">
            <div class="glass-card p-4">
                <h4><i class="fas fa-info-circle me-2"></i>About Furnace Sizing</h4>
                <div class="info-content">
                    <p>Proper furnace sizing ensures efficient heating, optimal comfort, and equipment longevity. The heating load calculation forms the basis for selecting the correct furnace capacity.</p>

                    <h6>Sizing Considerations:</h6>
                    <ul>
                        <li>Manual J heating load calculation</li>
                        <li>Equipment efficiency (AFUE rating)</li>
                        <li>Climate zone design temperature</li>
                        <li>Building envelope characteristics</li>
                    </ul>

                    <h6>Efficiency Ratings:</h6>
                    <ul>
                        <li>80% AFUE: Standard efficiency</li>
                        <li>90% AFUE: High efficiency</li>
                        <li>95% AFUE: Condensing furnace</li>
                        <li>98% AFUE: Top tier efficiency</li>
                    </ul>
                </div>
            </div>

            <div class="glass-card p-4 mt-4">
                <h4><i class="fas fa-lightbulb me-2"></i>Quick Reference</h4>
                <div class="quick-reference">
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('sizingGuidelines')">
                        Sizing Guidelines
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('efficiencyImpact')">
                        Efficiency Impact
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('fuelComparison')">
                        Fuel Comparison
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('installation')">
                        Installation Notes
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
function calculateFurnaceSize() {
    const heatingLoad = parseFloat(document.getElementById('totalHeatingLoad').value);
    const efficiency = parseFloat(document.getElementById('furnaceEfficiency').value);
    const fuelType = document.getElementById('fuelType').value;

    if (!heatingLoad) {
        showNotification('Please enter heating load', 'info');
        return;
    }

    if (heatingLoad <= 0) {
        showNotification('Heating load must be positive', 'info');
        return;
    }

    // Adjust for efficiency - required input capacity
    const inputCapacity = heatingLoad / (efficiency / 100);

    // Standard furnace sizes (BTU/hr input)
    const standardSizes = [40000, 60000, 80000, 100000, 120000, 140000, 160000, 200000];
    let recommendedSize = standardSizes.find(size => size >= inputCapacity) || 200000;

    // Find next smaller size for comparison
    const smallerSize = standardSizes.filter(size => size < inputCapacity).pop() || 40000;

    // Calculate actual output capacity
    const outputCapacity = recommendedSize * (efficiency / 100);

    // Oversizing analysis
    const oversizingPercent = ((outputCapacity - heatingLoad) / heatingLoad * 100).toFixed(1);

    // Fuel cost calculations
    const fuelCosts = {
        'naturalGas': 1.20, // $/therm
        'propane': 2.50, // $/gallon
        'oil': 3.80, // $/gallon
        'electric': 0.12 // $/kWh
    };

    const fuelEfficiencies = {
        'naturalGas': 1.0, // 1 therm = 100,000 BTU
        'propane': 0.82, // 1 gallon = 91,600 BTU
        'oil': 0.70, // 1 gallon = 138,500 BTU
        'electric': 3.412 // 1 kWh = 3,412 BTU
    };

    const fuelCost = fuelCosts[fuelType] || 1.20;
    const fuelEfficiency = fuelEfficiencies[fuelType] || 1.0;

    // Annual operating cost calculation (assume 2,000 heating hours)
    const annualOperatingHours = 2000;
    const annualHeatOutput = heatingLoad * annualOperatingHours; // BTU/year
    const annualFuelInput = annualHeatOutput / fuelEfficiency; // BTU/year of fuel
    const annualCost = (annualFuelInput / 1000) * (fuelCost / 10); // Simplified conversion

    const fuelNames = {
        'naturalGas': 'Natural Gas',
        'propane': 'Propane',
        'oil': 'Fuel Oil',
        'electric': 'Electric'
    };

    const efficiencyNames = {
        '80': 'Standard Efficiency',
        '90': 'High Efficiency',
        '95': 'Condensing',
        '98': 'Top Tier'
    };

    const resultHTML = `
        <div class="result-grid">
            <div class="result-item">
                <div class="result-label">Heating Load</div>
                <div class="result-value">${heatingLoad.toLocaleString()} BTU/hr</div>
            </div>
            <div class="result-item">
                <div class="result-label">Efficiency</div>
                <div class="result-value">${efficiency}% AFUE</div>
            </div>
            <div class="result-item">
                <div class="result-label">Required Input</div>
                <div class="result-value">${inputCapacity.toLocaleString()} BTU/hr</div>
            </div>
            <div class="result-item highlight">
                <div class="result-label">Recommended Size</div>
                <div class="result-value">${recommendedSize.toLocaleString()} BTU/hr</div>
            </div>
        </div>
        
        <div class="result-details">
            <h6><i class="fas fa-cog me-2"></i>Furnace Specifications</h6>
            <div class="equipment-section">
                <div class="equipment-item">
                    <strong>Input Capacity:</strong> ${recommendedSize.toLocaleString()} BTU/hr
                </div>
                <div class="equipment-item">
                    <strong>Output Capacity:</strong> ${outputCapacity.toLocaleString()} BTU/hr
                </div>
                <div class="equipment-item">
                    <strong>Efficiency Rating:</strong> ${efficiency}% AFUE (${efficiencyNames[efficiency]})
                </div>
                <div class="equipment-item">
                    <strong>Fuel Type:</strong> ${fuelNames[fuelType]}
                </div>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-chart-bar me-2"></i>Sizing Analysis</h6>
                <p><strong>Oversizing:</strong> ${oversizingPercent}% (acceptable: 5-15%)</p>
                <p><strong>Next Smaller Size:</strong> ${smallerSize.toLocaleString()} BTU/hr</p>
                <p><strong>Capacity Difference:</strong> ${(recommendedSize - smallerSize).toLocaleString()} BTU/hr</p>
                <p><strong>Recommendation:</strong> ${oversizingPercent <= 15 ? 'Appropriate sizing' : 'Consider smaller unit'}</p>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-dollar-sign me-2"></i>Operating Cost Analysis</h6>
                <p><strong>Estimated Annual Cost:</strong> $${annualCost.toLocaleString()}</p>
                <p><strong>Fuel Cost:</strong> $${fuelCost} per unit</p>
                <p><strong>Heating Season:</strong> 2,000 hours assumed</p>
                <p><strong>Cost per BTU:</strong> $${(annualCost / annualHeatOutput * 1000000).toFixed(4)} per million BTU</p>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-info-circle me-2"></i>System Integration</h6>
                <p>• Consider heat pump for milder climates</p>
                <p>• Verify gas line sizing and pressure</p>
                <p>• Ensure proper venting for high-efficiency units</p>
                <p>• Check electrical requirements for blowers</p>
            </div>
        </div>
    `;

    document.getElementById('furnaceSizeOutput').innerHTML = resultHTML;
    document.getElementById('furnaceSizeResult').style.display = 'block';

    saveCalculation('Furnace Sizing', `${heatingLoad.toLocaleString()}BTU/hr → ${recommendedSize.toLocaleString()}BTU/hr ${fuelNames[fuelType]}`);
}

// Reference functions
function showReference(type) {
    let message = '';

    switch (type) {
        case 'sizingGuidelines':
            message = 'Furnace Sizing Guidelines:\n\n' +
                'Load Calculation Methods:\n' +
                '• Manual J: ACCA residential standard\n' +
                '• Consider all building heat losses\n' +
                '• Include infiltration and ventilation\n' +
                '• Account for duct heat losses\n\n' +
                'Sizing Factors:\n' +
                '• Design temperature difference\n' +
                '• Building envelope R-values\n' +
                '• Infiltration rates (ACH)\n' +
                '• Internal heat gains\n' +
                '• Safety factor: 5-15%\n\n' +
                'Common Mistakes:\n' +
                '• Oversizing due to "bigger is better"\n' +
                '• Ignoring equipment efficiency\n' +
                '• Not considering climate variations\n' +
                '• Forgetting to include duct losses';
            break;
        case 'efficiencyImpact':
            message = 'Efficiency Impact on Sizing:\n\n' +
                'AFUE (Annual Fuel Utilization Efficiency):\n' +
                '• 80% AFUE: Standard units, lower cost\n' +
                '• 90% AFUE: High efficiency, condensing\n' +
                '• 95% AFUE: Premium condensing units\n' +
                '• 98% AFUE: Top tier efficiency\n\n' +
                'Sizing Impact:\n' +
                '• Higher AFUE = smaller input required\n' +
                '• More upfront cost, lower operating cost\n' +
                '• Longer payback in mild climates\n' +
                '• Better environmental performance\n\n' +
                'Operating Cost Comparison:\n' +
                '• 80% vs 90%: ~12% fuel savings\n' +
                '• 90% vs 95%: ~5% additional savings\n' +
                '• Consider local fuel costs\n' +
                '• Calculate simple payback period';
            break;
        case 'fuelComparison':
            message = 'Fuel Type Comparison:\n\n' +
                'Natural Gas:\n' +
                '• Lowest operating cost in most areas\n' +
                '• Clean burning, reliable supply\n' +
                '• Requires gas line infrastructure\n' +
                '• Moderate equipment cost\n\n' +
                'Propane:\n' +
                '• Higher cost than natural gas\n' +
                '• Good for rural areas\n' +
                '• Requires storage tank\n' +
                '• Similar efficiency to natural gas\n\n' +
                'Fuel Oil:\n' +
                '• Declining usage in new construction\n' +
                '• Requires storage and delivery\n' +
                '• Higher maintenance requirements\n' +
                '• Environmental considerations\n\n' +
                'Electric:\n' +
                '• Highest operating cost\n' +
                '• No combustion products\n' +
                '• 100% efficiency at point of use\n' +
                '• Good for heat pump systems';
            break;
        case 'installation':
            message = 'Installation Considerations:\n\n' +
                'Gas Line Requirements:\n' +
                '• Verify adequate gas pressure\n' +
                '• Check meter capacity\n' +
                '• Ensure proper line sizing\n' +
                '• Consider future expansion needs\n\n' +
                'Venting Requirements:\n' +
                '• Standard efficiency: Conventional vent\n' +
                '• High efficiency: PVC vent pipe\n' +
                '• Follow manufacturer specifications\n' +
                '• Consider combustion air requirements\n\n' +
                'Electrical Requirements:\n' +
                '• Most require 120V control circuit\n' +
                '• High-efficiency units may need 240V\n' +
                '• Check circuit breaker capacity\n' +
                '• Consider variable speed blowers\n\n' +
                'Ductwork Considerations:\n' +
                '• Ensure proper sealing\n' +
                '• Check static pressure requirements\n' +
                '• Consider zoning for larger systems\n' +
                '• Verify return air provisions';
            break;
    }

    showNotification(message, 'info');
}

// Utility functions
function saveCalculation(type, calculation) {
    let recent = JSON.parse(localStorage.getItem('recentFurnaceCalculations') || '[]');
    recent.unshift({
        type: type,
        calculation: calculation,
        timestamp: new Date().toLocaleString()
    });

    // Keep only last 10 calculations
    recent = recent.slice(0, 10);
    localStorage.setItem('recentFurnaceCalculations', JSON.stringify(recent));
    loadRecentCalculations();
}

function loadRecentCalculations() {
    const recent = JSON.parse(localStorage.getItem('recentFurnaceCalculations') || '[]');
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
        if (e.key === 'Enter' && e.target.id === 'totalHeatingLoad') {
            calculateFurnaceSize();
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
    background: rgba(220, 53, 69, 0.2);
    border-left-color: #dc3545;
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



