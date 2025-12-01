<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Bishwo Calculator</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .error-animation {
            animation: errorShake 0.5s ease-in-out;
        }
        @keyframes errorShake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include APP_PATH . '/Views/partials/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto text-center">
            <!-- Error Icon -->
            <div class="error-animation mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-red-600 text-4xl"></i>
                </div>
            </div>

            <!-- Error Message -->
            <div class="bg-white rounded-lg shadow-sm border p-8 mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Failed</h1>
                <p class="text-gray-600 mb-6">Sorry, your payment could not be processed. Please try again or choose a different payment method.</p>

                <!-- Error Details -->
                <?php if (isset($error_message) && !empty($error_message)): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                        <div class="text-sm text-red-800">
                            <strong>Error Details:</strong> <?= htmlspecialchars($error_message) ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Payment Information -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Payment Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="block text-gray-600">Transaction ID</label>
                            <div class="font-medium">#<?= $transaction_id ?? 'N/A' ?></div>
                        </div>
                        <div>
                            <label class="block text-gray-600">Payment Method</label>
                            <div class="font-medium"><?= ucfirst($payment_method ?? 'Unknown') ?></div>
                        </div>
                        <div>
                            <label class="block text-gray-600">Amount</label>
                            <div class="font-medium"><?= $currency_symbol ?? '$' ?><?= number_format($amount ?? 0, 2) ?></div>
                        </div>
                        <div>
                            <label class="block text-gray-600">Date</label>
                            <div class="font-medium"><?= date('M j, Y g:i A') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Common Solutions -->
                <div class="bg-blue-50 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-3">Common Solutions</h3>
                    <ul class="text-sm text-blue-800 space-y-2 text-left">
                        <li class="flex items-start space-x-2">
                            <i class="fas fa-check text-blue-600 mt-0.5"></i>
                            <span>Check if your payment method has sufficient funds</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <i class="fas fa-check text-blue-600 mt-0.5"></i>
                            <span>Verify your payment method details are correct</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <i class="fas fa-check text-blue-600 mt-0.5"></i>
                            <span>Try using a different payment method</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <i class="fas fa-check text-blue-600 mt-0.5"></i>
                            <span>Contact your bank if the issue persists</span>
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4">
                    <div class="flex space-x-4">
                        <button onclick="retryPayment()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            <i class="fas fa-redo mr-2"></i>
                            Try Again
                        </button>
                        <button onclick="changePaymentMethod()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            <i class="fas fa-credit-card mr-2"></i>
                            Different Method
                        </button>
                    </div>
                    <div class="flex space-x-4">
                        <a href="/support" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            <i class="fas fa-headset mr-2"></i>
                            Contact Support
                        </a>
                        <a href="/dashboard" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            <i class="fas fa-home mr-2"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Still Need Help?</h3>
                <p class="text-gray-600 mb-4">Our support team is available to assist you with any payment issues.</p>
                <div class="flex justify-center space-x-6 text-sm text-gray-600">
                    <a href="mailto:support@bishwocalculator.com" class="flex items-center space-x-1 hover:text-blue-600">
                        <i class="fas fa-envelope"></i>
                        <span>Email Support</span>
                    </a>
                    <a href="/faq" class="flex items-center space-x-1 hover:text-blue-600">
                        <i class="fas fa-question-circle"></i>
                        <span>Payment FAQ</span>
                    </a>
                    <a href="/account" class="flex items-center space-x-1 hover:text-blue-600">
                        <i class="fas fa-user-cog"></i>
                        <span>Account Settings</span>
                    </a>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start space-x-2">
                    <i class="fas fa-shield-alt text-yellow-600 mt-0.5"></i>
                    <div class="text-sm text-yellow-800">
                        <strong>Security Notice:</strong> No charges have been made to your account. 
                        Your payment information remains secure.
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function retryPayment() {
            // Add loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            button.disabled = true;

            // Redirect to checkout with same payment method
            const paymentMethod = '<?= $payment_method ?? "" ?>';
            window.location.href = `/payment/checkout?retry=1&method=${paymentMethod}`;
        }

        function changePaymentMethod() {
            // Redirect to checkout page for new payment method selection
            window.location.href = '/payment/checkout';
        }

        // Auto-retry notification
        setTimeout(() => {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.innerHTML = 'You can retry your payment or try a different method';
            document.body.appendChild(notification);

            // Auto-hide notification
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }, 3000);
    </script>

    <?php include APP_PATH . '/Views/partials/footer.php'; ?>
</body>
</html>
