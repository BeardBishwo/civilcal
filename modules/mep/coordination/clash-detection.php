<?php
/**
 * MEP Clash Detection and Coordination Analysis
 * 
 * This module provides comprehensive clash detection for MEP systems including:
 * - Pipe and duct routing conflicts
 * - Equipment placement conflicts  
 * - Space allocation issues
 * - 3D coordination analysis
 * - Priority-based clash resolution
 * 
 * @package MEP_Coordination
 * @version 1.0
 * @author AEC Calculator Team
 */

// Include required files
require_once '../../../includes/config.php';
require_once '../../../includes/Database.php';
require_once '../../../includes/functions.php';

// Initialize database connection
$db = new Database();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP Clash Detection & Coordination | AEC Calculator</title>
    <link rel="stylesheet" href="../../../assets/css/estimation.css">
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .clash-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .clash-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .clash-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .clash-results {
            grid-column: 1 / -1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .clash-item {
            background: white;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .clash-item.resolved {
            border-left-color: #28a745;
            opacity: 0.7;
        }
        
        .clash-severity {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .severity-critical { background: #dc3545; color: white; }
        .severity-high { background: #fd7e14; color: white; }
        .severity-medium { background: #ffc107; color: #212529; }
        .severity-low { background: #28a745; color: white; }
        
        .coordination-3d {
            background: #e9ecef;
            border: 2px dashed #6c757d;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        .priority-matrix {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        
        .priority-cell {
            padding: 15px;
            text-align: center;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .priority-high { background: #dc3545; color: white; }
        .priority-medium { background: #ffc107; color: #212529; }
        .priority-low { background: #28a745; color: white; }
        
        .space-analysis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .space-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .space-usage {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .usage-optimal { color: #28a745; }
        .usage-warning { color: #ffc107; }
        .usage-critical { color: #dc3545; }
    </style>
</head>
<body>
    <?php include '../../../includes/header.php'; ?>
    
    <div class="clash-container">
        <div class="page-header">
            <h1>MEP Clash Detection & Coordination</h1>
            <p>Advanced 3D coordination analysis and clash detection for MEP systems</p>
        </div>
        
        <form id="clashDetectionForm" method="POST">
            <div class="clash-grid">
                <!-- System Configuration -->
                <div class="clash-section">
                    <h3>System Configuration</h3>
                    
                    <div class="form-group">
                        <label for="projectType">Project Type:</label>
                        <select id="projectType" name="projectType" required>
                            <option value="">Select Project Type</option>
                            <option value="commercial">Commercial Building</option>
                            <option value="residential">Residential Complex</option>
                            <option value="industrial">Industrial Facility</option>
                            <option value="healthcare">Healthcare Facility</option>
                            <option value="education">Educational Building</option>
                            <option value="mixed-use">Mixed Use Development</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="buildingHeight">Building Height (m):</label>
                        <input type="number" id="buildingHeight" name="buildingHeight" step="0.1" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="totalFloors">Total Floors:</label>
                        <input type="number" id="totalFloors" name="totalFloors" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="floorHeight">Floor Height (m):</label>
                        <input type="number" id="floorHeight" name="floorHeight" step="0.1" min="2.5" value="3.0" required>
                    </div>
                </div>
                
                <!-- MEP Systems -->
                <div class="clash-section">
                    <h3>MEP Systems Analysis</h3>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="systems[]" value="hvac" checked> HVAC Systems</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="systems[]" value="electrical" checked> Electrical Systems</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="systems[]" value="plumbing" checked> Plumbing Systems</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="systems[]" value="fire-protection" checked> Fire Protection</label>
                    </div>
                    
                    <div class="form-group">
                        <label for="coordinationLevel">Coordination Level:</label>
                        <select id="coordinationLevel" name="coordinationLevel">
                            <option value="basic">Basic (25mm tolerance)</option>
                            <option value="standard" selected>Standard (10mm tolerance)</option>
                            <option value="high">High Precision (5mm tolerance)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Space Constraints -->
                <div class="clash-section">
                    <h3>Space Constraints</h3>
                    
                    <div class="form-group">
                        <label for="ceilingHeight">Ceiling Height (m):</label>
                        <input type="number" id="ceilingHeight" name="ceilingHeight" step="0.1" min="2.4" value="2.7" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="slabThickness">Slab Thickness (mm):</label>
                        <input type="number" id="slabThickness" name="slabThickness" min="100" value="200" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="serviceSpace">Service Space (% of floor area):</label>
                        <input type="number" id="serviceSpace" name="serviceSpace" min="5" max="30" value="15" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="clearanceRequired">Minimum Clearance (mm):</label>
                        <input type="number" id="clearanceRequired" name="clearanceRequired" min="25" value="50" required>
                    </div>
                </div>
                
                <!-- Analysis Parameters -->
                <div class="clash-section">
                    <h3>Analysis Parameters</h3>
                    
                    <div class="form-group">
                        <label for="analysisType">Analysis Type:</label>
                        <select id="analysisType" name="analysisType">
                            <option value="comprehensive" selected>Comprehensive</option>
                            <option value="hard-clashes">Hard Clashes Only</option>
                            <option value="soft-clashes">Soft Clashes Only</option>
                            <option value="4d-simulation">4D Time-based</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="prioritySystem">Priority System:</label>
                        <select id="prioritySystem" name="prioritySystem">
                            <option value="fire-protection">Fire Protection</option>
                            <option value="electrical">Electrical</option>
                            <option value="hvac">HVAC</option>
                            <option value="plumbing">Plumbing</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="resolutionMethod">Resolution Method:</label>
                        <select id="resolutionMethod" name="resolutionMethod">
                            <option value="automatic">Automatic</option>
                            <option value="manual" selected>Manual Review</option>
                            <option value="hybrid">Hybrid Approach</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Run Clash Detection</button>
                <button type="button" class="btn btn-secondary" onclick="exportResults()">Export Results</button>
                <button type="button" class="btn btn-info" onclick="generateReport()">Generate Report</button>
            </div>
        </form>
        
        <!-- 3D Coordination View -->
        <div class="coordination-3d">
            <h4>3D Coordination View</h4>
            <p>Interactive 3D model showing MEP system coordination</p>
            <p><em>Integration with BIM software (Revit, Navisworks) would display here</em></p>
        </div>
        
        <!-- Clash Detection Results -->
        <div id="clashResults" class="clash-results" style="display: none;">
            <h3>Clash Detection Results</h3>
            
            <div class="priority-matrix">
                <div class="priority-cell priority-high">Critical: 3</div>
                <div class="priority-cell priority-medium">High: 7</div>
                <div class="priority-cell priority-low">Medium: 12</div>
            </div>
            
            <div id="clashList">
                <!-- Clash results will be populated here -->
            </div>
        </div>
        
        <!-- Space Analysis -->
        <div id="spaceAnalysis" class="clash-results" style="display: none;">
            <h3>Space Utilization Analysis</h3>
            
            <div class="space-analysis">
                <div class="space-card">
                    <h4>HVAC Space</h4>
                    <div class="space-usage usage-optimal">85%</div>
                    <p>Optimal utilization</p>
                </div>
                
                <div class="space-card">
                    <h4>Electrical Space</h4>
                    <div class="space-usage usage-warning">92%</div>
                    <p>Approaching limit</p>
                </div>
                
                <div class="space-card">
                    <h4>Plumbing Space</h4>
                    <div class="space-usage usage-optimal">78%</div>
                    <p>Good utilization</p>
                </div>
                
                <div class="space-card">
                    <h4>Fire Protection</h4>
                    <div class="space-usage usage-critical">98%</div>
                    <p>Critical - needs review</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../../../includes/footer.php'; ?>
    
    <script>
        document.getElementById('clashDetectionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Simulate clash detection analysis
            setTimeout(() => {
                displayClashResults();
                displaySpaceAnalysis();
            }, 2000);
        });
        
        function displayClashResults() {
            const results = [
                {
                    id: 1,
                    type: 'Hard Clash',
                    description: 'HVAC duct intersects with electrical conduit in ceiling space',
                    location: 'Floor 3, Zone B',
                    severity: 'critical',
                    systems: ['HVAC', 'Electrical'],
                    resolution: 'Relocate electrical conduit 150mm to the east'
                },
                {
                    id: 2,
                    type: 'Hard Clash',
                    description: 'Fire sprinkler pipe conflicts with HVAC supply duct',
                    location: 'Floor 2, Main corridor',
                    severity: 'high',
                    systems: ['Fire Protection', 'HVAC'],
                    resolution: 'Adjust sprinkler routing around duct'
                },
                {
                    id: 3,
                    type: 'Soft Clash',
                    description: 'Insufficient clearance between plumbing stack and electrical panel',
                    location: 'Floor 1, Electrical room',
                    severity: 'medium',
                    systems: ['Plumbing', 'Electrical'],
                    resolution: 'Increase clearance to 100mm minimum'
                },
                {
                    id: 4,
                    type: 'Hard Clash',
                    description: 'Multiple MEP systems in same ceiling space',
                    location: 'Floor 4, Conference area',
                    severity: 'high',
                    systems: ['HVAC', 'Electrical', 'Plumbing'],
                    resolution: 'Redistribute systems across multiple ceiling zones'
                }
            ];
            
            const clashList = document.getElementById('clashList');
            clashList.innerHTML = '';
            
            results.forEach(clash => {
                const clashDiv = document.createElement('div');
                clashDiv.className = 'clash-item';
                clashDiv.innerHTML = `
                    <div style="display: flex; justify-content: between; align-items: center;">
                        <h4>Clash #${clash.id}: ${clash.type}</h4>
                        <span class="clash-severity severity-${clash.severity}">${clash.severity.toUpperCase()}</span>
                    </div>
                    <p><strong>Description:</strong> ${clash.description}</p>
                    <p><strong>Location:</strong> ${clash.location}</p>
                    <p><strong>Systems:</strong> ${clash.systems.join(', ')}</p>
                    <p><strong>Resolution:</strong> ${clash.resolution}</p>
                    <div style="margin-top: 10px;">
                        <button class="btn btn-sm btn-success" onclick="resolveClash(${clash.id})">Mark Resolved</button>
                        <button class="btn btn-sm btn-info" onclick="viewDetails(${clash.id})">View Details</button>
                    </div>
                `;
                clashList.appendChild(clashDiv);
            });
            
            document.getElementById('clashResults').style.display = 'block';
        }
        
        function displaySpaceAnalysis() {
            document.getElementById('spaceAnalysis').style.display = 'block';
        }
        
        function resolveClash(clashId) {
            // Simulate resolving a clash
            const clashItems = document.querySelectorAll('.clash-item');
            clashItems[clashId - 1].classList.add('resolved');
            clashItems[clashId - 1].style.opacity = '0.7';
        }
        
        function viewDetails(clashId) {
            // Simulate viewing clash details
            alert(`Viewing detailed information for Clash #${clashId}`);
        }
        
        function exportResults() {
            // Simulate exporting results
            alert('Exporting clash detection results...');
        }
        
        function generateReport() {
            // Simulate generating report
            alert('Generating comprehensive clash detection report...');
        }
    </script>
</body>
</html>
