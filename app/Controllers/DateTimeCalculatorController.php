<?php

namespace App\Controllers;

use App\Core\Controller;

class DateTimeCalculatorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Date Duration Calculator (Difference between two dates)
     */
    public function duration()
    {
        $this->view->render('calculators/datetime/duration', [
            'title' => 'Date Duration Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Date Adder (Add/Subtract days/weeks/months)
     */
    public function adder()
    {
        $this->view->render('calculators/datetime/adder', [
            'title' => 'Date Calculator (Add/Subtract)',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Work Day Calculator
     */
    public function workdays()
    {
        $this->view->render('calculators/datetime/workdays', [
            'title' => 'Work Day Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * Time Duration Calculator (Hours between times)
     */
    public function time()
    {
        $this->view->render('calculators/datetime/time', [
            'title' => 'Time Duration Calculator',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * API: Calculate Date Duration
     */
    public function api_duration()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $start = new \DateTime($input['start_date']);
        $end = new \DateTime($input['end_date']);
        
        $diff = $start->diff($end);
        
        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;
        $total_days = $diff->days;
        
        $this->json([
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'total_days' => $total_days,
            'hours' => $total_days * 24,
            'minutes' => $total_days * 1440
        ]);
    }
    
    /**
     * API: Date Adder
     */
    public function api_adder()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $date = new \DateTime($input['start_date']);
        $operation = $input['operation'] ?? 'add'; // add or sub
        
        $interval_spec = 'P';
        if (!empty($input['years'])) $interval_spec .= $input['years'] . 'Y';
        if (!empty($input['months'])) $interval_spec .= $input['months'] . 'M';
        if (!empty($input['days'])) $interval_spec .= $input['days'] . 'D';
        
        // If nothing added, force P0D
        if ($interval_spec === 'P') $interval_spec = 'P0D';
        
        $interval = new \DateInterval($interval_spec);
        
        if ($operation === 'sub') {
            $date->sub($interval);
        } else {
            $date->add($interval);
        }
        
        $this->json([
            'result_date' => $date->format('Y-m-d'),
            'result_formatted' => $date->format('l, F j, Y')
        ]);
    }

    /**
     * Nepali Date Converter (BS <-> AD)
     */
    public function nepali()
    {
        $this->view->render('calculators/datetime/nepali', [
            'title' => 'Nepali Date Converter (BS <-> AD)',
            'categories' => $this->getConverterCategories()
        ]);
    }

    /**
     * API: Nepali Converter
     */
    public function api_nepali()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? 'ad_to_bs';
        $year = $input['year'];
        $month = $input['month'];
        $day = $input['day'];
        
        require_once __DIR__ . '/../Helpers/NepaliCalendar.php';
        
        if ($type === 'ad_to_bs') {
            $result = \App\Helpers\NepaliCalendar::adToBs($year, $month, $day);
        } else {
            $result = \App\Helpers\NepaliCalendar::bsToAd($year, $month, $day);
        }
        
        $this->json($result);
    }

    /**
     * Helper to get categories for sidebar
     */
    private function getConverterCategories()
    {
        $db = \App\Core\Database::getInstance();
        return $db->findAll('calc_categories', ['type' => 'unit'], 'sort_order ASC');
    }
}
