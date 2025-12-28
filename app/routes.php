<?php
// Routes Definition - Cleaned Version with All Conflicts Resolved
// This file defines all application routes with comprehensive features
// All routing conflicts have been resolved

// Get router from global scope
$router = $GLOBALS["router"];

// Public Routes
$router->add("GET", "/", "HomeController@index");
$router->add("GET", "/features", "HomeController@features");
$router->add("GET", "/pricing", "HomeController@pricing");
$router->add("GET", "/about", "HomeController@about");
$router->add("GET", "/contact", "ContactController@index");
$router->add("POST", "/contact/submit", "ContactController@submit");
$router->add("GET", "/report", "ReportController@index");
$router->add("POST", "/report/submit", "ReportController@submit");
$router->add("GET", "/pages/preview/{id}", "HomeController@pagePreview");

// Blog Public Routes
$router->add("GET", "/blog", "BlogController@index");
$router->add("GET", "/blog/{slug}", "BlogController@show");

// Authentication Routes
$router->add("GET", "/login", "AuthController@showLogin", ["guest"]);
$router->add("POST", "/login", "AuthController@login", ["guest"]);
$router->add("GET", "/login/2fa", "AuthController@show2FA", ["guest"]);
$router->add("POST", "/login/2fa", "AuthController@verify2FA", ["guest"]);
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

// Google Auth Routes
$router->add("GET", "/user/login/google", "AuthController@loginWithGoogle");
$router->add("GET", "/user/login/google/callback", "AuthController@handleGoogleCallback");

// Basic Calculator Routes moved below for priority

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

// Nepali Unit Calculator Routes
$router->add(
    "GET",
    "/nepali",
    "CalculatorController@traditionalUnits",
);
$router->add(
    "GET",
    "/nepali/protected",
    "CalculatorController@traditionalUnits",
    ["auth"],
);
$router->add(
    "POST",
    "/api/nepali/convert",
    "ApiController@traditionalUnitsConvert",
);
$router->add(
    "POST",
    "/api/nepali/convert/protected",
    "ApiController@traditionalUnitsConvert",
    ["auth"],
);
$router->add(
    "POST",
    "/api/nepali/all-conversions",
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
// 2FA Routes
$router->add("POST", "/profile/2fa/enable", "ProfileController@enableTwoFactor", ["auth"]);
$router->add("POST", "/profile/2fa/confirm", "ProfileController@confirmTwoFactor", ["auth"]);
$router->add("POST", "/profile/2fa/disable", "ProfileController@disableTwoFactor", ["auth"]);

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
$router->add("GET", "/admin/dashboard_complex", "Admin\\DashboardController@dashboardComplex", [
    "auth",
    "admin",
]);

// MODULE MANAGEMENT - CONSOLIDATED (Fixed duplicate conflict)
$router->add("GET", "/admin/modules", "Admin\ModuleController@index", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/modules/activate",
    "Admin\ModuleController@activate",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/modules/deactivate",
    "Admin\ModuleController@deactivate",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/modules/{module}/settings",
    "Admin\ModuleController@settings",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/modules/settings/update",
    "Admin\ModuleController@updateSettings",
    ["auth", "admin"],
);

// Advertisement Management
$router->add("GET", "/admin/advertisements", "Admin\AdvertisementController@index", ["auth", "admin"]);
$router->add("GET", "/admin/advertisements/create", "Admin\AdvertisementController@create", ["auth", "admin"]);
$router->add("POST", "/admin/advertisements/store", "Admin\AdvertisementController@store", ["auth", "admin"]);
$router->add("GET", "/admin/advertisements/edit/{id}", "Admin\AdvertisementController@edit", ["auth", "admin"]);
$router->add("POST", "/admin/advertisements/update/{id}", "Admin\AdvertisementController@update", ["auth", "admin"]);
$router->add("POST", "/admin/advertisements/delete/{id}", "Admin\AdvertisementController@delete", ["auth", "admin"]);
$router->add("POST", "/admin/advertisements/toggle/{id}", "Admin\AdvertisementController@toggle", ["auth", "admin"]);

// Blog Management
$router->add("GET", "/admin/blog", "Admin\BlogController@index", ["auth", "admin"]);
$router->add("GET", "/admin/blog/create", "Admin\BlogController@create", ["auth", "admin"]);
$router->add("POST", "/admin/blog/store", "Admin\BlogController@store", ["auth", "admin"]);
$router->add("GET", "/admin/blog/edit/{id}", "Admin\BlogController@edit", ["auth", "admin"]);
$router->add("POST", "/admin/blog/update/{id}", "Admin\BlogController@update", ["auth", "admin"]);
$router->add("POST", "/admin/blog/delete/{id}", "Admin\BlogController@delete", ["auth", "admin"]);

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

// ADMIN SETTINGS - CONSOLIDATED (Fixed duplicate conflict)
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
    "/admin/settings/google",
    "Admin\SettingsController@google",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/settings/recaptcha",
    "Admin\SettingsController@recaptcha",
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
    "/admin/settings/advanced/save",
    "Admin\SettingsController@saveAdvanced",
    ["auth", "admin"],
);
// Permalink Settings Route
$router->add(
    "GET",
    "/admin/settings/permalinks",
    "Admin\\SettingsController@permalinks",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/settings/permalinks",
    "Admin\\SettingsController@permalinks",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/settings/backup",
    "Admin\BackupController@index",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/settings/backup",
    "Admin\BackupController@save",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/settings/payments",
    "Admin\SettingsController@payments",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/settings/payments/update",
    "Admin\SettingsController@savePayments",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/settings/update",
    "Admin\SettingsController@update",
    ["auth", "admin"],
);

// Payment Routes (Stripe)
$router->add("GET", "/payment/checkout/stripe", "Payment\StripeController@checkout", ["auth"]);
$router->add("GET", "/payment/stripe/success", "Payment\StripeController@success", ["auth"]);
$router->add("GET", "/payment/stripe/cancel", "Payment\StripeController@cancel", ["auth"]);
$router->add("POST", "/webhooks/stripe", "Payment\StripeController@webhook"); // Public webhook

// Project Routes
$router->add("GET", "/projects", "ProjectController@index", ["auth"] );
$router->add("POST", "/projects/store", "ProjectController@store", ["auth"]);
$router->add("GET", "/projects/view/{id}", "ProjectController@show", ["auth"]);
$router->add("POST", "/projects/delete/{id}", "ProjectController@delete", ["auth"]);

// Enterprise Estimation Routes (Excel-Style Sheets)
$router->add("GET", "/estimation/sheet", "EstimationController@sheet");
$router->add("GET", "/estimation/sheet/{id}", "EstimationController@sheet");
$router->add("GET", "/estimation/api/items", "EstimationController@getItems");
$router->add("POST", "/estimation/api/save", "EstimationController@saveGrid");
// Rate Manager Routes
$router->add("GET", "/estimation/rates", "EstimationController@rates_manager");
$router->add("GET", "/estimation/api/get_location_rates", "EstimationController@get_location_rates");
$router->add("POST", "/estimation/api/save_bulk_rates", "EstimationController@save_bulk_rates");
$router->add("POST", "/estimation/api/update_location", "EstimationController@update_location");
$router->add("GET", "/estimation/api/get_project_rates", "EstimationController@get_project_rates");
$router->add("GET", "/estimation/export/excel", "EstimationController@export_excel");
$router->add("GET", "/estimation/export/pdf", "EstimationController@export_pdf");
$router->add("POST", "/estimation/import/excel", "EstimationController@import_excel");
$router->add("POST", "/estimation/api/save_template", "EstimationController@save_template");
$router->add("GET", "/estimation/api/get_templates", "EstimationController@get_templates");
$router->add("GET", "/estimation/api/load_template", "EstimationController@load_template");
$router->add("GET", "/estimation/api/get_versions", "EstimationController@get_versions");
$router->add("POST", "/estimation/api/restore_version", "EstimationController@restore_version");

// Rate Analysis Routes
$router->add("GET", "/rate-analysis/item", "RateAnalysisController@item_rate_analysis");
$router->add("POST", "/rate-analysis/calculate", "RateAnalysisController@calculate_rate");
$router->add("POST", "/rate-analysis/save-to-location", "RateAnalysisController@save_rate_to_location");
$router->add("GET", "/rate-analysis/labor", "RateAnalysisController@labor_rate_analysis");
$router->add("GET", "/rate-analysis/equipment", "RateAnalysisController@equipment_hourly_rate");
$router->add("GET", "/rate-analysis/cash-flow", "RateAnalysisController@cash_flow_analysis");
$router->add("GET", "/rate-analysis/npv-irr", "RateAnalysisController@npv_irr_analysis");

// Calculator Platform Routes
$router->add("GET", "/calculator", "CalculatorController@index");
$router->add("GET", "/calculator/converter/{slug}", "CalculatorController@converter");
$router->add("POST", "/calculator/api/convert", "CalculatorController@convert");
$router->add("POST", "/calculator/api/calculate", "CalculatorController@calculate");
$router->add("GET", "/calculator/scientific", "CalculatorController@scientific");

// Legacy/Dynamic Tool Routes (Moved here to avoid intercepting /calculator/converter/*)
$router->add("GET", "/calculators", "CalculatorController@index");
$router->add("GET", "/calculator/{category}", "CalculatorController@category");
$router->add("GET", "/calculator/{category}/{tool}", "CalculatorController@tool");
$router->add("POST", "/calculator/{category}/{tool}/calculate", "CalculatorController@calculate");

// Mathematics Calculators
$router->add("GET", "/calculator/math/percentage", "MathCalculatorController@percentage");
$router->add("GET", "/calculator/math/fraction", "MathCalculatorController@fraction");
$router->add("GET", "/calculator/math/ratio", "MathCalculatorController@ratio");
$router->add("GET", "/calculator/math/square-root", "MathCalculatorController@square_root");
$router->add("GET", "/calculator/math/exponent", "MathCalculatorController@exponent");
$router->add("POST", "/calculator/api/percentage", "MathCalculatorController@api_percentage");
$router->add("POST", "/calculator/api/fraction", "MathCalculatorController@api_fraction");
$router->add("GET", "/calculator/math/bmi", "MathCalculatorController@bmi");
$router->add("GET", "/calculator/math/loan", "MathCalculatorController@loan");
$router->add("GET", "/calculator/math/age", "MathCalculatorController@age");
$router->add("GET", "/calculator/math/area", "MathCalculatorController@area");
$router->add("GET", "/calculator/math/volume", "MathCalculatorController@volume");
$router->add("GET", "/calculator/math/surface-area", "MathCalculatorController@surface_area");
$router->add("GET", "/calculator/math/surface-area", "MathCalculatorController@surface_area");
$router->add("GET", "/calculator/math/statistics", "MathCalculatorController@statistics");
$router->add("GET", "/calculator/math/age", "MathCalculatorController@age"); // Legacy Age calc kept here
$router->add("POST", "/calculator/api/bmi", "MathCalculatorController@api_bmi");
$router->add("POST", "/calculator/api/loan", "MathCalculatorController@api_loan");
$router->add("POST", "/calculator/api/statistics", "MathCalculatorController@api_statistics");
$router->add("GET", "/calculator/math/gcd-lcm", "MathCalculatorController@gcd_lcm");
$router->add("GET", "/calculator/math/quadratic", "MathCalculatorController@quadratic");
$router->add("GET", "/calculator/math/linear-equations", "MathCalculatorController@linear_equations");
$router->add("GET", "/calculator/math/trigonometry", "MathCalculatorController@trigonometry");
$router->add("GET", "/calculator/math/right-triangle", "MathCalculatorController@right_triangle");
$router->add("GET", "/calculator/math/pythagorean", "MathCalculatorController@pythagorean");

// Health Calculators
$router->add("GET", "/calculator/health/bmi", "HealthCalculatorController@bmi");
$router->add("GET", "/calculator/health/bmr", "HealthCalculatorController@bmr");
$router->add("GET", "/calculator/health/body-fat", "HealthCalculatorController@body_fat");
$router->add("GET", "/calculator/health/calories", "HealthCalculatorController@calories");

// Physics Calculators
$router->add("GET", "/calculator/physics/velocity", "PhysicsCalculatorController@velocity");
$router->add("GET", "/calculator/physics/force", "PhysicsCalculatorController@force");
$router->add("GET", "/calculator/physics/ohms-law", "PhysicsCalculatorController@ohms_law");
$router->add("GET", "/calculator/physics/energy", "PhysicsCalculatorController@energy");

// Chemistry Calculators
$router->add("GET", "/calculator/chemistry/molar-mass", "ChemistryCalculatorController@molar_mass");
$router->add("GET", "/calculator/chemistry/ph", "ChemistryCalculatorController@ph");
$router->add("GET", "/calculator/chemistry/gas-laws", "ChemistryCalculatorController@gas_laws");

// Statistics Calculators
$router->add("GET", "/calculator/statistics/basic", "StatisticsCalculatorController@basic");
$router->add("GET", "/calculator/statistics/dispersion", "StatisticsCalculatorController@dispersion");
$router->add("GET", "/calculator/statistics/probability", "StatisticsCalculatorController@probability");

// Finance Calculators
$router->add("GET", "/calculator/finance/loan", "FinanceCalculatorController@loan");
$router->add("GET", "/calculator/finance/investment", "FinanceCalculatorController@investment");
$router->add("GET", "/calculator/finance/salary", "FinanceCalculatorController@salary");
$router->add("GET", "/calculator/math/discount", "MathCalculatorController@discount");
$router->add("POST", "/calculator/api/gcd-lcm", "MathCalculatorController@api_gcd_lcm");
$router->add("POST", "/calculator/api/quadratic", "MathCalculatorController@api_quadratic");
$router->add("POST", "/calculator/api/pythagorean", "MathCalculatorController@api_pythagorean");
$router->add("POST", "/calculator/api/pythagorean", "MathCalculatorController@api_pythagorean");
$router->add("POST", "/calculator/api/discount", "MathCalculatorController@api_discount");

// Date & Time Calculators
$router->add("GET", "/calculator/datetime/duration", "DateTimeCalculatorController@duration");
$router->add("GET", "/calculator/datetime/adder", "DateTimeCalculatorController@adder");
$router->add("GET", "/calculator/datetime/workdays", "DateTimeCalculatorController@workdays");
$router->add("GET", "/calculator/datetime/time", "DateTimeCalculatorController@time");
$router->add("GET", "/calculator/datetime/nepali", "DateTimeCalculatorController@nepali");

$router->add("POST", "/calculator/api/datetime/duration", "DateTimeCalculatorController@api_duration");
$router->add("POST", "/calculator/api/datetime/adder", "DateTimeCalculatorController@api_adder");
$router->add("POST", "/calculator/api/datetime/nepali", "DateTimeCalculatorController@api_nepali");


// Finance Calculators
$router->add("GET", "/calculator/finance/mortgage", "FinanceCalculatorController@mortgage");
$router->add("GET", "/calculator/finance/investment", "FinanceCalculatorController@investment");
$router->add("GET", "/calculator/finance/compound-interest", "FinanceCalculatorController@compound_interest");
$router->add("GET", "/calculator/finance/savings", "FinanceCalculatorController@savings");
$router->add("GET", "/calculator/finance/roi", "FinanceCalculatorController@roi");
$router->add("POST", "/calculator/api/mortgage", "FinanceCalculatorController@api_mortgage");
$router->add("POST", "/calculator/api/compound-interest", "FinanceCalculatorController@api_compound_interest");
$router->add("POST", "/calculator/api/roi", "FinanceCalculatorController@api_roi");

// Health Calculators
$router->add("GET", "/calculator/health/bmr", "HealthCalculatorController@bmr");
$router->add("GET", "/calculator/health/calorie", "HealthCalculatorController@calorie");
$router->add("GET", "/calculator/health/body-fat", "HealthCalculatorController@body_fat");
$router->add("GET", "/calculator/health/water-intake", "HealthCalculatorController@water_intake");
$router->add("GET", "/calculator/health/pregnancy", "HealthCalculatorController@pregnancy");
$router->add("POST", "/calculator/api/bmr", "HealthCalculatorController@api_bmr");
$router->add("POST", "/calculator/api/body-fat", "HealthCalculatorController@api_body_fat");

// Physics Calculators
$router->add("GET", "/calculator/physics/velocity", "PhysicsCalculatorController@velocity");
$router->add("GET", "/calculator/physics/force", "PhysicsCalculatorController@force");
$router->add("GET", "/calculator/physics/kinetic-energy", "PhysicsCalculatorController@kinetic_energy");
$router->add("GET", "/calculator/physics/power", "PhysicsCalculatorController@power");
$router->add("GET", "/calculator/physics/ohms-law", "PhysicsCalculatorController@ohms_law");
$router->add("POST", "/calculator/api/velocity", "PhysicsCalculatorController@api_velocity");
$router->add("POST", "/calculator/api/force", "PhysicsCalculatorController@api_force");
$router->add("POST", "/calculator/api/kinetic-energy", "PhysicsCalculatorController@api_kinetic_energy");
$router->add("POST", "/calculator/api/power", "PhysicsCalculatorController@api_power");
$router->add("POST", "/calculator/api/ohms-law", "PhysicsCalculatorController@api_ohms_law");

$router->add(
    "POST",
    "/admin/email/send-test",
    "Admin\SettingsController@sendTestEmail",
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

// User Management Module Routes
$router->add("GET", "/api/favorites", "FavoritesController@index", ["auth"]);
$router->add("POST", "/api/favorites/toggle", "FavoritesController@toggle", ["auth"]);

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

$router->add(
    "POST",
    "/admin/users/bulk-delete",
    "Admin\UserManagementController@bulkDelete",
    ["auth", "admin"],
);

// Analytics Module Routes
$router->add("GET", "/admin/analytics", "Admin\\AnalyticsController@overview", [
    "auth",
    "admin",
]);
// ... existing analytics routes ...

// CALCULATORS MANAGEMENT ROUTES
$router->add("GET", "/admin/calculators", "Admin\\CalculatorManagementController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/calculators/create", "Admin\\CalculatorManagementController@create", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/calculators/store", "Admin\\CalculatorManagementController@store", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/calculators/edit/{id}", "Admin\\CalculatorManagementController@edit", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/calculators/update/{id}", "Admin\\CalculatorManagementController@update", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/calculators/delete/{id}", "Admin\\CalculatorManagementController@delete", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/calculators/toggle", "Admin\\CalculatorManagementController@toggle", [
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

//
// Content Management Module Routes
$router->add("GET", "/admin/content", "Admin\ContentController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/content/pages", "Admin\ContentController@pages", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/content/pages/create", "Admin\ContentController@create", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/content/pages/edit/{id}", "Admin\ContentController@edit", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/content/pages/save", "Admin\ContentController@save", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/content/pages/delete/{id}", "Admin\ContentController@delete", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/content/menus", "Admin\ContentController@menus", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/content/media", "Admin\\ContentController@media", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/content/media/upload", "Admin\\ContentController@uploadMedia", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/content/media/delete/{id}", "Admin\\ContentController@deleteMedia", [
    "auth",
    "admin",
]);
$router->add("POST", "/admin/content/media/update/{id}", "Admin\\ContentController@updateMedia", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/content/menus/save",
    "Admin\\ContentController@saveMenus",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/content/menus/create",
    "Admin\\ContentController@createMenu",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/content/menus/edit/{id}",
    "Admin\\ContentController@editMenu",
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

// THEME MANAGEMENT - CONSOLIDATED (Fixed duplicate conflict)
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

// Additional Theme Management Routes
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

// Additional Plugin Management Routes
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

// Backup Management Routes
$router->add(
    "GET",
    "/admin/backup",
    "Admin\BackupController@index",
    ["auth", "admin"],
);

$router->add(
    "POST",
    "/admin/backup/create",
    "Admin\BackupController@create",
    ["auth", "admin"],
);
$router->add(
    "GET",
    "/admin/backup/download/{backupName}",
    "Admin\BackupController@download",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/backup/delete/{backupName}",
    "Admin\BackupController@delete",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/backup/restore/{backupName}",
    "Admin\BackupController@restore",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/backup/schedule",
    "Admin\BackupController@schedule",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/backup/settings",
    "Admin\BackupController@settings",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/backup/cleanup",
    "Admin\BackupController@cleanup",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/backup/test",
    "Admin\BackupController@test",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/settings/backup/save",
    "Admin\BackupController@save",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/settings/advanced/generate-api-key",
    "Admin\BackupController@generateApiKey",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/backup/restore-from-id/{backupId}",
    "Admin\BackupController@restoreFromId",
    ["auth", "admin"],
);
$router->add(
    "DELETE",
    "/admin/backup/delete/{backupId}",
    "Admin\BackupController@delete",
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
$router->add(
    "GET",
    "/admin/debug/download-logs",
    "Admin\DebugController@downloadLogs",
    ["auth", "admin"]
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

// Calculations Management Routes (Found Missing)
$router->add("GET", "/admin/calculations", "Admin\CalculationsController@index", [
    "auth",
    "admin",
]);

// Audit Logs Routes (Found Missing)
$router->add("GET", "/admin/audit-logs", "Admin\AuditLogController@index", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/audit-logs/download", "Admin\AuditLogController@download", [
    "auth",
    "admin",
]);



// Marketplace Management Routes
$router->add(
    "GET",
    "/admin/marketplace",
    "Admin\MarketplaceController@index",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/marketplace/validate-license",
    "Admin\MarketplaceController@validateLicense",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/marketplace/install",
    "Admin\MarketplaceController@install",
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

// EMAIL MANAGEMENT - CONSOLIDATED (Fixed duplicate conflict)
$router->add("GET", "/admin/email", "Admin\\EmailManagerController@index", [
    "auth",
    "admin",
]);
$router->add(
    "POST",
    "/admin/email/send-test",
    "Admin\\EmailManagerController@sendTestEmail",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email/save-template",
    "Admin\\EmailManagerController@saveTemplate",
    ["auth", "admin"],
);

// Email Manager Admin Routes
$router->add(
    "GET",
    "/admin/email-manager",
    "Admin\\EmailManagerController@dashboard",
    ["auth", "admin"]
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
// Template Create Routes
$router->add(
    "GET",
    "/admin/email-manager/template/create",
    "Admin\\EmailManagerController@createTemplate",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/template/create",
    "Admin\\EmailManagerController@createTemplate",
    ["auth", "admin"],
);
// Template Edit Routes
$router->add(
    "GET",
    "/admin/email-manager/template/{id}/edit",
    "Admin\\EmailManagerController@editTemplate",
    ["auth", "admin"],
);
$router->add(
    "POST",
    "/admin/email-manager/template/{id}/update",
    "Admin\\EmailManagerController@updateTemplate",
    ["auth", "admin"],
);
// Template Delete Route
$router->add(
    "POST",
    "/admin/email-manager/template/{id}/delete",
    "Admin\\EmailManagerController@deleteTemplate",
    ["auth", "admin"],
);

// Additional Email Manager Routes for Missing Views
$router->add("GET", "/admin/email-manager/error", "Admin\\EmailManagerController@error", [
    "auth",
    "admin",
]);
$router->add("GET", "/admin/email-manager/thread-detail/{id}", "Admin\\EmailManagerController@threadDetail", [
    "auth",
    "admin",
]);

// Email Template Use Route
$router->add(
    "POST",
    "/admin/email-manager/templates/{id}/use",
    "Admin\\EmailManagerController@useTemplate",
    ["auth", "admin"],
);

// Notification System Routes
$router->add("GET", "/admin/notifications", "Admin\NotificationController@index", [
    "auth",
    "admin"
]);
$router->add("GET", "/admin/notifications/api", "Admin\NotificationController@getNotifications", [
    "auth",
    "admin"
]);
$router->add("POST", "/admin/notifications/mark-read/{id}", "Admin\NotificationController@markAsRead", [
    "auth",
    "admin"
]);
$router->add("POST", "/admin/notifications/mark-all-read", "Admin\NotificationController@markAllAsRead", [
    "auth",
    "admin"
]);
$router->add("DELETE", "/admin/notifications/delete/{id}", "Admin\NotificationController@delete", [
    "auth",
    "admin"
]);
$router->add("POST", "/admin/notifications/create", "Admin\NotificationController@create", [
    "auth",
    "admin"
]);

// API Endpoints for Real-time Notifications
$router->add("GET", "/api/notifications/unread-count", "Admin\NotificationController@getUnreadCount", ["auth"]);
$router->add("GET", "/api/notifications/list", "Admin\NotificationController@getNotifications", ["auth"]);
$router->add("POST", "/api/notifications/mark-read/{id}", "Admin\NotificationController@markAsRead", ["auth"]);
$router->add("POST", "/api/notifications/mark-all-read", "Admin\NotificationController@markAllAsRead", ["auth"]);

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

// User Management Routes
$router->add("GET", "/admin/users", "Admin\\UserManagementController@index", ["auth", "admin"]);
$router->add("GET", "/admin/users/create", "Admin\\UserManagementController@create", ["auth", "admin"]);
$router->add("POST", "/admin/users/store", "Admin\\UserManagementController@store", ["auth", "admin"]);
$router->add("GET", "/admin/users/{id}/edit", "Admin\\UserManagementController@edit", ["auth", "admin"]);
$router->add("POST", "/admin/users/{id}/update", "Admin\\UserManagementController@update", ["auth", "admin"]);
$router->add("POST", "/admin/users/{id}/delete", "Admin\\UserManagementController@delete", ["auth", "admin"]);
$router->add("GET", "/admin/users/roles", "Admin\\UserManagementController@roles", ["auth", "admin"]);
$router->add("GET", "/admin/users/permissions", "Admin\\UserManagementController@permissions", ["auth", "admin"]);
$router->add("GET", "/admin/users/bulk", "Admin\\UserManagementController@bulk", ["auth", "admin"]);
$router->add("POST", "/admin/users/bulk-delete", "Admin\\UserManagementController@bulkDelete", ["auth", "admin"]);

// Notification Routes
$router->add("GET", "/notifications", "NotificationController@index", ["auth"]);
$router->add("GET", "/notifications/unread-count", "NotificationController@getUnreadCount", ["auth"]);
$router->add("POST", "/notifications/{id}/read", "NotificationController@markAsRead", ["auth"]);
$router->add("POST", "/notifications/mark-all-read", "NotificationController@markAllAsRead", ["auth"]);
$router->add("DELETE", "/notifications/{id}", "NotificationController@delete", ["auth"]);

// Notification Preferences Routes
$router->add("GET", "/notifications/preferences", "NotificationPreferencesController@index", ["auth"]);
$router->add("GET", "/notifications/preferences/get", "NotificationPreferencesController@get", ["auth"]);
$router->add("POST", "/notifications/preferences/update", "NotificationPreferencesController@update", ["auth"]);

// Admin Notification Management Routes
$router->add("GET", "/admin/notifications/manage", "Admin\\NotificationManagementController@index", ["auth", "admin"]);
$router->add("GET", "/admin/notifications/create", "Admin\\NotificationManagementController@create", ["auth", "admin"]);
$router->add("POST", "/admin/notifications/send", "Admin\\NotificationManagementController@send", ["auth", "admin"]);
$router->add("POST", "/admin/notifications/broadcast", "Admin\\NotificationManagementController@broadcast", ["auth", "admin"]);
$router->add("POST", "/admin/notifications/send-to-admins", "Admin\\NotificationManagementController@sendToAdmins", ["auth", "admin"]);

// Notification History Route
$router->add("GET", "/notifications/history", "NotificationController@history", ["auth"]);

// Subscription Management Routes (Admin)
$router->add("GET", "/admin/subscriptions", "Admin\\SubscriptionController@index", ["auth", "admin"]);
$router->add("GET", "/admin/subscriptions/create", "Admin\\SubscriptionController@createPlanPage", ["auth", "admin"]);
$router->add("POST", "/admin/subscriptions/store", "Admin\\SubscriptionController@createPlan", ["auth", "admin"]);
$router->add("GET", "/admin/subscriptions/edit/{id}", "Admin\\SubscriptionController@edit", ["auth", "admin"]);
$router->add("POST", "/admin/subscriptions/update/{id}", "Admin\\SubscriptionController@update", ["auth", "admin"]);

// User Subscription Routes (Checkout Flow)
$router->add("GET", "/subscribe/{planId}", "SubscriptionController@checkout", ["auth"]);
$router->add("POST", "/subscribe/create", "SubscriptionController@create", ["auth"]);
$router->add("GET", "/subscribe/success", "SubscriptionController@success", ["auth"]);
$router->add("GET", "/subscribe/cancel", "SubscriptionController@cancel", ["auth"]);

// Webhook Routes (No auth - PayPal calls this)
$router->add("POST", "/webhooks/paypal", "WebhookController@paypal");




// Payment Gateway Routes
$router->add('GET', '/payment/checkout/{gateway}', 'PaymentController@checkout', ['auth']);
$router->add('GET', '/payment/callback/{gateway}', 'PaymentController@callback');
$router->add('POST', '/payment/webhook/{gateway}', 'PaymentController@webhook');
$router->add('GET', '/payment/webhook/{gateway}', 'PaymentController@webhook');
$router->add('GET', '/payment/failed', 'PaymentController@failed');

// ============================================
// CALCULATOR PERMALINK CATCH-ALL ROUTE
// Must be at the end to not interfere with other routes
// ============================================
$router->add('GET', '/{slug}', 'CalculatorController@permalink');
