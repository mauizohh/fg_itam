<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application (**FG IT Asset Management / FG ITAM**) is a Laravel 12 application running on PHP 8.4 (Laragon runtime: `C:\laragon\bin\php\php-8.4.26-Win32-vs17-x64\php.exe`). Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` (or inspect `composer.json` / `vendor/composer/installed.json`) to list direct dependencies with versions (`laravel/framework` ^11.0/^12.0, `livewire/livewire` ^3.5, `wireui/wireui` ^2.0/^2.1, `barryvdh/laravel-dompdf` ^2.2/^3.0, `laravel/pint`, `phpunit/phpunit`).
- JS packages: check `package.json` for the installed versions (`tailwindcss` ^3.4.0, `vite` ^5.0, `@tailwindcss/forms`, `axios`).

## Skills Activation

If this project has domain-specific skills available in `**/skills/**`, you MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one (e.g., `resources/views/components/stat-card.blade.php`, `resources/views/components/table-section.blade.php`, WireUI 2 components).
- **FG ITAM Domain Conventions**:
  - Database tables use the `fg_itam_` prefix in the shared `admidio` MySQL database (plus the `fg_helpdesk_assets` bridge table shared with `fg-ithd`).
  - `App\Models\Asset` uses a non-incrementing string primary key formatted as `IT-{DEV}-{YEAR}-{5-DIGIT}` (validated and generated via `App\Services\AssetIdGeneratorService`) and automatically syncs to `fg_helpdesk_assets` in `Asset::booted()`.
  - Record asset and administrative state changes via `App\Services\AuditLogService::record(...)`.
  - UI pages are full-page Livewire 3 components under `app/Livewire/` rendered with `layouts.app` (`resources/views/layouts/app.blade.php`), WireUI 2, and the FG Crimson theme (`#a92d31` / `#8f2428`).

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Database Protection & Accidental Data Loss Prevention (CRITICAL)

- **Mandatory User Confirmation Before Dropping/Refreshing**: You MUST explicitly ask the user and receive approval BEFORE executing any action, SQL statement, script, or command that deletes data, drops tables, truncates tables, or refreshes the database.
- **Destructive Commands Prohibited Without Explicit Permission**: NEVER run commands like `php artisan migrate:fresh`, `php artisan migrate:reset`, `php artisan migrate:refresh`, `php artisan db:wipe`, or custom commands/seeders that drop, truncate, or wipe tables without prior, explicit user approval.
- **Isolated Test Database (MANDATORY)**: Every test must use a copy of the database. Run tests on the copied database only. Configure an isolated test database name (e.g., `admidio_test` in `phpunit.xml` or `.env.testing`) before creating or running any test to ensure the primary database is never touched or modified.
- The local `admidio` database is shared with `fg-ithd` (`C:\Users\Mauizoh\fg-ithd`) and contains live helpdesk records, user credentials, configuration, `fg_helpdesk_assets`, and ITAM asset records (`fg_itam_*`) that are not tracked in Git. Always protect existing database data from being wiped or overwritten.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get an "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- When Laravel Boost MCP is enabled for this workspace (`php artisan boost:mcp`), prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs (local Laragon host: `http://fg-itam.test`). Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `livewire 3 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open `@.ai/rules/index.md` (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`; use `C:\laragon\bin\php\php-8.4.26-Win32-vs17-x64\php.exe artisan ...` if `php` is not on `PATH`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=assets`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'App\Models\Asset::where("status", "Available")->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention (`routes/api.php` currently serves `/api/v1/*` JSON responses via `App\Http\Controllers\Api\AssetApiController`).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` (when Boost MCP is available) for version-specific Laravel documentation and updated code examples.
- This project uses the streamlined Laravel 11+ structure: register middleware, exceptions, and routing in `bootstrap/app.php` and service providers in `bootstrap/providers.php`. There is no `app/Http/Kernel.php` or `app/Console/Kernel.php`, and commands in `app/Console/Commands/` auto-register.

## Database

- CRITICAL: MUST obtain explicit user approval before running any command, migration, or SQL query that drops, truncates, or refreshes tables (e.g., `migrate:fresh`, `migrate:reset`, `migrate:refresh`, `db:wipe`).
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== livewire/core rules ===

# Livewire

- Livewire allows you to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill (when available) for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- **Database Isolation for Testing (CRITICAL)**: Every test run must use a copy of the database. Run tests on the copied database only. Ensure an isolated test database name (e.g., `DB_DATABASE=admidio_test` in `phpunit.xml` or `.env.testing`) is configured before running any test so the primary `admidio` database is never touched or affected.
- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
</laravel-boost-guidelines>
