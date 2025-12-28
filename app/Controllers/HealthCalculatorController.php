<?php

namespace App\Controllers;

use App\Core\Controller;

class HealthCalculatorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * BMI Calculator (Body Mass Index)
     */
    public function bmi()
    {
        $this->view->render('calculators/health/bmi', [
            'title' => 'BMI Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * BMR Calculator (Basal Metabolic Rate)
     */
    public function bmr()
    {
        $this->view->render('calculators/health/bmr', [
            'title' => 'BMR Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Body Fat Calculator
     */
    public function body_fat()
    {
        $this->view->render('calculators/health/body_fat', [
            'title' => 'Body Fat Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Calorie Calculator (TDEE)
     */
    public function calories()
    {
        $this->view->render('calculators/health/calories', [
            'title' => 'Calorie Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }
}
