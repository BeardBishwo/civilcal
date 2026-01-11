<?php

namespace App\Core;

/**
 * Input Validation and Sanitization Class
 * 
 * Provides comprehensive validation and sanitization methods
 * to prevent XSS, SQL Injection, and data corruption.
 * 
 * @package App\Core
 */
class Validator
{
    /**
     * Sanitize input based on type
     * 
     * @param mixed $input Input to sanitize
     * @param string $type Type of sanitization (string, email, int, float, url, html)
     * @return mixed Sanitized input
     */
    public static function sanitize($input, $type = 'string')
    {
        if (is_null($input)) {
            return null;
        }

        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
                
            case 'int':
            case 'integer':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
                
            case 'float':
            case 'numeric':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
                
            case 'html':
                // Allow HTML but strip dangerous tags
                return strip_tags($input, '<p><br><strong><em><ul><ol><li><a><h1><h2><h3><h4><h5><h6>');
                
            case 'string':
            default:
                // Remove HTML tags and encode special characters
                return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * Sanitize an array of data based on rules
     * 
     * @param array $data Data to sanitize
     * @param array $rules Sanitization rules ['field' => 'type']
     * @return array Sanitized data
     */
    public static function sanitizeArray(array $data, array $rules)
    {
        $sanitized = [];
        
        foreach ($rules as $field => $type) {
            if (isset($data[$field])) {
                $sanitized[$field] = self::sanitize($data[$field], $type);
            }
        }
        
        return $sanitized;
    }

    /**
     * Validate data against rules
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validate(array $data, array $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            
            foreach ($ruleList as $rule) {
                // Parse rule with parameters (e.g., "min:3")
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;
                
                $value = $data[$field] ?? null;
                
                // Apply validation rule
                $result = self::applyRule($value, $ruleName, $ruleParam, $field);
                
                if ($result !== true) {
                    $errors[$field] = $result;
                    break; // Stop at first error for this field
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Apply a single validation rule
     * 
     * @param mixed $value Value to validate
     * @param string $rule Rule name
     * @param mixed $param Rule parameter
     * @param string $field Field name for error message
     * @return bool|string True if valid, error message if invalid
     */
    private static function applyRule($value, $rule, $param = null, $field = 'Field')
    {
        switch ($rule) {
            case 'required':
                return self::required($value) ? true : "$field is required";
                
            case 'email':
                return self::email($value) ? true : "$field must be a valid email address";
                
            case 'min':
                return self::min($value, $param) ? true : "$field must be at least $param characters";
                
            case 'max':
                return self::max($value, $param) ? true : "$field must not exceed $param characters";
                
            case 'numeric':
                return self::numeric($value) ? true : "$field must be numeric";
                
            case 'alpha':
                return self::alpha($value) ? true : "$field must contain only letters";
                
            case 'alphanumeric':
                return self::alphanumeric($value) ? true : "$field must contain only letters and numbers";
                
            case 'url':
                return self::url($value) ? true : "$field must be a valid URL";
                
            case 'integer':
            case 'int':
                return self::integer($value) ? true : "$field must be an integer";
                
            case 'boolean':
            case 'bool':
                return self::boolean($value) ? true : "$field must be true or false";
                
            case 'array':
                return is_array($value) ? true : "$field must be an array";
                
            case 'json':
                return self::json($value) ? true : "$field must be valid JSON";
                
            default:
                return true; // Unknown rule, pass validation
        }
    }

    /**
     * Check if value is not empty
     */
    public static function required($value)
    {
        if (is_null($value)) {
            return false;
        }
        
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        
        if (is_array($value) && empty($value)) {
            return false;
        }
        
        return true;
    }

    /**
     * Validate email address
     */
    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check minimum length
     */
    public static function min($value, $min)
    {
        if (is_numeric($value)) {
            return $value >= $min;
        }
        
        return strlen($value) >= $min;
    }

    /**
     * Check maximum length
     */
    public static function max($value, $max)
    {
        if (is_numeric($value)) {
            return $value <= $max;
        }
        
        return strlen($value) <= $max;
    }

    /**
     * Check if value is numeric
     */
    public static function numeric($value)
    {
        return is_numeric($value);
    }

    /**
     * Check if value contains only letters
     */
    public static function alpha($value)
    {
        return preg_match('/^[a-zA-Z]+$/', $value) === 1;
    }

    /**
     * Check if value contains only letters and numbers
     */
    public static function alphanumeric($value)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $value) === 1;
    }

    /**
     * Validate URL
     */
    public static function url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Check if value is an integer
     */
    public static function integer($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Check if value is boolean
     */
    public static function boolean($value)
    {
        return is_bool($value) || in_array($value, [0, 1, '0', '1', 'true', 'false'], true);
    }

    /**
     * Check if value is valid JSON
     */
    public static function json($value)
    {
        if (!is_string($value)) {
            return false;
        }
        
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Validate CSRF token
     * 
     * @param string $token Token to validate
     * @return bool True if valid
     */
    public static function csrf($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Deep XSS cleaning (removes all HTML and JavaScript)
     * 
     * @param string $input Input to clean
     * @return string Cleaned input
     */
    public static function xssClean($input)
    {
        // Remove all HTML tags
        $input = strip_tags($input);
        
        // Remove any remaining script tags or event handlers
        $input = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $input);
        $input = preg_replace('/on\w+\s*=\s*["\'].*?["\']/i', '', $input);
        
        // Encode special characters
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }

    /**
     * Generate CSRF token
     * 
     * @return string Generated token
     */
    public static function generateCsrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate phone number (basic international format)
     * 
     * @param string $phone Phone number
     * @return bool True if valid
     */
    public static function phone($phone)
    {
        // Remove common separators
        $phone = preg_replace('/[\s\-\(\)]+/', '', $phone);
        
        // Check if it's a valid phone number (10-15 digits, optional + prefix)
        return preg_match('/^\+?[0-9]{10,15}$/', $phone) === 1;
    }

    /**
     * Validate date format
     * 
     * @param string $date Date string
     * @param string $format Expected format (default: Y-m-d)
     * @return bool True if valid
     */
    public static function date($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
