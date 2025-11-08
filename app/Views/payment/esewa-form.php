<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with eSewa - Bishwo Calculator</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .esewa-logo {
            max-width: 120px;
            height: auto;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include APP_PATH . '/Views/partials/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Your Payment</h1>
                <p class="text-gray-600">You will be redirected to eSewa for secure payment</p>
            </div>

            <!-- Payment Details -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Payment Details</h2>
                    <img src="/assets/images/esewa.png" alt="eSewa" class="esewa-logo">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                        <div class="text-gray-900">Bishwo Calculator Premium</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <div class="text-xl font-bold text-gray-900">रू<?= number_format($amount, 2) ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <div class="text-gray-900"><?= $customer_name ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                        <div class="text-gray-900 text-sm">#<?= $order_id ?></div>
                    </div>
                </div>

                <!-- Auto-submit Form -->
                <form id="esewa-form" method="POST" action="https://uat.esewa.com.np/epay/main" target="_blank">
                    <input type="hidden" name="amt" value="<?= $amount ?>">
                    <input type="hidden" name="psc" value="0">
                    <input type="hidden" name="pdc" value="0">
                    <input type="hidden" name="txnid" value="<?= $order_id ?>">
                    <input type="hidden" name="tAmt" value="<?= $amount ?>">
                    <input type="hidden" name="pid" value="<?= $order_id ?>">
                    <input type="hidden" name="scd" value="<?= $merchant_code ?>">
                    <input type="hidden" name="su" value="<?= $success_url ?>">
                    <input type="hidden" name="fu" value="<?= $failed_url ?>">
                    <input type="hidden" name="firstname" value="<?= $customer_name ?>">
                    <input type="hidden" name="lastname" value="">
                    <input type="hidden" name="email" value="<?= $customer_email ?>">
                    <input type="hidden" name="phone" value="<?= $customer_phone ?>">
                    <input type="hidden" name="product_code" value="EPAYTEST">
                </form>
            </div>

            <!-- Payment Instructions -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-blue-900 mb-3">How to pay with eSewa:</h3>
                <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
                    <li>Click the "Proceed to eSewa" button below</li>
                    <li>Login to your eSewa account or create one if you don't have</li>
                    <li>Enter your eSewa MPIN and confirm the payment</li>
                    <li>You will be redirected back to our website after successful payment</li>
                </ol>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4">
                <button onclick="submitEsewaForm()" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                    <i class="fas fa-mobile-alt mr-2"></i>
                    Proceed to eSewa
                </button>
                <button onclick="goBack()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Go Back
                </button>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start space-x-2">
                    <i class="fas fa-shield-alt text-yellow-600 mt-0.5"></i>
                    <div class="text-sm text-yellow-800">
                        <strong>Secure Payment:</strong> This payment is processed securely through eSewa's encrypted system. 
                        We never store your payment information.
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function submitEsewaForm() {
            // Add loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Redirecting...';
            button.disabled = true;

            // Submit the form
            document.getElementById('esewa-form').submit();
            
            // Reset button after a delay
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 3000);
        }

        function goBack() {
            window.history.back();
        }

        // Auto-submit on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                submitEsewaForm();
            }, 2000);
        });
    </script>

    <?php include APP_PATH . '/Views/partials/footer.php'; ?>
</body>
</html>
