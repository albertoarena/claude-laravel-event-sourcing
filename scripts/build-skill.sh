#!/usr/bin/env bash
# Build the distributable .skill archive from skill/laravel-spatie-event-sourcing.
# Run before tagging a release so the .skill artifact at the repo root matches
# the current skill/ directory contents.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SKILL_NAME="laravel-spatie-event-sourcing"
SOURCE_DIR="$REPO_ROOT/skill/$SKILL_NAME"
OUTPUT="$REPO_ROOT/$SKILL_NAME.skill"

if [ ! -d "$SOURCE_DIR" ]; then
    echo "ERROR: source directory not found: $SOURCE_DIR" >&2
    exit 1
fi

rm -f "$OUTPUT"
cd "$REPO_ROOT/skill"
zip -rq "$OUTPUT" "$SKILL_NAME" -x "*.DS_Store" "*/.*"

echo "OK: built $OUTPUT ($(du -h "$OUTPUT" | cut -f1))"
