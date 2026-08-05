# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

## [1.1.1] - 2026-08-05

### Added
- Plugin manifest metadata for the official directory listing: `displayName`, `homepage`, `repository`, `license`, `keywords`, and `$schema`
- Plugin-level `README.md` under `plugins/laravel-spatie-event-sourcing/`

## [1.1.0] - 2026-08-05

### Added
- Plugin marketplace packaging: `.claude-plugin/marketplace.json` and `plugins/laravel-spatie-event-sourcing/.claude-plugin/plugin.json`, enabling one-command install via `/plugin marketplace add` + `/plugin install`

### Changed
- Skill source moved to `plugins/laravel-spatie-event-sourcing/skills/laravel-spatie-event-sourcing/` (now the single source of truth); `scripts/build-skill.sh` builds the `.skill` archive from the new location
- README: lead with the one-command plugin install; copy-folder and `.skill` routes are now documented as fallbacks
- CLAUDE.md and CONTRIBUTING.md: updated repo layout and paths for the plugin structure

## [1.0.0] - 2026-05-19

### Added
- Compatibility matrix in README (PHP 8.2+, Laravel 10.x–13.x, Spatie ES ^7.0)
- `scripts/build-skill.sh` — reproducible build of the `.skill` archive from `skill/`
- GitHub Actions: `shellcheck.yml` (lint `*.sh` on PRs and pushes to `main`)
- GitHub Actions: `release.yml` (build and attach `.skill` to GitHub releases on `v*` tags)
- `CONTRIBUTING.md` — contributor guide covering workflow, conventions, commit/changelog rules, releases, and local checks

### Changed
- README: added Release, License, Code size, and Claude Code Skill badges under the H1
- `verify-setup.sh`: corrected the misleading PHP version comment (Spatie ES v7 requires PHP ^8.0; this skill deliberately targets 8.2+)

## [0.2.0] - 2026-04-28

### Added
- Orders worked example (`examples/orders/`) with ADR, 24 generated files, and walkthrough
- Stub templates: `command-bus.stub`, `command-handler-bus.stub`, `projector.pest.stub`, `projector.phpunit.stub`, `reactor.pest.stub`, `reactor.phpunit.stub`
- Migration structure guidance in SKILL.md Gate 2 (UUID primary key, no auto-increment, timestamps)
- Stub/reference sync rule in CLAUDE.md
- Human-readable workflow documentation (`docs/workflow.md`)
- `.skill` package installation option in README
- CHANGELOG.md

### Fixed
- Pest projector test pattern missing `RefreshDatabase` in `references/tdd-patterns.md`
- `verify-setup.sh`: added missing snapshots migration check, consistent success output for all checks, made script executable
- `read-model.stub` missing UUID primary key configuration

## [0.1.0] - 2025

### Added
- Two-gate workflow skill (Design ADR, then TDD implementation)
- Reference files: design heuristics, ADR template, TDD patterns, commands pattern, Spatie API cheatsheet
- Stub templates for aggregates, events, commands, handlers, projectors, reactors, read models
- Aggregate test stubs for Pest and PHPUnit
- Bootstrap script (`verify-setup.sh`) for project readiness checks
- Per-project and global installation options
- MIT license
