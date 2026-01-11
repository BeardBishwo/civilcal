<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 30px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .button { display: inline-block; background: #10b981 !important; color: white !important; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .features { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Welcome to {{site_name}}!</h1>
            <p>Your Professional Engineering Toolkit</p>
        </div>
        <div class="content">
            <h2>Account Verified Successfully!</h2>
            <p>Hello {{full_name}},</p>
            <p>Congratulations! Your {{site_name}} account has been verified and is now fully active. You can now access all our professional engineering calculation tools.</p>
            
            <div class="features">
                <h3>🚀 What's Available Now:</h3>
                <ul>
                    <li><strong>Civil Engineering:</strong> Structural calculations, concrete design, foundation analysis</li>
                    <li><strong>Electrical Engineering:</strong> Load calculations, power distribution, circuit design</li>
                    <li><strong>Mechanical/HVAC:</strong> Ventilation, heating/cooling load calculations</li>
                    <li><strong>Fire Safety:</strong> Fire protection system calculations</li>
                    <li><strong>Plumbing:</strong> Water supply and drainage calculations</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="{{login_url}}" class="button">Start Calculating</a>
            </div>
            
            <p><strong>Username:</strong> {{username}}</p>
            <p>If you have any questions or need support, don't hesitate to contact us.</p>
            <p>Welcome aboard!</p>
        </div>
        <div class="footer">
            <p>&copy; {{current_year}} {{site_name}}. All rights reserved.</p>
            <p>Building Excellence Through Professional Engineering Tools</p>
        </div>
    </div>
</body>
</html>
