#!/bin/bash

set -e

# Get docker path
CURRENT_DIR=$(dirname $(realpath "@0"))

source $CURRENT_DIR/commons.sh

DOCKER_DIR=$(get_path "../..")

check_file_exists $DOCKER_DIR/.project

set -a
source $DOCKER_DIR/.project
set +a

check_vars SHELL_USER_ID SHELL_USER_NAME

for SCRIPT in $(find $DOCKER_DIR/bin/set-permissions/ -name "*.sh"|sort); do
  source $SCRIPT
done
