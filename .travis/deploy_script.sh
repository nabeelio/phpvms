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

    rm -rf vendor
    composer install --no-dev --prefer-dist --no-interaction --verbose

    # Clean up the dependencies to remove some of the dev packages
#    declare -a remove_packages=(
#        'barryvdh/laravel-ide-helper'
#        'bpocallaghan/generators'
#        'codedungeon/phpunit-result-printer'
#        'fzaninotto/faker'
#        'nikic/php-parser'
#        'phpstan/phpstan'
#        'phpunit/phpunit',
#        'weebly/phpstan-laravel'
#    )
#
#    for pkg in "${remove_packages[@]}"
#    do
#        composer --optimize-autoloader --no-interaction remove $pkg
#    done

    # Leftover individual files to delete
    declare -a remove_files=(
        .git
        .github
        .sass-cache
        .idea
        .travis
        docker
        resources/docker
        tests
        _ide_helper.php
        .dpl
        .eslintignore
        .eslintrc
        .php_cs
        .php_cs.cache
        .phpstorm.meta.php
        .styleci.yml
        env.php
        config.php
        docker-compose.yml
        Makefile
        phpcs.xml
        phpunit.xml
        phpvms.iml
        Procfile
        phpstan.neon
        node_modules
        composer.phar
        vendor/willdurand/geocoder/tests
    )

    for file in "${remove_files[@]}"
    do
        rm -rf $file
    done

    find ./vendor -type d -name ".git" -print0 | xargs rm -rf
    find . -type d -name "sass-cache" -print0 | xargs rm -rf

    # clear any app specific stuff that might have been loaded in
    find storage/app -mindepth 1 -not -name '.gitignore' -not -name public -not -name import -print0 -exec rm -rf {} +
    find storage/app/public -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
    find storage/debugbar -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
    find storage/docker -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
    find storage/docker/data -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
    find storage/framework/cache -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
    find storage/framework/sessions -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
    find storage/framework/views -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
    find storage/logs -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +

    # Regenerate the autoloader and classes
    composer dump-autoload
    make clean

    echo "Creating Tarball"
    cd /tmp
    tar -czf $TAR_NAME -C $TRAVIS_BUILD_DIR/../ phpvms
    sha256sum $TAR_NAME > "$TAR_NAME.sha256"

    echo "Uploading to S3"
    mkdir -p $TRAVIS_BUILD_DIR/build
    cd $TRAVIS_BUILD_DIR/build

    mv "/tmp/$TAR_NAME" "/tmp/$TAR_NAME.sha256" .
    artifacts upload --target-paths "/" $TAR_NAME $TRAVIS_BUILD_DIR/VERSION $TAR_NAME.sha256

    # Upload the version for a tagged release. Move to a version file in different
    # tags. Within phpVMS, we have an option of which version to track in the admin
    if test "$TRAVIS_TAG"; then
        echo "Uploading release version file"
        cp "$TRAVIS_BUILD_DIR/VERSION" release_version
        artifacts upload --target-paths "/" release_version
    else
        echo "Uploading ${TRAVIS_BRANCH}_version file"
        cp $TRAVIS_BUILD_DIR/VERSION ${TRAVIS_BRANCH}_version
        artifacts upload --target-paths "/" ${TRAVIS_BRANCH}_version
    fi

    curl -X POST --data "{\"content\": \"A new build is available at http://downloads.phpvms.net/$TAR_NAME ($VERSION)\"}" -H "Content-Type: application/json"  $DISCORD_WEBHOOK_URL
fi
