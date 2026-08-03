#!/bin/sh
# Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.
cd cz-theme && npm run stop 2>/dev/null || true
docker compose down