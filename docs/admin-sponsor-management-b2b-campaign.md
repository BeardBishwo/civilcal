# Admin Sponsor Management & B2B Campaign Display System

## 0. Quick Map (code anchors)
- Admin sponsors list: `/admin/sponsors` → @app/routes.php#2076-2082 maps to @app/Controllers/Admin/SponsorController.php#29-33, view @themes/admin/views/sponsors/index.php#1-186
- Create sponsor: POST `/admin/sponsors/store` → @app/Controllers/Admin/SponsorController.php#35-60, model @app/Models/Sponsor.php#17-29
- Create campaign: POST `/admin/sponsors/campaigns/create` → @app/Controllers/Admin/SponsorController.php#62-79, model @app/Models/Campaign.php#17-34, form @themes/admin/views/sponsors/index.php#128-177
- Frontend delivery: Calculator pages call `Campaign::getActiveForCalculator` + `recordImpression` @app/Controllers/CalculatorController.php#37-112; banner rendered in @themes/default/views/calculator/converter.php
- Routing & admin middleware: @public/index.php#36-43 loads @app/routes.php; middleware pipeline in @app/Core/Router.php#76-183 with admin check @app/Middleware/AdminMiddleware.php#8-27

## 1. Purpose & Scope
- Provide admin-operated sponsorship and ad campaigns that display on calculator pages, with basic targeting (calculator slug), scheduling, prioritization, and impression accounting.
- Ensure only admins can manage sponsors/campaigns; campaigns shown to end users without exposing admin surfaces.

## 2. Actors
| Actor | Capabilities | Guards |
| --- | --- | --- |
| Admin | Create/list sponsors, upload logos, launch campaigns with targeting and limits. | AdminMiddleware + controller auth check. |
| End User | Views calculator pages; sees a single active campaign if available; impressions are logged. | Public access; campaign filters enforce active/time/limits. |

## 3. Data Model (tables implied)
- `sponsors`: id, name, website_url, contact_person, contact_email, phone, status, logo_path, created_at.
- `campaigns`: id, sponsor_id (FK), calculator_slug, title, banner_image, ad_text, start_date, end_date, priority, max_impressions, current_impressions, current_clicks, status.
- `ad_impressions`: id, campaign_id, user_id (nullable), ip_hash, user_agent, action_type ('view'|'click'), created_at.

## 4. Storage/Layout
- Logos saved under `/public/uploads/sponsors/` (publicly served).
- Banner images field exists but upload handler not implemented (future gap).

## 5. API & Routing Surface
- **GET /admin/sponsors** — list sponsors, show actions (requires auth+admin middleware).
- **POST /admin/sponsors/store** — create sponsor with optional logo upload.
- **POST /admin/sponsors/campaigns/create** — create campaign for a sponsor.
- **GET /calculator/{slug}** & **GET /calculator/{category}/{calculator}** — fetch and render active campaign for slug via `Campaign::getActiveForCalculator`.

## 6. Admin Flows
### 6.1 View Sponsors
1) Route `/admin/sponsors` hits `SponsorController@index` → fetches `Sponsor::getAll()` ordered by name.  
2) View renders table with logo/name/contact/status and action buttons; “New Campaign” button opens modal with sponsor prefilled.

### 6.2 Create Sponsor
1) Modal form POSTs to `/admin/sponsors/store` with company, website, contact, logo file.  
2) Controller validates method, prepares data, uploads logo to `/public/uploads/sponsors/` (creates dir if missing), saves filename.  
3) Calls `Sponsor::create`, then redirects back to `/admin/sponsors`.

### 6.3 Create Campaign
1) Modal POST `/admin/sponsors/campaigns/create` includes sponsor_id, calculator_slug (target), title, ad_text, dates, priority, max_impressions.  
2) Controller builds data array and calls `Campaign::create`.  
3) Redirects to sponsors list. (No validation of overlapping/duplicate slugs yet.)

## 7. Frontend Delivery Flow
1) Calculator controller `converter($categorySlug)` and `show($categorySlug,$calculatorSlug)` call `Campaign::getActiveForCalculator(slug)`.  
2) Query filters: status='active', sponsor status='active', date window contains NOW(), impressions under cap or unlimited, matching slug, ordered by priority DESC then RAND(), limit 1.  
3) If campaign found, `recordImpression` inserts `ad_impressions` row and increments `current_impressions`.  
4) View `calculator/converter.php` displays banner (logo/name, ad_text, CTA link to sponsor website) when `$campaign` is set.

## 8. Status/Business Rules & Invariants
- Sponsors: status defaults to `active`; inactive sponsors should suppress campaigns via join filter (already enforced in query).  
- Campaigns: must be `active`, within date range, and under `max_impressions` (0 = unlimited).  
- A calculator page shows at most one campaign (highest priority, then random).  
- Impressions are always logged before render; clicks can be logged via `recordClick` (helper present, endpoint not wired).  
- Admin access enforced twice: router middleware + controller constructor check.

## 9. Gaps / Risks / TODOs
- No server-side validation of required fields/date order/priority bounds; banner_image upload not wired.  
- No edit/delete endpoints for sponsors/campaigns; table action buttons are placeholders.  
- No click-tracking endpoint hooked to CTA link (function exists).  
- No rate-limiting or fraud prevention on impressions/clicks; IP hashed but could be gamed.  
- No pagination/search for sponsors; no campaign listing per sponsor.  
- Max impressions not decremented on clicks; only views counted.  
- Calculator slug targeting is exact match only; no category/global fallback.

## 10. Suggested Hardening/Enhancements (backlog)
1) Add validation: required fields, date ordering, numeric bounds; return admin flash errors.  
2) Implement sponsor/campaign edit/delete with soft-delete and logo cleanup.  
3) Wire banner uploads + storage; allow selecting existing media.  
4) Add campaign listing and per-sponsor counters in admin table.  
5) Add click endpoint using `recordClick`, and wire CTA to it (with redirect to website_url).  
6) Add rate limiting/fraud checks (per IP/user), and signed tokens for impression logging.  
7) Support global campaigns and category-level targeting fallback.  
8) Expose analytics dashboard (impressions, clicks, CTR by calculator).

## 11. QA Checklist
- Admin route requires auth+admin and renders sponsor list.  
- Sponsor creation saves metadata and logo to `/public/uploads/sponsors/`.  
- Campaign creation persists with correct slug, dates, priority, max_impressions.  
- On calculator page, active campaign returned only when date window and impression cap allow it.  
- Impression count increments and `ad_impressions` row recorded per view.  
- Banner renders with logo or initial and links to sponsor website.
