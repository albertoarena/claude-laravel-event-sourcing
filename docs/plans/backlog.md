# Backlog Plan

Improvements identified during project review (2026-04-28).

## Priority: High

### H1. Add the orders worked example

**Status:** DONE
**Sub-plan:** [orders-example.md](./orders-example.md)
**Impact:** Usability, regression testing, credibility

Copied from eval iteration 1 workspace (skill-generated output). Includes user prompt, ADR, 24 generated PHP files, walkthrough README, and test output placeholder.

---

### H2. Fix verify-setup.sh

**Status:** DONE
**Impact:** Correctness — script doesn't match what SKILL.md promises

Fixed: added snapshots migration check, consistent `OK:` output for all checks on success, and `chmod +x`.

---

### H3. Add missing stub templates

**Status:** DONE
**Sub-plan:** [missing-stubs.md](./missing-stubs.md)
**Impact:** Code generation completeness — every generated artifact type should have a stub

Added 6 stubs: `command-bus.stub`, `command-handler-bus.stub`, `projector.pest.stub`, `projector.phpunit.stub`, `reactor.pest.stub`, `reactor.phpunit.stub`. Skipped `migration.stub` — documented migration structure in SKILL.md Gate 2 instead. Added sync rule to CLAUDE.md.

---

## Priority: Medium

### M1. Fix Pest projector test pattern — missing RefreshDatabase

**Status:** DONE
**Impact:** Tests would fail — projector tests write to the database

Added `uses(RefreshDatabase::class)` to the Pest projector test example in `references/tdd-patterns.md`.

---

### M2. Fix read-model.stub UUID configuration

**Status:** DONE
**Impact:** Generated read models would fail on `::uuid()` lookups

The stub extends `Projection` but doesn't set:
```php
protected $primaryKey = 'uuid';
public $incrementing = false;
protected $keyType = 'string';
```

All examples use `Order::uuid('o-1')`, so this is required for generated code to work.

**Suggestion:** Also consider whether the stub should include a `protected $casts = [];` line as a placeholder, since read models often cast JSON columns.

---

### M3. Enrich stub placeholders

**Status:** WON'T FIX
**Impact:** Better code generation guidance for Claude

Current stubs provide structure (namespace, imports, base class), while reference files and the ADR guide the actual code generation. Adding named placeholders or a stub-usage reference would add indirection without measurable benefit. The `// TODO:` comments serve as useful markers if generation is ever partial.

---

### M4. Use the spec's more aggressive skill description

**Status:** RESOLVED — no change for 1.0
**Sub-plan:** [skill-description.md](./skill-description.md)
**Impact:** Better skill triggering — current description may under-trigger

**Decision (2026-05-19):** keep the current SKILL.md description. No telemetry from 0.1/0.2 indicates under-triggering, and the proposed "even if they don't explicitly name the skill" phrasing risks over-triggering on unrelated Laravel `Event::dispatch` questions — directly contradicting the skill's own `Do NOT use for:` exclusion list. Revisit in 1.1 if real activation data shows the skill is being missed.

---

### M5. Backfill `[Unreleased]` in CHANGELOG.md

**Status:** DONE
**Impact:** CLAUDE.md compliance — every notable change must be tracked

Identified during pre-1.0 review (2026-05-19). `[Unreleased]` section is currently empty despite the README badges commit (`1bcf486`). Fix: add `### Changed` → "README badges" entry. Also worth establishing a habit (or pre-commit hook) so future README/SKILL.md changes don't bypass the changelog.

---

### M6. Add compatibility matrix to README

**Status:** DONE
**Impact:** Critical for a 1.0 contract — users must know what's supported

Identified during pre-1.0 review (2026-05-19). `verify-setup.sh` encodes "Spatie ES v7, PHP 8.2+" in comments only; nothing user-facing states it.

**Proposed table** (place just below the badges):

| Requirement                     | Version    |
|---------------------------------|------------|
| PHP                             | 8.2+       |
| Laravel                         | 10.x, 11.x |
| spatie/laravel-event-sourcing   | ^7.0       |
| Claude Code                     | any        |

Open question: should `verify-setup.sh` also check the Laravel version, or leave it implicit via Spatie's own composer constraints? Decide before writing the matrix.

---

### M7. Reproducible `.skill` packaging

**Status:** DONE
**Impact:** Release hygiene — no manual zips, no drift between `skill/` and `.skill`

Identified during pre-1.0 review (2026-05-19). Currently `laravel-spatie-event-sourcing.skill` is a manually-built zip. Contents currently match `skill/` exactly (verified), but there's no script enforcing this.

**Plan:**
1. Add `scripts/build-skill.sh` (~5 lines: `cd skill && zip -r ../laravel-spatie-event-sourcing.skill laravel-spatie-event-sourcing -x "*.DS_Store"`).
2. Note in CLAUDE.md: "Run `scripts/build-skill.sh` before tagging a release."
3. **Defer:** the GitHub Action that builds + attaches the artifact on tag push — handled in M8 (GitHub Actions workflows).

Resolves the drift concern noted in pre-1.0 review point #6 permanently.

---

### M8. Add GitHub Actions workflows

**Status:** DONE (partial — shellcheck + release builder shipped; markdownlint, stub-ref sync, and evals runner deferred)

Added `.github/workflows/shellcheck.yml` (lint `*.sh`, severity warning, PRs + pushes to `main`) and `.github/workflows/release.yml` (build `.skill` via `scripts/build-skill.sh` and attach to GH release on `v*` tags). Other candidates from the original discussion (markdownlint, stub/reference sync check, eval runner) were deliberately deferred — see future M-items if/when needed.

**Original process notes (kept for reference on future workflow additions):**

**Status:** PENDING
**Impact:** CI hygiene — catch regressions in stubs, refs, and verify-setup.sh before release

Identified during pre-1.0 review (2026-05-19). No `.github/` directory currently exists.

**Process to follow** (per user instruction — do not just commit workflows):

1. Explain before adding — describe each proposed workflow (what it runs, when it triggers, why it matters for a skill-only repo) so the user can decide which to include.
2. Suggest how to improve — list candidate workflows: shellcheck on `verify-setup.sh`, markdownlint on SKILL.md + references, stub/reference sync check, automated eval runner, release-tag → `.skill` artifact builder.
3. Explain how to improve — for each candidate, walk through trade-offs (maintenance cost, false-positive risk, real value for a repo with no PHP runtime code).
4. Suggest how to do it — propose concrete YAML structure and which actions/runners to use.
5. OK — wait for user approval before adding any `.github/workflows/*.yml`.

---

## Priority: Low

### L1. Add docs/ content

**Status:** DONE
**Impact:** User-facing documentation

Added `docs/workflow.md` — human-readable explanation of the two-gate workflow. Skipped `installation.md` (README covers it), `design-heuristics.md` (would duplicate reference file), and `faq.md` (premature without user questions).

---

### L2. Add CHANGELOG.md

**Status:** DONE
**Impact:** Project hygiene

Added `CHANGELOG.md` with initial release entry. Added changelog tracking rule to `CLAUDE.md`.

---

### L4. Add CONTRIBUTING.md

**Status:** DONE
**Impact:** Project hygiene — signals maturity, sets expectations for outside contributors

Identified during pre-1.0 review (2026-05-19). Should cover: branch/PR conventions, commit message rules (already in CLAUDE.md — link or restate), the stub/reference sync rule, how to run evals locally, and the no-Claude-attribution policy.

---

### L3. Document .skill package installation

**Status:** DONE
**Impact:** Easier onboarding

Added Option B (.skill package) to README installation section. Shifted global clone to Option C.

---

## Task dependency graph

```
H2 (verify-setup.sh)  ─── no dependencies, do first
H3 (missing stubs)     ─── no dependencies, can parallel with H2
M1 (pest refresh)      ─── no dependencies, quick fix
M2 (read-model uuid)   ─── no dependencies, quick fix
M3 (enrich stubs)      ─── after H3 (stubs must exist first)
M4 (skill description) ─── no dependencies, quick fix
H1 (orders example)    ─── after H2, H3, M1, M2 (example should use fixed stubs/scripts)
L1 (docs)              ─── after H1 (can reference the example)
L2 (changelog)         ─── anytime
L3 (.skill docs)       ─── anytime
```

## Suggested execution order

1. Quick fixes first: H2, M1, M2, M4 (all independent, small edits)
2. H3 — missing stubs
3. M3 — enrich stubs (depends on H3)
4. H1 — orders example (depends on everything above being solid)
5. L1, L2, L3 — low priority, as needed
