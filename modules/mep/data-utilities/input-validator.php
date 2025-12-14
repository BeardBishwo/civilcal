<?php
/**
 * MEP Input Validator & Sanitizer
 * Comprehensive input validation and sanitization utilities for MEP calculations
 * Version: 1.0.0
 */

require_once '../../../app/Config/config.php';
require_once '../../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Services/Security.php';

class MEPInputValidator {
    private $db;
    private $security;
    private $errors = [];
    private $sanitized_data = [];
    
    // Validation rules storage
    private $validation_rules = [];
    
    // Measurement units and ranges
    private $valid_units = [
        'length' => ['mm', 'cm', 'm', 'km', 'in', 'ft', 'yd', 'mile'],
        'area' => ['mm2', 'cm2', 'm2', 'km2', 'in2', 'ft2', 'yd2', 'acre', 'hectare'],
        'volume' => ['mm3', 'cm3', 'm3', 'in3', 'ft3', 'gal', 'liter', 'ml'],
        'mass' => ['mg', 'g', 'kg', 'ton', 'lb', 'oz'],
        'pressure' => ['Pa', 'kPa', 'MPa', 'bar', 'psi', 'atm', 'torr'],
        'temperature' => ['C', 'F', 'K', 'R'],
        'power' => ['W', 'kW', 'MW', 'hp', 'BTU/hr'],
        'flow_rate' => ['m3/s', 'L/s', 'L/min', 'gal/min', 'cfm'],
        'velocity' => ['m/s', 'km/h', 'ft/s', 'mph', 'knots'],
        'energy' => ['J', 'kJ', 'MJ', 'GJ', 'Wh', 'kWh', 'MWh', 'BTU', 'cal']
    ];
    
    // MEP-specific valid ranges
    private $mep_ranges = [
        'building_length' => [0.1, 1000], // 0.1m to 1km
        'building_width' => [0.1, 1000],
        'building_height' => [0.5, 500],
        'room_area' => [1, 10000], // 1m² to 10,000m²
        'room_volume' => [1, 50000], // 1m³ to 50,000m³
        'hvac_load' => [0, 10000], // 0 to 10,000 kW
        'electrical_load' => [0, 5000], // 0 to 5,000 kW
        'water_flow' => [0, 1000], // 0 to 1,000 L/s
        'pressure' => [0, 5000], // 0 to 5,000 kPa
        'temperature' => [-50, 200], // -50°C to 200°C
        'humidity' => [0, 100], // 0% to 100%
        'air_velocity' => [0, 50], // 0 to 50 m/s
        'pipe_diameter' => [10, 2000], // 10mm to 2m
        'duct_size' => [100, 2000], // 100mm to 2m
        'electrical_voltage' => [12, 48000], // 12V to 48kV
        'power_factor' => [0.5, 1.0], // 0.5 to 1.0
        'efficiency' => [0, 1.0] // 0% to 100%
    ];

    public function __construct() {
        $this->db = new Database();
        $this->security = new Security();
        $this->setupDefaultValidationRules();
    }

    /**
     * Setup default validation rules for common MEP inputs
     */
    private function setupDefaultValidationRules() {
        $this->validation_rules = [
            'project_name' => [
                'required' => true,
                'type' => 'string',
                'min_length' => 1,
                'max_length' => 255,
                'pattern' => '/^[a-zA-Z0-9\s\-_\.]+$/'
            ],
            'building_type' => [
                'required' => true,
                'type' => 'string',
                'options' => ['residential', 'commercial', 'industrial', 'healthcare', 'educational', 'retail', 'office', 'warehouse', 'mixed-use']
            ],
            'floor_area' => [
                'required' => true,
                'type' => 'numeric',
                'min_value' => 1,
                'max_value' => 1000000,
                'unit' => 'm2'
            ],
            'number_of_floors' => [
                'required' => true,
                'type' => 'integer',
                'min_value' => 1,
                'max_value' => 200
            ],
            'hvac_load' => [
                'type' => 'numeric',
                'min_value' => 0,
                'max_value' => 10000,
                'unit' => 'kW'
            ],
            'electrical_load' => [
                'type' => 'numeric',
                'min_value' => 0,
                'max_value' => 5000,
                'unit' => 'kW'
            ],
            'water_demand' => [
                'type' => 'numeric',
                'min_value' => 0,
                'max_value' => 1000,
                'unit' => 'L/s'
            ],
            'email' => [
                'type' => 'email',
                'max_length' => 255
            ],
            'phone' => [
                'type' => 'phone',
                'pattern' => '/^[\+]?[1-9][\d]{0,15}$/'
            ],
            'coordinates' => [
                'type' => 'coordinate',
                'latitude_range' => [-90, 90],
                'longitude_range' => [-180, 180]
            ]
        ];
    }

    /**
     * Validate and sanitize input data
     */
    public function validate($data, $rules = null) {
        $this->errors = [];
        $this->sanitized_data = [];
        
        $validation_rules = $rules ?? $this->validation_rules;
        
        foreach ($data as $field => $value) {
            if (isset($validation_rules[$field])) {
                $result = $this->validateField($field, $value, $validation_rules[$field]);
                if ($result['valid']) {
                    $this->sanitized_data[$field] = $result['value'];
                } else {
                    $this->errors[$field] = $result['error'];
                }
            } else {
                // Apply default sanitization for unknown fields
                $this->sanitized_data[$field] = $this->sanitizeDefault($value);
            }
        }
        
        return [
            'valid' => empty($this->errors),
            'errors' => $this->errors,
            'data' => $this->sanitized_data
        ];
    }

    /**
     * Validate individual field
     */
    private function validateField($field, $value, $rules) {
        // Check required
        if (isset($rules['required']) && $rules['required'] && (empty($value) && $value !== '0')) {
            return ['valid' => false, 'error' => "$field is required"];
        }
        
        if (empty($value)) {
            return ['valid' => true, 'value' => null];
        }
        
        // Type validation
        if (isset($rules['type'])) {
            $type_result = $this->validateType($field, $value, $rules['type'], $rules);
            if (!$type_result['valid']) {
                return $type_result;
            }
            $value = $type_result['value'];
        }
        
        // Length validation
        if (isset($rules['min_length']) && strlen($value) < $rules['min_length']) {
            return ['valid' => false, 'error' => "$field must be at least {$rules['min_length']} characters"];
        }
        
        if (isset($rules['max_length']) && strlen($value) > $rules['max_length']) {
            return ['valid' => false, 'error' => "$field must not exceed {$rules['max_length']} characters"];
        }
        
        // Pattern validation
        if (isset($rules['pattern']) && !preg_match($rules['pattern'], $value)) {
            return ['valid' => false, 'error' => "$field format is invalid"];
        }
        
        // Options validation
        if (isset($rules['options']) && !in_array($value, $rules['options'])) {
            return ['valid' => false, 'error' => "$field must be one of: " . implode(', ', $rules['options'])];
        }
        
        // Range validation
        if (isset($rules['min_value']) || isset($rules['max_value'])) {
            $numeric_value = is_numeric($value) ? $value : floatval($value);
            
            if (isset($rules['min_value']) && $numeric_value < $rules['min_value']) {
                return ['valid' => false, 'error' => "$field must be at least {$rules['min_value']}"];
            }
            
            if (isset($rules['max_value']) && $numeric_value > $rules['max_value']) {
                return ['valid' => false, 'error' => "$field must not exceed {$rules['max_value']}"];
            }
        }
        
        // MEP-specific range validation
        if (isset($this->mep_ranges[$field])) {
            $range = $this->mep_ranges[$field];
            $numeric_value = is_numeric($value) ? $value : floatval($value);
            
            if ($numeric_value < $range[0] || $numeric_value > $range[1]) {
                return ['valid' => false, 'error' => "$field must be between {$range[0]} and {$range[1]}"];
            }
        }
        
        // Unit validation
        if (isset($rules['unit'])) {
            $unit_result = $this->validateUnit($field, $value, $rules['unit']);
            if (!$unit_result['valid']) {
                return $unit_result;
            }
        }
        
        return ['valid' => true, 'value' => $value];
    }

    /**
     * Validate data type
     */
    private function validateType($field, $value, $type, $rules) {
        switch ($type) {
            case 'string':
                if (!is_string($value)) {
                    return ['valid' => false, 'error' => "$field must be a string"];
                }
                break;
                
            case 'integer':
                if (!filter_var($value, FILTER_VALIDATE_INT) !== false) {
                    return ['valid' => false, 'error' => "$field must be an integer"];
                }
                break;
                
            case 'numeric':
            case 'float':
                if (!is_numeric($value)) {
                    return ['valid' => false, 'error' => "$field must be numeric"];
                }
                break;
                
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return ['valid' => false, 'error' => "$field must be a valid email"];
                }
                break;
                
            case 'phone':
                if (!$this->validatePhone($value)) {
                    return ['valid' => false, 'error' => "$field must be a valid phone number"];
                }
                break;
                
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    return ['valid' => false, 'error' => "$field must be a valid URL"];
                }
                break;
                
            case 'boolean':
                if (!in_array($value, [true, false, 1, 0, '1', '0', 'true', 'false'], true)) {
                    return ['valid' => false, 'error' => "$field must be boolean"];
                }
                break;
                
            case 'coordinate':
                if (!$this->validateCoordinate($value, $rules)) {
                    return ['valid' => false, 'error' => "$field must be valid coordinates"];
                }
                break;
                
            case 'date':
                if (!$this->validateDate($value)) {
                    return ['valid' => false, 'error' => "$field must be a valid date"];
                }
                break;
                
            case 'json':
                if (!$this->validateJSON($value)) {
                    return ['valid' => false, 'error' => "$field must be valid JSON"];
                }
                break;
                
            default:
                if (class_exists($type)) {
                    if (!$value instanceof $type) {
                        return ['valid' => false, 'error' => "$field must be an instance of $type"];
                    }
                }
                break;
        }
        
        return ['valid' => true, 'value' => $value];
    }

    /**
     * Validate unit and convert if needed
     */
    private function validateUnit($field, $value, $required_unit) {
        // Extract value and unit from input like "100 m2" or "50 kPa"
        if (is_string($value)) {
            $parts = explode(' ', trim($value));
            if (count($parts) >= 2) {
                $numeric_value = $parts[0];
                $unit = implode(' ', array_slice($parts, 1));
                
                if (!$this->isValidUnit($unit, $required_unit)) {
                    return ['valid' => false, 'error' => "Invalid unit for $field. Expected: $required_unit"];
                }
                
                return ['valid' => true, 'value' => [
                    'value' => $numeric_value,
                    'unit' => $unit,
                    'original' => $value
                ]];
            }
        }
        
        // Assume numeric value only, use default unit
        if (is_numeric($value)) {
            return ['valid' => true, 'value' => [
                'value' => $value,
                'unit' => $required_unit,
                'original' => $value
            ]];
        }
        
        return ['valid' => false, 'error' => "Invalid format for $field. Expected: [value] [unit]"];
    }

    /**
     * Check if unit is valid for category
     */
    private function isValidUnit($unit, $category) {
        return isset($this->valid_units[$category]) && in_array($unit, $this->valid_units[$category]);
    }

    /**
     * Validate phone number
     */
    private function validatePhone($phone) {
        // Remove all non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        // Check length (7-15 digits)
        return strlen($cleaned) >= 7 && strlen($cleaned) <= 15;
    }

    /**
     * Validate coordinate
     */
    private function validateCoordinate($value, $rules) {
        if (is_string($value)) {
            $coords = explode(',', $value);
            if (count($coords) !== 2) {
                return false;
            }
            
            $lat = floatval(trim($coords[0]));
            $lng = floatval(trim($coords[1]));
            
            $lat_range = $rules['latitude_range'] ?? [-90, 90];
            $lng_range = $rules['longitude_range'] ?? [-180, 180];
            
            return $lat >= $lat_range[0] && $lat <= $lat_range[1] &&
                   $lng >= $lng_range[0] && $lng <= $lng_range[1];
        }
        
        return false;
    }

    /**
     * Validate date
     */
    private function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Validate JSON
     */
    private function validateJSON($json) {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Default sanitization
     */
    private function sanitizeDefault($value) {
        if (is_array($value)) {
            return array_map([$this, 'sanitizeDefault'], $value);
        }
        
        if (is_string($value)) {
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }

    /**
     * Sanitize for database insertion
     */
    public function sanitizeForDatabase($data) {
        if (is_array($data)) {
            $sanitized = [];
            foreach ($data as $key => $value) {
                $sanitized[$key] = $this->sanitizeForDatabase($value);
            }
            return $sanitized;
        }
        
        if (is_string($data)) {
            return $this->db->escape($data);
        }
        
        return $data;
    }

    /**
     * Sanitize for HTML output
     */
    public function sanitizeForOutput($data) {
        if (is_array($data)) {
            $sanitized = [];
            foreach ($data as $key => $value) {
                $sanitized[$key] = $this->sanitizeForOutput($value);
            }
            return $sanitized;
        }
        
        if (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }

    /**
     * Validate MEP-specific calculations
     */
    public function validateMECalculation($calculation_type, $inputs) {
        $validation_results = [];
        
        switch ($calculation_type) {
            case 'hvac_load':
                $validation_results = $this->validateHVACLoadInputs($inputs);
                break;
                
            case 'electrical_load':
                $validation_results = $this->validateElectricalLoadInputs($inputs);
                break;
                
            case 'plumbing_flow':
                $validation_results = $this->validatePlumbingFlowInputs($inputs);
                break;
                
            case 'fire_protection':
                $validation_results = $this->validateFireProtectionInputs($inputs);
                break;
                
            default:
                $validation_results = ['valid' => false, 'error' => 'Unknown calculation type'];
        }
        
        return $validation_results;
    }

    /**
     * Validate HVAC load calculation inputs
     */
    private function validateHVACLoadInputs($inputs) {
        $rules = [
            'building_area' => ['required' => true, 'type' => 'numeric', 'min_value' => 1],
            'building_volume' => ['required' => true, 'type' => 'numeric', 'min_value' => 1],
            'occupancy_density' => ['required' => true, 'type' => 'numeric', 'min_value' => 0.1],
            'external_temperature' => ['type' => 'numeric', 'min_value' => -50, 'max_value' => 60],
            'internal_temperature' => ['type' => 'numeric', 'min_value' => 15, 'max_value' => 30],
            'humidity' => ['type' => 'numeric', 'min_value' => 0, 'max_value' => 100],
            'air_changes_per_hour' => ['type' => 'numeric', 'min_value' => 0.5, 'max_value' => 20]
        ];
        
        return $this->validate($inputs, $rules);
    }

    /**
     * Validate electrical load calculation inputs
     */
    private function validateElectricalLoadInputs($inputs) {
        $rules = [
            'lighting_load' => ['type' => 'numeric', 'min_value' => 0],
            'power_load' => ['type' => 'numeric', 'min_value' => 0],
            'hvac_load' => ['type' => 'numeric', 'min_value' => 0],
            'other_loads' => ['type' => 'numeric', 'min_value' => 0],
            'power_factor' => ['type' => 'numeric', 'min_value' => 0.5, 'max_value' => 1.0],
            'diversity_factor' => ['type' => 'numeric', 'min_value' => 0.5, 'max_value' => 1.0]
        ];
        
        return $this->validate($inputs, $rules);
    }

    /**
     * Validate plumbing flow calculation inputs
     */
    private function validatePlumbingFlowInputs($inputs) {
        $rules = [
            'fixture_count' => ['type' => 'integer', 'min_value' => 0],
            'flow_rate_per_fixture' => ['type' => 'numeric', 'min_value' => 0],
            'peak_demand_factor' => ['type' => 'numeric', 'min_value' => 1.0, 'max_value' => 4.0],
            'pressure' => ['type' => 'numeric', 'min_value' => 0],
            'pipe_diameter' => ['type' => 'numeric', 'min_value' => 10, 'max_value' => 2000],
            'pipe_length' => ['type' => 'numeric', 'min_value' => 0.1, 'max_value' => 10000]
        ];
        
        return $this->validate($inputs, $rules);
    }

    /**
     * Validate fire protection calculation inputs
     */
    private function validateFireProtectionInputs($inputs) {
        $rules = [
            'building_area' => ['required' => true, 'type' => 'numeric', 'min_value' => 1],
            'building_height' => ['type' => 'numeric', 'min_value' => 1, 'max_value' => 500],
            'occupancy_type' => ['type' => 'string', 'options' => ['residential', 'commercial', 'industrial', 'healthcare', 'educational']],
            'hazard_level' => ['type' => 'string', 'options' => ['low', 'moderate', 'high', 'extreme']],
            'sprinkler_density' => ['type' => 'numeric', 'min_value' => 0.05, 'max_value' => 0.5],
            'response_time_index' => ['type' => 'numeric', 'min_value' => 50, 'max_value' => 400]
        ];
        
        return $this->validate($inputs, $rules);
    }

    /**
     * Add custom validation rule
     */
    public function addRule($field, $rule) {
        $this->validation_rules[$field] = $rule;
    }

    /**
     * Get validation errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get sanitized data
     */
    public function getSanitizedData() {
        return $this->sanitized_data;
    }

    /**
     * Check if data is valid
     */
    public function isValid() {
        return empty($this->errors);
    }

    /**
     * Log validation attempts for security monitoring
     */
    public function logValidationAttempt($data, $result) {
        $log_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'data_keys' => array_keys($data),
            'validation_result' => $result,
            'error_count' => count($this->errors)
        ];
        
        // Log to file
        $log_entry = json_encode($log_data) . "\n";
        file_put_contents('logs/validation_attempts.log', $log_entry, FILE_APPEND | LOCK_EX);
        
        // Log to database if configured
        if (class_exists('Database')) {
            try {
                $db = new Database();
                $query = "INSERT INTO security_logs (event_type, details, ip_address, user_agent, created_at) 
                         VALUES ('validation_attempt', ?, ?, ?, NOW())";
                $db->executeQuery($query, [
                    json_encode($log_data),
                    $log_data['ip_address'],
                    $log_data['user_agent']
                ]);
            } catch (Exception $e) {
                error_log("Failed to log validation attempt: " . $e->getMessage());
            }
        }
    }
}

// AJAX handler for real-time validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $validator = new MEPInputValidator();
        
        switch ($_POST['action']) {
            case 'validate_field':
                $field = $_POST['field'] ?? '';
                $value = $_POST['value'] ?? '';
                $rules = json_decode($_POST['rules'] ?? '{}', true);
                
                $result = $validator->validate([$field => $value], [$field => $rules]);
                
                echo json_encode([
                    'success' => $result['valid'],
                    'value' => $result['valid'] ? ($result['data'][$field] ?? $value) : $value,
                    'errors' => $result['errors'],
                    'sanitized' => $result['data'][$field] ?? null
                ]);
                break;
                
            case 'validate_mep_calculation':
                $calculation_type = $_POST['calculation_type'] ?? '';
                $inputs = json_decode($_POST['inputs'] ?? '{}', true);
                
                $result = $validator->validateMECalculation($calculation_type, $inputs);
                
                echo json_encode([
                    'success' => $result['valid'],
                    'errors' => $result['errors'] ?? [],
                    'sanitized_data' => $result['data'] ?? []
                ]);
                break;
                
            case 'sanitize_data':
                $data = json_decode($_POST['data'] ?? '{}', true);
                $sanitize_type = $_POST['sanitize_type'] ?? 'default';
                
                switch ($sanitize_type) {
                    case 'database':
                        $sanitized = $validator->sanitizeForDatabase($data);
                        break;
                    case 'output':
                        $sanitized = $validator->sanitizeForOutput($data);
                        break;
                    default:
                        $sanitized = $validator->sanitize($data);
                }
                
                echo json_encode([
                    'success' => true,
                    'sanitized_data' => $sanitized
                ]);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'error' => 'Unknown action'
                ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// Display validation form for testing
if (!isset($_POST['action'])) {
    include '../../../../themes/default/views/partials/header.php';
    ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1><i class="fas fa-shield-alt"></i> MEP Input Validator & Sanitizer</h1>
                    <p class="lead">Comprehensive input validation and sanitization utilities for MEP calculations</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-check-circle"></i> Validation Features</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><strong>Data Type Validation:</strong> String, integer, numeric, email, phone, URL, boolean, date, JSON</li>
                            <li><strong>Range Validation:</strong> Min/max values, length limits, MEP-specific ranges</li>
                            <li><strong>Unit Validation:</strong> Length, area, volume, mass, pressure, temperature, power, flow rate, velocity, energy</li>
                            <li><strong>MEP-Specific Validation:</strong> HVAC load, electrical load, plumbing flow, fire protection</li>
                            <li><strong>Sanitization:</strong> Database-safe, HTML output-safe, default sanitization</li>
                            <li><strong>Real-time Validation:</strong> AJAX-based field validation</li>
                            <li><strong>Security Logging:</strong> Validation attempts tracking</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-cog"></i> Live Validation Test</h5>
                    </div>
                    <div class="card-body">
                        <form id="validationForm">
                            <div class="form-group">
                                <label>Project Name</label>
                                <input type="text" class="form-control" id="project_name" name="project_name" 
                                       placeholder="Enter project name">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>Building Type</label>
                                <select class="form-control" id="building_type" name="building_type">
                                    <option value="">Select building type</option>
                                    <option value="residential">Residential</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="industrial">Industrial</option>
                                    <option value="healthcare">Healthcare</option>
                                    <option value="educational">Educational</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>Floor Area (m²)</label>
                                <input type="number" class="form-control" id="floor_area" name="floor_area" 
                                       placeholder="e.g., 1000">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>Number of Floors</label>
                                <input type="number" class="form-control" id="number_of_floors" name="number_of_floors" 
                                       placeholder="e.g., 5">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>HVAC Load (kW)</label>
                                <input type="number" class="form-control" id="hvac_load" name="hvac_load" 
                                       placeholder="e.g., 250">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>Electrical Load (kW)</label>
                                <input type="number" class="form-control" id="electrical_load" name="electrical_load" 
                                       placeholder="e.g., 150">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="user@example.com">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Validate Input</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar"></i> Validation Results</h5>
                    </div>
                    <div class="card-body">
                        <div id="validationResults">
                            <p class="text-muted">Enter data in the form to see validation results...</p>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-tools"></i> MEP Calculation Validation</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Calculation Type</label>
                            <select class="form-control" id="calculation_type">
                                <option value="hvac_load">HVAC Load</option>
                                <option value="electrical_load">Electrical Load</option>
                                <option value="plumbing_flow">Plumbing Flow</option>
                                <option value="fire_protection">Fire Protection</option>
                            </select>
                        </div>
                        
                        <div id="calculationInputs">
                            <!-- Dynamic inputs based on calculation type -->
                        </div>
                        
                        <button type="button" class="btn btn-info" onclick="validateMECalculation()">
                            Validate MEP Calculation
                        </button>
                        
                        <div id="calculationResults" class="mt-3">
                            <p class="text-muted">Select calculation type and enter inputs...</p>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-database"></i> Sanitization Test</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Input Data (JSON format)</label>
                            <textarea class="form-control" id="sanitize_input" rows="4" 
                                      placeholder='{"name": "<script>showNotification('xss', 'info')</script>", "email": "test@domain.com"}'></textarea>
                        </div>
                        
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-secondary" onclick="testSanitization('default')">
                                Default Sanitize
                            </button>
                            <button type="button" class="btn btn-warning" onclick="testSanitization('database')">
                                Database Safe
                            </button>
                            <button type="button" class="btn btn-info" onclick="testSanitization('output')">
                                HTML Output Safe
                            </button>
                        </div>
                        
                        <div id="sanitizationResults" class="mt-3">
                            <p class="text-muted">Enter data and choose sanitization type...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Initialize real-time validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('validationForm');
        const fields = form.querySelectorAll('input, select, textarea');
        
        fields.forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this.name, this.value);
            });
        });
        
        // Update calculation inputs when type changes
        document.getElementById('calculation_type').addEventListener('change', function() {
            updateCalculationInputs();
        });
        
        // Initial calculation inputs update
        updateCalculationInputs();
    });
    
    // Field validation
    function validateField(fieldName, value) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        
        // Show loading
        field.classList.add('is-loading');
        
        const formData = new FormData();
        formData.append('action', 'validate_field');
        formData.append('field', fieldName);
        formData.append('value', value);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            field.classList.remove('is-loading');
            
            if (data.success) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                feedback.textContent = '';
            } else {
                field.classList.remove('is-valid');
                field.classList.add('is-invalid');
                feedback.textContent = data.errors[fieldName] || 'Invalid input';
            }
        })
        .catch(error => {
            field.classList.remove('is-loading', 'is-valid');
            field.classList.add('is-invalid');
            feedback.textContent = 'Validation error: ' + error.message;
        });
    }
    
    // Form submission
    document.getElementById('validationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        const validator = new MEPInputValidator();
        const result = validator.validate(data);
        
        const resultsDiv = document.getElementById('validationResults');
        
        if (result.valid) {
            resultsDiv.innerHTML = `
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle"></i> Validation Successful</h6>
                    <p>All inputs are valid and sanitized.</p>
                    <h6>Sanitized Data:</h6>
                    <pre>${JSON.stringify(result.data, null, 2)}</pre>
                </div>
            `;
        } else {
            let errorHtml = '<div class="alert alert-danger"><h6><i class="fas fa-exclamation-triangle"></i> Validation Failed</h6><ul>';
            for (let [field, error] of Object.entries(result.errors)) {
                errorHtml += `<li><strong>${field}:</strong> ${error}</li>`;
            }
            errorHtml += '</ul></div>';
            resultsDiv.innerHTML = errorHtml;
        }
    });
    
    // Update calculation inputs based on type
    function updateCalculationInputs() {
        const type = document.getElementById('calculation_type').value;
        const container = document.getElementById('calculationInputs');
        
        let inputsHtml = '';
        
        switch (type) {
            case 'hvac_load':
                inputsHtml = `
                    <div class="form-group">
                        <label>Building Area (m²)</label>
                        <input type="number" class="form-control" id="calc_building_area" placeholder="e.g., 1000">
                    </div>
                    <div class="form-group">
                        <label>Building Volume (m³)</label>
                        <input type="number" class="form-control" id="calc_building_volume" placeholder="e.g., 3000">
                    </div>
                    <div class="form-group">
                        <label>Occupancy Density (m²/person)</label>
                        <input type="number" step="0.1" class="form-control" id="calc_occupancy_density" placeholder="e.g., 10">
                    </div>
                    <div class="form-group">
                        <label>External Temperature (°C)</label>
                        <input type="number" class="form-control" id="calc_external_temperature" placeholder="e.g., 35">
                    </div>
                `;
                break;
                
            case 'electrical_load':
                inputsHtml = `
                    <div class="form-group">
                        <label>Lighting Load (kW)</label>
                        <input type="number" class="form-control" id="calc_lighting_load" placeholder="e.g., 50">
                    </div>
                    <div class="form-group">
                        <label>Power Load (kW)</label>
                        <input type="number" class="form-control" id="calc_power_load" placeholder="e.g., 100">
                    </div>
                    <div class="form-group">
                        <label>HVAC Load (kW)</label>
                        <input type="number" class="form-control" id="calc_hvac_load" placeholder="e.g., 250">
                    </div>
                    <div class="form-group">
                        <label>Power Factor</label>
                        <input type="number" step="0.01" class="form-control" id="calc_power_factor" placeholder="e.g., 0.9">
                    </div>
                `;
                break;
                
            case 'plumbing_flow':
                inputsHtml = `
                    <div class="form-group">
                        <label>Fixture Count</label>
                        <input type="number" class="form-control" id="calc_fixture_count" placeholder="e.g., 50">
                    </div>
                    <div class="form-group">
                        <label>Flow Rate per Fixture (L/s)</label>
                        <input type="number" step="0.1" class="form-control" id="calc_flow_rate_fixture" placeholder="e.g., 0.5">
                    </div>
                    <div class="form-group">
                        <label>Peak Demand Factor</label>
                        <input type="number" step="0.1" class="form-control" id="calc_peak_factor" placeholder="e.g., 2.5">
                    </div>
                    <div class="form-group">
                        <label>Pressure (kPa)</label>
                        <input type="number" class="form-control" id="calc_pressure" placeholder="e.g., 300">
                    </div>
                `;
                break;
                
            case 'fire_protection':
                inputsHtml = `
                    <div class="form-group">
                        <label>Building Area (m²)</label>
                        <input type="number" class="form-control" id="calc_building_area" placeholder="e.g., 2000">
                    </div>
                    <div class="form-group">
                        <label>Building Height (m)</label>
                        <input type="number" class="form-control" id="calc_building_height" placeholder="e.g., 30">
                    </div>
                    <div class="form-group">
                        <label>Occupancy Type</label>
                        <select class="form-control" id="calc_occupancy_type">
                            <option value="residential">Residential</option>
                            <option value="commercial">Commercial</option>
                            <option value="industrial">Industrial</option>
                            <option value="healthcare">Healthcare</option>
                            <option value="educational">Educational</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hazard Level</label>
                        <select class="form-control" id="calc_hazard_level">
                            <option value="low">Low</option>
                            <option value="moderate">Moderate</option>
                            <option value="high">High</option>
                            <option value="extreme">Extreme</option>
                        </select>
                    </div>
                `;
                break;
        }
        
        container.innerHTML = inputsHtml;
    }
    
    // Validate MEP calculation
    function validateMECalculation() {
        const type = document.getElementById('calculation_type').value;
        const inputs = {};
        
        // Collect all input values
        const container = document.getElementById('calculationInputs');
        const fields = container.querySelectorAll('input, select');
        
        fields.forEach(field => {
            if (field.value) {
                const fieldName = field.id.replace('calc_', '');
                inputs[fieldName] = field.value;
            }
        });
        
        const formData = new FormData();
        formData.append('action', 'validate_mep_calculation');
        formData.append('calculation_type', type);
        formData.append('inputs', JSON.stringify(inputs));
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById('calculationResults');
            
            if (data.success) {
                resultsDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle"></i> MEP Calculation Valid</h6>
                        <p>All calculation inputs are valid.</p>
                        <h6>Validated Data:</h6>
                        <pre>${JSON.stringify(data.sanitized_data, null, 2)}</pre>
                    </div>
                `;
            } else {
                let errorHtml = '<div class="alert alert-danger"><h6><i class="fas fa-exclamation-triangle"></i> Validation Failed</h6><ul>';
                for (let [field, error] of Object.entries(data.errors)) {
                    errorHtml += `<li><strong>${field}:</strong> ${error}</li>`;
                }
                errorHtml += '</ul></div>';
                resultsDiv.innerHTML = errorHtml;
            }
        })
        .catch(error => {
            document.getElementById('calculationResults').innerHTML = `
                <div class="alert alert-danger">
                    Error: ${error.message}
                </div>
            `;
        });
    }
    
    // Test sanitization
    function testSanitization(type) {
        const input = document.getElementById('sanitize_input').value;
        
        try {
            const data = JSON.parse(input);
            
            const formData = new FormData();
            formData.append('action', 'sanitize_data');
            formData.append('data', JSON.stringify(data));
            formData.append('sanitize_type', type);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById('sanitizationResults');
                
                resultsDiv.innerHTML = `
                    <div class="alert alert-info">
                        <h6><i class="fas fa-shield-alt"></i> ${type.charAt(0).toUpperCase() + type.slice(1)} Sanitization</h6>
                        <h6>Original:</h6>
                        <pre>${JSON.stringify(data.sanitized_data ? JSON.parse(input) : input, null, 2)}</pre>
                        <h6>Sanitized:</h6>
                        <pre>${JSON.stringify(data.sanitized_data, null, 2)}</pre>
                    </div>
                `;
            })
            .catch(error => {
                document.getElementById('sanitizationResults').innerHTML = `
                    <div class="alert alert-danger">
                        Sanitization error: ${error.message}
                    </div>
                `;
            });
            
        } catch (e) {
            document.getElementById('sanitizationResults').innerHTML = `
                <div class="alert alert-warning">
                    Invalid JSON input. Please check your syntax.
                </div>
            `;
        }
    }
    </script>
    
    <style>
    .is-valid {
        border-color: #28a745;
    }
    
    .is-invalid {
        border-color: #dc3545;
    }
    
    .is-loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }
    
    .is-valid ~ .invalid-feedback {
        display: none;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .pre {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .btn-group .btn {
        margin-right: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    </style>
    
    <?php
    include '../../../../themes/default/views/partials/footer.php';
}
?>


