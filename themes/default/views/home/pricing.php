<?php require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1>Pricing</h1>
            <p class="lead">Choose a plan that fits your needs.</p>
        </div>
    </div>

    <div class="row mt-4">
        <?php if (empty($plans)): ?>
            <div class="col-12 text-center">
                <p>No plans available at the moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($plans as $plan): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm <?php echo $plan['is_featured'] ? 'border-primary' : ''; ?>">
                         <?php if ($plan['is_featured']): ?>
                            <div class="card-header bg-primary text-white text-center py-2">
                                <span class="badge bg-light text-primary">Most Popular</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body text-center d-flex flex-column">
                            <h3 class="card-title"><?php echo htmlspecialchars($plan['name']); ?></h3>
                            <p class="text-muted"><?php echo htmlspecialchars($plan['description']); ?></p>
                            
                            <h2 class="card-price my-3">
                                <?php if ($plan['price_monthly'] > 0): ?>
                                    $<?php echo number_format($plan['price_monthly'], 2); ?><span class="text-muted fs-6">/mo</span>
                                <?php else: ?>
                                    Free
                                <?php endif; ?>
                            </h2>

                            <ul class="list-unstyled mt-3 mb-4 text-start mx-auto">
                                <?php 
                                    $features = json_decode($plan['features'], true) ?? [];
                                    foreach ($features as $feature): 
                                ?>
                                    <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i> <?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="mt-auto">
                                <?php if ($plan['price_monthly'] == 0): ?>
                                    <a href="<?php echo app_base_url('/register'); ?>" class="btn btn-outline-primary w-100">Get Started</a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#paymentModal_<?php echo $plan['id']; ?>">
                                        Subscribe
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Modal for Plan <?php echo $plan['id']; ?> -->
                <div class="modal fade" id="paymentModal_<?php echo $plan['id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Subscribe to <?php echo htmlspecialchars($plan['name']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-center mb-4">Select your preferred payment method:</p>
                                <div class="d-grid gap-2">
                                    <?php if (!empty($gateways['stripe'])): ?>
                                        <a href="<?php echo app_base_url('/payment/checkout/stripe?plan_id='.$plan['id'].'&type=monthly'); ?>" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-credit-card-2-front"></i> Pay with Stripe (Credit Card)
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($gateways['paypal'])): ?>
                                        <a href="<?php echo app_base_url('/payment/checkout/paypal?plan_id='.$plan['id'].'&type=monthly'); ?>" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-paypal"></i> Pay with PayPal
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($gateways['mollie'])): ?>
                                        <a href="<?php echo app_base_url('/payment/checkout/mollie?plan_id='.$plan['id'].'&type=monthly'); ?>" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-credit-card"></i> Pay with Mollie
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($gateways['paystack'])): ?>
                                        <a href="<?php echo app_base_url('/payment/checkout/paystack?plan_id='.$plan['id'].'&type=monthly'); ?>" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-wallet2"></i> Pay with PayStack
                                        </a>
                                    <?php endif; ?>
                                    
                                     <?php if (!empty($gateways['paddle'])): ?>
                                        <a href="<?php echo app_base_url('/payment/checkout/paddle?plan_id='.$plan['id'].'&type=monthly'); ?>" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-bag-check"></i> Pay with Paddle
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($gateways['bank'])): ?>
                                        <a href="<?php echo app_base_url('/payment/checkout/bank?plan_id='.$plan['id'].'&type=monthly'); ?>" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-bank"></i> Bank Transfer
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-3 text-center">
                                    <small class="text-muted">Yearly and Lifetime options also available inside dashboard.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php'; ?>
