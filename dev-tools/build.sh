#!/bin/sh
set -eu

# ensure that deps will work on lowest supported PHP version
composer config platform.php 2> /dev/null || composer config platform.php 5.6.0

# require suggested packages
composer require --no-update symfony/polyfill-mbstring

# install package deps without dev-deps / remove already installed dev-deps
composer update --no-interaction --no-progress --no-dev --prefer-stable
composer info -D | sort

if [ ! -f dev-tools/vendor ]; then
    composer install --working-dir dev-tools
fi

# build phar file
dev-tools/vendor/bin/box compile

# revert changes to composer
git checkout composer.json
composer update --no-interaction --no-progress -q
