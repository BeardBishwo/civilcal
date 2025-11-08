<?php
/**
 * MEP Clash Detection Report Generator
 * Advanced clash detection analysis for MEP systems coordination
 * Identifies conflicts between mechanical, electrical, and plumbing systems
 */

require_once '../../../includes/config.php';
require_once '../../../includes/Database.php';
require_once '../../../includes/functions.php';

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
            case 'analyze_clashes':
                $result = performClashAnalysis($_POST);
                $message = 'Clash analysis completed successfully!';
                $message_type = 'success';
                $clash_data = $result;
                break;
                
            case 'generate_report':
                $result = generateClashReport($_POST);
                $message = 'Clash detection report generated!';
                $message_type = 'success';
                $report_data = $result;
                break;
                
            case 'export_clashes':
                $result = exportClashData($_POST);
                if ($result) {
                    $message = 'Clash data exported successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error exporting clash data.';
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get saved clash analyses
$saved_analyses = array();
if ($project_id > 0) {
    $query = "SELECT * FROM mep_clash_analysis WHERE project_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $db->executeQuery($query, array($project_id));
    $saved_analyses = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : array();
}

/**
 * Perform comprehensive clash detection analysis
 */
function performClashAnalysis($data) {
    $building_area = floatval($data['building_area'] ?? 0);
    $floors = intval($data['floors'] ?? 1);
    $complexity_level = $data['complexity_level'] ?? 'medium';
    
    // Simulate MEP system layouts
    $mechanical_layout = generateMechanicalLayout($building_area, $floors);
    $electrical_layout = generateElectricalLayout($building_area, $floors);
    $plumbing_layout = generatePlumbingLayout($building_area, $floors);
    $fire_protection_layout = generateFireProtectionLayout($building_area, $floors);
    
    // Perform clash detection
    $hard_clashes = detectHardClashes($mechanical_layout, $electrical_layout, $plumbing_layout, $fire_protection_layout);
    $soft_clashes = detectSoftClashes($mechanical_layout, $electrical_layout, $plumbing_layout, $fire_protection_layout);
    $clashes4d = detect4DClashes($mechanical_layout, $electrical_layout, $plumbing_layout, $fire_protection_layout);
    
    // Calculate clash statistics
    $total_clashes = count($hard_clashes) + count($soft_clashes) + count($clashes4d);
    $clash_density = $building_area > 0 ? $total_clashes / $building_area : 0;
    
    // Generate resolution recommendations
    $resolution_recommendations = generateResolutionRecommendations($hard_clashes, $soft_clashes, $clashes4d);
    
    // Calculate coordination score
    $coordination_score = calculateCoordinationScore($total_clashes, $building_area, $complexity_level);
    
    // Calculate severity counts using traditional foreach loops
    $critical_clashes = 0;
    $major_clashes = 0;
    $minor_clashes = 0;
    
    foreach ($hard_clashes as $clash) {
        if ($clash['severity'] === 'critical') {
            $critical_clashes++;
        } elseif ($clash['severity'] === 'major') {
            $major_clashes++;
        } elseif ($clash['severity'] === 'minor') {
            $minor_clashes++;
        }
    }
    
    return array(
        'analysis_info' => array(
            'building_area' => $building_area,
            'floors' => $floors,
            'complexity_level' => $complexity_level,
            'analysis_date' => date('Y-m-d H:i:s'),
            'total_systems' => 4
        ),
        'system_layouts' => array(
            'mechanical' => $mechanical_layout,
            'electrical' => $electrical_layout,
            'plumbing' => $plumbing_layout,
            'fire_protection' => $fire_protection_layout
        ),
        'clash_results' => array(
                'hard_clashes' => $hard_clashes,
                'soft_clashes' => $soft_clashes,
                '4d_clashes' => $clashes4d,
                'total_clashes' => $total_clashes,
                'clash_density' => $clash_density
            ),
        'statistics' => array(
            'coordination_score' => $coordination_score,
            'critical_clashes' => $critical_clashes,
            'major_clashes' => $major_clashes,
            'minor_clashes' => $minor_clashes,
            'resolution_efficiency' => calculateResolutionEfficiency($resolution_recommendations)
        ),
        'recommendations' => $resolution_recommendations
    );
}

/**
 * Generate mechanical system layout
 */
function generateMechanicalLayout($area, $floors) {
    $zones = ceil($area / 500); // Assume 500m² per mechanical zone
    
    $layout = array(
        'ducts' => array(),
        'equipment' => array(),
        'clearances' => array()
    );
    
    // Generate ductwork
    for ($i = 0; $i < $zones * 2; $i++) {
        $layout['ducts'][] = array(
            'id' => 'MD-' . ($i + 1),
            'type' => 'main_duct',
            'size' => '800x400',
            'elevation' => rand(2800, 3200), // mm
            'x_position' => rand(1000, $area * 0.8),
            'y_position' => rand(1000, $area * 0.8),
            'length' => rand(5000, 15000)
        );
    }
    
    // Generate equipment
    for ($i = 0; $i < $floors; $i++) {
        $layout['equipment'][] = array(
            'id' => 'ME-' . ($i + 1),
            'type' => 'ahu',
            'size' => '2000x1500x800',
            'elevation' => 0,
            'floor' => $i + 1,
            'clearance_required' => 1000
        );
    }
    
    return $layout;
}

/**
 * Generate electrical system layout
 */
function generateElectricalLayout($area, $floors) {
    $panels = ceil($area / 300); // Assume 300m² per electrical panel
    
    $layout = array(
        'cables' => array(),
        'panels' => array(),
        'conduits' => array()
    );
    
    // Generate cable trays
    for ($i = 0; $i < $panels * 3; $i++) {
        $layout['cables'][] = array(
            'id' => 'EC-' . ($i + 1),
            'type' => 'power_cable',
            'size' => '400x100',
            'elevation' => rand(2600, 2900), // mm
            'x_position' => rand(500, $area * 0.9),
            'y_position' => rand(500, $area * 0.9),
            'length' => rand(3000, 12000)
        );
    }
    
    // Generate panels
    for ($i = 0; $i < $floors; $i++) {
        $layout['panels'][] = array(
            'id' => 'EP-' . ($i + 1),
            'type' => 'distribution_panel',
            'size' => '600x400x200',
            'elevation' => 1500,
            'floor' => $i + 1,
            'clearance_required' => 800
        );
    }
    
    return $layout;
}

/**
 * Generate plumbing system layout
 */
function generatePlumbingLayout($area, $floors) {
    $risers = ceil($floors / 2);
    
    $layout = array(
        'pipes' => array(),
        'fixtures' => array(),
        'equipment' => array()
    );
    
    // Generate pipe risers
    for ($i = 0; $i < $risers; $i++) {
        $layout['pipes'][] = array(
            'id' => 'PR-' . ($i + 1),
            'type' => 'water_riser',
            'size' => 'DN100',
            'elevation' => rand(100, 300), // mm above floor
            'x_position' => rand(200, $area * 0.95),
            'y_position' => rand(200, $area * 0.95),
            'height' => $floors * 3000
        );
    }
    
    // Generate fixtures
    for ($i = 0; $i < $floors * 10; $i++) {
        $layout['fixtures'][] = array(
            'id' => 'PF-' . ($i + 1),
            'type' => 'sink',
            'size' => '600x400',
            'elevation' => 850,
            'floor' => rand(1, $floors),
            'clearance_required' => 600
        );
    }
    
    return $layout;
}

/**
 * Generate fire protection system layout
 */
function generateFireProtectionLayout($area, $floors) {
    $sprinklers = ceil($area / 20); // Assume one sprinkler per 20m²
    
    $layout = array(
        'sprinklers' => array(),
        'pipes' => array(),
        'equipment' => array()
    );
    
    // Generate sprinklers
    for ($i = 0; $i < $sprinklers; $i++) {
        $layout['sprinklers'][] = array(
            'id' => 'FS-' . ($i + 1),
            'type' => 'sprinkler_head',
            'size' => 'DN25',
            'elevation' => rand(2700, 2900), // mm
            'x_position' => rand(100, $area * 0.98),
            'y_position' => rand(100, $area * 0.98),
            'coverage_radius' => 4000 // mm
        );
    }
    
    // Generate fire protection pipes
    for ($i = 0; $i < ceil($sprinklers / 10); $i++) {
        $layout['pipes'][] = array(
            'id' => 'FP-' . ($i + 1),
            'type' => 'fire_main',
            'size' => 'DN150',
            'elevation' => rand(200, 400),
            'x_position' => rand(300, $area * 0.97),
            'y_position' => rand(300, $area * 0.97),
            'length' => rand(5000, 20000)
        );
    }
    
    return $layout;
}

/**
 * Detect hard clashes (physical conflicts)
 */
function detectHardClashes($mechanical, $electrical, $plumbing, $fire_protection) {
    $clashes = array();
    $clash_id = 1;
    
    // Check mechanical vs electrical clashes
    foreach ($mechanical['ducts'] as $duct) {
        foreach ($electrical['cables'] as $cable) {
            if (abs($duct['elevation'] - $cable['elevation']) < 100 && 
                abs($duct['x_position'] - $cable['x_position']) < 500 &&
                abs($duct['y_position'] - $cable['y_position']) < 500) {
                
                $severity = abs($duct['elevation'] - $cable['elevation']) < 50 ? 'critical' : 'major';
                
                $clashes[] = array(
                    'id' => 'HC-' . $clash_id++,
                    'type' => 'hard_clash',
                    'systems' => array('Mechanical', 'Electrical'),
                    'location' => array(
                        'x' => ($duct['x_position'] + $cable['x_position']) / 2,
                        'y' => ($duct['y_position'] + $cable['y_position']) / 2,
                        'elevation' => ($duct['elevation'] + $cable['elevation']) / 2
                    ),
                    'severity' => $severity,
                    'description' => "Duct {$duct['id']} conflicts with cable {$cable['id']}",
                    'resolution_cost' => $severity === 'critical' ? 2500 : 1200,
                    'resolution_time' => $severity === 'critical' ? 8 : 4
                );
            }
        }
    }
    
    // Check plumbing vs electrical clashes
    foreach ($plumbing['pipes'] as $pipe) {
        foreach ($electrical['cables'] as $cable) {
            if (abs($pipe['elevation'] - $cable['elevation']) < 150 &&
                abs($pipe['x_position'] - $cable['x_position']) < 300 &&
                abs($pipe['y_position'] - $cable['y_position']) < 300) {
                
                $clashes[] = array(
                    'id' => 'HC-' . $clash_id++,
                    'type' => 'hard_clash',
                    'systems' => array('Plumbing', 'Electrical'),
                    'location' => array(
                        'x' => ($pipe['x_position'] + $cable['x_position']) / 2,
                        'y' => ($pipe['y_position'] + $cable['y_position']) / 2,
                        'elevation' => ($pipe['elevation'] + $cable['elevation']) / 2
                    ),
                    'severity' => 'major',
                    'description' => "Pipe {$pipe['id']} conflicts with cable {$cable['id']}",
                    'resolution_cost' => 1800,
                    'resolution_time' => 6
                );
            }
        }
    }
    
    return $clashes;
}

/**
 * Detect soft clashes (clearance violations)
 */
function detectSoftClashes($mechanical, $electrical, $plumbing, $fire_protection) {
    $clashes = array();
    $clash_id = 1;
    
    // Check clearance violations
    foreach ($mechanical['equipment'] as $equipment) {
        foreach ($electrical['panels'] as $panel) {
            if ($equipment['floor'] === $panel['floor']) {
                $required_clearance = $equipment['clearance_required'] + $panel['clearance_required'];
                $actual_clearance = 1000; // Simulated actual clearance
                
                if ($actual_clearance < $required_clearance) {
                    $severity = $actual_clearance < ($required_clearance * 0.5) ? 'major' : 'minor';
                    
                    $clashes[] = array(
                        'id' => 'SC-' . $clash_id++,
                        'type' => 'soft_clash',
                        'systems' => array('Mechanical', 'Electrical'),
                        'location' => array(
                            'floor' => $equipment['floor'],
                            'equipment_id' => $equipment['id'],
                            'panel_id' => $panel['id']
                        ),
                        'severity' => $severity,
                        'description' => "Insufficient clearance between {$equipment['id']} and {$panel['id']}",
                        'required_clearance' => $required_clearance,
                        'actual_clearance' => $actual_clearance,
                        'resolution_cost' => $severity === 'major' ? 800 : 300,
                        'resolution_time' => $severity === 'major' ? 3 : 1
                    );
                }
            }
        }
    }
    
    return $clashes;
}

/**
 * Detect 4D clashes (time/sequence conflicts)
 */
function detect4DClashes($mechanical, $electrical, $plumbing, $fire_protection) {
    $clashes = array();
    $clash_id = 1;
    
    // Simulate construction sequence conflicts
    $construction_phases = array('foundation', 'structure', 'mep_installation', 'finishing');
    
    foreach ($construction_phases as $phase) {
        // Check if systems are being installed in wrong sequence
        if ($phase === 'mep_installation') {
            // Simulate parallel installation conflicts
            if (count($mechanical['equipment']) > 0 && count($electrical['panels']) > 0) {
                $clashes[] = array(
                    'id' => '4D-' . $clash_id++,
                    'type' => '4d_clash',
                    'systems' => array('Mechanical', 'Electrical'),
                    'phase' => $phase,
                    'severity' => 'minor',
                    'description' => 'Parallel installation may cause access conflicts',
                    'recommended_sequence' => 'Install electrical conduits first, then mechanical ducts',
                    'resolution_cost' => 500,
                    'resolution_time' => 2
                );
            }
        }
    }
    
    return $clashes;
}

/**
 * Calculate coordination score
 */
function calculateCoordinationScore($total_clashes, $area, $complexity_level) {
    $base_score = 100;
    
    // Deduct points for clashes
    $clash_penalty = min($total_clashes * 2, 40);
    
    // Complexity adjustment
    $complexity_multiplier = array(
        'low' => 1.0,
        'medium' => 0.9,
        'high' => 0.8,
        'very_high' => 0.7
    );
    
    $complexity_factor = isset($complexity_multiplier[$complexity_level]) ? $complexity_multiplier[$complexity_level] : 0.9;
    
    $score = ($base_score - $clash_penalty) * $complexity_factor;
    
    return max(0, min(100, round($score, 1)));
}

/**
 * Calculate resolution efficiency
 */
function calculateResolutionEfficiency($recommendations) {
    if (empty($recommendations)) return 100;
    
    $total_cost = array_sum(array_column($recommendations, 'resolution_cost'));
    $total_time = array_sum(array_column($recommendations, 'resolution_time'));
    
    // Efficiency score based on cost and time
    $cost_efficiency = max(0, 100 - ($total_cost / 100));
    $time_efficiency = max(0, 100 - ($total_time / 10));
    
    return round(($cost_efficiency + $time_efficiency) / 2, 1);
}

/**
 * Generate resolution recommendations
 */
function generateResolutionRecommendations($hard_clashes, $soft_clashes, $clashes4d) {
    $recommendations = array();
    
    // Process hard clashes
    foreach ($hard_clashes as $clash) {
        if ($clash['severity'] === 'critical') {
            $recommendations[] = array(
                'clash_id' => $clash['id'],
                'priority' => 'immediate',
                'recommendation' => 'Redesign system routing to eliminate physical conflict',
                'resolution_cost' => $clash['resolution_cost'],
                'resolution_time' => $clash['resolution_time'],
                'implementation_steps' => array(
                    'Analyze current routing options',
                    'Coordinate with design team',
                    'Update drawings and specifications',
                    'Obtain approval for changes'
                )
            );
        } elseif ($clash['severity'] === 'major') {
            $recommendations[] = array(
                'clash_id' => $clash['id'],
                'priority' => 'high',
                'recommendation' => 'Adjust elevations or routing to maintain clearance',
                'resolution_cost' => $clash['resolution_cost'],
                'resolution_time' => $clash['resolution_time'],
                'implementation_steps' => array(
                    'Review installation drawings',
                    'Coordinate with other trades',
                    'Adjust installation sequence if needed'
                )
            );
        }
    }
    
    // Process soft clashes
    foreach ($soft_clashes as $clash) {
        if ($clash['severity'] === 'major') {
            $recommendations[] = array(
                'clash_id' => $clash['id'],
                'priority' => 'medium',
                'recommendation' => 'Relocate equipment or modify clearance requirements',
                'resolution_cost' => $clash['resolution_cost'],
                'resolution_time' => $clash['resolution_time'],
                'implementation_steps' => array(
                    'Evaluate relocation options',
                    'Check code compliance',
                    'Update floor plans'
                )
            );
        }
    }
    
    // Process 4D clashes
    foreach ($clashes4d as $clash) {
        $recommendations[] = array(
            'clash_id' => $clash['id'],
            'priority' => 'low',
            'recommendation' => $clash['description'],
            'resolution_cost' => $clash['resolution_cost'],
            'resolution_time' => $clash['resolution_time'],
            'implementation_steps' => array(
                'Review construction schedule',
                'Coordinate installation sequence',
                'Update work sequence documentation'
            )
        );
    }
    
    return $recommendations;
}

/**
 * Generate clash detection report
 */
function generateClashReport($data) {
    $clash_analysis = performClashAnalysis($data);
    
    return array(
        'report_header' => array(
            'title' => 'MEP Clash Detection Analysis Report',
            'project_name' => $data['project_name'] ?? 'MEP Project',
            'analysis_date' => date('Y-m-d H:i:s'),
            'analyst' => 'MEP Coordination Suite'
        ),
        'executive_summary' => array(
            'total_clashes' => $clash_analysis['clash_results']['total_clashes'],
            'coordination_score' => $clash_analysis['statistics']['coordination_score'],
            'critical_issues' => $clash_analysis['statistics']['critical_clashes'],
            'recommended_actions' => count($clash_analysis['recommendations'])
        ),
        'detailed_analysis' => $clash_analysis,
        'next_steps' => array(
            'Review critical clashes immediately',
            'Coordinate resolution with design team',
            'Update installation drawings',
            'Schedule follow-up clash detection'
        )
    );
}

/**
 * Export clash data
 */
function exportClashData($data) {
    $clash_analysis = performClashAnalysis($data);
    
    // Create export directory if it doesn't exist
    if (!is_dir('../../exports')) {
        mkdir('../../exports', 0755, true);
    }
    
    $filename = 'clash_analysis_' . date('Y-m-d_H-i-s') . '.json';
    $filepath = '../../exports/' . $filename;
    
    file_put_contents($filepath, json_encode($clash_analysis, JSON_PRETTY_PRINT));
    
    return true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP Clash Detection Report - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .clash-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #D32F2F, #F44336);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .analysis-grid {
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
            border-bottom: 2px solid #D32F2F;
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
            background: #D32F2F;
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
            background: #B71C1C;
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
        
        .clash-overview {
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
            border-left: 4px solid #D32F2F;
        }
        
        .overview-value {
            font-size: 24px;
            font-weight: 600;
            color: #D32F2F;
        }
        
        .overview-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .clash-item {
            background: #ffebee;
            border: 1px solid #ffcdd2;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #f44336;
        }
        
        .clash-severity {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .severity-critical {
            background: #ffebee;
            color: #c62828;
        }
        
        .severity-major {
            background: #fff3e0;
            color: #ef6c00;
        }
        
        .severity-minor {
            background: #e8f5e8;
            color: #2e7d32;
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
        
        .priority-immediate {
            background: #ffebee;
            color: #c62828;
        }
        
        .priority-high {
            background: #fff3e0;
            color: #ef6c00;
        }
        
        .priority-medium {
            background: #fff9c4;
            color: #f57f17;
        }
        
        .priority-low {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .coordination-score {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        
        .score-value {
            font-size: 48px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .score-label {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .system-layout {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .layout-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .stat-item {
            text-align: center;
            padding: 8px;
            background: white;
            border-radius: 4px;
        }
        
        .stat-value {
            font-weight: 600;
            color: #D32F2F;
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
            .analysis-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../../../includes/header.php'; ?>
    
    <div class="clash-container">
        <div class="page-header">
            <h1>MEP Clash Detection Analysis</h1>
            <p>Advanced clash detection for MEP system coordination</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="analysis-grid">
            <!-- Input Form -->
            <div class="card">
                <div class="card-header">Building Parameters</div>
                
                <form method="POST" id="clash-form">
                    <input type="hidden" name="action" value="analyze_clashes">
                    
                    <div class="form-group">
                        <label for="project_name">Project Name</label>
                        <input type="text" id="project_name" name="project_name" 
                               value="<?php echo htmlspecialchars($saved_analyses[0]['project_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="building_area">Building Area (m²)</label>
                            <input type="number" id="building_area" name="building_area" 
                                   value="<?php echo htmlspecialchars($saved_analyses[0]['building_area'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="floors">Number of Floors</label>
                            <input type="number" id="floors" name="floors" 
                                   value="<?php echo htmlspecialchars($saved_analyses[0]['floors'] ?? '1'); ?>" min="1" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="complexity_level">Project Complexity</label>
                        <select id="complexity_level" name="complexity_level" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="very_high">Very High</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Analyze Clashes</button>
                    <button type="button" class="btn btn-secondary" onclick="exportClashes()">Export Data</button>
                </form>
            </div>
            
            <!-- Clash Overview -->
            <div class="card">
                <div class="card-header">Clash Analysis Overview</div>
                <div id="clash-overview">
                    <p style="color: #666; text-align: center; padding: 50px 20px;">
                        Enter building parameters and click "Analyze Clashes" to start the analysis.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Results Section -->
        <div id="results-section" class="results-section">
            <div class="card">
                <div class="card-header">Clash Detection Results</div>
                
                <div id="clash-results"></div>
            </div>
        </div>
    </div>
    
    <?php include '../../../includes/footer.php'; ?>
    
    <script>
        function analyzeClashes() {
            const formData = new FormData(document.getElementById('clash-form'));
            formData.append('action', 'analyze_clashes');
            
            fetch('clash-detection-report.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayClashResults(data.results);
                } else {
                    alert('Error analyzing clashes: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error analyzing clashes');
            });
        }
        
        function exportClashes() {
            const formData = new FormData(document.getElementById('clash-form'));
            formData.append('action', 'export_clashes');
            
            fetch('clash-detection-report.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Clash data exported successfully!');
                } else {
                    alert('Error exporting data: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error exporting data');
            });
        }
        
        function displayClashResults(analysis) {
            document.getElementById('results-section').classList.add('active');
            
            const overviewHtml = `
                <div class="coordination-score">
                    <div class="score-value">${analysis.statistics.coordination_score}</div>
                    <div class="score-label">Coordination Score</div>
                </div>
                
                <div class="clash-overview">
                    <div class="overview-item">
                        <div class="overview-value">${analysis.clash_results.total_clashes}</div>
                        <div class="overview-label">Total Clashes</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${analysis.statistics.critical_clashes}</div>
                        <div class="overview-label">Critical Clashes</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${analysis.statistics.major_clashes}</div>
                        <div class="overview-label">Major Clashes</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${analysis.statistics.resolution_efficiency}%</div>
                        <div class="overview-label">Resolution Efficiency</div>
                    </div>
                </div>
                
                <h3>System Layout Analysis</h3>
                ${Object.entries(analysis.system_layouts).map(([system, layout]) => `
                    <div class="system-layout">
                        <h4>${system.charAt(0).toUpperCase() + system.slice(1)} System</h4>
                        <div class="layout-stats">
                            ${Object.entries(layout).map(([type, items]) => `
                                <div class="stat-item">
                                    <div class="stat-value">${items.length}</div>
                                    <div class="overview-label">${type.replace('_', ' ').toUpperCase()}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `).join('')}
                
                <h3>Hard Clashes</h3>
                ${analysis.clash_results.hard_clashes.length > 0 ? 
                    analysis.clash_results.hard_clashes.map(clash => `
                        <div class="clash-item">
                            <h4>${clash.id} <span class="clash-severity severity-${clash.severity}">${clash.severity.toUpperCase()}</span></h4>
                            <p><strong>Systems:</strong> ${clash.systems.join(' vs ')}</p>
                            <p><strong>Location:</strong> X: ${clash.location.x?.toFixed(0) || 'N/A'}m, Y: ${clash.location.y?.toFixed(0) || 'N/A'}m, Z: ${clash.location.elevation?.toFixed(0) || 'N/A'}mm</p>
                            <p><strong>Description:</strong> ${clash.description}</p>
                            <p><strong>Resolution Cost:</strong> $${clash.resolution_cost.toLocaleString()} | <strong>Time:</strong> ${clash.resolution_time} days</p>
                        </div>
                    `).join('') :
                    '<p style="color: #666; text-align: center;">No hard clashes detected.</p>'
                }
                
                <h3>Soft Clashes</h3>
                ${analysis.clash_results.soft_clashes.length > 0 ?
                    analysis.clash_results.soft_clashes.map(clash => `
                        <div class="clash-item">
                            <h4>${clash.id} <span class="clash-severity severity-${clash.severity}">${clash.severity.toUpperCase()}</span></h4>
                            <p><strong>Systems:</strong> ${clash.systems.join(' vs ')}</p>
                            <p><strong>Floor:</strong> ${clash.location.floor}</p>
                            <p><strong>Description:</strong> ${clash.description}</p>
                            <p><strong>Required Clearance:</strong> ${clash.required_clearance}mm | <strong>Actual:</strong> ${clash.actual_clearance}mm</p>
                            <p><strong>Resolution Cost:</strong> $${clash.resolution_cost.toLocaleString()} | <strong>Time:</strong> ${clash.resolution_time} days</p>
                        </div>
                    `).join('') :
                    '<p style="color: #666; text-align: center;">No soft clashes detected.</p>'
                }
                
                <h3>4D Clashes</h3>
                ${analysis.clash_results['4d_clashes'].length > 0 ?
                    analysis.clash_results['4d_clashes'].map(clash => `
                        <div class="clash-item">
                            <h4>${clash.id} <span class="clash-severity severity-${clash.severity}">${clash.severity.toUpperCase()}</span></h4>
                            <p><strong>Systems:</strong> ${clash.systems.join(' vs ')}</p>
                            <p><strong>Phase:</strong> ${clash.phase}</p>
                            <p><strong>Description:</strong> ${clash.description}</p>
                            <p><strong>Recommended Sequence:</strong> ${clash.recommended_sequence}</p>
                            <p><strong>Resolution Cost:</strong> $${clash.resolution_cost.toLocaleString()} | <strong>Time:</strong> ${clash.resolution_time} days</p>
                        </div>
                    `).join('') :
                    '<p style="color: #666; text-align: center;">No 4D clashes detected.</p>'
                }
                
                <h3>Resolution Recommendations</h3>
                ${analysis.recommendations.map(rec => `
                    <div class="recommendation-item">
                        <h4>Clash ${rec.clash_id} <span class="priority-badge priority-${rec.priority}">${rec.priority.toUpperCase()}</span></h4>
                        <p><strong>Recommendation:</strong> ${rec.recommendation}</p>
                        <p><strong>Cost:</strong> $${rec.resolution_cost.toLocaleString()} | <strong>Time:</strong> ${rec.resolution_time} days</p>
                        <div>
                            <strong>Implementation Steps:</strong>
                            <ul>
                                ${rec.implementation_steps.map(step => `<li>${step}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                `).join('')}
            `;
            
            document.getElementById('clash-results').innerHTML = overviewHtml;
        }
        
        // Form submission handler
        document.getElementById('clash-form').addEventListener('submit', function(e) {
            e.preventDefault();
            analyzeClashes();
        });
    </script>
</body>
</html>
