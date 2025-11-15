## URLs and Code Mapping

* `/help` → `app/Controllers/HelpController.php:21` (`index`) renders the help center

* `/admin` → `app/Controllers/Admin/MainDashboardController.php:21` (`index`) and gated by `app/Middleware/AdminMiddleware.php:8`

* `/profile` → `app/Controllers/ProfileController.php:27` (`index`) gated by `app/Middleware/AuthMiddleware.php:7`

* Router table: `app/routes.php:109-114` (help), `app/routes.php:79-81` (admin), `app/routes.php:48-61` (profile and related)

* Login handler: `app/Controllers/AuthController.php:31` (`login`) uses `app/Core/Auth.php:8` (`login`) and middleware in `app/Middleware/AuthMiddleware.php:7`

## Login Steps

* Open `http://localhost/Bishwo_Calculator/login`

* Sign in with your email and password you provided (keep credentials private)

* On success, the app sets session values and redirects to `dashboard` (see `app/Controllers/AuthController.php:69-73`)

## Validation Steps

* Help: visit `http://localhost/Bishwo_Calculator/help`; expect status 200 and the help center UI (controller at `app/Controllers/HelpController.php:21`)

* Profile: visit `http://localhost/Bishwo_Calculator/profile` after login; expect status 200, no PHP warnings, avatar fallback, and stats (controller at `app/Controllers/ProfileController.php:27`; avatar serving at `app/Controllers/ProfileController.php:274-307`)

* Admin: visit `http://localhost/Bishwo_Calculator/admin`; expect either dashboard if admin or an access denied page/redirect if not admin (`app/Middleware/AdminMiddleware.php:42-85`)

## Expected Results

* No "Undefined array key" or other PHP warnings on `/profile` and `/help`

* `/admin` accessible only with an admin role; non-admin sees 403 Access Denied HTML or redirect to `/login`

## If Issues Found

* Case sensitivity: use exact base `http://localhost/Bishwo_Calculator/...` to match router-derived base path

* Authentication failures: check `Auth::login` logic in `app/Core/Auth.php:8-86` and session setup in `app/Controllers/AuthController.php:61-73`

* Remaining profile warnings: inspect guarded rendering in the view `app/Views/user/profile.php` and `getProfileCompletion` usage (`app/Controllers/ProfileController.php:27-48`)

## Next Engineering Steps (upon approval)

1. Run a focused check for `/help`, `/profile`, `/admin` to confirm no PHP warnings and correct status codes
2. Re-run the backend tests to verify pages are clean and summarize any failures
3. If `/admin` is needed, confirm your user’s role via `User::isAdmin` and elevate or adjust as appropriate
4. Address any residual profile or audit log issues and re-verify

