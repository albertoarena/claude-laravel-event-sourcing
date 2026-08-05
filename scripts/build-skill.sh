#!/usr/bin/env bash
# Build the distributable .skill archive from the canonical skill directory
# under plugins/. Run before tagging a release so the .skill artifact at the
# repo root matches the current skill contents.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SKILL_NAME="laravel-spatie-event-sourcing"
SKILLS_DIR="$REPO_ROOT/plugins/$SKILL_NAME/skills"
SOURCE_DIR="$SKILLS_DIR/$SKILL_NAME"
OUTPUT="$REPO_ROOT/$SKILL_NAME.skill"

if [ ! -d "$SOURCE_DIR" ]; then
    echo "ERROR: source directory not found: $SOURCE_DIR" >&2
    exit 1
fi

rm -f "$OUTPUT"
cd "$SKILLS_DIR"
zip -rq "$OUTPUT" "$SKILL_NAME" -x "*.DS_Store" "*/.*"

echo "OK: built $OUTPUT ($(du -h "$OUTPUT" | cut -f1))"
