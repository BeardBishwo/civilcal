<?php

namespace App\Controllers;

use App\Core\Controller;

class HealthCalculatorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // BMR Calculator
    public function bmr()
    {
        $this->view->render('calculators/health/bmr', ['title' => 'BMR Calculator']);
    }

    // Calorie Calculator
    public function calorie()
    {
        $this->view->render('calculators/health/calorie', ['title' => 'Calorie Calculator']);
    }

    // Body Fat Calculator
    public function body_fat()
    {
        $this->view->render('calculators/health/body_fat', ['title' => 'Body Fat Calculator']);
    }

    // Water Intake Calculator
    public function water_intake()
    {
        $this->view->render('calculators/health/water_intake', ['title' => 'Water Intake Calculator']);
    }

    // Pregnancy Calculator
    public function pregnancy()
    {
        $this->view->render('calculators/health/pregnancy', ['title' => 'Pregnancy Calculator']);
    }

    // API: BMR Calculation
    public function api_bmr()
    {
        header('Content-Type: application/json');
        
        $weight = floatval($_POST['weight'] ?? 0);
        $height = floatval($_POST['height'] ?? 0);
        $age = intval($_POST['age'] ?? 0);
        $gender = $_POST['gender'] ?? 'male';
        
        if ($gender === 'male') {
            $bmr = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
        } else {
            $bmr = 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
        }
        
        echo json_encode([
            'success' => true,
            'bmr' => round($bmr, 2)
        ]);
    }

    // API: Body Fat Calculation (Navy Method)
    public function api_body_fat()
    {
        header('Content-Type: application/json');
        
        $gender = $_POST['gender'] ?? 'male';
        $height = floatval($_POST['height'] ?? 0);
        $waist = floatval($_POST['waist'] ?? 0);
        $neck = floatval($_POST['neck'] ?? 0);
        $hip = floatval($_POST['hip'] ?? 0);
        
        if ($gender === 'male') {
            $bodyFat = 495 / (1.0324 - 0.19077 * log10($waist - $neck) + 0.15456 * log10($height)) - 450;
        } else {
            $bodyFat = 495 / (1.29579 - 0.35004 * log10($waist + $hip - $neck) + 0.22100 * log10($height)) - 450;
        }
        
        echo json_encode([
            'success' => true,
            'bodyFat' => round($bodyFat, 2)
        ]);
    }
}
