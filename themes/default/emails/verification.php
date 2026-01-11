<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .button { display: inline-block; background: #4f46e5 !important; color: white !important; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 {{site_name}}</h1>
            <p>Professional Engineering Calculations</p>
        </div>
        <div class="content">
            <h2>Verify Your Email Address</h2>
            <p>Hello {{full_name}},</p>
            <p>Thank you for signing up with {{site_name}}! To complete your registration and start using our professional engineering calculation tools, please verify your email address.</p>
            <div style="text-align: center;">
                <a href="{{verification_link}}" class="button">Verify Email Address</a>
            </div>
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p><a href="{{verification_link}}">{{verification_link}}</a></p>
            <p>This verification link will expire in 24 hours for security purposes.</p>
            <p>If you didn't create an account with {{site_name}}, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; {{current_year}} {{site_name}}. All rights reserved.</p>
            <p>Professional Engineering Calculations | Building Excellence Through Technology</p>
        </div>
    </div>
</body>
</html>
