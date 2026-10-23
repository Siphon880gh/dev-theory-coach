---
name: add-drill
description: >-
  Add one Dev Theory Coach drill pack for a drill-family `slug`. Use when authoring `/drill/{slug}`.
---

# Add a drill pack

One valid unit per family. Slug = `slug` = guide slug. No runtime LLM. SAMPLE data.

## Files

The milestone loop replaces these TODOs when the runtime for this type ships. Do not invent a second path.

- New: `TODO path/<slug>.json` (source of truth; auto-loaded)
- Validate: `TODO command`

No register step if the runtime scans the directory. Otherwise list the registry file here.

## Shape

Match an existing file such as `TODO path/<first-slug>.json`. Required parts:

- Slug must be in `decisions.drill_families`. Otherwise record a skip; do not author a pack
- ≥8 items
- Each item: `prompt`, `accept`, `thoughtChain` of exactly 3 steps, `seconds`
- Timed. Thought chain is revealed after submit
- Original wording

## Verify

1. Validator passes
2. `./vendor/bin/phpunit`
3. The unit is reachable from its guide / hub (not "not authored yet")
