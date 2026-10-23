#!/usr/bin/env bash
# Shallow-clones reference repos into context/repos/ (gitignored).
# Re-run anytime: existing clones are fast-forwarded.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
REPOS="$ROOT/repos"
mkdir -p "$REPOS"

clone_repo() {
  local url="$1"
  local dest="$2"
  local path="$REPOS/$dest"

  if [[ -d "$path/.git" ]]; then
    echo "Updating $dest ..."
    git -C "$path" pull --ff-only
  elif [[ -d "$path" ]]; then
    echo "Skipping $dest: $path exists but is not a git clone"
  else
    echo "Cloning $dest ..."
    git clone --depth 1 "$url" "$path"
  fi
}

# AGENT: one line per source. Keep the folder name in sync with context/README.md.
# clone_repo https://github.com/<owner>/<repo>.git <folder-name>

echo "Done. Catalog: $ROOT/README.md"
