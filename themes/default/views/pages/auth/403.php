<?php
http_response_code(403);
require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="display-1 mb-4 text-danger">403</h1>
            <h2 class="mb-4">Access Denied</h2>
            <p class="lead mb-4">You don't have permission to access this resource.</p>
            <div class="mb-4">
                <a href="<?php echo app_base_url('index.php'); ?>" class="btn btn-primary">Return to Home</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>