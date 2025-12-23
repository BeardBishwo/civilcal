<?php
/**
 * Voltage Drop Wire Sizing Calculator - Migrated to Calculator Engine
 * 
 * This file now uses the new Calculator Engine while maintaining
 * the same URL and user experience as the original implementation.
 * 
 * Old URL: /electrical/voltage-drop/voltage-drop-sizing
 * New Backend: Uses CalculatorEngine
 */

// Bootstrap application
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

// Load shared template
require_once dirname(__DIR__) . '/shared/calculator-template.php';

// Render calculator
renderCalculator('voltage-drop-sizing');
