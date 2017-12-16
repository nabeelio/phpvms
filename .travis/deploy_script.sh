#!/usr/bin/env bash

if [ "$TRAVIS" = "true" ]; then

    if test "$TRAVIS_TAG"; then
        PKG_NAME=$TRAVIS_TAG
    else
        PKG_NAME=master
    fi

    TAR_NAME="phpvms-7.0.0-$PKG_NAME.tar.gz"
    echo "Writing $TAR_NAME"

    # delete all superfluous files
    echo "cleaning files"

    cd $TRAVIS_BUILD_DIR
    find ./vendor -type d -name ".git" | xargs rm -rf
    rm -rf .idea phpvms.iml .travis .dpl
    rm -rf .phpstorm.meta.php _ide_helper.php
    php artisan version:show > VERSION
    mv .env.dev.example .env

    echo "creating tarball"
    cd /tmp
    tar -czf $TAR_NAME -C $TRAVIS_BUILD_DIR/../ phpvms phpvms/.*

    echo "running rsync"
    rsync -ahP --delete-after /tmp/$TAR_NAME downloads@phpvms.net:/var/www/downloads/
fi
