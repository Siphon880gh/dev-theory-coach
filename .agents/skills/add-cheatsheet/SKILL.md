# Skill: add-cheatsheet

## Paths
- JSON: `content/cheatsheets/<slug>.json`
- Runtime: `src/Cheatsheet.php`
- Route: `GET /cheatsheet/{slug}` via `index.php`
- Example: `content/cheatsheets/arrays.json`

## Validate
```bash
./vendor/bin/phpunit --filter CheatsheetTest
```

Limits: ≤12 recall lines, one pickWhen, one skipWhen. Missing file → "not authored yet" slot.
