<?php
/**
 * MEP Coordination Map and 3D Visualization
 * 
 * This module provides comprehensive coordination mapping including:
 * - 3D system visualization and routing
 * - Spatial coordination analysis
 * - Integration with BIM models
 * - Interactive coordination maps
 * - System tracking and documentation
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
    <title>MEP Coordination Map & 3D Visualization | AEC Calculator</title>
    <link rel="stylesheet" href="../../../assets/css/estimation.css">
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .coordination-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .coordination-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .coordination-controls {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .coordination-viewer {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-height: 600px;
            position: relative;
        }
        
        .coordination-results {
            grid-column: 1 / -1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .viewer-3d {
            background: #2c3e50;
            border: 2px solid #34495e;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .viewer-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .view-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .view-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .view-btn.active {
            background: #3498db;
            border-color: #3498db;
        }
        
        .system-layer {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .system-layer:hover {
            background: #f8f9fa;
        }
        
        .layer-checkbox {
            margin-right: 10px;
        }
        
        .layer-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .layer-hvac { background: #e74c3c; }
        .layer-electrical { background: #f39c12; }
        .layer-plumbing { background: #3498db; }
        .layer-fire { background: #e74c3c; }
        .layer-structure { background: #95a5a6; }
        
        .coordination-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .info-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .info-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .coordination-legend {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin: 8px 0;
        }
        
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 2px;
            margin-right: 10px;
        }
        
        .routing-path {
            position: absolute;
            border: 2px dashed;
            pointer-events: none;
        }
        
        .path-hvac {
            border-color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
        }
        
        .path-electrical {
            border-color: #f39c12;
            background: rgba(243, 156, 18, 0.1);
        }
        
        .path-plumbing {
            border-color: #3498db;
            background: rgba(52, 152, 219, 0.1);
        }
        
        .path-fire {
            border-color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
        }
        
        .navigation-tools {
            position: absolute;
            bottom: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
        }
        
        .nav-btn {
            background: rgba(255,255,255,0.9);
            border: 1px solid #ddd;
            color: #333;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .nav-btn:hover {
            background: white;
        }
        
        .floor-selector {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.9);
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
        }
        
        .floor-selector select {
            border: none;
            background: transparent;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php include '../../../includes/header.php'; ?>
    
    <div class="coordination-container">
        <div class="page-header">
            <h1>MEP Coordination Map & 3D Visualization</h1>
            <p>Interactive 3D coordination mapping and spatial analysis</p>
        </div>
        
        <div class="coordination-grid">
            <!-- Coordination Controls -->
            <div class="coordination-controls">
                <h3>View Controls</h3>
                
                <div class="form-group">
                    <label for="viewMode">View Mode:</label>
                    <select id="viewMode" name="viewMode">
                        <option value="3d">3D View</option>
                        <option value="plan">Plan View</option>
                        <option value="section">Section View</option>
                        <option value="isometric">Isometric View</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="floorLevel">Floor Level:</label>
                    <select id="floorLevel" name="floorLevel">
                        <option value="all">All Floors</option>
                        <option value="roof">Roof Level</option>
                        <option value="4">Floor 4</option>
                        <option value="3" selected>Floor 3</option>
                        <option value="2">Floor 2</option>
                        <option value="1">Floor 1</option>
                        <option value="basement">Basement</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="zoomLevel">Zoom Level:</label>
                    <input type="range" id="zoomLevel" name="zoomLevel" min="1" max="10" value="5">
                </div>
                
                <h4 style="margin-top: 20px;">System Layers</h4>
                
                <div class="system-layer">
                    <input type="checkbox" class="layer-checkbox" id="layer-hvac" checked>
                    <div class="layer-color layer-hvac"></div>
                    <label for="layer-hvac">HVAC Systems</label>
                </div>
                
                <div class="system-layer">
                    <input type="checkbox" class="layer-checkbox" id="layer-electrical" checked>
                    <div class="layer-color layer-electrical"></div>
                    <label for="layer-electrical">Electrical Systems</label>
                </div>
                
                <div class="system-layer">
                    <input type="checkbox" class="layer-checkbox" id="layer-plumbing" checked>
                    <div class="layer-color layer-plumbing"></div>
                    <label for="layer-plumbing">Plumbing Systems</label>
                </div>
                
                <div class="system-layer">
                    <input type="checkbox" class="layer-checkbox" id="layer-fire" checked>
                    <div class="layer-color layer-fire"></div>
                    <label for="layer-fire">Fire Protection</label>
                </div>
                
                <div class="system-layer">
                    <input type="checkbox" class="layer-checkbox" id="layer-structure" checked>
                    <div class="layer-color layer-structure"></div>
                    <label for="layer-structure">Building Structure</label>
                </div>
                
                <div class="form-actions" style="margin-top: 20px;">
                    <button type="button" class="btn btn-primary" onclick="updateView()">Update View</button>
                    <button type="button" class="btn btn-secondary" onclick="resetView()">Reset View</button>
                </div>
            </div>
            
            <!-- Coordination Viewer -->
            <div class="coordination-viewer">
                <div class="viewer-3d" id="viewer3d">
                    <div class="floor-selector">
                        <label for="floorDisplay">Floor: </label>
                        <select id="floorDisplay" onchange="changeFloor(this.value)">
                            <option value="4">Floor 4</option>
                            <option value="3" selected>Floor 3</option>
                            <option value="2">Floor 2</option>
                            <option value="1">Floor 1</option>
                            <option value="basement">Basement</option>
                        </select>
                    </div>
                    
                    <div class="viewer-controls">
                        <button class="view-btn active" onclick="setView('3d')">3D</button>
                        <button class="view-btn" onclick="setView('plan')">Plan</button>
                        <button class="view-btn" onclick="setView('section')">Section</button>
                        <button class="view-btn" onclick="setView('iso')">Isometric</button>
                    </div>
                    
                    <div style="text-align: center; z-index: 10;">
                        <h3>3D MEP Coordination View</h3>
                        <p>Interactive visualization of coordinated MEP systems</p>
                        <p><em>BIM integration (Revit, Navisworks, ArchiCAD) would render here</em></p>
                    </div>
                    
                    <!-- Simulated 3D Elements -->
                    <div class="routing-path path-hvac" style="top: 20%; left: 10%; width: 200px; height: 4px; transform: rotate(15deg);"></div>
                    <div class="routing-path path-electrical" style="top: 35%; left: 30%; width: 150px; height: 3px; transform: rotate(-10deg);"></div>
                    <div class="routing-path path-plumbing" style="top: 50%; left: 20%; width: 180px; height: 5px; transform: rotate(25deg);"></div>
                    <div class="routing-path path-fire" style="top: 65%; left: 40%; width: 120px; height: 4px; transform: rotate(-20deg);"></div>
                    
                    <div class="navigation-tools">
                        <button class="nav-btn" onclick="rotateView('left')">⟲</button>
                        <button class="nav-btn" onclick="rotateView('right')">⟳</button>
                        <button class="nav-btn" onclick="zoomView('in')">+</button>
                        <button class="nav-btn" onclick="zoomView('out')">-</button>
                        <button class="nav-btn" onclick="panView('up')">▲</button>
                        <button class="nav-btn" onclick="panView('down')">▼</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Coordination Information -->
        <div class="coordination-info">
            <div class="info-card">
                <h4>Total Systems</h4>
                <div class="info-value" style="color: #3498db;">247</div>
                <p>MEP components mapped</p>
            </div>
            
            <div class="info-card">
                <h4>Coordination Points</h4>
                <div class="info-value" style="color: #e74c3c;">89</div>
                <p>System intersections</p>
            </div>
            
            <div class="info-card">
                <h4>Conflicts Resolved</h4>
                <div class="info-value" style="color: #27ae60;">76</div>
                <p>Issues resolved</p>
            </div>
            
            <div class="info-card">
                <h4>Accuracy Rate</h4>
                <div class="info-value" style="color: #f39c12;">94%</div>
                <p>Coordination accuracy</p>
            </div>
        </div>
        
        <!-- System Legend -->
        <div class="coordination-legend">
            <h3>System Legend</h3>
            
            <div class="legend-item">
                <div class="legend-color layer-hvac"></div>
                <span><strong>HVAC Systems:</strong> Supply/Return ducts, Chilled water pipes, Equipment connections</span>
            </div>
            
            <div class="legend-item">
                <div class="legend-color layer-electrical"></div>
                <span><strong>Electrical Systems:</strong> Power distribution, Lighting circuits, Control systems, Emergency power</span>
            </div>
            
            <div class="legend-item">
                <div class="legend-color layer-plumbing"></div>
                <span><strong>Plumbing Systems:</strong> Water supply, Drainage, Gas lines, Fixtures</span>
            </div>
            
            <div class="legend-item">
                <div class="legend-color layer-fire"></div>
                <span><strong>Fire Protection:</strong> Sprinkler systems, Standpipes, Detection systems, Emergency systems</span>
            </div>
            
            <div class="legend-item">
                <div class="legend-color layer-structure"></div>
                <span><strong>Building Structure:</strong> Columns, Beams, Slabs, Walls, Equipment supports</span>
            </div>
        </div>
        
        <!-- Coordination Results -->
        <div id="coordinationResults" class="coordination-results" style="display: none;">
            <h3>Coordination Analysis Results</h3>
            
            <div id="coordinationDetails">
                <!-- Coordination details will be populated here -->
            </div>
        </div>
    </div>
    
    <?php include '../../../includes/footer.php'; ?>
    
    <script>
        let currentView = '3d';
        let currentFloor = '3';
        
        function setView(viewType) {
            // Update active button
            document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            currentView = viewType;
            updateViewerContent();
        }
        
        function changeFloor(floor) {
            currentFloor = floor;
            updateViewerContent();
        }
        
        function updateViewerContent() {
            const viewer = document.getElementById('viewer3d');
            const title = viewer.querySelector('h3');
            const description = viewer.querySelector('p');
            
            switch(currentView) {
                case '3d':
                    title.textContent = `3D MEP Coordination View - Floor ${currentFloor}`;
                    description.textContent = 'Interactive 3D visualization of coordinated MEP systems';
                    break;
                case 'plan':
                    title.textContent = `Plan View - Floor ${currentFloor}`;
                    description.textContent = 'Top-down plan view showing system layouts and routing';
                    break;
                case 'section':
                    title.textContent = `Section View - Floor ${currentFloor}`;
                    description.textContent = 'Cross-sectional view showing vertical system integration';
                    break;
                case 'iso':
                    title.textContent = `Isometric View - Floor ${currentFloor}`;
                    description.textContent = 'Isometric projection showing spatial relationships';
                    break;
            }
        }
        
        function updateView() {
            // Simulate view update
            document.getElementById('coordinationResults').style.display = 'block';
            displayCoordinationDetails();
        }
        
        function resetView() {
            document.getElementById('viewMode').value = '3d';
            document.getElementById('floorLevel').value = '3';
            document.getElementById('zoomLevel').value = '5';
            
            // Check all layers
            document.querySelectorAll('.layer-checkbox').forEach(cb => cb.checked = true);
            
            setView('3d');
            changeFloor('3');
        }
        
        function rotateView(direction) {
            // Simulate rotation
            console.log(`Rotating view ${direction}`);
        }
        
        function zoomView(direction) {
            const zoomSlider = document.getElementById('zoomLevel');
            if (direction === 'in' && zoomSlider.value < 10) {
                zoomSlider.value++;
            } else if (direction === 'out' && zoomSlider.value > 1) {
                zoomSlider.value--;
            }
        }
        
        function panView(direction) {
            // Simulate panning
            console.log(`Panning view ${direction}`);
        }
        
        function displayCoordinationDetails() {
            const details = [
                {
                    type: 'HVAC Coordination',
                    status: 'Complete',
                    issues: 0,
                    description: 'All HVAC systems properly coordinated with structural elements',
                    recommendation: 'Proceed with installation'
                },
                {
                    type: 'Electrical Coordination',
                    status: 'Review Required',
                    issues: 2,
                    description: 'Minor conflicts detected in electrical panel locations',
                    recommendation: 'Relocate panels 300mm to avoid duct conflicts'
                },
                {
                    type: 'Plumbing Coordination',
                    status: 'Complete',
                    issues: 0,
                    description: 'Plumbing routing coordinated with all other systems',
                    recommendation: 'Approved for construction'
                },
                {
                    type: 'Fire Protection Coordination',
                    status: 'Complete',
                    issues: 0,
                    description: 'Fire protection systems meet all code requirements',
                    recommendation: 'Ready for AHJ review'
                }
            ];
            
            const detailsContainer = document.getElementById('coordinationDetails');
            detailsContainer.innerHTML = '';
            
            details.forEach(detail => {
                const detailDiv = document.createElement('div');
                detailDiv.className = 'system-priority-card';
                detailDiv.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h4>${detail.type}</h4>
                        <span class="priority-score score-${detail.issues === 0 ? 'low' : detail.issues <= 2 ? 'medium' : 'high'}">${detail.status}</span>
                    </div>
                    <p><strong>Issues Found:</strong> ${detail.issues}</p>
                    <p><strong>Description:</strong> ${detail.description}</p>
                    <p><strong>Recommendation:</strong> ${detail.recommendation}</p>
                `;
                detailsContainer.appendChild(detailDiv);
            });
        }
        
        // Layer visibility controls
        document.querySelectorAll('.layer-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const layerClass = this.id.replace('layer-', 'path-');
                const paths = document.querySelectorAll(`.${layerClass}`);
                
                paths.forEach(path => {
                    path.style.display = this.checked ? 'block' : 'none';
                });
            });
        });
    </script>
</body>
</html>
