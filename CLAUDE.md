# Claude Code — laravel-spatie-event-sourcing

## What this repo is

A Claude Code skill that generates event-sourced domain code for Laravel using `spatie/laravel-event-sourcing`. The repo is packaged as an installable plugin marketplace; the skill is the single source of truth and lives in `plugins/laravel-spatie-event-sourcing/skills/laravel-spatie-event-sourcing/`.

## Repo layout

```
.claude-plugin/
  marketplace.json      — Marketplace catalog (marketplace name: albertoarena)
plugins/laravel-spatie-event-sourcing/
  .claude-plugin/
    plugin.json         — Plugin manifest (name, description, version, author)
  skills/laravel-spatie-event-sourcing/   — canonical skill source
    SKILL.md            — Main skill file (frontmatter + instructions)
    references/         — Design heuristics, ADR template, TDD patterns, API cheatsheet
    assets/templates/   — .stub files for code generation
    scripts/            — verify-setup.sh (project bootstrap)
examples/orders/        — Worked example (order management domain)
docs/                   — Human-readable documentation
```

## Key conventions

- The skill follows a **two-gate workflow**: Gate 1 (Design/ADR) must be approved before Gate 2 (Implementation) begins.
- Tests are always written FIRST (TDD).
- Generated code goes under `app/Domain/<Context>/` in the target project.
- Events use past-tense names (`OrderPlaced`, not `OrderUpdated`).
- One reactor per side effect.
- Projectors are sync by default.

## Development

- When editing the skill, keep SKILL.md under 500 lines.
- Reference files provide detail that doesn't need to be in the main prompt.
- Stub templates use `{{ placeholder }}` syntax.
- Stub templates and reference file examples must stay in sync. When updating test patterns in `references/tdd-patterns.md`, update the corresponding `.stub` files in `assets/templates/`, and vice versa.

## Releases

- Before tagging a release, run `scripts/build-skill.sh` to regenerate `laravel-spatie-event-sourcing.skill` from the canonical skill directory under `plugins/`. The committed `.skill` artifact must match the skill contents at tag time.
- Bump `version` in `plugins/laravel-spatie-event-sourcing/.claude-plugin/plugin.json` to match the release tag so `/plugin install` users get update control.
- Primary install path is the plugin marketplace (`/plugin install`); the `.skill` archive is the manual fallback.

## Changelog

- Every major change (new features, breaking changes, significant fixes) must be tracked in `CHANGELOG.md`.
- Follow the [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) format.
- Add entries under `[Unreleased]` as changes are made. Move them to a versioned section on release.

## Git Commit Conventions

### Format

- type: short subject line (max 50 chars)
- Detailed body paragraph explaining what and why (not how).

### Rules

- No Claude attribution - NEVER include "Generated with Claude Code" or "Co-Authored-By: Claude"
- Keep first line under 50 characters
- Use heredoc for multi-line commit messages
