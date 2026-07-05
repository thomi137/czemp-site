#!/usr/bin/env zsh
# Upload artworks from CSV to WordPress via REST API
# Usage: ./upload.sh artworks.csv
# Requires: curl, jq

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

if [[ ! -f "${SCRIPT_DIR}/.env" ]]; then
    echo "Fehler: .env nicht gefunden. Kopiere .env.example und füll es aus." >&2
    exit 1
fi

source "${SCRIPT_DIR}/.env"

CSV="${1:-}"
if [[ -z "$CSV" || ! -f "$CSV" ]]; then
    echo "Verwendung: $0 <datei.csv>" >&2
    exit 1
fi

BASE="${WP_URL}/wp-json/czemp/v1"
WP_BASE="${WP_URL}/wp-json/wp/v2"

api() {
    curl -s -H "X-Migrate-Token: ${MIGRATE_TOKEN}" "$@"
}

declare -A COLLECTION_CACHE

get_or_create_collection() {
    local name="$1"
    [[ -n "${COLLECTION_CACHE[$name]+x}" ]] && { echo "${COLLECTION_CACHE[$name]}"; return; }

    local slug
    slug=$(echo "$name" | tr '[:upper:]' '[:lower:]' | tr ' ' '-' | iconv -f utf-8 -t ascii//TRANSLIT 2>/dev/null || echo "$name")

    local existing
    existing=$(curl -s "${WP_BASE}/collection?slug=${slug}" | jq -r '.[0].id // empty')

    if [[ -n "$existing" ]]; then
        COLLECTION_CACHE[$name]="$existing"
        echo "$existing"
        return
    fi

    local create_response
    create_response=$(api -X POST "${BASE}/collection" \
        -H "Content-Type: application/json" \
        -d "{\"name\": \"${name}\", \"slug\": \"${slug}\"}")

    local id
    id=$(echo "$create_response" | jq -r '.id // empty')

    if [[ -z "$id" || "$id" == "null" ]]; then
        echo "DEBUG collection: $(echo "$create_response" | jq -r '.message // .')" >&2
        echo ""
        return
    fi

    COLLECTION_CACHE[$name]="$id"
    echo "$id"
}

upload_image() {
    local filepath="$1"
    local filename
    filename=$(basename "$filepath")

    api -X POST "${BASE}/media" \
        -H "X-Filename: ${filename}" \
        -H "Content-Type: application/octet-stream" \
        --data-binary "@${filepath}" \
        | jq -r '.id'
}

ok=0
fail=0

CSV_DIR="$(cd "$(dirname "$CSV")" && pwd)"

tail -n +2 "$CSV" | while IFS=';' read -r title excerpt content collection folder name image_path; do
    # Windows \r bereinigen
    title="${title%$'\r'}"
    image_path="${image_path%$'\r'}"
    [[ -z "$title" ]] && continue

    echo -n "→ ${title} ... "

    collection_id=$(get_or_create_collection "$collection")

    if [[ ! "$collection_id" =~ ^[0-9]+$ ]]; then
        echo "✗ Kollektion '${collection}' nicht gefunden"
        ((fail++)) || true
        continue
    fi

    media_id="null"
    if [[ -n "$image_path" ]]; then
        # Pfad relativ zur CSV-Datei auflösen
        full_path="${CSV_DIR}/${image_path}"
        if [[ -f "$full_path" ]]; then
            media_id=$(upload_image "$full_path")
        else
            echo -n "[Bild nicht gefunden: ${image_path}] "
        fi
    fi

    result=$(api -X POST "${BASE}/artwork" \
        -H "Content-Type: application/json" \
        -d "$(jq -n \
            --arg title      "$title" \
            --arg excerpt    "$excerpt" \
            --arg content    "$content" \
            --argjson col    "$collection_id" \
            --argjson media  "${media_id}" \
            '{title: $title, excerpt: $excerpt, content: $content, collection: $col, featured_media: $media}')")

    id=$(echo "$result" | jq -r '.id // empty')
    if [[ -n "$id" ]]; then
        echo "✓ (id: ${id})"
        ((ok++)) || true
    else
        echo "✗ $(echo "$result" | jq -r '.message // "Unbekannter Fehler"')"
        ((fail++)) || true
    fi
done

echo ""
echo "Fertig: ${ok} hochgeladen, ${fail} Fehler."
