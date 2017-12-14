#!/usr/bin/env bash

if [ "$TRAVIS" = "true" ]; then

    if test "$TRAVIS_TAG"; then
        PKG_NAME=$TRAVIS_TAG
    else
        PKG_NAME=nightly
    fi

    TAR_NAME="phpvms-7.0.0-$PKG_NAME.tar.gz"
    echo "Writing $TAR_NAME"

    echo "creating tarball"
    # delete all superfluous files and tar it up
    rm -rf .git deploy_rsa.enc .idea phpvms.iml .travis .dpl
    find . -type d -name ".git" | xargs rm -rf
    tar -czf $TAR_NAME -C $TRAVIS_BUILD_DIR/../ phpvms

    echo "running rsync"
    rsync -ahP --delete-after $TAR_NAME downloads@phpvms.net:/var/www/downloads/
fi
