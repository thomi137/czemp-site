#!/usr/bin/env bash
# Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.
#
# Runs ON THE SERVER — called by backup.sh after every backup, and
# runnable standalone too. Keeps the newest KEEP_COUNT backups, deletes
# the rest. Count-based rather than age-based: robust to the cron firing
# early/late/twice, doesn't need to track calendar months itself.
set -euo pipefail

BACKUP_DIR="$HOME/backups"
KEEP_COUNT=6

cd "$BACKUP_DIR"

# Newest first (ls -t); anything past KEEP_COUNT gets deleted. `|| true`
# on the ls guards the zero-backups case — under pipefail, ls's "no match"
# exit code would otherwise abort the whole script via set -e before the
# pipeline's later stages (which handle finding nothing just fine) run.
ls -1t czemp-backup-*.zip 2>/dev/null | tail -n "+$((KEEP_COUNT + 1))" | while IFS= read -r old; do
    echo "Deleting old backup: ${old}"
    rm -f "$old"
done || true
