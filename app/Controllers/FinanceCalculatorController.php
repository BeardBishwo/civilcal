<?php

namespace App\Controllers;

use App\Core\Controller;

class FinanceCalculatorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // Mortgage Calculator
    public function mortgage()
    {
        $this->view->render('calculators/finance/mortgage', ['title' => 'Mortgage Calculator']);
    }

    // Investment Calculator
    public function investment()
    {
        $this->view->render('calculators/finance/investment', ['title' => 'Investment Calculator']);
    }

    // Compound Interest Calculator
    public function compound_interest()
    {
        $this->view->render('calculators/finance/compound_interest', ['title' => 'Compound Interest Calculator']);
    }

    // Savings Calculator
    public function savings()
    {
        $this->view->render('calculators/finance/savings', ['title' => 'Savings Calculator']);
    }

    // ROI Calculator
    public function roi()
    {
        $this->view->render('calculators/finance/roi', ['title' => 'ROI Calculator']);
    }

    // API: Mortgage Calculation
    public function api_mortgage()
    {
        header('Content-Type: application/json');
        
        $principal = floatval($_POST['principal'] ?? 0);
        $rate = floatval($_POST['rate'] ?? 0) / 100 / 12;
        $years = intval($_POST['years'] ?? 0);
        $months = $years * 12;
        
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

    // API: Compound Interest
    public function api_compound_interest()
    {
        header('Content-Type: application/json');
        
        $principal = floatval($_POST['principal'] ?? 0);
        $rate = floatval($_POST['rate'] ?? 0) / 100;
        $time = floatval($_POST['time'] ?? 0);
        $frequency = intval($_POST['frequency'] ?? 12);
        
        $amount = $principal * pow((1 + $rate / $frequency), $frequency * $time);
        $interest = $amount - $principal;
        
        echo json_encode([
            'success' => true,
            'amount' => round($amount, 2),
            'interest' => round($interest, 2)
        ]);
    }

    // API: ROI Calculation
    public function api_roi()
    {
        header('Content-Type: application/json');
        
        $investment = floatval($_POST['investment'] ?? 0);
        $return = floatval($_POST['return'] ?? 0);
        
        $roi = (($return - $investment) / $investment) * 100;
        $profit = $return - $investment;
        
        echo json_encode([
            'success' => true,
            'roi' => round($roi, 2),
            'profit' => round($profit, 2)
        ]);
    }
}
