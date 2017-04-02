#!/bin/bash

files_with_trailing_spaces=$(find . -type f -not -path "./.git/*" -not -path "./vendor/*" -not -path "./tests/Fixtures/*" -exec egrep -nH " $" {} \;)

if [[ $files_with_trailing_spaces ]]
then
    echo -e "\e[97;41mTrailing spaces detected:\e[0m"
    e=$(printf '\033')
    echo "${files_with_trailing_spaces}" | sed -E "s/^\.\/([^:]+):([0-9]+):(.*[^ ])( +)$/${e}[0;31m - in ${e}[0;33m\1${e}[0;31m at line ${e}[0;33m\2\n   ${e}[0;31m>${e}[0m \3${e}[41m\4${e}[0m/"

    exit 1
fi

echo -e "\e[0;32mNo trailing spaces detected.\e[0m"
