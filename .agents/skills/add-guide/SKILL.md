---
name: add-guide
description: >-
  Add one Dev Theory Coach guide for a `slug`. Use when authoring or extending `/guides`.
---

# Add a guide

One valid unit per family. Slug = `slug` = guide slug. No runtime LLM. Educational / non-live data. One guide per tick — never batch.

## Files

- New: `content/guides/<slug>.json` (source of truth; auto-loaded by `src/Catalog.php` / `src/Guides.php`)
- Validate: `./vendor/bin/phpunit` (especially `tests/GuidesTest.php`)

No register step; the runtime scans the directory.

## Shape

Match an existing file such as `content/guides/arrays.json` or `content/guides/binary-search.json`. Required:

- `slug`, `track`, `title`, `summary`
- `parts`: `rule`, `why`, `example`, `use`, `refuse`
- `body` 300–800 words assembled from those parts (lead the example with `Worked example.`)
- Worked example is **concrete** (digits, small structures, named steps) — not an abstract paraphrase of the rule
- Prefer ~400–600 teaching words; if short, expand the example — never pad
- `relatedCoachSlug` = slug
- Original wording. No 12-word span from `context/`
- No `SAMPLE` / `SAMPLE example` / `SAMPLE lesson` / `Recall checkpoint` in body or parts
- No identical paragraph repeated ≥3 times; no `sample: true` / `meta.sample` stamp

## Verify

1. `./vendor/bin/phpunit`
2. GuidesTest: no SAMPLE/checkpoint; concrete example (digit in `parts.example`); 300–800 without filler; no `sample` stamp
3. The unit is reachable from `/guides/<slug>` with coach CTA `/coach/<slug>`
