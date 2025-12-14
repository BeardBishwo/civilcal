<?php
/**
 * MEP Equipment Schedule Generator
 * Comprehensive equipment scheduling and tracking for MEP systems
 * Generates detailed equipment lists with specifications and schedules
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
            case 'generate_schedule':
                $result = generateEquipmentSchedule($_POST);
                $message = 'Equipment schedule generated successfully!';
                $message_type = 'success';
                $schedule_data = $result;
                break;
                
            case 'update_equipment':
                $result = updateEquipmentStatus($_POST);
                $message = 'Equipment status updated successfully!';
                $message_type = 'success';
                break;
                
            case 'export_schedule':
                $result = exportEquipmentSchedule($_POST);
                if ($result) {
                    $message = 'Equipment schedule exported successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error exporting schedule.';
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get saved equipment schedules
$saved_schedules = array();
if ($project_id > 0) {
    $query = "SELECT * FROM mep_equipment_schedule WHERE project_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $db->executeQuery($query, array($project_id));
    $saved_schedules = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : array();
}

/**
 * Generate comprehensive equipment schedule
 */
function generateEquipmentSchedule($data) {
    $building_area = floatval($data['building_area'] ?? 0);
    $floors = intval($data['floors'] ?? 1);
    $occupancy_type = $data['occupancy_type'] ?? 'office';
    $project_name = $data['project_name'] ?? 'MEP Project';
    
    // Generate HVAC equipment schedule
    $hvac_equipment = generateHVACEquipment($building_area, $floors, $occupancy_type);
    
    // Generate electrical equipment schedule
    $electrical_equipment = generateElectricalEquipment($building_area, $floors, $occupancy_type);
    
    // Generate plumbing equipment schedule
    $plumbing_equipment = generatePlumbingEquipment($building_area, $floors, $occupancy_type);
    
    // Generate fire protection equipment schedule
    $fire_protection_equipment = generateFireProtectionEquipment($building_area, $floors, $occupancy_type);
    
    // Calculate installation schedules
    $installation_schedule = calculateInstallationSchedule($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment, $floors);
    
    // Calculate procurement timeline
    $procurement_timeline = calculateProcurementTimeline($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment);
    
    // Calculate total costs
    $total_costs = calculateEquipmentCosts($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment);
    
    // Generate equipment maintenance schedules
    $maintenance_schedule = generateMaintenanceSchedule($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment);
    
    // Calculate equipment specifications
    $specifications = calculateEquipmentSpecifications($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment);
    
    return array(
        'project_info' => array(
            'name' => $project_name,
            'area' => $building_area,
            'floors' => $floors,
            'occupancy_type' => $occupancy_type,
            'generation_date' => date('Y-m-d H:i:s')
        ),
        'equipment_by_system' => array(
            'hvac' => $hvac_equipment,
            'electrical' => $electrical_equipment,
            'plumbing' => $plumbing_equipment,
            'fire_protection' => $fire_protection_equipment
        ),
        'installation_schedule' => $installation_schedule,
        'procurement_timeline' => $procurement_timeline,
        'cost_summary' => $total_costs,
        'maintenance_schedule' => $maintenance_schedule,
        'specifications' => $specifications,
        'compliance_status' => calculateComplianceStatus($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment)
    );
}

/**
 * Generate HVAC equipment schedule
 */
function generateHVACEquipment($area, $floors, $occupancy_type) {
    $equipment = array();
    $equipment_id = 1;
    
    // Calculate equipment quantities based on area and occupancy
    $ahu_count = max(1, ceil($area / 1000));
    $chiller_count = max(1, ceil($area / 2000));
    $boiler_count = max(1, ceil($area / 1500));
    $fan_count = ceil($area / 200);
    
    // Air Handling Units
    for ($i = 0; $i < $ahu_count; $i++) {
        $capacity = ($area / $ahu_count) * 80; // W/m²
        $equipment[] = array(
            'id' => 'HVAC-AHU-' . ($equipment_id++),
            'name' => 'Air Handling Unit AHU-' . ($i + 1),
            'type' => 'Air Handling Unit',
            'quantity' => 1,
            'capacity' => $capacity,
            'unit' => 'kW',
            'efficiency' => rand(75, 90),
            'power_consumption' => $capacity * 1.2,
            'dimensions' => array(
                'length' => rand(2000, 4000),
                'width' => rand(1500, 3000),
                'height' => rand(800, 1500)
            ),
            'weight' => rand(500, 2000),
            'manufacturer' => 'Carrier',
            'model' => '40RM-' . rand(100, 999),
            'specification' => 'Variable air volume with hot water reheat',
            'cost' => $capacity * 1500, // Cost per kW
            'lead_time' => rand(12, 16),
            'warranty' => '2 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    // Chillers
    for ($i = 0; $i < $chiller_count; $i++) {
        $capacity = ($area / $chiller_count) * 60; // kW
        $equipment[] = array(
            'id' => 'HVAC-CHL-' . ($equipment_id++),
            'name' => 'Chiller CHL-' . ($i + 1),
            'type' => 'Chiller',
            'quantity' => 1,
            'capacity' => $capacity,
            'unit' => 'kW',
            'efficiency' => rand(3.0, 4.0), // COP
            'power_consumption' => $capacity / rand(3.0, 4.0),
            'refrigerant' => 'R-410A',
            'manufacturer' => 'Trane',
            'model' => 'RTAC-' . rand(100, 300),
            'specification' => 'Air-cooled scroll compressor',
            'cost' => $capacity * 2000,
            'lead_time' => rand(16, 20),
            'warranty' => '5 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    // Boilers
    for ($i = 0; $i < $boiler_count; $i++) {
        $capacity = ($area / $boiler_count) * 50; // kW
        $equipment[] = array(
            'id' => 'HVAC-BLR-' . ($equipment_id++),
            'name' => 'Boiler BLR-' . ($i + 1),
            'type' => 'Boiler',
            'quantity' => 1,
            'capacity' => $capacity,
            'unit' => 'kW',
            'efficiency' => rand(85, 95),
            'fuel_type' => 'Natural Gas',
            'manufacturer' => 'Raytheon',
            'model' => 'Raytube-' . rand(200, 800),
            'specification' => 'Condensing hot water boiler',
            'cost' => $capacity * 1200,
            'lead_time' => rand(8, 12),
            'warranty' => '3 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    // Fans
    for ($i = 0; $i < $fan_count; $i++) {
        $capacity = rand(1, 15); // kW
        $equipment[] = array(
            'id' => 'HVAC-FAN-' . ($equipment_id++),
            'name' => 'Exhaust Fan FAN-' . ($i + 1),
            'type' => 'Exhaust Fan',
            'quantity' => 1,
            'capacity' => $capacity,
            'unit' => 'kW',
            'airflow' => $capacity * 1000, // CFM
            'manufacturer' => 'Greenheck',
            'model' => 'GB-' . rand(100, 999),
            'specification' => 'Backward inclined centrifugal',
            'cost' => $capacity * 800,
            'lead_time' => rand(4, 8),
            'warranty' => '5 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    return $equipment;
}

/**
 * Generate electrical equipment schedule
 */
function generateElectricalEquipment($area, $floors, $occupancy_type) {
    $equipment = array();
    $equipment_id = 1;
    
    // Calculate equipment quantities
    $panel_count = ceil($area / 300);
    $transformer_count = max(1, ceil($area / 2000));
    $generator_count = max(1, floor($floors / 5));
    
    // Electrical Panels
    for ($i = 0; $i < $panel_count; $i++) {
        $amperage = rand(100, 400);
        $equipment[] = array(
            'id' => 'ELEC-PNL-' . ($equipment_id++),
            'name' => 'Distribution Panel PNL-' . ($i + 1),
            'type' => 'Distribution Panel',
            'quantity' => 1,
            'capacity' => $amperage,
            'unit' => 'A',
            'voltage' => '208/120V',
            'phase' => 3,
            'circuits' => rand(24, 42),
            'manufacturer' => 'Square D',
            'model' => 'NEHB-' . $amperage,
            'specification' => 'NEMA 12 enclosure with main breaker',
            'cost' => $amperage * 50,
            'lead_time' => rand(6, 10),
            'warranty' => '10 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    // Transformers
    for ($i = 0; $i < $transformer_count; $i++) {
        $capacity = rand(75, 300); // kVA
        $equipment[] = array(
            'id' => 'ELEC-TXF-' . ($equipment_id++),
            'name' => 'Transformer TXF-' . ($i + 1),
            'type' => 'Dry Type Transformer',
            'quantity' => 1,
            'capacity' => $capacity,
            'unit' => 'kVA',
            'primary_voltage' => '480V',
            'secondary_voltage' => '208/120V',
            'connection' => 'Delta-Wye',
            'manufacturer' => 'Hammond',
            'model' => 'H3F-' . $capacity,
            'specification' => 'K-rated energy efficient',
            'cost' => $capacity * 200,
            'lead_time' => rand(8, 12),
            'warranty' => '10 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    // Emergency Generators
    for ($i = 0; $i < $generator_count; $i++) {
        $capacity = rand(200, 500); // kW
        $equipment[] = array(
            'id' => 'ELEC-GEN-' . ($equipment_id++),
            'name' => 'Emergency Generator GEN-' . ($i + 1),
            'type' => 'Emergency Generator',
            'quantity' => 1,
            'capacity' => $capacity,
            'unit' => 'kW',
            'fuel_type' => 'Diesel',
            'voltage' => '480/277V',
            'phase' => 3,
            'frequency' => '60Hz',
            'manufacturer' => 'Caterpillar',
            'model' => 'C' . rand(15, 32) . '-D6',
            'specification' => 'UL 2200 listed, EPA certified',
            'cost' => $capacity * 300,
            'lead_time' => rand(16, 24),
            'warranty' => '2 years',
            'installation_floor' => 1,
            'status' => 'planned'
        );
    }
    
    // UPS Systems
    for ($i = 0; $i < ceil($floors / 2); $i++) {
        $capacity = rand(10, 50); // kW
        $equipment[] = array(
            'id' => 'ELEC-UPS-' . ($equipment_id++),
            'name' => 'UPS System UPS-' . ($i + 1),
            'type' => 'Uninterruptible Power Supply',
            'quantity' => 1,
            'capacity' => $capacity,
            'unit' => 'kW',
            'runtime' => rand(15, 30),
            'efficiency' => rand(92, 97),
            'manufacturer' => 'APC',
            'model' => 'SURT' . rand(10, 40),
            'specification' => 'Online double conversion',
            'cost' => $capacity * 1000,
            'lead_time' => rand(8, 12),
            'warranty' => '2 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    return $equipment;
}

/**
 * Generate plumbing equipment schedule
 */
function generatePlumbingEquipment($area, $floors, $occupancy_type) {
    $equipment = array();
    $equipment_id = 1;
    
    // Calculate equipment quantities
    $pump_count = ceil($floors / 2);
    $water_heater_count = max(1, ceil($floors / 3));
    $tank_count = ceil($floors / 4);
    
    // Water Pumps
    for ($i = 0; $i < $pump_count; $i++) {
        $flow_rate = rand(50, 200); // GPM
        $equipment[] = array(
            'id' => 'PLMB-PMP-' . ($equipment_id++),
            'name' => 'Water Pump PMP-' . ($i + 1),
            'type' => 'Centrifugal Pump',
            'quantity' => 1,
            'capacity' => $flow_rate,
            'unit' => 'GPM',
            'head' => rand(100, 300), // feet
            'power' => $flow_rate * rand(2, 8) / 3960, // HP
            'manufacturer' => 'Bell & Gossett',
            'model' => 'e-' . rand(1510, 1550),
            'specification' => 'End suction centrifugal pump',
            'cost' => $flow_rate * 150,
            'lead_time' => rand(6, 10),
            'warranty' => '5 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    // Water Heaters
    for ($i = 0; $i < $water_heater_count; $i++) {
        $capacity = rand(200, 800); // MBH
        $equipment[] = array(
            'id' => 'PLMB-WH-' . ($equipment_id++),
            'name' => 'Water Heater WH-' . ($i + 1),
            'type' => 'Gas Water Heater',
            'quantity' => 1,
            'capacity' => $capacity,
            'unit' => 'MBH',
            'efficiency' => rand(80, 95),
            'fuel_type' => 'Natural Gas',
            'tank_capacity' => rand(80, 200), // gallons
            'manufacturer' => 'Rheem',
            'model' => 'PROG' . rand(40, 80),
            'specification' => 'Condensing tankless water heater',
            'cost' => $capacity * 50,
            'lead_time' => rand(4, 8),
            'warranty' => '6 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    // Pressure Tanks
    for ($i = 0; $i < $tank_count; $i++) {
        $capacity = rand(80, 300); // gallons
        $equipment[] = array(
            'id' => 'PLMB-TNK-' . ($equipment_id++),
            'name' => 'Pressure Tank TNK-' . ($i + 1),
            'type' => 'Pressure Tank',
            'quantity' => 1,
            'capacity' => $capacity,
            'unit' => 'gallons',
            'pressure_rating' => rand(125, 150), // PSI
            'material' => 'Steel',
            'manufacturer' => 'Wessels',
            'model' => 'ASME-' . $capacity,
            'specification' => 'ASME coded pressure vessel',
            'cost' => $capacity * 25,
            'lead_time' => rand(8, 12),
            'warranty' => '1 year',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    // Backflow Preventers
    for ($i = 0; $i < ceil($floors / 2); $i++) {
        $size = rand(2, 6); // inches
        $equipment[] = array(
            'id' => 'PLMB-BFP-' . ($equipment_id++),
            'name' => 'Backflow Preventer BFP-' . ($i + 1),
            'type' => 'Backflow Preventer',
            'quantity' => 1,
            'size' => $size,
            'unit' => 'inches',
            'pressure_rating' => rand(175, 300), // PSI
            'connection' => 'Flanged',
            'manufacturer' => 'Apollo',
            'model' => 'ARV-' . $size,
            'specification' => 'Reduced pressure zone assembly',
            'cost' => $size * 800,
            'lead_time' => rand(4, 8),
            'warranty' => '5 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    return $equipment;
}

/**
 * Generate fire protection equipment schedule
 */
function generateFireProtectionEquipment($area, $floors, $occupancy_type) {
    $equipment = array();
    $equipment_id = 1;
    
    // Calculate equipment quantities
    $sprinkler_count = ceil($area / 20);
    $hydrant_count = ceil($floors / 2);
    $pump_count = max(1, floor($area / 5000));
    
    // Sprinkler Heads
    for ($i = 0; $i < $sprinkler_count; $i++) {
        $k_factor = rand(5, 14); // K-factor
        $equipment[] = array(
            'id' => 'FIRE-SPR-' . ($equipment_id++),
            'name' => 'Sprinkler Head SPR-' . ($i + 1),
            'type' => 'Sprinkler Head',
            'quantity' => 1,
            'k_factor' => $k_factor,
            'response_type' => rand(0, 1) ? 'Standard' : 'Quick',
            'coverage_area' => rand(100, 200), // sq ft
            'temperature_rating' => rand(135, 286), // °F
            'manufacturer' => 'Tyco',
            'model' => 'TY-B-' . rand(1, 5),
            'specification' => 'UL Listed, FM Approved',
            'cost' => $k_factor * 15,
            'lead_time' => rand(2, 4),
            'warranty' => '1 year',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    // Fire Pumps
    for ($i = 0; $i < $pump_count; $i++) {
        $capacity = rand(500, 1500); // GPM
        $equipment[] = array(
            'id' => 'FIRE-PMP-' . ($equipment_id++),
            'name' => 'Fire Pump PMP-' . ($i + 1),
            'type' => 'Fire Pump',
            'quantity' => 1,
            'capacity' => $capacity,
            'unit' => 'GPM',
            'head' => rand(100, 200), // PSI
            'power' => $capacity * rand(1, 3) / 100, // HP
            'driver_type' => rand(0, 1) ? 'Electric' : 'Diesel',
            'manufacturer' => 'Patterson',
            'model' => 'PKZ-' . rand(500, 2000),
            'specification' => 'UL Listed, FM Approved, NFPA 20',
            'cost' => $capacity * 20,
            'lead_time' => rand(16, 20),
            'warranty' => '2 years',
            'installation_floor' => 1,
            'status' => 'planned'
        );
    }
    
    // Fire Hydrants
    for ($i = 0; $i < $hydrant_count; $i++) {
        $outlet_count = rand(2, 4);
        $equipment[] = array(
            'id' => 'FIRE-HYD-' . ($equipment_id++),
            'name' => 'Fire Hydrant HYD-' . ($i + 1),
            'type' => 'Fire Hydrant',
            'quantity' => 1,
            'outlet_count' => $outlet_count,
            'inlet_size' => rand(4, 6), // inches
            'working_pressure' => rand(175, 200), // PSI
            'flow_capacity' => rand(500, 1500), // GPM
            'manufacturer' => 'American Darling',
            'model' => 'B-62-B-' . rand(4, 6),
            'specification' => 'AWWA C502 compliant',
            'cost' => $outlet_count * 500,
            'lead_time' => rand(4, 8),
            'warranty' => '5 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    // Alarm Control Panels
    for ($i = 0; $i < ceil($floors / 3); $i++) {
        $zonal_capacity = rand(10, 50); // zones
        $equipment[] = array(
            'id' => 'FIRE-ACP-' . ($equipment_id++),
            'name' => 'Alarm Panel ACP-' . ($i + 1),
            'type' => 'Fire Alarm Control Panel',
            'quantity' => 1,
            'zonal_capacity' => $zonal_capacity,
            'notification_capacity' => $zonal_capacity * 4,
            'power_supply' => rand(10, 30), // amps
            'battery_backup' => rand(8, 24), // hours
            'manufacturer' => 'Simplex',
            'model' => '4010-' . $zonal_capacity,
            'specification' => 'UL Listed, NFPA 72 compliant',
            'cost' => $zonal_capacity * 200,
            'lead_time' => rand(8, 12),
            'warranty' => '3 years',
            'installation_floor' => rand(1, $floors),
            'status' => 'planned'
        );
    }
    
    return $equipment;
}

/**
 * Calculate installation schedule
 */
function calculateInstallationSchedule($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment, $floors) {
    $schedule = array();
    
    // Installation phases
    $phases = array(
        'Preparation' => array('duration' => 2, 'weeks' => 1),
        'Structural Support' => array('duration' => 4, 'weeks' => 2),
        'Major Equipment' => array('duration' => 8, 'weeks' => 4),
        'Distribution Systems' => array('duration' => 12, 'weeks' => 6),
        'Controls & Commissioning' => array('duration' => 6, 'weeks' => 3),
        'Final Testing' => array('duration' => 4, 'weeks' => 2)
    );
    
    $current_week = 1;
    
    foreach ($phases as $phase_name => $phase_info) {
        $schedule[] = array(
            'phase' => $phase_name,
            'start_week' => $current_week,
            'duration_weeks' => $phase_info['weeks'],
            'completion_week' => $current_week + $phase_info['weeks'] - 1,
            'equipment_count' => count($hvac_equipment) + count($electrical_equipment) + 
                                count($plumbing_equipment) + count($fire_protection_equipment),
            'activities' => generatePhaseActivities($phase_name)
        );
        $current_week += $phase_info['weeks'];
    }
    
    return $schedule;
}

/**
 * Generate phase activities
 */
function generatePhaseActivities($phase_name) {
    $activities = array(
        'Preparation' => array(
            'Site mobilization and setup',
            'Equipment staging and storage',
            'Utility coordination',
            'Safety planning and training'
        ),
        'Structural Support' => array(
            'Equipment foundations',
            'Pipe and duct supports',
            'Electrical raceway mounting',
            'Fire protection pipe hangers'
        ),
        'Major Equipment' => array(
            'HVAC equipment installation',
            'Electrical panel placement',
            'Plumbing pump installation',
            'Fire pump room setup'
        ),
        'Distribution Systems' => array(
            'Ductwork installation',
            'Electrical wiring and cabling',
            'Plumbing pipe installation',
            'Fire protection piping'
        ),
        'Controls & Commissioning' => array(
            'Control system programming',
            'BAS integration',
            'System balancing',
            'Performance testing'
        ),
        'Final Testing' => array(
            'System commissioning',
            'Safety system testing',
            'Performance verification',
            'Final inspections'
        )
    );
    
    return isset($activities[$phase_name]) ? $activities[$phase_name] : array();
}

/**
 * Calculate procurement timeline
 */
function calculateProcurementTimeline($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment) {
    $all_equipment = array_merge($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment);
    
    // Sort by lead time
    usort($all_equipment, function($a, $b) {
        return $a['lead_time'] - $b['lead_time'];
    });
    
    $procurement_plan = array();
    $current_week = -8; // Start procurement 8 weeks before installation
    
    foreach ($all_equipment as $equipment) {
        $procurement_plan[] = array(
            'equipment_id' => $equipment['id'],
            'equipment_name' => $equipment['name'],
            'procurement_start' => $current_week,
            'order_deadline' => $current_week + $equipment['lead_time'],
            'delivery_week' => $current_week + $equipment['lead_time'],
            'status' => 'planned'
        );
        $current_week++;
    }
    
    return $procurement_plan;
}

/**
 * Calculate equipment costs
 */
function calculateEquipmentCosts($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment) {
    $hvac_total = 0;
    $electrical_total = 0;
    $plumbing_total = 0;
    $fire_protection_total = 0;
    
    foreach ($hvac_equipment as $equipment) {
        $hvac_total += $equipment['cost'];
    }
    
    foreach ($electrical_equipment as $equipment) {
        $electrical_total += $equipment['cost'];
    }
    
    foreach ($plumbing_equipment as $equipment) {
        $plumbing_total += $equipment['cost'];
    }
    
    foreach ($fire_protection_equipment as $equipment) {
        $fire_protection_total += $equipment['cost'];
    }
    
    $grand_total = $hvac_total + $electrical_total + $plumbing_total + $fire_protection_total;
    
    return array(
        'hvac_total' => $hvac_total,
        'electrical_total' => $electrical_total,
        'plumbing_total' => $plumbing_total,
        'fire_protection_total' => $fire_protection_total,
        'grand_total' => $grand_total,
        'contingency' => $grand_total * 0.1, // 10% contingency
        'final_total' => $grand_total * 1.1
    );
}

/**
 * Generate maintenance schedule
 */
function generateMaintenanceSchedule($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment) {
    $all_equipment = array_merge($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment);
    
    $maintenance_schedules = array();
    
    foreach ($all_equipment as $equipment) {
        $equipment_type = $equipment['type'];
        
        // Determine maintenance frequency based on equipment type
        $frequency = determineMaintenanceFrequency($equipment_type);
        
        $maintenance_schedules[] = array(
            'equipment_id' => $equipment['id'],
            'equipment_name' => $equipment['name'],
            'maintenance_frequency' => $frequency,
            'next_service_date' => date('Y-m-d', strtotime('+' . $frequency['months'] . ' months')),
            'maintenance_tasks' => generateMaintenanceTasks($equipment_type),
            'estimated_cost' => $equipment['cost'] * 0.05 // 5% of equipment cost annually
        );
    }
    
    return $maintenance_schedules;
}

/**
 * Determine maintenance frequency
 */
function determineMaintenanceFrequency($equipment_type) {
    $frequencies = array(
        'Air Handling Unit' => array('months' => 3, 'type' => 'Quarterly'),
        'Chiller' => array('months' => 6, 'type' => 'Semi-Annual'),
        'Boiler' => array('months' => 6, 'type' => 'Semi-Annual'),
        'Exhaust Fan' => array('months' => 12, 'type' => 'Annual'),
        'Distribution Panel' => array('months' => 12, 'type' => 'Annual'),
        'Dry Type Transformer' => array('months' => 12, 'type' => 'Annual'),
        'Emergency Generator' => array('months' => 6, 'type' => 'Semi-Annual'),
        'Uninterruptible Power Supply' => array('months' => 6, 'type' => 'Semi-Annual'),
        'Centrifugal Pump' => array('months' => 6, 'type' => 'Semi-Annual'),
        'Gas Water Heater' => array('months' => 12, 'type' => 'Annual'),
        'Pressure Tank' => array('months' => 12, 'type' => 'Annual'),
        'Backflow Preventer' => array('months' => 12, 'type' => 'Annual'),
        'Sprinkler Head' => array('months' => 12, 'type' => 'Annual'),
        'Fire Pump' => array('months' => 6, 'type' => 'Semi-Annual'),
        'Fire Hydrant' => array('months' => 12, 'type' => 'Annual'),
        'Fire Alarm Control Panel' => array('months' => 6, 'type' => 'Semi-Annual')
    );
    
    return isset($frequencies[$equipment_type]) ? $frequencies[$equipment_type] : array('months' => 12, 'type' => 'Annual');
}

/**
 * Generate maintenance tasks
 */
function generateMaintenanceTasks($equipment_type) {
    $task_templates = array(
        'Air Handling Unit' => array(
            'Filter replacement',
            'Coil cleaning',
            'Belt tension check',
            'Control system calibration',
            'Airflow measurement'
        ),
        'Chiller' => array(
            'Refrigerant level check',
            'Compressor inspection',
            'Heat exchanger cleaning',
            'Control panel testing',
            'Performance monitoring'
        ),
        'Boiler' => array(
            'Burner cleaning and adjustment',
            'Safety valve testing',
            'Water quality testing',
            'Flue gas analysis',
            'Control system check'
        ),
        'Distribution Panel' => array(
            'Thermal imaging inspection',
            'Connection tightening',
            'Arc flash label update',
            'Load balancing check',
            'Protective device testing'
        ),
        'Centrifugal Pump' => array(
            'Seal inspection',
            'Bearing lubrication',
            'Vibration analysis',
            'Flow rate testing',
            'Suction/discharge pressure check'
        ),
        'Fire Pump' => array(
            'Weekly flow test',
            'Monthly pump test',
            'Annual inspection',
            'Diesel engine maintenance',
            'Control panel testing'
        )
    );
    
    return isset($task_templates[$equipment_type]) ? $task_templates[$equipment_type] : array(
        'Visual inspection',
        'Performance testing',
        'Safety check',
        'Documentation update'
    );
}

/**
 * Calculate equipment specifications
 */
function calculateEquipmentSpecifications($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment) {
    return array(
        'performance_standards' => array(
            'HVAC efficiency' => 'ASHRAE 90.1',
            'Electrical safety' => 'NEC 2020',
            'Plumbing code' => 'IPC 2018',
            'Fire protection' => 'NFPA 13, 14, 20'
        ),
        'compliance_requirements' => array(
            'Energy code compliance',
            'Accessibility standards',
            'Environmental regulations',
            'Building permit requirements'
        ),
        'testing_requirements' => array(
            'Factory testing for major equipment',
            'Site acceptance testing',
            'Performance verification',
            'Safety system testing'
        )
    );
}

/**
 * Calculate compliance status
 */
function calculateComplianceStatus($hvac_equipment, $electrical_equipment, $plumbing_equipment, $fire_protection_equipment) {
    return array(
        'codes_compliance' => array(
            'International Building Code' => 'Compliant',
            'International Mechanical Code' => 'Compliant',
            'National Electrical Code' => 'Compliant',
            'International Plumbing Code' => 'Compliant',
            'NFPA Standards' => 'Compliant'
        ),
        'certifications' => array(
            'UL Listings' => 'Required for electrical equipment',
            'FM Approvals' => 'Required for fire protection',
            'ASHRAE Standards' => 'Applied for HVAC design',
            'Energy Star' => 'Targeted for applicable equipment'
        ),
        'permit_status' => array(
            'Mechanical Permit' => 'Required',
            'Electrical Permit' => 'Required',
            'Plumbing Permit' => 'Required',
            'Fire Protection Permit' => 'Required'
        )
    );
}

/**
 * Update equipment status
 */
function updateEquipmentStatus($data) {
    // This would typically update a database
    // For now, just return success
    return array('success' => true, 'message' => 'Equipment status updated');
}

/**
 * Export equipment schedule
 */
function exportEquipmentSchedule($data) {
    $schedule_data = generateEquipmentSchedule($data);
    
    // Create export directory if it doesn't exist
    if (!is_dir('../../exports')) {
        mkdir('../../exports', 0755, true);
    }
    
    $filename = 'equipment_schedule_' . date('Y-m-d_H-i-s') . '.json';
    $filepath = '../../exports/' . $filename;
    
    file_put_contents($filepath, json_encode($schedule_data, JSON_PRETTY_PRINT));
    
    return true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP Equipment Schedule - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .equipment-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #673AB7, #9C27B0);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .analysis-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
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
            border-bottom: 2px solid #673AB7;
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
            background: #673AB7;
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
            background: #512DA8;
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
        
        .schedule-overview {
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
            border-left: 4px solid #673AB7;
        }
        
        .overview-value {
            font-size: 24px;
            font-weight: 600;
            color: #673AB7;
        }
        
        .overview-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .equipment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .equipment-table th,
        .equipment-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .equipment-table th {
            background: #f5f5f5;
            font-weight: 600;
            color: #333;
        }
        
        .equipment-table tr:hover {
            background: #f9f9f9;
        }
        
        .system-section {
            margin: 30px 0;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #673AB7;
        }
        
        .system-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        
        .cost-summary {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .cost-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .cost-item {
            text-align: center;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        
        .cost-value {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .installation-timeline {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .timeline-item {
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .timeline-item:last-child {
            border-bottom: none;
        }
        
        .phase-name {
            font-weight: 600;
            color: #1976d2;
        }
        
        .timeline-duration {
            color: #666;
            font-size: 14px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .status-planned {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .status-ordered {
            background: #fff3e0;
            color: #ef6c00;
        }
        
        .status-installed {
            background: #e3f2fd;
            color: #1976d2;
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
            
            .schedule-overview {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <?php include '../../../themes/default/views/partials/header.php'; ?>
    
    <div class="equipment-container">
        <div class="page-header">
            <h1>MEP Equipment Schedule</h1>
            <p>Comprehensive equipment scheduling and tracking for MEP systems</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="analysis-grid">
            <!-- Input Form -->
            <div class="card">
                <div class="card-header">Project Parameters</div>
                
                <form method="POST" id="schedule-form">
                    <input type="hidden" name="action" value="generate_schedule">
                    
                    <div class="form-group">
                        <label for="project_name">Project Name</label>
                        <input type="text" id="project_name" name="project_name" 
                               value="<?php echo htmlspecialchars($saved_schedules[0]['project_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="building_area">Building Area (m²)</label>
                            <input type="number" id="building_area" name="building_area" 
                                   value="<?php echo htmlspecialchars($saved_schedules[0]['building_area'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="floors">Number of Floors</label>
                            <input type="number" id="floors" name="floors" 
                                   value="<?php echo htmlspecialchars($saved_schedules[0]['floors'] ?? '1'); ?>" min="1" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="occupancy_type">Occupancy Type</label>
                        <select id="occupancy_type" name="occupancy_type" required>
                            <option value="office">Office</option>
                            <option value="retail">Retail</option>
                            <option value="hospital">Hospital</option>
                            <option value="school">School</option>
                            <option value="residential">Residential</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Generate Schedule</button>
                    <button type="button" class="btn btn-secondary" onclick="exportSchedule()">Export Schedule</button>
                </form>
            </div>
            
            <!-- Schedule Overview -->
            <div class="card">
                <div class="card-header">Equipment Schedule Overview</div>
                <div id="schedule-overview">
                    <p style="color: #666; text-align: center; padding: 50px 20px;">
                        Enter project parameters and click "Generate Schedule" to create the equipment schedule.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Results Section -->
        <div id="results-section" class="results-section">
            <div class="card">
                <div class="card-header">Equipment Schedule Results</div>
                
                <div id="schedule-results"></div>
            </div>
        </div>
    </div>
    
    <?php include '../../../themes/default/views/partials/footer.php'; ?>
    
    <script>
        function generateSchedule() {
            const formData = new FormData(document.getElementById('schedule-form'));
            formData.append('action', 'generate_schedule');
            
            fetch('equipment-schedule.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayScheduleResults(data.results);
                } else {
                    showNotification('Error generating schedule: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error generating schedule', 'danger');
            });
        }
        
        function exportSchedule() {
            const formData = new FormData(document.getElementById('schedule-form'));
            formData.append('action', 'export_schedule');
            
            fetch('equipment-schedule.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Schedule exported successfully!', 'info');
                } else {
                    showNotification('Error exporting schedule: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error exporting schedule', 'danger');
            });
        }
        
        function displayScheduleResults(schedule) {
            document.getElementById('results-section').classList.add('active');
            
            const totalEquipment = schedule.equipment_by_system.hvac.length + 
                                 schedule.equipment_by_system.electrical.length + 
                                 schedule.equipment_by_system.plumbing.length + 
                                 schedule.equipment_by_system.fire_protection.length;
            
            const overviewHtml = `
                <div class="schedule-overview">
                    <div class="overview-item">
                        <div class="overview-value">${totalEquipment}</div>
                        <div class="overview-label">Total Equipment</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${schedule.equipment_by_system.hvac.length}</div>
                        <div class="overview-label">HVAC Equipment</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${schedule.equipment_by_system.electrical.length}</div>
                        <div class="overview-label">Electrical Equipment</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${schedule.equipment_by_system.plumbing.length}</div>
                        <div class="overview-label">Plumbing Equipment</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${schedule.installation_schedule.length}</div>
                        <div class="overview-label">Installation Phases</div>
                    </div>
                </div>
                
                <div class="cost-summary">
                    <h3>Equipment Cost Summary</h3>
                    <div class="cost-grid">
                        <div class="cost-item">
                            <div class="cost-value">$${(schedule.cost_summary.hvac_total / 1000).toFixed(0)}K</div>
                            <div class="overview-label">HVAC Equipment</div>
                        </div>
                        <div class="cost-item">
                            <div class="cost-value">$${(schedule.cost_summary.electrical_total / 1000).toFixed(0)}K</div>
                            <div class="overview-label">Electrical Equipment</div>
                        </div>
                        <div class="cost-item">
                            <div class="cost-value">$${(schedule.cost_summary.plumbing_total / 1000).toFixed(0)}K</div>
                            <div class="overview-label">Plumbing Equipment</div>
                        </div>
                        <div class="cost-item">
                            <div class="cost-value">$${(schedule.cost_summary.fire_protection_total / 1000).toFixed(0)}K</div>
                            <div class="overview-label">Fire Protection</div>
                        </div>
                        <div class="cost-item">
                            <div class="cost-value">$${(schedule.cost_summary.final_total / 1000).toFixed(0)}K</div>
                            <div class="overview-label">Total with Contingency</div>
                        </div>
                    </div>
                </div>
                
                <div class="installation-timeline">
                    <h3>Installation Timeline</h3>
                    ${schedule.installation_schedule.map(phase => `
                        <div class="timeline-item">
                            <div class="phase-name">${phase.phase}</div>
                            <div class="timeline-duration">Week ${phase.start_week} - ${phase.completion_week} (${phase.duration_weeks} weeks)</div>
                            <div>Equipment: ${phase.equipment_count} items</div>
                        </div>
                    `).join('')}
                </div>
                
                <div class="system-section">
                    <h3 class="system-title">HVAC Equipment</h3>
                    <table class="equipment-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Cost</th>
                                <th>Lead Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${schedule.equipment_by_system.hvac.slice(0, 10).map(equipment => `
                                <tr>
                                    <td>${equipment.id}</td>
                                    <td>${equipment.name}</td>
                                    <td>${equipment.type}</td>
                                    <td>${equipment.capacity} ${equipment.unit}</td>
                                    <td>$${(equipment.cost / 1000).toFixed(0)}K</td>
                                    <td>${equipment.lead_time} weeks</td>
                                    <td><span class="status-badge status-${equipment.status}">${equipment.status}</span></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                
                <div class="system-section">
                    <h3 class="system-title">Electrical Equipment</h3>
                    <table class="equipment-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Cost</th>
                                <th>Lead Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${schedule.equipment_by_system.electrical.slice(0, 10).map(equipment => `
                                <tr>
                                    <td>${equipment.id}</td>
                                    <td>${equipment.name}</td>
                                    <td>${equipment.type}</td>
                                    <td>${equipment.capacity} ${equipment.unit}</td>
                                    <td>$${(equipment.cost / 1000).toFixed(0)}K</td>
                                    <td>${equipment.lead_time} weeks</td>
                                    <td><span class="status-badge status-${equipment.status}">${equipment.status}</span></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                
                <div class="system-section">
                    <h3 class="system-title">Maintenance Schedule Summary</h3>
                    <p>Annual maintenance cost estimate: $${(schedule.maintenance_schedule.reduce((sum, item) => sum + item.estimated_cost, 0) / 1000).toFixed(0)}K</p>
                    <p>Next service due: ${schedule.maintenance_schedule[0]?.next_service_date || 'TBD'}</p>
                </div>
            `;
            
            document.getElementById('schedule-results').innerHTML = overviewHtml;
        }
        
        // Form submission handler
        document.getElementById('schedule-form').addEventListener('submit', function(e) {
            e.preventDefault();
            generateSchedule();
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>



