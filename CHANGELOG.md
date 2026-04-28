# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added
- Backlog plan with sub-plans for pending improvements
- Human-readable workflow documentation (`docs/workflow.md`)
- `.skill` package installation option in README

### Fixed
- Pest projector test pattern missing `RefreshDatabase` in `references/tdd-patterns.md`

## [0.1.0] - 2025

### Added
- Two-gate workflow skill (Design ADR, then TDD implementation)
- Reference files: design heuristics, ADR template, TDD patterns, commands pattern, Spatie API cheatsheet
- Stub templates for aggregates, events, commands, handlers, projectors, reactors, read models
- Aggregate test stubs for Pest and PHPUnit
- Bootstrap script (`verify-setup.sh`) for project readiness checks
- Per-project and global installation options
- MIT license
