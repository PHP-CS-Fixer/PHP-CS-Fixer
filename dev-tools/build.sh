#!/bin/sh
set -eu

# ensure that deps will work on lowest supported PHP version
composer config platform.php 7.4

# Infection is not compatible with 7.4, and basically not needed in PHAR
composer remove --dev infection/infection --no-update

# install package deps without dev-deps / remove already installed dev-deps
# box can ignore dev-deps, but dev-deps, when installed, may lower version of prod-deps
composer update --optimize-autoloader --no-interaction --no-progress --no-scripts --no-dev
composer info -D | sort

# install box/phar
mkdir -p dev-tools/bin
if [ ! -x dev-tools/bin/box ]; then
    wget -O dev-tools/bin/box "https://github.com/box-project/box/releases/download/4.6.7/box.phar"
    chmod +x dev-tools/bin/box
fi
dev-tools/bin/box --version

# build phar file
dev-tools/bin/box compile

# revert changes to composer
git checkout composer.json
composer update --optimize-autoloader --no-interaction --no-progress --no-scripts -q
