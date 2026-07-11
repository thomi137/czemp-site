#!/usr/bin/env zsh
# Migrations-Script: CSV → WordPress (Medien, Kollektionen, Artworks, Gallery-Items)
#
# CSV-Format (Semikolon-getrennt, erste Zeile = Header):
#   title;excerpt;content;collection;folder;name;image_path
#
# Verwendung: ./migrate.sh werke.csv [/pfad/zu/flachen/bildern]
# Bilder werden anhand der Spalte "name" im Bilder-Ordner gesucht.
# Voraussetzungen: curl, jq, .env im selben Ordner

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

# ── Kollektion holen oder anlegen ──────────────────────────
declare -A COL_CACHE
declare -A COL_FIRST_IMAGE

get_or_create_collection() {
    local name="$1"
    [[ -n "${COL_CACHE[$name]+x}" ]] && { echo "${COL_CACHE[$name]}"; return; }

    local slug
    slug=$(echo "$name" | tr '[:upper:]' '[:lower:]' | tr ' ' '-' | \
           iconv -f utf-8 -t ascii//TRANSLIT 2>/dev/null || echo "$name")

    local existing
    existing=$(curl -sf "${WP_BASE}/collection?slug=${slug}" \
               | jq -r '.[0].id // empty' 2>/dev/null || true)

    if [[ -n "$existing" ]]; then
        COL_CACHE[$name]="$existing"
        echo "$existing"
        return
    fi

    local id
    id=$(api -X POST "${BASE}/collection" \
         -H "Content-Type: application/json" \
         -d "{\"name\":\"${name}\",\"slug\":\"${slug}\"}" \
         | jq -r '.id')

    COL_CACHE[$name]="$id"
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

# ── Gallery-Item zur Galerie-Seite hinzufügen ──────────────
add_gallery_item() {
    local collection="$1"
    local image_url="$2"
    local slug
    slug=$(echo "$collection" | tr '[:upper:]' '[:lower:]' | tr ' ' '-' | \
           iconv -f utf-8 -t ascii//TRANSLIT 2>/dev/null || echo "$collection")

    api -X POST "${BASE}/gallery-item" \
        -H "Content-Type: application/json" \
        -d "$(jq -n \
            --arg title     "$collection" \
            --arg image_url "$image_url" \
            --arg link_url  "${WP_URL}/kollektion/${slug}/" \
            '{title: $title, image_url: $image_url, link_url: $link_url}')" \
        | jq -r 'if .success then "ok" else (.message // "Fehler") end'
}

# ── Hauptschleife ──────────────────────────────────────────
ok=0; fail=0

tail -n +2 "$CSV" | while IFS=';' read -r title excerpt content collection folder name image_path; do
    title="${title%$'\r'}"
    name="${name%$'\r'}"
    [[ -z "$title" ]] && continue

    echo -n "→ ${title} (${collection}) ... "

    col_id=$(get_or_create_collection "$collection")
    if [[ ! "$col_id" =~ ^[0-9]+$ ]]; then
        echo "✗ Kollektion fehlgeschlagen"
        (( fail++ )) || true; continue
    fi

    media_id="null"; media_url=""
    if [[ -n "$name" ]]; then
        local_path="${IMAGES_DIR}/${name}"
        if [[ -f "$local_path" ]]; then
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
            --argjson col "$col_id" \
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

    if [[ -n "$media_url" && -z "${COL_FIRST_IMAGE[$collection]+x}" ]]; then
        COL_FIRST_IMAGE[$collection]="$media_url"
    fi
done

echo ""
echo "Artworks: ${ok} hochgeladen, ${fail} Fehler."

# ── Gallery-Items erstellen ────────────────────────────────
echo ""
echo "Erstelle Gallery-Items auf der Galerie-Seite ..."
gallery_ok=0; gallery_fail=0

for collection in "${(@k)COL_FIRST_IMAGE}"; do
    echo -n "  → ${collection} ... "
    result=$(add_gallery_item "$collection" "${COL_FIRST_IMAGE[$collection]}")
    if [[ "$result" == "ok" ]]; then
        echo "✓"
        (( gallery_ok++ )) || true
    else
        echo "✗ ${result}"
        (( gallery_fail++ )) || true
    fi
done

echo ""
echo "Gallery-Items: ${gallery_ok} erstellt, ${gallery_fail} Fehler."
echo "Fertig."
