<?php

namespace App\Controllers;

use App\Core\Controller;

class PhysicsCalculatorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Velocity Calculator
     */
    public function velocity()
    {
        $this->view->render('calculators/physics/velocity', [
            'title' => 'Velocity Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Force Calculator (Newton's 2nd Law)
     */
    public function force()
    {
        $this->view->render('calculators/physics/force', [
            'title' => 'Force Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Ohm's Law Calculator
     */
    public function ohms_law()
    {
        $this->view->render('calculators/physics/ohms_law', [
            'title' => 'Ohm\'s Law Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Energy Calculator (Kinetic & Potential)
     */
    public function energy()
    {
        $this->view->render('calculators/physics/energy', [
            'title' => 'Energy Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }
}
