<?php

namespace App\Controllers;

use App\Core\Controller;

class StatisticsCalculatorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Basic Statistics (Mean, Median, Mode)
     */
    public function basic()
    {
        $this->view->render('calculators/statistics/basic', [
            'title' => 'Basic Statistics',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Dispersion (Standard Deviation, Variance)
     */
    public function dispersion()
    {
        $this->view->render('calculators/statistics/dispersion', [
            'title' => 'Standard Deviation Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Probability (Permutations, Combinations)
     */
    public function probability()
    {
        $this->view->render('calculators/statistics/probability', [
            'title' => 'Probability Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }
}
