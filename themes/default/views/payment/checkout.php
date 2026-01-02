<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Checkout - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypal_client_id ?>&currency=USD"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include APP_PATH . '/Views/partials/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Your Payment</h1>
                <p class="text-gray-600">Choose your preferred payment method to unlock all features</p>
            </div>

            <!-- Current Plan & Pricing -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Your Subscription Plan</h2>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        <?= ucfirst($user_plan) ?> Plan
                    </span>
                </div>
                <div class="border-t pt-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">Premium Features</h3>
                            <p class="text-sm text-gray-600">Unlimited calculations, export options, priority support</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900"><?= $currency_symbol ?><?= number_format($price, 2) ?></div>
                            <div class="text-sm text-gray-600">per month</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Payment Method Selection -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Payment Method</h3>
                    
                    <div class="space-y-4">
                        <!-- PayPal (for International) -->
                        <?php if ($country_code === 'US' || $country_code === 'OTHER'): ?>
                        <div class="border rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex items-center space-x-3">
                                <input type="radio" name="payment_method" value="paypal" id="paypal" class="w-4 h-4 text-blue-600">
                                <label for="paypal" class="flex-1 cursor-pointer">
                                    <div class="flex items-center space-x-3">
                                        <i class="fab fa-paypal text-blue-600 text-xl"></i>
                                        <div>
                                            <div class="font-medium text-gray-900">PayPal</div>
                                            <div class="text-sm text-gray-600">Pay securely with PayPal</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- PayTM/UPI (for India) -->
                        <?php if ($country_code === 'IN'): ?>
                        <div class="border rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex items-center space-x-3">
                                <input type="radio" name="payment_method" value="paytm" id="paytm" class="w-4 h-4 text-blue-600">
                                <label for="paytm" class="flex-1 cursor-pointer">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-mobile-alt text-orange-600 text-xl"></i>
                                        <div>
                                            <div class="font-medium text-gray-900">PayTM / UPI</div>
                                            <div class="text-sm text-gray-600">Pay using PayTM, GPay, PhonePe</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- eSewa (for Nepal) -->
                        <?php if ($country_code === 'NP'): ?>
                        <div class="border rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex items-center space-x-3">
                                <input type="radio" name="payment_method" value="esewa" id="esewa" class="w-4 h-4 text-blue-600">
                                <label for="esewa" class="flex-1 cursor-pointer">
                                    <div class="flex items-center space-x-3">
                                        <img src="/assets/images/esewa.png" alt="eSewa" class="w-8 h-8">
                                        <div>
                                            <div class="font-medium text-gray-900">eSewa</div>
                                            <div class="text-sm text-gray-600">Pay securely with eSewa</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Khalti (for Nepal) -->
                        <div class="border rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex items-center space-x-3">
                                <input type="radio" name="payment_method" value="khalti" id="khalti" class="w-4 h-4 text-blue-600">
                                <label for="khalti" class="flex-1 cursor-pointer">
                                    <div class="flex items-center space-x-3">
                                        <img src="/assets/images/khalti.png" alt="Khalti" class="w-8 h-8">
                                        <div>
                                            <div class="font-medium text-gray-900">Khalti</div>
                                            <div class="text-sm text-gray-600">Pay with Khalti Digital Wallet</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Method Info -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                            <div class="text-sm text-blue-800">
                                <strong>Payment Security:</strong> All payments are processed securely. 
                                Your payment information is encrypted and never stored.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subscription Plan</span>
                            <span class="font-medium">Premium</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Billing Cycle</span>
                            <span class="font-medium">Monthly</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Location</span>
                            <span class="font-medium"><?= $country_name ?></span>
                        </div>
                        <hr class="my-3">
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total Amount</span>
                            <span class="text-blue-600"><?= $currency_symbol ?><?= number_format($price, 2) ?></span>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <button id="proceed-payment" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <i class="fas fa-lock mr-2"></i>
                        Proceed to Payment
                    </button>

                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500">
                            By proceeding, you agree to our 
                            <a href="/terms" class="text-blue-600 hover:underline">Terms of Service</a> 
                            and 
                            <a href="/privacy" class="text-blue-600 hover:underline">Privacy Policy</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Trust Indicators -->
            <div class="mt-8 text-center">
                <div class="flex justify-center space-x-8 text-gray-400">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shield-alt"></i>
                        <span class="text-sm">Secure Payment</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-lock"></i>
                        <span class="text-sm">SSL Encrypted</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-headset"></i>
                        <span class="text-sm">24/7 Support</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('proceed-payment').disabled = false;
                document.getElementById('proceed-payment').classList.remove('opacity-50', 'cursor-not-allowed');
            });
        });

        // Payment processing
        document.getElementById('proceed-payment').addEventListener('click', function() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const button = this;
            
            // Show loading state
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            button.disabled = true;

            // Redirect to payment processor
            if (selectedMethod === 'paypal') {
                window.location.href = '/payment/process-paypal';
            } else if (selectedMethod === 'paytm') {
                window.location.href = '/payment/process-paytm';
            } else if (selectedMethod === 'esewa') {
                window.location.href = '/payment/process-esewa';
            } else if (selectedMethod === 'khalti') {
                window.location.href = '/payment/process-khalti';
            }
        });
    </script>

    <?php include APP_PATH . '/Views/partials/footer.php'; ?>
</body>
</html>
