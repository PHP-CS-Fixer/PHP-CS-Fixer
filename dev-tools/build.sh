#!/usr/bin/env bash
set -e

# ensure that deps will work on lowest supported PHP version
composer config platform.php 2> /dev/null || composer config platform.php 5.6.0

# require suggested packages
composer require --no-update symfony/polyfill-mbstring

composer update --no-interaction --no-progress --no-dev --prefer-stable
composer info -D | sort

# install box2
composer global show kherge/box -q || travis_retry composer global require --no-interaction --no-progress kherge/box:^2.7

# build phar file
php -d phar.readonly=false $(composer config home)/vendor/bin/box build