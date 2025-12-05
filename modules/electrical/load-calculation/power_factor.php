<?php
/**
 * Power Factor Calculator
 * Calculates power factor, real power, reactive power, and apparent power
 */

namespace App\Calculators;

class PowerfactorCalculator extends BaseCalculator
{
    public function getName()
    {
        return "Power Factor Calculator";
    }
    
    public function getDescription()
    {
        return "Calculate power factor, real power (kW), reactive power (kVAR), and apparent power (kVA)";
    }
    
    public function validate($input)
    {
        $errors = [];
        
        $voltage = $input['voltage'] ?? null;
        $current = $input['current'] ?? null;
        $power = $input['power'] ?? null;
        
        // Validate voltage
        if ($voltage !== null && (!is_numeric($voltage) || $voltage <= 0)) {
            $errors[] = 'Voltage must be a positive number';
        }
        
        // Validate current
        if ($current !== null && (!is_numeric($current) || $current <= 0)) {
            $errors[] = 'Current must be a positive number';
        }
        
        // Validate power
        if ($power !== null && (!is_numeric($power) || $power < 0)) {
            $errors[] = 'Power must be a non-negative number';
        }
        
        // Need at least voltage, current, and power to calculate power factor
        if ($voltage === null || $current === null || $power === null) {
            $errors[] = 'Voltage, current, and power are required';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    public function calculate($input)
    {
        $voltage = floatval($input['voltage']);
        $current = floatval($input['current']);
        $realPower = floatval($input['power']); // Real power in watts
        
        // Calculate apparent power (S = V * I)
        $apparentPower = $voltage * $current;
        
        // Calculate power factor (PF = P / S)
        $powerFactor = $apparentPower > 0 ? $realPower / $apparentPower : 0;
        
        // Calculate reactive power (Q = sqrt(S^2 - P^2))
        $reactivePower = sqrt(max(0, pow($apparentPower, 2) - pow($realPower, 2)));
        
        // Calculate phase angle
        $phaseAngle = acos($powerFactor) * (180 / pi());
        
        return [
            'voltage' => $voltage,
            'current' => $current,
            'real_power' => round($realPower, 2),
            'apparent_power' => round($apparentPower, 2),
            'reactive_power' => round($reactivePower, 2),
            'power_factor' => round($powerFactor, 4),
            'phase_angle' => round($phaseAngle, 2),
            'power_factor_percentage' => round($powerFactor * 100, 2)
        ];
    }
}
