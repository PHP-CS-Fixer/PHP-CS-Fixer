#!/bin/sh
set -eu

files_with_wrong_permissions=$(
    git ls-files --stage . \
        ':!*.sh' \
        ':!php-cs-fixer' \
        ':!dev-tools/php-cs-fixer-internal' \
        ':!dev-tools/*.php' \
    | grep -E "^100755 " \
    | sort -fh
)

if [ "$files_with_wrong_permissions" ]
then
    printf '\033[97;41mWrong permissions detected:\033[0m\n'
    echo "${files_with_wrong_permissions}"
    exit 3
fi

if [ -x "php-cs-fixer" ]
then
    echo '"php-cs-fixer" is executable'
else
    echo '"php-cs-fixer" not is executable'
    exit 4
fi

if [ -x "dev-tools/php-cs-fixer-internal" ]
then
    echo '"dev-tools/php-cs-fixer-internal" is executable'
else
    echo '"dev-tools/php-cs-fixer-internal" not is executable'
    exit 4
fi

printf '\033[0;32mNo wrong permissions detected.\033[0m\n'
