<?php
/**
 * Ohm's Law Calculator
 * Calculates voltage, current, resistance, and power
 */

namespace App\Calculators;

class OhmsLawCalculator extends BaseCalculator
{
    public function getName()
    {
        return "Ohm's Law Calculator";
    }
    
    public function getDescription()
    {
        return "Calculate voltage, current, resistance, and power using Ohm's Law (V=IR, P=VI)";
    }
    
    public function validate($input)
    {
        $errors = [];
        
        $voltage = $input['voltage'] ?? null;
        $current = $input['current'] ?? null;
        $resistance = $input['resistance'] ?? null;
        
        // Check if we have at least 2 parameters
        $paramCount = 0;
        if ($voltage !== null) $paramCount++;
        if ($current !== null) $paramCount++;
        if ($resistance !== null) $paramCount++;
        
        if ($paramCount < 2) {
            $errors[] = 'Please provide at least two of: voltage, current, or resistance';
        }
        
        // Validate numeric values
        if ($voltage !== null && (!is_numeric($voltage) || $voltage < 0)) {
            $errors[] = 'Voltage must be a positive number';
        }
        if ($current !== null && (!is_numeric($current) || $current < 0)) {
            $errors[] = 'Current must be a positive number';
        }
        if ($resistance !== null && (!is_numeric($resistance) || $resistance <= 0)) {
            $errors[] = 'Resistance must be a positive number';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    public function calculate($input)
    {
        $voltage = isset($input['voltage']) ? floatval($input['voltage']) : null;
        $current = isset($input['current']) ? floatval($input['current']) : null;
        $resistance = isset($input['resistance']) ? floatval($input['resistance']) : null;
        
        if ($voltage !== null && $current !== null) {
            // Calculate resistance and power
            $resistance = $voltage / $current;
            $power = $voltage * $current;
            
            return [
                'voltage' => $voltage,
                'current' => $current,
                'resistance' => round($resistance, 4),
                'power' => round($power, 2),
                'formula_used' => 'R = V / I, P = V * I'
            ];
        } elseif ($voltage !== null && $resistance !== null) {
            // Calculate current and power
            $current = $voltage / $resistance;
            $power = $voltage * $current;
            
            return [
                'voltage' => $voltage,
                'current' => round($current, 4),
                'resistance' => $resistance,
                'power' => round($power, 2),
                'formula_used' => 'I = V / R, P = V * I'
            ];
        } elseif ($current !== null && $resistance !== null) {
            // Calculate voltage and power
            $voltage = $current * $resistance;
            $power = $voltage * $current;
            
            return [
                'voltage' => round($voltage, 2),
                'current' => $current,
                'resistance' => $resistance,
                'power' => round($power, 2),
                'formula_used' => 'V = I * R, P = V * I'
            ];
        }
        
        return [];
    }
}
