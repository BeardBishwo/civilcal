<?php
http_response_code(500);
require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="display-1 mb-4 text-danger">500</h1>
            <h2 class="mb-4">Internal Server Error</h2>
            <p class="lead mb-4">Something went wrong on our end. Please try again later.</p>
            <div class="mb-4">
                <a href="<?php echo app_base_url('index.php'); ?>" class="btn btn-primary">Return to Home</a>
                <button onclick="location.reload()" class="btn btn-outline-secondary ms-2">Try Again</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>