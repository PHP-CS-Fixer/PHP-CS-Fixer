#!/bin/sh

set -eu

cd "$(dirname "$0")"

mkdir -p bin

VERSION_CB="2.0.0.2"
VERSION_SC="stable"

echo λλλ phive
if [ ! -f bin/phive ]; then
    wget --no-clobber --output-document=bin/phive https://phar.io/releases/phive.phar
    wget --no-clobber --output-document=bin/phive.asc https://phar.io/releases/phive.phar.asc
    gpg --keyserver pool.sks-keyservers.net --recv-keys 0x9D8A98B29B2D5D79
    gpg --verify bin/phive.asc bin/phive
    chmod u+x bin/phive
    bin/phive --version
fi

echo λλλ checkbashisms
if [ ! -f bin/checkbashisms ]; then
    wget --no-clobber --output-document=bin/checkbashisms https://sourceforge.net/projects/checkbaskisms/files/${VERSION_CB}/checkbashisms/download
    chmod u+x bin/checkbashisms
    bin/checkbashisms --version
fi

echo λλλ shellcheck
if [ ! -f bin/shellcheck ]; then
    wget -qO- "https://storage.googleapis.com/shellcheck/shellcheck-${VERSION_SC}.linux.x86_64.tar.xz" | tar -xJv --directory bin shellcheck-${VERSION_SC}/shellcheck
    mv "bin/shellcheck-${VERSION_SC}/shellcheck" bin/
    rmdir "bin/shellcheck-${VERSION_SC}/"
    bin/shellcheck --version
fi

echo λλλ composer packages
composer update
composer info -D | sort

echo λλλ phive packages
./bin/phive install --trust-gpg-keys D2CCAC42F6295E7D,8E730BA25823D8B5
