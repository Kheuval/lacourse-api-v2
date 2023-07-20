#!/bin/bash
set -e

source $(dirname $(realpath "$0"))/commons.sh

# Get docker path
DOCKER_DIR=$(get_path "../..")

cd $DOCKER_DIR

# Match docker compose config lines
# - being preceded by "- "
# - starting with a "." "~" or "/"
# - not containing ":"
# - eventually finishing with a "/"

DOCKER_COMPOSE_V1_SYNTAX="(?<=- )[\.~/][^\:]*(?=:)/?"
DOCKER_COMPOSE_V2_SYNTAX="(?<=^\s{6}source: ).*"

command=$(docker-compose config | grep -oP "$DOCKER_COMPOSE_V1_SYNTAX|$DOCKER_COMPOSE_V2_SYNTAX")

for entry in $command; do
  if [[ ! -f "$entry" ]] && [[ ! -d "$entry" ]]; then
    echo "Resource \"$entry\" not found, will fail to mount"
    exit 1
  fi
done

echo -e "["$GREEN"ALL OK"$NOCOLOR"]"
