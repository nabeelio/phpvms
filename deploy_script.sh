#!/usr/bin/env bash

if test "$TRAVIS_TAG"; then
    PKG_NAME=$TRAVIS_TAG
else
    PKG_NAME=nightly
fi

echo $PKG_NAME
#rm -rf .git deploy_rsa.enc .idea phpvms.iml
tar -czvf $PKG_NAME.tar.gz -C $$TRAVIS_BUILD_DIR .
rsync -r --delete-after --quiet $PKG_NAME.tar.gz downloads@phpvms.net:/var/www/downloads/
