<?php require_once __DIR__ . '/../header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Error
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo app_base_url('/admin/email-manager'); ?>" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Email Manager
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="error-page">
                        <h2 class="headline text-warning"> <?php echo isset($code) ? $code : '500'; ?> </h2>
                        <div class="error-content">
                            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! <?php echo htmlspecialchars($message ?? 'Something went wrong'); ?></h3>
                            <p>
                                We could not find the page you were looking for.
                                Meanwhile, you may <a href="<?php echo app_base_url('/admin/email-manager'); ?>">return to email manager</a> or try using the search form.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .error-page {
        text-align: center;
    }

    .error-page .headline {
        font-size: 100px;
        font-weight: 300;
    }

    .error-page .error-content {
        margin-top: 20px;
    }

    .error-page .error-content h3 {
        font-size: 25px;
        color: #6c757d;
    }

    .error-page .error-content p {
        margin-top: 10px;
        color: #6c757d;
    }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>