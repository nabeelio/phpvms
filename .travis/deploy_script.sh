#!/usr/bin/env bash

if test "$TRAVIS_TAG"; then
    PKG_NAME=$TRAVIS_TAG
else
    PKG_NAME=nightly
fi

echo $PKG_NAME

if [ "$TRAVIS" = "true" ]; then

    # delete all superfluous files
    rm -rf .git deploy_rsa.enc .idea phpvms.iml .travis .dpl
    find . -type d -name ".git" | xargs rm -rf

    # tar and upload
    tar -czf $PKG_NAME.tar.gz -C $TRAVIS_BUILD_DIR phpvms
    rsync -r --delete-after --quiet $PKG_NAME.tar.gz downloads@phpvms.net:/var/www/downloads/
fi
