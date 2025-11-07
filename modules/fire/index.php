<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Protection Calculator Suite - AEC Calculator</title>
    <link rel="stylesheet" href="../../assets/css/theme.css">
    <link rel="stylesheet" href="../../assets/css/fire.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .back-btn {
            background: #6b7280;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
            transition: background 0.3s ease;
        }
        .back-btn:hover {
            background: #4b5563;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            color: #fff;
        }
        .header h1 {
            color: #dc2626;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .header p {
            font-size: 1.2em;
            color: #d1d5db;
            margin-bottom: 10px;
        }
        .category-section {
            background: linear-gradient(135deg, #1f2937, #374151);
            border: 2px solid #dc2626;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }
        .category-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #dc2626;
        }
        .category-icon {
            font-size: 2.5em;
            color: #dc2626;
        }
        .category-title {
            font-size: 1.8em;
            font-weight: bold;
            color: #fff;
        }
        .category-description {
            color: #d1d5db;
            margin-bottom: 20px;
            font-style: italic;
        }
        .calculators-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
        }
        .calculator-card {
            background: linear-gradient(135deg, #374151, #4b5563);
            border: 1px solid #6b7280;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }
        .calculator-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(220, 38, 38, 0.3);
            border-color: #dc2626;
        }
        .calculator-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        .calculator-icon {
            font-size: 1.8em;
            color: #dc2626;
        }
        .calculator-title {
            font-size: 1.3em;
            font-weight: bold;
            color: #fff;
        }
        .calculator-description {
            color: #d1d5db;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .calculator-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .calculator-features li {
            color: #9ca3af;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .calculator-features li::before {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #10b981;
            font-size: 0.8em;
        }
        .stats-section {
            background: linear-gradient(135deg, #374151, #4b5563);
            border: 1px solid #6b7280;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-item {
            text-align: center;
            color: #fff;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #dc2626;
            display: block;
        }
        .stat-label {
            color: #d1d5db;
            margin-top: 5px;
        }
        .compliance-note {
            background: linear-gradient(45deg, #fbbf24, #f59e0b);
            color: #92400e;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .calculators-grid {
                grid-template-columns: 1fr;
            }
            .header h1 {
                font-size: 2em;
            }
            .category-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <a href="../../hvac.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Main Toolkit
        </a>

        <div class="header">
            <h1><i class="fas fa-fire-extinguisher"></i> Fire Protection Calculator Suite</h1>
            <p>Professional fire protection engineering calculations based on NFPA standards</p>
            <p>Comprehensive suite of calculators for fire sprinkler systems, hydraulics, standpipes, hazard classification, and fire pumps</p>
        </div>

        <div class="stats-section">
            <h2 style="color: #dc2626; margin-bottom: 10px;"><i class="fas fa-chart-bar"></i> Calculator Statistics</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">13</span>
                    <div class="stat-label">Total Calculators</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">5</span>
                    <div class="stat-label">Main Categories</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">NFPA</span>
                    <div class="stat-label">Standards Compliant</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <div class="stat-label">Professional Grade</div>
                </div>
            </div>
        </div>

        <div class="category-section">
            <div class="category-header">
                <i class="fas fa-spray-can category-icon"></i>
                <div>
                    <div class="category-title">Sprinkler Systems</div>
                </div>
            </div>
            <div class="category-description">
                Comprehensive sprinkler system calculations for layout, discharge, and pipe sizing based on NFPA 13 standards.
            </div>
            <div class="calculators-grid">
                <a href="sprinklers/sprinkler-layout.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-th-large calculator-icon"></i>
                        <div class="calculator-title">Sprinkler Layout</div>
                    </div>
                    <div class="calculator-description">
                        Calculate optimal sprinkler spacing, coverage areas, and ensure NFPA 13 compliance for different hazard classifications.
                    </div>
                    <ul class="calculator-features">
                        <li>Room coverage analysis</li>
                        <li>Hazard-based spacing</li>
                        <li>Maximum spacing checks</li>
                        <li>Protection area calculations</li>
                    </ul>
                </a>

                <a href="sprinklers/discharge-calculations.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-water calculator-icon"></i>
                        <div class="calculator-title">Sprinkler Discharge</div>
                    </div>
                    <div class="calculator-description">
                        Calculate sprinkler discharge rates using K-factors and operating pressure according to NFPA standards.
                    </div>
                    <ul class="calculator-features">
                        <li>K-factor calculations (5.6, 8.0, 11.2+)</li>
                        <li>Pressure-flow relationships</li>
                        <li>Discharge density analysis</li>
                        <li>Coverage verification</li>
                    </ul>
                </a>

                <a href="sprinklers/pipe-sizing.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-arrows-alt-h calculator-icon"></i>
                        <div class="calculator-title">Pipe Sizing</div>
                    </div>
                    <div class="calculator-description">
                        Size fire protection piping for optimal flow distribution with velocity limits and friction loss calculations.
                    </div>
                    <ul class="calculator-features">
                        <li>Hazen-Williams calculations</li>
                        <li>Velocity limit compliance</li>
                        <li>Friction loss analysis</li>
                        <li>Flow distribution optimization</li>
                    </ul>
                </a>
            </div>
        </div>

        <div class="category-section">
            <div class="category-header">
                <i class="fas fa-tint category-icon"></i>
                <div>
                    <div class="category-title">Hydraulic Calculations</div>
                </div>
            </div>
            <div class="category-description">
                Advanced hydraulic calculations using the Hazen-Williams formula for pressure loss and flow analysis.
            </div>
            <div class="calculators-grid">
                <a href="hydraulics/hazen-williams.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-calculator calculator-icon"></i>
                        <div class="calculator-title">Hazen-Williams Calculator</div>
                    </div>
                    <div class="calculator-description">
                        Professional hydraulic calculations using the Hazen-Williams formula for pressure loss determination.
                    </div>
                    <ul class="calculator-features">
                        <li>Hazen-Williams C-factors</li>
                        <li>Pressure loss calculations</li>
                        <li>Flow rate analysis</li>
                        <li>Pipe diameter optimization</li>
                    </ul>
                </a>
            </div>
        </div>

        <div class="category-section">
            <div class="category-header">
                <i class="fas fa-building category-icon"></i>
                <div>
                    <div class="category-title">Standpipe Systems</div>
                </div>
            </div>
            <div class="category-description">
                Complete standpipe system analysis including classification, hose demand, and pressure calculations per NFPA 14.
            </div>
            <div class="calculators-grid">
                <a href="standpipes/standpipe-classification.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-list-alt calculator-icon"></i>
                        <div class="calculator-title">Standpipe Classification</div>
                    </div>
                    <div class="calculator-description">
                        Determine NFPA 14 standpipe classification based on building height, occupancy, and fire department access.
                    </div>
                    <ul class="calculator-features">
                        <li>Building height analysis</li>
                        <li>Occupancy-based requirements</li>
                        <li>Fire department access</li>
                        <li>Classification determination</li>
                    </ul>
                </a>

                <a href="standpipes/hose-demand.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-fire calculator-icon"></i>
                        <div class="calculator-title">Hose Demand</div>
                    </div>
                    <div class="calculator-description">
                        Calculate standpipe water demand for simultaneous hose streams with NFPA 14 compliance verification.
                    </div>
                    <ul class="calculator-features">
                        <li>Simultaneous demand calculations</li>
                        <li>Hose stream requirements</li>
                        <li>Flow rate analysis</li>
                        <li>System demand verification</li>
                    </ul>
                </a>

                <a href="standpipes/pressure-calculations.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-tachometer-alt calculator-icon"></i>
                        <div class="calculator-title">Pressure Calculations</div>
                    </div>
                    <div class="calculator-description">
                        Analyze standpipe pressure requirements and losses at various elevations and system configurations.
                    </div>
                    <ul class="calculator-features">
                        <li>Elevation pressure analysis</li>
                        <li>Pressure loss calculations</li>
                        <li>System pressure requirements</li>
                        <li>Flow rate vs pressure relationships</li>
                    </ul>
                </a>
            </div>
        </div>

        <div class="category-section">
            <div class="category-header">
                <i class="fas fa-exclamation-triangle category-icon"></i>
                <div>
                    <div class="category-title">Hazard Classification</div>
                </div>
            </div>
            <div class="category-description">
                Determine hazard classifications for sprinkler system design including occupancy, commodity, and design density analysis.
            </div>
            <div class="calculators-grid">
                <a href="hazard-classification/occupancy-assessment.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-building-lock calculator-icon"></i>
                        <div class="calculator-title">Occupancy Assessment</div>
                    </div>
                    <div class="calculator-description">
                        Determine NFPA 13 hazard classification for different occupancy types and sprinkler requirements.
                    </div>
                    <ul class="calculator-features">
                        <li>Occupancy type classification</li>
                        <li>Hazard level determination</li>
                        <li>Sprinkler requirements</li>
                        <li>Design criteria selection</li>
                    </ul>
                </a>

                <a href="hazard-classification/commodity-classification.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-boxes-stacked calculator-icon"></i>
                        <div class="commodity-title">Commodity Classification</div>
                    </div>
                    <div class="calculator-description">
                        Classify commodities for storage applications based on material composition, packaging, and storage configuration.
                    </div>
                    <ul class="calculator-features">
                        <li>Material composition analysis</li>
                        <li>Packaging classification</li>
                        <li>Storage height considerations</li>
                        <li>Classification determination</li>
                    </ul>
                </a>

                <a href="hazard-classification/design-density.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-chart-area calculator-icon"></i>
                        <div class="calculator-title">Design Density</div>
                    </div>
                    <div class="calculator-description">
                        Calculate required sprinkler density based on hazard classification, occupancy, and storage configuration.
                    </div>
                    <ul class="calculator-features">
                        <li>Design density calculations</li>
                        <li>Hazard-based requirements</li>
                        <li>Storage configuration factors</li>
                        <li>Coverage area optimization</li>
                    </ul>
                </a>
            </div>
        </div>

        <div class="category-section">
            <div class="category-header">
                <i class="fas fa-tint category-icon"></i>
                <div>
                    <div class="category-title">Fire Pumps</div>
                </div>
            </div>
            <div class="category-description">
                Comprehensive fire pump calculations including sizing, driver power requirements, and jockey pump analysis per NFPA 20.
            </div>
            <div class="calculators-grid">
                <a href="fire-pumps/pump-sizing.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-cog calculator-icon"></i>
                        <div class="calculator-title">Pump Sizing</div>
                    </div>
                    <div class="calculator-description">
                        Calculate required fire pump capacity and pressure with NFPA 20 compliance verification and environmental corrections.
                    </div>
                    <ul class="calculator-features">
                        <li>Flow and pressure requirements</li>
                        <li>Elevation corrections</li>
                        <li>Driver power calculations</li>
                        <li>NFPA 20 compliance</li>
                    </ul>
                </a>

                <a href="fire-pumps/driver-power.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-bolt calculator-icon"></i>
                        <div class="calculator-title">Driver Power</div>
                    </div>
                    <div class="calculator-description">
                        Calculate driver power requirements for electric motors, diesel engines, and steam turbines with efficiency corrections.
                    </div>
                    <ul class="calculator-features">
                        <li>Driver type analysis</li>
                        <li>Efficiency calculations</li>
                        <li>Power curve analysis</li>
                        <li>Environmental corrections</li>
                    </ul>
                </a>

                <a href="fire-pumps/jockey-pump.php" class="calculator-card">
                    <div class="calculator-header">
                        <i class="fas fa-sync-alt calculator-icon"></i>
                        <div class="calculator-title">Jockey Pump</div>
                    </div>
                    <div class="calculator-description">
                        Analyze jockey pump requirements for maintaining system pressure and minimizing main pump cycling.
                    </div>
                    <ul class="calculator-features">
                        <li>Pressure control analysis</li>
                        <li>Cycling frequency calculations</li>
                        <li>Flow requirements</li>
                        <li>Operating cycle simulation</li>
                    </ul>
                </a>
            </div>
        </div>

        <div class="compliance-note">
            <i class="fas fa-certificate"></i>
            All calculations comply with current NFPA standards including NFPA 13, 14, and 20.
            Results should be verified by qualified fire protection engineers.
        </div>
    </div>
</body>
</html>
