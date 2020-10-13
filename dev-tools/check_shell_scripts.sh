#!/bin/sh

set -eu

cd "$(dirname "$0")"

IFS='
'

SHELL_SCRIPTS=$(
    find ./ \
    -type f \
    -not -path '*/vendor/*' \
    -iname '*.sh'
)

# shellcheck disable=SC2086
bin/checkbashisms $SHELL_SCRIPTS

# shellcheck disable=SC2086
bin/shellcheck $SHELL_SCRIPTS
