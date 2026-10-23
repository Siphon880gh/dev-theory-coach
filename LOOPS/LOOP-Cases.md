# LOOP — Cases (exhaustive)

Deepen `/case/{slug}`. A case is a SAMPLE scenario, a committed decision, and a consequence the learner sees only after they commit. Tracks A and B are recorded skips. Tracks C and D each get one case.

Companions: [`RESOURCES.md`](../RESOURCES.md) · [`LOOP-Graph.md`](./LOOP-Graph.md) · `content/cases/`

---

## How to run

```text
/loop exhaustive Cases content using LOOPS/LOOP-Cases.md
```

**Before starting**

1. Leave any running dev server alone; verify with `./vendor/bin/phpunit`.
2. Stop yourself anytime; hard-stop on the 10-round error budget or Done(section).

**On each tick**

- Walk families in catalog order.
- If the family is not in `decisions.decision_families`, record a skip.
- Otherwise add **exactly one** case.
- Run `./vendor/bin/phpunit`.

---

## Product intent

The learner commits before the grade. The page says SAMPLE. The case gate from E9 still hides the best choice when quizzes on `arrays`, `hash-maps`, and `queues` are under 0.8. Design families (`capstone-url-shortener`, `capstone-feed`, `composition`) name at least two earlier slugs in the best consequence. Other cases are diagnose: accept or reject a proposal, or pick the option that matches the force.

---

## Coverage queue (strict order)

Floor: **1 case per decision family. A skip on every track A and track B family.**

1. Track A — skip each
2. Track B — skip each
3. Track C — one case each, catalog order
4. Track D — one case each, catalog order

Skip `requirements` if E9 already shipped a valid case.

**Done(section):** 29 cases and 24 skips. No case file for a track A or B slug.

---

## Loop prompt

```markdown
# OBJECTIVE
One decide-and-reveal case per system-design and pattern family; skip the rest.

**Done (section):** Coverage queue complete.

**Done (per tick):** Exactly one new case, or one recorded skip.

# CONTEXT
- Registry / files: `content/cases/<slug>.json` and `content/cases/skips.json`
- Catalog: `decisions.decision_families` and `decisions.design_families`
- Contract: `RESOURCES.md` → Case
- Skill: `.agents/skills/add-case/SKILL.md`
- Auto-verify: `./vendor/bin/phpunit`

# STEP-BY-STEP CADENCE
1. **Orient** — first family with neither a valid case nor a skip.
2. **Author or skip** — A/B: skip. C/D: scenario, prompt, 3 or 4 choices, one best, a consequence on each. Design families cite two earlier slugs in the best consequence.
3. **Wire** — slug matches the family. Do not put the consequence in a field the pre-commit template prints.
4. **Verify** — shape test; pre-commit HTML does not contain the best consequence; paste test.
5. **On FAIL** — ≤10 rounds.

# VERIFICATION RUBRIC
- [ ] One case or one skip; slug is legal for that action
- [ ] Consequences exist for every choice; design cases name two earlier slugs
- [ ] Original copy
- [ ] `./vendor/bin/phpunit` exits 0

# STOP CONDITIONS
- Done(section) → STOP with case count and skip count.
- Error budget 10 → handoff.
- Abort: a case for track A or B; showing the grade before commit; new slug.

# TICK OUTPUT
1. unit id + `slug` + track + case or skip
2. PASS | FIXING (n/10) | STOP
3. Next family
4. verify status
```
