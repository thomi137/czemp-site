#!/bin/sh
# Copyright (c) 2026 Thomas Prosser. All rights reserved.
cd cz-theme && npm run stop 2>/dev/null || true
docker compose down