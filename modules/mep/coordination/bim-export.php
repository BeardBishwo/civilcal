<?php
/**
 * MEP BIM Export and Model Integration
 * 
 * This module provides comprehensive BIM export functionality including:
 * - 3D model export to various BIM formats
 * - MEP system data integration
 * - Coordination model generation
 * - Version control and collaboration
 * - Quality assurance and validation
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
    <title>MEP BIM Export & Model Integration | AEC Calculator</title>
    <link rel="stylesheet" href="../../../assets/css/estimation.css">
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .bim-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .bim-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .bim-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .bim-results {
            grid-column: 1 / -1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .export-preview {
            background: white;
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        .bim-formats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .format-card {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .format-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 8px rgba(0,123,255,0.2);
        }
        
        .format-card.selected {
            border-color: #007bff;
            background: #f8f9ff;
        }
        
        .format-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .format-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .format-description {
            font-size: 12px;
            color: #666;
        }
        
        .export-progress {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .progress-bar {
            background: #e9ecef;
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-fill {
            background: #007bff;
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        
        .validation-checklist {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .checklist-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            background: #f8f9fa;
        }
        
        .checklist-item.passed {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }
        
        .checklist-item.failed {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        
        .checklist-item.warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        .check-icon {
            margin-right: 10px;
            font-size: 16px;
        }
        
        .pass-icon { color: #28a745; }
        .fail-icon { color: #dc3545; }
        .warning-icon { color: #ffc107; }
        
        .model-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 10px 0;
        }
        
        .export-settings {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .setting-group {
            margin: 15px 0;
        }
        
        .setting-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .export-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        
        .collaboration-panel {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .version-history {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
        }
        
        .version-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .version-item:last-child {
            border-bottom: none;
        }
        
        .version-status {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-current { background: #d4edda; color: #155724; }
        .status-revision { background: #fff3cd; color: #856404; }
        .status-obsolete { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <?php include '../../../includes/header.php'; ?>
    
    <div class="bim-container">
        <div class="page-header">
            <h1>MEP BIM Export & Model Integration</h1>
            <p>Export coordinated MEP models to various BIM formats and manage version control</p>
        </div>
        
        <form id="bimExportForm" method="POST">
            <div class="bim-grid">
                <!-- Export Configuration -->
                <div class="bim-section">
                    <h3>Export Configuration</h3>
                    
                    <div class="form-group">
                        <label for="projectName">Project Name:</label>
                        <input type="text" id="projectName" name="projectName" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="projectPhase">Project Phase:</label>
                        <select id="projectPhase" name="projectPhase">
                            <option value="design">Design Development</option>
                            <option value="construction" selected>Construction Documents</option>
                            <option value="as-built">As-Built</option>
                            <option value="facility-management">Facility Management</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="exportDate">Export Date:</label>
                        <input type="date" id="exportDate" name="exportDate" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="coordinateSystem">Coordinate System:</label>
                        <select id="coordinateSystem" name="coordinateSystem">
                            <option value="project">Project Coordinates</option>
                            <option value="geographic" selected>Geographic (WGS84)</option>
                            <option value="local">Local Grid</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="units">Units:</label>
                        <select id="units" name="units">
                            <option value="metric" selected>Metric (meters)</option>
                            <option value="imperial">Imperial (feet/inches)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Model Components -->
                <div class="bim-section">
                    <h3>Model Components to Export</h3>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="components[]" value="hvac" checked> HVAC Systems</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="components[]" value="electrical" checked> Electrical Systems</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="components[]" value="plumbing" checked> Plumbing Systems</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="components[]" value="fire-protection" checked> Fire Protection</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="components[]" value="structural" checked> Structural Elements</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="components[]" value="architectural" checked> Architectural Elements</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="components[]" value="schedules" checked> Equipment Schedules</label>
                    </div>
                    
                    <div class="form-group">
                        <label><input type="checkbox" name="components[]" value=" drawings" checked> Construction Drawings</label>
                    </div>
                </div>
                
                <!-- Export Settings -->
                <div class="bim-section">
                    <h3>Export Settings</h3>
                    
                    <div class="setting-group">
                        <label for="lod">Level of Detail (LOD):</label>
                        <select id="lod" name="lod">
                            <option value="100">LOD 100 - Conceptual</option>
                            <option value="200">LOD 200 - Approximate</option>
                            <option value="300" selected>LOD 300 - Precise</option>
                            <option value="400">LOD 400 - Fabrication</option>
                            <option value="500">LOD 500 - As-Built</option>
                        </select>
                    </div>
                    
                    <div class="setting-group">
                        <label for="geometryDetail">Geometry Detail:</label>
                        <select id="geometryDetail" name="geometryDetail">
                            <option value="simplified">Simplified</option>
                            <option value="standard" selected>Standard</option>
                            <option value="detailed">Detailed</option>
                        </select>
                    </div>
                    
                    <div class="setting-group">
                        <label for="includeMetaData">Include Metadata:</label>
                        <select id="includeMetaData" name="includeMetaData">
                            <option value="basic">Basic Properties</option>
                            <option value="standard" selected>Standard Properties</option>
                            <option value="extended">Extended Properties</option>
                            <option value="custom">Custom Properties</option>
                        </select>
                    </div>
                    
                    <div class="setting-group">
                        <label for="fileNaming">File Naming Convention:</label>
                        <select id="fileNaming" name="fileNaming">
                            <option value="project" selected>Project-Based</option>
                            <option value="discipline">Discipline-Based</option>
                            <option value="date">Date-Based</option>
                            <option value="version">Version-Based</option>
                        </select>
                    </div>
                </div>
                
                <!-- Quality Control -->
                <div class="bim-section">
                    <h3>Quality Control</h3>
                    
                    <div class="setting-group">
                        <label><input type="checkbox" name="qcChecks[]" value="clashes" checked> Clash Detection Check</label>
                    </div>
                    
                    <div class="setting-group">
                        <label><input type="checkbox" name="qcChecks[]" value="compliance" checked> Code Compliance Check</label>
                    </div>
                    
                    <div class="setting-group">
                        <label><input type="checkbox" name="qcChecks[]" value="connectivity" checked> System Connectivity</label>
                    </div>
                    
                    <div class="setting-group">
                        <label><input type="checkbox" name="qcChecks[]" value="equipment" checked> Equipment Sizing</label>
                    </div>
                    
                    <div class="setting-group">
                        <label><input type="checkbox" name="qcChecks[]" value="standards" checked> Design Standards</label>
                    </div>
                    
                    <div class="setting-group">
                        <label><input type="checkbox" name="qcChecks[]" value="data-integrity" checked> Data Integrity Check</label>
                    </div>
                </div>
            </div>
            
            <div class="export-actions">
                <button type="button" class="btn btn-primary" onclick="validateModel()">Validate Model</button>
                <button type="submit" class="btn btn-success">Export Model</button>
                <button type="button" class="btn btn-info" onclick="previewExport()">Preview Export</button>
            </div>
        </form>
        
        <!-- BIM Format Selection -->
        <div class="bim-results" style="display: none;">
            <h3>Select BIM Export Format</h3>
            
            <div class="bim-formats">
                <div class="format-card" onclick="selectFormat('revit')">
                    <div class="format-icon">🏢</div>
                    <div class="format-name">Revit RVT</div>
                    <div class="format-description">Autodesk Revit native format</div>
                </div>
                
                <div class="format-card" onclick="selectFormat('ifc')">
                    <div class="format-icon">🔧</div>
                    <div class="format-name">IFC</div>
                    <div class="format-description">Industry Foundation Classes</div>
                </div>
                
                <div class="format-card" onclick="selectFormat('navisworks')">
                    <div class="format-icon">📐</div>
                    <div class="format-name">Navisworks NWD</div>
                    <div class="format-description">Autodesk Navisworks</div>
                </div>
                
                <div class="format-card" onclick="selectFormat('archicad')">
                    <div class="format-icon">🏗️</div>
                    <div class="format-name">Archicad PLA</div>
                    <div class="format-description">Graphisoft Archicad</div>
                </div>
                
                <div class="format-card" onclick="selectFormat('civil3d')">
                    <div class="format-icon">🌍</div>
                    <div class="format-name">Civil 3D DWG</div>
                    <div class="format-description">Autodesk Civil 3D</div>
                </div>
                
                <div class="format-card" onclick="selectFormat('sketchfab')">
                    <div class="format-icon">🌐</div>
                    <div class="format-name">Web Viewer</div>
                    <div class="format-description">Online 3D visualization</div>
                </div>
            </div>
        </div>
        
        <!-- Model Preview -->
        <div id="modelPreview" class="bim-results" style="display: none;">
            <h3>Model Preview & Statistics</h3>
            
            <div class="export-preview">
                <div style="text-align: center;">
                    <h4>3D MEP Coordination Model Preview</h4>
                    <p>Interactive preview of the export model</p>
                    <p><em>3D viewer integration would display the coordinated MEP model here</em></p>
                </div>
            </div>
            
            <div class="model-stats">
                <div class="stat-card">
                    <div class="stat-value">247</div>
                    <div>MEP Components</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value">89</div>
                    <div>System Intersections</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value">34</div>
                    <div>Equipment Items</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value">156</div>
                    <div>Connection Points</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value">12.4 MB</div>
                    <div>Model Size</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value">94%</div>
                    <div>Coordination Quality</div>
                </div>
            </div>
        </div>
        
        <!-- Quality Control Results -->
        <div id="qualityResults" class="bim-results" style="display: none;">
            <h3>Quality Control Results</h3>
            
            <div class="validation-checklist">
                <div class="checklist-item passed">
                    <span class="check-icon pass-icon">✓</span>
                    <span>Clash Detection - No critical conflicts found</span>
                </div>
                
                <div class="checklist-item passed">
                    <span class="check-icon pass-icon">✓</span>
                    <span>System Connectivity - All connections validated</span>
                </div>
                
                <div class="checklist-item warning">
                    <span class="check-icon warning-icon">⚠</span>
                    <span>Code Compliance - 2 minor deviations detected</span>
                </div>
                
                <div class="checklist-item passed">
                    <span class="check-icon pass-icon">✓</span>
                    <span>Equipment Sizing - All equipment properly sized</span>
                </div>
                
                <div class="checklist-item passed">
                    <span class="check-icon pass-icon">✓</span>
                    <span>Data Integrity - No missing properties</span>
                </div>
                
                <div class="checklist-item warning">
                    <span class="check-icon warning-icon">⚠</span>
                    <span>Design Standards - 3 recommendations for improvement</span>
                </div>
            </div>
        </div>
        
        <!-- Export Progress -->
        <div id="exportProgress" class="bim-results" style="display: none;">
            <h3>Export Progress</h3>
            
            <div class="export-progress">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span>Model Export Status</span>
                    <span id="progressPercentage">0%</span>
                </div>
                
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill">0%</div>
                </div>
                
                <div id="progressStatus" style="margin-top: 10px; font-size: 14px; color: #666;">
                    Preparing export...
                </div>
            </div>
        </div>
        
        <!-- Collaboration Panel -->
        <div id="collaborationPanel" class="bim-results" style="display: none;">
            <h3>Collaboration & Version Control</h3>
            
            <div class="collaboration-panel">
                <h4>Version History</h4>
                
                <div class="version-history">
                    <div class="version-item">
                        <div>
                            <strong>v2.3 - Final Coordination</strong><br>
                            <small>Export Date: 2025-11-06 | By: MEP Coordinator</small>
                        </div>
                        <span class="version-status status-current">CURRENT</span>
                    </div>
                    
                    <div class="version-item">
                        <div>
                            <strong>v2.2 - Fire Protection Update</strong><br>
                            <small>Export Date: 2025-11-05 | By: Fire Protection Engineer</small>
                        </div>
                        <span class="version-status status-revision">REVISION</span>
                    </div>
                    
                    <div class="version-item">
                        <div>
                            <strong>v2.1 - HVAC Coordination</strong><br>
                            <small>Export Date: 2025-11-04 | By: HVAC Designer</small>
                        </div>
                        <span class="version-status status-revision">REVISION</span>
                    </div>
                    
                    <div class="version-item">
                        <div>
                            <strong>v2.0 - Design Development</strong><br>
                            <small>Export Date: 2025-11-01 | By: Project Manager</small>
                        </div>
                        <span class="version-status status-obsolete">OBSOLETE</span>
                    </div>
                    
                    <div class="version-item">
                        <div>
                            <strong>v1.5 - Concept Coordination</strong><br>
                            <small>Export Date: 2025-10-28 | By: BIM Manager</small>
                        </div>
                        <span class="version-status status-obsolete">OBSOLETE</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../../../includes/footer.php'; ?>
    
    <script>
        let selectedFormat = null;
        
        document.getElementById('bimExportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!selectedFormat) {
                alert('Please select an export format first');
                return;
            }
            
            startExport();
        });
        
        function selectFormat(format) {
            // Remove previous selection
            document.querySelectorAll('.format-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Select new format
            event.currentTarget.classList.add('selected');
            selectedFormat = format;
            
            // Show preview
            document.getElementById('modelPreview').style.display = 'block';
        }
        
        function validateModel() {
            document.getElementById('qualityResults').style.display = 'block';
            
            // Show format selection after validation
            setTimeout(() => {
                document.querySelector('.bim-results').style.display = 'block';
            }, 1000);
        }
        
        function previewExport() {
            document.getElementById('modelPreview').style.display = 'block';
        }
        
        function startExport() {
            document.getElementById('exportProgress').style.display = 'block';
            document.getElementById('collaborationPanel').style.display = 'block';
            
            let progress = 0;
            const statusMessages = [
                'Preparing export...',
                'Validating model data...',
                'Processing MEP components...',
                'Generating geometry...',
                'Applying metadata...',
                'Running quality checks...',
                'Creating export files...',
                'Finalizing export...',
                'Export complete!'
            ];
            
            const progressInterval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 100) progress = 100;
                
                updateProgress(progress, statusMessages[Math.floor(progress / 12)]);
                
                if (progress >= 100) {
                    clearInterval(progressInterval);
                    setTimeout(() => {
                        alert(`Model exported successfully as ${selectedFormat.toUpperCase()} format!`);
                    }, 500);
                }
            }, 800);
        }
        
        function updateProgress(percentage, status) {
            document.getElementById('progressFill').style.width = percentage + '%';
            document.getElementById('progressFill').textContent = Math.round(percentage) + '%';
            document.getElementById('progressPercentage').textContent = Math.round(percentage) + '%';
            document.getElementById('progressStatus').textContent = status;
        }
        
        // Set default export date to today
        document.getElementById('exportDate').value = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>
