# LOOP — <!-- agent: Section name --> (exhaustive)

<!-- agent: copy this file once per resource type as LOOP-<Type>.md (LOOP-Guides.md, LOOP-Coach.md, LOOP-Drills.md, LOOP-Cases.md, LOOP-Labs.md, LOOP-Simulations.md, LOOP-Games.md, LOOP-Home.md). Delete LOOP-Section.md when done. -->

Deepen `<!-- agent: route -->`. <!-- agent: one sentence on what this surface is and is not. -->

Companions: [`RESOURCES.md`](../RESOURCES.md) · [`LOOP-Graph.md`](./LOOP-Graph.md) · <!-- agent: registry files once runtime exists -->

---

## How to run

```text
/loop exhaustive <!-- agent: Section --> content using LOOPS/LOOP-<!-- agent: Section -->.md
```

**Before starting**
1. Leave any running dev server alone; verify with `./vendor/bin/phpunit`.
2. Stop yourself anytime; hard-stop on the 10-round error budget or Done(section).

**On each tick**
- Re-inventory this section by `slug` and track.
- Add **exactly one** new unit.
- Wire its join key and any related-slug links (Guide ↔ Coach same slug).
- Run `./vendor/bin/phpunit`.

---

## Product intent

<!-- agent: 3–6 lines. What the learner sees on this surface; what must always be present (e.g. SAMPLE label, coach CTA, thought-chain reveal); what this surface must never become (second hub, calculator, live data). -->

---

## Coverage queue (strict order)

Floor: **<!-- agent: e.g. ≥1 guide per family / ≥8 drill items per family / ≥6 cases per track -->** (see `.agents/state.json → decisions.content_floors`).

1. Track A families in catalog order
2. Track B families in catalog order
3. <!-- agent: further phases, e.g. second guide only when two distinct methods; advanced packs after floors -->

Skip a family that already meets the floor. Skip-only families (Games) record a skip and count toward Done.

**Done(section):** <!-- agent: computable sentence, e.g. "Every family in RESOURCES has ≥1 guide with track set and a coach CTA on the same slug." -->

---

## Loop prompt

```markdown
# OBJECTIVE
<!-- agent: one line, e.g. "One principle guide per family, on a multi-track Guides hub." -->

**Done (section):** Coverage queue complete.

**Done (per tick):** Exactly one new unit.

# CONTEXT
- Registry / files: <!-- agent: paths -->
- Catalog: `RESOURCES.md` families table
- Source ideas: `context/` — original wording only
- Contract: `RESOURCES.md` → Per-type contracts → <!-- agent: type -->
- Skill: `.agents/skills/add-<!-- agent: type -->/SKILL.md` (create on first repeat)
- Auto-verify: `./vendor/bin/phpunit`

# STEP-BY-STEP CADENCE
1. **Orient** — first family below floor (track order).
2. **Author** — <!-- agent: unit-specific cadence: length, required parts, SAMPLE setup, distinctness rule -->
3. **Wire** — join key, track, related slugs.
4. **Verify** — <!-- agent: what to check on screen / in validator -->; no verbatim paste from `context/`.
5. **On FAIL** — ≤10 rounds.

# VERIFICATION RUBRIC
- [ ] One unit; unique id; `slug` + track set
- [ ] <!-- agent: type-specific checks (coach: validator passes, ≥2 wrong nodes with rewind, ≥1 success) -->
- [ ] Original copy
- [ ] `./vendor/bin/phpunit` exits 0

# STOP CONDITIONS
- Done(section) → STOP with family → unit id list.
- Error budget 10 → handoff.
- Abort: rewriting the renderer "for cleanup"; adding a second hub; new `slug` not in RESOURCES.

# TICK OUTPUT
1. unit id + `slug` + track
2. PASS | FIXING (n/10) | STOP
3. Next family
4. verify status
```
