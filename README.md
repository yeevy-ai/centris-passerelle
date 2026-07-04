# centris-passerelle

[![Latest Version on Packagist](https://img.shields.io/packagist/v/yeevy/centris-passerelle.svg?style=flat-square)](https://packagist.org/packages/yeevy/centris-passerelle)
[![Tests](https://img.shields.io/github/actions/workflow/status/yeevy-ai/centris-passerelle/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/yeevy-ai/centris-passerelle/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/yeevy/centris-passerelle.svg?style=flat-square)](https://packagist.org/packages/yeevy/centris-passerelle)

> **Non officiel. Aucune affiliation avec Centris ou l'APCIQ/QFREB, ni endossement de leur part. Requiert une entente de diffusion valide.**
>
> **Unofficial. Not affiliated with or endorsed by Centris or QFREB. Requires a valid diffusion agreement.**

[Français](#français) | [English](#english)

---

## Français

Client PHP open source non officiel pour le flux FTP **Centris® Passerelle** (données d'inscriptions MLS du Québec distribuées aux courtiers autorisés). Analyse, synchronise et réconcilie les données d'inscriptions.

Noyau PHP pur, sans dépendance à un framework : utilisable depuis une extension WordPress, Laravel, Symfony ou un simple script cron.

### Comment fonctionne le flux Passerelle

- Aucune API publique. Le courtier signe une entente de diffusion avec Centris/APCIQ et reçoit des identifiants FTP limités à ses propres inscriptions.
- Centris dépose un **instantané complet** une ou deux fois par jour (pas de deltas) : les retraits se détectent par différence — une inscription présente en base mais absente du nouveau fichier est vendue, expirée ou retirée.
- Fichiers livrés : `INSCRIPTIONS.TXT` (inscriptions), `REMARQUES.TXT` (descriptions FR/EN), `PHOTOS.TXT`, `ADDENDA.TXT`, plus des fichiers de référence (courtiers, agences, caractéristiques, municipalités).
- Format : CSV délimité par virgules, champs entre guillemets, **encodage Windows-1252**, sans ligne d'en-tête, colonnes positionnelles (~150), une inscription par ligne (CRLF).

### Prérequis

- PHP 8.2+ avec l'extension `mbstring`
- Une entente de diffusion Passerelle valide (ce paquet ne fournit **aucune** donnée)

### Installation

```bash
composer require yeevy/centris-passerelle
```

### Utilisation

```php
use Yeevy\CentrisPasserelle\Parser\ListingsParser;
use Yeevy\CentrisPasserelle\Enums\ListingStatus;

$parser = new ListingsParser();

foreach ($parser->parseFile('/chemin/vers/INSCRIPTIONS.TXT') as $listing) {
    $listing->mlsNumber;      // « 9159788 » — clé d'upsert
    $listing->salePrice;      // 975000 (null pour les locations)
    $listing->status;         // ListingStatus::Active | ListingStatus::Sold | null
    $listing->descriptionFr;  // contient du HTML <br/>
    $listing->descriptionEn;
    $listing->latitude;
    $listing->longitude;
    $listing->dirtyHash;      // sha256 de la ligne brute — ignorez les lignes inchangées
    $listing->row;            // ligne brute complète pour les colonnes non cartographiées
}
```

L'analyse est paresseuse (générateur) : les instantanés volumineux ne saturent pas la mémoire. La conversion Windows-1252 → UTF-8 est appliquée automatiquement.

### Positions de colonnes

Les positions livrées avec le paquet sont **observées par la communauté** et peuvent varier selon la version de votre entente. Vérifiez-les contre la documentation PDF Passerelle fournie avec **votre** entente, puis surchargez-les au besoin :

```php
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Parser\ListingsParser;

$columns = ColumnMap::listings()->with([
    'status_code' => 120,   // position vérifiée dans votre documentation
]);

$parser = new ListingsParser($columns);
```

Un logger PSR-3 peut être injecté ; les lignes sans numéro MLS sont journalisées puis ignorées au lieu d'interrompre l'instantané :

```php
$parser = new ListingsParser(logger: $monLogger);
```

Si Centris introduit une nouvelle disposition de colonnes, elle sera publiée comme **profil nommé** plutôt qu'en écrasant la carte par défaut : `ColumnMap::listings('2027')` chargera `config/listings-2027.php`, et les profils existants continueront de fonctionner.

### Détection de dérive

Un changement de structure du flux ne provoque aucune erreur par lui-même — il se manifeste par des données décalées importées silencieusement. Validez l'instantané avant l'import :

```php
use Yeevy\CentrisPasserelle\Validation\SnapshotValidator;

$validator = new SnapshotValidator($columns);

// Échantillonne les lignes et vérifie les invariants (numéro MLS numérique,
// format des dates, coordonnées dans les bornes du Québec…).
// Lève ColumnMapMismatch si la structure ne correspond plus à la carte,
// ou si l'instantané est vide (ce qui dépublierait toutes les inscriptions).
$validator->validateFile('/chemin/vers/INSCRIPTIONS.TXT');
```

Les vérifications sont injectables — ajoutez des invariants propres à votre entente ou assouplissez ceux par défaut :

```php
new SnapshotValidator($columns, checks: [
    ...SnapshotValidator::defaultChecks(),
    fn (array $row, ColumnMap $columns): ?string => /* votre invariant */ null,
]);
```

### Cycle de vie des inscriptions

| Signal | Interprétation |
|---|---|
| `EV` (en vigueur) | Publier |
| `VE` (vendue) | Marquer vendue |
| Absente de l'instantané | Dépublier (étape de réconciliation) |

### Feuille de route

- Récupération FTP/SFTP (Flysystem) et pipeline de synchronisation complet
- Réconciliation des retraits par différence d'instantanés
- Analyseurs `REMARQUES.TXT`, `PHOTOS.TXT`, `ADDENDA.TXT` et fichiers de référence
- Enveloppe Laravel : `yeevy/laravel-centris` (dépôt séparé)

### Gestion des versions

Le paquet suit [SemVer](https://semver.org/lang/fr/) via les étiquettes git.

- **0.x** : les positions de colonnes sont observées par la communauté et l'API se stabilise — des ruptures peuvent survenir dans les versions mineures.
- **À partir de 1.0** : correctifs = patch ; nouveaux champs et analyseurs = mineure ; changement d'API = majeure.
- **Cartes de colonnes** : corriger une position par défaut est publié au minimum en version mineure avec une entrée de changelog explicite — le code compile mais les données changent. Une nouvelle disposition Centris devient un nouveau profil nommé, jamais un écrasement du profil existant.

### Tests

```bash
composer test      # Pest
composer analyse   # PHPStan niveau 8
composer format    # Pint
```

**Important :** ne commettez jamais de données réelles du flux. Les tests utilisent exclusivement des fixtures synthétiques.

### Licence

[MIT](LICENSE.md) — © Digital Unity Inc. ([Yeevy](https://yeevy.ai))

---

## English

Unofficial open-source PHP client for the **Centris® Passerelle** FTP feed (Quebec MLS listing data distributed to authorized brokers). Parses, syncs, and reconciles listing data.

Pure PHP core with no framework dependency: consumable from a WordPress plugin, Laravel, Symfony, or a bare cron script.

### How the Passerelle feed works

- No public API. The broker signs a diffusion agreement with Centris/QFREB and receives FTP credentials scoped to their own listings.
- Centris drops a **full snapshot** once or twice daily (no deltas): removals are detected by diffing — a listing present in your database but absent from the new file is sold, expired, or withdrawn.
- Delivered files: `INSCRIPTIONS.TXT` (listings master), `REMARQUES.TXT` (FR/EN descriptions), `PHOTOS.TXT`, `ADDENDA.TXT`, plus reference files (brokers, agencies, features, municipalities).
- Format: comma-delimited CSV, quoted fields, **Windows-1252 encoding**, no header row, positional columns (~150), one listing per CRLF line.

### Requirements

- PHP 8.2+ with the `mbstring` extension
- A valid Passerelle diffusion agreement (this package ships **no** data)

### Installation

```bash
composer require yeevy/centris-passerelle
```

### Usage

```php
use Yeevy\CentrisPasserelle\Parser\ListingsParser;
use Yeevy\CentrisPasserelle\Enums\ListingStatus;

$parser = new ListingsParser();

foreach ($parser->parseFile('/path/to/INSCRIPTIONS.TXT') as $listing) {
    $listing->mlsNumber;      // "9159788" — upsert key
    $listing->salePrice;      // 975000 (null for rentals)
    $listing->status;         // ListingStatus::Active | ListingStatus::Sold | null
    $listing->descriptionFr;  // contains HTML <br/>
    $listing->descriptionEn;
    $listing->latitude;
    $listing->longitude;
    $listing->dirtyHash;      // sha256 of the raw row — skip unchanged rows on upsert
    $listing->row;            // full raw row for unmapped columns
}
```

Parsing is lazy (generator-based), so large snapshots don't exhaust memory. Windows-1252 → UTF-8 conversion is applied automatically.

### Column positions

The positions shipped with the package are **community-observed** and may vary by agreement version. Verify them against the Passerelle PDF documentation that came with **your** agreement, then override as needed:

```php
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Parser\ListingsParser;

$columns = ColumnMap::listings()->with([
    'status_code' => 120,   // position verified in your documentation
]);

$parser = new ListingsParser($columns);
```

A PSR-3 logger can be injected; rows without an MLS number are logged and skipped instead of aborting the snapshot:

```php
$parser = new ListingsParser(logger: $myLogger);
```

If Centris introduces a new column layout, it will ship as a **named profile** rather than overwriting the default map: `ColumnMap::listings('2027')` loads `config/listings-2027.php`, and existing profiles keep working.

### Drift detection

A feed structure change raises no error by itself — it shows up as shifted data imported silently. Validate the snapshot before importing:

```php
use Yeevy\CentrisPasserelle\Validation\SnapshotValidator;

$validator = new SnapshotValidator($columns);

// Samples rows and checks invariants (numeric MLS number, date format,
// coordinates within Quebec bounds…). Throws ColumnMapMismatch when the
// structure no longer lines up with the map, or when the snapshot is
// empty (which would unpublish every listing).
$validator->validateFile('/path/to/INSCRIPTIONS.TXT');
```

Checks are injectable — add per-agreement invariants or relax the defaults:

```php
new SnapshotValidator($columns, checks: [
    ...SnapshotValidator::defaultChecks(),
    fn (array $row, ColumnMap $columns): ?string => /* your invariant */ null,
]);
```

### Listing lifecycle

| Signal | Interpretation |
|---|---|
| `EV` (en vigueur) | Publish |
| `VE` (vendue) | Mark sold |
| Absent from snapshot | Unpublish (reconciliation step) |

### Roadmap

- FTP/SFTP fetching (Flysystem) and full sync pipeline
- Removal reconciliation by snapshot diffing
- `REMARQUES.TXT`, `PHOTOS.TXT`, `ADDENDA.TXT` and reference-file parsers
- Laravel wrapper: `yeevy/laravel-centris` (separate repo)

### Versioning

The package follows [SemVer](https://semver.org) via git tags.

- **0.x**: column positions are community-observed and the API is still settling — breaking changes may land in minor versions.
- **From 1.0 on**: fixes = patch; new fields and parsers = minor; API changes = major.
- **Column maps**: correcting a shipped default position is released as at least a minor version with an explicit changelog entry — code still compiles, but data shifts. A new Centris layout becomes a new named profile, never an overwrite of an existing one.

### Testing

```bash
composer test      # Pest
composer analyse   # PHPStan level 8
composer format    # Pint
```

**Important:** never commit real feed data. Tests use synthetic fixtures only.

### License

[MIT](LICENSE.md) — © Digital Unity Inc. ([Yeevy](https://yeevy.ai))
