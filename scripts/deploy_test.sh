#!/bin/sh
REMOTE_USER="claudize"
REMOTE_HOST="claudia-zemp.ch"
REMOTE_PATH="/home/claudize/www/neu.claudia-zemp.ch/wp-content/themes/cz-theme"
SSH_KEY="$HOME/.ssh/claudize"

echo "Deploy to ${REMOTE_HOST}? (y/n)"
read answer
if [ "$answer" != "y" ]; then
    echo "Aborted."
    exit 0
fi

# Build zuerst
(cd cz-theme && npm run build)

# Packen ohne node_modules
tar --exclude='cz-theme/node_modules' -czf cz-theme.tar.gz cz-theme/

# Ordner anlegen falls nicht vorhanden
ssh -i ${SSH_KEY} ${REMOTE_USER}@${REMOTE_HOST} "mkdir -p ${REMOTE_PATH}"

# Rüberkopieren und entpacken
scp -i ${SSH_KEY} cz-theme.tar.gz ${REMOTE_USER}@${REMOTE_HOST}:/tmp/
ssh -i ${SSH_KEY} ${REMOTE_USER}@${REMOTE_HOST} "tar -xzf /tmp/cz-theme.tar.gz -C ${REMOTE_PATH} --strip-components=1 && rm /tmp/cz-theme.tar.gz"

# Aufräumen
rm cz-theme.tar.gz

echo "Deploy done."
