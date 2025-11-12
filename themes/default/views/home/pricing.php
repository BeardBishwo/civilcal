<?php require_once 'includes/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1>Pricing</h1>
            <p class="lead">Choose a plan that fits your needs. For now we accept PayPal payments (one-time).</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 offset-md-3">
            <div class="card p-4 text-center">
                <h3>Pro — One-time purchase</h3>
                <p class="mb-2">Unlock all calculators, export PDF, and priority support.</p>
                <h2>$29.99</h2>
                <div id="paypal-button-container" class="mt-3"></div>
                <div id="paymentResult" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<?php
// Pass PayPal client ID to the client side (use config value)
require_once __DIR__ . '/includes/config.php';
$paypalClientId = defined('PAYPAL_CLIENT_ID') ? PAYPAL_CLIENT_ID : '';
$mode = defined('PAYPAL_MODE') ? PAYPAL_MODE : 'sandbox';
?>

<?php if (!$paypalClientId): ?>
    <div class="container"><div class="alert alert-warning">PayPal is not configured. Set PAYPAL_CLIENT_ID and PAYPAL_SECRET in <code>includes/config.php</code>.</div></div>
<?php endif; ?>

<script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($paypalClientId); ?>&currency=USD<?php echo $mode==='sandbox' ? '&intent=capture' : ''; ?>"></script>
<script>
    const price = '29.99';
    paypal.Buttons({
        createOrder: function(data, actions) {
            // Call server to create order
            return fetch('/aec-calculator/api/create_paypal_order.php', {
                method: 'post',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ amount: price, currency: 'USD', description: 'Pro - One-time' })
            }).then(function(res){ return res.json(); }).then(function(data){ if (data && data.orderID) return data.orderID; throw new Error('Could not create order'); });
        },
        onApprove: function(data, actions) {
            // Capture on server
            return fetch('/aec-calculator/api/capture_paypal_order.php', {
                method: 'post',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ orderID: data.orderID })
            }).then(function(res){ return res.json(); }).then(function(details){
                if (details && details.success) {
                    document.getElementById('paymentResult').innerHTML = '<div class="alert alert-success">Payment successful. Thank you!</div>';
                } else {
                    document.getElementById('paymentResult').innerHTML = '<div class="alert alert-danger">Payment verification failed.</div>';
                }
            }).catch(function(err){ document.getElementById('paymentResult').innerHTML = '<div class="alert alert-danger">Payment failed: '+err.message+'</div>'; });
        },
        onError: function(err){ document.getElementById('paymentResult').innerHTML = '<div class="alert alert-danger">Payment error: '+err+'</div>'; }
    }).render('#paypal-button-container');
</script>

<?php require_once 'includes/footer.php'; ?>
