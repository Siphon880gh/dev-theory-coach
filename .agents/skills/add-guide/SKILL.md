---
name: add-guide
description: >-
  Add one Dev Theory Coach guide for a `slug`. Use when authoring or extending `/guides`.
---

# Add a guide

One valid unit per family. Slug = `slug` = guide slug. No runtime LLM. Educational / non-live data.

## Files

The milestone loop replaces these TODOs when the runtime for this type ships. Do not invent a second path.

- New: `TODO path/<slug>.json` (source of truth; auto-loaded)
- Validate: `TODO command`

No register step if the runtime scans the directory. Otherwise list the registry file here.

## Shape

Match an existing file such as `TODO path/<first-slug>.json`. Required parts:

- `slug`, `track`, `title`, `summary`
- `body` 300–800 words: rule, why, one worked example, when to use, when to refuse
- `relatedCoachSlug` = slug
- Original wording. No 12-word span from `context/`

## Verify

1. Validator passes
2. `./vendor/bin/phpunit`
3. The unit is reachable from its guide / hub (not "not authored yet")
