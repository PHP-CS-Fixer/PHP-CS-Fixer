#!/usr/bin/env bash
set -e

# ensure that deps will work on lowest supported PHP version
composer config platform.php 2> /dev/null || composer config platform.php 5.3.6

# require suggested packages
composer require --no-update symfony/polyfill-mbstring

# install package deps without dev-deps / remove already installed dev-deps
composer update --no-interaction --no-progress --no-dev --prefer-stable
composer info -D | sort

# install box2 globally
composer global show kherge/box -q || composer global require --no-interaction --no-progress kherge/box:^2.7

# build phar file
php -d phar.readonly=false $(composer config home)/vendor/bin/box build

# revert changes to composer
git checkout composer.json
composer update --no-interaction --no-progress -q
