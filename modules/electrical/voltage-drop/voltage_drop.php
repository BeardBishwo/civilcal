<?php
namespace App\Calculators;

// Ensure BaseCalculator is loaded
if (!class_exists('App\Calculators\BaseCalculator')) {
    require_once __DIR__ . '/../../../app/Calculators/BaseCalculator.php';
}

/**
 * Generic Voltage Drop Calculator
 * Handles basic voltage drop calculations
 */
class VoltageDropCalculator extends BaseCalculator
{
    public function validate($inputData)
    {
        $errors = [];
        
        if (!isset($inputData['current']) || !is_numeric($inputData['current'])) {
            $errors[] = 'Current must be a numeric value';
        } elseif ($inputData['current'] < 0) {
            $errors[] = 'Current cannot be negative';
        }
        
        if (!isset($inputData['length']) || !is_numeric($inputData['length'])) {
            $errors[] = 'Length must be a numeric value';
        } elseif ($inputData['length'] <= 0) {
            $errors[] = 'Length must be greater than zero';
        }
        
        if (!isset($inputData['resistance']) || !is_numeric($inputData['resistance'])) {
            $errors[] = 'Resistance must be a numeric value';
        } elseif ($inputData['resistance'] < 0) {
            $errors[] = 'Resistance cannot be negative';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    public function calculate($inputData)
    {
        $current = floatval($inputData['current']);
        $length = floatval($inputData['length']);
        $resistance = floatval($inputData['resistance']);
        
        // Voltage drop formula: VD = 2 × L × I × R / 1000
        // Where:
        // VD = Voltage Drop (volts)
        // L = One-way length of circuit (meters)
        // I = Current (amperes)
        // R = Resistance per unit length (ohms per meter)
        // 2 = Multiplier for two-way circuit (supply and return)
        
        $voltageDrop = 2 * $length * $current * $resistance / 1000;
        
        return [
            'result' => round($voltageDrop, 4),
            'voltage_drop' => round($voltageDrop, 4),
            'current' => $current,
            'length' => $length,
            'resistance' => $resistance,
            'formula' => 'VD = 2 × L × I × R / 1000',
            'unit' => 'volts'
        ];
    }
}
