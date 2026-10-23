# LOOP — Home (exhaustive)

Deepen `/`. Home names the next real slug and the credential. It is not a second catalog. This loop has a single copy pass, run last so the strings can name ids that exist.

Companions: [`RESOURCES.md`](../RESOURCES.md) · [`LOOP-Graph.md`](./LOOP-Graph.md)

---

## How to run

```text
/loop exhaustive Home content using LOOPS/LOOP-Home.md
```

**Before starting**

1. Leave any running dev server alone; verify with `./vendor/bin/phpunit`.
2. Stop yourself anytime; hard-stop on the 10-round error budget or Done(section).

**On each tick**

- If the home copy pass is already recorded, stop.
- Otherwise update dashboard strings once.
- Run `./vendor/bin/phpunit`.

---

## Product intent

First run names `arrays`. Path complete names the phone-screen credential in a sentence, not a percentage. The case-gate chip says locked or open using the three quiz slugs. Track labels appear in full when the home page mentions a track. Empty, error, and reset states stay.

---

## Coverage queue (strict order)

Floor: **1 home copy pass.**

1. One pass over dashboard strings.

**Done(section):** Tests assert first-run CTA contains `arrays`, path-complete copy does not contain a `%` character used as a score, and the four full track labels are available to the home template.

---

## Loop prompt

```markdown
# OBJECTIVE
Dashboard copy names real slugs and the credential.

**Done (section):** The single copy pass is done.

**Done (per tick):** That one pass.

# CONTEXT
- Files: the home template and `tests/` assertions from E7
- Contract: `EPIC_MAP.md` flows and E7 stories
- Auto-verify: `./vendor/bin/phpunit`

# STEP-BY-STEP CADENCE
1. **Orient** — read the current home strings.
2. **Author** — adjust only copy that is vague, decorative, or that names a slug which does not exist. Do not change unlock math.
3. **Verify** — E7 render tests still pass; SAMPLE remains; no new percentage.
4. **On FAIL** — ≤10 rounds.

# VERIFICATION RUBRIC
- [ ] One copy pass
- [ ] Next CTA still names a real slug
- [ ] Path-complete copy has no decorative percentage
- [ ] `./vendor/bin/phpunit` exits 0

# STOP CONDITIONS
- Done(section) → STOP.
- Error budget 10 → handoff.
- Abort: a second hub on home; changing beginner order.

# TICK OUTPUT
1. home copy pass
2. PASS | FIXING (n/10) | STOP
3. Done(section) or the string still wrong
4. verify status
```
