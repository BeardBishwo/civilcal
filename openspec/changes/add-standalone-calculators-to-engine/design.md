## Context

The Bishwo Calculator platform currently operates with a dual architecture:
- **Modern Calculator Engine**: A sophisticated system providing centralized validation, unit conversion, formula execution, and result formatting
- **Legacy Standalone Calculators**: Traditional PHP files with embedded HTML/CSS/JS that handle their own logic independently

This inconsistency creates maintenance overhead, inconsistent user experiences, and limits feature availability. The goal is to migrate all standalone calculators to the Calculator Engine while preserving functionality and improving the overall platform architecture.

**Constraints:**
- Maintain backward compatibility for existing calculator URLs
- Preserve calculation accuracy and business logic
- Ensure no downtime during migration
- Support phased rollout across different calculator categories
- Maintain existing user permissions and access controls

**Stakeholders:**
- End users (engineers, contractors, construction professionals)
- Admin users (calculator configuration and management)
- Development team (maintenance and future enhancements)

## Goals / Non-Goals

### Goals
- **Unified Architecture**: All calculators use the Calculator Engine for consistent behavior and maintenance
- **Enhanced User Experience**: Consistent UI patterns, better error handling, and improved accessibility
- **Admin Capabilities**: Full management of all calculators through admin interface
- **Feature Parity**: All calculators gain access to advanced features (history, exports, configuration)
- **Maintainability**: Simplified codebase with reduced duplication and easier testing
- **Performance**: Optimized calculation execution and resource usage

### Non-Goals
- **Breaking User Workflows**: No disruption to existing user calculation processes
- **Complete UI Redesign**: Focus on consistency rather than major visual changes
- **Database Schema Changes**: Avoid structural changes that would require complex migrations
- **Third-party Integration**: No new external dependencies or API integrations
- **Mobile App Changes**: Focus on web platform only

## Decisions

### Decision: Phased Migration Strategy
**What**: Migrate calculators in phases by category (Electrical → Civil → Others)
**Why**: Reduces risk, allows for validation at each step, and enables rollback if issues arise
**Alternatives considered**:
- Big bang migration: Too risky for production system
- Calculator-by-calculator: Too slow and creates prolonged inconsistency

### Decision: Backward Compatibility Layer
**What**: Implement compatibility layer in Calculator Engine to handle legacy calculator patterns
**Why**: Ensures seamless transition without breaking existing functionality
**Alternatives considered**:
- Direct replacement: High risk of breaking existing calculators
- Parallel systems: Creates prolonged inconsistency and maintenance burden

### Decision: Shared Template System
**What**: Use existing shared calculator template for all migrated calculators
**Why**: Provides immediate consistency and leverages proven patterns
**Alternatives considered**:
- Custom templates per category: Increases complexity and maintenance
- Individual UI updates: Too resource-intensive for initial migration

### Decision: Admin-Driven Configuration
**What**: Move all calculator configuration to admin interface with import/export capabilities
**Why**: Empowers administrators and reduces code deployment requirements
**Alternatives considered**:
- File-based configuration only: Less flexible for runtime changes
- Mixed approach: Creates confusion about where configuration lives

## Architecture

### Current State
```
Standalone Calculators → Direct PHP Processing → HTML Output
Calculator Engine → Centralized Processing → Template Rendering
```

### Target State
```
All Calculators → Calculator Engine → Shared Template → Consistent Output
```

### Key Components

#### 1. Enhanced Calculator Engine
- **Legacy Compatibility Layer**: Handles existing calculator patterns
- **Migration Utilities**: Converts standalone calculators to engine format
- **Validation Framework**: Ensures migrated calculators maintain accuracy
- **Performance Monitoring**: Tracks calculation execution times

#### 2. Admin Management Interface
- **Calculator Import Wizard**: Batch import of standalone calculators
- **Configuration Editor**: Visual editor for calculator parameters
- **Migration Status Dashboard**: Track progress across categories
- **Rollback Mechanisms**: Quick rollback for failed migrations

#### 3. Shared Template System
- **Enhanced Form Rendering**: Supports all existing calculator input patterns
- **Dynamic Validation**: Client-side validation based on engine configuration
- **Consistent Styling**: Unified CSS classes and responsive design
- **Accessibility Features**: WCAG 2.1 AA compliance

#### 4. Migration Pipeline
- **Inventory Scanner**: Automatically detects standalone calculators
- **Pattern Analyzer**: Identifies common patterns for batch processing
- **Configuration Generator**: Creates engine configuration from existing calculators
- **Validation Suite**: Ensures migrated calculators match original behavior

## Risks / Trade-offs

### Risk: Calculation Accuracy Loss
**Mitigation**: Comprehensive testing suite with side-by-side comparison of results
**Trade-off**: Additional development time for validation vs. risk of incorrect calculations

### Risk: Performance Degradation
**Mitigation**: Performance monitoring and optimization during migration
**Trade-off**: Engine overhead vs. improved maintainability and features

### Risk: User Experience Disruption
**Mitigation**: Gradual rollout with user feedback collection and quick fixes
**Trade-off**: Consistency improvements vs. temporary user adaptation period

### Risk: Migration Complexity
**Mitigation**: Phased approach with rollback capabilities and thorough documentation
**Trade-off**: Extended migration timeline vs. reduced risk of system-wide issues

## Migration Plan

### Phase 1: Foundation (Week 1-2)
1. Enhance Calculator Engine with legacy compatibility
2. Build migration utilities and validation tools
3. Create admin interface enhancements
4. Set up testing infrastructure

### Phase 2: Electrical Calculators (Week 3-4)
1. Migrate high-usage electrical calculators
2. Validate calculation accuracy
3. Test admin management interface
4. Gather user feedback and iterate

### Phase 3: Civil Engineering (Week 5-6)
1. Migrate civil engineering calculators
2. Optimize performance based on lessons learned
3. Expand admin capabilities
4. Validate cross-category consistency

### Phase 4: Remaining Categories (Week 7-8)
1. Migrate remaining calculator categories
2. Complete cleanup of standalone calculators
3. Final performance optimization
4. Documentation and training

### Rollback Strategy
- **Per-Category Rollback**: Each category can be rolled back independently
- **Calculator-Level Rollback**: Individual calculators can revert to standalone
- **Database Backup**: Full backup before each migration phase
- **Monitoring Alerts**: Automated alerts for calculation discrepancies

## Open Questions

1. **Configuration Storage**: Should calculator configurations be stored in database or files?
   - Database: Better for admin interface but adds complexity
   - Files: Simpler but less flexible for runtime changes

2. **URL Structure**: How to handle URL changes for better SEO while maintaining compatibility?
   - Redirect strategy: Clean URLs with redirects from old ones
   - Dual support: Support both old and new URL patterns

3. **Custom Calculator Logic**: How to handle calculators with unique business logic?
   - Plugin system: Allow custom logic within engine framework
   - Hybrid approach: Keep some calculators standalone with engine integration

4. **Performance Monitoring**: What metrics should be tracked during migration?
   - Calculation accuracy: Ensure no regression in results
   - Response times: Monitor for performance degradation
   - User engagement: Track usage patterns and satisfaction