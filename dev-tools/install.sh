#!/bin/sh
#
# dev-tools install
#
# installation script for dev-tools utilities
#
# script must be idempotent as to continue retrying in case of failure
# (e.g. network timed out) when invoking again so that temporary i/o or
# network problems can be dealt with by invoking the script again (and
# again) until success. required for unattended build.
#
# usage: ./dev-tools/install.sh
#    or: ./install.sh
#    or: ./install.sh || ./install.sh || ./install.sh
#
set -eu

cd "$(dirname "$0")"

mkdir -p bin

VERSION_CB="2.23.6"
VERSION_SC="stable"

echo λλλ checkbashisms
if [ ! -x bin/checkbashisms ]; then
    wget -qO- "https://deb.debian.org/debian/pool/main/d/devscripts/devscripts_${VERSION_CB}.tar.xz" \
        | tar -xJv -O "devscripts-${VERSION_CB}/scripts/checkbashisms.pl" \
        > bin/checkbashisms
    chmod u+x bin/checkbashisms
fi
bin/checkbashisms --version

echo λλλ shellcheck
if [ ! -x bin/shellcheck ]; then
    wget -qO- "https://github.com/koalaman/shellcheck/releases/download/${VERSION_SC}/shellcheck-${VERSION_SC}.linux.x86_64.tar.xz" \
        | tar -xJv -O shellcheck-${VERSION_SC}/shellcheck \
        > bin/shellcheck
    chmod u+x bin/shellcheck
fi
bin/shellcheck --version

echo λλλ composer packages
composer install -v
composer info -D | sort
