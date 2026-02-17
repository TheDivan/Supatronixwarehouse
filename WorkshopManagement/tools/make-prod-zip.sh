#!/usr/bin/env bash
set -euo pipefail

# Production zip packager for Supatronix MVP final patch
ROOT_DIR=$(cd "$(dirname "$0")/.." && pwd)
OUTPUT="supatronix-prod-final.zip"

echo "[prod-zip] Packaging Supatronix MVP final patch..."
echo "[prod-zip] Source root: $ROOT_DIR"

cd "$ROOT_DIR"

# Try to use git archive if git exists and it's a repo
if [ -d ".git" ]; then
  if command -v git >/dev/null 2>&1; then
    echo "[prod-zip] Using git archive HEAD..."
    git archive --format=zip --output="$OUTPUT" HEAD
    echo "[prod-zip] Created $OUTPUT from HEAD."
    exit 0
  else
    echo "[prod-zip] git not available, falling back to tarball."
  fi
fi

# Fallback: create a tarball of the repository contents and zip it.
echo "[prod-zip] Creating a tarball and then zipping..."
TARFILE="supatronix-prod-final.tar.gz"
tar -czf "$TARFILE" -C "$ROOT_DIR" .
zip -r "$OUTPUT" "$TARFILE" >/dev/null 2>&1 || true
if [ -f "$OUTPUT" ]; then
  echo "[prod-zip] Created $OUTPUT from tarball fallback."
else
  echo "[prod-zip] Zip failed; leaving tarball as fallback."
fi
rm -f "$TARFILE" || true
echo "[prod-zip] Done."
