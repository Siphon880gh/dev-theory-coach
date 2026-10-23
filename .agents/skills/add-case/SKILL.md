---
name: add-case
description: >-
  Add one Dev Theory Coach decision case for a `slug` on track C or D. Use when authoring `/case/{slug}`.
---

# Add a case

One valid unit per family. Slug = `slug` = guide slug. No runtime LLM. SAMPLE data.

## Files

The milestone loop replaces these TODOs when the runtime for this type ships. Do not invent a second path.

- New: `TODO path/<slug>.json` (source of truth; auto-loaded)
- Validate: `TODO command`

No register step if the runtime scans the directory. Otherwise list the registry file here.

## Shape

Match an existing file such as `TODO path/<first-slug>.json`. Required parts:

- Slug must be in `decisions.decision_families`. Otherwise record a skip
- SAMPLE scenario, prompt, 3 or 4 choices, one `best`, a consequence on every choice
- Design families (`capstone-url-shortener`, `capstone-feed`, `composition`) name at least two earlier slugs in the best consequence
- Consequences are not in the pre-commit HTML
- Original wording

## Verify

1. Validator passes
2. `./vendor/bin/phpunit`
3. The unit is reachable from its guide / hub (not "not authored yet")
