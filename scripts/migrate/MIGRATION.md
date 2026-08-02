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
