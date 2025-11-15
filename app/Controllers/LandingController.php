<?php

namespace App\Controllers;

use App\Core\Controller;

class LandingController extends Controller
{
    public function civil()
    {
        // Render the landing page using the View system so layout is applied
        return $this->view->render('landing/civil', [
            'page_title' => 'Civil Engineering Toolkit',
        ]);
    }
    
    public function electrical()
    {
        // Render the landing page using the View system so layout is applied
        return $this->view->render('landing/electrical', [
            'page_title' => 'Electrical Engineering Toolkit',
        ]);
    }
    
    public function plumbing()
    {
        // Render the landing page using the View system so layout is applied
        return $this->view->render('landing/plumbing', [
            'page_title' => 'Plumbing & HVAC Toolkit',
        ]);
    }
    
    public function hvac()
    {
        // Render the landing page using the View system so layout is applied
        return $this->view->render('landing/hvac', [
            'page_title' => 'HVAC Toolkit',
        ]);
    }
    
    public function fire()
    {
        // Render the landing page using the View system so layout is applied
        return $this->view->render('landing/fire', [
            'page_title' => 'Fire Protection Toolkit',
        ]);
    }
    
    public function site()
    {
        // Render the landing page using the View system so layout is applied
        return $this->view->render('landing/site', [
            'page_title' => 'Site Engineering Toolkit',
        ]);
    }
    
    public function structural()
    {
        // Render the landing page using the View system so layout is applied
        return $this->view->render('landing/structural', [
            'page_title' => 'Structural Engineering Toolkit',
        ]);
    }
    
    public function estimation()
    {
        // Render the landing page using the View system so layout is applied
        return $this->view->render('landing/estimation', [
            'page_title' => 'Estimation Toolkit',
        ]);
    }
    
    public function management()
    {
        // Render the landing page using the View system so layout is applied
        return $this->view->render('landing/management', [
            'page_title' => 'Project Management Toolkit',
        ]);
    }
    
    public function mep()
    {
        // Render the landing page using the View system so layout is applied
        return $this->view->render('landing/mep', [
            'page_title' => 'MEP Engineering Toolkit',
        ]);
    }
}