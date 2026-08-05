# Contributing

Thanks for your interest in this Claude Code skill. This document covers the conventions used in the repo so contributions land cleanly.

## Repo layout

```
.claude-plugin/marketplace.json                                 Marketplace catalog
plugins/laravel-spatie-event-sourcing/.claude-plugin/plugin.json  Plugin manifest
plugins/laravel-spatie-event-sourcing/skills/laravel-spatie-event-sourcing/
                                       The skill itself (SKILL.md, references, stubs, scripts)
examples/                              Worked examples
docs/                                  Human-readable documentation
scripts/                               Repo-level scripts (e.g. build-skill.sh)
.github/workflows/                     CI
```

See [`CLAUDE.md`](./CLAUDE.md) for a fuller architectural overview.

## Workflow

1. Fork and branch from `main`.
2. Make your changes (see conventions below).
3. Open a pull request. CI will run `shellcheck` on any `.sh` changes automatically.
4. Update `CHANGELOG.md` under `[Unreleased]` — every notable change must be tracked.

Direct pushes to `main` are reserved for maintainers and should still go through PRs whenever practical.

## Conventions

### Stub / reference sync

Stubs in `assets/templates/*.stub` and the patterns documented in `references/tdd-patterns.md` (and other reference files) **must stay in sync**. If you change a test pattern in a reference file, update the corresponding stub, and vice versa. This is mandated in `CLAUDE.md` and is the kind of drift that breaks code generation silently.

### Generated-code conventions

These are the rules the skill enforces and that any contribution touching them should preserve:

- Events use **past-tense** names (`OrderPlaced`, not `OrderUpdated`).
- One reactor per side effect.
- Projectors are **sync by default**; only opt into queued projectors when there's a clear reason.
- Tests are written **first** (TDD). Stub additions should ship with a matching test pattern.
- Generated code lives under `app/Domain/<Context>/` in the target project.

### SKILL.md size

Keep `plugins/laravel-spatie-event-sourcing/skills/laravel-spatie-event-sourcing/SKILL.md` under 500 lines. Detail belongs in `references/`; the main file is a prompt, not a manual.

### Commit messages

Format:

```
type: short subject line (max 50 chars)

Detailed body paragraph explaining what and why (not how).
```

Rules:

- First line under 50 characters.
- Use a heredoc for multi-line commit messages.
- **Never** include "Generated with Claude Code", "Co-Authored-By: Claude", or any other Claude attribution in commits, PR descriptions, or release notes.

### CHANGELOG

- Follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
- Add entries under `[Unreleased]` as you go.
- On release, the maintainer renames `[Unreleased]` to `[X.Y.Z] - YYYY-MM-DD` and starts a fresh empty `[Unreleased]` section above it.

## Releases (maintainers)

1. Ensure `[Unreleased]` reflects everything in the release.
2. Run `scripts/build-skill.sh` and verify `git status` is clean (no stale `.skill`).
3. Rename `[Unreleased]` → `[X.Y.Z] - YYYY-MM-DD` in `CHANGELOG.md`.
4. Commit, then `git tag -a vX.Y.Z -m "..."` and `git push --tags`.
5. The `release` workflow rebuilds `.skill` and attaches it to the GitHub release automatically.

Pre-release tags (`-rc`, `-beta`, `-alpha` suffix) are auto-marked as pre-releases on GitHub.

## Local checks

Before pushing:

- `scripts/build-skill.sh` — rebuilds the `.skill` archive; verify it still matches the canonical skill contents under `plugins/`.
- `claude plugin validate . --strict` and `claude plugin validate ./plugins/laravel-spatie-event-sourcing --strict` — validate the marketplace and plugin manifests.
- `shellcheck scripts/build-skill.sh plugins/laravel-spatie-event-sourcing/skills/laravel-spatie-event-sourcing/scripts/verify-setup.sh` — if you have `shellcheck` installed locally. CI runs this for you on `*.sh` changes either way.

## Evals

The `evals/evals.json` file defines expected skill behavior. There is currently **no automated eval runner** — evals are read by humans (and Claude) as a behavioral contract. An eval runner is on the backlog; contributions welcome.

## Questions

Open a GitHub issue if anything in this document is unclear or out of date.
