<?php

namespace App\Core;

/**
 * Universal Calculator Engine
 * Powers all calculators across the platform
 */
class CalculatorEngine
{
    private $precision = 10;
    private $history = [];

    /**
     * Evaluate a mathematical expression
     */
    public function evaluate($expression)
    {
        try {
            // Remove whitespace
            $expression = str_replace(' ', '', $expression);
            
            // Security: Only allow numbers, operators, and math functions
            if (!$this->isValidExpression($expression)) {
                throw new \Exception('Invalid expression');
            }

            // Replace common math functions
            $expression = $this->replaceMathFunctions($expression);
            
            // Evaluate using PHP's eval (safely)
            $result = @eval("return $expression;");
            
            if ($result === false) {
                throw new \Exception('Calculation error');
            }

            return round($result, $this->precision);
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Convert units
     */
    public function convertUnit($value, $fromUnit, $toUnit, $category)
    {
        // Load conversion factors from database
        $db = \App\Core\Database::getInstance();
        
        $from = $db->findOne('calc_units', [
            'symbol' => $fromUnit,
            'category_id' => $category
        ]);
        
        $to = $db->findOne('calc_units', [
            'symbol' => $toUnit,
            'category_id' => $category
        ]);

        if (!$from || !$to) {
            return ['error' => 'Unit not found'];
        }

        // Special handling for Temperature (Category ID 18)
        if ($category == 18) {
            return $this->convertTemperature($value, $fromUnit, $toUnit);
        }

        // Convert to base unit, then to target unit
        $baseValue = $value * $from['to_base_multiplier'];
        $result = $baseValue / $to['to_base_multiplier'];

        return round($result, $this->precision);
    }

    /**
     * Specialized conversion for temperature units
     */
    private function convertTemperature($value, $from, $to)
    {
        // First convert to Celsius (Base Unit)
        $celsius = 0;
        switch ($from) {
            case '°C': $celsius = $value; break;
            case '°F': $celsius = ($value - 32) / 1.8; break;
            case 'K':  $celsius = $value - 273.15; break;
            case '°R': $celsius = ($value / 1.8) - 273.15; break;
            default:   return ['error' => 'Source unit not supported'];
        }

        // Now convert from Celsius to target
        $result = 0;
        switch ($to) {
            case '°C': $result = $celsius; break;
            case '°F': $result = ($celsius * 1.8) + 32; break;
            case 'K':  $result = $celsius + 273.15; break;
            case '°R': $result = ($celsius + 273.15) * 1.8; break;
            default:   return ['error' => 'Target unit not supported'];
        }

        return round($result, $this->precision);
    }

    /**
     * Calculate percentage
     */
    public function percentage($value, $percent)
    {
        return ($value * $percent) / 100;
    }

    /**
     * Calculate compound interest
     */
    public function compoundInterest($principal, $rate, $time, $frequency = 1)
    {
        $amount = $principal * pow((1 + ($rate / (100 * $frequency))), ($frequency * $time));
        return round($amount, 2);
    }

    /**
     * Calculate BMI
     */
    public function calculateBMI($weight, $height)
    {
        // weight in kg, height in meters
        $bmi = $weight / ($height * $height);
        return round($bmi, 2);
    }

    /**
     * Validate expression for security
     */
    private function isValidExpression($expr)
    {
        // Only allow: numbers, operators, parentheses, dots, a-z (for math functions), and spaces
        // The hyphen must be properly handled in the character class
        $pattern = '/^[0-9+\-*\/().,\s a-z]+$/i';
        return preg_match($pattern, $expr);
    }

    /**
     * Replace math function names with PHP equivalents
     */
    private function replaceMathFunctions($expr)
    {
        // Order matters: replace longer names first, and use word boundaries for single-char constants
        $replacements = [
            'sqrt' => 'sqrt',
            'sin' => 'sin',
            'cos' => 'cos',
            'tan' => 'tan',
            'log' => 'log10',
            'ln' => 'log',
            'exp' => 'exp',
            'pow' => 'pow',
            'abs' => 'abs'
        ];

        foreach ($replacements as $search => $replace) {
            $expr = str_ireplace($search, $replace, $expr);
        }
        
        // Handle single-character constants with word boundaries to avoid breaking function names
        $expr = preg_replace('/\bpi\b/i', 'M_PI', $expr);
        $expr = preg_replace('/\be\b/i', 'M_E', $expr);

        return $expr;
    }

    /**
     * Format output with proper decimal places
     */
    public function formatOutput($value, $decimals = null)
    {
        $decimals = $decimals ?? $this->precision;
        return number_format($value, $decimals, '.', '');
    }

    /**
     * Save calculation to history
     */
    public function saveHistory($calculatorSlug, $inputs, $result, $userId = null)
    {
        $db = \App\Core\Database::getInstance();
        
        $db->insert('calc_history', [
            'user_id' => $userId ?? ($_SESSION['user_id'] ?? null),
            'calculator_slug' => $calculatorSlug,
            'inputs' => json_encode($inputs),
            'result' => $result,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get calculation history
     */
    public function getHistory($calculatorSlug = null, $userId = null, $limit = 10)
    {
        $db = \App\Core\Database::getInstance();
        
        $conditions = [];
        if ($userId) $conditions['user_id'] = $userId;
        if ($calculatorSlug) $conditions['calculator_slug'] = $calculatorSlug;
        
        return $db->find('calc_history', $conditions, 'created_at DESC', $limit);
    }
}
