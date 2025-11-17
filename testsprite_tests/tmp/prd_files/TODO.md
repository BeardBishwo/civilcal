# Admin Settings MVC Refactoring Plan

## Current Issues
- Settings pages are standalone HTML documents instead of MVC partial views
- SettingsController renders 'admin/settings/index' but files are named differently
- No proper integration with admin layout system
- Routes point to controller methods but views don't match

## Plan
1. Update SettingsController to properly render settings pages
2. Create proper partial views for each settings section
3. Update admin/settings/index.php to be the main settings layout
4. Ensure admin layout integration works correctly
5. Test all settings pages work properly

## Implementation Steps
- [ ] Update SettingsController methods to use correct view names
- [ ] Create admin/settings/partials/ directory for section content
- [ ] Refactor general.php into partial view
- [ ] Refactor other settings pages (application.php, users.php, etc.)
- [ ] Update index.php to be the main settings page with navigation
- [ ] Test admin/settings/general route works
- [ ] Test all other settings routes work
- [ ] Verify admin layout is applied correctly
