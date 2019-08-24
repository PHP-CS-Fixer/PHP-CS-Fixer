#!/bin/sh

set -eu

cd "$(dirname "$0")"

mkdir -p bin

VERSION_CB="2.0.0.2"
VERSION_SC="stable"

if [ ! -f bin/checkbashisms ]; then
    wget --no-clobber --output-document=bin/checkbashisms https://sourceforge.net/projects/checkbaskisms/files/${VERSION_CB}/checkbashisms/download
    chmod u+x bin/checkbashisms
    bin/checkbashisms --version
fi

if [ ! -f bin/shellcheck ]; then
    wget -qO- "https://storage.googleapis.com/shellcheck/shellcheck-${VERSION_SC}.linux.x86_64.tar.xz" | tar -xJv --directory bin shellcheck-${VERSION_SC}/shellcheck
    mv "bin/shellcheck-${VERSION_SC}/shellcheck" bin/
    rmdir "bin/shellcheck-${VERSION_SC}/"
    bin/shellcheck --version
fi
