# Admin Sponsor Management System - B2B Advertising Platform

## 0. Quick Map (code anchors)
- Routes: `/admin/sponsors` (list), POST `/admin/sponsors/store`, POST `/admin/sponsors/campaigns/create` @app/routes.php#2076-2082
- Controller: Admin sponsor/campaign handlers @app/Controllers/Admin/SponsorController.php#15-79 (admin gate in __construct)
- Models: Sponsor @app/Models/Sponsor.php#17-65; Campaign @app/Models/Campaign.php#17-80
- Admin UI: Sponsors table + modals @themes/admin/views/sponsors/index.php#1-186
- Frontend display: Campaign fetch/impression @app/Controllers/CalculatorController.php#37-112; banner render @themes/default/views/calculator/converter.php
- Migration: tables defined in @run_b2b_migration.php#5-54
- Routing/middleware pipeline: @public/index.php#20-43 → @app/Core/Router.php#75-183 → @app/Middleware/AdminMiddleware.php#8-27

## 1. Purpose & Scope
Enable admins to onboard sponsor companies and launch targeted campaigns that appear on calculator pages, with date/priority/impression limits and basic analytics (impressions). End users see banners; only admins manage sponsors/campaigns.

## 2. Actors
| Actor | Capabilities | Guards |
| --- | --- | --- |
| Admin | Create/list sponsors, upload logos, create campaigns (target calculator slug, date window, priority, max impressions). | AdminMiddleware + controller constructor check. |
| Visitor | Views calculator pages; sees at most one active campaign; triggers impression logging. | Public; selection filtered server-side. |

## 3. Data Model (tables)
- `sponsors`: id, name, website_url, contact_person, contact_email, phone, status, logo_path, created_at.
- `campaigns`: id, sponsor_id (FK), calculator_slug, title, banner_image (unused in UI), ad_text, start_date, end_date, priority, max_impressions, current_impressions, current_clicks, status.
- `ad_impressions`: campaign_id, user_id (nullable), ip_hash, user_agent, action_type ('view'|'click'), created_at.

## 4. Storage
- Logos: `/public/uploads/sponsors/` (publicly served). Banner image field exists but upload handling is not wired yet.

## 5. Admin Flows
### 5.1 View Sponsors
1) GET `/admin/sponsors` → `SponsorController@index` fetches `Sponsor::getAll()` ordered by name.  
2) Renders table with logo/name/contact/status and action buttons; “New Campaign” button opens modal with sponsor prefilled.

### 5.2 Create Sponsor
1) Modal form POST `/admin/sponsors/store` (multipart).  
2) Controller validates method, builds data, uploads logo to `/public/uploads/sponsors/` (creates dir if missing), sets `logo_path`.  
3) Calls `Sponsor::create`; redirect back to list.

### 5.3 Create Campaign
1) Modal POST `/admin/sponsors/campaigns/create` with sponsor_id, calculator_slug, title, ad_text, start_date, end_date, priority, max_impressions.  
2) Controller calls `Campaign::create`; redirect back.  
3) No validation of overlaps/slug existence or banner upload yet.

## 6. Frontend Delivery & Analytics
1) CalculatorController `converter($categorySlug)` (and `show(...)`) calls `Campaign::getActiveForCalculator(slug)`.  
2) Query filters: campaign status='active', sponsor status='active', date window contains NOW(), `current_impressions < max_impressions` or unlimited, slug match, ordered by priority DESC then RAND(), limit 1.  
3) If found, `recordImpression` inserts `ad_impressions` (with user_id, ip hash, UA) and increments `current_impressions`.  
4) View `calculator/converter.php` displays banner (logo or initial badge, ad_text/CTA to sponsor website) when `$campaign` exists.

## 7. Business Rules & Invariants
- Admin-only management; enforced by middleware + controller constructor.  
- Campaign eligibility: active status, sponsor active, within start/end dates, under max_impressions (0 = unlimited), matches calculator_slug.  
- At most one campaign shown per page (highest priority then random).  
- Impressions always logged before render; clicks supported in model via `recordClick` but no endpoint wired.

## 8. Gaps / Risks / TODOs
- No server-side validation for required fields/date order/priority bounds; no banner upload handling.  
- No edit/delete for sponsors/campaigns; action buttons are placeholders.  
- No click-tracking endpoint; CTA links go direct to website.  
- No rate limiting or fraud detection on impressions/clicks; IP hash only.  
- No pagination/search in sponsors list; no per-sponsor campaign list/count.  
- Targeting is exact slug only; no category/global fallback.  
- Max impressions not decremented on clicks; only views counted.  
- No error messaging/flash feedback on admin forms.

## 9. Security Notes
- Admin surfaces gated by session admin check.  
- Logos saved to public path; consider MIME/type/size validation and random filenames (already timestamped).  
- Impression logging uses hashed IP and truncated UA; may need privacy review.  
- Middleware pipeline critical; keep routes behind auth+admin.

## 10. QA Checklist
- Admin route `/admin/sponsors` requires admin and renders list.  
- Sponsor creation stores metadata + logo file under `/public/uploads/sponsors/`.  
- Campaign creation persists slug/dates/priority/max_impressions linked to sponsor.  
- Calculator page shows campaign only when active and under cap; otherwise no banner.  
- Impression count increments and `ad_impressions` row created when campaign shown.  
- Banner displays logo/initials, ad text, and CTA link to sponsor website.
