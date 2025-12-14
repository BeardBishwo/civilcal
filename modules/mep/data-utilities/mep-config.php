<?php
/**
 * MEP Configuration Management
 * Centralized configuration management for MEP calculations and settings
 * Version: 1.0.0
 */

require_once '../../../app/Config/config.php';
require_once '../../../../app/Core/DatabaseLegacy.php';

class MEPConfig {
    private $db;
    private $config_cache = [];
    private $config_file = '../../../db/mep_config.json';
    private $is_loaded = false;

    // Default configuration templates
    private $default_configs = [
        'hvac' => [
            'design_standards' => 'ASHRAE_90_1',
            'default_indoor_temp_cooling' => 24, // °C
            'default_indoor_temp_heating' => 21, // °C
            'default_humidity' => 50, // %
            'safety_factors' => [
                'cooling_load' => 1.15,
                'heating_load' => 1.25,
                'fan_power' => 1.1
            ],
            'duct_design' => [
                'max_velocity' => 10, // m/s
                'friction_factor' => 0.02,
                'leakage_class' => 3
            ],
            'equipment_efficiency' => [
                'chiller_min_seer' => 13,
                'heat_pump_min_hspf' => 8.2,
                'boiler_min_afue' => 80
            ]
        ],
        'electrical' => [
            'voltage_levels' => [
                'low_voltage' => [120, 240, 480],
                'medium_voltage' => [2400, 4160, 7200, 12470, 13800, 23000],
                'high_voltage' => [34500, 69000, 115000, 138000, 161000, 230000]
            ],
            'power_factors' => [
                'lighting' => 0.9,
                'motors' => 0.8,
                'transformers' => 0.85,
                'electronic_equipment' => 0.95
            ],
            'demand_factors' => [
                'receptacle_loads' => 0.5,
                'lighting' => 1.0,
                'motor_loads' => 0.8
            ],
            'conductor_ratings' => [
                'copper_ampacity_factor' => 0.8,
                'aluminum_ampacity_factor' => 0.7,
                'temperature_derating' => 0.88
            ]
        ],
        'plumbing' => [
            'fixture_units' => [
                'water_closet' => 4,
                'lavatory' => 1,
                'kitchen_sink' => 2,
                'shower' => 2,
                'bathtub' => 2,
                'dishwasher' => 2,
                'clothes_washer' => 4
            ],
            'pipe_materials' => [
                'copper' => ['type_k', 'type_l', 'type_m'],
                'pvc' => ['schedule_40', 'schedule_80'],
                'cpvc' => ['schedule_40', 'schedule_80'],
                'pex' => ['a', 'b', 'c'],
                'steel' => ['schedule_40', 'schedule_80']
            ],
            'pressure_ratings' => [
                'residential' => [45, 60], // psi min-max
                'commercial' => [60, 80],
                'industrial' => [80, 100]
            ],
            'flow_velocities' => [
                'water_supply' => [2, 8], // fps min-max
                'drainage' => [2, 6],
                'storm_drainage' => [3, 10]
            ]
        ],
        'fire_protection' => [
            'sprinkler_types' => [
                'standard_response' => ['upright', 'pendant', 'sidewall'],
                'quick_response' => ['upright', 'pendant', 'sidewall'],
                'extended_coverage' => ['upright', 'pendant'],
                'dry' => ['upright', 'pendant']
            ],
            'density_requirements' => [
                'light_hazard' => 0.1, // gpm/sq ft
                'ordinary_hazard_group_1' => 0.15,
                'ordinary_hazard_group_2' => 0.2,
                'extra_hazard_group_1' => 0.3,
                'extra_hazard_group_2' => 0.4
            ],
            'hydrant_spacing' => [
                'residential' => 300, // ft max spacing
                'commercial' => 250,
                'industrial' => 200
            ]
        ],
        'general' => [
            'units' => [
                'length' => 'm',
                'area' => 'm2',
                'volume' => 'm3',
                'pressure' => 'kPa',
                'temperature' => 'C',
                'power' => 'kW',
                'energy' => 'kWh',
                'flow_rate' => 'L/s'
            ],
            'precision' => [
                'calculations' => 4,
                'display' => 2,
                'cost_estimates' => 0
            ],
            'validation_ranges' => [
                'building_area' => [1, 1000000],
                'building_height' => [0.5, 1000],
                'occupancy_density' => [0.5, 50],
                'system_efficiency' => [0.1, 1.0]
            ]
        ]
    ];

    public function __construct() {
        $this->db = new Database();
        $this->loadConfiguration();
    }

    /**
     * Load configuration from file and database
     */
    public function loadConfiguration() {
        // Load from file first (faster)
        if (file_exists($this->config_file)) {
            try {
                $file_config = json_decode(file_get_contents($this->config_file), true);
                if ($file_config) {
                    $this->config_cache = array_merge($this->default_configs, $file_config);
                }
            } catch (Exception $e) {
                error_log('Error loading MEP config file: ' . $e->getMessage());
            }
        } else {
            $this->config_cache = $this->default_configs;
        }

        // Load from database (overrides file config)
        $this->loadFromDatabase();

        $this->is_loaded = true;
    }

    /**
     * Load configuration from database
     */
    private function loadFromDatabase() {
        $query = "SELECT config_key, config_value, category FROM mep_config WHERE status = 'active'";
        $result = $this->db->executeQuery($query);
        
        if ($result) {
            while ($row = $result->fetch()) {
                $category = $row['category'];
                $key = $row['config_key'];
                $value = json_decode($row['config_value'], true);
                
                if (!isset($this->config_cache[$category])) {
                    $this->config_cache[$category] = [];
                }
                
                $this->config_cache[$category][$key] = $value;
            }
        }
    }

    /**
     * Get configuration value
     */
    public function get($category, $key = null, $default = null) {
        if (!$this->is_loaded) {
            $this->loadConfiguration();
        }

        if ($key === null) {
            return $this->config_cache[$category] ?? $this->default_configs[$category] ?? $default;
        }

        return $this->config_cache[$category][$key] ?? $this->default_configs[$category][$key] ?? $default;
    }

    /**
     * Set configuration value
     */
    public function set($category, $key, $value, $description = '', $user_id = null) {
        // Validate value format
        if (!$this->validateConfigValue($category, $key, $value)) {
            throw new InvalidArgumentException("Invalid configuration value for {$category}.{$key}");
        }

        // Update cache
        if (!isset($this->config_cache[$category])) {
            $this->config_cache[$category] = [];
        }
        $this->config_cache[$category][$key] = $value;

        // Save to database
        $query = "INSERT INTO mep_config (category, config_key, config_value, description, user_id, updated_at) 
                 VALUES (?, ?, ?, ?, ?, NOW()) 
                 ON DUPLICATE KEY UPDATE 
                 config_value = VALUES(config_value), 
                 description = VALUES(description), 
                 updated_at = NOW()";
        
        $params = [
            $category,
            $key,
            json_encode($value),
            $description,
            $user_id
        ];

        return $this->db->executeQuery($query, $params);
    }

    /**
     * Delete configuration value
     */
    public function delete($category, $key) {
        // Remove from cache
        unset($this->config_cache[$category][$key]);

        // Remove from database
        $query = "DELETE FROM mep_config WHERE category = ? AND config_key = ?";
        return $this->db->executeQuery($query, [$category, $key]);
    }

    /**
     * Get all configuration categories
     */
    public function getCategories() {
        return array_keys($this->config_cache);
    }

    /**
     * Get all configuration for a category
     */
    public function getCategory($category) {
        return $this->get($category);
    }

    /**
     * Validate configuration value
     */
    private function validateConfigValue($category, $key, $value) {
        // Basic validation based on key patterns
        switch ($key) {
            case 'default_indoor_temp_cooling':
            case 'default_indoor_temp_heating':
                return is_numeric($value) && $value >= 10 && $value <= 35;
            
            case 'default_humidity':
                return is_numeric($value) && $value >= 0 && $value <= 100;
            
            case 'safety_factors':
            case 'power_factors':
            case 'demand_factors':
                if (!is_array($value)) return false;
                foreach ($value as $factor) {
                    if (!is_numeric($factor) || $factor <= 0) return false;
                }
                return true;
            
            case 'duct_design':
            case 'equipment_efficiency':
            case 'pipe_materials':
            case 'pressure_ratings':
            case 'flow_velocities':
            case 'density_requirements':
            case 'hydrant_spacing':
                return is_array($value);
            
            default:
                // Allow any value for custom keys
                return true;
        }
    }

    /**
     * Reset to default configuration
     */
    public function resetToDefaults($category = null) {
        if ($category) {
            $this->config_cache[$category] = $this->default_configs[$category] ?? [];
            
            // Delete from database
            $query = "DELETE FROM mep_config WHERE category = ?";
            $this->db->executeQuery($query, [$category]);
        } else {
            $this->config_cache = $this->default_configs;
            
            // Clear database
            $query = "DELETE FROM mep_config";
            $this->db->executeQuery($query);
        }
    }

    /**
     * Export configuration to JSON
     */
    public function exportToFile($file_path = null) {
        $export_data = [
            'export_date' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
            'configuration' => $this->config_cache
        ];

        $file_path = $file_path ?? $this->config_file;
        
        try {
            $json_data = json_encode($export_data, JSON_PRETTY_PRINT);
            file_put_contents($file_path, $json_data);
            return true;
        } catch (Exception $e) {
            error_log('Error exporting MEP config: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Import configuration from JSON file
     */
    public function importFromFile($file_path) {
        if (!file_exists($file_path)) {
            throw new Exception("Configuration file not found: {$file_path}");
        }

        try {
            $json_data = json_decode(file_get_contents($file_path), true);
            
            if (!isset($json_data['configuration'])) {
                throw new Exception('Invalid configuration file format');
            }

            $imported_config = $json_data['configuration'];
            
            // Validate and merge configuration
            foreach ($imported_config as $category => $configs) {
                if (!isset($this->default_configs[$category])) {
                    continue; // Skip unknown categories
                }
                
                foreach ($configs as $key => $value) {
                    try {
                        $this->set($category, $key, $value, "Imported from file");
                    } catch (Exception $e) {
                        error_log("Failed to import {$category}.{$key}: " . $e->getMessage());
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            error_log('Error importing MEP config: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get HVAC configuration
     */
    public function getHVACConfig() {
        return $this->get('hvac');
    }

    /**
     * Get electrical configuration
     */
    public function getElectricalConfig() {
        return $this->get('electrical');
    }

    /**
     * Get plumbing configuration
     */
    public function getPlumbingConfig() {
        return $this->get('plumbing');
    }

    /**
     * Get fire protection configuration
     */
    public function getFireProtectionConfig() {
        return $this->get('fire_protection');
    }

    /**
     * Get general configuration
     */
    public function getGeneralConfig() {
        return $this->get('general');
    }

    /**
     * Get MEP calculation constants
     */
    public function getCalculationConstants() {
        return [
            'hvac' => [
                'air_density' => 1.225, // kg/m³ at sea level
                'specific_heat_air' => 1.005, // kJ/kg·K
                'latent_heat_vaporization' => 2260, // kJ/kg
                'stefan_boltzmann' => 5.67e-8, // W/m²·K⁴
                'solar_constant' => 1367, // W/m²
                'glass_solar_gain' => 0.87,
                'u_value_glass' => 5.7, // W/m²·K
                'infiltration_rate' => 0.5 // ACH (Air Changes per Hour)
            ],
            'electrical' => [
                'copper_resistivity' => 1.68e-8, // Ω·m at 20°C
                'aluminum_resistivity' => 2.82e-8, // Ω·m at 20°C
                'temperature_coefficient_copper' => 0.00393, // /°C
                'temperature_coefficient_aluminum' => 0.00403, // /°C
                'power_triple_zero' => 3,
                'impedance_factor' => 0.866
            ],
            'plumbing' => [
                'water_density' => 1000, // kg/m³
                'water_viscosity' => 1.002e-6, // m²/s at 20°C
                'roughness_steel' => 0.045e-3, // m
                'roughness_copper' => 0.0015e-3, // m
                'roughness_pvc' => 0.007e-3, // m
                'gravity' => 9.81 // m/s²
            ]
        ];
    }

    /**
     * Create database table for MEP configuration
     */
    public static function createDatabaseTable() {
        $db = new Database();
        
        $sql = "CREATE TABLE IF NOT EXISTS mep_config (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category VARCHAR(50) NOT NULL,
            config_key VARCHAR(100) NOT NULL,
            config_value TEXT NOT NULL,
            description TEXT,
            user_id INT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_config (category, config_key),
            INDEX idx_category (category),
            INDEX idx_status (status),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $db->executeQuery($sql);
    }

    /**
     * Get configuration history/changelog
     */
    public function getConfigurationHistory($category = null, $limit = 50) {
        $where_clause = $category ? "WHERE category = ?" : "";
        $params = $category ? [$category] : [];
        
        $query = "SELECT category, config_key, config_value, description, user_id, updated_at 
                 FROM mep_config 
                 {$where_clause}
                 ORDER BY updated_at DESC 
                 LIMIT ?";
        
        $params[] = $limit;
        
        $result = $this->db->executeQuery($query, $params);
        
        if (!$result) {
            return [];
        }
        
        $history = [];
        while ($row = $result->fetch()) {
            $history[] = [
                'category' => $row['category'],
                'key' => $row['config_key'],
                'value' => json_decode($row['config_value'], true),
                'description' => $row['description'],
                'user_id' => $row['user_id'],
                'updated_at' => $row['updated_at']
            ];
        }
        
        return $history;
    }

    /**
     * Backup configuration
     */
    public function backupConfiguration($backup_name = null) {
        $backup_name = $backup_name ?? 'mep_config_backup_' . date('Y-m-d_H-i-s');
        $backup_file = '../../../db/backups/' . $backup_name . '.json';
        
        // Ensure backup directory exists
        $backup_dir = dirname($backup_file);
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        
        $backup_data = [
            'backup_name' => $backup_name,
            'backup_date' => date('Y-m-d H:i:s'),
            'configuration' => $this->config_cache,
            'metadata' => [
                'total_categories' => count($this->config_cache),
                'version' => '1.0.0'
            ]
        ];
        
        try {
            $json_data = json_encode($backup_data, JSON_PRETTY_PRINT);
            file_put_contents($backup_file, $json_data);
            return $backup_file;
        } catch (Exception $e) {
            error_log('Error creating MEP config backup: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get system information
     */
    public function getSystemInfo() {
        return [
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => $this->getDatabaseVersion(),
            'config_cache_size' => count($this->config_cache),
            'last_loaded' => $this->is_loaded ? date('Y-m-d H:i:s') : null,
            'config_file_exists' => file_exists($this->config_file),
            'backup_directory_exists' => is_dir('../../../db/backups/'),
            'writable_config_file' => is_writable(dirname($this->config_file)),
            'memory_usage' => memory_get_usage(true),
            'max_execution_time' => ini_get('max_execution_time')
        ];
    }

    /**
     * Get database version
     */
    private function getDatabaseVersion() {
        $result = $this->db->executeQuery("SELECT VERSION() as version");
        return $result ? $result->fetch()['version'] : 'Unknown';
    }
}

// Initialize configuration on first load
if (!isset($GLOBALS['mep_config'])) {
    $GLOBALS['mep_config'] = new MEPConfig();
}

// AJAX handler for configuration management
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $config = new MEPConfig();
        
        switch ($_POST['action']) {
            case 'get_config':
                $category = $_POST['category'] ?? null;
                $key = $_POST['key'] ?? null;
                
                $result = $key ? $config->get($category, $key) : $config->getCategory($category);
                
                echo json_encode([
                    'success' => true,
                    'data' => $result
                ]);
                break;
                
            case 'set_config':
                $category = $_POST['category'];
                $key = $_POST['key'];
                $value = json_decode($_POST['value'], true);
                $description = $_POST['description'] ?? '';
                
                $config->set($category, $key, $value, $description);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Configuration updated successfully'
                ]);
                break;
                
            case 'delete_config':
                $category = $_POST['category'];
                $key = $_POST['key'];
                
                $config->delete($category, $key);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Configuration deleted successfully'
                ]);
                break;
                
            case 'get_categories':
                echo json_encode([
                    'success' => true,
                    'data' => $config->getCategories()
                ]);
                break;
                
            case 'export_config':
                $file_path = $_POST['file_path'] ?? null;
                $success = $config->exportToFile($file_path);
                
                echo json_encode([
                    'success' => $success,
                    'message' => $success ? 'Configuration exported successfully' : 'Export failed'
                ]);
                break;
                
            case 'import_config':
                $file_path = $_POST['file_path'];
                $success = $config->importFromFile($file_path);
                
                echo json_encode([
                    'success' => $success,
                    'message' => $success ? 'Configuration imported successfully' : 'Import failed'
                ]);
                break;
                
            case 'reset_category':
                $category = $_POST['category'];
                $config->resetToDefaults($category);
                
                echo json_encode([
                    'success' => true,
                    'message' => "Category '{$category}' reset to defaults"
                ]);
                break;
                
            case 'backup_config':
                $backup_name = $_POST['backup_name'] ?? null;
                $backup_file = $config->backupConfiguration($backup_name);
                
                echo json_encode([
                    'success' => $backup_file !== false,
                    'message' => $backup_file ? "Backup created: {$backup_file}" : 'Backup failed',
                    'backup_file' => $backup_file
                ]);
                break;
                
            case 'get_history':
                $category = $_POST['category'] ?? null;
                $limit = intval($_POST['limit'] ?? 50);
                
                $history = $config->getConfigurationHistory($category, $limit);
                
                echo json_encode([
                    'success' => true,
                    'data' => $history
                ]);
                break;
                
            case 'get_system_info':
                $info = $config->getSystemInfo();
                
                echo json_encode([
                    'success' => true,
                    'data' => $info
                ]);
                break;
                
            case 'get_constants':
                $constants = $config->getCalculationConstants();
                
                echo json_encode([
                    'success' => true,
                    'data' => $constants
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

// Display configuration management interface
if (!isset($_POST['action'])) {
    include '../../../../themes/default/views/partials/header.php';
    ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1><i class="fas fa-cog"></i> MEP Configuration Management</h1>
                    <p class="lead">Centralized configuration management for MEP calculations and settings</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-sliders-h"></i> Configuration Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Configuration Category</label>
                            <select class="form-control" id="configCategory">
                                <option value="">Select category</option>
                                <option value="hvac">HVAC Systems</option>
                                <option value="electrical">Electrical Systems</option>
                                <option value="plumbing">Plumbing Systems</option>
                                <option value="fire_protection">Fire Protection</option>
                                <option value="general">General Settings</option>
                            </select>
                        </div>
                        
                        <div id="configContent">
                            <p class="text-muted">Select a category to view and modify configuration...</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-tools"></i> Management Tools</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical w-100" role="group">
                            <button type="button" class="btn btn-primary" onclick="exportConfig()">
                                <i class="fas fa-download"></i> Export Configuration
                            </button>
                            <button type="button" class="btn btn-success" onclick="importConfig()">
                                <i class="fas fa-upload"></i> Import Configuration
                            </button>
                            <button type="button" class="btn btn-info" onclick="showHistory()">
                                <i class="fas fa-history"></i> Configuration History
                            </button>
                            <button type="button" class="btn btn-warning" onclick="createBackup()">
                                <i class="fas fa-save"></i> Create Backup
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="showSystemInfo()">
                                <i class="fas fa-info-circle"></i> System Information
                            </button>
                            <button type="button" class="btn btn-dark" onclick="showConstants()">
                                <i class="fas fa-calculator"></i> Calculation Constants
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-bell"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="resetCategory()">
                                Reset Category
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="resetAll()">
                                Reset All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modals -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Configuration</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Configuration File</label>
                        <input type="file" class="form-control" id="importFile" accept=".json">
                        <small class="form-text text-muted">Select a JSON configuration file to import</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmImport()">Import</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="historyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configuration History</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="historyContent">
                        <p class="text-muted">Loading history...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Initialize configuration management
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('configCategory').addEventListener('change', function() {
            loadConfiguration(this.value);
        });
    });
    
    // Load configuration for selected category
    function loadConfiguration(category) {
        if (!category) {
            document.getElementById('configContent').innerHTML = 
                '<p class="text-muted">Select a category to view and modify configuration...</p>';
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'get_config');
        formData.append('category', category);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayConfiguration(category, data.data);
            } else {
                document.getElementById('configContent').innerHTML = 
                    '<div class="alert alert-danger">Error loading configuration: ' + (data.error || 'Unknown error') + '</div>';
            }
        })
        .catch(error => {
            document.getElementById('configContent').innerHTML = 
                '<div class="alert alert-danger">Error: ' + error.message + '</div>';
        });
    }
    
    // Display configuration form
    function displayConfiguration(category, config) {
        let html = '<div class="config-form">';
        
        Object.keys(config).forEach(key => {
            const value = config[key];
            const displayValue = typeof value === 'object' ? JSON.stringify(value, null, 2) : value;
            
            html += `
                <div class="form-group">
                    <label>${key.replace(/_/g, ' ').toUpperCase()}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="config_${key}" 
                               value="${typeof value === 'object' ? '' : displayValue}" 
                               data-key="${key}" ${typeof value === 'object' ? 'readonly' : ''}>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" 
                                    onclick="editComplexValue('${key}', ${JSON.stringify(value).replace(/"/g, '"')})"
                                    ${typeof value === 'object' ? '' : 'style="display:none;"'}>
                                Edit
                            </button>
                            <button type="button" class="btn btn-outline-primary" 
                                    onclick="saveConfig('${category}', '${key}')">
                                Save
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Type: ${typeof value}</small>
                </div>
            `;
        });
        
        html += '</div>';
        document.getElementById('configContent').innerHTML = html;
    }
    
    // Edit complex (object/array) configuration values
    function editComplexValue(key, value) {
        const modalHtml = `
            <div class="modal fade" id="editValueModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit ${key.replace(/_/g, ' ')}</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Value (JSON format)</label>
                                <textarea class="form-control" id="complexValue" rows="10">${JSON.stringify(value, null, 2)}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveComplexValue('${key}')">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('editValueModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        $('#editValueModal').modal('show');
    }
    
    // Save complex configuration value
    function saveComplexValue(key) {
        const category = document.getElementById('configCategory').value;
        const valueText = document.getElementById('complexValue').value;
        
        try {
            const value = JSON.parse(valueText);
            saveConfiguration(category, key, value);
            $('#editValueModal').modal('hide');
        } catch (e) {
            showNotification('Invalid JSON: ' + e.message);
        }
    }
    
    // Save configuration value
    function saveConfig(category, key) {
        const input = document.getElementById(`config_${key}`);
        let value;
        
        if (input.hasAttribute('readonly', 'info')) {
            // This is a complex value, should have been edited via modal
            return;
        } else {
            value = input.value;
            // Try to parse as number if it looks numeric
            if (!isNaN(value) && value !== '') {
                value = parseFloat(value);
            }
        }
        
        saveConfiguration(category, key, value);
    }
    
    // Save configuration to server
    function saveConfiguration(category, key, value) {
        const formData = new FormData();
        formData.append('action', 'set_config');
        formData.append('category', category);
        formData.append('key', key);
        formData.append('value', JSON.stringify(value));
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Configuration saved successfully', 'success');
            } else {
                showAlert('Error saving configuration: ' + (data.error || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            showAlert('Error: ' + error.message, 'danger');
        });
    }
    
    // Export configuration
    function exportConfig() {
        const formData = new FormData();
        formData.append('action', 'export_config');
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Configuration exported successfully', 'success');
            } else {
                showAlert('Export failed: ' + (data.error || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            showAlert('Error: ' + error.message, 'danger');
        });
    }
    
    // Import configuration
    function importConfig() {
        $('#importModal').modal('show');
    }
    
    // Confirm import
    function confirmImport() {
        const file = document.getElementById('importFile').files[0];
        if (!file) {
            showAlert('Please select a file to import', 'warning');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'import_config');
        formData.append('file_path', file.name); // In real implementation, upload file first
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            $('#importModal').modal('hide');
            if (data.success) {
                showAlert('Configuration imported successfully', 'success');
                // Reload current category if any
                const category = document.getElementById('configCategory').value;
                if (category) loadConfiguration(category);
            } else {
                showAlert('Import failed: ' + (data.error || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            $('#importModal').modal('hide');
            showAlert('Error: ' + error.message, 'danger');
        });
    }
    
    // Show configuration history
    function showHistory() {
        const formData = new FormData();
        formData.append('action', 'get_history');
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayHistory(data.data);
                $('#historyModal').modal('show');
            } else {
                showAlert('Error loading history: ' + (data.error || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            showAlert('Error: ' + error.message, 'danger');
        });
    }
    
    // Display configuration history
    function displayHistory(history) {
        let html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Category</th><th>Key</th><th>Value</th><th>Updated</th></tr></thead><tbody>';
        
        history.forEach(item => {
            const value = typeof item.value === 'object' ? 
                JSON.stringify(item.value).substring(0, 50) + '...' : 
                item.value;
            
            html += `
                <tr>
                    <td><strong>${item.category}</strong></td>
                    <td>${item.key}</td>
                    <td>${value}</td>
                    <td>${item.updated_at}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        document.getElementById('historyContent').innerHTML = html;
    }
    
    // Create backup
    function createBackup() {
        const formData = new FormData();
        formData.append('action', 'backup_config');
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Backup created: ' + data.backup_file, 'success');
            } else {
                showAlert('Backup failed: ' + (data.error || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            showAlert('Error: ' + error.message, 'danger');
        });
    }
    
    // Show system information
    function showSystemInfo() {
        const formData = new FormData();
        formData.append('action', 'get_system_info');
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySystemInfo(data.data);
            } else {
                showAlert('Error loading system info: ' + (data.error || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            showAlert('Error: ' + error.message, 'danger');
        });
    }
    
    // Display system information
    function displaySystemInfo(info) {
        let html = '<div class="table-responsive"><table class="table table-sm">';
        
        Object.keys(info).forEach(key => {
            const value = info[key];
            const displayValue = typeof value === 'object' ? 
                JSON.stringify(value, null, 2) : value;
            
            html += `
                <tr>
                    <td><strong>${key.replace(/_/g, ' ').toUpperCase()}</strong></td>
                    <td>${displayValue}</td>
                </tr>
            `;
        });
        
        html += '</table></div>';
        
        const modalHtml = `
            <div class="modal fade" id="systemInfoModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">System Information</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            ${html}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('systemInfoModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        $('#systemInfoModal').modal('show');
    }
    
    // Show calculation constants
    function showConstants() {
        const formData = new FormData();
        formData.append('action', 'get_constants');
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayConstants(data.data);
            } else {
                showAlert('Error loading constants: ' + (data.error || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            showAlert('Error: ' + error.message, 'danger');
        });
    }
    
    // Display calculation constants
    function displayConstants(constants) {
        let html = '<div class="accordion" id="constantsAccordion">';
        
        Object.keys(constants).forEach((category, index) => {
            html += `
                <div class="card">
                    <div class="card-header" id="heading${index}">
                        <h2 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" 
                                    data-target="#collapse${index}" aria-expanded="true" 
                                    aria-controls="collapse${index}">
                                ${category.toUpperCase()} Constants
                            </button>
                        </h2>
                    </div>
                    <div id="collapse${index}" class="collapse ${index === 0 ? 'show' : ''}" 
                         aria-labelledby="heading${index}" data-parent="#constantsAccordion">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead><tr><th>Constant</th><th>Value</th><th>Unit</th></tr></thead>
                                    <tbody>
            `;
            
            Object.keys(constants[category]).forEach(constant => {
                const value = constants[category][constant];
                const unit = getUnitForConstant(constant);
                
                html += `
                    <tr>
                        <td>${constant.replace(/_/g, ' ')}</td>
                        <td>${value}</td>
                        <td>${unit}</td>
                    </tr>
                `;
            });
            
            html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        
        const modalHtml = `
            <div class="modal fade" id="constantsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">MEP Calculation Constants</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            ${html}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('constantsModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        $('#constantsModal').modal('show');
    }
    
    // Get unit for constant (simplified mapping)
    function getUnitForConstant(constant) {
        const unitMap = {
            'air_density': 'kg/m³',
            'specific_heat_air': 'kJ/kg·K',
            'latent_heat_vaporization': 'kJ/kg',
            'stefan_boltzmann': 'W/m²·K⁴',
            'solar_constant': 'W/m²',
            'copper_resistivity': 'Ω·m',
            'aluminum_resistivity': 'Ω·m',
            'water_density': 'kg/m³',
            'water_viscosity': 'm²/s',
            'gravity': 'm/s²'
        };
        
        return unitMap[constant] || '';
    }
    
    // Reset category
    function resetCategory() {
        const category = document.getElementById('configCategory').value;
        if (!category) {
            showAlert('Please select a category to reset', 'warning');
            return;
        }
        
        showConfirmModal('Reset Category', `Are you sure you want to reset the ${category} category to defaults?`, function() {
            const formData = new FormData();
            formData.append('action', 'reset_category');
            formData.append('category', category);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    loadConfiguration(category);
                } else {
                    showAlert('Reset failed: ' + (data.error || 'Unknown error'), 'danger');
                }
            })
            .catch(error => {
                showAlert('Error: ' + error.message, 'danger');
            });
        });
    }
    
    // Reset all configuration
    function resetAll() {
        showConfirmModal('Reset All', 'Are you sure you want to reset ALL configuration to defaults? This cannot be undone!', function() {
            const formData = new FormData();
            formData.append('action', 'reset_category'); // Use reset_category without category param
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('All configuration reset to defaults', 'success');
                    const category = document.getElementById('configCategory').value;
                    if (category) loadConfiguration(category);
                } else {
                    showAlert('Reset failed: ' + (data.error || 'Unknown error'), 'danger');
                }
            })
            .catch(error => {
                showAlert('Error: ' + error.message, 'danger');
            });
        });
    }
    
    // Show alert message
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        // Remove existing alerts
        document.querySelectorAll('.alert').forEach(alert => alert.remove());
        
        // Add new alert
        document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
    </script>
    
    <style>
    .config-form .form-group {
        margin-bottom: 1.5rem;
    }
    
    .input-group-append .btn {
        margin-left: 0.25rem;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 0.5rem;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
    
    .accordion .card {
        border: 1px solid rgba(0, 0, 0, 0.125);
        margin-bottom: 0.5rem;
    }
    
    .table-sm td, .table-sm th {
        padding: 0.3rem;
    }
    
    .modal-lg {
        max-width: 900px;
    }
    </style>
    
    <?php
    include '../../../../themes/default/views/partials/footer.php';
}
?>


