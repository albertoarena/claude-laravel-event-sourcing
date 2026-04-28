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

**Status:** PENDING DECISION
**Sub-plan:** [skill-description.md](./skill-description.md)
**Impact:** Better skill triggering — current description may under-trigger

Proposed: merge the spec's aggressive trigger phrases with the current negative boundaries. See sub-plan for full before/after comparison and trade-offs.

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
