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

    php artisan phpvms:version --write > VERSION
    VERSION=`cat VERSION`
    echo "Version: $VERSION"

    make clean

    rm -rf env.php
    find ./vendor -type d -name ".git" -print0 | xargs rm -rf
    find . -type d -name "sass-cache" -print0 | xargs rm -rf

    # clear any app specific stuff that might have been loaded in
    find storage/app/public -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
    find storage/app -mindepth 1 -not -name '.gitignore' -not -name public -print0 -exec rm -rf {} +

    # Remove any development files
    rm -rf .sass-cache
    rm -rf .idea phpvms.iml .travis .dpl
    rm -rf .phpstorm.meta.php _ide_helper.php phpunit.xml Procfile

    # remove large sized files
    rm -rf .git
    rm -rf node_modules
    rm -rf composer.phar

    # delete files in vendor that are rather large
    rm -rf vendor/willdurand/geocoder/tests

    echo "creating tarball"
    cd /tmp
    tar -czf $TAR_NAME -C $TRAVIS_BUILD_DIR/../ phpvms

    echo "running rsync"
    rsync -ahP --delete-after /tmp/$TAR_NAME downloads@phpvms.net:/var/www/phpvms/downloads/

    cd /tmp/
    artifacts upload --target-paths "/" $TAR_NAME

    curl -X POST --data "{\"content\": \"A new build is available at http://phpvms.net/downloads/$TAR_NAME ($VERSION)\"}" -H "Content-Type: application/json"  $DISCORD_WEBHOOK_URL
fi
