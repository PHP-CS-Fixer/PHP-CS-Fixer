#!/bin/sh
set -eu

files_with_trailing_spaces=$(
    git grep -In "\\s$" \
        ':!doc/rules/*' \
        ':!tests/Fixtures/*' \
    | sort -fh
)

if [ "$files_with_trailing_spaces" ]
then
    printf '\033[97;41mTrailing whitespaces detected:\033[0m\n'
    e=$(printf '\033')
    echo "${files_with_trailing_spaces}" | sed -E "s/^([^:]+):([0-9]+):(.*[^\\t ])?([\\t ]+)$/${e}[0;31m - in ${e}[0;33m\\1${e}[0;31m at line ${e}[0;33m\\2\\n   ${e}[0;31m>${e}[0m \\3${e}[41;1m\\4${e}[0m/"

    exit 3
fi

printf '\033[0;32mNo trailing whitespaces detected.\033[0m\n'
