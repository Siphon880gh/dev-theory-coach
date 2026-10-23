# Dev Theory Coach (THEORY_CORE) — Epic Map

Living product map for AI-assisted incremental delivery.  
Companions: [`IMPLEMENTATION_STORIES.md`](./IMPLEMENTATION_STORIES.md) · [`.agents/state.json`](./.agents/state.json) · [`RESOURCES.md`](./RESOURCES.md) · [`context/README.md`](./context/README.md) · [`gauntlet/contract.md`](./gauntlet/contract.md)

| Field | Value |
|-------|-------|
| **Status** | E0 complete · next E1.M1 |
| **Platform** | Web (PHP 7.4+ server-rendered + vanilla JS, localStorage; PHPUnit) |
| **Primary learner** | Working software engineer preparing for interviews, in 15–25 minute sessions |
| **Curriculum principle** | Name the concept the prompt is asking for, say the cost or the force, and reject the common wrong pick |
| **Finalize** | Approach A — thin playable Beginner path + MVP freeze; remaining runtime queues, then content drain via `LOOPS/` |

---

## App summary

| Field | Value |
|-------|-------|
| **App name** | Dev Theory Coach (THEORY_CORE) |
| **One-liner** | Teach interview concepts with guides, step-by-step choices, and quizzes so a candidate can name the right idea and reject the wrong one. |
| **Success signal** | Learner finishes the Beginner path with persisted progress: all 12 phone-screen guides read, the `arrays` quiz at ≥ 0.8, and the `arrays` coach success node reached. |

This app covers the four curricula in `context/curriculums/`. It does not run code and it does not replace `../leetcode-coach`, which owns problem practice and mini games.

### Product decision

The curricula already name the learner, the slugs, the order, and the outcome of each lesson, so the plan did not stop for a reverse interview. Three shapes were graded against that outcome:

1. A second problem-solving trainer. Rejected. It duplicates leetcode-coach.
2. A system-design simulator. Rejected. Tracks A, B, and D would be thin, and a general simulator is a large engine for a job drills and cases already do.
3. Guides and coach trees on every lesson, quizzes as the practice the learner asked for, drills only where the answer is closed-form, and cases where the lesson is a decision. Chosen.

Games were rejected. The curricula name a different mechanic on almost every lesson (shift a cell, drop a key in a bucket, sift a heap). A gallery of one-off games would stay thin. Coach trees carry the choice. Quizzes carry the check.

---

## Resource types for this topic

The topic spans procedural practice (structures and algorithms), recall (name the tool), and systems decisions (design interviews and pattern forces).

| Type | Why it fits this topic | Rung | Tier |
|------|------------------------|------|------|
| Guide | Every lesson is a concept with a rule, a reason, and a "use / refuse" line | recognize | P0 |
| Coach | The lesson is a choice: which structure, which walk, which consistency, which pattern | explain | P0 (one tree; the rest via the graph) |
| Quiz | The learner asked for quizzes. Misconceptions are multiple choice with a reason on every option | apply | P0 (engine + `arrays` pack; the rest via the graph) |
| Drill | Seven families have a fast closed-form answer (a bound, a half to discard, an order-of-magnitude QPS) | apply, under time | Post-MVP |
| Case | System design and pattern lessons are decisions with a consequence | diagnose; design on three capstones | Post-MVP |
| Cheatsheet | Interview morning is a one-screen recap, after the learner can already explain the unit | recognize, retention | Later |

Not chosen:

- **Game** — one distinct mechanic per lesson, so a Games loop would mostly build new runtimes. Skipped as a type, not recorded as per-family skips.
- **Lab** — no bench procedure to checkpoint.
- **Simulation** — estimates and limiters are numbers and choices, not a system the learner has to watch.

---

## Scope

### MVP (P0 — playable Beginner path)

- Family catalog (`slug` + track) for all 53 families, rendered on the Guides hub
- Guides for every Beginner family; coach CTA on the same slug
- One coaching tree playable (`arrays`: array vs list); remaining trees via the content graph
- Quiz engine plus a 6-item pack for `arrays`
- Fixed Beginner path + progress + unlocks
- Dashboard wired to the real next action
- Case gate stored and shown (locked until `arrays`, `hash-maps`, and `queues` quizzes are each ≥ 0.8). No case UI in P0

The MVP credential is: 12 beginner guides read, `arrays` quiz ≥ 0.8, `arrays` coach success reached. Other quizzes are not required for that credential. The content graph adds them later and does not revoke it.

### Post-MVP

- Drill engine and an 8-item `complexity` pack
- Case engine and one `requirements` case (accept or reject a proposed requirement)
- Four track paths whose edges are the `After` column
- Prerequisite unlocks (a family opens when every `After` slug is complete)

### Later

- Cheatsheet runtime and the `arrays` sheet

### Advanced

- Locked row names the missing prerequisite slug

### Constraints

- Educational / non-live practice data only; no live APIs, credentials, or hardware I/O
- Progress in `localStorage` (`theory_core_progress_v1`); no auth in MVP
- Step coaching = deterministic graphs only — no runtime LLM
- `context/` is a source of ideas; rewrite lessons (do not paste)
- `slug` is the join key; resource type is the cognitive mode
- Do not skip P0 order without updating `.agents/state.json`
- No family is safety-critical. Capstone cases still wait for the case gate
- `queues` is the data structure. `work-queues` is the system-design lesson. Do not merge them
- A fresh critic judges each finished milestone against [`gauntlet/contract.md`](./gauntlet/contract.md). The builder does not grade its own milestone

---

## Beginner learning order (user-facing)

Phone-screen set. Each step's prerequisites already appear above it.

1. `arrays` — index cost vs a middle insert
2. `strings` — when an edit copies
3. `hash-maps` — average case vs a total collision
4. `linked-lists` — splice vs jump to index i
5. `stacks` — last in, first out
6. `queues` — first in, first out, and a deque
7. `complexity` — which bound the question asked for
8. `recursion` — base, shrink, stack
9. `two-pointers` — which index moves, and why
10. `binary-search` — which half still holds the answer
11. `trees` — which walk matches the job
12. `sliding-window` — fixed vs variable, and a summary you can update from both ends

Then the rest of track A, the rest of track B, track C, and track D, in the `After` order in `RESOURCES.md`. Track D's first lesson has no prerequisite. Its cases still wait for the case gate.

---

## User needs

| Kind | Needs |
|------|-------|
| **Use** | In one sitting, open the next concept, read the rule, walk the choice, and check it with a quiz |
| **Usability** | Fast loop; works offline on bundled content; next action always obvious |
| **Meaning** | The learner can say the concept's name, the cost or the force, and the wrong pick they rejected |
| **Social** | Path complete feels like a credential: a phone-screen set, not a trophy |

---

## Flows

- **First-run:** Beginner path → Home with `arrays` unlocked
- **Primary loop:** Next milestone → guide (with coach) / quiz → score → unlock
- **Coaching loop:** Guide → same-slug tree → choice nodes → wrong rewind → success
- **Quiz loop:** Untimed items → commit → explanation per option → score written back
- **Drill loop (post-MVP):** Timed item → submit → thought chain → accuracy and latency written back
- **Case loop (post-MVP):** educational scenario → commit → consequence and grade. Locked until the case gate passes
- **Errors:** Corrupt progress → reset; locked → show the missing slug; unknown slug → home

---

## State & persistence

| Layer | Contents |
|-------|----------|
| Persisted business | Path, milestone status, scores, accuracy, latency, case-gate flag |
| Persisted prefs | Active path ID (`beginner` by default) |
| Temporary UI | Item index, timer, coach history |
| Offline | Bundled guides, trees, quiz packs, and later drill packs, cases, and cheatsheets |

**UI states:** empty path · error · success (milestone complete) · first-run · locked · path complete

---

## Epic list

| ID | Name | Goal / outcome | Priority | Status |
|----|------|----------------|----------|--------|
| E0 | Planning & Decisions | Spec, epics, loops, agent state, context | Mandatory | Complete |
| E1 | Family catalog | Stable `slug` + track + Guides hub rows for all 53 families | MVP | todo |
| E2 | Learning path planner | Beginner path in P0; four track paths after | MVP (thin) | todo |
| E3 | Progress & unlocks | Resume-able progress, beginner unlocks, then prerequisite unlocks | MVP | todo |
| E4 | Guides | Hub + guide pages + coach CTA; 12 beginner guides authored | MVP | todo |
| E5 | Step-by-step coaching | Schema, nav, `arrays` tree in P0; library via the graph | MVP thin + later | todo |
| E6 | Quizzes | Engine + 6-item `arrays` pack | MVP | todo |
| E7 | Dashboard | Home = real path + next CTA + path-complete credential | MVP | todo |
| E8 | Drills | Engine + 8-item `complexity` pack | Post-MVP | todo |
| E9 | Cases | Decide-and-reveal engine + one `requirements` case | Post-MVP | todo |
| E10 | Cheatsheets | One-screen recap runtime + `arrays` sheet | Later | todo |
| E11 | Locked reason | A locked row names the missing prerequisite slug | Advanced | todo |

---

## Epic details

### E0. Planning & Decisions

- **Includes:** This map, stories, `RESOURCES.md`, `LOOPS/`, `AGENTS_LOOP-Continue-Milestone.md`, `.agents/state.json`, `context/`, `gauntlet/contract.md`
- **Out of scope:** Product code

### E1. Family catalog

- **Goal:** One `slug` list, shared by every surface.
- **Includes:** `content/families.json` for all 53 families; track labels; PHPUnit comparison against `RESOURCES.md`.
- **Out of scope:** Guide bodies.

### E2. Learning path planner

- **Goal:** A frozen Beginner path, then one path per track using `After`.
- **Includes:** `content/paths/*.json` and `contentRefs`.
- **Out of scope:** A generated planner that invents order.

### E3. Progress & unlocks

- **Goal:** Refresh keeps progress. Beginner order unlocks in P0. `After` edges unlock after E3.M3.
- **Includes:** `theory_core_progress_v1`, corrupt reset, case-gate flag.
- **Out of scope:** Accounts.

### E4. Guides

- **Goal:** Read a unit and open its coach.
- **Includes:** `/guides`, `/guides/{slug}`, 12 beginner bodies, paste check against `context/`.
- **Out of scope:** The other 41 guide bodies (content graph).

### E5. Step-by-step coaching

- **Goal:** A wrong choice explains itself and rewinds. A right path reaches success.
- **Includes:** Validator, nav engine, `/coach/{slug}` slot, the `arrays` tree.
- **Out of scope:** The other 52 trees (content graph).

### E6. Quizzes

- **Goal:** Check the concept without a timer and without running code.
- **Includes:** Renderer, 6 `arrays` items, score writeback.
- **Out of scope:** The other packs (content graph).

### E7. Dashboard

- **Goal:** Home names the next slug. Path complete is a credential state.
- **Includes:** Next CTA, case-gate chip, path-complete state.
- **Out of scope:** Decorative percentages.

### E8. Drills

- **Goal:** Timed closed-form practice with a thought chain after submit.
- **Includes:** Engine and the `complexity` pack of 8.
- **Out of scope:** Drills for families that are not in `decisions.drill_families`.

### E9. Cases

- **Goal:** Commit to a practice decision and read the consequence.
- **Includes:** Engine, the case gate check, one `requirements` case.
- **Out of scope:** The other decision-family cases (content graph).

### E10. Cheatsheets

- **Goal:** One screen of "pick when / skip when" per family.
- **Includes:** Renderer and the `arrays` sheet.
- **Out of scope:** The other 52 sheets (content graph).

### E11. Locked reason

- **Goal:** A locked unit says which slug is still missing.
- **Includes:** Copy on the hub and the path.
- **Out of scope:** A new resource type.

---

## Implementation order (milestones)

See [`IMPLEMENTATION_STORIES.md`](./IMPLEMENTATION_STORIES.md).

### P0 — MVP freeze

```
E1.M1 → E3.M1 → E4.M1 → E5.M1 → E5.M2 → E6.M1 → E2.M1 → E3.M2 → E7.M1
── MVP FREEZE: Beginner path complete credential ──
```

**Rationale:** The catalog and the progress key exist before pages. Guides exist before the coach CTA. The coach engine exists before the `arrays` tree. The quiz engine exists before the path points at it. The path and unlocks exist before the dashboard names a next action.

### Post-P0 (`post_mvp_order`)

```
E8.M1 → E9.M1 → E2.M2 → E3.M3
```

### Later (`later_order`)

```
E10.M1
```

### Advanced (`advanced_order`)

```
E11.M1
```

Content volume (the other guides, trees, quizzes, drills, cases, and sheets) is not a milestone. It is [`LOOPS/LOOP-Graph.md`](./LOOPS/LOOP-Graph.md), and it starts only after these queues are done.

---

## Coverage check

| Requirement | Epics |
|-------------|-------|
| `slug` + four tracks | E1, E4 |
| Beginner path + progress | E2, E3, E7 |
| Guides + coach | E4, E5 |
| Quizzes | E6 |
| Drills | E8 |
| Cases + case gate | E9, E2, E3 |
| Cheatsheets | E10 |
| Locked reason | E11 |
| Quality bar the builder does not self-grade | `gauntlet/contract.md` on every milestone |
| Content drain after runtime | `LOOPS/` |

---

## Agent notes

- Do **not** skip ahead of P0 without `.agents/state.json`
- After freeze, continue `post_mvp_order`, then `later_order`, then `advanced_order`, via [`AGENTS_LOOP-Continue-Milestone.md`](./AGENTS_LOOP-Continue-Milestone.md)
- After all milestone queues, drain content with [`LOOPS/LOOP-Graph.md`](./LOOPS/LOOP-Graph.md)
- Never mix a milestone loop and a content graph in the same session
- Coaching sessions are content graphs only
- A milestone is not done until its gauntlet critic returns WIN
