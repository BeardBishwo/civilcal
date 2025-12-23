<?php

namespace App\Services;

use PDO;

/**
 * Calculator Management Service
 * 
 * Handles CRUD operations for admin-configurable calculators
 * Loads calculator definitions from database instead of hardcoded files
 * 
 * @package App\Services
 */
class CalculatorManagement
{
    private ?PDO $db = null;
    
    public function __construct()
    {
        global $pdo;
        $this->db = $pdo ?? null;
    }
    
    /**
     * Get all calculators
     */
    public function getAllCalculators(array $filters = []): array
    {
        $sql = "SELECT * FROM calculators WHERE 1=1";
        $params = [];
        
        if (isset($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
        }
        
        if (isset($filters['is_active'])) {
            $sql .= " AND is_active = ?";
            $params[] = (int)$filters['is_active'];
        }
        
        $sql .= " ORDER BY category, order_index, name";
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    /**
     * Get calculator by ID
     */
    public function getCalculator(string $calculatorId): ?array
    {
        $sql = "SELECT * FROM calculators WHERE calculator_id = ? LIMIT 1";
        $result = $this->db->query($sql, [$calculatorId])->fetch();
        
        if (!$result) {
            return null;
        }
        
        // Parse config JSON
        $result['config'] = json_decode($result['config_json'], true);
        
        // Load inputs, outputs, formulas
        $result['inputs'] = $this->getCalculatorInputs($result['id']);
        $result['outputs'] = $this->getCalculatorOutputs($result['id']);
        $result['formulas'] = $this->getCalculatorFormulas($result['id']);
        
        return $result;
    }
    
    /**
     * Create new calculator
     */
    public function createCalculator(array $data): int
    {
        $config = $this->buildConfig($data);
        
        $sql = "INSERT INTO calculators (
            calculator_id, name, description, category, subcategory,
            version, icon, is_active, is_premium, config_json, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['calculator_id'],
            $data['name'],
            $data['description'] ?? '',
            $data['category'],
            $data['subcategory'] ?? null,
            $data['version'] ?? '1.0',
            $data['icon'] ?? null,
            $data['is_active'] ?? 1,
            $data['is_premium'] ?? 0,
            json_encode($config),
            $data['created_by'] ?? null
        ];
        
        $this->db->query($sql, $params);
        $calculatorId = $this->db->lastInsertId();
        
        // Insert inputs, outputs, formulas
        if (isset($data['inputs'])) {
            $this->saveInputs($calculatorId, $data['inputs']);
        }
        if (isset($data['outputs'])) {
            $this->saveOutputs($calculatorId, $data['outputs']);
        }
        if (isset($data['formulas'])) {
            $this->saveFormulas($calculatorId, $data['formulas']);
        }
        
        return $calculatorId;
    }
    
    /**
     * Update calculator
     */
    public function updateCalculator(string $calculatorId, array $data): bool
    {
        // Get current calculator
        $current = $this->db->query(
            "SELECT id FROM calculators WHERE calculator_id = ?",
            [$calculatorId]
        )->fetch();
        
        if (!$current) {
            return false;
        }
        
        $id = $current['id'];
        $config = $this->buildConfig($data);
        
        $sql = "UPDATE calculators SET 
            name = ?, description = ?, category = ?, subcategory = ?,
            version = ?, icon = ?, is_active = ?, is_premium = ?,
            config_json = ?, updated_by = ?
            WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['description'] ?? '',
            $data['category'],
            $data['subcategory'] ?? null,
            $data['version'] ?? '1.0',
            $data['icon'] ?? null,
            $data['is_active'] ?? 1,
            $data['is_premium'] ?? 0,
            json_encode($config),
            $data['updated_by'] ?? null,
            $id
        ];
        
        $this->db->query($sql, $params);
        
        // Update inputs, outputs, formulas
        if (isset($data['inputs'])) {
            $this->db->query("DELETE FROM calculator_inputs WHERE calculator_id = ?", [$id]);
            $this->saveInputs($id, $data['inputs']);
        }
        if (isset($data['outputs'])) {
            $this->db->query("DELETE FROM calculator_outputs WHERE calculator_id = ?", [$id]);
            $this->saveOutputs($id, $data['outputs']);
        }
        if (isset($data['formulas'])) {
            $this->db->query("DELETE FROM calculator_formulas WHERE calculator_id = ?", [$id]);
            $this->saveFormulas($id, $data['formulas']);
        }
        
        return true;
    }
    
    /**
     * Delete calculator
     */
    public function deleteCalculator(string $calculatorId): bool
    {
        $sql = "DELETE FROM calculators WHERE calculator_id = ?";
        return $this->db->query($sql, [$calculatorId])->rowCount() > 0;
    }
    
    /**
     * Generate calculator configuration from database
     * This replaces hardcoded PHP config files
     */
    public function generateConfigArray(string $calculatorId): array
    {
        if (!$this->db) {
            throw new \Exception("Database not available");
        }
        
        $calc = $this->getCalculator($calculatorId);
        
        if (!$calc) {
            throw new \Exception("Calculator not found: {$calculatorId}");
        }
        
        return [
            'name' => $calc['name'],
            'description' => $calc['description'],
            'category' => $calc['category'],
            'subcategory' => $calc['subcategory'],
            'version' => $calc['version'],
            'inputs' => array_map(function($input) {
                return [
                    'name' => $input['field_name'],
                    'type' => $input['field_type'],
                    'unit' => $input['unit'],
                    'unit_type' => $input['unit_type'],
                    'required' => (bool)$input['is_required'],
                    'label' => $input['field_label'],
                    'min' => $input['min_value'],
                    'max' => $input['max_value'],
                    'default' => $input['default_value'],
                    'placeholder' => $input['placeholder'],
                    'help_text' => $input['help_text']
                ];
            }, $calc['inputs']),
            'formulas' => $this->buildFormulasArray($calc['formulas']),
            'outputs' => array_map(function($output) {
                return [
                    'name' => $output['output_name'],
                    'label' => $output['output_label'],
                    'unit' => $output['unit'],
                    'type' => $output['output_type'],
                    'precision' => $output['precision']
                ];
            }, $calc['outputs'])
        ];
    }
    
    // Helper methods
    private function getCalculatorInputs(int $id): array
    {
        return $this->db->query(
            "SELECT * FROM calculator_inputs WHERE calculator_id = ? ORDER BY order_index",
            [$id]
        )->fetchAll();
    }
    
    private function getCalculatorOutputs(int $id): array
    {
        return $this->db->query(
            "SELECT * FROM calculator_outputs WHERE calculator_id = ? ORDER BY order_index",
            [$id]
        )->fetchAll();
    }
    
    private function getCalculatorFormulas(int $id): array
    {
        return $this->db->query(
            "SELECT * FROM calculator_formulas WHERE calculator_id = ? ORDER BY order_index",
            [$id]
        )->fetchAll();
    }
    
    private function saveInputs(int $calculatorId, array $inputs): void
    {
        $sql = "INSERT INTO calculator_inputs (
            calculator_id, field_name, field_label, field_type, unit, unit_type,
            is_required, min_value, max_value, default_value, placeholder,
            help_text, validation_pattern, options_json, order_index
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        foreach ($inputs as $index => $input) {
            $this->db->query($sql, [
                $calculatorId,
                $input['name'],
                $input['label'],
                $input['type'] ?? 'number',
                $input['unit'] ?? null,
                $input['unit_type'] ?? null,
                $input['required'] ?? 1,
                $input['min'] ?? null,
                $input['max'] ?? null,
                $input['default'] ?? null,
                $input['placeholder'] ?? null,
                $input['help_text'] ?? null,
                $input['pattern'] ?? null,
                isset($input['options']) ? json_encode($input['options']) : null,
                $index
            ]);
        }
    }
    
    private function saveOutputs(int $calculatorId, array $outputs): void
    {
        $sql = "INSERT INTO calculator_outputs (
            calculator_id, output_name, output_label, unit, output_type, precision, is_visible, order_index
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        foreach ($outputs as $index => $output) {
            $this->db->query($sql, [
                $calculatorId,
                $output['name'],
                $output['label'],
                $output['unit'] ?? null,
                $output['type'] ?? 'number',
                $output['precision'] ?? 2,
                $output['visible'] ?? 1,
                $index
            ]);
        }
    }
    
    private function saveFormulas(int $calculatorId, array $formulas): void
    {
        $sql = "INSERT INTO calculator_formulas (
            calculator_id, result_name, formula, formula_type, description, dependencies, order_index
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $index = 0;
        foreach ($formulas as $resultName => $formula) {
            $formulaString = is_callable($formula) ? 'function' : (string)$formula;
            
            $this->db->query($sql, [
                $calculatorId,
                $resultName,
                $formulaString,
                is_callable($formula) ? 'function' : 'expression',
                null,
                null,
                $index++
            ]);
        }
    }
    
    private function buildConfig(array $data): array
    {
        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'category' => $data['category'],
            'version' => $data['version'] ?? '1.0'
        ];
    }
    
    private function buildFormulasArray(array $formulaRows): array
    {
        $formulas = [];
        foreach ($formulaRows as $row) {
            $formulas[$row['result_name']] = $row['formula'];
        }
        return $formulas;
    }
}
