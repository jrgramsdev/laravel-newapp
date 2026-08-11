#!/usr/bin/env bash
#
# push.sh — stage, commit, sync, and push in one step.
#
# Usage:
#   ./push.sh "your commit message"
#   ./push.sh                       # prompts for a message
#
set -euo pipefail

# Always run from the project root (the folder this script lives in).
cd "$(dirname "$0")"

# 1. Commit message: use the arguments, or ask for one.
MSG="$*"
if [ -z "$MSG" ]; then
    printf "Commit message: "
    read -r MSG
fi
if [ -z "$MSG" ]; then
    echo "✋ No commit message given. Aborting."
    exit 1
fi

# 2. Stage everything.
git add -A

# 3. Commit — but only if there's something staged.
if git diff --cached --quiet; then
    echo "ℹ️  No file changes to commit."
else
    git commit -m "$MSG"
    echo "✅ Committed: $MSG"
fi

# 4. Sync with the remote first so the push isn't rejected.
BRANCH="$(git rev-parse --abbrev-ref HEAD)"
echo "⤵️  Pulling latest from origin/$BRANCH ..."
git pull --rebase origin "$BRANCH"

# 5. Push.
echo "⤴️  Pushing to origin/$BRANCH ..."
git push -u origin "$BRANCH"

echo "🎉 Done. origin/$BRANCH is up to date (Render will auto-deploy)."
