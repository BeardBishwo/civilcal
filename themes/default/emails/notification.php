<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{title}}</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div style="max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

        <!-- Header / Logo -->
        <div style="background-color: #ffffff; padding: 20px; text-align: center; border-bottom: 1px solid #f3f4f6;">
            <h2 style="margin: 0; color: #111827; font-size: 24px; font-weight: 800;">{{site_name}}</h2>
        </div>

        <!-- Billboard Image (Conditional Logic handled by PHP replacement if possible, or we assume variable is empty string if not set) -->
        <!-- We will use a placeholder in HTML and replace it in PHP if it exists -->
        {{billboard_section}}

        <!-- Content -->
        <div style="padding: 40px 30px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <span style="font-size: 48px;">{{icon}}</span>
            </div>

            <h1 style="margin: 0 0 16px; color: #111827; font-size: 24px; font-weight: 700; text-align: center;">{{title}}</h1>

            <div style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 32px;">
                {{message}}
            </div>

            <!-- Action Button -->
            {{action_section}}

        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0; color: #6b7280; font-size: 14px;">
                &copy; {{current_year}} {{site_name}}. All rights reserved.
            </p>
            <p style="margin: 10px 0 0; color: #9ca3af; font-size: 12px;">
                You received this email because you have notifications enabled.
                <a href="{{baseUrl}}/user/settings" style="color: #6b7280; text-decoration: underline;">Manage Preferences</a>
            </p>
        </div>
    </div>
</body>

</html>