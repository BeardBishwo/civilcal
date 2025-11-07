# Bishwo Calculator - Project Structure Report

**Generated:** 2025-11-07 19:53:42

## Project Structure

```
bishwo_calculator/
├── .env.example
├── .git
├── .htaccess
├── README.md
├── TH
├── app
│   ├── Calculators
│   │   ├── BaseCalculator.php
│   │   ├── CivilCalculator.php
│   │   ├── ElectricalCalculator.php
│   │   ├── HvacCalculator.php
│   │   └── PlumbingCalculator.php
│   ├── Controllers
│   │   ├── Admin
│   │   │   ├── DashboardController.php
│   │   │   ├── EmailManagerController.php
│   │   │   ├── ModuleController.php
│   │   │   ├── PluginController.php
│   │   │   ├── SettingsController.php
│   │   │   ├── ThemeController.php
│   │   │   └── UserController.php
│   │   ├── ApiController.php
│   │   ├── AuthController.php
│   │   ├── CalculatorController.php
│   │   ├── CommentController.php
│   │   ├── ExportController.php
│   │   ├── HistoryController.php
│   │   ├── ProfileController.php
│   │   ├── ShareController.php
│   │   └── UserController.php
│   ├── Core
│   │   ├── Auth.php
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Model.php
│   │   ├── Router.php
│   │   ├── Session.php
│   │   ├── Validator.php
│   │   └── View.php
│   ├── Middleware
│   │   ├── AdminMiddleware.php
│   │   ├── AuthMiddleware.php
│   │   └── CorsMiddleware.php
│   ├── Models
│   │   ├── Calculation.php
│   │   ├── CalculationHistory.php
│   │   ├── Comment.php
│   │   ├── EmailResponse.php
│   │   ├── EmailTemplate.php
│   │   ├── EmailThread.php
│   │   ├── ExportTemplate.php
│   │   ├── Payment.php
│   │   ├── Project.php
│   │   ├── Settings.php
│   │   ├── Share.php
│   │   ├── Subscription.php
│   │   ├── User.php
│   │   └── Vote.php
│   ├── Services
│   │   ├── CalculatorService.php
│   │   ├── EmailService.php
│   │   ├── ExportService.php
│   │   ├── FileService.php
│   │   ├── PaymentService.php
│   │   ├── PluginManager.php
│   │   ├── ThemeBuilder.php
│   │   └── ThemeManager.php
│   ├── Views
│   │   ├── admin
│   │   │   ├── dashboard.php
│   │   │   ├── email-manager
│   │   │   │   └── dashboard.php
│   │   │   ├── plugins
│   │   │   │   └── index.php
│   │   │   ├── settings
│   │   │   ├── themes
│   │   │   │   └── index.php
│   │   │   └── users
│   │   ├── auth
│   │   │   ├── forgot-password.php
│   │   │   ├── login.php
│   │   │   └── register.php
│   │   ├── calculators
│   │   │   ├── civil
│   │   │   ├── electrical
│   │   │   ├── estimation
│   │   │   ├── fire
│   │   │   ├── hvac
│   │   │   ├── mep
│   │   │   ├── plumbing
│   │   │   ├── project-management
│   │   │   ├── site
│   │   │   └── structural
│   │   ├── layouts
│   │   │   ├── admin.php
│   │   │   ├── auth.php
│   │   │   └── main.php
│   │   ├── partials
│   │   │   ├── footer.php
│   │   │   ├── header.php
│   │   │   └── navigation.php
│   │   ├── share
│   │   │   └── public-view.php
│   │   └── user
│   │       ├── exports.php
│   │       ├── history.php
│   │       ├── modals
│   │       │   └── profile-modals.php
│   │       └── profile.php
│   ├── bootstrap.php
│   └── routes.php
├── composer.json
├── composer.lock
├── config
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   └── services.php
├── database
│   ├── migrations
│   │   ├── 001_create_users_table.php
│   │   ├── 001_plugin_theme_system.php
│   │   ├── 002_create_subscriptions_table.php
│   │   ├── 002_theme_editor_tables.php
│   │   ├── 003_create
│   │   ├── 004_create_calculation_history.php
│   │   ├── 009_create_export_templates.php
│   │   ├── 010_add_profile_fields_to_users.php
│   │   ├── 011_create_shares_table.php
│   │   ├── 012_create_comments_table.php
│   │   ├── 013_create_votes_table.php
│   │   ├── 014_create_email_threads_table.php
│   │   ├── 015_create_email_responses_table.php
│   │   └── 016_create_email_templates_table.php
│   └── run_migration.php
├── debug
│   ├── debug-config.php
│   ├── error-handler.php
│   ├── log-view
│   ├── logger.php
│   ├── logs
│   │   ├── access.log
│   │   ├── debug.log
│   │   ├── error.log
│   │   └── system.log
│   └── temp
├── includes
│   ├── BackupManager.php
│   ├── ComplianceConfig.php
│   ├── Database.php
│   ├── EmailManager.php
│   ├── EnvConfig.php
│   ├── Middleware.php
│   ├── Security.php
│   ├── SecurityConstants.php
│   ├── TenantScope.php
│   ├── TwoFactorAuth.php
│   ├── VersionChecker.php
│   ├── back-to-top.php
│   ├── config.php
│   ├── db.php
│   ├── dev_logger.php
│   ├── footer.php
│   ├── functions.php
│   ├── header.php
│   ├── mailer.php
│   └── test-footer.php
├── index.php
├── install
│   ├── assets
│   │   ├── css
│   │   │   └── install.css
│   │   ├── images
│   │   │   └── README.md
│   │   └── js
│   │       └── install.js
│   ├── cleanup.php
│   ├── includes
│   │   ├── Installer.php
│   │   └── Requirements.php
│   └── index.php
├── install_todo.md
├── modules
│   ├── civil
│   │   ├── brickwork
│   │   │   ├── brick-quantity.php
│   │   │   ├── mortar-ratio.php
│   │   │   └── plastering-estimator.php
│   │   ├── concrete
│   │   │   ├── concrete-mix.php
│   │   │   ├── concrete-strength.php
│   │   │   ├── concrete-volume.php
│   │   │   └── rebar-calculation.php
│   │   ├── earthwork
│   │   │   ├── cut-and-fill-volume.php
│   │   │   ├── excavation-volume.php
│   │   │   └── slope-calculation.php
│   │   ├── resources
│   │   │   └── css
│   │   └── structural
│   │       ├── beam-load-capacity.php
│   │       ├── column-design.php
│   │       ├── foundation-design.php
│   │       └── slab-design.php
│   ├── electrical
│   │   ├── conduit-sizing
│   │   │   ├── cable-tray-sizing.php
│   │   │   ├── conduit-fill-calculation.php
│   │   │   ├── entrance-service-sizing.php
│   │   │   └── junction-box-sizing.php
│   │   ├── load-calculation
│   │   │   ├── arc-flash-boundary.php
│   │   │   ├── battery-load-bank-sizing.php
│   │   │   ├── demand-load-calculation.php
│   │   │   ├── feeder-sizing.php
│   │   │   ├── general-lighting-load.php
│   │   │   ├── motor-full-load-amps.php
│   │   │   ├── ocpd-sizing.php
│   │   │   ├── panel-schedule.php
│   │   │   └── receptacle-load.php
│   │   ├── short-circuit
│   │   │   ├── available-fault-current.php
│   │   │   ├── ground-conductor-sizing.php
│   │   │   └── power-factor-correction.php
│   │   ├── voltage-drop
│   │   │   ├── single-phase-voltage-drop.php
│   │   │   ├── three-phase-voltage-drop.php
│   │   │   ├── voltage-drop-sizing.php
│   │   │   └── voltage-regulation.php
│   │   ├── w
│   │   └── wire-sizing
│   │       ├── motor-circuit-wire-sizing.php
│   │       ├── motor-circuit-wiring.php
│   │       ├── transformer-kva-sizing.php
│   │       ├── wire-ampacity.php
│   │       └── wire-size-by-current.php
│   ├── estimation
│   │   ├── cost-estimation
│   │   │   ├── boq-preparation.php
│   │   │   ├── contingency-overheads.php
│   │   │   ├── cost-escalation.php
│   │   │   ├── item-rate-analysis.php
│   │   │   └── project-cost-summary.php
│   │   ├── equipment-estimation
│   │   │   ├── equipment-allocation.php
│   │   │   ├── equipment-hourly-rate.php
│   │   │   ├── fuel-consumption.php
│   │   │   └── machinery-usage.php
│   │   ├── labor-estimation
│   │   │   ├── labor-cost-estimator.php
│   │   │   ├── labor-hour-calculation.php
│   │   │   ├── labor-rate-analysis.php
│   │   │   └── manpower-requirement.php
│   │   ├── material-estimation
│   │   │   ├── concrete-materials.php
│   │   │   ├── masonry-materials.php
│   │   │   ├── paint-materials.php
│   │   │   ├── plaster-materials.php
│   │   │   └── tile-materials.php
│   │   ├── project-financials
│   │   │   ├── break-even-analysis.php
│   │   │   ├── cash-flow-analysis.php
│   │   │   ├── npv-irr-analysis.php
│   │   │   ├── payback-period.php
│   │   │   └── profit-loss-analysis.php
│   │   ├── quantity-takeoff
│   │   │   ├── brickwork-quantity.php
│   │   │   ├── concrete-quantity.php
│   │   │   ├── flooring-quantity.php
│   │   │   ├── formwork-quantity.php
│   │   │   ├── paint-quantity.php
│   │   │   ├── plaster-quantity.php
│   │   │   └── rebar-quantity.php
│   │   ├── reports
│   │   │   ├── detailed-boq-report.php
│   │   │   ├── equipment-cost-report.php
│   │   │   ├── financial-dashboard.php
│   │   │   ├── labor-cost-report.php
│   │   │   ├── material-cost-report.php
│   │   │   └── summary-report.php
│   │   └── tender-bidding
│   │       ├── bid-price-comparison.php
│   │       ├── bid-sheet-generator.php
│   │       ├── pre-bid-analysis.php
│   │       └── rate-deviation.php
│   ├── fire
│   │   ├── fire-pumps
│   │   │   ├── driver-power.php
│   │   │   ├── jockey-pump.php
│   │   │   └── pump-sizing.php
│   │   ├── hazard-classification
│   │   │   ├── commodity-classification.php
│   │   │   ├── design-density.php
│   │   │   └── occupancy-assessment.php
│   │   ├── hydraulics
│   │   │   └── hazen-williams.php
│   │   ├── index.php
│   │   ├── sprinklers
│   │   │   ├── discharge-calculations.php
│   │   │   ├── pipe-sizing.php
│   │   │   └── sprinkler-layout.php
│   │   └── standpipes
│   │       ├── hose-demand.php
│   │       ├── pressure-calculations.php
│   │       └── standpipe-classification.php
│   ├── hvac
│   │   ├── duct-sizing
│   │   │   ├── equivalent-duct.php
│   │   │   ├── fitting-loss.php
│   │   │   ├── grille-sizing.php
│   │   │   ├── pressure-drop.php
│   │   │   └── velocity-sizing.php
│   │   ├── energy-analysis
│   │   │   ├── co2-emissions.php
│   │   │   ├── energy-consumption.php
│   │   │   ├── insulation-savings.php
│   │   │   └── payback-period.php
│   │   ├── equipment
│   │   ├── equipment-sizing
│   │   │   ├── ac-sizing.php
│   │   │   ├── chiller-sizing.php
│   │   │   ├── furnace-sizing.php
│   │   │   └── pump-sizing.php
│   │   ├── load-calculation
│   │   │   ├── cooling-load.php
│   │   │   ├── heating-load.php
│   │   │   ├── infiltration.php
│   │   │   └── ventilation.php
│   │   └── psychrometrics
│   │       ├── air-properties.php
│   │       ├── cooling-load-psych.php
│   │       ├── enthalpy.php
│   │       └── sensible-heat-ratio.php
│   ├── m
│   ├── management
│   ├── mep
│   │   ├── bootstrap.php
│   │   ├── coordination
│   │   │   ├── bim-export.php
│   │   │   ├── clash-detection.php
│   │   │   ├── coordination-map.php
│   │   │   ├── space-allocation.php
│   │   │   └── system-priority.php
│   │   ├── cost-management
│   │   │   ├── boq-generator.php
│   │   │   ├── cost-optimization.php
│   │   │   ├── cost-summary.php
│   │   │   ├── material-takeoff.php
│   │   │   └── vendor-pricing.php
│   │   ├── data-utilities
│   │   │   ├── api-endpoints.php
│   │   │   ├── input-validator.php
│   │   │   ├── material-database.php
│   │   │   ├── mep-config.php
│   │   │   ├── permissions.php
│   │   │   └── unit-converter.php
│   │   ├── electrical
│   │   │   ├── conduit-sizing.php
│   │   │   ├── earthing-system.php
│   │   │   ├── emergency-power.php
│   │   │   ├── lighting-layout.php
│   │   │   ├── mep-electrical-summary.php
│   │   │   ├── panel
│   │   │   ├── panel-schedule.php
│   │   │   ├── power-dist
│   │   │   └── transformer-sizing.php
│   │   ├── energy-efficiency
│   │   │   ├── energy-consumption.php
│   │   │   ├── green-rating.php
│   │   │   ├── hvac-efficiency.php
│   │   │   ├── solar-system.php
│   │   │   └── water-efficiency.php
│   │   ├── fire-protection
│   │   │   ├── fire-hydrant-system.php
│   │   │   ├── fire-pump-sizing.php
│   │   │   ├── fire-safety-zoning.php
│   │   │   ├── fire-tank-sizing.php
│   │   │   └── sprinkler
│   │   ├── in
│   │   ├── integration
│   │   │   ├── autocad-layer-mapper.php
│   │   │   ├── bim-integration.php
│   │   │   ├── cloud-sync.php
│   │   │   ├── project-sharing.php
│   │   │   └── revit-plugin.php
│   │   ├── mechanical
│   │   │   ├── chilled-water-piping.php
│   │   │   ├── equipment-database.php
│   │   │   ├── hvac-duct-sizing.php
│   │   │   └── hvac-load-est
│   │   ├── plumbing
│   │   │   ├── drainage-system.php
│   │   │   ├── plumbing-fixture-count.php
│   │   │   ├── pump-selection.php
│   │   │   ├── storm-water.php
│   │   │   ├── water-supply.php
│   │   │   └── water-tank-sizing.php
│   │   └── reports-documentation
│   │       ├── clash-detection-report.php
│   │       ├── equipment-schedule.php
│   │       ├── load-summary.php
│   │       ├── mep-summary.php
│   │       └── pdf-export.php
│   ├── plumbing
│   │   ├── drainage
│   │   │   ├── drainage-pipe-sizing.php
│   │   │   ├── grease-trap-sizing.php
│   │   │   ├── soil-stack-sizing.php
│   │   │   ├── storm-drainage.php
│   │   │   ├── trap-sizing.php
│   │   │   └── vent-pipe-sizing.php
│   │   ├── fixtures
│   │   │   ├── fixture-unit-calculation.php
│   │   │   ├── shower-sizing.php
│   │   │   ├── sink-sizing.php
│   │   │   └── toilet-flow.php
│   │   ├── hot_water
│   │   │   ├── heat-loss-calculation.php
│   │   │   ├── recirculation-loop.php
│   │   │   ├── safety-valve.php
│   │   │   ├── storage-tank-sizing.php
│   │   │   └── water-heater-sizing.php
│   │   ├── pipe_sizing
│   │   │   ├── expansion-loop-sizing.php
│   │   │   ├── gas-pipe-sizing.php
│   │   │   ├── pipe-flow-capacity.php
│   │   │   └── water-pipe-sizing.php
│   │   ├── stormwater
│   │   │   ├── downpipe-sizing.php
│   │   │   ├── gutter-sizing.php
│   │   │   ├── pervious-area.php
│   │   │   └── stormwater-storage.php
│   │   └── water_supply
│   │       ├── cold-water-demand.php
│   │       ├── hot-water-demand.php
│   │       ├── main-isolation-valve.php
│   │       ├── pressure-loss.php
│   │       ├── pump-sizing.php
│   │       ├── storage-tank-sizing.php
│   │       ├── water-demand-calculation.php
│   │       └── water-hammer-calculation.php
│   ├── project-management
│   │   ├── analytics
│   │   │   ├── cost-analysis.php
│   │   │   ├── custom-reports.php
│   │   │   ├── performance-dashboard.php
│   │   │   ├── predictive-analytics.php
│   │   │   ├── resource-utilization.php
│   │   │   └── trend-analysis.php
│   │   ├── communication
│   │   │   ├── discussion-board.php
│   │   │   ├── document-sharing.php
│   │   │   ├── email-integration.php
│   │   │   ├── meeting-minutes.php
│   │   │   ├── notification-system.php
│   │   │   └── team-chat.php
│   │   ├── dashboard
│   │   │   ├── gantt-chart.php
│   │   │   ├── milestone-tracker.php
│   │   │   ├── project-health.php
│   │   │   ├── project-overview.php
│   │   │   ├── task-summary.php
│   │   │   └── weather-integration.php
│   │   ├── documents
│   │   │   ├── approval-workflow.php
│   │   │   ├── archive-system.php
│   │   │   ├── document-repository.php
│   │   │   ├── drawing-register.php
│   │   │   ├── submittal-tracking.php
│   │   │   └── version-control.php
│   │   ├── financial
│   │   │   ├── budget-tracking.php
│   │   │   ├── cost-control.php
│   │   │   ├── financial-reports.php
│   │   │   ├── forecast-analysis.php
│   │   │   ├── invoice-management.php
│   │   │   └── payment-tracking.php
│   │   ├── integration
│   │   │   ├── accounting-sync.php
│   │   │   ├── api-endpoints.php
│   │   │   ├── bim-integration.php
│   │   │   ├── calendar-sync.php
│   │   │   ├── data-import-export.php
│   │   │   └── email-integration.php
│   │   ├── procurement
│   │   │   ├── delivery-tracking.php
│   │   │   ├── inventory-tracking.php
│   │   │   ├── material-requests.php
│   │   │   ├── purchase-orders.php
│   │   │   ├── stock-control.php
│   │   │   └── vendor-management.php
│   │   ├── quality
│   │   │   ├── audit-reports.php
│   │   │   ├── compliance-tracking.php
│   │   │   ├── inspection-reports.php
│   │   │   ├── quality-checklist.php
│   │   │   ├── risk-assessment.php
│   │   │   └── safety-incidents.php
│   │   ├── reports
│   │   │   ├── custom-reports.php
│   │   │   ├── daily-reports.php
│   │   │   ├── delay-analysis.php
│   │   │   ├── performance-metrics.php
│   │   │   ├── progress-photos.php
│   │   │   └── status-updates.php
│   │   ├── resources
│   │   │   ├── availability-tracker.php
│   │   │   ├── daily-report.php
│   │   │   ├── equipment-allocation.php
│   │   │   ├── manpower-planning.php
│   │   │   ├── material-tracking.php
│   │   │   ├── resource-calendar.php
│   │   │   └── skill-matrix.php
│   │   ├── scheduling
│   │   │   ├── assign-task.php
│   │   │   ├── calendar-view.php
│   │   │   ├── create-task.php
│   │   │   ├── recurring-tasks.php
│   │   │   ├── schedule-optimizer.php
│   │   │   └── task-dependency.php
│   │   ├── settings
│   │   │   ├── project-settings.php
│   │   │   ├── role-permissions.php
│   │   │   ├── system-backup.php
│   │   │   ├── template-management.php
│   │   │   ├── user-management.php
│   │   │   └── workflow-config.php
│   │   └── template-coming-soon.php
│   ├── site
│   │   ├── concrete-tools
│   │   │   ├── placement-rate.php
│   │   │   ├── temperature-control.php
│   │   │   ├── testing-requirements.php
│   │   │   └── yardage-adjustments.php
│   │   ├── earthwork
│   │   │   ├── cut-fill-balancing.php
│   │   │   ├── equipment-production.php
│   │   │   ├── slope-paving.php
│   │   │   ├── swelling-shrink.php
│   │   │   └── swelling-shrinkage.php
│   │   ├── productivity
│   │   │   ├── cost-productivity.php
│   │   │   ├── equipment-utilization.php
│   │   │   ├── labor-productivity.php
│   │   │   └── schedule-compression.php
│   │   ├── safety
│   │   │   ├── crane-setup.php
│   │   │   ├── evacuation-planning.php
│   │   │   ├── fall-protection.php
│   │   │   └── trench-safety.php
│   │   └── surveying
│   │       ├── batter-boards.php
│   │       ├── grade-rod.php
│   │       ├── horizontal-curve-staking.php
│   │       └── slope-staking.php
│   └── structural
│       ├── beam-analysis
│       │   ├── beam-design.php
│       │   ├── beam-load-combination.php
│       │   ├── cantilever-beam.php
│       │   ├── continuous-beam.php
│       │   └── simply-supported-beam.php
│       ├── column-design
│       │   ├── biaxial-column.php
│       │   ├── column-footing-link.php
│       │   ├── long-column.php
│       │   ├── short-column.php
│       │   └── steel-column-design.php
│       ├── foundation-design
│       │   ├── combined-footing.php
│       │   ├── foundation-pressure.php
│       │   ├── isolated-footing.php
│       │   ├── pile-foundation.php
│       │   └── raft-foundation.php
│       ├── load-analysis
│       │   ├── dead-load.php
│       │   ├── live-load.php
│       │   ├── load-combination.php
│       │   ├── seismic-load.php
│       │   └── wind-load.php
│       ├── reinforcement
│       │   ├── bar-bending-schedule.php
│       │   ├── detailing-drawing.php
│       │   ├── rebar-anchorage.php
│       │   ├── reinforcement-optimizer.php
│       │   └── stirrup-design.php
│       ├── reports
│       │   ├── beam-report.php
│       │   ├── column-report.php
│       │   ├── foundation-report.php
│       │   ├── full-structure-summary.php
│       │   └── load-analysis-summary.php
│       ├── slab-design
│       │   ├── flat-slab.php
│       │   ├── one-way-slab.php
│       │   ├── slab-load-calculation.php
│       │   ├── two-way-slab.php
│       │   └── waffle-slab.php
│       └── steel-structure
│           ├── connection-design.php
│           ├── purlin-design.php
│           ├── steel-base-plate.php
│           ├── steel-beam-design.php
│           └── steel-truss-analysis.php
├── mvc_structure.md
├── plugins
│   ├── calculator-plugins
│   │   ├── advanced-steel
│   │   └── green-building-tools
│   │       └── plugin.json
│   └── theme-plugins
├── public
│   ├── .htaccess
│   ├── assets
│   │   ├── css
│   │   │   ├── admin
│   │   │   │   └── email-manager.css
│   │   │   ├── history.css
│   │   │   ├── main.css
│   │   │   └── share.css
│   │   ├── images
│   │   ├── js
│   │   │   ├── exports.js
│   │   │   ├── history.js
│   │   │   ├── profile.js
│   │   │   └── share.js
│   │   └── uploads
│   └── index.php
├── saas_idea.md
├── storage
│   ├── app
│   │   ├── GeoLite2-City.mmdb
│   │   ├── api_cert_chain.crt
│   │   ├── bookmarklet.uncompressed.js
│   │   ├── bookmarklet.uncompressed.min.js
│   │   ├── jShortener.js
│   │   ├── tmp
│   │   │   └── index.html
│   │   └── wpplugin.php
│   ├── backups
│   ├── cache
│   ├── logs
│   └── sessions
├── structurereport.md
├── tests
│   ├── Feature
│   ├── Unit
│   ├── basic_test.php
│   ├── clean_project_structure.php
│   ├── comprehensive_functional_test.php
│   ├── run_tests.php
│   └── saas_system_test.php
├── themes
│   ├── default
│   │   ├── assets
│   │   │   ├── css
│   │   │   │   ├── back-to-top.css
│   │   │   │   ├── civil.css
│   │   │   │   ├── electrical.css
│   │   │   │   ├── estimation.css
│   │   │   │   ├── fire.css
│   │   │   │   ├── footer.css
│   │   │   │   ├── header.css
│   │   │   │   ├── home.css
│   │   │   │   ├── hvac.css
│   │   │   │   ├── management.css
│   │   │   │   ├── mep.css
│   │   │   │   ├── plumbing.css
│   │   │   │   ├── responsive.css
│   │   │   │   ├── site.css
│   │   │   │   ├── structural.css
│   │   │   │   └── theme.css
│   │   │   ├── images
│   │   │   │   ├── applogo.png
│   │   │   │   ├── banner.jpg
│   │   │   │   ├── favicon.png
│   │   │   │   └── profile.png
│   │   │   ├── js
│   │   │   │   ├── auth.js
│   │   │   │   ├── back-to-top.js
│   │   │   │   ├── header.js
│   │   │   │   ├── home.js
│   │   │   │   ├── main.js
│   │   │   │   └── theme.js
│   │   │   └── uploads
│   │   ├── helpers.php
│   │   ├── theme.json
│   │   └── views
│   │       ├── 403.php
│   │       ├── 404.php
│   │       ├── 500.php
│   │       ├── civil
│   │       │   └── index.php
│   │       ├── electrical
│   │       │   └── index.php
│   │       ├── index.php
│   │       ├── layouts
│   │       │   ├── admin.php
│   │       │   ├── auth.php
│   │       │   ├── login.php
│   │       │   ├── logout.php
│   │       │   ├── main.php
│   │       │   ├── register.php
│   │       │   ├── reset.php
│   │       │   └── verify.php
│   │       ├── pages
│   │       │   └── auth
│   │       │       ├── forgot-password.php
│   │       │       ├── login.php
│   │       │       └── register.php
│   │       └── partials
│   │           ├── civil.php
│   │           ├── electrical.php
│   │           ├── estimation.php
│   │           ├── fire.php
│   │           ├── footer.php
│   │           ├── header.php
│   │           ├── hvac.php
│   │           ├── index.php
│   │           ├── management.php
│   │           ├── mep.php
│   │           ├── plumbing.php
│   │           ├── site.php
│   │           └── structural.php
│   └── professional
│       ├── assets
│       │   ├── css
│       │   ├── images
│       │   └── js
│       ├── theme.json
│       └── views
│           └── layouts
│               └── main.php
├── vendor
└── version.json
```

## Vendor Dependencies

```json
vendor{
  "require": {
    "phpmailer/phpmailer": "^7.0",
    "paragonie/random_compat": "^9.99",
    "abraham/twitteroauth": "^3.1",
    "altcha-org/altcha": "^1.1",
    "defuse/php-encryption": "^2.2",
    "endroid/qr-code": "4.6.1",
    "jaybizzle/crawler-detect": "^1.2",
    "maxmind-db/reader": "^1.10",
    "mollie/mollie-api-php": "^2.71",
    "monolog/monolog": "^2.0",
    "nikic/fast-route": "^1.3",
    "paypal/paypal-checkout-sdk": "^1.0",
    "phpfastcache/phpfastcache": "^8.0",
    "setasign/fpdf": "^1.8",
    "pragmarx/google2fa": "^8.0",
    "stripe/stripe-php": "^15.10",
    "symfony/validator": "^5.4",
    "ramsey/uuid": "^4.7",
    "league/csv": "^9.0",
    "nesbot/carbon": "^2.0",
    "vlucas/phpdotenv": "^5.5",
    "guzzlehttp/guzzle": "^7.0",
    "symfony/cache": "^5.4",
    "respect/validation": "^2.2",
    "intervention/image": "^2.7",
    "phpoffice/phpspreadsheet": "^1.29",
    "tecnickcom/tcpdf": "^6.6",
    "mpdf/mpdf": "^8.1",
    "markrogoyski/math-php": "^1.0"
  }
}
```

## Statistics

- **Total Files:** 564
- **Total Directories:** 171
- **Total Items:** 735
- **Excludes:** vendor/ folder

---
*This report excludes the vendor folder to provide a clean view of the main project structure.*
