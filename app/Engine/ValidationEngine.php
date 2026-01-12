<?php

namespace App\Engine;

/**
 * Validation Engine - Input validation for calculators
 * 
 * Validates user inputs against calculator schemas
 * Supports: required fields, types, ranges, patterns, custom rules
 * 
 * @package App\Engine
 */
class ValidationEngine
{
    /**
     * Validate inputs against schema
     * 
     * @param array $inputs User input data
     * @param array $schema Input schema from calculator config
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validate(array $inputs, array $schema): array
    {
        $errors = [];
        
        foreach ($schema as $fieldSchema) {
            $name = $fieldSchema['name'];
            $value = $inputs[$name] ?? null;
            
            // Required field check
            if (($fieldSchema['required'] ?? false) && ($value === null || $value === '')) {
                $errors[$name] = ucfirst($name) . ' is required';
                continue;
            }
            
            // Skip further validation if not required and empty
            if ($value === null || $value === '') {
                continue;
            }
            
            // Type validation
            $typeError = $this->validateType($value, $fieldSchema['type'] ?? 'string', $name);
            if ($typeError) {
                $errors[$name] = $typeError;
                continue;
            }
            
            // Range validation (for numbers)
            if (isset($fieldSchema['min']) || isset($fieldSchema['max'])) {
                $rangeError = $this->validateRange($value, $fieldSchema, $name);
                if ($rangeError) {
                    $errors[$name] = $rangeError;
                }
            }
            
            // Pattern validation (for strings)
            if (isset($fieldSchema['pattern'])) {
                $patternError = $this->validatePattern($value, $fieldSchema['pattern'], $name);
                if ($patternError) {
                    $errors[$name] = $patternError;
                }
            }
            
            // Custom validation function
            if (isset($fieldSchema['validator']) && is_callable($fieldSchema['validator'])) {
                $customError = $fieldSchema['validator']($value, $inputs);
                if ($customError) {
                    $errors[$name] = $customError;
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validate field type
     */
    private function validateType($value, string $type, string $fieldName): ?string
    {
        switch ($type) {
            case 'number':
            case 'float':
            case 'double':
                if (!is_numeric($value)) {
                    return ucfirst($fieldName) . ' must be a number';
                }
                break;
                
            case 'integer':
            case 'int':
                if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                    return ucfirst($fieldName) . ' must be an integer';
                }
                break;
                
            case 'string':
                if (!is_string($value)) {
                    return ucfirst($fieldName) . ' must be a string';
                }
                break;
                
            case 'boolean':
            case 'bool':
                if (!is_bool($value) && !in_array($value, [0, 1, '0', '1', 'true', 'false'], true)) {
                    return ucfirst($fieldName) . ' must be a boolean';
                }
                break;
                
            case 'array':
                if (!is_array($value)) {
                    return ucfirst($fieldName) . ' must be an array';
                }
                break;
        }
        
        return null;
    }
    
    /**
     * Validate numeric range
     */
    private function validateRange($value, array $schema, string $fieldName): ?string
    {
        $numValue = (float) $value;
        
        if (isset($schema['min']) && $numValue < $schema['min']) {
            return ucfirst($fieldName) . ' must be at least ' . $schema['min'];
        }
        
        if (isset($schema['max']) && $numValue > $schema['max']) {
            return ucfirst($fieldName) . ' must be at most ' . $schema['max'];
        }
        
        return null;
    }
    
    /**
     * Validate against regex pattern
     */
    private function validatePattern($value, string $pattern, string $fieldName): ?string
    {
        if (!preg_match($pattern, $value)) {
            return ucfirst($fieldName) . ' format is invalid';
        }
        
        return null;
    }
}
