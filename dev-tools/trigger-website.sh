#!/usr/bin/env bash
set -e

TOKEN=$1
MSG_SUFFIX=$2

if [ ! -z $MSG_SUFFIX ]
then
    MSG_SUFFIX=" for ${MSG_SUFFIX}"
fi

REPO=$(sed "s@/@%2F@g" <<< "PHP-CS-Fixer/PHP-CS-Fixer.github.io")

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
    https://api.travis-ci.org/repo/${REPO}/requests
