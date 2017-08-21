#!/usr/bin/env bash
set -e

IFS=$'\n'; CHANGED_FILES=($(git diff --name-only --diff-filter=ACMRTUXB "${COMMIT_RANGE}")); unset IFS
EXTRA_ARGS=('--path-mode=intersection' '--' "${CHANGED_FILES[@]}")
vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run --stop-on-violation --using-cache=no "${EXTRA_ARGS[@]}"
