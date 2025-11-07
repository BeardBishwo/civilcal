<?php
/**
 * Material Takeoff System for MEP Coordination Suite
 * 
 * Comprehensive material takeoff calculations for MEP systems
 * with detailed quantity calculations, material specifications, and cost estimation
 */

session_start();
require_once '../../../includes/Database.php';
require_once '../../../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$db = new Database();
$user_id = $_SESSION['user_id'];

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'calculate_takeoff':
                echo json_encode(calculateMaterialTakeoff($_POST, $db));
                break;
            case 'save_takeoff':
                echo json_encode(saveMaterialTakeoff($_POST, $db, $user_id));
                break;
            case 'load_takeoff':
                echo json_encode(loadMaterialTakeoff($_POST['takeoff_id'], $db));
                break;
            case 'get_material_list':
                echo json_encode(getMaterialList($_POST['system_type'], $db));
                break;
            case 'export_takeoff':
                echo json_encode(exportTakeoff($_POST['takeoff_id'], $_POST['format'], $db));
                break;
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

function calculateMaterialTakeoff($data, $db) {
    $system_type = $data['system_type'] ?? '';
    $building_type = $data['building_type'] ?? '';
    $area = floatval($data['area'] ?? 0);
    $floors = intval($data['floors'] ?? 1);
    $quality_level = $data['quality_level'] ?? 'standard';
    
    $takeoff_items = [];
    $total_cost = 0;
    
    switch ($system_type) {
        case 'hvac':
            $takeoff_items = calculateHVACMaterialTakeoff($building_type, $area, $floors, $quality_level);
            break;
        case 'electrical':
            $takeoff_items = calculateElectricalMaterialTakeoff($building_type, $area, $floors, $quality_level);
            break;
        case 'plumbing':
            $takeoff_items = calculatePlumbingMaterialTakeoff($building_type, $area, $floors, $quality_level);
            break;
        case 'fire_protection':
            $takeoff_items = calculateFireProtectionMaterialTakeoff($building_type, $area, $floors, $quality_level);
            break;
        case 'all_systems':
            $takeoff_items = calculateAllSystemsMaterialTakeoff($building_type, $area, $floors, $quality_level);
            break;
    }
    
    // Calculate total cost
    foreach ($takeoff_items as $item) {
        $total_cost += $item['quantity'] * $item['unit_cost'];
    }
    
    return [
        'success' => true,
        'takeoff_items' => $takeoff_items,
        'total_cost' => $total_cost,
        'system_type' => $system_type,
        'building_type' => $building_type,
        'area' => $area,
        'floors' => $floors
    ];
}

function calculateHVACMaterialTakeoff($building_type, $area, $floors, $quality_level) {
    $items = [];
    $multiplier = getQualityMultiplier($quality_level);
    
    // HVAC Equipment
    $chiller_capacity = calculateChillerCapacity($area, $building_type);
    $ahu_capacity = calculateAHUCapacity($area, $building_type);
    $fan_coils = calculateFanCoilCount($area);
    
    $items[] = [
        'category' => 'HVAC Equipment',
        'item_name' => 'Chiller Unit',
        'specification' => getChillerSpecification($chiller_capacity, $quality_level),
        'quantity' => 1,
        'unit' => 'units',
        'unit_cost' => getChillerCost($chiller_capacity, $quality_level),
        'total_cost' => 1 * getChillerCost($chiller_capacity, $quality_level),
        'remarks' => "Capacity: {$chiller_capacity} TR"
    ];
    
    $items[] = [
        'category' => 'HVAC Equipment',
        'item_name' => 'Air Handling Unit',
        'specification' => getAHUSpecification($ahu_capacity, $quality_level),
        'quantity' => $floors > 5 ? 2 : 1,
        'unit' => 'units',
        'unit_cost' => getAHUCost($ahu_capacity, $quality_level),
        'total_cost' => ($floors > 5 ? 2 : 1) * getAHUCost($ahu_capacity, $quality_level),
        'remarks' => "Capacity: {$ahu_capacity} CFM"
    ];
    
    $items[] = [
        'category' => 'HVAC Equipment',
        'item_name' => 'Fan Coil Units',
        'specification' => 'Ceiling mounted FCU units',
        'quantity' => $fan_coils,
        'unit' => 'units',
        'unit_cost' => 2500 * $multiplier,
        'total_cost' => $fan_coils * 2500 * $multiplier,
        'remarks' => 'Various capacities 0.5-2.0 TR'
    ];
    
    // Ductwork and Accessories
    $duct_area = $area * 0.8; // Approximate duct area
    $duct_length = $duct_area / 1.2; // Approximate length per unit area
    
    $items[] = [
        'category' => 'Ductwork',
        'item_name' => 'Supply Air Ducts',
        'specification' => 'Galvanized steel, insulated',
        'quantity' => $duct_length * 0.6,
        'unit' => 'm',
        'unit_cost' => 85 * $multiplier,
        'total_cost' => $duct_length * 0.6 * 85 * $multiplier,
        'remarks' => 'Various sizes'
    ];
    
    $items[] = [
        'category' => 'Ductwork',
        'item_name' => 'Return Air Ducts',
        'specification' => 'Galvanized steel, insulated',
        'quantity' => $duct_length * 0.4,
        'unit' => 'm',
        'unit_cost' => 75 * $multiplier,
        'total_cost' => $duct_length * 0.4 * 75 * $multiplier,
        'remarks' => 'Various sizes'
    ];
    
    $items[] = [
        'category' => 'Ductwork Accessories',
        'item_name' => 'Dampers',
        'specification' => 'Fire dampers and volume control dampers',
        'quantity' => ceil($fan_coils / 5),
        'unit' => 'units',
        'unit_cost' => 450 * $multiplier,
        'total_cost' => ceil($fan_coils / 5) * 450 * $multiplier,
        'remarks' => 'Fire and volume control'
    ];
    
    $items[] = [
        'category' => 'Ductwork Accessories',
        'item_name' => 'Diffusers and Grilles',
        'specification' => 'Aluminum diffusers and grilles',
        'quantity' => $fan_coils * 3,
        'unit' => 'units',
        'unit_cost' => 180 * $multiplier,
        'total_cost' => $fan_coils * 3 * 180 * $multiplier,
        'remarks' => 'Supply and return'
    ];
    
    // Piping System
    $pipe_length = $area * 0.6;
    
    $items[] = [
        'category' => 'Piping',
        'item_name' => 'Chilled Water Pipes',
        'specification' => 'Carbon steel, insulated',
        'quantity' => $pipe_length,
        'unit' => 'm',
        'unit_cost' => 120 * $multiplier,
        'total_cost' => $pipe_length * 120 * $multiplier,
        'remarks' => 'Various sizes 50-300mm'
    ];
    
    $items[] = [
        'category' => 'Piping',
        'item_name' => 'Valves and Fittings',
        'specification' => 'Cast iron and bronze valves',
        'quantity' => ceil($pipe_length / 10),
        'unit' => 'units',
        'unit_cost' => 350 * $multiplier,
        'total_cost' => ceil($pipe_length / 10) * 350 * $multiplier,
        'remarks' => 'Gate, globe, check valves'
    ];
    
    return $items;
}

function calculateElectricalMaterialTakeoff($building_type, $area, $floors, $quality_level) {
    $items = [];
    $multiplier = getQualityMultiplier($quality_level);
    
    // Main Electrical Equipment
    $transformer_capacity = calculateTransformerCapacity($area, $building_type);
    $panel_count = calculatePanelCount($area, $floors);
    
    $items[] = [
        'category' => 'Electrical Equipment',
        'item_name' => 'Power Transformer',
        'specification' => getTransformerSpecification($transformer_capacity),
        'quantity' => 1,
        'unit' => 'units',
        'unit_cost' => getTransformerCost($transformer_capacity),
        'total_cost' => 1 * getTransformerCost($transformer_capacity),
        'remarks' => "Capacity: {$transformer_capacity} kVA"
    ];
    
    $items[] = [
        'category' => 'Electrical Equipment',
        'item_name' => 'Main Distribution Panel',
        'specification' => 'Metal clad switchgear',
        'quantity' => 1,
        'unit' => 'units',
        'unit_cost' => 8500 * $multiplier,
        'total_cost' => 8500 * $multiplier,
        'remarks' => '400A, 3-phase'
    ];
    
    $items[] = [
        'category' => 'Electrical Equipment',
        'item_name' => 'Branch Circuit Panels',
        'specification' => 'Circuit breaker panels',
        'quantity' => $panel_count,
        'unit' => 'units',
        'unit_cost' => 3200 * $multiplier,
        'total_cost' => $panel_count * 3200 * $multiplier,
        'remarks' => '200A, 3-phase'
    ];
    
    // Wiring and Cables
    $cable_length = $area * 4.5;
    
    $items[] = [
        'category' => 'Wiring & Cables',
        'item_name' => 'Power Cables',
        'specification' => 'XLPE insulated, copper conductors',
        'quantity' => $cable_length,
        'unit' => 'm',
        'unit_cost' => 45 * $multiplier,
        'total_cost' => $cable_length * 45 * $multiplier,
        'remarks' => 'Various sizes 2.5-185 sq.mm'
    ];
    
    $items[] = [
        'category' => 'Wiring & Cables',
        'item_name' => 'Control Cables',
        'specification' => 'PVC insulated, copper conductors',
        'quantity' => $cable_length * 0.3,
        'unit' => 'm',
        'unit_cost' => 25 * $multiplier,
        'total_cost' => $cable_length * 0.3 * 25 * $multiplier,
        'remarks' => 'Control and instrumentation'
    ];
    
    // Lighting
    $lighting_points = calculateLightingPoints($area, $building_type);
    $lighting_cost = getLightingCost($building_type, $quality_level);
    
    $items[] = [
        'category' => 'Lighting',
        'item_name' => 'LED Light Fixtures',
        'specification' => getLightingSpecification($building_type, $quality_level),
        'quantity' => $lighting_points,
        'unit' => 'units',
        'unit_cost' => $lighting_cost,
        'total_cost' => $lighting_points * $lighting_cost,
        'remarks' => 'LED technology'
    ];
    
    // Switches and Sockets
    $switch_count = ceil($area / 50);
    $socket_count = ceil($area / 20);
    
    $items[] = [
        'category' => 'Accessories',
        'item_name' => 'Light Switches',
        'specification' => 'Modular switches',
        'quantity' => $switch_count,
        'unit' => 'units',
        'unit_cost' => 85 * $multiplier,
        'total_cost' => $switch_count * 85 * $multiplier,
        'remarks' => '1-gang and 2-gang'
    ];
    
    $items[] = [
        'category' => 'Accessories',
        'item_name' => 'Power Sockets',
        'specification' => '13A switched sockets',
        'quantity' => $socket_count,
        'unit' => 'units',
        'unit_cost' => 125 * $multiplier,
        'total_cost' => $socket_count * 125 * $multiplier,
        'remarks' => 'Single and double'
    ];
    
    return $items;
}

function calculatePlumbingMaterialTakeoff($building_type, $area, $floors, $quality_level) {
    $items = [];
    $multiplier = getQualityMultiplier($quality_level);
    
    // Plumbing Fixtures
    $fixture_counts = calculateFixtureCounts($building_type, $area, $floors);
    
    $items[] = [
        'category' => 'Plumbing Fixtures',
        'item_name' => 'Water Closets',
        'specification' => 'Wall hung WC with flush valve',
        'quantity' => $fixture_counts['wc'],
        'unit' => 'units',
        'unit_cost' => 2800 * $multiplier,
        'total_cost' => $fixture_counts['wc'] * 2800 * $multiplier,
        'remarks' => 'European standard'
    ];
    
    $items[] = [
        'category' => 'Plumbing Fixtures',
        'item_name' => 'Wash Basins',
        'specification' => 'Counter top basins',
        'quantity' => $fixture_counts['basin'],
        'unit' => 'units',
        'unit_cost' => 1800 * $multiplier,
        'total_cost' => $fixture_counts['basin'] * 1800 * $multiplier,
        'remarks' => 'Ceramic basins'
    ];
    
    $items[] = [
        'category' => 'Plumbing Fixtures',
        'item_name' => 'Shower Units',
        'specification' => 'Thermostatic shower mixers',
        'quantity' => $fixture_counts['shower'],
        'unit' => 'units',
        'unit_cost' => 3500 * $multiplier,
        'total_cost' => $fixture_counts['shower'] * 3500 * $multiplier,
        'remarks' => 'Complete shower units'
    ];
    
    // Pipes and Fittings
    $water_pipe_length = $area * 2.5;
    $drain_pipe_length = $area * 1.8;
    
    $items[] = [
        'category' => 'Piping',
        'item_name' => 'Water Supply Pipes',
        'specification' => 'PPR pipes and fittings',
        'quantity' => $water_pipe_length,
        'unit' => 'm',
        'unit_cost' => 65 * $multiplier,
        'total_cost' => $water_pipe_length * 65 * $multiplier,
        'remarks' => 'Hot and cold water'
    ];
    
    $items[] = [
        'category' => 'Piping',
        'item_name' => 'Drainage Pipes',
        'specification' => 'UPVC pipes and fittings',
        'quantity' => $drain_pipe_length,
        'unit' => 'm',
        'unit_cost' => 55 * $multiplier,
        'total_cost' => $drain_pipe_length * 55 * $multiplier,
        'remarks' => 'Soil and waste pipes'
    ];
    
    $items[] = [
        'category' => 'Piping',
        'item_name' => 'Vent Pipes',
        'specification' => 'UPVC vent pipes',
        'quantity' => $floors * 4,
        'unit' => 'm',
        'unit_cost' => 45 * $multiplier,
        'total_cost' => $floors * 4 * 45 * $multiplier,
        'remarks' => 'Roof ventilation'
    ];
    
    // Valves and Accessories
    $valve_count = ceil($water_pipe_length / 20);
    
    $items[] = [
        'category' => 'Valves & Accessories',
        'item_name' => 'Gate Valves',
        'specification' => 'Brass gate valves',
        'quantity' => $valve_count,
        'unit' => 'units',
        'unit_cost' => 320 * $multiplier,
        'total_cost' => $valve_count * 320 * $multiplier,
        'remarks' => 'Various sizes 15-50mm'
    ];
    
    $items[] = [
        'category' => 'Valves & Accessories',
        'item_name' => 'Mixer Taps',
        'specification' => 'Single lever mixer taps',
        'quantity' => $fixture_counts['basin'] + $fixture_counts['shower'],
        'unit' => 'units',
        'unit_cost' => 1800 * $multiplier,
        'total_cost' => ($fixture_counts['basin'] + $fixture_counts['shower']) * 1800 * $multiplier,
        'remarks' => 'Chrome plated'
    ];
    
    return $items;
}

function calculateFireProtectionMaterialTakeoff($building_type, $area, $floors, $quality_level) {
    $items = [];
    $multiplier = getQualityMultiplier($quality_level);
    
    // Sprinkler System
    $sprinkler_heads = calculateSprinklerHeadCount($area, $floors);
    $sprinkler_pipe_length = $area * 3.2;
    
    $items[] = [
        'category' => 'Sprinkler System',
        'item_name' => 'Sprinkler Heads',
        'specification' => 'Quick response heads',
        'quantity' => $sprinkler_heads,
        'unit' => 'units',
        'unit_cost' => 180 * $multiplier,
        'total_cost' => $sprinkler_heads * 180 * $multiplier,
        'remarks' => 'K=5.6, 68°C'
    ];
    
    $items[] = [
        'category' => 'Sprinkler System',
        'item_name' => 'Sprinkler Pipes',
        'specification' => 'Sch 40 steel pipes',
        'quantity' => $sprinkler_pipe_length,
        'unit' => 'm',
        'unit_cost' => 85 * $multiplier,
        'total_cost' => $sprinkler_pipe_length * 85 * $multiplier,
        'remarks' => 'Black steel, threaded'
    ];
    
    // Fire Alarm System
    $smoke_detectors = calculateSmokeDetectorCount($area, $floors);
    $fire_alarm_panels = $floors > 3 ? 2 : 1;
    
    $items[] = [
        'category' => 'Fire Alarm',
        'item_name' => 'Smoke Detectors',
        'specification' => 'Photoelectric smoke detectors',
        'quantity' => $smoke_detectors,
        'unit' => 'units',
        'unit_cost' => 450 * $multiplier,
        'total_cost' => $smoke_detectors * 450 * $multiplier,
        'remarks' => 'Addressable type'
    ];
    
    $items[] = [
        'category' => 'Fire Alarm',
        'item_name' => 'Fire Alarm Panel',
        'specification' => 'Addressable control panel',
        'quantity' => $fire_alarm_panels,
        'unit' => 'units',
        'unit_cost' => 15000 * $multiplier,
        'total_cost' => $fire_alarm_panels * 15000 * $multiplier,
        'remarks' => 'With voice evacuation'
    ];
    
    // Fire Hydrants
    $hydrant_count = calculateHydrantCount($area, $floors);
    
    $items[] = [
        'category' => 'Fire Hydrants',
        'item_name' => 'External Hydrants',
        'specification' => 'Dry barrel hydrants',
        'quantity' => ceil($area / 2000) + 2,
        'unit' => 'units',
        'unit_cost' => 8500 * $multiplier,
        'total_cost' => (ceil($area / 2000) + 2) * 8500 * $multiplier,
        'remarks' => '6" inlet, 4" outlets'
    ];
    
    // Emergency Lighting
    $emergency_lights = calculateEmergencyLightCount($area, $floors);
    
    $items[] = [
        'category' => 'Emergency Systems',
        'item_name' => 'Emergency Lights',
        'specification' => 'LED emergency lighting',
        'quantity' => $emergency_lights,
        'unit' => 'units',
        'unit_cost' => 650 * $multiplier,
        'total_cost' => $emergency_lights * 650 * $multiplier,
        'remarks' => '3-hour duration'
    ];
    
    return $items;
}

function calculateAllSystemsMaterialTakeoff($building_type, $area, $floors, $quality_level) {
    $all_items = [];
    
    // Combine all system takeoffs
    $hvac_items = calculateHVACMaterialTakeoff($building_type, $area, $floors, $quality_level);
    $electrical_items = calculateElectricalMaterialTakeoff($building_type, $area, $floors, $quality_level);
    $plumbing_items = calculatePlumbingMaterialTakeoff($building_type, $area, $floors, $quality_level);
    $fire_items = calculateFireProtectionMaterialTakeoff($building_type, $area, $floors, $quality_level);
    
    $all_items = array_merge($hvac_items, $electrical_items, $plumbing_items, $fire_items);
    
    // Add coordination and installation items
    $coordination_multiplier = 0.05; // 5% coordination cost
    $installation_multiplier = 0.15; // 15% installation cost
    
    $subtotal = 0;
    foreach ($all_items as $item) {
        $subtotal += $item['total_cost'];
    }
    
    $coordination_cost = $subtotal * $coordination_multiplier;
    $installation_cost = $subtotal * $installation_multiplier;
    
    $all_items[] = [
        'category' => 'Coordination',
        'item_name' => 'MEP Coordination',
        'specification' => '3D coordination and clash detection',
        'quantity' => 1,
        'unit' => 'project',
        'unit_cost' => $coordination_cost,
        'total_cost' => $coordination_cost,
        'remarks' => '5% of material cost'
    ];
    
    $all_items[] = [
        'category' => 'Installation',
        'item_name' => 'Installation Labor',
        'specification' => 'Skilled labor for installation',
        'quantity' => 1,
        'unit' => 'project',
        'unit_cost' => $installation_cost,
        'total_cost' => $installation_cost,
        'remarks' => '15% of material cost'
    ];
    
    return $all_items;
}

// Helper functions
function getQualityMultiplier($quality_level) {
    switch ($quality_level) {
        case 'basic': return 0.7;
        case 'standard': return 1.0;
        case 'premium': return 1.4;
        case 'luxury': return 1.8;
        default: return 1.0;
    }
}

function calculateChillerCapacity($area, $building_type) {
    $area_per_tr = [
        'residential' => 15,
        'office' => 12,
        'retail' => 10,
        'hospital' => 8,
        'school' => 14,
        'hotel' => 16,
        'industrial' => 18
    ];
    
    $area_tr = $area / ($area_per_tr[$building_type] ?? 12);
    return ceil($area_tr * 1.2); // 20% safety factor
}

function calculateAHUCapacity($area, $building_type) {
    $air_changes = [
        'residential' => 4,
        'office' => 6,
        'retail' => 8,
        'hospital' => 12,
        'school' => 8,
        'hotel' => 5,
        'industrial' => 10
    ];
    
    $ach = $air_changes[$building_type] ?? 6;
    return ceil($area * $ach);
}

function calculateFanCoilCount($area) {
    return ceil($area / 100); // One FCU per 100 sq.m
}

function getChillerSpecification($capacity, $quality_level) {
    $specs = [
        'basic' => "Air-cooled chiller, R-410A refrigerant",
        'standard' => "Air-cooled chiller with variable speed drive",
        'premium' => "Water-cooled chiller with high efficiency",
        'luxury' => "Variable refrigerant flow system"
    ];
    
    return $specs[$quality_level] ?? $specs['standard'];
}

function getChillerCost($capacity, $quality_level) {
    $base_cost = $capacity * 1500; // Base cost per TR
    $multiplier = getQualityMultiplier($quality_level);
    return $base_cost * $multiplier;
}

function getAHUSpecification($capacity, $quality_level) {
    $specs = [
        'basic' => "Single zone AHU with fixed speed fan",
        'standard' => "VAV AHU with energy recovery",
        'premium' => "Multi-zone AHU with advanced controls",
        'luxury' => "Dedicated outdoor air system with controls"
    ];
    
    return $specs[$quality_level] ?? $specs['standard'];
}

function getAHUCost($capacity, $quality_level) {
    $base_cost = $capacity * 12; // Base cost per CFM
    $multiplier = getQualityMultiplier($quality_level);
    return $base_cost * $multiplier;
}

function calculateTransformerCapacity($area, $building_type) {
    $watt_per_sqm = [
        'residential' => 80,
        'office' => 120,
        'retail' => 180,
        'hospital' => 150,
        'school' => 100,
        'hotel' => 140,
        'industrial' => 200
    ];
    
    $watts = $area * ($watt_per_sqm[$building_type] ?? 120);
    return ceil($watts / 1000); // Convert to kVA
}

function getTransformerSpecification($capacity) {
    return "Oil-immersed transformer, {$capacity} kVA, 11kV/400V";
}

function getTransformerCost($capacity) {
    return $capacity * 180; // Cost per kVA
}

function calculatePanelCount($area, $floors) {
    return ceil($area / 500) + $floors; // One panel per 500 sq.m plus one per floor
}

function calculateLightingPoints($area, $building_type) {
    $lux_levels = [
        'residential' => 300,
        'office' => 500,
        'retail' => 750,
        'hospital' => 400,
        'school' => 300,
        'hotel' => 200,
        'industrial' => 400
    ];
    
    $lux = $lux_levels[$building_type] ?? 500;
    return ceil($area / 12); // Approximate lighting points
}

function getLightingCost($building_type, $quality_level) {
    $costs = [
        'basic' => 180,
        'standard' => 280,
        'premium' => 450,
        'luxury' => 650
    ];
    
    return $costs[$quality_level] ?? $costs['standard'];
}

function getLightingSpecification($building_type, $quality_level) {
    $specs = [
        'basic' => "Basic LED panel lights",
        'standard' => "LED panel lights with dimming",
        'premium' => "Smart LED lighting with sensors",
        'luxury' => "Architectural LED lighting with controls"
    ];
    
    return $specs[$quality_level] ?? $specs['standard'];
}

function calculateFixtureCounts($building_type, $area, $floors) {
    $area_per_fixture = [
        'residential' => 40,
        'office' => 60,
        'retail' => 50,
        'hospital' => 25,
        'school' => 45,
        'hotel' => 35,
        'industrial' => 80
    ];
    
    $people_per_sqm = 1 / ($area_per_fixture[$building_type] ?? 50);
    $total_people = $area * $people_per_sqm;
    
    return [
        'wc' => ceil($total_people / 15), // 1 WC per 15 people
        'basin' => ceil($total_people / 12), // 1 basin per 12 people
        'shower' => ceil($total_people / 20) // 1 shower per 20 people
    ];
}

function calculateSprinklerHeadCount($area, $floors) {
    // Density: 1 head per 12 sq.m with max 15 sq.m per head
    return ceil($area / 12);
}

function calculateSmokeDetectorCount($area, $floors) {
    // One detector per 60 sq.m with max spacing 10m
    return ceil($area / 60);
}

function calculateHydrantCount($area, $floors) {
    // External hydrants: one per 2000 sq.m plus 2 minimum
    return ceil($area / 2000) + 2;
}

function calculateEmergencyLightCount($area, $floors) {
    // One emergency light per 100 sq.m
    return ceil($area / 100);
}

function saveMaterialTakeoff($data, $db, $user_id) {
    $project_name = $data['project_name'] ?? '';
    $system_type = $data['system_type'] ?? '';
    $building_type = $data['building_type'] ?? '';
    $area = floatval($data['area'] ?? 0);
    $floors = intval($data['floors'] ?? 1);
    $quality_level = $data['quality_level'] ?? 'standard';
    $takeoff_items = json_encode($data['takeoff_items'] ?? []);
    
    $sql = "INSERT INTO material_takeoffs (user_id, project_name, system_type, building_type, area, floors, quality_level, takeoff_items, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $params = [$user_id, $project_name, $system_type, $building_type, $area, $floors, $quality_level, $takeoff_items];
    $result = $db->executeQuery($sql, $params);
    
    if ($result) {
        $takeoff_id = $db->lastInsertId();
        return ['success' => true, 'takeoff_id' => $takeoff_id];
    } else {
        throw new Exception('Failed to save material takeoff');
    }
}

function loadMaterialTakeoff($takeoff_id, $db) {
    $sql = "SELECT * FROM material_takeoffs WHERE id = ?";
    $result = $db->executeQuery($sql, [$takeoff_id]);
    
    if ($result && $row = $result->fetch()) {
        return [
            'success' => true,
            'takeoff' => [
                'id' => $row['id'],
                'project_name' => $row['project_name'],
                'system_type' => $row['system_type'],
                'building_type' => $row['building_type'],
                'area' => floatval($row['area']),
                'floors' => intval($row['floors']),
                'quality_level' => $row['quality_level'],
                'takeoff_items' => json_decode($row['takeoff_items'], true),
                'created_at' => $row['created_at']
            ]
        ];
    } else {
        throw new Exception('Material takeoff not found');
    }
}

function getMaterialList($system_type, $db) {
    $materials = [];
    
    switch ($system_type) {
        case 'hvac':
            $materials = [
                'Chiller Units', 'Air Handling Units', 'Fan Coil Units', 'Ductwork',
                'Diffusers', 'Grilles', 'Dampers', 'Valves', 'Pipes', 'Insulation'
            ];
            break;
        case 'electrical':
            $materials = [
                'Transformers', 'Distribution Panels', 'Circuit Breakers', 'Cables',
                'Light Fixtures', 'Switches', 'Sockets', 'Conduits', 'Bus Ducts'
            ];
            break;
        case 'plumbing':
            $materials = [
                'Water Closets', 'Wash Basins', 'Showers', 'Pipes', 'Valves',
                'Fixtures', 'Pumps', 'Tanks', 'Fittings', 'Insulation'
            ];
            break;
        case 'fire_protection':
            $materials = [
                'Sprinkler Heads', 'Fire Pumps', 'Alarm Panels', 'Detectors',
                'Hose Reels', 'Emergency Lights', 'Pipes', 'Valves', 'Controllers'
            ];
            break;
    }
    
    return ['success' => true, 'materials' => $materials];
}

function exportTakeoff($takeoff_id, $format, $db) {
    $load_result = loadMaterialTakeoff($takeoff_id, $db);
    if (!$load_result['success']) {
        throw new Exception('Failed to load takeoff');
    }
    
    $takeoff = $load_result['takeoff'];
    
    switch ($format) {
        case 'pdf':
            return exportToPDF($takeoff);
        case 'excel':
            return exportToExcel($takeoff);
        case 'csv':
            return exportToCSV($takeoff);
        default:
            throw new Exception('Unsupported export format');
    }
}

function exportToPDF($takeoff) {
    // Generate PDF content
    $html = generateTakeoffHTML($takeoff);
    return ['success' => true, 'html' => $html, 'type' => 'pdf'];
}

function exportToExcel($takeoff) {
    // Generate Excel data
    $data = generateTakeoffData($takeoff);
    return ['success' => true, 'data' => $data, 'type' => 'excel'];
}

function exportToCSV($takeoff) {
    // Generate CSV data
    $csv = generateTakeoffCSV($takeoff);
    return ['success' => true, 'csv' => $csv, 'type' => 'csv'];
}

function generateTakeoffHTML($takeoff) {
    $html = "<h2>Material Takeoff Report</h2>";
    $html .= "<p><strong>Project:</strong> {$takeoff['project_name']}</p>";
    $html .= "<p><strong>System Type:</strong> " . ucwords(str_replace('_', ' ', $takeoff['system_type'])) . "</p>";
    $html .= "<p><strong>Building Type:</strong> " . ucwords(str_replace('_', ' ', $takeoff['building_type'])) . "</p>";
    $html .= "<p><strong>Area:</strong> {$takeoff['area']} sq.m</p>";
    $html .= "<p><strong>Floors:</strong> {$takeoff['floors']}</p>";
    $html .= "<p><strong>Quality Level:</strong> " . ucwords($takeoff['quality_level']) . "</p>";
    
    $html .= "<table border='1' style='width:100%; border-collapse:collapse;'>";
    $html .= "<tr><th>Category</th><th>Item Name</th><th>Specification</th><th>Quantity</th><th>Unit</th><th>Unit Cost</th><th>Total Cost</th><th>Remarks</th></tr>";
    
    $total_cost = 0;
    foreach ($takeoff['takeoff_items'] as $item) {
        $html .= "<tr>";
        $html .= "<td>{$item['category']}</td>";
        $html .= "<td>{$item['item_name']}</td>";
        $html .= "<td>{$item['specification']}</td>";
        $html .= "<td>{$item['quantity']}</td>";
        $html .= "<td>{$item['unit']}</td>";
        $html .= "<td>$" . number_format($item['unit_cost'], 2) . "</td>";
        $html .= "<td>$" . number_format($item['total_cost'], 2) . "</td>";
        $html .= "<td>{$item['remarks']}</td>";
        $html .= "</tr>";
        $total_cost += $item['total_cost'];
    }
    
    $html .= "<tr><td colspan='6'><strong>Total Cost</strong></td><td><strong>$" . number_format($total_cost, 2) . "</strong></td><td></td></tr>";
    $html .= "</table>";
    
    return $html;
}

function generateTakeoffData($takeoff) {
    $data = [
        'Project Information' => [
            'Project Name' => $takeoff['project_name'],
            'System Type' => ucwords(str_replace('_', ' ', $takeoff['system_type'])),
            'Building Type' => ucwords(str_replace('_', ' ', $takeoff['building_type'])),
            'Area (sq.m)' => $takeoff['area'],
            'Floors' => $takeoff['floors'],
            'Quality Level' => ucwords($takeoff['quality_level'])
        ],
        'Items' => $takeoff['takeoff_items']
    ];
    
    return $data;
}

function generateTakeoffCSV($takeoff) {
    $csv = "Category,Item Name,Specification,Quantity,Unit,Unit Cost,Total Cost,Remarks\n";
    
    foreach ($takeoff['takeoff_items'] as $item) {
        $csv .= "\"{$item['category']}\",\"{$item['item_name']}\",\"{$item['specification']}\",{$item['quantity']},\"{$item['unit']}\",{$item['unit_cost']},{$item['total_cost']},\"{$item['remarks']}\"\n";
    }
    
    return $csv;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Takeoff System - MEP Coordination Suite</title>
    <link rel="stylesheet" href="../../../assets/css/estimation.css">
    <style>
        .takeoff-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .takeoff-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .takeoff-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-group select,
        .form-group input {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .takeoff-results {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .results-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .summary-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .takeoff-table {
            overflow-x: auto;
            margin-top: 20px;
        }
        
        .takeoff-table table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        .takeoff-table th,
        .takeoff-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .takeoff-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .takeoff-table tr:hover {
            background: #f8f9fa;
        }
        
        .category-header {
            background: #667eea !important;
            color: white;
            font-weight: bold;
        }
        
        .total-row {
            background: #667eea !important;
            color: white;
            font-weight: bold;
        }
        
        .export-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .cost-breakdown {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .cost-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .cost-category {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .cost-amount {
            font-size: 18px;
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="takeoff-container">
        <div class="takeoff-header">
            <h1>Material Takeoff System</h1>
            <p>Comprehensive MEP material takeoffs with detailed calculations</p>
        </div>
        
        <div class="takeoff-form">
            <h3>Project Information</h3>
            <form id="takeoffForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="project_name">Project Name</label>
                        <input type="text" id="project_name" name="project_name" placeholder="Enter project name" required>
                    </div>
                    <div class="form-group">
                        <label for="system_type">System Type</label>
                        <select id="system_type" name="system_type" required>
                            <option value="">Select System</option>
                            <option value="hvac">HVAC Systems</option>
                            <option value="electrical">Electrical Systems</option>
                            <option value="plumbing">Plumbing Systems</option>
                            <option value="fire_protection">Fire Protection Systems</option>
                            <option value="all_systems">All MEP Systems</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="building_type">Building Type</label>
                        <select id="building_type" name="building_type" required>
                            <option value="">Select Building Type</option>
                            <option value="residential">Residential</option>
                            <option value="office">Office Building</option>
                            <option value="retail">Retail/Commercial</option>
                            <option value="hospital">Hospital/Healthcare</option>
                            <option value="school">School/Education</option>
                            <option value="hotel">Hotel/Hospitality</option>
                            <option value="industrial">Industrial</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="area">Building Area (sq.m)</label>
                        <input type="number" id="area" name="area" placeholder="Enter total floor area" required>
                    </div>
                    <div class="form-group">
                        <label for="floors">Number of Floors</label>
                        <input type="number" id="floors" name="floors" value="1" min="1" max="50" required>
                    </div>
                    <div class="form-group">
                        <label for="quality_level">Quality Level</label>
                        <select id="quality_level" name="quality_level" required>
                            <option value="basic">Basic</option>
                            <option value="standard" selected>Standard</option>
                            <option value="premium">Premium</option>
                            <option value="luxury">Luxury</option>
                        </select>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="button" class="btn" onclick="calculateTakeoff()">Calculate Material Takeoff</button>
                </div>
            </form>
        </div>
        
        <div class="takeoff-results" id="takeoffResults" style="display: none;">
            <h3>Material Takeoff Results</h3>
            
            <div class="results-summary">
                <div class="summary-item">
                    <div class="summary-value" id="totalCost">$0</div>
                    <div class="summary-label">Total Cost</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" id="totalItems">0</div>
                    <div class="summary-label">Total Items</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" id="costPerSqm">$0/sq.m</div>
                    <div class="summary-label">Cost per sq.m</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" id="projectArea">0 sq.m</div>
                    <div class="summary-label">Project Area</div>
                </div>
            </div>
            
            <div class="cost-breakdown" id="costBreakdown"></div>
            
            <div class="takeoff-table" id="takeoffTable"></div>
            
            <div class="export-section">
                <h4>Export Options</h4>
                <button type="button" class="btn btn-secondary" onclick="saveTakeoff()">Save Takeoff</button>
                <button type="button" class="btn btn-secondary" onclick="exportTakeoff('pdf')">Export to PDF</button>
                <button type="button" class="btn btn-secondary" onclick="exportTakeoff('excel')">Export to Excel</button>
                <button type="button" class="btn btn-secondary" onclick="exportTakeoff('csv')">Export to CSV</button>
            </div>
        </div>
        
        <div class="loading" id="loading">
            <div class="loading-spinner"></div>
            <p>Calculating material takeoff...</p>
        </div>
    </div>

    <script>
        function calculateTakeoff() {
            const form = document.getElementById('takeoffForm');
            const formData = new FormData(form);
            formData.append('action', 'calculate_takeoff');
            
            document.getElementById('loading').style.display = 'block';
            document.getElementById('takeoffResults').style.display = 'none';
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading').style.display = 'none';
                
                if (data.success) {
                    displayTakeoffResults(data);
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                document.getElementById('loading').style.display = 'none';
                alert('Error: ' + error.message);
            });
        }
        
        function displayTakeoffResults(data) {
            // Update summary
            document.getElementById('totalCost').textContent = '$' + data.total_cost.toLocaleString();
            document.getElementById('totalItems').textContent = data.takeoff_items.length;
            document.getElementById('costPerSqm').textContent = '$' + (data.total_cost / data.area).toFixed(0) + '/sq.m';
            document.getElementById('projectArea').textContent = data.area + ' sq.m';
            
            // Display cost breakdown by category
            displayCostBreakdown(data.takeoff_items);
            
            // Display takeoff table
            displayTakeoffTable(data.takeoff_items);
            
            document.getElementById('takeoffResults').style.display = 'block';
            
            // Store current takeoff data for export/save
            window.currentTakeoffData = data;
        }
        
        function displayCostBreakdown(items) {
            const breakdownContainer = document.getElementById('costBreakdown');
            const categories = {};
            
            items.forEach(item => {
                if (!categories[item.category]) {
                    categories[item.category] = 0;
                }
                categories[item.category] += item.total_cost;
            });
            
            let breakdownHTML = '';
            Object.keys(categories).forEach(category => {
                breakdownHTML += `
                    <div class="cost-item">
                        <div class="cost-category">${category}</div>
                        <div class="cost-amount">$${categories[category].toLocaleString()}</div>
                    </div>
                `;
            });
            
            breakdownContainer.innerHTML = breakdownHTML;
        }
        
        function displayTakeoffTable(items) {
            const tableContainer = document.getElementById('takeoffTable');
            let tableHTML = '<table>';
            
            tableHTML += `
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Item Name</th>
                        <th>Specification</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Unit Cost</th>
                        <th>Total Cost</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
            `;
            
            let currentCategory = '';
            items.forEach(item => {
                if (item.category !== currentCategory) {
                    if (currentCategory !== '') {
                        tableHTML += '<tr class="category-header"><td colspan="8">' + currentCategory + ' - Subtotal: $' + getCategorySubtotal(items, currentCategory).toLocaleString() + '</td></tr>';
                    }
                    currentCategory = item.category;
                }
                
                tableHTML += `
                    <tr>
                        <td>${item.category}</td>
                        <td>${item.item_name}</td>
                        <td>${item.specification}</td>
                        <td>${item.quantity}</td>
                        <td>${item.unit}</td>
                        <td>$${item.unit_cost.toLocaleString()}</td>
                        <td>$${item.total_cost.toLocaleString()}</td>
                        <td>${item.remarks}</td>
                    </tr>
                `;
            });
            
            if (currentCategory !== '') {
                const subtotal = items.reduce((sum, item) => sum + item.total_cost, 0);
                tableHTML += `<tr class="total-row"><td colspan="6"><strong>Total Cost</strong></td><td><strong>$${subtotal.toLocaleString()}</strong></td><td></td></tr>`;
            }
            
            tableHTML += '</tbody></table>';
            tableContainer.innerHTML = tableHTML;
        }
        
        function getCategorySubtotal(items, category) {
            return items.filter(item => item.category === category)
                       .reduce((sum, item) => sum + item.total_cost, 0);
        }
        
        function saveTakeoff() {
            if (!window.currentTakeoffData) {
                alert('No takeoff data to save');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'save_takeoff');
            formData.append('project_name', document.getElementById('project_name').value);
            formData.append('system_type', window.currentTakeoffData.system_type);
            formData.append('building_type', window.currentTakeoffData.building_type);
            formData.append('area', window.currentTakeoffData.area);
            formData.append('floors', window.currentTakeoffData.floors);
            formData.append('quality_level', window.currentTakeoffData.quality_level);
            formData.append('takeoff_items', JSON.stringify(window.currentTakeoffData.takeoff_items));
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Material takeoff saved successfully!');
                } else {
                    alert('Error saving takeoff: ' + data.error);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
        
        function exportTakeoff(format) {
            if (!window.currentTakeoffData) {
                alert('No takeoff data to export');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'export_takeoff');
            formData.append('takeoff_id', 'current');
            formData.append('format', format);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    handleExport(data, format);
                } else {
                    alert('Error exporting takeoff: ' + data.error);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
        
        function handleExport(data, format) {
            switch (format) {
                case 'pdf':
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(data.html);
                    printWindow.document.close();
                    printWindow.print();
                    break;
                case 'excel':
                    const dataStr = JSON.stringify(data.data);
                    const dataBlob = new Blob([dataStr], {type: 'application/json'});
                    const url = URL.createObjectURL(dataBlob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'material-takeoff.json';
                    link.click();
                    break;
                case 'csv':
                    const csvBlob = new Blob([data.csv], {type: 'text/csv'});
                    const csvUrl = URL.createObjectURL(csvBlob);
                    const csvLink = document.createElement('a');
                    csvLink.href = csvUrl;
                    csvLink.download = 'material-takeoff.csv';
                    csvLink.click();
                    break;
            }
        }
    </script>
</body>
</html>
