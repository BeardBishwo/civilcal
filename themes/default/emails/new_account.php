<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #4f46e5, #4338ca); color: white; padding: 30px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .button { display: inline-block; background: #4f46e5 !important; color: white !important; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .credentials-box { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .warning { color: #b91c1c; background: #fef2f2; padding: 10px; border-radius: 4px; font-size: 14px; margin-top: 15px; border-left: 4px solid #ef4444; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{site_name}}</h1>
        </div>
        <div class="content">
            <h2>Your Account Has Been Created</h2>
            <p>Hello {{full_name}},</p>
            <p>An administrator has created an account for you. Here are your login credentials:</p>
            
            <div class="credentials-box">
                <p><strong>Username:</strong> {{username}}</p>
                <p><strong>Temporary Password:</strong> {{password}}</p>
            </div>

            <div class="warning">
                <strong>⚠️ Important:</strong> This temporary password will expire in 1 hour. Please log in immediately to change it.
            </div>
            
            <div style="text-align: center;">
                <a href="{{login_url}}" class="button">Log In Now</a>
            </div>
            
            <p>If the button doesn't work, you can login here:</p>
            <p><a href="{{login_url}}">{{login_url}}</a></p>
        </div>
        <div class="footer">
            <p>&copy; {{current_year}} {{site_name}}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
