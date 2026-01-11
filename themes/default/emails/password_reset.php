<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 30px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .button { display: inline-block; background: #ef4444 !important; color: white !important; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset Request</h1>
            <p>EngiCal Pro Security</p>
        </div>
        <div class="content">
            <h2>Reset Your Password</h2>
            <p>Hello {{full_name}},</p>
            <p>We received a request to reset your password for your EngiCal Pro account. Click the button below to create a new password:</p>
            <div style="text-align: center;">
                <a href="{{reset_link}}" class="button">Reset Password</a>
            </div>
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p><a href="{{reset_link}}">{{reset_link}}</a></p>
            <p><strong>This reset link will expire in 1 hour for security purposes.</strong></p>
            <p>If you didn't request a password reset, please ignore this email. Your password will remain unchanged.</p>
        </div>
        <div class="footer">
            <p>&copy; {{current_year}} {{site_name}}. All rights reserved.</p>
            <p>For security reasons, this email was sent to {{email}}</p>
        </div>
    </div>
</body>
</html>
