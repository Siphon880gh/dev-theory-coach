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

# Local siblings (do not clone; they already sit next to this app):
#   ../leetcode-coach   PHP guides, coach trees, mini games — problem practice, not this syllabus
#   ../mental-math      PHP guides hub, drills, coach rewind — runtime patterns
#   ../stocks-trainer   React cases and path UI — case-player patterns only

clone_repo https://github.com/trekhleb/javascript-algorithms.git javascript-algorithms
clone_repo https://github.com/yangshun/tech-interview-handbook.git tech-interview-handbook
clone_repo https://github.com/jwasham/coding-interview-university.git coding-interview-university
clone_repo https://github.com/donnemartin/system-design-primer.git system-design-primer
clone_repo https://github.com/iluwatar/java-design-patterns.git java-design-patterns

echo "Done. Catalog: $ROOT/README.md"
