#!/bin/sh
set -eu

# Check for American English spellings that should be British English
# Excludes: CHANGELOG.md (historical), vendor/, generated docs, class/method names, and test fixtures

# @TODO v4: conclude on removing exceptions for "Analyzer" (shall be Analyser)

american_spellings=$(
    git grep -In \
        -e '\bbehavior\b[^(]' \
        -e '\bfavor\b[^(]' \
        -e '\bhonor\b[^(]' \
        -e '\banalyze\b[^(]' \
        -e '\banalyzes\b[^(]' \
        -e '\binitialize\b[^(]' \
        -e '\binitializes\b[^(]' \
        ':!vendor/*' \
        ':!*.lock' \
    | grep -v 'behaviour' \
    | grep -v 'favour' \
    | grep -v 'honour' \
    | grep -v 'analyse' \
    | grep -v 'analyses' \
    | grep -v 'initialise' \
    | grep -v 'initialises' \
    | grep -v 'TokensAnalyzer' \
    | grep -v 'ControlCaseStructuresAnalyzer' \
    | grep -v 'AttributeAnalyzer' \
    | grep -v 'GotoLabelAnalyzer' \
    | grep -v 'DataProviderAnalyzer' \
    | sort -fh \
    || true
)

if [ "$american_spellings" ]
then
    printf '\033[97;41mAmerican English spellings detected:\033[0m\n'
    printf '\033[0;31mPlease use British English spelling (e.g., "behaviour" not "behavior", "colour" not "color").\033[0m\n\n'
    echo "${american_spellings}"
    exit 3
fi

printf '\033[0;32mNo American English spellings detected.\033[0m\n'
