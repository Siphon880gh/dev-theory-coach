# Resource types

A **resource** is a teaching unit the learner can open. <!-- agent: list the chosen types, e.g. Guides, step-by-step coach sessions, drills, cases, labs, simulations. --> are resources.

**Not resources:** learning paths, the dashboard, and nav. Those find or sequence resources.

All data is **SAMPLE / educational**. <!-- agent: one sentence on what this app is not (not a calculator / not a brokerage / not a replacement for a licensed electrician). -->

Teachable unit is `slug` (what you are learning). **Track** is which learner conversation the unit belongs to. Cognitive ability is the **resource type** (how you practice). The same `slug` should echo across types — see [`LOOPS/LOOP-Graph.md`](./LOOPS/LOOP-Graph.md).

---

## Tracks

One **Guides** hub. <!-- agent: N --> tracks. Every guide has a step-by-step coach on the **same slug**.

| Track | Label | What it trains |
|-------|-------|----------------|
| **A** `<!-- agent: id -->` | <!-- agent: full label --> | <!-- agent: --> |
| **B** `<!-- agent: id -->` | <!-- agent: full label --> | <!-- agent: --> |

<!-- agent: state the gate between tracks, e.g. "Track A drill accuracy gates Track C graded cases." -->

---

## Families (`slug` catalog)

Prerequisite order within each track. The first <!-- agent: n --> Track A families form the Beginner path.

| # | `slug` | Track | Unit (one line) | Safety-critical | Rungs required |
|---|---------------|-------|-----------------|-----------------|----------------|
| 1 | `<!-- agent: kebab-id -->` | A | <!-- agent: --> | no | recognize, explain, apply |
| 2 | | | | | |

<!-- agent: complete for every family; this table is the source for LOOP-Graph echo families and for state.json → curriculum.families. -->

---

## Mastery ladder and floors

| Rung | Learner can… | Proven by |
|------|--------------|-----------|
| recognize | name the unit and when it applies | Guide |
| explain | reconstruct the reasoning unaided | Coach |
| apply | produce the answer under time / in procedure | <!-- agent: Drill / Lab --> |
| diagnose | find what is wrong in a scenario | <!-- agent: Case / Simulation --> |
| design | combine units to hit a goal | <!-- agent: Case pack / Lab / Simulation target --> |

**Coverage floors** (mirror in `.agents/state.json → decisions.content_floors`):

| Floor | Value |
|-------|-------|
| `guides_per_family` | 1 |
| `coach_per_guide` | 1 |
| <!-- agent: `drills_per_family` --> | <!-- agent: 8 --> |
| <!-- agent: `cases_per_track` --> | <!-- agent: 6 --> |

**Done(graph):** every family meets every floor for every rung its row requires.

---

## Catalog

| Type | What the learner does | Route | Registry |
|------|------------------------|-------|----------|
| **Guide** | Read the unit: rule, why, worked example, when to use; open the coach | `/guides`, `/guides/:slug` | <!-- agent: path once runtime exists --> |
| **Step-by-step** | Walk a method tree: wrong → explain → rewind → success | `/coach/:slug` (same slug as the guide) | <!-- agent: --> |
| <!-- agent: one row per remaining chosen type --> | | | |

Path milestones point at resources with `contentRefs` such as `guide:<slug>`, `coach:<slug>`, `drill:<group>`, `case:<pack>`.

---

## Per-type contracts

### Guide (hub)

`slug` (= `slug`), `track`, `title`, `summary`, `body` (300–800 words, original), `relatedCoachSlug` (= slug). If the coach tree is not authored yet, the CTA still opens `/coach/:slug` with a "not authored yet" state. Never 404.

### Step-by-step (coach)

Deterministic graph: `start` + ≥2 continue layers, ≥2 `wrong` nodes with `rewind_to`, ≥1 `success` with empty choices. `meta.slug` = slug. First message is a SAMPLE setup. Teach **which method to pick**, not a quiz restated as buttons. No runtime LLM.

<!-- agent: add a contract block per remaining chosen type (drill item shape, case shape, lab checkpoint shape, simulation model + target shape). Keep each to ≤6 lines. -->
