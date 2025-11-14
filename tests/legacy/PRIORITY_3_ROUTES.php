<?php
/**
 * Priority 3 - Admin Theme Customization Routes
 * 
 * Add these routes to your router configuration
 * Location: app/routes.php or app/config/routes.php
 */

// Theme Customization Routes
$router->get('/admin/themes/:id/customize', 'Admin\ThemeCustomizeController@index');
$router->post('/admin/themes/:id/save-colors', 'Admin\ThemeCustomizeController@saveColors');
$router->post('/admin/themes/:id/save-typography', 'Admin\ThemeCustomizeController@saveTypography');
$router->post('/admin/themes/:id/save-features', 'Admin\ThemeCustomizeController@saveFeatures');
$router->post('/admin/themes/:id/save-layout', 'Admin\ThemeCustomizeController@saveLayout');
$router->post('/admin/themes/:id/save-custom_css', 'Admin\ThemeCustomizeController@saveCustomCSS');
$router->get('/admin/themes/:id/preview', 'Admin\ThemeCustomizeController@preview');
$router->post('/admin/themes/:id/reset', 'Admin\ThemeCustomizeController@reset');

?>


