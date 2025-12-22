<?php
/**
 * Payment Settings Page - Premium Modern UI/UX Design
 */
?>

<style>
    .payment-settings-container {
        background: #f8fafc;
        min-height: 100vh;
        padding: 2.5rem;
    }

    .settings-breadcrumb {
        margin-bottom: 2rem;
        animation: slideDown 0.6s ease-out;
    }

    .settings-breadcrumb h1 {
        font-size: 2.8rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 0.5rem;
        letter-spacing: -0.8px;
    }

    .settings-breadcrumb p {
        font-size: 1.1rem;
        color: #718096;
        margin-bottom: 0;
    }

    .payment-form-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .settings-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        margin-bottom: 2rem;
        animation: fadeInUp 0.7s ease-out;
        border: 1px solid #edf2f7;
    }

    .card-header-simple {
        padding: 1.5rem 2rem 0.5rem 2rem;
        border-bottom: none;
    }

    .card-header-simple h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0;
    }
    
    .card-body {
        padding: 1.5rem 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background-color: #fff;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .form-text {
        font-size: 0.85rem;
        color: #718096;
        margin-top: 0.5rem;
    }
    
    .toggle-switch-group {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
        margin-right: 0.75rem;
    }
    
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e0;
        transition: .4s;
        border-radius: 24px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: #3b82f6;
    }
    
    input:checked + .slider:before {
        transform: translateX(24px);
    }
    
    .toggle-label {
        font-weight: 500;
        color: #2d3748;
    }

    .btn-save {
        background-color: #2563eb;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
        width: 100%;
        max-width: 200px;
    }

    .btn-save:hover {
        background-color: #1d4ed8;
    }

    .readonly-field {
        background-color: #f7fafc;
        cursor: text;
    }
    
    .copy-btn {
        background: #e2e8f0;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0 6px 6px 0;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        color: #4a5568;
        border-left: 1px solid #cbd5e0;
    }
    
    .copy-btn:hover {
        background: #cbd5e0;
    }
    
    .input-group {
        display: flex;
    }
    
    .input-group .form-control {
        border-radius: 6px 0 0 6px;
    }

    /* Transition for gateway settings */
    .gateway-settings {
        transition: all 0.3s ease-in-out;
        overflow: hidden;
        max-height: 2000px; /* Arbitrary large height */
        opacity: 1;
    }

    .gateway-settings.hidden {
        max-height: 0;
        opacity: 0;
        margin-bottom: 0;
        padding: 0;
    }

    @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="admin-content">
    <div class="payment-settings-container">
        <div class="settings-breadcrumb">
            <h1>Payment Gateway</h1>
        </div>

        <div class="payment-form-container">
            
            <!-- PayPal Basic -->
            <div class="settings-card">
                <div class="card-header-simple">
                    <h2>Paypal Basic Checkout</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo app_base_url('/admin/settings/payments/update'); ?>" method="POST" class="ajax-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="setting_group" value="payments">
                        <input type="hidden" name="gateway" value="paypal_basic">
                        <input type="hidden" name="paypal_basic_enabled" value="0"> <!-- Default 0 if unchecked -->

                        <div class="toggle-switch-group">
                            <label class="toggle-switch">
                                <input type="checkbox" name="paypal_basic_enabled" value="1" <?= ($settings['paypal_basic_enabled'] ?? '') == '1' ? 'checked' : '' ?> class="gateway-toggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label">Enable</span>
                        </div>
                        <p class="form-text mb-3" style="margin-top: -1rem; margin-bottom: 1.5rem !important;">Collect payments via basic paypal checkout.</p>
                        
                        <div class="gateway-settings">
                            <div class="form-group alert alert-info p-3" style="font-size: 0.9em; background-color: #eff6ff; border-color: #bfdbfe; color: #1e3a8a;">
                                <strong>Note:</strong> We are going to sell this SaaS product in CodeCanyon. Each admin will have to put their own credentials.
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="paypal_email">PayPal Email</label>
                                <input type="email" class="form-control" id="paypal_email" name="paypal_email" value="<?= htmlspecialchars($settings['paypal_email'] ?? '') ?>" placeholder="paypal@example.com">
                                <div class="form-text">Payments will be sent to this address. Please make sure that you enable IPN and enable notification.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">PayPal IPN</label>
                                <div class="input-group">
                                    <input type="text" class="form-control readonly-field" value="<?= app_base_url('/ipn') ?>" readonly>
                                    <button type="button" class="copy-btn" onclick="copyToClipboard('<?= app_base_url('/ipn') ?>')">Copy</button>
                                </div>
                                <div class="form-text">For more info click here</div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-save">Save Settings</button>
                    </form>
                </div>
            <!-- PayPal Smart Checkout (API) -->
            <div class="settings-card">
                <div class="card-header-simple">
                    <h2>Paypal Smart Checkout (API)</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo app_base_url('/admin/settings/payments/update'); ?>" method="POST" class="ajax-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="setting_group" value="payments">
                        <input type="hidden" name="gateway" value="paypal_api">
                        <input type="hidden" name="paypal_api_enabled" value="0">

                        <div class="toggle-switch-group">
                            <label class="toggle-switch">
                                <input type="checkbox" name="paypal_api_enabled" value="1" <?= ($settings['paypal_api_enabled'] ?? '') == '1' ? 'checked' : '' ?> class="gateway-toggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label">Enable</span>
                        </div>
                        <p class="form-text mb-3" style="margin-top: -1rem; margin-bottom: 1.5rem !important;">Advanced PayPal integration with Smart Buttons.</p>
                        
                        <div class="gateway-settings">
                            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label" for="paypal_client_id">Client ID</label>
                                    <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" value="<?= htmlspecialchars($settings['paypal_client_id'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="paypal_client_secret">Client Secret</label>
                                    <input type="password" class="form-control" id="paypal_client_secret" name="paypal_client_secret" value="<?= htmlspecialchars($settings['paypal_client_secret'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check form-switch" style="margin-top: 0.5rem;">
                                    <input class="form-check-input" type="checkbox" id="paypal_sandbox_mode" name="paypal_sandbox_mode" value="1" <?= ($settings['paypal_sandbox_mode'] ?? '0') == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="paypal_sandbox_mode">Sandbox Mode</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-save">Save Settings</button>
                    </form>
                </div>
            </div>

            <!-- Stripe -->
            <div class="settings-card">
                <div class="card-header-simple">
                     <span style="color:red; font-size: 0.8em; float: right;">You cannot enable both Stripe and PayStack/Paddle at the same time.</span>
                    <h2>Stripe Payments</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo app_base_url('/admin/settings/payments/update'); ?>" method="POST" class="ajax-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="setting_group" value="payments">
                        <input type="hidden" name="gateway" value="stripe">
                        <input type="hidden" name="stripe_enabled" value="0">

                        <div class="toggle-switch-group">
                            <label class="toggle-switch">
                                <input type="checkbox" name="stripe_enabled" value="1" <?= ($settings['stripe_enabled'] ?? '') == '1' ? 'checked' : '' ?> class="gateway-toggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label">Enable</span>
                        </div>
                        <p class="form-text mb-3" style="margin-top: -1rem; margin-bottom: 1.5rem !important;">Collect payments securely with Stripe.</p>

                        <div class="gateway-settings">
                            <div class="form-group">
                                <label class="form-label" for="stripe_checkout_type">Checkout</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="stripe_checkout_type" id="builtin" value="builtin" <?= ($settings['stripe_checkout_type'] ?? '') == 'builtin' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="builtin">Built-in Checkout</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="stripe_checkout_type" id="hosted" value="hosted" <?= ($settings['stripe_checkout_type'] ?? '') == 'hosted' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="hosted">Stripe Hosted Checkout</label>
                                </div>
                               <div class="form-text">Choose between built-in checkout or Stripe hosted checkout.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="stripe_publishable_key">Stripe Publishable Key</label>
                                <input type="text" class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" value="<?= htmlspecialchars($settings['stripe_publishable_key'] ?? '') ?>">
                                <div class="form-text">Get your stripe keys from here once logged in click here</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="stripe_secret_key">Stripe Secret Key</label>
                                <input type="password" class="form-control" id="stripe_secret_key" name="stripe_secret_key" value="<?= htmlspecialchars($settings['stripe_secret_key'] ?? '') ?>">
                                <div class="form-text">Get your stripe keys from here once logged in click here</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="stripe_webhook_secret">Webhook Signature Key</label>
                                <input type="text" class="form-control" id="stripe_webhook_secret" name="stripe_webhook_secret" value="<?= htmlspecialchars($settings['stripe_webhook_secret'] ?? '') ?>">
                                <div class="form-text">Webhook signature is a security measure to verify the authenticity of the data incoming from Stripe. It is highly recommended that you add this for safety measure. You can find your key after adding a webhook. Click here to find your signature key.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Webhook URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control readonly-field" value="<?= app_base_url('/webhook') ?>" readonly>
                                    <button type="button" class="copy-btn" onclick="copyToClipboard('<?= app_base_url('/webhook') ?>')">Copy</button>
                                </div>
                                <div class="form-text">You can add your webhooks here. For more info, please check the docs.</div>
                            </div>
                        </div>

                         <button type="submit" class="btn btn-save">Save Settings</button>
                    </form>
                </div>
            </div>

            <!-- Mollie -->
            <div class="settings-card">
                 <div class="card-header-simple">
                    <h2>Mollie Payments</h2>
                </div>
                <div class="card-body">
                     <form action="<?php echo app_base_url('/admin/settings/payments/update'); ?>" method="POST" class="ajax-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="setting_group" value="payments">
                        <input type="hidden" name="gateway" value="mollie">
                        <input type="hidden" name="mollie_enabled" value="0">

                         <div class="toggle-switch-group">
                            <label class="toggle-switch">
                                <input type="checkbox" name="mollie_enabled" value="1" <?= ($settings['mollie_enabled'] ?? '') == '1' ? 'checked' : '' ?> class="gateway-toggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label">Enable</span>
                        </div>
                        <p class="form-text mb-3" style="margin-top: -1rem; margin-bottom: 1.5rem !important;">Collect payments securely with Mollie.</p>

                        <div class="gateway-settings">
                            <div class="form-group">
                                <label class="form-label" for="mollie_api_key">Mollie API Key</label>
                                <input type="password" class="form-control" id="mollie_api_key" name="mollie_api_key" value="<?= htmlspecialchars($settings['mollie_api_key'] ?? '') ?>">
                                <div class="form-text">Get your API key from your Mollie account.</div>
                            </div>
                        </div>

                         <button type="submit" class="btn btn-save">Save Settings</button>
                    </form>
                </div>
            </div>

            <!-- Paypal API -->
            <div class="settings-card">
                <div class="card-header-simple">
                    <h2>Paypal API Payments</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo app_base_url('/admin/settings/payments/update'); ?>" method="POST" class="ajax-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="setting_group" value="payments">
                         <input type="hidden" name="gateway" value="paypal_api">
                         <input type="hidden" name="paypal_api_enabled" value="0">

                         <div class="toggle-switch-group">
                            <label class="toggle-switch">
                                <input type="checkbox" name="paypal_api_enabled" value="1" <?= ($settings['paypal_api_enabled'] ?? '') == '1' ? 'checked' : '' ?> class="gateway-toggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label">Enable</span>
                        </div>
                        <p class="form-text mb-3" style="margin-top: -1rem; margin-bottom: 1.5rem !important;">Collect payments securely with PayPal API.</p>

                        <div class="gateway-settings">
                            <div class="form-group">
                                <label class="form-label" for="paypal_client_id">Client ID</label>
                                <textarea class="form-control" id="paypal_client_id" name="paypal_client_id" rows="2"><?= htmlspecialchars($settings['paypal_client_id'] ?? '') ?></textarea>
                                <div class="form-text">Please enter your live client ID.</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="paypal_client_secret">Client Secret Key</label>
                                 <textarea class="form-control" id="paypal_client_secret" name="paypal_client_secret" rows="2"><?= htmlspecialchars($settings['paypal_client_secret'] ?? '') ?></textarea>
                                <div class="form-text">Please enter your live client secret.</div>
                            </div>
                        </div>

                         <button type="submit" class="btn btn-save">Save Settings</button>
                    </form>
                </div>
            </div>

            <!-- Paddle Billing -->
            <div class="settings-card">
                 <div class="card-header-simple">
                    <span style="color:red; font-size: 0.8em; float: right;">You cannot enable both Stripe and Paddle at the same time because they both work in the same way. You must choose one.</span>
                    <h2>Paddle Billing</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo app_base_url('/admin/settings/payments/update'); ?>" method="POST" class="ajax-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="setting_group" value="payments">
                        <input type="hidden" name="gateway" value="paddle_billing">
                        <input type="hidden" name="paddle_billing_enabled" value="0">

                        <div class="toggle-switch-group">
                            <label class="toggle-switch">
                                <input type="checkbox" name="paddle_billing_enabled" value="1" <?= ($settings['paddle_billing_enabled'] ?? '') == '1' ? 'checked' : '' ?> class="gateway-toggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label">Enable</span>
                        </div>
                        <p class="form-text mb-3" style="margin-top: -1rem; margin-bottom: 1.5rem !important;">Collect payments securely with Paddle Billing.</p>

                        <div class="gateway-settings">
                            <div class="form-group">
                                <label class="form-label" for="paddle_client_token">Client-side Token</label>
                                <input type="text" class="form-control" id="paddle_client_token" name="paddle_client_token" value="<?= htmlspecialchars($settings['paddle_client_token'] ?? '') ?>">
                                <div class="form-text">Get your client-side token from Paddle dashboard</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="paddle_api_key">API Key</label>
                                <input type="text" class="form-control" id="paddle_api_key" name="paddle_api_key" value="<?= htmlspecialchars($settings['paddle_api_key'] ?? '') ?>">
                                <div class="form-text">Get your API key from Paddle dashboard</div>
                            </div>
                            
                             <div class="form-group">
                                <label class="form-label">Webhook URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control readonly-field" value="<?= app_base_url('/webhook/paddlebilling') ?>" readonly>
                                    <button type="button" class="copy-btn" onclick="copyToClipboard('<?= app_base_url('/webhook/paddlebilling') ?>')">Copy</button>
                                </div>
                                <div class="form-text">Add this webhook URL to your Paddle dashboard to receive payment notifications</div>
                            </div>

                             <div class="form-group">
                                <label class="form-label" for="paddle_webhook_secret">Webhook Secret Key</label>
                                <input type="text" class="form-control" id="paddle_webhook_secret" name="paddle_webhook_secret" value="<?= htmlspecialchars($settings['paddle_webhook_secret'] ?? '') ?>">
                                <div class="form-text">You can find this when creating a notification webhook in your Paddle dashboard</div>
                            </div>
                        </div>

                         <button type="submit" class="btn btn-save">Save Settings</button>
                    </form>
                </div>
            </div>

            <!-- Paddle Classic -->
             <div class="settings-card">
                <div class="card-header-simple">
                    <span style="color:red; font-size: 0.8em; float: right;">You cannot enable both Stripe and Paddle at the same time because they both work in the same way. You must choose one.</span>
                    <h2>Paddle Classic</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo app_base_url('/admin/settings/payments/update'); ?>" method="POST" class="ajax-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                         <input type="hidden" name="gateway" value="paddle_classic">
                         <input type="hidden" name="paddle_classic_enabled" value="0">

                        <div class="toggle-switch-group">
                            <label class="toggle-switch">
                                <input type="checkbox" name="paddle_classic_enabled" value="1" <?= ($settings['paddle_classic_enabled'] ?? '') == '1' ? 'checked' : '' ?> class="gateway-toggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label">Enable</span>
                        </div>
                        <p class="form-text mb-3" style="margin-top: -1rem; margin-bottom: 1.5rem !important;">Collect payments securely with Paddle. This payment method is not available for new Paddle accounts. You need to use Paddle Billing instead.</p>

                         <div class="gateway-settings">
                             <div class="form-group">
                                <label class="form-label" for="paddle_vendor_id">Paddle Vendor ID</label>
                                <input type="text" class="form-control" id="paddle_vendor_id" name="paddle_vendor_id" value="<?= htmlspecialchars($settings['paddle_vendor_id'] ?? '') ?>">
                                <div class="form-text">Get your vendor id from here once logged in click here</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="paddle_classic_api_key">Paddle API Key</label>
                                <input type="text" class="form-control" id="paddle_classic_api_key" name="paddle_classic_api_key" value="<?= htmlspecialchars($settings['paddle_classic_api_key'] ?? '') ?>">
                                 <div class="form-text">Get your paddle keys from here once logged in click here</div>
                            </div>
                             <div class="form-group">
                                <label class="form-label" for="paddle_public_key">Paddle Public Key</label>
                                <textarea class="form-control" id="paddle_public_key" name="paddle_public_key" rows="2"><?= htmlspecialchars($settings['paddle_public_key'] ?? '') ?></textarea>
                                <div class="form-text">Get your paddle public key from here once logged in click here</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Webhook URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control readonly-field" value="<?= app_base_url('/webhook/paddle') ?>" readonly>
                                    <button type="button" class="copy-btn" onclick="copyToClipboard('<?= app_base_url('/webhook/paddle') ?>')">Copy</button>
                                </div>
                                <div class="form-text">You can add your webhooks here. For more info, please check the docs.</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="paddle_monthly_plan_id">Monthly Plan ID</label>
                                <input type="text" class="form-control" id="paddle_monthly_plan_id" name="paddle_monthly_plan_id" value="<?= htmlspecialchars($settings['paddle_monthly_plan_id'] ?? '') ?>">
                                 <div class="form-text">You need to create a single monthly plan manually and insert the plan ID here. View documentation for more information.</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="paddle_yearly_plan_id">Yearly Plan ID</label>
                                <input type="text" class="form-control" id="paddle_yearly_plan_id" name="paddle_yearly_plan_id" value="<?= htmlspecialchars($settings['paddle_yearly_plan_id'] ?? '') ?>">
                                 <div class="form-text">You need to create a single yearly plan manually and insert the plan ID here. View documentation for more information.</div>
                            </div>
                        </div>

                         <button type="submit" class="btn btn-save">Save Settings</button>
                    </form>
                </div>
            </div>

            <!-- PayStack -->
             <div class="settings-card">
                <div class="card-header-simple">
                     <span style="color:red; font-size: 0.8em; float: right;">You cannot enable both Stripe and PayStack at the same time because they both work in the same way. You must choose one.</span>
                    <h2>PayStack Payments</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo app_base_url('/admin/settings/payments/update'); ?>" method="POST" class="ajax-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="gateway" value="paystack">
                        <input type="hidden" name="paystack_enabled" value="0">

                         <div class="toggle-switch-group">
                            <label class="toggle-switch">
                                <input type="checkbox" name="paystack_enabled" value="1" <?= ($settings['paystack_enabled'] ?? '') == '1' ? 'checked' : '' ?> class="gateway-toggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label">Enable</span>
                        </div>
                        <p class="form-text mb-3" style="margin-top: -1rem; margin-bottom: 1.5rem !important;">Collect payments securely with PayStack.</p>

                        <div class="gateway-settings">
                             <div class="form-group">
                                <label class="form-label" for="paystack_secret_key">Secret Key</label>
                                <input type="password" class="form-control" id="paystack_secret_key" name="paystack_secret_key" value="<?= htmlspecialchars($settings['paystack_secret_key'] ?? '') ?>">
                                <div class="form-text">Get your paystack keys from here once logged in click here</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="paystack_public_key">Public Key</label>
                                <input type="text" class="form-control" id="paystack_public_key" name="paystack_public_key" value="<?= htmlspecialchars($settings['paystack_public_key'] ?? '') ?>">
                                <div class="form-text">Get your paystack keys from here once logged in click here</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Webhook URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control readonly-field" value="<?= app_base_url('/webhook/paystack') ?>" readonly>
                                    <button type="button" class="copy-btn" onclick="copyToClipboard('<?= app_base_url('/webhook/paystack') ?>')">Copy</button>
                                </div>
                                <div class="form-text">You can add your webhooks here. For more info, please check the docs.</div>
                            </div>
                        </div>

                         <button type="submit" class="btn btn-save">Save Settings</button>
                    </form>
                </div>
            </div>

             <!-- Bank Transfer -->
             <div class="settings-card">
                <div class="card-header-simple">
                    <h2>Bank Transfer</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo app_base_url('/admin/settings/payments/update'); ?>" method="POST" class="ajax-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="gateway" value="bank_transfer">
                        <input type="hidden" name="bank_transfer_enabled" value="0">

                        <div class="toggle-switch-group">
                            <label class="toggle-switch">
                                <input type="checkbox" name="bank_transfer_enabled" value="1" <?= ($settings['bank_transfer_enabled'] ?? '') == '1' ? 'checked' : '' ?> class="gateway-toggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label">Enable</span>
                        </div>
                        <p class="form-text mb-3" style="margin-top: -1rem; margin-bottom: 1.5rem !important;">Transfer payments via your bank.</p>

                        <div class="gateway-settings">
                            <div class="form-group">
                                <label class="form-label" for="bank_info">Bank Info</label>
                                <textarea class="form-control" id="bank_info" name="bank_info" rows="5" placeholder="Enter the full information where your users can send payments to via their bank."><?= htmlspecialchars($settings['bank_info'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                         <button type="submit" class="btn btn-save">Save Settings</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle toggle switch logic
    const toggles = document.querySelectorAll('.gateway-toggle');
    
    toggles.forEach(toggle => {
        // Initial check
        handleToggle(toggle);
        
        // Add change listener
        toggle.addEventListener('change', function() {
            handleToggle(this);
        });
    });

    function handleToggle(toggle) {
        const settingsContainer = toggle.closest('form').querySelector('.gateway-settings');
        if (settingsContainer) {
            if (toggle.checked) {
                settingsContainer.classList.remove('hidden');
            } else {
                settingsContainer.classList.add('hidden');
            }
        }
    }
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // You would ideally show a toast notification here
        showNotification("Copied to clipboard!", "success");
    }, (err) => {
        console.error('Could not copy text: ', err);
    });
}
</script>
