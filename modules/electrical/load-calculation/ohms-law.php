<?php
/**
 * Ohm's Law Calculator - Migrated to Calculator Engine
 * 
 * This file now uses the new Calculator Engine while maintaining
 * the same URL and user experience as the original implementation.
 * 
 * Old URL: /electrical/load-calculation/ohms-law
 * New Backend: Uses CalculatorEngine
 */

// Bootstrap application
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

// Load shared template
require_once dirname(__DIR__, 3) . '/themes/default/views/shared/calculator-template.php';

// Render calculator
renderCalculator('ohms-law');
