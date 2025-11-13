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
                    <li class="breadcrumb-item active text-white">Chiller Sizing</li>
                </ol>
            </nav>
            <h1 class="text-white"><i class="fas fa-industry me-3"></i>Chiller Sizing Calculator</h1>
            <p class="text-white-50">Calculate proper chiller capacity for commercial and industrial cooling applications</p>
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
                <h4 class="mb-4"><i class="fas fa-calculator me-2"></i>Chiller Sizing Calculation</h4>
                
                <div class="calculator-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Total Cooling Load</span>
                                <input type="number" class="form-control" id="chillerLoad" placeholder="Tons" step="0.1">
                                <span class="input-group-text">Tons</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Chiller Type</span>
                                <select class="form-control" id="chillerType">
                                    <option value="airCooled">Air Cooled</option>
                                    <option value="waterCooled">Water Cooled</option>
                                    <option value="absorption">Absorption</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Safety Factor</span>
                                <input type="number" class="form-control" id="chillerSafety" placeholder="%" step="5" value="10">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Efficiency</span>
                                <select class="form-control" id="chillerEfficiency">
                                    <option value="standard">Standard (EER 10-12)</option>
                                    <option value="high">High Efficiency (EER 13-16)</option>
                                    <option value="premium">Premium (EER 17+)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary w-100" onclick="calculateChillerSize()">
                        <i class="fas fa-calculator me-2"></i>Calculate Chiller Size
                    </button>
                </div>

                <!-- Results Section -->
                <div class="results-section">
                    <div class="result-card" id="chillerSizeResult">
                        <h5><i class="fas fa-industry me-2"></i>Chiller Sizing Results</h5>
                        <div id="chillerSizeOutput"></div>
                        <button class="btn btn-light btn-sm mt-3" onclick="clearResults('chillerSizeResult')">
                            <i class="fas fa-times me-1"></i>Clear Results
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="col-lg-4">
            <div class="glass-card p-4">
                <h4><i class="fas fa-info-circle me-2"></i>About Chiller Sizing</h4>
                <div class="info-content">
                    <p>Proper chiller sizing is critical for commercial and industrial cooling systems. The selection depends on cooling load, efficiency requirements, installation constraints, and operating conditions.</p>
                    
                    <h6>Sizing Considerations:</h6>
                    <ul>
                        <li>Peak and part-load cooling requirements</li>
                        <li>Chiller type and efficiency ratings</li>
                        <li>Temperature and flow conditions</li>
                        <li>Installation and maintenance access</li>
                    </ul>
                    
                    <h6>Chiller Types:</h6>
                    <ul>
                        <li>Air Cooled: Lower cost, outdoor installation</li>
                        <li>Water Cooled: Higher efficiency, cooling tower required</li>
                        <li>Absorption: Heat recovery, district heating</li>
                    </ul>
                </div>
            </div>

            <div class="glass-card p-4 mt-4">
                <h4><i class="fas fa-lightbulb me-2"></i>Quick Reference</h4>
                <div class="quick-reference">
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('sizingFactors')">
                        Sizing Factors
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('efficiencyRatings')">
                        Efficiency Ratings
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('typeComparison')">
                        Type Comparison
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('installation')">
                        Installation Guidelines
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
function calculateChillerSize() {
    const chillerLoad = parseFloat(document.getElementById('chillerLoad').value);
    const chillerType = document.getElementById('chillerType').value;
    const safetyFactor = parseFloat(document.getElementById('chillerSafety').value) || 10;
    const efficiency = document.getElementById('chillerEfficiency').value;
    
    if (!chillerLoad) {
        alert('Please enter chiller load');
        return;
    }
    
    if (chillerLoad <= 0) {
        alert('Chiller load must be positive');
        return;
    }
    
    // Type-specific adjustment factors
    const typeFactors = {
        'airCooled': 1.0,      // Air cooled - standard reference
        'waterCooled': 0.9,    // Water cooled - typically more efficient
        'absorption': 1.2      // Absorption - less efficient, larger
    };
    
    const factor = typeFactors[chillerType] || 1.0;
    const adjustedLoad = chillerLoad * factor * (1 + safetyFactor / 100);
    
    // Standard chiller sizes (tons)
    const standardSizes = [10, 20, 30, 40, 50, 60, 75, 100, 125, 150, 200, 250, 300, 400, 500];
    let recommendedSize = standardSizes.find(size => size >= adjustedLoad) || 500;
    
    // Find next smaller size for comparison
    const smallerSize = standardSizes.filter(size => size < adjustedLoad).pop() || 10;
    
    // Efficiency ratings
    const efficiencyData = {
        'standard': { eer: 11, name: 'Standard Efficiency', kwPerTon: 1.2 },
        'high': { eer: 15, name: 'High Efficiency', kwPerTon: 0.88 },
        'premium': { eer: 18, name: 'Premium Efficiency', kwPerTon: 0.74 }
    };
    
    const effData = efficiencyData[efficiency] || efficiencyData.standard;
    
    // Power consumption calculation
    const powerKW = recommendedSize * effData.kwPerTon;
    const annualOperatingHours = 4000; // Assume 4000 hours per year operation
    const annualEnergy = powerKW * annualOperatingHours;
    const electricityCost = 0.12; // $/kWh
    const annualCost = annualEnergy * electricityCost;
    
    // Oversizing analysis
    const actualCapacity = recommendedSize;
    const oversizingPercent = ((actualCapacity - chillerLoad) / chillerLoad * 100).toFixed(1);
    
    const typeNames = {
        'airCooled': 'Air Cooled Chiller',
        'waterCooled': 'Water Cooled Chiller',
        'absorption': 'Absorption Chiller'
    };
    
    const resultHTML = `
        <div class="result-grid">
            <div class="result-item">
                <div class="result-label">Base Cooling Load</div>
                <div class="result-value">${chillerLoad.toFixed(1)} Tons</div>
            </div>
            <div class="result-item">
                <div class="result-label">Type Factor</div>
                <div class="result-value">${(factor * 100).toFixed(0)}%</div>
            </div>
            <div class="result-item">
                <div class="result-label">Adjusted Load</div>
                <div class="result-value">${adjustedLoad.toFixed(1)} Tons</div>
            </div>
            <div class="result-item highlight">
                <div class="result-label">Recommended Size</div>
                <div class="result-value">${recommendedSize} Tons</div>
            </div>
        </div>
        
        <div class="result-details">
            <h6><i class="fas fa-cog me-2"></i>Chiller Specifications</h6>
            <div class="equipment-section">
                <div class="equipment-item">
                    <strong>Capacity:</strong> ${recommendedSize} Tons (${(recommendedSize * 12000).toLocaleString()} BTU/hr)
                </div>
                <div class="equipment-item">
                    <strong>Type:</strong> ${typeNames[chillerType]}
                </div>
                <div class="equipment-item">
                    <strong>Efficiency:</strong> ${effData.name} (${effData.eer} EER)
                </div>
                <div class="equipment-item">
                    <strong>Power Consumption:</strong> ${powerKW.toFixed(1)} kW
                </div>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-chart-bar me-2"></i>Sizing Analysis</h6>
                <p><strong>Oversizing:</strong> ${oversizingPercent}% (acceptable: 5-15%)</p>
                <p><strong>Next Smaller Size:</strong> ${smallerSize} Tons</p>
                <p><strong>Capacity Difference:</strong> ${(recommendedSize - smallerSize).toFixed(1)} Tons</p>
                <p><strong>Recommendation:</strong> ${oversizingPercent <= 20 ? 'Appropriate sizing' : 'Consider smaller unit'}</p>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-leaf me-2"></i>Energy Analysis</h6>
                <p><strong>Annual Energy Use:</strong> ${annualEnergy.toLocaleString()} kWh</p>
                <p><strong>Annual Operating Cost:</strong> $${annualCost.toLocaleString()}</p>
                <p><strong>Energy Performance:</strong> ${effData.kwPerTon} kW/ton</p>
                <p><strong>Operating Hours:</strong> ${annualOperatingHours.toLocaleString()}/year</p>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>System Requirements</h6>
                <p>• Verify electrical service capacity (${(powerKW * 1.25).toFixed(0)} kA recommended)</p>
                <p>• Check cooling water requirements (water cooled only)</p>
                <p>• Ensure adequate space for maintenance access</p>
                <p>• Consider sound levels for occupied areas</p>
            </div>
        </div>
    `;
    
    document.getElementById('chillerSizeOutput').innerHTML = resultHTML;
    document.getElementById('chillerSizeResult').style.display = 'block';
    
    saveCalculation('Chiller Sizing', `${chillerLoad} tons → ${recommendedSize} tons ${typeNames[chillerType]}`);
}

// Reference functions
function showReference(type) {
    let message = '';
    
    switch(type) {
        case 'sizingFactors':
            message = 'Chiller Sizing Factors:\n\n' +
                     'Load Considerations:\n' +
                     '• Peak cooling load analysis\n' +
                     '• Part-load performance curves\n' +
                     '• Diversity factors for multiple chillers\n' +
                     '• Future expansion provisions\n\n' +
                     'System Factors:\n' +
                     '• Chilled water supply/return temperatures\n' +
                     '• Building cooling distribution system\n' +
                     '• Internal heat gains and losses\n' +
                     '• Ambient design conditions\n\n' +
                     'Safety Guidelines:\n' +
                     '• 10-15% safety factor typical\n' +
                     '• Consider equipment redundancy\n' +
                     '• Account for degraded performance over time\n' +
                     '• Include maintenance downtime factors';
            break;
        case 'efficiencyRatings':
            message = 'Chiller Efficiency Ratings:\n\n' +
                     'EER (Energy Efficiency Ratio):\n' +
                     '• BTU/hr cooling per kW input\n' +
                     '• Higher values = better efficiency\n' +
                     '• Measured at ARI conditions (44°F/54°F)\n\n' +
                     'IPLV (Integrated Part Load Value):\n' +
                     '• Weighted average at part-load conditions\n' +
                     '• More representative of actual operation\n' +
                     '• Uses ARI standard weighting factors\n\n' +
                     'Efficiency Categories:\n' +
                     '• Standard: 10-12 EER (1.2 kW/ton)\n' +
                     '• High: 13-16 EER (0.88 kW/ton)\n' +
                     '• Premium: 17+ EER (0.74 kW/ton)\n' +
                     '• Variable speed: 20+ EER possible\n\n' +
                     'Selection Guidelines:\n' +
                     '• Consider local electricity rates\n' +
                     '• Calculate life-cycle cost analysis\n' +
                     '• Account for part-load efficiency\n' +
                     '• Factor in maintenance costs';
            break;
        case 'typeComparison':
            message = 'Chiller Type Comparison:\n\n' +
                     'Air Cooled Chillers:\n' +
                     '• Lower initial cost\n' +
                     '• Outdoor installation only\n' +
                     '• No cooling tower required\n' +
                     '• Higher operating costs in hot climates\n' +
                     '• 0.9x sizing factor\n\n' +
                     'Water Cooled Chillers:\n' +
                     '• Higher efficiency (lower kW/ton)\n' +
                     '• Requires cooling tower system\n' +
                     '• Can be installed indoors\n' +
                     '• Lower operating costs\n' +
                     '• 0.9x sizing factor (reference)\n\n' +
                     'Absorption Chillers:\n' +
                     '• Utilize waste heat or steam\n' +
                     '• Lower electricity consumption\n' +
                     '• Higher total energy use\n' +
                     '• Good for cogeneration systems\n' +
                     '• 1.2x sizing factor\n\n' +
                     'Selection Criteria:\n' +
                     '• Available energy sources\n' +
                     '• Installation constraints\n' +
                     '• Operating cost objectives\n' +
                     '• Environmental considerations';
            break;
        case 'installation':
            message = 'Chiller Installation Guidelines:\n\n' +
                     'Space Requirements:\n' +
                     '• Allow 3-4 feet around unit for service\n' +
                     '• Verify ceiling height for indoor units\n' +
                     '• Consider crane access for large units\n' +
                     '• Plan for future maintenance access\n\n' +
                     'Piping Considerations:\n' +
                     '• Chilled water supply: 40-45°F\n' +
                     '• Chilled water return: 50-55°F\n' +
                     '• Provide isolation valves for service\n' +
                     '• Include flow measurement points\n\n' +
                     'Electrical Requirements:\n' +
                     '• 3-phase power supply typical\n' +
                     '• Verify voltage and frequency\n' +
                     '• Provide disconnect within sight\n' +
                     '• Consider power factor correction\n\n' +
                     'Foundation and Mounting:\n' +
                     '• Level concrete pad or frame\n' +
                     '• Account for vibration isolation\n' +
                     '• Consider expansion joint connections\n' +
                     '• Verify structural load capacity';
            break;
    }
    
    alert(message);
}

// Utility functions
function saveCalculation(type, calculation) {
    let recent = JSON.parse(localStorage.getItem('recentChillerCalculations') || '[]');
    recent.unshift({
        type: type,
        calculation: calculation,
        timestamp: new Date().toLocaleString()
    });
    
    // Keep only last 10 calculations
    recent = recent.slice(0, 10);
    localStorage.setItem('recentChillerCalculations', JSON.stringify(recent));
    loadRecentCalculations();
}

function loadRecentCalculations() {
    const recent = JSON.parse(localStorage.getItem('recentChillerCalculations') || '[]');
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
        if (e.key === 'Enter' && e.target.id === 'chillerLoad') {
            calculateChillerSize();
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
    background: rgba(0, 123, 255, 0.2);
    border-left-color: #007bff;
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



