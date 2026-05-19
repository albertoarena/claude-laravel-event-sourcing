# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Changed
- README: added Release, License, Code size, and Claude Code Skill badges under the H1

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
