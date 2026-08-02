#!/usr/bin/env zsh
# Copyright (c) 2026 Thomas Prosser. All rights reserved.
# Zwei Modi:
#   ./download.sh [data] [output.json]   Werke+Kollektionen als JSON exportieren (Standard)
#   ./download.sh media [output.zip]     ALLE Mediendateien (jeder Status) herunterladen und zippen
#                                         — Backup vor dem Löschen der Medienbibliothek.
# Requires: curl, jq. Für "media" zusätzlich WP_USER + WP_APP_PASSWORD in .env
# (WP-Admin → Benutzer → Profil → Anwendungspasswörter).

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

if [[ ! -f "${SCRIPT_DIR}/.env" ]]; then
    echo "Fehler: .env nicht gefunden." >&2
    exit 1
fi
source "${SCRIPT_DIR}/.env"

BASE="${WP_URL}/wp-json/wp/v2"

MODE="${1:-data}"
if [[ "$MODE" != "media" && "$MODE" != "data" ]]; then
    # Rückwärtskompatibel: erstes Argument war schon immer der JSON-Output-Pfad
    OUTPUT="$MODE"
    MODE="data"
else
    OUTPUT="${2:-}"
fi

download_data() {
    local output="${OUTPUT:-artworks.json}"

    echo "Kollektionen herunterladen..."
    curl -s "${BASE}/collection?per_page=100" > /tmp/cz_collections.json

    echo "Werke herunterladen..."
    pages=$(curl -sI "${BASE}/artwork?per_page=100" \
        | grep -i 'x-wp-totalpages:' | tr -d '\r' | awk '{print $2}')
    pages=${pages:-1}

    for page in $(seq 1 $pages); do
        echo "  Seite ${page}/${pages}..."
        curl -s "${BASE}/artwork?per_page=100&page=${page}&_embed=1" > "/tmp/cz_artworks_${page}.json"
    done

    jq -s 'add' /tmp/cz_artworks_*.json > /tmp/cz_artworks.json
    rm -f /tmp/cz_artworks_*.json

    echo "Exportiere nach ${output}..."

    jq -n \
        --slurpfile collections /tmp/cz_collections.json \
        --slurpfile artworks    /tmp/cz_artworks.json \
        '{
            exported_at: now | todate,
            collections: [
                $collections[0][] | { id, name, slug, description }
            ],
            artworks: [
                $artworks[0][] | {
                    id,
                    title:      .title.rendered,
                    excerpt:    .excerpt.rendered,
                    content:    .content.rendered,
                    collection: .collection,
                    image_url:  (._embedded["wp:featuredmedia"][0].source_url? // null),
                    date:       .date
                }
            ]
        }' > "$output"

    rm -f /tmp/cz_collections.json /tmp/cz_artworks.json

    total=$(jq '.artworks | length' "$output")
    echo "✓ ${total} Werke gespeichert in ${output}"
}

download_media() {
    local output="${OUTPUT:-${SCRIPT_DIR}/media_backup_$(date +%Y-%m-%d).zip}"

    if [[ -z "${WP_USER:-}" || -z "${WP_APP_PASSWORD:-}" ]]; then
        echo "Fehler: WP_USER / WP_APP_PASSWORD nicht in .env gesetzt (siehe .env.example)." >&2
        exit 1
    fi

    local tmpdir
    tmpdir=$(mktemp -d)
    trap "rm -rf '$tmpdir'" EXIT

    echo "Prüfe Verbindung/Auth gegen ${BASE}/media ..."
    local http_status
    http_status=$(curl -sS -o /dev/null -w '%{http_code}' \
        -u "${WP_USER}:${WP_APP_PASSWORD}" "${BASE}/media?per_page=1" 2>&1) || true
    if [[ "$http_status" != "200" ]]; then
        echo "Fehler: HTTP ${http_status:-<keine Antwort, Verbindung fehlgeschlagen>} von ${BASE}/media." >&2
        echo "Häufigste Ursache bei 401: der Hosting-Server strippt den Authorization-Header" >&2
        echo "(Shared Hosting/Apache) — dann braucht es in .htaccess:" >&2
        echo '  RewriteCond %{HTTP:Authorization} ^(.*)' >&2
        echo '  RewriteRule .* - [E=HTTP_AUTHORIZATION:%1]' >&2
        echo "Sonst: WP_URL, WP_USER, WP_APP_PASSWORD in .env prüfen." >&2
        exit 1
    fi

    echo "Ermittle Seitenzahl der Medienbibliothek..."
    local pages
    pages=$(curl -sI -u "${WP_USER}:${WP_APP_PASSWORD}" "${BASE}/media?per_page=100" \
        | grep -i 'x-wp-totalpages:' | tr -d '\r' | awk '{print $2}') || true
    pages=${pages:-1}

    local total=0
    for page in $(seq 1 "$pages"); do
        echo "  Seite ${page}/${pages}..."
        curl -s -u "${WP_USER}:${WP_APP_PASSWORD}" \
            "${BASE}/media?per_page=100&page=${page}" \
            > "${tmpdir}/page_${page}.json"

        local count
        count=$(jq 'length' "${tmpdir}/page_${page}.json")
        for i in $(seq 0 $((count - 1))); do
            local url filename id
            url=$(jq -r ".[$i].source_url" "${tmpdir}/page_${page}.json")
            id=$(jq -r ".[$i].id" "${tmpdir}/page_${page}.json")
            [[ "$url" == "null" || -z "$url" ]] && continue
            filename=$(basename "$url")
            # ID-Präfix gegen Dateinamenskollisionen (z.B. gleich benannte Uploads)
            curl -s -u "${WP_USER}:${WP_APP_PASSWORD}" "$url" -o "${tmpdir}/${id}_${filename}"
            (( total++ )) || true
        done
        rm -f "${tmpdir}/page_${page}.json"
    done

    echo "Zippe ${total} Dateien nach ${output}..."
    (cd "$tmpdir" && zip -q -r "$output" .)

    echo "✓ ${total} Mediendateien gesichert in ${output}"
}

case "$MODE" in
    media) download_media ;;
    data)  download_data ;;
esac
