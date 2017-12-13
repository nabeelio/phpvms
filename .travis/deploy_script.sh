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
    mkdir -p /tmp/out/phpvms
    cp -a $TRAVIS_BUILD_DIR/. /tmp/out/phpvms
    tar -czf $PKG_NAME.tar.gz -C /tmp/out/
    rsync -r --delete-after --quiet $PKG_NAME.tar.gz downloads@phpvms.net:/var/www/downloads/
    rm -rf /tmp/out
fi
