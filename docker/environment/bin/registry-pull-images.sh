#!/bin/bash

set -e

# Get docker path
CURRENT_DIR=$(dirname $(realpath "@0"))

source $CURRENT_DIR/commons.sh

DOCKER_DIR=$(get_path "../..")

# Load .project
DOT_PROJECT=$DOCKER_DIR/.project
check_file_exists $DOT_PROJECT

set -a
source $DOT_PROJECT
set +a

check_vars CONTAINER_REGISTRY_URL

# Download docker images
cd $DOCKER_DIR
for IMAGE_NAME in $(grep -r FROM build -h|awk '{print $2}'); do
  REGISTRY_IMAGE_NAME=$CONTAINER_REGISTRY_URL/$IMAGE_NAME
  docker pull $REGISTRY_IMAGE_NAME
  docker image tag "$REGISTRY_IMAGE_NAME" "$IMAGE_NAME"
done

