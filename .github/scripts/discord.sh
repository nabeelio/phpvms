#!/usr/bin/env bash

./version.sh

curl -X POST \
  --data "{\"content\": \"A new build is available at http://downloads.phpvms.net/$TAR_NAME (${FULL_VERSION})\"}" \
  -H "Content-Type: application/json" \
  $DISCORD_WEBHOOK