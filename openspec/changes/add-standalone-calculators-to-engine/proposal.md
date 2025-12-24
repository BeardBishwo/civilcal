# Change: Integrate Standalone Calculators into Calculator Engine

## Why

The Bishwo Calculator platform currently has a mixed architecture where some calculators use the modern Calculator Engine while others remain as standalone implementations. This inconsistency creates several problems:

1. **Maintenance Burden**: Standalone calculators require duplicate code for common functionality (form rendering, validation, result display)
2. **Inconsistent User Experience**: Different calculators have varying UI patterns and behaviors
3. **Limited Features**: Standalone calculators lack advanced features like calculation history, export capabilities, and admin-configurable parameters
4. **Technical Debt**: Maintaining two different calculator architectures increases complexity and slows development

The Calculator Engine provides a robust, standardized framework with:
- Centralized validation and unit conversion
- Consistent UI/UX patterns
- Admin-configurable calculators
- Enhanced security and performance
- Better testability and maintainability

## What Changes

### Breaking Changes
- **URL Structure**: Some calculators may need URL parameter adjustments to work with the engine
- **Form Data**: Input field names must match engine configuration requirements
- **Output Format**: Results will follow the standardized engine format

### New Features
- **Admin Management**: All calculators can be managed through the admin dashboard
- **Configuration**: Calculator parameters, validation rules, and units become configurable
- **Enhanced UX**: Consistent form styling, error handling, and result display
- **Export Support**: All calculators gain export capabilities (PDF, Excel, etc.)

### Affected Calculators
The following standalone calculators will be migrated to the Calculator Engine:

**Electrical Engineering:**
- arc-flash-boundary.php
- battery-load-bank-sizing.php
- demand-load-calculation.php
- feeder-sizing.php
- general-lighting-load.php
- motor-full-load-amps.php
- ocpd-sizing.php
- ohms-law.php (already migrated)
- panel-schedule.php (already migrated)
- power_factor.php
- receptacle-load.php
- voltage_divider.php

**Other Categories:**
- Additional calculators in civil, plumbing, HVAC, fire, structural, and estimation modules

## Impact

### Affected Specs
- **calculators-engine**: Core calculator functionality
- **admin-calculator-management**: Admin interface for managing calculators
- **user-experience**: Consistent calculator interface patterns
- **api-calculators**: API endpoints for calculator operations

### Affected Code
- **modules/electrical/load-calculation/**: Electrical calculators directory
- **modules/civil/**: Civil engineering calculators
- **modules/plumbing/**: Plumbing calculators
- **modules/hvac/**: HVAC calculators
- **modules/fire/**: Fire protection calculators
- **modules/structural/**: Structural engineering calculators
- **modules/estimation/**: Estimation calculators
- **app/Engine/CalculatorEngine.php**: Core engine functionality
- **app/Services/CalculatorManagement.php**: Admin management service
- **app/Config/Calculators/**: Calculator configuration files

### Migration Strategy
1. **Phase 1**: Migrate electrical calculators (highest usage)
2. **Phase 2**: Migrate civil engineering calculators
3. **Phase 3**: Migrate remaining categories
4. **Phase 4**: Remove standalone calculator files and update routing

### Backward Compatibility
- Existing calculator URLs will be preserved through routing updates
- Legacy standalone calculators will remain as fallback during transition
- Database schema changes will be backward compatible