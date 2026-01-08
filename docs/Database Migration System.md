# Database Migration System: Execution Flow and Error Handling

## Overview

This document explains the complete database migration architecture, covering production and development runners, migration discovery logic, class execution patterns, error handling strategies, and supporting infrastructure such as the database singleton. It is designed to give developers a detailed understanding of how migrations are orchestrated, how failures are managed, and best practices for writing robust migrations.

## 1. Migration Runners

### 1.1 Production Runner (`database/migrate.php`)
- **Bootstrap**: Loads application bootstrap (`require_once app/bootstrap.php`) enabling autoloading and configuration (`migrate.php`@1-4).
- **Migration List**: Hardcoded array of migration filenames in desired order, ensuring deterministic execution (`migrate.php`@12-32).
- **Database Connection**: Obtains PDO via `Database::getInstance()->getPdo()` (singleton) (`migrate.php`@36-40).
- **Execution Loop**:
  1. Require migration file (`migrate.php`@45-47).
  2. Convert filename (e.g., `018_create_complete_system_tables.php`) to PascalCase class name by stripping numeric prefix, replacing underscores with spaces, and capitalizing (`migrate.php`@50-53).
  3. Instantiate migration class and reflect `up` method (`migrate.php`@56-63).
  4. Call `up($pdo)` if method expects parameter; otherwise call `up()` (supports legacy migrations).
- **Error Handling**: Per-migration `try/catch` increments `failed` counter and logs error without halting entire run (`migrate.php`@73-79).
- **Reporting**: Outputs summary, warns if failures occurred, otherwise prints success message (`migrate.php`@91-103).

### 1.2 Generic Discovery Runner (`database/run_migration.php`)
- **Purpose**: Automatically discovers migrations without maintaining manual list; ideal for development or continuing migrations.
- **Steps**:
  1. Acquire PDO from singleton (`run_migration.php`@12-16).
  2. Discover all `*.php` files in `database/migrations` and sort them (`run_migration.php`@18-24).
  3. For each file, read content to detect if class-based (`preg_match('/class\s+(\w+)/')`) (`run_migration.php`@46-57).
  4. Require file and resolve class name with namespace fallback (`App\Migrations`) if necessary (`run_migration.php`@62-75).
  5. Instantiate migration and call `up($pdo)` (expects modern migrations that accept PDO) (`run_migration.php`@85-89).
  6. Track success/failure counts and exit with non-zero status if failures occur to integrate with CI/CD (`run_migration.php`@109-122).

### 1.3 Specialized Runners
- **run_new_migrations.php**: Executes curated list of new migrations for certain releases, verifying success by listing tables (`run_new_migrations.php`@16-50).
- **run_email_migrations.php**: Handles email subsystem migrations with duplicate table detection; warns if tables already exist (`run_email_migrations.php`@48-78).
- **simple_migrate.php**: Lightweight script for seeding calculator data; uses direct PDO (not singleton) and handles duplicates gracefully (`app/db/migrations/simple_migrate.php`).

## 2. Migration Class Patterns

### 2.1 Parameter Handling
- Modern migrations declare `up($pdo = null)` allowing both runner-provided PDO and singleton fallback (`018_create_complete_system_tables.php`@2-9).
- Legacy migrations may have parameterless `up()` and internally create their own PDO; reflection logic accommodates both.

### 2.2 Idempotent Schema Changes
- Example: `019_enhance_settings_table.php` queries table schema (`DESCRIBE settings`) to check for existing columns before altering (`019_enhance_settings_table.php`@15-24).
- Pattern encourages idempotency, enabling safe re-runs and avoiding duplicate column errors.

### 2.3 Transactions
- Complex migrations (e.g., `027_create_enterprise_quiz_tables.php`) wrap DDL/DML in transactions using `$pdo->beginTransaction()` and commit/rollback logic (`027_create_enterprise_quiz_tables.php`@27-214).
- On exceptions, transaction is rolled back, preventing partial schema updates.

### 2.4 Error Propagation
- Migrations log descriptive errors and re-throw exceptions so runners can report failure (e.g., `023_set_default_logo_favicon.php`@86-90).

## 3. Database Singleton Infrastructure

### 3.1 Configuration Loading
- `App\Core\Database` loads `config/database.php`, validates required keys (`host`, `database`, `username`) and builds DSN (`Database.php`@18-37).

### 3.2 PDO Creation
- Creates PDO with configured username/password, sets `ERRMODE_EXCEPTION`, default fetch mode `FETCH_ASSOC`, disables emulated prepares (`Database.php`@40-49).
- Wraps connection errors in generic Exception with enriched message (`Database.php`@51-56).
- Singleton ensures single PDO instance reused across migrations, reducing connection overhead.

## 4. Error Handling Strategies

### 4.1 Top-Level Protection
- Production runner wraps entire migration process in top-level `try/catch` to handle fatal errors gracefully (`migrate.php`@8-11, 84-90).

### 4.2 Per-Migration Isolation
- Each migration executed within `try/catch`; failure increments `failed` count but does not abort iteration (`migrate.php`@74-78).
- Generic runner also catches per-migration exceptions and tracks success/failure (`run_migration.php`@93-107).

### 4.3 Duplicate Detection
- Specialized runners check for already existing tables and log informational message rather than failing (e.g., `run_email_migrations.php`@59-63).
- Migration scripts can implement similar logic by introspecting schema before executing DDL.

### 4.4 Transactions & Rollbacks
- Migrations performing multiple operations often call `$pdo->beginTransaction()` and rollback upon error (`027_create_enterprise_quiz_tables.php`@27-214).
- Runner-level transaction management can be added if executing multiple migrations atomically, though currently each migration is responsible for its own transactions.

### 4.5 Exit Codes
- Runners return exit code `1` when failures occur, enabling automation tools (CI/CD) to detect migration issues (`run_migration.php`@118-121, `run_new_migrations.php`@62-66).

### 4.6 Debug Output
- Specialized scripts output stack traces for debugging (e.g., `run_new_migrations.php`@62-66), aiding rapid diagnosis.

## 5. Migration Execution Flow (End-to-End)

1. **Bootstrap**: Environment prepared and configuration loaded.
2. **Connection**: PDO acquired via singleton or direct connection for specialized scripts.
3. **Discovery**: Migration list determined (hardcoded array, glob search, or curated list).
4. **Class Loading**: Files included; class names derived by transforming filenames or using regex detection.
5. **Instantiation**: Migration objects created; reflection used to adapt to signature differences.
6. **Execution**: `up()` executed with or without PDO parameter; optional transactions manage atomicity.
7. **Error Handling**: Exceptions caught; transactions rolled back; failures logged.
8. **Reporting**: Success/failure counts displayed; exit code set for automation.

## 6. Best Practices for Writing Migrations

1. **Idempotency**: Always check schema state before altering; migrations should be safe to re-run.
2. **Optional PDO Parameter**: Accept `$pdo = null` and fallback to singleton for compatibility with all runners.
3. **Transactions**: Use transactions for multi-step operations affecting multiple tables.
4. **Logging**: Provide clear output for successes and failures to aid troubleshooting.
5. **Error Propagation**: Re-throw exceptions after cleanup so runners can detect failure.
6. **Rollback Strategy**: Ensure rollback occurs on errors to maintain database consistency.
7. **Testing**: Run migrations locally using discovery runner and curated runner to ensure compatibility.

## 7. Performance Considerations

- **Batch Execution**: Runners execute migrations sequentially; consider grouping related migrations where appropriate.
- **Connection Reuse**: Singleton prevents redundant connection creation, reducing overhead.
- **Large Datasets**: Data migrations involving large tables should paginate or chunk operations.
- **Indexes & Constraints**: Temporarily disabling constraints or indexes can improve performance but must be handled carefully with rollback plans.

## 8. Future Enhancements

1. **Down Migrations**: Implement `down()` methods for reversible migrations and add runner support for rollbacks.
2. **Migration Metadata Table**: Track executed migrations in database instead of relying on arrays/glob; prevents re-execution and supports versioning.
3. **CLI Interface**: Provide command-line tool (e.g., `php artisan migrate` style) for richer control and status output.
4. **Dependency Resolution**: Allow migrations to declare dependencies to ensure prerequisite migrations run first.
5. **Dry Run Mode**: Preview SQL statements without executing to review planned changes.
6. **Multiple Environments**: Support environment-specific configuration (dev, staging, production) for selective migrations.
7. **Logging Integration**: Route migration logs to centralized logging/monitoring for production observability.
8. **Parallel Execution**: Evaluate controlled parallelism for long-running migrations where operations are independent.

## 9. Troubleshooting Tips

- **Check Logs**: Review runner output; specialized runners provide stack traces and duplicate warnings.
- **Verify Connection**: Ensure `config/database.php` matches target environment and credentials are valid.
- **Schema Validation**: Use `SHOW TABLES` or `DESCRIBE table` to confirm expected structure after migration.
- **Rollback Manually**: If no `down()` method exists, manually revert changes in SQL before rerunning migration.
- **Inspect Hash/Name**: Confirm class name matches filename transformation; misnamed classes will fail to instantiate.
- **Environment Isolation**: Run migrations in staging before production to catch errors early.

## Conclusion

The database migration system balances backward compatibility with modern best practices. Multiple runners accommodate different workflows—from tightly controlled production deployments to automated discovery in development. By leveraging reflection, transactions, and robust error handling, the system executes migrations safely while providing clear feedback. Adhering to the documented patterns and continually enhancing the tooling will keep the migration workflow reliable and maintainable as the application evolves.
