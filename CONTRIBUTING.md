# Contributing

Merci ! Contributions in English or French are welcome.

## Setup

```bash
composer install
composer test      # Pest
composer analyse   # PHPStan level 8
composer format    # Pint
```

All three must pass — CI enforces them across PHP 8.2–8.4 on Ubuntu and Windows, with both lowest and stable dependencies.

## The one hard rule

**Never commit real feed data.** No real rows from `INSCRIPTIONS.TXT` or any other Passerelle file, no real MLS numbers, broker names, or credentials — in fixtures, tests, examples, or commit messages. Test fixtures live in `tests/fixtures/synthetic/` and are entirely invented (MLS `9999999`, `Rue Exemple`, 555 phone numbers). The `.gitignore` blocks `*.TXT` and `fixtures/real/` as a safety net; don't work around it.

## Column positions

Shipped maps are **community-observed** — positions may vary by diffusion-agreement version. If yours differ, don't edit the default map: propose a new named profile (`config/listings-<profile>.php`) and describe which agreement version it matches. Corrections to defaults need a clear explanation of what was verified.

## Style

- Small, focused commits — one logical change each.
- Follow the existing structure: parsers extend `FileParser`, DTOs are `final readonly`, encoding goes through `FeedReader`/`Encoding` only.
- New behavior comes with tests against synthetic fixtures.
