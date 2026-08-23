#!/usr/bin/env bash
# Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.
#
# Runs ON THE SERVER (deployed there by deploy.sh), triggered by a cron job
# set up in the hosting provider's control panel — not by anything in this
# repo. Zips the entire webroot + a DB dump into one timestamped archive in
# ~/backups (outside the public docroot on purpose — a backup left inside
# the webroot would be downloadable by anyone who finds the URL), then
# prunes old ones via cleanup.sh so a single cron entry is enough.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
WEBROOT="$HOME/www/claudia-zemp.ch"
BACKUP_DIR="$HOME/backups"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
ARCHIVE="${BACKUP_DIR}/czemp-backup-${TIMESTAMP}.zip"

WORK_DIR="$(mktemp -d)"
trap 'rm -rf "$WORK_DIR"' EXIT

mkdir -p "$BACKUP_DIR"

echo "Exporting database..."
(cd "$WEBROOT" && wp db export "${WORK_DIR}/database.sql")

echo "Zipping webroot..."
(
    cd "$HOME"
    zip -rq "$ARCHIVE" "www/claudia-zemp.ch" \
        -x "www/claudia-zemp.ch/wp-content/upgrade/*" \
        -x "www/claudia-zemp.ch/wp-content/upgrade-temp-backup/*" \
        -x "www/claudia-zemp.ch/wp-content/cache/*"
)

echo "Adding database dump..."
(cd "$WORK_DIR" && zip -q "$ARCHIVE" database.sql)

echo "Backup created: ${ARCHIVE}"

echo "Pruning old backups..."
"${SCRIPT_DIR}/cleanup.sh"

echo "Done."
