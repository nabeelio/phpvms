#!/usr/bin/env bash

if test "$GIT_TAG_NAME"; then
  VERSION=$GIT_TAG_NAME

  # Pass in the tag as the version to write out
  php artisan phpvms:version --write --write-full-version "${VERSION}"
  FULL_VERSION=$(php artisan phpvms:version)
else
  BRANCH=${GITHUB_REF##*/}
  echo "On branch $BRANCH"

#   if [ "$BRANCH" != "master" ] && [ "$BRANCH" != "dev" ]; then
#     echo "Not on valid branch, exiting"
#     exit 0
#   fi

  # Write the version out but place the branch ID in there
  # This is only for the dev branch
  BASE_VERSION=$(php artisan phpvms:version --base-only)

  # This now includes the pre-release version, so "-dev" by default
  VERSION=${BASE_VERSION}

  # Don't pass in a version here, just write out the latest hash
  php artisan phpvms:version --write "${VERSION}"
  FULL_VERSION=$(php artisan phpvms:version)
fi

FILE_NAME="phpvms-${VERSION}"
TAR_NAME="$FILE_NAME.tar.gz"
ZIP_NAME="$FILE_NAME.zip"
PWD=`pwd`

echo "Version: ${VERSION}"
echo "Full Version: ${FULL_VERSION}"
echo "Package name: ${TAR_NAME}"
echo "Current directory: ${PWD}"

echo "Cleaning files"

# Leftover individual files to delete
declare -a remove_files=(
  .git
  #.github
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

# Done