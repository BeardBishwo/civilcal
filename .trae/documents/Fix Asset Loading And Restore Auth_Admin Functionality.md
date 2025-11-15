## Diagnosis
- Asset requests to `theme-assets.php` abort with `net::ERR_ABORTED` because URLs point to `.../theme-assets.php` at the project root while the actual file lives in `public/theme-assets.php`. This happens when the web server document root is the project root, not `public`.
- Evidence:
  - Asset proxy: `public/theme-assets.php` streams files securely (public/theme-assets.php:1–52).
  - URL builder: `ThemeManager::themeUrl()` generates `{$baseUrl}/theme-assets.php?...` (app/Services/ThemeManager.php:991–1006).
  - Base URL strips `/public` for clean URLs (app/Services/ThemeManager.php:56–65, app/Config/config.php:17–41), assuming docroot is `public`.
  - Header loads CSS via `themeUrl(...)` (themes/default/views/partials/header.php:121–129) and footer loads JS similarly (themes/default/views/partials/footer.php:21–27).
- Result: Pages render HTML but no CSS/JS across modules (e.g., `landing/civil.php`), and UI-driven flows (login, register, admin) feel broken.

## Fix Strategy
- Make `ThemeManager::themeUrl()` docroot-aware so it automatically targets `/public/theme-assets.php` when `public` is not the document root; keep current behavior when `public` is the docroot.
- Leave `getBaseUrl()` and `APP_BASE` logic intact (clean URLs), but ensure the proxy path itself is correct in both deployments.
- Verify asset delivery; then validate key flows (civil landing, calculator pages, login/register, admin) and run the project’s test suite.

## Implementation Steps
1. Update `themeUrl` to compute the correct proxy path dynamically:
   - Detect whether `BASE_PATH.'/theme-assets.php'` exists in the web root; if not, prefix `/public` (e.g., return `{$baseUrl}/public/theme-assets.php?...`).
   - Keep query handling (`?v=mtime`) and path sanitation identical.
   - File to change: `app/Services/ThemeManager.php` (around themeUrl at 991–1006).
2. No view changes required:
   - Header/footer keep calling `ThemeManager->themeUrl(...)`; they will start working once step 1 is applied.
   - Asset files exist (e.g., `themes/default/assets/css/theme.css`, `themes/default/assets/js/back-to-top.js`).
3. Quick auth hardening pass (post-asset fix):
   - Add CSRF validation in `AuthController@login/register/forgotPassword` for forms already emitting tokens (themes/default/views/auth/*.php).
   - Ensure session start and redirects remain intact.
4. Routing/admin check:
   - Confirm `/admin` routes dispatch to `Admin\MainDashboardController@index` (app/routes.php:79–81) and admin layout loads correctly (app/Views/layouts/admin.php).
   - Align the dashboard entry if multiple variants exist.

## Verification Plan
- Browser checks:
  - Load home and `civil` pages; confirm CSS (`theme.css`, `civil.css`) and JS (`back-to-top.js`) requests return 200 and render correctly.
  - Confirm console shows “Back to top script loaded successfully”.
- Functional flows:
  - Login and Register: submit forms, verify redirect, session, and CSRF handling.
  - Admin: open `/admin` and `/admin/dashboard`, verify widgets render and navigation works.
- Tests:
  - Run PHP scripts via the project harness `tests/test_runner.php` to surface any regressions.
  - Execute UI/system checks from `tests/frontend` and key diagnostics in `tests/server` (asset and MVC tests).

## Deliverables
- Working site with themed CSS/JS on all modules.
- Login, register, and admin flows functional.
- Test suite executed with summary of pass/fail and targeted fixes where needed.

## Notes
- If you prefer a server-level fix, setting the site’s document root to `public` in Laragon also resolves the asset proxy path without code changes; the code-level fix will make both setups work seamlessly.