#!/bin/sh

set -eu

IFS='
'

SHELL_SCRIPTS=$(
    find ./ \
    -type f \
    -not -path '*/vendor/*' \
    -not -path './dev-tools/ci-integration.sh' \
    -iname '*.sh'
)

# shellcheck disable=SC2086
checkbashisms $SHELL_SCRIPTS

# shellcheck disable=SC2086
shellcheck $SHELL_SCRIPTS

shellcheck --exclude=SC2086 ./dev-tools/ci-integration.sh
