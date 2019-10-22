#!/bin/sh
#
# dev-tools install
#
# installation script for dev-tool utilities (phive, checkbashisms,
# etc.) but not composer.
#
# script must be idempotent as to continue retrying in case of failure
# (e.g. network timed out) when invoking again so that temporary i/o or
# network problems can be dealt with by invoking the script again (and
# again) until success. required for unattended build.
#
# usage: ./dev-tools/install.sh
#    or: travis_retry ./dev-tools/install.sh
#    or: ./install.sh
#    or: ./install.sh || ./install.sh || ./install.sh
#
set -eu

cd "$(dirname "$0")"

mkdir -p bin

###
# temporary work-around: phive expects gpg2 as gpg, build box has gpg1 as gpg
#   phive/phive:208 <https://github.com/phar-io/phive/issues/208>
if [ "${TRAVIS-}" = true ]; then
  sudo apt-get install dirmngr --install-recommends
  [ ! -f "/usr/bin/gpg1"  ] && sudo mv -- "/usr/bin/gpg" "/usr/bin/gpg1"
  sudo ln -sfT "/usr/bin/gpg2" "/usr/bin/gpg"
fi

VERSION_CB="2.0.0.2"
VERSION_SC="stable"

echo λλλ phive
if [ ! -x bin/phive ]; then
    wget -Obin/phive https://phar.io/releases/phive.phar
    wget -Obin/phive.asc https://phar.io/releases/phive.phar.asc
    gpg --keyserver pool.sks-keyservers.net --recv-keys 0x9D8A98B29B2D5D79
    gpg --verify bin/phive.asc bin/phive
    chmod u+x bin/phive
fi
bin/phive --version

echo λλλ checkbashisms
if [ ! -x bin/checkbashisms ]; then
    wget -Obin/checkbashisms https://sourceforge.net/projects/checkbaskisms/files/${VERSION_CB}/checkbashisms/download
    chmod u+x bin/checkbashisms
fi
bin/checkbashisms --version

echo λλλ shellcheck
if [ ! -x bin/shellcheck ]; then
    wget -qO- "https://storage.googleapis.com/shellcheck/shellcheck-${VERSION_SC}.linux.x86_64.tar.xz" \
        | tar -xJv -O shellcheck-${VERSION_SC}/shellcheck \
        > bin/shellcheck
    chmod u+x bin/shellcheck
fi
bin/shellcheck --version

echo λλλ composer packages
composer update
composer info -D | sort

echo λλλ phive packages

# echo "debug: #phive/phive:208"
# see above
./bin/phive install --trust-gpg-keys D2CCAC42F6295E7D,8E730BA25823D8B5
