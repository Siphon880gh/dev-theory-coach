# Skill: add-coaching-session

Add a deterministic step-by-step coach tree for one family slug.

## Paths

- Session JSON: `content/coaching/<slug>.json`
- Validator: `src/CoachValidator.php`
- Runtime / nav / HTML: `src/Coach.php`
- Route: `GET /coach/{slug}` via `index.php`
- Example: `content/coaching/arrays.json`

## Validate command

```bash
./vendor/bin/phpunit --filter CoachTest
php -r 'require "vendor/autoload.php"; TheoryCore\CoachValidator::loadFile("content/coaching/<slug>.json"); echo "ok\n";'
```

## Schema checklist

- `meta.slug` equals the filename slug and the guide `relatedCoachSlug`
- `start` names an existing node
- At least one `success` node with **empty** `choices`
- Every `wrong` node has `rewind_to` pointing at an existing node and an `explanation`
- `continue` nodes expose labeled choices with `to` targets
- Missing files render the "not authored yet" slot (HTTP 200)

## Steps

1. Copy `content/coaching/arrays.json` as a structural template.
2. Rewrite prompts for the new slug; keep wrong → rewind → success playable.
3. Run the validate command above.
4. Confirm `/guides/{slug}` CTA still points at `/coach/{slug}`.
