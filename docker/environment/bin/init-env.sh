#!/bin/bash
set -e

source $(dirname $(realpath "$0"))/commons.sh

# Get docker path
DOCKER_DIR=$(get_path "../..")


if [ -f $DOCKER_DIR/.project ]; then
  echo "$DOCKER_DIR/.project exists, initialization already done, delete .project to re-initialize"
  exit 1
fi

PROJECT_BASE=$DOCKER_DIR/config/project/project.base
if [ -f "$PROJECT_BASE" ]; then
  set -a
  source $PROJECT_BASE
  set +a
  echo -e "➡️  Load base variables from $GREEN""config/project/project.base$NOCOLOR"
fi

DOTPROJECT_LOCAL=$DOCKER_DIR/.project.local
if [ -f "$DOTPROJECT_LOCAL" ]; then
  set -a
  source $DOTPROJECT_LOCAL
  set +a
  echo -e "➡️  Load base variables from $GREEN"".project.local$NOCOLOR"
fi

# Get current user
if [ ! "$USER" ]; then
  check_vars USER
fi

if [ "$USER" = "root" ]; then
  echo "Cannot run with root user, try USER=<username> $0"
  exit 1
fi

export USER_NAME="$USER"
export USER_ID=$(id -u $USER)

# Check we have all variables
check_vars ENV PROJECT_NAME PROJECT_PUBLIC_NAME USER_NAME USER_ID CONTAINER_REGISTRY_SECRET

GUG_GENERATE="gug env:generate:template -n -e -s"

for FILE in $(find $DOCKER_DIR/environment/project -mindepth 1 -maxdepth 1); do
  FILENAME=$(basename $FILE)
  $GUG_GENERATE $FILE $DOCKER_DIR/config/project/$FILENAME
done

check_file_exists $DOCKER_DIR/config/project/project.local
mv $DOCKER_DIR/config/project/project.local $DOCKER_DIR/.project.local
