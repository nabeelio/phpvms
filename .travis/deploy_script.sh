#!/usr/bin/env bash

if [ "$TRAVIS" = "true" ]; then

  cd $TRAVIS_BUILD_DIR

  if test "$TRAVIS_TAG"; then
    PKG_NAME=$TRAVIS_TAG
    VERSION=$TRAVIS_TAG

    # Pass in the tag as the version to write out
    php artisan phpvms:version --write $VERSION
  else
    echo "On branch $TRAVIS_BRANCH"

    if [ "$TRAVIS_BRANCH" != "master" ] && [ "$TRAVIS_BRANCH" != "dev" ]; then
      echo "Not on valid branch, exiting"
      exit 0
    fi

    # Write the version out but place the branch ID in there
    # This is only for the dev branch
    BASE_VERSION=$(php artisan phpvms:version --base-only)

    # This now includes the pre-release version, so "-dev" by default
    PKG_NAME=${BASE_VERSION}

    # Don't pass in a version here, just write out the latest hash
    php artisan phpvms:version --write >VERSION
    VERSION=$(cat VERSION)
  fi

  echo "Version: $VERSION"
  echo "Package name: $TAR_NAME"

  FILE_NAME="phpvms-$PKG_NAME"
  TAR_NAME="$FILE_NAME.tar.gz"
  ZIP_NAME="$FILE_NAME.zip"

  echo "Cleaning files"

  rm -rf vendor
  composer install --no-dev --prefer-dist --no-interaction --verbose

  # Leftover individual files to delete
  declare -a remove_files=(
    .git
    .github
    .sass-cache
    .idea
    .travis
    docker
    _ide_helper.php
    .dockerignore
    .dpl
    .editorconfig
    .eslintignore
    .eslintrc
    .php_cs
    .php_cs.cache
    .phpstorm.meta.php
    .styleci.yml
    .phpunit.result.cache
    env.php
    intellij_style.xml
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

  for file in "${remove_files[@]}"; do
    rm -rf $file
  done

  find ./vendor -type d -name ".git" -print0 | xargs rm -rf
  find . -type d -name "sass-cache" -print0 | xargs rm -rf

  # clear any app specific stuff that might have been loaded in
  find bootstrap/cache -mindepth 1 -maxdepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
  find storage/app -mindepth 1 -maxdepth 1 -not -name '.gitignore' -not -name public -not -name import -print0 -exec rm -rf {} +
  find storage/app/public -mindepth 1 -maxdepth 1 -not -name '.gitignore' -not -name avatars -not -name uploads -print0 -exec rm -rf {} +
  find storage/app/public/avatars -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
  find storage/app/public/uploads -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
  find storage/debugbar -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
  find storage/docker -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
  find storage/framework/cache -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
  find storage/framework/sessions -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
  find storage/framework/views -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +
  find storage/logs -mindepth 1 -not -name '.gitignore' -print0 -exec rm -rf {} +

  mkdir -p storage/app/public/avatars
  mkdir -p storage/app/public/uploads
  mkdir -p storage/framework/cache
  mkdir -p storage/framework/sessions
  mkdir -p storage/framework/views

  # Regenerate the autoloader and classes
  composer dump-autoload
  make clean

  cd /tmp
  ls -al $TRAVIS_BUILD_DIR/../

  tar -czf $TAR_NAME -C $TRAVIS_BUILD_DIR
  sha256sum $TAR_NAME >"$TAR_NAME.sha256"
  tar2zip $TAR_NAME
  sha256sum $ZIP_NAME >"$ZIP_NAME.sha256"

  ls -al /tmp

  echo "Uploading to S3"
  mkdir -p $TRAVIS_BUILD_DIR/build
  cd $TRAVIS_BUILD_DIR/build

  mv "/tmp/$TAR_NAME" "/tmp/$ZIP_NAME" "/tmp/$TAR_NAME.sha256" "/tmp/$ZIP_NAME.sha256".
  artifacts upload --target-paths "/" $ZIP_NAME $TAR_NAME $TRAVIS_BUILD_DIR/VERSION $TAR_NAME.sha256 $ZIP_NAME.sha256

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

  curl -X POST --data "{\"content\": \"A new build is available at http://downloads.phpvms.net/$TAR_NAME ($VERSION)\"}" -H "Content-Type: application/json" $DISCORD_WEBHOOK_URL
fi
