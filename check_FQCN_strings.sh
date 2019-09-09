#!/bin/sh
set -eu

FQCN_strings=$(
    find . \
        -type f \
        -name '*.php' \
        -not -path "./.git/*" \
        -not -path "./dev-tools/bin/*" \
        -not -path "./dev-tools/vendor/*" \
        -not -path "./vendor/*" \
        -not -path "./tests/Fixtures/*" \
        -exec grep -EIHn "'[a-zA-Z0-9]+(\\\\{1,2}[a-zA-Z0-9]+)+'" {} \; \
    | sort -fh
)

if [ "$FQCN_strings" ]
then
    printf '\033[97;41mUsage of FQCN_strings detected:\033[0m\n'
    e=$(printf '\033')
    echo "${FQCN_strings}" | sed -E "s/^\\.\\/([^:]+):([0-9]+):(.*[^\\t ])?([\\t ]+)$/${e}[0;31m - in ${e}[0;33m\\1${e}[0;31m at line ${e}[0;33m\\2\\n   ${e}[0;31m>${e}[0m \\3${e}[41;1m\\4${e}[0m/"

    exit 3
fi

printf '\033[0;32mNo usage of FQCN_strings detected.\033[0m\n'
