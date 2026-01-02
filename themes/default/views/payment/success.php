<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .success-animation {
            animation: successPulse 2s ease-in-out;
        }
        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include APP_PATH . '/Views/partials/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto text-center">
            <!-- Success Icon -->
            <div class="success-animation mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-4xl"></i>
                </div>
            </div>

            <!-- Success Message -->
            <div class="bg-white rounded-lg shadow-sm border p-8 mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
                <p class="text-gray-600 mb-6">Thank you for your payment. Your premium features are now activated.</p>

                <!-- Payment Details -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Payment Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="block text-gray-600">Transaction ID</label>
                            <div class="font-medium">#<?= $transaction_id ?></div>
                        </div>
                        <div>
                            <label class="block text-gray-600">Payment Method</label>
                            <div class="font-medium"><?= ucfirst($payment_method) ?></div>
                        </div>
                        <div>
                            <label class="block text-gray-600">Amount Paid</label>
                            <div class="font-medium"><?= $currency_symbol ?><?= number_format($amount, 2) ?></div>
                        </div>
                        <div>
                            <label class="block text-gray-600">Date</label>
                            <div class="font-medium"><?= date('M j, Y g:i A') ?></div>
                        </div>
                    </div>
                </div>

                <!-- What's Next -->
                <div class="bg-blue-50 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-3">What happens next?</h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-blue-600"></i>
                            <span>Your premium subscription is now active</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-blue-600"></i>
                            <span>All calculator features are unlocked</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-blue-600"></i>
                            <span>Export and sharing options are now available</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-blue-600"></i>
                            <span>Priority support is enabled</span>
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <a href="/dashboard" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Go to Dashboard
                    </a>
                    <a href="/calculators" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        <i class="fas fa-calculator mr-2"></i>
                        Start Calculating
                    </a>
                </div>
            </div>

            <!-- Support Info -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Need Help?</h3>
                <p class="text-gray-600 mb-4">If you have any questions about your subscription or need assistance, our support team is here to help.</p>
                <div class="flex justify-center space-x-6 text-sm text-gray-600">
                    <a href="/support" class="flex items-center space-x-1 hover:text-blue-600">
                        <i class="fas fa-headset"></i>
                        <span>Contact Support</span>
                    </a>
                    <a href="/faq" class="flex items-center space-x-1 hover:text-blue-600">
                        <i class="fas fa-question-circle"></i>
                        <span>FAQ</span>
                    </a>
                    <a href="/account" class="flex items-center space-x-1 hover:text-blue-600">
                        <i class="fas fa-user-cog"></i>
                        <span>Account Settings</span>
                    </a>
                </div>
            </div>

            <!-- Email Confirmation -->
            <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-start space-x-2">
                    <i class="fas fa-envelope text-green-600 mt-0.5"></i>
                    <div class="text-sm text-green-800">
                        <strong>Confirmation Email:</strong> A receipt and confirmation email has been sent to 
                        <strong><?= $customer_email ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Auto-redirect after some time
        setTimeout(() => {
            // Show notification about auto-redirect
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.innerHTML = 'Redirecting to dashboard in 10 seconds...';
            document.body.appendChild(notification);

            // Auto-redirect timer
            let countdown = 10;
            const interval = setInterval(() => {
                notification.innerHTML = `Redirecting to dashboard in ${countdown} seconds...`;
                countdown--;
                if (countdown < 0) {
                    clearInterval(interval);
                    window.location.href = '/dashboard';
                }
            }, 1000);
        }, 5000);
    </script>

    <?php include APP_PATH . '/Views/partials/footer.php'; ?>
</body>
</html>
