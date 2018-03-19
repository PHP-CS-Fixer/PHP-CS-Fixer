#!/bin/sh

command -v php >/dev/null 2>&1 || { echo "I require \`php\` but it's not available. Aborting." >&2; exit 255; }
command -v grep >/dev/null 2>&1 || { echo "I require \`grep\` but it's not available. Aborting." >&2; exit 255; }
command -v awk >/dev/null 2>&1 || { echo "I require \`awk\` but it's not available. Aborting." >&2; exit 255; }

if [ "" = "$1" ] || [ "" = "$2" ];
then
    echo "Usage: bash benchmark.sh BRANCH1 BRANCH2 ...BRANCHN"
    exit 1;
fi

for BRANCH in "$@"
do
    git checkout "$BRANCH" > /dev/null 2>&1 &&
    git reset --hard > /dev/null 2>&1 &&
    printf '%s' "$BRANCH"
    (for _ in $(seq 1 10); do php php-cs-fixer fix --dry-run 2> /dev/null ; done) | grep -i seconds | awk '
    {
        total += $5;
        ++count;
    }
    END {
        print " mean:" (total/count) " total:" total " rounds:" count
    }'
done
