<?php

namespace App\Engine;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Formula Registry - Formula execution and management
 * 
 * Executes mathematical formulas safely using Symfony ExpressionLanguage
 * 
 * @package App\Engine
 */
class FormulaRegistry
{
    private $language;

    public function __construct()
    {
        $this->language = new ExpressionLanguage();
        $this->registerFunctions();
    }

    /**
     * Execute formulas with given inputs
     * 
     * @param array $formulas Formula definitions from config
     * @param array $inputs Validated and normalized inputs
     * @return array Calculated results
     */
    public function execute(array $formulas, array $inputs): array
    {
        $results = [];
        
        // Sanitize inputs: Convert hyphens to underscores for the calculation context
        $safeInputs = [];
        foreach ($inputs as $key => $value) {
            $safeKey = str_replace('-', '_', $key);
            $safeInputs[$safeKey] = $value;
        }
        
        $context = $safeInputs; // Start context with sanitized inputs
        
        foreach ($formulas as $resultName => $formula) {
            try {
                // Ensure the current result name is also sanitized if used as a variable later
                $safeResultName = str_replace('-', '_', $resultName);
                
                $evaluated = $this->evaluateFormula($formula, $context);
                
                // If formula returns an array (group result), sanitize all its keys
                if (is_array($evaluated)) {
                    $safeGroup = [];
                    foreach ($evaluated as $k => $v) {
                        $safeK = str_replace('-', '_', $k);
                        $safeGroup[$safeK] = $v;
                        $context[$safeK] = $v;
                    }
                    $results[$resultName] = $evaluated; // Original key for output consistency
                    $context[$safeResultName] = $evaluated;
                } else {
                    $results[$resultName] = $evaluated; // Original key for output consistency
                    $context[$safeResultName] = $evaluated;
                }
            } catch (\Exception $e) {
                // Log the error but throw a user-friendly message
                error_log("Formula Execution Error [{$resultName}]: " . $e->getMessage());
                throw new \Exception("Error calculating '{$resultName}'. Please check your inputs.");
            }
        }
        
        return $results;
    }
    
    /**
     * Evaluate a single formula
     * 
     * @param mixed $formula Formula string or callable
     * @param array $context Variables available for formula
     * @return mixed Calculated result
     */
    private function evaluateFormula($formula, array $context)
    {
        // If formula is a callable, execute it
        if (is_callable($formula)) {
            return call_user_func($formula, $context);
        }
        
        // If formula is a string, parse and evaluate it
        if (is_string($formula)) {
            // Clean context: ExpressionLanguage only allows valid variable names
            // but we might have keys that are not valid variable names. 
            // In typical calculator config, keys are 'length', 'width' (valid).
            return $this->language->evaluate($formula, $context);
        }
        
        // If formula is a numeric value, return it
        if (is_numeric($formula)) {
            return $formula;
        }
        
        throw new \Exception("Invalid formula type");
    }

    /**
     * Register standard math functions
     */
    private function registerFunctions()
    {
        // Register standard PHP math functions
        $functions = [
            'abs', 'ceil', 'floor', 'round', 'max', 'min', 
            'pow', 'sqrt', 'exp', 'log', 'log10', 
            'sin', 'cos', 'tan', 'asin', 'acos', 'atan',
            'deg2rad', 'rad2deg', 'pi'
        ];

        foreach ($functions as $func) {
            $this->language->register($func, function (...$args) use ($func) {
                return sprintf('%s(%s)', $func, implode(', ', $args));
            }, function ($arguments, ...$args) use ($func) {
                return call_user_func_array($func, $args);
            });
        }
    }
}

