<?php

namespace App\Controllers;

use App\Core\Controller;

class PhysicsCalculatorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // Velocity Calculator
    public function velocity()
    {
        $this->view->render('calculators/physics/velocity', ['title' => 'Velocity Calculator']);
    }

    // Force Calculator
    public function force()
    {
        $this->view->render('calculators/physics/force', ['title' => 'Force Calculator']);
    }

    // Kinetic Energy Calculator
    public function kinetic_energy()
    {
        $this->view->render('calculators/physics/kinetic_energy', ['title' => 'Kinetic Energy Calculator']);
    }

    // Power Calculator
    public function power()
    {
        $this->view->render('calculators/physics/power', ['title' => 'Power Calculator']);
    }

    // Ohms Law Calculator
    public function ohms_law()
    {
        $this->view->render('calculators/physics/ohms_law', ['title' => 'Ohms Law Calculator']);
    }

    // API: Velocity Calculation
    public function api_velocity()
    {
        header('Content-Type: application/json');
        
        $distance = floatval($_POST['distance'] ?? 0);
        $time = floatval($_POST['time'] ?? 0);
        
        $velocity = $time > 0 ? $distance / $time : 0;
        
        echo json_encode([
            'success' => true,
            'velocity' => round($velocity, 4)
        ]);
    }

    // API: Force Calculation (F = ma)
    public function api_force()
    {
        header('Content-Type: application/json');
        
        $mass = floatval($_POST['mass'] ?? 0);
        $acceleration = floatval($_POST['acceleration'] ?? 0);
        
        $force = $mass * $acceleration;
        
        echo json_encode([
            'success' => true,
            'force' => round($force, 4)
        ]);
    }

    // API: Kinetic Energy (KE = 1/2 mv²)
    public function api_kinetic_energy()
    {
        header('Content-Type: application/json');
        
        $mass = floatval($_POST['mass'] ?? 0);
        $velocity = floatval($_POST['velocity'] ?? 0);
        
        $energy = 0.5 * $mass * pow($velocity, 2);
        
        echo json_encode([
            'success' => true,
            'energy' => round($energy, 4)
        ]);
    }

    // API: Power (P = W/t)
    public function api_power()
    {
        header('Content-Type: application/json');
        
        $work = floatval($_POST['work'] ?? 0);
        $time = floatval($_POST['time'] ?? 0);
        
        $power = $time > 0 ? $work / $time : 0;
        
        echo json_encode([
            'success' => true,
            'power' => round($power, 4)
        ]);
    }

    // API: Ohm's Law
    public function api_ohms_law()
    {
        header('Content-Type: application/json');
        
        $type = $_POST['type'] ?? 'voltage';
        $value1 = floatval($_POST['value1'] ?? 0);
        $value2 = floatval($_POST['value2'] ?? 0);
        
        $result = 0;
        
        switch ($type) {
            case 'voltage': // V = IR
                $result = $value1 * $value2;
                break;
            case 'current': // I = V/R
                $result = $value2 > 0 ? $value1 / $value2 : 0;
                break;
            case 'resistance': // R = V/I
                $result = $value2 > 0 ? $value1 / $value2 : 0;
                break;
        }
        
        echo json_encode([
            'success' => true,
            'result' => round($result, 4),
            'type' => $type
        ]);
    }
}
