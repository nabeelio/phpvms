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

echo "Version: ${VERSION}"
echo "Full Version: ${FULL_VERSION}"
echo "Package name: ${TAR_NAME}"