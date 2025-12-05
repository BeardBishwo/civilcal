<?php

namespace App\Calculators;

abstract class BaseCalculator
{
    protected $calculatorSlug;
    
    /**
     * Set calculator slug
     */
    public function setCalculatorSlug($slug)
    {
        $this->calculatorSlug = $slug;
        return $this;
    }
    
    /**
     * Get calculator name
     */
    public function getName()
    {
        return 'Base Calculator';
    }
    
    /**
     * Get calculator description
     */
    public function getDescription()
    {
        return 'Base calculator class';
    }
    
    /**
     * Validate input data
     * Must return ['valid' => bool, 'errors' => array]
     */
    abstract public function validate($input);
    
    /**
     * Perform calculation
     * Must return array of results
     */
    abstract public function calculate($input);
}
