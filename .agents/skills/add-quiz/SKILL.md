---
name: add-quiz
description: >-
  Add one Dev Theory Coach quiz pack for a `slug`. Use when authoring `/quiz/{slug}`.
---

# Add a quiz pack

One valid unit per family. Slug = `slug` = guide slug. No runtime LLM. Educational / non-live data.

## Files

The milestone loop replaces these TODOs when the runtime for this type ships. Do not invent a second path.

- New: `TODO path/<slug>.json` (source of truth; auto-loaded)
- Validate: `TODO command`

No register step if the runtime scans the directory. Otherwise list the registry file here.

## Shape

Match an existing file such as `TODO path/<first-slug>.json`. Required parts:

- ≥6 items
- Each item: `prompt`, exactly 4 `choices`, `answerIndex`, `whyRight`, `whyWrong` for every other choice
- Untimed. No code runner
- Prompts are distinct. Original wording

## Verify

1. Validator passes
2. `./vendor/bin/phpunit`
3. The unit is reachable from its guide / hub (not "not authored yet")
