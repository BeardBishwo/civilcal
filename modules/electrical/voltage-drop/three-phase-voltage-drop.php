<?php
/**
 * Three Phase Voltage Drop Calculator - Migrated to Calculator Engine
 */

require_once dirname(__DIR__, 3) . '/app/bootstrap.php';
require_once dirname(__DIR__, 3) . '/themes/default/views/shared/calculator-template.php';

renderCalculator('three-phase-voltage-drop');
