# Task 1: Strategy Analysis for Theme-Aware View Rendering

## Objective
Clarify the desired strategy for migrating from `app/Views` to theme-based view rendering and completing the migration.

## Current Situation
- Views are currently split between `app/Views` and `themes/admin/views`
- Two rendering mechanisms exist:
  - `Controller::view()` - hardcoded to `app/Views`
  - `View::render()` - theme-aware with fallbacks to `app/Views`
- Goal: Delete `app/Views` and use only theme-based rendering

## Strategy Decision

### Chosen Approach
1. **Keep controllers using `$this->view->render()`** - Do not hardcode theme paths in controllers
2. **Let `View::render()` and `ThemeManager` resolve paths** to theme directories
3. **Remove all fallbacks to `app/Views`** once files are migrated
4. **Delete `app/Views` directory** after validation

### Rationale
- Preserves theme flexibility
- Keeps controllers clean and theme-agnostic
- Eliminates dual view systems
- Simplifies maintenance

## Implementation Direction
- Admin views: Use `admin/` prefixed paths, resolve to `themes/admin/views/`
- Frontend views: Use non-prefixed paths, resolve to `themes/<active-theme>/views/`
- All controllers should use `$this->view->render()` method
- Remove or deprecate `Controller::view()` method

## Success Criteria
- All view rendering goes through theme system
- No hardcoded paths to `app/Views`
- `app/Views` can be safely deleted
- All pages function correctly