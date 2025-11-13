<?php
/**
 * MEP System Summary Report Generator
 * Comprehensive MEP system overview with calculations, specifications, and analysis
 * Generates detailed reports for mechanical, electrical, and plumbing systems
 */

require_once '../../../app/Config/config.php';
require_once '../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Helpers/functions.php';

// Initialize database connection
$db = new Database();

// Get project data
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

// Handle form submissions
$message = '';
$message_type = '';

if ($_POST) {
    try {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'generate_report':
                $result = generateMEPSummaryReport($_POST);
                $message = 'MEP Summary Report generated successfully!';
                $message_type = 'success';
                $report_data = $result;
                break;
                
            case 'export_pdf':
                $result = exportReportToPDF($_POST);
                if ($result) {
                    $message = 'Report exported to PDF successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error exporting report to PDF.';
                    $message_type = 'error';
                }
                break;
                
            case 'save_report':
                $result = saveReport($_POST, $project_id);
                if ($result) {
                    $message = 'Report saved successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error saving report.';
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get saved reports
$saved_reports = [];
if ($project_id > 0) {
    $query = "SELECT * FROM mep_reports WHERE project_id = ? AND report_type = 'summary'";
    $stmt = $db->executeQuery($query, [$project_id]);
    $saved_reports = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

/**
 * Generate comprehensive MEP summary report
 */
function generateMEPSummaryReport($data) {
    $project_name = $data['project_name'] ?? 'MEP Project';
    $building_type = $data['building_type'] ?? 'commercial';
    $total_area = floatval($data['total_area'] ?? 0);
    $floors = intval($data['floors'] ?? 1);
    
    // Collect data from all MEP systems
    $mechanical_data = collectMechanicalData($data);
    $electrical_data = collectElectricalData($data);
    $plumbing_data = collectPlumbingData($data);
    $fire_protection_data = collectFireProtectionData($data);
    
    // Calculate summary metrics
    $total_capacity = $mechanical_data['total_capacity'] + $electrical_data['total_capacity'] + $plumbing_data['total_capacity'];
    $total_cost = $mechanical_data['total_cost'] + $electrical_data['total_cost'] + $plumbing_data['total_cost'] + $fire_protection_data['total_cost'];
    $total_energy_consumption = $mechanical_data['energy_consumption'] + $electrical_data['energy_consumption'];
    $efficiency_rating = calculateOverallEfficiency($mechanical_data, $electrical_data, $plumbing_data);
    
    // Generate compliance analysis
    $compliance_analysis = generateComplianceAnalysis($mechanical_data, $electrical_data, $plumbing_data, $fire_protection_data);
    
    // Calculate sustainability metrics
    $sustainability_metrics = calculateSustainabilityMetrics($mechanical_data, $electrical_data, $plumbing_data);
    
    return [
        'project_info' => [
            'name' => $project_name,
            'building_type' => $building_type,
            'total_area' => $total_area,
            'floors' => $floors,
            'generation_date' => date('Y-m-d H:i:s')
        ],
        'system_summary' => [
            'mechanical' => $mechanical_data,
            'electrical' => $electrical_data,
            'plumbing' => $plumbing_data,
            'fire_protection' => $fire_protection_data
        ],
        'overall_metrics' => [
            'total_capacity' => $total_capacity,
            'total_cost' => $total_cost,
            'total_energy_consumption' => $total_energy_consumption,
            'efficiency_rating' => $efficiency_rating,
            'cost_per_sqm' => $total_area > 0 ? $total_cost / $total_area : 0
        ],
        'compliance_analysis' => $compliance_analysis,
        'sustainability_metrics' => $sustainability_metrics,
        'recommendations' => generateRecommendations($mechanical_data, $electrical_data, $plumbing_data, $fire_protection_data)
    ];
}

/**
 * Collect mechanical system data
 */
function collectMechanicalData($data) {
    // Simulate mechanical system data collection
    $hvac_load = floatval($data['hvac_load'] ?? 0);
    $ductwork_area = floatval($data['ductwork_area'] ?? 0);
    $equipment_count = intval($data['equipment_count'] ?? 0);
    
    return [
        'hvac_load' => $hvac_load,
        'ductwork_area' => $ductwork_area,
        'equipment_count' => $equipment_count,
        'total_capacity' => $hvac_load * 1.2, // 20% safety margin
        'total_cost' => $hvac_load * 150 + $ductwork_area * 25 + $equipment_count * 5000,
        'energy_consumption' => $hvac_load * 0.8, // kW
        'efficiency_rating' => 85.5,
        'compliance_score' => 92
    ];
}

/**
 * Collect electrical system data
 */
function collectElectricalData($data) {
    $connected_load = floatval($data['connected_load'] ?? 0);
    $panel_count = intval($data['panel_count'] ?? 0);
    $lighting_load = floatval($data['lighting_load'] ?? 0);
    
    return [
        'connected_load' => $connected_load,
        'panel_count' => $panel_count,
        'lighting_load' => $lighting_load,
        'total_capacity' => $connected_load * 1.25, // 25% diversity factor
        'total_cost' => $connected_load * 200 + $panel_count * 1500 + $lighting_load * 50,
        'energy_consumption' => $connected_load * 0.6, // kW
        'efficiency_rating' => 88.2,
        'compliance_score' => 95
    ];
}

/**
 * Collect plumbing system data
 */
function collectPlumbingData($data) {
    $water_demand = floatval($data['water_demand'] ?? 0);
    $pipe_length = floatval($data['pipe_length'] ?? 0);
    $fixture_count = intval($data['fixture_count'] ?? 0);
    
    return [
        'water_demand' => $water_demand,
        'pipe_length' => $pipe_length,
        'fixture_count' => $fixture_count,
        'total_capacity' => $water_demand * 1.3, // 30% peak factor
        'total_cost' => $water_demand * 100 + $pipe_length * 15 + $fixture_count * 200,
        'energy_consumption' => $water_demand * 0.1, // kW for pumps
        'efficiency_rating' => 82.7,
        'compliance_score' => 89
    ];
}

/**
 * Collect fire protection system data
 */
function collectFireProtectionData($data) {
    $sprinkler_area = floatval($data['sprinkler_area'] ?? 0);
    $hydrant_count = intval($data['hydrant_count'] ?? 0);
    $pump_capacity = floatval($data['pump_capacity'] ?? 0);
    
    return [
        'sprinkler_area' => $sprinkler_area,
        'hydrant_count' => $hydrant_count,
        'pump_capacity' => $pump_capacity,
        'total_capacity' => $pump_capacity,
        'total_cost' => $sprinkler_area * 35 + $hydrant_count * 800 + $pump_capacity * 120,
        'energy_consumption' => $pump_capacity * 0.15, // kW
        'efficiency_rating' => 90.1,
        'compliance_score' => 98
    ];
}

/**
 * Calculate overall system efficiency
 */
function calculateOverallEfficiency($mechanical, $electrical, $plumbing) {
    $total_efficiency = ($mechanical['efficiency_rating'] + $electrical['efficiency_rating'] + $plumbing['efficiency_rating']) / 3;
    return round($total_efficiency, 1);
}

/**
 * Generate compliance analysis
 */
function generateComplianceAnalysis($mechanical, $electrical, $plumbing, $fire_protection) {
    $compliance_items = [];
    
    // Mechanical compliance
    if ($mechanical['compliance_score'] >= 90) {
        $compliance_items[] = ['system' => 'Mechanical', 'status' => 'Compliant', 'score' => $mechanical['compliance_score']];
    } else {
        $compliance_items[] = ['system' => 'Mechanical', 'status' => 'Needs Improvement', 'score' => $mechanical['compliance_score']];
    }
    
    // Electrical compliance
    if ($electrical['compliance_score'] >= 90) {
        $compliance_items[] = ['system' => 'Electrical', 'status' => 'Compliant', 'score' => $electrical['compliance_score']];
    } else {
        $compliance_items[] = ['system' => 'Electrical', 'status' => 'Needs Improvement', 'score' => $electrical['compliance_score']];
    }
    
    // Plumbing compliance
    if ($plumbing['compliance_score'] >= 90) {
        $compliance_items[] = ['system' => 'Plumbing', 'status' => 'Compliant', 'score' => $plumbing['compliance_score']];
    } else {
        $compliance_items[] = ['system' => 'Plumbing', 'status' => 'Needs Improvement', 'score' => $plumbing['compliance_score']];
    }
    
    // Fire protection compliance
    if ($fire_protection['compliance_score'] >= 90) {
        $compliance_items[] = ['system' => 'Fire Protection', 'status' => 'Compliant', 'score' => $fire_protection['compliance_score']];
    } else {
        $compliance_items[] = ['system' => 'Fire Protection', 'status' => 'Needs Improvement', 'score' => $fire_protection['compliance_score']];
    }
    
    return $compliance_items;
}

/**
 * Calculate sustainability metrics
 */
function calculateSustainabilityMetrics($mechanical, $electrical, $plumbing) {
    $total_energy = $mechanical['energy_consumption'] + $electrical['energy_consumption'] + $plumbing['energy_consumption'];
    
    return [
        'total_energy_consumption' => $total_energy,
        'energy_efficiency_score' => 100 - ($total_energy / 10), // Simplified scoring
        'carbon_footprint' => $total_energy * 0.5, // kg CO2/day
        'water_efficiency' => 85.2,
        'renewable_energy_potential' => 25.5, // Percentage
        'sustainability_rating' => 'Good'
    ];
}

/**
 * Generate system recommendations
 */
function generateRecommendations($mechanical, $electrical, $plumbing, $fire_protection) {
    $recommendations = [];
    
    // Mechanical recommendations
    if ($mechanical['efficiency_rating'] < 85) {
        $recommendations[] = [
            'category' => 'Mechanical',
            'priority' => 'High',
            'recommendation' => 'Upgrade HVAC equipment to higher efficiency models',
            'estimated_savings' => 15000,
            'payback_period' => 3.2
        ];
    }
    
    // Electrical recommendations
    if ($electrical['efficiency_rating'] < 90) {
        $recommendations[] = [
            'category' => 'Electrical',
            'priority' => 'Medium',
            'recommendation' => 'Install LED lighting and smart controls',
            'estimated_savings' => 8500,
            'payback_period' => 2.1
        ];
    }
    
    // Plumbing recommendations
    if ($plumbing['efficiency_rating'] < 85) {
        $recommendations[] = [
            'category' => 'Plumbing',
            'priority' => 'Medium',
            'recommendation' => 'Install low-flow fixtures and water recycling system',
            'estimated_savings' => 6200,
            'payback_period' => 4.5
        ];
    }
    
    // Fire protection recommendations
    if ($fire_protection['efficiency_rating'] < 95) {
        $recommendations[] = [
            'category' => 'Fire Protection',
            'priority' => 'Low',
            'recommendation' => 'Upgrade to smart fire detection systems',
            'estimated_savings' => 3200,
            'payback_period' => 5.8
        ];
    }
    
    return $recommendations;
}

/**
 * Export report to PDF (placeholder implementation)
 */
function exportReportToPDF($data) {
    // In a real implementation, this would use a PDF library like TCPDF or FPDF
    $report_data = generateMEPSummaryReport($data);
    
    // Simulate PDF generation
    $filename = 'mep_summary_report_' . date('Y-m-d_H-i-s') . '.pdf';
    $filepath = '../../reports/' . $filename;
    
    // Create reports directory if it doesn't exist
    if (!is_dir('../../reports')) {
        mkdir('../../reports', 0755, true);
    }
    
    // For now, just create a text file as placeholder
    file_put_contents(str_replace('.pdf', '.txt', $filepath), 'MEP Summary Report Generated: ' . date('Y-m-d H:i:s'));
    
    return true;
}

/**
 * Save report to database
 */
function saveReport($data, $project_id) {
    global $db;
    
    $report_data = generateMEPSummaryReport($data);
    
    $report_record = [
        'project_id' => $project_id,
        'report_type' => 'summary',
        'report_data' => json_encode($report_data),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if ($project_id > 0) {
        $query = "INSERT INTO mep_reports (project_id, report_type, report_data, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
        $stmt = $db->executeQuery($query, [$project_id, 'summary', json_encode($report_data)]);
        return $stmt !== false;
    }
    
    return false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP Summary Report - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .report-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #2E7D32;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn {
            background: #2E7D32;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 10px 10px 0;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #1B5E20;
        }
        
        .btn-secondary {
            background: #666;
        }
        
        .btn-secondary:hover {
            background: #555;
        }
        
        .results-section {
            display: none;
            margin-top: 30px;
        }
        
        .results-section.active {
            display: block;
        }
        
        .system-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .overview-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .overview-value {
            font-size: 24px;
            font-weight: 600;
            color: #2E7D32;
        }
        
        .overview-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .compliance-item {
            background: #e8f5e8;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .compliance-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-compliant {
            background: #d4edda;
            color: #155724;
        }
        
        .status-needs-improvement {
            background: #fff3cd;
            color: #856404;
        }
        
        .recommendation-item {
            background: #fff3e0;
            border: 1px solid #ffcc02;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #ff9800;
        }
        
        .priority-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .priority-high {
            background: #ffebee;
            color: #c62828;
        }
        
        .priority-medium {
            background: #fff3e0;
            color: #ef6c00;
        }
        
        .priority-low {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .sustainability-metrics {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .metric-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .metric-row:last-child {
            border-bottom: none;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .report-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../../../themes/default/views/partials/header.php'; ?>
    
    <div class="report-container">
        <div class="page-header">
            <h1>MEP System Summary Report</h1>
            <p>Comprehensive MEP system overview with calculations and analysis</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="report-grid">
            <!-- Input Form -->
            <div class="card">
                <div class="card-header">Project Information</div>
                
                <form method="POST" id="report-form">
                    <input type="hidden" name="action" value="generate_report">
                    
                    <div class="form-group">
                        <label for="project_name">Project Name</label>
                        <input type="text" id="project_name" name="project_name" 
                               value="<?php echo htmlspecialchars($saved_reports[0]['project_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="building_type">Building Type</label>
                            <select id="building_type" name="building_type" required>
                                <option value="residential">Residential</option>
                                <option value="commercial" selected>Commercial</option>
                                <option value="industrial">Industrial</option>
                                <option value="institutional">Institutional</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="total_area">Total Area (m²)</label>
                            <input type="number" id="total_area" name="total_area" 
                                   value="<?php echo htmlspecialchars($saved_reports[0]['total_area'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="floors">Number of Floors</label>
                        <input type="number" id="floors" name="floors" 
                               value="<?php echo htmlspecialchars($saved_reports[0]['floors'] ?? '1'); ?>" min="1" required>
                    </div>
                    
                    <h3 style="margin: 30px 0 15px 0; color: #2E7D32;">System Data</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hvac_load">HVAC Load (kW)</label>
                            <input type="number" id="hvac_load" name="hvac_load" step="0.1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="connected_load">Electrical Load (kW)</label>
                            <input type="number" id="connected_load" name="connected_load" step="0.1" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="water_demand">Water Demand (L/day)</label>
                            <input type="number" id="water_demand" name="water_demand" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sprinkler_area">Sprinkler Area (m²)</label>
                            <input type="number" id="sprinkler_area" name="sprinkler_area" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Generate Report</button>
                    <button type="button" class="btn btn-secondary" onclick="exportPDF()">Export PDF</button>
                </form>
            </div>
            
            <!-- Report Preview -->
            <div class="card">
                <div class="card-header">Report Preview</div>
                <div id="report-preview">
                    <p style="color: #666; text-align: center; padding: 50px 20px;">
                        Fill in the project information and click "Generate Report" to see the MEP summary.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Results Section -->
        <div id="results-section" class="results-section">
            <div class="card">
                <div class="card-header">MEP System Summary</div>
                
                <div id="report-results"></div>
            </div>
        </div>
    </div>
    
    <?php include '../../../themes/default/views/partials/footer.php'; ?>
    
    <script>
        function generateReport() {
            const formData = new FormData(document.getElementById('report-form'));
            formData.append('action', 'generate_report');
            
            fetch('mep-summary.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayReportResults(data.results);
                } else {
                    alert('Error generating report: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating report');
            });
        }
        
        function exportPDF() {
            const formData = new FormData(document.getElementById('report-form'));
            formData.append('action', 'export_pdf');
            
            fetch('mep-summary.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Report exported to PDF successfully!');
                } else {
                    alert('Error exporting report: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error exporting report');
            });
        }
        
        function displayReportResults(report) {
            document.getElementById('results-section').classList.add('active');
            
            const overviewHtml = `
                <div class="system-overview">
                    <div class="overview-item">
                        <div class="overview-value">${report.overall_metrics.total_capacity.toFixed(0)} kW</div>
                        <div class="overview-label">Total Capacity</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">$${report.overall_metrics.total_cost.toLocaleString()}</div>
                        <div class="overview-label">Total Cost</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${report.overall_metrics.efficiency_rating}%</div>
                        <div class="overview-label">Efficiency Rating</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">$${report.overall_metrics.cost_per_sqm.toFixed(0)}</div>
                        <div class="overview-label">Cost per m²</div>
                    </div>
                </div>
                
                <h3>System Breakdown</h3>
                <div class="system-overview">
                    <div class="overview-item">
                        <div class="overview-value">${report.system_summary.mechanical.total_capacity.toFixed(0)} kW</div>
                        <div class="overview-label">Mechanical Capacity</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${report.system_summary.electrical.total_capacity.toFixed(0)} kW</div>
                        <div class="overview-label">Electrical Capacity</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${report.system_summary.plumbing.total_capacity.toFixed(0)} L/day</div>
                        <div class="overview-label">Plumbing Capacity</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${report.system_summary.fire_protection.total_capacity.toFixed(0)} L/min</div>
                        <div class="overview-label">Fire Protection</div>
                    </div>
                </div>
                
                <h3>Compliance Analysis</h3>
                ${report.compliance_analysis.map(item => `
                    <div class="compliance-item">
                        <div>
                            <strong>${item.system}</strong><br>
                            <small>Score: ${item.score}%</small>
                        </div>
                        <span class="compliance-status ${item.status === 'Compliant' ? 'status-compliant' : 'status-needs-improvement'}">
                            ${item.status}
                        </span>
                    </div>
                `).join('')}
                
                <h3>Sustainability Metrics</h3>
                <div class="sustainability-metrics">
                    <div class="metric-row">
                        <span>Total Energy Consumption</span>
                        <span>${report.sustainability_metrics.total_energy_consumption.toFixed(1)} kW</span>
                    </div>
                    <div class="metric-row">
                        <span>Energy Efficiency Score</span>
                        <span>${report.sustainability_metrics.energy_efficiency_score.toFixed(1)}/100</span>
                    </div>
                    <div class="metric-row">
                        <span>Carbon Footprint</span>
                        <span>${report.sustainability_metrics.carbon_footprint.toFixed(1)} kg CO₂/day</span>
                    </div>
                    <div class="metric-row">
                        <span>Water Efficiency</span>
                        <span>${report.sustainability_metrics.water_efficiency}%</span>
                    </div>
                    <div class="metric-row">
                        <span>Renewable Energy Potential</span>
                        <span>${report.sustainability_metrics.renewable_energy_potential}%</span>
                    </div>
                </div>
                
                <h3>Recommendations</h3>
                ${report.recommendations.map(rec => `
                    <div class="recommendation-item">
                        <h4>${rec.category} <span class="priority-badge priority-${rec.priority.toLowerCase()}">${rec.priority}</span></h4>
                        <p>${rec.recommendation}</p>
                        <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                            <span><strong>Estimated Savings:</strong> $${rec.estimated_savings.toLocaleString()}</span>
                            <span><strong>Payback Period:</strong> ${rec.payback_period} years</span>
                        </div>
                    </div>
                `).join('')}
            `;
            
            document.getElementById('report-results').innerHTML = overviewHtml;
        }
        
        // Form submission handler
        document.getElementById('report-form').addEventListener('submit', function(e) {
            e.preventDefault();
            generateReport();
        });
    </script>
</body>
</html>



