# Project setup checks

Run these checks from the Laravel project root on first invocation. They are
read-only: inspect files and run `php -r`, never install packages, publish
vendor files, or run migrations.

Report each check as `OK: …` or `MISSING: …` followed by its `Fix:` command.
If any check fails, print the fixes and **stop** until the user has applied them.

## 0. PHP 8.2+

Run `php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;'`.

Spatie ES v7 allows PHP ^8.0, but this skill targets PHP 8.2+ deliberately
(modern Laravel norms, matches the supported range in the README). Laravel's
version is not checked; Spatie's composer constraints enforce it at install time.

- Missing or below 8.2 → `MISSING: PHP 8.2+ required (found <version>)`
  - Fix: Install PHP 8.2 or later

## 1. `spatie/laravel-event-sourcing` in `composer.json`

If `composer.json` does not exist, stop: `ERROR: No composer.json found. Are you in a Laravel project root?`

- `"spatie/laravel-event-sourcing"` not in `composer.json` → `MISSING: spatie/laravel-event-sourcing not found in composer.json`
  - Fix: `composer require spatie/laravel-event-sourcing`

## 2. `stored_events` and `snapshots` migrations published

Look in `database/migrations/` for files matching `*create_stored_events_table*`
and `*create_snapshots_table*`.

- `database/migrations/` missing → `MISSING: database/migrations directory not found`
- Either migration missing → `MISSING: <table> migration not found`
  - Fix: `php artisan vendor:publish --provider="Spatie\EventSourcing\EventSourcingServiceProvider" --tag="event-sourcing-migrations"`

## 3. `config/event-sourcing.php` published

- File missing → `MISSING: config/event-sourcing.php not published`
  - Fix: `php artisan vendor:publish --provider="Spatie\EventSourcing\EventSourcingServiceProvider" --tag="event-sourcing-config"`

## 4. Test framework

Check `composer.json`:

- `"pestphp/pest"` present → `OK: Pest detected`
- else `"phpunit/phpunit"` present → `OK: PHPUnit detected (Pest is recommended but PHPUnit works fine)`
- neither → `MISSING: No test framework found`
  - Fix: `composer require pestphp/pest --dev`

## Result

- Any failures → `<n> issue(s) found. Fix them and re-run this check.`
- All pass → `All checks passed. Ready for event sourcing!`
