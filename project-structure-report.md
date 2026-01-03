# Project Structure Report

Generated: 2026-01-03 02:47:13

```
=== PROJECT STATISTICS ===
📁 Directories: 1,534
📄 Files: 20,481
💾 Total Size: 1.38 GB
🐘 PHP Files: 7332 (1,053,536 lines)
📋 JSON Files: 162
🎨 CSS Files: 37
⚡ JS Files: 479
📝 MD Files: 238
🗄️ SQL Files: 27

=== STATISTICS (EXCLUDING VENDOR/NODE_MODULES/STORAGE CACHE) ===
📁 Directories: 1,470
📄 Files: 15,195
💾 Total Size: 1.37 GB
🐘 PHP Files: 7332 (1,053,536 lines)
📋 JSON Files: 156
🎨 CSS Files: 30
⚡ JS Files: 33
📝 MD Files: 228
🗄️ SQL Files: 27

--- FILE TREE ---
Bishwo_Calculator/
├── .env
├── .env.production
├── .htaccess
├── Admin Media Management Workflow - Upload, Storage, and Modal Interface.md
├── Admin Panel Content Management System - Pages, Menus & Media.md
├── Admin Panel Media Management System.md
├── AGENTS.md
├── api/
│   ├── admin/
│   │   ├── dashboard-stats.php
│   │   ├── dashboard.php
│   │   └── settings.php
│   ├── calculate.php
│   ├── calculations.php
│   ├── calculator/
│   │   └── index.php
│   ├── calculators.php
│   ├── check-username.php
│   ├── forgot-password.php
│   ├── health-check.php
│   ├── library/
│   ├── login.php
│   ├── logout.php
│   ├── profile.php
│   ├── register.php
│   └── search.php
├── API Test Suite Configuration and Backend Endpoints.md
├── app/
│   ├── bootstrap.php
│   ├── Calculators/
│   │   ├── BaseCalculator.php
│   │   ├── CalculatorFactory.php
│   │   ├── CivilCalculator.php
│   │   ├── ElectricalCalculator.php
│   │   ├── HvacCalculator.php
│   │   ├── PlumbingCalculator.php
│   │   └── TraditionalUnitsCalculator.php
│   ├── Config/
│   │   ├── Calculators/
│   │   │   ├── civil.php
│   │   │   ├── electrical.php
│   │   │   ├── estimation.php
│   │   │   ├── fire.php
│   │   │   ├── hvac.php
│   │   │   ├── management.php
│   │   │   ├── mep.php
│   │   │   ├── plumbing.php
│   │   │   ├── site.php
│   │   │   └── structural.php
│   │   ├── ComplianceConfig.php
│   │   ├── config.php
│   │   ├── db.php
│   │   ├── images.php
│   │   ├── norms.php
│   │   ├── PayPal.php
│   │   └── Stripe.php
│   ├── Console/
│   │   └── SetupPayPal.php
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── ActivityController.php
│   │   │   ├── AdvertisementController.php
│   │   │   ├── AnalyticsController.php
│   │   │   ├── AuditController.php
│   │   │   ├── AuditLogController.php
│   │   │   ├── BackupController.php
│   │   │   ├── BlogController.php
│   │   │   ├── BountyController.php
│   │   │   ├── CalculationsController.php
│   │   │   ├── CalculatorController.php
│   │   │   ├── CalculatorManagementController.php
│   │   │   ├── ContentController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DebugController.php
│   │   │   ├── EmailManagerController.php
│   │   │   ├── ErrorLogController.php
│   │   │   ├── HelpController.php
│   │   │   ├── ImageController.php
│   │   │   ├── IPRestrictionsController.php
│   │   │   ├── LibraryController.php
│   │   │   ├── LogoController.php
│   │   │   ├── LogsController.php
│   │   │   ├── MarketplaceController.php
│   │   │   ├── MediaApiController.php
│   │   │   ├── ModuleController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── NotificationManagementController.php
│   │   │   ├── PluginController.php
│   │   │   ├── Quiz/
│   │   │   │   ├── ExamController.php
│   │   │   │   ├── LeaderboardController.php
│   │   │   │   ├── QuestionBankController.php
│   │   │   │   ├── QuestionImportController.php
│   │   │   │   ├── QuizDashboardController.php
│   │   │   │   ├── ResultsController.php
│   │   │   │   └── SyllabusController.php
│   │   │   ├── SearchController.php
│   │   │   ├── SecurityAlertsController.php
│   │   │   ├── SettingsController.php
│   │   │   ├── SetupController.php
│   │   │   ├── SponsorController.php
│   │   │   ├── SubscriptionController.php
│   │   │   ├── SystemStatusController.php
│   │   │   ├── ThemeController.php
│   │   │   ├── ThemeCustomizeController.php
│   │   │   ├── UserController.php
│   │   │   ├── UserManagementController.php
│   │   │   └── UserManagementController_export.php
│   │   ├── Api/
│   │   │   ├── AdminController.php
│   │   │   ├── AuthController.php
│   │   │   ├── AuthController_backup.php
│   │   │   ├── BountyApiController.php
│   │   │   ├── Civil/
│   │   │   │   └── StatusController.php
│   │   │   ├── Electrical/
│   │   │   │   └── StatusController.php
│   │   │   ├── HumanApiController.php
│   │   │   ├── Hvac/
│   │   │   │   └── StatusController.php
│   │   │   ├── LibraryApiController.php
│   │   │   ├── LocationController.php
│   │   │   ├── MarketingController.php
│   │   │   ├── ProfileController.php
│   │   │   └── V1/
│   │   │       └── HealthController.php
│   │   ├── ApiController.php
│   │   ├── AuthController.php
│   │   ├── BlogController.php
│   │   ├── BountyController.php
│   │   ├── CalculatorController.php
│   │   ├── CareerController.php
│   │   ├── ChemistryCalculatorController.php
│   │   ├── CommentController.php
│   │   ├── ContactController.php
│   │   ├── DataExportController.php
│   │   ├── DateTimeCalculatorController.php
│   │   ├── DeveloperController.php
│   │   ├── EstimationController.php
│   │   ├── ExportController.php
│   │   ├── FavoritesController.php
│   │   ├── FinanceCalculatorController.php
│   │   ├── ForumController.php
│   │   ├── HealthCalculatorController.php
│   │   ├── HelpController.php
│   │   ├── HistoryController.php
│   │   ├── HomeController.php
│   │   ├── HoneypotController.php
│   │   ├── InterestController.php
│   │   ├── LandingController.php
│   │   ├── LegalController.php
│   │   ├── LibraryController.php
│   │   ├── MathCalculatorController.php
│   │   ├── NotificationController.php
│   │   ├── NotificationPreferencesController.php
│   │   ├── PageController.php
│   │   ├── Payment/
│   │   │   └── StripeController.php
│   │   ├── PaymentController.php
│   │   ├── PhysicsCalculatorController.php
│   │   ├── ProfileController.php
│   │   ├── ProfileImageController.php
│   │   ├── ProjectController.php
│   │   ├── QuestController.php
│   │   ├── Quiz/
│   │   │   ├── ExamEngineController.php
│   │   │   ├── FirmController.php
│   │   │   ├── GamificationController.php
│   │   │   ├── LeaderboardController.php
│   │   │   ├── LifelineController.php
│   │   │   ├── MultiplayerController.php
│   │   │   └── PortalController.php
│   │   ├── RateAnalysisController.php
│   │   ├── ReportController.php
│   │   ├── ShareController.php
│   │   ├── ShopController.php
│   │   ├── StatisticsCalculatorController.php
│   │   ├── SubscriptionController.php
│   │   ├── TwoFactorController.php
│   │   ├── UserController.php
│   │   ├── ViewerController.php
│   │   └── WebhookController.php
│   ├── Core/
│   │   ├── AdminModule.php
│   │   ├── AdminModuleManager.php
│   │   ├── Auth.php
│   │   ├── CacheManager.php
│   │   ├── Container.php
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── DatabaseLegacy.php
│   │   ├── EnhancedDatabase.php
│   │   ├── Exceptions/
│   │   │   └── ValidationException.php
│   │   ├── MathEngine.php
│   │   ├── Model.php
│   │   ├── ModelLogger.php
│   │   ├── OptimizedController.php
│   │   ├── PDOCompat.php
│   │   ├── Router.php
│   │   ├── SafeModel.php
│   │   ├── Session.php
│   │   ├── Validator.php
│   │   └── View.php
│   ├── db/
│   │   ├── migrations/
│   │   │   ├── calculator_management_system.sql
│   │   │   ├── create_tables.php
│   │   │   ├── migrate_civil_calculators.php
│   │   │   ├── migrate_electrical.php
│   │   │   ├── migrate_plumbing.php
│   │   │   ├── run_migrations.php
│   │   │   └── simple_migrate.php
│   │   └── site_meta.json
│   ├── debug_settings.php
│   ├── Engine/
│   │   ├── CalculatorEngine.php
│   │   ├── FormulaRegistry.php
│   │   ├── ResultFormatter.php
│   │   ├── UnitConverter.php
│   │   └── ValidationEngine.php
│   ├── Helpers/
│   │   ├── AdHelper.php
│   │   ├── functions.php
│   │   ├── ImageHelper.php
│   │   ├── NepaliCalendar.php
│   │   ├── SchemaHelper.php
│   │   ├── TimeHelper.php
│   │   └── UrlHelper.php
│   ├── Libraries/
│   │   └── PayTMLibrary.php
│   ├── logs/
│   │   └── media_info.log
│   ├── Middleware/
│   │   ├── AdminMiddleware.php
│   │   ├── AnalyticsTracker.php
│   │   ├── AuthMiddleware.php
│   │   ├── CorsMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   ├── GuestMiddleware.php
│   │   ├── MaintenanceMiddleware.php
│   │   ├── RateLimitMiddleware.php
│   │   └── SecurityMiddleware.php
│   ├── Models/
│   │   ├── ActivityLog.php
│   │   ├── Advertisement.php
│   │   ├── Analytics.php
│   │   ├── AuditLog.php
│   │   ├── BountyRequest.php
│   │   ├── BountySubmission.php
│   │   ├── Calculation.php
│   │   ├── CalculationHistory.php
│   │   ├── Campaign.php
│   │   ├── Comment.php
│   │   ├── EmailResponse.php
│   │   ├── EmailTemplate.php
│   │   ├── EmailThread.php
│   │   ├── EnhancedUser.php
│   │   ├── ExportTemplate.php
│   │   ├── Image.php
│   │   ├── LibraryFile.php
│   │   ├── Media.php
│   │   ├── Menu.php
│   │   ├── Module.php
│   │   ├── Notification.php
│   │   ├── NotificationPreference.php
│   │   ├── Page.php
│   │   ├── Payment.php
│   │   ├── Plugin.php
│   │   ├── Post.php
│   │   ├── Project.php
│   │   ├── Role.php
│   │   ├── Search.php
│   │   ├── Settings.php
│   │   ├── Share.php
│   │   ├── Sponsor.php
│   │   ├── Subscription.php
│   │   ├── Theme.php
│   │   ├── Transaction.php
│   │   ├── User.php
│   │   ├── UserSubscription.php
│   │   └── Vote.php
│   ├── Modules/
│   │   ├── Admin/
│   │   │   ├── DashboardModule.php
│   │   │   ├── SystemSettingsModule.php
│   │   │   └── UserManagementModule.php
│   │   ├── Analytics/
│   │   │   └── AnalyticsModule.php
│   │   ├── BaseProvider.php
│   │   ├── Civil/
│   │   │   └── CivilServiceProvider.php
│   │   ├── Electrical/
│   │   │   └── ElectricalServiceProvider.php
│   │   ├── Hvac/
│   │   │   └── HvacServiceProvider.php
│   │   └── ModuleManager.php
│   ├── Router/
│   │   └── CalculatorRouter.php
│   ├── routes.php
│   ├── routes.php.backup
│   ├── routes_viewer_snippet.php
│   ├── Services/
│   │   ├── ActivityLogger.php
│   │   ├── AdvancedCache.php
│   │   ├── AnalyticsService.php
│   │   ├── AuditLogger.php
│   │   ├── BackupService.php
│   │   ├── BattlePassService.php
│   │   ├── BotEngine.php
│   │   ├── Cache.php
│   │   ├── CalculationService.php
│   │   ├── CalculatorManagement.php
│   │   ├── CalculatorService.php
│   │   ├── ContentService.php
│   │   ├── DataExportService.php
│   │   ├── EconomicSecurityService.php
│   │   ├── EmailManager.php
│   │   ├── EmailService.php
│   │   ├── ExportService.php
│   │   ├── FileService.php
│   │   ├── FileUploadService.php
│   │   ├── FirmService.php
│   │   ├── GamificationService.php
│   │   ├── Gateways/
│   │   │   ├── BankTransferService.php
│   │   │   ├── MollieService.php
│   │   │   ├── PaddleService.php
│   │   │   ├── PayPalService.php
│   │   │   ├── PayStackService.php
│   │   │   └── StripeService.php
│   │   ├── GDPRService.php
│   │   ├── GeolocationService.php
│   │   ├── GoogleAuthService.php
│   │   ├── ImageManager.php
│   │   ├── ImageOptimizer.php
│   │   ├── ImageRetrievalService.php
│   │   ├── ImageUploadService.php
│   │   ├── InstallerService.php
│   │   ├── IPRestrictionService.php
│   │   ├── LeaderboardService.php
│   │   ├── LifelineService.php
│   │   ├── LobbyService.php
│   │   ├── Logger.php
│   │   ├── MenuService.php
│   │   ├── MissionService.php
│   │   ├── ModuleService.php
│   │   ├── NonceService.php
│   │   ├── NotificationService.php
│   │   ├── PaymentService.php
│   │   ├── PayPalService.php
│   │   ├── PerformanceMonitor.php
│   │   ├── PermalinkService.php
│   │   ├── PluginManager.php
│   │   ├── PremiumThemeManager.php
│   │   ├── QueryOptimizer.php
│   │   ├── QuestService.php
│   │   ├── RankService.php
│   │   ├── RateLimiter.php
│   │   ├── RecaptchaService.php
│   │   ├── SearchIndexer.php
│   │   ├── Security.php
│   │   ├── SecurityAlertService.php
│   │   ├── SecurityMonitor.php
│   │   ├── SecurityNotificationService.php
│   │   ├── SecurityValidator.php
│   │   ├── SettingsService.php
│   │   ├── ShortcodeService.php
│   │   ├── StripeService.php
│   │   ├── SuspiciousActivityDetector.php
│   │   ├── SystemMonitoringService.php
│   │   ├── ThemeBuilder.php
│   │   ├── ThemeImageLoader.php
│   │   ├── ThemeManager.php
│   │   ├── ThemeService.php
│   │   ├── TranslationService.php
│   │   ├── TwoFactorAuthService.php
│   │   ├── VersionChecker.php
│   │   ├── WatermarkService.php
│   │   └── WidgetManager.php
│   ├── storage/
│   │   └── cache/
│   │       ├── 0ab3a6faf42848e1003ae626ba15ce9d.cache
│   │       └── f035a3664f1e240764433a30877cb794.cache
│   └── Views/
│       ├── admin/
│       │   └── widgets/
│       │       ├── create.php
│       │       ├── index.php
│       │       └── settings.php
│       ├── layouts/
│       │   ├── admin.php
│       │   ├── auth.php
│       │   └── main.php
│       └── partials/
│           └── navigation.php
├── battle-pass-gamification.md
├── Blueprint Vault File Management - Dual-File Upload, Watermarking & Preview Generation.md
├── Blueprint Vault Viewer Strategy and Final Tables.md
├── Bounty System and Library Resource Management - Dual Marketplace Architecture.md
├── Civil City Growth Strategy and Monetization Roadmap.md
├── composer.json
├── composer.lock
├── config/
│   ├── app.php
│   ├── database.php
│   ├── installed.lock
│   ├── installer.php
│   ├── mail.php
│   ├── paypal.env.example
│   └── services.php
├── cron/
│   ├── cleanup.php
│   ├── daily_reset.php
│   ├── migrate_leaderboard.php
│   ├── migrate_schema.php
│   ├── reset_season.php
│   └── update_leaderboard.php
├── database/
│   ├── add_email_template_columns.php
│   ├── add_enterprise_templates.php
│   ├── analytics_schema.sql
│   ├── check_category.php
│   ├── check_schema.php
│   ├── check_threads.php
│   ├── check_users.php
│   ├── enhanced_permalink_system.sql
│   ├── fix_sessions_table.php
│   ├── image_optimization.sql
│   ├── migrate.php
│   ├── migrate_content_tables.php
│   ├── migrations/
│   │   ├── 001_create_users_table.php
│   │   ├── 001_plugin_theme_system.php
│   │   ├── 002_create_subscriptions_table.php
│   │   ├── 002_theme_editor_tables.php
│   │   ├── 003_create_subscriptions_table.php
│   │   ├── 004_create_calculation_history.php
│   │   ├── 009_create_export_templates.php
│   │   ├── 010_add_profile_fields_to_users.php
│   │   ├── 011_create_shares_table.php
│   │   ├── 012_create_comments_table.php
│   │   ├── 013_create_votes_table.php
│   │   ├── 014_create_email_threads_table.php
│   │   ├── 015_create_email_responses_table.php
│   │   ├── 016_create_email_templates_table.php
│   │   ├── 017_create_site_settings_table.php
│   │   ├── 018_create_complete_system_tables.php
│   │   ├── 019_enhance_settings_table.php
│   │   ├── 020_create_content_tables.php
│   │   ├── 021_create_gdpr_tables.php
│   │   ├── 022_create_2fa_tables.php
│   │   ├── 023_set_default_logo_favicon.php
│   │   ├── 025_create_admin_notifications_table.php
│   │   ├── 026_create_paypal_subscriptions.php
│   │   ├── 027_create_enterprise_quiz_tables.php
│   │   ├── 028_add_shuffle_to_exams.php
│   │   ├── 029_create_leaderboard_table.php
│   │   ├── 030_create_ghost_mode_tables.php
│   │   ├── 031_create_civil_city_tables.php
│   │   ├── 032_create_security_tables.php
│   │   ├── add_bounty_preview_column.sql
│   │   ├── add_comprehensive_units.sql
│   │   ├── add_file_hash_columns.sql
│   │   ├── add_report_fields_to_email_threads.sql
│   │   ├── add_themes_table.php
│   │   ├── add_theme_customizations_table.php
│   │   ├── blueprint_vault_setup.sql
│   │   ├── bounty_system_setup.sql
│   │   ├── career_setup.sql
│   │   ├── create_calculator_platform.sql
│   │   ├── create_est_boq_versions.sql
│   │   ├── create_est_templates.sql
│   │   ├── create_images_table.php
│   │   ├── create_notifications_tables.sql
│   │   ├── create_premium_themes_table.php
│   │   ├── human_elements_setup.sql
│   │   ├── lifeline_economy_setup.sql
│   │   ├── optimize_estimation_db.sql
│   │   ├── seed_all_units.sql
│   │   └── viewer_setup.sql
│   ├── OPTIMIZATION_REPORT.md
│   ├── payment_settings_table.sql
│   ├── paypal_subscription_schema.sql
│   ├── run_email_migrations.php
│   ├── run_migration.php
│   ├── run_new_migrations.php
│   ├── run_notifications_migration.php
│   ├── run_permalink_migration.php
│   ├── search_schema.sql
│   ├── seed_email_templates.php
│   ├── setup_db.php
│   ├── setup_enterprise_email.php
│   ├── setup_payment_settings.php
│   ├── setup_paypal_schema.php
│   ├── test_settings.php
│   ├── test_system_variables.php
│   ├── update_templates_to_use_variables.php
│   ├── verify_themes.php
│   └── verify_urls.php
├── Database Migration System.md
├── docs/
│   ├── admin-sponsor-management-b2b-campaign.md
│   ├── admin-sponsor-management-platform.md
│   ├── bounty-shop-dual-marketplace.md
│   ├── completed_audits/
│   │   ├── 01_SECURITY_AUDIT_REPORT.md
│   │   ├── 02_PRODUCTION_READINESS_REPORT.md
│   │   ├── 03_SHARED_HOSTING_OPTIMIZATION_GUIDE.md
│   │   ├── 04_OPERATIONAL_EXCELLENCE_GUIDE.md
│   │   ├── 05_FINAL_PROJECT_STATUS_REPORT.md
│   │   ├── Admin_Panel_Security_Features.md
│   │   ├── Database Migration System.md
│   │   ├── Gamification System Architecture.md
│   │   ├── Gamification_Service_Architecture.md
│   │   ├── Gamification_System.md
│   │   ├── Production_Readiness_Automation.md
│   │   ├── Quiz_System.md
│   │   ├── README.md
│   │   ├── Security Services Implementation.md
│   │   └── Security_Patch_Verification.md
│   ├── library-file-management-complete-flow.md
│   └── library-file-management-viewer.md
├── dual-track-psc-world-integration-plan.md
├── Dual-Track Career System and Rank Ladder.md
├── Dual-Track Career System Infrastructure - Current State.md
├── Dual File Upload Strategy and Master Prompt.md
├── favicon.ico
├── forgot-password.php
├── Gamification Shop & Resource Management System.md
├── Gamification System Architecture.md
├── includes/
│   └── config.php
├── index.php
├── install/
│   ├── activate_modules.php
│   ├── ajax/
│   │   └── test-email.php
│   ├── apply_indexes.php
│   ├── assets/
│   │   ├── css/
│   │   │   └── install.css
│   │   ├── images/
│   │   │   └── banner.jpg
│   │   └── js/
│   │       └── install.js
│   ├── check_table.php
│   ├── create_backups_table.sql
│   ├── database.sql
│   ├── includes/
│   │   ├── Installer.php
│   │   ├── migration_compat.php
│   │   └── Requirements.php
│   ├── index.php
│   ├── installer.php
│   ├── performance_indexes.sql
│   ├── plugins.sql
│   ├── setup_backups_table.php
│   ├── steps/
│   │   ├── admin.php
│   │   ├── complete.php
│   │   ├── database.php
│   │   ├── requirements.php
│   │   ├── settings.php
│   │   └── welcome.php
│   └── sync_modules.php
├── Library & Bounty System - Dual Marketplace.md
├── library-blueprint-vault-api.md
├── logout.php
├── manifest.json
├── Media Manager Modal.md
├── Media Upload and Storage System.md
├── migrate_identity.php
├── modules/
│   ├── civil/
│   │   ├── brickwork/
│   │   │   ├── brick-quantity.php
│   │   │   ├── mortar-ratio.php
│   │   │   └── plastering-estimator.php
│   │   ├── concrete/
│   │   │   ├── concrete-mix.php
│   │   │   ├── concrete-strength.php
│   │   │   ├── concrete-volume.php
│   │   │   ├── formwork-quantity.php
│   │   │   └── rebar-calculation.php
│   │   ├── earthwork/
│   │   │   ├── cut-and-fill-volume.php
│   │   │   ├── excavation-volume.php
│   │   │   └── slope-calculation.php
│   │   └── structural/
│   │       ├── beam-load-capacity.php
│   │       ├── column-design.php
│   │       ├── foundation-design.php
│   │       └── slab-design.php
│   ├── country/
│   │   └── nepali-land.php
│   ├── electrical/
│   │   ├── conduit-sizing/
│   │   │   ├── cable-tray-sizing.php
│   │   │   ├── conduit-fill-calculation.php
│   │   │   ├── entrance-service-sizing.php
│   │   │   └── junction-box-sizing.php
│   │   ├── load-calculation/
│   │   │   ├── arc-flash-boundary.php
│   │   │   ├── battery-load-bank-sizing.php
│   │   │   ├── demand-load-calculation.php
│   │   │   ├── feeder-sizing.php
│   │   │   ├── general-lighting-load.php
│   │   │   ├── motor-full-load-amps.php
│   │   │   ├── ocpd-sizing.php
│   │   │   ├── ohms-law.php
│   │   │   ├── panel-schedule.php
│   │   │   ├── power-factor.php
│   │   │   ├── power_factor.php
│   │   │   ├── receptacle-load.php
│   │   │   ├── voltage-divider.php
│   │   │   └── voltage_divider.php
│   │   ├── short-circuit/
│   │   │   ├── available-fault-current.php
│   │   │   ├── ground-conductor-sizing.php
│   │   │   └── power-factor-correction.php
│   │   ├── voltage-drop/
│   │   │   ├── generic-voltage-drop.php
│   │   │   ├── single-phase-voltage-drop.php
│   │   │   ├── three-phase-voltage-drop.php
│   │   │   ├── voltage-drop-sizing.php
│   │   │   ├── voltage-regulation.php
│   │   │   └── voltage_drop.php
│   │   └── wire-sizing/
│   │       ├── motor-circuit-wire-sizing.php
│   │       ├── motor-circuit-wiring.php
│   │       ├── transformer-kva-sizing.php
│   │       ├── wire-ampacity.php
│   │       └── wire-size-by-current.php
│   ├── estimation/
│   │   ├── cost-estimation/
│   │   │   ├── boq-preparation.php
│   │   │   ├── contingency-overheads.php
│   │   │   ├── cost-escalation.php
│   │   │   ├── item-rate-analysis.php
│   │   │   └── project-cost-summary.php
│   │   ├── equipment-estimation/
│   │   │   ├── equipment-allocation.php
│   │   │   ├── equipment-hourly-rate.php
│   │   │   ├── fuel-consumption.php
│   │   │   └── machinery-usage.php
│   │   ├── financial/
│   │   │   ├── bid-price-comparison.php
│   │   │   ├── bid-sheet-generator.php
│   │   │   ├── cash-flow-analysis.php
│   │   │   ├── npv-irr-analysis.php
│   │   │   └── profit-loss-analysis.php
│   │   ├── labor/
│   │   │   ├── labor-hour-calculation.php
│   │   │   └── manpower-requirement.php
│   │   ├── labor-estimation/
│   │   │   ├── labor-cost-estimator.php
│   │   │   ├── labor-hour-calculation.php
│   │   │   ├── labor-rate-analysis.php
│   │   │   └── manpower-requirement.php
│   │   ├── machinery/
│   │   │   ├── equipment-hourly-rate.php
│   │   │   ├── fuel-consumption.php
│   │   │   └── machinery-usage.php
│   │   ├── material-estimation/
│   │   │   ├── concrete-materials.php
│   │   │   ├── masonry-materials.php
│   │   │   ├── paint-materials.php
│   │   │   ├── plaster-materials.php
│   │   │   └── tile-materials.php
│   │   ├── materials/
│   │   │   ├── concrete-materials.php
│   │   │   ├── masonry-materials.php
│   │   │   ├── paint-materials.php
│   │   │   ├── plaster-materials.php
│   │   │   └── tile-materials.php
│   │   ├── project-financials/
│   │   │   ├── break-even-analysis.php
│   │   │   ├── cash-flow-analysis.php
│   │   │   ├── npv-irr-analysis.php
│   │   │   ├── payback-period.php
│   │   │   └── profit-loss-analysis.php
│   │   ├── quantity/
│   │   │   ├── brickwork-quantity.php
│   │   │   ├── concrete-quantity.php
│   │   │   ├── flooring-quantity.php
│   │   │   ├── formwork-quantity.php
│   │   │   ├── paint-quantity.php
│   │   │   ├── plaster-quantity.php
│   │   │   └── rebar-quantity.php
│   │   ├── quantity-takeoff/
│   │   │   ├── brickwork-quantity.php
│   │   │   ├── concrete-quantity.php
│   │   │   ├── flooring-quantity.php
│   │   │   ├── formwork-quantity.php
│   │   │   ├── paint-quantity.php
│   │   │   ├── plaster-quantity.php
│   │   │   └── rebar-quantity.php
│   │   ├── rates/
│   │   │   ├── boq-preparation.php
│   │   │   ├── contingency-overheads.php
│   │   │   ├── item-rate-analysis.php
│   │   │   ├── labor-rate-analysis.php
│   │   │   └── project-cost-summary.php
│   │   ├── reports/
│   │   │   ├── detailed-boq-report.php
│   │   │   ├── equipment-cost-report.php
│   │   │   ├── financial-dashboard.php
│   │   │   ├── labor-cost-report.php
│   │   │   ├── material-cost-report.php
│   │   │   └── summary-report.php
│   │   └── tender-bidding/
│   │       ├── bid-price-comparison.php
│   │       ├── bid-sheet-generator.php
│   │       ├── pre-bid-analysis.php
│   │       └── rate-deviation.php
│   ├── fire/
│   │   ├── fire-pumps/
│   │   │   ├── driver-power.php
│   │   │   ├── jockey-pump.php
│   │   │   └── pump-sizing.php
│   │   ├── hazard-classification/
│   │   │   ├── commodity-classification.php
│   │   │   ├── design-density.php
│   │   │   └── occupancy-assessment.php
│   │   ├── hydraulics/
│   │   │   └── hazen-williams.php
│   │   ├── index.php
│   │   ├── sprinklers/
│   │   │   ├── discharge-calculations.php
│   │   │   ├── pipe-sizing.php
│   │   │   └── sprinkler-layout.php
│   │   └── standpipes/
│   │       ├── hose-demand.php
│   │       ├── pressure-calculations.php
│   │       └── standpipe-classification.php
│   ├── hvac/
│   │   ├── duct-sizing/
│   │   │   ├── duct-by-velocity.php
│   │   │   ├── equivalent-duct.php
│   │   │   ├── equivalent-round.php
│   │   │   ├── fitting-loss.php
│   │   │   ├── grille-sizing.php
│   │   │   ├── pressure-drop.php
│   │   │   └── velocity-sizing.php
│   │   ├── energy-analysis/
│   │   │   ├── co2-emissions.php
│   │   │   ├── energy-consumption.php
│   │   │   ├── insulation-savings.php
│   │   │   └── payback-period.php
│   │   ├── equipment-sizing/
│   │   │   ├── ac-sizing.php
│   │   │   ├── chiller-sizing.php
│   │   │   ├── furnace-sizing.php
│   │   │   └── pump-sizing.php
│   │   ├── load-calculation/
│   │   │   ├── cooling-load.php
│   │   │   ├── heating-load.php
│   │   │   ├── infiltration.php
│   │   │   └── ventilation.php
│   │   └── psychrometrics/
│   │       ├── air-properties.php
│   │       ├── cooling-load-psych.php
│   │       ├── enthalpy.php
│   │       └── sensible-heat-ratio.php
│   ├── management/
│   │   ├── dashboard/
│   │   │   ├── gantt-chart.php
│   │   │   ├── milestone-tracker.php
│   │   │   └── project-overview.php
│   │   ├── documents/
│   │   │   ├── document-repository.php
│   │   │   ├── drawing-register.php
│   │   │   └── submittal-tracking.php
│   │   ├── financial/
│   │   │   ├── budget-tracking.php
│   │   │   ├── cost-control.php
│   │   │   └── forecast-analysis.php
│   │   ├── quality/
│   │   │   ├── audit-reports.php
│   │   │   ├── quality-checklist.php
│   │   │   └── safety-incidents.php
│   │   ├── resources/
│   │   │   ├── equipment-allocation.php
│   │   │   ├── manpower-planning.php
│   │   │   └── material-tracking.php
│   │   └── scheduling/
│   │       ├── assign-task.php
│   │       ├── create-task.php
│   │       └── task-dependency.php
│   ├── mathematics/
│   ├── mep/
│   │   ├── bootstrap.php
│   │   ├── collaboration/
│   │   │   ├── bim-integration.php
│   │   │   ├── cloud-sync.php
│   │   │   ├── project-sharing.php
│   │   │   └── revit-plugin.php
│   │   ├── coordination/
│   │   │   ├── bim-export.php
│   │   │   ├── clash-detection.php
│   │   │   ├── coordination-map.php
│   │   │   ├── space-allocation.php
│   │   │   └── system-priority.php
│   │   ├── cost-management/
│   │   │   ├── boq-generator.php
│   │   │   ├── cost-optimization.php
│   │   │   ├── cost-summary.php
│   │   │   ├── material-takeoff.php
│   │   │   └── vendor-pricing.php
│   │   ├── data-utilities/
│   │   │   ├── api-endpoints.php
│   │   │   ├── input-validator.php
│   │   │   ├── material-database.php
│   │   │   ├── mep-config.php
│   │   │   ├── permissions.php
│   │   │   └── unit-converter.php
│   │   ├── electrical/
│   │   │   ├── conduit-sizing.php
│   │   │   ├── earthing-system.php
│   │   │   ├── emergency-power.php
│   │   │   ├── lighting-layout.php
│   │   │   ├── mep-electrical-summary.php
│   │   │   ├── panel-schedule-mep.php
│   │   │   ├── panel-schedule.php
│   │   │   └── transformer-sizing.php
│   │   ├── energy-efficiency/
│   │   │   ├── energy-consumption.php
│   │   │   ├── green-rating.php
│   │   │   ├── hvac-efficiency.php
│   │   │   ├── solar-system.php
│   │   │   └── water-efficiency.php
│   │   ├── fire/
│   │   │   ├── fire-hydrant-system.php
│   │   │   ├── fire-pump-sizing.php
│   │   │   ├── fire-safety-zoning.php
│   │   │   └── fire-tank-sizing.php
│   │   ├── fire-protection/
│   │   │   ├── fire-hydrant-system.php
│   │   │   ├── fire-pump-sizing.php
│   │   │   ├── fire-safety-zoning.php
│   │   │   └── fire-tank-sizing.php
│   │   ├── integration/
│   │   │   ├── autocad-layer-mapper.php
│   │   │   ├── bim-integration.php
│   │   │   ├── cloud-sync.php
│   │   │   ├── project-sharing.php
│   │   │   └── revit-plugin.php
│   │   ├── management/
│   │   │   ├── boq-generator.php
│   │   │   ├── cost-optimization.php
│   │   │   ├── cost-summary.php
│   │   │   ├── material-takeoff.php
│   │   │   └── vendor-pricing.php
│   │   ├── mechanical/
│   │   │   ├── chilled-water-piping.php
│   │   │   ├── equipment-database.php
│   │   │   └── hvac-duct-sizing.php
│   │   ├── plumbing/
│   │   │   ├── drainage-system.php
│   │   │   ├── plumbing-fixture-count.php
│   │   │   ├── pump-selection.php
│   │   │   ├── storm-water.php
│   │   │   ├── water-supply.php
│   │   │   └── water-tank-sizing.php
│   │   ├── reports/
│   │   │   ├── clash-detection-report.php
│   │   │   ├── equipment-schedule.php
│   │   │   ├── load-summary.php
│   │   │   ├── mep-summary.php
│   │   │   └── pdf-export.php
│   │   ├── reports-documentation/
│   │   │   ├── clash-detection-report.php
│   │   │   ├── equipment-schedule.php
│   │   │   ├── load-summary.php
│   │   │   ├── mep-summary.php
│   │   │   └── pdf-export.php
│   │   ├── sustainability/
│   │   │   ├── energy-consumption.php
│   │   │   ├── green-rating.php
│   │   │   ├── hvac-efficiency.php
│   │   │   ├── solar-system.php
│   │   │   └── water-efficiency.php
│   │   └── system/
│   │       ├── api-endpoints.php
│   │       ├── autocad-layer-mapper.php
│   │       ├── input-validator.php
│   │       ├── material-database.php
│   │       ├── mep-config.php
│   │       ├── permissions.php
│   │       └── unit-converter.php
│   ├── plumbing/
│   │   ├── drainage/
│   │   │   ├── drainage-pipe-sizing.php
│   │   │   ├── grease-trap-sizing.php
│   │   │   ├── soil-stack-sizing.php
│   │   │   ├── storm-drainage.php
│   │   │   ├── trap-sizing.php
│   │   │   └── vent-pipe-sizing.php
│   │   ├── fixtures/
│   │   │   ├── fixture-unit-calculation.php
│   │   │   ├── shower-sizing.php
│   │   │   ├── sink-sizing.php
│   │   │   └── toilet-flow.php
│   │   ├── hot_water/
│   │   │   ├── heat-loss-calculation.php
│   │   │   ├── hot-water-storage.php
│   │   │   ├── recirculation-loop.php
│   │   │   ├── safety-valve-calculation.php
│   │   │   ├── safety-valve.php
│   │   │   ├── storage-tank-sizing.php
│   │   │   └── water-heater-sizing.php
│   │   ├── pipe_sizing/
│   │   │   ├── expansion-loop-sizing.php
│   │   │   ├── gas-pipe-sizing.php
│   │   │   ├── pipe-flow-capacity.php
│   │   │   ├── pressure-loss.php
│   │   │   └── water-pipe-sizing.php
│   │   ├── potable_water/
│   │   │   ├── main-isolation-valve.php
│   │   │   ├── pump-sizing.php
│   │   │   ├── safety-valve.php
│   │   │   └── storage-tank-sizing.php
│   │   ├── shared/
│   │   ├── stormwater/
│   │   │   ├── downpipe-sizing.php
│   │   │   ├── gutter-sizing.php
│   │   │   ├── pervious-area.php
│   │   │   ├── storm-drainage.php
│   │   │   └── stormwater-storage.php
│   │   └── water_supply/
│   │       ├── cold-water-demand.php
│   │       ├── cold-water-storage.php
│   │       ├── hot-water-demand.php
│   │       ├── main-isolation-valve.php
│   │       ├── pressure-loss.php
│   │       ├── pump-sizing.php
│   │       ├── storage-tank-sizing.php
│   │       ├── water-demand-calculation.php
│   │       └── water-hammer-calculation.php
│   ├── project-management/
│   │   ├── analytics/
│   │   │   ├── cost-analysis.php
│   │   │   ├── custom-reports.php
│   │   │   ├── performance-dashboard.php
│   │   │   ├── predictive-analytics.php
│   │   │   ├── resource-utilization.php
│   │   │   └── trend-analysis.php
│   │   ├── communication/
│   │   │   ├── discussion-board.php
│   │   │   ├── document-sharing.php
│   │   │   ├── email-integration.php
│   │   │   ├── meeting-minutes.php
│   │   │   ├── notification-system.php
│   │   │   └── team-chat.php
│   │   ├── dashboard/
│   │   │   ├── gantt-chart.php
│   │   │   ├── milestone-tracker.php
│   │   │   ├── project-health.php
│   │   │   ├── project-overview.php
│   │   │   ├── task-summary.php
│   │   │   └── weather-integration.php
│   │   ├── documents/
│   │   │   ├── approval-workflow.php
│   │   │   ├── archive-system.php
│   │   │   ├── document-repository.php
│   │   │   ├── drawing-register.php
│   │   │   ├── submittal-tracking.php
│   │   │   └── version-control.php
│   │   ├── financial/
│   │   │   ├── budget-tracking.php
│   │   │   ├── cost-control.php
│   │   │   ├── financial-reports.php
│   │   │   ├── forecast-analysis.php
│   │   │   ├── invoice-management.php
│   │   │   └── payment-tracking.php
│   │   ├── integration/
│   │   │   ├── accounting-sync.php
│   │   │   ├── api-endpoints.php
│   │   │   ├── bim-integration.php
│   │   │   ├── calendar-sync.php
│   │   │   ├── data-import-export.php
│   │   │   └── email-integration.php
│   │   ├── procurement/
│   │   │   ├── delivery-tracking.php
│   │   │   ├── inventory-tracking.php
│   │   │   ├── material-requests.php
│   │   │   ├── purchase-orders.php
│   │   │   ├── stock-control.php
│   │   │   └── vendor-management.php
│   │   ├── quality/
│   │   │   ├── audit-reports.php
│   │   │   ├── compliance-tracking.php
│   │   │   ├── inspection-reports.php
│   │   │   ├── quality-checklist.php
│   │   │   ├── risk-assessment.php
│   │   │   └── safety-incidents.php
│   │   ├── reports/
│   │   │   ├── custom-reports.php
│   │   │   ├── daily-reports.php
│   │   │   ├── delay-analysis.php
│   │   │   ├── performance-metrics.php
│   │   │   ├── progress-photos.php
│   │   │   └── status-updates.php
│   │   ├── resources/
│   │   │   ├── availability-tracker.php
│   │   │   ├── daily-report.php
│   │   │   ├── equipment-allocation.php
│   │   │   ├── manpower-planning.php
│   │   │   ├── material-tracking.php
│   │   │   ├── resource-calendar.php
│   │   │   └── skill-matrix.php
│   │   ├── scheduling/
│   │   │   ├── assign-task.php
│   │   │   ├── calendar-view.php
│   │   │   ├── create-task.php
│   │   │   ├── recurring-tasks.php
│   │   │   ├── schedule-optimizer.php
│   │   │   └── task-dependency.php
│   │   ├── settings/
│   │   │   ├── project-settings.php
│   │   │   ├── role-permissions.php
│   │   │   ├── system-backup.php
│   │   │   ├── template-management.php
│   │   │   ├── user-management.php
│   │   │   └── workflow-config.php
│   │   └── template-coming-soon.php
│   ├── site/
│   │   ├── concrete/
│   │   │   ├── placement-rate.php
│   │   │   ├── temperature-control.php
│   │   │   ├── testing-requirements.php
│   │   │   └── yardage-adjustments.php
│   │   ├── concrete-tools/
│   │   │   ├── placement-rate.php
│   │   │   ├── temperature-control.php
│   │   │   ├── testing-requirements.php
│   │   │   └── yardage-adjustments.php
│   │   ├── earthwork/
│   │   │   ├── cut-fill-balancing.php
│   │   │   ├── cut-fill.php
│   │   │   ├── equipment-production.php
│   │   │   ├── excavation-cost.php
│   │   │   ├── slope-paving.php
│   │   │   ├── soil-compaction.php
│   │   │   ├── swelling-shrink.php
│   │   │   ├── swelling-shrinkage.php
│   │   │   ├── topsoil-removal.php
│   │   │   └── trench-volume.php
│   │   ├── equipment/
│   │   │   ├── equipment-production.php
│   │   │   ├── fleet-sizing.php
│   │   │   └── owning-operating-cost.php
│   │   ├── materials/
│   │   │   ├── asphalt-calculator.php
│   │   │   ├── bricks-calculation.php
│   │   │   ├── cement-mortar.php
│   │   │   ├── concrete-mix.php
│   │   │   ├── flooring-quantity.php
│   │   │   ├── paint-materials.php
│   │   │   ├── paint-quantity.php
│   │   │   └── tile-calculator.php
│   │   ├── productivity/
│   │   │   ├── cost-productivity.php
│   │   │   ├── equipment-utilization.php
│   │   │   ├── labor-productivity.php
│   │   │   └── schedule-compression.php
│   │   ├── safety/
│   │   │   ├── crane-setup.php
│   │   │   ├── crane-stability.php
│   │   │   ├── evacuation-planning.php
│   │   │   ├── excavation-safety.php
│   │   │   ├── fall-protection.php
│   │   │   ├── scaffold-load.php
│   │   │   └── trench-safety.php
│   │   └── surveying/
│   │       ├── area-coordinates.php
│   │       ├── batter-boards.php
│   │       ├── coordinates-distance.php
│   │       ├── curve-setting.php
│   │       ├── grade-rod.php
│   │       ├── horizontal-curve-staking.php
│   │       ├── leveling-reduction.php
│   │       ├── slope-gradient.php
│   │       └── slope-staking.php
│   └── structural/
│       ├── beam-analysis/
│       │   ├── beam-design.php
│       │   ├── beam-load-combination.php
│       │   ├── cantilever-beam.php
│       │   ├── continuous-beam.php
│       │   └── simply-supported-beam.php
│       ├── column-design/
│       │   ├── biaxial-column.php
│       │   ├── column-footing-link.php
│       │   ├── long-column.php
│       │   ├── short-column.php
│       │   └── steel-column-design.php
│       ├── foundation-design/
│       │   ├── combined-footing.php
│       │   ├── foundation-pressure.php
│       │   ├── isolated-footing.php
│       │   ├── pile-foundation.php
│       │   ├── raft-foundation.php
│       │   └── strap-footing.php
│       ├── load-analysis/
│       │   ├── dead-load.php
│       │   ├── live-load.php
│       │   ├── load-combination.php
│       │   ├── seismic-load.php
│       │   └── wind-load.php
│       ├── reinforcement/
│       │   ├── bar-bending-schedule.php
│       │   ├── detailing-drawing.php
│       │   ├── development-length.php
│       │   ├── lap-length.php
│       │   ├── rebar-anchorage.php
│       │   ├── rebar-spacing.php
│       │   ├── reinforcement-optimizer.php
│       │   └── stirrup-design.php
│       ├── reports/
│       │   ├── bar-bending-schedule.php
│       │   ├── beam-report.php
│       │   ├── column-report.php
│       │   ├── cost-estimate.php
│       │   ├── foundation-report.php
│       │   ├── full-structure-summary.php
│       │   ├── load-analysis-summary.php
│       │   ├── material-summary.php
│       │   ├── quantity-takeoff.php
│       │   └── structural-report.php
│       ├── slab-design/
│       │   ├── cantilever-slab.php
│       │   ├── flat-slab.php
│       │   ├── one-way-slab.php
│       │   ├── slab-load-calculation.php
│       │   ├── two-way-slab.php
│       │   └── waffle-slab.php
│       └── steel-structure/
│           ├── composite-beam.php
│           ├── connection-design.php
│           ├── plate-girder.php
│           ├── purlin-design.php
│           ├── steel-base-plate.php
│           ├── steel-beam-design.php
│           └── steel-truss-analysis.php
├── Multiplayer Quiz Lobby System - Real-time Competitive Quiz Platform.md
├── node_modules/
├── opencode.md
├── openspec/
│   ├── AGENTS.md
│   ├── changes/
│   │   ├── add-standalone-calculators-to-engine/
│   │   │   ├── design.md
│   │   │   ├── proposal.md
│   │   │   ├── specs/
│   │   │   │   ├── admin-calculator-management/
│   │   │   │   │   └── spec.md
│   │   │   │   ├── calculators-engine/
│   │   │   │   │   └── spec.md
│   │   │   │   └── user-experience/
│   │   │   │       └── spec.md
│   │   │   └── tasks.md
│   │   └── archive/
│   ├── project.md
│   └── specs/
├── package-lock.json
├── package.json
├── project-structure-report.php
├── project-structure-report.txt
├── public/
│   ├── .htaccess
│   ├── assets/
│   │   ├── badges/
│   │   ├── css/
│   │   │   └── global-notifications.css
│   │   ├── data/
│   │   │   ├── english_locations.json
│   │   │   └── nepali_locations.json
│   │   ├── icons/
│   │   │   ├── favicon.ico
│   │   │   ├── icon-192.png
│   │   │   └── icon-512.png
│   │   ├── js/
│   │   │   ├── admin/
│   │   │   │   └── settings-manager.js
│   │   │   ├── admin.js
│   │   │   ├── app-utils.js
│   │   │   ├── exports.js
│   │   │   ├── global-notifications.js
│   │   │   ├── history.js
│   │   │   ├── profile.js
│   │   │   ├── responsive-nav.js
│   │   │   ├── search-toggle.js
│   │   │   └── share.js
│   │   └── vendor/
│   │       ├── abraham/twitteroauth (^3.1)
│   │       ├── altcha-org/altcha (^1.1)
│   │       ├── bacon/bacon-qr-code (^2.0)
│   │       ├── defuse/php-encryption (^2.2)
│   │       ├── endroid/qr-code (4.6.1)
│   │       ├── guzzlehttp/guzzle (^7.0)
│   │       ├── intervention/image (^3.11)
│   │       ├── jaybizzle/crawler-detect (^1.2)
│   │       ├── league/csv (^9.0)
│   │       ├── markrogoyski/math-php (^1.0)
│   │       ├── maxmind-db/reader (^1.12)
│   │       ├── mollie/mollie-api-php (^2.71)
│   │       ├── monolog/monolog (^2.0)
│   │       ├── mpdf/mpdf (^8.1)
│   │       ├── nesbot/carbon (^2.0)
│   │       ├── nikic/fast-route (^1.3)
│   │       ├── paragonie/random_compat (^9.99)
│   │       ├── paypal/rest-api-sdk-php (^1.6)
│   │       ├── phpfastcache/phpfastcache (^8.0)
│   │       ├── phpmailer/phpmailer (^7.0)
│   │       ├── phpoffice/phpspreadsheet (^5.3)
│   │       ├── pragmarx/google2fa (^9.0)
│   │       ├── ramsey/uuid (^4.7)
│   │       ├── respect/validation (^2.2)
│   │       ├── sentry/sentry (^4.18)
│   │       ├── setasign/fpdf (^1.8)
│   │       ├── stripe/stripe-php (^15.10)
│   │       ├── symfony/cache (^5.4)
│   │       ├── symfony/validator (^5.4)
│   │       ├── tecnickcom/tcpdf (^6.6)
│   │       └── vlucas/phpdotenv (^5.5)
│   ├── debug_base.php
│   ├── debug_reset_v2.php
│   ├── index.php
│   ├── manifest.json
│   ├── notification-demo.html
│   ├── robots.txt
│   ├── service-worker.js
│   ├── sitemap.xml
│   ├── storage/
│   │   ├── .htaccess
│   │   └── media/
│   │       └── images/
│   ├── sw.js
│   ├── templates/
│   │   └── question_import_template.csv
│   ├── theme-assets.php
│   └── uploads/
│       ├── .htaccess
│       ├── avatars/
│       │   ├── Bishwo-God_691c8a6b15f9f2.68215981.png
│       │   ├── Bishwo-God_691c85fa5f5151.00565393.png
│       │   ├── Bishwo-God_691c87a5a91d59.44925376.png
│       │   └── Bishwo-God_691c99af254bc2.55542627.png
│       └── settings/
│           ├── favicon.png
│           └── logo.png
├── quiz-system-no-personalization-plan.md
├── Quiz System Authentication and Exam Flow.md
├── Quiz System Infrastructure - Foundation for Suggestion Engine.md
├── Quiz URLs.md
├── Rank Assets Folder and Naming Convention.md
├── run_b2b_migration.php
├── run_library_migration.php
├── scripts/
│   ├── add_production_indexes.php
│   ├── inspect_projects_schema.php
│   ├── inspect_schema.php
│   ├── migrate_locations.php
│   ├── migrate_project_location_column.php
│   ├── migrate_urls.php
│   ├── run_bounty_migration.php
│   ├── run_career_migration.php
│   ├── run_hash_migration.php
│   ├── run_human_migration.php
│   ├── run_onboarding_migration.php
│   ├── run_premium_migration.php
│   ├── run_shop_migration.php
│   ├── run_viewer_migration.php
│   ├── run_watermark_migration.php
│   ├── seed_boq_data.php
│   ├── seed_locations_from_local_json.php
│   ├── seed_locations_full.php
│   ├── temp_locations.json
│   ├── test_location_api.php
│   └── test_rate_injection.php
├── Security Services Implementation.md
├── service-worker.js
├── shop_error_output.html
├── sitemap.php
├── speckit.constitution
├── storage/
│   ├── app/
│   │   ├── api_cert_chain.crt
│   │   ├── bookmarklet.uncompressed.js
│   │   ├── bookmarklet.uncompressed.min.js
│   │   ├── calculators_status.json
│   │   ├── GeoLite2-City.mmdb
│   │   ├── jShortener.js
│   │   ├── modules_config.json
│   │   └── wpplugin.php
│   ├── backups/
│   ├── cache/
│   │   └── ratelimit/
│   ├── exports/
│   │   └── user_data_4_2025-11-18_16-01-43.zip
│   ├── install.lock
│   ├── installed.lock
│   ├── installer.processed
│   ├── library/
│   │   ├── approved/
│   │   │   ├── cad/
│   │   │   │   ├── lib_6957a12d9d6dd_1767350573.pdf
│   │   │   │   └── lib_69579d3dc5056_1767349565.dwg
│   │   │   ├── doc/
│   │   │   ├── excel/
│   │   │   ├── image/
│   │   │   ├── other/
│   │   │   └── pdf/
│   │   ├── previews/
│   │   │   ├── preview_6957a12d9e695_1767350573.jpg
│   │   │   ├── preview_6957a31dd4e28_1767351069.png
│   │   │   ├── preview_6957a404da2ed_1767351300.jpg
│   │   │   ├── preview_6957a813b8149_1767352339.png
│   │   │   ├── preview_6957b2760b54e_1767354998.png
│   │   │   └── preview_69579d3dc6274_1767349565.jpg
│   │   └── quarantine/
│   │       ├── lib_6957a31dd465f_1767351069.pdf
│   │       ├── lib_6957a404d87b0_1767351300.dwg
│   │       ├── lib_6957a813b7083_1767352339.xlsx
│   │       ├── lib_6957b23e3f5cc_1767354942.dwg
│   │       └── lib_6957b2760a38f_1767354998.dwg
│   ├── logs/
│   │   ├── 2025-12-14.log
│   │   ├── 2025-12-17.log
│   │   ├── 2025-12-18.log
│   │   ├── 2025-12-19.log
│   │   ├── 2025-12-20.log
│   │   ├── 2025-12-21.log
│   │   ├── 2025-12-22.log
│   │   ├── 2025-12-23.log
│   │   ├── 2025-12-24.log
│   │   ├── 2025-12-25.log
│   │   ├── 2025-12-26.log
│   │   ├── 2025-12-27.log
│   │   ├── 2025-12-28.log
│   │   ├── 2025-12-29.log
│   │   ├── 2025-12-30.log
│   │   ├── 2025-12-31.log
│   │   ├── 2026-01-01.log
│   │   ├── 2026-01-02.log
│   │   ├── 2026-01-03.log
│   │   ├── cron_daily.log
│   │   └── php_error.log
│   ├── menus.json
│   ├── public/
│   │   └── previews/
│   └── uploads/
│       ├── .htaccess
│       └── temp/
│           └── .htaccess
├── Suggestion Engine & Onboarding Flow - Existing vs Planned.md
├── Suggestion Engine and Onboarding Controller.md
├── themes/
│   ├── admin/
│   │   ├── assets/
│   │   │   ├── css/
│   │   │   │   ├── admin.css
│   │   │   │   └── notifications-beautiful.css
│   │   │   ├── images/
│   │   │   │   └── admin-logo.png
│   │   │   └── js/
│   │   │       ├── admin.js
│   │   │       ├── notification-fixed.js
│   │   │       └── theme-toggle.js
│   │   ├── layouts/
│   │   │   └── main.php
│   │   └── views/
│   │       ├── activity/
│   │       │   └── index.php
│   │       ├── admin/
│   │       │   └── widgets/
│   │       │       ├── create.php
│   │       │       ├── index.php
│   │       │       └── settings.php
│   │       ├── advertisements/
│   │       │   ├── form.php
│   │       │   └── index.php
│   │       ├── analytics/
│   │       │   ├── calculators.php
│   │       │   ├── overview.php
│   │       │   ├── performance.php
│   │       │   ├── reports.php
│   │       │   └── users.php
│   │       ├── audit/
│   │       │   └── index.php
│   │       ├── backup/
│   │       │   └── index.php
│   │       ├── blog/
│   │       │   ├── create.php
│   │       │   ├── edit.php
│   │       │   ├── form.php
│   │       │   └── index.php
│   │       ├── bounty/
│   │       │   └── requests.php
│   │       ├── calculations/
│   │       │   └── index.php
│   │       ├── calculators/
│   │       │   ├── create.php
│   │       │   ├── index.php
│   │       │   └── list.php
│   │       ├── configured-dashboard.php
│   │       ├── content/
│   │       │   ├── create.php
│   │       │   ├── index.php
│   │       │   ├── media.php
│   │       │   ├── menus.php
│   │       │   ├── menu_edit.php
│   │       │   ├── pages-optimized.php
│   │       │   └── pages.php
│   │       ├── dashboard.php
│   │       ├── dashboard_complex.php
│   │       ├── debug/
│   │       │   ├── dashboard.php
│   │       │   ├── error-logs.php
│   │       │   ├── live-monitor.php
│   │       │   └── tests.php
│   │       ├── email-manager/
│   │       │   ├── dashboard.php
│   │       │   ├── error.php
│   │       │   ├── settings.php
│   │       │   ├── template-form.php
│   │       │   ├── templates.php
│   │       │   ├── thread-detail.php
│   │       │   └── threads.php
│   │       ├── errors/
│   │       │   └── 404.php
│   │       ├── help/
│   │       │   └── index.php
│   │       ├── library/
│   │       │   └── requests.php
│   │       ├── logo-settings.php
│   │       ├── logs/
│   │       │   ├── index.php
│   │       │   └── view.php
│   │       ├── marketplace/
│   │       │   └── index.php
│   │       ├── menu-customization.php
│   │       ├── modules/
│   │       │   ├── index.php
│   │       │   └── settings.php
│   │       ├── modules.php
│   │       ├── notifications/
│   │       │   ├── history.php
│   │       │   ├── index.php
│   │       │   └── preferences.php
│   │       ├── partials/
│   │       │   └── media_modal.php
│   │       ├── performance-dashboard.php
│   │       ├── plugins/
│   │       │   └── index.php
│   │       ├── quiz/
│   │       │   ├── dashboard.php
│   │       │   ├── exams/
│   │       │   │   ├── builder.php
│   │       │   │   ├── form.php
│   │       │   │   └── index.php
│   │       │   ├── import.php
│   │       │   ├── leaderboard/
│   │       │   │   └── index.php
│   │       │   ├── questions/
│   │       │   │   ├── form.php
│   │       │   │   └── index.php
│   │       │   ├── results/
│   │       │   │   └── index.php
│   │       │   ├── settings.php
│   │       │   └── syllabus/
│   │       │       └── index.php
│   │       ├── security/
│   │       │   ├── alerts.php
│   │       │   └── ip_restrictions.php
│   │       ├── settings/
│   │       │   ├── advanced.php
│   │       │   ├── api.php
│   │       │   ├── application.php
│   │       │   ├── backup.php
│   │       │   ├── economy.php
│   │       │   ├── email.php
│   │       │   ├── general.php
│   │       │   ├── google.php
│   │       │   ├── index.php
│   │       │   ├── payments.php
│   │       │   ├── performance.php
│   │       │   ├── permalinks.php
│   │       │   ├── recaptcha.php
│   │       │   ├── security.php
│   │       │   ├── simple_index.php
│   │       │   └── users.php
│   │       ├── setup/
│   │       │   └── checklist.php
│   │       ├── sponsors/
│   │       │   └── index.php
│   │       ├── subscriptions/
│   │       │   ├── create-plan.php
│   │       │   ├── edit-plan.php
│   │       │   └── index.php
│   │       ├── system/
│   │       │   └── status.php
│   │       ├── system-status/
│   │       │   └── index.php
│   │       ├── system-status.php
│   │       ├── themes/
│   │       │   ├── customize.php
│   │       │   ├── index.php
│   │       │   └── preview.php
│   │       └── users/
│   │           ├── admins.php
│   │           ├── banned.php
│   │           ├── bulk.php
│   │           ├── create.php
│   │           ├── edit.php
│   │           ├── inactive.php
│   │           ├── index.php
│   │           ├── logs/
│   │           │   └── logins.php
│   │           ├── permissions.php
│   │           └── roles.php
│   ├── basic/
│   │   └── views/
│   │       └── quiz/
│   │           ├── analysis/
│   │           │   └── report.php
│   │           ├── arena/
│   │           │   └── room.php
│   │           └── portal/
│   │               ├── index.php
│   │               └── overview.php
│   ├── default/
│   │   ├── assets/
│   │   │   ├── css/
│   │   │   │   ├── back-to-top.css
│   │   │   │   ├── calculator-platform.css
│   │   │   │   ├── civil.css
│   │   │   │   ├── electrical.css
│   │   │   │   ├── estimation.css
│   │   │   │   ├── fire.css
│   │   │   │   ├── floating-calculator.css
│   │   │   │   ├── footer.css
│   │   │   │   ├── header.css
│   │   │   │   ├── home.css
│   │   │   │   ├── hvac.css
│   │   │   │   ├── logo-enhanced.css
│   │   │   │   ├── management.css
│   │   │   │   ├── mep.css
│   │   │   │   ├── plumbing.css
│   │   │   │   ├── site.css
│   │   │   │   ├── structural.css
│   │   │   │   ├── theme.css
│   │   │   │   └── top-header.css
│   │   │   ├── images/
│   │   │   │   ├── 404.svg
│   │   │   │   ├── adroll.svg
│   │   │   │   ├── adwords.svg
│   │   │   │   ├── airbnb.svg
│   │   │   │   ├── aliexpress.svg
│   │   │   │   ├── amazon.png
│   │   │   │   ├── amazonmusic.svg
│   │   │   │   ├── applemusic.svg
│   │   │   │   ├── appstore.svg
│   │   │   │   ├── aroll.svg
│   │   │   │   ├── avatar-f1.jpg
│   │   │   │   ├── avatar-f1.svg
│   │   │   │   ├── avatar-f2.jpg
│   │   │   │   ├── avatar-m1.jpg
│   │   │   │   ├── avatar-m1.svg
│   │   │   │   ├── avatar-m2.jpg
│   │   │   │   ├── avatar-m2.svg
│   │   │   │   ├── bandcamp.svg
│   │   │   │   ├── banner.jpg
│   │   │   │   ├── bing.svg
│   │   │   │   ├── browsers/
│   │   │   │   │   ├── chrome.svg
│   │   │   │   │   ├── edge.svg
│   │   │   │   │   ├── firefox.svg
│   │   │   │   │   ├── handheld.svg
│   │   │   │   │   ├── ie.svg
│   │   │   │   │   ├── index.php
│   │   │   │   │   ├── internet.svg
│   │   │   │   │   ├── konqueror.svg
│   │   │   │   │   ├── maxthon.svg
│   │   │   │   │   ├── mobile.svg
│   │   │   │   │   ├── opera.svg
│   │   │   │   │   ├── safari.svg
│   │   │   │   │   └── unknown.svg
│   │   │   │   ├── calendly.svg
│   │   │   │   ├── deezer.svg
│   │   │   │   ├── eventbrite.svg
│   │   │   │   ├── facebook.png
│   │   │   │   ├── facebook.svg
│   │   │   │   ├── favicon.png
│   │   │   │   ├── filters.png
│   │   │   │   ├── flags/
│   │   │   │   │   ├── ad.svg
│   │   │   │   │   ├── ae.svg
│   │   │   │   │   ├── af.svg
│   │   │   │   │   ├── ag.svg
│   │   │   │   │   ├── ai.svg
│   │   │   │   │   ├── al.svg
│   │   │   │   │   ├── am.svg
│   │   │   │   │   ├── ao.svg
│   │   │   │   │   ├── aq.svg
│   │   │   │   │   ├── ar.svg
│   │   │   │   │   ├── as.svg
│   │   │   │   │   ├── at.svg
│   │   │   │   │   ├── au.svg
│   │   │   │   │   ├── aw.svg
│   │   │   │   │   ├── ax.svg
│   │   │   │   │   ├── az.svg
│   │   │   │   │   ├── ba.svg
│   │   │   │   │   ├── bb.svg
│   │   │   │   │   ├── bd.svg
│   │   │   │   │   ├── be.svg
│   │   │   │   │   ├── bf.svg
│   │   │   │   │   ├── bg.svg
│   │   │   │   │   ├── bh.svg
│   │   │   │   │   ├── bi.svg
│   │   │   │   │   ├── bj.svg
│   │   │   │   │   ├── bl.svg
│   │   │   │   │   ├── bm.svg
│   │   │   │   │   ├── bn.svg
│   │   │   │   │   ├── bo.svg
│   │   │   │   │   ├── bq.svg
│   │   │   │   │   ├── br.svg
│   │   │   │   │   ├── bs.svg
│   │   │   │   │   ├── bt.svg
│   │   │   │   │   ├── bv.svg
│   │   │   │   │   ├── bw.svg
│   │   │   │   │   ├── by.svg
│   │   │   │   │   ├── bz.svg
│   │   │   │   │   ├── ca.svg
│   │   │   │   │   ├── cc.svg
│   │   │   │   │   ├── cd.svg
│   │   │   │   │   ├── cf.svg
│   │   │   │   │   ├── cg.svg
│   │   │   │   │   ├── ch.svg
│   │   │   │   │   ├── ci.svg
│   │   │   │   │   ├── ck.svg
│   │   │   │   │   ├── cl.svg
│   │   │   │   │   ├── cm.svg
│   │   │   │   │   ├── cn.svg
│   │   │   │   │   ├── co.svg
│   │   │   │   │   ├── cr.svg
│   │   │   │   │   ├── cu.svg
│   │   │   │   │   ├── cv.svg
│   │   │   │   │   ├── cw.svg
│   │   │   │   │   ├── cx.svg
│   │   │   │   │   ├── cy.svg
│   │   │   │   │   ├── cz.svg
│   │   │   │   │   ├── de.svg
│   │   │   │   │   ├── dj.svg
│   │   │   │   │   ├── dk.svg
│   │   │   │   │   ├── dm.svg
│   │   │   │   │   ├── do.svg
│   │   │   │   │   ├── dz.svg
│   │   │   │   │   ├── ec.svg
│   │   │   │   │   ├── ee.svg
│   │   │   │   │   ├── eg.svg
│   │   │   │   │   ├── eh.svg
│   │   │   │   │   ├── er.svg
│   │   │   │   │   ├── es-ct.svg
│   │   │   │   │   ├── es.svg
│   │   │   │   │   ├── et.svg
│   │   │   │   │   ├── eu.svg
│   │   │   │   │   ├── fi.svg
│   │   │   │   │   ├── fj.svg
│   │   │   │   │   ├── fk.svg
│   │   │   │   │   ├── fm.svg
│   │   │   │   │   ├── fo.svg
│   │   │   │   │   ├── fr.svg
│   │   │   │   │   ├── ga.svg
│   │   │   │   │   ├── gb-eng.svg
│   │   │   │   │   ├── gb-nir.svg
│   │   │   │   │   ├── gb-sct.svg
│   │   │   │   │   ├── gb-wls.svg
│   │   │   │   │   ├── gb.svg
│   │   │   │   │   ├── gd.svg
│   │   │   │   │   ├── ge.svg
│   │   │   │   │   ├── gf.svg
│   │   │   │   │   ├── gg.svg
│   │   │   │   │   ├── gh.svg
│   │   │   │   │   ├── gi.svg
│   │   │   │   │   ├── gl.svg
│   │   │   │   │   ├── gm.svg
│   │   │   │   │   ├── gn.svg
│   │   │   │   │   ├── gp.svg
│   │   │   │   │   ├── gq.svg
│   │   │   │   │   ├── gr.svg
│   │   │   │   │   ├── gs.svg
│   │   │   │   │   ├── gt.svg
│   │   │   │   │   ├── gu.svg
│   │   │   │   │   ├── gw.svg
│   │   │   │   │   ├── gy.svg
│   │   │   │   │   ├── hk.svg
│   │   │   │   │   ├── hm.svg
│   │   │   │   │   ├── hn.svg
│   │   │   │   │   ├── hr.svg
│   │   │   │   │   ├── ht.svg
│   │   │   │   │   ├── hu.svg
│   │   │   │   │   ├── id.svg
│   │   │   │   │   ├── ie.svg
│   │   │   │   │   ├── il.svg
│   │   │   │   │   ├── im.svg
│   │   │   │   │   ├── in.svg
│   │   │   │   │   ├── index.php
│   │   │   │   │   ├── io.svg
│   │   │   │   │   ├── iq.svg
│   │   │   │   │   ├── ir.svg
│   │   │   │   │   ├── is.svg
│   │   │   │   │   ├── it.svg
│   │   │   │   │   ├── je.svg
│   │   │   │   │   ├── jm.svg
│   │   │   │   │   ├── jo.svg
│   │   │   │   │   ├── jp.svg
│   │   │   │   │   ├── ke.svg
│   │   │   │   │   ├── kg.svg
│   │   │   │   │   ├── kh.svg
│   │   │   │   │   ├── ki.svg
│   │   │   │   │   ├── km.svg
│   │   │   │   │   ├── kn.svg
│   │   │   │   │   ├── kp.svg
│   │   │   │   │   ├── kr.svg
│   │   │   │   │   ├── kw.svg
│   │   │   │   │   ├── ky.svg
│   │   │   │   │   ├── kz.svg
│   │   │   │   │   ├── la.svg
│   │   │   │   │   ├── lb.svg
│   │   │   │   │   ├── lc.svg
│   │   │   │   │   ├── li.svg
│   │   │   │   │   ├── lk.svg
│   │   │   │   │   ├── lr.svg
│   │   │   │   │   ├── ls.svg
│   │   │   │   │   ├── lt.svg
│   │   │   │   │   ├── lu.svg
│   │   │   │   │   ├── lv.svg
│   │   │   │   │   ├── ly.svg
│   │   │   │   │   ├── ma.svg
│   │   │   │   │   ├── mc.svg
│   │   │   │   │   ├── md.svg
│   │   │   │   │   ├── me.svg
│   │   │   │   │   ├── mf.svg
│   │   │   │   │   ├── mg.svg
│   │   │   │   │   ├── mh.svg
│   │   │   │   │   ├── mk.svg
│   │   │   │   │   ├── ml.svg
│   │   │   │   │   ├── mm.svg
│   │   │   │   │   ├── mn.svg
│   │   │   │   │   ├── mo.svg
│   │   │   │   │   ├── mp.svg
│   │   │   │   │   ├── mq.svg
│   │   │   │   │   ├── mr.svg
│   │   │   │   │   ├── ms.svg
│   │   │   │   │   ├── mt.svg
│   │   │   │   │   ├── mu.svg
│   │   │   │   │   ├── mv.svg
│   │   │   │   │   ├── mw.svg
│   │   │   │   │   ├── mx.svg
│   │   │   │   │   ├── my.svg
│   │   │   │   │   ├── mz.svg
│   │   │   │   │   ├── na.svg
│   │   │   │   │   ├── nc.svg
│   │   │   │   │   ├── ne.svg
│   │   │   │   │   ├── nf.svg
│   │   │   │   │   ├── ng.svg
│   │   │   │   │   ├── ni.svg
│   │   │   │   │   ├── nl.svg
│   │   │   │   │   ├── no.svg
│   │   │   │   │   ├── np.svg
│   │   │   │   │   ├── nr.svg
│   │   │   │   │   ├── nu.svg
│   │   │   │   │   ├── nz.svg
│   │   │   │   │   ├── om.svg
│   │   │   │   │   ├── pa.svg
│   │   │   │   │   ├── pe.svg
│   │   │   │   │   ├── pf.svg
│   │   │   │   │   ├── pg.svg
│   │   │   │   │   ├── ph.svg
│   │   │   │   │   ├── pk.svg
│   │   │   │   │   ├── pl.svg
│   │   │   │   │   ├── pm.svg
│   │   │   │   │   ├── pn.svg
│   │   │   │   │   ├── pr.svg
│   │   │   │   │   ├── ps.svg
│   │   │   │   │   ├── pt.svg
│   │   │   │   │   ├── pw.svg
│   │   │   │   │   ├── py.svg
│   │   │   │   │   ├── qa.svg
│   │   │   │   │   ├── re.svg
│   │   │   │   │   ├── ro.svg
│   │   │   │   │   ├── rs.svg
│   │   │   │   │   ├── ru.svg
│   │   │   │   │   ├── rw.svg
│   │   │   │   │   ├── sa.svg
│   │   │   │   │   ├── sb.svg
│   │   │   │   │   ├── sc.svg
│   │   │   │   │   ├── sd.svg
│   │   │   │   │   ├── se.svg
│   │   │   │   │   ├── sg.svg
│   │   │   │   │   ├── sh.svg
│   │   │   │   │   ├── si.svg
│   │   │   │   │   ├── sj.svg
│   │   │   │   │   ├── sk.svg
│   │   │   │   │   ├── sl.svg
│   │   │   │   │   ├── sm.svg
│   │   │   │   │   ├── sn.svg
│   │   │   │   │   ├── so.svg
│   │   │   │   │   ├── sr.svg
│   │   │   │   │   ├── ss.svg
│   │   │   │   │   ├── st.svg
│   │   │   │   │   ├── sv.svg
│   │   │   │   │   ├── sx.svg
│   │   │   │   │   ├── sy.svg
│   │   │   │   │   ├── sz.svg
│   │   │   │   │   ├── tc.svg
│   │   │   │   │   ├── td.svg
│   │   │   │   │   ├── tf.svg
│   │   │   │   │   ├── tg.svg
│   │   │   │   │   ├── th.svg
│   │   │   │   │   ├── tj.svg
│   │   │   │   │   ├── tk.svg
│   │   │   │   │   ├── tl.svg
│   │   │   │   │   ├── tm.svg
│   │   │   │   │   ├── tn.svg
│   │   │   │   │   ├── to.svg
│   │   │   │   │   ├── tr.svg
│   │   │   │   │   ├── tt.svg
│   │   │   │   │   ├── tv.svg
│   │   │   │   │   ├── tw.svg
│   │   │   │   │   ├── tz.svg
│   │   │   │   │   ├── ua.svg
│   │   │   │   │   ├── ug.svg
│   │   │   │   │   ├── um.svg
│   │   │   │   │   ├── un.svg
│   │   │   │   │   ├── unknown.svg
│   │   │   │   │   ├── us.svg
│   │   │   │   │   ├── uy.svg
│   │   │   │   │   ├── uz.svg
│   │   │   │   │   ├── va.svg
│   │   │   │   │   ├── vc.svg
│   │   │   │   │   ├── ve.svg
│   │   │   │   │   ├── vg.svg
│   │   │   │   │   ├── vi.svg
│   │   │   │   │   ├── vn.svg
│   │   │   │   │   ├── vu.svg
│   │   │   │   │   ├── wf.svg
│   │   │   │   │   ├── ws.svg
│   │   │   │   │   ├── ye.svg
│   │   │   │   │   ├── yt.svg
│   │   │   │   │   ├── za.svg
│   │   │   │   │   ├── zm.svg
│   │   │   │   │   └── zw.svg
│   │   │   │   ├── ga.svg
│   │   │   │   ├── google.svg
│   │   │   │   ├── googleplay.svg
│   │   │   │   ├── grubhub.png
│   │   │   │   ├── gtm.svg
│   │   │   │   ├── iheartradio.svg
│   │   │   │   ├── index.php
│   │   │   │   ├── instagram.png
│   │   │   │   ├── instagram.svg
│   │   │   │   ├── itunes.svg
│   │   │   │   ├── joox.svg
│   │   │   │   ├── landing.png
│   │   │   │   ├── linkedin.png
│   │   │   │   ├── linkedin.svg
│   │   │   │   ├── logo.png
│   │   │   │   ├── maintenance.svg
│   │   │   │   ├── map.png
│   │   │   │   ├── maps.svg
│   │   │   │   ├── messenger.png
│   │   │   │   ├── mixcloud.svg
│   │   │   │   ├── netflix.svg
│   │   │   │   ├── opensea.svg
│   │   │   │   ├── opentable.svg
│   │   │   │   ├── os/
│   │   │   │   │   ├── android.svg
│   │   │   │   │   ├── blackberry.svg
│   │   │   │   │   ├── chrome.svg
│   │   │   │   │   ├── index.php
│   │   │   │   │   ├── ipad.svg
│   │   │   │   │   ├── iphone.svg
│   │   │   │   │   ├── linux.svg
│   │   │   │   │   ├── mac.svg
│   │   │   │   │   ├── ubuntu.svg
│   │   │   │   │   ├── unknown.svg
│   │   │   │   │   └── windows.svg
│   │   │   │   ├── pandora.svg
│   │   │   │   ├── paypal.svg
│   │   │   │   ├── pinterest.svg
│   │   │   │   ├── playstore.svg
│   │   │   │   ├── profile.png
│   │   │   │   ├── profiles.png
│   │   │   │   ├── qrcodes.png
│   │   │   │   ├── quora.svg
│   │   │   │   ├── reddit.svg
│   │   │   │   ├── roundedlines.svg
│   │   │   │   ├── shapes.svg
│   │   │   │   ├── shortcuts.svg
│   │   │   │   ├── slack.svg
│   │   │   │   ├── snapchat.svg
│   │   │   │   ├── soundcloud.svg
│   │   │   │   ├── spotify.svg
│   │   │   │   ├── stop.svg
│   │   │   │   ├── stubhub.svg
│   │   │   │   ├── tawkto.svg
│   │   │   │   ├── telegram.png
│   │   │   │   ├── threads.svg
│   │   │   │   ├── ticketmaster.svg
│   │   │   │   ├── tidal.svg
│   │   │   │   ├── tidio.svg
│   │   │   │   ├── tiktok.png
│   │   │   │   ├── tiktok.svg
│   │   │   │   ├── twitch.svg
│   │   │   │   ├── twitter.png
│   │   │   │   ├── twitter.svg
│   │   │   │   ├── typeform.svg
│   │   │   │   ├── unknown.svg
│   │   │   │   ├── user.png
│   │   │   │   ├── vimeo.svg
│   │   │   │   ├── vkmusic.svg
│   │   │   │   ├── walmart.png
│   │   │   │   ├── whatsapp.svg
│   │   │   │   ├── wolt.png
│   │   │   │   ├── wp.svg
│   │   │   │   ├── x.svg
│   │   │   │   ├── yandexmusic.svg
│   │   │   │   ├── yelp.png
│   │   │   │   ├── youtube.png
│   │   │   │   ├── youtube.svg
│   │   │   │   ├── youtubemusic.svg
│   │   │   │   ├── zapier.svg
│   │   │   │   └── zoom.svg
│   │   │   ├── js/
│   │   │   │   ├── back-to-top.js
│   │   │   │   ├── calculator-export.js
│   │   │   │   ├── favorites.js
│   │   │   │   ├── floating-calculator.js
│   │   │   │   ├── header.js
│   │   │   │   ├── main.js
│   │   │   │   ├── quest-tracker.js
│   │   │   │   ├── scientific-calculator.js
│   │   │   │   ├── smart_reader.js
│   │   │   │   └── tilt.js
│   │   │   └── resources/
│   │   │       ├── achievements/
│   │   │       │   ├── chief_engineer.png
│   │   │       │   ├── intern.png
│   │   │       │   ├── rank_01_intern.png
│   │   │       │   ├── rank_02_surveyor.png
│   │   │       │   ├── rank_03_supervisor.png
│   │   │       │   └── rank_04_assistant.png
│   │   │       ├── avatars/
│   │   │       │   ├── avatar_anon_helmet.webp
│   │   │       │   ├── avatar_core_female_classic.webp
│   │   │       │   ├── avatar_core_male_classic.webp
│   │   │       │   ├── avatar_core_male_glasses.webp
│   │   │       │   ├── avatar_core_male_hoodie.webp
│   │   │       │   ├── avatar_core_male_masked.webp
│   │   │       │   ├── avatar_female_rank_01_intern.webp
│   │   │       │   ├── avatar_female_rank_02_surveyor.webp
│   │   │       │   ├── avatar_female_rank_03_supervisor.webp
│   │   │       │   ├── avatar_female_rank_04_assistant.webp
│   │   │       │   ├── avatar_female_rank_05_senior.webp
│   │   │       │   ├── avatar_female_rank_06_manager.webp
│   │   │       │   ├── avatar_female_rank_07_chief.webp
│   │   │       │   ├── avatar_male_rank_01_intern.webp
│   │   │       │   ├── avatar_male_rank_02_surveyor.webp
│   │   │       │   ├── avatar_male_rank_03_supervisor.webp
│   │   │       │   ├── avatar_male_rank_04_assistant.webp
│   │   │       │   ├── avatar_male_rank_05_senior.webp
│   │   │       │   ├── avatar_male_rank_06_manager.webp
│   │   │       │   ├── avatar_male_rank_07_chief.webp
│   │   │       │   ├── avatar_mascot_brick_bot.webp
│   │   │       │   ├── avatar_mascot_cone_buddy.webp
│   │   │       │   ├── avatar_mascot_robo_theo.webp
│   │   │       │   ├── avatar_role_draftsman.webp
│   │   │       │   ├── avatar_role_site_trainee.webp
│   │   │       │   ├── avatar_role_structural_nerd.webp
│   │   │       │   └── avatar_role_survey_student.webp
│   │   │       ├── buildings/
│   │   │       │   ├── saw_farm.webp
│   │   │       │   └── shop.webp
│   │   │       ├── currency/
│   │   │       │   ├── coin.webp
│   │   │       │   └── coin_bundle.webp
│   │   │       ├── frames/
│   │   │       │   ├── frame_shop_01_hazard.webp
│   │   │       │   ├── frame_shop_02_blueprint.webp
│   │   │       │   └── frame_shop_03_gold.webp
│   │   │       ├── materials/
│   │   │       │   ├── bbcement.webp
│   │   │       │   ├── brick_bundle.webp
│   │   │       │   ├── brick_single.webp
│   │   │       │   ├── log.webp
│   │   │       │   ├── log_bundle.webp
│   │   │       │   ├── plank.webp
│   │   │       │   ├── plank_bundle.webp
│   │   │       │   ├── riversand.webp
│   │   │       │   ├── steel.webp
│   │   │       │   └── steel_bundle.webp
│   │   │       └── ranks/
│   │   │           ├── rank_01_intern.webp
│   │   │           ├── rank_02_surveyor.webp
│   │   │           ├── rank_03_supervisor.webp
│   │   │           ├── rank_04_assistant.webp
│   │   │           ├── rank_05_senior.webp
│   │   │           ├── rank_06_manager.webp
│   │   │           └── rank_07_chief.webp
│   │   ├── theme.json
│   │   └── views/
│   │       ├── auth/
│   │       │   ├── 2fa-verify.php
│   │       │   ├── forgot.php
│   │       │   ├── login.php
│   │       │   ├── logout.php
│   │       │   ├── register.php
│   │       │   ├── report.php
│   │       │   ├── reset.php
│   │       │   └── verify.php
│   │       ├── blog/
│   │       │   ├── index.php
│   │       │   └── show.php
│   │       ├── bounty/
│   │       │   ├── create.php
│   │       │   ├── dashboard.php
│   │       │   ├── index.php
│   │       │   └── show.php
│   │       ├── calculator/
│   │       │   ├── category.php
│   │       │   ├── converter.php
│   │       │   ├── dashboard-scientific.php
│   │       │   ├── index.php
│   │       │   └── scientific.php
│   │       ├── calculators/
│   │       │   ├── cash_flow_analysis.php
│   │       │   ├── chemistry/
│   │       │   │   ├── gas_laws.php
│   │       │   │   ├── molar_mass.php
│   │       │   │   └── ph.php
│   │       │   ├── civil/
│   │       │   ├── datetime/
│   │       │   │   ├── adder.php
│   │       │   │   ├── duration.php
│   │       │   │   ├── nepali.php
│   │       │   │   ├── time.php
│   │       │   │   └── workdays.php
│   │       │   ├── equipment_hourly_rate.php
│   │       │   ├── finance/
│   │       │   │   ├── compound_interest.php
│   │       │   │   ├── investment.php
│   │       │   │   ├── loan.php
│   │       │   │   ├── mortgage.php
│   │       │   │   ├── roi.php
│   │       │   │   └── salary.php
│   │       │   ├── health/
│   │       │   │   ├── bmi.php
│   │       │   │   ├── bmr.php
│   │       │   │   ├── body_fat.php
│   │       │   │   └── calories.php
│   │       │   ├── item_rate_analysis.php
│   │       │   ├── labor_rate_analysis.php
│   │       │   ├── math/
│   │       │   │   ├── age.php
│   │       │   │   ├── area.php
│   │       │   │   ├── bmi.php
│   │       │   │   ├── discount.php
│   │       │   │   ├── fraction.php
│   │       │   │   ├── gcd_lcm.php
│   │       │   │   ├── linear_equations.php
│   │       │   │   ├── loan.php
│   │       │   │   ├── percentage.php
│   │       │   │   ├── quadratic.php
│   │       │   │   ├── right_triangle.php
│   │       │   │   ├── statistics.php
│   │       │   │   ├── surface_area.php
│   │       │   │   ├── trigonometry.php
│   │       │   │   └── volume.php
│   │       │   ├── nepali.php
│   │       │   ├── npv_irr_analysis.php
│   │       │   ├── physics/
│   │       │   │   ├── energy.php
│   │       │   │   ├── force.php
│   │       │   │   ├── ohms_law.php
│   │       │   │   └── velocity.php
│   │       │   └── statistics/
│   │       │       ├── basic.php
│   │       │       ├── dispersion.php
│   │       │       └── probability.php
│   │       ├── contact.php
│   │       ├── dashboard.php
│   │       ├── developer/
│   │       │   ├── index.php
│   │       │   └── playground.php
│   │       ├── errors/
│   │       │   ├── 404.php
│   │       │   └── 500.php
│   │       ├── estimation/
│   │       │   ├── rates_manager.php
│   │       │   └── sheet.php
│   │       ├── help/
│   │       │   ├── article.php
│   │       │   ├── index.php
│   │       │   ├── index_complex.php
│   │       │   ├── index_simple.php
│   │       │   └── search.php
│   │       ├── home/
│   │       │   ├── contact.php
│   │       │   ├── maintenance.php
│   │       │   ├── pricing.php
│   │       │   ├── privacy.php
│   │       │   ├── profile.php
│   │       │   └── terms.php
│   │       ├── index.php
│   │       ├── landing/
│   │       │   ├── civil.php
│   │       │   ├── electrical.php
│   │       │   ├── estimation.php
│   │       │   ├── fire.php
│   │       │   ├── hvac.php
│   │       │   ├── management.php
│   │       │   ├── mep.php
│   │       │   ├── plumbing.php
│   │       │   ├── site.php
│   │       │   └── structural.php
│   │       ├── legal/
│   │       │   ├── privacy.php
│   │       │   ├── refund.php
│   │       │   └── terms.php
│   │       ├── library/
│   │       │   ├── index.php
│   │       │   ├── upload.php
│   │       │   └── viewer/
│   │       │       └── pdf.php
│   │       ├── onboarding/
│   │       │   └── index.php
│   │       ├── pages/
│   │       │   └── page.php
│   │       ├── partials/
│   │       │   ├── back-to-top.php
│   │       │   ├── calculator_sidebar.php
│   │       │   ├── floating-calculator.php
│   │       │   ├── footer.php
│   │       │   ├── header.php
│   │       │   ├── project-selector.php
│   │       │   ├── resource_hud.php
│   │       │   ├── theme-helpers.php
│   │       │   └── VersionChecker.php
│   │       ├── payment/
│   │       │   ├── checkout.php
│   │       │   ├── esewa-form.php
│   │       │   ├── failed.php
│   │       │   └── success.php
│   │       ├── projects/
│   │       │   ├── index.php
│   │       │   └── view.php
│   │       ├── quiz/
│   │       │   ├── firms/
│   │       │   │   ├── dashboard.php
│   │       │   │   └── index.php
│   │       │   ├── gamification/
│   │       │   │   ├── battle_pass.php
│   │       │   │   ├── city.php
│   │       │   │   ├── sawmill.php
│   │       │   │   └── shop.php
│   │       │   ├── leaderboard/
│   │       │   │   └── index.php
│   │       │   ├── multiplayer/
│   │       │   │   ├── lobby.php
│   │       │   │   └── menu.php
│   │       │   └── portal/
│   │       │       └── index.php
│   │       ├── report.php
│   │       ├── share/
│   │       │   └── public-view.php
│   │       ├── shared/
│   │       │   ├── calculator-template.php
│   │       │   └── coming_soon.php
│   │       ├── shop/
│   │       │   └── index.php
│   │       └── user/
│   │           ├── 2fa-setup.php
│   │           ├── components/
│   │           │   └── avatar_selector.php
│   │           ├── exports.php
│   │           ├── history.php
│   │           ├── modals/
│   │           │   └── profile-modals.php
│   │           └── profile.php
│   └── email/
│       └── notification.php
├── tools/
│   ├── migration-wizard/
│   │   └── views/
│   ├── optimize_images.php
│   ├── reindex.php
│   └── verify_features.php
├── vendor/
│   ├── abraham/twitteroauth (^3.1)
│   ├── altcha-org/altcha (^1.1)
│   ├── bacon/bacon-qr-code (^2.0)
│   ├── defuse/php-encryption (^2.2)
│   ├── endroid/qr-code (4.6.1)
│   ├── guzzlehttp/guzzle (^7.0)
│   ├── intervention/image (^3.11)
│   ├── jaybizzle/crawler-detect (^1.2)
│   ├── league/csv (^9.0)
│   ├── markrogoyski/math-php (^1.0)
│   ├── maxmind-db/reader (^1.12)
│   ├── mollie/mollie-api-php (^2.71)
│   ├── monolog/monolog (^2.0)
│   ├── mpdf/mpdf (^8.1)
│   ├── nesbot/carbon (^2.0)
│   ├── nikic/fast-route (^1.3)
│   ├── paragonie/random_compat (^9.99)
│   ├── paypal/rest-api-sdk-php (^1.6)
│   ├── phpfastcache/phpfastcache (^8.0)
│   ├── phpmailer/phpmailer (^7.0)
│   ├── phpoffice/phpspreadsheet (^5.3)
│   ├── pragmarx/google2fa (^9.0)
│   ├── ramsey/uuid (^4.7)
│   ├── respect/validation (^2.2)
│   ├── sentry/sentry (^4.18)
│   ├── setasign/fpdf (^1.8)
│   ├── stripe/stripe-php (^15.10)
│   ├── symfony/cache (^5.4)
│   ├── symfony/validator (^5.4)
│   ├── tecnickcom/tcpdf (^6.6)
│   └── vlucas/phpdotenv (^5.5)
├── verify_footer.php
└── version.json
```
