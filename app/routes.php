<?php
// Routes Definition - Complete Updated Version
// This file defines all application routes with comprehensive features

// Get router from global scope
$router = $GLOBALS["router"];

// Public Routes
$router->add("GET", "/", "HomeController@index");
$router->add("GET", "/features", "HomeController@features");
$router->add("GET", "/pricing", "HomeController@pricing");
$router->add("GET", "/about", "HomeController@about");
$router->add("GET", "/contact", "HomeController@contact");
$router->add("POST", "/contact", "HomeController@contact");

// Authentication Routes
$router->add("GET", "/login", "AuthController@showLogin", ["guest"]);
$router->add("POST", "/login", "AuthController@login", ["guest"]);
$router->add("GET", "/register", "AuthController@showRegister", ["guest"]);
$router->add("POST", "/register", "AuthController@register", ["guest"]);
$router->add("GET", "/forgot-password", "AuthController@showForgotPassword", [
    "guest",
]);
$router->add("POST", "/forgot-password", "AuthController@forgotPassword", [
    "guest",
]);
$router->add("GET", "/logout", "AuthController@logout"); // No middleware - can be accessed anytime
$router->add("POST", "/logout", "AuthController@logout"); // No middleware - can be accessed anytime

// Calculator Routes (Public)
$router->add("GET", "/calculators", "CalculatorController@index");
$router->add("GET", "/calculator/{category}", "CalculatorController@category");
$router->add(
    "GET",
    "/calculator/{category}/{tool}",
    "CalculatorController@tool",
);
$router->add(
    "POST",
    "/calculator/{category}/{tool}/calculate",
    "CalculatorController@calculate",
);

// Calculator Routes (Protected)
$router->add("GET", "/dashboard", "CalculatorController@dashboard", ["auth"]);
$router->add("GET", "/calculators/protected", "CalculatorController@index", [
    "auth",
]);
$router->add(
    "GET",
    "/calculators/{category}/protected",
    "CalculatorController@category",
    ["auth"],
);
$router->add(
    "GET",
    "/calculators/{category}/{calculator}/protected",
    "CalculatorController@show",
    ["auth"],
);
$router->add(
    "POST",
    "/api/calculate/{calculator}/protected",
    "ApiController@calculate",
    ["auth"],
);

// Traditional Units Calculator Routes
$router->add(
    "GET",
    "/calculators/traditional-units",
    "CalculatorController@traditionalUnits",
);
$router->add(
    "GET",
    "/calculators/traditional-units/protected",
    "CalculatorController@traditionalUnits",
    ["auth"],
);
$router->add(
    "POST",
    "/api/traditional-units/convert",
    "ApiController@traditionalUnitsConvert",
);
$router->add(
    "POST",
    "/api/traditional-units/convert/protected",
    "ApiController@traditionalUnitsConvert",
    ["auth"],
);
$router->add(
    "POST",
    "/api/traditional-units/all-conversions",
    "ApiController@traditionalUnitsAllConversions",
);
$router->add(
    "POST",
    "/api/traditional-units/all-conversions/protected",
    "ApiController@traditionalUnitsAllConversions",
    ["auth"],
);

// User Routes
$router->add("GET", "/profile", "ProfileController@index", ["auth"]);
$router->add("POST", "/profile/update", "ProfileController@update", ["auth"]);
$router->add(
    "POST",
    "/profile/change-password",
    "ProfileController@changePassword",
    ["auth"],
);
$router->add("GET", "/history", "ProfileController@history", ["auth"]);
$router->add(
    "POST",
    "/history/delete/{id}",
    "ProfileController@deleteCalculation",
    ["auth"],
);

// Extended Profile Management Routes (from original routes.php)
$router->add("GET", "/user/profile", "ProfileController@index", ["auth"]);
$router->add("POST", "/profile/update", "ProfileController@updateProfile", [
    "auth",
]);
$router->add(
    "POST",
    "/profile/notifications",
    "ProfileController@updateNotifications",
    ["auth"],
);
$router->add("POST", "/profile/privacy", "ProfileController@updatePrivacy", [
    "auth",
]);
$router->add("POST", "/profile/password", "ProfileController@changePassword", [
    "auth",
]);
$router->add("POST", "/profile/delete", "ProfileController@deleteAccount", [
    "auth",
]);
$router->add(
    "GET",
    "/profile/avatar/{filename}",
    "ProfileController@serveAvatar",
    ["auth"],
);

// Authentication API Routes (for frontend AJAX)
$router->add("POST", "/api/login", "Api\AuthController@login");
$router->add("POST", "/api/register", "Api\AuthController@register");
$router->add(
    "POST",
    "/api/forgot-password",
    "Api\AuthController@forgotPassword",
);
$router->add("GET", "/api/logout", "Api\AuthController@logout");
$router->add(
    "GET",
    "/api/check-remember",
    "Api\AuthController@checkRememberToken",
);
$router->add("GET", "/api/user-status", "Api\AuthController@userStatus");
$router->add("GET", "/api/check-username", "Api\AuthController@checkUsername");
$router->add(
    "POST",
    "/api/resend-verification",
    "Api\AuthController@resendVerification",
);
$router->add("GET", "/api/location", "Api\LocationController@getLocation");
$router->add("GET", "/api/location/status", "Api\LocationController@getStatus");
$router->add(
    "GET",
    "/api/marketing/stats",
    "Api\MarketingController@getStats",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/api/marketing/opt-in-users",
    "Api\MarketingController@getOptInUsers",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/api/marketing/update-preferences",
    "Api\MarketingController@updatePreferences",
    ["auth"],
);

// Activity Log Routes
$router->add("GET", "/admin/activity", "Admin\ActivityController@index", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/activity/export",
    "Admin\ActivityController@export",
    ["auth", "admin"],
);

// Admin Dashboard Routes (WordPress-like admin system)
$router->add("GET", "/admin", "Admin\DashboardController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/dashboard", "Admin\\DashboardController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/configured-dashboard", "Admin\\DashboardController@configuredDashboard", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/dashboard/complex", "Admin\\DashboardController@dashboardComplex", [
    "auth",
    "admin",
]);

// Module Management
$router->add("GET", "/admin/modules", "Admin\DashboardController@modules", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/modules/activate",
    "Admin\DashboardController@activateModule",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/modules/deactivate",
    "Admin\DashboardController@deactivateModule",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/modules/{module}/settings",
    "Admin\DashboardController@moduleSettings",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/modules/settings/update",
    "Admin\DashboardController@updateModuleSettings",
    ["auth", "admin"],
);

// Logo & Branding Settings
$router->add("GET", "/admin/logo-settings", "Admin\LogoController@index", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/logo-settings", "Admin\LogoController@update", [
    "auth",
    "admin",
]);

// Admin Setup Checklist
$router->add(
    "GET",
    "/admin/setup/checklist",
    "Admin\SetupController@checklist",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/setup/update-item",
    "Admin\SetupController@updateItem",
    ["auth", "admin"],
);

// Comprehensive Admin Settings Routes
$router->add("GET", "/admin/settings", "Admin\SettingsController@index", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/settings/general",
    "Admin\SettingsController@general",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/settings/application",
    "Admin\SettingsController@application",
    ["auth", "admin"],
);
$router->add("GET", "/admin/settings/users", "Admin\SettingsController@users", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/settings/security",
    "Admin\SettingsController@security",
    ["auth", "admin"],
);
$router->add("GET", "/admin/settings/email", "Admin\SettingsController@email", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/settings/api", "Admin\SettingsController@api", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/settings/performance",
    "Admin\SettingsController@performance",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/settings/advanced",
    "Admin\SettingsController@advanced",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/settings/update",
    "Admin\SettingsController@update",
    ["auth", "admin"],
);

// Help Center Routes
$router->add("GET", "/help", "HelpController@index");
$router->add("GET", "/help/search", "HelpController@search");
$router->add("GET", "/help/category/{category}", "HelpController@category");
$router->add("GET", "/help/article/{slug}", "HelpController@article");

// Developer Documentation Routes
$router->add("GET", "/developers", "DeveloperController@index");
$router->add("GET", "/developers/{category}", "DeveloperController@category");
$router->add(
    "GET",
    "/developers/{category}/{endpoint}",
    "DeveloperController@endpoint",
);
$router->add("GET", "/developers/sdk", "DeveloperController@sdk");
$router->add("GET", "/developers/sdk/{language}", "DeveloperController@sdk");
$router->add("GET", "/developers/playground", "DeveloperController@playground");

// Advanced Admin Features
$router->add(
    "GET",
    "/admin/menu-customization",
    "Admin\\DashboardController@menuCustomization",
    ["auth", "admin"],
);

// System Status Routes
$router->add(
    "GET",
    "/admin/system-status",
    "Admin\\SystemStatusController@index",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/system-status/run-health-check",
    "Admin\\SystemStatusController@runHealthCheck",
    ["auth", "admin"],
);
$router->add("GET", "/admin/performance-dashboard", "Admin\\DashboardController@performanceDashboard", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/widget-management",
    "Admin\DashboardController@widgetManagement",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/system-status",
    "Admin\SystemStatusController@index",
    ["auth", "admin"],
);

// User Management Module Routes
$router->add("GET", "/admin/users", "Admin\UserManagementController@index", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/users/create",
    "Admin\UserManagementController@create",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/users/store",
    "Admin\UserManagementController@store",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/users/{id}/edit",
    "Admin\UserManagementController@edit",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/users/{id}/update",
    "Admin\UserManagementController@update",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/users/{id}/delete",
    "Admin\UserManagementController@delete",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/users/roles",
    "Admin\UserManagementController@roles",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/users/permissions",
    "Admin\UserManagementController@permissions",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/users/bulk",
    "Admin\UserManagementController@bulk",
    ["auth", "admin"],
);

// Analytics Module Routes
$router->add("GET", "/admin/analytics", "Admin\AnalyticsController@overview", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/analytics/overview",
    "Admin\AnalyticsController@overview",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/analytics/users",
    "Admin\AnalyticsController@users",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/analytics/calculators",
    "Admin\AnalyticsController@calculators",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/analytics/performance",
    "Admin\AnalyticsController@performance",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/analytics/reports",
    "Admin\AnalyticsController@reports",
    ["auth", "admin"],
);

// Content Management Module Routes
$router->add("GET", "/admin/content", "Admin\ContentController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/content/pages", "Admin\ContentController@pages", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/content/menus", "Admin\ContentController@menus", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/content/media", "Admin\ContentController@media", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/content/menus/save",
    "Admin\ContentController@saveMenus",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/content/page/create",
    "Admin\ContentController@create",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/content/page/edit/{slug}",
    "Admin\ContentController@edit",
    ["auth", "admin"],
);
$router->add("POST", "/admin/content/save", "Admin\ContentController@save", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/content/publish",
    "Admin\ContentController@publish",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/content/preview/{slug}",
    "Admin\ContentController@preview",
    ["auth", "admin"],
);

// System Settings Module Routes
$router->add("GET", "/admin/settings", "Admin\SettingsController@general", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/settings/email", "Admin\SettingsController@email", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/email/send-test",
    "Admin\SettingsController@sendTestEmail",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/settings/security",
    "Admin\SettingsController@security",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/settings/backup",
    "Admin\SettingsController@backup",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/settings/performance",
    "Admin\SettingsController@performance",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/settings/update",
    "Admin\SettingsController@update",
    ["auth", "admin"],
);

// Theme & Customization Routes
$router->add("GET", "/admin/themes", "Admin\ThemeController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/themes/preview", "Admin\ThemeController@preview", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/themes/customize",
    "Admin\ThemeController@customize",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/activate",
    "Admin\ThemeController@activate",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/themes/{id}/preview",
    "Admin\ThemeController@previewById",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/{id}/save-colors",
    "Admin\ThemeController@saveColors",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/{id}/save-typography",
    "Admin\ThemeController@saveTypography",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/{id}/save-features",
    "Admin\ThemeController@saveFeatures",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/{id}/save-layout",
    "Admin\ThemeController@saveLayout",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/{id}/save-custom_css",
    "Admin\ThemeController@saveCustomCss",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/{id}/reset",
    "Admin\ThemeController@resetCustomizations",
    ["auth", "admin"],
);

// Plugin System Routes
$router->add("GET", "/admin/plugins", "Admin\PluginController@index", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/plugins/install",
    "Admin\PluginController@install",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/plugins/activate",
    "Admin\PluginController@activate",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/plugins/deactivate",
    "Admin\PluginController@deactivate",
    ["auth", "admin"],
);

// Admin API Routes (RESTful admin operations)
$router->add(
    "GET",
    "/api/admin/dashboard/stats",
    "Api\AdminController@getDashboardStats",
    ["auth", "admin"],
);
$router->add("GET", "/api/admin/modules", "Api\AdminController@getModules", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/api/admin/modules/toggle",
    "Api\AdminController@toggleModule",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/api/admin/system/health",
    "Api\AdminController@getSystemHealth",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/api/admin/backup/create",
    "Api\AdminController@createBackup",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/api/admin/activity",
    "Api\AdminController@getUserActivity",
    ["auth", "admin"],
);

// Debug & Testing Routes
$router->add("GET", "/admin/debug", "Admin\DebugController@index", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/debug/error-logs",
    "Admin\DebugController@errorLogs",
    ["auth", "admin"],
);
$router->add("GET", "/admin/debug/tests", "Admin\DebugController@runTests", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/debug/run-tests",
    "Admin\DebugController@runTests",
    ["auth", "admin", "csrf"],
);
$router->add(
    "GET",
    "/admin/debug/live-errors",
    "Admin\DebugController@liveErrors",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/debug/live-errors",
    "Admin\DebugController@liveErrors",
    ["auth", "admin", "csrf"],
);
$router->add(
    "POST",
    "/admin/debug/clear-logs",
    "Admin\DebugController@clearLogs",
    ["auth", "admin", "csrf"],
);

// API Routes (Enhanced)
$router->add("POST", "/api/calculate", "ApiController@calculate");
$router->add("GET", "/api/calculators", "ApiController@getCalculators");
$router->add(
    "GET",
    "/api/calculator/{category}/{tool}",
    "ApiController@getCalculator",
);
$router->add("GET", "/api/calculations", "ApiController@getUserCalculations", [
    "auth",
]);
$router->add("GET", "/api/calculations/{id}", "ApiController@getCalculation", [
    "auth",
]);

// API v1 Routes (versioned, API-key or session auth handled in controller)
$router->add("POST", "/api/v1/calculate", "ApiController@calculate");
$router->add("GET", "/api/v1/calculators", "ApiController@getCalculators");
$router->add(
    "GET",
    "/api/v1/calculator/{category}/{tool}",
    "ApiController@getCalculator",
);
$router->add(
    "GET",
    "/api/v1/calculations",
    "ApiController@getUserCalculations",
);
$router->add(
    "GET",
    "/api/v1/calculations/{id}",
    "ApiController@getCalculation",
);
// API v1 Health
$router->add("GET", "/api/v1/health", "Api\\V1\\HealthController@health");

// Additional Admin Routes (using existing controllers)
$router->add("GET", "/admin/users", "Admin\UserController@index", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/settings/save",
    "Admin\SettingsController@saveSettings",
    ["auth", "admin"],
);

// Calculators Management Routes
$router->add("GET", "/admin/calculators", "Admin\\CalculatorController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/calculators/list", "Admin\\CalculatorController@list", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/calculators/add",
    "Admin\CalculatorController@addCalculator",
    ["auth", "admin"],
);

// Modules Management Routes
$router->add("GET", "/admin/modules", "Admin\ModuleController@index", [
    "auth",
    "admin",
]);

// Widget Management Routes
$router->add("GET", "/admin/widgets", "WidgetController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/widgets/create", "WidgetController@create", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/widgets/create", "WidgetController@create", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/widgets/edit/{id}", "WidgetController@edit", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/widgets/edit/{id}", "WidgetController@edit", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/widgets/delete/{id}", "WidgetController@delete", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/widgets/toggle/{id}", "WidgetController@toggle", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/widgets/toggle-visibility/{id}",
    "WidgetController@toggleVisibility",
    ["auth", "admin"],
);
$router->add("POST", "/admin/widgets/reorder", "WidgetController@reorder", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/widgets/preview/{id}", "WidgetController@preview", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/widgets/settings/{id}",
    "WidgetController@settings",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/widgets/settings/{id}",
    "WidgetController@settings",
    ["auth", "admin"],
);
$router->add("GET", "/admin/widgets/setup", "WidgetController@setup", [
    "auth",
    "admin",
]);

// Widget API Routes
$router->add("GET", "/api/widgets/render", "ApiController@renderWidgets", [
    "auth",
]);
$router->add(
    "POST",
    "/api/widgets/setting/{id}",
    "ApiController@updateWidgetSetting",
    ["auth"],
);

// Plugin Management Routes
$router->add("GET", "/admin/plugins", "Admin\PluginController@index", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/plugins/upload", "Admin\PluginController@upload", [
    "auth",
    "admin",
    "ratelimit",
]);
$router->add("POST", "/admin/plugins/toggle", "Admin\PluginController@toggle", [
    "auth",
    "admin",
    "ratelimit",
]);
$router->add(
    "POST",
    "/admin/plugins/toggle/{slug}/{action}",
    "Admin\PluginController@toggle",
    ["auth", "admin", "ratelimit"],
);
$router->add(
    "POST",
    "/admin/plugins/delete/{slug}",
    "Admin\PluginController@delete",
    ["auth", "admin", "ratelimit"],
);
$router->add(
    "GET",
    "/admin/plugins/details/{slug}",
    "Admin\PluginController@details",
    ["auth", "admin", "ratelimit"],
);
$router->add(
    "POST",
    "/admin/plugins/refresh",
    "Admin\PluginController@refresh",
    ["auth", "admin", "ratelimit"],
);

// Theme Management Routes
$router->add("GET", "/admin/themes", "Admin\ThemeController@index", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/themes/upload", "Admin\ThemeController@upload", [
    "auth",
    "admin",
    "ratelimit",
]);
$router->add("POST", "/admin/themes/deactivate", "Admin\\ThemeController@deactivate", [
    "auth",
    "admin",
    "ratelimit",
]);
$router->add(
    "POST",
    "/admin/themes/activate/{slug}",
    "Admin\ThemeController@activate",
    ["auth", "admin", "ratelimit"],
);
$router->add(
    "POST",
    "/admin/themes/activate",
    "Admin\ThemeController@activate",
    ["auth", "admin", "ratelimit"],
);
$router->add(
    "POST",
    "/admin/themes/delete/{slug}",
    "Admin\ThemeController@delete",
    ["auth", "admin", "ratelimit"],
);
$router->add("POST", "/admin/themes/delete", "Admin\ThemeController@delete", [
    "auth",
    "admin",
    "ratelimit",
]);
$router->add("POST", "/admin/themes/restore", "Admin\ThemeController@restore", [
    "auth",
    "admin",
    "ratelimit",
]);
$router->add(
    "POST",
    "/admin/themes/hardDelete",
    "Admin\ThemeController@hardDelete",
    ["auth", "admin", "ratelimit"],
);
$router->add(
    "GET",
    "/admin/themes/details/{slug}",
    "Admin\ThemeController@details",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/{id}/settings",
    "Admin\ThemeController@updateSettings",
    ["auth", "admin", "ratelimit"],
);

// Premium Theme Management Routes
$router->add(
    "GET",
    "/admin/premium-themes",
    "Admin\PremiumThemeController@index",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/premium-themes/create",
    "Admin\PremiumThemeController@create",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/premium-themes",
    "Admin\PremiumThemeController@store",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/premium-themes/{id}",
    "Admin\PremiumThemeController@show",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/premium-themes/{id}/edit",
    "Admin\PremiumThemeController@edit",
    ["auth", "admin"],
);
$router->add(
    "PUT",
    "/admin/premium-themes/{id}",
    "Admin\PremiumThemeController@update",
    ["auth", "admin"],
);
$router->add(
    "DELETE",
    "/admin/premium-themes/{id}",
    "Admin\PremiumThemeController@destroy",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/premium-themes/{id}/activate",
    "Admin\PremiumThemeController@activate",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/premium-themes/{id}/deactivate",
    "Admin\PremiumThemeController@deactivate",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/premium-themes/validate-license",
    "Admin\PremiumThemeController@validateLicense",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/premium-themes/install",
    "Admin\PremiumThemeController@installTheme",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/premium-themes/{id}/settings",
    "Admin\PremiumThemeController@updateSettings",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/premium-themes/{id}/settings",
    "Admin\PremiumThemeController@settings",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/premium-themes/{id}/analytics",
    "Admin\PremiumThemeController@analytics",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/premium-themes/{id}/customize",
    "Admin\PremiumThemeController@customize",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/premium-themes/{id}/customize",
    "Admin\PremiumThemeController@updateCustomization",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/premium-themes/{id}/preview",
    "Admin\PremiumThemeController@preview",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/premium-themes/upload-zip",
    "Admin\PremiumThemeController@uploadZip",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/premium-themes/marketplace",
    "Admin\PremiumThemeController@marketplace",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/premium-themes/export/{id}",
    "Admin\PremiumThemeController@export",
    ["auth", "admin"],
);

// API Routes for Premium Themes
$router->add(
    "GET",
    "/api/premium-themes/active",
    "ApiController@getActivePremiumTheme",
);
$router->add(
    "GET",
    "/api/premium-themes/settings",
    "ApiController@getPremiumThemeSettings",
);
$router->add(
    "POST",
    "/api/premium-themes/settings",
    "ApiController@updatePremiumThemeSettings",
);
$router->add(
    "GET",
    "/api/premium-themes/custom-css",
    "ApiController@getCustomCSS",
);
$router->add(
    "POST",
    "/api/premium-themes/custom-css",
    "ApiController@updateCustomCSS",
);
$router->add(
    "POST",
    "/api/premium-themes/toggle-dark-mode",
    "ApiController@toggleDarkMode",
);
$router->add(
    "POST",
    "/api/premium-themes/change-skin",
    "ApiController@changeCalculatorSkin",
);
$router->add(
    "GET",
    "/api/premium-themes/preview/{id}",
    "ApiController@previewPremiumTheme",
);

// History Management Routes
$router->add("GET", "/history", "HistoryController@index", ["auth"]);
$router->add("GET", "/history/search", "HistoryController@search", ["auth"]);
$router->add("POST", "/history/save", "HistoryController@saveCalculation", [
    "auth",
]);
$router->add(
    "POST",
    "/history/favorite/{id}",
    "HistoryController@toggleFavorite",
    ["auth"],
);
$router->add("GET", "/history/delete/{id}", "HistoryController@delete", [
    "auth",
]);
$router->add("GET", "/history/view/{id}", "HistoryController@view", ["auth"]);
$router->add("GET", "/history/stats", "HistoryController@stats", ["auth"]);
$router->add("GET", "/history/recent", "HistoryController@recent", ["auth"]);
$router->add(
    "GET",
    "/history/by-type/{calculatorType}",
    "HistoryController@byType",
    ["auth"],
);
$router->add("GET", "/history/export", "HistoryController@export", ["auth"]);
$router->add("POST", "/history/bulk-delete", "HistoryController@bulkDelete", [
    "auth",
]);
$router->add(
    "POST",
    "/history/bulk-favorite",
    "HistoryController@bulkFavorite",
    ["auth"],
);

// Export Management Routes
$router->add("GET", "/user/exports/templates", "ExportController@templates", [
    "auth",
]);
$router->add(
    "POST",
    "/user/exports/create-template",
    "ExportController@createTemplate",
    ["auth"],
);
$router->add(
    "POST",
    "/user/exports/update-template/{id}",
    "ExportController@updateTemplate",
    ["auth"],
);
$router->add(
    "POST",
    "/user/exports/delete-template/{id}",
    "ExportController@deleteTemplate",
    ["auth"],
);
$router->add(
    "POST",
    "/user/exports/duplicate-template/{id}",
    "ExportController@duplicateTemplate",
    ["auth"],
);
$router->add(
    "GET",
    "/user/exports/template-config/{id}",
    "ExportController@getTemplateConfig",
    ["auth"],
);
$router->add("POST", "/user/exports/export", "ExportController@export", [
    "auth",
]);
$router->add(
    "GET",
    "/user/exports/download/{filename}",
    "ExportController@download",
    ["auth"],
);
$router->add(
    "GET",
    "/user/exports/search",
    "ExportController@searchTemplates",
    ["auth"],
);
$router->add("GET", "/user/exports/stats", "ExportController@getStats", [
    "auth",
]);
$router->add("POST", "/user/exports/cleanup", "ExportController@cleanup", [
    "auth",
]);

// Share & Comment System Routes
$router->add("GET", "/share/create", "ShareController@create", ["auth"]);
$router->add("POST", "/share/store", "ShareController@store", ["auth"]);
$router->add("GET", "/share/my-shares", "ShareController@myShares", ["auth"]);
$router->add("GET", "/share/public/{token}", "ShareController@publicView");
$router->add("POST", "/share/{id}/embed", "ShareController@generateEmbed", [
    "auth",
]);
$router->add("DELETE", "/share/{id}", "ShareController@destroy", ["auth"]);

// Comment System Routes
$router->add("POST", "/comments", "CommentController@store", ["auth"]);
$router->add("POST", "/comments/{id}/reply", "CommentController@reply", [
    "auth",
]);
$router->add("POST", "/comments/{id}/vote", "CommentController@vote", ["auth"]);
$router->add("PUT", "/comments/{id}", "CommentController@update", ["auth"]);
$router->add("DELETE", "/comments/{id}", "CommentController@destroy", ["auth"]);
$router->add(
    "GET",
    "/comments/share/{shareId}",
    "CommentController@getByShare",
);

// Email & Notifications Management Routes
$router->add("GET", "/admin/email", "Admin\EmailManagerController@index", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/email/send-test",
    "Admin\EmailManagerController@sendTestEmail",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email/save-template",
    "Admin\EmailManagerController@saveTemplate",
    ["auth", "admin"],
);

// Billing & Subscriptions Management Routes
$router->add(
    "GET",
    "/admin/subscriptions",
    "Admin\SubscriptionController@index",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/subscriptions/create-plan",
    "Admin\SubscriptionController@createPlan",
    ["auth", "admin"],
);

// Help & Logs Management Routes
$router->add("GET", "/admin/help", "Admin\HelpController@index", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/help/clear-logs",
    "Admin\HelpController@clearLogs",
    ["auth", "admin", "ratelimit"],
);
$router->add(
    "POST",
    "/admin/help/backup",
    "Admin\HelpController@backupSystem",
    ["auth", "admin", "ratelimit"],
);
$router->add(
    "POST",
    "/admin/help/export-themes",
    "Admin\HelpController@exportThemes",
    ["auth", "admin", "ratelimit"],
);
$router->add(
    "POST",
    "/admin/help/export-plugins",
    "Admin\HelpController@exportPlugins",
    ["auth", "admin", "ratelimit"],
);
$router->add("POST", "/admin/help/restore", "Admin\HelpController@restore", [
    "auth",
    "admin",
    "ratelimit",
]);
$router->add(
    "GET",
    "/admin/help/download-backup",
    "Admin\HelpController@downloadBackup",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/help/export-logs",
    "Admin\HelpController@exportLogs",
    ["auth", "admin", "ratelimit"],
);

// Audit Logs Viewer
$router->add("GET", "/admin/audit-logs", "Admin\AuditLogController@index", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/audit-logs/download",
    "Admin\AuditLogController@download",
    ["auth", "admin"]
);

// Additional Admin Routes for Missing Views
$router->add("GET", "/admin/activity", "Admin\ActivityController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/audit", "Admin\AuditController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/backup", "Admin\BackupController@index", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/backup/create", "Admin\BackupController@create", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/backup/delete/{backupName}", "Admin\BackupController@delete", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/backup/restore/{backupName}", "Admin\BackupController@restore", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/backup/schedule", "Admin\BackupController@schedule", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/backup/settings", "Admin\BackupController@settings", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/backup/download/{backupName}", "Admin\BackupController@download", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/calculations", "Admin\CalculationsController@index", [
    "auth",
    "admin",
]);

// Theme Customization Routes
$router->add(
    "GET",
    "/admin/themes/:id/customize",
    "Admin\ThemeCustomizeController@index",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/:id/save-colors",
    "Admin\ThemeCustomizeController@saveColors",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/:id/save-typography",
    "Admin\ThemeCustomizeController@saveTypography",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/:id/save-features",
    "Admin\ThemeCustomizeController@saveFeatures",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/:id/save-layout",
    "Admin\ThemeCustomizeController@saveLayout",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/:id/save-custom_css",
    "Admin\\ThemeCustomizeController@saveCustomCSS",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/themes/:id/preview",
    "Admin\\ThemeCustomizeController@preview",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/themes/:id/reset",
    "Admin\\ThemeCustomizeController@reset",
    ["auth", "admin"],
);

// Email Manager Admin Routes
$router->add(
    "GET",
    "/admin/email-manager",
    "Admin\\EmailManagerController@dashboard",
    ["auth", "admin"],
);
$router->add("GET", "/admin/email", "Admin\\EmailManagerController@index", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/email-manager/stats",
    "Admin\\EmailManagerController@stats",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/email-manager/threads",
    "Admin\\EmailManagerController@threads",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/email-manager/thread/{id}",
    "Admin\\EmailManagerController@viewThread",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/thread/{id}/reply",
    "Admin\\EmailManagerController@reply",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/thread/{id}/status",
    "Admin\\EmailManagerController@updateStatus",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/thread/{id}/assign",
    "Admin\\EmailManagerController@assign",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/thread/{id}/priority",
    "Admin\\EmailManagerController@updatePriority",
    ["auth", "admin"],
);

$router->add(
    "GET",
    "/admin/email-manager/settings",
    "Admin\\EmailManagerController@settings",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/settings",
    "Admin\\EmailManagerController@updateSettings",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/test-email",
    "Admin\\EmailManagerController@testEmail",
    ["auth", "admin"],
);

// Email Templates Admin Routes
$router->add(
    "GET",
    "/admin/email-manager/templates",
    "Admin\\EmailManagerController@templates",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/templates",
    "Admin\\EmailManagerController@createTemplate",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/email-manager/template/{id}",
    "Admin\\EmailManagerController@editTemplate",
    ["auth", "admin"],
);
$router->add(
    "PUT",
    "/admin/email-manager/template/{id}",
    "Admin\\EmailManagerController@updateTemplate",
    ["auth", "admin"],
);
$router->add(
    "DELETE",
    "/admin/email-manager/template/{id}",
    "Admin\EmailManagerController@deleteTemplate",
    ["auth", "admin"],
);
// Email Manager Admin Routes
$router->add(
    "GET",
    "/admin/email-manager",
    "Admin\\EmailManagerController@dashboard",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/email-manager/stats",
    "Admin\\EmailManagerController@stats",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/email-manager/threads",
    "Admin\\EmailManagerController@threads",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/email-manager/thread/{id}",
    "Admin\\EmailManagerController@viewThread",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/thread/{id}/reply",
    "Admin\\EmailManagerController@reply",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/thread/{id}/status",
    "Admin\\EmailManagerController@updateStatus",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/thread/{id}/assign",
    "Admin\\EmailManagerController@assign",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/thread/{id}/priority",
    "Admin\EmailManagerController@updatePriority",
    ["auth", "admin"]
);

// Additional Email Manager Routes for Missing Views
$router->add("GET", "/admin/email-manager/error", "Admin\EmailManagerController@error", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/email-manager/thread-detail/{id}", "Admin\EmailManagerController@threadDetail", [
    "auth",
    "admin",
]);

// Email Templates Admin Routes
$router->add(
    "GET",
    "/admin/email-manager/templates",
    "Admin\\EmailManagerController@templates",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/templates",
    "Admin\\EmailManagerController@createTemplate",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/email-manager/template/{id}",
    "Admin\\EmailManagerController@editTemplate",
    ["auth", "admin"],
);
$router->add(
    "PUT",
    "/admin/email-manager/template/{id}",
    "Admin\\EmailManagerController@updateTemplate",
    ["auth", "admin"],
);
$router->add(
    "DELETE",
    "/admin/email-manager/template/{id}",
    "Admin\\EmailManagerController@deleteTemplate",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/templates/{id}/use",
    "Admin\\EmailManagerController@useTemplate",
    ["auth", "admin"],
);

// Error Monitoring & Logging Routes
$router->add("GET", "/admin/error-logs", "Admin\ErrorLogController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/logs", "Admin\LogsController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/logs/{id}", "Admin\LogsController@view", [
    "auth",
    "admin",
]);

// System Status Route
$router->add("GET", "/admin/system/status", "Admin\DashboardController@systemStatus", [
    "auth",
    "admin",
]);
$router->add(
    "GET",
    "/admin/error-logs/get-error-stats",
    "Admin\ErrorLogController@getErrorStats",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/error-logs/get-method-calls",
    "Admin\ErrorLogController@getMethodCalls",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/error-logs/get-failed-calls",
    "Admin\ErrorLogController@getFailedCalls",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/error-logs/clear-logs",
    "Admin\ErrorLogController@clearLogs",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/error-logs/export-logs",
    "Admin\ErrorLogController@exportLogs",
    ["auth", "admin"],
);

// Help & Documentation Routes
$router->add("GET", "/help", "HelpController@index");
$router->add("GET", "/admin/help", "Admin\\HelpController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/help/{category}", "HelpController@category");
$router->add("GET", "/help/{category}/{article}", "HelpController@article");
$router->add("GET", "/developers", "DeveloperController@index");
$router->add("GET", "/developers/api", "DeveloperController@api");
$router->add("GET", "/developers/documentation", "DeveloperController@documentation");
$router->add("GET", "/developers/guides", "DeveloperController@guides");

// Landing Page Routes
$router->add("GET", "/civil", "LandingController@civil");
$router->add("GET", "/electrical", "LandingController@electrical");
$router->add("GET", "/plumbing", "LandingController@plumbing");
$router->add("GET", "/hvac", "LandingController@hvac");
$router->add("GET", "/fire", "LandingController@fire");
$router->add("GET", "/site", "LandingController@site");
$router->add("GET", "/structural", "LandingController@structural");
$router->add("GET", "/estimation", "LandingController@estimation");
$router->add("GET", "/management", "LandingController@management");
$router->add("GET", "/mep", "LandingController@mep");

// Load module service providers
\App\Modules\ModuleManager::load($router);

// Boot active plugins
try {
    $pluginManager = new \App\Services\PluginManager();
    $pluginManager->bootAll();
} catch (\Throwable $e) {
    \App\Services\Logger::exception($e, ["when" => "boot_plugins_from_routes"]);
}

// Page routes
$router->add('GET', '/page/{slug}', 'PageController@show');
