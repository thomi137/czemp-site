#!/bin/sh
# Copyright (c) 2026 Thomas Prosser. All rights reserved.

cleanup() {
    echo "Stopping Docker..."
    docker compose down
}

trap cleanup INT TERM

docker compose up -d
cd cz-theme && npm run start
