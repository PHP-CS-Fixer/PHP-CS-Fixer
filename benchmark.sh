#!/bin/sh
set -eu

command -v php >/dev/null 2>&1 || { echo "I require \`php\` but it's not available. Aborting." >&2; exit 255; }
command -v grep >/dev/null 2>&1 || { echo "I require \`grep\` but it's not available. Aborting." >&2; exit 255; }
command -v awk >/dev/null 2>&1 || { echo "I require \`awk\` but it's not available. Aborting." >&2; exit 255; }

BRANCH1=${1:-''}
BRANCH2=${2:-''}

if [ "" = "$BRANCH1" ] || [ "" = "$BRANCH2" ];
then
    echo "Usage: ./benchmark.sh BRANCH1 BRANCH2 ...BRANCHN"
    exit 1;
fi

for BRANCH in "$@"
do
    git checkout "$BRANCH" > /dev/null 2>&1 &&
    git reset --hard > /dev/null 2>&1 &&
    printf '%s' "$BRANCH"
    composer update -q
    (for _ in $(seq 1 10); do php php-cs-fixer fix --dry-run 2> /dev/null ; done) | grep -i seconds | awk '
    {
        total += $5;
        ++count;
    }
    END {
        print " mean:" (total/count) " total:" total " rounds:" count
    }'
done
