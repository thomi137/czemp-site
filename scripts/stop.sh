#!/bin/sh
cd cz-theme && npm run stop 2>/dev/null || true
docker compose down