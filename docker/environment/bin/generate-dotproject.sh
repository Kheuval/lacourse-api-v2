#!/bin/bash
set -e

source $(dirname $(realpath "$0"))/commons.sh

# Get docker path
DOCKER_DIR=$(get_path "../..")

if [ -f $DOCKER_DIR/.project ]; then
  echo "$DOCKER_DIR/.project exists,  delete .project to re-generate"
  exit 1
fi

# Load .project.local
DOT_PROJECT_LOCAL=$DOCKER_DIR/.project.local
check_file_exists $DOT_PROJECT_LOCAL
set -a
source $DOT_PROJECT_LOCAL
set +a
check_vars ENV

# Dump .project file
cd $DOCKER_DIR

> .project
cat ./config/project/project.base >> .project
echo  >> .project
cat  ./config/project/project.${ENV} >> .project
echo  >> .project
cat  ./.project.local >> .project
