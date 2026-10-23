---
name: add-cheatsheet
description: >-
  Add one Dev Theory Coach cheatsheet for a `slug`. Use when authoring `/cheatsheet/{slug}`.
---

# Add a cheatsheet

One valid unit per family. Slug = `slug` = guide slug. No runtime LLM. SAMPLE data.

## Files

The milestone loop replaces these TODOs when the runtime for this type ships. Do not invent a second path.

- New: `TODO path/<slug>.json` (source of truth; auto-loaded)
- Validate: `TODO command`

No register step if the runtime scans the directory. Otherwise list the registry file here.

## Shape

Match an existing file such as `TODO path/<first-slug>.json`. Required parts:

- ≤12 recall lines
- One pick-when sentence and one skip-when sentence
- No rule that the guide for this slug does not already teach
- Original wording

## Verify

1. Validator passes
2. `./vendor/bin/phpunit`
3. The unit is reachable from its guide / hub (not "not authored yet")
