#!/bin/sh
# Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.
#
# Pushes backup.sh/cleanup.sh/restore.sh to the server, outside the public
# webroot (same reasoning as ~/backups itself). Run whenever these scripts
# change — the cron entry that actually schedules backup.sh is set up by
# hand in the hosting provider's control panel, not by this script.
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

REMOTE_USER="claudize"
REMOTE_HOST="claudia-zemp.ch"
REMOTE_SCRIPTS_DIR="/home/claudize/scripts"
SSH_KEY="$HOME/.ssh/claudize"

echo "Deploy backup/cleanup/restore scripts to ${REMOTE_HOST}:${REMOTE_SCRIPTS_DIR}? (y/n)"
read answer
if [ "$answer" != "y" ]; then
    echo "Aborted."
    exit 0
fi

ssh -i ${SSH_KEY} ${REMOTE_USER}@${REMOTE_HOST} "mkdir -p ${REMOTE_SCRIPTS_DIR}"
scp -i ${SSH_KEY} "${SCRIPT_DIR}/backup.sh" "${SCRIPT_DIR}/cleanup.sh" "${SCRIPT_DIR}/restore.sh" ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_SCRIPTS_DIR}/
ssh -i ${SSH_KEY} ${REMOTE_USER}@${REMOTE_HOST} "chmod +x ${REMOTE_SCRIPTS_DIR}/*.sh"

echo "Deployed."
echo "Cron path for your hosting panel: ${REMOTE_SCRIPTS_DIR}/backup.sh"
