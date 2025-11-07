<?php

class AddProfileFieldsToUsers
{
    public function up()
    {
        $sql = "ALTER TABLE users 
        ADD COLUMN avatar VARCHAR(255) NULL,
        ADD COLUMN professional_title VARCHAR(255) NULL,
        ADD COLUMN company VARCHAR(255) NULL,
        ADD COLUMN phone VARCHAR(20) NULL,
        ADD COLUMN timezone VARCHAR(100) NULL DEFAULT 'UTC',
        ADD COLUMN measurement_system ENUM('metric', 'imperial') DEFAULT 'metric',
        ADD COLUMN notification_preferences JSON NULL,
        ADD COLUMN email_notifications BOOLEAN DEFAULT TRUE,
        ADD COLUMN calculation_privacy ENUM('public', 'private', 'team') DEFAULT 'private',
        ADD COLUMN profile_completed BOOLEAN DEFAULT FALSE,
        ADD COLUMN last_login DATETIME NULL,
        ADD COLUMN login_count INT DEFAULT 0,
        ADD COLUMN bio TEXT NULL,
        ADD COLUMN website VARCHAR(255) NULL,
        ADD COLUMN location VARCHAR(255) NULL,
        ADD COLUMN social_links JSON NULL,
        ADD COLUMN email_verified_at DATETIME NULL,
        ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE,
        ADD COLUMN two_factor_secret VARCHAR(255) NULL,
        ADD COLUMN updated_at DATETIME NULL";
        
        return $sql;
    }

    public function down()
    {
        $sql = "ALTER TABLE users 
        DROP COLUMN IF EXISTS avatar,
        DROP COLUMN IF EXISTS professional_title,
        DROP COLUMN IF EXISTS company,
        DROP COLUMN IF EXISTS phone,
        DROP COLUMN IF EXISTS timezone,
        DROP COLUMN IF EXISTS measurement_system,
        DROP COLUMN IF EXISTS notification_preferences,
        DROP COLUMN IF EXISTS email_notifications,
        DROP COLUMN IF EXISTS calculation_privacy,
        DROP COLUMN IF EXISTS profile_completed,
        DROP COLUMN IF EXISTS last_login,
        DROP COLUMN IF EXISTS login_count,
        DROP COLUMN IF EXISTS bio,
        DROP COLUMN IF EXISTS website,
        DROP COLUMN IF EXISTS location,
        DROP COLUMN IF EXISTS social_links,
        DROP COLUMN IF EXISTS email_verified_at,
        DROP COLUMN IF EXISTS two_factor_enabled,
        DROP COLUMN IF EXISTS two_factor_secret,
        DROP COLUMN IF EXISTS updated_at";
        
        return $sql;
    }
}
?>
