<?php

namespace App\Controllers;

use App\Core\Controller;

class ChemistryCalculatorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Molar Mass Calculator
     */
    public function molar_mass()
    {
        $this->view->render('calculators/chemistry/molar_mass', [
            'title' => 'Molar Mass Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * pH Calculator
     */
    public function ph()
    {
        $this->view->render('calculators/chemistry/ph', [
            'title' => 'pH Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Gas Laws Calculator (Boyle's, Charles's, Ideal)
     */
    public function gas_laws()
    {
        $this->view->render('calculators/chemistry/gas_laws', [
            'title' => 'Gas Laws Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }
}
