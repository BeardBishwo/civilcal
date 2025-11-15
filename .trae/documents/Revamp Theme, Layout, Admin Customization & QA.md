## Scope Overview
- Deliver a professional, accessible theme and a consistent layout, fix broken links, add modular components, enhance admin customization, implement robust error handling, and validate with MCP testsprite.

## Phase 1: Audit & Design Foundations
- Consolidate chrome rendering in `app/Views/layouts/main.php` and theme partials; remove direct header/footer includes from views (verified on landing pages and theme index).
- Establish design tokens: colors, typography, spacing, shadows, motion in `themes/default/assets/css/theme.css` and `themes/default/theme.json`.
- Accessibility: define contrast-safe palette (WCAG AA) and typographic scale; load web-safe/GF fonts via `theme.json` → `ThemeManager`.
- Global motion: introduce subtle transitions for focus/hover/active; respect `prefers-reduced-motion`.

## Phase 2: Layout Restructuring
- Header (`themes/default/views/partials/header.php`):
  - Proper logo placement, primary nav, utility links (profile/admin), responsive mobile menu.
  - Sticky header with shadow on scroll; keyboard navigable dropdowns.
- Footer (`themes/default/views/partials/footer.php`):
  - Sticky-footer layout using flex column: wrap in `main.php` → `body > .page` with `min-height: 100vh; display: flex;` and `main{flex:1}` to keep footer at viewport bottom.
  - Footer content: copyright, links, back-to-top; responsive grid.
- Main layout (`app/Views/layouts/main.php`):
  - Introduce `.page` container, standardized `header/main/footer` structure.
  - Section management for left/right content areas using CSS grid utility classes; responsive breakpoints.

## Phase 3: Componentization
- Create reusable partials/components under `themes/default/views/partials/`:
  - `nav.php`, `hero.php`, `section.php`, `card.php`, `subnav.php`, `button.php`.
- Migrate landing views (`themes/default/views/landing/*`) to use components (hero + category grid) without duplicating chrome.
- Adopt MVC-friendly helpers (already present: `theme-helpers.php`) for asset inclusion and cache-busting.

## Phase 4: Functional Improvements
- Links and routing:
  - Validate profile/admin links; fix paths in views to match `app/routes.php`.
  - Ensure authenticated-only routes use `auth` middleware and redirect properly.
- Error handling:
  - Add centralized error handler (global exception + 404/500 views) in `app/Core` and route fallbacks in `app/Core/Router.php`.
  - Uniform flash messages and form validation feedback.
- Modularity:
  - Ensure all modules render via `View::render()` and do not include header/footer directly.

## Phase 5: Admin CMS-Like Controls
- Extend `Admin\ThemeCustomizeController` and `admin/themes/customize.php`:
  - Tabs for Colors, Typography, Layout, Motion; bind to `theme.json` via `ThemeManager`.
  - Live preview endpoint (`/admin/themes/preview`) applying draft tokens without publishing.
- Content management (WordPress-like):
  - Add `ContentController` + views for Pages, Sections, Menus.
  - Models/services for storing content (DB or JSON-backed initially); status: draft/published, versioning.
  - WYSIWYG editor, slug management, menu builder; preview before publish.

## Phase 6: Code Review & QA (MCP testsprite)
- Generate repository code summary and PRD; produce frontend test plan.
- Bootstrap tests on local service; validate critical flows: home, landing pages, calculators, profile, admin customization.
- Add smoke tests for header/nav/footer behavior and responsive breakpoints.
- Cross-browser checks and accessibility pass (keyboard nav, focus rings, contrast).

## Phase 7: Deliverables
- Fully functional site with new theme and layout.
- Documentation: design tokens, component usage, admin customization workflow.
- Testing report: testsprite outputs + manual QA matrix.
- Verified responsive design across major browsers.
- Admin panel with theme and content editing, with preview/publish.

## Key Files To Update
- `app/Views/layouts/main.php`, `app/Views/layouts/admin.php`
- `themes/default/views/partials/header.php`, `themes/default/views/partials/footer.php`, `themes/default/views/partials/*` (new components)
- `themes/default/assets/css/theme.css`, `themes/default/theme.json`
- `app/Services/ThemeManager.php` (ensure config propagation)
- `app/Core/Router.php`, `app/Core/ErrorHandler.php` (new), `app/routes.php` (route sanity)
- `app/Controllers/Admin/ThemeCustomizeController.php`, `app/Views/admin/themes/customize.php`
- New: `app/Controllers/ContentController.php`, `app/Models/Content.php` or service layer

## Acceptance Criteria
- Single header/footer per page; footer fixed to viewport bottom on short pages.
- WCAG AA color contrast; consistent font scale; smooth but subtle motion respecting reduced-motion.
- All primary navigation and profile/admin links work under auth rules.
- Centralized error handling with user-friendly 404/500 pages.
- Admin can customize theme tokens and preview changes; content pages/menus manageable and previewable.
- Tests pass and QA checklist validated.

If approved, I will implement these phases iteratively, verifying with testsprite at key milestones and sharing preview links for visual validation.