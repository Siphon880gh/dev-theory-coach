# LOOP — Guides (exhaustive)

Deepen `/guides`. A guide teaches one slug: the rule, why it holds, one **concrete** worked example, when to use it, and when to refuse it. It is not a problem statement and not a second hub.

Companions: [`RESOURCES.md`](../RESOURCES.md) · [`LOOP-Graph.md`](./LOOP-Graph.md) · `content/guides/` · `content/families.json`

---

## How to run

```text
/loop exhaustive Guides content using LOOPS/LOOP-Guides.md
```

**Before starting**

1. Leave any running dev server alone; verify with `./vendor/bin/phpunit`.
2. Stop yourself anytime; hard-stop on the 10-round error budget or Done(section).

**On each tick**

- Re-inventory guides by `slug` and track.
- Add **exactly one** new guide (never batch Stage 0).
- Set `relatedCoachSlug` to the same slug.
- Run `./vendor/bin/phpunit`.

---

## Product intent

The learner sees the track's full label, the title, and a body they can finish in one sitting. The coach CTA is always there. The guide never becomes a LeetCode writeup, a catalog of every variant, or a paste of `context/curriculums/`.

Never put SAMPLE / "SAMPLE example" / "SAMPLE lesson" / "Recall checkpoint" in the body or parts. Never pad with identical repeated paragraphs or slogan loops to hit 300 words. If short, expand the **worked example** with real steps and concrete values. Prefer ~400–600 teaching words inside the 300–800 band. No `sample: true` stamps.

---

## Coverage queue (strict order)

Floor: **≥1 guide per family** (see `.agents/state.json → decisions.content_floors`). Floors count *quality* units, not a race to green PHPUnit.

1. Track A families in catalog order
2. Track B families in catalog order
3. Track C families in catalog order
4. Track D families in catalog order

Skip a family that already has a guide, including the 12 beginner guides from E4.

**Done(section):** Every family in RESOURCES has one guide file, track set, body 300–800 words of real teaching (no SAMPLE/checkpoint padding), five required parts with a **concrete** worked example, and a coach CTA on the same slug.

---

## Loop prompt

```markdown
# OBJECTIVE
One guide per family, on a four-track Guides hub.

**Done (section):** Coverage queue complete.

**Done (per tick):** Exactly one new guide.

# CONTEXT
- Registry / files: `content/guides/<slug>.json`, loaded by `src/Catalog.php`
- Catalog: `RESOURCES.md` families table and `content/families.json`
- Source ideas: `context/curriculums/<track>.md` for the outcome and the practice seed; clones only for a worked-example idea
- Contract: `RESOURCES.md` → Per-type contracts → Guide
- Skill: `.agents/skills/add-guide/SKILL.md` (create on first repeat)
- Auto-verify: `./vendor/bin/phpunit`

# STEP-BY-STEP CADENCE
1. **Orient** — first family below floor (track order, then catalog order).
2. **Author** — one guide only. 300–800 words: rule, why, one concrete worked example (numbers / small structures / named steps), when to use, when to refuse. Match the curriculum outcome. Do not add a new slug. Never batch. Never pad with SAMPLE labels, Recall checkpoints, or identical paragraphs — expand the example if under 300 words.
3. **Wire** — `slug`, `track`, `relatedCoachSlug` = slug. No `sample` stamp.
4. **Verify** — word count, five parts, paste test (no 12-word span from `context/`), CTA href `/coach/<slug>`, quality gates below.
5. **On FAIL** — ≤10 rounds.

# VERIFICATION RUBRIC
- [ ] One unit; unique id; `slug` + track set
- [ ] 300–800 words; rule, why, example, use, refuse
- [ ] Worked example has concrete values (digit or named structure in `parts.example`)
- [ ] No SAMPLE / SAMPLE example / SAMPLE lesson / Recall checkpoint in body or parts
- [ ] No identical paragraph appearing ≥3 times; no `sample: true`
- [ ] Original copy
- [ ] `./vendor/bin/phpunit` exits 0

# STOP CONDITIONS
- Done(section) → STOP with family → guide path list.
- Error budget 10 → handoff.
- Abort: rewriting the renderer "for cleanup"; adding a second hub; new `slug` not in RESOURCES; batching guides; padding / SAMPLE-in-prose to meet the floor.

# TICK OUTPUT
1. unit id + `slug` + track
2. PASS | FIXING (n/10) | STOP
3. Next family
4. verify status
```
