#!/usr/bin/env bash

if [ "$TRAVIS" = "true" ]; then

    if test "$TRAVIS_TAG"; then
        PKG_NAME=$TRAVIS_TAG
    else
        PKG_NAME=nightly
    fi

    echo "Writing $PKG_NAME.tar.gz"

    # delete all superfluous files
    rm -rf .git deploy_rsa.enc .idea phpvms.iml .travis .dpl
    find . -type d -name ".git" | xargs rm -rf

    # tar and upload
    tar -czf phpvms-7.0.0-$PKG_NAME.tar.gz -C $TRAVIS_BUILD_DIR/../ phpvms
    rsync -ahP --delete-after $PKG_NAME.tar.gz downloads@phpvms.net:/var/www/downloads/
    rm -rf /tmp/out
fi
