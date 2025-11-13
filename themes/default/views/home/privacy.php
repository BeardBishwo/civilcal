<?php
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php';
require_once dirname(__DIR__, 4) . '/app/Config/ComplianceConfig.php';
?>

<div class="container my-5">
    <h1>Privacy Policy</h1>
    <p class="lead">Last updated: <?php echo date('F j, Y'); ?></p>

    <section class="mb-4">
        <h2>1. Information We Collect</h2>
        <h3>1.1 Account Information</h3>
        <p>When you register for an account, we collect:</p>
        <ul>
            <li>Name</li>
            <li>Email address</li>
            <li>Company name (if provided)</li>
            <li>Password (stored securely using one-way encryption)</li>
        </ul>

        <h3>1.2 Usage Data</h3>
        <p>We collect data about how you use the service:</p>
        <ul>
            <li>Calculation history</li>
            <li>Saved favorites</li>
            <li>Login times and IP addresses for security</li>
        </ul>
    </section>

    <section class="mb-4">
        <h2>2. How We Use Your Information</h2>
        <ul>
            <li>To provide and maintain the service</li>
            <li>To notify you about changes to our service</li>
            <li>To provide customer support</li>
            <li>To detect, prevent and address technical issues</li>
        </ul>
    </section>

    <section class="mb-4">
        <h2>3. Data Retention</h2>
        <p>We retain different types of data for different periods:</p>
        <ul>
            <li>Account information: While your account is active</li>
            <li>Calculation history: <?php echo ComplianceConfig::RETENTION_PERIOD_HISTORY; ?> days</li>
            <li>Usage logs: <?php echo ComplianceConfig::RETENTION_PERIOD_LOGS; ?> days</li>
            <li>Contact form submissions: <?php echo ComplianceConfig::RETENTION_PERIOD_CONTACTS; ?> days</li>
        </ul>
    </section>

    <section class="mb-4">
        <h2>4. Your Rights</h2>
        <p>You have the right to:</p>
        <ul>
            <li>Access your personal data</li>
            <li>Correct inaccurate data</li>
            <li>Request deletion of your data</li>
            <li>Receive a copy of your data</li>
            <li>Object to processing of your data</li>
        </ul>
    </section>

    <section class="mb-4">
        <h2>5. Security</h2>
        <p>We implement appropriate security measures including:</p>
        <ul>
            <li>Encryption of data in transit and at rest</li>
            <li>Regular security assessments</li>
            <li>Access controls and authentication</li>
            <li>Regular backups</li>
            <li>Monitoring for suspicious activities</li>
        </ul>
    </section>

    <section class="mb-4">
        <h2>6. Contact Us</h2>
        <p>For privacy-related inquiries:</p>
        <ul>
            <li>Email: privacy@aeccalculator.com</li>
            <li>Address: [Your business address]</li>
        </ul>
    </section>

    <?php if (ComplianceConfig::GDPR_ENABLED): ?>
    <section class="mb-4">
        <h2>7. GDPR Rights</h2>
        <p>Under GDPR, you have additional rights:</p>
        <ul>
            <li>Right to be forgotten</li>
            <li>Data portability</li>
            <li>Restriction of processing</li>
            <li>Withdrawal of consent</li>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (ComplianceConfig::CCPA_ENABLED): ?>
    <section class="mb-4">
        <h2>8. California Privacy Rights</h2>
        <p>Under CCPA, California residents have the right to:</p>
        <ul>
            <li>Know what personal information is collected</li>
            <li>Know whether personal information is sold or disclosed</li>
            <li>Opt-out of the sale of personal information</li>
            <li>Access their personal information</li>
            <li>Equal service and price</li>
        </ul>
    </section>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php'; ?>


