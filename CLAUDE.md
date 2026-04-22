# Claude Code — laravel-spatie-event-sourcing

## What this repo is

A Claude Code skill that generates event-sourced domain code for Laravel using `spatie/laravel-event-sourcing`. The skill lives in `skill/laravel-spatie-event-sourcing/`.

## Repo layout

```
skill/laravel-spatie-event-sourcing/
  SKILL.md              — Main skill file (frontmatter + instructions)
  references/           — Design heuristics, ADR template, TDD patterns, API cheatsheet
  assets/templates/     — .stub files for code generation
  scripts/              — verify-setup.sh (project bootstrap)
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

## Git Commit Conventions

### Format

- type: short subject line (max 50 chars)
- Detailed body paragraph explaining what and why (not how).

### Rules

- No Claude attribution - NEVER include "Generated with Claude Code" or "Co-Authored-By: Claude"
- Keep first line under 50 characters
- Use heredoc for multi-line commit messages
