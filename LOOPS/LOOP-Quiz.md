# LOOP — Quiz (exhaustive)

Deepen `/quiz/{slug}`. A quiz is an untimed check of one slug. Each wrong choice explains itself. It is not a timed drill and not a code runner.

Companions: [`RESOURCES.md`](../RESOURCES.md) · [`LOOP-Graph.md`](./LOOP-Graph.md) · `content/quizzes/`

---

## How to run

```text
/loop exhaustive Quiz content using LOOPS/LOOP-Quiz.md
```

**Before starting**

1. Leave any running dev server alone; verify with `./vendor/bin/phpunit`.
2. Stop yourself anytime; hard-stop on the 10-round error budget or Done(section).

**On each tick**

- Re-inventory quiz packs by `slug`.
- Add **exactly one** pack of at least 6 items.
- Run `./vendor/bin/phpunit`.

---

## Product intent

The learner answers from the guide they just read. Prompts use educational numbers and systems (not live data). There is no timer. Submitting writes accuracy. A score ≥ 0.8 is the apply flag for families that are not also drills. Drill families still take the timed drill for the under-time flag.

---

## Coverage queue (strict order)

Floor: **≥6 quiz items per family**.

1. Track A, catalog order
2. Track B, catalog order
3. Track C, catalog order
4. Track D, catalog order

Skip `arrays` if it already has ≥6 valid items.

**Done(section):** Every family has a pack of ≥6 items, each with 4 choices, one answer, `whyRight`, and `whyWrong` on every distractor.

---

## Loop prompt

```markdown
# OBJECTIVE
Six original quiz items per family, reasons on every wrong choice.

**Done (section):** Coverage queue complete.

**Done (per tick):** Exactly one new quiz pack.

# CONTEXT
- Registry / files: `content/quizzes/<slug>.json`
- Catalog: `RESOURCES.md`
- Source ideas: curriculum Practice line, rewritten as misconceptions
- Contract: `RESOURCES.md` → Quiz
- Skill: `.agents/skills/add-quiz/SKILL.md`
- Auto-verify: `./vendor/bin/phpunit`

# STEP-BY-STEP CADENCE
1. **Orient** — first family under 6 valid items.
2. **Author** — ≥6 items. Each wrong choice is a real misconception for this slug (wrong cost, wrong structure, wrong force). No two items share a prompt stem.
3. **Wire** — `slug` matches the family. No timer field.
4. **Verify** — shape test, paste test, renderer smoke.
5. **On FAIL** — ≤10 rounds.

# VERIFICATION RUBRIC
- [ ] One pack; `slug` + track set; ≥6 items
- [ ] Exactly 4 choices; `whyWrong` on each distractor
- [ ] Original copy; items are distinct
- [ ] `./vendor/bin/phpunit` exits 0

# STOP CONDITIONS
- Done(section) → STOP with family → item count.
- Error budget 10 → handoff.
- Abort: adding a code runner; turning the quiz into a drill; new slug.

# TICK OUTPUT
1. unit id + `slug` + track
2. PASS | FIXING (n/10) | STOP
3. Next family
4. verify status
```
