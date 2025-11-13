<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<div class="container py-4">
    <h1 class="text-white">Main Isolation Valve</h1>
    <p class="text-white-50">Placeholder: select main valve size and attributes for mains.</p>
    <a href="<?php echo function_exists('app_base_url') ? app_base_url('modules/plumbing/index.php') : '../modules/plumbing/index.php'; ?>" class="btn btn-outline-light">Back</a>
</div>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

