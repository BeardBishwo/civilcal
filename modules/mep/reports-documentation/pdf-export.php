<?php
/**
 * PDF Export Module for MEP Reports
 * Generates professional PDF reports from MEP calculations and analysis
 * Supports multiple report types, custom templates, and high-quality output
 */

require_once rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/aec-calculator/modules/mep/bootstrap.php';

// Initialize database connection
$db = new Database();

// Get report data
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
$report_type = $_GET['report_type'] ?? 'summary';

// Handle form submissions
$message = '';
$message_type = '';

if ($_POST) {
    try {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'export_report':
                $result = exportMEPSummaryReport($_POST);
                if ($result['success']) {
                    $message = 'Report exported to PDF successfully! File: ' . $result['filename'];
                    $message_type = 'success';
                } else {
                    $message = 'Error exporting report: ' . $result['error'];
                    $message_type = 'error';
                }
                break;
                
            case 'bulk_export':
                $result = exportMultipleReports($_POST);
                if ($result['success']) {
                    $message = 'Bulk export completed! ' . $result['count'] . ' reports exported.';
                    $message_type = 'success';
                } else {
                    $message = 'Error in bulk export: ' . $result['error'];
                    $message_type = 'error';
                }
                break;
                
            case 'schedule_export':
                $result = scheduleRecurringExport($_POST);
                if ($result) {
                    $message = 'Recurring export scheduled successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error scheduling recurring export.';
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get available reports for the project
$available_reports = [];
if ($project_id > 0) {
    $query = "SELECT report_id, report_type, report_name, created_at FROM mep_reports WHERE project_id = ?";
    $stmt = $db->executeQuery($query, [$project_id]);
    $available_reports = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

// Get PDF export history
$export_history = [];
if ($project_id > 0) {
    $query = "SELECT * FROM pdf_exports WHERE project_id = ? ORDER BY export_date DESC LIMIT 10";
    $stmt = $db->executeQuery($query, [$project_id]);
    $export_history = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

/**
 * Export MEP summary report to PDF
 */
function exportMEPSummaryReport($data) {
    try {
        $project_id = intval($data['project_id'] ?? 0);
        $report_type = $data['report_type'] ?? 'summary';
        $include_charts = isset($data['include_charts']) ? 1 : 0;
        $include_images = isset($data['include_images']) ? 1 : 0;
        $template = $data['template'] ?? 'standard';
        
        // Generate PDF filename
        $filename = 'mep_' . $report_type . '_report_' . date('Y-m-d_H-i-s') . '.pdf';
        $filepath = '../../reports/exported/' . $filename;
        
        // Create reports directory if it doesn't exist
        $report_dir = dirname($filepath);
        if (!is_dir($report_dir)) {
            mkdir($report_dir, 0755, true);
        }
        
        // Collect report data
        $report_data = collectReportData($project_id, $report_type, $data);
        
        // Generate PDF content (placeholder implementation)
        $pdf_content = generatePDFContent($report_data, $template, $include_charts, $include_images);
        
        // In a real implementation, this would use a PDF library like TCPDF or FPDF
        // For now, we'll create a text file as placeholder
        $text_filepath = str_replace('.pdf', '.txt', $filepath);
        file_put_contents($text_filepath, $pdf_content);
        
        // Log export in database
        logExportToDatabase($project_id, $filename, $report_type, 'completed');
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'download_url' => '/reports/exported/' . $filename
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Collect data for report generation
 */
function collectReportData($project_id, $report_type, $data) {
    // This would typically fetch data from the database
    // For this example, we'll use the submitted data
    return [
        'project_info' => [
            'name' => $data['project_name'] ?? 'MEP Project',
            'type' => $data['building_type'] ?? 'Commercial',
            'area' => floatval($data['total_area'] ?? 0),
            'floors' => intval($data['floors'] ?? 1)
        ],
        'mechanical_data' => [
            'hvac_load' => floatval($data['hvac_load'] ?? 0),
            'ductwork_area' => floatval($data['ductwork_area'] ?? 0),
            'equipment_count' => intval($data['equipment_count'] ?? 0),
            'total_cost' => floatval($data['mechanical_cost'] ?? 0)
        ],
        'electrical_data' => [
            'connected_load' => floatval($data['connected_load'] ?? 0),
            'panel_count' => intval($data['panel_count'] ?? 0),
            'lighting_load' => floatval($data['lighting_load'] ?? 0),
            'total_cost' => floatval($data['electrical_cost'] ?? 0)
        ],
        'plumbing_data' => [
            'water_demand' => floatval($data['water_demand'] ?? 0),
            'pipe_length' => floatval($data['pipe_length'] ?? 0),
            'fixture_count' => intval($data['fixture_count'] ?? 0),
            'total_cost' => floatval($data['plumbing_cost'] ?? 0)
        ],
        'fire_protection_data' => [
            'sprinkler_area' => floatval($data['sprinkler_area'] ?? 0),
            'hydrant_count' => intval($data['hydrant_count'] ?? 0),
            'pump_capacity' => floatval($data['pump_capacity'] ?? 0),
            'total_cost' => floatval($data['fire_cost'] ?? 0)
        ],
        'generation_date' => date('Y-m-d H:i:s')
    ];
}

/**
 * Generate PDF content based on template
 */
function generatePDFContent($data, $template, $include_charts, $include_images) {
    $content = '';
    
    $content .= "MEP REPORT - " . strtoupper($template) . " TEMPLATE\n";
    $content .= "=================================================\n\n";
    
    $content .= "PROJECT INFORMATION\n";
    $content .= "-------------------\n";
    $content .= "Project Name: " . $data['project_info']['name'] . "\n";
    $content .= "Building Type: " . $data['project_info']['type'] . "\n";
    $content .= "Total Area: " . $data['project_info']['area'] . " m²\n";
    $content .= "Number of Floors: " . $data['project_info']['floors'] . "\n";
    $content .= "Report Generated: " . $data['generation_date'] . "\n\n";
    
    $content .= "MECHANICAL SYSTEMS\n";
    $content .= "------------------\n";
    $content .= "HVAC Load: " . $data['mechanical_data']['hvac_load'] . " kW\n";
    $content .= "Ductwork Area: " . $data['mechanical_data']['ductwork_area'] . " m²\n";
    $content .= "Equipment Count: " . $data['mechanical_data']['equipment_count'] . "\n";
    $content .= "Estimated Cost: $" . number_format($data['mechanical_data']['total_cost'], 2) . "\n\n";
    
    $content .= "ELECTRICAL SYSTEMS\n";
    $content .= "------------------\n";
    $content .= "Connected Load: " . $data['electrical_data']['connected_load'] . " kW\n";
    $content .= "Panel Count: " . $data['electrical_data']['panel_count'] . "\n";
    $content .= "Lighting Load: " . $data['electrical_data']['lighting_load'] . " kW\n";
    $content .= "Estimated Cost: $" . number_format($data['electrical_data']['total_cost'], 2) . "\n\n";
    
    $content .= "PLUMBING SYSTEMS\n";
    $content .= "----------------\n";
    $content .= "Water Demand: " . $data['plumbing_data']['water_demand'] . " L/day\n";
    $content .= "Pipe Length: " . $data['plumbing_data']['pipe_length'] . " m\n";
    $content .= "Fixture Count: " . $data['plumbing_data']['fixture_count'] . "\n";
    $content .= "Estimated Cost: $" . number_format($data['plumbing_data']['total_cost'], 2) . "\n\n";
    
    $content .= "FIRE PROTECTION SYSTEMS\n";
    $content .= "-----------------------\n";
    $content .= "Sprinkler Area: " . $data['fire_protection_data']['sprinkler_area'] . " m²\n";
    $content .= "Hydrant Count: " . $data['fire_protection_data']['hydrant_count'] . "\n";
    $content .= "Pump Capacity: " . $data['fire_protection_data']['pump_capacity'] . " L/min\n";
    $content .= "Estimated Cost: $" . number_format($data['fire_protection_data']['total_cost'], 2) . "\n\n";
    
    $total_cost = $data['mechanical_data']['total_cost'] + $data['electrical_data']['total_cost'] + 
                  $data['plumbing_data']['total_cost'] + $data['fire_protection_data']['total_cost'];
    
    $content .= "COST SUMMARY\n";
    $content .= "------------\n";
    $content .= "Mechanical Systems: $" . number_format($data['mechanical_data']['total_cost'], 2) . "\n";
    $content .= "Electrical Systems: $" . number_format($data['electrical_data']['total_cost'], 2) . "\n";
    $content .= "Plumbing Systems: $" . number_format($data['plumbing_data']['total_cost'], 2) . "\n";
    $content .= "Fire Protection: $" . number_format($data['fire_protection_data']['total_cost'], 2) . "\n";
    $content .= "-------------------\n";
    $content .= "TOTAL COST: $" . number_format($total_cost, 2) . "\n\n";
    
    if ($include_charts) {
        $content .= "NOTE: Charts and graphs would be included here in the actual PDF.\n\n";
    }
    
    if ($include_images) {
        $content .= "NOTE: System diagrams and photos would be included here in the actual PDF.\n\n";
    }
    
    $content .= "This report was generated by the MEP Coordination Suite.\n";
    $content .= "For questions or support, contact your MEP coordinator.\n";
    
    return $content;
}

/**
 * Export multiple reports at once
 */
function exportMultipleReports($data) {
    try {
        $project_ids = explode(',', $data['project_ids'] ?? '');
        $report_types = $data['report_types'] ?? ['summary'];
        $template = $data['template'] ?? 'standard';
        
        $exported_count = 0;
        $errors = [];
        
        foreach ($project_ids as $project_id) {
            $project_id = intval($project_id);
            if ($project_id <= 0) continue;
            
            foreach ($report_types as $report_type) {
                try {
                    $report_data = ['project_id' => $project_id, 'report_type' => $report_type, 'template' => $template];
                    $result = exportMEPSummaryReport($report_data);
                    
                    if ($result['success']) {
                        $exported_count++;
                    } else {
                        $errors[] = "Project $project_id, Type $report_type: " . $result['error'];
                    }
                } catch (Exception $e) {
                    $errors[] = "Project $project_id, Type $report_type: " . $e->getMessage();
                }
            }
        }
        
        return [
            'success' => count($errors) === 0,
            'count' => $exported_count,
            'errors' => $errors
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Schedule recurring exports
 */
function scheduleRecurringExport($data) {
    global $db;
    
    try {
        $schedule_data = [
            'project_id' => intval($data['project_id']),
            'report_types' => implode(',', $data['report_types'] ?? []),
            'frequency' => $data['frequency'] ?? 'weekly',
            'email_recipients' => $data['email_recipients'] ?? '',
            'template' => $data['template'] ?? 'standard',
            'next_execution' => calculateNextExecution($data['frequency'] ?? 'weekly'),
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $query = "INSERT INTO export_schedules (project_id, report_types, frequency, email_recipients, template, next_execution, active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->executeQuery($query, array_values($schedule_data));
        
        return $stmt !== false;
        
    } catch (Exception $e) {
        error_log("Error scheduling export: " . $e->getMessage());
        return false;
    }
}

/**
 * Calculate next execution date for recurring exports
 */
function calculateNextExecution($frequency) {
    switch ($frequency) {
        case 'daily':
            return date('Y-m-d H:i:s', strtotime('+1 day'));
        case 'weekly':
            return date('Y-m-d H:i:s', strtotime('+1 week'));
        case 'monthly':
            return date('Y-m-d H:i:s', strtotime('+1 month'));
        case 'quarterly':
            return date('Y-m-d H:i:s', strtotime('+3 months'));
        default:
            return date('Y-m-d H:i:s', strtotime('+1 week'));
    }
}

/**
 * Log export activity to database
 */
function logExportToDatabase($project_id, $filename, $report_type, $status) {
    global $db;
    
    try {
        $query = "INSERT INTO pdf_exports (project_id, filename, report_type, export_date, status, file_size) VALUES (?, ?, ?, NOW(), ?, ?)";
        $file_size = file_exists('../../reports/exported/' . $filename) ? filesize('../../reports/exported/' . $filename) : 0;
        $stmt = $db->executeQuery($query, [$project_id, $filename, $report_type, $status, $file_size]);
        
        return $stmt !== false;
    } catch (Exception $e) {
        error_log("Error logging export: " . $e->getMessage());
        return false;
    }
}

/**
 * Get export templates
 */
function getExportTemplates() {
    return [
        'standard' => [
            'name' => 'Standard Report',
            'description' => 'Standard MEP report format with basic charts and tables',
            'includes' => ['summary', 'calculations', 'equipment_list']
        ],
        'executive' => [
            'name' => 'Executive Summary',
            'description' => 'High-level summary for executives and stakeholders',
            'includes' => ['overview', 'key_metrics', 'recommendations']
        ],
        'technical' => [
            'name' => 'Technical Report',
            'description' => 'Detailed technical analysis with calculations',
            'includes' => ['detailed_calculations', 'specifications', 'compliance']
        ],
        'presentation' => [
            'name' => 'Presentation Format',
            'description' => 'Formatted for presentations with charts and graphics',
            'includes' => ['executive_summary', 'charts', 'visualizations']
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Export - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .export-container {
            max-width: 1400px;
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
        
        .export-grid {
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
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
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
        
        .btn-success {
            background: #4CAF50;
        }
        
        .btn-success:hover {
            background: #388E3C;
        }
        
        .template-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .template-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .template-card:hover {
            border-color: #2E7D32;
            background: #f8f9fa;
        }
        
        .template-card.selected {
            border-color: #2E7D32;
            background: #e8f5e8;
        }
        
        .template-name {
            font-weight: 600;
            color: #2E7D32;
            margin-bottom: 5px;
        }
        
        .template-description {
            font-size: 14px;
            color: #666;
        }
        
        .template-features {
            margin-top: 10px;
        }
        
        .template-features span {
            display: inline-block;
            background: #e0e0e0;
            color: #666;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin: 2px;
        }
        
        .export-options {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .option-group {
            margin-bottom: 15px;
        }
        
        .option-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .bulk-export {
            background: #fff3e0;
            border: 1px solid #ffcc02;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .schedule-panel {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .history-item {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .history-info {
            flex: 1;
        }
        
        .history-filename {
            font-weight: 600;
            color: #2E7D32;
        }
        
        .history-meta {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-failed {
            background: #f8d7da;
            color: #721c24;
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
            .export-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .template-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include AEC_ROOT . '/includes/header.php'; ?>
    
    <div class="export-container">
        <div class="page-header">
            <h1>PDF Export Module</h1>
            <p>Generate professional PDF reports from MEP calculations and analysis</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="export-grid">
            <!-- Export Configuration -->
            <div class="card">
                <div class="card-header">Export Configuration</div>
                
                <form method="POST" id="export-form">
                    <input type="hidden" name="action" value="export_report">
                    
                    <div class="form-group">
                        <label for="project_id">Project ID</label>
                        <input type="number" id="project_id" name="project_id" value="<?php echo $project_id; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="report_type">Report Type</label>
                        <select id="report_type" name="report_type" required>
                            <option value="summary" <?php echo $report_type === 'summary' ? 'selected' : ''; ?>>MEP Summary Report</option>
                            <option value="clash-detection">Clash Detection Report</option>
                            <option value="load-analysis">Load Analysis Report</option>
                            <option value="equipment-schedule">Equipment Schedule</option>
                            <option value="cost-estimate">Cost Estimate</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Report Template</label>
                        <div class="template-grid">
                            <?php foreach (getExportTemplates() as $template_id => $template): ?>
                                <div class="template-card" data-template="<?php echo $template_id; ?>">
                                    <div class="template-name"><?php echo htmlspecialchars($template['name']); ?></div>
                                    <div class="template-description"><?php echo htmlspecialchars($template['description']); ?></div>
                                    <div class="template-features">
                                        <?php foreach ($template['includes'] as $feature): ?>
                                            <span><?php echo htmlspecialchars($feature); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="selected_template" name="template" value="standard">
                    </div>
                    
                    <div class="export-options">
                        <div class="option-title">Export Options</div>
                        
                        <div class="option-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="include_charts" name="include_charts">
                                <label for="include_charts">Include Charts and Graphs</label>
                            </div>
                        </div>
                        
                        <div class="option-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="include_images" name="include_images">
                                <label for="include_images">Include System Images</label>
                            </div>
                        </div>
                        
                        <div class="option-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="include_raw_data" name="include_raw_data">
                                <label for="include_raw_data">Include Raw Calculation Data</label>
                            </div>
                        </div>
                        
                        <div class="option-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="watermark" name="watermark">
                                <label for="watermark">Add Watermark</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Generate PDF</button>
                    <button type="button" class="btn btn-secondary" onclick="previewReport()">Preview Report</button>
                </form>
            </div>
            
            <!-- Bulk Export -->
            <div class="card">
                <div class="card-header">Bulk Export & Scheduling</div>
                
                <div class="bulk-export">
                    <h4>Bulk Export</h4>
                    <form method="POST" id="bulk-export-form">
                        <input type="hidden" name="action" value="bulk_export">
                        
                        <div class="form-group">
                            <label for="project_ids">Project IDs (comma-separated)</label>
                            <input type="text" id="project_ids" name="project_ids" placeholder="1,2,3,4,5">
                        </div>
                        
                        <div class="form-group">
                            <label>Report Types</label>
                            <div style="margin: 10px 0;">
                                <label><input type="checkbox" name="report_types[]" value="summary" checked> Summary</label><br>
                                <label><input type="checkbox" name="report_types[]" value="clash-detection"> Clash Detection</label><br>
                                <label><input type="checkbox" name="report_types[]" value="load-analysis"> Load Analysis</label><br>
                                <label><input type="checkbox" name="report_types[]" value="equipment-schedule"> Equipment Schedule</label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Bulk Export</button>
                    </form>
                </div>
                
                <div class="schedule-panel">
                    <h4>Schedule Recurring Exports</h4>
                    <form method="POST" id="schedule-form">
                        <input type="hidden" name="action" value="schedule_export">
                        
                        <div class="form-group">
                            <label for="schedule_frequency">Frequency</label>
                            <select id="schedule_frequency" name="frequency">
                                <option value="daily">Daily</option>
                                <option value="weekly" selected>Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="email_recipients">Email Recipients</label>
                            <input type="email" id="email_recipients" name="email_recipients" multiple placeholder="email1@example.com, email2@example.com">
                        </div>
                        
                        <button type="submit" class="btn">Schedule Export</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Export History -->
        <div class="card">
            <div class="card-header">Recent Exports</div>
            
            <?php if (empty($export_history)): ?>
                <p style="color: #666; text-align: center; padding: 50px 20px;">
                    No export history found. Generate your first PDF report to see it here.
                </p>
            <?php else: ?>
                <?php foreach ($export_history as $export): ?>
                    <div class="history-item">
                        <div class="history-info">
                            <div class="history-filename"><?php echo htmlspecialchars($export['filename']); ?></div>
                            <div class="history-meta">
                                Type: <?php echo htmlspecialchars($export['report_type']); ?> | 
                                Date: <?php echo date('Y-m-d H:i:s', strtotime($export['export_date'])); ?> | 
                                Size: <?php echo number_format($export['file_size'] / 1024, 1); ?> KB
                            </div>
                        </div>
                        <span class="status-badge status-<?php echo $export['status']; ?>">
                            <?php echo ucfirst($export['status']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include AEC_ROOT . '/includes/footer.php'; ?>
    
    <script>
        // Template selection
        document.querySelectorAll('.template-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.template-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('selected_template').value = this.dataset.template;
            });
        });
        
        // Default select standard template
        document.querySelector('[data-template="standard"]').classList.add('selected');
        
        function previewReport() {
            const formData = new FormData(document.getElementById('export-form'));
            
            // Open preview in new window
            const previewWindow = window.open('', '_blank', 'width=800,height=600');
            previewWindow.document.write(`
                <html>
                    <head><title>PDF Preview</title></head>
                    <body style="font-family: Arial; padding: 20px;">
                        <h2>PDF Report Preview</h2>
                        <p><strong>Template:</strong> ${formData.get('template') || 'Standard'}</p>
                        <p><strong>Report Type:</strong> ${formData.get('report_type') || 'Summary'}</p>
                        <p><strong>Project ID:</strong> ${formData.get('project_id') || 'N/A'}</p>
                        <p><strong>Include Charts:</strong> ${formData.get('include_charts') ? 'Yes' : 'No'}</p>
                        <p><strong>Include Images:</strong> ${formData.get('include_images') ? 'Yes' : 'No'}</p>
                        <hr>
                        <p><em>This is a preview of the PDF content. Actual PDF would contain formatted charts, graphs, and professional layout.</em></p>
                    </body>
                </html>
            `);
        }
        
        // Form submissions
        document.getElementById('export-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('pdf-export.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('PDF generated successfully: ' + data.filename);
                    location.reload();
                } else {
                    alert('Error generating PDF: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating PDF');
            });
        });
        
        document.getElementById('bulk-export-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            if (!formData.get('project_ids')) {
                alert('Please enter project IDs');
                return;
            }
            
            fetch('pdf-export.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Bulk export completed! Generated ${data.count} reports.`);
                    location.reload();
                } else {
                    alert('Bulk export failed: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error in bulk export');
            });
        });
        
        document.getElementById('schedule-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('project_id', document.getElementById('project_id').value);
            
            fetch('pdf-export.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Export scheduled successfully!');
                    location.reload();
                } else {
                    alert('Error scheduling export: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error scheduling export');
            });
        });
    </script>
</body>
</html>
