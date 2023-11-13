#!/bin/sh
set -eu

# ensure that deps will work on lowest supported PHP version
composer config platform.php 7.4

# install package deps without dev-deps / remove already installed dev-deps
# box can ignore dev-deps, but dev-deps, when installed, may lower version of prod-deps
composer update --optimize-autoloader --no-interaction --no-progress --no-scripts --no-dev
composer info -D | sort

composer show -d dev-tools humbug/box -q || composer update -d dev-tools --no-interaction --no-progress

# build phar file
dev-tools/vendor/bin/box compile

# revert changes to composer
git checkout composer.json
composer update --optimize-autoloader --no-interaction --no-progress --no-scripts -q
