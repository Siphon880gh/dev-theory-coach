# LOOP — Drills (exhaustive)

Deepen `/drill/{slug}`. A drill is a timed closed-form answer plus a thought chain shown after submit. It exists only for the seven drill families. Other families are a recorded skip.

Companions: [`RESOURCES.md`](../RESOURCES.md) · [`LOOP-Graph.md`](./LOOP-Graph.md) · `content/drills/`

---

## How to run

```text
/loop exhaustive Drills content using LOOPS/LOOP-Drills.md
```

**Before starting**

1. Leave any running dev server alone; verify with `./vendor/bin/phpunit`.
2. Stop yourself anytime; hard-stop on the 10-round error budget or Done(section).

**On each tick**

- Walk families in catalog order.
- If the family is not in `decisions.drill_families`, record a skip and count it.
- Otherwise add **exactly one** pack of ≥8 items.
- Run `./vendor/bin/phpunit`.

---

## Product intent

The learner produces a bound, a discarded half, an index move, or an order-of-magnitude QPS before the timer ends, then reads the three-step chain. A non-drill family never grows a pack. The not-a-drill slot from E8 stays.

Drill families: `complexity`, `binary-search`, `two-pointers`, `sliding-window`, `sorting`, `estimates`, `rate-limiting`.

---

## Coverage queue (strict order)

Floor: **≥8 items on each drill family. A skip on every other family.**

1. All families in catalog order. Author or skip.

Skip `complexity` if E8 already shipped ≥8 valid items.

**Done(section):** 7 packs of ≥8, and 46 recorded skips. No pack exists outside the drill list.

---

## Loop prompt

```markdown
# OBJECTIVE
Eight timed items on each drill family; an explicit skip everywhere else.

**Done (section):** Coverage queue complete.

**Done (per tick):** Exactly one new drill pack, or one recorded skip.

# CONTEXT
- Registry / files: `content/drills/<slug>.json` and `content/drills/skips.json` (one slug per skip line)
- Catalog: `decisions.drill_families`
- Contract: `RESOURCES.md` → Drill
- Skill: `.agents/skills/add-drill/SKILL.md`
- Auto-verify: `./vendor/bin/phpunit`

# STEP-BY-STEP CADENCE
1. **Orient** — first family with neither a valid pack nor a skip.
2. **Author or skip** — non-drill: append the slug to `skips.json` and stop the tick. Drill: ≥8 items, each with `accept`, `thoughtChain` of 3, and `seconds`.
3. **Wire** — slug must be on the drill list to have a pack.
4. **Verify** — shape test; a test fails if a non-drill slug has a pack.
5. **On FAIL** — ≤10 rounds.

# VERIFICATION RUBRIC
- [ ] One pack or one skip; slug is legal for that action
- [ ] Pack items have accept + 3-step chain + seconds
- [ ] Original copy
- [ ] `./vendor/bin/phpunit` exits 0

# STOP CONDITIONS
- Done(section) → STOP with pack counts and skip count.
- Error budget 10 → handoff.
- Abort: authoring a drill for a non-drill family; removing the not-a-drill slot.

# TICK OUTPUT
1. unit id + `slug` + track + pack or skip
2. PASS | FIXING (n/10) | STOP
3. Next family
4. verify status
```
