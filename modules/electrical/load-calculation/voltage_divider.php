<?php
/**
 * Voltage Divider Calculator
 * Calculates output voltage in a voltage divider circuit
 */

namespace App\Calculators;

class VoltagedividerCalculator extends BaseCalculator
{
    public function getName()
    {
        return "Voltage Divider Calculator";
    }
    
    public function getDescription()
    {
        return "Calculate output voltage in a voltage divider circuit using two resistors";
    }
    
    public function validate($input)
    {
        $errors = [];
        
        $voltageIn = $input['voltage_in'] ?? null;
        $resistor1 = $input['resistor1'] ?? null;
        $resistor2 = $input['resistor2'] ?? null;
        
        // Check all required fields
        if ($voltageIn === null) {
            $errors[] = 'Input voltage (voltage_in) is required';
        } elseif (!is_numeric($voltageIn) || $voltageIn < 0) {
            $errors[] = 'Input voltage must be a non-negative number';
        }
        
        if ($resistor1 === null) {
            $errors[] = 'Resistor 1 (resistor1) is required';
        } elseif (!is_numeric($resistor1) || $resistor1 <= 0) {
            $errors[] = 'Resistor 1 must be a positive number';
        }
        
        if ($resistor2 === null) {
            $errors[] = 'Resistor 2 (resistor2) is required';
        } elseif (!is_numeric($resistor2) || $resistor2 <= 0) {
            $errors[] = 'Resistor 2 must be a positive number';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    public function calculate($input)
    {
        $voltageIn = floatval($input['voltage_in']);
        $resistor1 = floatval($input['resistor1']);
        $resistor2 = floatval($input['resistor2']);
        
        // Voltage divider formula: Vout = Vin * (R2 / (R1 + R2))
        $totalResistance = $resistor1 + $resistor2;
        $voltageOut = $voltageIn * ($resistor2 / $totalResistance);
        
        // Calculate current through the divider
        $current = $voltageIn / $totalResistance;
        
        // Calculate power dissipation in each resistor
        $powerR1 = $current * $current * $resistor1;
        $powerR2 = $current * $current * $resistor2;
        $totalPower = $powerR1 + $powerR2;
        
        return [
            'voltage_in' => $voltageIn,
            'voltage_out' => round($voltageOut, 4),
            'resistor1' => $resistor1,
            'resistor2' => $resistor2,
            'total_resistance' => $totalResistance,
            'current' => round($current, 6),
            'power_r1' => round($powerR1, 4),
            'power_r2' => round($powerR2, 4),
            'total_power' => round($totalPower, 4)
        ];
    }
}
