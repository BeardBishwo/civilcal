<?php
/**
 * MEP Space Allocation and Optimization Analysis
 * 
 * This module provides comprehensive space allocation analysis including:
 * - MEP space requirements and optimization
 * - Ceiling plenum analysis
 * - Equipment room sizing
 * - Service corridor planning
 * - Vertical shaft coordination
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
    <title>MEP Space Allocation & Optimization | AEC Calculator</title>
    <link rel="stylesheet" href="../../../assets/css/estimation.css">
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .space-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .space-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .space-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .space-results {
            grid-column: 1 / -1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .allocation-chart {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }
        
        .space-bar {
            display: flex;
            align-items: center;
            margin: 10px 0;
            background: #f8f9fa;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .space-bar-fill {
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
            transition: width 0.3s ease;
        }
        
        .bar-hvac { background: #e74c3c; }
        .bar-electrical { background: #f39c12; }
        .bar-plumbing { background: #3498db; }
        .bar-fire { background: #e74c3c; }
        .bar-structure { background: #95a5a6; }
        .bar-access { background: #27ae60; }
        
        .space-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .summary-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .optimal { color: #27ae60; }
        .warning { color: #f39c12; }
        .critical { color: #e74c3c; }
        
        .room-analysis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .room-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
        }
        
        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .room-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-optimal { background: #d4edda; color: #155724; }
        .status-tight { background: #fff3cd; color: #856404; }
        .status-overcrowded { background: #f8d7da; color: #721c24; }
        
        .space-requirement {
            margin: 10px 0;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
        }
        
        .plenum-analysis {
            background: white;
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .plenum-visual {
            background: #e9ecef;
            border: 1px dashed #6c757d;
            height: 200px;
            border-radius: 4px;
            position: relative;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .plenum-layer {
            position: absolute;
            border-radius: 2px;
        }
        
        .plenum-ducts {
            background: #e74c3c;
            opacity: 0.7;
        }
        
        .plenum-pipes {
            background: #3498db;
            opacity: 0.7;
        }
        
        .plenum-conduits {
            background: #f39c12;
            opacity: 0.7;
        }
        
        .plenum-clearance {
            background: #27ae60;
            opacity: 0.3;
        }
        
        .optimization-suggestions {
            background: #e8f4f8;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        
        .suggestion-item {
            margin: 8px 0;
            padding: 8px;
            background: white;
            border-radius: 4px;
        }
        
        .cost-impact {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .impact-low { background: #d4edda; color: #155724; }
        .impact-medium { background: #fff3cd; color: #856404; }
        .impact-high { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <?php include '../../../includes/header.php'; ?>
    
    <div class="space-container">
        <div class="page-header">
            <h1>MEP Space Allocation & Optimization</h1>
            <p>Comprehensive space analysis and optimization for MEP systems</p>
        </div>
        
        <form id="spaceAnalysisForm" method="POST">
            <div class="space-grid">
                <!-- Building Configuration -->
                <div class="space-section">
                    <h3>Building Configuration</h3>
                    
                    <div class="form-group">
                        <label for="buildingType">Building Type:</label>
                        <select id="buildingType" name="buildingType" required>
                            <option value="">Select Building Type</option>
                            <option value="office">Office Building</option>
                            <option value="hospital">Hospital/Healthcare</option>
                            <option value="school">School/Educational</option>
                            <option value="retail">Retail/Commercial</option>
                            <option value="residential">Residential</option>
                            <option value="industrial">Industrial</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="totalFloorArea">Total Floor Area (m²):</label>
                        <input type="number" id="totalFloorArea" name="totalFloorArea" min="100" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="numberOfFloors">Number of Floors:</label>
                        <input type="number" id="numberOfFloors" name="numberOfFloors" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="floorHeight">Floor Height (m):</label>
                        <input type="number" id="floorHeight" name="floorHeight" step="0.1" min="2.5" value="3.0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="ceilingType">Ceiling Type:</label>
                        <select id="ceilingType" name="ceilingType">
                            <option value="suspended">Suspended Ceiling</option>
                            <option value="exposed">Exposed Structure</option>
                            <option value="concrete">Concrete Slab</option>
                            <option value="composite">Composite System</option>
                        </select>
                    </div>
                </div>
                
                <!-- MEP Requirements -->
                <div class="space-section">
                    <h3>MEP Space Requirements</h3>
                    
                    <div class="form-group">
                        <label for="hvacLoad">HVAC Load Density (W/m²):</label>
                        <input type="number" id="hvacLoad" name="hvacLoad" min="50" max="200" value="80" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="electricalDensity">Electrical Density (W/m²):</label>
                        <input type="number" id="electricalDensity" name="electricalDensity" min="20" max="150" value="50" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="plumbingFixtures">Plumbing Fixtures per Floor:</label>
                        <input type="number" id="plumbingFixtures" name="plumbingFixtures" min="2" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="fireProtectionLevel">Fire Protection Level:</label>
                        <select id="fireProtectionLevel" name="fireProtectionLevel">
                            <option value="basic">Basic (Sprinklers only)</option>
                            <option value="standard" selected>Standard (Sprinklers + Standpipes)</option>
                            <option value="enhanced">Enhanced (Full coverage)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="equipmentRedundancy">Equipment Redundancy:</label>
                        <select id="equipmentRedundancy" name="equipmentRedundancy">
                            <option value="basic">Basic (N)</option>
                            <option value="redundant" selected>Redundant (N+1)</option>
                            <option value="full">Full Redundancy (2N)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Space Constraints -->
                <div class="space-section">
                    <h3>Space Constraints</h3>
                    
                    <div class="form-group">
                        <label for="plenumHeight">Available Plenum Height (mm):</label>
                        <input type="number" id="plenumHeight" name="plenumHeight" min="100" value="300" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="serviceSpacePercent">Service Space (% of floor area):</label>
                        <input type="number" id="serviceSpacePercent" name="serviceSpacePercent" min="10" max="35" value="20" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="equipmentRoomLimit">Equipment Room Max Area (m²):</label>
                        <input type="number" id="equipmentRoomLimit" name="equipmentRoomLimit" min="10" value="50" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="corridorWidth">Service Corridor Width (m):</label>
                        <input type="number" id="corridorWidth" name="corridorWidth" min="0.8" step="0.1" value="1.2" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="maintenanceAccess">Maintenance Access Required:</label>
                        <select id="maintenanceAccess" name="maintenanceAccess">
                            <option value="standard" selected>Standard</option>
                            <option value="enhanced">Enhanced Access</option>
                            <option value="minimal">Minimal Access</option>
                        </select>
                    </div>
                </div>
                
                <!-- Optimization Parameters -->
                <div class="space-section">
                    <h3>Optimization Parameters</h3>
                    
                    <div class="form-group">
                        <label for="costPriority">Cost vs Space Priority:</label>
                        <select id="costPriority" name="costPriority">
                            <option value="cost-first">Cost Optimized</option>
                            <option value="balanced" selected>Balanced</option>
                            <option value="space-first">Space Optimized</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="futureExpansion">Future Expansion Factor:</label>
                        <select id="futureExpansion" name="futureExpansion">
                            <option value="0">No Expansion</option>
                            <option value="10">10% Expansion</option>
                            <option value="20" selected>20% Expansion</option>
                            <option value="30">30% Expansion</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sustainabilityTarget">Sustainability Target:</label>
                        <select id="sustainabilityTarget" name="sustainabilityTarget">
                            <option value="standard">Standard Practice</option>
                            <option value="leed-silver">LEED Silver</option>
                            <option value="leed-gold" selected>LEED Gold</option>
                            <option value="leed-platinum">LEED Platinum</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="buildingHeight">Total Building Height (m):</label>
                        <input type="number" id="buildingHeight" name="buildingHeight" step="0.1" required>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Analyze Space Allocation</button>
                <button type="button" class="btn btn-secondary" onclick="optimizeSpace()">Optimize Layout</button>
                <button type="button" class="btn btn-info" onclick="generateReport()">Generate Report</button>
            </div>
        </form>
        
        <!-- Space Allocation Results -->
        <div id="spaceResults" class="space-results" style="display: none;">
            <h3>Space Allocation Analysis Results</h3>
            
            <div class="space-summary">
                <div class="summary-card">
                    <h4>Total MEP Space Required</h4>
                    <div class="summary-value optimal">18.5%</div>
                    <p>Of total floor area</p>
                </div>
                
                <div class="summary-card">
                    <h4>Plenum Utilization</h4>
                    <div class="summary-value warning">67%</div>
                    <p>Of available height</p>
                </div>
                
                <div class="summary-card">
                    <h4>Equipment Room Sizing</h4>
                    <div class="summary-value optimal">42 m²</div>
                    <p>Adequate for load</p>
                </div>
                
                <div class="summary-card">
                    <h4>Service Corridors</h4>
                    <div class="summary-value optimal">12.8 m</div>
                    <p>Total length per floor</p>
                </div>
            </div>
            
            <!-- Space Allocation Chart -->
            <div class="allocation-chart">
                <h4>Space Allocation Breakdown</h4>
                
                <div class="space-bar">
                    <span style="width: 120px; font-weight: bold;">HVAC Systems</span>
                    <div class="space-bar-fill bar-hvac" style="width: 45%;">45% - 8.3 m²</div>
                </div>
                
                <div class="space-bar">
                    <span style="width: 120px; font-weight: bold;">Electrical Systems</span>
                    <div class="space-bar-fill bar-electrical" style="width: 25%;">25% - 4.6 m²</div>
                </div>
                
                <div class="space-bar">
                    <span style="width: 120px; font-weight: bold;">Plumbing Systems</span>
                    <div class="space-bar-fill bar-plumbing" style="width: 15%;">15% - 2.8 m²</div>
                </div>
                
                <div class="space-bar">
                    <span style="width: 120px; font-weight: bold;">Fire Protection</span>
                    <div class="space-bar-fill bar-fire" style="width: 10%;">10% - 1.9 m²</div>
                </div>
                
                <div class="space-bar">
                    <span style="width: 120px; font-weight: bold;">Access/Maintenance</span>
                    <div class="space-bar-fill bar-access" style="width: 5%;">5% - 0.9 m²</div>
                </div>
            </div>
        </div>
        
        <!-- Room Analysis -->
        <div id="roomAnalysis" class="space-results" style="display: none;">
            <h3>Equipment Room Analysis</h3>
            
            <div class="room-analysis">
                <div class="room-card">
                    <div class="room-header">
                        <h4>HVAC Equipment Room</h4>
                        <span class="room-status status-optimal">OPTIMAL</span>
                    </div>
                    
                    <div class="space-requirement">
                        <span>Required Area:</span>
                        <span>32 m²</span>
                    </div>
                    <div class="space-requirement">
                        <span>Available Area:</span>
                        <span>35 m²</span>
                    </div>
                    <div class="space-requirement">
                        <span>Ceiling Height:</span>
                        <span>4.5 m</span>
                    </div>
                    
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        Adequate space for AHU, pumps, and associated equipment with proper maintenance access.
                    </p>
                </div>
                
                <div class="room-card">
                    <div class="room-header">
                        <h4>Electrical Room</h4>
                        <span class="room-status status-tight">TIGHT</span>
                    </div>
                    
                    <div class="space-requirement">
                        <span>Required Area:</span>
                        <span>18 m²</span>
                    </div>
                    <div class="space-requirement">
                        <span>Available Area:</span>
                        <span>16 m²</span>
                    </div>
                    <div class="space-requirement">
                        <span>Ceiling Height:</span>
                        <span>3.0 m</span>
                    </div>
                    
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        Consider relocating some panels to adjacent space or optimizing panel arrangement.
                    </p>
                </div>
                
                <div class="room-card">
                    <div class="room-header">
                        <h4>Pump Room</h4>
                        <span class="room-status status-optimal">OPTIMAL</span>
                    </div>
                    
                    <div class="space-requirement">
                        <span>Required Area:</span>
                        <span>12 m²</span>
                    </div>
                    <div class="space-requirement">
                        <span>Available Area:</span>
                        <span>15 m²</span>
                    </div>
                    <div class="space-requirement">
                        <span>Ceiling Height:</span>
                        <span>3.5 m</span>
                    </div>
                    
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        Good space allocation with room for future expansion and redundancy equipment.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Plenum Analysis -->
        <div id="plenumAnalysis" class="space-results" style="display: none;">
            <h3>Ceiling Plenum Analysis</h3>
            
            <div class="plenum-analysis">
                <div class="plenum-visual">
                    <div class="plenum-layer plenum-ducts" style="top: 20%; left: 10%; width: 60%; height: 40px;"></div>
                    <div class="plenum-layer plenum-pipes" style="top: 50%; left: 20%; width: 40%; height: 25px;"></div>
                    <div class="plenum-layer plenum-conduits" style="top: 70%; left: 30%; width: 35%; height: 20px;"></div>
                    <div class="plenum-layer plenum-clearance" style="top: 0%; left: 0%; width: 100%; height: 15px; background: #27ae60; opacity: 0.3;"></div>
                    <div class="plenum-layer plenum-clearance" style="top: 85%; left: 0%; width: 100%; height: 15px; background: #27ae60; opacity: 0.3;"></div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <strong>Plenum Height:</strong> 300mm<br>
                        <strong>Occupied Height:</strong> 200mm<br>
                        <strong>Clearance:</strong> 100mm (33%)
                    </div>
                    <div>
                        <strong>Ducts:</strong> 40% of area<br>
                        <strong>Pipes:</strong> 25% of area<br>
                        <strong>Conduits:</strong> 20% of area
                    </div>
                    <div>
                        <strong>Available Space:</strong> 15%<br>
                        <strong>Future Expansion:</strong> Available<br>
                        <strong>Status:</strong> <span style="color: #27ae60;">Adequate</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Optimization Suggestions -->
        <div id="optimizationSuggestions" class="space-results" style="display: none;">
            <h3>Space Optimization Suggestions</h3>
            
            <div class="optimization-suggestions">
                <h4>Recommended Optimizations:</h4>
                
                <div class="suggestion-item">
                    <strong>Electrical Room Expansion</strong>
                    <span class="cost-impact impact-medium">MEDIUM COST</span>
                    <p>Relocate non-critical panels to adjacent storage room (+4 m²)</p>
                </div>
                
                <div class="suggestion-item">
                    <strong>Ductwork Optimization</strong>
                    <span class="cost-impact impact-low">LOW COST</span>
                    <p>Use oval ducts in tight spaces to reduce height requirement by 15%</p>
                </div>
                
                <div class="suggestion-item">
                    <strong>Vertical Riser Consolidation</strong>
                    <span class="cost-impact impact-high">HIGH COST</span>
                    <p>Combine MEP risers in central shafts to save 12% vertical space</p>
                </div>
                
                <div class="suggestion-item">
                    <strong>Maintenance Access Improvement</strong>
                    <span class="cost-impact impact-low">LOW COST</span>
                    <p>Install service panels and access doors to reduce clearance requirements</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../../../includes/footer.php'; ?>
    
    <script>
        document.getElementById('spaceAnalysisForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Simulate space analysis
            setTimeout(() => {
                displaySpaceResults();
                displayRoomAnalysis();
                displayPlenumAnalysis();
                displayOptimizationSuggestions();
            }, 2000);
        });
        
        function displaySpaceResults() {
            document.getElementById('spaceResults').style.display = 'block';
        }
        
        function displayRoomAnalysis() {
            document.getElementById('roomAnalysis').style.display = 'block';
        }
        
        function displayPlenumAnalysis() {
            document.getElementById('plenumAnalysis').style.display = 'block';
        }
        
        function displayOptimizationSuggestions() {
            document.getElementById('optimizationSuggestions').style.display = 'block';
        }
        
        function optimizeSpace() {
            // Simulate space optimization
            alert('Running space optimization algorithm...');
            
            setTimeout(() => {
                alert('Optimization complete! Space efficiency improved by 8%');
            }, 1500);
        }
        
        function generateReport() {
            // Simulate report generation
            alert('Generating comprehensive space allocation report...');
        }
        
        // Auto-calculate building height
        document.getElementById('numberOfFloors').addEventListener('input', function() {
            const floors = parseInt(this.value);
            const height = floors * parseFloat(document.getElementById('floorHeight').value);
            document.getElementById('buildingHeight').value = height;
        });
        
        document.getElementById('floorHeight').addEventListener('input', function() {
            const floors = parseInt(document.getElementById('numberOfFloors').value);
            const height = floors * parseFloat(this.value);
            document.getElementById('buildingHeight').value = height;
        });
    </script>
</body>
</html>
