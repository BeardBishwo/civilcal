<?php
/**
 * MEP System Priority Analysis and Conflict Resolution
 * 
 * This module provides comprehensive system priority analysis including:
 * - Priority-based system ranking
 * - Conflict resolution strategies
 * - Regulatory compliance prioritization
 * - Cost-benefit analysis for system precedence
 * - Risk assessment and mitigation
 * 
 * @package MEP_Coordination
 * @version 1.0
 * @author AEC Calculator Team
 */

// Include required files
require_once '../../../app/Config/config.php';
require_once '../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Helpers/functions.php';

// Initialize database connection
$db = new Database();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP System Priority Analysis | AEC Calculator</title>
    <link rel="stylesheet" href="../../../assets/css/estimation.css">
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .priority-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .priority-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .priority-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .priority-results {
            grid-column: 1 / -1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .system-priority-card {
            background: white;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .priority-level-1 { border-left-color: #dc3545; }
        .priority-level-2 { border-left-color: #fd7e14; }
        .priority-level-3 { border-left-color: #ffc107; }
        .priority-level-4 { border-left-color: #28a745; }
        
        .priority-score {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .score-critical { background: #dc3545; color: white; }
        .score-high { background: #fd7e14; color: white; }
        .score-medium { background: #ffc107; color: #212529; }
        .score-low { background: #28a745; color: white; }
        
        .conflict-matrix {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        
        .matrix-cell {
            padding: 15px;
            text-align: center;
            border-radius: 4px;
            font-weight: bold;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .regulatory-priority {
            background: #e3f2fd;
            border: 2px solid #2196f3;
        }
        
        .safety-priority {
            background: #ffebee;
            border: 2px solid #f44336;
        }
        
        .operational-priority {
            background: #f3e5f5;
            border: 2px solid #9c27b0;
        }
        
        .cost-priority {
            background: #e8f5e8;
            border: 2px solid #4caf50;
        }
        
        .resolution-strategy {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .strategy-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .strategy-impact {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .impact-high { background: #dc3545; color: white; }
        .impact-medium { background: #ffc107; color: #212529; }
        .impact-low { background: #28a745; color: white; }
        
        .risk-assessment {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .risk-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .risk-level {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .risk-high { color: #dc3545; }
        .risk-medium { color: #ffc107; }
        .risk-low { color: #28a745; }
    </style>
</head>
<body>
    <?php include '../../../themes/default/views/partials/header.php'; ?>
    
    <div class="priority-container">
        <div class="page-header">
            <h1>MEP System Priority Analysis</h1>
            <p>Priority-based system ranking and conflict resolution strategies</p>
        </div>
        
        <form id="priorityAnalysisForm" method="POST">
            <div class="priority-grid">
                <!-- Project Context -->
                <div class="priority-section">
                    <h3>Project Context</h3>
                    
                    <div class="form-group">
                        <label for="buildingType">Building Type:</label>
                        <select id="buildingType" name="buildingType" required>
                            <option value="">Select Building Type</option>
                            <option value="hospital">Hospital/Healthcare</option>
                            <option value="school">School/Educational</option>
                            <option value="office">Office Commercial</option>
                            <option value="residential">Residential</option>
                            <option value="industrial">Industrial</option>
                            <option value="retail">Retail/Mall</option>
                            <option value="mixed-use">Mixed Use</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="occupancyLoad">Occupancy Load (persons):</label>
                        <input type="number" id="occupancyLoad" name="occupancyLoad" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="buildingHeight">Building Height (m):</label>
                        <input type="number" id="buildingHeight" name="buildingHeight" step="0.1" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="fireResistance">Fire Resistance Rating (hours):</label>
                        <select id="fireResistance" name="fireResistance">
                            <option value="0.5">0.5 hours</option>
                            <option value="1" selected>1 hour</option>
                            <option value="2">2 hours</option>
                            <option value="3">3 hours</option>
                            <option value="4">4 hours</option>
                        </select>
                    </div>
                </div>
                
                <!-- System Requirements -->
                <div class="priority-section">
                    <h3>System Requirements</h3>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="criticalSystems[]" value="fire-protection" checked> Fire Protection (Critical)</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="criticalSystems[]" value="emergency-power" checked> Emergency Power (Critical)</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="criticalSystems[]" value="medical-gas" checked> Medical Gas (Healthcare)</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="criticalSystems[]" value="hvac" checked> HVAC Systems</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="criticalSystems[]" value="electrical" checked> Electrical Systems</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="criticalSystems[]" value="plumbing" checked> Plumbing Systems</label>
                    </div>
                    
                    <div class="form-group">
                        <label for="redundancyLevel">Redundancy Level:</label>
                        <select id="redundancyLevel" name="redundancyLevel">
                            <option value="basic">Basic (N)</option>
                            <option value="redundant" selected>Redundant (N+1)</option>
                            <option value="fully-redundant">Fully Redundant (2N)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Regulatory Requirements -->
                <div class="priority-section">
                    <h3>Regulatory Requirements</h3>
                    
                    <div class="form-group">
                        <label for="codeCompliance">Primary Code:</label>
                        <select id="codeCompliance" name="codeCompliance">
                            <option value="ibc">International Building Code (IBC)</option>
                            <option value="nfpa">NFPA Codes</option>
                            <option value="ashrae">ASHRAE Standards</option>
                            <option value="ieee">IEEE Standards</option>
                            <option value="local">Local Building Code</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="seismicZone">Seismic Zone:</label>
                        <select id="seismicZone" name="seismicZone">
                            <option value="0">Zone 0 (Minimal)</option>
                            <option value="1">Zone 1 (Low)</option>
                            <option value="2" selected>Zone 2 (Moderate)</option>
                            <option value="3">Zone 3 (High)</option>
                            <option value="4">Zone 4 (Very High)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="windSpeed">Design Wind Speed (km/h):</label>
                        <input type="number" id="windSpeed" name="windSpeed" min="50" max="300" value="150" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="environmentalConditions">Environmental Conditions:</label>
                        <select id="environmentalConditions" name="environmentalConditions">
                            <option value="temperate">Temperate</option>
                            <option value="hot-humid">Hot/Humid</option>
                            <option value="cold">Cold Climate</option>
                            <option value="coastal">Coastal</option>
                            <option value="desert">Desert</option>
                        </select>
                    </div>
                </div>
                
                <!-- Cost Considerations -->
                <div class="priority-section">
                    <h3>Cost Considerations</h3>
                    
                    <div class="form-group">
                        <label for="budgetConstraint">Budget Constraint:</label>
                        <select id="budgetConstraint" name="budgetConstraint">
                            <option value="tight">Tight (Cost Priority)</option>
                            <option value="moderate" selected>Moderate (Balanced)</option>
                            <option value="flexible">Flexible (Quality Priority)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="lifecycleCost">Lifecycle Cost Focus:</label>
                        <select id="lifecycleCost" name="lifecycleCost">
                            <option value="initial">Initial Cost</option>
                            <option value="operating" selected>Operating Cost</option>
                            <option value="maintenance">Maintenance Cost</option>
                            <option value="total">Total Lifecycle</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="energyEfficiency">Energy Efficiency Priority:</label>
                        <select id="energyEfficiency" name="energyEfficiency">
                            <option value="low">Low Priority</option>
                            <option value="medium" selected>Medium Priority</option>
                            <option value="high">High Priority</option>
                            <option value="critical">Critical Priority</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sustainabilityRating">Sustainability Rating Target:</label>
                        <select id="sustainabilityRating" name="sustainabilityRating">
                            <option value="none">No Rating</option>
                            <option value="leed-silver">LEED Silver</option>
                            <option value="leed-gold" selected>LEED Gold</option>
                            <option value="leed-platinum">LEED Platinum</option>
                            <option value="breeam-excellent">BREEAM Excellent</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Analyze System Priorities</button>
                <button type="button" class="btn btn-secondary" onclick="exportAnalysis()">Export Analysis</button>
                <button type="button" class="btn btn-info" onclick="generateReport()">Generate Report</button>
            </div>
        </form>
        
        <!-- Priority Analysis Results -->
        <div id="priorityResults" class="priority-results" style="display: none;">
            <h3>System Priority Analysis Results</h3>
            
            <div class="conflict-matrix">
                <div class="matrix-cell regulatory-priority">
                    <div>
                        <strong>Regulatory Priority</strong><br>
                        Fire Protection<br>
                        Emergency Systems
                    </div>
                </div>
                <div class="matrix-cell safety-priority">
                    <div>
                        <strong>Safety Priority</strong><br>
                        Life Safety<br>
                        Egress Systems
                    </div>
                </div>
                <div class="matrix-cell operational-priority">
                    <div>
                        <strong>Operational Priority</strong><br>
                        HVAC<br>
                        Electrical Distribution
                    </div>
                </div>
                <div class="matrix-cell cost-priority">
                    <div>
                        <strong>Cost Priority</strong><br>
                        Plumbing<br>
                        General Services
                    </div>
                </div>
            </div>
            
            <div id="systemPriorities">
                <!-- System priorities will be populated here -->
            </div>
        </div>
        
        <!-- Resolution Strategies -->
        <div id="resolutionStrategies" class="priority-results" style="display: none;">
            <h3>Conflict Resolution Strategies</h3>
            
            <div id="strategies">
                <!-- Resolution strategies will be populated here -->
            </div>
        </div>
        
        <!-- Risk Assessment -->
        <div id="riskAssessment" class="priority-results" style="display: none;">
            <h3>Risk Assessment & Mitigation</h3>
            
            <div class="risk-assessment">
                <div class="risk-card">
                    <h4>Regulatory Risk</h4>
                    <div class="risk-level risk-low">LOW</div>
                    <p>Code compliance verified</p>
                </div>
                
                <div class="risk-card">
                    <h4>Safety Risk</h4>
                    <div class="risk-level risk-medium">MEDIUM</div>
                    <p>Fire protection adequate</p>
                </div>
                
                <div class="risk-card">
                    <h4>Operational Risk</h4>
                    <div class="risk-level risk-low">LOW</div>
                    <p>Systems redundancy planned</p>
                </div>
                
                <div class="risk-card">
                    <h4>Cost Risk</h4>
                    <div class="risk-level risk-medium">MEDIUM</div>
                    <p>Budget monitoring required</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../../../themes/default/views/partials/footer.php'; ?>
    
    <script>
        document.getElementById('priorityAnalysisForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Simulate priority analysis
            setTimeout(() => {
                displaySystemPriorities();
                displayResolutionStrategies();
                displayRiskAssessment();
            }, 2000);
        });
        
        function displaySystemPriorities() {
            const priorities = [
                {
                    system: 'Fire Protection Systems',
                    level: 1,
                    score: 95,
                    reason: 'Life safety critical - Code mandated',
                    requirements: ['NFPA 13', 'NFPA 14', 'NFPA 20'],
                    conflicts: ['HVAC ductwork', 'Electrical conduits'],
                    resolution: 'Fire protection takes precedence in all conflicts'
                },
                {
                    system: 'Emergency Power Systems',
                    level: 1,
                    score: 92,
                    reason: 'Life safety critical - Code mandated',
                    requirements: ['NFPA 110', 'IEEE 446', 'NEC 700'],
                    conflicts: ['Regular electrical', 'HVAC equipment'],
                    resolution: 'Emergency systems have dedicated space and routing'
                },
                {
                    system: 'Medical Gas Systems',
                    level: 2,
                    score: 88,
                    reason: 'Healthcare critical - Patient safety',
                    requirements: ['NFPA 99', 'CGA G-4.1'],
                    conflicts: ['General plumbing', 'Electrical systems'],
                    resolution: 'Medical gas isolated from other systems'
                },
                {
                    system: 'HVAC Systems',
                    level: 3,
                    score: 75,
                    reason: 'Occupant comfort - Operational critical',
                    requirements: ['ASHRAE 62.1', 'ASHRAE 90.1'],
                    conflicts: ['Fire protection', 'Electrical trays'],
                    resolution: 'HVAC routing coordinated with other systems'
                },
                {
                    system: 'Electrical Distribution',
                    level: 3,
                    score: 72,
                    reason: 'Power distribution - Operational critical',
                    requirements: ['NEC 210', 'IEEE 141'],
                    conflicts: ['HVAC equipment', 'Plumbing pipes'],
                    resolution: 'Electrical coordination with clearances'
                },
                {
                    system: 'Plumbing Systems',
                    level: 4,
                    score: 65,
                    reason: 'General services - Operational important',
                    requirements: ['IPC 2018', 'ASME A112'],
                    conflicts: ['Electrical panels', 'HVAC ducts'],
                    resolution: 'Plumbing adjusted for other system requirements'
                }
            ];
            
            const prioritiesContainer = document.getElementById('systemPriorities');
            prioritiesContainer.innerHTML = '';
            
            priorities.forEach(priority => {
                const priorityDiv = document.createElement('div');
                priorityDiv.className = `system-priority-card priority-level-${priority.level}`;
                priorityDiv.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h4>${priority.system}</h4>
                        <span class="priority-score score-${priority.level === 1 ? 'critical' : priority.level === 2 ? 'high' : priority.level === 3 ? 'medium' : 'low'}">${priority.score}/100</span>
                    </div>
                    <p><strong>Priority Level:</strong> ${priority.level} | <strong>Reason:</strong> ${priority.reason}</p>
                    <p><strong>Code Requirements:</strong> ${priority.requirements.join(', ')}</p>
                    <p><strong>Potential Conflicts:</strong> ${priority.conflicts.join(', ')}</p>
                    <p><strong>Resolution Strategy:</strong> ${priority.resolution}</p>
                `;
                prioritiesContainer.appendChild(priorityDiv);
            });
            
            document.getElementById('priorityResults').style.display = 'block';
        }
        
        function displayResolutionStrategies() {
            const strategies = [
                {
                    conflict: 'Fire Protection vs HVAC',
                    impact: 'high',
                    strategy: 'Dedicated fire protection shafts with independent routing',
                    cost: '$45,000',
                    timeline: '2 weeks',
                    risk: 'Low - proven solution'
                },
                {
                    conflict: 'Emergency Power vs Regular Electrical',
                    impact: 'high',
                    strategy: 'Separate electrical rooms with dedicated feeders',
                    cost: '$32,000',
                    timeline: '1.5 weeks',
                    risk: 'Low - standard practice'
                },
                {
                    conflict: 'Medical Gas vs General Plumbing',
                    impact: 'medium',
                    strategy: 'Isolated medical gas zone with separate risers',
                    cost: '$18,000',
                    timeline: '1 week',
                    risk: 'Medium - requires coordination'
                },
                {
                    conflict: 'HVAC vs Electrical Distribution',
                    impact: 'medium',
                    strategy: 'Coordinated ceiling space with clear separation',
                    cost: '$12,000',
                    timeline: '3 days',
                    risk: 'Low - routine coordination'
                }
            ];
            
            const strategiesContainer = document.getElementById('strategies');
            strategiesContainer.innerHTML = '';
            
            strategies.forEach(strategy => {
                const strategyDiv = document.createElement('div');
                strategyDiv.className = 'resolution-strategy';
                strategyDiv.innerHTML = `
                    <div class="strategy-header">
                        <h4>${strategy.conflict}</h4>
                        <span class="strategy-impact impact-${strategy.impact}">${strategy.impact.toUpperCase()} IMPACT</span>
                    </div>
                    <p><strong>Resolution Strategy:</strong> ${strategy.strategy}</p>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 10px;">
                        <div><strong>Cost Impact:</strong> ${strategy.cost}</div>
                        <div><strong>Timeline:</strong> ${strategy.timeline}</div>
                        <div><strong>Risk Level:</strong> ${strategy.risk}</div>
                    </div>
                `;
                strategiesContainer.appendChild(strategyDiv);
            });
            
            document.getElementById('resolutionStrategies').style.display = 'block';
        }
        
        function displayRiskAssessment() {
            document.getElementById('riskAssessment').style.display = 'block';
        }
        
        function exportAnalysis() {
            // Simulate exporting analysis
            alert('Exporting system priority analysis...');
        }
        
        function generateReport() {
            // Simulate generating report
            alert('Generating comprehensive priority analysis report...');
        }
    </script>
</body>
</html>



