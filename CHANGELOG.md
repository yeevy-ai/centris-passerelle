# Changelog

All notable changes to `yeevy/centris-passerelle` will be documented in this file.

## v0.3.1 - 2026-07-05

Housekeeping release — no code changes.

The v0.3.0 tag was briefly re-pointed during a rebase and Packagist had already indexed the original reference; per Packagist's version-immutability policy, v0.3.0 has been restored to its published commit and this tag carries the current mainline (identical code, changelog file up to date). Install `^0.3` as usual.

## v0.3.0 — Archive extraction and photo downloads - 2026-07-05

### Highlights

- **`ZipExtractor` + `ZipExtractingSource`** — for diffusion agreements delivering the drop as a ZIP: extracts the archive's .TXT entries flattened (guarding against zip-slip paths) and composes as a `FeedSource` decorator, so archived drops flow through `ListingsSynchronizer` exactly like plain ones. Requires `ext-zip` (suggest-only).
- **`PhotoDownloader`** — downloads listing photos through any PSR-18 client into content-addressed files (`{sha256}.{ext}`), so identical bytes — re-drops, co-listings sharing media — are stored exactly once. `download()` throws `PhotoDownloadFailed`; `downloadAll()` logs and skips failures so one broken URL never aborts the batch. Extension derived from the response content type.

### New dependencies

PSR HTTP interfaces only: `psr/http-client`, `psr/http-factory`, `psr/http-message`. Bring any PSR-18 client (e.g. Guzzle — suggest-only).

No breaking changes — all v0.2.0 APIs are untouched.

## v0.2.0 — Sync engine - 2026-07-05

### Highlights

- **`ListingsSynchronizer`** — applies a full snapshot to consumer storage: drift validation first (nothing is written when it fails), dirty-hash upserts through the new `ListingRepository` contract, and removal reconciliation by diffing storage against the snapshot. Returns a `SyncResult` (created / updated / skipped / removed).
- **`ListingRepository` contract** — storage stays consumer-owned (Eloquent, PDO, WordPress, …); the package never touches a database.
- **PSR-14 lifecycle events** — `ListingCreated`, `ListingUpdated` (including EV→VE sold transitions), `ListingRemoved`.
- **Feed sources** — `FeedSource` contract with `LocalDirectorySource` (drop folders, cron scripts) and `FlysystemFeedSource` for the Passerelle FTP account (pair with `league/flysystem-ftp` or `-sftp-v3`; flysystem is suggest-only).

### New dependency

`psr/event-dispatcher ^1.0` (interface only).

No breaking changes — all v0.1.0 APIs are untouched.

## v0.1.0 — Initial release - 2026-07-05

Unofficial PHP client for the Centris® Passerelle FTP feed. Not affiliated with or endorsed by Centris or QFREB. Requires a valid diffusion agreement.

### Highlights

- **14 streaming parsers** covering every file in a Passerelle drop: `INSCRIPTIONS` (listings master, ~160 columns), `REMARQUES`, `PHOTOS`, `ADDENDA`, `CARACTERISTIQUES`, `DEPENSES`, `RENOVATIONS`, `LIENS_ADDITIONNELS`, `VISITES_LIBRES`, `UNITES_DETAILLEES`, `PIECES_UNITES`, `MEMBRES`, `FIRMES`, `BUREAUX` — lazy generators, safe for large snapshots
- **Typed readonly DTOs** with lenient casts (malformed values become null), sha256 dirty hashes for upsert skipping, and raw-row access for unmapped columns
- **Config-driven column maps** — community-observed defaults, per-agreement overrides, and named profiles so a future Centris layout never breaks existing consumers
- **Drift detection** — `SnapshotValidator` samples rows before import and throws `ColumnMapMismatch` instead of silently importing shifted data; checks are injectable
- **Central Windows-1252 → UTF-8 conversion**, CRLF/quoted-CSV handling via league/csv
- **PSR-3 logging** — rows missing their join key are logged and skipped, never abort a snapshot

### Requirements

PHP 8.2+ with mbstring, league/csv ^9.27. Quality gates: PHPStan level 8, Pest (60 tests), Pint.

### Caveat

Column positions are community-observed — verify against the Passerelle PDF documentation that comes with your diffusion agreement, and override via `ColumnMap` where they differ.
