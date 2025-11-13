<?php
// Start session and include necessary files
session_start();
require_once '../../app/Config/db.php';
require_once '../../app/Helpers/functions.php';
require_once '../../themes/default/views/partials/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-white"><i class="fas fa-exchange-alt me-3"></i>Swelling & Shrinkage Calculator</h1>
            <p class="text-white-50">Calculate material volume changes during earthwork operations</p>
        </div>
        <div>
            <button class="btn btn-light me-2" onclick="toggleFavorite('swelling-shrinkage')" id="favoriteBtn">
                <i class="far fa-star"></i> Add to Favorites
            </button>
            <a href="../index.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Back to Site Tools
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="glass-card p-4 mb-4">
                <h4 class="mb-4"><i class="fas fa-exchange-alt me-2"></i>Material Volume Change Calculator</h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="calculator-form">
                            <h5>Material Parameters</h5>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Bank Volume</span>
                                <input type="number" class="form-control" id="bankVolume" placeholder="CY" step="0.1">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Material Type</span>
                                <select class="form-control" id="materialType">
                                    <option value="clay">Clay</option>
                                    <option value="sand">Sand</option>
                                    <option value="gravel">Gravel</option>
                                    <option value="rock">Rock</option>
                                    <option value="topsoil">Topsoil</option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Operation</span>
                                <select class="form-control" id="earthworkOp">
                                    <option value="excavation">Excavation</option>
                                    <option value="compaction">Compaction</option>
                                </select>
                            </div>
                            <button class="btn btn-primary w-100" onclick="calculateVolumeChange()">
                                Calculate Volume Changes
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-panel">
                            <h5><i class="fas fa-info-circle me-2"></i>Volume Factors</h5>
                            <ul>
                                <li><strong>Bank Volume:</strong> In-place volume in ground</li>
                                <li><strong>Loose Volume:</strong> Excavated loose material</li>
                                <li><strong>Compacted Volume:</strong> After compaction</li>
                                <li><strong>Swell:</strong> Increase when excavated</li>
                                <li><strong>Shrinkage:</strong> Decrease when compacted</li>
                            </ul>
                            
                            <h6 class="mt-4">Typical Factors:</h6>
                            <div class="formula-box">
                                <p><strong>Sand:</strong> 1.12 swell, 0.92 shrink</p>
                                <p><strong>Clay:</strong> 1.25 swell, 0.85 shrink</p>
                                <p><strong>Rock:</strong> 1.50 swell, 0.75 shrink</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div class="results-section mt-4">
                    <div class="result-card" id="volumeChangeResult">
                        <h5><i class="fas fa-exchange-alt me-2"></i>Volume Changes</h5>
                        <div id="volumeChangeOutput"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Reference -->
            <div class="glass-card p-4 mb-4">
                <h4><i class="fas fa-book me-2"></i>Earthwork Reference</h4>
                <div class="quick-reference">
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('factors')">
                        Volume Factors
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('operations')">
                        Operations Guide
                    </button>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('cost')">
                        Cost Impact
                    </button>
                </div>
            </div>

            <!-- Recent Calculations -->
            <div class="glass-card p-4">
                <h4><i class="fas fa-history me-2"></i>Recent Calculations</h4>
                <div id="recentCalculations">
                    <!-- Recent calculations will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateVolumeChange() {
    const bankVolume = parseFloat(document.getElementById('bankVolume').value);
    const materialType = document.getElementById('materialType').value;
    const operation = document.getElementById('earthworkOp').value;
    
    if (!bankVolume) {
        alert('Please enter bank volume');
        return;
    }
    
    // Swell and shrinkage factors
    const factors = {
        'clay': { swell: 1.25, shrinkage: 0.85 },
        'sand': { swell: 1.12, shrinkage: 0.92 },
        'gravel': { swell: 1.15, shrinkage: 0.95 },
        'rock': { swell: 1.50, shrinkage: 0.75 },
        'topsoil': { swell: 1.30, shrinkage: 0.80 }
    };
    
    const factor = factors[materialType] || factors.clay;
    
    let looseVolume, compactedVolume, adjustedBank;
    
    if (operation === 'excavation') {
        looseVolume = bankVolume * factor.swell;
        compactedVolume = bankVolume * factor.shrinkage;
        adjustedBank = bankVolume;
    } else {
        compactedVolume = bankVolume;
        adjustedBank = bankVolume / factor.shrinkage;
        looseVolume = adjustedBank * factor.swell;
    }
    
    const resultHTML = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Material Type:</strong> ${materialType.charAt(0).toUpperCase() + materialType.slice(1)}</p>
                <p><strong>Operation:</strong> ${operation === 'excavation' ? 'Excavation' : 'Compaction'}</p>
                <p><strong>Bank Volume:</strong> ${bankVolume.toFixed(1)} CY</p>
                <p><strong>Loose Volume:</strong> ${looseVolume.toFixed(1)} CY</p>
                <p><strong>Compacted Volume:</strong> ${compactedVolume.toFixed(1)} CY</p>
            </div>
            <div class="col-md-6">
                <p><strong>Swell Factor:</strong> ${((factor.swell - 1) * 100).toFixed(0)}%</p>
                <p><strong>Shrinkage Factor:</strong> ${((1 - factor.shrinkage) * 100).toFixed(0)}%</p>
                <p><strong>Volume Increase:</strong> ${((looseVolume - bankVolume) / bankVolume * 100).toFixed(1)}%</p>
                <p><strong>Volume Decrease:</strong> ${((bankVolume - compactedVolume) / bankVolume * 100).toFixed(1)}%</p>
                <p><strong>Truck Loads (10 CY):</strong> ${Math.ceil(looseVolume / 10)} loads</p>
            </div>
        </div>
        <div class="mt-4">
            <h6>Practical Applications:</h6>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Excavation Hauling:</strong></p>
                    <ul>
                        <li>Bank ${bankVolume.toFixed(1)} CY becomes ${looseVolume.toFixed(1)} CY loose</li>
                        <li>Need ${Math.ceil(looseVolume / 10)} truck loads (10 CY each)</li>
                        <li>Consider swell in hauling capacity</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <p><strong>Compaction Planning:</strong></p>
                    <ul>
                        <li>Bank ${bankVolume.toFixed(1)} CY becomes ${compactedVolume.toFixed(1)} CY compacted</li>
                        <li>May need additional material for embankment</li>
                        <li>Account for shrinkage in final grades</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <h6>Quality Control Notes:</h6>
            <ul>
                <li>Factors vary by material condition and moisture</li>
                <li>Always verify factors with project specifications</li>
                <li>Consider compaction requirements in calculations</li>
                <li>Account for material quality and gradation</li>
                <li>Monitor actual vs. assumed factors in field</li>
            </ul>
        </div>
    `;
    
    document.getElementById('volumeChangeOutput').innerHTML = resultHTML;
    document.getElementById('volumeChangeResult').style.display = 'block';
    saveCalculation('Volume Change', `${materialType}: ${bankVolume.toFixed(1)}CY bank → ${looseVolume.toFixed(1)}CY loose`);
}

function toggleFavorite(calculator) {
    let favorites = JSON.parse(localStorage.getItem('favoriteCalculators') || '[]');
    const btn = document.getElementById('favoriteBtn');
    
    if (favorites.includes(calculator)) {
        favorites = favorites.filter(fav => fav !== calculator);
        btn.innerHTML = '<i class="far fa-star"></i> Add to Favorites';
    } else {
        favorites.push(calculator);
        btn.innerHTML = '<i class="fas fa-star"></i> Remove from Favorites';
    }
    
    localStorage.setItem('favoriteCalculators', JSON.stringify(favorites));
}

function showReference(type) {
    let message = '';
    
    switch(type) {
        case 'factors':
            message = 'Typical Volume Factors by Material:\n\n' +
                     'Clay:\n' +
                     '- Swell Factor: 25% increase\n' +
                     '- Shrinkage Factor: 15% decrease\n' +
                     '- Bank → Loose: 1.25\n' +
                     '- Loose → Compacted: 0.85\n\n' +
                     'Sand & Gravel:\n' +
                     '- Swell Factor: 8-15% increase\n' +
                     '- Shrinkage Factor: 5-8% decrease\n' +
                     '- Good for construction fill\n\n' +
                     'Rock:\n' +
                     '- Swell Factor: 30-50% increase\n' +
                     '- Shrinkage Factor: 25% decrease\n' +
                     '- High variability by rock type';
            break;
        case 'operations':
            message = 'Earthwork Operation Impacts:\n\n' +
                     'Excavation:\n' +
                     '- Material increases in volume when loosened\n' +
                     '- Affects hauling capacity and costs\n' +
                     '- Consider swell in haul distance planning\n\n' +
                     'Hauling:\n' +
                     '- Load vehicles based on loose volume\n' +
                     '- Account for material expansion\n' +
                     '- Plan for excess material disposal\n\n' +
                     'Compaction:\n' +
                     '- Material decreases in volume\n' -
                     '- May require additional material\n' +
                     '- Affects embankment quantities';
            break;
        case 'cost':
            message = 'Cost Impact Analysis:\n\n' +
                     'Excavation Costs:\n' +
                     '- Increase in hauling volumes\n' +
                     '- More truck loads required\n' +
                     '- Longer haul distances for excess material\n\n' +
                     'Compaction Costs:\n' +
                     '- Additional material needed\n' +
                     '- Longer compaction time\n' +
                     '- Quality control testing\n\n' +
                     'Planning Recommendations:\n' +
                     '- Use project-specific factors\n' +
                     '- Monitor actual performance\n' +
                     '- Adjust factors based on results';
            break;
    }
    
    alert(message);
}

function saveCalculation(type, calculation) {
    let recent = JSON.parse(localStorage.getItem('recentSiteCalculations') || '[]');
    recent.unshift({
        type: type,
        calculation: calculation,
        timestamp: new Date().toLocaleString()
    });
    
    // Keep only last 10 calculations
    recent = recent.slice(0, 10);
    localStorage.setItem('recentSiteCalculations', JSON.stringify(recent));
    loadRecentCalculations();
}

function loadRecentCalculations() {
    const recent = JSON.parse(localStorage.getItem('recentSiteCalculations') || '[]');
    const container = document.getElementById('recentCalculations');
    
    if (recent.length === 0) {
        container.innerHTML = '<p class="text-muted">No recent calculations</p>';
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

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadRecentCalculations();
    
    // Check if this calculator is in favorites
    const favorites = JSON.parse(localStorage.getItem('favoriteCalculators') || '[]');
    if (favorites.includes('swelling-shrinkage')) {
        document.getElementById('favoriteBtn').innerHTML = '<i class="fas fa-star"></i> Remove from Favorites';
    }
});
</script>

<style>
.calculator-form {
    background: rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.calculator-form h5 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding-bottom: 0.5rem;
}

.info-panel {
    background: rgba(255, 255, 255, 0.05);
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.info-panel h5, .info-panel h6 {
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.info-panel ul {
    margin-bottom: 1rem;
}

.formula-box {
    background: rgba(0, 0, 0, 0.2);
    padding: 1rem;
    border-radius: 5px;
    margin-top: 1rem;
    text-align: center;
}

.result-card {
    background: var(--success-color);
    color: white;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1rem;
    display: none;
}

.result-card h5 {
    margin-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    padding-bottom: 0.5rem;
}

.quick-reference .btn {
    text-align: left;
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
    min-width: 160px;
}
</style>

<?php require_once '../../themes/default/views/partials/footer.php'; ?>



