#!/bin/bash
set -e

source $(dirname $(realpath "$0"))/commons.sh

# Get docker path
DOCKER_DIR=$(get_path "../..")

# Load .project
DOT_PROJECT=$DOCKER_DIR/.project
check_file_exists $DOT_PROJECT

set -a
source $DOT_PROJECT
set +a

# Check requirements
check_vars ENV PROJECT_NAME PROJECT_PUBLIC_NAME SHELL_USER_ID SHELL_USER_NAME

echo -e "➡️  Dumping configuration for user $GREEN$SHELL_USER_NAME$NOCOLOR and environment $GREEN$ENV$NOCOLOR"


GUG_GENERATE="gug env:generate:template -n -e -s"

# Dump docker-compose
check_file_exists $DOCKER_DIR/environment/docker-compose/docker-compose.${ENV}.yml

cd $DOCKER_DIR \
  && cp environment/docker-compose/docker-compose.base.yml docker-compose.yml \
  && cp environment/docker-compose/docker-compose.${ENV}.yml docker-compose.override.yml

# .env
check_file_exists $DOCKER_DIR/environment/dotenv/dotenv.${ENV}

cd $DOCKER_DIR \
  && rm -f .env \
  && if [ ! -f ".env.local" ]; then $GUG_GENERATE environment/dotenv/dotenv.local .env.local; fi \
  && $GUG_GENERATE -o environment/dotenv/dotenv.base > .env \
  && $GUG_GENERATE -o environment/dotenv/dotenv.${ENV} >> .env \
  && cat .env.local >> .env

# Dump nginx configuration
cd $DOCKER_DIR/config/nginx \
  && source generate-src.sh \
  && source generate-dist.sh
