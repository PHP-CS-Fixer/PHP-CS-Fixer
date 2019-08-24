#!/bin/sh
set -eu

TOKEN=$1
MSG_SUFFIX=${2:-''}

if [ -n "$MSG_SUFFIX" ]
then
    MSG_SUFFIX=" for ${MSG_SUFFIX}"
fi

REPO=$(echo "PHP-CS-Fixer/PHP-CS-Fixer.github.io" | sed "s@/@%2F@g")

body="{
    \"request\": {
        \"branch\": \"generate\",
        \"message\": \"Build triggered automatically${MSG_SUFFIX}\"
    }
}"

curl -s -X POST \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Travis-API-Version: 3" \
    -H "User-Agent: API Explorer" \
    -H "Authorization: token ${TOKEN}" \
    -d "${body}" \
    "https://api.travis-ci.org/repo/${REPO}/requests"
