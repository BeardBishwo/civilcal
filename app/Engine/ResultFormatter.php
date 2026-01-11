<?php

namespace App\Engine;

/**
 * Result Formatter - Standardized output formatting
 * 
 * Formats calculation results consistently:
 * - Applies precision/rounding
 * - Adds units
 * - Structures output
 * - Handles special formatting (currency, percentages, etc.)
 * 
 * @package App\Engine
 */
class ResultFormatter
{
    /**
     * Format calculation results
     * 
     * @param array $results Raw calculation results
     * @param array $outputSchema Output definitions from config
     * @return array Formatted results
     */
    public function format(array $results, array $outputSchema): array
    {
        $formatted = [];
        
        foreach ($outputSchema as $outputDef) {
            $name = $outputDef['name'];
            $value = $results[$name] ?? null;
            
            if ($value === null) {
                continue;
            }
            
            $formatted[$name] = $this->formatValue(
                $value,
                $outputDef
            );

            // Special handling for structured data like Rate Analysis breakdown
            if (($outputDef['type'] ?? '') === 'table' && is_array($value)) {
                $formatted[$name]['is_table'] = true;
                $formatted[$name]['rows'] = $value;
            }
        }
        
        return $formatted;
    }
    
    /**
     * Format a single value
     */
    private function formatValue($value, array $config): array
    {
        $precision = $config['precision'] ?? 2;
        $unit = $config['unit'] ?? '';
        $label = $config['label'] ?? ucfirst($config['name']);
        $type = $config['type'] ?? 'number';
        
        // Round to specified precision if numeric
        $formattedValue = (is_numeric($value)) ? round($value, $precision) : $value;
        
        // Apply special formatting based on type
        $displayValue = $this->applyTypeFormatting($formattedValue, $type, $config);
        
        return [
            'value' => $formattedValue,
            'display' => $displayValue,
            'unit' => $unit,
            'label' => $label,
            'formatted' => $displayValue . ($unit ? ' ' . $unit : '')
        ];
    }
    
    /**
     * Apply type-specific formatting
     */
    private function applyTypeFormatting($value, string $type, array $config): string
    {
        switch ($type) {
            case 'currency':
                $currency = $config['currency'] ?? 'USD';
                $symbol = $this->getCurrencySymbol($currency);
                return $symbol . number_format($value, 2);
                
            case 'percentage':
                return number_format($value, $config['precision'] ?? 1) . '%';
                
            case 'integer':
                return number_format($value, 0);
                
            case 'scientific':
                return is_numeric($value) ? sprintf('%.2e', $value) : $value;
                
            case 'string':
                return (string)$value;
                
            case 'table':
                return 'Table Data'; // Label for structured output
                
            case 'number':
            default:
                $precision = $config['precision'] ?? 2;
                return is_numeric($value) ? number_format($value, $precision) : (string)$value;
        }
    }
    
    /**
     * Get currency symbol
     */
    private function getCurrencySymbol(string $currency): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            'NPR' => 'Rs.',
            'JPY' => '¥',
            'CNY' => '¥',
        ];
        
        return $symbols[$currency] ?? $currency . ' ';
    }
    
    /**
     * Format results for JSON API response
     */
    public function formatForApi(array $results, array $outputSchema): array
    {
        $formatted = $this->format($results, $outputSchema);
        
        $apiResponse = [];
        foreach ($formatted as $name => $data) {
            $apiResponse[$name] = [
                'value' => $data['value'],
                'unit' => $data['unit'],
                'display' => $data['formatted']
            ];
        }
        
        return $apiResponse;
    }
    
    /**
     * Format results for human-readable display
     */
    public function formatForDisplay(array $results, array $outputSchema): array
    {
        $formatted = $this->format($results, $outputSchema);
        
        $display = [];
        foreach ($formatted as $name => $data) {
            $display[] = [
                'label' => $data['label'],
                'value' => $data['formatted']
            ];
        }
        
        return $display;
    }
}
