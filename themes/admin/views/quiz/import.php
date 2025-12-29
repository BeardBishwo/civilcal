<?php include dirname(__DIR__, 2) . '/partials/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0">Bulk Import Questions</h6>
                        <a href="<?= app_base_url('admin/quiz/questions') ?>" class="btn btn-outline-primary btn-sm ms-auto">Back to Bank</a>
                    </div>
                </div>
                <div class="card-body px-4 pt-4 pb-2">
                    
                    <div class="alert alert-info text-white" role="alert">
                        <strong><i class="fas fa-info-circle"></i> Instructions:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Upload a <strong>CSV</strong> file with UTF-8 encoding.</li>
                            <li>Columns: <strong>Question, A, B, C, D, Correct (A/B/C/D), Difficulty (easy/medium/hard), Explanation, Related Tool Link</strong></li>
                            <li>Pro Tip: Use <strong>Google Sheets</strong> and export as CSV to preserve Nepali text.</li>
                        </ul>
                    </div>

                    <form action="<?= app_base_url('admin/quiz/import/upload') ?>" method="POST" enctype="multipart/form-data" class="mt-4">
                        <div class="form-group">
                            <label for="file">Select CSV File</label>
                            <input type="file" name="file" id="file" class="form-control" accept=".csv" required>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn bg-gradient-primary w-100">
                                <i class="fas fa-cloud-upload-alt me-2"></i> Start Import
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-5">
                        <h6>Download Template</h6>
                        <p class="text-sm">Use this template to ensure your table structure is correct.</p>
                        <a href="<?= app_base_url('public/templates/question_import_template.csv') ?>" class="btn btn-sm btn-dark" download>
                            <i class="fas fa-download me-2"></i> Download CSV Template
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__, 2) . '/partials/footer.php'; ?>
