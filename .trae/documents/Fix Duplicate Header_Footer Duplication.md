## Root Cause
- Many view files include `header.php` and `footer.php` directly while the layout (`app/Views/layouts/main.php`) also includes them, producing two headers and two footers.
- Theme footer closes `</main></body></html>`, which is safe in the layout when used alone, but duplicates when views also include it.

## Scope of Changes
- Rely exclusively on layouts to render header and footer.
- Remove header/footer includes from views under `app/Views/**` and `themes/default/views/**` that currently do direct `include`/`require` of those partials.

## Files To Update (initial set)
- `themes/default/views/index.php` (remove header/footer includes)
- `app/Views/admin/settings/index.php` (remove header/footer includes)
- `app/Views/admin/dashboard_complex.php` (remove header/footer includes)
- `app/Views/admin/setup/checklist.php` (remove header/footer includes)
- `app/Views/help/article.php` (remove header/footer includes)
- `app/Views/developer/index.php` (remove header/footer includes)
- `app/Views/user/profile.php` (remove header/footer includes)
- `app/Views/payment/failed.php`, `success.php`, `esewa-form.php`, `checkout.php` (remove header/footer includes)
- Plus any other views containing `include/require` of `partials/header.php` or `partials/footer.php`.

## Implementation Steps
1. Keep layout-driven inclusion:
   - Confirm `app/Views/layouts/main.php` includes header/footer once and wraps `<?= $content ?>`.
2. Edit the listed views to remove lines that include `themes/default/views/partials/header.php` and `.../footer.php` (or app equivalents). No functional content is removed—only duplicate chrome.
3. Ensure admin pages rely on `app/Views/layouts/admin.php` and do not include header/footer inside the view files.
4. Optional hardening: add `themes/default/views/layouts/main.php` later and migrate theme views to stop self-including partials; not required for resolving duplication now.

## Validation
- Load homepage, admin dashboard, help article, user profile, and a payment page.
- Inspect DOM: exactly one `header` and one footer block.
- Visual check: no stacked headers/footers as in the screenshots.
- Smoke-test interactive elements (navbar toggles, back-to-top) once to ensure scripts still bind.

## Rollback Plan
- Changes are deletions of include lines only; if needed, restore those lines in the specific views.