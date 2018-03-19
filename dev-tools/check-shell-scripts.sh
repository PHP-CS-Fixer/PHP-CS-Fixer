#!/bin/sh

set -eu

IFS='
'

SHELL_SCRIPTS=$(find ./ -type f -not -path '*/vendor/*' -iname '*.sh')

# shellcheck disable=SC2086
checkbashisms $SHELL_SCRIPTS

# shellcheck disable=SC2086
shellcheck $SHELL_SCRIPTS
