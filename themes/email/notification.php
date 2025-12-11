<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f3f4f6;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            color: white;
            font-size: 28px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .notification-icon {
            width: 64px;
            height: 64px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 32px;
        }
        .notification-title {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 16px;
            text-align: center;
        }
        .notification-message {
            font-size: 16px;
            line-height: 1.6;
            color: #6b7280;
            margin: 0 0 32px;
            text-align: center;
        }
        .action-button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 16px;
            text-align: center;
        }
        .button-container {
            text-align: center;
            margin: 32px 0;
        }
        .email-footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            font-size: 14px;
            color: #9ca3af;
            margin: 0 0 16px;
        }
        .footer-links {
            margin: 16px 0 0;
        }
        .footer-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 12px;
            font-size: 14px;
        }
        .unsubscribe {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 16px;
        }
        .unsubscribe a {
            color: #9ca3af;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1><?= htmlspecialchars($siteName ?? 'Bishwo Calculator') ?></h1>
        </div>
        
        <div class="email-body">
            <?php if (isset($icon)): ?>
            <div class="notification-icon">
                <?= $icon ?>
            </div>
            <?php endif; ?>
            
            <h2 class="notification-title"><?= htmlspecialchars($title) ?></h2>
            <p class="notification-message"><?= nl2br(htmlspecialchars($message)) ?></p>
            
            <?php if (isset($actionUrl) && isset($actionText)): ?>
            <div class="button-container">
                <a href="<?= htmlspecialchars($actionUrl) ?>" class="action-button">
                    <?= htmlspecialchars($actionText) ?>
                </a>
            </div>
            <?php endif; ?>
            
            <?php if (isset($metadata) && is_array($metadata)): ?>
            <div style="margin-top: 32px; padding: 20px; background: #f9fafb; border-radius: 8px;">
                <?php foreach ($metadata as $key => $value): ?>
                <p style="margin: 8px 0; font-size: 14px; color: #6b7280;">
                    <strong><?= htmlspecialchars(ucfirst($key)) ?>:</strong> <?= htmlspecialchars($value) ?>
                </p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="email-footer">
            <p class="footer-text">
                This is an automated notification from <?= htmlspecialchars($siteName ?? 'Bishwo Calculator') ?>
            </p>
            <div class="footer-links">
                <a href="<?= $baseUrl ?? '' ?>">Visit Website</a>
                <a href="<?= $baseUrl ?? '' ?>/notifications/preferences">Notification Settings</a>
                <a href="<?= $baseUrl ?? '' ?>/help">Help Center</a>
            </div>
            <p class="unsubscribe">
                Don't want these emails? <a href="<?= $baseUrl ?? '' ?>/notifications/preferences">Update your preferences</a>
            </p>
        </div>
    </div>
</body>
</html>
