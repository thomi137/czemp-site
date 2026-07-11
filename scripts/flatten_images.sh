#!/bin/sh
SRC="/Users/tprosser/Projects/czemp-site/scripts/migrate/images/images_original"
DST="/Users/tprosser/Projects/czemp-site/scripts/migrate/images/images_flat"

mkdir -p "$DST"

find "$SRC" -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" -o -iname "*.webp" -o -iname "*.gif" -o -iname "*.heic" \) | while read -r file; do
    base=$(basename "$file")
    dest="$DST/$base"
    if [ -e "$dest" ]; then
        name="${base%.*}"
        ext="${base##*.}"
        i=1
        while [ -e "$DST/${name}_${i}.${ext}" ]; do
            i=$((i + 1))
        done
        dest="$DST/${name}_${i}.${ext}"
    fi
    cp "$file" "$dest"
done

COUNT=$(find "$DST" -type f | wc -l | tr -d ' ')
ZIP="/Users/tprosser/Projects/czemp-site/scripts/migrate/images/images_flat.zip"
cd "$DST" && zip -q "$ZIP" ./*
rm -rf "$DST"
echo "Fertig. $COUNT Bilder → $ZIP"
