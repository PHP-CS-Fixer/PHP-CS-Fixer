#!/bin/sh
set -eu

IFS='
'
CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB "${COMMIT_RANGE}")
if ! echo "${CHANGED_FILES}" | grep -qE "^(\\.php-cs-fixer(\\.dist)?\\.php|composer\\.lock)$"; then EXTRA_ARGS=$(printf -- '--path-mode=intersection\n--\n%s' "${CHANGED_FILES}"); else EXTRA_ARGS=''; fi
vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php -v --dry-run --stop-on-violation --using-cache=no ${EXTRA_ARGS}
