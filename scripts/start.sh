#!/bin/sh
# Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

cleanup() {
    echo "Stopping Docker..."
    docker compose down
}

trap cleanup INT TERM

docker compose up -d
cd cz-theme && npm run start
