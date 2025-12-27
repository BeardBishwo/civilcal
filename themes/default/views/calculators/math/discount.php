<?php $page_title = $title ?? 'Discount Calculator'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo app_base_url('/themes/default/assets/css/floating-calculator.css'); ?>">
    <style>
        body { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 100vh; padding: 40px 0; }
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 700px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.3rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; }
        .calc-btn { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 12px; padding: 30px; margin-top: 30px; }
        .result-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .result-value { font-size: 1.5rem; font-weight: 700; }
        .final-price { font-size: 3rem !important; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-tag" style="font-size: 2.5rem;"></i>
                <h2>Discount Calculator</h2>
                <p class="mb-0 mt-2">Calculate sale price & savings</p>
            </div>
            <div class="calc-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Original Price ($)</label>
                        <input type="number" id="price" class="form-control calc-input" value="100" step="any">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Discount (%)</label>
                        <input type="number" id="discount" class="form-control calc-input" value="20" step="any">
                    </div>
                </div>
                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate</button>
                <div class="result-box" id="resultBox" style="display:none;">
                    <div class="result-item">
                        <span>Discount Amount:</span>
                        <span class="result-value">$<span id="discountAmount">0</span></span>
                    </div>
                    <div class="result-item">
                        <span>You Save:</span>
                        <span class="result-value">$<span id="savings">0</span></span>
                    </div>
                    <div class="result-item" style="border:none;">
                        <span>Final Price:</span>
                        <span class="result-value final-price">$<span id="finalPrice">0</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const appBase = "<?php echo rtrim(app_base_url(), '/'); ?>";
        function calculate() {
            const price = document.getElementById('price').value;
            const discount = document.getElementById('discount').value;
            fetch(appBase + '/calculator/api/discount', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `price=${price}&discount=${discount}`
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('resultBox').style.display = 'block';
                document.getElementById('discountAmount').textContent = data.discountAmount.toFixed(2);
                document.getElementById('savings').textContent = data.savings.toFixed(2);
                document.getElementById('finalPrice').textContent = data.finalPrice.toFixed(2);
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
