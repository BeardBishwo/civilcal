<?php

namespace App\Controllers;

use App\Core\Controller;

class LandingController extends Controller
{
    public function civil()
    {
        // Set the view path to the landing page
        $viewPath = BASE_PATH . '/themes/default/views/landing/civil.php';
        
        // Check if the file exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to 404 if file doesn't exist
            http_response_code(404);
            echo "Page not found";
        }
    }
    
    public function electrical()
    {
        // Set the view path to the landing page
        $viewPath = BASE_PATH . '/themes/default/views/landing/electrical.php';
        
        // Check if the file exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to 404 if file doesn't exist
            http_response_code(404);
            echo "Page not found";
        }
    }
    
    public function plumbing()
    {
        // Set the view path to the landing page
        $viewPath = BASE_PATH . '/themes/default/views/landing/plumbing.php';
        
        // Check if the file exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to 404 if file doesn't exist
            http_response_code(404);
            echo "Page not found";
        }
    }
    
    public function hvac()
    {
        // Set the view path to the landing page
        $viewPath = BASE_PATH . '/themes/default/views/landing/hvac.php';
        
        // Check if the file exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to 404 if file doesn't exist
            http_response_code(404);
            echo "Page not found";
        }
    }
    
    public function fire()
    {
        // Set the view path to the landing page
        $viewPath = BASE_PATH . '/themes/default/views/landing/fire.php';
        
        // Check if the file exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to 404 if file doesn't exist
            http_response_code(404);
            echo "Page not found";
        }
    }
    
    public function site()
    {
        // Set the view path to the landing page
        $viewPath = BASE_PATH . '/themes/default/views/landing/site.php';
        
        // Check if the file exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to 404 if file doesn't exist
            http_response_code(404);
            echo "Page not found";
        }
    }
    
    public function structural()
    {
        // Set the view path to the landing page
        $viewPath = BASE_PATH . '/themes/default/views/landing/structural.php';
        
        // Check if the file exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to 404 if file doesn't exist
            http_response_code(404);
            echo "Page not found";
        }
    }
    
    public function estimation()
    {
        // Set the view path to the landing page
        $viewPath = BASE_PATH . '/themes/default/views/landing/estimation.php';
        
        // Check if the file exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to 404 if file doesn't exist
            http_response_code(404);
            echo "Page not found";
        }
    }
    
    public function management()
    {
        // Set the view path to the landing page
        $viewPath = BASE_PATH . '/themes/default/views/landing/management.php';
        
        // Check if the file exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to 404 if file doesn't exist
            http_response_code(404);
            echo "Page not found";
        }
    }
    
    public function mep()
    {
        // Set the view path to the landing page
        $viewPath = BASE_PATH . '/themes/default/views/landing/mep.php';
        
        // Check if the file exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to 404 if file doesn't exist
            http_response_code(404);
            echo "Page not found";
        }
    }
}