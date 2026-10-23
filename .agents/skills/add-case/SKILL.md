# Skill: add-case

## Paths
- Case JSON: `content/cases/<slug>.json`
- Runtime: `src/CaseStudy.php` (PHP reserved word avoided)
- Route: `GET /case/{slug}` via `index.php`
- Example: `content/cases/requirements.json`

## Validate
```bash
./vendor/bin/phpunit --filter CaseTest
php -r 'require "vendor/autoload.php"; TheoryCore\CaseStudy::assertValid(TheoryCore\CaseStudy::load("<slug>")); echo "ok\n";'
```

Gate-closed pages must not reveal the best choice. Pre-commit HTML must omit consequences.
