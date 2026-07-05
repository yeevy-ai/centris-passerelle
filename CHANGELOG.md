# Changelog

All notable changes to `yeevy/centris-passerelle` will be documented in this file.

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
