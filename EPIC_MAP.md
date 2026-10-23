# Dev Theory Coach (THEORY_CORE) — Epic Map

Living product map for AI-assisted incremental delivery.  
Companions: [`IMPLEMENTATION_STORIES.md`](./IMPLEMENTATION_STORIES.md) · [`.agents/state.json`](./.agents/state.json) · [`RESOURCES.md`](./RESOURCES.md) · [`context/README.md`](./context/README.md)

| Field | Value |
|-------|-------|
| **Status** | E0 complete · next E1.M1 |
| **Platform** | Web (PHP 8.2 server-rendered + vanilla JS, localStorage; PHPUnit) |
| **Primary learner** | <!-- agent: persona + session length --> |
| **Curriculum principle** | <!-- agent: one sentence, e.g. "Recognize the shape, then scale; safety cases before hands-on labs" --> |
| **Finalize** | Approach A — thin playable Beginner path + MVP freeze; content drain via `LOOPS/` after runtime |

---

## App summary

| Field | Value |
|-------|-------|
| **App name** | Dev Theory Coach (THEORY_CORE) |
| **One-liner** | <!-- agent: --> |
| **Success signal** | Learner completes the Beginner path with persisted progress; <!-- agent: what they can now do without help --> |

---

## Resource types for this topic

<!-- agent: from reference/pedagogy-map.md; one row per chosen type; delete types you did not choose. -->

| Type | Why it fits developer interview concepts: data structures, algorithms, system design, and design patterns | Rung | Tier |
|------|----------------------|------|------|
| Guide | | recognize | P0 |
| Coach | | explain | P0 |
| | | | |

Not chosen: <!-- agent: name the types you rejected and why in one line each -->

---

## Scope

### MVP (P0 — playable Beginner path)

- Family catalog (`slug` + track) rendered on the Guides hub
- Guides for every Beginner family; coach CTA on the same slug
- One coaching tree playable (schema + nav engine + one tree); remaining trees via loops
- <!-- agent: the one practice type in P0 (drill engine / case engine / lab runner) with a thin pack -->
- Fixed Beginner path + progress + unlocks
- Dashboard wired to the real next action

### Post-MVP

- <!-- agent: remaining resource types as epics, volume milestones, second path -->

### Constraints

- SAMPLE / educational data only; no live APIs, credentials, or hardware I/O
- Progress in `localStorage` (`theory_core_progress_v1`); no auth in MVP
- Step coaching = deterministic graphs only — no runtime LLM
- `context/` is a source of ideas; rewrite lessons (do not paste)
- `slug` is the join key; resource type is the cognitive mode
- Do not skip P0 order without updating `.agents/state.json`
- <!-- agent: topic-specific safety constraint if any -->

---

## Beginner learning order (user-facing)

<!-- agent: 6–10 steps, each naming a family or gate -->

1. 
2. 

---

## User needs

| Kind | Needs |
|------|-------|
| **Use** | <!-- agent: --> |
| **Usability** | Fast loop; works offline on bundled content; next action always obvious |
| **Meaning** | <!-- agent: --> |
| **Social** | Path complete feels like a credential |

---

## Flows

- **First-run:** Beginner path → Home with first family unlocked
- **Primary loop:** Next milestone → guide (with coach) / practice → score → unlock
- **Coaching loop:** Guide → same-slug tree → choice nodes → wrong rewind → success
- <!-- agent: one line per remaining type -->
- **Errors:** Corrupt progress → reset; locked → show prerequisite; unknown slug → home

---

## State & persistence

| Layer | Contents |
|-------|----------|
| Persisted business | Path, milestone status, scores, accuracy, latency, streaks |
| Persisted prefs | Active path ID |
| Temporary UI | Item index, timer, coach history |
| Offline | Bundled guides, trees, <!-- agent: packs --> |

**UI states:** empty path · error · success (milestone complete) · first-run · locked · path complete

---

## Epic list

<!-- agent: keep E0; one epic per runtime surface; later/advanced at the end. IDs are permanent. -->

| ID | Name | Goal / outcome | Priority | Status |
|----|------|----------------|----------|--------|
| E0 | Planning & Decisions | Spec, epics, loops, agent state, context | Mandatory | Complete |
| E1 | Family catalog | Stable `slug` + track + Guides hub rows | MVP | todo |
| E2 | Learning path planner | Fixed Beginner path; later paths | MVP (thin) | todo |
| E3 | Progress & unlocks | Resume-able progress + gate flags | MVP | todo |
| E4 | Guides | Hub + guide pages + coach CTA | MVP | todo |
| E5 | Step-by-step coaching | Schema, nav, one tree in P0; library via loops | MVP thin + later | todo |
| E6 | <!-- agent: first practice type --> | | MVP | todo |
| E7 | Dashboard | Home = real path + next CTA | MVP | todo |
| E8 | <!-- agent: next type --> | | Later | todo |

---

## Epic details

### E0. Planning & Decisions

- **Includes:** This map, stories, `RESOURCES.md`, `LOOPS/`, `AGENTS_LOOP-Continue-Milestone.md`, `.agents/state.json`, `context/`
- **Out of scope:** Product code

<!-- agent: one block per epic: Goal, Includes, Out of scope. Keep each ≤5 lines. -->

---

## Implementation order (milestones)

See [`IMPLEMENTATION_STORIES.md`](./IMPLEMENTATION_STORIES.md).

### P0 — MVP freeze

```
E1.M1 → E3.M1 → E4.M1 → E5.M1 → E5.M2 → E6.M1 → E2.M1 → E3.M2 → E7.M1
── MVP FREEZE: Beginner path complete credential ──
```

**Rationale:** <!-- agent: one sentence -->

### Post-P0 (`post_mvp_order`)

```
<!-- agent -->
```

### Later (`later_order`)

```
<!-- agent -->
```

### Advanced (`advanced_order`)

```
<!-- agent -->
```

---

## Coverage check

| Requirement | Epics |
|-------------|-------|
| `slug` + tracks | E1, E4 |
| Path + progress | E2, E3, E7 |
| Guides + coach | E4, E5 |
| <!-- agent: each remaining type --> | |
| Content drain after runtime | `LOOPS/` |

---

## Agent notes

- Do **not** skip ahead of P0 without `.agents/state.json`
- After freeze, continue queues via [`AGENTS_LOOP-Continue-Milestone.md`](./AGENTS_LOOP-Continue-Milestone.md)
- After all milestone queues, drain content with [`LOOPS/LOOP-Graph.md`](./LOOPS/LOOP-Graph.md)
- Never mix a milestone loop and a content graph in the same session
- Coaching sessions are content graphs only
