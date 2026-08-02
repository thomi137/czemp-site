# Artwork Migration — Pipeline & Log

Documents how Claudia's artwork photos + spreadsheet get turned into WordPress
`artwork` posts, and tracks the back-and-forth with her about missing/unclear
images so it doesn't just live in WhatsApp.

## Pipeline

```
images_original/  (nested delivery from Claudia, HEIC + misc formats)
        │
        ▼  scripts/flatten_images.sh
images_flat.zip   (all files in one flat folder, de-duplicated, zipped)
        │
        ▼  unzip → images/
images.csv + images/  (title, excerpt, content, collection, name → filename)
        │
        ▼  scripts/migrate/migrate.sh <csv> <images-dir>
WordPress: collection terms, media uploads, artwork posts, gallery-items
```

### `flatten_images.sh`

One-off prep step. Walks `migrate/images/images_original/`, collects every
image (jpg/jpeg/png/webp/gif/heic) regardless of subfolder nesting, copies
them into a single flat `migrate/images/images_flat/` (same-named files get a
`_1`, `_2`, … suffix), zips the result to `images_flat.zip`, then deletes the
working folder. Run this once per delivery batch before touching `migrate.sh`.

### `migrate.sh`

Reads `images.csv` (semicolon-separated: `title;excerpt;content;collection;folder;name;image_path`)
and for each row:

1. Gets-or-creates the `collection` taxonomy term (cached per run).
2. Finds the image by the `name` column, matched **by filename stem**
   (extension-agnostic — see note below), uploads it to the WP media library
   via `czemp/v1/media`.
3. Creates the `artwork` post (title/excerpt/content) with that collection
   and featured image.
4. After all rows: creates one gallery-item per collection using the first
   successfully-uploaded image, linking to `/kollektion/<slug>/`.

Needs `.env` (`WP_URL`, `MIGRATE_TOKEN`) in the same folder — see `.env.example`.

Usage:

```bash
./migrate.sh images.csv images/
```

(`images/` must be passed explicitly — the script's default,
`images/images_flat`, only exists right after `flatten_images.sh` + unzip.)

**HEIC note (2026-08-01):** Claudia delivers `.HEIC`; iCloud's own export
converts most of these to `.jpeg`/`.jpg`/`.png` before we ever see them, so
the CSV's `name` column (e.g. `IMG_0033.HEIC`) usually doesn't match the
actual file extension on disk. `migrate.sh` looks up images by stem
(`IMG_0033.*`) instead of exact filename for this reason — no need to edit
the CSV or rename files when extensions don't match.

## Status log

### 2026-08-01 — first reconciliation of `images.csv` vs `images/`

Compared all 265 `name` entries in `images.csv` against the 252 files in
`migrate/images/`. Findings:

**Missing entirely (7 files, 5 artworks fully unillustrated):**
- Whole "LICHT" wordplay sub-series (collection *Stempeldruck*) — 5 titles,
  no photo at all: `LICHTblick` (IMG_2926), `blauLICHT` (IMG_2952),
  `schLICHT` (IMG_2940), `rotLICHT` (IMG_2931), `hoffnungsLICHT` (IMG_2929).
  EXIF dates on neighboring files show these sit in an unfilled gap between
  Oct 6 and Oct 20, 2025 — genuinely not delivered, not a renaming artifact.
- `ROYAL SEAT` (Skulpturen/3D) — 2 of 3 referenced images present
  (IMG_4995, IMG_1665), missing IMG_4990.
- `MONDLICHT` (Skulpturen/3D) — 1 of 2 referenced images present (IMG_4985),
  missing IMG_4979.

**No image assigned in the CSV at all:**
- `FORMEN TANZEN SAMBA` (Fussmalerei)
- `BESCHWIPST` (Skulpturen/3D)

**Extra files in `images/` not referenced by any CSV row** (flagged for
Claudia to confirm, not blocking):
- `DSC_3288/3295/3297/3299.JPG` — EXIF dated 2005, unrelated to any current
  artwork; likely old scans swept into the export.
- `IMG_8117.jpeg` — extra unused shot from the Dec 4, 2024 session.
- `IMG_5466 2.JPG` — exact duplicate of `IMG_5466.JPG` (identical EXIF
  timestamp), safe to ignore.

Ruled out: iCloud renaming/renumbering files under different sequence
numbers. Checked EXIF `DateTimeOriginal` across the whole folder (via
`identify`, no exiftool installed) — the `IMG_####` numbering is strictly
chronological with no gaps bridged by any of the "extra" files above.

**Sent to Claudia via WhatsApp** (German), asking her to supply:

> Nächste Schritte für Claudia — fehlende/unklare Bilder
>
> 1. Serie „LICHT" (Kollektion Stempeldruck) — komplett fehlend, 5 Bilder:
>    LICHTblick (IMG_2926), blauLICHT (IMG_2952), schLICHT (IMG_2940),
>    rotLICHT (IMG_2931), hoffnungsLICHT (IMG_2929)
> 2. ROYAL SEAT — fehlt noch eine Ansicht (IMG_4990)
> 3. MONDLICHT — fehlt noch eines (IMG_4979)
> 4. FORMEN TANZEN SAMBA — noch gar kein Bild vorhanden
> 5. BESCHWIPST — noch gar kein Bild vorhanden
>
> Zur Kontrolle: 4 alte Scans von 2005 (DSC_3288/95/97/99), ein Zusatzfoto
> (IMG_8117) und ein Duplikat (IMG_5466) liegen im Ordner, aber sind keinem
> Werk zugeordnet — gehören die irgendwo hin oder können sie weg?

**Open / waiting on:** Claudia's reply with the 7 missing photos + images for
the 2 unillustrated artworks, and confirmation on the 6 unassigned extra
files. Full migration run is blocked on this until resolved (or until we
decide to run it now and backfill featured images later).

### 2026-08-02 — first delivery reconciled, new extras flagged

Claudia's first-delivery batch arrived and was dropped into
`migrate/images_first_del/` (252 files) for comparison against
`images.csv` (254 unique `name` stems) and the working `migrate/images/`
(243 files at the time).

Compared `images.csv` name stems against `images/`: 19 referenced files
were missing. All 19 were found in `images_first_del/` and copied over —
nothing ended up missing from both. Recovered: `Bildschirmfoto 2026-07-29
um 10.12.38.png`, `IMG_1665`, `IMG_3352`, `IMG_3569`, `IMG_4796`,
`IMG_5593`, `IMG_5598`, `IMG_6847`, `IMG_6893`, `IMG_7829`, `IMG_7830`,
`IMG_7985`, `IMG_7987`, `IMG_7989`, `IMG_8003`, `IMG_8008`, `IMG_8042`,
`IMG_8112`, `Mein Film 1.mov`.

`images/` now has 262 files against 254 CSV stems (`images_first_del/`
was a temporary staging folder and has since been deleted). Re-running
the same comparison the other way — files in `images/` not referenced by
any CSV row — surfaces a new set of 8 unreferenced extras (supersedes the
2026-08-01 list above, which is resolved):

- `IMG_1303.jpeg`
- `IMG_2121.jpeg`
- `IMG_3126.jpeg`
- `IMG_3174.JPG`
- `IMG_3612.jpeg`
- `IMG_4449.jpeg`
- `IMG_4456.MOV`
- `IMG_8132.jpeg`

Sent to Claudia for confirmation on where these belong (or whether they
can be discarded).

**Open / waiting on:** Claudia's reply on the 8 unreferenced extras above.

### 2026-08-02 — migrate.sh rewritten, one data gap found

`migrate.sh` had a real parsing bug: it read the CSV with `IFS=';' read`
(7 fields) while `images.csv` is comma-delimited with quoted fields and 8
columns (`subcollection` was added at some point without updating the
script) — every field after `collection` was silently shifting by one.
Also hit a second bug while testing: `slugify()`'s `iconv -f utf-8 -t
ascii//TRANSLIT` is unreliable on macOS' BSD `iconv` for German umlauts —
it mangles the character *and* trips the `|| echo "$1"` fallback, which
then leaks the untransliterated original name into the "slug". Both
fixed: CSV is now parsed via a Python preprocessing step (proper
quoting/embedded-newline handling), and `slugify()` does manual ä/ö/ü/ß
transliteration instead of relying on iconv.

`migrate.sh` now also creates `subcollection` as a child term under
`collection` (both are just `collection` taxonomy terms, subcollection
has the parent's term id) and creates one gallery-item per term that
actually has artworks — parent collections *and* subcollections both get
their own card, image = first successfully-matched image for that exact
term.

Dry-run (fake IDs, no network) against the current `images.csv` +
`images/`: 297 real rows (45 fully-blank rows correctly skipped), 22
distinct collection/subcollection terms, only 1 row without a resolvable
image.

**New data gap found:** `BLITZGEDANKEN` has two legitimate rows (once
under collection *Strukturiert*, once under *ART SALE COLLECTION* — same
artwork, two collection memberships, matches how this CSV already models
multi-collection pieces) plus two bare rows (lines 35 and 328, exact
duplicates of each other) with only `title` and `name` (`IMG_6847.HEIC`)
filled in — `collection` is empty. As-is these two rows will fail during
migration (`get_or_create_collection("")` errors on an empty term name)
rather than crash the run, but the row is effectively a lost row unless
resolved. Looks like an incomplete CSV entry (a second photo added
without a collection) rather than intentional — flagged for Claudia/
Thomas to fill in or delete before the real migration run.

## Prod cut-over runbook

**Before the maintenance window** (no prod impact yet):

1. Resolve the `BLITZGEDANKEN` gap above (fill in a `collection` for the
   two bare rows, or delete them from `images.csv`).
2. In prod `wp-config.php`, add, right before
   `require_once(ABSPATH."wp-settings.php");`:
   ```php
   define('CZ_MIGRATE_TOKEN', 'g7fKmuaNdWiY80OuAFJiIfxCYGSD4KZdZTh6k5l3');
   ```
   This value was generated for this migration only — treat it as already
   "used" since it's now in this file. Delete the `define()` line (and
   clear `MIGRATE_TOKEN` from `.env`) as soon as the migration run in
   step 8 below is done; don't leave a standing backdoor into the write
   endpoints.
3. In WP-Admin on prod: Users → Profile → Application Passwords → create
   one (e.g. name `migrate-backup`). Put the username and the generated
   password into `scripts/migrate/.env` as `WP_USER` / `WP_APP_PASSWORD`
   (see `.env.example`). Revoke it again after step 4.
4. Set `WP_URL="https://claudia-zemp.ch"` in `scripts/migrate/.env`
   (currently points at the `neu.` staging site).

**Maintenance window:**

1. `cd scripts/migrate && ./download.sh media` — downloads every media
   item (any status) from prod and zips it locally
   (`media_backup_<date>.zip`, already covered by the blanket
   `scripts/migrate/**` gitignore rule). Sanity-check the zip's file
   count before continuing.
2. Export the prod database (hosting control panel / phpMyAdmin → Export
   → SQL) and store the dump somewhere outside the repo. This is the one
   step that makes step 4 below reversible — don't skip it.
3. Turn on Seedprod maintenance mode.
4. WP-Admin → Media → list view, select all → Delete Permanently
   (repeat per page — Media list defaults to 20/page; append
   `?mode=list&paged=1` and bump `per_page` via Screen Options to cut
   down on repeats).
5. Deactivate and delete all plugins except Seedprod (leave it running
   until step 12).
6. Deploy `cz-theme/` (built — `npm run build` first) and `wp-content/`
   to prod, activate the theme in WP-Admin → Appearance.
7. Confirm `CZ_MIGRATE_TOKEN` from the pre-flight step is live (re-check
   `wp-config.php` made it to prod with the deploy).
8. From `scripts/migrate/`: `./migrate.sh images.csv images/`. Watch the
   per-row `✓`/`✗` output — failures are expected only for the
   unresolved `BLITZGEDANKEN` rows if you didn't fix them in step 1.
9. Spot-check in WP-Admin: a handful of artworks (featured image +
   collection assigned), a subcollection term's parent is correct, and
   the gallery-item cards on the Galerie page for both a top-level
   collection and a subcollection.
10. Copy the front page content from `neu.claudia-zemp.ch` (open both in
    the block editor, copy blocks across, or export/import the
    `front-page` template if it was customized on staging).
11. Remove the `CZ_MIGRATE_TOKEN` define from `wp-config.php`, clear
    `MIGRATE_TOKEN`/`WP_USER`/`WP_APP_PASSWORD` from `.env`, revoke the
    Application Password in WP-Admin.
12. Turn off Seedprod maintenance mode.
13. Smoke-test the live site (homepage, gallery archive, a collection
    and a subcollection page, a single artwork page, mobile menu).
