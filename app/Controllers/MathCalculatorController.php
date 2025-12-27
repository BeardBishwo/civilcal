<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CalculatorEngine;

class MathCalculatorController extends Controller
{
    private $engine;

    public function __construct()
    {
        parent::__construct();
        $this->engine = new CalculatorEngine();
    }

    /**
     * Percentage Calculator
     */
    public function percentage()
    {
        $this->view->render('calculators/math/percentage', [
            'title' => 'Percentage Calculator'
        ]);
    }

    /**
     * Fraction Calculator
     */
    public function fraction()
    {
        $this->view->render('calculators/math/fraction', [
            'title' => 'Fraction Calculator'
        ]);
    }

    /**
     * Ratio Calculator
     */
    public function ratio()
    {
        $this->view->render('calculators/math/ratio', [
            'title' => 'Ratio Calculator'
        ]);
    }

    /**
     * Square Root Calculator
     */
    public function square_root()
    {
        $this->view->render('calculators/math/square_root', [
            'title' => 'Square Root Calculator'
        ]);
    }

    /**
     * Exponent Calculator
     */
    public function exponent()
    {
        $this->view->render('calculators/math/exponent', [
            'title' => 'Exponent Calculator'
        ]);
    }

    /**
     * API: Calculate Percentage
     */
    public function api_percentage()
    {
        header('Content-Type: application/json');
        
        $type = $_POST['type'] ?? 'what_is';
        $value1 = floatval($_POST['value1'] ?? 0);
        $value2 = floatval($_POST['value2'] ?? 0);
        
        $result = 0;
        
        switch ($type) {
            case 'what_is': // What is X% of Y?
                $result = ($value1 / 100) * $value2;
                break;
            case 'is_what_percent': // X is what % of Y?
                $result = ($value1 / $value2) * 100;
                break;
            case 'percent_change': // % change from X to Y
                $result = (($value2 - $value1) / $value1) * 100;
                break;
        }
        
        echo json_encode(['success' => true, 'result' => round($result, 4)]);
    }

    /**
     * API: Calculate Fraction
     */
    public function api_fraction()
    {
        header('Content-Type: application/json');
        
        $operation = $_POST['operation'] ?? 'add';
        $n1 = intval($_POST['numerator1'] ?? 0);
        $d1 = intval($_POST['denominator1'] ?? 1);
        $n2 = intval($_POST['numerator2'] ?? 0);
        $d2 = intval($_POST['denominator2'] ?? 1);
        
        $resultN = 0;
        $resultD = 1;
        
        switch ($operation) {
            case 'add':
                $resultN = ($n1 * $d2) + ($n2 * $d1);
                $resultD = $d1 * $d2;
                break;
            case 'subtract':
                $resultN = ($n1 * $d2) - ($n2 * $d1);
                $resultD = $d1 * $d2;
                break;
            case 'multiply':
                $resultN = $n1 * $n2;
                $resultD = $d1 * $d2;
                break;
            case 'divide':
                $resultN = $n1 * $d2;
                $resultD = $d1 * $n2;
                break;
        }
        
        // Simplify fraction
        $gcd = $this->gcd(abs($resultN), abs($resultD));
        $resultN /= $gcd;
        $resultD /= $gcd;
        
        echo json_encode([
            'success' => true,
            'numerator' => $resultN,
            'denominator' => $resultD,
            'decimal' => $resultD != 0 ? round($resultN / $resultD, 6) : 0
        ]);
    }

    /**
     * Greatest Common Divisor
     */
    private function gcd($a, $b)
    {
        return $b == 0 ? $a : $this->gcd($b, $a % $b);
    }

    /**
     * BMI Calculator
     */
    public function bmi()
    {
        $this->view->render('calculators/math/bmi', [
            'title' => 'BMI Calculator'
        ]);
    }

    /**
     * Loan Calculator
     */
    public function loan()
    {
        $this->view->render('calculators/math/loan', [
            'title' => 'Loan Calculator'
        ]);
    }

    /**
     * Age Calculator
     */
    public function age()
    {
        $this->view->render('calculators/math/age', [
            'title' => 'Age Calculator'
        ]);
    }

    /**
     * Area Calculator
     */
    public function area()
    {
        $this->view->render('calculators/math/area', [
            'title' => 'Area Calculator'
        ]);
    }

    /**
     * Statistics Calculator
     */
    public function statistics()
    {
        $this->view->render('calculators/math/statistics', [
            'title' => 'Statistics Calculator'
        ]);
    }

    /**
     * API: Calculate BMI
     */
    public function api_bmi()
    {
        header('Content-Type: application/json');
        
        $weight = floatval($_POST['weight'] ?? 0);
        $height = floatval($_POST['height'] ?? 0);
        $unit = $_POST['unit'] ?? 'metric';
        
        if ($unit === 'imperial') {
            // Convert pounds to kg, inches to meters
            $weight = $weight * 0.453592;
            $height = ($height * 0.0254);
        } else {
            $height = $height / 100; // cm to m
        }
        
        $bmi = $weight / ($height * $height);
        
        $category = '';
        if ($bmi < 18.5) $category = 'Underweight';
        elseif ($bmi < 25) $category = 'Normal';
        elseif ($bmi < 30) $category = 'Overweight';
        else $category = 'Obese';
        
        echo json_encode([
            'success' => true,
            'bmi' => round($bmi, 2),
            'category' => $category
        ]);
    }

    /**
     * API: Calculate Loan
     */
    public function api_loan()
    {
        header('Content-Type: application/json');
        
        $principal = floatval($_POST['principal'] ?? 0);
        $rate = floatval($_POST['rate'] ?? 0) / 100 / 12; // Monthly rate
        $months = intval($_POST['months'] ?? 0);
        
        if ($rate == 0) {
            $monthly = $principal / $months;
        } else {
            $monthly = $principal * ($rate * pow(1 + $rate, $months)) / (pow(1 + $rate, $months) - 1);
        }
        
        $total = $monthly * $months;
        $interest = $total - $principal;
        
        echo json_encode([
            'success' => true,
            'monthly' => round($monthly, 2),
            'total' => round($total, 2),
            'interest' => round($interest, 2)
        ]);
    }

    /**
     * API: Calculate Statistics
     */
    public function api_statistics()
    {
        header('Content-Type: application/json');
        
        $numbers = json_decode($_POST['numbers'] ?? '[]');
        
        if (empty($numbers)) {
            echo json_encode(['success' => false, 'error' => 'No numbers provided']);
            return;
        }
        
        sort($numbers);
        $count = count($numbers);
        $sum = array_sum($numbers);
        $mean = $sum / $count;
        
        // Median
        $middle = floor($count / 2);
        if ($count % 2 == 0) {
            $median = ($numbers[$middle - 1] + $numbers[$middle]) / 2;
        } else {
            $median = $numbers[$middle];
        }
        
        // Standard Deviation
        $variance = 0;
        foreach ($numbers as $num) {
            $variance += pow($num - $mean, 2);
        }
        $variance /= $count;
        $stdDev = sqrt($variance);
        
        echo json_encode([
            'success' => true,
            'count' => $count,
            'sum' => round($sum, 2),
            'mean' => round($mean, 4),
            'median' => round($median, 4),
            'min' => $numbers[0],
            'max' => $numbers[$count - 1],
            'stdDev' => round($stdDev, 4)
        ]);
    }

    // GCD/LCM Calculator
    public function gcd_lcm()
    {
        $this->view->render('calculators/math/gcd_lcm', ['title' => 'GCD/LCM Calculator']);
    }

    // Quadratic Equation Calculator
    public function quadratic()
    {
        $this->view->render('calculators/math/quadratic', ['title' => 'Quadratic Equation Calculator']);
    }

    // Pythagorean Theorem Calculator
    public function pythagorean()
    {
        $this->view->render('calculators/math/pythagorean', ['title' => 'Pythagorean Theorem Calculator']);
    }

    // Discount Calculator
    public function discount()
    {
        $this->view->render('calculators/math/discount', ['title' => 'Discount Calculator']);
    }

    // API: GCD/LCM
    public function api_gcd_lcm()
    {
        header('Content-Type: application/json');
        
        $num1 = intval($_POST['num1'] ?? 0);
        $num2 = intval($_POST['num2'] ?? 0);
        
        $gcd = $this->gcd(abs($num1), abs($num2));
        $lcm = ($num1 * $num2) / $gcd;
        
        echo json_encode([
            'success' => true,
            'gcd' => abs($gcd),
            'lcm' => abs($lcm)
        ]);
    }

    // API: Quadratic Equation
    public function api_quadratic()
    {
        header('Content-Type: application/json');
        
        $a = floatval($_POST['a'] ?? 0);
        $b = floatval($_POST['b'] ?? 0);
        $c = floatval($_POST['c'] ?? 0);
        
        $discriminant = ($b * $b) - (4 * $a * $c);
        
        if ($discriminant < 0) {
            echo json_encode([
                'success' => true,
                'type' => 'complex',
                'message' => 'No real solutions'
            ]);
            return;
        }
        
        $x1 = (-$b + sqrt($discriminant)) / (2 * $a);
        $x2 = (-$b - sqrt($discriminant)) / (2 * $a);
        
        echo json_encode([
            'success' => true,
            'type' => 'real',
            'x1' => round($x1, 4),
            'x2' => round($x2, 4),
            'discriminant' => round($discriminant, 4)
        ]);
    }

    // API: Pythagorean Theorem
    public function api_pythagorean()
    {
        header('Content-Type: application/json');
        
        $a = floatval($_POST['a'] ?? 0);
        $b = floatval($_POST['b'] ?? 0);
        $c = floatval($_POST['c'] ?? 0);
        
        $result = 0;
        $solving = $_POST['solving'] ?? 'c';
        
        if ($solving === 'c') {
            $result = sqrt(($a * $a) + ($b * $b));
        } elseif ($solving === 'a') {
            $result = sqrt(($c * $c) - ($b * $b));
        } else {
            $result = sqrt(($c * $c) - ($a * $a));
        }
        
        echo json_encode([
            'success' => true,
            'result' => round($result, 4)
        ]);
    }

    // API: Discount
    public function api_discount()
    {
        header('Content-Type: application/json');
        
        $price = floatval($_POST['price'] ?? 0);
        $discount = floatval($_POST['discount'] ?? 0);
        
        $discountAmount = ($price * $discount) / 100;
        $finalPrice = $price - $discountAmount;
        
        echo json_encode([
            'success' => true,
            'discountAmount' => round($discountAmount, 2),
            'finalPrice' => round($finalPrice, 2),
            'savings' => round($discountAmount, 2)
        ]);
    }
}
