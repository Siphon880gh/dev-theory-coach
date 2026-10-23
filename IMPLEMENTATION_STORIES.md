# Dev Theory Coach (THEORY_CORE) — Implementation Stories

Stories and acceptance criteria for each milestone.  
Parent map: [`EPIC_MAP.md`](./EPIC_MAP.md) · Runtime state: [`.agents/state.json`](./.agents/state.json) · Resources: [`RESOURCES.md`](./RESOURCES.md)

**Status key:** `todo` · `in_progress` · `done` · `blocked`  
**Finalize:** Approach A — thin playable Beginner path + MVP freeze

---

## Global conventions

- Stack: Web (PHP 8.2 server-rendered + vanilla JS, localStorage; PHPUnit)
- Persist in `localStorage` (`theory_core_progress_v1`)
- Join key: `slug`. Resource type is a separate field/route
- SAMPLE / educational data only
- `context/` is ideas only — rewrite copy
- Step coaching is deterministic; no runtime LLM
- Do not start post-P0 until the MVP freeze checklist is met
- Auto-verify: `./vendor/bin/phpunit`

---

## Build order

### P0 — MVP freeze (do in sequence)

| # | Milestone | Epic | Tier | Status |
|---|-----------|------|------|--------|
| 1 | E1.M1 | E1 | P0 | todo |
| 2 | E3.M1 | E3 | P0 | todo |
| 3 | E4.M1 | E4 | P0 | todo |
| 4 | E5.M1 | E5 | P0 | todo |
| 5 | E5.M2 | E5 | P0 | todo |
| 6 | E6.M1 | E6 | P0 | todo |
| 7 | E2.M1 | E2 | P0 (thin) | todo |
| 8 | E3.M2 | E3 | P0 | todo |
| 9 | E7.M1 | E7 | P0 | todo |

**MVP freeze:** Beginner path completable — Guides hub + every Beginner guide + one coaching tree + <!-- agent: practice type + thin pack --> + unlocks + live dashboard + path-complete credential.

### Post-P0

| # | Milestone | Epic | Tier | Status |
|---|-----------|------|------|--------|
| 10 | <!-- agent --> | | Post | todo |

### Later

| # | Milestone | Epic | Tier | Status |
|---|-----------|------|------|--------|
| | | | Later | todo |

### Advanced

| # | Milestone | Epic | Tier | Status |
|---|-----------|------|------|--------|
| | | | Advanced | todo |

---

## E1 — Family catalog

### E1.M1 — Schema + Beginner seed *(P0)*

**Outcome:** `slug` list with track exists for every Beginner family and renders on the Guides hub.

| ID | Story | Acceptance |
|----|-------|------------|
| E1.M1.S1 | Define the family catalog type (`id`, `track`, `title`, `summary`, `rungs`) and seed every Beginner family from `RESOURCES.md` | Catalog loads; ids match RESOURCES exactly; no duplicates |
| E1.M1.S2 | Track definitions with full labels | Both labels render verbatim from RESOURCES |
| E1.M1.S3 | Test: every Beginner family present; ids kebab-case; tracks valid | `./vendor/bin/phpunit` covers it |

---

## E3 — Progress & unlocks

### E3.M1 — Store + frozen path IDs *(P0)*

**Outcome:** Progress survives refresh; Beginner milestone IDs are canonical.

| ID | Story | Acceptance |
|----|-------|------------|
| E3.M1.S1 | ProgressStore: get/set pathId, milestone statuses, scores, latencies | JSON round-trip in `localStorage` key `theory_core_progress_v1` |
| E3.M1.S2 | Schema version + corrupt → reset + recovery flag | Test covers corrupt JSON |
| E3.M1.S3 | Seed Beginner path milestone IDs only | IDs match E2.M1 |

### E3.M2 — Unlock rules & writeback *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E3.M2.S1 | Unlock: prior milestone complete; gate milestone required before graded content | Locked items not startable |
| E3.M2.S2 | Practice sessions write scores and latency | Store updates after a finished unit |
| E3.M2.S3 | Reset path control with confirm | First-run state after confirm |

---

## E4 — Guides

### E4.M1 — Hub + guide page + coach CTA *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E4.M1.S1 | `/guides` lists families grouped by track (full labels) | Every Beginner family appears under the right heading |
| E4.M1.S2 | `/guides/:slug` renders title, summary, body, coach CTA to `/coach/:slug` | Unknown slug → home; CTA present even if tree missing |
| E4.M1.S3 | Author guides for every Beginner family (original copy, 300–800 words) | No verbatim text from `context/` |

---

## E5 — Step-by-step coaching

### E5.M1 — Schema + validator + nav engine *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E5.M1.S1 | Session schema: `start`, continue layers, `wrong` nodes with `rewind_to`, `success` | Validator rejects graphs with no success or dangling rewinds |
| E5.M1.S2 | Nav engine with history; wrong → explain → rewind | Unit test walks a fixture tree |
| E5.M1.S3 | `/coach/:slug` UI; "not authored yet" slot when tree missing | Never 404 |

### E5.M2 — First tree *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E5.M2.S1 | One session for the first Beginner family, same slug as its guide | Validator passes; playable from the guide CTA |
| E5.M2.S2 | Skill `.agents/skills/add-coaching-session/SKILL.md` documents the real file paths + validate command | Skill matches runtime |

---

## E6 — <!-- agent: first practice type -->

### E6.M1 — Engine + thin pack *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E6.M1.S1 | <!-- agent: engine (timed drill / decide-and-reveal case / lab checkpoints) --> | |
| E6.M1.S2 | Thin pack: ≥<!-- agent: n --> items for Beginner families | |
| E6.M1.S3 | Writeback to ProgressStore | Score visible after session |

---

## E2 — Learning path planner

### E2.M1 — Beginner path *(P0 thin)*

| ID | Story | Acceptance |
|----|-------|------------|
| E2.M1.S1 | Beginner path template with frozen milestone IDs + `contentRefs` | Every ref resolves to an existing resource or a slot page |
| E2.M1.S2 | Gate milestone defined (<!-- agent: which metric/threshold -->) | Store exposes gate pass/fail |

---

## E7 — Dashboard

### E7.M1 — Home = real path *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E7.M1.S1 | Home shows next milestone CTA naming the real slug/group | Not decorative % |
| E7.M1.S2 | Path-complete credential state | Renders after last milestone |

---

<!-- agent: add epics/milestones for post-P0, later, advanced. Every story needs an Acceptance cell a command can check, or an explicit "human taste" note. -->
