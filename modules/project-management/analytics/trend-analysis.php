<?php
require_once '../../../app/Config/config.php';
require_once '../../../themes/default/views/partials/header.php';
?>

<style>
.coming-soon-container {
    min-height: 100vh;
    margin-top: -80px; /* Adjust for header */
    margin-bottom: -80px; /* Adjust for footer */
    display: flex;
    align-items: center;
    justify-content: center;
}

.coming-soon-content {
    text-align: center;
    transform: translateY(-40px); /* Offset to account for header/footer */
}

.coming-soon-title {
    font-size: 6rem;
    font-weight: 800;
    color: #4e73df; /* Bootstrap primary color */
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.coming-soon-subtitle {
    font-size: 1.8rem;
    color: #5a5c69;
    font-weight: 500;
}
</style>

<div class="coming-soon-container">
    <div class="coming-soon-content">
        <h1 class="coming-soon-title">Coming Soon</h1>
        <p class="coming-soon-subtitle">This feature is under development.</p>
    </div>
</div>

<?php require_once '../../../themes/default/views/partials/footer.php'; ?>
