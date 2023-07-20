#!/bin/bash

set -e

source $(dirname $(realpath "@0"))/commons.sh

DOCKER_DIR=$(get_path "../..")

# Load .project
DOT_PROJECT=$DOCKER_DIR/.project
check_file_exists $DOT_PROJECT

set -a
source $DOT_PROJECT
set +a


check_vars CONTAINER_REGISTRY_URL CONTAINER_REGISTRY_SECRET

# Log in docker registry
docker login $CONTAINER_REGISTRY_URL -u nologin -p $CONTAINER_REGISTRY_SECRET
