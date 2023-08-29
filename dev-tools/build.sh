#!/bin/sh
set -eu

# ensure that deps will work on lowest supported PHP version
composer config platform.php 7.4

# Include Doctrine Annotations and Lexer in the PHAR build (for people that use `@DoctrineAnnotation rule set).
# Only add requirement to the composer.json, it will be installed later.
composer require doctrine/annotations:"^2" doctrine/lexer:"^2 || ^3" --no-update

# install package deps without dev-deps / remove already installed dev-deps
# box can ignore dev-deps, but dev-deps, when installed, may lower version of prod-deps
composer update --no-interaction --no-progress --no-dev --prefer-stable --optimize-autoloader
composer info -D | sort

composer show -d dev-tools humbug/box -q || composer update -d dev-tools --no-interaction --no-progress

# build phar file
dev-tools/vendor/bin/box compile

# revert changes to composer
git checkout composer.json
composer update --no-interaction --no-progress -q
