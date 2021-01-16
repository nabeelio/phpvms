#!/usr/bin/env bash

./.github/scripts/version.sh

echo "Version: ${VERSION}"
echo "Full Version: ${FULL_VERSION}"
echo "Package name: ${TAR_NAME}"
echo "Current directory: ${BASE_DIR}"

echo "Cleaning files"

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

cd /tmp

ls -al $BASE_DIR/../

tar -czf $TAR_NAME -C $BASE_DIR .
sha256sum $TAR_NAME >"$TAR_NAME.sha256"
tar2zip $TAR_NAME
sha256sum $ZIP_NAME >"$ZIP_NAME.sha256"

ls -al /tmp

echo "Moving to dist"
mkdir -p $BASE_DIR/dist
cd $BASE_DIR/dist

mv "/tmp/$TAR_NAME" "/tmp/$ZIP_NAME" "/tmp/$TAR_NAME.sha256" "/tmp/$ZIP_NAME.sha256" .

