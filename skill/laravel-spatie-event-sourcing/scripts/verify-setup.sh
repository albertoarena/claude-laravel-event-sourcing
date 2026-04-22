#!/usr/bin/env bash
#
# verify-setup.sh — Checks that a Laravel project is ready for Spatie event sourcing.
# Run from the Laravel project root.
#
# Exit codes:
#   0 — All checks passed
#   1 — One or more checks failed (fix commands printed to stdout)

set -euo pipefail

ERRORS=0

# 0. Check PHP version (spatie/laravel-event-sourcing v7 requires PHP 8.2+)
PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;' 2>/dev/null || echo "0.0")
PHP_MAJOR=$(echo "$PHP_VERSION" | cut -d. -f1)
PHP_MINOR=$(echo "$PHP_VERSION" | cut -d. -f2)

if [ "$PHP_MAJOR" -lt 8 ] || { [ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 2 ]; }; then
    echo "MISSING: PHP 8.2+ required (found $PHP_VERSION)"
    echo "  Fix: Install PHP 8.2 or later"
    ERRORS=$((ERRORS + 1))
else
    echo "OK: PHP $PHP_VERSION"
fi

# 1. Check spatie/laravel-event-sourcing is in composer.json
if [ ! -f composer.json ]; then
    echo "ERROR: No composer.json found. Are you in a Laravel project root?"
    exit 1
fi

if ! grep -q '"spatie/laravel-event-sourcing"' composer.json; then
    echo "MISSING: spatie/laravel-event-sourcing not found in composer.json"
    echo "  Fix: composer require spatie/laravel-event-sourcing"
    ERRORS=$((ERRORS + 1))
fi

# 2. Check that stored_events and snapshots migrations have been published
MIGRATION_DIR="database/migrations"
if [ -d "$MIGRATION_DIR" ]; then
    if ! ls "$MIGRATION_DIR"/*create_stored_events_table* 1>/dev/null 2>&1; then
        echo "MISSING: stored_events migration not found"
        echo "  Fix: php artisan vendor:publish --provider=\"Spatie\EventSourcing\EventSourcingServiceProvider\" --tag=\"event-sourcing-migrations\""
        ERRORS=$((ERRORS + 1))
    fi
else
    echo "MISSING: database/migrations directory not found"
    ERRORS=$((ERRORS + 1))
fi

# 3. Check config/event-sourcing.php is published
if [ ! -f config/event-sourcing.php ]; then
    echo "MISSING: config/event-sourcing.php not published"
    echo "  Fix: php artisan vendor:publish --provider=\"Spatie\EventSourcing\EventSourcingServiceProvider\" --tag=\"event-sourcing-config\""
    ERRORS=$((ERRORS + 1))
fi

# 4. Check test framework
if grep -q '"pestphp/pest"' composer.json; then
    echo "OK: Pest detected"
elif grep -q '"phpunit/phpunit"' composer.json; then
    echo "OK: PHPUnit detected (Pest is recommended but PHPUnit works fine)"
else
    echo "MISSING: No test framework found"
    echo "  Fix: composer require pestphp/pest --dev"
    ERRORS=$((ERRORS + 1))
fi

if [ "$ERRORS" -gt 0 ]; then
    echo ""
    echo "$ERRORS issue(s) found. Fix them and re-run this check."
    exit 1
else
    echo "All checks passed. Ready for event sourcing!"
    exit 0
fi
