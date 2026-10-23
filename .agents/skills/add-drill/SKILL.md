# Skill: add-drill

## Paths
- Pack JSON: `content/drills/<slug>.json`
- Runtime: `src/Drill.php`
- Route: `GET /drill/{slug}` via `index.php`
- Allowed slugs only: see `Drill::drillFamilies()` / `decisions.drill_families` in `.agents/state.json`
- Example: `content/drills/complexity.json`

## Validate
```bash
./vendor/bin/phpunit --filter DrillTest
php -r 'require "vendor/autoload.php"; TheoryCore\Drill::assertValid(TheoryCore\Drill::load("<slug>")); echo "ok\n";'
```

Non-drill families must render "This family is not a drill." — never invent a fake pack.
