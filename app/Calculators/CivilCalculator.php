<?php
namespace App\Calculators;

class CivilCalculator extends BaseCalculator
{
    public function calculate($inputs)
    {
        $calculatorSlug = $this->calculatorSlug ?? 'unknown';
        
        switch ($calculatorSlug) {
            case 'concrete-volume':
                return $this->calculateConcreteVolume($inputs);
            case 'brick-quantity':
                return $this->calculateBrickQuantity($inputs);
            case 'rebar-calculation':
                return $this->calculateRebar($inputs);
            default:
                throw new \Exception("Unknown calculator: $calculatorSlug");
        }
    }
    
    public function validate($inputs)
    {
        $calculatorSlug = $this->calculatorSlug ?? 'unknown';
        
        switch ($calculatorSlug) {
            case 'concrete-volume':
                return $this->validateConcreteVolume($inputs);
            case 'brick-quantity':
                return $this->validateBrickQuantity($inputs);
            case 'rebar-calculation':
                return $this->validateRebar($inputs);
            default:
                return ['valid' => false, 'errors' => ['Calculator not found']];
        }
    }
    
    private function calculateConcreteVolume($inputs)
    {
        $length = floatval($inputs['length'] ?? 0);
        $width = floatval($inputs['width'] ?? 0);
        $depth = floatval($inputs['depth'] ?? 0);
        
        $volume = $length * $width * $depth;
        
        return [
            'volume' => $volume,
            'volume_cubic_meters' => $volume,
            'volume_cubic_feet' => $volume * 35.3147,
            'volume_cubic_yards' => $volume * 1.30795,
            'result' => $volume
        ];
    }
    
    private function validateConcreteVolume($inputs)
    {
        $errors = [];
        
        if (!isset($inputs['length']) || !is_numeric($inputs['length']) || $inputs['length'] <= 0) {
            $errors[] = 'Length must be a positive number';
        }
        
        if (!isset($inputs['width']) || !is_numeric($inputs['width']) || $inputs['width'] <= 0) {
            $errors[] = 'Width must be a positive number';
        }
        
        if (!isset($inputs['depth']) || !is_numeric($inputs['depth']) || $inputs['depth'] <= 0) {
            $errors[] = 'Depth must be a positive number';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    private function calculateBrickQuantity($inputs)
    {
        $wallLength = floatval($inputs['wall_length'] ?? 0);
        $wallHeight = floatval($inputs['wall_height'] ?? 0);
        $wallThickness = floatval($inputs['wall_thickness'] ?? 0.23);
        
        $brickLength = 0.19;
        $brickHeight = 0.09;
        $mortarThickness = 0.01;
        
        $wallArea = $wallLength * $wallHeight;
        $brickArea = ($brickLength + $mortarThickness) * ($brickHeight + $mortarThickness);
        $bricksPerSqm = 1 / $brickArea;
        $totalBricks = $wallArea * $bricksPerSqm;
        
        return [
            'total_bricks' => ceil($totalBricks),
            'bricks_per_sqm' => round($bricksPerSqm, 2),
            'wall_area' => round($wallArea, 2),
            'result' => ceil($totalBricks)
        ];
    }
    
    private function validateBrickQuantity($inputs)
    {
        $errors = [];
        
        if (!isset($inputs['wall_length']) || !is_numeric($inputs['wall_length']) || $inputs['wall_length'] <= 0) {
            $errors[] = 'Wall length must be a positive number';
        }
        
        if (!isset($inputs['wall_height']) || !is_numeric($inputs['wall_height']) || $inputs['wall_height'] <= 0) {
            $errors[] = 'Wall height must be a positive number';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    private function calculateRebar($inputs)
    {
        $barDiameter = floatval($inputs['bar_diameter'] ?? 12) / 1000;
        $barLength = floatval($inputs['bar_length'] ?? 12);
        $numberOfBars = intval($inputs['number_of_bars'] ?? 1);
        
        $steelDensity = 7850;
        $barArea = pi() * pow($barDiameter / 2, 2);
        $volumePerBar = $barArea * $barLength;
        $weightPerBar = $volumePerBar * $steelDensity;
        $totalWeight = $weightPerBar * $numberOfBars;
        
        return [
            'weight_per_bar' => round($weightPerBar, 2),
            'total_weight' => round($totalWeight, 2),
            'number_of_bars' => $numberOfBars,
            'result' => round($totalWeight, 2)
        ];
    }
    
    private function validateRebar($inputs)
    {
        $errors = [];
        
        if (!isset($inputs['bar_diameter']) || !is_numeric($inputs['bar_diameter']) || $inputs['bar_diameter'] <= 0) {
            $errors[] = 'Bar diameter must be a positive number';
        }
        
        if (!isset($inputs['bar_length']) || !is_numeric($inputs['bar_length']) || $inputs['bar_length'] <= 0) {
            $errors[] = 'Bar length must be a positive number';
        }
        
        if (!isset($inputs['number_of_bars']) || !is_numeric($inputs['number_of_bars']) || $inputs['number_of_bars'] <= 0) {
            $errors[] = 'Number of bars must be a positive integer';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
