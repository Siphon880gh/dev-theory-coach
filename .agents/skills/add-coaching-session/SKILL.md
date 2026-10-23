---
name: add-coaching-session
description: >-
  Add one Dev Theory Coach coaching session for a `slug`. Use when pairing `/coach/{slug}` with its guide.
---

# Add a coaching session

One valid unit per family. Slug = `slug` = guide slug. No runtime LLM. SAMPLE data.

## Files

The milestone loop replaces these TODOs when the runtime for this type ships. Do not invent a second path.

- New: `TODO path/<slug>.json` (source of truth; auto-loaded)
- Validate: `TODO command`

No register step if the runtime scans the directory. Otherwise list the registry file here.

## Shape

Match an existing file such as `TODO path/<first-slug>.json`. Required parts:

- `start` + two more continue layers
- ≥2 `wrong` nodes with `rewind_to` pointing at a real node
- ≥1 `success` with empty choices
- `meta.slug` = slug = guide slug
- First message is a SAMPLE setup
- Teach which method to pick, not a quiz restated as buttons
- No runtime LLM

## Verify

1. Validator passes
2. `./vendor/bin/phpunit`
3. The unit is reachable from its guide / hub (not "not authored yet")
