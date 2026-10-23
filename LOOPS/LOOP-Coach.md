# LOOP — Coach (exhaustive)

Deepen `/coach/{slug}`. A coach is a deterministic choice tree for the same slug as the guide. It teaches which method to pick. It is not a quiz with buttons and not a chat with a model.

Companions: [`RESOURCES.md`](../RESOURCES.md) · [`LOOP-Graph.md`](./LOOP-Graph.md) · `content/coaching/`

---

## How to run

```text
/loop exhaustive Coach content using LOOPS/LOOP-Coach.md
```

**Before starting**

1. Leave any running dev server alone; verify with `./vendor/bin/phpunit`.
2. Stop yourself anytime; hard-stop on the 10-round error budget or Done(section).

**On each tick**

- Re-inventory coaching files by `slug`.
- Add **exactly one** new tree.
- Run the validator and `./vendor/bin/phpunit`.

---

## Product intent

The learner starts from a SAMPLE setup, picks a method, and on a wrong pick reads why it fails and returns to an earlier layer. Success has no further choices. The page never offers a free-text box or an LLM reply. A missing tree stays a "not authored yet" slot until this loop fills it.

---

## Coverage queue (strict order)

Floor: **1 coach per guide**, same slug.

1. Track A, catalog order
2. Track B, catalog order
3. Track C, catalog order
4. Track D, catalog order

Skip `arrays` if E5 already shipped a valid tree. Do not replace it unless the validator fails.

**Done(section):** Every guide slug has a validator-clean tree with ≥2 continue layers, ≥2 `wrong` nodes that `rewind_to` a real node, and ≥1 `success` with empty choices.

---

## Loop prompt

```markdown
# OBJECTIVE
One choice tree per guide, same slug, wrong path rewinds.

**Done (section):** Coverage queue complete.

**Done (per tick):** Exactly one new coaching session.

# CONTEXT
- Registry / files: `content/coaching/<slug>.json`
- Catalog: `RESOURCES.md`
- Source ideas: the guide just written, plus the curriculum "step-by-step" line
- Contract: `RESOURCES.md` → Step-by-step (coach)
- Skill: `.agents/skills/add-coaching-session/SKILL.md`
- Auto-verify: `./vendor/bin/phpunit`

# STEP-BY-STEP CADENCE
1. **Orient** — first guide whose coaching file is missing or invalid.
2. **Author** — SAMPLE first message. The choice is which method, structure, or tradeoff. Two wrong branches name the mistake and rewind. One success.
3. **Wire** — `meta.slug` equals the guide slug.
4. **Verify** — validator accepts the file; page renders; guide CTA no longer shows the empty slot.
5. **On FAIL** — ≤10 rounds.

# VERIFICATION RUBRIC
- [ ] One tree; `meta.slug` matches the guide
- [ ] ≥2 `wrong` nodes with `rewind_to`; ≥1 `success` with empty choices; no dangling rewind
- [ ] Teaches a method choice, not a restated quiz
- [ ] Original copy
- [ ] `./vendor/bin/phpunit` exits 0

# STOP CONDITIONS
- Done(section) → STOP with slug → file list.
- Error budget 10 → handoff.
- Abort: runtime LLM; free-text answers; new slug; rewriting the nav engine for one tree.

# TICK OUTPUT
1. unit id + `slug` + track
2. PASS | FIXING (n/10) | STOP
3. Next family
4. verify status
```
