<?php
require_once __DIR__ . '/../app/bootstrap.php';

echo "=======================================================\n";
echo "     Bishwo Calculator - Production Database Setup     \n";
echo "=======================================================\n\n";

echo "Starting database migrations...\n\n";

try {
    // List of all migration files in order
    $migrations = [
        
        '001_create_users_table.php',
        '001_plugin_theme_system.php',
        '002_create_subscriptions_table.php',
        '002_theme_editor_tables.php',
        '003_create_subscriptions_table.php',
        '004_create_calculation_history.php',
        '009_create_export_templates.php',
        '010_add_profile_fields_to_users.php',
        '011_create_shares_table.php',
        '012_create_comments_table.php',
        '013_create_votes_table.php',
        '014_create_email_threads_table.php',
        '015_create_email_responses_table.php',
        '016_create_email_templates_table.php',
        '018_create_complete_system_tables.php',


    ];

    $completed = 0;
    $failed = 0;

    $pdo = \App\Core\Database::getInstance()->getPdo();

    foreach ($migrations as $migrationFile) {
        $migrationPath = __DIR__ . '/migrations/' . $migrationFile;
        
        if (file_exists($migrationPath)) {
            echo "📁 Running migration: $migrationFile\n";
            
            try {
                require_once $migrationPath;
                
                // Convert filename to class name
                $className = str_replace('.php', '', $migrationFile);
                $className = preg_replace('/^\d+_/', '', $className);
                $className = str_replace('_', ' ', $className);
                $className = str_replace(' ', '', ucwords($className));
                
                if (class_exists($className)) {
                    $migration = new $className();
                    
                    if (method_exists($migration, 'up')) {
                        $reflection = new ReflectionMethod($migration, 'up');
                        if ($reflection->getNumberOfParameters() >= 1) {
                            $migration->up($pdo);
                        } else {
                            $migration->up();
                        }
                        echo "✅ Completed: $migrationFile\n\n";
                        $completed++;
                    } else {
                        echo "❌ No up() method found in: $className\n\n";
                        $failed++;
                    }
                } else {
                    echo "❌ Class not found: $className\n\n";
                    $failed++;
                }
                
            } catch (Exception $e) {
                echo "❌ Error in $migrationFile: " . $e->getMessage() . "\n\n";
                $failed++;
            }
            
        } else {
            echo "⚠️  Migration file not found: $migrationFile\n\n";
            $failed++;
        }
    }

    echo "=======================================================\n";
    echo "                 MIGRATION SUMMARY                     \n";
    echo "=======================================================\n";
    echo "✅ Completed migrations: $completed\n";
    echo "❌ Failed migrations: $failed\n";
    echo "📊 Total migrations: " . count($migrations) . "\n\n";

    if ($failed === 0) {
        echo "🎉 ALL MIGRATIONS COMPLETED SUCCESSFULLY!\n\n";
        
        echo "=======================================================\n";
        echo "            DEFAULT ADMIN ACCOUNT CREATED             \n";
        echo "=======================================================\n";
        echo "👤 Username: admin\n";
        echo "🔑 Password: admin123\n";
        echo "📧 Email: admin@bishwocalculator.com\n";
        echo "🔐 Role: Administrator\n\n";
        
        echo "⚠️  IMPORTANT SECURITY NOTES:\n";
        echo "   • Change the default password immediately\n";
        echo "   • Update admin email to your email address\n";
        echo "   • Enable email verification for new users\n";
        echo "   • Configure SMTP settings for production\n\n";
        
        echo "🌐 ADMIN PANEL ACCESS:\n";
        echo "   URL: http://yourdomain.com/admin\n";
        echo "   Or: http://localhost/bishwo_calculator/admin\n\n";
        
        echo "📋 NEXT STEPS FOR PRODUCTION:\n";
        echo "   1. Update database configuration\n";
        echo "   2. Configure environment variables\n";
        echo "   3. Set up SSL certificate\n";
        echo "   4. Configure email service (SMTP)\n";
        echo "   5. Set up regular backups\n";
        echo "   6. Configure monitoring and logging\n\n";
        
        echo "✅ Bishwo Calculator is ready for deployment!\n";
        
    } else {
        echo "⚠️  Some migrations failed. Please check the errors above.\n";
        echo "💡 Review and fix the issues, then run the migration again.\n";
    }

} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "📞 Please check your database configuration and try again.\n";
}

echo "\n=======================================================\n";
echo "                MIGRATION PROCESS COMPLETE             \n";
echo "=======================================================\n";
?>
