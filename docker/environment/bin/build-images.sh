#!/bin/bash
set -e

source $(dirname $(realpath "@0"))/commons.sh

DOCKER_DIR=$(get_path "../..")
BUILD_SRC=$DOCKER_DIR/environment/build-src

# Load .project
DOT_PROJECT=$DOCKER_DIR/.project
check_file_exists $DOT_PROJECT

set -a
source $DOT_PROJECT
set +a

# Check requirements
check_vars ENVIRONMENTS PROJECT_NAME BASE_REGISTRY_URL SHELL_USER_NAME SHELL_USER_ID

# Define which images to build, default is all images defined by a Dockerfile
IMAGES=()
if [ ! "$_IMAGE" ]; then
  cd $BUILD_SRC
  for FILE in $(find -name Dockerfile); do
    IMAGE_NAME=$(dirname $FILE)
    # Remove first 2 character https://stackoverflow.com/questions/6594085/remove-first-character-of-a-string-in-bash
    IMAGE_NAME=${IMAGE_NAME:2}
    IMAGES+=($IMAGE_NAME)
  done
else
  IMAGES+=("$_IMAGE")
fi

# Define which environments to build, default is all environments from $ENVIRONMENTS variable
ENVS=()
if [ ! "$_ENV" ]; then
  ENVS=("$ENVIRONMENTS")
else
  ENVS+=($_ENV)
fi

for IMAGE_NAME in ${IMAGES[@]}; do
  for ENV in ${ENVS[@]}; do
    IMAGE_ENV=$PROJECT_NAME/$IMAGE_NAME/$ENV
    echo -e "➡️  Generate $GREEN$PROJECT_NAME$NO_COLOR image for $GREEN$IMAGE_ENV$NO_COLOR"
    docker build \
      --pull \
      --build-arg CONTAINER_REGISTRY_URL=$BASE_REGISTRY_URL \
      --build-arg SHELL_USER_ID=$SHELL_USER_ID \
      --build-arg SHELL_USER_NAME=$SHELL_USER_NAME \
      -t $IMAGE_ENV $BUILD_SRC/$IMAGE_NAME
  done
done

