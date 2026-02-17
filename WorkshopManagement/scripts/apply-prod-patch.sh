#!/usr/bin/env bash
set -euo pipefail

# apply-prod-patch.sh
# Usage:
#   ./scripts/apply-prod-patch.sh [--branch <branch-name>] [--dry-run]
# Options:
#   --branch BRANCH  - switch to or create a branch to commit the patch
#   --dry-run        - show what would be changed without applying

BRANCH=""
DRY_RUN=false

while [[ $# -gt 0 ]]; do
  case "$1" in
    --branch)
      BRANCH="$2"; shift 2;;
    --dry-run)
      DRY_RUN=true; shift;;
    *)
      echo "Unknown arg: $1"; exit 1;;
  esac
done

ZIP="supatronix-prod-final.zip"
if [ ! -f "$ZIP" ]; then
  echo "[apply-prod-patch] ERROR: Missing patch ZIP '$ZIP' in repo root. Run the patch generator first (tools/make-prod-zip.sh)."
  exit 2
fi

ROOT_DIR=$(pwd)
TMPDIR=$(mktemp -d)
echo "[apply-prod-patch] Working dir: $ROOT_DIR"
echo "[apply-prod-patch] Using temp dir: $TMPDIR"

echo "[apply-prod-patch] Extracting patch..."
if command -v unzip >/dev/null 2>&1; then
  unzip -q "$ZIP" -d "$TMPDIR"/
elif command -v 7z >/dev/null 2>&1; then
  7z x -y "$ZIP" -o"$TMPDIR" >/dev/null
else
  echo "[apply-prod-patch] ERROR: neither unzip nor 7z found. Install unzip or 7z to proceed."; exit 3
fi

echo "[apply-prod-patch] Preparing to apply patch..."
if [ -n "$BRANCH" ]; then
  echo "[apply-prod-patch] Creating/Switching to branch: $BRANCH"
  git checkout -B "$BRANCH"
fi

# Copy files from patch into repo. Do not delete files not in patch; only overwrite newer files.
RSYNC_CMD=$(command -v rsync >/dev/null 2>&1 && echo "rsync -a --no-owner --no-group")
if [ -n "$RSYNC_CMD" ]; then
  echo "[apply-prod-patch] Copying files with rsync..."
  rsync -a --exclude '.git' "$TMPDIR/" "$ROOT_DIR/"
else
  echo "[apply-prod-patch] rsync not available; falling back to cp -R."
  cp -R "$TMPDIR/." "$ROOT_DIR/"
fi

CHANGED=$(git status --porcelain | wc -l)
if [ "$CHANGED" -eq 0 ]; then
  echo "[apply-prod-patch] No changes detected after applying patch."
  rm -rf "$TMPDIR"
  exit 0
fi

if [ "$DRY_RUN" = true ]; then
  echo "[apply-prod-patch] DRY RUN: changes detected but not committed."
  echo "Changed files (preview):"
  git status --porcelain
  rm -rf "$TMPDIR"
  exit 0
fi

echo "[apply-prod-patch] Staging and committing changes..."
git add -A
git commit -m "prod(final): apply Supatronix MVP patch (RBAC runtime, Phase 2, Not-Repairable PDF, seeds, CI/CD, Docker Compose)"

echo "[apply-prod-patch] Patch applied on branch: $(git rev-parse --abbrev-ref HEAD)"
git log -1 --oneline

echo "[apply-prod-patch] Done. You can now push the branch and open a PR."
rm -rf "$TMPDIR"
