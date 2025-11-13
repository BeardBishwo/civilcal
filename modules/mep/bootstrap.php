<?php
// Bootstrap file for MEP modules — sets project root and includes core helpers
if (!defined('AEC_ROOT')) {
    define('AEC_ROOT', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/aec-calculator');
}

// Include core configuration and helpers
@require_once AEC_ROOT . '/app/Config/config.php';
@require_once AEC_ROOT . '/app/Core/DatabaseLegacy.php';
@require_once AEC_ROOT . '/app/Config/db.php';
@require_once AEC_ROOT . '/app/Helpers/functions.php';

// Silence errors above with @ so missing optional files don't break bootstrap in odd setups.
// Primary purpose: provide a consistent absolute path for module includes like header/footer.

?>




