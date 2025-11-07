<?php
// Start session and include necessary files
session_start();
require_once '../../../includes/db.php';
require_once '../../../includes/functions.php';
require_once '../../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../../index.php" class="text-white-50">Home</a></li>
                    <li class="breadcrumb-item"><a href="../index.php" class="text-white-50">HVAC</a></li>
                    <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Equipment Sizing</a></li>
                    <li class="breadcrumb-item active text-white">Pump Sizing</li>
                </ol>
            </nav>
            <h1 class="text-white"><i class="fas fa-pump-medical me-3"></i>Pump Sizing Calculator</h1>
            <p class="text-white-50">Calculate proper pump capacity and power requirements for HVAC hydronic systems</p>
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
                <h4 class="mb-4"><i class="fas fa-calculator me-2"></i>Pump Sizing Calculation</h4>
                
                <div class="calculator-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Flow Rate</span>
                                <input type="number" class="form-control" id="pumpFlow" placeholder="GPM" step="0.1">
                                <span class="input-group-text">GPM</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Total Head</span>
                                <input type="number" class="form-control" id="pumpHead" placeholder="feet" step="0.1">
                                <span class="input-group-text">ft</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Efficiency</span>
                                <input type="number" class="form-control" id="pumpEfficiency" placeholder="%" step="1" value="75">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Fluid Type</span>
                                <select class="form-control" id="fluidType">
                                    <option value="water">Water</option>
                                    <option value="glycol">Glycol Solution</option>
                                    <option value="saltWater">Salt Water</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary w-100" onclick="calculatePumpPower()">
                        <i class="fas fa-calculator me-2"></i>Calculate Pump Power
                    </button>
                </div>

                <!-- Results Section -->
                <div class="results-section">
                    <div class="result-card" id="pumpPowerResult">
                        <h5><i class="fas fa-pump-medical me-2"></i>Pump Sizing Results</h5>
                        <div id="pumpPowerOutput"></div>
                        <button class="btn btn-light btn-sm mt-3" onclick="clearResults('pumpPowerResult')">
                            <i class="fas fa-times me-1"></i>Clear Results
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="col-lg-4">
            <div class="glass-card p-4">
                <h4><i class="fas fa-info-circle me-2"></i>About Pump Sizing</h4>
                <div class="info-content">
                    <p>Proper pump sizing is essential for efficient HVAC hydronic system operation. The selection depends on flow requirements, system resistance, fluid properties, and desired efficiency.</p>
                    
                    <h6>Sizing Considerations:</h6>
                    <ul>
                        <li>System flow requirements (GPM)</li>
                        <li>Total dynamic head (feet of water)</li>
                        <li>Pump and motor efficiency</li>
                        <li>Operating point and control method</li>
                    </ul>
                    
                    <h6>Head Components:</h6>
                    <ul>
                        <li>Static head: Elevation changes</li>
                        <li>Friction head: Pipe and fitting losses</li>
                        <li>Equipment head: Heat exchangers, etc.</li>
                        <li>Safety margin: 10-15% typically</li>
                    </ul>
                </div>
            </div>

            <div class="glass-card p-4 mt-4">
                <h4><i class="fas fa-lightbulb me-2"></i>Quick Reference</h4>
                <div class="quick-reference">
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('headCalculation')">
                        Head Calculation
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('efficiencyFactors')">
                        Efficiency Factors
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('systemDesign')">
                        System Design
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('variableSpeed')">
                        Variable Speed Drives
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
function calculatePumpPower() {
    const flowRate = parseFloat(document.getElementById('pumpFlow').value);
    const totalHead = parseFloat(document.getElementById('pumpHead').value);
    const efficiency = parseFloat(document.getElementById('pumpEfficiency').value) || 75;
    const fluidType = document.getElementById('fluidType').value;
    
    if (!flowRate || !totalHead) {
        alert('Please enter flow rate and total head');
        return;
    }
    
    if (flowRate <= 0 || totalHead <= 0) {
        alert('Flow rate and head must be positive');
        return;
    }
    
    // Fluid density factors (relative to water)
    const fluidDensity = {
        'water': 1.0,        // Water at 60°F
        'glycol': 1.05,      // 30% glycol solution
        'saltWater': 1.025   // Typical seawater
    };
    
    const densityFactor = fluidDensity[fluidType] || 1.0;
    
    // Pump power calculation: P = (Q × H × SG) / (3960 × η)
    // Where: Q = GPM, H = feet, SG = specific gravity, η = efficiency
    const specificGravity = densityFactor;
    const powerHP = (flowRate * totalHead * specificGravity) / (3960 * (efficiency / 100));
    const powerKW = powerHP * 0.7457;
    
    // Add 20% safety margin
    const recommendedHP = powerHP * 1.2;
    const recommendedKW = powerKW * 1.2;
    
    // Annual operating cost calculation
    const annualOperatingHours = 6000; // Assume 6000 hours per year
    const electricityCost = 0.12; // $/kWh
    const annualEnergy = powerKW * annualOperatingHours;
    const annualCost = annualEnergy * electricityCost;
    
    // System curve considerations
    const systemCurve = {
        'low': { description: 'Low resistance system', factor: 0.8 },
        'medium': { description: 'Medium resistance system', factor: 1.0 },
        'high': { description: 'High resistance system', factor: 1.3 }
    };
    
    // Determine system type based on head
    let systemType = 'medium';
    if (totalHead < 50) systemType = 'low';
    else if (totalHead > 150) systemType = 'high';
    
    const fluidNames = {
        'water': 'Water (60°F)',
        'glycol': '30% Glycol Solution',
        'saltWater': 'Salt Water'
    };
    
    const efficiencyRating = efficiency >= 85 ? 'Excellent' : 
                           efficiency >= 75 ? 'Good' : 
                           efficiency >= 65 ? 'Fair' : 'Poor';
    
    const resultHTML = `
        <div class="result-grid">
            <div class="result-item">
                <div class="result-label">Flow Rate</div>
                <div class="result-value">${flowRate.toFixed(1)} GPM</div>
            </div>
            <div class="result-item">
                <div class="result-label">Total Head</div>
                <div class="result-value">${totalHead.toFixed(1)} ft</div>
            </div>
            <div class="result-item">
                <div class="result-label">Fluid Type</div>
                <div class="result-value">${fluidNames[fluidType]}</div>
            </div>
            <div class="result-item highlight">
                <div class="result-label">Required Power</div>
                <div class="result-value">${powerHP.toFixed(2)} HP</div>
            </div>
        </div>
        
        <div class="result-details">
            <h6><i class="fas fa-cog me-2"></i>Pump Specifications</h6>
            <div class="equipment-section">
                <div class="equipment-item">
                    <strong>Hydraulic Power:</strong> ${powerHP.toFixed(2)} HP (${powerKW.toFixed(2)} kW)
                </div>
                <div class="equipment-item">
                    <strong>Pump Efficiency:</strong> ${efficiency}% (${efficiencyRating})
                </div>
                <div class="equipment-item">
                    <strong>Specific Gravity:</strong> ${specificGravity.toFixed(3)}
                </div>
                <div class="equipment-item">
                    <strong>System Type:</strong> ${systemCurve[systemType].description}
                </div>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-chart-bar me-2"></i>Recommended Equipment</h6>
                <p><strong>Recommended Pump:</strong> ${recommendedHP.toFixed(1)} HP (${recommendedKW.toFixed(1)} kW)</p>
                <p><strong>Safety Margin:</strong> 20% (typical range: 10-25%)</p>
                <p><strong>Next Standard Size:</strong> ${Math.ceil(recommendedHP)} HP</p>
                <p><strong>Motor Efficiency:</strong> Assume 90% (consider premium efficiency)</p>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-leaf me-2"></i>Energy Analysis</h6>
                <p><strong>Annual Energy Use:</strong> ${annualEnergy.toLocaleString()} kWh</p>
                <p><strong>Annual Operating Cost:</strong> $${annualCost.toLocaleString()}</p>
                <p><strong>Operating Hours:</strong> ${annualOperatingHours.toLocaleString()}/year</p>
                <p><strong>Energy per 1000 GPM:</strong> ${(powerKW / flowRate * 1000).toFixed(2)} kW per 1000 GPM</p>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Installation Notes</h6>
                <p>• Install on system centerline to avoid cavitation</p>
                <p>• Provide adequate suction head (NPSH requirements)</p>
                <p>• Include isolation valves for maintenance</p>
                <p>• Consider variable speed drive for energy savings</p>
            </div>
        </div>
    `;
    
    document.getElementById('pumpPowerOutput').innerHTML = resultHTML;
    document.getElementById('pumpPowerResult').style.display = 'block';
    
    saveCalculation('Pump Sizing', `${flowRate} GPM @ ${totalHead} ft → ${powerHP.toFixed(2)} HP`);
}

// Reference functions
function showReference(type) {
    let message = '';
    
    switch(type) {
        case 'headCalculation':
            message = 'Pump Head Calculation:\n\n' +
                     'Total Dynamic Head (TDH) = Static Head + Friction Head + Equipment Head\n\n' +
                     'Static Head:\n' +
                     '• Elevation difference between suction and discharge\n' +
                     '• Positive: pump below discharge point\n' +
                     '• Negative: pump above discharge point\n\n' +
                     'Friction Head:\n' +
                     '• Pipe friction losses (Darcy-Weisbach equation)\n' +
                     '• Fitting losses (valves, elbows, tees)\n' +
                     '• Use Hazen-Williams for quick estimates\n\n' +
                     'Equipment Head:\n' +
                     '• Heat exchangers (20-50 ft typical)\n' +
                     '• Chiller bundles (30-80 ft typical)\n' +
                     '• Filters and strainers (5-20 ft)\n\n' +
                     'Safety Factor: Add 10-15% to calculated head';
            break;
        case 'efficiencyFactors':
            message = 'Pump Efficiency Factors:\n\n' +
                     'Pump Efficiency Ranges:\n' +
                     '• End suction pumps: 65-85%\n' +
                     '• Double suction pumps: 75-90%\n' +
                     '• Vertical turbine: 70-88%\n' +
                     '• Submersible: 65-85%\n\n' +
                     'Motor Efficiency:\n' +
                     '• Standard efficiency: 85-90%\n' +
                     '• NEMA Premium: 90-95%\n' +
                     '• Variable speed: 88-95%\n\n' +
                     'Best Efficiency Point (BEP):\n' +
                     '• Operate pump near BEP for maximum efficiency\n' +
                     '• Avoid operation below 70% of BEP flow\n' +
                     '• System curve should intersect pump curve near BEP\n\n' +
                     'Part Load Efficiency:\n' +
                     '• Variable speed drives improve part-load efficiency\n' +
                     '• Throttle valves waste energy\n' +
                     '• Consider pump redundancy for optimal loading';
            break;
        case 'systemDesign':
            message = 'Hydronic System Design:\n\n' +
                     'Design Flow Rates:\n' +
                     '• Chilled water: 2.4 GPM per ton\n' +
                     '• Hot water: 1.0 GPM per 10,000 BTU/hr\n' +
                     '• Condenser water: 3.0 GPM per ton\n' +
                     '• Glycol systems: Reduce flow 10-15%\n\n' +
                     'Pipe Sizing Guidelines:\n' +
                     '• Target velocity: 4-8 ft/sec (water)\n' +
                     '• Maximum velocity: 10 ft/sec (avoid noise)\n' +
                     '• Use Hazen-Williams C=120 (steel), C=150 (copper)\n\n' +
                     'System Components:\n' +
                     '• Air separators for automatic venting\n' +
                     '• Expansion tanks for thermal expansion\n' +
                     '• Isolation valves for zone control\n' +
                     '• Balancing valves for flow control\n\n' +
                     'Control Strategies:\n' +
                     '• Variable primary flow (VPF)\n' +
                     '• Primary-secondary pumping\n' +
                     '• Variable speed drives for energy savings';
            break;
        case 'variableSpeed':
            message = 'Variable Speed Drive Benefits:\n\n' +
                     'Energy Savings:\n' +
                     '• Affinity laws: Power ∝ Speed³\n' +
                     '• 50% speed = 12.5% power consumption\n' +
                     '• Typical savings: 20-50% annually\n' +
                     '• Payback period: 1-3 years\n\n' +
                     'System Advantages:\n' +
                     '• Better control accuracy\n' +
                     '• Reduced water hammer\n' +
                     '• Soft start/stop capability\n' +
                     '• Lower maintenance costs\n\n' +
                     'Control Methods:\n' +
                     '• DDC (Direct Digital Control)\n' +
                     '• Differential pressure sensors\n' +
                     '• Flow-based control\n' +
                     '• Temperature-based control\n\n' +
                     'Sizing Considerations:\n' +
                     '• VSD should be sized for maximum load\n' +
                     '• Consider starting torque requirements\n' +
                     '• Account for VSD efficiency (95-98%)\n' +
                     '• Filter harmonic distortion if required';
            break;
    }
    
    alert(message);
}

// Utility functions
function saveCalculation(type, calculation) {
    let recent = JSON.parse(localStorage.getItem('recentPumpCalculations') || '[]');
    recent.unshift({
        type: type,
        calculation: calculation,
        timestamp: new Date().toLocaleString()
    });
    
    // Keep only last 10 calculations
    recent = recent.slice(0, 10);
    localStorage.setItem('recentPumpCalculations', JSON.stringify(recent));
    loadRecentCalculations();
}

function loadRecentCalculations() {
    const recent = JSON.parse(localStorage.getItem('recentPumpCalculations') || '[]');
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
        if (e.key === 'Enter' && (e.target.id === 'pumpFlow' || e.target.id === 'pumpHead')) {
            calculatePumpPower();
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
    background: rgba(255, 193, 7, 0.2);
    border-left-color: #ffc107;
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

<?php require_once '../../../includes/footer.php'; ?>
