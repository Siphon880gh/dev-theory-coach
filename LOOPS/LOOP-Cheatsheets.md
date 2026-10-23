# LOOP — Cheatsheets (exhaustive)

Deepen `/cheatsheet/{slug}`. One screen: at most 12 recall lines, one pick-when sentence, one skip-when sentence. It is a recap, not a second guide.

Companions: [`RESOURCES.md`](../RESOURCES.md) · [`LOOP-Graph.md`](./LOOP-Graph.md) · `content/cheatsheets/`

---

## How to run

```text
/loop exhaustive Cheatsheets content using LOOPS/LOOP-Cheatsheets.md
```

**Before starting**

1. Leave any running dev server alone; verify with `./vendor/bin/phpunit`.
2. Stop yourself anytime; hard-stop on the 10-round error budget or Done(section).

**On each tick**

- Re-inventory sheets by `slug`.
- Add **exactly one** sheet.
- Run `./vendor/bin/phpunit`.

---

## Product intent

The learner can scan the sheet the morning of an interview and remember when the concept applies and when it does not. The page says SAMPLE. It does not introduce a rule the guide never taught.

---

## Coverage queue (strict order)

Floor: **1 cheatsheet per family**.

1. Track A through D, catalog order.

Skip `arrays` if E10 already shipped a valid sheet.

**Done(section):** Every family has one sheet within the line limits, with both sentences, original wording.

---

## Loop prompt

```markdown
# OBJECTIVE
One recap screen per family.

**Done (section):** Coverage queue complete.

**Done (per tick):** Exactly one new cheatsheet.

# CONTEXT
- Registry / files: `content/cheatsheets/<slug>.json`
- Contract: `RESOURCES.md` → Cheatsheet
- Skill: `.agents/skills/add-cheatsheet/SKILL.md`
- Source: the guide for that slug, compressed. Do not add new claims.
- Auto-verify: `./vendor/bin/phpunit`

# STEP-BY-STEP CADENCE
1. **Orient** — first family without a valid sheet.
2. **Author** — ≤12 recall lines, pick-when, skip-when.
3. **Wire** — slug and track.
4. **Verify** — line count, both sentences, paste test.
5. **On FAIL** — ≤10 rounds.

# VERIFICATION RUBRIC
- [ ] One sheet; slug + track set
- [ ] ≤12 lines; pick-when; skip-when
- [ ] Original copy; no new rule
- [ ] `./vendor/bin/phpunit` exits 0

# STOP CONDITIONS
- Done(section) → STOP with family → file list.
- Error budget 10 → handoff.
- Abort: turning the sheet into a guide; new slug.

# TICK OUTPUT
1. unit id + `slug` + track
2. PASS | FIXING (n/10) | STOP
3. Next family
4. verify status
```
