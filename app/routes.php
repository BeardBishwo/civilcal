<?php
// Routes Definition
// This file defines all application routes

$router->add('GET', '/', 'CalculatorController@dashboard', ['auth']);
$router->add('GET', '/login', 'AuthController@showLogin', ['guest']);
$router->add('POST', '/login', 'AuthController@login', ['guest']);
$router->add('GET', '/register', 'AuthController@showRegister', ['guest']);
$router->add('POST', '/register', 'AuthController@register', ['guest']);
$router->add('POST', '/logout', 'AuthController@logout', ['auth']);

// Calculator Routes
$router->add('GET', '/calculators', 'CalculatorController@index', ['auth']);
$router->add('GET', '/calculators/{category}', 'CalculatorController@category', ['auth']);
$router->add('GET', '/calculators/{category}/{calculator}', 'CalculatorController@show', ['auth']);
$router->add('POST', '/api/calculate/{calculator}', 'ApiController@calculate', ['auth']);

// User Routes
$router->add('GET', '/profile', 'UserController@profile', ['auth']);
$router->add('POST', '/profile', 'UserController@updateProfile', ['auth']);

// Admin Routes
$router->add('GET', '/admin', 'Admin\DashboardController@index', ['auth', 'admin']);
$router->add('GET', '/admin/users', 'Admin\UserController@index', ['auth', 'admin']);
$router->add('GET', '/admin/settings', 'Admin\SettingsController@index', ['auth', 'admin']);

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
