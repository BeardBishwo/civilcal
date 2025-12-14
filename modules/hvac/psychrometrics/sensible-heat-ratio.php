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
                    <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Psychrometrics</a></li>
                    <li class="breadcrumb-item active text-white">Sensible Heat Ratio</li>
                </ol>
            </nav>
            <h1 class="text-white"><i class="fas fa-balance-scale me-3"></i>Sensible Heat Ratio Calculator</h1>
            <p class="text-white-50">Calculate the ratio of sensible to total heat for HVAC system analysis</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Back to Psychrometrics
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Calculator -->
        <div class="col-lg-8">
            <div class="glass-card p-4">
                <h4 class="mb-4"><i class="fas fa-calculator me-2"></i>Sensible Heat Ratio Calculation</h4>
                
                <div class="calculator-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Sensible Heat</span>
                                <input type="number" class="form-control" id="sensibleHeat" placeholder="kW" step="0.1">
                                <span class="input-group-text">kW</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Latent Heat</span>
                                <input type="number" class="form-control" id="latentHeat" placeholder="kW" step="0.1">
                                <span class="input-group-text">kW</span>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary w-100" onclick="calculateSHR()">
                        <i class="fas fa-calculator me-2"></i>Calculate SHR
                    </button>
                </div>

                <!-- Results Section -->
                <div class="results-section">
                    <div class="result-card" id="shrResult">
                        <h5><i class="fas fa-balance-scale me-2"></i>Sensible Heat Ratio Results</h5>
                        <div id="shrOutput"></div>
                        <button class="btn btn-light btn-sm mt-3" onclick="clearResults('shrResult')">
                            <i class="fas fa-times me-1"></i>Clear Results
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="col-lg-4">
            <div class="glass-card p-4">
                <h4><i class="fas fa-info-circle me-2"></i>About SHR</h4>
                <div class="info-content">
                    <h6>What is Sensible Heat Ratio?</h6>
                    <p class="small">Sensible Heat Ratio (SHR) is the ratio of sensible heat to total heat in cooling applications. It's crucial for proper HVAC system design and performance analysis.</p>
                    
                    <h6>Typical SHR Values:</h6>
                    <ul class="small">
                        <li>0.80 - 0.95: Office spaces</li>
                        <li>0.65 - 0.85: Residential</li>
                        <li>0.70 - 0.90: Commercial</li>
                        <li>0.60 - 0.80: High humidity loads</li>
                    </ul>
                    
                    <h6>Applications:</h6>
                    <ul class="small">
                        <li>AC unit selection</li>
                        <li>Coil sizing</li>
                        <li>System performance</li>
                        <li>Load analysis</li>
                    </ul>
                </div>
            </div>

            <div class="glass-card p-4 mt-4">
                <h4><i class="fas fa-lightbulb me-2"></i>Quick Reference</h4>
                <div class="quick-reference">
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('coolingApplication')">
                        Cooling Applications
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('shrGuidelines')">
                        SHR Guidelines
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('typicalValues')">
                        Typical Values
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('systemImpact')">
                        System Impact
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
function calculateSHR() {
    const sensible = parseFloat(document.getElementById('sensibleHeat').value);
    const latent = parseFloat(document.getElementById('latentHeat').value);
    
    if (!sensible || !latent) {
        showNotification('Please enter both sensible and latent heat values', 'info');
        return;
    }
    
    if (sensible < 0 || latent < 0) {
        showNotification('Heat values must be positive', 'info');
        return;
    }
    
    const total = sensible + latent;
    const shr = sensible / total;
    
    let shrDescription = '';
    let performanceNote = '';
    
    if (shr > 0.9) {
        shrDescription = 'Mostly sensible cooling (dry climate)';
        performanceNote = 'High sensible load - dry air';
    } else if (shr > 0.8) {
        shrDescription = 'High sensible cooling';
        performanceNote = 'Moderate to high sensible load';
    } else if (shr > 0.7) {
        shrDescription = 'Balanced sensible/latent cooling';
        performanceNote = 'Well-balanced cooling load';
    } else if (shr > 0.6) {
        shrDescription = 'Moderate latent cooling (humid climate)';
        performanceNote = 'Significant moisture removal needed';
    } else {
        shrDescription = 'High latent cooling (very humid)';
        performanceNote = 'Primary dehumidification required';
    }
    
    // Calculate system recommendations
    let coilRecommendation = '';
    if (shr > 0.85) {
        coilRecommendation = 'Standard cooling coil suitable';
    } else if (shr > 0.7) {
        coilRecommendation = 'Standard coil with good dehumidification';
    } else {
        coilRecommendation = 'Enhanced dehumidification coil or reheat';
    }
    
    // Fan speed recommendation
    let fanRecommendation = '';
    if (shr > 0.8) {
        fanRecommendation = 'Variable speed fan advantageous';
    } else {
        fanRecommendation = 'Two-speed fan recommended';
    }
    
    const resultHTML = `
        <div class="result-grid">
            <div class="result-item">
                <div class="result-label">Sensible Heat</div>
                <div class="result-value">${sensible.toFixed(2)} kW</div>
            </div>
            <div class="result-item">
                <div class="result-label">Latent Heat</div>
                <div class="result-value">${latent.toFixed(2)} kW</div>
            </div>
            <div class="result-item">
                <div class="result-label">Total Heat</div>
                <div class="result-value">${total.toFixed(2)} kW</div>
            </div>
            <div class="result-item highlight">
                <div class="result-label">Sensible Heat Ratio</div>
                <div class="result-value">${shr.toFixed(3)}</div>
            </div>
        </div>
        
        <div class="result-details">
            <h6><i class="fas fa-chart-pie me-2"></i>Load Breakdown</h6>
            <div class="progress mb-3">
                <div class="progress-bar bg-info" style="width: ${(shr * 100).toFixed(1)}%">
                    Sensible: ${(shr * 100).toFixed(1)}%
                </div>
                <div class="progress-bar bg-success" style="width: ${((1 - shr) * 100).toFixed(1)}%">
                    Latent: ${((1 - shr) * 100).toFixed(1)}%
                </div>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-info me-2"></i>Analysis</h6>
                <p><strong>Description:</strong> ${shrDescription}</p>
                <p><strong>Performance Note:</strong> ${performanceNote}</p>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-cog me-2"></i>System Recommendations</h6>
                <p><strong>Coil Type:</strong> ${coilRecommendation}</p>
                <p><strong>Fan Control:</strong> ${fanRecommendation}</p>
            </div>
            
            <div class="result-section">
                <h6><i class="fas fa-fire me-2"></i>Heat Flow Analysis</h6>
                <p><strong>Sensible Heat Factor:</strong> ${(shr * 100).toFixed(1)}%</p>
                <p><strong>Latent Heat Factor:</strong> ${((1 - shr) * 100).toFixed(1)}%</p>
                <p><strong>Moisture Removal:</strong> ${(latent * 0.67).toFixed(2)} kg/hr</p>
            </div>
        </div>
    `;
    
    document.getElementById('shrOutput').innerHTML = resultHTML;
    document.getElementById('shrResult').style.display = 'block';
    
    saveCalculation('Sensible Heat Ratio', `SHR = ${shr.toFixed(3)} (${shrDescription})`);
}

// Reference functions
function showReference(type) {
    let message = '';
    
    switch(type) {
        case 'coolingApplication':
            message = 'SHR in Cooling Applications:\n\n' +
                     'Low SHR (< 0.7): High humidity areas\n' +
                     '• Primary dehumidification\n' +
                     '• Enhanced latent capacity\n' +
                     '• Reheat often required\n\n' +
                     'High SHR (> 0.8): Dry climates\n' +
                     '• Temperature control focus\n' +
                     '• Standard coils sufficient\n' +
                     '• Lower moisture removal\n\n' +
                     'Balanced SHR (0.7-0.8):\n' +
                     '• Moderate humidity control\n' +
                     '• Well-balanced design';
            break;
        case 'shrGuidelines':
            message = 'SHR Design Guidelines:\n\n' +
                     'Office Spaces: 0.85-0.92\n' +
                     '• Low internal moisture gains\n' +
                     '• People: 70-100W sensible, 30-50W latent\n' +
                     '• Equipment: Mostly sensible\n\n' +
                     'Residential: 0.65-0.85\n' +
                     '• Higher moisture from cooking, bathing\n' +
                     '• Variable occupancy\n\n' +
                     'Commercial/Retail: 0.70-0.90\n' +
                     '• Depends on occupancy density\n' +
                     '• External moisture infiltration\n\n' +
                     'Restaurants: 0.60-0.80\n' +
                     '• High moisture from cooking\n' +
                     '• Often requires separate dehumidification';
            break;
        case 'typicalValues':
            message = 'Typical SHR Values by Application:\n\n' +
                     'Hot/Dry Climate: 0.85-0.95\n' +
                     '• Low external humidity\n' +
                     '• Internal sensible gains dominate\n\n' +
                     'Hot/Humid Climate: 0.60-0.75\n' +
                     '• High external humidity\n' +
                     '• External latent loads significant\n\n' +
                     'Mixed Climate: 0.70-0.85\n' +
                     '• Moderate humidity levels\n' +
                     '• Seasonal variations\n\n' +
                     'Cool/Humid Climate: 0.65-0.80\n' +
                     '• Lower sensible loads\n' +
                     '• High moisture condensation risk';
            break;
        case 'systemImpact':
            message = 'SHR Impact on HVAC Systems:\n\n' +
                     'High SHR (> 0.85):\n' +
                     '• Longer cooling cycles\n' +
                     '• Better dehumidification control\n' +
                     '• Lower energy for moisture removal\n' +
                     '• Variable speed advantageous\n\n' +
                     'Low SHR (< 0.7):\n' +
                     '• Shorter, more frequent cycles\n' +
                     '• Higher latent capacity needed\n' +
                     '• Reheat may be required\n' +
                     '• Higher energy for dehumidification\n\n' +
                     'System Design Implications:\n' +
                     '• Coil selection based on SHR\n' +
                     '• Fan control strategy\n' +
                     '• Control sequence optimization\n' +
                     '• Equipment sizing adjustments';
            break;
    }
    
    showNotification(message, 'info');
}

// Utility functions
function saveCalculation(type, calculation) {
    let recent = JSON.parse(localStorage.getItem('recentHRVCalculations') || '[]');
    recent.unshift({
        type: type,
        calculation: calculation,
        timestamp: new Date().toLocaleString()
    });
    
    // Keep only last 10 calculations
    recent = recent.slice(0, 10);
    localStorage.setItem('recentHRVCalculations', JSON.stringify(recent));
    loadRecentCalculations();
}

function loadRecentCalculations() {
    const recent = JSON.parse(localStorage.getItem('recentHRVCalculations') || '[]');
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
        if (e.key === 'Enter' && (e.target.id === 'sensibleHeat' || e.target.id === 'latentHeat')) {
            calculateSHR();
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

.info-content h6 {
    color: var(--primary-color);
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
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



