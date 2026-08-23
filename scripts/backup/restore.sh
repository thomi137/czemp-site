#!/usr/bin/env bash
# Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.
#
# Runs ON THE SERVER, manual only — never scheduled. Restores the live
# site (files + DB) from a backup.sh archive. Destructive: rsync --delete
# makes the webroot match the archive exactly, removing anything added
# since that backup was taken. Dumps the current DB to a pre-restore
# safety file first, so a mistaken restore is itself recoverable.
set -euo pipefail

BACKUP_DIR="$HOME/backups"
WEBROOT="$HOME/www/claudia-zemp.ch"

if [ $# -ne 1 ]; then
    echo "Usage: $0 <backup-filename.zip | /full/path/to/backup.zip>"
    exit 1
fi

BACKUP_FILE="$1"
case "$BACKUP_FILE" in
    /*) : ;; # already an absolute path
    *) BACKUP_FILE="${BACKUP_DIR}/${BACKUP_FILE}" ;;
esac

if [ ! -f "$BACKUP_FILE" ]; then
    echo "Backup not found: ${BACKUP_FILE}"
    exit 1
fi

echo "This will OVERWRITE the live site (all files under ${WEBROOT} and the database)"
echo "with the contents of: ${BACKUP_FILE}"
echo "Anything added to the live site since that backup was taken will be deleted."
echo "Type YES (all caps) to continue:"
read -r confirm
if [ "$confirm" != "YES" ]; then
    echo "Aborted."
    exit 0
fi

WORK_DIR="$(mktemp -d)"
trap 'rm -rf "$WORK_DIR"' EXIT

echo "Extracting backup..."
unzip -q "$BACKUP_FILE" -d "$WORK_DIR"

if [ ! -f "${WORK_DIR}/database.sql" ] || [ ! -d "${WORK_DIR}/www/claudia-zemp.ch" ]; then
    echo "Archive doesn't look like a backup.sh output (missing database.sql or www/claudia-zemp.ch) — aborting."
    exit 1
fi

SAFETY_TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
SAFETY_FILE="${BACKUP_DIR}/pre-restore-${SAFETY_TIMESTAMP}.sql"
echo "Saving current database to ${SAFETY_FILE} first, as a safety net..."
(cd "$WEBROOT" && wp db export "$SAFETY_FILE")

echo "Restoring files..."
rsync -a --delete "${WORK_DIR}/www/claudia-zemp.ch/" "${WEBROOT}/"

echo "Restoring database..."
(cd "$WEBROOT" && wp db import "${WORK_DIR}/database.sql")

echo "Restore complete."
