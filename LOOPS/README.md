# LOOPS — section content loops

Reusable `/loop` prompts that deepen **content** per app section. Runtime and milestone queues stay in [`AGENTS_LOOP-Continue-Milestone.md`](../AGENTS_LOOP-Continue-Milestone.md). These loops assume every milestone queue is already `done` (MVP freeze, drills, cases, cheatsheets, locked-reason).

**Preferred for first-pass exhaustive content:** one graph session that picks the next section each tick — [`LOOP-Graph.md`](./LOOP-Graph.md). Default policy drains **Guides** (track order), then **pairs each guide with its coach**, then echoes remaining hops, then drains leftovers.

```text
/loop content graph using LOOPS/LOOP-Graph.md
```

To pin a policy: `policy=DRAIN` or `policy=ECHO`. Default is `FOUNDATION_THEN_ECHO`.

To run a **single** section instead, use that file only. Do not start the graph and a section loop in the same session.

## How to run (one section)

| Section (nav / route) | Loop file | `/loop` line |
|-----------------------|-----------|--------------|
| **Graph (all sections)** | [`LOOP-Graph.md`](./LOOP-Graph.md) | `/loop content graph using LOOPS/LOOP-Graph.md` |
| Guides `/guides` | [`LOOP-Guides.md`](./LOOP-Guides.md) | `/loop exhaustive Guides content using LOOPS/LOOP-Guides.md` |
| Coach `/coach/:slug` | [`LOOP-Coach.md`](./LOOP-Coach.md) | `/loop exhaustive Coach content using LOOPS/LOOP-Coach.md` |
| Quiz `/quiz/:slug` | [`LOOP-Quiz.md`](./LOOP-Quiz.md) | `/loop exhaustive Quiz content using LOOPS/LOOP-Quiz.md` |
| Drills `/drill/:slug` | [`LOOP-Drills.md`](./LOOP-Drills.md) | `/loop exhaustive Drills content using LOOPS/LOOP-Drills.md` |
| Cases `/case/:slug` | [`LOOP-Cases.md`](./LOOP-Cases.md) | `/loop exhaustive Cases content using LOOPS/LOOP-Cases.md` |
| Cheatsheets `/cheatsheet/:slug` | [`LOOP-Cheatsheets.md`](./LOOP-Cheatsheets.md) | `/loop exhaustive Cheatsheets content using LOOPS/LOOP-Cheatsheets.md` |
| Home `/` | [`LOOP-Home.md`](./LOOP-Home.md) | `/loop exhaustive Home content using LOOPS/LOOP-Home.md` |

Prefer **no interval** so each wake adds the next content unit.

**Before starting**

1. Leave any running dev server alone. Verify with `./vendor/bin/phpunit`.
2. Stop the loop yourself when you want. Hard-stop on the error budget or Done(section).
3. Do not run [`AGENTS_LOOP-Continue-Milestone.md`](../AGENTS_LOOP-Continue-Milestone.md) in the same session.

## Shared rules (every section)

- Educational / non-live practice data only (no on-screen SAMPLE stamp required).
- Rewrite from `context/` — do not paste those files verbatim. Curricula win on order and on the outcome.
- `slug` is the join key. Do not invent a parallel enum. Do not add a family.
- Tracks are labeled in full on Guides.
- Guide → Coach uses the **same slug**.
- One content unit per tick.
- Distinct items: do not clone-and-tweak a prompt with a renamed title.
- After any code change: `./vendor/bin/phpunit`.
- Create or adapt `.agents/skills/*` when the same authoring workflow repeats.
- Do not change the Beginner path unlock order.
- `queues` is the structure. `work-queues` is the design lesson.

## Not these files

Milestone delivery stays in [`AGENTS_LOOP-Continue-Milestone.md`](../AGENTS_LOOP-Continue-Milestone.md).
