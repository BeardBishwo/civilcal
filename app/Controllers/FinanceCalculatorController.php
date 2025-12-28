<?php

namespace App\Controllers;

use App\Core\Controller;

class FinanceCalculatorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Loan Calculator
     */
    public function loan()
    {
        $this->view->render('calculators/finance/loan', [
            'title' => 'Loan Calculator'
        ]);
    }

    /**
     * Investment Calculator
     */
    public function investment()
    {
        $this->view->render('calculators/finance/investment', [
            'title' => 'Investment Calculator'
        ]);
    }

    /**
     * Salary Calculator
     */
    public function salary()
    {
        $this->view->render('calculators/finance/salary', [
            'title' => 'Salary Calculator'
        ]);
    }
}
