# CodeCanyon Marketplace Readiness Checklist

Focus: Licensing, configuration UX, installer, documentation, demo data, and what CodeCanyon reviewers usually look for.

---

## 1. Licensing

### What CodeCanyon expects

- **Clear license statement** in documentation and in-app footer/admin
- **License key validation** (optional but recommended for premium scripts)
- **One domain per license** enforcement (optional but common)
- **Copyright notice** in footer and admin panel
- **No obfuscated code** unless explicitly stated

### Current state

- No visible license key system in installer or admin
- No copyright/license notice in admin or public footer
- No domain validation
- Code is open and readable (good)

### Action items

- Add copyright/license notice to admin footer and public footer
- Add a simple license key field in admin (optional, but can boost perceived value)
- Add a “License” page in admin showing key, domain, and terms

---

## 2. Configuration UX

### What buyers expect

- **First-run wizard** (installer) that is clear and modern
- **Admin settings page** with clear sections and tooltips
- **In-place help** (small help icons or tooltips next to complex fields)
- **One-click actions** for common tasks (clear cache, backup, send test email)
- **Validation and friendly error messages** when settings are invalid

### Current state

- Installer is well-structured and modern (two versions: index.php and installer.php)
- Admin settings exist under `/admin/settings` with subpages
- Some tooltips and help exist in installer
- Admin settings pages are present but need a review for consistency and help text

### Action items

- Add help tooltips to key admin settings (email, payment, security)
- Add a “System Health” or “Environment Check” admin page for post-install diagnostics
- Add one-click buttons: “Test Email,” “Clear Cache,” “Run Backup”
- Ensure all admin forms have clear validation messages

---

## 3. Installer

### CodeCanyon reviewer expectations

- **Self-contained installer** that does not require manual file edits
- **Requirements check** with clear pass/fail
- **Database setup** (create tables, create admin)
- **Security post-install** (delete/rename install folder)
- **No hardcoded paths** (works in subdirectory)

### Current state

- Installer exists in `/install/` with:
  - Requirements check (PHP version, extensions, permissions)
  - Database configuration
  - Admin user creation
  - Email configuration (optional)
  - Clean UI with progress steps
  - Auto-delete option for install folder
- Two installer entry points: `index.php` and `installer.php` (some redundancy)
- `.env` file creation and lock files are handled
- Good XAMPP/local setup guidance

### Action items

- Consolidate to one installer entry point (remove redundancy between index.php and installer.php)
- Add a “Demo Data” option in installer (optional, to pre-populate calculators/sample data)
- Add a final “Security Check” step reminding to delete install folder and set file permissions
- Test installer on a clean host (not just XAMPP) to ensure it works in shared hosting

---

## 4. Documentation

### What buyers need

- **README** with installation steps and requirements
- **Admin Guide** (how to configure users, calculators, themes, etc.)
- **User Guide** (how to use calculators, save history, etc.)
- **Developer/Customization Guide** (how to add calculators, themes, modules)
- **FAQ/Support** section
- **Changelog** for updates

### Current state

- Internal documentation is strong (`app/Views/README.md` is detailed)
- No buyer-facing docs in the repo root
- No changelog or FAQ visible to buyers

### Action items

- Create `README.md` in repo root for buyers (installation, requirements, quick start)
- Create `docs/` folder:
  - `admin-guide.md`
  - `user-guide.md`
  - `customization.md`
  - `faq.md`
  - `CHANGELOG.md`
- Add a “Help” section in the admin that links to these docs
- Include a “Support” link in admin footer

---

## 5. Demo Data

### Why it matters

- Buyers want to see the app populated immediately after install
- Empty apps look “broken” in demos
- Demo data showcases features (calculators, users, reports)

### Current state

- Installer creates admin user and database tables
- No demo data (calculators, sample users, reports)

### Action items

- Add an “Install Demo Data” checkbox in installer
- Create a small SQL seed with:
  - 2-3 sample calculators with sample calculations
  - 1 demo user (non-admin)
  - Sample calculation history
  - Sample analytics data (optional)
- Ensure demo data can be removed later from admin

---

## 6. What CodeCanyon Reviewers Usually Look For

| Area | What they check | How you score | Action |
|-------|----------------|----------------|--------|
| Code Quality | Clean, readable PHP/JS/CSS; no errors; no hardcoded credentials | Strong (MVC, services, themes) | Run through all pages and fix any PHP notices/warnings |
| Security | Input escaping, CSRF, SQL injection prevention, file upload safety, admin protection | Good (CSRF token, middleware) | Verify CSRF on all forms; ensure file uploads are validated |
| Installer | Works out of the box, requirements check, no manual edits | Good (modern installer) | Consolidate installer; add demo data option |
| Documentation | Clear install/config docs; no broken links | Internal docs good; buyer-facing missing | Add buyer docs in repo root |
| UI/UX | Responsive, consistent, no broken pages, good error messages | Good (Bootstrap/Tailwind) | Click through every admin link; fix broken pages |
| Performance | Not excessively slow; no obvious N+1 queries; caching optional | Unknown (no profiling) | Add simple caching toggles; avoid heavy queries |
| Licensing | License notice, no obfuscation, one domain per license | Missing | Add license notice and optional key system |

---

## 7. Quick Pre-Submission Checklist

- [ ] Installer works on a clean host (not just XAMPP)
- [ ] All admin pages load without errors
- [ ] All forms have CSRF token and validation
- [ ] Installer can be deleted post-install (auto or manual)
- [ ] Public and admin footers show copyright/license
- [ ] Demo data option works (optional)
- [ ] Buyer README and docs exist in repo root
- [ ] No test/debug files in the final package
- [ ] No hardcoded paths (works in subdirectory)
- [ ] No PHP notices/warnings with error_reporting(E_ALL)
- [ ] Responsive design works on mobile
- [ ] Email test works (if configured)
- [ ] Backup/restore works (if present)

---

## 8. Packaging for CodeCanyon

### What to include

- All application files (excluding `/install` after it runs)
- Documentation files (`README.md`, `docs/`, `CHANGELOG.md`)
- License file (`LICENSE.md`)
- `database.sql` (for manual import, if needed)
- Any assets (images, CSS/JS)

### What to exclude

- `/tests/`, `/debug/`, `/documentation/` (internal)
- `.env.example` is OK, but not `.env` with real data
- Any development logs or temporary files
- IDE files (`.vscode`, `.idea`)

### Suggested folder structure for the ZIP

```
Bishwo-Calculator/
├── app/
├── config/
├── database/
├── docs/
│   ├── admin-guide.md
│   ├── user-guide.md
│   ├── customization.md
│   ├── faq.md
│   └── CHANGELOG.md
├── install/
├── public/
├── storage/
│   ├── .htaccess
│   └── .gitkeep (to keep empty dirs)
├── themes/
├── vendor/
├── .env.example
├── README.md
├── LICENSE.md
└── database.sql
```

---

## 9. Priorities for “Best Seller” polish

1. **Installer consolidation and demo data** (high impact on first impression)
2. **Buyer-facing documentation** (reduces support tickets)
3. **Admin help tooltips and one-click actions** (UX win)
4. **License/copyright in footers** (marketplace requirement)
5. **Full admin page click-through and fix any broken views** (prevents rejections)
6. **Performance note in docs** (e.g., “Enable caching for production”)

---

## 10. Next concrete steps

1. Run through the installer on a fresh folder and note any rough edges.
2. Click every admin link and fix any 404/500/layout issues.
3. Add buyer docs in repo root (`README.md`, `docs/`).
4. Add copyright/license notice to admin and public footers.
5. Add a “Demo Data” option in the installer.
6. Package without `/tests/` and internal docs, then test the ZIP on a clean host.

After these, the project should be in a strong position for CodeCanyon approval and good buyer satisfaction.