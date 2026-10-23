# Dev Theory Coach (THEORY_CORE) — Implementation Stories

Stories and acceptance criteria for each milestone.  
Parent map: [`EPIC_MAP.md`](./EPIC_MAP.md) · Runtime state: [`.agents/state.json`](./.agents/state.json) · Resources: [`RESOURCES.md`](./RESOURCES.md) · Critic: [`gauntlet/contract.md`](./gauntlet/contract.md)

**Status key:** `todo` · `in_progress` · `done` · `blocked`  
**Finalize:** Approach A — thin playable Beginner path + MVP freeze

---

## Global conventions

- Stack: Web (PHP 8.2 server-rendered + vanilla JS, localStorage; PHPUnit)
- Persist in `localStorage` (`theory_core_progress_v1`)
- Join key: `slug`. Resource type is a separate field/route
- SAMPLE / educational data only. Every learner-facing page says SAMPLE
- `context/` is ideas only — rewrite copy. A test must fail if a guide body contains a 12-word sequence copied from `context/curriculums/`
- Step coaching is deterministic; no runtime LLM
- Do not start post-P0 until the MVP freeze checklist is met
- Auto-verify: `./vendor/bin/phpunit`
- A milestone flips to `done` only after phpunit passes **and** the gauntlet critic for that milestone returns WIN
- File layout (do not invent a second one):
  - `router.php` — front controller for `/`, `/guides`, `/guides/{slug}`, `/coach/{slug}`, `/quiz/{slug}`, and later `/drill/{slug}`, `/case/{slug}`, `/cheatsheet/{slug}`
  - `src/Catalog.php`, `src/ProgressSchema.php`, `src/CoachValidator.php`, `src/Quiz.php`, later `src/Drill.php`, `src/Case.php`, `src/Cheatsheet.php`
  - `assets/js/progress.js` — the only writer of `theory_core_progress_v1`
  - `content/families.json`
  - `content/guides/<slug>.json`, `content/coaching/<slug>.json`, `content/quizzes/<slug>.json`, `content/drills/<slug>.json`, `content/cases/<slug>.json`, `content/cheatsheets/<slug>.json`
  - `content/paths/beginner.json` and, after E2.M2, `structures.json`, `algorithms.json`, `systems.json`, `patterns.json`
  - `tests/` — PHPUnit
- Unknown slug redirects home. A missing coach, quiz, drill, case, or cheatsheet renders a "not authored yet" slot. Never 404
- Track labels on screen are the full strings: Data structures, Algorithms, System design, Design patterns

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

**MVP freeze:** Beginner path completable — Guides hub lists all 53 families, all 12 beginner guides render, `arrays` coach is playable, `arrays` quiz scores, unlocks follow the beginner order, dashboard names the next slug, path-complete credential renders. Case gate is visible and locked. phpunit exits 0. Integration critic on E7.M1 returns WIN.

### Post-P0

| # | Milestone | Epic | Tier | Status |
|---|-----------|------|------|--------|
| 10 | E8.M1 | E8 | Post | todo |
| 11 | E9.M1 | E9 | Post | todo |
| 12 | E2.M2 | E2 | Post | todo |
| 13 | E3.M3 | E3 | Post | todo |

### Later

| # | Milestone | Epic | Tier | Status |
|---|-----------|------|------|--------|
| 14 | E10.M1 | E10 | Later | todo |

### Advanced

| # | Milestone | Epic | Tier | Status |
|---|-----------|------|------|--------|
| 15 | E11.M1 | E11 | Advanced | todo |

---

## E1 — Family catalog

### E1.M1 — Schema + full seed *(P0)*

**Outcome:** `slug` list with track exists for every family and the Guides hub can group by track.

| ID | Story | Acceptance |
|----|-------|------------|
| E1.M1.S1 | Add `composer.json` with `phpunit/phpunit` and a `Catalog` that loads `content/families.json` | `./vendor/bin/phpunit` exits 0; the test fails if the JSON slugs differ in order or spelling from the slug column of `RESOURCES.md` |
| E1.M1.S2 | Store track id, full label, title, summary, `after`, and `rungs` on every family | Test asserts the four labels verbatim and asserts every `after` slug exists in the catalog |
| E1.M1.S3 | Render a hub grouping fixture (string or DOM) with full track labels | Test asserts "Data structures", "Algorithms", "System design", and "Design patterns" each appear, and `arrays` is under Data structures |

---

## E3 — Progress & unlocks

### E3.M1 — Store + frozen path IDs *(P0)*

**Outcome:** Progress survives refresh. Beginner milestone IDs are canonical before the path template exists.

| ID | Story | Acceptance |
|----|-------|------------|
| E3.M1.S1 | `assets/js/progress.js` get/set pathId, milestone statuses, scores, latencies under `theory_core_progress_v1`. `src/ProgressSchema.php` is the schema the JS comments must match | PHPUnit round-trips a fixture through `ProgressSchema`. A test reads the JS source and asserts the key string `theory_core_progress_v1` |
| E3.M1.S2 | Schema version. Corrupt JSON decodes to a reset state plus `recovery: true` | Test covers a truncated JSON string |
| E3.M1.S3 | Seed path id `beginner` and milestone ids `beginner:<slug>` for the 12 slugs in `curriculum.beginner_learning_order`, in that order | Test asserts the id list equals that array |

### E3.M2 — Unlock rules & writeback *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E3.M2.S1 | Beginner milestone N is locked until N−1 is complete. N=1 (`beginner:arrays`) starts unlocked. Case-gate flag is false until quiz accuracy on `arrays`, `hash-maps`, and `queues` is each ≥ 0.8 | Tests cover the first lock, a mid-path lock, and both sides of the 0.8 threshold |
| E3.M2.S2 | Finishing the `arrays` quiz writes score and accuracy. Reaching the `arrays` coach success node writes a coach-complete flag | Store fixture updates after both events |
| E3.M2.S3 | Reset control requires confirm, then returns first-run state | Test: reset without confirm is a no-op; reset with confirm clears milestones and sets no recovery flag |

---

## E4 — Guides

### E4.M1 — Hub + guide page + beginner bodies *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E4.M1.S1 | `GET /guides` lists all 53 families grouped by track, full labels | PHPUnit renders the route (or a view function) and asserts every slug appears once under its track |
| E4.M1.S2 | `GET /guides/{slug}` renders title, summary, body, and a coach CTA to `/coach/{slug}`. Unknown slug redirects home | Test for `arrays` sees the CTA. Test for `not-a-slug` is a redirect, not a 404 status |
| E4.M1.S3 | Author `content/guides/<slug>.json` for all 12 beginner slugs. Body 300–800 words, original, with rule, why, one SAMPLE worked example, when to use, when to refuse | Word-count test on all 12. Paste test: no guide body contains any contiguous 12-word span from `context/curriculums/`. `relatedCoachSlug` equals the slug |

---

## E5 — Step-by-step coaching

### E5.M1 — Schema + validator + nav engine *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E5.M1.S1 | Session schema: `start`, continue layers, `wrong` nodes with `rewind_to`, `success` with empty choices. `meta.slug` set | Validator rejects a graph with no success node, a `rewind_to` that names a missing node, and a success node that still has choices |
| E5.M1.S2 | Nav engine keeps history. Choosing a wrong node records the explanation and the rewind target | Unit test walks a fixture: wrong → rewind → success |
| E5.M1.S3 | `GET /coach/{slug}` renders the tree or a "not authored yet" slot | `GET /coach/strings` is 200 with the slot text. It is not a 404 |

### E5.M2 — First tree *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E5.M2.S1 | One session for `arrays`: the choice is array vs linked list for "jump to index i" and for "splice a node you already hold." Same slug as the guide | Validator passes. The guide CTA target exists. At least two `wrong` nodes rewind. One `success` |
| E5.M2.S2 | Update `.agents/skills/add-coaching-session/SKILL.md` so file paths and the validate command match the runtime | The skill no longer contains `TODO`. A test or a script check asserts the path it names exists |

---

## E6 — Quizzes

### E6.M1 — Engine + arrays pack *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E6.M1.S1 | Untimed quiz renderer. Each item has a prompt, exactly 4 choices, one `answerIndex`, `whyRight`, and `whyWrong` for every other choice. Page contains the word SAMPLE. No code runner, no timer | Renderer test fails if any item has a missing `whyWrong`. HTML fixture contains `SAMPLE` |
| E6.M1.S2 | `content/quizzes/arrays.json` has ≥ 6 items. Prompts are original. They check index cost, middle insert, append, and "array vs list" | Count test. Paste test, same 12-word rule as guides |
| E6.M1.S3 | Finishing the quiz writes score and accuracy through `progress.js` | Fixture: 5/6 correct stores accuracy `0.833…` (or the exact fraction the schema documents) and a submitted flag |

---

## E2 — Learning path planner

### E2.M1 — Beginner path *(P0 thin)*

| ID | Story | Acceptance |
|----|-------|------------|
| E2.M1.S1 | `content/paths/beginner.json` lists the 12 beginner milestones in `beginner_learning_order`. Each `contentRefs` includes `guide:<slug>`. `beginner:arrays` also includes `coach:arrays` and `quiz:arrays`. Other quiz and coach refs are absent in P0 so missing packs do not block the credential | A test resolves every ref to a file or, for non-arrays coach, to the slot route. Order matches state.json |
| E2.M1.S2 | Case gate definition lives on the path object: quizzes `arrays`, `hash-maps`, `queues` each ≥ 0.8 unlock graded cases | `ProgressSchema` (or a pure function it calls) returns pass and fail for fixtures at 0.79 and 0.80 |

---

## E7 — Dashboard

### E7.M1 — Home = real path *(P0)*

| ID | Story | Acceptance |
|----|-------|------------|
| E7.M1.S1 | `GET /` shows one next-action CTA whose href contains the next beginner slug. First run names `arrays`. After that guide is read, the CTA moves forward. No decorative percentage | Render tests for first-run and for "arrays guide read" |
| E7.M1.S2 | Path-complete state renders only when all 12 beginner guides are read, `arrays` quiz accuracy ≥ 0.8, and `arrays` coach success is stored. Case-gate chip shows locked or open from the real flag | Tests for incomplete, complete, and gate-open copy |

**MVP freeze checklist** (all must be true before `post_mvp_order`):

- [ ] `./vendor/bin/phpunit` exits 0
- [ ] E1.M1 through E7.M1 are `done` in `.agents/state.json`
- [ ] Integration critic in `gauntlet/contract.md` returned WIN for the beginner path
- [ ] `queues` and `work-queues` are both present and are different guides

---

## E8 — Drills

### E8.M1 — Engine + complexity pack *(Post)*

| ID | Story | Acceptance |
|----|-------|------------|
| E8.M1.S1 | Timed drill renderer. After submit, show a 3-step thought chain. Record latency. Page says SAMPLE. Families outside `decisions.drill_families` have no drill route content; the slot says the family is not a drill | Test: `complexity` renders; `arrays` renders the not-a-drill slot, not a fake pack |
| E8.M1.S2 | `content/drills/complexity.json` has ≥ 8 items with `accept`, `thoughtChain` length 3, and `seconds` | Count and shape test. Paste test |
| E8.M1.S3 | A finished drill writes accuracy and median latency | Fixture covers 8 submits |
| E8.M1.S4 | Update `.agents/skills/add-drill/SKILL.md` with real paths and the validate command | Skill contains no `TODO`; named path exists |

---

## E9 — Cases

### E9.M1 — Engine + requirements case *(Post)*

| ID | Story | Acceptance |
|----|-------|------------|
| E9.M1.S1 | Decide-and-reveal renderer. Choices are visible; consequences are not, until commit. Page says SAMPLE. If the case gate is closed, the page explains the gate and does not reveal the best choice | Tests for gate-closed and gate-open |
| E9.M1.S2 | `content/cases/requirements.json`: a SAMPLE prompt, 3 or 4 choices, one `best`, a consequence on every choice. The decision is accept or reject a proposed requirement that does not change storage, traffic, or failure | Shape test. Paste test. Consequence text does not appear in the pre-commit HTML |
| E9.M1.S3 | Commit writes the grade | Fixture stores the choice id and whether it matched `best` |
| E9.M1.S4 | Update `.agents/skills/add-case/SKILL.md` with real paths | Skill contains no `TODO`; named path exists |

---

## E2.M2 — Track paths *(Post)*

| ID | Story | Acceptance |
|----|-------|------------|
| E2.M2.S1 | Paths `structures`, `algorithms`, `systems`, `patterns` list every family in that track, catalog order | Test: union of path slugs equals the 53 catalog slugs, no extras |
| E2.M2.S2 | Each node records its `after` slugs from `families.json` | Test: path edges equal the catalog `after` arrays |

---

## E3.M3 — Prerequisite unlocks *(Post)*

| ID | Story | Acceptance |
|----|-------|------------|
| E3.M3.S1 | On the four track paths, a family is locked until every `after` slug has its guide marked read and its quiz submitted when that quiz file exists. A missing quiz file does not block (slot). Beginner path linear rule from E3.M2 still applies when that path is active | Tests: `strings` locked before `arrays`; `caching` locked until `read-and-write-paths`, `hash-maps`, and `linked-lists` are done; `when-to-name-a-pattern` starts unlocked |
| E3.M3.S2 | Case pages stay behind the case gate even if the guide is unlocked | Test: `requirements` guide can be read while its case stays gated at quiz accuracy 0.5 |

---

## E10 — Cheatsheets

### E10.M1 — Runtime + arrays sheet *(Later)*

| ID | Story | Acceptance |
|----|-------|------------|
| E10.M1.S1 | `GET /cheatsheet/{slug}` renders at most 12 recall lines, one pick-when sentence, one skip-when sentence, or a "not authored yet" slot | Slot test for `strings`. Shape test for `arrays` |
| E10.M1.S2 | Author `content/cheatsheets/arrays.json` inside those limits. Original wording | Line-count test. Paste test |
| E10.M1.S3 | Update `.agents/skills/add-cheatsheet/SKILL.md` with real paths | Skill contains no `TODO`; named path exists |

---

## E11 — Locked reason

### E11.M1 — Name the missing slug *(Advanced)*

| ID | Story | Acceptance |
|----|-------|------------|
| E11.M1.S1 | A locked hub row and a locked path row include the missing slug in the visible text | Render test: `strings` locked shows `arrays`. `caching` locked shows each unmet slug among `read-and-write-paths`, `hash-maps`, `linked-lists` |
| E11.M1.S2 | Unlocked rows do not show a missing-slug sentence | Render test on `arrays` at first run |

**Runtime-complete checklist** (before the content graph):

- [ ] Every id in `implementation_order`, `post_mvp_order`, `later_order`, and `advanced_order` is `done`
- [ ] `./vendor/bin/phpunit` exits 0
- [ ] Final integration critic returned WIN
- [ ] No milestone was marked done on a LOSE or on an exhausted critic budget
