# MVC Views Directory

This directory contains **Controller-rendered views** for the MVC framework.

## Structure:
- `admin/` - Admin panel views (rendered by AdminController)
- `auth/` - Authentication views (rendered by AuthController) 
- `user/` - User dashboard views (rendered by UserController)
- `payment/` - Payment system views (rendered by PaymentController)
- `layouts/` - Layout templates for MVC views
- `partials/` - Reusable partial views for MVC

## Usage:
These views are rendered by Controllers using the View class:
```php
// In a Controller:
$this->view->render('admin/dashboard', $data);
```

## vs themes/default/views/
- **app/Views/** = MVC Controller views (framework)
- **themes/default/views/** = Theme direct-access pages (public-facing)

Both are needed for different purposes in the application architecture.
