<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horizontal Curve Staking Calculator - Site Tools</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --glass-bg: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            color: white;
            min-height: 100vh;
            line-height: 1.6;
        }

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .py-4 {
            padding: 2rem 0;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .me-3 {
            margin-right: 1rem;
        }

        .me-2 {
            margin-right: 0.5rem;
        }

        .text-white {
            color: white;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.5);
        }

        .text-muted {
            color: rgba(255, 255, 255, 0.6);
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        h5 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        h6 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        p {
            margin-bottom: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-light {
            background: rgba(255, 255, 255, 0.9);
            color: var(--dark-color);
        }

        .btn-light:hover {
            background: white;
        }

        .btn-outline-light {
            background: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-outline-primary {
            background: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .w-100 {
            width: 100%;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }

        .col-lg-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
            padding: 0 15px;
        }

        .col-lg-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 0 15px;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .p-4 {
            padding: 1.5rem;
        }

        .mb-4 {
            margin-bottom: 2rem;
        }

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

        .results-section {
            margin-top: 2rem;
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
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }

        .recent-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .input-group {
            display: flex;
            margin-bottom: 1rem;
        }

        .input-group-text {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--dark-color);
            padding: 0.5rem;
            border-radius: 5px 0 0 5px;
            font-weight: 500;
            min-width: 160px;
            display: flex;
            align-items: center;
        }

        .form-control {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0 5px 5px 0;
            background: rgba(255, 255, 255, 0.9);
            color: var(--dark-color);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            overflow: hidden;
        }

        .table-dark {
            background: rgba(0, 0, 0, 0.2);
        }

        .table-dark th,
        .table-dark td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table-dark th {
            font-weight: 600;
            background: rgba(0, 0, 0, 0.3);
        }

        .table-dark td {
            border-color: rgba(255, 255, 255, 0.05);
        }

        .table-sm th,
        .table-sm td {
            padding: 0.3rem;
        }

        .border {
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .rounded {
            border-radius: 5px;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        ol, ul {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        li {
            margin-bottom: 0.5rem;
        }

        strong {
            font-weight: 600;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .col-lg-8, .col-lg-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .row {
                margin: 0 -10px;
            }
            
            .col-lg-8, .col-lg-4, .col-md-6 {
                padding: 0 10px;
            }
            
            .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
            
            .btn {
                width: 100%;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-chart-line me-3"></i>Horizontal Curve Staking</h1>
                <p class="text-white-50">Calculate curve data for roadway curve staking</p>
            </div>
            <div>
                <button class="btn btn-light me-2" onclick="toggleFavorite('curve-staking')" id="favoriteBtn">
                    <i class="far fa-star"></i> Add to Favorites
                </button>
                <a href="../../index.php" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left"></i> Back to Site Tools
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="glass-card p-4 mb-4">
                    <h4><i class="fas fa-chart-line me-2"></i>Horizontal Curve Staking Calculator</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="calculator-form">
                                <h5>Curve Parameters</h5>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Radius</span>
                                    <input type="number" class="form-control" id="curveRadius" placeholder="feet" step="1">
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Deflection Angle</span>
                                    <input type="number" class="form-control" id="deflectionAngle" placeholder="degrees" step="0.1">
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Station Interval</span>
                                    <input type="number" class="form-control" id="stationInterval" placeholder="feet" step="1" value="25">
                                </div>
                                <button class="btn btn-primary w-100" onclick="calculateCurveStaking()">
                                    Calculate Curve Staking Data
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-panel">
                                <h5><i class="fas fa-info-circle me-2"></i>Curve Elements</h5>
                                <ul>
                                    <li><strong>Radius:</strong> Centerline radius of curve</li>
                                    <li><strong>Deflection Angle:</strong> Total angle at center</li>
                                    <li><strong>Station Interval:</strong> Stake spacing along curve</li>
                                    <li><strong>PC/PT:</strong> Point of Curvature/Tangency</li>
                                </ul>
                                
                                <h6 class="mt-4">Key Formulas:</h6>
                                <div class="formula-box">
                                    <p><strong>Curve Length = R × Δ</strong></p>
                                    <p><strong>Tangent Length = R × tan(Δ/2)</strong></p>
                                    <p><strong>External Distance = R × (sec(Δ/2) - 1)</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results -->
                    <div class="results-section mt-4">
                        <div class="result-card" id="curveStakingResult">
                            <h5><i class="fas fa-chart-line me-2"></i>Curve Staking Data</h5>
                            <div id="curveStakingOutput"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Reference -->
                <div class="glass-card p-4 mb-4">
                    <h4><i class="fas fa-book me-2"></i>Curve Staking Guide</h4>
                    <div class="quick-reference">
                        <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('stakingProcedure')">
                            Staking Procedure
                        </button>
                        <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('calculations')">
                            Calculation Methods
                        </button>
                        <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('equipment')">
                            Survey Equipment
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
        function calculateCurveStaking() {
            const radius = parseFloat(document.getElementById('curveRadius').value);
            const deflection = parseFloat(document.getElementById('deflectionAngle').value);
            const interval = parseFloat(document.getElementById('stationInterval').value);
            
            if (!radius || !deflection) {
                showNotification('Please enter radius and deflection angle', 'info');
                return;
            }
            
            // Convert deflection angle to radians
            const deflectionRad = deflection * (Math.PI / 180);
            
            // Calculate curve elements
            const curveLength = radius * deflectionRad;
            const tangentLength = radius * Math.tan(deflectionRad / 2);
            const externalDist = radius * (1 / Math.cos(deflectionRad / 2) - 1);
            const middleOrdinate = radius * (1 - Math.cos(deflectionRad / 2));
            const chordLength = 2 * radius * Math.sin(deflectionRad / 2);
            
            // Calculate station numbers
            const numStations = Math.floor(curveLength / interval);
            const stations = [];
            
            for (let i = 0; i <= numStations; i++) {
                const stationNumber = i * interval;
                const stationDeflection = (stationNumber / curveLength) * deflection;
                const stationChord = (stationNumber / curveLength) * chordLength;
                
                stations.push({
                    station: i === 0 ? 'PC' : i === numStations ? 'PT' : `PC +${stationNumber.toFixed(0)}`,
                    deflection: stationDeflection.toFixed(2),
                    chord: stationChord.toFixed(2),
                    cumulativeChord: (i * stationChord).toFixed(2)
                });
            }
            
            const resultHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Curve Radius:</strong> ${radius} feet</p>
                        <p><strong>Deflection Angle:</strong> ${deflection}°</p>
                        <p><strong>Station Interval:</strong> ${interval} feet</p>
                        <p><strong>Curve Length:</strong> ${curveLength.toFixed(2)} feet</p>
                        <p><strong>Tangent Length:</strong> ${tangentLength.toFixed(2)} feet</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>External Distance:</strong> ${externalDist.toFixed(2)} feet</p>
                        <p><strong>Middle Ordinate:</strong> ${middleOrdinate.toFixed(2)} feet</p>
                        <p><strong>Total Chord Length:</strong> ${chordLength.toFixed(2)} feet</p>
                        <p><strong>Number of Stations:</strong> ${numStations + 1}</p>
                        <p><strong>Curve Type:</strong> ${deflection < 90 ? 'Sharp Curve' : deflection < 180 ? 'Standard Curve' : 'Gentle Curve'}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <h6>Staking Schedule:</h6>
                    <table class="table table-sm table-dark">
                        <thead>
                            <tr>
                                <th>Station</th>
                                <th>Deflection Angle</th>
                                <th>Chord Length</th>
                                <th>Instructions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${stations.map(station => `
                                <tr>
                                    <td>${station.station}</td>
                                    <td>${station.deflection}°</td>
                                    <td>${station.chord} ft</td>
                                    <td>${station.station === 'PC' ? 'Set at tangent point' : 
                                        station.station === 'PT' ? 'Set at tangent end' : 
                                        'Measure from previous station'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <h6>Field Procedure:</h6>
                    <ol>
                        <li>Set up instrument at PC (Point of Curvature)</li>
                        <li>Locate PT by measuring tangent length: ${tangentLength.toFixed(2)} ft</li>
                        <li>Stake PC at zero deflection</li>
                        <li>Move instrument along tangents as needed</li>
                        <li>Use deflection angles for each station</li>
                        <li>Verify PT location matches calculated distance</li>
                    </ol>
                </div>
            `;
            
            document.getElementById('curveStakingOutput').innerHTML = resultHTML;
            document.getElementById('curveStakingResult').style.display = 'block';
            saveCalculation('Curve Staking', `R=${radius}ft, Δ=${deflection}°, L=${curveLength.toFixed(1)}ft`);
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
                case 'stakingProcedure':
                    message = 'Standard Curve Staking Procedure:\n\n' +
                             '1. Locate and mark Point of Curvature (PC)\n' +
                             '2. Locate and mark Point of Tangency (PT)\n' +
                             '3. Set instrument at PC\n' +
                             '4. Stake PC with zero deflection\n' +
                             '5. Work from PC to PT using deflection angles\n' +
                             '6. Measure chord distances between stakes\n' +
                             '7. Check final PT location for accuracy\n' +
                             '8. Verify curve geometry with measurements\n' +
                             '9. Record all field notes and calculations\n' +
                             '10. Check work for accuracy and completeness';
                    break;
                case 'calculations':
                    message = 'Curve Calculation Methods:\n\n' +
                             'Deflection Angle Method:\n' +
                             '- Use total deflection divided by station spacing\n' +
                             '- Each station gets cumulative deflection\n' +
                             '- Most common for field staking\n\n' +
                             'Chord Method:\n' +
                             '- Calculate chord lengths between stations\n' +
                             '- Use actual chord distances, not arc\n' +
                             '- Requires accurate distance measurements\n\n' +
                             'Coordinate Method:\n' +
                             '- Calculate X,Y coordinates for each point\n' +
                             '- Use survey software for precision\n' +
                             '- Good for complex curves';
                    break;
                case 'equipment':
                    message = 'Required Survey Equipment:\n\n' +
                             'Survey Instruments:\n' +
                             '- Total station or theodolite\n' +
                             '- Tripod with adjustable legs\n' +
                             '- Reflector/prism for measurements\n\n' +
                             'Measuring Tools:\n' +
                             '- Electronic distance meter (EDM)\n' +
                             '- Steel tape for verification\n' +
                             '- Plumb bob for centering\n\n' +
                             'Field Supplies:\n' +
                             '- Marking paint or flagging\n' +
                             '- Wooden stakes and hubs\n' +
                             '- Field book or data collector\n' +
                             '- Calculator or tablet\n' +
                             '- Backup paper notes\n\n' +
                             'Safety Equipment:\n' +
                             '- High-visibility clothing\n' +
                             '- Traffic control as needed';
                    break;
            }
            
            showNotification(message, 'info');
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
            if (favorites.includes('curve-staking')) {
                document.getElementById('favoriteBtn').innerHTML = '<i class="fas fa-star"></i> Remove from Favorites';
            }
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
