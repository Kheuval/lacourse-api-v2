#!/bin/bash
set -e

source $(dirname $(realpath "$0"))/commons.sh

# Get docker path
DOCKER_DIR=$(get_path "../..")
BUILD_SRC=$DOCKER_DIR/environment/build
BUILD_DIST=$DOCKER_DIR/build

# Load .project
DOT_PROJECT=$DOCKER_DIR/.project
check_file_exists $DOT_PROJECT

set -a
source $DOT_PROJECT
set +a

# Check requirements
check_vars ENV PROJECT_NAME

echo -e "➡️  Generate build/ directory for environment $GREEN$ENV$NOCOLOR"

# Reset build/
rm -rf $BUILD_DIST
mkdir $BUILD_DIST
echo '*' > $BUILD_DIST/.gitignore

# Dump contextualized Dockerfiles
cd $BUILD_SRC
for FILE in $(find -type f); do
  mkdir -p $BUILD_DIST/$(dirname $FILE)
  envsubst < $FILE > $BUILD_DIST/$FILE
done

