#!/usr/bin/env bash
set -e

CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB "${COMMIT_RANGE}")
if ! echo "${CHANGED_FILES}" | grep -qE "^(\\.php_cs(\\.dist)?|composer\\.lock)$"; then IFS=$'\n' EXTRA_ARGS=('--path-mode=intersection' '--' ${CHANGED_FILES[@]}); fi
vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run --stop-on-violation --using-cache=no "${EXTRA_ARGS[@]}"
