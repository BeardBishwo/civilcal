<?php
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php';
require_once dirname(__DIR__, 4) . '/app/Config/ComplianceConfig.php';
?>

<div class="container my-5">
    <h1>Terms of Service</h1>
    <p class="lead">Last updated: <?php echo date('F j, Y'); ?></p>

    <section class="mb-4">
        <h2>1. Agreement to Terms</h2>
        <p>By accessing or using the AEC Calculator ("Service"), you agree to be bound by these Terms of Service and all applicable laws and regulations. If you do not agree with any of these terms, you are prohibited from using the Service.</p>
    </section>

    <section class="mb-4">
        <h2>2. Use License</h2>
        <p>Permission is granted to temporarily access and use the Service for personal, non-commercial use only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
        <ul>
            <li>Modify or copy the Service's materials</li>
            <li>Use the Service for any commercial purpose</li>
            <li>Attempt to decompile or reverse engineer any software contained in the Service</li>
            <li>Remove any copyright or other proprietary notations</li>
            <li>Transfer the materials to another person</li>
        </ul>
    </section>

    <section class="mb-4">
        <h2>3. Disclaimer</h2>
        <p>The Service's materials are provided on an 'as is' basis. The AEC Calculator:</p>
        <ul>
            <li>Makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</li>
            <li>Does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its website or otherwise relating to such materials or on any sites linked to this site.</li>
        </ul>
    </section>

    <section class="mb-4">
        <h2>4. Limitations</h2>
        <p>In no event shall the AEC Calculator or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the Service, even if the AEC Calculator or an authorized representative has been notified orally or in writing of the possibility of such damage.</p>
    </section>

    <section class="mb-4">
        <h2>5. Accuracy of Materials</h2>
        <p>While we strive for accuracy, the materials appearing in the Service could include technical, typographical, or photographic errors. The AEC Calculator does not warrant that any of the materials on its website are accurate, complete or current. The AEC Calculator may make changes to the materials contained in the Service at any time without notice.</p>
    </section>

    <section class="mb-4">
        <h2>6. Account Terms</h2>
        <p>To access certain features of the Service, you must register for an account. When you create an account, you must:</p>
        <ul>
            <li>Provide accurate and complete information</li>
            <li>Maintain the security of your account credentials</li>
            <li>Notify us immediately of any unauthorized access</li>
            <li>Be responsible for all activities under your account</li>
        </ul>
    </section>

    <section class="mb-4">
        <h2>7. Payment Terms</h2>
        <?php if (ComplianceConfig::SUBSCRIPTION_ENABLED): ?>
        <p>For premium features:</p>
        <ul>
            <li>Subscription fees are billed in advance</li>
            <li>Subscriptions automatically renew unless cancelled</li>
            <li>Refunds are provided according to our refund policy</li>
            <li>We may change subscription prices with 30 days notice</li>
        </ul>
        <?php endif; ?>
    </section>

    <section class="mb-4">
        <h2>8. Modifications</h2>
        <p>The AEC Calculator may revise these terms of service at any time without notice. By using this Service you are agreeing to be bound by the then current version of these terms of service.</p>
    </section>

    <section class="mb-4">
        <h2>9. Governing Law</h2>
        <p>These terms and conditions are governed by and construed in accordance with the laws of [Your jurisdiction] and you irrevocably submit to the exclusive jurisdiction of the courts in that location.</p>
    </section>

    <section class="mb-4">
        <h2>10. Contact Information</h2>
        <p>Questions about the Terms of Service should be sent to:</p>
        <ul>
            <li>Email: legal@aeccalculator.com</li>
            <li>Address: [Your business address]</li>
        </ul>
    </section>
</div>

<?php require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php'; ?>


