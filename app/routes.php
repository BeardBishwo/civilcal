<?php
// Routes Definition - Complete Updated Version
// This file defines all application routes with comprehensive features

// Get router from global scope
$router = $GLOBALS['router'];

// Public Routes
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/features', 'HomeController@features');
$router->add('GET', '/pricing', 'HomeController@pricing');
$router->add('GET', '/about', 'HomeController@about');
$router->add('GET', '/contact', 'HomeController@contact');
$router->add('POST', '/contact', 'HomeController@contact');

// Authentication Routes
$router->add('GET', '/login', 'AuthController@showLogin', ['guest']);
$router->add('POST', '/login', 'AuthController@login', ['guest']);
$router->add('GET', '/register', 'AuthController@showRegister', ['guest']);
$router->add('POST', '/register', 'AuthController@register', ['guest']);
$router->add('POST', '/logout', 'AuthController@logout', ['auth']);

// Calculator Routes (Public)
$router->add('GET', '/calculators', 'CalculatorController@index');
$router->add('GET', '/calculator/{category}', 'CalculatorController@category');
$router->add('GET', '/calculator/{category}/{tool}', 'CalculatorController@tool');
$router->add('POST', '/calculator/{category}/{tool}/calculate', 'CalculatorController@calculate');

// Calculator Routes (Protected)
$router->add('GET', '/dashboard', 'CalculatorController@dashboard', ['auth']);
$router->add('GET', '/calculators/protected', 'CalculatorController@index', ['auth']);
$router->add('GET', '/calculators/{category}/protected', 'CalculatorController@category', ['auth']);
$router->add('GET', '/calculators/{category}/{calculator}/protected', 'CalculatorController@show', ['auth']);
$router->add('POST', '/api/calculate/{calculator}/protected', 'ApiController@calculate', ['auth']);

// Traditional Units Calculator Routes
$router->add('GET', '/calculators/traditional-units', 'CalculatorController@traditionalUnits');
$router->add('GET', '/calculators/traditional-units/protected', 'CalculatorController@traditionalUnits', ['auth']);
$router->add('POST', '/api/traditional-units/convert', 'ApiController@traditionalUnitsConvert');
$router->add('POST', '/api/traditional-units/convert/protected', 'ApiController@traditionalUnitsConvert', ['auth']);
$router->add('POST', '/api/traditional-units/all-conversions', 'ApiController@traditionalUnitsAllConversions');
$router->add('POST', '/api/traditional-units/all-conversions/protected', 'ApiController@traditionalUnitsAllConversions', ['auth']);

// User Routes
$router->add('GET', '/profile', 'ProfileController@index', ['auth']);
$router->add('POST', '/profile/update', 'ProfileController@update', ['auth']);
$router->add('POST', '/profile/change-password', 'ProfileController@changePassword', ['auth']);
$router->add('GET', '/history', 'ProfileController@history', ['auth']);
$router->add('POST', '/history/delete/{id}', 'ProfileController@deleteCalculation', ['auth']);

// Extended Profile Management Routes (from original routes.php)
$router->add('GET', '/user/profile', 'ProfileController@index', ['auth']);
$router->add('POST', '/profile/update', 'ProfileController@updateProfile', ['auth']);
$router->add('POST', '/profile/notifications', 'ProfileController@updateNotifications', ['auth']);
$router->add('POST', '/profile/privacy', 'ProfileController@updatePrivacy', ['auth']);
$router->add('POST', '/profile/password', 'ProfileController@changePassword', ['auth']);
$router->add('POST', '/profile/delete', 'ProfileController@deleteAccount', ['auth']);
$router->add('GET', '/profile/avatar/{filename}', 'ProfileController@serveAvatar', ['auth']);

// API Routes (Enhanced)
$router->add('POST', '/api/calculate', 'ApiController@calculate');
$router->add('GET', '/api/calculators', 'ApiController@getCalculators');
$router->add('GET', '/api/calculator/{category}/{tool}', 'ApiController@getCalculator');
$router->add('GET', '/api/calculations', 'ApiController@getUserCalculations', ['auth']);
$router->add('GET', '/api/calculations/{id}', 'ApiController@getCalculation', ['auth']);

// Admin Routes
$router->add('GET', '/admin', 'Admin\DashboardController@index', ['auth', 'admin']);
$router->add('GET', '/admin/users', 'Admin\UserController@index', ['auth', 'admin']);
$router->add('GET', '/admin/settings', 'Admin\SettingsController@index', ['auth', 'admin']);
$router->add('POST', '/admin/settings/save', 'Admin\SettingsController@saveSettings', ['auth', 'admin']);

// Calculators Management Routes
$router->add('GET', '/admin/calculators', 'Admin\CalculatorController@index', ['auth', 'admin']);
$router->add('POST', '/admin/calculators/add', 'Admin\CalculatorController@addCalculator', ['auth', 'admin']);

// Modules Management Routes
$router->add('GET', '/admin/modules', 'Admin\ModuleController@index', ['auth', 'admin']);

// Widget Management Routes
$router->add('GET', '/admin/widgets', 'WidgetController@index', ['auth', 'admin']);
$router->add('GET', '/admin/widgets/create', 'WidgetController@create', ['auth', 'admin']);
$router->add('POST', '/admin/widgets/create', 'WidgetController@create', ['auth', 'admin']);
$router->add('GET', '/admin/widgets/edit/{id}', 'WidgetController@edit', ['auth', 'admin']);
$router->add('POST', '/admin/widgets/edit/{id}', 'WidgetController@edit', ['auth', 'admin']);
$router->add('POST', '/admin/widgets/delete/{id}', 'WidgetController@delete', ['auth', 'admin']);
$router->add('POST', '/admin/widgets/toggle/{id}', 'WidgetController@toggle', ['auth', 'admin']);
$router->add('POST', '/admin/widgets/toggle-visibility/{id}', 'WidgetController@toggleVisibility', ['auth', 'admin']);
$router->add('POST', '/admin/widgets/reorder', 'WidgetController@reorder', ['auth', 'admin']);
$router->add('GET', '/admin/widgets/preview/{id}', 'WidgetController@preview', ['auth', 'admin']);
$router->add('GET', '/admin/widgets/settings/{id}', 'WidgetController@settings', ['auth', 'admin']);
$router->add('POST', '/admin/widgets/settings/{id}', 'WidgetController@settings', ['auth', 'admin']);
$router->add('GET', '/admin/widgets/setup', 'WidgetController@setup', ['auth', 'admin']);

// Widget API Routes
$router->add('GET', '/api/widgets/render', 'ApiController@renderWidgets', ['auth']);
$router->add('POST', '/api/widgets/setting/{id}', 'ApiController@updateWidgetSetting', ['auth']);

// Plugin Management Routes
$router->add('GET', '/admin/plugins', 'Admin\PluginController@index', ['auth', 'admin']);
$router->add('POST', '/admin/plugins/upload', 'Admin\PluginController@upload', ['auth', 'admin']);
$router->add('POST', '/admin/plugins/toggle/{slug}/{action}', 'Admin\PluginController@toggle', ['auth', 'admin']);
$router->add('POST', '/admin/plugins/delete/{slug}', 'Admin\PluginController@delete', ['auth', 'admin']);
$router->add('GET', '/admin/plugins/details/{slug}', 'Admin\PluginController@details', ['auth', 'admin']);
$router->add('POST', '/admin/plugins/refresh', 'Admin\PluginController@refresh', ['auth', 'admin']);

// Theme Management Routes
$router->add('GET', '/admin/themes', 'Admin\ThemeController@index', ['auth', 'admin']);
$router->add('POST', '/admin/themes/upload', 'Admin\ThemeController@upload', ['auth', 'admin']);
$router->add('POST', '/admin/themes/activate/{slug}', 'Admin\ThemeController@activate', ['auth', 'admin']);
$router->add('POST', '/admin/themes/delete/{slug}', 'Admin\ThemeController@delete', ['auth', 'admin']);
$router->add('GET', '/admin/themes/details/{slug}', 'Admin\ThemeController@details', ['auth', 'admin']);

// History Management Routes
$router->add('GET', '/history', 'HistoryController@index', ['auth']);
$router->add('GET', '/history/search', 'HistoryController@search', ['auth']);
$router->add('POST', '/history/save', 'HistoryController@saveCalculation', ['auth']);
$router->add('POST', '/history/favorite/{id}', 'HistoryController@toggleFavorite', ['auth']);
$router->add('GET', '/history/delete/{id}', 'HistoryController@delete', ['auth']);
$router->add('GET', '/history/view/{id}', 'HistoryController@view', ['auth']);
$router->add('GET', '/history/stats', 'HistoryController@stats', ['auth']);
$router->add('GET', '/history/recent', 'HistoryController@recent', ['auth']);
$router->add('GET', '/history/by-type/{calculatorType}', 'HistoryController@byType', ['auth']);
$router->add('GET', '/history/export', 'HistoryController@export', ['auth']);
$router->add('POST', '/history/bulk-delete', 'HistoryController@bulkDelete', ['auth']);
$router->add('POST', '/history/bulk-favorite', 'HistoryController@bulkFavorite', ['auth']);

// Export Management Routes
$router->add('GET', '/user/exports/templates', 'ExportController@templates', ['auth']);
$router->add('POST', '/user/exports/create-template', 'ExportController@createTemplate', ['auth']);
$router->add('POST', '/user/exports/update-template/{id}', 'ExportController@updateTemplate', ['auth']);
$router->add('POST', '/user/exports/delete-template/{id}', 'ExportController@deleteTemplate', ['auth']);
$router->add('POST', '/user/exports/duplicate-template/{id}', 'ExportController@duplicateTemplate', ['auth']);
$router->add('GET', '/user/exports/template-config/{id}', 'ExportController@getTemplateConfig', ['auth']);
$router->add('POST', '/user/exports/export', 'ExportController@export', ['auth']);
$router->add('GET', '/user/exports/download/{filename}', 'ExportController@download', ['auth']);
$router->add('GET', '/user/exports/search', 'ExportController@searchTemplates', ['auth']);
$router->add('GET', '/user/exports/stats', 'ExportController@getStats', ['auth']);
$router->add('POST', '/user/exports/cleanup', 'ExportController@cleanup', ['auth']);

// Share & Comment System Routes
$router->add('GET', '/share/create', 'ShareController@create', ['auth']);
$router->add('POST', '/share/store', 'ShareController@store', ['auth']);
$router->add('GET', '/share/my-shares', 'ShareController@myShares', ['auth']);
$router->add('GET', '/share/public/{token}', 'ShareController@publicView');
$router->add('POST', '/share/{id}/embed', 'ShareController@generateEmbed', ['auth']);
$router->add('DELETE', '/share/{id}', 'ShareController@destroy', ['auth']);

// Comment System Routes
$router->add('POST', '/comments', 'CommentController@store', ['auth']);
$router->add('POST', '/comments/{id}/reply', 'CommentController@reply', ['auth']);
$router->add('POST', '/comments/{id}/vote', 'CommentController@vote', ['auth']);
$router->add('PUT', '/comments/{id}', 'CommentController@update', ['auth']);
$router->add('DELETE', '/comments/{id}', 'CommentController@destroy', ['auth']);
$router->add('GET', '/comments/share/{shareId}', 'CommentController@getByShare');

// Email & Notifications Management Routes
$router->add('GET', '/admin/email', 'Admin\EmailManagerController@index', ['auth', 'admin']);
$router->add('POST', '/admin/email/send-test', 'Admin\EmailManagerController@sendTestEmail', ['auth', 'admin']);
$router->add('POST', '/admin/email/save-template', 'Admin\EmailManagerController@saveTemplate', ['auth', 'admin']);

// Billing & Subscriptions Management Routes
$router->add('GET', '/admin/subscriptions', 'Admin\SubscriptionController@index', ['auth', 'admin']);
$router->add('POST', '/admin/subscriptions/create-plan', 'Admin\SubscriptionController@createPlan', ['auth', 'admin']);

// Help & Logs Management Routes
$router->add('GET', '/admin/help', 'Admin\HelpController@index', ['auth', 'admin']);
$router->add('POST', '/admin/help/clear-logs', 'Admin\HelpController@clearLogs', ['auth', 'admin']);
$router->add('POST', '/admin/help/backup', 'Admin\HelpController@backupSystem', ['auth', 'admin']);

// Email Manager Admin Routes
$router->add('GET', '/admin/email-manager', 'Admin\EmailManagerController@dashboard', ['auth', 'admin']);
$router->add('GET', '/admin/email-manager/threads', 'Admin\EmailManagerController@threads', ['auth', 'admin']);
$router->add('GET', '/admin/email-manager/thread/{id}', 'Admin\EmailManagerController@viewThread', ['auth', 'admin']);
$router->add('POST', '/admin/email-manager/thread/{id}/reply', 'Admin\EmailManagerController@reply', ['auth', 'admin']);
$router->add('POST', '/admin/email-manager/thread/{id}/status', 'Admin\EmailManagerController@updateStatus', ['auth', 'admin']);
$router->add('POST', '/admin/email-manager/thread/{id}/assign', 'Admin\EmailManagerController@assign', ['auth', 'admin']);
$router->add('POST', '/admin/email-manager/thread/{id}/priority', 'Admin\EmailManagerController@updatePriority', ['auth', 'admin']);

// Email Templates Admin Routes
$router->add('GET', '/admin/email-manager/templates', 'Admin\EmailManagerController@templates', ['auth', 'admin']);
$router->add('POST', '/admin/email-manager/templates', 'Admin\EmailManagerController@createTemplate', ['auth', 'admin']);
$router->add('GET', '/admin/email-manager/template/{id}', 'Admin\EmailManagerController@editTemplate', ['auth', 'admin']);
$router->add('PUT', '/admin/email-manager/template/{id}', 'Admin\EmailManagerController@updateTemplate', ['auth', 'admin']);
$router->add('DELETE', '/admin/email-manager/template/{id}', 'Admin\EmailManagerController@deleteTemplate', ['auth', 'admin']);
$router->add('POST', '/admin/email-manager/templates/{id}/use', 'Admin\EmailManagerController@useTemplate', ['auth', 'admin']);

// Error Monitoring & Logging Routes
$router->add('GET', '/admin/error-logs', 'Admin\ErrorLogController@index', ['auth', 'admin']);
$router->add('GET', '/admin/error-logs/get-error-stats', 'Admin\ErrorLogController@getErrorStats', ['auth', 'admin']);
$router->add('GET', '/admin/error-logs/get-method-calls', 'Admin\ErrorLogController@getMethodCalls', ['auth', 'admin']);
$router->add('GET', '/admin/error-logs/get-failed-calls', 'Admin\ErrorLogController@getFailedCalls', ['auth', 'admin']);
$router->add('POST', '/admin/error-logs/clear-logs', 'Admin\ErrorLogController@clearLogs', ['auth', 'admin']);
$router->add('GET', '/admin/error-logs/export-logs', 'Admin\ErrorLogController@exportLogs', ['auth', 'admin']);

// API Routes for Share & Comment System
$router->add('GET', '/api/comments/{shareId}', 'CommentController@getByShare');
$router->add('POST', '/api/comments/{id}/vote', 'CommentController@vote', ['auth']);
$router->add('POST', '/api/share/{id}/embed', 'ShareController@generateEmbed', ['auth']);
$router->add('POST', '/api/share', 'ShareController@store', ['auth']);
?>
