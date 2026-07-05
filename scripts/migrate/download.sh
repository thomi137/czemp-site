#!/usr/bin/env zsh
# Download all artworks and collections from WordPress to JSON
# Usage: ./download.sh [output.json]
# Requires: curl, jq

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

if [[ ! -f "${SCRIPT_DIR}/.env" ]]; then
    echo "Fehler: .env nicht gefunden." >&2
    exit 1
fi

source "${SCRIPT_DIR}/.env"

OUTPUT="${1:-artworks.json}"
BASE="${WP_URL}/wp-json/wp/v2"

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

echo "Exportiere nach ${OUTPUT}..."

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
    }' > "$OUTPUT"

rm -f /tmp/cz_collections.json /tmp/cz_artworks.json

total=$(jq '.artworks | length' "$OUTPUT")
echo "✓ ${total} Werke gespeichert in ${OUTPUT}"
