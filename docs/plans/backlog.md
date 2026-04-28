# Backlog Plan

Improvements identified during project review (2026-04-28).

## Priority: High

### H1. Add the orders worked example

**Status:** TODO
**Sub-plan:** [orders-example.md](./orders-example.md)
**Impact:** Usability, regression testing, credibility

The `skill-spec.md` (section 9) defines a full worked example but it was never created.
The `examples/` directory does not exist.

Artifacts needed:
- `examples/orders/README.md` — walkthrough narrative
- `examples/orders/01-user-prompt.md` — the trigger prompt
- `examples/orders/02-adr.md` — Gate 1 output
- `examples/orders/03-generated/` — full generated code tree
- `examples/orders/04-test-output.txt` — passing test run

---

### H2. Fix verify-setup.sh

**Status:** TODO
**Impact:** Correctness — script doesn't match what SKILL.md promises

Issues:
1. **Missing snapshots migration check.** SKILL.md says the script checks for `snapshots` table, but the script only checks `*create_stored_events_table*`.
2. **Inconsistent output.** PHP and test framework print `OK:` on success; spatie package and config checks are silent on success.
3. **Script not executable.** Should `chmod +x` or document `bash scripts/verify-setup.sh`.

Fix approach: straightforward edits to `scripts/verify-setup.sh`. Single task, no sub-plan needed.

---

### H3. Add missing stub templates

**Status:** TODO
**Sub-plan:** [missing-stubs.md](./missing-stubs.md)
**Impact:** Code generation completeness — every generated artifact type should have a stub

Missing stubs:
1. `migration.stub` — read model table migration
2. `command-bus.stub` — command DTO with `Dispatchable` trait (bus style)
3. `command-handler-bus.stub` — handler with `handle()` method (bus style)
4. `projector.pest.stub` — Pest projector test
5. `projector.phpunit.stub` — PHPUnit projector test
6. `reactor.pest.stub` — Pest reactor test
7. `reactor.phpunit.stub` — PHPUnit reactor test

---

## Priority: Medium

### M1. Fix Pest projector test pattern — missing RefreshDatabase

**Status:** TODO
**Impact:** Tests would fail — projector tests write to the database

In `references/tdd-patterns.md`, the Pest projector test is missing `uses(RefreshDatabase::class)`.
The PHPUnit version correctly includes it. One-line fix.

---

### M2. Fix read-model.stub UUID configuration

**Status:** TODO
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

**Status:** TODO
**Impact:** Better code generation guidance for Claude

Current stubs use `// TODO:` comments where generated code should go. Consider replacing with named placeholders like `{{ stateProperties }}`, `{{ commandMethods }}`, `{{ applyMethods }}` so the SKILL.md instructions can reference them explicitly.

**Alternative approach (recommended):** Keep stubs as structural scaffolds with `// TODO:` comments, but add a `references/stub-usage.md` file that documents how Claude should fill each stub. This avoids over-engineering the placeholder system while giving Claude clear instructions. The stubs then serve as the shape; the reference file provides the fill logic.

---

### M4. Use the spec's more aggressive skill description

**Status:** TODO
**Impact:** Better skill triggering — current description may under-trigger

Replace the YAML `description` in SKILL.md with the version from `skill-spec.md` section 2, which includes explicit trigger phrases like "let's event-source X", "add ES to my Laravel project", etc.

Single edit to SKILL.md frontmatter. No sub-plan needed.

---

## Priority: Low

### L1. Add docs/ content

**Status:** TODO
**Impact:** User-facing documentation

The spec (section 10) lists:
- `docs/installation.md`
- `docs/workflow.md` — the two gates explained for humans
- `docs/design-heuristics.md` — prose version of the AI-facing heuristics
- `docs/faq.md`

**Suggestion:** Start with `workflow.md` only. The README already covers installation, and `design-heuristics.md` would largely duplicate the reference file. A FAQ is premature until there are actual user questions. Alternatively, expand the README with a "How it works" section instead of creating separate doc files.

---

### L2. Add CHANGELOG.md

**Status:** TODO
**Impact:** Project hygiene

Start with a single entry for the initial release. Keep it simple — no tooling, just a manually maintained file.

---

### L3. Document .skill package installation

**Status:** TODO
**Impact:** Easier onboarding

The `laravel-spatie-event-sourcing.skill` ZIP package exists in the repo root but isn't mentioned in the README. Add a third installation option.

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
