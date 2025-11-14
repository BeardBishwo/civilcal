<?php
/**
 * Marketing Tools - Utility for managing marketing preferences and campaigns
 * This script provides tools for managing users who opted in for marketing
 */

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/app/bootstrap.php';

// Check if this is being run from command line or web
$isCommandLine = php_sapi_name() === 'cli';

if (!$isCommandLine) {
    // Basic security check for web access
    session_start();
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        die('Access denied. Admin privileges required.');
    }
    header('Content-Type: text/plain');
}

echo "📧 MARKETING TOOLS\n";
echo "==================\n\n";

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    // Get command line argument or web parameter
    $action = $isCommandLine ? ($argv[1] ?? 'help') : ($_GET['action'] ?? 'help');
    
    switch ($action) {
        case 'list':
            exportMarketingList($pdo);
            break;
            
        case 'stats':
            showMarketingStats($pdo);
            break;
            
        case 'export-csv':
            exportMarketingCSV($pdo);
            break;
            
        case 'export-json':
            exportMarketingJSON($pdo);
            break;
            
        case 'recent':
            showRecentOptIns($pdo);
            break;
            
        case 'help':
        default:
            showHelp();
            break;
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

/**
 * Export marketing list in readable format
 */
function exportMarketingList($pdo) {
    echo "📧 MARKETING EMAIL LIST\n";
    echo "=======================\n\n";
    
    $stmt = $pdo->prepare("
        SELECT 
            email, 
            first_name, 
            last_name, 
            username,
            created_at,
            terms_agreed_at
        FROM users 
        WHERE marketing_emails = 1 
        ORDER BY created_at DESC
    ");
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "No users have opted in for marketing emails.\n";
        return;
    }
    
    echo "Total marketing subscribers: " . count($users) . "\n\n";
    
    foreach ($users as $user) {
        $name = trim($user['first_name'] . ' ' . $user['last_name']);
        if (empty($name)) {
            $name = $user['username'];
        }
        
        echo "📧 {$user['email']}\n";
        echo "   Name: {$name}\n";
        echo "   Joined: " . date('M j, Y', strtotime($user['created_at'])) . "\n";
        echo "   Terms Agreed: " . ($user['terms_agreed_at'] ? date('M j, Y', strtotime($user['terms_agreed_at'])) : 'N/A') . "\n\n";
    }
}

/**
 * Show marketing statistics
 */
function showMarketingStats($pdo) {
    echo "📊 MARKETING STATISTICS\n";
    echo "=======================\n\n";
    
    // Total users
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Marketing opt-ins
    $optInStmt = $pdo->query("SELECT COUNT(*) as opt_in FROM users WHERE marketing_emails = 1");
    $optIn = $optInStmt->fetch(PDO::FETCH_ASSOC)['opt_in'];
    
    // Recent registrations (last 30 days)
    $recentStmt = $pdo->query("SELECT COUNT(*) as recent FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $recent = $recentStmt->fetch(PDO::FETCH_ASSOC)['recent'];
    
    // Recent marketing opt-ins (last 30 days)
    $recentMarketingStmt = $pdo->query("SELECT COUNT(*) as recent_marketing FROM users WHERE marketing_emails = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $recentMarketing = $recentMarketingStmt->fetch(PDO::FETCH_ASSOC)['recent_marketing'];
    
    $optInRate = $total > 0 ? round(($optIn / $total) * 100, 1) : 0;
    $recentOptInRate = $recent > 0 ? round(($recentMarketing / $recent) * 100, 1) : 0;
    
    echo "👥 Total Users: {$total}\n";
    echo "📧 Marketing Subscribers: {$optIn}\n";
    echo "📈 Overall Opt-in Rate: {$optInRate}%\n\n";
    
    echo "📅 Last 30 Days:\n";
    echo "   New Registrations: {$recent}\n";
    echo "   New Marketing Subscribers: {$recentMarketing}\n";
    echo "   Recent Opt-in Rate: {$recentOptInRate}%\n\n";
    
    // Growth by month
    echo "📈 MONTHLY GROWTH:\n";
    $monthlyStmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as total_users,
            SUM(marketing_emails) as marketing_users
        FROM users 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month DESC
    ");
    
    $monthlyData = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($monthlyData as $month) {
        $rate = $month['total_users'] > 0 ? round(($month['marketing_users'] / $month['total_users']) * 100, 1) : 0;
        echo "   {$month['month']}: {$month['marketing_users']}/{$month['total_users']} users ({$rate}%)\n";
    }
}

/**
 * Export marketing list as CSV
 */
function exportMarketingCSV($pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            email, 
            first_name, 
            last_name, 
            username,
            created_at,
            terms_agreed_at
        FROM users 
        WHERE marketing_emails = 1 
        ORDER BY created_at DESC
    ");
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $filename = 'marketing_list_' . date('Y-m-d') . '.csv';
    
    echo "📄 Exporting to CSV: {$filename}\n\n";
    
    // CSV Header
    echo "email,first_name,last_name,username,registration_date,terms_agreed_date\n";
    
    foreach ($users as $user) {
        echo "\"{$user['email']}\",\"{$user['first_name']}\",\"{$user['last_name']}\",\"{$user['username']}\",\"{$user['created_at']}\",\"{$user['terms_agreed_at']}\"\n";
    }
    
    echo "\n✅ CSV export complete! {$filename} contains " . count($users) . " marketing subscribers.\n";
}

/**
 * Export marketing list as JSON
 */
function exportMarketingJSON($pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            email, 
            first_name, 
            last_name, 
            username,
            created_at,
            terms_agreed_at
        FROM users 
        WHERE marketing_emails = 1 
        ORDER BY created_at DESC
    ");
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [
        'export_date' => date('Y-m-d H:i:s'),
        'total_subscribers' => count($users),
        'subscribers' => $users
    ];
    
    echo json_encode($data, JSON_PRETTY_PRINT);
}

/**
 * Show recent marketing opt-ins
 */
function showRecentOptIns($pdo, $days = 7) {
    echo "🆕 RECENT MARKETING OPT-INS (Last {$days} days)\n";
    echo "===============================================\n\n";
    
    $stmt = $pdo->prepare("
        SELECT 
            email, 
            first_name, 
            last_name, 
            username,
            created_at
        FROM users 
        WHERE marketing_emails = 1 
        AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ORDER BY created_at DESC
    ");
    
    $stmt->execute([$days]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "No new marketing opt-ins in the last {$days} days.\n";
        return;
    }
    
    echo "New marketing subscribers: " . count($users) . "\n\n";
    
    foreach ($users as $user) {
        $name = trim($user['first_name'] . ' ' . $user['last_name']);
        if (empty($name)) {
            $name = $user['username'];
        }
        
        echo "📧 {$user['email']} ({$name})\n";
        echo "   Registered: " . date('M j, Y H:i', strtotime($user['created_at'])) . "\n\n";
    }
}

/**
 * Show help information
 */
function showHelp() {
    echo "🔧 MARKETING TOOLS HELP\n";
    echo "=======================\n\n";
    
    echo "Available commands:\n\n";
    echo "📧 list          - Show all marketing subscribers\n";
    echo "📊 stats         - Show marketing statistics and growth\n";
    echo "📄 export-csv    - Export subscribers as CSV format\n";
    echo "📋 export-json   - Export subscribers as JSON format\n";
    echo "🆕 recent        - Show recent opt-ins (last 7 days)\n";
    echo "❓ help          - Show this help message\n\n";
    
    echo "Usage:\n";
    echo "Command line: php marketing_tools.php [command]\n";
    echo "Web access:   /marketing_tools.php?action=[command]\n\n";
    
    echo "Examples:\n";
    echo "php marketing_tools.php stats\n";
    echo "php marketing_tools.php export-csv > marketing_list.csv\n";
    echo "curl 'http://localhost/marketing_tools.php?action=list'\n\n";
    
    echo "📝 Notes:\n";
    echo "- Web access requires admin login\n";
    echo "- All data respects user privacy preferences\n";
    echo "- Only users who opted in are included\n";
    echo "- Export functions can be used for email campaigns\n";
}
?>
