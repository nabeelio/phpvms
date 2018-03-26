#!/usr/bin/env bash

if [ "$TRAVIS" = "true" ]; then

    cd $TRAVIS_BUILD_DIR

    if test "$TRAVIS_TAG"; then
        PKG_NAME=$TRAVIS_TAG
    else
        echo "On branch $TRAVIS_BRANCH"

        if [ "$TRAVIS_BRANCH" != "master" ] && [ "$TRAVIS_BRANCH" != "dev" ]; then
            echo "Not on valid branch, exiting"
            exit 0;
        fi;

        BASE_VERSION=`php artisan phpvms:version --base-only`
        PKG_NAME=${BASE_VERSION}-${TRAVIS_BRANCH}
    fi

    FILE_NAME="phpvms-$PKG_NAME"
    TAR_NAME="$FILE_NAME.tar.gz"
    echo "Writing $TAR_NAME"

    php artisan phpvms:version --write > VERSION
    VERSION=`cat VERSION`
    echo "Version: $VERSION"

    echo "Cleaning files"

    make clean

    # Clean up the dependencies to only remove the dev packages
    #rm -rf vendor
    #composer install --no-interaction --no-dev

    rm -rf env.php config.php
    find ./vendor -type d -name ".git" -print0 | xargs rm -rf
    find . -type d -name "sass-cache" -print0 | xargs rm -rf

    # clear any app specific stuff that might have been loaded in
    find storage/app/public -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
    find storage/app -mindepth 1 -not -name '.gitignore' -not -name public -not -name import -print0 -exec rm -rf {} +

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
    sha256sum $TAR_NAME > "$TAR_NAME.sha256"

    echo "uploading to s3"
    mkdir -p $TRAVIS_BUILD_DIR/build
    cd $TRAVIS_BUILD_DIR/build

    mv "/tmp/$TAR_NAME" "/tmp/$TAR_NAME.sha256" .
    artifacts upload --target-paths "/" $TAR_NAME $TRAVIS_BUILD_DIR/VERSION $TAR_NAME.sha256

    # Upload the version for a tagged release. Move to a version file in different
    # tags. Within phpVMS, we have an option of which version to track in the admin
    if test "$TRAVIS_TAG"; then
        echo "uploading release version file"
        cp "$TRAVIS_BUILD_DIR/VERSION" release_version
        artifacts upload --target-paths "/" release_version
    else
        echo "uploading $TRAVIS_BRANCH_version file"
        cp $TRAVIS_BUILD_DIR/VERSION ${TRAVIS_BRANCH}_version
        artifacts upload --target-paths "/" ${TRAVIS_BRANCH}_version
    fi

    curl -X POST --data "{\"content\": \"A new build is available at http://downloads.phpvms.net/$TAR_NAME ($VERSION)\"}" -H "Content-Type: application/json"  $DISCORD_WEBHOOK_URL
fi
