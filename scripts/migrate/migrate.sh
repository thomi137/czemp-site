#!/usr/bin/env zsh
# Copyright (c) 2026 Thomas Prosser. All rights reserved.
# Migrations-Script: CSV → WordPress (Medien, Kollektionen, Artworks, Gallery-Items)
#
# CSV-Format (Komma-getrennt, korrekt gequotet, erste Zeile = Header):
#   title,excerpt,content,collection,subcollection,folder,name,image_path
#
# "collection" ist immer eine Top-Level-Kollektion. Ist "subcollection"
# gesetzt, wird sie als Kind-Term unter "collection" angelegt und das
# Werk dort einsortiert (nicht zusätzlich auf die Elternkollektion).
#
# Bilder werden anhand der Spalte "name" im Bilder-Ordner gesucht.
# Komplett leere Zeilen (z.B. Leerzeile am Dateiende) werden übersprungen.
#
# Verwendung: ./migrate.sh werke.csv [/pfad/zu/bildern]
# Voraussetzungen: curl, jq, python3, .env im selben Ordner

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

if [[ ! -f "${SCRIPT_DIR}/.env" ]]; then
    echo "Fehler: .env nicht gefunden." >&2
    exit 1
fi
source "${SCRIPT_DIR}/.env"

CSV="${1:-}"
IMAGES_DIR="${2:-${SCRIPT_DIR}/images/images_flat}"

if [[ -z "$CSV" || ! -f "$CSV" ]]; then
    echo "Verwendung: $0 <werke.csv> [bilder-ordner]" >&2
    exit 1
fi

BASE="${WP_URL}/wp-json/czemp/v1"
WP_BASE="${WP_URL}/wp-json/wp/v2"

api() { curl -sf -H "X-Migrate-Token: ${MIGRATE_TOKEN}" "$@"; }

slugify() {
    # macOS' BSD iconv handles //TRANSLIT unreliably for German umlauts
    # (emits a warning, mangles the char, and a naive `|| echo "$1"`
    # fallback then leaks the untransliterated original) — so transliterate
    # by hand instead of relying on iconv.
    local s="${1:l}"
    s="${s//ä/ae}"; s="${s//ö/oe}"; s="${s//ü/ue}"; s="${s//ß/ss}"
    s=$(echo "$s" | sed -E 's/[^a-z0-9]+/-/g; s/^-+//; s/-+$//')
    echo "$s"
}

# ── CSV robust einlesen ─────────────────────────────────────
# Echte CSV-Quotierung (Kommas/Zeilenumbrüche in Feldern) lässt sich mit
# bloßem `read -d','` nicht sicher parsen, daher via Python vorverarbeiten:
# ein Datensatz pro Zeile, Felder getrennt durch \x1f (Unit Separator).
# Zeilenumbrüche innerhalb von Feldern werden zu literalem "\n" escaped,
# damit jede Ausgabezeile garantiert genau einem CSV-Datensatz entspricht.
csv_to_records() {
    python3 - "$1" << 'PYEOF'
import csv, sys

path = sys.argv[1]
with open(path, newline='', encoding='utf-8-sig') as f:
    rows = list(csv.reader(f))

if not rows:
    sys.exit(0)

ncols = len(rows[0])
for row in rows[1:]:
    if not any(cell.strip() for cell in row):
        continue  # komplett leere Zeile überspringen
    row = (row + [''] * ncols)[:ncols]
    row = [c.replace('\r\n', '\\n').replace('\n', '\\n').replace('\r', '\\n') for c in row]
    sys.stdout.write('\x1f'.join(row) + '\n')
PYEOF
}

unescape() {
    local nl=$'\n'
    local s="$1"
    print -r -- "${s//\\n/$nl}"
}

# ── Kollektion holen oder anlegen (mit optionalem Parent) ──
declare -A COL_CACHE

get_or_create_collection() {
    local name="$1"
    local parent_id="${2:-0}"

    if [[ -z "$name" ]]; then
        # Leerer Name darf NIE als "?slug=" (leer) an die REST-API gehen —
        # WP ignoriert dann den Filter komplett und liefert irgendeinen
        # beliebigen Top-Level-Term zurück (der alphabetisch erste), der
        # dann fälschlich als Treffer durchgeht. Lieber hart fehlschlagen.
        return 1
    fi

    local cache_key="${parent_id}:${name}"
    [[ -n "${COL_CACHE[$cache_key]+x}" ]] && { echo "${COL_CACHE[$cache_key]}"; return; }

    local slug
    slug=$(slugify "$name")

    local existing
    existing=$(curl -sf "${WP_BASE}/collection?slug=${slug}" \
               | jq -r --argjson parent "$parent_id" '[.[] | select(.parent == $parent)][0].id // empty' \
               2>/dev/null || true)

    if [[ -n "$existing" ]]; then
        COL_CACHE[$cache_key]="$existing"
        echo "$existing"
        return
    fi

    local payload
    if [[ "$parent_id" -gt 0 ]]; then
        payload="{\"name\":\"${name}\",\"slug\":\"${slug}\",\"parent\":${parent_id}}"
    else
        payload="{\"name\":\"${name}\",\"slug\":\"${slug}\"}"
    fi

    local id
    id=$(api -X POST "${BASE}/collection" \
         -H "Content-Type: application/json" \
         -d "$payload" \
         | jq -r '.id')

    COL_CACHE[$cache_key]="$id"
    echo "$id"
}

# ── Bild hochladen ─────────────────────────────────────────
upload_image() {
    local filepath="$1"
    local filename
    filename=$(basename "$filepath")

    api -X POST "${BASE}/media" \
        -H "X-Filename: ${filename}" \
        -H "Content-Type: application/octet-stream" \
        --data-binary "@${filepath}" \
        | jq -r '{id: .id, url: .url}'
}

# ── Kollektions-Thumbnail setzen (term-meta, core REST) ────
# Es gibt keinen "gallery-item"-REST-Endpoint (der wurde nie gebaut) — die
# Galerie-Kacheln (czemp-theme/collection-grid und czemp-theme/
# collection-subcategories) lesen stattdessen direkt das "thumbnail_id"
# Term-Meta der Kollektion. Das core /wp/v2/collection/<id> braucht
# echte WP-Auth (Basic Auth via Application Password), nicht den Migrate-Token.
set_collection_thumbnail() {
    local term_id="$1"
    local media_id="$2"

    if [[ -z "${WP_USER:-}" || -z "${WP_APP_PASSWORD:-}" ]]; then
        echo "kein WP_USER/WP_APP_PASSWORD in .env"
        return
    fi

    curl -sf -u "${WP_USER}:${WP_APP_PASSWORD}" -X POST "${WP_BASE}/collection/${term_id}" \
        -H "Content-Type: application/json" \
        -d "$(jq -n --argjson mid "$media_id" '{meta: {thumbnail_id: $mid}}')" \
        2>/dev/null | jq -r '.meta.thumbnail_id // "Fehler"' || echo "Fehler"
}

# ── Hauptschleife ──────────────────────────────────────────
ok=0; fail=0

declare -A GALLERY_MEDIA_ID   # key -> media_id des ersten erfolgreich hochgeladenen Bilds
declare -A GALLERY_TERM_ID    # key -> term_id (Kollektion oder Unterkollektion)

csv_to_records "$CSV" | while IFS=$'\x1f' read -r title excerpt content collection subcollection folder name image_path; do
    title=$(unescape "$title")
    excerpt=$(unescape "$excerpt")
    content=$(unescape "$content")
    [[ -z "$title" ]] && continue

    echo -n "→ ${title} (${collection}${subcollection:+ > $subcollection}) ... "

    top_id=$(get_or_create_collection "$collection" 0)
    if [[ ! "$top_id" =~ ^[0-9]+$ ]]; then
        echo "✗ Kollektion fehlgeschlagen"
        (( fail++ )) || true; continue
    fi

    if [[ -n "$subcollection" ]]; then
        assign_id=$(get_or_create_collection "$subcollection" "$top_id")
        gallery_key="${collection}|${subcollection}"
    else
        assign_id="$top_id"
        gallery_key="${collection}|"
    fi

    if [[ ! "$assign_id" =~ ^[0-9]+$ ]]; then
        echo "✗ Unterkollektion fehlgeschlagen"
        (( fail++ )) || true; continue
    fi

    media_id="null"; media_url=""
    if [[ -n "$name" ]]; then
        # Claudia liefert HEIC, iCloud exportiert oft als JPEG/JPG/PNG,
        # daher nicht auf die exakte Endung aus dem CSV verlassen,
        # sondern per Dateiname ohne Endung suchen.
        stem="${name%.*}"
        local_path=""
        for candidate in "${IMAGES_DIR}/${stem}".*; do
            [[ -f "$candidate" ]] && { local_path="$candidate"; break; }
        done
        if [[ -n "$local_path" ]]; then
            result=$(upload_image "$local_path")
            media_id=$(echo "$result" | jq -r '.id')
            media_url=$(echo "$result" | jq -r '.url')
        else
            echo -n "[Bild nicht gefunden: ${name}] "
        fi
    fi

    post_result=$(api -X POST "${BASE}/artwork" \
        -H "Content-Type: application/json" \
        -d "$(jq -n \
            --arg title   "$title" \
            --arg excerpt "$excerpt" \
            --arg content "$content" \
            --argjson col "$assign_id" \
            --argjson med "$media_id" \
            '{title: $title, excerpt: $excerpt, content: $content,
              collection: $col, featured_media: $med}')")

    post_id=$(echo "$post_result" | jq -r '.id // empty')
    if [[ -z "$post_id" ]]; then
        echo "✗ $(echo "$post_result" | jq -r '.message // "Fehler"')"
        (( fail++ )) || true; continue
    fi

    echo "✓ (id: ${post_id})"
    (( ok++ )) || true

    if [[ "$media_id" =~ ^[0-9]+$ && -z "${GALLERY_MEDIA_ID[$gallery_key]+x}" ]]; then
        GALLERY_MEDIA_ID[$gallery_key]="$media_id"
        GALLERY_TERM_ID[$gallery_key]="$assign_id"
    fi
done

echo ""
echo "Artworks: ${ok} hochgeladen, ${fail} Fehler."

# ── Kollektions-Thumbnails setzen (ein Thumbnail pro Kollektion/Unterkollektion) ──
echo ""
echo "Setze Kollektions-Thumbnails ..."
gallery_ok=0; gallery_fail=0

for key in "${(@k)GALLERY_TERM_ID}"; do
    echo -n "  → term ${GALLERY_TERM_ID[$key]} (${key%|}) ... "
    result=$(set_collection_thumbnail "${GALLERY_TERM_ID[$key]}" "${GALLERY_MEDIA_ID[$key]}")
    if [[ "$result" =~ ^[0-9]+$ ]]; then
        echo "✓ (thumbnail_id: ${result})"
        (( gallery_ok++ )) || true
    else
        echo "✗ ${result}"
        (( gallery_fail++ )) || true
    fi
done

echo ""
echo "Thumbnails: ${gallery_ok} gesetzt, ${gallery_fail} Fehler."
echo "Fertig."
