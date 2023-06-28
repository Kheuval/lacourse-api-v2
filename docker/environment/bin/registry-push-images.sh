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
check_vars ENVIRONMENTS PROJECT_NAME CONTAINER_REGISTRY_URL


set +e
TAG=$(git describe --tags 2>/dev/null)
set -e

TAG_INFO=""
if [ "$TAG" ]; then
  TAG_INFO=" (🏷️  $TAG)"
fi
echo -e "➡️  Push $GREEN$PROJECT_NAME$NO_COLOR images to registry $CONTAINER_REGISTRY_URL$TAG_INFO"



# Define which images to push, default is all images defined by a Dockerfile
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
    IMAGE_LOCAL=$PROJECT_NAME/$IMAGE_NAME/$ENV
    IMAGE_REGISTRY=$CONTAINER_REGISTRY_URL/$PROJECT_NAME/$IMAGE_NAME/$ENV

    # Tag and push latest
    docker tag $IMAGE_LOCAL $IMAGE_REGISTRY
    docker push $IMAGE_REGISTRY

    # Tag and push tagged
    if [ "$TAG" ]; then
      docker tag $IMAGE_LOCAL $IMAGE_REGISTRY:$TAG
      docker push $IMAGE_REGISTRY:$TAG
    fi
  done
done

