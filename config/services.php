<?php
return [
    'cors' => [
        'allowed_origins' => ['*'],
        'allowed_methods' => ['GET','POST','PUT','PATCH','DELETE','OPTIONS'],
        'allowed_headers' => ['Content-Type','X-Requested-With','X-CSRF-Token','Authorization'],
        'allow_credentials' => true,
        'max_age' => 86400
    ],

    // Payment Gateway Configurations
    'payment_gateways' => [
        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', true),
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'currency' => env('STRIPE_CURRENCY', 'USD'),
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
        ],

        'paypal' => [
            'enabled' => env('PAYPAL_ENABLED', true),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'sandbox' => env('PAYPAL_SANDBOX', true),
            'currency' => env('PAYPAL_CURRENCY', 'USD'),
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
        ],

        'adyen' => [
            'enabled' => env('ADYEN_ENABLED', false),
            'api_key' => env('ADYEN_API_KEY'),
            'merchant_account' => env('ADYEN_MERCHANT_ACCOUNT'),
            'client_key' => env('ADYEN_CLIENT_KEY'),
            'environment' => env('ADYEN_ENVIRONMENT', 'test'),
            'currency' => env('ADYEN_CURRENCY', 'USD'),
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY'],
        ],

        'authorizenet' => [
            'enabled' => env('AUTHORIZENET_ENABLED', false),
            'api_login_id' => env('AUTHORIZENET_API_LOGIN_ID'),
            'transaction_key' => env('AUTHORIZENET_TRANSACTION_KEY'),
            'sandbox' => env('AUTHORIZENET_SANDBOX', true),
            'currency' => env('AUTHORIZENET_CURRENCY', 'USD'),
            'supported_currencies' => ['USD', 'CAD'],
        ],

        'braintree' => [
            'enabled' => env('BRAINTREE_ENABLED', false),
            'merchant_id' => env('BRAINTREE_MERCHANT_ID'),
            'public_key' => env('BRAINTREE_PUBLIC_KEY'),
            'private_key' => env('BRAINTREE_PRIVATE_KEY'),
            'environment' => env('BRAINTREE_ENVIRONMENT', 'sandbox'),
            'currency' => env('BRAINTREE_CURRENCY', 'USD'),
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
        ],

        'flutterwave' => [
            'enabled' => env('FLUTTERWAVE_ENABLED', false),
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
            'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
            'environment' => env('FLUTTERWAVE_ENVIRONMENT', 'sandbox'),
            'currency' => env('FLUTTERWAVE_CURRENCY', 'USD'),
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'NGN', 'KES', 'GHS', 'ZAR'],
        ],

        'paystack' => [
            'enabled' => env('PAYSTACK_ENABLED', false),
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
            'environment' => env('PAYSTACK_ENVIRONMENT', 'test'),
            'currency' => env('PAYSTACK_CURRENCY', 'USD'),
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'NGN', 'KES', 'GHS', 'ZAR'],
        ],

        'razorpay' => [
            'enabled' => env('RAZORPAY_ENABLED', false),
            'key_id' => env('RAZORPAY_KEY_ID'),
            'key_secret' => env('RAZORPAY_KEY_SECRET'),
            'environment' => env('RAZORPAY_ENVIRONMENT', 'test'),
            'currency' => env('RAZORPAY_CURRENCY', 'USD'),
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'INR', 'SGD', 'AUD'],
        ],

        'square' => [
            'enabled' => env('SQUARE_ENABLED', false),
            'application_id' => env('SQUARE_APPLICATION_ID'),
            'access_token' => env('SQUARE_ACCESS_TOKEN'),
            'environment' => env('SQUARE_ENVIRONMENT', 'sandbox'),
            'webhook_signature_key' => env('SQUARE_WEBHOOK_SIGNATURE_KEY'),
            'currency' => env('SQUARE_CURRENCY', 'USD'),
            'supported_currencies' => ['USD', 'CAD', 'GBP', 'EUR', 'JPY', 'AUD'],
        ],

        'coinpayments' => [
            'enabled' => env('COINPAYMENTS_ENABLED', false),
            'merchant_id' => env('COINPAYMENTS_MERCHANT_ID'),
            'public_key' => env('COINPAYMENTS_PUBLIC_KEY'),
            'private_key' => env('COINPAYMENTS_PRIVATE_KEY'),
            'ipn_secret' => env('COINPAYMENTS_IPN_SECRET'),
            'environment' => env('COINPAYMENTS_ENVIRONMENT', 'test'),
            'supported_currencies' => ['BTC', 'ETH', 'LTC', 'USDT', 'BCH', 'XRP', 'DASH'],
        ],

        'twocheckout' => [
            'enabled' => env('TWOCO_ENABLED', false),
            'merchant_id' => env('TWOCO_MERCHANT_ID'),
            'secret_word' => env('TWOCO_SECRET_WORD'),
            'sandbox' => env('TWOCO_SANDBOX', true),
            'currency' => env('TWOCO_CURRENCY', 'USD'),
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'CHF'],
        ],

        'bank_transfer' => [
            'enabled' => env('BANK_TRANSFER_ENABLED', true),
            'instructions' => env('BANK_TRANSFER_INSTRUCTIONS', 'Please transfer the amount to the provided bank details and include your order ID in the reference.'),
            'currency' => env('BANK_TRANSFER_CURRENCY', 'USD'),
        ],
    ],

    // Default payment gateway
    'default_payment_gateway' => env('DEFAULT_PAYMENT_GATEWAY', 'stripe'),

    // Webhook configurations
    'webhooks' => [
        'retry_attempts' => env('WEBHOOK_RETRY_ATTEMPTS', 3),
        'timeout' => env('WEBHOOK_TIMEOUT', 30),
        'signature_verification' => env('WEBHOOK_SIGNATURE_VERIFICATION', true),
    ],
];
