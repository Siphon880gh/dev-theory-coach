# LOOP — Graph (which section loop next)

Orchestrator for the section content loops. One `/loop` session; this file **picks** which section loop to execute each tick. Authoring rules always come from the chosen section file — this file does not invent content.

Companions: [`README.md`](./README.md) · [`LOOP-Guides.md`](./LOOP-Guides.md) · [`LOOP-Coach.md`](./LOOP-Coach.md) · [`LOOP-Quiz.md`](./LOOP-Quiz.md) · [`LOOP-Drills.md`](./LOOP-Drills.md) · [`LOOP-Cases.md`](./LOOP-Cases.md) · [`LOOP-Cheatsheets.md`](./LOOP-Cheatsheets.md) · [`LOOP-Home.md`](./LOOP-Home.md)

Requires **every milestone queue done** (runtime exists, including drills, cases, and cheatsheets). If any queue id is still `todo`, stop and use [`AGENTS_LOOP-Continue-Milestone.md`](../AGENTS_LOOP-Continue-Milestone.md) instead. Do not scaffold runtime inside a content tick.

---

## How to run

```text
/loop content graph using LOOPS/LOOP-Graph.md
```

```text
/loop content graph using LOOPS/LOOP-Graph.md policy=DRAIN
/loop content graph using LOOPS/LOOP-Graph.md policy=ECHO
```

**Default policy: `FOUNDATION_THEN_ECHO`.** Drain Guides (track order), then **pair each guide with its coach**, then walk remaining family echo chains, then drain leftover floors.

**Before starting**

1. Leave any running dev server alone; verify with `./vendor/bin/phpunit`.
2. Stop yourself anytime; hard-stop on the error budget or Done(graph).
3. Do not also start a single-section `/loop` or the milestone loop in the same session.

---

## Policies

| Policy | When to use | Rule |
|--------|-------------|------|
| **`FOUNDATION_THEN_ECHO`** *(default)* | Normal exhaustive pass | Stage 0 drain Guides. Stage 1 pair Coach (one tree per guide). Stage 2 echo remaining hops. Stage 3 leftover floors. |
| **`DRAIN`** | Finish one surface before another | Stay on one section file until Done(section). Then next node in drain order. |
| **`ECHO`** | Keep related teaching in lockstep from tick 1 | No foundation drain. Each PASS follows the echo edge for the `slug` just shipped. |

Do not mix policies mid-run unless the human names a new one.

**Required pair:** Guide → Coach on the same slug. If unpaired guides exist after Stage 0, Stage 1 is exclusive Coach ticks (track order). Do not author more guides while unpaired coaches remain.

---

## Nodes (section loops)

| Node | File | Unit per tick | Runtime epic |
|------|------|---------------|--------------|
| Guides | `LOOP-Guides.md` | 1 guide | E4 |
| Coach | `LOOP-Coach.md` | 1 coaching session (slug = guide slug) | E5 |
| Quiz | `LOOP-Quiz.md` | 1 quiz pack (6 items) | E6 |
| Drills | `LOOP-Drills.md` | 1 drill pack (8 items), or a recorded skip | E8 |
| Cases | `LOOP-Cases.md` | 1 case, or a recorded skip | E9 |
| Cheatsheets | `LOOP-Cheatsheets.md` | 1 cheatsheet | E10 |
| Home | `LOOP-Home.md` | dashboard copy pass | E7 |

If a node's runtime epic is not `done` in `.agents/state.json`, **park** it: treat as Done for graph purposes and note `parked: <epic>_missing` in tick output. Do not scaffold runtime inside a content tick. The intended session starts after E11, so nothing should be parked.

---

## Drain order

Used by `DRAIN`, and by Stage 0 / Stage 3 of `FOUNDATION_THEN_ECHO`.

```text
Guides → Coach → Quiz → Drills → Cases → Cheatsheets → Home
```

Why this order:

1. **Guides** — one hub, track order. The rule and the refusal live here.
2. **Coach** — the choice tree for each guide, same slug, before more practice.
3. **Quiz** — the apply check every family has. Six items, reasons on the wrong choices.
4. **Drills** — timed closed form, only the seven drill families. Every other family is a skip.
5. **Cases** — diagnose and design, only tracks C and D. Tracks A and B are skips.
6. **Cheatsheets** — one screen after the unit already exists.
7. **Home** — last, so dashboard copy can name real ids.

Never skip a node in `DRAIN` while it is short of its own Done(section). A recorded skip counts toward that family's floor.

---

## Echo families (similar resource hops)

A **family** is one `slug` expressed on several surfaces. After a PASS, hop to the next **unfilled** hop in that family's chain.

Walk tracks in order (A, then B, then C, then D). Beginner guides and the `arrays` coach, `arrays` quiz, `complexity` drill, `requirements` case, and `arrays` cheatsheet may already exist from the milestone loop. Skip a hop that already meets its floor.

| # | `slug` | Track | Typical chain (skip missing hops) |
|---|--------|-------|-----------------------------------|
| 1 | `arrays` | A | Guide → Coach → Quiz → Cheatsheet |
| 2 | `strings` | A | Guide → Coach → Quiz → Cheatsheet |
| 3 | `hash-maps` | A | Guide → Coach → Quiz → Cheatsheet |
| 4 | `linked-lists` | A | Guide → Coach → Quiz → Cheatsheet |
| 5 | `stacks` | A | Guide → Coach → Quiz → Cheatsheet |
| 6 | `queues` | A | Guide → Coach → Quiz → Cheatsheet |
| 7 | `trees` | A | Guide → Coach → Quiz → Cheatsheet |
| 8 | `binary-search-trees` | A | Guide → Coach → Quiz → Cheatsheet |
| 9 | `heaps` | A | Guide → Coach → Quiz → Cheatsheet |
| 10 | `graphs` | A | Guide → Coach → Quiz → Cheatsheet |
| 11 | `tries` | A | Guide → Coach → Quiz → Cheatsheet |
| 12 | `union-find` | A | Guide → Coach → Quiz → Cheatsheet |
| 13 | `complexity` | B | Guide → Coach → Quiz → Drill → Cheatsheet |
| 14 | `recursion` | B | Guide → Coach → Quiz → Cheatsheet |
| 15 | `binary-search` | B | Guide → Coach → Quiz → Drill → Cheatsheet |
| 16 | `two-pointers` | B | Guide → Coach → Quiz → Drill → Cheatsheet |
| 17 | `sliding-window` | B | Guide → Coach → Quiz → Drill → Cheatsheet |
| 18 | `sorting` | B | Guide → Coach → Quiz → Drill → Cheatsheet |
| 19 | `bfs-dfs` | B | Guide → Coach → Quiz → Cheatsheet |
| 20 | `backtracking` | B | Guide → Coach → Quiz → Cheatsheet |
| 21 | `greedy` | B | Guide → Coach → Quiz → Cheatsheet |
| 22 | `dynamic-programming` | B | Guide → Coach → Quiz → Cheatsheet |
| 23 | `graph-algorithms` | B | Guide → Coach → Quiz → Cheatsheet |
| 24 | `intervals` | B | Guide → Coach → Quiz → Cheatsheet |
| 25 | `interview-frame` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 26 | `estimates` | C | Guide → Coach → Quiz → Drill → Case → Cheatsheet |
| 27 | `requirements` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 28 | `api-and-boundaries` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 29 | `data-model` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 30 | `read-and-write-paths` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 31 | `caching` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 32 | `storage-and-indexes` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 33 | `replication-and-consistency` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 34 | `sharding` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 35 | `work-queues` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 36 | `rate-limiting` | C | Guide → Coach → Quiz → Drill → Case → Cheatsheet |
| 37 | `failure` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 38 | `capstone-url-shortener` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 39 | `capstone-feed` | C | Guide → Coach → Quiz → Case → Cheatsheet |
| 40 | `when-to-name-a-pattern` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 41 | `strategy` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 42 | `state` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 43 | `observer` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 44 | `factory` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 45 | `builder` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 46 | `adapter` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 47 | `facade` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 48 | `decorator` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 49 | `singleton` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 50 | `command` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 51 | `composite` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 52 | `proxy` | D | Guide → Coach → Quiz → Case → Cheatsheet |
| 53 | `composition` | D | Guide → Coach → Quiz → Case → Cheatsheet |

**Echo edge rules**

- First hop after a new guide is always **Coach** (same slug) if that session is missing.
- One hop = one tick = one unit in the **target** section file.
- Section-specific queue order still binds (track order, then catalog order).
- Drill hops: if the family is not in `decisions.drill_families`, record skip and continue the chain.
- Case hops: if the family is not in `decisions.decision_families`, record skip and continue.
- Design families (`capstone-url-shortener`, `capstone-feed`, `composition`) use the Case hop. The case must name two earlier slugs. There is no separate Design node.
- Home hops only after the named surface exists.

---

## Stage machine (`FOUNDATION_THEN_ECHO`)

```text
Stage 0  DRAIN Guides until Done(guides) — track order
Stage 1  PAIR Coach: exclusive coach ticks until every guide slug has a session
Stage 2  ECHO families 1 → 53 (remaining hops)
Stage 3  DRAIN leftover section floors in drain order
Done(graph) when every section file's Done(section) is true
```

If Stage 0 is already met, start at Stage 1. Stage 2 may complete some floors early. Stage 3 only fills gaps.

---

## Per-tick algorithm

1. **Read** this file + every section file's coverage queue (do not trust stale counts). Count files on disk.
2. **Policy** — default `FOUNDATION_THEN_ECHO`; honor `policy=` from the user line.
3. **Pick target**
   - `DRAIN`: first node in drain order that is not Done(section).
   - `ECHO`: current family + next unfilled hop; if none, next family; if none, drain leftovers.
   - `FOUNDATION_THEN_ECHO`: Stage 0–3 as above. Remember `stage`, `family_id`, and `last_unit` across ticks (chat state is enough).
4. **Execute** the target section's **Loop prompt** for **one** unit.
5. **On PASS** — record `{ loop, family_id, unit_id, next_hop }`.
6. **On FAIL** — stay on the **same** section loop; consume that loop's 10-round budget.
7. **Done(graph)** — all Done(section) → STOP with a per-node tally against `decisions.content_floors`.

---

## Loop prompt

```markdown
# OBJECTIVE
Run the LOOPS/ section content loops as one graph until every section's Done(section) is true.

**Done (graph):** Every section's coverage floor met per its file (floors in `.agents/state.json → decisions.content_floors`). 53 guides, 53 coaches, 53 quiz packs of ≥6, 7 drill packs of ≥8 (other families skipped), 29 cases (tracks C and D; other families skipped), 53 cheatsheets, and one Home copy pass.

**Done (per tick):** Exactly one content unit in **one** section loop, chosen by the policy's picker, verified with that section's rubric.

# CONTEXT
- Picker + families: `LOOPS/LOOP-Graph.md`
- Authoring: only the chosen `LOOPS/LOOP-*.md`
- Shared: SAMPLE; original copy from `context/`; `slug` join key; tracks labeled in full; Guide→Coach same slug; Beginner unlocks unchanged; one unit per tick; `queues` is not `work-queues`
- Auto-verify: `./vendor/bin/phpunit`
- Policy default: FOUNDATION_THEN_ECHO
- If any milestone queue is unfinished: STOP and run AGENTS_LOOP-Continue-Milestone.md instead

# STEP-BY-STEP CADENCE
1. **Orient** — inventory counts from source; determine stage/family; pick target loop + gap.
2. **Announce** which loop and why (drain node vs coach pair vs echo hop vs leftover).
3. **Author** using that section file's cadence only.
4. **Verify** using that section's rubric + `./vendor/bin/phpunit`.
5. **On PASS** — compute next hop (echo) or stay (drain until Done(section)). Unpaired guide → next_hop Coach.
6. **On FAIL** — same section; max 10 rounds → Error Handoff Summary (include graph stage/family).
7. **Do not** start a second section unit in the same tick after PASS unless time remains **and** you re-run the picker (still one unit).

# VERIFICATION RUBRIC
PASS only if:
- [ ] Picker chose a legal target
- [ ] Section rubric for that loop also PASSes
- [ ] No unlock/schema drift; no pasted context docs; tracks labeled in full
- [ ] `./vendor/bin/phpunit` exits 0

# STOP CONDITIONS
- Done(graph) → STOP with per-section floors vs counts.
- Error budget 10 on the blocking unit → handoff (stage, family, loop, id, stderr).
- Human verification: policy change; new `slug` not in RESOURCES.
- Abort: running two section loops without the picker; rewriting runtime "for cleanup"; running the milestone loop in the same session; adding a slug.

# TICK OUTPUT (keep short)
1. policy + stage (if any) + family_id (if any) + track
2. target loop + unit id
3. why this loop (guides drain | coach pair | echo hop from X | leftover drain)
4. PASS | FIXING (n/10) | STOP
5. next_hop (loop + family) or Done(section) / Done(graph)
6. verify status
```

---

## Authoring notes

- Section files still say "stop at Done(section)." Under the graph, that is **not** a process stop unless policy is `DRAIN` and you just finished that node — continue to the next node/family.
- Home / dashboard copy is last in drain order so it can name real ids.
- Milestone `AGENTS_LOOP-*.md` files are out of this graph.
- Content order does not change unlocks. Do not edit `content/paths/beginner.json` order.
- The `arrays` guide, `arrays` coach, `arrays` quiz, `complexity` drill, `requirements` case, and `arrays` cheatsheet are seeds. Do not rewrite them unless a test fails.
